@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Logs de Pacientes</h1>
    </div>

    @php
        $actionLabels = [
            'created' => 'Cadastro',
            'updated' => 'Alteração',
            'deleted' => 'Exclusão',
        ];

        $fieldLabels = [
            'nome' => 'Nome',
            'cpf' => 'CPF',
            'email' => 'E-mail',
            'telefone' => 'Celular',
            'data_nascimento' => 'Data de nascimento',
            'sexo' => 'Sexo',
            'endereco' => 'Endereço',
            'numero_endereco' => 'Número',
            'cep' => 'CEP',
            'bairro' => 'Bairro',
            'tipo_moradia' => 'Tipo de imóvel',
            'complemento' => 'Complemento',
        ];

        $formatCpf = function ($value) {
            $digits = preg_replace('/\D/', '', (string) $value);

            if (strlen($digits) === 11) {
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
            }

            return $value ?: 'Não informado';
        };

        $formatValue = function ($field, $value) use ($formatCpf) {
            if ($field === 'cpf') {
                return $formatCpf($value);
            }

            if ($field === 'tipo_moradia') {
                return $value ? ucfirst((string) $value) : 'Não informado';
            }

            if ($field === 'data_nascimento' && $value) {
                try {
                    return \Illuminate\Support\Carbon::parse($value)->format('d/m/Y');
                } catch (\Throwable $exception) {
                    return $value;
                }
            }

            return $value ?: 'Não informado';
        };

        $resolvePatientName = function ($log) {
            $properties = $log->properties ?? [];
            $targetUser = $properties['target_user'] ?? [];
            $before = is_array($properties['before'] ?? null) ? $properties['before'] : [];
            $after = is_array($properties['after'] ?? null) ? $properties['after'] : [];

            return $targetUser['nome'] ?? $before['nome'] ?? $after['nome'] ?? 'Paciente não identificado';
        };

        $resolvePatientCpf = function ($log) use ($formatCpf) {
            $properties = $log->properties ?? [];
            $targetUser = $properties['target_user'] ?? [];
            $before = is_array($properties['before'] ?? null) ? $properties['before'] : [];
            $after = is_array($properties['after'] ?? null) ? $properties['after'] : [];

            return $formatCpf($targetUser['cpf'] ?? $before['cpf'] ?? $after['cpf'] ?? null);
        };

        $changesFor = function ($log) {
            $properties = $log->properties ?? [];
            $before = is_array($properties['before'] ?? null) ? $properties['before'] : [];
            $after = is_array($properties['after'] ?? null) ? $properties['after'] : [];
            $keys = collect(array_unique(array_merge(array_keys($before), array_keys($after))));

            return $keys->filter(function ($key) use ($before, $after) {
                return ($before[$key] ?? null) !== ($after[$key] ?? null);
            });
        };
    @endphp

    <div class="section-body">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Filtros</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.patients.logs') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="patient_cpf">CPF do paciente</label>
                                <input type="text" class="form-control" id="patient_cpf" name="patient_cpf" value="{{ $patientCpfSearch ? $formatCpf($patientCpfSearch) : '' }}" placeholder="000.000.000-00">
                            </div>
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-sm px-3">Filtrar</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="activity_date">Data da atividade</label>
                                <input type="date" class="form-control" id="activity_date" name="activity_date" value="{{ $activityDateSearch }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="action_type">Tipo de alteração</label>
                                <select class="form-control" id="action_type" name="action_type">
                                    <option value="">Todos</option>
                                    <option value="created" {{ $actionTypeSearch === 'created' ? 'selected' : '' }}>Cadastro</option>
                                    <option value="updated" {{ $actionTypeSearch === 'updated' ? 'selected' : '' }}>Alteração</option>
                                    <option value="deleted" {{ $actionTypeSearch === 'deleted' ? 'selected' : '' }}>Exclusão</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Atividades registradas</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Paciente</th>
                                <th>CPF</th>
                                <th>Tipo de alteração</th>
                                <th>Responsável</th>
                                <th>Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patientLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>{{ $resolvePatientName($log) }}</td>
                                    <td>{{ $resolvePatientCpf($log) }}</td>
                                    <td>{{ $actionLabels[$log->action] ?? ucfirst($log->action) }}</td>
                                    <td>{{ trim(($log->user->nome ?? '') . ' ' . ($log->user->sobrenome ?? '')) ?: 'Sistema' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#patient-log-details-{{ $log->id }}" aria-expanded="false" aria-controls="patient-log-details-{{ $log->id }}">
                                            Ver detalhes
                                        </button>
                                    </td>
                                </tr>
                                <tr class="collapse" id="patient-log-details-{{ $log->id }}">
                                    <td colspan="6" class="bg-light">
                                        <div class="py-2">
                                            <p class="mb-2"><strong>Descrição:</strong> {{ $log->description }}</p>

                                            @php($changes = $changesFor($log))

                                            @if($changes->isNotEmpty())
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered mb-0 bg-white">
                                                        <thead>
                                                            <tr>
                                                                <th>Campo</th>
                                                                <th>Antes</th>
                                                                <th>Depois</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($changes as $key)
                                                                <tr>
                                                                    <td>{{ $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</td>
                                                                    <td>{{ $formatValue($key, data_get($log->properties, 'before.' . $key)) }}</td>
                                                                    <td>{{ $formatValue($key, data_get($log->properties, 'after.' . $key)) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @elseif(! empty(data_get($log->properties, 'before')))
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered mb-0 bg-white">
                                                        <thead>
                                                            <tr>
                                                                <th>Campo</th>
                                                                <th>Valor registrado</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach(data_get($log->properties, 'before') as $key => $value)
                                                                <tr>
                                                                    <td>{{ $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</td>
                                                                    <td>{{ $formatValue($key, $value) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @elseif(! empty(data_get($log->properties, 'after')))
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered mb-0 bg-white">
                                                        <thead>
                                                            <tr>
                                                                <th>Campo</th>
                                                                <th>Valor registrado</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach(data_get($log->properties, 'after') as $key => $value)
                                                                <tr>
                                                                    <td>{{ $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</td>
                                                                    <td>{{ $formatValue($key, $value) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-muted">Nenhum detalhe adicional registrado para esta ação.</div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum log de paciente encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($patientLogs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">
                        {{ $patientLogs->links('vendor.pagination.patients-blocks') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
