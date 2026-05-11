@extends('admin.layouts.master')
@section('content')
<section class="section">
    @php
        $selectedProfessionals = collect((array) request()->input('medicos', []))
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->values();
        $shouldGroupAppointments = $selectedProfessionals->count() > 1;
        $groupedAgendamentos = $shouldGroupAppointments
            ? $agendamentos->groupBy(fn ($agendamento) => $agendamento->medico_exibicao ?: 'Profissional não informado')
            : collect(['Lista de Agendamentos' => $agendamentos]);
    @endphp
    <style>
        .agenda-group-card {
            border: 1px solid #dce9f7;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 61, 107, 0.08);
            overflow: hidden;
        }

        .agenda-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 22px;
            background: linear-gradient(135deg, #f4f9ff 0%, #e9f3ff 100%);
            border-bottom: 1px solid #d9e8f7;
        }

        .agenda-group-title {
            margin: 0;
            color: #0f3d6b;
            font-size: 18px;
            font-weight: 800;
        }

        .agenda-group-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            height: 42px;
            padding: 0 14px;
            border-radius: 999px;
            background: #0f5aa6;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
        }

        .agenda-enhanced-table thead th {
            background: #f7fbff;
            color: #41617d;
            border-top: 0;
            font-size: 12px;
            letter-spacing: .04em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .agenda-enhanced-table {
            min-width: 1040px;
        }

        .agenda-enhanced-table td {
            white-space: nowrap;
        }

        .agenda-enhanced-table td:nth-child(1),
        .agenda-enhanced-table td:nth-child(2),
        .agenda-enhanced-table td:nth-child(3) {
            white-space: normal;
            min-width: 154px;
        }

        .agenda-enhanced-table tbody tr:hover {
            background: rgba(15, 90, 166, 0.05);
        }

        .agenda-status-badge {
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

        .agenda-patient-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 170px;
        }

        .agenda-service-cell {
            min-width: 180px;
            white-space: normal;
        }

        .agenda-end-cell {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .agenda-patient-cell img,
        .agenda-modal-photo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(23, 111, 190, 0.12);
            flex: 0 0 auto;
        }

        .agenda-modal-photo {
            width: 92px;
            height: 92px;
            margin: 0;
        }

        .agenda-patient-summary {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .agenda-patient-summary-copy {
            min-width: 0;
            text-align: left;
        }

        .agenda-patient-summary-copy p {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #16344d;
        }

        .agenda-patient-summary-copy small {
            display: block;
            margin-top: 6px;
            color: #5b7895;
        }

        .agenda-actions {
            display: inline-flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .agenda-actions form {
            margin: 0;
        }

        .agenda-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .agenda-stat-card .card-icon {
            margin: 14px 14px 0;
        }

        .agenda-filters-grid {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .agenda-filters-fields {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 0;
        }

        .agenda-filters-actions {
            margin-top: 2px;
        }

        .agenda-stat-col {
            flex: 0 0 auto;
            width: auto;
            max-width: 100%;
        }

        .agenda-stat-card {
            width: fit-content;
            min-width: 190px;
            max-width: 100%;
            margin-right: auto;
        }

        .agenda-stat-card .card-wrap {
            padding: 14px 14px 16px;
        }

        .agenda-stat-card .card-header h4 {
            font-size: 11px;
            line-height: 1.25;
            white-space: normal;
            word-break: normal;
            overflow-wrap: normal;
            margin-bottom: 0;
            text-align: left;
        }

        .agenda-stat-card .card-body {
            font-size: 1.35rem;
            line-height: 1.1;
        }

        .agenda-details-modal {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(9, 17, 26, 0.52);
        }

        .agenda-details-modal.is-open {
            display: flex;
        }

        .agenda-details-dialog {
            width: min(760px, 100%);
            max-height: calc(100vh - 40px);
            overflow: auto;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 24px 54px rgba(15, 23, 42, 0.24);
        }

        .agenda-details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px 18px;
        }

        .agenda-detail-item {
            min-width: 0;
        }

        .agenda-detail-item-full {
            grid-column: 1 / -1;
        }

        .agenda-detail-item label {
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #5b7895;
        }

        .agenda-detail-item p {
            margin-bottom: 0;
            color: #16344d;
            word-break: break-word;
        }

        .agenda-details-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .agenda-details-close {
            border: 0;
            background: transparent;
            color: #5b7895;
            font-size: 24px;
            line-height: 1;
            padding: 0;
        }

        html[data-theme="dark"] .agenda-group-card,
        html[data-theme="dark"] .section-body .card {
            background: linear-gradient(180deg, rgba(22, 40, 59, 0.98) 0%, rgba(19, 33, 49, 0.98) 100%);
            border-color: rgba(143, 197, 255, 0.16);
            box-shadow: 0 18px 34px rgba(2, 8, 15, 0.34);
        }

        html[data-theme="dark"] .section-body .card-header,
        html[data-theme="dark"] .section-body .card-body,
        html[data-theme="dark"] .agenda-group-header {
            background: transparent !important;
            color: #eef5fc;
            border-color: rgba(143, 197, 255, 0.16);
        }

        html[data-theme="dark"] .agenda-group-header {
            background: linear-gradient(135deg, rgba(24, 43, 64, 0.98) 0%, rgba(19, 33, 49, 0.98) 100%) !important;
        }

        html[data-theme="dark"] .agenda-group-title,
        html[data-theme="dark"] .card-header h4,
        html[data-theme="dark"] .form-group label,
        html[data-theme="dark"] .section-header h1,
        html[data-theme="dark"] .breadcrumb-item,
        html[data-theme="dark"] .breadcrumb-item a {
            color: #eef5fc !important;
        }

        html[data-theme="dark"] .agenda-enhanced-table,
        html[data-theme="dark"] .agenda-enhanced-table tbody tr,
        html[data-theme="dark"] .agenda-enhanced-table tbody td,
        html[data-theme="dark"] .table-striped tbody tr:nth-of-type(odd) {
            background: transparent !important;
            color: #eef5fc;
        }

        html[data-theme="dark"] .agenda-enhanced-table thead th {
            background: rgba(24, 43, 64, 0.96);
            color: #a9c5df;
            border-color: rgba(143, 197, 255, 0.16);
        }

        html[data-theme="dark"] .agenda-enhanced-table tbody tr:hover {
            background: rgba(118, 187, 255, 0.08) !important;
        }

        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .custom-select,
        html[data-theme="dark"] select.form-control {
            background: #16283b !important;
            border-color: rgba(143, 197, 255, 0.16) !important;
            color: #eef5fc !important;
        }

        html[data-theme="dark"] .btn-light {
            background: rgba(24, 43, 64, 0.96) !important;
            border-color: rgba(143, 197, 255, 0.16) !important;
            color: #eef5fc !important;
        }

        html[data-theme="dark"] .btn-outline-primary {
            border-color: rgba(118, 187, 255, 0.36) !important;
            color: #cfe6fb !important;
        }

        html[data-theme="dark"] .btn-outline-primary:hover {
            background: rgba(58, 127, 198, 0.18) !important;
            color: #ffffff !important;
        }

        html[data-theme="dark"] .card-statistic-1 {
            background: rgba(24, 43, 64, 0.98) !important;
            border: 1px solid rgba(143, 197, 255, 0.16);
            box-shadow: none;
        }

        html[data-theme="dark"] .card-statistic-1 .card-header h4,
        html[data-theme="dark"] .card-statistic-1 .card-body,
        html[data-theme="dark"] .card-statistic-1 .card-wrap {
            color: #eef5fc !important;
        }

        html[data-theme="dark"] .agenda-detail-item label {
            color: #a9c5df;
        }

        html[data-theme="dark"] .agenda-detail-item p {
            color: #eef5fc;
        }

        html[data-theme="dark"] .agenda-patient-summary-copy p {
            color: #eef5fc;
        }

        html[data-theme="dark"] .agenda-patient-summary-copy small {
            color: #a9c5df;
        }

        html[data-theme="dark"] .agenda-details-dialog {
            background: linear-gradient(180deg, rgba(22, 40, 59, 0.99) 0%, rgba(19, 33, 49, 0.99) 100%);
            border: 1px solid rgba(143, 197, 255, 0.16);
        }

        html[data-theme="dark"] .agenda-details-close {
            color: #a9c5df;
        }

        @media (max-width: 991.98px) {
            .agenda-group-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .agenda-enhanced-table {
                min-width: 900px;
            }
        }

        @media (max-width: 767.98px) {
            .agenda-stat-col {
                max-width: 100%;
            }

            .agenda-stat-card {
                width: 100%;
                min-width: 0;
                max-width: 100%;
            }

            .agenda-filters-fields {
                display: block;
            }

            .agenda-details-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .agenda-patient-summary {
                flex-direction: column;
                align-items: flex-start;
            }

            .agenda-details-modal {
                padding: 12px;
            }

            .agenda-group-card {
                border-radius: 14px;
            }

            .agenda-group-header {
                padding: 16px;
            }

            .agenda-group-title {
                font-size: 16px;
            }

            .agenda-group-count {
                min-width: 38px;
                height: 38px;
            }

            .agenda-enhanced-table {
                min-width: 760px;
            }

            .agenda-status-badge {
                min-width: 96px;
                padding: 6px 10px;
            }
        }
    </style>
    <div class="section-header">
        <h1>Agenda Geral</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Agendamentos</div>
            <div class="breadcrumb-item">Agenda Geral</div>
        </div>
    </div>

    <div class="section-body">
        @php
            $showMultiProfessionalFilter = count($professionals) > 1;
        @endphp

        <div class="row">
            <div class="col-12">
                <div class="row mb-4">
                    <div class="col-xl-auto col-lg-auto col-md-5 col-12 agenda-stat-col">
                        <div class="card card-statistic-1 agenda-stat-card mb-0">
                            <div class="card-icon bg-primary"><i class="fas fa-calendar-check"></i></div>
                            <div class="card-wrap">
                                <div class="card-header"><h4>Total agendamentos</h4></div>
                                <div class="card-body">{{ $totalAgendamentos }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Filtros rápidos</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.agendamentos.index') }}">
                            <div class="agenda-filters-grid">
                                <div class="row agenda-filters-fields">
                                        <div class="{{ $showMultiProfessionalFilter ? 'col-xl-4 col-lg-5' : 'col-lg-6' }} col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="q">Busca global</label>
                                                <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Digite o nome, CPF ou data do agendamento">
                                            </div>
                                        </div>
                                        <div class="col-xl-2 col-lg-3 col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="medico">Profissional</label>
                                                <select class="form-control" id="medico" name="medico" data-native-select="true">
                                                    <option value="">{{ $showMultiProfessionalFilter ? 'Todos' : 'Selecione' }}</option>
                                                    @foreach($professionals as $professional)
                                                        <option value="{{ $professional['nome'] }}" {{ request('medico') === $professional['nome'] ? 'selected' : '' }}>{{ $professional['nome'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @if($showMultiProfessionalFilter)
                                            <div class="col-lg-12 col-12 mt-2">
                                                <label class="d-block">Visualizar até 3 Profissionais</label>
                                                <div class="row">
                                                    @for($doctorIndex = 0; $doctorIndex < 3; $doctorIndex++)
                                                        <div class="col-xl-2 col-lg-3 col-md-4 col-12">
                                                            <div class="form-group">
                                                                <select class="form-control" name="medicos[]" data-native-select="true">
                                                                    <option value="">Profissional {{ $doctorIndex + 1 }}</option>
                                                                    @foreach($professionals as $professional)
                                                                        <option value="{{ $professional['nome'] }}" {{ (request('medicos.' . $doctorIndex) === $professional['nome']) ? 'selected' : '' }}>{{ $professional['nome'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        @endif
                                </div>
                                <div class="row agenda-filters-actions">
                                        <div class="col-lg-12 col-12">
                                            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                                <button type="submit" class="btn btn-primary px-4">Aplicar filtros</button>
                                                <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-light px-4">Limpar</a>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
                        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                            <h4 class="mb-0">Lista de Agendamentos</h4>
                            <a href="{{ route('admin.agendamentos.index', array_merge(request()->except('page', 'period'), ['period' => 'dia'])) }}" class="btn btn-outline-primary btn-sm">Agendamentos do Dia</a>
                            <a href="{{ route('admin.agendamentos.index', array_merge(request()->except('page', 'period'), ['period' => 'semana'])) }}" class="btn btn-outline-primary btn-sm">Agendamentos da Semana</a>
                            <a href="{{ route('admin.agendamentos.index', array_merge(request()->except('page', 'period'), ['period' => 'mes'])) }}" class="btn btn-outline-primary btn-sm">Agendamentos do Mês</a>
                            <a href="{{ route('admin.agendamentos.index', request()->except('page', 'period')) }}" class="btn btn-light btn-sm">Ver Todos</a>
                        </div>
                        <div class="card-header-action">
                            <a href="{{ route('admin.agendamentos.create') }}" class="btn btn-primary btn-sm">Novo Agendamento</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @forelse($groupedAgendamentos as $groupTitle => $appointmentsGroup)
                            <div class="agenda-group-card {{ $loop->first ? '' : 'mt-4' }}">
                                @if($shouldGroupAppointments)
                                    <div class="agenda-group-header">
                                        <h5 class="agenda-group-title">{{ $groupTitle }}</h5>
                                        <span class="agenda-group-count">{{ $appointmentsGroup->count() }}</span>
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-striped agenda-enhanced-table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Nome</th>
                                                <th class="text-center">Serviço</th>
                                                <th class="text-center">Profissional</th>
                                                <th class="text-center">Data</th>
                                                <th class="text-center">Horário</th>
                                                <th class="text-center">Final</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($appointmentsGroup as $agendamento)
                                                @php
                                                    $canManageAppointment = ! auth()->user()?->isClinicManager();
                                                    $endTime = $agendamento->data_agendamento->copy()
                                                        ->setTimeFromTimeString(substr((string) $agendamento->horario, 0, 5))
                                                        ->addMinutes((int) ($agendamento->duracao_exibicao ?? $agendamento->duracao_minutos ?? 30))
                                                        ->format('H:i');
                                                    $appointmentCpf = preg_replace('/\D+/', '', (string) ($agendamento->cpf_exibicao ?? $agendamento->patient?->cpf ?? $agendamento->cpf ?? ''));
                                                    $appointmentCpf = strlen($appointmentCpf) === 11
                                                        ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $appointmentCpf)
                                                        : ($agendamento->cpf_exibicao ?? $agendamento->patient?->cpf ?? $agendamento->cpf ?? '-');
                                                    $appointmentStatusLabel = $agendamento->status_visual['label'] ?? ucfirst((string) $agendamento->status);
                                                    $appointmentDescription = $agendamento->motivo_consulta ?: ($agendamento->descricao ?: '-');
                                                    $appointmentEditUrl = $canManageAppointment ? route('admin.agendamentos.edit', ['agendamento' => $agendamento, 'return_to' => url()->full()]) : '';
                                                @endphp
                                                <tr>
                                                    <td class="text-center align-middle">
                                                        <div class="agenda-patient-cell">
                                                            <img src="{{ $agendamento->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto de {{ $agendamento->nome }}">
                                                            <span>{{ $agendamento->nome }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle agenda-service-cell">{{ $agendamento->servico }}</td>
                                                    <td class="text-center align-middle">{{ $agendamento->medico_exibicao }}</td>
                                                    <td class="text-center align-middle">{{ $agendamento->data_agendamento->format('d/m/Y') }}</td>
                                                    <td class="text-center align-middle">{{ $agendamento->horario }}</td>
                                                    <td class="text-center align-middle">{{ $endTime }}</td>
                                                    <td class="text-center align-middle">
                                                        <span class="agenda-status-badge" style="background-color: {{ $agendamento->status_visual['color'] }};">{{ $agendamento->status_visual['label'] }}</span>
                                                    </td>
                                                    <td class="text-center align-middle action-button-cell">
                                                        <div class="agenda-actions action-button-group">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-secondary"
                                                            data-agenda-name="{{ e($agendamento->nome ?: '-') }}"
                                                            data-agenda-professional="{{ e($agendamento->medico_exibicao ?: '-') }}"
                                                            data-agenda-email="{{ e($agendamento->email ?: '-') }}"
                                                            data-agenda-phone="{{ e($agendamento->telefone ?: '-') }}"
                                                            data-agenda-cpf="{{ e($appointmentCpf ?: '-') }}"
                                                            data-agenda-photo="{{ e($agendamento->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png')) }}"
                                                            data-agenda-service="{{ e($agendamento->servico ?: '-') }}"
                                                            data-agenda-date="{{ e($agendamento->data_agendamento?->format('d/m/Y') ?: '-') }}"
                                                            data-agenda-start-time="{{ e(substr((string) $agendamento->horario, 0, 5) ?: '-') }}"
                                                            data-agenda-end-time="{{ e($endTime ?: '-') }}"
                                                            data-agenda-status="{{ e($appointmentStatusLabel ?: '-') }}"
                                                            data-agenda-status-color="{{ e($agendamento->status_visual['color'] ?? '#0f5aa6') }}"
                                                            data-agenda-created-at="{{ e($agendamento->created_at?->format('d/m/Y H:i') ?: '-') }}"
                                                            data-agenda-description="{{ e($appointmentDescription) }}"
                                                            data-agenda-edit-url="{{ e($appointmentEditUrl) }}"
                                                        >Ver</button>
                                                        @if($canManageAppointment)
                                                            <a href="{{ route('admin.agendamentos.edit', ['agendamento' => $agendamento, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-info">Editar</a>
                                                            <form action="{{ route('admin.agendamentos.cancel', $agendamento) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancelar este agendamento?')">Cancelar</button>
                                                            </form>
                                                        @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <div class="table-responsive">
                                <table class="table table-striped agenda-enhanced-table mb-0">
                                    <tbody>
                                        <tr>
                                            <td colspan="8" class="text-center">Nenhum agendamento encontrado</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <div class="agenda-details-modal" id="agendaAppointmentDetailsModal" aria-hidden="true">
            <div class="agenda-details-dialog">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
                    <h4 class="mb-0">Informações do paciente</h4>
                    <button type="button" class="agenda-details-close" data-agenda-modal-close aria-label="Fechar">&times;</button>
                </div>
                <div class="card-body">
                    <div class="agenda-details-grid">
                        <div class="agenda-detail-item agenda-detail-item-full">
                            <div class="agenda-patient-summary">
                                <img src="{{ asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto do paciente" class="agenda-modal-photo" data-agenda-modal-photo>
                                <div class="agenda-patient-summary-copy">
                                    <label>Paciente</label>
                                    <p data-agenda-modal-name>-</p>
                                    <small>Informações principais do atendimento</small>
                                </div>
                            </div>
                        </div>
                        <div class="agenda-detail-item">
                            <label>Serviço</label>
                            <p data-agenda-modal-service>-</p>
                        </div>
                        <div class="agenda-detail-item">
                            <label>Profissional</label>
                            <p data-agenda-modal-professional>-</p>
                        </div>
                        <div class="agenda-detail-item">
                            <label>Data</label>
                            <p data-agenda-modal-date>-</p>
                        </div>
                        <div class="agenda-detail-item">
                            <label>Status</label>
                            <p><span class="agenda-status-badge" data-agenda-modal-status style="background-color: #0f5aa6;">-</span></p>
                        </div>
                        <div class="agenda-detail-item">
                            <label>Email</label>
                            <p data-agenda-modal-email>-</p>
                        </div>
                        <div class="agenda-detail-item">
                            <label>Telefone</label>
                            <p data-agenda-modal-phone>-</p>
                        </div>
                        <div class="agenda-detail-item">
                            <label>CPF</label>
                            <p data-agenda-modal-cpf>-</p>
                        </div>
                        <div class="agenda-detail-item">
                            <label>Horário inicial</label>
                            <p data-agenda-modal-start-time>-</p>
                        </div>
                        <div class="agenda-detail-item">
                            <label>Horário final</label>
                            <p data-agenda-modal-end-time>-</p>
                        </div>
                        <div class="agenda-detail-item">
                            <label>Criado em</label>
                            <p data-agenda-modal-created-at>-</p>
                        </div>
                        <div class="agenda-detail-item agenda-detail-item-full">
                            <label>Descrição</label>
                            <p data-agenda-modal-description>-</p>
                        </div>
                    </div>
                </div>
                <div class="card-header agenda-details-actions">
                    <a href="#" class="btn btn-warning d-none" data-agenda-modal-edit>Editar</a>
                    <button type="button" class="btn btn-light" data-agenda-modal-close>Fechar</button>
                </div>
            </div>
        </div>

    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var detailsButtons = document.querySelectorAll('[data-agenda-name]');
        var detailsModal = document.getElementById('agendaAppointmentDetailsModal');
        var closeButtons = detailsModal ? detailsModal.querySelectorAll('[data-agenda-modal-close]') : [];

        if (!detailsButtons.length || !detailsModal) {
            return;
        }

        var fields = {
            photo: detailsModal.querySelector('[data-agenda-modal-photo]'),
            name: detailsModal.querySelector('[data-agenda-modal-name]'),
            professional: detailsModal.querySelector('[data-agenda-modal-professional]'),
            email: detailsModal.querySelector('[data-agenda-modal-email]'),
            phone: detailsModal.querySelector('[data-agenda-modal-phone]'),
            cpf: detailsModal.querySelector('[data-agenda-modal-cpf]'),
            service: detailsModal.querySelector('[data-agenda-modal-service]'),
            date: detailsModal.querySelector('[data-agenda-modal-date]'),
            startTime: detailsModal.querySelector('[data-agenda-modal-start-time]'),
            endTime: detailsModal.querySelector('[data-agenda-modal-end-time]'),
            createdAt: detailsModal.querySelector('[data-agenda-modal-created-at]'),
            description: detailsModal.querySelector('[data-agenda-modal-description]'),
            status: detailsModal.querySelector('[data-agenda-modal-status]'),
            editLink: detailsModal.querySelector('[data-agenda-modal-edit]')
        };

        var closeModal = function () {
            detailsModal.classList.remove('is-open');
            detailsModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };

        detailsButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                fields.photo.setAttribute('src', button.dataset.agendaPhoto || '{{ asset('backend/assets/img/avatar/avatar-1.png') }}');
                fields.photo.setAttribute('alt', 'Foto de ' + (button.dataset.agendaName || 'paciente'));
                fields.name.textContent = button.dataset.agendaName || '-';
                fields.professional.textContent = button.dataset.agendaProfessional || '-';
                fields.email.textContent = button.dataset.agendaEmail || '-';
                fields.phone.textContent = button.dataset.agendaPhone || '-';
                fields.cpf.textContent = button.dataset.agendaCpf || '-';
                fields.service.textContent = button.dataset.agendaService || '-';
                fields.date.textContent = button.dataset.agendaDate || '-';
                fields.startTime.textContent = button.dataset.agendaStartTime || '-';
                fields.endTime.textContent = button.dataset.agendaEndTime || '-';
                fields.createdAt.textContent = button.dataset.agendaCreatedAt || '-';
                fields.description.textContent = button.dataset.agendaDescription || '-';
                fields.status.textContent = button.dataset.agendaStatus || '-';
                fields.status.style.backgroundColor = button.dataset.agendaStatusColor || '#0f5aa6';

                if (button.dataset.agendaEditUrl) {
                    fields.editLink.href = button.dataset.agendaEditUrl;
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
