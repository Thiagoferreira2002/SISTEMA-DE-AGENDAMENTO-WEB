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
            min-width: 1180px;
        }

        .agenda-enhanced-table td {
            white-space: nowrap;
        }

        .agenda-enhanced-table td:nth-child(1),
        .agenda-enhanced-table td:nth-child(3),
        .agenda-enhanced-table td:nth-child(4) {
            white-space: normal;
            min-width: 170px;
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

        @media (max-width: 991.98px) {
            .agenda-group-header {
                flex-direction: column;
                align-items: flex-start;
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
                <div class="card">
                    <div class="card-header">
                        <h4>Filtros rápidos</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.agendamentos.index') }}">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0">
                                    <div class="card card-statistic-1 mb-0 h-100">
                                        <div class="card-icon bg-primary"><i class="fas fa-calendar-check"></i></div>
                                        <div class="card-wrap">
                                            <div class="card-header"><h4>Total de Agendamentos</h4></div>
                                            <div class="card-body">{{ $totalAgendamentos }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 col-12">
                                    <div class="row">
                                        <div class="{{ $showMultiProfessionalFilter ? 'col-lg-6' : 'col-lg-8' }} col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="q">Busca global</label>
                                                <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Digite o nome, CPF ou data do agendamento">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
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
                                                        <div class="col-lg-4 col-md-6 col-12">
                                                            <div class="form-group">
                                                                <select class="form-control" name="medicos[]" data-native-select="true">
                                                                    <option value="">Selecione o Profissional {{ $doctorIndex + 1 }}</option>
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
                                        <div class="col-lg-12 col-12">
                                            <div class="d-flex flex-wrap" style="gap: 8px;">
                                                <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                                                <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-light">Limpar</a>
                                            </div>
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
                                                <th class="text-center">CPF</th>
                                                <th class="text-center">Profissional</th>
                                                <th class="text-center">Serviço</th>
                                                <th class="text-center">Data</th>
                                                <th class="text-center">Horário</th>
                                                <th class="text-center">Horário Final</th>
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
                                                @endphp
                                                <tr>
                                                    <td class="text-center align-middle">{{ $agendamento->nome }}</td>
                                                    <td class="text-center align-middle">{{ $agendamento->cpf_exibicao ?: '-' }}</td>
                                                    <td class="text-center align-middle">{{ $agendamento->medico_exibicao }}</td>
                                                    <td class="text-center align-middle">{{ $agendamento->servico }}</td>
                                                    <td class="text-center align-middle">{{ $agendamento->data_agendamento->format('d/m/Y') }}</td>
                                                    <td class="text-center align-middle">{{ $agendamento->horario }}</td>
                                                    <td class="text-center align-middle">{{ $endTime }}</td>
                                                    <td class="text-center align-middle">
                                                        <span class="agenda-status-badge" style="background-color: {{ $agendamento->status_visual['color'] }};">{{ $agendamento->status_visual['label'] }}</span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <a href="{{ route('admin.agendamentos.show', ['agendamento' => $agendamento, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-info">Ver</a>
                                                        @if($canManageAppointment)
                                                            <a href="{{ route('admin.agendamentos.edit', ['agendamento' => $agendamento, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-warning">Editar</a>
                                                            <form action="{{ route('admin.agendamentos.cancel', $agendamento) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancelar este agendamento?')">Cancelar</button>
                                                            </form>
                                                        @endif
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
                                            <td colspan="9" class="text-center">Nenhum agendamento encontrado</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
