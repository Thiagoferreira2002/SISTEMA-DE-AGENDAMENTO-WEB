@extends('admin.layouts.master')
@section('content')
<style>
    .queue-summary-card {
        width: fit-content;
        min-width: 190px;
        max-width: 100%;
    }

    .queue-summary-card .card-icon {
        margin: 14px 14px 0;
    }

    .queue-summary-card .card-wrap {
        padding: 14px 14px 16px;
    }

    .queue-summary-card .card-header h4 {
        font-size: 11px;
        line-height: 1.25;
        white-space: normal;
        margin-bottom: 0;
    }

    .queue-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .queue-actions form {
        margin: 0;
    }

    .queue-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .queue-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 110px;
        padding: 7px 12px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
        color: #fff;
    }

    .queue-patient-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 180px;
    }

    .queue-patient-cell img,
    .queue-modal-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(23, 111, 190, 0.12);
        flex: 0 0 auto;
    }

    .queue-modal-photo {
        width: 92px;
        height: 92px;
        margin: 0;
    }

    .queue-service-cell {
        min-width: 180px;
        white-space: normal;
    }

    .queue-end-cell {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .queue-patient-summary {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .queue-patient-summary-copy {
        min-width: 0;
        text-align: left;
    }

    .queue-patient-summary-copy p {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #16344d;
    }

    .queue-patient-summary-copy small {
        display: block;
        margin-top: 6px;
        color: #5b7895;
    }

    .queue-details-modal {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(9, 17, 26, 0.52);
    }

    .queue-details-modal.is-open {
        display: flex;
    }

    .queue-details-dialog {
        width: min(760px, 100%);
        max-height: calc(100vh - 40px);
        overflow: auto;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 24px 54px rgba(15, 23, 42, 0.24);
    }

    .queue-details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 18px;
    }

    .queue-detail-item {
        min-width: 0;
    }

    .queue-detail-item-full {
        grid-column: 1 / -1;
    }

    .queue-detail-item label {
        display: block;
        margin-bottom: 4px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #5b7895;
    }

    .queue-detail-item p {
        margin-bottom: 0;
        color: #16344d;
        word-break: break-word;
    }

    .queue-details-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .queue-details-close {
        border: 0;
        background: transparent;
        color: #5b7895;
        font-size: 24px;
        line-height: 1;
        padding: 0;
    }

    html[data-theme="dark"] .queue-details-dialog {
        background: linear-gradient(180deg, rgba(22, 40, 59, 0.99) 0%, rgba(19, 33, 49, 0.99) 100%);
        border: 1px solid rgba(143, 197, 255, 0.16);
    }

    html[data-theme="dark"] .queue-detail-item label {
        color: #a9c5df;
    }

    html[data-theme="dark"] .queue-detail-item p,
    html[data-theme="dark"] .queue-details-close {
        color: #eef5fc;
    }

    html[data-theme="dark"] .queue-patient-summary-copy p {
        color: #eef5fc;
    }

    html[data-theme="dark"] .queue-patient-summary-copy small {
        color: #a9c5df;
    }

    @media (max-width: 767.98px) {
        .queue-actions {
            flex-wrap: wrap;
            justify-content: center;
        }

        .queue-actions > *,
        .queue-actions form,
        .queue-actions .btn {
            width: 100%;
        }

        .queue-actions-cell {
            min-width: 220px !important;
            white-space: normal !important;
        }

        .queue-details-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .queue-patient-summary {
            flex-direction: column;
            align-items: flex-start;
        }

        .queue-details-modal {
            padding: 12px;
        }
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>{{ $pageTitle ?? 'Fila de Espera' }}</h1>
    </div>

    <div class="section-body">
        @if(session('success') && !str_contains(session('success'), 'O registro já está em Agendamentos Finalizados.'))
            <div class="alert alert-success mt-3 mb-4">{{ session('success') }}</div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning mt-3 mb-4">{{ session('warning') }}</div>
        @endif

        @if(($baseRoute ?? '') === 'admin.doctor.pending-finalization')
            <div class="row mb-4">
                <div class="col-xl-auto col-lg-auto col-md-5 col-12">
                    <div class="card card-statistic-1 mb-0 queue-summary-card">
                        <div class="card-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>Atendimentos em Atraso</h4></div>
                            <div class="card-body">{{ $totalPatientsInQueue }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row mb-4">
                <div class="col-xl-auto col-lg-auto col-md-5 col-12">
                    <div class="card card-statistic-1 mb-0 queue-summary-card">
                        <div class="card-icon bg-primary"><i class="fas fa-user-clock"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>Pacientes na Fila</h4></div>
                            <div class="card-body">{{ $totalPatientsInQueue }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h4>Filtros da fila</h4>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex flex-wrap" style="gap: 8px;">
                    <a href="{{ route($baseRoute ?? 'admin.doctor.queue', array_merge(request()->except('page', 'date'), ['period' => 'dia'])) }}" class="btn {{ $period === 'dia' && empty($selectedDate) ? 'btn-primary' : 'btn-outline-primary' }}">Dia</a>
                    <a href="{{ route($baseRoute ?? 'admin.doctor.queue', array_merge(request()->except('page', 'date'), ['period' => 'semana'])) }}" class="btn {{ $period === 'semana' && empty($selectedDate) ? 'btn-primary' : 'btn-outline-primary' }}">Semana</a>
                    <a href="{{ route($baseRoute ?? 'admin.doctor.queue', array_merge(request()->except('page', 'date'), ['period' => 'mes'])) }}" class="btn {{ $period === 'mes' && empty($selectedDate) ? 'btn-primary' : 'btn-outline-primary' }}">Mês</a>
                    <a href="{{ route($baseRoute ?? 'admin.doctor.queue') }}" class="btn btn-light border">Visualizar todos</a>
                </div>

                <form method="GET" action="{{ route($baseRoute ?? 'admin.doctor.queue') }}">
                    <input type="hidden" name="period" value="{{ $period }}">

                    <div class="row {{ ($baseRoute ?? '') === 'admin.doctor.pending-finalization' ? '' : 'align-items-end' }}">
                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="queue-search">Paciente</label>
                                <input type="text" class="form-control" id="queue-search" name="q" value="{{ $search }}" placeholder="Digite o nome do paciente">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-md-0">
                                <label for="queue-date">Data</label>
                                <input type="date" class="form-control" id="queue-date" name="date" value="{{ $selectedDate }}">
                            </div>
                        </div>
                        @if(($professionalOptions ?? collect())->isNotEmpty())
                            <div class="{{ ($baseRoute ?? '') === 'admin.doctor.pending-finalization' ? 'col-lg-2 col-md-2' : 'col-lg-2 col-md-3' }}">
                                <div class="form-group mb-md-0">
                                    <label for="queue-professional">Profissional</label>
                                    <select class="form-control" id="queue-professional" name="professional_id">
                                        <option value="">Todos os profissionais</option>
                                        @foreach($professionalOptions as $professionalOption)
                                            <option value="{{ $professionalOption->id }}" {{ (string) ($selectedProfessionalId ?? '') === (string) $professionalOption->id ? 'selected' : '' }}>{{ $professionalOption->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(($baseRoute ?? '') === 'admin.doctor.pending-finalization')
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                                    <a href="{{ route($baseRoute ?? 'admin.doctor.queue') }}" class="btn btn-light">Limpar</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row mt-3 align-items-end">
                            <div class="col-md-{{ ($professionalOptions ?? collect())->isNotEmpty() ? '2' : '5' }} d-flex flex-wrap align-items-center" style="gap: 8px;">
                                <button type="submit" class="btn btn-primary">Pesquisar</button>
                                <a href="{{ route($baseRoute ?? 'admin.doctor.queue') }}" class="btn btn-light">Limpar</a>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>{{ $cardTitle ?? 'Pacientes na fila' }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-mobile-cards">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Serviço</th>
                                <th>Profissional</th>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Final</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($queue as $item)
                                @php
                                    $queueCpf = preg_replace('/\D+/', '', (string) ($item->cpf_exibicao ?? $item->cpf ?? ''));
                                    $queueCpf = strlen($queueCpf) === 11
                                        ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $queueCpf)
                                        : ($item->cpf_exibicao ?? $item->cpf ?? '-');
                                    $queueStatusLabel = ucfirst($item->status ?? 'pendente');
                                    $queueStatusColor = $item->status === 'confirmado'
                                        ? '#47c363'
                                        : ($item->status === 'cancelado' ? '#fc544b' : '#ffa426');
                                    $queueDescription = $item->motivo_consulta ?: ($item->descricao ?: '-');
                                @endphp
                                <tr>
                                    <td class="table-mobile-full" data-label="Paciente">
                                        <div class="queue-patient-cell">
                                            <img src="{{ $item->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto de {{ $item->nome }}">
                                            <span>{{ $item->nome }}</span>
                                        </div>
                                    </td>
                                    <td class="queue-service-cell" data-label="Serviço">{{ $item->servico }}</td>
                                    <td data-label="Profissional">{{ $item->profissional_fila }}</td>
                                    <td data-label="Data">{{ $item->data_agendamento->format('d/m/Y') }}</td>
                                    <td data-label="Horário">{{ $item->horario }}</td>
                                    <td data-label="Final">{{ $item->horario_final_exibicao ?: '-' }}</td>
                                    <td data-label="Status">
                                        <span class="badge badge-{{ $item->status === 'confirmado' ? 'success' : ($item->status === 'cancelado' ? 'danger' : 'warning') }}">
                                            {{ $queueStatusLabel }}
                                        </span>
                                    </td>
                                    <td class="queue-actions-cell action-button-cell table-mobile-full" data-label="Ações">
                                        <div class="queue-actions action-button-group">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-secondary"
                                                data-queue-name="{{ e($item->nome ?: '-') }}"
                                                data-queue-professional="{{ e($item->profissional_fila ?: '-') }}"
                                                data-queue-email="{{ e($item->email ?: '-') }}"
                                                data-queue-phone="{{ e($item->telefone ?: '-') }}"
                                                data-queue-cpf="{{ e($queueCpf ?: '-') }}"
                                                data-queue-photo="{{ e($item->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png')) }}"
                                                data-queue-service="{{ e($item->servico ?: '-') }}"
                                                data-queue-date="{{ e($item->data_agendamento?->format('d/m/Y') ?: '-') }}"
                                                data-queue-start-time="{{ e(substr((string) $item->horario, 0, 5) ?: '-') }}"
                                                data-queue-end-time="{{ e($item->horario_final_exibicao ?: '-') }}"
                                                data-queue-status="{{ e($queueStatusLabel) }}"
                                                data-queue-status-color="{{ e($queueStatusColor) }}"
                                                data-queue-created-at="{{ e($item->created_at?->format('d/m/Y H:i') ?: '-') }}"
                                                data-queue-description="{{ e($queueDescription) }}"
                                            >Ver</button>
                                            <form method="POST" action="{{ route('admin.doctor.queue.finish', $item) }}" class="mb-0">
                                                @csrf
                                                <input type="hidden" name="q" value="{{ $search }}">
                                                <input type="hidden" name="date" value="{{ $selectedDate }}">
                                                <input type="hidden" name="period" value="{{ $period }}">
                                                <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deseja finalizar este atendimento?');">Finalizar atendimento</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ $emptyMessage ?? 'Nenhum paciente encontrado na fila para os filtros informados.' }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="queue-details-modal" id="queueAppointmentDetailsModal" aria-hidden="true">
            <div class="queue-details-dialog">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
                    <h4 class="mb-0">Informações do paciente</h4>
                    <button type="button" class="queue-details-close" data-queue-modal-close aria-label="Fechar">&times;</button>
                </div>
                <div class="card-body">
                    <div class="queue-details-grid">
                        <div class="queue-detail-item queue-detail-item-full">
                            <div class="queue-patient-summary">
                                <img src="{{ asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto do paciente" class="queue-modal-photo" data-queue-modal-photo>
                                <div class="queue-patient-summary-copy">
                                    <label>Paciente</label>
                                    <p data-queue-modal-name>-</p>
                                    <small>Informações principais do atendimento</small>
                                </div>
                            </div>
                        </div>
                        <div class="queue-detail-item">
                            <label>Serviço</label>
                            <p data-queue-modal-service>-</p>
                        </div>
                        <div class="queue-detail-item">
                            <label>Profissional</label>
                            <p data-queue-modal-professional>-</p>
                        </div>
                        <div class="queue-detail-item">
                            <label>Data</label>
                            <p data-queue-modal-date>-</p>
                        </div>
                        <div class="queue-detail-item">
                            <label>Status</label>
                            <p><span class="queue-status-badge" data-queue-modal-status style="background-color: #0f5aa6;">-</span></p>
                        </div>
                        <div class="queue-detail-item">
                            <label>Email</label>
                            <p data-queue-modal-email>-</p>
                        </div>
                        <div class="queue-detail-item">
                            <label>Telefone</label>
                            <p data-queue-modal-phone>-</p>
                        </div>
                        <div class="queue-detail-item">
                            <label>CPF</label>
                            <p data-queue-modal-cpf>-</p>
                        </div>
                        <div class="queue-detail-item">
                            <label>Horário inicial</label>
                            <p data-queue-modal-start-time>-</p>
                        </div>
                        <div class="queue-detail-item">
                            <label>Horário final</label>
                            <p data-queue-modal-end-time>-</p>
                        </div>
                        <div class="queue-detail-item">
                            <label>Criado em</label>
                            <p data-queue-modal-created-at>-</p>
                        </div>
                        <div class="queue-detail-item queue-detail-item-full">
                            <label>Descrição</label>
                            <p data-queue-modal-description>-</p>
                        </div>
                    </div>
                </div>
                <div class="card-header queue-details-actions">
                    <button type="button" class="btn btn-light" data-queue-modal-close>Fechar</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var detailsButtons = document.querySelectorAll('[data-queue-name]');
        var detailsModal = document.getElementById('queueAppointmentDetailsModal');
        var closeButtons = detailsModal ? detailsModal.querySelectorAll('[data-queue-modal-close]') : [];

        if (!detailsButtons.length || !detailsModal) {
            return;
        }

        var fields = {
            photo: detailsModal.querySelector('[data-queue-modal-photo]'),
            name: detailsModal.querySelector('[data-queue-modal-name]'),
            professional: detailsModal.querySelector('[data-queue-modal-professional]'),
            email: detailsModal.querySelector('[data-queue-modal-email]'),
            phone: detailsModal.querySelector('[data-queue-modal-phone]'),
            cpf: detailsModal.querySelector('[data-queue-modal-cpf]'),
            service: detailsModal.querySelector('[data-queue-modal-service]'),
            date: detailsModal.querySelector('[data-queue-modal-date]'),
            startTime: detailsModal.querySelector('[data-queue-modal-start-time]'),
            endTime: detailsModal.querySelector('[data-queue-modal-end-time]'),
            createdAt: detailsModal.querySelector('[data-queue-modal-created-at]'),
            description: detailsModal.querySelector('[data-queue-modal-description]'),
            status: detailsModal.querySelector('[data-queue-modal-status]')
        };

        var closeModal = function () {
            detailsModal.classList.remove('is-open');
            detailsModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };

        detailsButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                fields.photo.setAttribute('src', button.dataset.queuePhoto || '{{ asset('backend/assets/img/avatar/avatar-1.png') }}');
                fields.photo.setAttribute('alt', 'Foto de ' + (button.dataset.queueName || 'paciente'));
                fields.name.textContent = button.dataset.queueName || '-';
                fields.professional.textContent = button.dataset.queueProfessional || '-';
                fields.email.textContent = button.dataset.queueEmail || '-';
                fields.phone.textContent = button.dataset.queuePhone || '-';
                fields.cpf.textContent = button.dataset.queueCpf || '-';
                fields.service.textContent = button.dataset.queueService || '-';
                fields.date.textContent = button.dataset.queueDate || '-';
                fields.startTime.textContent = button.dataset.queueStartTime || '-';
                fields.endTime.textContent = button.dataset.queueEndTime || '-';
                fields.createdAt.textContent = button.dataset.queueCreatedAt || '-';
                fields.description.textContent = button.dataset.queueDescription || '-';
                fields.status.textContent = button.dataset.queueStatus || '-';
                fields.status.style.backgroundColor = button.dataset.queueStatusColor || '#0f5aa6';

                detailsModal.classList.add('is-open');
                detailsModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            });
        });

        closeButtons.forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        detailsModal.addEventListener('click', function (event) {
            if (event.target === detailsModal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && detailsModal.classList.contains('is-open')) {
                closeModal();
            }
        });
    });
</script>
@endsection
