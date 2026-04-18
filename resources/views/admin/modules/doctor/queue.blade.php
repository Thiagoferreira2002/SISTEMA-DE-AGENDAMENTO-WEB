@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Fila de Espera</h1>
    </div>

    <div class="section-body">
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-user-clock"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Pacientes na fila</h4></div>
                        <div class="card-body">{{ $totalPatientsInQueue }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4>Filtros da fila</h4>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex flex-wrap" style="gap: 8px;">
                    <a href="{{ route('admin.doctor.queue', array_merge(request()->except('page', 'date'), ['period' => 'dia'])) }}" class="btn {{ $period === 'dia' && empty($selectedDate) ? 'btn-primary' : 'btn-outline-primary' }}">Dia</a>
                    <a href="{{ route('admin.doctor.queue', array_merge(request()->except('page', 'date'), ['period' => 'semana'])) }}" class="btn {{ $period === 'semana' && empty($selectedDate) ? 'btn-primary' : 'btn-outline-primary' }}">Semana</a>
                    <a href="{{ route('admin.doctor.queue', array_merge(request()->except('page', 'date'), ['period' => 'mes'])) }}" class="btn {{ $period === 'mes' && empty($selectedDate) ? 'btn-primary' : 'btn-outline-primary' }}">Mês</a>
                </div>

                <form method="GET" action="{{ route('admin.doctor.queue') }}">
                    <input type="hidden" name="period" value="{{ $period }}">

                    <div class="row align-items-end">
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
                        <div class="col-md-5 d-flex flex-wrap align-items-center" style="gap: 8px;">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                            <a href="{{ route('admin.doctor.queue') }}" class="btn btn-light">Limpar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Pacientes na fila</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Paciente</th>
                                <th>Profissional</th>
                                <th>Tempo de Espera</th>
                                <th>Chegada</th>
                                <th>Tipo</th>
                                <th>Serviço</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($queue as $item)
                                <tr>
                                    <td>{{ $item->data_agendamento->format('d/m/Y') }}</td>
                                    <td>{{ $item->horario }}</td>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->profissional_fila }}</td>
                                    <td>{{ $item->tempo_espera }} min</td>
                                    <td><span class="badge badge-{{ $item->status_chegada_classe }}">{{ $item->status_chegada }}</span></td>
                                    <td><i class="{{ $item->tipo_atendimento['icon'] }} mr-1"></i>{{ $item->tipo_atendimento['label'] }}</td>
                                    <td>{{ $item->servico }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->status === 'confirmado' ? 'success' : ($item->status === 'cancelado' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($item->status ?? 'pendente') }}
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.doctor.queue.finish', $item) }}">
                                            @csrf
                                            <input type="hidden" name="q" value="{{ $search }}">
                                            <input type="hidden" name="date" value="{{ $selectedDate }}">
                                            <input type="hidden" name="period" value="{{ $period }}">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deseja finalizar este atendimento?');">Finalizar atendimento</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nenhum paciente encontrado na fila para os filtros informados.</td>
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
