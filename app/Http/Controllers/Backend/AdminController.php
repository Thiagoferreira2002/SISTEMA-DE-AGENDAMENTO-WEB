<?php

namespace App\Http\Controllers\Backend;

use App\Models\Agendamento;
use App\Models\Patient;
use App\Models\Professional;
use App\Http\Controllers\Controller;
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
           'finalizados' => route('admin.patients.history'),
       ];

       $appointmentsQuery = Agendamento::query();
       $activeAppointmentsQuery = Agendamento::query()->where(function ($query) {
           $query->whereNull('status')
               ->orWhereIn('status', ['pendente', 'confirmado']);
       });
       $completedAppointmentsQuery = Agendamento::query()->where('status', 'concluido');
       $upcomingAppointmentsQuery = Agendamento::query()
           ->with('professional')
           ->where(function ($query) {
               $query->whereDate('data_agendamento', '>', now()->toDateString())
                   ->orWhere(function ($todayQuery) {
                       $todayQuery->whereDate('data_agendamento', now()->toDateString())
                           ->where('horario', '>=', now()->format('H:i'));
                   });
           })
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
               $appointmentsQuery->where('professional_id', $professional->id);
               $activeAppointmentsQuery->where('professional_id', $professional->id);
               $completedAppointmentsQuery->where('professional_id', $professional->id);
               $upcomingAppointmentsQuery->where('professional_id', $professional->id);
               $dashboardLinks['confirmados'] = route('admin.doctor.queue');
               $dashboardLinks['complementar'] = route('admin.patients.history');
               $dashboardLinks['finalizados'] = route('admin.patients.history');
               $totalPacientes = null;
           } else {
               $appointmentsQuery->whereRaw('1 = 0');
               $activeAppointmentsQuery->whereRaw('1 = 0');
               $completedAppointmentsQuery->whereRaw('1 = 0');
               $upcomingAppointmentsQuery->whereRaw('1 = 0');
               $totalPacientes = null;
           }
       } else {
           $totalPacientes = Patient::count();
       }

       $totalAgendamentos = (clone $activeAppointmentsQuery)->count();
       $agendamentosPendentes = (clone $activeAppointmentsQuery)->where(function ($query) {
           $query->whereNull('status')->orWhere('status', 'pendente');
       })->count();
       $agendamentosConfirmados = (clone $activeAppointmentsQuery)->where('status', 'confirmado')->count();
       $agendamentosFinalizados = (clone $completedAppointmentsQuery)->count();
       $proximosAgendamentos = $upcomingAppointmentsQuery
           ->orderBy('data_agendamento')
           ->orderBy('horario')
           ->limit(6)
           ->get();

       return view('admin.dashboard', compact(
           'totalAgendamentos',
           'agendamentosPendentes',
           'agendamentosConfirmados',
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

       session(['navbar_notifications_seen_at' => now()->toIso8601String()]);

       $fallback = route('admin.agendamentos.confirmations');

       return redirect($this->resolveSafeRedirect($request->string('redirect_to')->toString(), $fallback));
   }

   public function editAccount(Request $request)
   {
       return view('admin.account.edit', [
           'user' => $request->user(),
       ]);
   }

   public function updateAccount(Request $request)
   {
       $user = $request->user();

       $validated = $request->validate([
           'nome' => ['required', 'string', 'max:255'],
           'sobrenome' => ['nullable', 'string', 'max:255'],
           'cpf' => ['nullable', 'string', 'size:14', Rule::unique('users', 'cpf')->ignore($user->id)],
           'fone' => ['nullable', 'string', 'max:20'],
           'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
           'capa' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
       ], [
           'cpf.size' => 'O CPF deve estar no formato 000.000.000-00.',
           'capa.image' => 'Selecione uma imagem válida para a foto do perfil.',
           'capa.mimes' => 'A foto do perfil deve ser JPG, PNG ou WEBP.',
           'capa.max' => 'A foto do perfil deve ter no máximo 2 MB.',
       ]);

       $validated['cpf'] = preg_replace('/\D+/', '', (string) ($validated['cpf'] ?? '')) ?: null;
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

       return redirect()->route('admin.account.edit')->with('success', 'Seus dados foram atualizados com sucesso.');
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
}
