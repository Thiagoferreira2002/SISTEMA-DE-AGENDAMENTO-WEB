<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Agendamento;
use App\Models\ClinicHour;
use App\Models\Insurance;
use App\Models\InsurancePlan;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\ProcedurePrice;
use App\Models\Professional;
use App\Models\Room;
use App\Models\Unit;
use App\Models\User;
use App\Traits\RecordsActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ClinicManagementController extends Controller
{
    use RecordsActivity;

    public function __construct()
    {
        $this->middleware('admin');
    }

    public function settingsIndex(): View
    {
        $cards = [
            [
                'title' => 'Horário da Clínica',
                'description' => 'Defina o horário de abertura e término da clínica para impedir agendamentos fora da janela de atendimento.',
                'route' => route('admin.settings.clinic-hours'),
                'icon' => 'fas fa-clock',
            ],
            [
                'title' => 'Profissionais de Saúde',
                'description' => 'Gerencie quem realiza os atendimentos com nome, especialidade principal, CPF, CRM, vínculo de agenda por dia/horário e cor de identificação na agenda geral.',
                'route' => route('admin.settings.professionals'),
                'icon' => 'fas fa-user-md',
            ],
            [
                'title' => 'Procedimentos (Serviços)',
                'description' => 'Mantenha a lista de tudo o que a clínica oferece, como consulta médica, eletrocardiograma, retorno e demais serviços prestados.',
                'route' => route('admin.settings.procedures'),
                'icon' => 'fas fa-notes-medical',
            ],
        ];

        if (Auth::user()?->canManageCadastrosBase()) {
            $cards[] = [
                'title' => 'Usuários e Permissões',
                'description' => 'Defina níveis de acesso como Admin, Recepcionista e Profissional, controle permissões para editar prontuários e excluir agendamentos, e acompanhe logs de atividade.',
                'route' => route('admin.settings.users'),
                'icon' => 'fas fa-users-cog',
            ];

            $cards[] = [
                'title' => 'Logs de Atividade',
                'description' => 'Consulte o histórico completo das alterações administrativas, filtre por CPF do usuário afetado e acompanhe os detalhes das mudanças.',
                'route' => route('admin.settings.activity-logs'),
                'icon' => 'fas fa-history',
            ];
        }

        if (Auth::user()?->isPrimaryAdmin()) {
            $cards[3]['description'] = 'Defina níveis de acesso como Admin, Recepcionista, Profissional e Gestor da Clínica, controle permissões por módulo e acompanhe logs de atividade.';
        }

        return view('admin.modules.settings.index', compact('cards'));
    }

    public function waitlist(): View
    {
        $waitlist = Agendamento::where('status', 'pendente')
            ->orderByDesc('prioridade')
            ->orderBy('data_limite_espera')
            ->orderBy('data_agendamento')
            ->orderBy('horario')
            ->get();

        return view('admin.modules.agendamentos.waitlist', compact('waitlist'));
    }

    public function scheduleBlocks(): View
    {
        $blockedSlots = collect([
            ['titulo' => 'Feriado Nacional', 'tipo' => 'Feriado', 'data' => now()->addDays(9)->format('d/m/Y'), 'periodo' => 'Dia inteiro', 'recorrencia' => 'Não recorrente', 'motivo' => 'Clínica fechada'],
            ['titulo' => 'Intervalo de Almoço', 'tipo' => 'Almoço', 'data' => now()->addDays(1)->format('d/m/Y'), 'periodo' => '12:00 às 13:30', 'recorrencia' => 'Todos os dias úteis', 'motivo' => 'Bloqueio operacional'],
            ['titulo' => 'Ausência médica', 'tipo' => 'Congresso', 'data' => now()->addDays(4)->format('d/m/Y'), 'periodo' => '08:00 às 11:00', 'recorrencia' => 'Evento único', 'motivo' => 'Treinamento externo'],
        ]);

        return view('admin.modules.agendamentos.blocks', compact('blockedSlots'));
    }

    public function clinicHours(): View
    {
        $setupWarning = null;
        $clinicHours = null;

        if ($this->hasTables(['clinic_hours'])) {
            $clinicHours = ClinicHour::query()->firstOrCreate([], [
                'opening_time' => '07:00:00',
                'closing_time' => '19:00:00',
                'lunch_start_time' => null,
                'lunch_end_time' => null,
            ]);
        } else {
            $setupWarning = 'Execute as migrations dos cadastros base para gerenciar o horário de funcionamento da clínica.';
        }

        return view('admin.modules.settings.clinic-hours', compact('clinicHours', 'setupWarning'));
    }

    public function updateClinicHours(Request $request): RedirectResponse
    {
        if (! $this->hasTables(['clinic_hours'])) {
            return redirect()->route('admin.settings.clinic-hours')->with('warning', 'Execute as migrations dos cadastros base para alterar o horário da clínica.');
        }

        $validated = $request->validate([
            'opening_time' => ['required', 'date_format:H:i'],
            'closing_time' => ['required', 'date_format:H:i', 'after:opening_time'],
            'lunch_start_time' => ['nullable', 'date_format:H:i'],
            'lunch_end_time' => ['nullable', 'date_format:H:i', 'after:lunch_start_time'],
        ], [
            'opening_time.required' => 'O campo horário de abertura é obrigatório.',
            'opening_time.date_format' => 'O campo horário de abertura deve estar no formato HH:MM.',
            'closing_time.required' => 'O campo horário de término é obrigatório.',
            'closing_time.date_format' => 'O campo horário de término deve estar no formato HH:MM.',
            'closing_time.after' => 'O horário de término deve ser depois do horário de abertura.',
            'lunch_start_time.date_format' => 'O campo início do almoço deve estar no formato HH:MM.',
            'lunch_end_time.date_format' => 'O campo término do almoço deve estar no formato HH:MM.',
            'lunch_end_time.after' => 'O término do almoço deve ser depois do início do almoço.',
        ]);

        if (filled($validated['lunch_start_time'] ?? null) xor filled($validated['lunch_end_time'] ?? null)) {
            return redirect()->route('admin.settings.clinic-hours')
                ->withErrors(['lunch_start_time' => 'Preencha o início e o término do almoço para ativar esse bloqueio.'])
                ->withInput();
        }

        if (filled($validated['lunch_start_time'] ?? null) && filled($validated['lunch_end_time'] ?? null)) {
            if ($validated['lunch_start_time'] <= $validated['opening_time'] || $validated['lunch_end_time'] >= $validated['closing_time']) {
                return redirect()->route('admin.settings.clinic-hours')
                    ->withErrors(['lunch_start_time' => 'O horário de almoço deve ficar dentro do horário de funcionamento da clínica.'])
                    ->withInput();
            }
        }

        $clinicHours = ClinicHour::query()->firstOrCreate([], [
            'opening_time' => '07:00:00',
            'closing_time' => '19:00:00',
            'lunch_start_time' => null,
            'lunch_end_time' => null,
        ]);

        $before = [
            'opening_time' => substr((string) $clinicHours->opening_time, 0, 5),
            'closing_time' => substr((string) $clinicHours->closing_time, 0, 5),
            'lunch_start_time' => $clinicHours->lunch_start_time ? substr((string) $clinicHours->lunch_start_time, 0, 5) : null,
            'lunch_end_time' => $clinicHours->lunch_end_time ? substr((string) $clinicHours->lunch_end_time, 0, 5) : null,
        ];

        $clinicHours->update([
            'opening_time' => $validated['opening_time'],
            'closing_time' => $validated['closing_time'],
            'lunch_start_time' => $validated['lunch_start_time'] ?? null,
            'lunch_end_time' => $validated['lunch_end_time'] ?? null,
        ]);

        $this->recordActivity('updated', $clinicHours, 'Horário de funcionamento da clínica atualizado.', [
            'submenu' => 'Horário da Clínica',
            'before' => $before,
            'after' => [
                'opening_time' => substr((string) $clinicHours->opening_time, 0, 5),
                'closing_time' => substr((string) $clinicHours->closing_time, 0, 5),
                'lunch_start_time' => $clinicHours->lunch_start_time ? substr((string) $clinicHours->lunch_start_time, 0, 5) : null,
                'lunch_end_time' => $clinicHours->lunch_end_time ? substr((string) $clinicHours->lunch_end_time, 0, 5) : null,
            ],
        ]);

        return redirect()->route('admin.settings.clinic-hours')->with('success', 'Horário da clínica atualizado com sucesso.');
    }

    private function clinicHoursWindow(): ?array
    {
        if (! $this->hasTables(['clinic_hours'])) {
            return null;
        }

        $clinicHours = ClinicHour::query()->first();

        if (! $clinicHours) {
            return null;
        }

        return [
            'opening_time' => substr((string) $clinicHours->opening_time, 0, 5),
            'closing_time' => substr((string) $clinicHours->closing_time, 0, 5),
            'lunch_start_time' => $clinicHours->lunch_start_time ? substr((string) $clinicHours->lunch_start_time, 0, 5) : null,
            'lunch_end_time' => $clinicHours->lunch_end_time ? substr((string) $clinicHours->lunch_end_time, 0, 5) : null,
        ];
    }

    private function adjustScheduleToClinicHours(int $dayOfWeek, string $startTime, string $endTime, ?string $breakStartTime, ?string $breakEndTime, ?array $clinicHoursWindow): ?array
    {
        $effectiveStart = $startTime;
        $effectiveEnd = $endTime;

        if ($clinicHoursWindow) {
            $effectiveStart = max($effectiveStart, $clinicHoursWindow['opening_time']);
            $effectiveEnd = min($effectiveEnd, $clinicHoursWindow['closing_time']);
        }

        if ($effectiveStart >= $effectiveEnd) {
            return null;
        }

        $effectiveBreakStart = null;
        $effectiveBreakEnd = null;

        if ($clinicHoursWindow && ! empty($clinicHoursWindow['lunch_start_time']) && ! empty($clinicHoursWindow['lunch_end_time'])) {
            $effectiveBreakStart = max($clinicHoursWindow['lunch_start_time'], $effectiveStart);
            $effectiveBreakEnd = min($clinicHoursWindow['lunch_end_time'], $effectiveEnd);
        } elseif ($breakStartTime && $breakEndTime) {
            $effectiveBreakStart = max($breakStartTime, $effectiveStart);
            $effectiveBreakEnd = min($breakEndTime, $effectiveEnd);
        }

        if ($effectiveBreakStart && $effectiveBreakEnd) {
            if (! ($effectiveStart < $effectiveBreakStart && $effectiveBreakStart < $effectiveBreakEnd && $effectiveBreakEnd < $effectiveEnd)) {
                $effectiveBreakStart = null;
                $effectiveBreakEnd = null;
            }
        }

        return [
            'day_of_week' => $dayOfWeek,
            'start_time' => $effectiveStart,
            'break_start_time' => $effectiveBreakStart,
            'break_end_time' => $effectiveBreakEnd,
            'end_time' => $effectiveEnd,
        ];
    }

    public function confirmations(Request $request): View
    {
        $globalSearch = trim($request->string('q')->toString());
        $cpfSearch = preg_replace('/\D+/', '', $globalSearch);
        $period = in_array($request->input('period'), ['dia', 'semana', 'mes'], true)
            ? $request->input('period')
            : '';

        $pendingBaseQuery = Agendamento::query()
            ->where(function ($query) {
                $query->where('status', 'pendente')
                    ->orWhereNull('status');
            });

        $authenticatedProfessional = $this->authenticatedProfessional();

        if ($authenticatedProfessional) {
            $applyProfessionalScope = function ($query) use ($authenticatedProfessional) {
                $query->where('professional_id', $authenticatedProfessional->id)
                    ->orWhere('medico', $authenticatedProfessional->nome);
            };

            $pendingBaseQuery->where($applyProfessionalScope);
        } elseif ($this->isProfessionalUser()) {
            $pendingBaseQuery->whereRaw('1 = 0');
        }

        if ($globalSearch !== '') {
            $applySearchFilter = function ($query) use ($globalSearch, $cpfSearch) {
                $query->where('nome', 'like', '%' . $globalSearch . '%')
                    ->orWhereDate('data_agendamento', $globalSearch);

                $dateSearch = preg_replace('/\D+/', '', $globalSearch);

                if ($dateSearch !== '') {
                    $query->orWhereRaw("DATE_FORMAT(data_agendamento, '%d%m%Y') like ?", ['%' . $dateSearch . '%'])
                        ->orWhereRaw("DATE_FORMAT(data_agendamento, '%Y%m%d') like ?", ['%' . $dateSearch . '%']);
                }

                if ($cpfSearch !== '') {
                    $query->orWhereHas('patient', function ($patientQuery) use ($cpfSearch) {
                        $patientQuery->whereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), '/', '') like ?", ['%' . $cpfSearch . '%']);
                    });
                }
            };

            $pendingBaseQuery->where($applySearchFilter);
        }

        if ($period === 'dia') {
            $pendingBaseQuery->whereDate('data_agendamento', now()->toDateString());
        }

        if ($period === 'semana') {
            $dateWindow = [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ];

            $pendingBaseQuery->whereBetween('data_agendamento', $dateWindow);
        }

        if ($period === 'mes') {
            $pendingBaseQuery->whereYear('data_agendamento', now()->year)
                ->whereMonth('data_agendamento', now()->month);
        }

        $appointments = $pendingBaseQuery
            ->orderBy('data_agendamento')
            ->orderBy('horario')
            ->get();

        $summary = [
            'pendentes' => $appointments->count(),
        ];

        return view('admin.modules.agendamentos.confirmations', compact('appointments', 'summary'));
    }

    private function isProfessionalUser(): bool
    {
        $user = Auth::user();

        return (bool) $user
            && $user->normalizedRole() === 'profissional';
    }

    private function authenticatedProfessional(): ?Professional
    {
        if (! $this->isProfessionalUser() || ! Schema::hasTable('professionals')) {
            return null;
        }

        $user = Auth::user();

        $professional = Professional::where('user_id', Auth::id())
            ->where('ativo', true)
            ->first();

        if ($professional) {
            return $professional;
        }

        $userCpf = preg_replace('/\D+/', '', (string) ($user?->cpf ?? ''));

        if ($userCpf !== '') {
            $professional = Professional::where('ativo', true)
                ->get()
                ->first(function (Professional $item) use ($userCpf) {
                    return preg_replace('/\D+/', '', (string) ($item->cpf ?? '')) === $userCpf;
                });

            if ($professional) {
                return $professional;
            }
        }

        $fullName = mb_strtolower(trim((string) ($user?->full_name ?? '')));

        if ($fullName === '') {
            return null;
        }

        return Professional::where('ativo', true)
            ->get()
            ->first(function (Professional $item) use ($fullName) {
                return mb_strtolower(trim((string) $item->nome)) === $fullName;
            });
    }

    public function promoteWaitlist(Agendamento $agendamento): RedirectResponse
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);

        $agendamento->update(['status' => 'confirmado']);
        $this->recordActivity('updated', $agendamento, 'Paciente promovido para agendamento ativo.', ['status' => 'confirmado']);

        return redirect()->route('admin.agendamentos.waitlist')->with('success', 'Paciente promovido para agendamento ativo.');
    }

    public function confirmAppointment(Agendamento $agendamento): RedirectResponse
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);

        $agendamento->update(['status' => 'confirmado']);
        $this->recordActivity('updated', $agendamento, 'Status do agendamento atualizado para confirmado.', ['status' => 'confirmado']);

        return redirect()->route('admin.agendamentos.confirmations')->with('success', 'Agendamento confirmado com sucesso. Ele agora aparece apenas na Agenda Geral.');
    }

    public function pendAppointment(Agendamento $agendamento): RedirectResponse
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);

        $agendamento->update(['status' => 'pendente']);
        $this->recordActivity('updated', $agendamento, 'Status do agendamento atualizado para pendente.', ['status' => 'pendente']);

        return redirect()->route('admin.agendamentos.confirmations')->with('success', 'Status do agendamento atualizado para pendente.');
    }

    public function cancelAppointment(Request $request, Agendamento $agendamento): RedirectResponse
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);

        $this->recordActivity('deleted', $agendamento, 'Agendamento excluído a partir da tela de confirmações.', [
            'nome' => $agendamento->nome,
            'status' => $agendamento->status,
        ]);
        $agendamento->delete();

        return redirect($request->input('return_to', route('admin.agendamentos.confirmations')))
            ->with('success', 'Agendamento cancelado com sucesso.');
    }

    private function ensureAuthenticatedProfessionalCanAccessAppointment(Agendamento $agendamento): void
    {
        $professional = $this->authenticatedProfessional();

        if (! $professional) {
            return;
        }

        $appointmentProfessionalName = mb_strtolower(trim((string) ($agendamento->professional?->nome ?: $agendamento->medico)));
        $authenticatedProfessionalName = mb_strtolower(trim((string) $professional->nome));
        $matchesProfessional = (string) $agendamento->professional_id === (string) $professional->id
            || ($appointmentProfessionalName !== '' && $appointmentProfessionalName === $authenticatedProfessionalName);

        abort_unless($matchesProfessional, 403);
    }

    public function patientHistory(Request $request): View
    {
        $perPage = 6;
        $period = in_array($request->input('period'), ['dia', 'semana', 'mes', 'ano'], true)
            ? $request->input('period')
            : '';
        $search = trim((string) $request->input('q'));
        $cpfSearch = preg_replace('/\D/', '', $search);
        $professionalFilter = (string) $request->input('professional_id', '');

        $historyQuery = Agendamento::where('status', 'concluido');

        $authenticatedProfessional = $this->authenticatedProfessional();

        if ($authenticatedProfessional) {
            $historyQuery->where(function ($query) use ($authenticatedProfessional) {
                $query->where('professional_id', $authenticatedProfessional->id)
                    ->orWhere('medico', $authenticatedProfessional->nome);
            });
        } elseif ($this->isProfessionalUser()) {
            $historyQuery->whereRaw('1 = 0');
        }

        if (! $authenticatedProfessional && $search !== '') {
            $historyQuery->where(function ($query) use ($search, $cpfSearch) {
                $query->where('nome', 'like', '%' . $search . '%');

                if ($cpfSearch !== '') {
                    $query->orWhereHas('patient', function ($patientQuery) use ($cpfSearch) {
                        $patientQuery->whereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), '/', '') like ?", ['%' . $cpfSearch . '%']);
                    });
                }
            });
        }

        if (! $authenticatedProfessional && $professionalFilter !== '') {
            $historyQuery->where('professional_id', (int) $professionalFilter);
        }

        if ($period === 'dia') {
            $historyQuery->whereDate('data_agendamento', now()->toDateString());
        }

        if ($period === 'semana') {
            $historyQuery->whereBetween('data_agendamento', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ]);
        }

        if ($period === 'mes') {
            $historyQuery->whereYear('data_agendamento', now()->year)
                ->whereMonth('data_agendamento', now()->month);
        }

        if ($period === 'ano') {
            $historyQuery->whereYear('data_agendamento', now()->year);
        }

        $history = $historyQuery
            ->orderByDesc('data_agendamento')
            ->orderByDesc('horario')
            ->paginate($perPage)
            ->withQueryString();

        $totalFinishedAppointments = $history->total();

        $history->getCollection()->transform(function ($item) {
            $item->medico_historico = $item->medico ?: 'Não informado';

            return $item;
        });

        $professionalOptions = ! $authenticatedProfessional && $this->hasTables(['professionals'])
            ? Professional::query()->where('ativo', true)->orderBy('nome')->get(['id', 'nome'])
            : collect();

        return view('admin.modules.patients.history', compact('history', 'period', 'totalFinishedAppointments', 'search', 'professionalFilter', 'professionalOptions', 'authenticatedProfessional'));
    }

    public function patientDocuments(): View
    {
        $patients = Patient::orderBy('nome')->get();
        $documentTypes = ['Exame Laboratorial', 'Laudo de Imagem', 'Documento Pessoal', 'Termo de Consentimento'];

        return view('admin.modules.patients.documents', compact('patients', 'documentTypes'));
    }

    public function doctorQueue(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $selectedDate = $request->filled('date') ? (string) $request->input('date') : '';
        $period = in_array($request->input('period'), ['dia', 'semana', 'mes'], true)
            ? $request->input('period')
            : 'dia';

        $queueQuery = Agendamento::with('professional')
            ->where(function ($query) {
                $query->whereIn('status', ['pendente', 'confirmado'])
                    ->orWhereNull('status');
            });

        $authenticatedProfessional = $this->authenticatedProfessional();

        if ($authenticatedProfessional) {
            $queueQuery->where(function ($query) use ($authenticatedProfessional) {
                $query->where('professional_id', $authenticatedProfessional->id)
                    ->orWhere('medico', $authenticatedProfessional->nome);
            });
        } elseif ($this->isProfessionalUser()) {
            $queueQuery->whereRaw('1 = 0');
        }

        if ($search !== '') {
            $queueQuery->where('nome', 'like', '%' . $search . '%');
        }

        if ($selectedDate !== '') {
            $queueQuery->whereDate('data_agendamento', $selectedDate);
        } elseif ($period === 'dia') {
            $queueQuery->whereDate('data_agendamento', now()->toDateString());
        } elseif ($period === 'semana') {
            $queueQuery->whereBetween('data_agendamento', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ]);
        } elseif ($period === 'mes') {
            $queueQuery->whereYear('data_agendamento', now()->year)
                ->whereMonth('data_agendamento', now()->month);
        }

        $queue = $queueQuery
            ->orderBy('data_agendamento')
            ->orderBy('horario')
            ->get()
            ->values()
            ->map(function ($item) {
                $item->profissional_fila = $item->professional?->nome ?: ($item->medico ?: 'Não informado');

                return $item;
            });

        $totalPatientsInQueue = $queue->count();

        return view('admin.modules.doctor.queue', compact('queue', 'period', 'search', 'selectedDate', 'totalPatientsInQueue'));
    }

    public function finishAppointment(Request $request, Agendamento $agendamento): RedirectResponse
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);

        if ($agendamento->status === 'concluido') {
            return redirect()
                ->route('admin.doctor.queue', $request->only(['q', 'date', 'period']))
                ->with('warning', 'Este atendimento já foi finalizado.');
        }

        if ($agendamento->status === 'cancelado') {
            return redirect()
                ->route('admin.doctor.queue', $request->only(['q', 'date', 'period']))
                ->with('warning', 'Não é possível finalizar um atendimento cancelado.');
        }

        $agendamento->update(['status' => 'concluido']);
        $this->recordActivity('updated', $agendamento, 'Atendimento finalizado e enviado para Serviços Finalizados.', ['status' => 'concluido']);

        return redirect()
            ->route('admin.doctor.queue', $request->only(['q', 'date', 'period']))
            ->with('success', 'Atendimento finalizado com sucesso. O registro já está em Serviços Finalizados.');
    }

    public function medicalRecords(): View
    {
        $patients = Patient::orderBy('nome')->get();
        $recentAppointments = Agendamento::orderByDesc('data_agendamento')
            ->orderByDesc('horario')
            ->limit(10)
            ->get();

        $timelineEntries = collect([
            [
                'titulo' => 'Anamnese',
                'timestamp' => now()->subMinutes(35)->format('d/m/Y H:i'),
                'conteudo' => 'Queixa principal de cefaleia recorrente. Histórico familiar de hipertensão. Sono irregular e sedentarismo.',
            ],
            [
                'titulo' => 'Exame Físico',
                'timestamp' => now()->subMinutes(20)->format('d/m/Y H:i'),
                'conteudo' => 'PA 12x8 mmHg, Peso 74 kg, Altura 1,70 m, IMC 25,6. Sem alterações relevantes ao exame físico.',
            ],
            [
                'titulo' => 'Evolução Clínica',
                'timestamp' => now()->subMinutes(5)->format('d/m/Y H:i'),
                'conteudo' => 'Paciente orientado sobre higiene do sono e atividade física. Solicitado retorno em 30 dias.',
            ],
        ]);

        $examData = [
            'pressao' => '12x8',
            'peso' => 74,
            'altura' => 1.70,
        ];
        $examData['imc'] = number_format($examData['peso'] / ($examData['altura'] * $examData['altura']), 1, ',', '.');

        $textTemplates = [
            'Exame físico sem alterações.',
            'Paciente orientado e sem sinais de alarme.',
            'Mantida conduta clínica e retorno programado.',
        ];

        $accessLog = collect([
            ['usuario' => 'Dr. Marcos Lima', 'perfil' => 'Médico', 'data' => now()->subMinutes(4)->format('d/m/Y H:i')],
            ['usuario' => 'Recepção 01', 'perfil' => 'Recepção', 'data' => now()->subHour()->format('d/m/Y H:i')],
            ['usuario' => 'Dra. Helena Souza', 'perfil' => 'Médico', 'data' => now()->subDay()->format('d/m/Y H:i')],
        ]);

        return view('admin.modules.doctor.records', compact('patients', 'recentAppointments', 'timelineEntries', 'examData', 'textTemplates', 'accessLog'));
    }

    public function prescriptions(): View
    {
        $patients = Patient::orderBy('nome')->limit(10)->get();
        $templates = ['Receita simples', 'Pedido de exame', 'Renovação de tratamento', 'Encaminhamento'];

        $medications = collect([
            ['nome' => 'Paracetamol 750mg', 'principio_ativo' => 'Paracetamol', 'posologia' => 'Tomar 1 comprimido de 8 em 8 horas'],
            ['nome' => 'Amoxicilina 500mg', 'principio_ativo' => 'Amoxicilina', 'posologia' => 'Tomar 1 cápsula de 8 em 8 horas por 7 dias'],
            ['nome' => 'Ibuprofeno 600mg', 'principio_ativo' => 'Ibuprofeno', 'posologia' => 'Tomar 1 comprimido de 12 em 12 horas após as refeições'],
            ['nome' => 'Loratadina 10mg', 'principio_ativo' => 'Loratadina', 'posologia' => 'Tomar 1 comprimido ao dia'],
        ]);

        $favoriteCombos = collect([
            ['nome' => 'Kit Gripe', 'itens' => 'Paracetamol + Loratadina + Hidratação oral'],
            ['nome' => 'Pós-consulta clínica', 'itens' => 'Vitamina D + retorno em 30 dias'],
        ]);

        $specialPrescriptionGuidance = [
            'fornecedor' => 'Farmácia Central LTDA',
            'comprador' => 'Paciente ou responsável legal',
        ];

        return view('admin.modules.doctor.prescriptions', compact('patients', 'templates', 'medications', 'favoriteCombos', 'specialPrescriptionGuidance'));
    }

    public function reports(): View
    {
        $recentAppointments = Agendamento::orderByDesc('data_agendamento')
            ->orderByDesc('horario')
            ->limit(10)
            ->get();

        $reportTemplates = ['Atestado médico', 'Laudo clínico', 'Declaração de comparecimento', 'Resumo de atendimento'];

        $cidCatalog = collect([
            ['codigo' => 'J11', 'descricao' => 'Influenza (gripe), vírus não identificado'],
            ['codigo' => 'I10', 'descricao' => 'Hipertensão essencial (primária)'],
            ['codigo' => 'M54.5', 'descricao' => 'Dor lombar baixa'],
            ['codigo' => 'R51', 'descricao' => 'Cefaleia'],
        ]);

        $digitalSignatureInfo = [
            'status' => 'Pronto para integração',
            'provider' => 'ICP-Brasil / certificado A1 ou A3',
        ];

        return view('admin.modules.doctor.reports', compact('recentAppointments', 'reportTemplates', 'cidCatalog', 'digitalSignatureInfo'));
    }

    public function professionals(Request $request): View
    {
        $setupWarning = null;
        $professionals = collect();
        $professionalUserSearch = trim((string) $request->input('professional_user_search'));

        if ($this->hasTables(['professionals', 'professional_schedules'])) {
            $professionalsQuery = Professional::with(['schedules', 'user'])->orderBy('nome');

            if ($professionalUserSearch !== '') {
                $professionalsQuery->whereHas('user', function ($query) use ($professionalUserSearch) {
                    $query->where('nome', 'like', '%' . $professionalUserSearch . '%')
                        ->orWhere('sobrenome', 'like', '%' . $professionalUserSearch . '%')
                        ->orWhereRaw("CONCAT(COALESCE(nome, ''), ' ', COALESCE(sobrenome, '')) like ?", ['%' . $professionalUserSearch . '%'])
                        ->orWhere('email', 'like', '%' . $professionalUserSearch . '%');
                });
            }

            $professionals = $professionalsQuery->get();
            $clinicHoursWindow = $this->clinicHoursWindow();

            $professionals->each(function (Professional $professional) use ($clinicHoursWindow) {
                $professional->display_schedules = $professional->schedules
                    ->map(fn ($schedule) => $this->adjustScheduleToClinicHours(
                        $schedule->day_of_week,
                        substr((string) $schedule->start_time, 0, 5),
                        substr((string) $schedule->end_time, 0, 5),
                        $schedule->break_start_time ? substr((string) $schedule->break_start_time, 0, 5) : null,
                        $schedule->break_end_time ? substr((string) $schedule->break_end_time, 0, 5) : null,
                        $clinicHoursWindow,
                    ))
                    ->filter()
                    ->values();
            });
        } else {
            $setupWarning = 'Os cadastros de profissionais ainda dependem das novas migrations. Execute as migrations para habilitar o módulo completo.';
        }

        $availableUsers = collect();

        if (Schema::hasColumn('users', 'role')) {
            $availableUsers = User::whereIn('role', ['profissional', 'medico'])->orderBy('nome')->get();

            if ($availableUsers->isEmpty()) {
                $setupWarning = $setupWarning
                    ? $setupWarning . ' Nenhum usuário com papel Profissional está disponível para vínculo.'
                    : 'Cadastre primeiro um usuário com papel Profissional em Usuários e Permissões para então criar um novo profissional.';
            }
        } else {
            $setupWarning = $setupWarning
                ? $setupWarning . ' A coluna de papel de acesso ainda não está disponível para validar o vínculo com profissionais.'
                : 'Execute as migrations de papéis e permissões para vincular profissionais a usuários com papel Profissional.';
        }

        $weekDays = $this->weekDays();
        $professionalCouncils = $this->professionalCouncils();

        $formatCpf = function ($value) {
            $digits = preg_replace('/\D/', '', (string) $value);

            if (strlen($digits) === 11) {
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
            }

            return $value ?: '';
        };

        return view('admin.modules.settings.professionals', compact('professionals', 'availableUsers', 'weekDays', 'setupWarning', 'formatCpf', 'professionalCouncils', 'professionalUserSearch', 'clinicHoursWindow'));
    }

    public function storeProfessional(Request $request): RedirectResponse
    {
        if (! $this->hasTables(['professionals', 'professional_schedules'])) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Execute as migrations dos cadastros base para cadastrar profissionais.');
        }

        if (! Schema::hasColumn('users', 'role')) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Execute as migrations de papéis e permissões antes de cadastrar profissionais vinculados a usuários com papel Profissional.');
        }

        $linkedUser = User::find($request->input('user_id'));
        $normalizedCpf = preg_replace('/\D/', '', (string) ($linkedUser?->cpf));

        $request->merge([
            'cpf' => $normalizedCpf !== '' ? $normalizedCpf : null,
        ]);

        $professionalCouncils = $this->professionalCouncils();

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('professionals', 'user_id'),
                function ($attribute, $value, $fail) {
                    $linkedUser = User::find($value);

                    if (! $linkedUser || ! in_array($linkedUser->role, ['profissional', 'medico'], true)) {
                        $fail('Selecione um usuário com papel Profissional para vincular ao profissional.');
                    }
                },
            ],
            'especialidade_principal' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:20|unique:professionals,cpf',
            'registro_tipo' => ['required', 'string', 'max:20', Rule::in(array_keys($professionalCouncils))],
            'registro_numero' => 'required|string|max:50',
            'agenda_color' => 'required|string|max:20',
            'schedule_day_of_week' => 'nullable|array',
            'schedule_day_of_week.*' => ['nullable', 'string', Rule::in(['weekdays', '1', '2', '3', '4', '5', '6', '7'])],
            'schedule_start_time' => 'nullable|array',
            'schedule_start_time.*' => 'nullable|date_format:H:i',
            'schedule_end_time' => 'nullable|array',
            'schedule_end_time.*' => 'nullable|date_format:H:i',
        ], [
            'user_id.required' => 'Selecione um usuário com papel Profissional para cadastrar o profissional.',
            'user_id.unique' => 'Este usuário já está vinculado a outro profissional.',
            'cpf.unique' => 'O profissional nao foi salvo porque o CPF deste usuario ja esta vinculado a outro profissional cadastrado.',
            'registro_tipo.in' => 'Selecione um conselho profissional válido.',
        ]);

        $this->validateScheduleRows(
            $request->input('schedule_day_of_week', []),
            $request->input('schedule_start_time', []),
            $request->input('schedule_end_time', []),
            $this->clinicHoursWindow()
        );

        $linkedUser = User::findOrFail($request->input('user_id'));
        $professionalName = trim(($linkedUser->nome ?? '') . ' ' . ($linkedUser->sobrenome ?? ''));

        $professional = Professional::create([
            'user_id' => $linkedUser->id,
            'nome' => $professionalName,
            'especialidade_principal' => $request->especialidade_principal,
            'cpf' => $request->cpf,
            'registro_tipo' => strtoupper($request->registro_tipo),
            'registro_numero' => $request->registro_numero,
            'agenda_color' => $request->agenda_color,
            'ativo' => true,
        ]);

        $this->syncSchedules(
            $professional,
            $request->input('schedule_day_of_week', []),
            $request->input('schedule_start_time', []),
            $request->input('schedule_end_time', [])
        );

        $this->recordActivity('created', $professional, 'Profissional de saúde cadastrado.', [
            'submenu' => 'Profissionais de Saúde',
            'target_user' => [
                'id' => $professional->id,
                'nome' => $professionalName,
                'cpf' => $professional->cpf,
                'email' => $linkedUser->email,
            ],
            'user_id' => $linkedUser->id,
            'user_email' => $linkedUser->email,
            'nome' => $professionalName,
            'especialidade' => $professional->especialidade_principal,
            'registro' => $professional->registro_completo,
        ]);

        return redirect()->route('admin.settings.professionals')->with('success', 'Profissional cadastrado com sucesso.');
    }

    public function updateProfessional(Request $request, Professional $professional): RedirectResponse
    {
        if (! $this->hasTables(['professionals', 'professional_schedules'])) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Execute as migrations dos cadastros base para editar profissionais.');
        }

        if (! Schema::hasColumn('users', 'role')) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Execute as migrations de papéis e permissões antes de editar profissionais vinculados.');
        }

        $linkedUser = User::find($request->input('user_id'));
        $normalizedCpf = preg_replace('/\D/', '', (string) ($linkedUser?->cpf));

        $request->merge([
            'cpf' => $normalizedCpf !== '' ? $normalizedCpf : null,
        ]);

        $professionalCouncils = $this->professionalCouncils();

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('professionals', 'user_id')->ignore($professional->id),
                function ($attribute, $value, $fail) {
                    $linkedUser = User::find($value);

                    if (! $linkedUser || ! in_array($linkedUser->role, ['profissional', 'medico'], true)) {
                        $fail('Selecione um usuário com papel Profissional para vincular ao profissional.');
                    }
                },
            ],
            'especialidade_principal' => 'required|string|max:255',
            'cpf' => ['nullable', 'string', 'max:20', Rule::unique('professionals', 'cpf')->ignore($professional->id)],
            'registro_tipo' => ['required', 'string', 'max:20', Rule::in(array_keys($professionalCouncils))],
            'registro_numero' => 'required|string|max:50',
            'agenda_color' => 'required|string|max:20',
            'schedule_day_of_week' => 'nullable|array',
            'schedule_day_of_week.*' => ['nullable', 'string', Rule::in(['weekdays', '1', '2', '3', '4', '5', '6', '7'])],
            'schedule_start_time' => 'nullable|array',
            'schedule_start_time.*' => 'nullable|date_format:H:i',
            'schedule_end_time' => 'nullable|array',
            'schedule_end_time.*' => 'nullable|date_format:H:i',
        ], [
            'user_id.required' => 'Selecione um usuário com papel Profissional para vincular ao profissional.',
            'user_id.unique' => 'Este usuário já está vinculado a outro profissional.',
            'cpf.unique' => 'O profissional nao foi salvo porque o CPF deste usuario ja esta vinculado a outro profissional cadastrado.',
            'registro_tipo.in' => 'Selecione um conselho profissional válido.',
        ]);

        $this->validateScheduleRows(
            $request->input('schedule_day_of_week', []),
            $request->input('schedule_start_time', []),
            $request->input('schedule_end_time', []),
            $this->clinicHoursWindow()
        );

        $previousValues = [
            'user_id' => $professional->user_id,
            'nome' => $professional->nome,
            'especialidade_principal' => $professional->especialidade_principal,
            'cpf' => $professional->cpf,
            'registro_tipo' => $professional->registro_tipo,
            'registro_numero' => $professional->registro_numero,
            'agenda_color' => $professional->agenda_color,
            'schedules' => $professional->schedules->map(fn ($schedule) => [
                'day_of_week' => $schedule->day_of_week,
                'start_time' => $schedule->start_time,
                'break_start_time' => $schedule->break_start_time,
                'break_end_time' => $schedule->break_end_time,
                'end_time' => $schedule->end_time,
            ])->values()->all(),
        ];

        $linkedUser = User::findOrFail($request->input('user_id'));
        $professionalName = trim(($linkedUser->nome ?? '') . ' ' . ($linkedUser->sobrenome ?? ''));

        $professional->update([
            'user_id' => $linkedUser->id,
            'nome' => $professionalName,
            'especialidade_principal' => $request->especialidade_principal,
            'cpf' => $request->cpf,
            'registro_tipo' => strtoupper($request->registro_tipo),
            'registro_numero' => $request->registro_numero,
            'agenda_color' => $request->agenda_color,
        ]);

        $professional->schedules()->delete();
        $this->syncSchedules(
            $professional,
            $request->input('schedule_day_of_week', []),
            $request->input('schedule_start_time', []),
            $request->input('schedule_end_time', [])
        );

        $professional->load('schedules');

        $this->recordActivity('updated', $professional, 'Profissional de saúde atualizado.', [
            'submenu' => 'Profissionais de Saúde',
            'target_user' => [
                'id' => $professional->id,
                'nome' => $professional->nome,
                'cpf' => $professional->cpf,
                'email' => $linkedUser->email,
            ],
            'before' => $previousValues,
            'after' => [
                'user_id' => $professional->user_id,
                'nome' => $professional->nome,
                'especialidade_principal' => $professional->especialidade_principal,
                'cpf' => $professional->cpf,
                'registro_tipo' => $professional->registro_tipo,
                'registro_numero' => $professional->registro_numero,
                'agenda_color' => $professional->agenda_color,
                'schedules' => $professional->schedules->map(fn ($schedule) => [
                    'day_of_week' => $schedule->day_of_week,
                    'start_time' => $schedule->start_time,
                    'break_start_time' => $schedule->break_start_time,
                    'break_end_time' => $schedule->break_end_time,
                    'end_time' => $schedule->end_time,
                ])->values()->all(),
            ],
        ]);

        return redirect()->route('admin.settings.professionals')->with('success', 'Profissional atualizado com sucesso.');
    }

    public function destroyProfessional(Professional $professional): RedirectResponse
    {
        if (! $this->hasTables(['professionals'])) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Execute as migrations dos cadastros base para excluir profissionais.');
        }

        $professional->loadMissing('user');

        $this->recordActivity('deleted', $professional, 'Profissional de saúde removido.', [
            'submenu' => 'Profissionais de Saúde',
            'target_user' => [
                'id' => $professional->id,
                'nome' => $professional->nome,
                'cpf' => $professional->cpf,
                'email' => $professional->user?->email,
            ],
            'before' => [
                'user_id' => $professional->user_id,
                'nome' => $professional->nome,
                'especialidade_principal' => $professional->especialidade_principal,
                'cpf' => $professional->cpf,
                'registro_tipo' => $professional->registro_tipo,
                'registro_numero' => $professional->registro_numero,
            ],
        ]);

        try {
            $professional->delete();
        } catch (\Throwable $exception) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Não foi possível excluir este profissional porque ele possui vínculos ativos no sistema.');
        }

        return redirect()->route('admin.settings.professionals')->with('success', 'Profissional excluído com sucesso.');
    }

    public function insurance(): View
    {
        $setupWarning = null;
        $insurances = collect();
        $procedures = collect();

        if ($this->hasTables(['insurances', 'insurance_plans', 'procedure_prices', 'procedures'])) {
            $insurances = Insurance::with(['plans', 'procedurePrices.procedure'])->orderBy('nome')->get();
            $procedures = Procedure::orderBy('nome')->get();
        } else {
            $setupWarning = 'Os cadastros de convênios e planos ainda dependem das novas migrations. Execute as migrations para habilitar o módulo completo.';
        }

        return view('admin.modules.settings.insurance', compact('insurances', 'procedures', 'setupWarning'));
    }

    public function storeInsurance(Request $request): RedirectResponse
    {
        if (! $this->hasTables(['insurances'])) {
            return redirect()->route('admin.settings.insurance')->with('warning', 'Execute as migrations dos cadastros base para cadastrar convênios.');
        }

        $request->validate([
            'nome' => 'required|string|max:255|unique:insurances,nome',
            'ans' => 'nullable|string|max:20',
            'cnpj' => 'nullable|string|max:20',
            'requires_guide' => 'nullable|boolean',
            'requires_authorization' => 'nullable|boolean',
        ]);

        $insurance = Insurance::create([
            'nome' => $request->nome,
            'ans' => $request->ans,
            'cnpj' => $request->cnpj,
            'requires_guide' => $request->boolean('requires_guide'),
            'requires_authorization' => $request->boolean('requires_authorization'),
            'ativo' => true,
        ]);

        $this->recordActivity('created', $insurance, 'Convênio cadastrado.', ['nome' => $insurance->nome]);

        return redirect()->route('admin.settings.insurance')->with('success', 'Convênio cadastrado com sucesso.');
    }

    public function storeInsurancePlan(Request $request): RedirectResponse
    {
        if (! $this->hasTables(['insurances', 'insurance_plans'])) {
            return redirect()->route('admin.settings.insurance')->with('warning', 'Execute as migrations dos cadastros base para cadastrar planos.');
        }

        $request->validate([
            'insurance_id' => 'required|exists:insurances,id',
            'nome' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:50',
        ]);

        $plan = InsurancePlan::create([
            'insurance_id' => $request->insurance_id,
            'nome' => $request->nome,
            'codigo' => $request->codigo,
            'ativo' => true,
        ]);

        $this->recordActivity('created', $plan, 'Plano de convênio cadastrado.', ['insurance_id' => $plan->insurance_id]);

        return redirect()->route('admin.settings.insurance')->with('success', 'Plano cadastrado com sucesso.');
    }

    public function storeProcedurePrice(Request $request): RedirectResponse
    {
        if (! $this->hasTables(['insurances', 'insurance_plans', 'procedures', 'procedure_prices'])) {
            return redirect()->route('admin.settings.insurance')->with('warning', 'Execute as migrations dos cadastros base para configurar tabelas de preços.');
        }

        $request->validate([
            'procedure_id' => 'required|exists:procedures,id',
            'insurance_id' => 'required|exists:insurances,id',
            'insurance_plan_id' => 'nullable|exists:insurance_plans,id',
            'valor' => 'required|numeric|min:0',
        ]);

        $price = ProcedurePrice::updateOrCreate(
            [
                'procedure_id' => $request->procedure_id,
                'insurance_id' => $request->insurance_id,
                'insurance_plan_id' => $request->input('insurance_plan_id'),
            ],
            ['valor' => $request->valor]
        );

        $this->recordActivity('updated', $price, 'Tabela de preço configurada para convênio.', [
            'procedure_id' => $price->procedure_id,
            'insurance_id' => $price->insurance_id,
            'insurance_plan_id' => $price->insurance_plan_id,
            'valor' => $price->valor,
        ]);

        return redirect()->route('admin.settings.insurance')->with('success', 'Preço do procedimento vinculado ao convênio com sucesso.');
    }

    public function procedures(): View
    {
        $setupWarning = null;
        $procedures = collect();
        $professionalOptions = collect();
        $selectedProfessionalId = request()->integer('professional_filter');

        if ($this->hasTables(['procedures', 'procedure_prices'])) {
            if ($this->hasTables(['professionals'])) {
                $professionalOptions = Professional::where('ativo', true)
                    ->orderBy('nome')
                    ->get(['id', 'nome', 'especialidade_principal']);
            }

            $procedureQuery = Procedure::with(['professional', 'prices.insurance', 'prices.plan'])
                ->orderBy('nome');

            if ($selectedProfessionalId && $this->hasTables(['professionals'])) {
                $procedureQuery->where('professional_id', $selectedProfessionalId);
            }

            $procedures = $procedureQuery
                ->paginate(6)
                ->withQueryString();
        } else {
            $setupWarning = 'Os cadastros de procedimentos ainda dependem das novas migrations. Execute as migrations para habilitar o módulo completo.';
        }

        return view('admin.modules.settings.procedures', compact('procedures', 'setupWarning', 'professionalOptions', 'selectedProfessionalId'));
    }

    public function storeProcedure(Request $request): RedirectResponse
    {
        if (! $this->hasTables(['procedures'])) {
            return redirect()->route('admin.settings.procedures')->with('warning', 'Execute as migrations dos cadastros base para cadastrar procedimentos.');
        }

        $durationOptions = range(15, 180, 15);

        $request->validate([
            'nome' => 'required|string|max:255|unique:procedures,nome',
            'duracao_minutos' => ['required', 'integer', Rule::in($durationOptions)],
            'professional_id' => $this->hasTables(['professionals']) ? 'required|exists:professionals,id' : 'nullable',
        ], [
            'nome.unique' => 'Já existe um procedimento cadastrado com esse nome.',
        ]);

        $procedure = Procedure::create([
            'professional_id' => $request->integer('professional_id'),
            'nome' => $request->nome,
            'duracao_minutos' => $request->duracao_minutos,
            'codigo_tuss' => null,
            'valor_particular' => 0,
            'ativo' => true,
        ]);

        $this->recordActivity('created', $procedure, 'Procedimento cadastrado.', [
            'submenu' => 'Procedimentos',
            'before' => [],
            'after' => [
                'professional_id' => $procedure->professional_id,
                'nome' => $procedure->nome,
                'duracao_minutos' => $procedure->duracao_minutos,
                'status' => $procedure->ativo ? 'ativo' : 'cancelado',
            ],
        ]);

        return redirect()->route('admin.settings.procedures')->with('success', 'Procedimento cadastrado com sucesso.');
    }

    public function updateProcedure(Request $request, Procedure $procedure): RedirectResponse
    {
        if (! $this->hasTables(['procedures'])) {
            return redirect()->route('admin.settings.procedures')->with('warning', 'Execute as migrations dos cadastros base para editar procedimentos.');
        }

        $durationOptions = range(15, 180, 15);

        $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('procedures', 'nome')->ignore($procedure->id)],
            'duracao_minutos' => ['required', 'integer', Rule::in($durationOptions)],
            'professional_id' => $this->hasTables(['professionals']) ? 'required|exists:professionals,id' : 'nullable',
        ], [
            'nome.unique' => 'Já existe um procedimento cadastrado com esse nome.',
        ]);

        $before = [
            'professional_id' => $procedure->professional_id,
            'nome' => $procedure->nome,
            'duracao_minutos' => $procedure->duracao_minutos,
            'status' => $procedure->ativo ? 'ativo' : 'cancelado',
        ];

        $procedure->update([
            'professional_id' => $request->integer('professional_id'),
            'nome' => $request->nome,
            'duracao_minutos' => $request->duracao_minutos,
        ]);

        $this->recordActivity('updated', $procedure, 'Procedimento atualizado.', [
            'submenu' => 'Procedimentos',
            'before' => $before,
            'after' => [
                'professional_id' => $procedure->professional_id,
                'nome' => $procedure->nome,
                'duracao_minutos' => $procedure->duracao_minutos,
                'status' => $procedure->ativo ? 'ativo' : 'cancelado',
            ],
        ]);

        return redirect()->route('admin.settings.procedures')->with('success', 'Procedimento atualizado com sucesso.');
    }

    public function toggleProcedureStatus(Procedure $procedure): RedirectResponse
    {
        if (! $this->hasTables(['procedures'])) {
            return redirect()->route('admin.settings.procedures')->with('warning', 'Execute as migrations dos cadastros base para alterar o status dos procedimentos.');
        }

        $previousStatus = $procedure->ativo ? 'ativo' : 'cancelado';
        $procedure->update(['ativo' => ! $procedure->ativo]);
        $newStatus = $procedure->ativo ? 'ativo' : 'cancelado';

        $this->recordActivity('updated', $procedure, 'Status do procedimento alterado.', [
            'submenu' => 'Procedimentos',
            'before' => ['status' => $previousStatus],
            'after' => ['status' => $newStatus],
        ]);

        return redirect()->route('admin.settings.procedures')->with('success', 'Status do procedimento atualizado com sucesso.');
    }

    public function destroyProcedure(Procedure $procedure): RedirectResponse
    {
        if (! $this->hasTables(['procedures'])) {
            return redirect()->route('admin.settings.procedures')->with('warning', 'Execute as migrations dos cadastros base para excluir procedimentos.');
        }

        $this->recordActivity('deleted', $procedure, 'Procedimento removido.', [
            'submenu' => 'Procedimentos',
            'before' => [
                'professional_id' => $procedure->professional_id,
                'nome' => $procedure->nome,
                'duracao_minutos' => $procedure->duracao_minutos,
                'status' => $procedure->ativo ? 'ativo' : 'cancelado',
            ],
        ]);

        $procedure->delete();

        return redirect()->route('admin.settings.procedures')->with('success', 'Procedimento excluído com sucesso.');
    }

    public function units(): View
    {
        $setupWarning = null;
        $units = collect();

        if ($this->hasTables(['units', 'rooms'])) {
            $units = Unit::with('rooms')->orderBy('nome')->get();
        } else {
            $setupWarning = 'Os cadastros de unidades e salas ainda dependem das novas migrations. Execute as migrations para habilitar o módulo completo.';
        }

        return view('admin.modules.settings.units', compact('units', 'setupWarning'));
    }

    public function storeUnit(Request $request): RedirectResponse
    {
        if (! $this->hasTables(['units'])) {
            return redirect()->route('admin.settings.units')->with('warning', 'Execute as migrations dos cadastros base para cadastrar unidades.');
        }

        $request->validate([
            'nome' => 'required|string|max:255|unique:units,nome',
            'endereco' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $unit = Unit::create([
            'nome' => $request->nome,
            'endereco' => $request->endereco,
            'telefone' => $request->telefone,
            'email' => $request->email,
            'ativo' => true,
        ]);

        $this->recordActivity('created', $unit, 'Unidade de atendimento cadastrada.', ['nome' => $unit->nome]);

        return redirect()->route('admin.settings.units')->with('success', 'Unidade cadastrada com sucesso.');
    }

    public function storeRoom(Request $request): RedirectResponse
    {
        if (! $this->hasTables(['units', 'rooms'])) {
            return redirect()->route('admin.settings.units')->with('warning', 'Execute as migrations dos cadastros base para cadastrar salas.');
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'nome' => 'required|string|max:255',
        ]);

        $room = Room::create([
            'unit_id' => $request->unit_id,
            'nome' => $request->nome,
            'ativo' => true,
        ]);

        $this->recordActivity('created', $room, 'Sala cadastrada para unidade.', ['unit_id' => $room->unit_id]);

        return redirect()->route('admin.settings.units')->with('success', 'Sala cadastrada com sucesso.');
    }

    public function usersPermissions(Request $request): View
    {
        $this->ensureCadastrosBaseManagerAccess();

        $userSearch = trim((string) $request->input('cpf_search'));
        $cpfSearch = preg_replace('/\D/', '', $userSearch);
        $roleFilter = in_array($request->input('role_filter'), ['recepcionista', 'profissional', 'gestor_clinica'], true)
            ? $request->input('role_filter')
            : '';

        $usersQuery = User::query()
            ->orderByRaw("CASE WHEN nivel = 'admin' OR role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('nome');

        if ($userSearch !== '') {
            $usersQuery->where(function ($query) use ($userSearch, $cpfSearch) {
                if ($cpfSearch !== '') {
                    $query->orWhere('cpf', 'like', '%' . $cpfSearch . '%');
                }

                $query->orWhere('nome', 'like', '%' . $userSearch . '%')
                    ->orWhere('sobrenome', 'like', '%' . $userSearch . '%')
                    ->orWhereRaw("CONCAT(COALESCE(nome, ''), ' ', COALESCE(sobrenome, '')) like ?", ['%' . $userSearch . '%']);
            });
        }

        if ($roleFilter !== '') {
            $usersQuery->where(function ($query) use ($roleFilter) {
                $query->where('role', $roleFilter);

                if ($roleFilter === 'profissional') {
                    $query->orWhere('role', 'medico');
                }
            });
        }

        $users = $usersQuery->paginate(6, ['*'], 'users_page')->withQueryString();
        $roles = ['recepcionista', 'profissional', 'gestor_clinica'];
        $availablePermissions = User::submenuPermissionLabels();

        $setupWarning = null;

        if (! Schema::hasColumn('users', 'role') || ! Schema::hasColumn('users', 'permissions')) {
            $setupWarning = 'A configuração de papéis e acessos por módulo depende das migrations de perfis e permissões. A listagem funciona, mas é necessário executar as migrations antes de editar os papéis.';
        }

        return view('admin.modules.settings.users', compact('users', 'roles', 'availablePermissions', 'setupWarning', 'userSearch', 'roleFilter'));
    }

    public function activityLogs(Request $request): View
    {
        $this->ensureCadastrosBaseManagerAccess();

        $activityLogs = collect();
        $subjectDisplayNames = collect();
        $affectedUserCpfSearch = preg_replace('/\D/', '', (string) $request->input('affected_user_cpf'));
        $activityDateSearch = trim((string) $request->input('activity_date'));
        $actionTypeSearch = in_array($request->input('action_type'), ['created', 'updated', 'deleted'], true)
            ? $request->input('action_type')
            : '';

        if (Schema::hasTable('activity_logs')) {
            $activityLogsQuery = ActivityLog::with('user')->latest();

            if ($actionTypeSearch !== '') {
                $activityLogsQuery->where('action', $actionTypeSearch);
            }

            if ($activityDateSearch !== '') {
                $activityLogsQuery->whereDate('created_at', $activityDateSearch);
            }

            if ($affectedUserCpfSearch !== '') {
                $matchingUserIds = User::query()
                    ->where('cpf', $affectedUserCpfSearch)
                    ->pluck('id');

                $activityLogsQuery->where(function ($query) use ($affectedUserCpfSearch, $matchingUserIds) {
                    if ($matchingUserIds->isNotEmpty()) {
                        $query->where(function ($subQuery) use ($matchingUserIds) {
                            $subQuery->where('subject_type', User::class)
                                ->whereIn('subject_id', $matchingUserIds);
                        });
                    }

                    $query->orWhere('properties->target_user->cpf', $affectedUserCpfSearch);
                });
            }

            $activityLogs = $activityLogsQuery->paginate(6, ['*'], 'logs_page')->withQueryString();
            $activityLogItems = $activityLogs->getCollection();

            $userIds = $activityLogItems
                ->filter(fn ($log) => $log->subject_type === User::class)
                ->pluck('subject_id')
                ->unique()
                ->values();

            $appointmentIds = $activityLogItems
                ->filter(fn ($log) => $log->subject_type === Agendamento::class)
                ->pluck('subject_id')
                ->unique()
                ->values();

            $professionalIds = $activityLogItems
                ->filter(fn ($log) => $log->subject_type === Professional::class)
                ->pluck('subject_id')
                ->unique()
                ->values();

            $procedureIds = $activityLogItems
                ->filter(fn ($log) => $log->subject_type === Procedure::class)
                ->pluck('subject_id')
                ->unique()
                ->values();

            if ($userIds->isNotEmpty()) {
                $userNames = User::whereIn('id', $userIds)
                    ->get(['id', 'nome', 'sobrenome'])
                    ->mapWithKeys(fn (User $user) => [
                        User::class.'|'.$user->id => trim($user->full_name),
                    ]);

                $subjectDisplayNames = $subjectDisplayNames->merge($userNames);
            }

            if ($appointmentIds->isNotEmpty()) {
                $appointmentNames = Agendamento::whereIn('id', $appointmentIds)
                    ->get(['id', 'nome', 'servico'])
                    ->mapWithKeys(fn (Agendamento $agendamento) => [
                        Agendamento::class.'|'.$agendamento->id => trim(($agendamento->nome ?: 'Agendamento') . ($agendamento->servico ? ' - ' . $agendamento->servico : '')),
                    ]);

                $subjectDisplayNames = $subjectDisplayNames->merge($appointmentNames);
            }

            if ($professionalIds->isNotEmpty()) {
                $professionalNames = Professional::whereIn('id', $professionalIds)
                    ->get(['id', 'nome'])
                    ->mapWithKeys(fn (Professional $professional) => [
                        Professional::class.'|'.$professional->id => trim($professional->nome),
                    ]);

                $subjectDisplayNames = $subjectDisplayNames->merge($professionalNames);
            }

            if ($procedureIds->isNotEmpty()) {
                $procedureNames = Procedure::whereIn('id', $procedureIds)
                    ->get(['id', 'nome'])
                    ->mapWithKeys(fn (Procedure $procedure) => [
                        Procedure::class.'|'.$procedure->id => trim($procedure->nome),
                    ]);

                $subjectDisplayNames = $subjectDisplayNames->merge($procedureNames);
            }
        }

        return view('admin.modules.settings.activity-logs', compact('activityLogs', 'subjectDisplayNames', 'affectedUserCpfSearch', 'activityDateSearch', 'actionTypeSearch'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $this->ensureCadastrosBaseManagerAccess();

        $normalizedPhone = preg_replace('/\D/', '', (string) $request->input('fone'));

        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
            'cpf' => ($normalizedCpf = preg_replace('/\D/', '', (string) $request->input('cpf'))) !== '' ? $normalizedCpf : null,
            'fone' => $normalizedPhone !== '' ? $normalizedPhone : null,
        ]);

        $validationRules = [
            'nome' => 'required|string|max:255',
            'sobrenome' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|size:11|unique:users,cpf',
            'fone' => 'nullable|string|regex:/^\d{10,11}$/|unique:users,fone',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|in:ativo,cancelado',
            'role' => 'required|in:recepcionista,profissional,medico,gestor_clinica',
        ];

        $request->validate($validationRules, [
            'cpf.size' => 'O CPF deve conter 11 dígitos.',
            'cpf.unique' => 'O usuario nao foi salvo porque o CPF informado ja esta vinculado a outro usuario cadastrado.',
            'fone.regex' => 'O telefone deve conter entre 10 e 11 dígitos.',
            'fone.unique' => 'O usuario nao foi salvo porque o celular informado ja esta vinculado a outro usuario cadastrado.',
            'email.unique' => 'O usuario nao foi salvo porque o e-mail informado ja esta vinculado a outro usuario cadastrado.',
        ]);

        $normalizedRole = $request->input('role') === 'medico' ? 'profissional' : $request->input('role', 'recepcionista');

        $payload = [
            'nome' => $request->nome,
            'sobrenome' => $request->sobrenome,
            'cpf' => $request->cpf,
            'fone' => $request->fone,
            'email' => $request->email,
            'password' => $request->password,
            'status' => $request->status,
            'nivel' => 'user',
        ];

        if (Schema::hasColumn('users', 'role')) {
            $payload['role'] = $normalizedRole;
        }

        if (Schema::hasColumn('users', 'permissions')) {
            $payload['permissions'] = User::submenuPermissionsForRole($normalizedRole);
        }

        $user = User::create($payload);

        $this->recordActivity('created', $user, 'Cadastro de usuário realizado.', [
            'target_user' => [
                'id' => $user->id,
                'nome' => trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')),
                'cpf' => $user->cpf,
                'email' => $user->email,
            ],
            'after' => [
                'cpf' => $user->cpf,
                'role' => $payload['role'] ?? null,
                'permissions' => $payload['permissions'] ?? [],
                'status' => $user->status,
            ],
        ]);

        return redirect()->route('admin.settings.users')->with('success', 'Novo usuário cadastrado com sucesso.');
    }

    public function updateUserPermissions(Request $request, User $user): RedirectResponse
    {
        $this->ensureCadastrosBaseManagerAccess();
        $this->ensureManagedUser($user, true);

        if (! Schema::hasColumn('users', 'role') || ! Schema::hasColumn('users', 'permissions')) {
            return redirect()->route('admin.settings.users')->with('warning', 'Execute as migrations dos cadastros base para editar papéis e acessos de submenu.');
        }

        $normalizedPhone = preg_replace('/\D/', '', (string) $request->input('fone'));

        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email', $user->email))),
            'cpf' => $request->has('cpf') ? (($normalizedCpf = preg_replace('/\D/', '', (string) $request->input('cpf'))) !== '' ? $normalizedCpf : null) : $user->cpf,
            'fone' => $request->has('fone') ? ($normalizedPhone !== '' ? $normalizedPhone : null) : $user->fone,
        ]);

        $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'sobrenome' => 'nullable|string|max:255',
            'cpf' => ['nullable', 'string', 'size:11', Rule::unique('users', 'cpf')->ignore($user->id)],
            'fone' => ['nullable', 'string', 'regex:/^\d{10,11}$/', Rule::unique('users', 'fone')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'status' => 'sometimes|required|in:ativo,cancelado',
            'role' => ['required', Rule::in($user->isPrimaryAdmin() ? ['admin'] : ['recepcionista', 'profissional', 'medico', 'gestor_clinica'])],
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'cpf.size' => 'O CPF deve conter 11 dígitos.',
            'cpf.unique' => 'O usuario nao foi salvo porque o CPF informado ja esta vinculado a outro usuario cadastrado.',
            'fone.regex' => 'O telefone deve conter entre 10 e 11 dígitos.',
            'fone.unique' => 'O usuario nao foi salvo porque o celular informado ja esta vinculado a outro usuario cadastrado.',
            'email.unique' => 'O usuario nao foi salvo porque o e-mail informado ja esta vinculado a outro usuario cadastrado.',
        ]);

        $previousNome = $user->nome;
        $previousSobrenome = $user->sobrenome;
        $previousCpf = $user->cpf;
        $previousPhone = $user->fone;
        $previousEmail = $user->email;
        $previousStatus = $user->status;
        $previousRole = $user->role;
        $previousPermissions = $user->permissions ?? [];

        $newRole = $request->role === 'medico' ? 'profissional' : $request->role;
        $newPermissions = $newRole === 'admin'
            ? array_keys(User::submenuPermissionLabels())
            : User::submenuPermissionsForRole($newRole);

        $updatePayload = [
            'nome' => $request->input('nome', $user->nome),
            'sobrenome' => $request->input('sobrenome', $user->sobrenome),
            'cpf' => $request->input('cpf', $user->cpf),
            'fone' => $request->input('fone', $user->fone),
            'email' => $request->input('email', $user->email),
            'status' => $request->input('status', $user->status),
            'role' => $newRole,
            'nivel' => $newRole === 'admin' ? 'admin' : 'user',
            'permissions' => $newPermissions,
        ];

        if ($request->filled('password')) {
            $updatePayload['password'] = Hash::make($request->password);
        }

        $user->update($updatePayload);

        $this->recordActivity('updated', $user, 'Perfil de acesso e permissões de submenu atualizados.', [
            'target_user' => [
                'id' => $user->id,
                'nome' => trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')),
                'cpf' => $user->cpf,
                'email' => $user->email,
            ],
            'before' => [
                'nome' => $previousNome,
                'sobrenome' => $previousSobrenome,
                'cpf' => $previousCpf,
                'fone' => $previousPhone,
                'email' => $previousEmail,
                'status' => $previousStatus,
                'role' => $previousRole,
                'permissions' => $previousPermissions,
            ],
            'after' => [
                'nome' => $user->nome,
                'sobrenome' => $user->sobrenome,
                'cpf' => $user->cpf,
                'fone' => $user->fone,
                'email' => $user->email,
                'status' => $user->status,
                'role' => $user->role,
                'permissions' => $user->permissions,
            ],
        ]);

        return redirect()->route('admin.settings.users')->with('success', 'Acessos do usuário atualizados com sucesso.');
    }

    public function toggleUserStatus(User $user): RedirectResponse
    {
        $this->ensureCadastrosBaseManagerAccess();
        $this->ensureManagedUser($user);

        $previousStatus = $user->status;
        $newStatus = $user->status === 'ativo' ? 'cancelado' : 'ativo';

        $user->update(['status' => $newStatus]);

        $this->recordActivity('updated', $user, 'Status de acesso do usuário alterado.', [
            'target_user' => [
                'id' => $user->id,
                'nome' => trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')),
                'cpf' => $user->cpf,
                'email' => $user->email,
            ],
            'before' => ['status' => $previousStatus],
            'after' => ['status' => $newStatus],
        ]);

        return redirect()->route('admin.settings.users')->with('success', 'Status do usuário atualizado com sucesso.');
    }

    public function destroyUser(User $user): RedirectResponse
    {
        $this->ensureCadastrosBaseManagerAccess();
        $this->ensureManagedUser($user);

        $this->recordActivity('deleted', $user, 'Usuário removido da área administrativa.', [
            'target_user' => [
                'id' => $user->id,
                'nome' => trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')),
                'cpf' => $user->cpf,
                'email' => $user->email,
            ],
            'before' => [
                'role' => $user->role,
                'status' => $user->status,
                'permissions' => $user->permissions ?? [],
            ],
        ]);
        $user->delete();

        return redirect()->route('admin.settings.users')->with('success', 'Usuário excluído com sucesso.');
    }

    private function syncSchedules(Professional $professional, array $days, array $starts, array $ends): void
    {
        $usedDays = [];

        foreach ($days as $index => $day) {
            $start = $starts[$index] ?? null;
            $end = $ends[$index] ?? null;

            if (! $day || ! $start || ! $end || $start >= $end) {
                continue;
            }

            $targetDays = $day === 'weekdays' ? [1, 2, 3, 4, 5] : [(int) $day];

            foreach ($targetDays as $targetDay) {
                if (in_array($targetDay, $usedDays, true)) {
                    continue;
                }

                $professional->schedules()->create([
                    'day_of_week' => $targetDay,
                    'start_time' => $start,
                    'break_start_time' => null,
                    'break_end_time' => null,
                    'end_time' => $end,
                ]);

                $usedDays[] = $targetDay;
            }
        }
    }

    private function validateScheduleRows(array $days, array $starts, array $ends, ?array $clinicHoursWindow = null): void
    {
        $usedDays = [];

        foreach ($days as $index => $day) {
            $start = $starts[$index] ?? null;
            $end = $ends[$index] ?? null;

            if (! $day && ! $start && ! $end) {
                continue;
            }

            if (! $day || ! $start || ! $end) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'schedule_day_of_week' => 'Preencha o dia, o horário de início e o horário de fim em cada vínculo de agenda utilizado.',
                ]);
            }

            $targetDays = $day === 'weekdays' ? [1, 2, 3, 4, 5] : [(int) $day];

            foreach ($targetDays as $targetDay) {
                if (in_array($targetDay, $usedDays, true)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_day_of_week' => 'Cada dia da semana pode ser escolhido apenas uma vez no vínculo de agenda.',
                    ]);
                }

                $usedDays[] = $targetDay;
            }

            if ($clinicHoursWindow) {
                $openingTime = $clinicHoursWindow['opening_time'] ?? null;
                $closingTime = $clinicHoursWindow['closing_time'] ?? null;

                if (($openingTime && $start < $openingTime) || ($closingTime && $end > $closingTime)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_day_of_week' => 'Os horários do vínculo de agenda devem respeitar o horário configurado para a clínica.',
                    ]);
                }
            }

            if ($start >= $end) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'schedule_day_of_week' => 'O horário final do vínculo de agenda deve ser maior que o horário inicial.',
                ]);
            }
        }
    }

    private function weekDays(): array
    {
        return [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }

    private function professionalCouncils(): array
    {
        return [
            'CRM' => [
                'name' => 'Conselho Regional de Medicina',
                'category' => 'Médicos',
                'profession' => 'Médico(a)',
            ],
            'CRO' => [
                'name' => 'Conselho Regional de Odontologia',
                'category' => 'Dentistas',
                'profession' => 'Dentista',
            ],
            'CRP' => [
                'name' => 'Conselho Regional de Psicologia',
                'category' => 'Psicólogos',
                'profession' => 'Psicólogo(a)',
            ],
            'COREN' => [
                'name' => 'Conselho Regional de Enfermagem',
                'category' => 'Enfermeiros e Técnicos',
                'profession' => 'Enfermeiro(a)',
            ],
            'CREFITO' => [
                'name' => 'Conselho Regional de Fisioterapia e Terapia Ocupacional',
                'category' => 'Fisioterapeutas e T.O.',
                'profession' => 'Fisioterapeuta / T.O.',
            ],
            'CRN' => [
                'name' => 'Conselho Regional de Nutricionistas',
                'category' => 'Nutricionistas',
                'profession' => 'Nutricionista',
            ],
            'CRF' => [
                'name' => 'Conselho Regional de Farmácia',
                'category' => 'Farmacêuticos',
                'profession' => 'Farmacêutico(a)',
            ],
            'CRFA' => [
                'name' => 'Conselho Regional de Fonoaudiologia',
                'category' => 'Fonoaudiólogos',
                'profession' => 'Fonoaudiólogo(a)',
            ],
            'CRBM' => [
                'name' => 'Conselho Regional de Biomedicina',
                'category' => 'Biomédicos',
                'profession' => 'Biomédico(a)',
            ],
            'CREF' => [
                'name' => 'Conselho Regional de Educação Física',
                'category' => 'Profissionais de Ed. Física (Atuação em reabilitação)',
                'profession' => 'Profissional de Ed. Física',
            ],
            'CRMV' => [
                'name' => 'Conselho Regional de Medicina Veterinária',
                'category' => 'Veterinários (caso o sistema seja pet)',
                'profession' => 'Veterinário(a)',
            ],
        ];
    }

    private function hasTables(array $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function ensurePrimaryAdminAccess(): void
    {
        if (! Auth::user()?->isPrimaryAdmin()) {
            abort(403, 'Apenas Thiago Ferreira pode acessar esta área.');
        }
    }

    private function ensureCadastrosBaseManagerAccess(): void
    {
        if (! Auth::user()?->canManageCadastrosBase()) {
            abort(403, 'Seu perfil não possui permissão para gerenciar Cadastros Base.');
        }
    }

    private function ensureManagedUser(User $user, bool $allowUpdate = false): void
    {
        if ($allowUpdate && Auth::user()?->isPrimaryAdmin() && ($user->isPrimaryAdmin() || $user->id === Auth::id())) {
            return;
        }

        if ($user->isPrimaryAdmin() || $user->id === Auth::id()) {
            abort(403, 'O administrador principal não pode ser alterado por esta tela.');
        }
    }
}
