<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Patient;
use App\Models\User;
use App\Traits\RecordsActivity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class PatientController extends Controller
{
    use RecordsActivity;

    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = 6;

        $patientsQuery = Patient::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim($request->string('q')->toString());
                $cpfSearch = preg_replace('/\D+/', '', $search);

                $query->where(function ($innerQuery) use ($search, $cpfSearch) {
                    if ($search !== '') {
                        $innerQuery->where('nome', 'like', "%{$search}%");
                    }

                    if ($cpfSearch !== '') {
                        $innerQuery->orWhere('cpf', 'like', "%{$cpfSearch}%");
                    }
                });

                if ($search === '' && $cpfSearch === '') {
                    $query->whereRaw('1 = 0');
                }
            })
            ->orderBy('nome');

        if ($request->input('status') === 'completo' || $request->input('status') === 'incompleto') {
            $patients = $patientsQuery->get();

            if ($request->input('status') === 'completo') {
                $patients = $patients->filter(fn ($patient) => empty($patient->cadastro_pendencias))->values();
            }

            if ($request->input('status') === 'incompleto') {
                $patients = $patients->filter(fn ($patient) => ! empty($patient->cadastro_pendencias))->values();
            }

            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            $patients = new LengthAwarePaginator(
                $patients->forPage($currentPage, $perPage)->values(),
                $patients->count(),
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        } else {
            $patients = $patientsQuery->paginate($perPage)->withQueryString();
        }

        return view('admin.patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.agendamentos.create', ['tab' => 'paciente']);
    }

    public function duplicateCheck(Request $request): JsonResponse
    {
        $ignorePatientId = $request->filled('patient_id') ? (int) $request->input('patient_id') : null;
        $conflicts = [];

        foreach (['cpf', 'email', 'telefone'] as $field) {
            $message = $this->patientDuplicateMessage($field, $request->input($field), $ignorePatientId);

            if ($message !== null) {
                $conflicts[$field] = $message;
            }
        }

        return response()->json([
            'conflicts' => $conflicts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $hasCepColumn = Schema::hasColumn('patients', 'cep');
        $hasBairroColumn = Schema::hasColumn('patients', 'bairro');
        $hasComplementoColumn = Schema::hasColumn('patients', 'complemento');
        $hasNumeroEnderecoColumn = Schema::hasColumn('patients', 'numero_endereco');
        $hasTipoMoradiaColumn = Schema::hasColumn('patients', 'tipo_moradia');
        $duplicateRules = $this->patientDuplicateRules();

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => ['nullable', 'string', 'max:20', ...$duplicateRules['cpf']],
            'email' => ['required', 'email', ...$duplicateRules['email']],
            'telefone' => ['required', 'string', 'max:20', ...$duplicateRules['telefone']],
            'draft_key' => 'nullable|string|max:120',
            'cep' => $hasCepColumn ? 'nullable|string|max:10' : 'nullable',
            'bairro' => $hasBairroColumn ? 'nullable|string|max:255' : 'nullable',
            'complemento' => $hasComplementoColumn ? 'nullable|string|max:255' : 'nullable',
            'numero_endereco' => $hasNumeroEnderecoColumn ? 'nullable|string|max:20' : 'nullable',
            'tipo_moradia' => $hasTipoMoradiaColumn ? 'nullable|in:casa,apartamento,condominio,sobrado,comercial,rural,outro' : 'nullable',
            'telefone_recado' => 'nullable|string|max:20',
            'convenio' => 'nullable|string|max:255',
            'numero_carteirinha' => 'nullable|string|max:255',
            'data_nascimento' => 'nullable|date|before_or_equal:today',
            'sexo' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
        ], $this->patientValidationMessages(), $this->patientValidationAttributes());

        $payload = $request->only([
            'nome',
            'cpf',
            'email',
            'telefone',
            'data_nascimento',
            'sexo',
            'endereco',
        ]);

        if ($hasCepColumn) {
            $payload['cep'] = $request->input('cep');
        }

        if ($hasBairroColumn) {
            $payload['bairro'] = $request->input('bairro');
        }

        if ($hasComplementoColumn) {
            $payload['complemento'] = $request->input('complemento');
        }

        if ($hasNumeroEnderecoColumn) {
            $payload['numero_endereco'] = $request->input('numero_endereco');
        }

        if ($hasTipoMoradiaColumn) {
            $payload['tipo_moradia'] = $request->input('tipo_moradia');
        }

        $patient = Patient::create($payload);

        $this->recordActivity('created', $patient, 'Paciente cadastrado.', [
            'submenu' => 'Pacientes',
            'target_user' => $this->patientLogIdentity($patient),
            'after' => $this->patientLogPayload($patient),
        ]);

        if ($request->input('origem') === 'agendamento') {
            $clearDraftKeys = array_values(array_filter([
                trim($request->string('draft_key')->toString()),
            ]));

            return redirect()
                ->route('admin.agendamentos.create', ['tab' => 'paciente'])
                ->with('clear_draft_keys', $clearDraftKeys)
                ->with('success', 'Paciente cadastrado com sucesso!');
        }

        return redirect()->route('admin.patients.index')->with('success', 'Paciente cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $history = $patient->agendamentos()
            ->latest('data_agendamento')
            ->paginate(6, ['*'], 'history_page')
            ->withQueryString();

        return view('admin.patients.show', compact('patient', 'history'));
    }

    public function logs(Request $request): View
    {
        $patientLogs = collect();
        $patientCpfSearch = preg_replace('/\D/', '', (string) $request->input('patient_cpf'));
        $activityDateSearch = trim((string) $request->input('activity_date'));
        $actionTypeSearch = in_array($request->input('action_type'), ['created', 'updated', 'deleted'], true)
            ? $request->input('action_type')
            : '';

        if (Schema::hasTable('activity_logs')) {
            $patientLogsQuery = ActivityLog::with('user')
                ->where('subject_type', Patient::class)
                ->latest();

            if ($actionTypeSearch !== '') {
                $patientLogsQuery->where('action', $actionTypeSearch);
            }

            if ($activityDateSearch !== '') {
                $patientLogsQuery->whereDate('created_at', $activityDateSearch);
            }

            if ($patientCpfSearch !== '') {
                $patientLogsQuery->where(function ($query) use ($patientCpfSearch) {
                    $query->where('properties->target_user->cpf', $patientCpfSearch)
                        ->orWhere('properties->before->cpf', $patientCpfSearch)
                        ->orWhere('properties->after->cpf', $patientCpfSearch);
                });
            }

            $patientLogs = $patientLogsQuery->paginate(8, ['*'], 'patient_logs_page')->withQueryString();
        }

        return view('admin.modules.patients.logs', compact('patientLogs', 'patientCpfSearch', 'activityDateSearch', 'actionTypeSearch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $previousValues = $this->patientLogPayload($patient);
        $hasCepColumn = Schema::hasColumn('patients', 'cep');
        $hasBairroColumn = Schema::hasColumn('patients', 'bairro');
        $hasComplementoColumn = Schema::hasColumn('patients', 'complemento');
        $hasNumeroEnderecoColumn = Schema::hasColumn('patients', 'numero_endereco');
        $hasTipoMoradiaColumn = Schema::hasColumn('patients', 'tipo_moradia');
        $duplicateRules = $this->patientDuplicateRules($patient->id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => ['nullable', 'string', 'max:20', ...$duplicateRules['cpf']],
            'email' => ['required', 'email', ...$duplicateRules['email']],
            'telefone' => ['required', 'string', 'max:20', ...$duplicateRules['telefone']],
            'cep' => $hasCepColumn ? 'nullable|string|max:10' : 'nullable',
            'bairro' => $hasBairroColumn ? 'nullable|string|max:255' : 'nullable',
            'complemento' => $hasComplementoColumn ? 'nullable|string|max:255' : 'nullable',
            'numero_endereco' => $hasNumeroEnderecoColumn ? 'nullable|string|max:20' : 'nullable',
            'tipo_moradia' => $hasTipoMoradiaColumn ? 'nullable|in:casa,apartamento,condominio,sobrado,comercial,rural,outro' : 'nullable',
            'telefone_recado' => 'nullable|string|max:20',
            'convenio' => 'nullable|string|max:255',
            'numero_carteirinha' => 'nullable|string|max:255',
            'data_nascimento' => 'nullable|date|before_or_equal:today',
            'sexo' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
        ], $this->patientValidationMessages(), $this->patientValidationAttributes());

        $payload = $request->only([
            'nome',
            'cpf',
            'email',
            'telefone',
            'data_nascimento',
            'sexo',
            'endereco',
        ]);

        if ($hasCepColumn) {
            $payload['cep'] = $request->input('cep');
        }

        if ($hasBairroColumn) {
            $payload['bairro'] = $request->input('bairro');
        }

        if ($hasComplementoColumn) {
            $payload['complemento'] = $request->input('complemento');
        }

        if ($hasNumeroEnderecoColumn) {
            $payload['numero_endereco'] = $request->input('numero_endereco');
        }

        if ($hasTipoMoradiaColumn) {
            $payload['tipo_moradia'] = $request->input('tipo_moradia');
        }

        $patient->update($payload);

        $this->recordActivity('updated', $patient, 'Paciente atualizado.', [
            'submenu' => 'Pacientes',
            'target_user' => $this->patientLogIdentity($patient),
            'before' => $previousValues,
            'after' => $this->patientLogPayload($patient->fresh()),
        ]);

        return redirect()->route('admin.patients.index')->with('success', 'Paciente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $previousValues = $this->patientLogPayload($patient);

        $this->recordActivity('deleted', $patient, 'Paciente excluido.', [
            'submenu' => 'Pacientes',
            'target_user' => $this->patientLogIdentity($patient),
            'before' => $previousValues,
        ]);

        $patient->delete();
        return redirect()->route('admin.patients.index')->with('success', 'Paciente inativado com sucesso!');
    }

    private function patientValidationMessages(): array
    {
        return [
            'cpf.unique' => 'O CPF informado nao foi salvo porque ja esta vinculado a outro paciente cadastrado.',
            'email.unique' => 'O e-mail informado nao foi salvo porque ja esta vinculado a outro paciente cadastrado.',
            'telefone.unique' => 'O celular informado nao foi salvo porque ja esta vinculado a outro paciente cadastrado.',
            'email.email' => 'Informe um e-mail valido para concluir o cadastro.',
            'data_nascimento.before_or_equal' => 'A data de nascimento nao pode ser maior que a data atual.',
            'nome.required' => 'Informe o nome completo do paciente.',
            'telefone.required' => 'Informe o celular do paciente.',
            'email.required' => 'Informe o e-mail do paciente.',
        ];
    }

    private function patientValidationAttributes(): array
    {
        return [
            'cpf' => 'CPF',
            'email' => 'e-mail',
            'telefone' => 'celular',
        ];
    }

    private function patientDuplicateRules(?int $ignorePatientId = null): array
    {
        return [
            'cpf' => [function ($attribute, $value, $fail) use ($ignorePatientId) {
                $message = $this->patientDuplicateMessage('cpf', $value, $ignorePatientId);

                if ($message !== null) {
                    $fail($message);
                }
            }],
            'email' => [function ($attribute, $value, $fail) use ($ignorePatientId) {
                $message = $this->patientDuplicateMessage('email', $value, $ignorePatientId);

                if ($message !== null) {
                    $fail($message);
                }
            }],
            'telefone' => [function ($attribute, $value, $fail) use ($ignorePatientId) {
                $message = $this->patientDuplicateMessage('telefone', $value, $ignorePatientId);

                if ($message !== null) {
                    $fail($message);
                }
            }],
        ];
    }

    private function patientDuplicateMessage(string $field, mixed $value, ?int $ignorePatientId = null): ?string
    {
        $normalizedValue = $this->normalizePatientComparableValue($field, $value);

        if ($normalizedValue === '') {
            return null;
        }

        if (! $this->shouldCheckPatientDuplicate($field, $normalizedValue)) {
            return null;
        }

        if ($this->patientFieldExists($field, $normalizedValue, $ignorePatientId)) {
            return match ($field) {
                'cpf' => 'O paciente nao foi salvo porque este CPF ja foi cadastrado em Pacientes.',
                'email' => 'O paciente nao foi salvo porque este e-mail ja foi cadastrado em Pacientes.',
                'telefone' => 'O paciente nao foi salvo porque este celular ja foi cadastrado em Pacientes.',
                default => null,
            };
        }

        if ($this->userFieldExists($field, $normalizedValue)) {
            return match ($field) {
                'cpf' => 'O paciente nao foi salvo porque este CPF ja foi cadastrado em Usuarios e Permissoes.',
                'email' => 'O paciente nao foi salvo porque este e-mail ja foi cadastrado em Usuarios e Permissoes.',
                'telefone' => 'O paciente nao foi salvo porque este celular ja foi cadastrado em Usuarios e Permissoes.',
                default => null,
            };
        }

        return null;
    }

    private function shouldCheckPatientDuplicate(string $field, string $normalizedValue): bool
    {
        return match ($field) {
            'cpf' => strlen($normalizedValue) === 11,
            'email' => str_contains($normalizedValue, '@'),
            'telefone' => strlen($normalizedValue) >= 10,
            default => false,
        };
    }

    private function normalizePatientComparableValue(string $field, mixed $value): string
    {
        $stringValue = trim((string) $value);

        return match ($field) {
            'cpf', 'telefone' => preg_replace('/\D/', '', $stringValue),
            'email' => mb_strtolower($stringValue),
            default => $stringValue,
        };
    }

    private function patientFieldExists(string $field, string $normalizedValue, ?int $ignorePatientId = null): bool
    {
        if (! Schema::hasTable('patients') || ! Schema::hasColumn('patients', $field)) {
            return false;
        }

        return Patient::query()
            ->when($ignorePatientId !== null, fn ($query) => $query->where('id', '!=', $ignorePatientId))
            ->whereNotNull($field)
            ->get(['id', $field])
            ->contains(function (Patient $patient) use ($field, $normalizedValue) {
                return $this->normalizePatientComparableValue($field, $patient->{$field}) === $normalizedValue;
            });
    }

    private function userFieldExists(string $field, string $normalizedValue): bool
    {
        $userColumn = $field === 'telefone' ? 'fone' : $field;

        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', $userColumn)) {
            return false;
        }

        return User::query()
            ->whereNotNull($userColumn)
            ->get([$userColumn])
            ->contains(function (User $user) use ($field, $userColumn, $normalizedValue) {
                return $this->normalizePatientComparableValue($field, $user->{$userColumn}) === $normalizedValue;
            });
    }

    private function patientLogIdentity(Patient $patient): array
    {
        return [
            'id' => $patient->id,
            'nome' => $patient->nome,
            'cpf' => preg_replace('/\D/', '', (string) $patient->cpf) ?: null,
            'email' => $patient->email,
        ];
    }

    private function patientLogPayload(Patient $patient): array
    {
        return [
            'nome' => $patient->nome,
            'cpf' => preg_replace('/\D/', '', (string) $patient->cpf) ?: null,
            'email' => $patient->email,
            'telefone' => $patient->telefone,
            'data_nascimento' => optional($patient->data_nascimento)->format('Y-m-d'),
            'sexo' => $patient->sexo,
            'endereco' => $patient->endereco,
            'numero_endereco' => $patient->numero_endereco,
            'cep' => $patient->cep,
            'bairro' => $patient->bairro,
            'tipo_moradia' => $patient->tipo_moradia,
            'complemento' => $patient->complemento,
        ];
    }
}
