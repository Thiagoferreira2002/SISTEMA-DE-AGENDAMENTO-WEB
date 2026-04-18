@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Lista de Espera</h1>
    </div>

    <div class="section-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4>Pacientes aguardando encaixe</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Agendamentos pendentes podem ser tratados como fila de oportunidade para encaixes e desistências.</p>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Serviço</th>
                                <th>Turno</th>
                                <th>Data limite</th>
                                <th>Prioridade</th>
                                <th>Data desejada</th>
                                <th>Horário</th>
                                <th>Contato</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($waitlist as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->servico }}</td>
                                    <td>{{ $item->preferencia_turno ?: 'Qualquer horário' }}</td>
                                    <td>{{ $item->data_limite_espera?->format('d/m/Y') ?: '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->prioridade >= 10 ? 'danger' : ($item->prioridade >= 5 ? 'warning' : 'secondary') }}">
                                            {{ $item->prioridade >= 10 ? 'Urgente' : ($item->prioridade >= 5 ? 'Preferencial' : 'Normal') }}
                                        </span>
                                    </td>
                                    <td>{{ $item->data_agendamento->format('d/m/Y') }}</td>
                                    <td>{{ $item->horario }}</td>
                                    <td>{{ $item->telefone }}</td>
                                    <td>
                                        <form action="{{ route('admin.agendamentos.promote', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Promover</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Nenhum paciente na lista de espera.</td>
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
