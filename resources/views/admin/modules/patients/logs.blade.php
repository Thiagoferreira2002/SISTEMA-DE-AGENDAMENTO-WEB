@extends('admin.layouts.master')

@section('content')
<style>
    .patient-log-detail-card {
        border: 1px solid rgba(23, 111, 190, 0.1);
        border-radius: 14px;
        background: #ffffff;
        padding: 14px 16px;
    }

    .patient-log-detail-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #6b88a3;
        margin-bottom: 6px;
    }

    .patient-log-detail-value {
        color: #18354d;
        font-weight: 600;
        word-break: break-word;
    }

    .patient-log-detail-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .patient-log-details-modal {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(9, 17, 26, 0.52);
    }

    .patient-log-details-modal.is-open {
        display: flex;
    }

    .patient-log-details-dialog {
        width: min(900px, 100%);
        max-height: calc(100vh - 64px);
        overflow: auto;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 24px 54px rgba(15, 23, 42, 0.24);
    }

    .patient-log-change-table {
        min-width: 100%;
        margin-bottom: 0;
        background: transparent;
    }

    .patient-log-change-table thead th {
        white-space: nowrap;
    }

    html[data-theme="dark"] .patient-log-detail-card,
    html[data-theme="dark"] .patient-log-change-table {
        background: rgba(22, 40, 59, 0.96);
        border-color: rgba(143, 197, 255, 0.12);
    }

    html[data-theme="dark"] .patient-log-details-dialog {
        background: linear-gradient(180deg, rgba(22, 40, 59, 0.99) 0%, rgba(19, 33, 49, 0.99) 100%);
        border: 1px solid rgba(143, 197, 255, 0.16);
    }

    html[data-theme="dark"] .patient-log-detail-label {
        color: #a7c1d9;
    }

    html[data-theme="dark"] .patient-log-detail-value,
    html[data-theme="dark"] .patient-log-change-table td,
    html[data-theme="dark"] .patient-log-change-table th {
        color: #eef5fc;
    }

    @media (max-width: 767.98px) {
        .patient-log-toggle {
            width: 100%;
        }

        .patient-log-details-table {
            min-width: 520px;
        }

        .patient-log-detail-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
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

        $renderPatientLogDetails = function ($log) use ($changesFor, $fieldLabels, $formatValue, $resolvePatientName, $resolvePatientCpf, $actionLabels) {
            $changes = $changesFor($log);

            ob_start();
            ?>
            <div class="patient-log-detail-grid mb-3">
                <div class="patient-log-detail-card">
                    <div class="patient-log-detail-label">Paciente</div>
                    <div class="patient-log-detail-value"><?php echo e($resolvePatientName($log)); ?></div>
                </div>
                <div class="patient-log-detail-card">
                    <div class="patient-log-detail-label">CPF</div>
                    <div class="patient-log-detail-value"><?php echo e($resolvePatientCpf($log)); ?></div>
                </div>
                <div class="patient-log-detail-card">
                    <div class="patient-log-detail-label">Tipo de alteração</div>
                    <div class="patient-log-detail-value"><?php echo e($actionLabels[$log->action] ?? ucfirst($log->action)); ?></div>
                </div>
            </div>

            <div class="patient-log-detail-card mb-3">
                <div class="patient-log-detail-label">Descrição</div>
                <div class="patient-log-detail-value"><?php echo e($log->description ?: 'Nenhuma descrição registrada.'); ?></div>
            </div>

            <?php if ($changes->isNotEmpty()): ?>
                <div class="table-responsive patient-log-detail-card">
                    <table class="table table-sm table-bordered patient-log-change-table">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Antes</th>
                                <th>Depois</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($changes as $key): ?>
                                <tr>
                                    <td><?php echo e($fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key))); ?></td>
                                    <td><?php echo e($formatValue($key, data_get($log->properties, 'before.' . $key))); ?></td>
                                    <td><?php echo e($formatValue($key, data_get($log->properties, 'after.' . $key))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (! empty(data_get($log->properties, 'before'))): ?>
                <div class="table-responsive patient-log-detail-card">
                    <table class="table table-sm table-bordered patient-log-change-table">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Valor registrado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (data_get($log->properties, 'before') as $key => $value): ?>
                                <tr>
                                    <td><?php echo e($fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key))); ?></td>
                                    <td><?php echo e($formatValue($key, $value)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (! empty(data_get($log->properties, 'after'))): ?>
                <div class="table-responsive patient-log-detail-card">
                    <table class="table table-sm table-bordered patient-log-change-table">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Valor registrado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (data_get($log->properties, 'after') as $key => $value): ?>
                                <tr>
                                    <td><?php echo e($fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key))); ?></td>
                                    <td><?php echo e($formatValue($key, $value)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="patient-log-detail-card">
                    <div class="patient-log-detail-value text-muted">Nenhum detalhe adicional registrado para esta ação.</div>
                </div>
            <?php endif;

            return trim(ob_get_clean());
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
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label for="patient_cpf">CPF do paciente</label>
                                <input type="text" class="form-control" id="patient_cpf" name="patient_cpf" value="{{ $patientCpfSearch ? $formatCpf($patientCpfSearch) : '' }}" placeholder="000.000.000-00">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label for="activity_date">Data da atividade</label>
                                <input type="date" class="form-control" id="activity_date" name="activity_date" value="{{ $activityDateSearch }}">
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-4">
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
                    </div>
                    <div class="row mt-2">
                        <div class="col-lg-4 col-md-6">
                            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                <button type="submit" class="btn btn-primary px-4">Filtrar</button>
                                <a href="{{ route('admin.patients.logs') }}" class="btn btn-light">Limpar</a>
                            </div>
                        </div>
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
                                        <button class="btn btn-sm btn-outline-primary patient-log-toggle" type="button" data-patient-log-trigger data-patient-log-id="{{ $log->id }}" data-patient-log-title="{{ e('Detalhes do log de ' . $resolvePatientName($log)) }}">
                                            Ver detalhes
                                        </button>
                                        <template id="patient-log-template-{{ $log->id }}">{!! $renderPatientLogDetails($log) !!}</template>
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

        <div class="patient-log-details-modal" id="patient-log-details-modal" aria-hidden="true">
            <div class="patient-log-details-dialog">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
                    <h4 class="mb-0" data-patient-log-modal-title>Detalhes do log do paciente</h4>
                    <button type="button" class="btn btn-link p-0 text-muted" data-patient-log-modal-close aria-label="Fechar" style="font-size: 24px; line-height: 1;">&times;</button>
                </div>
                <div class="card-body" data-patient-log-modal-body>
                    <div class="text-muted">Selecione um log para visualizar os detalhes.</div>
                </div>
                <div class="card-header d-flex justify-content-end" style="gap: 8px;">
                    <button type="button" class="btn btn-secondary" data-patient-log-modal-close>Fechar</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('patient-log-details-modal');
        var modalTitle = modal ? modal.querySelector('[data-patient-log-modal-title]') : null;
        var modalBody = modal ? modal.querySelector('[data-patient-log-modal-body]') : null;
        var closeButtons = modal ? modal.querySelectorAll('[data-patient-log-modal-close]') : [];
        var triggers = document.querySelectorAll('[data-patient-log-trigger]');

        if (!modal || !triggers.length) {
            return;
        }

        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function openModal(title, content) {
            if (modalTitle) {
                modalTitle.textContent = title || 'Detalhes do log do paciente';
            }

            if (modalBody) {
                modalBody.innerHTML = content || '<div class="text-muted">Não foi possível carregar os detalhes.</div>';
            }

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        triggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var template = document.getElementById('patient-log-template-' + (trigger.dataset.patientLogId || ''));
                openModal(trigger.dataset.patientLogTitle, template ? template.innerHTML : '');
            });
        });

        closeButtons.forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                closeModal();
            }
        });
    });
</script>
@endsection
