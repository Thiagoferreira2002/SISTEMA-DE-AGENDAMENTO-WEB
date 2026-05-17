@extends('admin.layouts.master')
@section('content')
<style>
    .waitlist-stat-col {
        flex: 0 0 auto;
        width: auto;
        max-width: 100%;
    }

    .waitlist-summary-card {
        width: fit-content;
        min-width: 190px;
        max-width: 100%;
        margin-right: auto;
    }

    .waitlist-summary-card .card-icon {
        margin: 14px 14px 0;
    }

    .waitlist-summary-card .card-wrap {
        padding: 14px 14px 16px;
    }

    .waitlist-summary-card .card-header h4 {
        font-size: 11px;
        line-height: 1.25;
        white-space: normal;
        margin-bottom: 0;
    }

    .waitlist-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        justify-content: flex-start;
        width: auto;
        max-width: 100%;
        white-space: nowrap;
    }

    .waitlist-actions form {
        margin: 0;
    }

    .waitlist-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: auto;
        min-width: 0;
        max-width: 100%;
        min-height: 28px;
        padding: 4px 6px;
        font-size: 10px;
        line-height: 1.1;
        border-radius: 9px;
        white-space: nowrap;
    }

    .waitlist-actions-cell {
        min-width: 160px;
        white-space: nowrap;
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

    .waitlist-list-header {
        gap: 12px;
    }

    @media (max-width: 767.98px) {
        .waitlist-stat-col,
        .waitlist-summary-card {
            width: 100%;
            min-width: 0;
            max-width: 100%;
        }

        .waitlist-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
        }

        .waitlist-actions > *,
        .waitlist-actions form,
        .waitlist-actions .btn {
            width: 100%;
        }

        .waitlist-actions-cell {
            min-width: 220px !important;
            white-space: normal !important;
        }
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Lista de Espera</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.agendamentos.index') }}">Agendamentos</a></div>
            <div class="breadcrumb-item">Fila de Espera</div>
        </div>
    </div>

    <div class="section-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row mb-4">
            <div class="col-xl-auto col-lg-auto col-md-5 col-12 waitlist-stat-col">
                <div class="card card-statistic-1 mb-0 waitlist-summary-card">
                    <div class="card-icon bg-primary"><i class="fas fa-user-clock"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Pacientes na Fila</h4></div>
                        <div class="card-body">{{ $waitlist->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between waitlist-list-header">
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
                                    <td class="text-center align-middle action-button-cell waitlist-actions-cell table-mobile-full" data-label="Ação">
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
