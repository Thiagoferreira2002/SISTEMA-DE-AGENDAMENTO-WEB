@extends('admin.layouts.master')
@section('content')
<style>
    .section-body > .row > .col-12 > .card,
    .section-body > .col-12 > .card {
        border: 1px solid #d2dbe6 !important;
        box-shadow: inset 0 0 0 1px #d2dbe6;
    }

    html[data-theme="dark"] .section-body > .row > .col-12 > .card,
    html[data-theme="dark"] .section-body > .col-12 > .card {
        border-color: #000000 !important;
        box-shadow: inset 0 0 0 1px #000000;
    }

    .card-statistic-1.history-summary-card {
        border: 1px solid #d2dbe6 !important;
        box-shadow: inset 0 0 0 1px #d2dbe6;
    }

    .history-summary-card {
        width: fit-content;
        min-width: 190px;
        max-width: 100%;
        border: 1px solid #d2dbe6 !important;
        box-shadow: inset 0 0 0 1px #d2dbe6;
    }

    html[data-theme="dark"] .history-summary-card {
        border-color: #000000 !important;
        box-shadow: inset 0 0 0 1px #000000;
    }

    html[data-theme="dark"] .card-statistic-1.history-summary-card {
        border-color: #000000 !important;
        box-shadow: inset 0 0 0 1px #000000;
    }

    .history-summary-card .card-icon {
        margin: 14px 14px 0;
    }

    .history-summary-card .card-wrap {
        padding: 14px 14px 16px;
    }

    .history-summary-card .card-header h4 {
        font-size: 11px;
        line-height: 1.25;
        white-space: normal;
        margin-bottom: 0;
    }

    .patient-history-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .patient-history-actions form {
        margin: 0;
    }

    .patient-history-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .history-filters-row {
        row-gap: 14px;
    }

    .history-filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-height: 42px;
    }

    .history-filter-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: auto;
        white-space: nowrap;
    }

    .history-enhanced-table {
        min-width: 900px;
    }

    .history-patient-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 170px;
    }

    .history-patient-cell img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(23, 111, 190, 0.12);
        flex: 0 0 auto;
    }

    .history-patient-cell span {
        min-width: 0;
        word-break: break-word;
    }

    .history-service-cell {
        min-width: 170px;
        white-space: normal;
    }

    .history-status-badge {
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

    .history-details-modal {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(9, 17, 26, 0.52);
    }

    .history-details-modal.is-open {
        display: flex;
    }

    .history-details-dialog {
        width: min(860px, 100%);
        max-height: calc(100vh - 40px);
        overflow: auto;
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid #d2dbe6;
        box-shadow: 0 24px 54px rgba(15, 23, 42, 0.24);
    }

    .history-details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 18px;
    }

    .history-detail-item {
        min-width: 0;
    }

    .history-detail-item-full {
        grid-column: 1 / -1;
    }

    .history-detail-item label {
        display: block;
        margin-bottom: 4px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #5b7895;
    }

    .history-detail-item p {
        margin-bottom: 0;
        color: #16344d;
        word-break: break-word;
    }

    .history-modal-photo {
        width: 92px;
        height: 92px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(23, 111, 190, 0.12);
        flex: 0 0 auto;
        margin: 0;
    }

    .history-patient-summary {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .history-patient-summary-copy {
        min-width: 0;
        text-align: left;
    }

    .history-patient-summary-copy p {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #16344d;
    }

    .history-patient-summary-copy small {
        display: block;
        margin-top: 6px;
        color: #5b7895;
    }

    html[data-theme="dark"] .history-details-dialog {
        background: linear-gradient(180deg, rgba(22, 40, 59, 0.99) 0%, rgba(19, 33, 49, 0.99) 100%);
        border: 1px solid #000000;
    }

    html[data-theme="dark"] .history-detail-item label,
    html[data-theme="dark"] .history-patient-summary-copy small {
        color: #a9c5df;
    }

    html[data-theme="dark"] .history-detail-item p,
    html[data-theme="dark"] .history-patient-summary-copy p {
        color: #eef5fc;
    }

    @media (max-width: 767.98px) {
        .history-filters-row {
            display: block;
        }

        .history-filter-actions {
            align-items: flex-start;
            margin-top: 2px;
            width: 100%;
        }

        .history-filter-actions .btn {
            width: auto !important;
            min-width: 0;
            max-width: 100%;
            padding-left: 14px !important;
            padding-right: 14px !important;
        }

        .history-table-responsive {
            overflow-x: visible !important;
            padding: 0 10px 18px;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards {
            display: block;
            width: 100% !important;
            min-width: 0 !important;
            table-layout: auto;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody {
            display: grid;
            width: 100%;
            gap: 18px;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody tr {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 22px;
            width: 100%;
            min-height: 330px;
            padding: 22px !important;
            border-radius: 18px;
            overflow: visible;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: 8px;
            min-height: 0;
            padding: 0 !important;
            border: 0 !important;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td::before {
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td + td {
            border-top: 0 !important;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(1),
        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(2) {
            grid-column: 1 / -1;
            padding-bottom: 18px !important;
            border-bottom: 1px solid var(--border-soft) !important;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(1) {
            order: 2;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(2) {
            order: 1;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(3),
        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(4) {
            order: 3;
            min-height: 62px;
            padding-bottom: 18px !important;
            border-bottom: 1px solid var(--border-soft) !important;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(5),
        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(6) {
            grid-column: 1 / -1;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(5) {
            order: 4;
        }

        .history-table-responsive .history-enhanced-table.table-mobile-cards tbody td:nth-child(6) {
            order: 5;
        }

        .history-patient-cell {
            justify-content: flex-start;
            min-width: 0;
            gap: 12px;
        }

        .patient-history-actions {
            display: flex !important;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 10px;
            width: 100%;
            max-width: none;
            margin: 0;
            padding-top: 4px;
        }

        .patient-history-actions > *,
        .patient-history-actions form,
        .patient-history-actions .btn {
            width: auto !important;
            min-width: 0;
            max-width: 100%;
            box-sizing: border-box;
            flex: 0 0 auto;
        }

        .patient-history-actions .btn {
            min-height: 38px;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .history-details-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .history-patient-summary {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>{{ $moduleTitle ?? 'Agendamentos Finalizados' }}</h1>
    </div>

    <div class="section-body">
        <div class="row mb-4">
            <div class="col-xl-auto col-lg-auto col-md-5 col-12">
                <div class="card card-statistic-1 mb-0 history-summary-card">
                    <div class="card-icon bg-primary"><i class="fas fa-check-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>{{ $moduleCounterLabel ?? 'Agendamentos Finalizados' }}</h4></div>
                        <div class="card-body">{{ $totalFinishedAppointments }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4>Filtros</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ $moduleRoute ?? route('admin.agendamentos.completed') }}">
                    <div class="row align-items-end history-filters-row">
                        <div class="col-xl-2 col-lg-2 col-md-3">
                            <div class="form-group mb-md-0">
                                <label for="period">Período</label>
                                <select class="form-control" id="period" name="period">
                                    <option value="">Todos</option>
                                    <option value="dia" {{ $period === 'dia' ? 'selected' : '' }}>Dia</option>
                                    <option value="semana" {{ $period === 'semana' ? 'selected' : '' }}>Semana</option>
                                    <option value="mes" {{ $period === 'mes' ? 'selected' : '' }}>Mês</option>
                                    <option value="ano" {{ $period === 'ano' ? 'selected' : '' }}>Ano</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="service">Serviço</label>
                                <select class="form-control" id="service" name="service">
                                    <option value="">Todos os serviços</option>
                                    @foreach(($serviceOptions ?? collect()) as $serviceOption)
                                        <option value="{{ $serviceOption }}" {{ (string) ($serviceFilter ?? '') === (string) $serviceOption ? 'selected' : '' }}>{{ $serviceOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3">
                            <div class="form-group mb-md-0">
                                <label for="start_date">Data inicial</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                            </div>
                        </div>
                        @if(empty($authenticatedProfessional))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group mb-md-0">
                                    <label for="history-search">Paciente por CPF ou nome</label>
                                    <input type="text" class="form-control" id="history-search" name="q" value="{{ $search ?? '' }}" placeholder="Digite o CPF ou o nome do paciente">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3">
                                <div class="form-group mb-md-0">
                                    <label for="professional_id">Profissional</label>
                                    <select class="form-control" id="professional_id" name="professional_id">
                                        <option value="">Todos os profissionais</option>
                                        @foreach(($professionalOptions ?? collect()) as $professional)
                                            <option value="{{ $professional->id }}" {{ (string) ($professionalFilter ?? '') === (string) $professional->id ? 'selected' : '' }}>{{ $professional->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group mb-0 history-filter-actions">
                                <button type="submit" class="btn btn-primary px-4">Filtrar</button>
                                <a href="{{ $moduleRoute ?? route('admin.agendamentos.completed') }}" class="btn btn-light px-4">Limpar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>{{ $moduleCardTitle ?? 'Lista de agendamentos finalizados' }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive history-table-responsive">
                    <table class="table table-striped table-mobile-cards history-enhanced-table">
                        <thead>
                            <tr>
                                <th>Linha do Tempo</th>
                                <th>Paciente</th>
                                <th>Profissional</th>
                                <th>Serviço</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                                @php
                                    $historyStatusLabel = ucfirst((string) ($item->status ?? 'finalizado'));
                                    $historyStatusColor = $item->status === 'cancelado'
                                        ? '#fc544b'
                                        : ($item->status === 'confirmado' ? '#47c363' : '#0f5aa6');
                                    $historyCpf = preg_replace('/\D+/', '', (string) ($item->cpf_exibicao ?? $item->cpf ?? ''));
                                    $historyCpf = strlen($historyCpf) === 11
                                        ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $historyCpf)
                                        : ($item->cpf_exibicao ?? $item->cpf ?? '-');
                                    $historyEndTime = $item->horario_final_exibicao
                                        ?? optional($item->data_agendamento)->copy()?->setTimeFromTimeString(substr((string) $item->horario, 0, 5))?->addMinutes((int) ($item->duracao_exibicao ?? $item->duracao_minutos ?? 30))?->format('H:i')
                                        ?? '-';
                                    $historyDescription = $item->motivo_consulta ?: ($item->descricao ?: '-');
                                @endphp
                                <tr>
                                    <td data-label="Linha do Tempo">{{ $item->data_agendamento->format('d/m/Y') }} às {{ $item->horario }}</td>
                                    <td class="table-mobile-full" data-label="Paciente">
                                        <div class="history-patient-cell">
                                            <img src="{{ $item->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto de {{ $item->nome }}">
                                            <span>{{ $item->nome }}</span>
                                        </div>
                                    </td>
                                    <td data-label="Profissional">{{ $item->medico_historico }}</td>
                                    <td class="history-service-cell" data-label="Serviço">{{ $item->servico }}</td>
                                    <td data-label="Status"><span class="history-status-badge" style="background-color: {{ $historyStatusColor }};">{{ $historyStatusLabel }}</span></td>
                                    <td class="table-mobile-full action-button-cell" data-label="Ações">
                                        <div class="patient-history-actions action-button-group">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-secondary"
                                                data-history-name="{{ e($item->nome ?: '-') }}"
                                                data-history-professional="{{ e($item->medico_historico ?: '-') }}"
                                                data-history-email="{{ e($item->email ?: '-') }}"
                                                data-history-phone="{{ e($item->telefone ?: '-') }}"
                                                data-history-cpf="{{ e($historyCpf ?: '-') }}"
                                                data-history-photo="{{ e($item->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png')) }}"
                                                data-history-service="{{ e($item->servico ?: '-') }}"
                                                data-history-date="{{ e($item->data_agendamento?->format('d/m/Y') ?: '-') }}"
                                                data-history-start-time="{{ e(substr((string) $item->horario, 0, 5) ?: '-') }}"
                                                data-history-end-time="{{ e($historyEndTime ?: '-') }}"
                                                data-history-status="{{ e($historyStatusLabel) }}"
                                                data-history-status-color="{{ e($historyStatusColor) }}"
                                                data-history-created-at="{{ e($item->created_at?->format('d/m/Y H:i') ?: '-') }}"
                                                data-history-description="{{ e($historyDescription) }}"
                                                @if(auth()->user()?->canMutateOutsideCadastrosBase())
                                                    data-history-edit-url="{{ e(route('admin.agendamentos.edit', ['agendamento' => $item, 'return_to' => url()->full()])) }}"
                                                @endif
                                            >Ver</button>
                                            @if(auth()->user()?->canMutateOutsideCadastrosBase())
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-info"
                                                    data-history-name="{{ e($item->nome ?: '-') }}"
                                                    data-history-professional="{{ e($item->medico_historico ?: '-') }}"
                                                    data-history-email="{{ e($item->email ?: '-') }}"
                                                    data-history-phone="{{ e($item->telefone ?: '-') }}"
                                                    data-history-cpf="{{ e($historyCpf ?: '-') }}"
                                                    data-history-photo="{{ e($item->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png')) }}"
                                                    data-history-service="{{ e($item->servico ?: '-') }}"
                                                    data-history-date="{{ e($item->data_agendamento?->format('d/m/Y') ?: '-') }}"
                                                    data-history-start-time="{{ e(substr((string) $item->horario, 0, 5) ?: '-') }}"
                                                    data-history-end-time="{{ e($historyEndTime ?: '-') }}"
                                                    data-history-status="{{ e($historyStatusLabel) }}"
                                                    data-history-status-color="{{ e($historyStatusColor) }}"
                                                    data-history-created-at="{{ e($item->created_at?->format('d/m/Y H:i') ?: '-') }}"
                                                    data-history-description="{{ e($historyDescription) }}"
                                                    data-history-edit-url="{{ e(route('admin.agendamentos.edit', ['agendamento' => $item, 'return_to' => url()->full()])) }}"
                                                >Editar</button>
                                                <form action="{{ route('admin.agendamentos.cancel', $item) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancelar este agendamento?')">Cancelar</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum histórico disponível.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($history->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $history->links('vendor.pagination.patients-blocks') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="history-details-modal" id="historyAppointmentDetailsModal" aria-hidden="true">
            <div class="history-details-dialog">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
                    <h4 class="mb-0">Informações do agendamento finalizado</h4>
                    <button type="button" class="btn btn-link p-0 text-muted" data-history-modal-close aria-label="Fechar" style="font-size: 24px; line-height: 1;">&times;</button>
                </div>
                <div class="card-body">
                    <div class="history-details-grid">
                        <div class="history-detail-item history-detail-item-full">
                            <div class="history-patient-summary">
                                <img src="{{ asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto do paciente" class="history-modal-photo" data-history-modal-photo>
                                <div class="history-patient-summary-copy">
                                    <label>Paciente</label>
                                    <p data-history-modal-name>-</p>
                                    <small>Resumo do atendimento concluído</small>
                                </div>
                            </div>
                        </div>
                        <div class="history-detail-item"><label>Serviço</label><p data-history-modal-service>-</p></div>
                        <div class="history-detail-item"><label>Profissional</label><p data-history-modal-professional>-</p></div>
                        <div class="history-detail-item"><label>Data</label><p data-history-modal-date>-</p></div>
                        <div class="history-detail-item"><label>Status</label><p><span class="history-status-badge" data-history-modal-status style="background-color: #0f5aa6;">-</span></p></div>
                        <div class="history-detail-item"><label>Email</label><p data-history-modal-email>-</p></div>
                        <div class="history-detail-item"><label>Telefone</label><p data-history-modal-phone>-</p></div>
                        <div class="history-detail-item"><label>CPF</label><p data-history-modal-cpf>-</p></div>
                        <div class="history-detail-item"><label>Horário inicial</label><p data-history-modal-start-time>-</p></div>
                        <div class="history-detail-item"><label>Horário final</label><p data-history-modal-end-time>-</p></div>
                        <div class="history-detail-item"><label>Criado em</label><p data-history-modal-created-at>-</p></div>
                        <div class="history-detail-item history-detail-item-full"><label>Descrição</label><p data-history-modal-description>-</p></div>
                    </div>
                </div>
                <div class="card-header d-flex justify-content-end" style="gap: 8px;">
                    <a href="#" class="btn btn-warning d-none" data-history-modal-edit>Editar</a>
                    <button type="button" class="btn btn-light" data-history-modal-close>Fechar</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var defaultPhotoUrl = @json(asset('backend/assets/img/avatar/avatar-1.png'));
        var detailsButtons = document.querySelectorAll('[data-history-name]');
        var detailsModal = document.getElementById('historyAppointmentDetailsModal');
        var closeButtons = detailsModal ? detailsModal.querySelectorAll('[data-history-modal-close]') : [];

        if (!detailsButtons.length || !detailsModal) {
            return;
        }

        var fields = {
            photo: detailsModal.querySelector('[data-history-modal-photo]'),
            name: detailsModal.querySelector('[data-history-modal-name]'),
            professional: detailsModal.querySelector('[data-history-modal-professional]'),
            email: detailsModal.querySelector('[data-history-modal-email]'),
            phone: detailsModal.querySelector('[data-history-modal-phone]'),
            cpf: detailsModal.querySelector('[data-history-modal-cpf]'),
            service: detailsModal.querySelector('[data-history-modal-service]'),
            date: detailsModal.querySelector('[data-history-modal-date]'),
            startTime: detailsModal.querySelector('[data-history-modal-start-time]'),
            endTime: detailsModal.querySelector('[data-history-modal-end-time]'),
            createdAt: detailsModal.querySelector('[data-history-modal-created-at]'),
            description: detailsModal.querySelector('[data-history-modal-description]'),
            status: detailsModal.querySelector('[data-history-modal-status]'),
            editLink: detailsModal.querySelector('[data-history-modal-edit]')
        };

        function closeModal() {
            detailsModal.classList.remove('is-open');
            detailsModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        detailsButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                fields.photo.setAttribute('src', button.dataset.historyPhoto || defaultPhotoUrl);
                fields.photo.setAttribute('alt', 'Foto de ' + (button.dataset.historyName || 'paciente'));
                fields.name.textContent = button.dataset.historyName || '-';
                fields.professional.textContent = button.dataset.historyProfessional || '-';
                fields.email.textContent = button.dataset.historyEmail || '-';
                fields.phone.textContent = button.dataset.historyPhone || '-';
                fields.cpf.textContent = button.dataset.historyCpf || '-';
                fields.service.textContent = button.dataset.historyService || '-';
                fields.date.textContent = button.dataset.historyDate || '-';
                fields.startTime.textContent = button.dataset.historyStartTime || '-';
                fields.endTime.textContent = button.dataset.historyEndTime || '-';
                fields.createdAt.textContent = button.dataset.historyCreatedAt || '-';
                fields.description.textContent = button.dataset.historyDescription || '-';
                fields.status.textContent = button.dataset.historyStatus || '-';
                fields.status.style.backgroundColor = button.dataset.historyStatusColor || '#0f5aa6';

                if (button.dataset.historyEditUrl) {
                    fields.editLink.href = button.dataset.historyEditUrl;
                    fields.editLink.classList.remove('d-none');
                } else {
                    fields.editLink.href = '#';
                    fields.editLink.classList.add('d-none');
                }

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
