@extends('admin.layouts.master')
@section('content')
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
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card card-statistic-1 mb-0">
                        <div class="card-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>Atendimentos em Atraso</h4></div>
                            <div class="card-body">{{ $totalPatientsInQueue }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(($baseRoute ?? '') === 'admin.doctor.pending-finalization' && !empty($hasDelayedAppointments))
            <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
                <div>
                    <strong>Existem atendimentos em atraso aguardando finalização.</strong>
                    <div class="small mt-1">Total encontrado: {{ $totalPatientsInQueue }}</div>
                </div>
                <a href="{{ route('admin.doctor.pending-finalization') }}" class="btn btn-warning">Visualizar todos os atrasos</a>
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
                        @if(($professionalOptions ?? collect())->isNotEmpty())
                            <div class="col-md-3">
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
                        <div class="col-md-{{ ($professionalOptions ?? collect())->isNotEmpty() ? '2' : '5' }} d-flex flex-wrap align-items-center" style="gap: 8px;">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                            <a href="{{ route($baseRoute ?? 'admin.doctor.queue') }}" class="btn btn-light">Limpar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>{{ $cardTitle ?? 'Pacientes na fila' }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Horário Final</th>
                                <th>Paciente</th>
                                <th>Profissional</th>
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
                                    <td>{{ $item->horario_final_exibicao ?: '-' }}</td>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->profissional_fila }}</td>
                                    <td>{{ $item->servico }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->status === 'confirmado' ? 'success' : ($item->status === 'cancelado' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($item->status ?? 'pendente') }}
                                        </span>
                                    </td>
                                    <td style="white-space: nowrap; min-width: 250px;">
                                        <div class="d-inline-flex flex-nowrap align-items-center" style="gap: 8px;">
                                            <a href="{{ route('admin.agendamentos.show', ['agendamento' => $item, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-info">Ver</a>
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
    </div>
</section>
@endsection
