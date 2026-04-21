@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Agenda Geral</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Agendamentos</div>
            <div class="breadcrumb-item">Agenda Geral</div>
        </div>
    </div>

    <div class="section-body">
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
                                        <div class="{{ !empty($hideProfessionalFilter) ? 'col-lg-8' : 'col-lg-6' }} col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="q">Busca global</label>
                                                <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Digite o nome, CPF ou data do agendamento">
                                            </div>
                                        </div>
                                        @if(empty($hideProfessionalFilter))
                                            <div class="col-lg-4 col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="medico">Médico</label>
                                                    <select class="form-control" id="medico" name="medico">
                                                        <option value="">Todos</option>
                                                        @foreach($professionals as $professional)
                                                            <option value="{{ $professional['nome'] }}" {{ request('medico') === $professional['nome'] ? 'selected' : '' }}>{{ $professional['nome'] }}</option>
                                                        @endforeach
                                                    </select>
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

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nome</th>
                                        <th class="text-center">CPF</th>
                                        <th class="text-center">Médico</th>
                                        <th class="text-center">Serviço</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Horário</th>
                                        <th class="text-center">Horário Final</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($agendamentos as $agendamento)
                                    @php
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
                                            <span class="badge text-white" style="background-color: {{ $agendamento->status_visual['color'] }};">{{ $agendamento->status_visual['label'] }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="{{ route('admin.agendamentos.show', ['agendamento' => $agendamento, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-info">Ver</a>
                                            <a href="{{ route('admin.agendamentos.edit', ['agendamento' => $agendamento, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-warning">Editar</a>
                                            <form action="{{ route('admin.agendamentos.cancel', $agendamento) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancelar este agendamento?')">Cancelar</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Nenhum agendamento encontrado</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
