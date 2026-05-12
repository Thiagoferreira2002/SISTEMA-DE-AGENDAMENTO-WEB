@extends('admin.layouts.master')
@section('content')
<style>
    .waitlist-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .waitlist-actions form {
        margin: 0;
    }

    .waitlist-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .waitlist-patient-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 180px;
    }

    .waitlist-patient-cell img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(23, 111, 190, 0.12);
        flex: 0 0 auto;
    }

    .waitlist-service-cell {
        min-width: 220px;
    }

    .waitlist-service-title,
    .waitlist-meta-line {
        display: block;
    }

    .waitlist-service-title {
        font-weight: 700;
        color: #18354d;
    }

    .waitlist-meta-line {
        margin-top: 4px;
        font-size: 12px;
        color: #6b88a3;
        white-space: normal;
    }

    .waitlist-end-cell {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 767.98px) {
        .waitlist-actions {
            flex-wrap: wrap;
            justify-content: center;
        }

        .waitlist-actions > *,
        .waitlist-actions form,
        .waitlist-actions .btn {
            width: 100%;
        }
    }
</style>
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
                    <table class="table table-striped table-mobile-cards">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Serviço</th>
                                <th>Profissional</th>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Final</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($waitlist as $item)
                                @php
                                    $endTime = $item->data_agendamento->copy()
                                        ->setTimeFromTimeString(substr((string) $item->horario, 0, 5))
                                        ->addMinutes((int) ($item->duracao_exibicao ?? $item->duracao_minutos ?? 30))
                                        ->format('H:i');
                                    $priorityLabel = $item->prioridade >= 10 ? 'Urgente' : ($item->prioridade >= 5 ? 'Preferencial' : 'Normal');
                                @endphp
                                <tr>
                                    <td class="table-mobile-full" data-label="Paciente">
                                        <div class="waitlist-patient-cell">
                                            <img src="{{ $item->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto de {{ $item->nome }}">
                                            <div>
                                                <span>{{ $item->nome }}</span>
                                                <span class="waitlist-meta-line">{{ $item->telefone ?: 'Contato não informado' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Serviço">
                                        <div class="waitlist-service-cell">
                                            <span class="waitlist-service-title">{{ $item->servico }}</span>
                                            <span class="waitlist-meta-line">Preferência: {{ $item->preferencia_turno ?: 'Qualquer horário' }}</span>
                                            <span class="waitlist-meta-line">Prioridade: {{ $priorityLabel }}</span>
                                            <span class="waitlist-meta-line">Limite: {{ $item->data_limite_espera?->format('d/m/Y') ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td data-label="Profissional">{{ $item->medico ?: 'Não informado' }}</td>
                                    <td data-label="Data">{{ $item->data_agendamento->format('d/m/Y') }}</td>
                                    <td data-label="Horário">{{ $item->horario }}</td>
                                    <td data-label="Final">
                                        <div class="waitlist-end-cell">
                                            <span>{{ $endTime }}</span>
                                            <span class="badge badge-{{ $item->prioridade >= 10 ? 'danger' : ($item->prioridade >= 5 ? 'warning' : 'secondary') }}">{{ $priorityLabel }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle action-button-cell table-mobile-full" data-label="Ação">
                                        <div class="waitlist-actions action-button-group">
                                            <form action="{{ route('admin.agendamentos.promote', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Promover</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nenhum paciente na lista de espera.</td>
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
