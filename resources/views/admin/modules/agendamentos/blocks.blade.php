@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Bloqueio de Horários</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-ban"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Bloqueios Ativos</h4></div>
                        <div class="card-body">{{ $blockedSlots->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Agenda de indisponibilidade</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-6 col-12">
                        <div class="border rounded p-3 bg-light h-100">
                            <h6 class="mb-3">Novo bloqueio</h6>
                            <div class="row">
                                <div class="col-md-6"><div class="form-group"><label>Tipo de bloqueio</label><select class="form-control"><option>Almoço</option><option>Reunião</option><option>Congresso</option><option>Feriado</option></select></div></div>
                                <div class="col-md-6"><div class="form-group"><label>Recorrência</label><select class="form-control"><option>Não recorrente</option><option>Toda segunda-feira 08:00 às 09:00</option><option>Todos os dias úteis</option></select></div></div>
                            </div>
                            <div class="form-group mb-0"><label>Descrição do motivo</label><textarea class="form-control" rows="3" placeholder="Explique claramente o motivo do bloqueio"></textarea></div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="border rounded p-3 bg-light h-100">
                            <h6 class="mb-3">Como usar</h6>
                            <p class="mb-2">Use bloqueios recorrentes para almoço, reuniões fixas e indisponibilidades médicas periódicas.</p>
                            <p class="mb-0">Mantenha a descrição clara para que a recepção consiga explicar ao paciente por que aquele horário não está disponível.</p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Data</th>
                                <th>Período</th>
                                <th>Recorrência</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blockedSlots as $slot)
                                <tr>
                                    <td>{{ $slot['titulo'] }}</td>
                                    <td>{{ $slot['tipo'] }}</td>
                                    <td>{{ $slot['data'] }}</td>
                                    <td>{{ $slot['periodo'] }}</td>
                                    <td>{{ $slot['recorrencia'] }}</td>
                                    <td>{{ $slot['motivo'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
