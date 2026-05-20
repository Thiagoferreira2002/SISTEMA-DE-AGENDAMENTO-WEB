<?php

namespace App\Http\Controllers\Backend;

use App\Models\Agendamento;
use App\Models\Patient;
use App\Models\Professional;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
   //acessar o painel
   public function dashboard(){
       $user = Auth::user();
       $isProfessionalDashboard = false;
       $dashboardTitle = 'Painel de Controle';
       $dashboardSubtitle = 'Acompanhe os números principais, os próximos atendimentos e o ritmo da clínica.';
       $dashboardWelcome = 'Painel de Controle';
       $dashboardLinks = [
           'total' => route('admin.agendamentos.index'),
           'pendentes' => route('admin.agendamentos.confirmations'),
           'confirmados' => route('admin.agendamentos.index'),
           'complementar' => route('admin.patients.index'),
           'atrasados' => route('admin.agendamentos.delayed-appointments'),
           'finalizados' => route('admin.agendamentos.completed'),
       ];

       $appointmentsQuery = Agendamento::query();
       $activeAppointmentsQuery = Agendamento::query()->where(function ($query) {
           $query->whereNull('status')
               ->orWhereIn('status', ['pendente', 'confirmado']);
       });
       $completedAppointmentsQuery = Agendamento::query()->where('status', 'concluido');
       $upcomingAppointmentsQuery = Agendamento::query()
           ->with(['professional', 'patient'])
           ->whereDate('data_agendamento', '>=', now()->toDateString())
           ->where(function ($query) {
               $query->whereNull('status')
                   ->orWhereIn('status', ['pendente', 'confirmado']);
           });

       if ($user && $user->normalizedRole() === 'profissional') {
           $isProfessionalDashboard = true;
           $dashboardTitle = 'Painel do Profissional';
           $dashboardSubtitle = 'Veja os atendimentos vinculados ao seu perfil e acompanhe sua agenda em tempo real.';
           $dashboardWelcome = 'Boas-vindas, ' . trim((string) ($user->nome ?? $user->full_name ?? 'Profissional'));

           $professional = $this->authenticatedProfessional();

           if ($professional) {
               $this->applyProfessionalAppointmentScope($appointmentsQuery, $professional, $user);
               $this->applyProfessionalAppointmentScope($activeAppointmentsQuery, $professional, $user);
               $this->applyProfessionalAppointmentScope($completedAppointmentsQuery, $professional, $user);
               $this->applyProfessionalAppointmentScope($upcomingAppointmentsQuery, $professional, $user);
               $dashboardLinks['confirmados'] = route('admin.doctor.queue');
               $dashboardLinks['finalizados'] = route('admin.agendamentos.completed', ['source' => 'doctor']);
               $totalPacientes = Patient::count();
           } else {
               $appointmentsQuery->whereRaw('1 = 0');
               $activeAppointmentsQuery->whereRaw('1 = 0');
               $completedAppointmentsQuery->whereRaw('1 = 0');
               $upcomingAppointmentsQuery->whereRaw('1 = 0');
               $totalPacientes = 0;
           }
       } elseif ($user && $user->normalizedRole() === 'recepcionista') {
           $dashboardSubtitle = 'Acompanhe a agenda ativa, as confirmações pendentes e o fluxo operacional da recepção.';
           $totalPacientes = Patient::count();
       } elseif ($user && $user->isClinicManager()) {
           $dashboardSubtitle = 'Acompanhe os indicadores operacionais da clínica, os atendimentos e os serviços finalizados.';
           $totalPacientes = Patient::count();
       } else {
           $totalPacientes = Patient::count();
       }

       $totalAgendamentos = (clone $activeAppointmentsQuery)->count();
       $agendamentosPendentes = (clone $activeAppointmentsQuery)->where(function ($query) {
           $query->whereNull('status')->orWhere('status', 'pendente');
       })->count();
       $agendamentosConfirmados = (clone $activeAppointmentsQuery)->where('status', 'confirmado')->count();
       $agendamentosEmAtraso = (clone $activeAppointmentsQuery)
           ->orderBy('data_agendamento')
           ->orderBy('horario')
           ->get()
           ->filter(fn (Agendamento $agendamento) => $this->appointmentHasPassedEndTime($agendamento))
           ->count();
       $agendamentosFinalizados = (clone $completedAppointmentsQuery)->count();
       $proximosAgendamentos = $upcomingAppointmentsQuery
           ->orderBy('data_agendamento')
           ->orderBy('horario')
           ->get()
           ->filter(fn (Agendamento $agendamento) => ! $this->appointmentHasPassedEndTime($agendamento))
           ->take(6)
           ->values();

       return view('admin.dashboard', compact(
           'totalAgendamentos',
           'agendamentosPendentes',
           'agendamentosConfirmados',
           'agendamentosEmAtraso',
           'totalPacientes',
           'proximosAgendamentos',
           'isProfessionalDashboard',
           'dashboardTitle',
           'dashboardSubtitle',
           'dashboardWelcome',
           'dashboardLinks',
           'agendamentosFinalizados'
       ));
   }

   //fazer login
   public function login(){
     return view('admin.auth.login');
   }

   //recuperar a senha
   public function recuperarSenha(){
    return view('admin.auth.forgot-password');
   }

   public function markNotificationsRead(Request $request){
       $user = Auth::user();

       if (! $user) {
           return redirect()->route('admin.login');
       }

       $notificationId = $request->integer('notification_id');

       if ($notificationId > 0) {
           $notification = Agendamento::query()->find($notificationId);

           if ($notification && $this->userCanReadNotification($user, $notification)) {
               $this->markNotificationAsRead($notification, (int) $user->id);
           }
       } else {
           $this->markVisibleNotificationsAsRead($user);
       }

       $fallback = route('admin.agendamentos.confirmations');

       return redirect($this->resolveSafeRedirect($request->string('redirect_to')->toString(), $fallback));
   }

   public function editAccount(Request $request)
   {
       return view('admin.account.edit', [
           'user' => $request->user(),
       ]);
   }

   public function tutorial()
   {
       return view('admin.tutorial');
   }

   public function updateAccount(Request $request)
   {
       $user = $request->user();
       $canEditCpf = $user->canEditCpf();

       $validated = $request->validate([
           'nome' => ['required', 'string', 'max:255'],
           'sobrenome' => ['nullable', 'string', 'max:255'],
           'cpf' => $canEditCpf ? ['nullable', 'string', 'size:14', Rule::unique('users', 'cpf')->ignore($user->id)] : ['nullable'],
           'fone' => ['nullable', 'string', 'max:20'],
           'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
           'capa' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
       ], [
           'cpf.size' => 'O CPF deve estar no formato 000.000.000-00.',
           'capa.image' => 'Selecione uma imagem válida para a foto do perfil.',
           'capa.mimes' => 'A foto do perfil deve ser JPG, PNG ou WEBP.',
           'capa.max' => 'A foto do perfil deve ter no máximo 2 MB.',
       ]);

       if ($canEditCpf) {
           $validated['cpf'] = preg_replace('/\D+/', '', (string) ($validated['cpf'] ?? '')) ?: null;
       } else {
           unset($validated['cpf']);
       }

       $validated['fone'] = preg_replace('/\D+/', '', (string) ($validated['fone'] ?? '')) ?: null;

       if ($request->hasFile('capa')) {
           $directory = 'backend/assets/img/profile';
           $fileName = 'profile-' . $user->id . '-' . Str::uuid() . '.' . strtolower((string) $request->file('capa')->getClientOriginalExtension());

           File::ensureDirectoryExists(public_path($directory));
           $request->file('capa')->move(public_path($directory), $fileName);

           $this->deletePreviousProfilePhoto($user->capa);

           $validated['capa'] = $directory . '/' . $fileName;
       } else {
           unset($validated['capa']);
       }

       $user->update($validated);
       $this->syncLinkedProfiles($user->fresh());

       return redirect()->route('admin.account.edit')->with('success', 'Seus dados foram atualizados com sucesso.');
   }

   private function markVisibleNotificationsAsRead($user): void
   {
       $query = Agendamento::query();

       if ($user->normalizedRole() === 'profissional') {
           $professional = $this->authenticatedProfessional();

           if (! $professional) {
               return;
           }

           $this->applyProfessionalAppointmentScope($query, $professional, $user);

           $query->whereDate('data_agendamento', '>=', now()->toDateString())
               ->whereNotIn('status', ['cancelado', 'concluido']);
       }

       $query->get()->each(function (Agendamento $notification) use ($user) {
           $this->markNotificationAsRead($notification, (int) $user->id);
       });
   }

   private function markNotificationAsRead(Agendamento $notification, int $userId): void
   {
       $readBy = collect($notification->notification_read_by ?? [])
           ->map(fn ($value) => (int) $value)
           ->push($userId)
           ->unique()
           ->values()
           ->all();

       $notification->forceFill([
           'notification_read_by' => $readBy,
       ])->save();
   }

   private function userCanReadNotification($user, Agendamento $notification): bool
   {
       if ($user->normalizedRole() !== 'profissional') {
           return true;
       }

       $professional = $this->authenticatedProfessional();

       return $professional && $this->appointmentMatchesProfessional($notification, $professional, $user);
   }

   private function applyProfessionalAppointmentScope($query, Professional $professional, $user): void
   {
       $nameCandidates = $this->professionalNameCandidates($professional, $user);

       $query->where(function ($scopedQuery) use ($professional, $nameCandidates) {
           $scopedQuery->where('profissional_id', $professional->id);

           foreach ($nameCandidates as $name) {
               $scopedQuery->orWhereRaw('LOWER(TRIM(medico)) = ?', [mb_strtolower(trim($name))]);
           }
       });
   }

   private function appointmentEndDateTime(Agendamento $agendamento): ?Carbon
   {
       if (! $agendamento->data_agendamento || ! $agendamento->horario) {
           return null;
       }

       return $agendamento->data_agendamento->copy()
           ->setTimeFromTimeString(substr((string) $agendamento->horario, 0, 5))
           ->addMinutes((int) ($agendamento->duracao_minutos ?: 30));
   }

   private function appointmentHasPassedEndTime(Agendamento $agendamento): bool
   {
       if (in_array((string) $agendamento->status, ['cancelado', 'concluido'], true)) {
           return false;
       }

       $endTime = $this->appointmentEndDateTime($agendamento);

       return $endTime ? $endTime->lessThanOrEqualTo(now()) : false;
   }

   private function appointmentMatchesProfessional(Agendamento $appointment, Professional $professional, $user): bool
   {
       if ((int) $appointment->professional_id === (int) $professional->id) {
           return true;
       }

       $appointmentProfessionalName = mb_strtolower(trim((string) ($appointment->professional?->nome ?: $appointment->medico)));

       if ($appointmentProfessionalName === '') {
           return false;
       }

       foreach ($this->professionalNameCandidates($professional, $user) as $nameCandidate) {
           if ($appointmentProfessionalName === mb_strtolower(trim($nameCandidate))) {
               return true;
           }
       }

       return false;
   }

   private function professionalNameCandidates(Professional $professional, $user): array
   {
       return collect([
           $professional->nome,
           $user?->full_name,
           trim((string) (($user?->nome ?? '') . ' ' . ($user?->sobrenome ?? ''))),
           $user?->nome,
       ])
           ->filter(fn ($value) => trim((string) $value) !== '')
           ->map(fn ($value) => trim((string) $value))
           ->unique()
           ->values()
           ->all();
   }

   private function resolveSafeRedirect(string $candidate, string $fallback): string
   {
       $applicationUrl = rtrim((string) config('app.url'), '/');

       if ($candidate === '') {
           return $fallback;
       }

       if (Str::startsWith($candidate, ['/'])) {
           return $candidate;
       }

       if ($applicationUrl !== '' && Str::startsWith($candidate, $applicationUrl)) {
           return $candidate;
       }

       return $fallback;
   }

   private function deletePreviousProfilePhoto(?string $path): void
   {
       $normalizedPath = ltrim(str_replace('\\', '/', (string) $path), '/');

       if ($normalizedPath === '') {
           return;
       }

       if (Storage::disk('public')->exists($normalizedPath)) {
           Storage::disk('public')->delete($normalizedPath);
       }

       if (is_file(public_path($normalizedPath))) {
           File::delete(public_path($normalizedPath));
       }
   }

   private function authenticatedProfessional(): ?Professional
   {
       $user = Auth::user();

       if (! $user || $user->normalizedRole() !== 'profissional') {
           return null;
       }

       $professional = Professional::query()
           ->where('user_id', $user->id)
           ->where('ativo', true)
           ->first();

       if ($professional) {
           return $professional;
       }

       $userCpf = preg_replace('/\D+/', '', (string) ($user->cpf ?? ''));

       if ($userCpf !== '') {
           $professional = Professional::query()
               ->where('ativo', true)
               ->get()
               ->first(function (Professional $item) use ($userCpf) {
                   return preg_replace('/\D+/', '', (string) ($item->cpf ?? '')) === $userCpf;
               });

           if ($professional) {
               return $professional;
           }
       }

       $fullName = mb_strtolower(trim((string) ($user->full_name ?? '')));

       if ($fullName === '') {
           return null;
       }

       return Professional::query()
           ->where('ativo', true)
           ->get()
           ->first(function (Professional $item) use ($fullName) {
               return mb_strtolower(trim((string) $item->nome)) === $fullName;
           });
   }

   private function syncLinkedProfiles($user): void
   {
       $professional = Professional::query()
           ->where('user_id', $user->id)
           ->first();

       if (! $professional) {
           return;
       }

       $professional->update([
           'nome' => $user->full_name,
           'cpf' => $user->cpf ?: $professional->cpf,
       ]);
   }
}
