@extends('admin.layouts.master')
@section('content')
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
                                                {{ ucfirst($appointment->status ?? 'pendente') }}
                                            </span>
                                        </td>
                                        <td class="action-button-cell table-mobile-full" data-label="Ações">
                                            <form method="POST" action="{{ route('admin.agendamentos.cancel-operational', $appointment) }}" class="mb-0 d-inline-block">
                                                @csrf
                                                <input type="hidden" name="q" value="{{ $search }}">
                                                <input type="hidden" name="date" value="{{ $selectedDate }}">
                                                <input type="hidden" name="period" value="{{ $period }}">
                                                <input type="hidden" name="professional_id" value="{{ $selectedProfessionalId ?? '' }}">
                                                <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Deseja cancelar este atendimento?');">Cancelar atendimento</button>
                                            </form>
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
@endsection
