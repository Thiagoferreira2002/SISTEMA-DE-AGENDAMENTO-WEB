@extends('admin.layouts.master')

@section('content')
<style>
    .log-change-before {
        background-color: #fff5f5;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        padding: 10px 12px;
    }

    .log-change-after {
        background-color: #f2fff5;
        border: 1px solid #b7e4c7;
        border-radius: 8px;
        padding: 10px 12px;
    }

    .log-schedule-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .log-schedule-item {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 12px;
        line-height: 1.4;
    }

    .log-schedule-item-before {
        background-color: #ffe3e3;
        color: #9b1c1c;
    }

    .log-schedule-item-after {
        background-color: #d9fbe3;
        color: #166534;
    }

    .logs-filter-control {
        min-width: 210px;
        max-width: 210px;
    }

    .logs-filter-select {
        min-width: 225px;
        max-width: 225px;
    }

</style>
<section class="section">
    <div class="section-header">
        <h1>Logs de Atividade</h1>
    </div>

    @php
        $actionLabels = [
            'created' => 'Cadastro',
            'updated' => 'Alteração',
            'deleted' => 'Exclusão',
        ];

        $fieldLabels = [
            'nome' => 'Nome',
            'duracao_minutos' => 'Duração',
            'sobrenome' => 'Sobrenome',
            'cpf' => 'CPF',
            'fone' => 'Telefone',
            'user_id' => 'Usuário vinculado',
            'role' => 'Papel de acesso',
            'permissions' => 'Permissões de submenu',
            'status' => 'Status de acesso',
            'email' => 'E-mail',
            'especialidade_principal' => 'Especialidade principal',
            'registro_tipo' => 'Conselho de saúde',
            'registro_numero' => 'Número do registro',
            'agenda_color' => 'Cor da agenda',
            'schedules' => 'Vínculo de agenda',
            'id' => 'Código',
        ];

        $roleLabels = [
            'medico' => 'Profissional',
            'profissional' => 'Profissional',
            'recepcionista' => 'Recepcionista',
            'admin' => 'Administrador',
        ];

        $statusLabels = [
            'ativo' => 'Ativo',
            'cancelado' => 'Inativo',
        ];

        $permissionLabels = [
            'agendamentos' => 'Agendamentos',
            'pacientes' => 'Pacientes',
            'painel_doutor' => 'Painel do Profissional',
        ];

        $weekDays = [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        $formatCpf = function ($value) {
            $digits = preg_replace('/\D/', '', (string) $value);

            if (strlen($digits) === 11) {
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
            }

            return $value ?: 'Não informado';
        };

        $formatScheduleEntry = function ($schedule) use ($weekDays) {
            if (! is_array($schedule)) {
                return (string) $schedule;
            }

            $day = $weekDays[(int) ($schedule['day_of_week'] ?? 0)] ?? ($schedule['day_of_week'] ?? 'Dia não informado');
            $start = isset($schedule['start_time']) ? substr((string) $schedule['start_time'], 0, 5) : '--:--';
            $end = isset($schedule['end_time']) ? substr((string) $schedule['end_time'], 0, 5) : '--:--';
            $breakStart = ! empty($schedule['break_start_time']) ? substr((string) $schedule['break_start_time'], 0, 5) : null;
            $breakEnd = ! empty($schedule['break_end_time']) ? substr((string) $schedule['break_end_time'], 0, 5) : null;

            return $day . ' ' . $start . ' às ' . $end . ($breakStart && $breakEnd ? ' • descanso ' . $breakStart . ' às ' . $breakEnd : '');
        };

        $formatLogValue = function ($field, $value) use ($roleLabels, $statusLabels, $permissionLabels, $formatCpf, $formatScheduleEntry) {
            if (is_array($value)) {
                if ($field === 'permissions') {
                    if (empty($value)) {
                        return 'Nenhum submenu liberado';
                    }

                    return collect($value)
                        ->map(fn ($item) => $permissionLabels[$item] ?? $item)
                        ->implode(', ');
                }

                if ($field === 'schedules') {
                    if (empty($value)) {
                        return 'Sem agenda definida';
                    }

                    return collect($value)
                        ->map(fn ($item) => $formatScheduleEntry($item))
                        ->implode(' | ');
                }

                if (empty($value)) {
                    return 'Nenhum';
                }

                return collect($value)->map(function ($item) use ($field, $roleLabels, $statusLabels) {
                    if ($field === 'role') {
                        return $roleLabels[$item] ?? $item;
                    }

                    if ($field === 'status') {
                        return $statusLabels[$item] ?? $item;
                    }

                    return $item;
                })->implode(', ');
            }

            if ($field === 'role') {
                return $roleLabels[$value] ?? ($value ?: 'Não informado');
            }

            if ($field === 'status') {
                return $statusLabels[$value] ?? ($value ?: 'Não informado');
            }

            if ($field === 'cpf') {
                return $formatCpf($value);
            }

            if ($field === 'duracao_minutos') {
                $minutes = (int) $value;

                if ($minutes <= 0) {
                    return 'Não informado';
                }

                if ($minutes < 60) {
                    return $minutes . ' min';
                }

                if ($minutes % 60 === 0) {
                    return ($minutes / 60) . 'h';
                }

                return floor($minutes / 60) . 'h ' . ($minutes % 60) . 'min';
            }

            return $value ?: 'Não informado';
        };

        $normalizeScheduleItems = function ($value) use ($formatScheduleEntry) {
            $value = is_array($value) ? $value : [];

            return collect($value)
                ->map(fn ($item) => $formatScheduleEntry($item))
                ->filter(fn ($item) => trim((string) $item) !== '')
                ->values();
        };

        $resolveSubmenu = function ($log) {
            $properties = $log->properties ?? [];

            if (! empty($properties['submenu'])) {
                return $properties['submenu'];
            }

            return match ($log->subject_type) {
                \App\Models\User::class => 'Usuários e Permissões',
                \App\Models\Professional::class => 'Profissionais de Saúde',
                \App\Models\Insurance::class, \App\Models\InsurancePlan::class, \App\Models\ProcedurePrice::class => 'Convênios',
                \App\Models\Procedure::class => 'Procedimentos',
                \App\Models\Unit::class, \App\Models\Room::class => 'Unidades',
                \App\Models\Agendamento::class => 'Agendamentos',
                default => 'Não identificado',
            };
        };

        $resolveTargetUser = function ($log) use ($subjectDisplayNames, $formatCpf) {
            $properties = $log->properties ?? [];
            $targetUser = $properties['target_user'] ?? [];
            $before = is_array($properties['before'] ?? null) ? $properties['before'] : [];
            $after = is_array($properties['after'] ?? null) ? $properties['after'] : [];

            if (! empty($targetUser['nome'])) {
                return trim($targetUser['nome']);
            }

            if (! empty($before['nome'])) {
                return trim((string) $before['nome']);
            }

            if (! empty($after['nome'])) {
                return trim((string) $after['nome']);
            }

            $resolvedName = $subjectDisplayNames->get($log->subject_type . '|' . $log->subject_id);

            if (! empty($resolvedName)) {
                return $resolvedName;
            }

            if (! empty($targetUser['email'])) {
                return trim((string) $targetUser['email']);
            }

            if (! empty($before['email'])) {
                return trim((string) $before['email']);
            }

            if (! empty($after['email'])) {
                return trim((string) $after['email']);
            }

            if (! empty($targetUser['cpf'])) {
                return 'CPF ' . $formatCpf($targetUser['cpf']);
            }

            if (! empty($before['cpf'])) {
                return 'CPF ' . $formatCpf($before['cpf']);
            }

            if (! empty($after['cpf'])) {
                return 'CPF ' . $formatCpf($after['cpf']);
            }

            if ($log->subject_type === \App\Models\User::class) {
                return 'Nome não identificado (ID ' . $log->subject_id . ')';
            }

            if ($log->subject_type === \App\Models\ClinicHour::class) {
                return 'Horário da clínica';
            }

            if ($log->subject_type === \App\Models\ProcedurePrice::class) {
                return 'Tabela de preço';
            }

            if (! empty($properties['nome'])) {
                return trim($properties['nome']);
            }

            if (! empty($properties['registro'])) {
                return trim((string) $properties['registro']);
            }

            return 'Registro #' . $log->subject_id;
        };

        $buildChangeSummary = function ($before, $after) use ($fieldLabels, $formatLogValue, $normalizeScheduleItems) {
            $before = is_array($before) ? $before : [];
            $after = is_array($after) ? $after : [];
            $keys = collect(array_unique(array_merge(array_keys($before), array_keys($after))));

            return $keys->filter(function ($key) use ($before, $after) {
                return ($before[$key] ?? null) !== ($after[$key] ?? null);
            })->map(function ($key) use ($before, $after, $fieldLabels, $formatLogValue, $normalizeScheduleItems) {
                $change = [
                    'key' => $key,
                    'label' => $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                    'before' => $formatLogValue($key, $before[$key] ?? null),
                    'after' => $formatLogValue($key, $after[$key] ?? null),
                ];

                if ($key === 'schedules') {
                    $change['before_items'] = $normalizeScheduleItems($before[$key] ?? []);
                    $change['after_items'] = $normalizeScheduleItems($after[$key] ?? []);
                }

                return $change;
            })->values();
        };
    @endphp

    <div class="section-body">
        <div class="card" id="logs-atividade">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
                <h4 class="mb-0">Logs de atividade</h4>
                <form action="{{ route('admin.settings.activity-logs') }}#logs-atividade" method="GET" class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                    <input type="text" class="form-control cpf-mask logs-filter-control" id="affected-user-cpf-search" name="affected_user_cpf" value="{{ $formatCpf($affectedUserCpfSearch ?? '') }}" placeholder="CPF do usuário afetado" maxlength="14" inputmode="numeric">
                    <input type="date" class="form-control logs-filter-control" id="activity-date-search" name="activity_date" value="{{ $activityDateSearch ?? '' }}">
                    <select class="form-control logs-filter-control logs-filter-select" id="logs-action-type-search" name="action_type">
                        <option value="">Todos os tipos de alteração</option>
                        <option value="created" {{ ($actionTypeSearch ?? '') === 'created' ? 'selected' : '' }}>Cadastro</option>
                        <option value="updated" {{ ($actionTypeSearch ?? '') === 'updated' ? 'selected' : '' }}>Alteração</option>
                        <option value="deleted" {{ ($actionTypeSearch ?? '') === 'deleted' ? 'selected' : '' }}>Exclusão</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                    @if(!empty($affectedUserCpfSearch) || !empty($activityDateSearch) || !empty($actionTypeSearch))
                        <a href="{{ route('admin.settings.activity-logs') }}#logs-atividade" class="btn btn-light border">Limpar</a>
                    @endif
                    <div class="w-100 small text-muted mt-1">
                        Preencha um ou mais campos para combinar os filtros na mesma pesquisa.
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Responsável</th>
                                <th>Local da alteração</th>
                                <th>Usuário afetado</th>
                                <th>Tipo de alteração</th>
                                <th>Descrição</th>
                                <th class="text-center">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activityLogs as $log)
                                @php
                                    $properties = $log->properties ?? [];
                                    $before = $properties['before'] ?? null;
                                    $after = $properties['after'] ?? null;
                                    $changeSummary = $buildChangeSummary($before, $after);
                                @endphp
                                <tr>
                                    <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>{{ trim((($log->user->nome ?? '') . ' ' . ($log->user->sobrenome ?? ''))) ?: 'Sistema' }}</td>
                                    <td>{{ $resolveSubmenu($log) }}</td>
                                    <td>{{ $resolveTargetUser($log) }}</td>
                                    <td>{{ $actionLabels[$log->action] ?? 'Alteração' }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#log-details-{{ $log->id }}" aria-expanded="false" aria-controls="#log-details-{{ $log->id }}">
                                            Visualizar
                                        </button>
                                    </td>
                                </tr>
                                <tr class="collapse bg-light" id="log-details-{{ $log->id }}">
                                    <td colspan="7">
                                        <div class="p-3">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <strong>Registro</strong>
                                                    <div class="text-muted small mt-1">{{ $resolveTargetUser($log) }}</div>
                                                    <div class="text-muted small mt-2"><strong>Tipo de alteração:</strong> {{ $actionLabels[$log->action] ?? 'Alteração' }}</div>
                                                    <div class="text-muted small mt-2"><strong>Local da alteração:</strong> {{ $resolveSubmenu($log) }}</div>
                                                    <div class="text-muted small mt-2"><strong>Usuário afetado:</strong> {{ $resolveTargetUser($log) }}</div>
                                                </div>
                                                <div class="col-md-8">
                                                    <strong>Alterações</strong>

                                                    @if($changeSummary->isNotEmpty())
                                                        <div class="border rounded p-2 bg-white mt-2 mb-3">
                                                            <div class="font-weight-bold mb-2">Resumo da alteração</div>
                                                            @foreach($changeSummary as $change)
                                                                <div class="small mb-2">
                                                                    <div class="text-muted">{{ $change['label'] }}</div>
                                                                    @if(($change['key'] ?? null) === 'schedules')
                                                                        <div class="row mt-2">
                                                                            <div class="col-md-6 mb-2">
                                                                                <div class="log-change-before">
                                                                                    <div class="font-weight-bold text-danger">Antes</div>
                                                                                    @if(!empty($change['before_items']) && $change['before_items']->isNotEmpty())
                                                                                        <div class="log-schedule-list">
                                                                                            @foreach($change['before_items'] as $item)
                                                                                                <span class="log-schedule-item log-schedule-item-before">{{ $item }}</span>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="mt-2 text-muted">Sem agenda definida anteriormente.</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6 mb-2">
                                                                                <div class="log-change-after">
                                                                                    <div class="font-weight-bold text-success">Depois</div>
                                                                                    @if(!empty($change['after_items']) && $change['after_items']->isNotEmpty())
                                                                                        <div class="log-schedule-list">
                                                                                            @foreach($change['after_items'] as $item)
                                                                                                <span class="log-schedule-item log-schedule-item-after">{{ $item }}</span>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="mt-2 text-muted">Agenda removida ou não definida.</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div><span class="text-danger">{{ $change['before'] }}</span> → <span class="text-success">{{ $change['after'] }}</span></div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if($before || $after)
                                                        <div class="row mt-2">
                                                            <div class="col-md-6 mb-3">
                                                                <div class="border rounded p-2 h-100 bg-white">
                                                                    <div class="font-weight-bold mb-2">Antes</div>
                                                                    @if(!empty($before))
                                                                        @foreach($before as $key => $value)
                                                                            <div class="small mb-2">
                                                                                <div class="text-muted text-uppercase">{{ $fieldLabels[$key] ?? str_replace('_', ' ', $key) }}</div>
                                                                                <div>{{ $formatLogValue($key, $value) }}</div>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="small text-muted">Sem valor anterior registrado.</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="border rounded p-2 h-100 bg-white">
                                                                    <div class="font-weight-bold mb-2">Depois</div>
                                                                    @if(!empty($after))
                                                                        @foreach($after as $key => $value)
                                                                            <div class="small mb-2">
                                                                                <div class="text-muted text-uppercase">{{ $fieldLabels[$key] ?? str_replace('_', ' ', $key) }}</div>
                                                                                <div>{{ $formatLogValue($key, $value) }}</div>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="small text-muted">Sem valor novo registrado.</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif(!empty($properties))
                                                        <div class="row mt-2">
                                                            @foreach($properties as $key => $value)
                                                                @continue($key === 'target_user')
                                                                <div class="col-md-4 mb-3">
                                                                    <div class="border rounded p-2 h-100 bg-white">
                                                                        <div class="text-muted text-uppercase small">{{ $fieldLabels[$key] ?? str_replace('_', ' ', $key) }}</div>
                                                                        <div class="mt-1">{{ $formatLogValue($key, $value) }}</div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="small text-muted mt-2">Este log não possui detalhes adicionais.</div>
                                                    @endif

                                                    <div class="d-flex justify-content-end mt-3">
                                                        <button class="btn btn-sm btn-danger" type="button" data-toggle="collapse" data-target="#log-details-{{ $log->id }}" aria-expanded="true" aria-controls="#log-details-{{ $log->id }}">
                                                            Fechar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">Nenhum log registrado ainda.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($activityLogs, 'links'))
                    <div class="d-flex justify-content-end mt-3">
                        {{ $activityLogs->fragment('logs-atividade')->links('vendor.pagination.patients-blocks') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    $(function () {
        function formatCpf(value) {
            var digits = String(value || '').replace(/\D/g, '').slice(0, 11);

            if (!digits.length) {
                return '';
            }

            if (digits.length <= 3) {
                return digits;
            }

            if (digits.length <= 6) {
                return digits.slice(0, 3) + '.' + digits.slice(3);
            }

            if (digits.length <= 9) {
                return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6);
            }

            return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6, 9) + '-' + digits.slice(9);
        }

        function bindCpfFormatter(selector) {
            $(selector).each(function () {
                var input = $(this);

                input.val(formatCpf(input.val()));

                input.on('input', function () {
                    var cursorAtEnd = this.selectionStart === this.value.length;
                    this.value = formatCpf(this.value);

                    if (cursorAtEnd) {
                        this.setSelectionRange(this.value.length, this.value.length);
                    }
                });
            });
        }

        bindCpfFormatter('#affected-user-cpf-search');
    });
</script>
@endpush
@endsection
