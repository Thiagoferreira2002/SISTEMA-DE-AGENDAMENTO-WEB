<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Agendamento;
use App\Models\ClinicHour;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Professional;
use App\Models\ProfessionalAbsence;
use App\Models\User;
use App\Traits\RecordsActivity;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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
                'title' => 'Usuários e Permissões',
                'description' => 'Defina níveis de acesso como Admin, Recepcionista e Profissional, controle permissões para editar prontuários e excluir agendamentos, e acompanhe logs de atividade.',
                'route' => route('admin.settings.users'),
                'icon' => 'fas fa-users-cog',
            ],
            [
                'title' => 'Profissionais de Saúde',
                'description' => 'Gerencie quem realiza os atendimentos com nome, especialidade principal, CPF, CRM, vínculo de agenda por dia/horário e cor de identificação na agenda geral.',
                'route' => route('admin.settings.professionals'),
                'icon' => 'fas fa-user-md',
            ],
            [
                'title' => 'Procedimentos',
                'description' => 'Mantenha a lista de tudo o que a clínica oferece, como consulta médica, eletrocardiograma, retorno e demais serviços prestados.',
                'route' => route('admin.settings.procedures'),
                'icon' => 'fas fa-notes-medical',
            ],
            [
                'title' => 'Horário da Clínica',
                'description' => 'Defina o horário de abertura e término da clínica para impedir agendamentos fora da janela de atendimento.',
                'route' => route('admin.settings.clinic-hours'),
                'icon' => 'fas fa-clock',
            ],
            [
                'title' => 'Logs de Atividade',
                'description' => 'Consulte o histórico completo das alterações administrativas, filtre por CPF do usuário afetado e acompanhe os detalhes das mudanças.',
                'route' => route('admin.settings.activity-logs'),
                'icon' => 'fas fa-history',
            ],
        ];

        if (! Auth::user()?->canManageCadastrosBase()) {
            $cards = array_values(array_filter($cards, function (array $card) {
                return ! in_array($card['title'], ['Usuários e Permissões', 'Logs de Atividade'], true);
            }));
        }

        if (Auth::user()?->isPrimaryAdmin()) {
            $cards[0]['description'] = 'Defina níveis de acesso como Admin, Recepcionista, Profissional e Gestor da Clínica, controle permissões por módulo e acompanhe logs de atividade.';
        }

        return view('admin.modules.settings.index', compact('cards'));
    }

    public function waitlist(): View
    {
        $waitlist = Agendamento::with('patient')
            ->where('status', 'pendente')
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

    public function doctorAbsences(Request $request): View
    {
        $minimumAbsenceDate = $this->minimumAbsenceDateForRegistration();
        $clinicHoursWindow = $this->clinicHoursWindow();
        $professional = $this->selectedProfessionalForAbsences($request);
        $professionalOptions = $this->canManageAbsencesForOthers() && Schema::hasTable('profissionais')
            ? Professional::query()
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(['id', 'nome'])
            : collect();

        abort_unless($professional || $professionalOptions->isNotEmpty() || $this->canManageAbsencesForOthers(), 403);

        $professionalSchedules = collect();
        $absences = collect();
        $appointments = collect();

        if (Schema::hasTable('ausencias_profissionais')) {
            $this->cleanupExpiredProfessionalAbsences($professional);
        }

        if ($professional) {
            $professional->loadMissing('schedules');

            $professionalSchedules = $professional->schedules
                ->map(fn ($schedule) => [
                    'day_of_week' => (int) $schedule->day_of_week,
                    'start_time' => substr((string) $schedule->start_time, 0, 5),
                    'end_time' => substr((string) $schedule->end_time, 0, 5),
                    'break_start_time' => $schedule->break_start_time ? substr((string) $schedule->break_start_time, 0, 5) : null,
                    'break_end_time' => $schedule->break_end_time ? substr((string) $schedule->break_end_time, 0, 5) : null,
                ])
                ->values();

            $absences = Schema::hasTable('ausencias_profissionais')
                ? $professional->absences()
                    ->whereDate('data_ausencia', '>=', $minimumAbsenceDate->toDateString())
                    ->get()
                : collect();

            $appointments = Agendamento::query()
                ->whereDate('data_agendamento', '>=', now()->toDateString())
                ->where(function ($query) use ($professional) {
                    $query->where('profissional_id', $professional->id)
                        ->orWhere('medico', $professional->nome);
                })
                ->where(function ($query) {
                    $query->whereIn('status', ['pendente', 'confirmado'])
                        ->orWhereNull('status');
                })
                ->orderBy('data_agendamento')
                ->orderBy('horario')
                ->get()
                ->map(function (Agendamento $appointment) {
                    return [
                        'date' => optional($appointment->data_agendamento)->format('Y-m-d'),
                        'start_time' => substr((string) $appointment->horario, 0, 5),
                        'end_time' => optional($this->appointmentEndDateTime($appointment))?->format('H:i'),
                        'patient_name' => $appointment->nome ?: 'Paciente',
                        'service' => $appointment->servico ?: 'Atendimento',
                    ];
                })
                ->values();
        }

        $setupWarning = null;

        if (! Schema::hasTable('ausencias_profissionais')) {
            $setupWarning = 'Execute as migrations para habilitar o controle de ausências pontuais do profissional.';
        } elseif (! $professional && $professionalOptions->isEmpty()) {
            $setupWarning = 'Cadastre e ative um profissional para gerenciar as ausências.';
        }

        return view('admin.modules.doctor.absences', [
            'professional' => $professional,
            'absences' => $absences,
            'appointments' => $appointments,
            'minimumAbsenceDate' => $minimumAbsenceDate,
            'clinicHours' => $clinicHoursWindow,
            'professionalSchedules' => $professionalSchedules,
            'setupWarning' => $setupWarning,
            'professionalOptions' => $professionalOptions,
            'isProfessionalAbsenceContext' => $this->isProfessionalUser(),
        ]);
    }

    public function storeDoctorAbsence(Request $request): RedirectResponse
    {
        $professional = $this->selectedProfessionalForAbsences($request);

        abort_unless($professional && ($this->isProfessionalUser() || $this->canManageAbsencesForOthers()), 403);

        if (! Schema::hasTable('ausencias_profissionais')) {
            return redirect()->route('admin.doctor.absences', $this->absenceRouteParameters($professional))
                ->with('warning', 'Execute as migrations para habilitar o controle de ausências pontuais do profissional.');
        }

        $minimumAbsenceDate = $this->minimumAbsenceDateForRegistration();

        $validated = $request->validate([
            'data_ausencia' => ['required', 'date', 'after_or_equal:' . $minimumAbsenceDate->toDateString()],
            'hora_inicial' => ['required', 'date_format:H:i'],
            'hora_final' => ['required', 'date_format:H:i', 'after:hora_inicial'],
            'motivo' => ['required', 'string', 'max:120'],
            'observacao' => ['nullable', 'string'],
        ], [
            'data_ausencia.required' => 'Informe a data da ausência.',
            'data_ausencia.after_or_equal' => $minimumAbsenceDate->isToday()
                ? 'A ausência deve ser cadastrada para hoje ou uma data futura.'
                : 'O horário da clínica já foi encerrado hoje. Registre a ausência a partir de amanhã.',
            'hora_inicial.required' => 'Informe o horário inicial da ausência.',
            'hora_final.required' => 'Informe o horário final da ausência.',
            'hora_final.after' => 'O horário final deve ser maior que o horário inicial.',
            'motivo.required' => 'Informe o motivo da ausência.',
        ]);

        $absenceDate = Carbon::parse($validated['data_ausencia']);

        if ($absenceDate->isToday() && $validated['hora_inicial'] < now()->format('H:i')) {
            return redirect()->route('admin.doctor.absences', $this->absenceRouteParameters($professional))
                ->withInput()
                ->withErrors(['hora_inicial' => 'O horário inicial não pode ser anterior ao horário atual.']);
        }

        if (! $this->professionalScheduleCoversInterval($professional, $absenceDate, $validated['hora_inicial'], $validated['hora_final'])) {
            return redirect()->route('admin.doctor.absences', $this->absenceRouteParameters($professional))
                ->withInput()
                ->withErrors(['hora_inicial' => 'A ausência precisa estar dentro de um horário de atendimento configurado para esse dia.']);
        }

        if ($this->professionalAbsenceOverlapsExisting($professional, $absenceDate, $validated['hora_inicial'], $validated['hora_final'])) {
            return redirect()->route('admin.doctor.absences', $this->absenceRouteParameters($professional))
                ->withInput()
                ->withErrors(['hora_inicial' => 'Já existe uma ausência cadastrada para esse período.']);
        }

        $conflictingAppointments = $this->professionalAppointmentsOverlappingInterval(
            $professional,
            $absenceDate,
            $validated['hora_inicial'],
            $validated['hora_final']
        );

        if ($conflictingAppointments->isNotEmpty()) {
            return redirect()->route('admin.doctor.absences', $this->absenceRouteParameters($professional))
                ->withInput()
                ->withErrors([
                    'hora_inicial' => 'Já existem agendamentos ativos nesse período. Remarque ou finalize esses horários antes de registrar a ausência.',
                ]);
        }

        $absence = ProfessionalAbsence::create([
            'profissional_id' => $professional->id,
            'data_ausencia' => $validated['data_ausencia'],
            'hora_inicial' => $validated['hora_inicial'],
            'hora_final' => $validated['hora_final'],
            'motivo' => $validated['motivo'],
            'observacao' => $validated['observacao'] ?? null,
        ]);

        $this->recordActivity('created', $absence, 'Ausência pontual do profissional cadastrada.', [
            'professional_id' => $professional->id,
            'data_ausencia' => $validated['data_ausencia'],
            'hora_inicial' => $validated['hora_inicial'],
            'hora_final' => $validated['hora_final'],
            'motivo' => $validated['motivo'],
        ]);

        return redirect()->route('admin.doctor.absences', $this->absenceRouteParameters($professional))
            ->with('success', 'Ausência pontual cadastrada com sucesso.');
    }

    public function destroyDoctorAbsence(Request $request, ProfessionalAbsence $absence): RedirectResponse
    {
        $professional = $this->authenticatedProfessional();

        if ($professional) {
            abort_unless((int) $absence->profissional_id === (int) $professional->id, 403);
        } else {
            abort_unless($this->canManageAbsencesForOthers(), 403);
            $professional = $absence->professional;
        }

        $absenceSnapshot = [
            'professional_id' => $absence->profissional_id,
            'data_ausencia' => optional($absence->data_ausencia)->format('Y-m-d'),
            'hora_inicial' => substr((string) $absence->hora_inicial, 0, 5),
            'hora_final' => substr((string) $absence->hora_final, 0, 5),
            'motivo' => $absence->motivo,
        ];

        $absence->delete();

        $this->recordActivity('deleted', $professional, 'Ausência pontual do profissional removida.', $absenceSnapshot);

        return redirect()->route('admin.doctor.absences', $this->absenceRouteParameters($professional))
            ->with('success', 'Ausência pontual removida com sucesso.');
    }

    public function clinicHours(): View
    {
        $setupWarning = null;
        $clinicHours = null;

        if ($this->hasTables(['horarios_clinica'])) {
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
        if (! $this->hasTables(['horarios_clinica'])) {
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

        $conflictingAppointment = Agendamento::query()
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereIn('status', ['pendente', 'confirmado']);
            })
            ->whereDate('data_agendamento', '>=', now()->toDateString())
            ->orderBy('data_agendamento')
            ->orderBy('horario')
            ->get()
            ->first(fn (Agendamento $agendamento) => ! $this->appointmentFitsClinicWindow($agendamento, $validated));

        if ($conflictingAppointment) {
            return redirect()->route('admin.settings.clinic-hours')
                ->withErrors([
                    'opening_time' => 'Nao e possivel alterar o horario da clinica porque existe um agendamento ativo em conflito: ' . $conflictingAppointment->nome . ' em ' . optional($conflictingAppointment->data_agendamento)->format('d/m/Y') . ' das ' . substr((string) $conflictingAppointment->horario, 0, 5) . ' as ' . $this->appointmentEndTimeLabel($conflictingAppointment) . '.',
                ])
                ->withInput();
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

    private function appointmentFitsClinicWindow(Agendamento $agendamento, array $validatedHours): bool
    {
        $startTime = substr((string) $agendamento->horario, 0, 5);
        $endTime = $this->appointmentEndTimeLabel($agendamento);

        if ($startTime < $validatedHours['opening_time'] || $endTime > $validatedHours['closing_time']) {
            return false;
        }

        $lunchStart = $validatedHours['lunch_start_time'] ?? null;
        $lunchEnd = $validatedHours['lunch_end_time'] ?? null;

        if (! $lunchStart || ! $lunchEnd) {
            return true;
        }

        return ! ($startTime < $lunchEnd && $endTime > $lunchStart);
    }

    private function appointmentEndTimeLabel(Agendamento $agendamento): string
    {
        return Carbon::createFromFormat('H:i', substr((string) $agendamento->horario, 0, 5))
            ->addMinutes((int) ($agendamento->duracao_minutos ?: 30))
            ->format('H:i');
    }

    private function clinicHoursWindow(): ?array
    {
        if (! $this->hasTables(['horarios_clinica'])) {
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
        $serviceFilter = trim((string) $request->input('service', ''));
        $selectedDate = trim((string) $request->input('date', ''));
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
                $query->where('profissional_id', $authenticatedProfessional->id)
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

        if ($serviceFilter !== '') {
            $pendingBaseQuery->where('servico', $serviceFilter);
        }

        if ($selectedDate !== '') {
            $pendingBaseQuery->whereDate('data_agendamento', $selectedDate);
        }

        if ($selectedDate === '' && $period === 'dia') {
            $pendingBaseQuery->whereDate('data_agendamento', now()->toDateString());
        }

        if ($selectedDate === '' && $period === 'semana') {
            $dateWindow = [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ];

            $pendingBaseQuery->whereBetween('data_agendamento', $dateWindow);
        }

        if ($selectedDate === '' && $period === 'mes') {
            $pendingBaseQuery->whereYear('data_agendamento', now()->year)
                ->whereMonth('data_agendamento', now()->month);
        }

        $appointments = $pendingBaseQuery
            ->orderBy('data_agendamento')
            ->orderBy('horario')
            ->get()
            ->filter(fn (Agendamento $item) => ! $this->appointmentHasPassedEndTime($item))
            ->values();

        $summary = [
            'pendentes' => $appointments->count(),
        ];

        $serviceOptionsQuery = Agendamento::query()
            ->where(function ($query) {
                $query->where('status', 'pendente')
                    ->orWhereNull('status');
            })
            ->whereNotNull('servico')
            ->where('servico', '!=', '');

        if ($authenticatedProfessional) {
            $serviceOptionsQuery->where(function ($query) use ($authenticatedProfessional) {
                $query->where('profissional_id', $authenticatedProfessional->id)
                    ->orWhere('medico', $authenticatedProfessional->nome);
            });
        } elseif ($this->isProfessionalUser()) {
            $serviceOptionsQuery->whereRaw('1 = 0');
        }

        $serviceOptions = $serviceOptionsQuery
            ->select('servico')
            ->distinct()
            ->orderBy('servico')
            ->pluck('servico');

        return view('admin.modules.agendamentos.confirmations', compact('appointments', 'summary', 'serviceOptions', 'serviceFilter', 'selectedDate'));
    }

    private function isProfessionalUser(): bool
    {
        $user = Auth::user();

        return (bool) $user
            && $user->normalizedRole() === 'profissional';
    }

    private function authenticatedProfessional(): ?Professional
    {
        if (! $this->isProfessionalUser() || ! Schema::hasTable('profissionais')) {
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

    private function canManageAbsencesForOthers(): bool
    {
        $role = Auth::user()?->normalizedRole();

        return in_array($role, ['admin', 'gestor_clinica', 'recepcionista'], true);
    }

    private function selectedProfessionalForAbsences(?Request $request = null): ?Professional
    {
        $authenticatedProfessional = $this->authenticatedProfessional();

        if ($authenticatedProfessional) {
            return $authenticatedProfessional;
        }

        if (! $this->canManageAbsencesForOthers() || ! Schema::hasTable('profissionais')) {
            return null;
        }

        $professionalQuery = Professional::query()
            ->where('ativo', true)
            ->orderBy('nome');

        $selectedProfessionalId = (int) ($request?->input('professional_id', $request?->query('professional_id')) ?? 0);

        if ($selectedProfessionalId > 0) {
            $selectedProfessional = (clone $professionalQuery)
                ->whereKey($selectedProfessionalId)
                ->first();

            if ($selectedProfessional) {
                return $selectedProfessional;
            }
        }

        return $professionalQuery->first();
    }

    private function absenceRouteParameters(?Professional $professional = null): array
    {
        if ($this->isProfessionalUser() || ! $professional) {
            return [];
        }

        return ['professional_id' => $professional->id];
    }

    private function cleanupExpiredProfessionalAbsences(?Professional $professional = null): void
    {
        $query = ProfessionalAbsence::query();

        if ($professional) {
            $query->where('profissional_id', $professional->id);
        }

        $query->where(function ($builder) {
            $builder->whereDate('data_ausencia', '<', now()->toDateString())
                ->orWhere(function ($sameDayQuery) {
                    $sameDayQuery->whereDate('data_ausencia', now()->toDateString())
                        ->where('hora_final', '<=', now()->format('H:i:s'));
                });
        })->delete();
    }

    private function minimumAbsenceDateForRegistration(): Carbon
    {
        $minimumDate = now();

        if (! Schema::hasTable('horarios_clinica')) {
            return $minimumDate;
        }

        $clinicClosingTime = ClinicHour::query()->value('closing_time');

        if (! $clinicClosingTime) {
            return $minimumDate;
        }

        $clinicClosingDateTime = now()->copy()->setTimeFromTimeString((string) $clinicClosingTime);

        return now()->greaterThanOrEqualTo($clinicClosingDateTime)
            ? $minimumDate->copy()->addDay()
            : $minimumDate;
    }

    private function professionalScheduleCoversInterval(Professional $professional, Carbon $date, string $startTime, string $endTime): bool
    {
        $professional->loadMissing('schedules');

        $requestedStart = Carbon::createFromFormat('H:i', substr($startTime, 0, 5));
        $requestedEnd = Carbon::createFromFormat('H:i', substr($endTime, 0, 5));
        $dayOfWeek = $date->dayOfWeekIso;

        return $professional->schedules->contains(function ($schedule) use ($dayOfWeek, $requestedStart, $requestedEnd) {
            if ((int) $schedule->day_of_week !== $dayOfWeek) {
                return false;
            }

            $scheduleStart = Carbon::createFromFormat('H:i:s', (string) $schedule->start_time);
            $scheduleEnd = Carbon::createFromFormat('H:i:s', (string) $schedule->end_time);

            if (! ($requestedStart->greaterThanOrEqualTo($scheduleStart) && $requestedEnd->lessThanOrEqualTo($scheduleEnd))) {
                return false;
            }

            if ($schedule->break_start_time && $schedule->break_end_time) {
                $breakStart = Carbon::createFromFormat('H:i:s', (string) $schedule->break_start_time);
                $breakEnd = Carbon::createFromFormat('H:i:s', (string) $schedule->break_end_time);

                if ($requestedStart->lt($breakEnd) && $requestedEnd->gt($breakStart)) {
                    return false;
                }
            }

            return true;
        });
    }

    private function professionalAbsenceOverlapsExisting(Professional $professional, Carbon $date, string $startTime, string $endTime): bool
    {
        return ProfessionalAbsence::query()
            ->where('profissional_id', $professional->id)
            ->whereDate('data_ausencia', $date->format('Y-m-d'))
            ->get()
            ->contains(function (ProfessionalAbsence $absence) use ($startTime, $endTime) {
                return $this->timeRangesOverlap(
                    substr((string) $absence->hora_inicial, 0, 5),
                    substr((string) $absence->hora_final, 0, 5),
                    $startTime,
                    $endTime
                );
            });
    }

    private function professionalAppointmentsOverlappingInterval(Professional $professional, Carbon $date, string $startTime, string $endTime)
    {
        return Agendamento::query()
            ->whereDate('data_agendamento', $date->format('Y-m-d'))
            ->where(function ($query) use ($professional) {
                $query->where('profissional_id', $professional->id)
                    ->orWhere('medico', $professional->nome);
            })
            ->where(function ($query) {
                $query->whereIn('status', ['pendente', 'confirmado'])
                    ->orWhereNull('status');
            })
            ->get()
            ->filter(function (Agendamento $appointment) use ($startTime, $endTime) {
                $appointmentEnd = optional($this->appointmentEndDateTime($appointment))?->format('H:i');

                if (! $appointmentEnd) {
                    return false;
                }

                return $this->timeRangesOverlap(
                    substr((string) $appointment->horario, 0, 5),
                    $appointmentEnd,
                    $startTime,
                    $endTime
                );
            })
            ->values();
    }

    private function timeRangesOverlap(string $startA, string $endA, string $startB, string $endB): bool
    {
        $rangeAStart = Carbon::createFromFormat('H:i', substr($startA, 0, 5));
        $rangeAEnd = Carbon::createFromFormat('H:i', substr($endA, 0, 5));
        $rangeBStart = Carbon::createFromFormat('H:i', substr($startB, 0, 5));
        $rangeBEnd = Carbon::createFromFormat('H:i', substr($endB, 0, 5));

        return $rangeAStart->lt($rangeBEnd) && $rangeAEnd->gt($rangeBStart);
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

        if (Auth::user()?->isClinicManager()) {
            return redirect($request->input('return_to', route('admin.agendamentos.confirmations')))
                ->with('layout_warning', 'O Gestor da Clínica possui acesso somente para visualização em Agendamentos.');
        }

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
        $matchesProfessional = (string) $agendamento->professional_id === (string) $professional->id
            || collect($this->professionalNameCandidates($professional, Auth::user()))
                ->contains(fn ($candidate) => $appointmentProfessionalName !== '' && $appointmentProfessionalName === mb_strtolower(trim((string) $candidate)));

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
        $serviceFilter = trim((string) $request->input('service', ''));
        $startDate = trim((string) $request->input('start_date', ''));
        $endDate = '';

        $historyQuery = Agendamento::where('status', 'concluido');

        $authenticatedProfessional = $this->authenticatedProfessional();

        if ($authenticatedProfessional) {
            $historyQuery->where(function ($query) use ($authenticatedProfessional) {
                $query->where('profissional_id', $authenticatedProfessional->id)
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
            $historyQuery->where('profissional_id', (int) $professionalFilter);
        }

        if ($serviceFilter !== '') {
            $historyQuery->where('servico', $serviceFilter);
        }

        if ($startDate !== '') {
            $historyQuery->whereDate('data_agendamento', '>=', $startDate);
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
            $item->medico_historico = $item->professional?->nome ?: ($item->medico_historico ?? $item->medico ?: 'Não informado');
            $item->medico_historico = $item->medico ?: 'Não informado';

            $item->medico_historico = $item->professional?->nome ?: ($item->medico_historico ?: 'Não informado');
            return $item;
        });

        $professionalOptions = ! $authenticatedProfessional && $this->hasTables(['profissionais'])
            ? Professional::query()->where('ativo', true)->orderBy('nome')->get(['id', 'nome'])
            : collect();

        $serviceOptionsQuery = Agendamento::query()
            ->where('status', 'concluido')
            ->whereNotNull('servico')
            ->where('servico', '!=', '');

        if ($authenticatedProfessional) {
            $serviceOptionsQuery->where(function ($query) use ($authenticatedProfessional) {
                $query->where('profissional_id', $authenticatedProfessional->id)
                    ->orWhere('medico', $authenticatedProfessional->nome);
            });
        } elseif ($this->isProfessionalUser()) {
            $serviceOptionsQuery->whereRaw('1 = 0');
        }

        $serviceOptions = $serviceOptionsQuery
            ->select('servico')
            ->distinct()
            ->orderBy('servico')
            ->pluck('servico');

        $moduleRoute = $authenticatedProfessional
            ? route('admin.agendamentos.completed', ['source' => 'doctor'])
            : route('admin.agendamentos.completed');

        return view('admin.modules.patients.history', [
            'history' => $history,
            'period' => $period,
            'totalFinishedAppointments' => $totalFinishedAppointments,
            'search' => $search,
            'professionalFilter' => $professionalFilter,
            'professionalOptions' => $professionalOptions,
            'serviceFilter' => $serviceFilter,
            'serviceOptions' => $serviceOptions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'authenticatedProfessional' => $authenticatedProfessional,
            'moduleTitle' => 'Agendamentos Finalizados',
            'moduleCardTitle' => 'Lista de agendamentos finalizados',
            'moduleRoute' => $moduleRoute,
            'moduleCounterLabel' => 'Agendamentos Finalizados',
        ]);
    }

    public function patientDocuments(): View
    {
        $patients = Patient::orderBy('nome')->get();
        $documentTypes = ['Exame Laboratorial', 'Laudo de Imagem', 'Documento Pessoal', 'Termo de Consentimento'];

        return view('admin.modules.patients.documents', compact('patients', 'documentTypes'));
    }

    public function doctorQueue(Request $request): View
    {
        $queueQuery = $this->baseDoctorQueueQuery();
        $search = trim($request->string('q')->toString());
        $selectedDate = $request->filled('date') ? (string) $request->input('date') : '';
        $selectedProfessionalId = $request->filled('professional_id') ? (string) $request->input('professional_id') : '';
        $period = in_array($request->input('period'), ['dia', 'semana', 'mes'], true)
            ? $request->input('period')
            : '';

        $this->applyDoctorQueueFilters($queueQuery, $search, $selectedDate, $period, $selectedProfessionalId);

        $queue = $queueQuery
            ->orderBy('data_agendamento')
            ->orderBy('horario')
            ->get()
            ->values()
            ->filter(fn (Agendamento $item) => ! $this->appointmentHasPassedEndTime($item))
            ->map(function ($item) {
                $item->profissional_fila = $item->professional?->nome ?: ($item->medico ?: 'Não informado');
                $item->horario_final_exibicao = optional($this->appointmentEndDateTime($item))->format('H:i');
                $item->cpf_exibicao = $item->patient?->cpf ?: ($item->cpf ?: null);

                return $item;
            })
            ->values();

        $totalPatientsInQueue = $queue->count();
        $hasDelayedAppointments = $totalPatientsInQueue > 0;

        $pageTitle = 'Fila de Espera';
        $cardTitle = 'Pacientes na fila';
        $emptyMessage = 'Nenhum paciente encontrado na fila para os filtros informados.';
        $baseRoute = 'admin.doctor.queue';
        $professionalOptions = ! $this->authenticatedProfessional() && Schema::hasTable('profissionais')
            ? Professional::query()->where('ativo', true)->orderBy('nome')->get(['id', 'nome'])
            : collect();

        return view('admin.modules.doctor.queue', compact('queue', 'period', 'search', 'selectedDate', 'selectedProfessionalId', 'professionalOptions', 'totalPatientsInQueue', 'pageTitle', 'cardTitle', 'emptyMessage', 'baseRoute', 'hasDelayedAppointments'));
    }

    public function doctorPendingFinalization(Request $request): View
    {
        $queueQuery = $this->baseDoctorQueueQuery();
        $search = trim($request->string('q')->toString());
        $selectedDate = $request->filled('date') ? (string) $request->input('date') : '';
        $selectedProfessionalId = $request->filled('professional_id') ? (string) $request->input('professional_id') : '';
        $period = in_array($request->input('period'), ['dia', 'semana', 'mes'], true)
            ? $request->input('period')
            : '';

        $this->applyDoctorQueueFilters($queueQuery, $search, $selectedDate, $period, $selectedProfessionalId);

        $queue = $queueQuery
            ->orderBy('data_agendamento')
            ->orderBy('horario')
            ->get()
            ->values()
            ->filter(fn (Agendamento $item) => $this->appointmentHasPassedEndTime($item))
            ->map(function ($item) {
                $item->profissional_fila = $item->professional?->nome ?: ($item->medico ?: 'Não informado');
                $item->horario_final_exibicao = optional($this->appointmentEndDateTime($item))->format('H:i');
                $item->cpf_exibicao = $item->patient?->cpf ?: ($item->cpf ?: null);

                return $item;
            })
            ->values();

        $totalPatientsInQueue = $queue->count();
        $hasDelayedAppointments = $totalPatientsInQueue > 0;

        $pageTitle = 'Atendimentos em Atraso';
        $cardTitle = 'Agendamentos não finalizados';
        $emptyMessage = 'Nenhum atendimento atrasado encontrado para os filtros informados.';
        $baseRoute = 'admin.doctor.pending-finalization';
        $professionalOptions = ! $this->authenticatedProfessional() && Schema::hasTable('profissionais')
            ? Professional::query()->where('ativo', true)->orderBy('nome')->get(['id', 'nome'])
            : collect();

        return view('admin.modules.doctor.queue', compact('queue', 'period', 'search', 'selectedDate', 'selectedProfessionalId', 'professionalOptions', 'totalPatientsInQueue', 'pageTitle', 'cardTitle', 'emptyMessage', 'baseRoute', 'hasDelayedAppointments'));
    }

    private function baseDoctorQueueQuery()
    {
        $queueQuery = Agendamento::with(['professional', 'patient'])
            ->where(function ($query) {
                $query->whereIn('status', ['pendente', 'confirmado'])
                    ->orWhereNull('status');
            });

        $authenticatedProfessional = $this->authenticatedProfessional();

        if ($authenticatedProfessional) {
            $nameCandidates = $this->professionalNameCandidates($authenticatedProfessional, Auth::user());

            $queueQuery->where(function ($query) use ($authenticatedProfessional, $nameCandidates) {
                $query->where('profissional_id', $authenticatedProfessional->id);

                foreach ($nameCandidates as $nameCandidate) {
                    $query->orWhereRaw('LOWER(TRIM(medico)) = ?', [mb_strtolower(trim((string) $nameCandidate))]);
                }
            });
        } elseif ($this->isProfessionalUser()) {
            $queueQuery->whereRaw('1 = 0');
        }

        return $queueQuery;
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

    private function applyDoctorQueueFilters($queueQuery, string $search, string $selectedDate, string $period, string $selectedProfessionalId = ''): void
    {
        if ($search !== '') {
            $queueQuery->where('nome', 'like', '%' . $search . '%');
        }

        if ($selectedProfessionalId !== '' && ! $this->authenticatedProfessional()) {
            $selectedProfessional = Schema::hasTable('profissionais')
                ? Professional::query()->where('ativo', true)->find((int) $selectedProfessionalId)
                : null;

            if ($selectedProfessional) {
                $nameCandidates = $this->professionalNameCandidates($selectedProfessional, null);

                $queueQuery->where(function ($query) use ($selectedProfessional, $nameCandidates) {
                    $query->where('profissional_id', $selectedProfessional->id);

                    foreach ($nameCandidates as $nameCandidate) {
                        $query->orWhereRaw('LOWER(TRIM(medico)) = ?', [mb_strtolower(trim((string) $nameCandidate))]);
                    }
                });
            }
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
        if (in_array((string) $agendamento->status, ['concluido', 'cancelado'], true)) {
            return false;
        }

        $endTime = $this->appointmentEndDateTime($agendamento);

        return $endTime ? $endTime->lessThanOrEqualTo(now()) : false;
    }

    public function finishAppointment(Request $request, Agendamento $agendamento): RedirectResponse
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);
        $redirectTo = $request->input('return_to', route('admin.doctor.queue', $request->only(['q', 'date', 'period'])));

        if (Auth::user()?->isClinicManager()) {
            return redirect($redirectTo)
                ->with('layout_warning', 'O Gestor da Clínica possui acesso somente para visualização em Atendimentos em Atraso.');
        }

        if ($agendamento->status === 'concluido') {
            return redirect($redirectTo)
                ->with('warning', 'Este atendimento já foi finalizado.');
        }

        if ($agendamento->status === 'cancelado') {
            return redirect($redirectTo)
                ->with('warning', 'Não é possível finalizar um atendimento cancelado.');
        }

        $agendamento->update(['status' => 'concluido']);
        $this->recordActivity('updated', $agendamento, 'Atendimento finalizado e enviado para Agendamentos Finalizados.', ['status' => 'concluido']);

        return redirect($redirectTo)
            ->with('success', 'Atendimento finalizado com sucesso. O registro já está em Agendamentos Finalizados.');
    }

    public function cancelOperationalAppointment(Request $request, Agendamento $agendamento): RedirectResponse
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);
        $redirectTo = $request->input('return_to', route('admin.doctor.queue', $request->only(['q', 'date', 'period', 'professional_id'])));

        if (Auth::user()?->isClinicManager()) {
            return redirect($redirectTo)
                ->with('layout_warning', 'O Gestor da Clínica possui acesso somente para visualização em Atendimentos em Atraso.');
        }

        if ($agendamento->status === 'concluido') {
            return redirect($redirectTo)
                ->with('warning', 'NÃ£o Ã© possÃ­vel cancelar um atendimento jÃ¡ finalizado.');
        }

        if ($agendamento->status === 'cancelado') {
            return redirect($redirectTo)
                ->with('warning', 'Este atendimento jÃ¡ foi cancelado.');
        }

        $agendamento->update(['status' => 'cancelado']);
        $this->recordActivity('updated', $agendamento, 'Atendimento cancelado a partir do fluxo operacional.', ['status' => 'cancelado']);

        return redirect($redirectTo)
            ->with('success', 'Atendimento cancelado com sucesso.');
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

        if ($this->hasTables(['profissionais', 'agendas_profissionais'])) {
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
        if (! $this->hasTables(['profissionais', 'agendas_profissionais'])) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Execute as migrations dos cadastros base para cadastrar profissionais.');
        }

        if (! Schema::hasColumn('users', 'role')) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Execute as migrations de papéis e permissões antes de cadastrar profissionais vinculados a usuários com papel Profissional.');
        }

        $linkedUser = User::find($request->input('user_id'));
        $normalizedCpf = preg_replace('/\D/', '', (string) ($linkedUser?->cpf));
        $subspecialties = collect($request->input('subespecialidades', []))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();

        $request->merge([
            'cpf' => $normalizedCpf !== '' ? $normalizedCpf : null,
            'agenda_color' => mb_strtolower(trim((string) $request->input('agenda_color'))),
            'nome' => trim((string) $request->input('nome')),
            'subespecialidades' => $subspecialties,
            'registro_numero' => preg_replace('/\D/', '', (string) $request->input('registro_numero')),
            'rqe' => trim((string) $request->input('rqe')) ?: null,
            'schedule_mode' => trim((string) $request->input('schedule_mode')) ?: 'specific_hours',
        ]);

        if ($request->filled('rqe')) {
            $request->merge([
                'rqe' => preg_replace('/\D/', '', (string) $request->input('rqe')),
            ]);
        }

        $professionalCouncils = $this->professionalCouncils();

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('profissionais', 'user_id'),
                function ($attribute, $value, $fail) {
                    $linkedUser = User::find($value);

                    if (! $linkedUser || ! in_array($linkedUser->role, ['profissional', 'medico'], true)) {
                        $fail('Selecione um usuário com papel Profissional para vincular ao profissional.');
                    }
                },
            ],
            'nome' => 'required|string|max:255',
            'especialidade_principal' => 'required|string|max:255',
            'subespecialidades' => 'nullable|array',
            'subespecialidades.*' => 'nullable|string|max:80',
            'cpf' => 'nullable|string|max:20|unique:profissionais,cpf',
            'registro_tipo' => ['required', 'string', 'max:20', Rule::in(array_keys($professionalCouncils))],
            'registro_numero' => ['required', 'string', 'regex:/^\d{1,20}$/'],
            'rqe' => ['nullable', 'string', 'regex:/^\d{1,20}$/'],
            'agenda_color' => ['required', 'string', 'max:20', Rule::unique('profissionais', 'agenda_color')],
            'schedule_mode' => ['required', 'string', Rule::in(['clinic_hours', 'specific_hours'])],
            'schedule_day_of_week' => 'nullable|array',
            'schedule_day_of_week.*' => ['nullable', 'string', Rule::in(['weekdays', '1', '2', '3', '4', '5', '6', '7'])],
            'schedule_morning_start_time' => 'nullable|array',
            'schedule_morning_start_time.*' => 'nullable|date_format:H:i',
            'schedule_morning_end_time' => 'nullable|array',
            'schedule_morning_end_time.*' => 'nullable|date_format:H:i',
            'schedule_afternoon_start_time' => 'nullable|array',
            'schedule_afternoon_start_time.*' => 'nullable|date_format:H:i',
            'schedule_afternoon_end_time' => 'nullable|array',
            'schedule_afternoon_end_time.*' => 'nullable|date_format:H:i',
            'schedule_start_time' => 'nullable|array',
            'schedule_start_time.*' => 'nullable|date_format:H:i',
            'schedule_end_time' => 'nullable|array',
            'schedule_end_time.*' => 'nullable|date_format:H:i',
        ], [
            'user_id.required' => 'Selecione um usuário com papel Profissional para cadastrar o profissional.',
            'user_id.unique' => 'Este usuário já está vinculado a outro profissional.',
            'cpf.unique' => 'O profissional nao foi salvo porque o CPF deste usuario ja esta vinculado a outro profissional cadastrado.',
            'registro_tipo.in' => 'Selecione um conselho profissional válido.',
            'registro_numero.regex' => 'O número do registro no conselho deve conter apenas números e no máximo 20 dígitos.',
            'rqe.regex' => 'O RQE deve conter apenas números e no máximo 20 dígitos.',
            'agenda_color.unique' => 'Esta cor de agenda já está em uso por outro profissional.',
        ]);

        $clinicHoursWindow = $this->clinicHoursWindow();

        [$scheduleDays, $scheduleStarts, $scheduleEnds] = $this->resolveProfessionalScheduleSubmission(
            $request->input('schedule_mode'),
            $request->input('schedule_day_of_week', []),
            $request->input('schedule_start_time', []),
            $request->input('schedule_end_time', []),
            $clinicHoursWindow,
            $request->input('schedule_morning_start_time', []),
            $request->input('schedule_morning_end_time', []),
            $request->input('schedule_afternoon_start_time', []),
            $request->input('schedule_afternoon_end_time', []),
        );

        $this->validateScheduleRows($scheduleDays, $scheduleStarts, $scheduleEnds, $clinicHoursWindow);

        $linkedUser = User::findOrFail($request->input('user_id'));
        $professionalName = trim((string) $request->input('nome')) ?: trim(($linkedUser->nome ?? '') . ' ' . ($linkedUser->sobrenome ?? ''));

        $professional = Professional::create([
            'user_id' => $linkedUser->id,
            'nome' => $professionalName,
            'especialidade_principal' => $request->especialidade_principal,
            'subespecialidades' => $request->input('subespecialidades', []),
            'cpf' => $request->cpf,
            'registro_tipo' => strtoupper($request->registro_tipo),
            'registro_numero' => $request->registro_numero,
            'rqe' => $request->rqe,
            'agenda_color' => $request->agenda_color,
            'ativo' => true,
        ]);

        $this->syncSchedules(
            $professional,
            $scheduleDays,
            $scheduleStarts,
            $scheduleEnds,
            $clinicHoursWindow
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
            'subespecialidades' => $professional->subespecialidades,
            'registro' => $professional->registro_completo,
            'rqe' => $professional->rqe,
        ]);

        return redirect()->route('admin.settings.professionals')->with('success', 'Profissional cadastrado com sucesso.');
    }

    public function updateProfessional(Request $request, Professional $professional): RedirectResponse
    {
        if (! $this->hasTables(['profissionais', 'agendas_profissionais'])) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Execute as migrations dos cadastros base para editar profissionais.');
        }

        if (! Schema::hasColumn('users', 'role')) {
            return redirect()->route('admin.settings.professionals')->with('warning', 'Execute as migrations de papéis e permissões antes de editar profissionais vinculados.');
        }

        $linkedUser = User::find($request->input('user_id'));
        $normalizedCpf = preg_replace('/\D/', '', (string) ($linkedUser?->cpf));
        $subspecialties = collect($request->input('subespecialidades', []))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();

        $request->merge([
            'cpf' => $normalizedCpf !== '' ? $normalizedCpf : null,
            'agenda_color' => mb_strtolower(trim((string) $request->input('agenda_color'))),
            'nome' => trim((string) $request->input('nome')),
            'subespecialidades' => $subspecialties,
            'registro_numero' => preg_replace('/\D/', '', (string) $request->input('registro_numero')),
            'rqe' => trim((string) $request->input('rqe')) ?: null,
            'schedule_mode' => trim((string) $request->input('schedule_mode')) ?: 'specific_hours',
        ]);

        if ($request->filled('rqe')) {
            $request->merge([
                'rqe' => preg_replace('/\D/', '', (string) $request->input('rqe')),
            ]);
        }

        $professionalCouncils = $this->professionalCouncils();

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('profissionais', 'user_id')->ignore($professional->id),
                function ($attribute, $value, $fail) {
                    $linkedUser = User::find($value);

                    if (! $linkedUser || ! in_array($linkedUser->role, ['profissional', 'medico'], true)) {
                        $fail('Selecione um usuário com papel Profissional para vincular ao profissional.');
                    }
                },
            ],
            'nome' => 'required|string|max:255',
            'especialidade_principal' => 'required|string|max:255',
            'subespecialidades' => 'nullable|array',
            'subespecialidades.*' => 'nullable|string|max:80',
            'cpf' => ['nullable', 'string', 'max:20', Rule::unique('profissionais', 'cpf')->ignore($professional->id)],
            'registro_tipo' => ['required', 'string', 'max:20', Rule::in(array_keys($professionalCouncils))],
            'registro_numero' => ['required', 'string', 'regex:/^\d{1,20}$/'],
            'rqe' => ['nullable', 'string', 'regex:/^\d{1,20}$/'],
            'agenda_color' => ['required', 'string', 'max:20', Rule::unique('profissionais', 'agenda_color')->ignore($professional->id)],
            'schedule_mode' => ['required', 'string', Rule::in(['clinic_hours', 'specific_hours'])],
            'schedule_day_of_week' => 'nullable|array',
            'schedule_day_of_week.*' => ['nullable', 'string', Rule::in(['weekdays', '1', '2', '3', '4', '5', '6', '7'])],
            'schedule_morning_start_time' => 'nullable|array',
            'schedule_morning_start_time.*' => 'nullable|date_format:H:i',
            'schedule_morning_end_time' => 'nullable|array',
            'schedule_morning_end_time.*' => 'nullable|date_format:H:i',
            'schedule_afternoon_start_time' => 'nullable|array',
            'schedule_afternoon_start_time.*' => 'nullable|date_format:H:i',
            'schedule_afternoon_end_time' => 'nullable|array',
            'schedule_afternoon_end_time.*' => 'nullable|date_format:H:i',
            'schedule_start_time' => 'nullable|array',
            'schedule_start_time.*' => 'nullable|date_format:H:i',
            'schedule_end_time' => 'nullable|array',
            'schedule_end_time.*' => 'nullable|date_format:H:i',
        ], [
            'user_id.required' => 'Selecione um usuário com papel Profissional para vincular ao profissional.',
            'user_id.unique' => 'Este usuário já está vinculado a outro profissional.',
            'cpf.unique' => 'O profissional nao foi salvo porque o CPF deste usuario ja esta vinculado a outro profissional cadastrado.',
            'registro_tipo.in' => 'Selecione um conselho profissional válido.',
            'registro_numero.regex' => 'O número do registro no conselho deve conter apenas números e no máximo 20 dígitos.',
            'rqe.regex' => 'O RQE deve conter apenas números e no máximo 20 dígitos.',
            'agenda_color.unique' => 'Esta cor de agenda já está em uso por outro profissional.',
        ]);

        $clinicHoursWindow = $this->clinicHoursWindow();

        [$scheduleDays, $scheduleStarts, $scheduleEnds] = $this->resolveProfessionalScheduleSubmission(
            $request->input('schedule_mode'),
            $request->input('schedule_day_of_week', []),
            $request->input('schedule_start_time', []),
            $request->input('schedule_end_time', []),
            $clinicHoursWindow,
            $request->input('schedule_morning_start_time', []),
            $request->input('schedule_morning_end_time', []),
            $request->input('schedule_afternoon_start_time', []),
            $request->input('schedule_afternoon_end_time', []),
        );

        $this->validateScheduleRows($scheduleDays, $scheduleStarts, $scheduleEnds, $clinicHoursWindow);

        $previousValues = [
            'user_id' => $professional->user_id,
            'nome' => $professional->nome,
            'especialidade_principal' => $professional->especialidade_principal,
            'subespecialidades' => $professional->subespecialidades,
            'cpf' => $professional->cpf,
            'registro_tipo' => $professional->registro_tipo,
            'registro_numero' => $professional->registro_numero,
            'rqe' => $professional->rqe,
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
        $professionalName = trim((string) $request->input('nome')) ?: trim(($linkedUser->nome ?? '') . ' ' . ($linkedUser->sobrenome ?? ''));

        $professional->update([
            'user_id' => $linkedUser->id,
            'nome' => $professionalName,
            'especialidade_principal' => $request->especialidade_principal,
            'subespecialidades' => $request->input('subespecialidades', []),
            'cpf' => $request->cpf,
            'registro_tipo' => strtoupper($request->registro_tipo),
            'registro_numero' => $request->registro_numero,
            'rqe' => $request->rqe,
            'agenda_color' => $request->agenda_color,
        ]);

        $professional->schedules()->delete();
        $this->syncSchedules(
            $professional,
            $scheduleDays,
            $scheduleStarts,
            $scheduleEnds,
            $clinicHoursWindow
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
                'subespecialidades' => $professional->subespecialidades,
                'cpf' => $professional->cpf,
                'registro_tipo' => $professional->registro_tipo,
                'registro_numero' => $professional->registro_numero,
                'rqe' => $professional->rqe,
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
        if (! $this->hasTables(['profissionais'])) {
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

    public function procedures(): View
    {
        $setupWarning = null;
        $procedures = collect();
        $professionalOptions = collect();
        $selectedProfessionalId = request()->integer('professional_filter');

        if ($this->hasTables(['procedimentos'])) {
            if ($this->hasTables(['profissionais'])) {
                $professionalOptions = Professional::where('ativo', true)
                    ->orderBy('nome')
                    ->get(['id', 'nome', 'especialidade_principal']);
            }

            $procedureQuery = Procedure::with('professional')
                ->orderBy('nome');

            if ($selectedProfessionalId && $this->hasTables(['profissionais'])) {
                $procedureQuery->where('profissional_id', $selectedProfessionalId);
            }

            $procedures = $procedureQuery
                ->paginate(6)
                ->withQueryString();
        } else {
            $setupWarning = 'Os cadastros de procedimentos ainda dependem das migrations. Execute as migrations para habilitar o módulo completo.';
        }

        return view('admin.modules.settings.procedures', compact('procedures', 'setupWarning', 'professionalOptions', 'selectedProfessionalId'));
    }

    public function storeProcedure(Request $request): RedirectResponse
    {
        if (! $this->hasTables(['procedimentos'])) {
            return redirect()->route('admin.settings.procedures')->with('warning', 'Execute as migrations dos cadastros base para cadastrar procedimentos.');
        }

        $durationOptions = range(15, 180, 15);

        $request->validate([
            'nome' => 'required|string|max:255|unique:procedimentos,nome',
            'duracao_minutos' => ['required', 'integer', Rule::in($durationOptions)],
            'professional_id' => $this->hasTables(['profissionais']) ? 'required|exists:profissionais,id' : 'nullable',
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
        if (! $this->hasTables(['procedimentos'])) {
            return redirect()->route('admin.settings.procedures')->with('warning', 'Execute as migrations dos cadastros base para editar procedimentos.');
        }

        $durationOptions = range(15, 180, 15);

        $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('procedimentos', 'nome')->ignore($procedure->id)],
            'duracao_minutos' => ['required', 'integer', Rule::in($durationOptions)],
            'professional_id' => $this->hasTables(['profissionais']) ? 'required|exists:profissionais,id' : 'nullable',
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
        if (! $this->hasTables(['procedimentos'])) {
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
        if (! $this->hasTables(['procedimentos'])) {
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
        $responsibleSearch = trim((string) $request->input('responsible'));
        $affectedUserCpfSearch = preg_replace('/\D/', '', (string) $request->input('affected_user_cpf'));
        $activityDateSearch = trim((string) $request->input('activity_date'));
        $actionTypeSearch = in_array($request->input('action_type'), ['created', 'updated', 'deleted'], true)
            ? $request->input('action_type')
            : '';

        if (Schema::hasTable('logs_atividades')) {
            $activityLogsQuery = ActivityLog::with('user')->latest();

            if ($actionTypeSearch !== '') {
                $activityLogsQuery->where('action', $actionTypeSearch);
            }

            if ($responsibleSearch !== '') {
                $activityLogsQuery->whereHas('user', function ($query) use ($responsibleSearch) {
                    $query->whereRaw("TRIM(CONCAT(COALESCE(nome, ''), ' ', COALESCE(sobrenome, ''))) like ?", ['%' . $responsibleSearch . '%'])
                        ->orWhere('email', 'like', '%' . $responsibleSearch . '%');
                });
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

        return view('admin.modules.settings.activity-logs', compact('activityLogs', 'subjectDisplayNames', 'responsibleSearch', 'affectedUserCpfSearch', 'activityDateSearch', 'actionTypeSearch'));
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
            'capa' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $request->validate($validationRules, [
            'cpf.size' => 'O CPF deve conter 11 dígitos.',
            'cpf.unique' => 'O usuario nao foi salvo porque o CPF informado ja esta vinculado a outro usuario cadastrado.',
            'fone.regex' => 'O telefone deve conter entre 10 e 11 dígitos.',
            'fone.unique' => 'O usuario nao foi salvo porque o celular informado ja esta vinculado a outro usuario cadastrado.',
            'email.unique' => 'O usuario nao foi salvo porque o e-mail informado ja esta vinculado a outro usuario cadastrado.',
            'capa.image' => 'Selecione uma imagem válida para a foto do usuário.',
            'capa.mimes' => 'A foto do usuário deve ser JPG, PNG ou WEBP.',
            'capa.max' => 'A foto do usuário deve ter no máximo 2 MB.',
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

        if ($request->hasFile('capa')) {
            $directory = 'backend/assets/img/profile';
            $fileName = 'profile-user-' . Str::uuid() . '.' . strtolower((string) $request->file('capa')->getClientOriginalExtension());

            File::ensureDirectoryExists(public_path($directory));
            $request->file('capa')->move(public_path($directory), $fileName);

            $payload['capa'] = $directory . '/' . $fileName;
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
            'capa' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'cpf.size' => 'O CPF deve conter 11 dígitos.',
            'cpf.unique' => 'O usuario nao foi salvo porque o CPF informado ja esta vinculado a outro usuario cadastrado.',
            'fone.regex' => 'O telefone deve conter entre 10 e 11 dígitos.',
            'fone.unique' => 'O usuario nao foi salvo porque o celular informado ja esta vinculado a outro usuario cadastrado.',
            'email.unique' => 'O usuario nao foi salvo porque o e-mail informado ja esta vinculado a outro usuario cadastrado.',
            'capa.image' => 'Selecione uma imagem válida para a foto do usuário.',
            'capa.mimes' => 'A foto do usuário deve ser JPG, PNG ou WEBP.',
            'capa.max' => 'A foto do usuário deve ter no máximo 2 MB.',
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

        if ($request->hasFile('capa')) {
            $directory = 'backend/assets/img/profile';
            $fileName = 'profile-user-' . $user->id . '-' . Str::uuid() . '.' . strtolower((string) $request->file('capa')->getClientOriginalExtension());

            File::ensureDirectoryExists(public_path($directory));
            $request->file('capa')->move(public_path($directory), $fileName);

            if (!empty($user->capa) && is_file(public_path($user->capa))) {
                File::delete(public_path($user->capa));
            }

            $updatePayload['capa'] = $directory . '/' . $fileName;
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

    private function syncSchedules(Professional $professional, array $days, array $starts, array $ends, ?array $clinicHoursWindow = null): void
    {
        $persistedSchedules = [];

        foreach ($days as $index => $day) {
            $start = $starts[$index] ?? null;
            $end = $ends[$index] ?? null;

            if (! $day || ! $start || ! $end || $start >= $end) {
                continue;
            }

            $targetDays = $day === 'weekdays' ? [1, 2, 3, 4, 5] : [(int) $day];

            foreach ($targetDays as $targetDay) {
                $scheduleKey = $targetDay . '|' . $start . '|' . $end;

                if (in_array($scheduleKey, $persistedSchedules, true)) {
                    continue;
                }

                $scheduleData = $this->adjustScheduleToClinicHours(
                    $targetDay,
                    $start,
                    $end,
                    null,
                    null,
                    $clinicHoursWindow,
                );

                if (! $scheduleData) {
                    continue;
                }

                $professional->schedules()->create($scheduleData);

                $persistedSchedules[] = $scheduleKey;
            }
        }
    }

    private function resolveProfessionalScheduleSubmission(
        string $scheduleMode,
        array $days,
        array $starts,
        array $ends,
        ?array $clinicHoursWindow = null,
        array $morningStarts = [],
        array $morningEnds = [],
        array $afternoonStarts = [],
        array $afternoonEnds = [],
    ): array
    {
        if ($scheduleMode !== 'clinic_hours') {
            $normalizedDays = [];
            $normalizedStarts = [];
            $normalizedEnds = [];
            $usedDays = [];

            foreach ($days as $index => $day) {
                $day = trim((string) $day);
                $morningStart = $morningStarts[$index] ?? null;
                $morningEnd = $morningEnds[$index] ?? null;
                $afternoonStart = $afternoonStarts[$index] ?? null;
                $afternoonEnd = $afternoonEnds[$index] ?? null;
                $legacyStart = $starts[$index] ?? null;
                $legacyEnd = $ends[$index] ?? null;

                $hasMorning = $morningStart || $morningEnd;
                $hasAfternoon = $afternoonStart || $afternoonEnd;
                $hasLegacy = $legacyStart || $legacyEnd;

                if ($day === '' && ! $hasMorning && ! $hasAfternoon && ! $hasLegacy) {
                    continue;
                }

                if ($day === '') {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_day_of_week' => 'Selecione o dia da semana para cada horário específico informado.',
                    ]);
                }

                if (in_array($day, $usedDays, true)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_day_of_week' => 'Cada dia da semana pode ser usado apenas uma vez nos horários específicos do profissional.',
                    ]);
                }

                $usedDays[] = $day;

                if ($hasLegacy) {
                    $normalizedDays[] = $day;
                    $normalizedStarts[] = $legacyStart;
                    $normalizedEnds[] = $legacyEnd;
                    continue;
                }

                if (($morningStart && ! $morningEnd) || (! $morningStart && $morningEnd)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_day_of_week' => 'Preencha o início e o fim da manhã no mesmo dia sempre que usar o período da manhã.',
                    ]);
                }

                if (($afternoonStart && ! $afternoonEnd) || (! $afternoonStart && $afternoonEnd)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_day_of_week' => 'Preencha o início e o fim da tarde no mesmo dia sempre que usar o período da tarde.',
                    ]);
                }

                if (! $hasMorning && ! $hasAfternoon) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_day_of_week' => 'Informe ao menos um período válido de manhã ou de tarde para cada dia utilizado.',
                    ]);
                }

                if ($morningStart && $morningEnd) {
                    $normalizedDays[] = $day;
                    $normalizedStarts[] = $morningStart;
                    $normalizedEnds[] = $morningEnd;
                }

                if ($afternoonStart && $afternoonEnd) {
                    $normalizedDays[] = $day;
                    $normalizedStarts[] = $afternoonStart;
                    $normalizedEnds[] = $afternoonEnd;
                }
            }

            return [$normalizedDays, $normalizedStarts, $normalizedEnds];
        }

        $openingTime = $clinicHoursWindow['opening_time'] ?? null;
        $closingTime = $clinicHoursWindow['closing_time'] ?? null;

        if (! $openingTime || ! $closingTime) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'schedule_mode' => 'Configure primeiro o horário da clínica para usar a agenda automática de segunda a sexta.',
            ]);
        }

        return [
            ['weekdays'],
            [$openingTime],
            [$closingTime],
        ];
    }

    private function validateScheduleRows(array $days, array $starts, array $ends, ?array $clinicHoursWindow = null): void
    {
        $dayIntervals = [];

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
                $dayIntervals[$targetDay] = $dayIntervals[$targetDay] ?? [];

                foreach ($dayIntervals[$targetDay] as $existingInterval) {
                    if ($start < $existingInterval['end'] && $end > $existingInterval['start']) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'schedule_day_of_week' => 'Os horários informados para o mesmo dia não podem se sobrepor.',
                        ]);
                    }
                }

                $dayIntervals[$targetDay][] = [
                    'start' => $start,
                    'end' => $end,
                ];
            }

            if ($clinicHoursWindow) {
                $openingTime = $clinicHoursWindow['opening_time'] ?? null;
                $closingTime = $clinicHoursWindow['closing_time'] ?? null;
                $lunchStartTime = $clinicHoursWindow['lunch_start_time'] ?? null;
                $lunchEndTime = $clinicHoursWindow['lunch_end_time'] ?? null;

                if (($openingTime && $start < $openingTime) || ($closingTime && $end > $closingTime)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_day_of_week' => 'Os horários do vínculo de agenda devem respeitar o horário configurado para a clínica.',
                    ]);
                }

                $crossesClinicInterval = $lunchStartTime
                    && $lunchEndTime
                    && $start < $lunchEndTime
                    && $end > $lunchStartTime;

                $coversEntireClinicInterval = $crossesClinicInterval
                    && $start < $lunchStartTime
                    && $end > $lunchEndTime;

                if ($crossesClinicInterval && ! $coversEntireClinicInterval) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_day_of_week' => 'Quando o vínculo atravessar o intervalo da clínica, escolha um início antes e um fim depois do intervalo. O sistema aplicará a pausa automaticamente.',
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
        $authenticatedUser = Auth::user();

        if ($allowUpdate && $authenticatedUser?->isPrimaryAdmin() && ($user->isPrimaryAdmin() || $user->id === Auth::id())) {
            return;
        }

        if ($authenticatedUser?->canManageUser($user)) {
            return;
        }

        $message = 'Você não possui permissão para alterar este usuário nesta tela.';

        if ($user->isPrimaryAdmin()) {
            $message = 'O administrador principal não pode ser alterado por esta tela.';
        } elseif ($authenticatedUser && (int) $user->id === (int) $authenticatedUser->id) {
            $message = 'Use Minha Conta para editar os seus próprios dados.';
        } elseif ($authenticatedUser?->isClinicManager()) {
            $message = 'O Gestor da Clínica não pode editar, inativar ou excluir usuários com papel igual ou superior ao seu.';
        }

        throw new HttpResponseException(
            redirect()->route('admin.settings.users')->with('layout_warning', $message)
        );
    }
}
