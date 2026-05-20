@extends('admin.layouts.master')
@section('content')
@php
    $canOperateDelayedAppointments = ! auth()->user()?->isClinicManager();
@endphp
<style>
    .section-body > .card,
    .section-body > .row > .col-12 > .card {
        border: 1px solid #d2dbe6 !important;
        box-shadow: inset 0 0 0 1px #d2dbe6;
    }

    html[data-theme="dark"] .section-body > .card,
    html[data-theme="dark"] .section-body > .row > .col-12 > .card {
        border-color: #000000 !important;
        box-shadow: inset 0 0 0 1px #000000;
    }

    .delayed-stat-col {
        flex: 0 0 auto;
        width: auto;
        max-width: 100%;
    }

    .delayed-summary-card {
        width: fit-content;
        min-width: 190px;
        max-width: 100%;
        margin-right: auto;
        border: 1px solid #d2dbe6 !important;
        box-shadow: inset 0 0 0 1px #d2dbe6;
    }

    html[data-theme="dark"] .delayed-summary-card {
        border-color: #000000 !important;
        box-shadow: inset 0 0 0 1px #000000;
    }

    .delayed-summary-card .card-icon {
        margin: 14px 14px 0;
    }

    .delayed-summary-card .card-wrap {
        padding: 14px 14px 16px;
    }

    .delayed-summary-card .card-header h4 {
        font-size: 11px;
        line-height: 1.25;
        white-space: normal;
        margin-bottom: 0;
    }

    .delayed-status-badge {
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

    .delayed-patient-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 180px;
    }

    .delayed-patient-cell img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(23, 111, 190, 0.12);
        flex: 0 0 auto;
    }

    .delayed-service-cell {
        min-width: 180px;
        white-space: normal;
    }

    .delayed-filter-shortcuts,
    .delayed-filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .delayed-filter-shortcuts .btn,
    .delayed-filter-actions .btn {
        width: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .delayed-list-header {
        gap: 12px;
    }

    .delayed-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        justify-content: flex-start;
        width: auto;
        max-width: 100%;
        white-space: nowrap;
    }

    .delayed-actions form {
        margin: 0;
    }

    .delayed-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: auto;
        min-width: 0;
        max-width: 100%;
        min-height: 28px;
        padding: 4px 6px;
        font-size: 10px;
        line-height: 1.1;
        border-radius: 9px;
        white-space: nowrap;
    }

    .delayed-actions-cell {
        min-width: 235px;
        white-space: nowrap;
    }

    .delayed-details-modal {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(9, 17, 26, 0.52);
    }

    .delayed-details-modal.is-open {
        display: flex;
    }

    .delayed-details-dialog {
        width: min(760px, 100%);
        max-height: calc(100vh - 40px);
        overflow: auto;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 24px 54px rgba(15, 23, 42, 0.24);
    }

    .delayed-details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 18px;
    }

    .delayed-detail-item {
        min-width: 0;
    }

    .delayed-detail-item-full {
        grid-column: 1 / -1;
    }

    .delayed-detail-item label {
        display: block;
        margin-bottom: 4px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #5b7895;
    }

    .delayed-detail-item p {
        margin-bottom: 0;
        color: #16344d;
        word-break: break-word;
    }

    .delayed-details-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .delayed-details-close {
        border: 0;
        background: transparent;
        color: #5b7895;
        font-size: 24px;
        line-height: 1;
        padding: 0;
    }

    .delayed-patient-summary {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .delayed-modal-photo {
        width: 92px;
        height: 92px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(23, 111, 190, 0.12);
        flex: 0 0 auto;
    }

    .delayed-patient-summary-copy {
        min-width: 0;
        text-align: left;
    }

    .delayed-patient-summary-copy p {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #16344d;
    }

    .delayed-patient-summary-copy small {
        display: block;
        margin-top: 6px;
        color: #5b7895;
    }

    .delayed-modal-status-badge {
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

    @media (max-width: 767.98px) {
        .delayed-stat-col,
        .delayed-summary-card {
            width: 100%;
            min-width: 0;
            max-width: 100%;
        }

        .delayed-patient-cell img {
            width: 36px;
            height: 36px;
        }

        .delayed-patient-cell {
            min-width: 140px;
        }

        .delayed-service-cell {
            min-width: 140px;
        }

        .delayed-filter-shortcuts,
        .delayed-filter-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: 100%;
            align-items: stretch;
            gap: 8px;
        }

        .delayed-filter-shortcuts .btn,
        .delayed-filter-actions .btn {
            width: 100%;
        }

        .delayed-actions {
            flex-wrap: wrap;
            justify-content: center;
        }

        .delayed-actions > *,
        .delayed-actions form,
        .delayed-actions .btn {
            width: 100%;
        }

        .delayed-actions-cell {
            min-width: 220px !important;
            white-space: normal !important;
        }

        .delayed-details-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .delayed-patient-summary {
            flex-direction: column;
            align-items: flex-start;
        }

        .delayed-details-modal {
            padding: 12px;
        }
    }

    html[data-theme="dark"] .delayed-details-dialog {
        background: #16283b;
        box-shadow: 0 24px 54px rgba(2, 8, 15, 0.44);
    }

    html[data-theme="dark"] .delayed-detail-item label,
    html[data-theme="dark"] .delayed-details-close,
    html[data-theme="dark"] .delayed-patient-summary-copy small {
        color: #a9c5df;
    }

    html[data-theme="dark"] .delayed-detail-item p,
    html[data-theme="dark"] .delayed-patient-summary-copy p {
        color: #eef5fc;
    }
</style>

<section class="section">
    <div class="section-header">
        <h1>Atendimentos em Atraso</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.agendamentos.index') }}">Agendamentos</a></div>
            <div class="breadcrumb-item">Atendimentos em Atraso</div>
        </div>
    </div>

    <div class="section-body">
        @if(session('success'))
            <div class="alert alert-success mt-3 mb-4">{{ session('success') }}</div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning mt-3 mb-4">{{ session('warning') }}</div>
        @endif

        <div class="row mb-4">
            <div class="col-xl-auto col-lg-auto col-md-5 col-12 delayed-stat-col">
                <div class="card card-statistic-1 mb-0 delayed-summary-card">
                    <div class="card-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Atendimentos em Atraso</h4></div>
                        <div class="card-body">{{ $totalDelayedAppointments ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4>Filtros</h4>
            </div>
            <div class="card-body">
                <div class="mb-3 delayed-filter-shortcuts">
                    <a href="{{ route('admin.agendamentos.delayed-appointments', array_merge(request()->except('page', 'date'), ['period' => 'dia'])) }}" class="btn {{ $period === 'dia' && empty($selectedDate) ? 'btn-primary' : 'btn-outline-primary' }}">Dia</a>
                    <a href="{{ route('admin.agendamentos.delayed-appointments', array_merge(request()->except('page', 'date'), ['period' => 'semana'])) }}" class="btn {{ $period === 'semana' && empty($selectedDate) ? 'btn-primary' : 'btn-outline-primary' }}">Semana</a>
                    <a href="{{ route('admin.agendamentos.delayed-appointments', array_merge(request()->except('page', 'date'), ['period' => 'mes'])) }}" class="btn {{ $period === 'mes' && empty($selectedDate) ? 'btn-primary' : 'btn-outline-primary' }}">Mês</a>
                    <a href="{{ route('admin.agendamentos.delayed-appointments') }}" class="btn btn-light border">Visualizar todos</a>
                </div>

                <form method="GET" action="{{ route('admin.agendamentos.delayed-appointments') }}">
                    <input type="hidden" name="period" value="{{ $period }}">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="delayed-search">Paciente</label>
                                <input type="text" class="form-control" id="delayed-search" name="q" value="{{ $search }}" placeholder="Digite o nome do paciente">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-md-0">
                                <label for="delayed-date">Data</label>
                                <input type="date" class="form-control" id="delayed-date" name="date" value="{{ $selectedDate }}">
                            </div>
                        </div>
                        @if(($professionals ?? collect())->isNotEmpty())
                            <div class="col-md-3">
                                <div class="form-group mb-md-0">
                                    <label for="delayed-professional">Profissional</label>
                                    <select class="form-control" id="delayed-professional" name="professional_id">
                                        <option value="">Todos os profissionais</option>
                                        @foreach($professionals as $professional)
                                            <option value="{{ $professional->id }}" {{ (string) ($selectedProfessionalId ?? '') === (string) $professional->id ? 'selected' : '' }}>{{ $professional->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="delayed-filter-actions">
                                <button type="submit" class="btn btn-primary">Pesquisar</button>
                                <a href="{{ route('admin.agendamentos.delayed-appointments') }}" class="btn btn-light">Limpar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between delayed-list-header">
                <h4>Agendamentos não finalizados</h4>
            </div>
            <div class="card-body">
                @if($delayedAppointments->isEmpty())
                    <div class="alert alert-info" role="alert">
                        Nenhum atendimento atrasado encontrado para os filtros informados.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-mobile-cards">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Serviço</th>
                                    <th>Profissional</th>
                                    <th>Data</th>
                                    <th>Horário</th>
                                    <th>Fim</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($delayedAppointments as $appointment)
                                    @php
                                        $delayedCpf = preg_replace('/\D+/', '', (string) ($appointment->cpf_exibicao ?? $appointment->cpf ?? ''));
                                        $delayedCpf = strlen($delayedCpf) === 11
                                            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $delayedCpf)
                                            : ($appointment->cpf_exibicao ?? $appointment->cpf ?? '-');
                                        $delayedStatusLabel = ucfirst($appointment->status ?? 'pendente');
                                        $delayedStatusColor = $appointment->status === 'confirmado'
                                            ? '#47c363'
                                            : ($appointment->status === 'cancelado' ? '#fc544b' : '#ffa426');
                                        $delayedDescription = $appointment->motivo_consulta ?: ($appointment->descricao ?: '-');
                                    @endphp
                                    <tr>
                                        <td class="delayed-patient-cell" data-label="Paciente">
                                            <img src="{{ $appointment->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto de {{ $appointment->nome }}">
                                            <span>{{ $appointment->nome ?: $appointment->patient?->nome ?: 'Paciente' }}</span>
                                        </td>
                                        <td class="delayed-service-cell" data-label="Serviço">
                                            <span>{{ $appointment->servico ?: '-' }}</span>
                                        </td>
                                        <td data-label="Profissional">
                                            <span>{{ $appointment->profissional_fila ?: 'Não informado' }}</span>
                                        </td>
                                        <td data-label="Data">
                                            <span>{{ optional($appointment->data_agendamento)->format('d/m/Y') ?: '-' }}</span>
                                        </td>
                                        <td data-label="Horário">
                                            <span>{{ substr($appointment->horario ?? '', 0, 5) ?: '-' }}</span>
                                        </td>
                                        <td data-label="Fim">
                                            <span>{{ $appointment->horario_final_exibicao ?: '-' }}</span>
                                        </td>
                                        <td data-label="Status">
                                            <span class="delayed-status-badge" style="background-color: {{ $appointment->status === 'confirmado' ? '#47c363' : '#0f5aa6' }};">
                                                {{ $delayedStatusLabel }}
                                            </span>
                                        </td>
                                        <td class="delayed-actions-cell action-button-cell table-mobile-full" data-label="Ações">
                                            <div class="delayed-actions action-button-group">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    data-delayed-name="{{ e($appointment->nome ?: '-') }}"
                                                    data-delayed-professional="{{ e($appointment->profissional_fila ?: '-') }}"
                                                    data-delayed-email="{{ e($appointment->email ?: '-') }}"
                                                    data-delayed-phone="{{ e($appointment->telefone ?: '-') }}"
                                                    data-delayed-cpf="{{ e($delayedCpf ?: '-') }}"
                                                    data-delayed-photo="{{ e($appointment->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png')) }}"
                                                    data-delayed-service="{{ e($appointment->servico ?: '-') }}"
                                                    data-delayed-date="{{ e($appointment->data_agendamento?->format('d/m/Y') ?: '-') }}"
                                                    data-delayed-start-time="{{ e(substr((string) $appointment->horario, 0, 5) ?: '-') }}"
                                                    data-delayed-end-time="{{ e($appointment->horario_final_exibicao ?: '-') }}"
                                                    data-delayed-status="{{ e($delayedStatusLabel) }}"
                                                    data-delayed-status-color="{{ e($delayedStatusColor) }}"
                                                    data-delayed-created-at="{{ e($appointment->created_at?->format('d/m/Y H:i') ?: '-') }}"
                                                    data-delayed-description="{{ e($delayedDescription) }}"
                                                >Ver</button>
                                            @if($canOperateDelayedAppointments)
                                                <form method="POST" action="{{ route('admin.doctor.queue.finish', $appointment) }}" class="mb-0">
                                                    @csrf
                                                    <input type="hidden" name="q" value="{{ $search }}">
                                                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                                                    <input type="hidden" name="period" value="{{ $period }}">
                                                    <input type="hidden" name="professional_id" value="{{ $selectedProfessionalId ?? '' }}">
                                                    <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deseja finalizar este atendimento?');">Finalizar atendimento</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.agendamentos.cancel-operational', $appointment) }}" class="mb-0">
                                                @csrf
                                                <input type="hidden" name="q" value="{{ $search }}">
                                                <input type="hidden" name="date" value="{{ $selectedDate }}">
                                                <input type="hidden" name="period" value="{{ $period }}">
                                                <input type="hidden" name="professional_id" value="{{ $selectedProfessionalId ?? '' }}">
                                                <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Deseja cancelar este atendimento?');">Cancelar atendimento</button>
                                            </form>
                                            @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">Nenhum atendimento atrasado encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<div class="delayed-details-modal" id="delayedAppointmentDetailsModal" aria-hidden="true">
    <div class="delayed-details-dialog">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
            <h4 class="mb-0">Informações do paciente</h4>
            <button type="button" class="delayed-details-close" data-delayed-modal-close aria-label="Fechar">&times;</button>
        </div>
        <div class="card-body">
            <div class="delayed-details-grid">
                <div class="delayed-detail-item delayed-detail-item-full">
                    <div class="delayed-patient-summary">
                        <img src="{{ asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto do paciente" class="delayed-modal-photo" data-delayed-modal-photo>
                        <div class="delayed-patient-summary-copy">
                            <label>Paciente</label>
                            <p data-delayed-modal-name>-</p>
                            <small>Informações principais do atendimento</small>
                        </div>
                    </div>
                </div>
                <div class="delayed-detail-item">
                    <label>Serviço</label>
                    <p data-delayed-modal-service>-</p>
                </div>
                <div class="delayed-detail-item">
                    <label>Profissional</label>
                    <p data-delayed-modal-professional>-</p>
                </div>
                <div class="delayed-detail-item">
                    <label>Data</label>
                    <p data-delayed-modal-date>-</p>
                </div>
                <div class="delayed-detail-item">
                    <label>Status</label>
                    <p><span class="delayed-modal-status-badge" data-delayed-modal-status style="background-color: #0f5aa6;">-</span></p>
                </div>
                <div class="delayed-detail-item">
                    <label>Email</label>
                    <p data-delayed-modal-email>-</p>
                </div>
                <div class="delayed-detail-item">
                    <label>Telefone</label>
                    <p data-delayed-modal-phone>-</p>
                </div>
                <div class="delayed-detail-item">
                    <label>CPF</label>
                    <p data-delayed-modal-cpf>-</p>
                </div>
                <div class="delayed-detail-item">
                    <label>Horário inicial</label>
                    <p data-delayed-modal-start-time>-</p>
                </div>
                <div class="delayed-detail-item">
                    <label>Horário final</label>
                    <p data-delayed-modal-end-time>-</p>
                </div>
                <div class="delayed-detail-item">
                    <label>Criado em</label>
                    <p data-delayed-modal-created-at>-</p>
                </div>
                <div class="delayed-detail-item delayed-detail-item-full">
                    <label>Descrição</label>
                    <p data-delayed-modal-description>-</p>
                </div>
            </div>
        </div>
        <div class="card-header delayed-details-actions">
            <button type="button" class="btn btn-light" data-delayed-modal-close>Fechar</button>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var detailsButtons = document.querySelectorAll('[data-delayed-name]');
        var detailsModal = document.getElementById('delayedAppointmentDetailsModal');
        var closeButtons = detailsModal ? detailsModal.querySelectorAll('[data-delayed-modal-close]') : [];

        if (!detailsButtons.length || !detailsModal) {
            return;
        }

        var fields = {
            photo: detailsModal.querySelector('[data-delayed-modal-photo]'),
            name: detailsModal.querySelector('[data-delayed-modal-name]'),
            professional: detailsModal.querySelector('[data-delayed-modal-professional]'),
            email: detailsModal.querySelector('[data-delayed-modal-email]'),
            phone: detailsModal.querySelector('[data-delayed-modal-phone]'),
            cpf: detailsModal.querySelector('[data-delayed-modal-cpf]'),
            service: detailsModal.querySelector('[data-delayed-modal-service]'),
            date: detailsModal.querySelector('[data-delayed-modal-date]'),
            startTime: detailsModal.querySelector('[data-delayed-modal-start-time]'),
            endTime: detailsModal.querySelector('[data-delayed-modal-end-time]'),
            createdAt: detailsModal.querySelector('[data-delayed-modal-created-at]'),
            description: detailsModal.querySelector('[data-delayed-modal-description]'),
            status: detailsModal.querySelector('[data-delayed-modal-status]')
        };

        var closeModal = function () {
            detailsModal.classList.remove('is-open');
            detailsModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };

        detailsButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                fields.photo.setAttribute('src', button.dataset.delayedPhoto || '{{ asset('backend/assets/img/avatar/avatar-1.png') }}');
                fields.photo.setAttribute('alt', 'Foto de ' + (button.dataset.delayedName || 'paciente'));
                fields.name.textContent = button.dataset.delayedName || '-';
                fields.professional.textContent = button.dataset.delayedProfessional || '-';
                fields.email.textContent = button.dataset.delayedEmail || '-';
                fields.phone.textContent = button.dataset.delayedPhone || '-';
                fields.cpf.textContent = button.dataset.delayedCpf || '-';
                fields.service.textContent = button.dataset.delayedService || '-';
                fields.date.textContent = button.dataset.delayedDate || '-';
                fields.startTime.textContent = button.dataset.delayedStartTime || '-';
                fields.endTime.textContent = button.dataset.delayedEndTime || '-';
                fields.createdAt.textContent = button.dataset.delayedCreatedAt || '-';
                fields.description.textContent = button.dataset.delayedDescription || '-';
                fields.status.textContent = button.dataset.delayedStatus || '-';
                fields.status.style.backgroundColor = button.dataset.delayedStatusColor || '#0f5aa6';

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
