<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\ClinicHour;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Professional;
use App\Traits\RecordsActivity;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AgendamentoController extends Controller
{
    use RecordsActivity;

    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        [$agendamentos] = $this->filteredAppointments($request);
        $professionals = $this->professionalOptions();
        $totalAgendamentos = $agendamentos->count();

        return view('admin.agendamentos.index', compact(
            'agendamentos',
            'professionals',
            'totalAgendamentos'
        ));
    }

    public function calendar(Request $request)
    {
        $professionalOptions = $this->professionalOptions();
        $procedureOptions = $this->procedureOptions();
        $hideProfessionalFilter = $this->isProfessionalUser();
        $selectedProfessionalId = $request->string('professional_id')->toString();
        $selectedProcedureId = $request->string('procedure_id')->toString();
        $selectedCalendarDate = $request->string('calendar_date')->toString();
        $returnUrl = $this->resolveReturnUrl($request);
        $clinicHours = $this->clinicHoursConfig();

        return view('admin.agendamentos.calendar', compact(
            'professionalOptions',
            'procedureOptions',
            'hideProfessionalFilter',
            'selectedProfessionalId',
            'selectedProcedureId',
            'selectedCalendarDate',
            'clinicHours',
            'returnUrl'
        ));
    }

    public function delayedAppointments(Request $request)
    {
        $user = Auth::user();
        $search = trim($request->string('q')->toString());
        $selectedDate = $request->filled('date') ? (string) $request->input('date') : '';
        $selectedProfessionalId = $request->filled('professional_id') ? (string) $request->input('professional_id') : '';
        $period = in_array($request->input('period'), ['dia', 'semana', 'mes'], true)
            ? $request->input('period')
            : '';

        $query = Agendamento::with(['professional', 'patient'])
            ->where(function ($q) {
                $q->whereIn('status', ['pendente', 'confirmado'])
                    ->orWhereNull('status');
            });

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('nome', 'like', "%{$search}%");
                    });
            });
        }

        if ($selectedProfessionalId !== '') {
            $selectedProfessional = Schema::hasTable('profissionais')
                ? Professional::query()->where('ativo', true)->find((int) $selectedProfessionalId)
                : null;

            if ($selectedProfessional) {
                $query->where(function ($q) use ($selectedProfessional) {
                    $q->where('profissional_id', $selectedProfessional->id)
                        ->orWhere('medico', $selectedProfessional->nome);
                });
            }
        }

        // Apply date filter
        if ($selectedDate) {
            $query->whereDate('data_agendamento', $selectedDate);
        } elseif ($period === 'dia') {
            $query->whereDate('data_agendamento', now()->toDateString());
        } elseif ($period === 'semana') {
            $query->whereBetween('data_agendamento', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'mes') {
            $query->whereMonth('data_agendamento', now()->month)
                ->whereYear('data_agendamento', now()->year);
        }

        $delayedAppointments = $query
            ->orderBy('data_agendamento')
            ->orderBy('horario')
            ->get()
            ->filter(fn (Agendamento $item) => $this->appointmentHasPassedEndTime($item))
            ->map(function ($item) {
                $item->profissional_fila = $item->professional?->nome ?: ($item->medico ?: 'Não informado');
                $endTime = $this->appointmentEndDateTime($item);
                $item->horario_final_exibicao = optional($endTime)->format('H:i');
                $item->cpf_exibicao = $item->patient?->cpf ?: ($item->cpf ?: null);
                return $item;
            })
            ->values();

        $totalDelayedAppointments = $delayedAppointments->count();
        $professionals = Schema::hasTable('profissionais')
            ? Professional::query()->where('ativo', true)->orderBy('nome')->get(['id', 'nome'])
            : collect();

        return view('admin.agendamentos.delayed', compact(
            'delayedAppointments',
            'period',
            'search',
            'selectedDate',
            'selectedProfessionalId',
            'totalDelayedAppointments',
            'professionals'
        ));
    }

    public function calendarEvents(Request $request): JsonResponse
    {
        [$agendamentos] = $this->filteredAppointments($request, true);

        return response()->json($this->buildCalendarEvents($agendamentos));
    }

    private function filteredAppointments(Request $request, bool $includeCalendarHistory = false): array
    {
        $focusedAppointmentId = $request->integer('open_agendamento');
        $professionals = $this->professionals();
        $globalSearch = trim($request->string('q')->toString());
        $selectedCalendarDate = $request->string('calendar_date')->toString();
        $period = in_array($request->input('period'), ['dia', 'semana', 'mes'], true)
            ? $request->input('period')
            : '';
        $professionalRecords = $this->hasTables(['profissionais'])
            ? Professional::select('id', 'nome', 'agenda_color')->get()
            : collect();
        $professionalById = $professionalRecords->keyBy('id');
        $professionalByName = $professionalRecords->keyBy('nome');

        $agendamentos = Agendamento::with(['patient', 'professional', 'procedure'])
            ->orderBy('data_agendamento')
            ->orderBy('horario')
            ->get()
            ->map(fn (Agendamento $agendamento) => $this->decorateAppointment($agendamento, $professionals, $professionalById, $professionalByName))
            ->reject(function (Agendamento $agendamento) use ($focusedAppointmentId, $includeCalendarHistory) {
                if ($focusedAppointmentId && (int) $agendamento->id === $focusedAppointmentId) {
                    return false;
                }

                if ((string) $agendamento->status === 'cancelado') {
                    return true;
                }

                if ($includeCalendarHistory) {
                    return false;
                }

                if ((string) $agendamento->status === 'concluido') {
                    return true;
                }

                return $this->appointmentHasPassedEndTime($agendamento);
            })
            ->values();

        $agendamentos = $this->restrictAppointmentsForAuthenticatedProfessional($agendamentos);

        $selectedProfessionalId = $request->string('professional_id')->toString();
        $selectedProcedureId = $request->string('procedure_id')->toString();
        $selectedDoctors = collect((array) $request->input('medicos', []))
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->take(3)
            ->values();

        if ($selectedProfessionalId !== '') {
            $agendamentos = $agendamentos->filter(function (Agendamento $agendamento) use ($selectedProfessionalId) {
                return (string) $agendamento->professional_id === $selectedProfessionalId;
            })->values();
        }

        if ($selectedCalendarDate !== '') {
            $agendamentos = $agendamentos->filter(function (Agendamento $agendamento) use ($selectedCalendarDate) {
                return $agendamento->data_agendamento
                    && $agendamento->data_agendamento->format('Y-m-d') === $selectedCalendarDate;
            })->values();
        }

        if ($selectedProcedureId !== '') {
            $agendamentos = $agendamentos->filter(function (Agendamento $agendamento) use ($selectedProcedureId) {
                $appointmentProcedureId = $agendamento->procedure_id ?? $agendamento->procedimento_id;

                return (string) $appointmentProcedureId === $selectedProcedureId;
            })->values();
        }

        if ($selectedDoctors->isNotEmpty() && ! $this->isProfessionalUser()) {
            $allowedDoctors = $selectedDoctors->all();

            $agendamentos = $agendamentos->filter(function (Agendamento $agendamento) use ($allowedDoctors) {
                return in_array((string) $agendamento->medico_exibicao, $allowedDoctors, true);
            })->values();
        } elseif ($request->filled('medico')) {
            $agendamentos = $agendamentos->where('medico_exibicao', $request->string('medico')->toString())->values();
        }

        if ($globalSearch !== '') {
            $search = mb_strtolower($globalSearch);
            $cpfSearch = preg_replace('/\D+/', '', $globalSearch);
            $dateSearch = preg_replace('/\D+/', '', $globalSearch);

            $agendamentos = $agendamentos->filter(function (Agendamento $agendamento) use ($search, $cpfSearch, $dateSearch) {
                $fields = [
                    $agendamento->nome,
                    $agendamento->cpf_exibicao,
                    $agendamento->data_agendamento?->format('d/m/Y'),
                    $agendamento->data_agendamento?->format('Y-m-d'),
                ];

                foreach ($fields as $field) {
                    if ($field !== null && mb_stripos((string) $field, $search) !== false) {
                        return true;
                    }
                }

                if ($cpfSearch !== '') {
                    $appointmentCpf = preg_replace('/\D+/', '', (string) ($agendamento->cpf_exibicao ?? ''));

                    if ($appointmentCpf !== '' && str_contains($appointmentCpf, $cpfSearch)) {
                        return true;
                    }
                }

                if ($dateSearch !== '') {
                    $appointmentDate = preg_replace('/\D+/', '', (string) ($agendamento->data_agendamento?->format('d/m/Y') ?? ''));

                    if ($appointmentDate !== '' && str_contains($appointmentDate, $dateSearch)) {
                        return true;
                    }
                }

                return false;
            })->values();
        }

        if ($period === 'dia') {
            $agendamentos = $agendamentos->filter(fn (Agendamento $agendamento) => $agendamento->data_agendamento && $agendamento->data_agendamento->isToday())->values();
        }

        if ($period === 'semana') {
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();

            $agendamentos = $agendamentos->filter(function (Agendamento $agendamento) use ($startOfWeek, $endOfWeek) {
                return $agendamento->data_agendamento
                    && $agendamento->data_agendamento->betweenIncluded($startOfWeek, $endOfWeek);
            })->values();
        }

        if ($period === 'mes') {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $agendamentos = $agendamentos->filter(function (Agendamento $agendamento) use ($currentMonth, $currentYear) {
                return $agendamento->data_agendamento
                    && (int) $agendamento->data_agendamento->month === $currentMonth
                    && (int) $agendamento->data_agendamento->year === $currentYear;
            })->values();
        }

        return [$agendamentos, $professionals];
    }

    private function buildCalendarEvents($agendamentos): array
    {
        return $agendamentos->map(function (Agendamento $agendamento) {
            $startTime = substr((string) $agendamento->horario, 0, 5);
            $endTime = $this->appointmentEndDateTime($agendamento);

            return [
                'id' => $agendamento->id,
                'title' => trim(($agendamento->nome ?? '') . ' - ' . ($agendamento->servico ?? 'Consulta')),
                'start' => $agendamento->data_agendamento->format('Y-m-d') . 'T' . $startTime . ':00',
                'end' => $endTime->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $agendamento->status === 'concluido'
                    ? $agendamento->status_visual['color']
                    : ($agendamento->professional?->agenda_color ?: $agendamento->status_visual['color']),
                'borderColor' => $agendamento->status === 'concluido'
                    ? $agendamento->status_visual['color']
                    : ($agendamento->professional?->agenda_color ?: $agendamento->status_visual['color']),
                'textColor' => '#ffffff',
                'nome' => $agendamento->nome,
                'email' => $agendamento->email,
                'telefone' => $agendamento->telefone,
                'servico' => $agendamento->servico,
                'status' => $agendamento->status_visual['label'],
                'is_finalized' => $agendamento->status === 'concluido',
                'horario' => $startTime,
                'horario_final' => $endTime->format('H:i'),
                'medico' => $agendamento->medico_exibicao,
                'motivo' => $agendamento->motivo_exibicao,
                'agendamento_id' => $agendamento->id,
            ];
        })->values()->all();
    }

    private function appointmentEndDateTime(Agendamento $agendamento): ?Carbon
    {
        if (! $agendamento->data_agendamento || ! $agendamento->horario) {
            return null;
        }

        return $agendamento->data_agendamento->copy()
            ->setTimeFromTimeString(substr((string) $agendamento->horario, 0, 5))
            ->addMinutes((int) ($agendamento->duracao_exibicao ?? $agendamento->duracao_minutos ?? 30));
    }

    private function appointmentHasPassedEndTime(Agendamento $agendamento): bool
    {
        if (in_array((string) $agendamento->status, ['concluido', 'cancelado'], true)) {
            return false;
        }

        $endTime = $this->appointmentEndDateTime($agendamento);

        return $endTime ? $endTime->lessThanOrEqualTo(now()) : false;
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('nome')->get(['id', 'nome', 'cpf', 'email', 'telefone']);
        $preselectedPatient = $request->filled('patient_id')
            ? $patients->firstWhere('id', (int) $request->input('patient_id'))
            : null;
        $procedureOptions = $this->procedureOptions();
        $professionalOptions = $this->professionalOptions();
        $lockedProfessional = $this->authenticatedProfessional();
        $clinicHours = $this->clinicHoursConfig();
        $occupiedAppointments = $this->occupiedAppointmentsData();
        $setupWarning = null;
        $returnUrl = $this->resolveReturnUrl($request);

        if (! $this->hasTables(['profissionais', 'procedimentos'])) {
            $setupWarning = 'Os cadastros base de profissionais e procedimentos ainda não foram migrados. O agendamento seguirá usando opções de contingência até as migrations serem executadas.';
        }

        if ($this->isProfessionalUser() && ! $lockedProfessional) {
            $setupWarning = $setupWarning
                ? $setupWarning . ' Seu usuário profissional ainda não está vinculado a um cadastro de profissional de saúde.'
                : 'Seu usuário profissional ainda não está vinculado a um cadastro de profissional de saúde.';
        }

        return view('admin.agendamentos.create', compact('patients', 'preselectedPatient', 'procedureOptions', 'professionalOptions', 'lockedProfessional', 'clinicHours', 'occupiedAppointments', 'setupWarning', 'returnUrl'));
    }

    public function store(Request $request)
    {
        $request->validate(array_merge($this->rules(), [
            'patient_id' => 'required|exists:pacientes,id',
            'horario_final' => 'required|date_format:H:i|after:horario',
        ]), $this->validationMessages(), $this->validationAttributes());

        $this->ensureStartTimeIsNotInPast($request);
        $this->ensureAppointmentWithinClinicHours($request);

        $appointment = Agendamento::create($this->buildAppointmentPayload($request));
        $this->recordActivity('created', $appointment, 'Agendamento criado com validação de procedimento e agenda.', [
            'procedure_id' => $appointment->procedure_id,
            'professional_id' => $appointment->professional_id,
        ]);

        return redirect($this->resolveReturnUrl($request))->with('success', 'Agendamento criado com sucesso!');
    }

    public function show(Request $request, Agendamento $agendamento)
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);
        $agendamento->loadMissing(['patient', 'professional', 'procedure']);
        $agendamento->cpf_exibicao = $agendamento->patient?->cpf ?: null;
        $returnUrl = $this->resolveDetailReturnUrl($request);
        $canEditAppointment = $this->canEditAppointment($agendamento);

        return view('admin.agendamentos.show', compact('agendamento', 'returnUrl', 'canEditAppointment'));
    }

    public function edit(Request $request, Agendamento $agendamento)
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);
        abort_unless($this->canEditAppointment($agendamento), 403);

        $procedureOptions = $this->procedureOptions();
        $professionalOptions = $this->professionalOptions();
        $lockedProfessional = $this->authenticatedProfessional();
        $clinicHours = $this->clinicHoursConfig();
        $occupiedAppointments = $this->occupiedAppointmentsData($agendamento);
        $returnUrl = $this->resolveDetailReturnUrl($request);

        return view('admin.agendamentos.edit', compact('agendamento', 'procedureOptions', 'professionalOptions', 'lockedProfessional', 'clinicHours', 'occupiedAppointments', 'returnUrl'));
    }

    public function update(Request $request, Agendamento $agendamento)
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);
        abort_unless($this->canEditAppointment($agendamento), 403);

        $request->validate(array_merge($this->rules(), [
            'status' => 'required|in:pendente,confirmado,concluido',
            'horario_final' => 'required|date_format:H:i|after:horario',
        ]), $this->validationMessages(), $this->validationAttributes());

        $this->ensureAppointmentWithinClinicHours($request);

        $payload = $this->buildAppointmentPayload($request, $agendamento);
        $payload['status'] = $request->status;

        $agendamento->update($payload);
        $this->recordActivity('updated', $agendamento, 'Agendamento atualizado com revalidação de agenda.', ['status' => $agendamento->status]);

        if ($agendamento->status === 'pendente') {
            return redirect()->route('admin.agendamentos.confirmations')->with('success', 'Agendamento atualizado com sucesso e enviado novamente para Confirmações.');
        }

        return redirect($this->resolveReturnUrl($request))->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function destroy(Request $request, Agendamento $agendamento)
    {
        $this->ensureAuthenticatedProfessionalCanAccessAppointment($agendamento);

        $this->recordActivity('deleted', $agendamento, 'Agendamento removido.', ['nome' => $agendamento->nome]);
        $agendamento->delete();

        return redirect($this->resolveReturnUrl($request))->with('success', 'Agendamento excluído com sucesso!');
    }

    private function resolveReturnUrl(Request $request): string
    {
        $fallback = route('admin.agendamentos.index');

        foreach ([$request->input('return_to'), url()->previous()] as $candidate) {
            if (! is_string($candidate) || trim($candidate) === '') {
                continue;
            }

            if (! $this->isSafeReturnUrl($candidate)) {
                continue;
            }

            if (Str::startsWith($candidate, [route('admin.agendamentos.store'), route('admin.agendamentos.update', ['agendamento' => $request->route('agendamento') ?? 0])])) {
                continue;
            }

            return $candidate;
        }

        return $fallback;
    }

    private function resolveDetailReturnUrl(Request $request): string
    {
        $candidate = $request->query('return_to', $request->input('return_to'));

        if (is_string($candidate) && trim($candidate) !== '') {
            $decodedCandidate = urldecode($candidate);

            if ($this->isSafeReturnUrl($decodedCandidate)) {
                return $decodedCandidate;
            }
        }

        return $this->resolveReturnUrl($request);
    }

    private function canEditAppointment(Agendamento $agendamento): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ($user->isClinicManager()) {
            return false;
        }

        if ((string) $agendamento->status === 'concluido') {
            return $user->canManageCadastrosBase();
        }

        return true;
    }

    private function isSafeReturnUrl(string $url): bool
    {
        $applicationUrls = collect([
            rtrim((string) config('app.url'), '/'),
            rtrim(url('/'), '/'),
        ])->filter()->unique()->values();

        if (Str::startsWith($url, ['/'])) {
            return true;
        }

        return $applicationUrls->contains(fn ($applicationUrl) => Str::startsWith($url, $applicationUrl));
    }

    private function rules(): array
    {
        return [
            'patient_id' => 'nullable|exists:pacientes,id',
            'procedure_id' => $this->hasTables(['procedimentos']) ? 'required|exists:procedimentos,id' : 'nullable',
            'professional_id' => $this->hasTables(['profissionais']) ? 'required|exists:profissionais,id' : 'nullable',
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'required|string|max:20',
            'servico' => 'nullable|string|max:255',
            'medico' => 'nullable|string|max:255',
            'duracao_minutos' => 'required|integer|min:5|max:480',
            'motivo_consulta' => 'nullable|string',
            'observacao_alerta' => 'nullable|string',
            'prioridade' => 'nullable|integer|min:0|max:10',
            'preferencia_turno' => 'nullable|string|max:30',
            'data_limite_espera' => 'nullable|date|after_or_equal:today',
            'data_agendamento' => 'required|date|after_or_equal:today',
            'horario' => 'required|date_format:H:i',
        ];
    }

    private function validationMessages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'patient_id.required' => 'Busque um paciente cadastrado pelo CPF antes de salvar o agendamento.',
            'patient_id.exists' => 'O paciente selecionado não foi encontrado. Cadastre o paciente antes de continuar.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'telefone.required' => 'O campo telefone é obrigatório.',
            'professional_id.required' => 'O campo profissional é obrigatório.',
            'procedure_id.required' => 'O campo procedimento é obrigatório.',
            'data_agendamento.required' => 'O campo data do agendamento é obrigatório.',
            'horario.required' => 'O campo horário inicial é obrigatório.',
            'horario_final.required' => 'Selecione um horário de término válido. Você pode encerrar exatamente no início do intervalo da clínica, mas não pode avançar para dentro dele.',
            'data_agendamento.after_or_equal' => 'A data do agendamento deve ser a partir de hoje.',
            'horario.after_or_equal' => 'O horário inicial não pode ser anterior ao horário atual.',
            'horario_final.after' => 'O horário de término deve ser depois do horário de início.',
            'clinic_hours.opening' => 'O horário inicial deve ser a partir da abertura da clínica.',
            'clinic_hours.closing' => 'O horário de término deve estar dentro do horário de funcionamento da clínica.',
            'clinic_hours.lunch' => 'O agendamento pode terminar exatamente no início do intervalo da clínica, mas não pode avançar para dentro dele.',
        ];
    }

    private function validationAttributes(): array
    {
        return [
            'patient_id' => 'paciente',
            'nome' => 'nome',
            'email' => 'e-mail',
            'telefone' => 'telefone',
            'professional_id' => 'profissional',
            'procedure_id' => 'procedimento',
            'data_agendamento' => 'data do agendamento',
            'horario' => 'horário inicial',
            'horario_final' => 'horário de término',
            'motivo_consulta' => 'motivo do agendamento',
        ];
    }

    private function buildAppointmentPayload(Request $request, ?Agendamento $agendamento = null): array
    {
        $patient = $request->filled('patient_id') ? Patient::find($request->integer('patient_id')) : null;
        $procedure = $this->hasTables(['procedimentos']) && $request->filled('procedure_id') ? Procedure::find($request->integer('procedure_id')) : null;
        $professional = $this->authenticatedProfessional();

        if (! $professional && $this->hasTables(['profissionais', 'agendas_profissionais']) && $request->filled('professional_id')) {
            $professional = Professional::with('schedules')->find($request->integer('professional_id'));
        }

        if ($this->isProfessionalUser() && ! $professional) {
            throw ValidationException::withMessages([
                'professional_id' => 'Seu usuário precisa estar vinculado a um profissional para agendar atendimentos.',
            ]);
        }

        if ($procedure && $professional && $procedure->professional_id && (string) $procedure->professional_id !== (string) $professional->id) {
            throw ValidationException::withMessages([
                'procedure_id' => 'Selecione um procedimento cadastrado para o profissional informado.',
            ]);
        }

        $duration = $this->resolveDurationMinutes($request, $procedure);
        $date = Carbon::parse($request->data_agendamento);
        $ignoreId = $agendamento?->id;

        if ($professional && ! $this->professionalIsAvailable($professional, $date, $request->horario, $duration, $ignoreId)) {
            throw ValidationException::withMessages([
                'horario' => 'O profissional selecionado não atende neste horário ou já possui encaixe conflitante.',
            ]);
        }

        $patientConflict = $patient
            ? $this->patientOverlappingAppointment($patient, $date, $request->horario, $duration, $ignoreId)
            : null;

        if ($patientConflict) {
            $conflictProfessional = $patientConflict->professional?->nome ?: $patientConflict->medico ?: 'outro profissional';
            $conflictStart = substr((string) $patientConflict->horario, 0, 5);
            $conflictEnd = Carbon::createFromFormat('H:i', $conflictStart)
                ->addMinutes((int) ($patientConflict->duracao_minutos ?: 30))
                ->format('H:i');

            throw ValidationException::withMessages([
                'horario' => 'Este paciente ja possui agendamento com ' . $conflictProfessional . ' das ' . $conflictStart . ' as ' . $conflictEnd . ' neste mesmo periodo.',
            ]);
        }

        $existingPatient = $agendamento?->patient;
        $existingProcedure = $agendamento?->procedure;
        $existingProfessional = $agendamento?->professional;

        return [
            'patient_id' => $patient?->id ?? $agendamento?->patient_id,
            'procedure_id' => $procedure?->id ?? $agendamento?->procedure_id,
            'professional_id' => $professional?->id ?? $agendamento?->professional_id,
            'nome' => $patient?->nome ?? $existingPatient?->nome ?? $request->nome,
            'email' => $patient?->email ?? $existingPatient?->email ?? $request->email,
            'telefone' => $patient?->telefone ?? $existingPatient?->telefone ?? $request->telefone,
            'servico' => $procedure?->nome ?? $existingProcedure?->nome ?? $request->servico,
            'medico' => $professional?->nome ?? $existingProfessional?->nome ?? $request->medico,
            'duracao_minutos' => $duration,
            'motivo_consulta' => $request->filled('motivo_consulta') ? $request->motivo_consulta : $agendamento?->motivo_consulta,
            'observacao_alerta' => $request->filled('observacao_alerta') ? $request->observacao_alerta : $agendamento?->observacao_alerta,
            'prioridade' => $request->filled('prioridade') ? $request->input('prioridade') : ($agendamento?->prioridade ?? 0),
            'preferencia_turno' => $request->filled('preferencia_turno') ? $request->preferencia_turno : $agendamento?->preferencia_turno,
            'data_limite_espera' => $request->filled('data_limite_espera') ? $request->data_limite_espera : $agendamento?->data_limite_espera,
            'data_agendamento' => $request->data_agendamento,
            'horario' => $request->horario,
            'descricao' => $request->filled('motivo_consulta') ? $request->motivo_consulta : ($agendamento?->descricao ?? null),
            'status' => $agendamento?->status ?? 'pendente',
            'user_id' => $agendamento?->user_id ?? Auth::id(),
        ];
    }

    private function ensureStartTimeIsNotInPast(Request $request): void
    {
        if (! $request->filled('data_agendamento') || ! $request->filled('horario')) {
            return;
        }

        $appointmentDate = Carbon::parse($request->string('data_agendamento')->toString());

        if (! $appointmentDate->isToday()) {
            return;
        }

        $selectedTime = substr($request->string('horario')->toString(), 0, 5);
        $currentTime = now()->format('H:i');

        if ($selectedTime < $currentTime) {
            throw ValidationException::withMessages([
                'horario' => $this->validationMessages()['horario.after_or_equal'],
            ]);
        }
    }

    private function ensureAppointmentWithinClinicHours(Request $request): void
    {
        $clinicHours = $this->clinicHoursConfig();

        if (! $clinicHours || ! $request->filled('horario') || ! $request->filled('horario_final')) {
            return;
        }

        $openingTime = substr((string) $clinicHours['opening_time'], 0, 5);
        $closingTime = substr((string) $clinicHours['closing_time'], 0, 5);
        $startTime = substr($request->string('horario')->toString(), 0, 5);
        $endTime = substr($request->string('horario_final')->toString(), 0, 5);
        if ($startTime < $openingTime) {
            throw ValidationException::withMessages([
                'horario' => $this->validationMessages()['clinic_hours.opening'],
            ]);
        }

        if ($endTime > $closingTime) {
            throw ValidationException::withMessages([
                'horario_final' => $this->validationMessages()['clinic_hours.closing'],
            ]);
        }

        $lunchStartTime = $clinicHours['lunch_start_time'] ?? null;
        $lunchEndTime = $clinicHours['lunch_end_time'] ?? null;

        if ($lunchStartTime && $lunchEndTime && ! ($endTime <= $lunchStartTime || $startTime >= $lunchEndTime)) {
            throw ValidationException::withMessages([
                'horario_final' => $this->validationMessages()['clinic_hours.lunch'],
            ]);
        }

    }

    private function resolveDurationMinutes(Request $request, ?Procedure $procedure): int
    {
        if ($request->filled('horario') && $request->filled('horario_final')) {
            $start = Carbon::createFromFormat('H:i', substr((string) $request->horario, 0, 5));
            $end = Carbon::createFromFormat('H:i', substr((string) $request->horario_final, 0, 5));

            return max(5, $start->diffInMinutes($end));
        }

        return $procedure?->duracao_minutos ?? $request->integer('duracao_minutos');
    }

    private function professionalIsAvailable(Professional $professional, Carbon $date, string $startTime, int $duration, ?int $ignoreId = null): bool
    {
        $requestedStart = Carbon::createFromFormat('H:i', $startTime);
        $requestedEnd = $requestedStart->copy()->addMinutes($duration);
        $dayOfWeek = $date->dayOfWeekIso;

        $hasCoverage = $professional->schedules->contains(function ($schedule) use ($dayOfWeek, $requestedStart, $requestedEnd) {
            if ((int) $schedule->day_of_week !== $dayOfWeek) {
                return false;
            }

            $scheduleStart = Carbon::createFromFormat('H:i:s', $schedule->start_time);
            $scheduleEnd = Carbon::createFromFormat('H:i:s', $schedule->end_time);

            if (! ($requestedStart->greaterThanOrEqualTo($scheduleStart) && $requestedEnd->lessThanOrEqualTo($scheduleEnd))) {
                return false;
            }

            if ($schedule->break_start_time && $schedule->break_end_time) {
                $breakStart = Carbon::createFromFormat('H:i:s', $schedule->break_start_time);
                $breakEnd = Carbon::createFromFormat('H:i:s', $schedule->break_end_time);

                $overlapsBreak = $requestedStart->lt($breakEnd) && $requestedEnd->gt($breakStart);

                if ($overlapsBreak) {
                    return false;
                }
            }

            return true;
        });

        if (! $hasCoverage) {
            return false;
        }

        $conflicts = Agendamento::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->whereDate('data_agendamento', $date->format('Y-m-d'))
            ->whereIn('status', ['pendente', 'confirmado'])
            ->where(function ($query) use ($professional) {
                $query->where('profissional_id', $professional->id)
                    ->orWhere('medico', $professional->nome);
            })
            ->get();

        return ! $conflicts->contains(fn ($item) => $this->hasTimeOverlap($startTime, $duration, $item->horario, $item->duracao_minutos ?: 30));
    }

    private function hasTimeOverlap(string $startA, int $durationA, string $startB, int $durationB): bool
    {
        $rangeAStart = Carbon::createFromFormat('H:i', substr($startA, 0, 5));
        $rangeAEnd = $rangeAStart->copy()->addMinutes($durationA);
        $rangeBStart = Carbon::createFromFormat('H:i', substr($startB, 0, 5));
        $rangeBEnd = $rangeBStart->copy()->addMinutes($durationB);

        return $rangeAStart->lt($rangeBEnd) && $rangeBStart->lt($rangeAEnd);
    }

    private function patientOverlappingAppointment(Patient $patient, Carbon $date, string $startTime, int $duration, ?int $ignoreId = null): ?Agendamento
    {
        return Agendamento::query()
            ->with('professional:id,nome')
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('paciente_id', $patient->id)
            ->whereDate('data_agendamento', $date->format('Y-m-d'))
            ->whereIn('status', ['pendente', 'confirmado'])
            ->orderBy('horario')
            ->get()
            ->first(fn ($item) => $this->hasTimeOverlap($startTime, $duration, $item->horario, $item->duracao_minutos ?: 30));
    }

    private function decorateAppointment(Agendamento $agendamento, array $professionals, $professionalById, $professionalByName): Agendamento
    {
        $professionalNames = collect($professionals)->pluck('nome')->values();
        $professional = $agendamento->professional_id ? $professionalById->get($agendamento->professional_id) : $professionalByName->get($agendamento->medico);

        $agendamento->medico_exibicao = $agendamento->professional?->nome ?: $agendamento->medico ?: $professionalNames[$agendamento->id % max($professionalNames->count(), 1)];
        $agendamento->cpf_exibicao = $agendamento->patient?->cpf ?: null;
        $procedure = $agendamento->procedure ?: collect($this->procedures())->firstWhere('nome', $agendamento->servico);
        $agendamento->duracao_exibicao = $agendamento->duracao_minutos ?: ($procedure['duracao'] ?? $procedure?->duracao_minutos ?? 30);
        $agendamento->motivo_exibicao = $agendamento->motivo_consulta ?: ($agendamento->descricao ?: 'Consulta clínica');
        $agendamento->status_visual = $this->resolveVisualStatus($agendamento);
        $agendamento->agenda_color = $professional->agenda_color ?? null;

        return $agendamento;
    }

    private function resolveVisualStatus(Agendamento $agendamento): array
    {
        if ($agendamento->status === 'concluido') {
            return ['label' => 'Finalizado', 'color' => '#5f6b7a'];
        }

        if ($agendamento->status === 'confirmado') {
            return ['label' => 'Confirmado', 'color' => '#28a745'];
        }

        if ($agendamento->status === 'cancelado') {
            return ['label' => 'Cancelado', 'color' => '#dc3545'];
        }

        return ['label' => 'Pendente', 'color' => '#ffc107'];
    }

    private function professionals(): array
    {
        if (! $this->hasTables(['profissionais', 'agendas_profissionais'])) {
            return [
                ['id' => null, 'nome' => 'Dra. Helena Souza', 'especialidade' => 'Clínica Geral', 'registro' => 'CRM 12345', 'cor' => '#0d6efd', 'disponibilidade' => 'Seg a Sex, 08h às 17h'],
                ['id' => null, 'nome' => 'Dr. Marcos Lima', 'especialidade' => 'Cardiologia', 'registro' => 'CRM 67890', 'cor' => '#28a745', 'disponibilidade' => 'Ter e Qui, 09h às 18h'],
                ['id' => null, 'nome' => 'Dra. Carla Mendes', 'especialidade' => 'Dermatologia', 'registro' => 'CRM 54321', 'cor' => '#dc3545', 'disponibilidade' => 'Seg, Qua e Sex, 13h às 19h'],
            ];
        }

        $records = Professional::with('schedules')->where('ativo', true)->orderBy('nome')->get();

        if ($records->isNotEmpty()) {
            $dayLabels = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb', 7 => 'Dom'];
            $clinicHours = $this->clinicHoursConfig();

            return $records->map(function ($professional) use ($dayLabels, $clinicHours) {
                $availability = $professional->schedules
                    ->map(function ($schedule) use ($dayLabels, $clinicHours) {
                        $effectiveSchedule = $this->adjustScheduleToClinicHours($schedule, $clinicHours);

                        if (! $effectiveSchedule) {
                            return null;
                        }

                        return $dayLabels[$schedule->day_of_week] . ' ' . $effectiveSchedule['start_time'] . ' às ' . $effectiveSchedule['end_time'];
                    })
                    ->filter()
                    ->join(' • ');

                return [
                    'id' => $professional->id,
                    'nome' => $professional->nome,
                    'especialidade' => $professional->especialidade_principal,
                    'registro' => $professional->registro_completo,
                    'cor' => $professional->agenda_color,
                    'disponibilidade' => $availability,
                ];
            })->all();
        }

        return [
            ['id' => null, 'nome' => 'Dra. Helena Souza', 'especialidade' => 'Clínica Geral', 'registro' => 'CRM 12345', 'cor' => '#0d6efd', 'disponibilidade' => 'Seg a Sex, 08h às 17h'],
            ['id' => null, 'nome' => 'Dr. Marcos Lima', 'especialidade' => 'Cardiologia', 'registro' => 'CRM 67890', 'cor' => '#28a745', 'disponibilidade' => 'Ter e Qui, 09h às 18h'],
            ['id' => null, 'nome' => 'Dra. Carla Mendes', 'especialidade' => 'Dermatologia', 'registro' => 'CRM 54321', 'cor' => '#dc3545', 'disponibilidade' => 'Seg, Qua e Sex, 13h às 19h'],
        ];
    }

    private function procedures(): array
    {
        if (! $this->hasTables(['procedimentos'])) {
            return [
                ['id' => null, 'professional_id' => null, 'nome' => 'Consulta', 'duracao' => 30, 'valor' => '150,00', 'codigo_tuss' => null],
                ['id' => null, 'professional_id' => null, 'nome' => 'Exame', 'duracao' => 60, 'valor' => '220,00', 'codigo_tuss' => null],
                ['id' => null, 'professional_id' => null, 'nome' => 'Procedimento', 'duracao' => 120, 'valor' => '480,00', 'codigo_tuss' => null],
                ['id' => null, 'professional_id' => null, 'nome' => 'Retorno', 'duracao' => 20, 'valor' => '90,00', 'codigo_tuss' => null],
            ];
        }

        $records = Procedure::where('ativo', true)->orderBy('nome')->get();

        if ($records->isNotEmpty()) {
            return $records->map(fn ($procedure) => [
                'id' => $procedure->id,
                'professional_id' => $procedure->professional_id,
                'nome' => $procedure->nome,
                'duracao' => $procedure->duracao_minutos,
                'valor' => number_format((float) $procedure->valor_particular, 2, ',', '.'),
                'codigo_tuss' => $procedure->codigo_tuss,
            ])->all();
        }

        return [
            ['id' => null, 'professional_id' => null, 'nome' => 'Consulta', 'duracao' => 30, 'valor' => '150,00', 'codigo_tuss' => null],
            ['id' => null, 'professional_id' => null, 'nome' => 'Exame', 'duracao' => 60, 'valor' => '220,00', 'codigo_tuss' => null],
            ['id' => null, 'professional_id' => null, 'nome' => 'Procedimento', 'duracao' => 120, 'valor' => '480,00', 'codigo_tuss' => null],
            ['id' => null, 'professional_id' => null, 'nome' => 'Retorno', 'duracao' => 20, 'valor' => '90,00', 'codigo_tuss' => null],
        ];
    }

    private function professionalOptions(): array
    {
        $lockedProfessional = $this->authenticatedProfessional();

        return collect($this->professionals())->filter(function ($professional) use ($lockedProfessional) {
            if (! $lockedProfessional) {
                return true;
            }

            return (string) $professional['id'] === (string) $lockedProfessional->id;
        })->map(function ($professional) {
            $clinicHours = $this->clinicHoursConfig();
            $record = $this->hasTables(['profissionais', 'agendas_profissionais']) && $professional['id']
                ? Professional::with('schedules')->find($professional['id'])
                : null;

            return [
                'id' => $professional['id'],
                'nome' => $professional['nome'],
                'especialidade' => $professional['especialidade'],
                'cor' => $professional['cor'] ?? '#0d6efd',
                'schedules' => $record?->schedules?->map(function ($schedule) use ($clinicHours) {
                    $effectiveSchedule = $this->adjustScheduleToClinicHours($schedule, $clinicHours);

                    if (! $effectiveSchedule) {
                        return null;
                    }

                    return [
                        'day_of_week' => $schedule->day_of_week,
                        'start_time' => $effectiveSchedule['start_time'],
                        'break_start_time' => $effectiveSchedule['break_start_time'],
                        'break_end_time' => $effectiveSchedule['break_end_time'],
                        'end_time' => $effectiveSchedule['end_time'],
                    ];
                })->filter()->values()->all() ?? [],
            ];
        })->values()->all();
    }

    private function adjustScheduleToClinicHours($schedule, ?array $clinicHours): ?array
    {
        $effectiveStart = substr((string) $schedule->start_time, 0, 5);
        $effectiveEnd = substr((string) $schedule->end_time, 0, 5);

        if ($clinicHours) {
            $effectiveStart = max($effectiveStart, $clinicHours['opening_time']);
            $effectiveEnd = min($effectiveEnd, $clinicHours['closing_time']);
        }

        if ($effectiveStart >= $effectiveEnd) {
            return null;
        }

        $effectiveBreakStart = $schedule->break_start_time ? substr((string) $schedule->break_start_time, 0, 5) : null;
        $effectiveBreakEnd = $schedule->break_end_time ? substr((string) $schedule->break_end_time, 0, 5) : null;

        if ($effectiveBreakStart && $effectiveBreakEnd) {
            $effectiveBreakStart = max($effectiveBreakStart, $effectiveStart);
            $effectiveBreakEnd = min($effectiveBreakEnd, $effectiveEnd);

            if (! ($effectiveStart < $effectiveBreakStart && $effectiveBreakStart < $effectiveBreakEnd && $effectiveBreakEnd < $effectiveEnd)) {
                $effectiveBreakStart = null;
                $effectiveBreakEnd = null;
            }
        } else {
            $effectiveBreakStart = null;
            $effectiveBreakEnd = null;
        }

        return [
            'start_time' => $effectiveStart,
            'break_start_time' => $effectiveBreakStart,
            'break_end_time' => $effectiveBreakEnd,
            'end_time' => $effectiveEnd,
        ];
    }

    private function isProfessionalUser(): bool
    {
        $user = Auth::user();

        return (bool) $user
            && $user->normalizedRole() === 'profissional';
    }

    private function authenticatedProfessional(): ?Professional
    {
        if (! $this->isProfessionalUser() || ! $this->hasTables(['profissionais'])) {
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

    private function restrictAppointmentsForAuthenticatedProfessional(Collection $agendamentos): Collection
    {
        $professional = $this->authenticatedProfessional();

        if (! $professional) {
            if ($this->isProfessionalUser()) {
                return collect();
            }

            return $agendamentos;
        }

        return $agendamentos->filter(function (Agendamento $agendamento) use ($professional) {
            $appointmentProfessionalName = mb_strtolower(trim((string) ($agendamento->professional?->nome ?: $agendamento->medico)));
            $authenticatedProfessionalName = mb_strtolower(trim((string) $professional->nome));

            if ((string) $agendamento->professional_id === (string) $professional->id) {
                return true;
            }

            if ($appointmentProfessionalName === '' || $authenticatedProfessionalName === '') {
                return false;
            }

            return $appointmentProfessionalName === $authenticatedProfessionalName;
        })->values();
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

    private function procedureOptions(): array
    {
        if (! $this->hasTables(['procedimentos'])) {
            return $this->procedures();
        }

        $records = Procedure::where('ativo', true)->orderBy('nome')->get();

        if ($records->isNotEmpty()) {
            return $records->map(fn ($procedure) => [
                'id' => $procedure->id,
                'professional_id' => $procedure->professional_id,
                'nome' => $procedure->nome,
                'duracao' => $procedure->duracao_minutos,
                'valor' => number_format((float) $procedure->valor_particular, 2, ',', '.'),
                'codigo_tuss' => $procedure->codigo_tuss,
            ])->all();
        }

        return $this->procedures();
    }

    private function clinicHoursConfig(): ?array
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

    private function occupiedAppointmentsData(?Agendamento $ignoredAppointment = null): array
    {
        if (! $this->hasTables(['agendamentos'])) {
            return [];
        }

        return Agendamento::query()
            ->with('professional:id,nome')
            ->when($ignoredAppointment, fn ($query) => $query->where('id', '!=', $ignoredAppointment->id))
            ->whereIn('status', ['pendente', 'confirmado'])
            ->get(['id', 'profissional_id', 'medico', 'data_agendamento', 'horario', 'duracao_minutos', 'status'])
            ->map(function (Agendamento $agendamento) {
                $startTime = substr((string) $agendamento->horario, 0, 5);
                $duration = (int) ($agendamento->duracao_minutos ?: 30);
                $endTime = Carbon::createFromFormat('H:i', $startTime)
                    ->addMinutes($duration)
                    ->format('H:i');

                return [
                    'date' => $agendamento->data_agendamento?->format('Y-m-d'),
                    'professional_id' => $agendamento->professional_id,
                    'professional_name' => $agendamento->professional?->nome ?: $agendamento->medico,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ];
            })
            ->values()
            ->all();
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
}
