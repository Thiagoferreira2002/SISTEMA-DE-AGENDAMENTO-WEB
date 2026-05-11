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
        min-width: 198px;
        max-width: 198px;
    }

    .logs-filter-select {
        min-width: 244px;
        max-width: 244px;
    }

    .logs-filter-date {
        min-width: 142px;
        max-width: 142px;
    }

    .logs-filter-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        width: auto;
    }

    .logs-filter-item {
        flex: 0 0 auto;
    }

    .logs-filter-note {
        width: 100%;
    }

    .activity-log-modal-meta {
        display: grid;
        gap: 12px;
    }

    .activity-log-modal-card {
        border: 1px solid rgba(23, 111, 190, 0.1);
        border-radius: 14px;
        background: #ffffff;
        padding: 14px 16px;
    }

    .activity-log-modal-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #6b88a3;
        margin-bottom: 6px;
    }

    .activity-log-modal-value {
        color: #18354d;
        font-weight: 600;
        word-break: break-word;
    }

    .activity-log-modal-summary-grid {
        display: grid;
        gap: 12px;
    }

    .activity-log-modal-inline-diff {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .activity-log-modal-inline-diff .text-danger,
    .activity-log-modal-inline-diff .text-success {
        font-weight: 700;
        word-break: break-word;
    }

    .activity-log-modal-columns {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .activity-log-modal-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .activity-log-details-modal {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(9, 17, 26, 0.52);
    }

    .activity-log-details-modal.is-open {
        display: flex;
    }

    .activity-log-details-dialog {
        width: min(880px, 100%);
        max-height: calc(100vh - 64px);
        overflow: auto;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 24px 54px rgba(15, 23, 42, 0.24);
    }

    html[data-theme="dark"] .activity-log-modal-card {
        background: rgba(22, 40, 59, 0.96);
        border-color: rgba(143, 197, 255, 0.12);
    }

    html[data-theme="dark"] .activity-log-details-dialog {
        background: linear-gradient(180deg, rgba(22, 40, 59, 0.99) 0%, rgba(19, 33, 49, 0.99) 100%);
        border: 1px solid rgba(143, 197, 255, 0.16);
    }

    html[data-theme="dark"] .activity-log-modal-label {
        color: #a7c1d9;
    }

    html[data-theme="dark"] .activity-log-modal-value {
        color: #eef5fc;
    }

    @media (max-width: 767.98px) {
        .logs-filter-form {
            width: 100%;
        }

        .logs-filter-item {
            width: 100%;
        }

        .logs-filter-item .form-control,
        .logs-filter-item .btn {
            width: 100%;
            max-width: 100%;
        }

        .activity-log-modal-columns,
        .activity-log-modal-grid {
            grid-template-columns: minmax(0, 1fr);
        }
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
            'minha_conta' => 'Minha Conta',
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
                \App\Models\Procedure::class => 'Procedimentos',
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

        $activityLogModalItems = $activityLogs->mapWithKeys(function ($log) use ($buildChangeSummary, $fieldLabels, $formatLogValue, $resolveSubmenu, $resolveTargetUser, $actionLabels) {
            $properties = $log->properties ?? [];
            $before = is_array($properties['before'] ?? null) ? $properties['before'] : [];
            $after = is_array($properties['after'] ?? null) ? $properties['after'] : [];
            $changeSummary = $buildChangeSummary($before, $after)->map(function ($change) {
                return [
                    'key' => $change['key'] ?? null,
                    'label' => $change['label'] ?? 'Alteração',
                    'before' => $change['before'] ?? 'Não informado',
                    'after' => $change['after'] ?? 'Não informado',
                    'before_items' => isset($change['before_items']) ? $change['before_items']->values()->all() : [],
                    'after_items' => isset($change['after_items']) ? $change['after_items']->values()->all() : [],
                ];
            })->values()->all();

            $beforeItems = collect($before)->map(function ($value, $key) use ($fieldLabels, $formatLogValue) {
                return [
                    'label' => $fieldLabels[$key] ?? str_replace('_', ' ', $key),
                    'value' => $formatLogValue($key, $value),
                ];
            })->values()->all();

            $afterItems = collect($after)->map(function ($value, $key) use ($fieldLabels, $formatLogValue) {
                return [
                    'label' => $fieldLabels[$key] ?? str_replace('_', ' ', $key),
                    'value' => $formatLogValue($key, $value),
                ];
            })->values()->all();

            $propertyItems = collect($properties)
                ->reject(fn ($value, $key) => $key === 'target_user' || $key === 'before' || $key === 'after')
                ->map(function ($value, $key) use ($fieldLabels, $formatLogValue) {
                    return [
                        'label' => $fieldLabels[$key] ?? str_replace('_', ' ', $key),
                        'value' => $formatLogValue($key, $value),
                    ];
                })->values()->all();

            return [
                (string) $log->id => [
                    'title' => 'Detalhes do log de atividade',
                    'record' => $resolveTargetUser($log),
                    'action' => $actionLabels[$log->action] ?? 'Alteração',
                    'submenu' => $resolveSubmenu($log),
                    'target' => $resolveTargetUser($log),
                    'description' => $log->description ?: 'Sem descrição.',
                    'created_at' => $log->created_at?->format('d/m/Y H:i') ?: 'Não informado',
                    'change_summary' => $changeSummary,
                    'before_items' => $beforeItems,
                    'after_items' => $afterItems,
                    'property_items' => $propertyItems,
                ],
            ];
        })->all();
    @endphp

    <div class="section-body">
        <div class="card" id="logs-atividade">
            <div class="card-header">
                <form action="{{ route('admin.settings.activity-logs') }}#logs-atividade" method="GET" class="logs-filter-form">
                    <div class="logs-filter-item">
                        <input type="text" class="form-control logs-filter-control" id="responsible-search" name="responsible" value="{{ $responsibleSearch ?? '' }}" placeholder="Responsável pela ação">
                    </div>
                    <div class="logs-filter-item">
                        <input type="text" class="form-control cpf-mask logs-filter-control" id="affected-user-cpf-search" name="affected_user_cpf" value="{{ $formatCpf($affectedUserCpfSearch ?? '') }}" placeholder="CPF do usuário afetado" maxlength="14" inputmode="numeric">
                    </div>
                    <div class="logs-filter-item">
                        <input type="date" class="form-control logs-filter-control logs-filter-date" id="activity-date-search" name="activity_date" value="{{ $activityDateSearch ?? '' }}">
                    </div>
                    <div class="logs-filter-item">
                        <select class="form-control logs-filter-control logs-filter-select" id="logs-action-type-search" name="action_type">
                            <option value="">Todos os tipos de alteração</option>
                            <option value="created" {{ ($actionTypeSearch ?? '') === 'created' ? 'selected' : '' }}>Cadastro</option>
                            <option value="updated" {{ ($actionTypeSearch ?? '') === 'updated' ? 'selected' : '' }}>Alteração</option>
                            <option value="deleted" {{ ($actionTypeSearch ?? '') === 'deleted' ? 'selected' : '' }}>Exclusão</option>
                        </select>
                    </div>
                    <div class="logs-filter-item">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                    @if(!empty($responsibleSearch) || !empty($affectedUserCpfSearch) || !empty($activityDateSearch) || !empty($actionTypeSearch))
                        <div class="logs-filter-item">
                            <a href="{{ route('admin.settings.activity-logs') }}#logs-atividade" class="btn btn-light border">Limpar</a>
                        </div>
                    @endif
                    <div class="logs-filter-note small text-muted mt-1">
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
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-log-details-trigger data-log-id="{{ $log->id }}">
                                            Visualizar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">Nenhum log registrado ainda.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="activity-log-details-modal" id="activity-log-details-modal" aria-hidden="true">
                    <div class="activity-log-details-dialog">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
                            <h4 class="mb-0" data-activity-log-modal-title>Detalhes do log de atividade</h4>
                            <button type="button" class="btn btn-link p-0 text-muted" data-activity-log-modal-close aria-label="Fechar" style="font-size: 24px; line-height: 1;">&times;</button>
                        </div>
                        <div class="card-body" data-activity-log-modal-body>
                            <div class="text-muted">Selecione um log para visualizar os detalhes.</div>
                        </div>
                        <div class="card-header d-flex justify-content-end" style="gap: 8px;">
                            <button class="btn btn-secondary" type="button" data-activity-log-modal-close>Fechar</button>
                        </div>
                    </div>
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
        var activityLogItems = {{ Illuminate\Support\Js::from($activityLogModalItems) }};
        var activityLogModal = document.getElementById('activity-log-details-modal');
        var activityLogModalTitle = activityLogModal ? activityLogModal.querySelector('[data-activity-log-modal-title]') : null;
        var activityLogModalBody = activityLogModal ? activityLogModal.querySelector('[data-activity-log-modal-body]') : null;
        var activityLogCloseButtons = activityLogModal ? activityLogModal.querySelectorAll('[data-activity-log-modal-close]') : [];

        function escapeHtml(value) {
            return $('<div>').text(value == null ? '' : String(value)).html();
        }

        function renderKeyValueCard(item) {
            return '<div class="activity-log-modal-card">'
                + '<div class="activity-log-modal-label">' + escapeHtml(item.label || '') + '</div>'
                + '<div class="activity-log-modal-value">' + escapeHtml(item.value || 'Não informado') + '</div>'
                + '</div>';
        }

        function renderScheduleDiff(items, emptyMessage, itemClass) {
            if (!items || !items.length) {
                return '<div class="mt-2 text-muted">' + escapeHtml(emptyMessage) + '</div>';
            }

            return '<div class="log-schedule-list">' + items.map(function (item) {
                return '<span class="log-schedule-item ' + itemClass + '">' + escapeHtml(item) + '</span>';
            }).join('') + '</div>';
        }

        function renderChangeSummary(items) {
            if (!items || !items.length) {
                return '';
            }

            return '<div class="activity-log-modal-card mb-3">'
                + '<div class="font-weight-bold mb-3">Resumo da alteração</div>'
                + '<div class="activity-log-modal-summary-grid">'
                + items.map(function (item) {
                    var content = '';

                    if (item.key === 'schedules') {
                        content = '<div class="row mt-2">'
                            + '<div class="col-md-6 mb-2">'
                            + '<div class="log-change-before">'
                            + '<div class="font-weight-bold text-danger">Antes</div>'
                            + renderScheduleDiff(item.before_items || [], 'Sem agenda definida anteriormente.', 'log-schedule-item-before')
                            + '</div>'
                            + '</div>'
                            + '<div class="col-md-6 mb-2">'
                            + '<div class="log-change-after">'
                            + '<div class="font-weight-bold text-success">Depois</div>'
                            + renderScheduleDiff(item.after_items || [], 'Agenda removida ou não definida.', 'log-schedule-item-after')
                            + '</div>'
                            + '</div>'
                            + '</div>';
                    } else {
                        content = '<div class="activity-log-modal-inline-diff">'
                            + '<span class="text-danger">' + escapeHtml(item.before || 'Não informado') + '</span>'
                            + '<span>&rarr;</span>'
                            + '<span class="text-success">' + escapeHtml(item.after || 'Não informado') + '</span>'
                            + '</div>';
                    }

                    return '<div>'
                        + '<div class="activity-log-modal-label">' + escapeHtml(item.label || 'Alteração') + '</div>'
                        + content
                        + '</div>';
                }).join('')
                + '</div>'
                + '</div>';
        }

        function renderColumns(title, items, emptyMessage) {
            return '<div class="activity-log-modal-card">'
                + '<div class="font-weight-bold mb-3">' + escapeHtml(title) + '</div>'
                + (items && items.length
                    ? items.map(renderKeyValueCard).join('')
                    : '<div class="text-muted">' + escapeHtml(emptyMessage) + '</div>')
                + '</div>';
        }

        function renderActivityLogModal(logId) {
            var item = activityLogItems[logId];

            if (!item) {
                if (activityLogModalTitle) {
                    activityLogModalTitle.textContent = 'Detalhes do log de atividade';
                }

                if (activityLogModalBody) {
                    activityLogModalBody.innerHTML = '<div class="text-muted">Não foi possível carregar os detalhes deste log.</div>';
                }

                return false;
            }

            if (activityLogModalTitle) {
                activityLogModalTitle.textContent = item.title || 'Detalhes do log de atividade';
            }

            var metaHtml = '<div class="activity-log-modal-meta mb-3">'
                + '<div class="activity-log-modal-grid">'
                + renderKeyValueCard({ label: 'Registro', value: item.record || 'Não informado' })
                + renderKeyValueCard({ label: 'Tipo de alteração', value: item.action || 'Alteração' })
                + renderKeyValueCard({ label: 'Local da alteração', value: item.submenu || 'Não identificado' })
                + renderKeyValueCard({ label: 'Usuário afetado', value: item.target || 'Não informado' })
                + renderKeyValueCard({ label: 'Descrição', value: item.description || 'Sem descrição.' })
                + renderKeyValueCard({ label: 'Data', value: item.created_at || 'Não informado' })
                + '</div>'
                + '</div>';

            var summaryHtml = renderChangeSummary(item.change_summary || []);
            var columnsHtml = '<div class="activity-log-modal-columns">'
                + renderColumns('Antes', item.before_items || [], 'Sem valor anterior registrado.')
                + renderColumns('Depois', item.after_items || [], 'Sem valor novo registrado.')
                + '</div>';

            var propertiesHtml = '';

            if ((!item.before_items || !item.before_items.length) && (!item.after_items || !item.after_items.length) && item.property_items && item.property_items.length) {
                propertiesHtml = '<div class="activity-log-modal-card mt-3">'
                    + '<div class="font-weight-bold mb-3">Detalhes adicionais</div>'
                    + '<div class="activity-log-modal-grid">' + item.property_items.map(renderKeyValueCard).join('') + '</div>'
                    + '</div>';
            }

            if ((!item.before_items || !item.before_items.length) && (!item.after_items || !item.after_items.length) && (!item.property_items || !item.property_items.length)) {
                propertiesHtml = '<div class="activity-log-modal-card mt-3"><div class="text-muted">Este log não possui detalhes adicionais.</div></div>';
            }

            if (activityLogModalBody) {
                activityLogModalBody.innerHTML = metaHtml + summaryHtml + columnsHtml + propertiesHtml;
            }

            return true;
        }

        function openActivityLogModal() {
            if (!activityLogModal) {
                return;
            }

            activityLogModal.classList.add('is-open');
            activityLogModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeActivityLogModal() {
            if (!activityLogModal) {
                return;
            }

            activityLogModal.classList.remove('is-open');
            activityLogModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        if (activityLogCloseButtons.length) {
            activityLogCloseButtons.forEach(function (button) {
                button.addEventListener('click', closeActivityLogModal);
            });
        }

        if (activityLogModal) {
            activityLogModal.addEventListener('click', function (event) {
                if (event.target === activityLogModal) {
                    closeActivityLogModal();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && activityLogModal && activityLogModal.classList.contains('is-open')) {
                closeActivityLogModal();
            }
        });

        $(document).on('click', '[data-log-details-trigger]', function () {
            var logId = String($(this).data('logId') || '');

            if (!activityLogModal) {
                return;
            }

            if (renderActivityLogModal(logId)) {
                openActivityLogModal();
            }
        });

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
