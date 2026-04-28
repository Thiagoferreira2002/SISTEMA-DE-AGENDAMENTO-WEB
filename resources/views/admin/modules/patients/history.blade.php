@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ $moduleTitle ?? 'Agendamentos Finalizados' }}</h1>
    </div>

    <div class="section-body">
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1 mb-0">
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
                    <div class="row align-items-end">
                        <div class="col-md-3">
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
                        @if(empty($authenticatedProfessional))
                            <div class="col-md-4">
                                <div class="form-group mb-md-0">
                                    <label for="history-search">Paciente por CPF ou nome</label>
                                    <input type="text" class="form-control" id="history-search" name="q" value="{{ $search ?? '' }}" placeholder="Digite o CPF ou o nome do paciente">
                                </div>
                            </div>
                            <div class="col-md-3">
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
                        <div class="col-md-2 d-flex flex-wrap align-items-center" style="gap: 8px;">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ $moduleRoute ?? route('admin.agendamentos.completed') }}" class="btn btn-light">Limpar</a>
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
                <div class="table-responsive">
                    <table class="table table-striped">
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
                                <tr>
                                    <td>{{ $item->data_agendamento->format('d/m/Y') }} às {{ $item->horario }}</td>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->medico_historico }}</td>
                                    <td>{{ $item->servico }}</td>
                                    <td>{{ ucfirst($item->status) }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 8px;">
                                            <a href="{{ route('admin.agendamentos.show', ['agendamento' => $item, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-info">Ver</a>
                                            @if(auth()->user()?->canMutateOutsideCadastrosBase())
                                                <a href="{{ route('admin.agendamentos.edit', ['agendamento' => $item, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-warning">Editar</a>
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
    </div>
</section>
@endsection
