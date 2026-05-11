@extends('admin.layouts.master')
@section('content')
<style>
    .patient-page-shell {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .patient-summary-card {
        border: 1px solid rgba(23, 111, 190, 0.08);
        border-radius: 24px;
        padding: 28px;
        background: linear-gradient(135deg, #f9fcff 0%, #eef6ff 52%, #f8fffd 100%);
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
    }

    .patient-summary-layout {
        display: grid;
        grid-template-columns: minmax(240px, 320px) minmax(0, 1fr);
        gap: 24px;
        align-items: center;
    }

    .patient-summary-profile {
        text-align: left;
        padding: 20px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(23, 111, 190, 0.08);
    }

    .patient-summary-avatar {
        width: 156px;
        height: 156px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.96);
        box-shadow: 0 20px 34px rgba(23, 111, 190, 0.16);
        display: block;
        margin-left: 0;
        margin-right: auto;
    }

    .patient-summary-name {
        margin-top: 18px;
        font-size: 26px;
        font-weight: 800;
        color: #18354d;
    }

    .patient-summary-role {
        margin-top: 6px;
        color: #5d7791;
        font-size: 14px;
    }

    .patient-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .patient-summary-stat,
    .patient-show-card,
    .patient-history-card {
        border: 1px solid rgba(23, 111, 190, 0.08);
        border-radius: 22px;
        background: #ffffff;
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.06);
    }

    .patient-summary-stat {
        padding: 18px 20px;
        min-height: 120px;
    }

    .patient-summary-stat-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #6b88a3;
    }

    .patient-summary-stat-value {
        margin-top: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #173752;
        word-break: break-word;
    }

    .patient-info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .patient-show-card {
        height: 100%;
        padding: 24px;
    }

    .patient-card-title {
        margin-bottom: 18px;
        font-size: 16px;
        font-weight: 800;
        color: #173752;
    }

    .patient-field-list {
        display: grid;
        gap: 14px;
    }

    .patient-field-item {
        padding-bottom: 12px;
        border-bottom: 1px solid #edf3f8;
    }

    .patient-field-item:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .patient-field-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #6b88a3;
        margin-bottom: 4px;
    }

    .patient-field-value {
        color: #18354d;
        font-weight: 600;
    }

    .patient-history-card {
        padding: 24px;
    }

    html[data-theme="dark"] .patient-summary-card {
        background: linear-gradient(135deg, rgba(19, 33, 49, 0.98) 0%, rgba(23, 44, 66, 0.96) 52%, rgba(18, 37, 34, 0.96) 100%);
        border-color: rgba(143, 197, 255, 0.12);
        box-shadow: 0 26px 50px rgba(2, 8, 15, 0.28);
    }

    html[data-theme="dark"] .patient-summary-profile,
    html[data-theme="dark"] .patient-summary-stat,
    html[data-theme="dark"] .patient-show-card,
    html[data-theme="dark"] .patient-history-card {
        background: rgba(22, 40, 59, 0.96);
        border-color: rgba(143, 197, 255, 0.12);
        box-shadow: 0 18px 32px rgba(2, 8, 15, 0.22);
    }

    html[data-theme="dark"] .patient-summary-name,
    html[data-theme="dark"] .patient-summary-stat-value,
    html[data-theme="dark"] .patient-card-title,
    html[data-theme="dark"] .patient-field-value {
        color: #eef5fc;
    }

    html[data-theme="dark"] .patient-summary-role,
    html[data-theme="dark"] .patient-summary-stat-label,
    html[data-theme="dark"] .patient-field-label {
        color: #a7c1d9;
    }

    html[data-theme="dark"] .patient-field-item {
        border-bottom-color: rgba(143, 197, 255, 0.12);
    }

    @media (max-width: 767.98px) {
        .patient-summary-layout,
        .patient-summary-grid,
        .patient-info-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .patient-show-card,
        .patient-history-card {
            padding: 18px !important;
        }

        .patient-summary-card {
            padding: 18px;
        }

        .patient-summary-avatar {
            width: 124px;
            height: 124px;
        }
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Detalhes do Paciente</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Pacientes</a></div>
            <div class="breadcrumb-item">Detalhes</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Informações do Paciente</h4>
                        <div class="card-header-action">
                            @if(! auth()->user()?->isClinicManager())
                                <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-warning">Editar</a>
                            @endif
                            <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">Voltar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="patient-page-shell">
                            <div class="patient-summary-card">
                                <div class="patient-summary-layout">
                                    <div class="patient-summary-profile">
                                        <img src="{{ $patient->foto_url }}" alt="Foto de {{ $patient->nome }}" class="patient-summary-avatar">
                                        <div class="patient-summary-name">{{ $patient->nome }}</div>
                                        <div class="patient-summary-role">Paciente cadastrado na clínica</div>
                                    </div>
                                    <div class="patient-summary-grid">
                                        <div class="patient-summary-stat">
                                            <div class="patient-summary-stat-label">Status cadastral</div>
                                            <div class="patient-summary-stat-value"><span class="badge badge-{{ $patient->cadastro_status_class }}">{{ $patient->cadastro_status_label }}</span></div>
                                        </div>
                                        <div class="patient-summary-stat">
                                            <div class="patient-summary-stat-label">Telefone principal</div>
                                            <div class="patient-summary-stat-value">{{ $patient->telefone ?: '-' }}</div>
                                        </div>
                                        <div class="patient-summary-stat">
                                            <div class="patient-summary-stat-label">E-mail</div>
                                            <div class="patient-summary-stat-value">{{ $patient->email ?: '-' }}</div>
                                        </div>
                                        <div class="patient-summary-stat">
                                            <div class="patient-summary-stat-label">CPF</div>
                                            <div class="patient-summary-stat-value">{{ $patient->cpf ?: '-' }}</div>
                                        </div>
                                        <div class="patient-summary-stat">
                                            <div class="patient-summary-stat-label">Pendências</div>
                                            <div class="patient-summary-stat-value">{{ empty($patient->cadastro_pendencias) ? 'Nenhuma' : count($patient->cadastro_pendencias) . ' item(ns)' }}</div>
                                        </div>
                                        <div class="patient-summary-stat">
                                            <div class="patient-summary-stat-label">Última atualização</div>
                                            <div class="patient-summary-stat-value">{{ $patient->updated_at?->format('d/m/Y H:i') ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="patient-info-grid">
                                <div class="patient-show-card">
                                    <div class="patient-card-title">Dados pessoais</div>
                                    <div class="patient-field-list">
                                        <div class="patient-field-item"><div class="patient-field-label">Nome completo</div><div class="patient-field-value">{{ $patient->nome }}</div></div>
                                        <div class="patient-field-item"><div class="patient-field-label">CPF</div><div class="patient-field-value">{{ $patient->cpf ?: '-' }}</div></div>
                                        <div class="patient-field-item"><div class="patient-field-label">Data de nascimento</div><div class="patient-field-value">{{ $patient->data_nascimento ? $patient->data_nascimento->format('d/m/Y') : '-' }}</div></div>
                                        <div class="patient-field-item"><div class="patient-field-label">Sexo</div><div class="patient-field-value">{{ $patient->sexo ?: '-' }}</div></div>
                                    </div>
                                </div>

                                <div class="patient-show-card">
                                    <div class="patient-card-title">Contato</div>
                                    <div class="patient-field-list">
                                        <div class="patient-field-item"><div class="patient-field-label">Celular</div><div class="patient-field-value">{{ $patient->telefone ?: '-' }}</div></div>
                                        <div class="patient-field-item"><div class="patient-field-label">E-mail</div><div class="patient-field-value">{{ $patient->email ?: '-' }}</div></div>
                                        <div class="patient-field-item"><div class="patient-field-label">Situação cadastral</div><div class="patient-field-value">{{ empty($patient->cadastro_pendencias) ? 'Cadastro completo' : 'Cadastro com pendências' }}</div></div>
                                    </div>
                                </div>

                                <div class="patient-show-card">
                                    <div class="patient-card-title">Endereço e observações</div>
                                    <div class="patient-field-list">
                                        <div class="patient-field-item"><div class="patient-field-label">Endereço</div><div class="patient-field-value">{{ $patient->endereco ?: '-' }}</div></div>
                                        <div class="patient-field-item"><div class="patient-field-label">Número e complemento</div><div class="patient-field-value">{{ trim(($patient->numero_endereco ?: '-') . ($patient->complemento ? ' • ' . $patient->complemento : '')) }}</div></div>
                                        <div class="patient-field-item"><div class="patient-field-label">CEP e bairro</div><div class="patient-field-value">{{ ($patient->cep ?: '-') . ' • ' . ($patient->bairro ?: '-') }}</div></div>
                                        <div class="patient-field-item"><div class="patient-field-label">Tipo de imóvel</div><div class="patient-field-value">{{ $patient->tipo_moradia ? ucfirst($patient->tipo_moradia) : '-' }}</div></div>
                                        <div class="patient-field-item"><div class="patient-field-label">Pendências cadastrais</div><div class="patient-field-value">{{ empty($patient->cadastro_pendencias) ? 'Nenhuma' : implode(', ', $patient->cadastro_pendencias) }}</div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="patient-history-card">
                                <h5 class="mb-3">Linha do Tempo de Consultas</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Profissional</th>
                                                <th>Serviço</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($history as $item)
                                                <tr>
                                                    <td>{{ $item->data_agendamento->format('d/m/Y') }} às {{ $item->horario }}</td>
                                                    <td>{{ $item->medico ?: 'Não informado' }}</td>
                                                    <td>{{ $item->servico }}</td>
                                                    <td>{{ ucfirst($item->status) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Sem consultas registradas.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if(method_exists($history, 'hasPages') && $history->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $history->links('vendor.pagination.patients-blocks') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
