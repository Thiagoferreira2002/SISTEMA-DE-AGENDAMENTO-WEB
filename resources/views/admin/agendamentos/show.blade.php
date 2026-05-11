@extends('admin.layouts.master')
@section('content')
<style>
    .appointment-page-shell {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .appointment-summary-card {
        border: 1px solid rgba(23, 111, 190, 0.08);
        border-radius: 24px;
        padding: 28px;
        background: linear-gradient(135deg, #f9fcff 0%, #eef6ff 52%, #f7fbff 100%);
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
    }

    .appointment-summary-grid {
        display: grid;
        grid-template-columns: minmax(260px, 320px) minmax(0, 1fr);
        gap: 22px;
        align-items: stretch;
    }

    .appointment-summary-patient,
    .appointment-detail-card,
    .appointment-history-card {
        border: 1px solid rgba(23, 111, 190, 0.08);
        border-radius: 22px;
        background: #ffffff;
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.06);
    }

    .appointment-summary-patient {
        padding: 22px;
        text-align: center;
    }

    .appointment-summary-avatar {
        width: 124px;
        height: 124px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.96);
        box-shadow: 0 18px 34px rgba(23, 111, 190, 0.16);
    }

    .appointment-summary-name {
        margin-top: 16px;
        font-size: 24px;
        font-weight: 800;
        color: #18354d;
    }

    .appointment-summary-caption {
        margin-top: 6px;
        color: #5d7791;
        font-size: 14px;
    }

    .appointment-summary-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .appointment-stat {
        padding: 18px 20px;
        min-height: 112px;
        border: 1px solid rgba(23, 111, 190, 0.08);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.84);
    }

    .appointment-stat-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #6b88a3;
    }

    .appointment-stat-value {
        margin-top: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #173752;
        word-break: break-word;
    }

    .appointment-detail-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .appointment-detail-card {
        padding: 24px;
    }

    .appointment-card-title {
        margin-bottom: 18px;
        font-size: 16px;
        font-weight: 800;
        color: #173752;
    }

    .appointment-field-list {
        display: grid;
        gap: 14px;
    }

    .appointment-field-item {
        padding-bottom: 12px;
        border-bottom: 1px solid #edf3f8;
    }

    .appointment-field-item:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .appointment-field-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #6b88a3;
        margin-bottom: 4px;
    }

    .appointment-field-value {
        color: #18354d;
        font-weight: 600;
    }

    html[data-theme="dark"] .appointment-summary-card {
        background: linear-gradient(135deg, rgba(19, 33, 49, 0.98) 0%, rgba(23, 44, 66, 0.96) 52%, rgba(18, 37, 49, 0.96) 100%);
        border-color: rgba(143, 197, 255, 0.12);
        box-shadow: 0 26px 50px rgba(2, 8, 15, 0.28);
    }

    html[data-theme="dark"] .appointment-summary-patient,
    html[data-theme="dark"] .appointment-detail-card,
    html[data-theme="dark"] .appointment-stat {
        background: rgba(22, 40, 59, 0.96);
        border-color: rgba(143, 197, 255, 0.12);
        box-shadow: 0 18px 32px rgba(2, 8, 15, 0.22);
    }

    html[data-theme="dark"] .appointment-summary-name,
    html[data-theme="dark"] .appointment-stat-value,
    html[data-theme="dark"] .appointment-card-title,
    html[data-theme="dark"] .appointment-field-value {
        color: #eef5fc;
    }

    html[data-theme="dark"] .appointment-summary-caption,
    html[data-theme="dark"] .appointment-stat-label,
    html[data-theme="dark"] .appointment-field-label {
        color: #a7c1d9;
    }

    html[data-theme="dark"] .appointment-field-item {
        border-bottom-color: rgba(143, 197, 255, 0.12);
    }

    @media (max-width: 767.98px) {
        .appointment-summary-grid,
        .appointment-summary-stats,
        .appointment-detail-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .appointment-summary-card,
        .appointment-detail-card {
            padding: 18px;
        }

        .appointment-summary-avatar {
            width: 104px;
            height: 104px;
        }
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Detalhes do Agendamento</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ $returnUrl }}">Agendamentos</a></div>
            <div class="breadcrumb-item">Detalhes</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Informações do Agendamento</h4>
                        <div class="card-header-action">
                            @if($canEditAppointment ?? true)
                                <a href="{{ route('admin.agendamentos.edit', ['agendamento' => $agendamento, 'return_to' => $returnUrl]) }}" class="btn btn-warning">Editar</a>
                            @endif
                            <a href="{{ $returnUrl }}" class="btn btn-secondary">Voltar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $startTimeValue = substr((string) $agendamento->horario, 0, 5);
                            $endTimeValue = optional($agendamento->data_agendamento)
                                ? $agendamento->data_agendamento->copy()->setTimeFromTimeString($startTimeValue)->addMinutes((int) ($agendamento->duracao_minutos ?: 30))->format('H:i')
                                : '-';
                            $appointmentCpf = preg_replace('/\D+/', '', (string) ($agendamento->cpf_exibicao ?? $agendamento->patient?->cpf ?? $agendamento->cpf ?? ''));
                            $appointmentCpf = strlen($appointmentCpf) === 11
                                ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $appointmentCpf)
                                : ($agendamento->cpf_exibicao ?? $agendamento->patient?->cpf ?? $agendamento->cpf ?? '-');
                            $appointmentStatusLabel = $agendamento->status === 'confirmado'
                                ? 'Confirmado'
                                : ($agendamento->status === 'pendente' ? 'Pendente' : ($agendamento->status === 'cancelado' ? 'Cancelado' : ucfirst((string) $agendamento->status)));
                            $appointmentStatusClass = $agendamento->status === 'confirmado'
                                ? 'success'
                                : ($agendamento->status === 'pendente' ? 'warning' : ($agendamento->status === 'cancelado' ? 'danger' : 'secondary'));
                        @endphp
                        <div class="appointment-page-shell">
                            <div class="appointment-summary-card">
                                <div class="appointment-summary-grid">
                                    <div class="appointment-summary-patient">
                                        <img src="{{ $agendamento->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto de {{ $agendamento->nome }}" class="appointment-summary-avatar">
                                        <div class="appointment-summary-name">{{ $agendamento->nome }}</div>
                                        <div class="appointment-summary-caption">Atendimento agendado na clínica</div>
                                    </div>
                                    <div class="appointment-summary-stats">
                                        <div class="appointment-stat">
                                            <div class="appointment-stat-label">Status</div>
                                            <div class="appointment-stat-value"><span class="badge badge-{{ $appointmentStatusClass }}">{{ $appointmentStatusLabel }}</span></div>
                                        </div>
                                        <div class="appointment-stat">
                                            <div class="appointment-stat-label">Serviço</div>
                                            <div class="appointment-stat-value">{{ $agendamento->servico ?: '-' }}</div>
                                        </div>
                                        <div class="appointment-stat">
                                            <div class="appointment-stat-label">Data do agendamento</div>
                                            <div class="appointment-stat-value">{{ $agendamento->data_agendamento->format('d/m/Y') }}</div>
                                        </div>
                                        <div class="appointment-stat">
                                            <div class="appointment-stat-label">Horário inicial</div>
                                            <div class="appointment-stat-value">{{ $startTimeValue }}</div>
                                        </div>
                                        <div class="appointment-stat">
                                            <div class="appointment-stat-label">Horário final</div>
                                            <div class="appointment-stat-value">{{ $endTimeValue }}</div>
                                        </div>
                                        <div class="appointment-stat">
                                            <div class="appointment-stat-label">Criado em</div>
                                            <div class="appointment-stat-value">{{ $agendamento->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="appointment-detail-grid">
                                <div class="appointment-detail-card">
                                    <div class="appointment-card-title">Dados do paciente</div>
                                    <div class="appointment-field-list">
                                        <div class="appointment-field-item"><div class="appointment-field-label">Nome</div><div class="appointment-field-value">{{ $agendamento->nome }}</div></div>
                                        <div class="appointment-field-item"><div class="appointment-field-label">CPF</div><div class="appointment-field-value">{{ $appointmentCpf ?: '-' }}</div></div>
                                        <div class="appointment-field-item"><div class="appointment-field-label">Telefone</div><div class="appointment-field-value">{{ $agendamento->telefone ?: '-' }}</div></div>
                                        <div class="appointment-field-item"><div class="appointment-field-label">E-mail</div><div class="appointment-field-value">{{ $agendamento->email ?: '-' }}</div></div>
                                    </div>
                                </div>

                                <div class="appointment-detail-card">
                                    <div class="appointment-card-title">Agenda e atendimento</div>
                                    <div class="appointment-field-list">
                                        <div class="appointment-field-item"><div class="appointment-field-label">Serviço</div><div class="appointment-field-value">{{ $agendamento->servico ?: '-' }}</div></div>
                                        <div class="appointment-field-item"><div class="appointment-field-label">Data</div><div class="appointment-field-value">{{ $agendamento->data_agendamento->format('d/m/Y') }}</div></div>
                                        <div class="appointment-field-item"><div class="appointment-field-label">Horário inicial</div><div class="appointment-field-value">{{ $startTimeValue }}</div></div>
                                        <div class="appointment-field-item"><div class="appointment-field-label">Horário final</div><div class="appointment-field-value">{{ $endTimeValue }}</div></div>
                                    </div>
                                </div>

                                <div class="appointment-detail-card">
                                    <div class="appointment-card-title">Observações</div>
                                    <div class="appointment-field-list">
                                        <div class="appointment-field-item"><div class="appointment-field-label">Status</div><div class="appointment-field-value">{{ $appointmentStatusLabel }}</div></div>
                                        <div class="appointment-field-item"><div class="appointment-field-label">Criado em</div><div class="appointment-field-value">{{ $agendamento->created_at->format('d/m/Y H:i') }}</div></div>
                                        <div class="appointment-field-item"><div class="appointment-field-label">Descrição</div><div class="appointment-field-value">{{ $agendamento->motivo_consulta ?: ($agendamento->descricao ?: '-') }}</div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
