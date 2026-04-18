@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Confirmações</h1>
    </div>

    <div class="section-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-hourglass-half"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Pendentes</h4></div>
                        <div class="card-body">{{ $summary['pendentes'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-check"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Confirmados</h4></div>
                        <div class="card-body">{{ $summary['confirmados'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-times"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Cancelados</h4></div>
                        <div class="card-body">{{ $summary['cancelados'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Painel operacional de confirmações</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.agendamentos.confirmations') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="q">Busca por nome ou CPF</label>
                                <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Digite o nome ou CPF do paciente">
                            </div>
                            <div class="mb-3 d-flex flex-wrap" style="gap: 8px;">
                                @if(request()->filled('period'))
                                    <input type="hidden" name="period" value="{{ request('period') }}">
                                @endif
                                <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                                <a href="{{ route('admin.agendamentos.confirmations', request()->except('q', 'status')) }}" class="btn btn-light">Limpar</a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">Todos</option>
                                    <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="confirmado" {{ request('status') === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                                    <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </form>

                <div class="mb-3 d-flex flex-wrap justify-content-end" style="gap: 8px;">
                    <a href="{{ route('admin.agendamentos.confirmations', array_merge(request()->except('page', 'period'), ['period' => 'dia'])) }}" class="btn btn-outline-primary btn-sm">Dia</a>
                    <a href="{{ route('admin.agendamentos.confirmations', array_merge(request()->except('page', 'period'), ['period' => 'semana'])) }}" class="btn btn-outline-primary btn-sm">Semana</a>
                    <a href="{{ route('admin.agendamentos.confirmations', array_merge(request()->except('page', 'period'), ['period' => 'mes'])) }}" class="btn btn-outline-primary btn-sm">Mês</a>
                    <a href="{{ route('admin.agendamentos.confirmations', request()->except('page', 'period')) }}" class="btn btn-light btn-sm">Ver todos</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">Paciente</th>
                                <th class="text-center">Data</th>
                                <th class="text-center">Serviço</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Canal sugerido</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                @php
                                    $status = $appointment->status ?: 'pendente';
                                @endphp
                                <tr>
                                    <td class="text-center align-middle">{{ $appointment->nome }}</td>
                                    <td class="text-center align-middle">{{ $appointment->data_agendamento->format('d/m/Y') }} às {{ $appointment->horario }}</td>
                                    <td class="text-center align-middle">{{ $appointment->servico }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-{{ $status === 'confirmado' ? 'success' : ($status === 'cancelado' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $appointment->telefone) }}?text={{ urlencode('Olá ' . $appointment->nome . ', confirmamos sua consulta amanhã às ' . $appointment->horario . '?') }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success d-inline-flex align-items-center justify-content-center" style="min-width: 110px;">WhatsApp</a>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex flex-wrap justify-content-center align-items-center" style="gap: 8px;">
                                            <form action="{{ route('admin.agendamentos.pend', $appointment) }}" method="POST" class="mb-0 d-inline-block">
                                            @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">Pendente</button>
                                            </form>
                                            <form action="{{ route('admin.agendamentos.confirm', $appointment) }}" method="POST" class="mb-0 d-inline-block">
                                            @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Confirmar</button>
                                            </form>
                                            <form action="{{ route('admin.agendamentos.cancel', $appointment) }}" method="POST" class="mb-0 d-inline-block">
                                            @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Cancelado</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum agendamento disponível para confirmação.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
