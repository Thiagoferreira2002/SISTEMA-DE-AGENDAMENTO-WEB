@extends('admin.layouts.master')
@section('content')
<style>
    .appointment-planner-shell,
    .appointment-day-overview,
    .appointment-overview-panel {
        border-color: #d2dbe6 !important;
    }

    .appointment-planner-shell {
        box-shadow: inset 0 0 0 1px #d2dbe6, 0 12px 28px rgba(18, 58, 99, 0.06);
    }

    html[data-theme="dark"] .appointment-planner-shell,
    html[data-theme="dark"] .appointment-day-overview,
    html[data-theme="dark"] .appointment-overview-panel,
    html[data-theme="dark"] .appointment-planner-shell .alert-light,
    html[data-theme="dark"] .appointment-planner-shell .border.rounded {
        border-color: #000000 !important;
    }

    html[data-theme="dark"] .appointment-planner-shell {
        box-shadow: inset 0 0 0 1px #000000, 0 18px 36px rgba(2, 8, 15, 0.32);
    }

    .appointment-planner-shell {
        border: 1px solid rgba(30, 144, 255, 0.14);
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(244,249,255,.94));
        box-shadow: 0 12px 28px rgba(18, 58, 99, 0.06);
    }

    html[data-theme="dark"] .appointment-planner-shell {
        border-color: rgba(143, 197, 255, 0.18);
        background: linear-gradient(180deg, rgba(22,40,59,.98), rgba(19,33,49,.98));
        box-shadow: 0 18px 36px rgba(2, 8, 15, 0.32);
    }

    .appointment-planner-shell .planner-select,
    .appointment-planner-shell .planner-date {
        min-height: 48px;
        font-weight: 600;
        border-radius: 16px;
    }

    .appointment-planner-shell .planner-date {
        border: 1px solid rgba(30, 144, 255, 0.18);
        background: linear-gradient(180deg, #ffffff 0%, #f4f9ff 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.6);
    }

    html[data-theme="dark"] .appointment-planner-shell .planner-date,
    html[data-theme="dark"] .appointment-planner-shell select[data-native-select="true"] {
        border-color: rgba(143, 197, 255, 0.18);
        background: linear-gradient(180deg, #24415f 0%, #17304a 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.03), 0 10px 24px rgba(0, 0, 0, .16);
    }

    html[data-theme="dark"] .appointment-planner-shell .planner-date:hover,
    html[data-theme="dark"] .appointment-planner-shell select[data-native-select="true"]:hover {
        border-color: rgba(158, 208, 255, 0.3);
        background: linear-gradient(180deg, #2a4969 0%, #1c3956 100%);
    }

    html[data-theme="dark"] .appointment-planner-shell .planner-date:focus,
    html[data-theme="dark"] .appointment-planner-shell select[data-native-select="true"]:focus {
        border-color: rgba(177, 219, 255, 0.36);
        background: linear-gradient(180deg, #315477 0%, #21405f 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.04), 0 0 0 3px rgba(143, 197, 255, 0.18), 0 12px 28px rgba(0, 0, 0, .18);
    }

    html[data-theme="dark"] .appointment-planner-shell select[data-native-select="true"] {
        padding-right: 54px;
        background-image: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0) 42%), linear-gradient(180deg, #24415f 0%, #17304a 100%), linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.08)), linear-gradient(45deg, transparent 50%, #b8dcff 50%), linear-gradient(135deg, #b8dcff 50%, transparent 50%) !important;
        background-position: 0 0, 0 0, calc(100% - 42px) 50%, calc(100% - 18px) calc(50% - 2px), calc(100% - 12px) calc(50% - 2px) !important;
        background-size: 100% 100%, 100% 100%, 1px 18px, 6px 6px, 6px 6px !important;
        background-repeat: no-repeat !important;
    }

    html[data-theme="dark"] .appointment-planner-shell select[data-native-select="true"]:hover {
        background-image: linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0) 42%), linear-gradient(180deg, #2a4969 0%, #1c3956 100%), linear-gradient(180deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.1)), linear-gradient(45deg, transparent 50%, #d4e9ff 50%), linear-gradient(135deg, #d4e9ff 50%, transparent 50%) !important;
    }

    html[data-theme="dark"] .appointment-planner-shell select[data-native-select="true"]:focus {
        background-image: linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0) 42%), linear-gradient(180deg, #315477 0%, #21405f 100%), linear-gradient(180deg, rgba(233, 239, 245, 0.18), rgba(233, 239, 245, 0.18)), linear-gradient(45deg, transparent 50%, #eef2f7 50%), linear-gradient(135deg, #eef2f7 50%, transparent 50%) !important;
    }

    .planner-field-feedback {
        display: none;
        margin-top: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .planner-field-feedback.is-visible {
        display: block;
    }

    .planner-field-feedback.is-danger {
        color: #c0392b;
    }

    .planner-field-feedback.is-success {
        color: #1f7a3d;
    }

    .appointment-planner-shell .planner-select option:disabled {
        color: #6c757d;
        background: #eef1f4;
    }

    .appointment-day-overview {
        border: 1px solid rgba(30, 144, 255, 0.18);
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(248, 251, 255, 0.98), rgba(237, 245, 255, 0.96));
        padding: 20px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.75);
    }

    html[data-theme="dark"] .appointment-day-overview,
    html[data-theme="dark"] .appointment-planner-shell .alert-light,
    html[data-theme="dark"] .appointment-planner-shell .border.rounded {
        border-color: rgba(143, 197, 255, 0.16) !important;
        background: rgba(19, 33, 49, 0.92) !important;
        color: var(--text-primary) !important;
    }

    .appointment-day-overview-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #4d6d8a;
        margin-bottom: 16px;
    }

    html[data-theme="dark"] .appointment-day-overview-title {
        color: #a9c5df;
    }

    .appointment-day-overview-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .appointment-overview-panel {
        min-width: 0;
        padding: 14px;
        border-radius: 16px;
        background: rgba(255,255,255,.78);
        border: 1px solid rgba(23, 111, 190, 0.12);
    }

    .appointment-overview-heading {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        font-size: 12px;
        font-weight: 700;
        color: #264e73;
    }

    .appointment-overview-heading i {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(23, 111, 190, 0.1);
    }

    .appointment-overview-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .appointment-overview-empty {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #5f7388;
    }

    .appointment-chip-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .appointment-chip {
        display: inline-flex;
        align-items: center;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .appointment-chip.available { background: rgba(40, 167, 69, 0.12); color: #1b6f35; }
    .appointment-chip.occupied { background: rgba(220, 53, 69, 0.12); color: #a12839; }
    .appointment-chip.interval { background: rgba(108, 117, 125, 0.16); color: #495057; }
    .appointment-chip.neutral { background: rgba(30, 144, 255, 0.12); color: #155a9d; }

    html[data-theme="dark"] .appointment-chip.available { background: rgba(64, 201, 117, 0.16); color: #92e1ae; }
    html[data-theme="dark"] .appointment-chip.occupied { background: rgba(255, 107, 129, 0.16); color: #ffb4c1; }
    html[data-theme="dark"] .appointment-chip.interval { background: rgba(173, 181, 189, 0.12); color: #d3dde6; }
    html[data-theme="dark"] .appointment-chip.neutral { background: rgba(118, 187, 255, 0.16); color: #bfe0ff; }
    html[data-theme="dark"] .appointment-overview-panel { background: rgba(13, 25, 39, 0.55); border-color: rgba(143, 197, 255, 0.14); }
    html[data-theme="dark"] .appointment-overview-heading { color: #d7eaff; }
    html[data-theme="dark"] .appointment-overview-heading i { background: rgba(143, 197, 255, 0.14); }
    html[data-theme="dark"] .appointment-overview-empty { color: #a9c5df; }

    .appointment-planner-shell select[data-native-select="true"] {
        appearance: auto;
        -webkit-appearance: menulist;
        -moz-appearance: menulist;
        width: 100%;
        min-height: 48px;
        border: 1px solid rgba(30, 144, 255, 0.18);
        border-radius: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #f4f9ff 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.6);
    }

    html[data-theme="dark"] .appointment-planner-shell .text-muted,
    html[data-theme="dark"] .appointment-planner-shell small,
    html[data-theme="dark"] .appointment-planner-shell .alert-light small {
        color: var(--text-secondary) !important;
    }

    #telefone,
    #p_telefone {
        letter-spacing: 0.04em;
        font-variant-numeric: tabular-nums;
    }

    .patient-shortcuts {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .appointment-planner-shell .btn-form-search {
        min-height: 28px;
        padding: 0.24rem 0.62rem;
        font-size: 0.78rem;
        width: auto;
        min-width: 92px;
    }

    .appointment-planner-shell .btn-form-action {
        min-width: 96px;
        min-height: 34px;
        padding: 0.34rem 0.74rem;
        font-size: 0.8rem;
    }

    .patient-create-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .patient-create-actions .btn {
        width: auto;
        min-width: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    @media (max-width: 991.98px) {
        .patient-create-name-col {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .patient-create-cpf-col,
        .patient-create-sex-col,
        .patient-create-birth-col {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 1024px) {
        .section-body form .form-action-bar,
        .appointment-planner-shell .form-action-bar {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .section-body form .form-action-bar > *,
        .appointment-planner-shell .form-action-bar > *,
        .appointment-planner-shell .btn-form-action {
            flex: 0 0 auto !important;
            width: auto !important;
            min-width: 0;
            max-width: 100%;
        }
    }

    @media (max-width: 767.98px) {
        .appointment-planner-shell {
            padding: 16px !important;
        }

        .appointment-planner-shell .alert-light,
        .appointment-planner-shell .border.rounded {
            padding: 16px !important;
        }

        .appointment-planner-shell .nav-tabs .nav-link,
        .appointment-planner-shell .btn-form-search {
            width: 100%;
        }

        .appointment-planner-shell .form-action-bar {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .appointment-planner-shell .form-action-bar > *,
        .appointment-planner-shell .btn-form-action {
            width: auto !important;
            min-width: 0;
            max-width: 100%;
            flex: 0 0 auto;
        }

        .patient-create-actions .btn {
            width: auto;
            padding-left: 14px !important;
            padding-right: 14px !important;
        }

        .patient-create-cpf-col,
        .patient-create-sex-col,
        .patient-create-birth-col {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .appointment-planner-shell .form-action-bar {
            width: 100%;
        }

        #patient-search-button {
            width: 100%;
        }

        .patient-shortcuts > * {
            width: 100%;
        }

        .appointment-day-overview {
            padding: 14px;
        }

        .appointment-day-overview-grid {
            grid-template-columns: 1fr;
        }

        .appointment-chip {
            width: 100%;
            justify-content: center;
            text-align: center;
        }
    }
</style>
<section class="section">
    @php
        $activeTab = old('tab', request('tab', 'agendamento'));
        $isPatientForm = $activeTab === 'paciente';
        $minimumAppointmentDate = now();
        $clinicClosingTime = $clinicHours->closing_time ?? null;

        if (!empty($clinicClosingTime)) {
            $clinicClosingDateTime = now()->copy()->setTimeFromTimeString($clinicClosingTime);

            if (now()->greaterThanOrEqualTo($clinicClosingDateTime)) {
                $minimumAppointmentDate = $minimumAppointmentDate->copy()->addDay();
            }
        }
    @endphp

    <div class="section-header">
        <h1>{{ $isPatientForm ? 'Cadastro de Pacientes' : 'Novo Agendamento' }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ $returnUrl }}">Agendamentos</a></div>
            <div class="breadcrumb-item">{{ $isPatientForm ? 'Cadastro de Pacientes' : 'Novo' }}</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $isPatientForm ? 'Cadastro de Pacientes' : 'Novo Agendamento' }}</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(!empty($setupWarning))
                            <div class="alert alert-warning">{{ $setupWarning }}</div>
                        @endif
                        @if(! $isPatientForm && $errors->any())
                            <div class="alert alert-danger">
                                <strong>Não foi possível salvar o agendamento.</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="border rounded p-3 appointment-planner-shell">
                            @if(! $isPatientForm)
                                <form action="{{ route('admin.agendamentos.store') }}" method="POST" novalidate>
                                    @csrf
                                    <input type="hidden" name="tab" value="agendamento">
                                    <input type="hidden" name="return_to" value="{{ $returnUrl }}">
                                    <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $preselectedPatient?->id) }}">
                                    <input type="hidden" name="duracao_minutos" id="duracao_minutos" value="{{ old('duracao_minutos', 30) }}">
                                    <input type="hidden" name="servico" id="servico_nome" value="{{ old('servico') }}">
                                    <input type="hidden" name="medico" id="medico_nome" value="{{ old('medico', $lockedProfessional->nome ?? '') }}">

                                    <div class="alert alert-light border mb-4">
                                        <strong class="d-block mb-2">Busca de paciente por CPF</strong>
                                        <div class="row align-items-end">
                                            <div class="col-xl-5 col-lg-6 col-md-7 col-12">
                                                <input type="text" class="form-control" id="patient_search" placeholder="Digite o CPF do paciente" autocomplete="off" inputmode="numeric" value="{{ old('patient_search', $preselectedPatient?->cpf) }}">
                                            </div>
                                            <div class="col-xl-2 col-lg-2 col-md-4 col-12 mt-2 mt-md-0">
                                                <button type="button" class="btn btn-primary btn-form-search" id="patient-search-button">Buscar CPF</button>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-2">Informe o CPF para localizar um paciente já cadastrado. Se o CPF não existir, o agendamento não poderá ser salvo até o paciente ser cadastrado.</small>
                                        <div id="patient-search-feedback" class="small mt-2"></div>
                                        @error('patient_id')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                        <div class="mt-3 patient-shortcuts">
                                            <a href="{{ route('admin.agendamentos.create', ['tab' => 'paciente', 'return_to' => $returnUrl]) }}" class="btn btn-outline-secondary btn-sm">Cadastrar novo paciente</a>
                                            <a href="{{ route('admin.patients.index') }}" class="btn btn-light btn-sm">Visualizar pacientes cadastrados</a>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group">
                                                <label for="nome">Nome *</label>
                                                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $preselectedPatient?->nome) }}" required>
                                                @error('nome')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email *</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $preselectedPatient?->email) }}" required>
                                                @error('email')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-6">
                                            <div class="form-group">
                                                <label for="telefone">Telefone *</label>
                                                <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone', $preselectedPatient?->telefone) }}" placeholder="(11) 99999-9999" maxlength="15" inputmode="numeric" required>
                                                @error('telefone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-6">
                                            <div class="form-group">
                                                <label for="medico">Profissional *</label>
                                                @if($lockedProfessional)
                                                    <input type="hidden" id="professional_id" name="professional_id" value="{{ old('professional_id', $lockedProfessional->id) }}">
                                                    <input type="text" class="form-control" value="{{ $lockedProfessional->nome }}" readonly>
                                                @else
                                                    <select class="form-control planner-select @error('professional_id') is-invalid @enderror" id="professional_id" name="professional_id" data-native-select="true">
                                                        <option value="">Selecione</option>
                                                        @foreach($professionalOptions as $professional)
                                                            <option value="{{ $professional['id'] }}" data-name="{{ $professional['nome'] }}" data-color="{{ $professional['cor'] }}" {{ (string) old('professional_id') === (string) $professional['id'] ? 'selected' : '' }}>{{ $professional['nome'] }} - {{ $professional['especialidade'] }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                @error('professional_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                @error('medico')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-6">
                                            <div class="form-group">
                                                <label for="procedure_id">Procedimento *</label>
                                                <select class="form-control planner-select @error('procedure_id') is-invalid @enderror" id="procedure_id" name="procedure_id" data-native-select="true">
                                                    <option value="">Selecione um profissional</option>
                                                </select>
                                                @error('procedure_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                @error('servico')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">Duração: <span id="duracao_preview">{{ old('duracao_minutos', 30) }} min</span></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-6">
                                            <div class="form-group">
                                                <label for="data_agendamento">Data *</label>
                                                <input type="date" class="form-control planner-date @error('data_agendamento') is-invalid @enderror" id="data_agendamento" name="data_agendamento" value="{{ old('data_agendamento') }}" min="{{ $minimumAppointmentDate->format('Y-m-d') }}" required>
                                                <div id="date-validation-feedback" class="planner-field-feedback"></div>
                                                <div id="professional-availability-feedback" class="small mt-2" style="display:none;"></div>
                                                @error('data_agendamento')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-6">
                                            <div class="form-group">
                                                <label for="horario">Horário inicial *</label>
                                                <select class="form-control planner-select @error('horario') is-invalid @enderror" id="horario" name="horario" required data-native-select="true">
                                                    <option value="">Selecione</option>
                                                </select>
                                                <div id="time-validation-feedback" class="planner-field-feedback"></div>
                                                @error('horario')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-6">
                                            <div class="form-group">
                                                <label for="horario_final">Horário final *</label>
                                                <select class="form-control planner-select @error('horario_final') is-invalid @enderror" id="horario_final" name="horario_final" required data-native-select="true">
                                                    <option value="">Selecione</option>
                                                </select>
                                                @error('horario_final')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div id="appointment-day-overview" class="appointment-day-overview mt-2" style="display:none;"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-8 col-12">
                                            <div class="form-group">
                                                <label for="motivo_consulta">Motivo do Agendamento</label>
                                                <textarea class="form-control @error('motivo_consulta') is-invalid @enderror" id="motivo_consulta" name="motivo_consulta" rows="3" placeholder="Descreva o motivo do agendamento">{{ old('motivo_consulta') }}</textarea>
                                                @error('motivo_consulta')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center form-action-bar" style="gap: 10px;">
                                        <button type="submit" class="btn btn-primary btn-form-action">Salvar</button>
                                        <a href="{{ $returnUrl }}" class="btn btn-secondary btn-form-action">Cancelar</a>
                                    </div>
                                </form>
                            @else
                                <form action="{{ route('admin.patients.store') }}" method="POST" id="patient-create-form" enctype="multipart/form-data" autocomplete="off" data-patient-live-check="true" data-patient-duplicate-url="{{ route('admin.patients.duplicate-check') }}">
                                    @csrf
                                    <input type="hidden" name="draft_key" value="admin.patients.create.inline">
                                    <input type="hidden" name="origem" value="agendamento">
                                    <input type="hidden" name="tab" value="paciente">
                                    <input type="hidden" name="return_to" value="{{ $returnUrl }}">
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            O cadastro do paciente nao foi salvo. Verifique os campos destacados abaixo.
                                        </div>
                                    @endif

                                    <div class="border rounded p-3 mb-4">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 10px;">
                                            <div>
                                                <h5 class="mb-1">Qualidade do cadastro</h5>
                                                <p class="text-muted mb-0 small">Preencha os campos recomendados para reduzir retrabalho no atendimento.</p>
                                            </div>
                                            <span class="small text-muted" data-patient-progress-text>0 de 0 campos preenchidos</span>
                                        </div>
                                        <div class="progress mt-3" style="height: 10px; border-radius: 999px; overflow: hidden;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-patient-progress-bar></div>
                                        </div>
                                        <p class="small text-muted mt-2 mb-0" data-patient-missing-fields>Carregando campos do cadastro.</p>
                                    </div>

                                    <div class="border rounded p-3 mb-4">
                                        <h5 class="mb-3">Foto do paciente</h5>
                                        <div class="row align-items-center">
                                            <div class="col-lg-3 col-md-4 mb-3 mb-md-0 text-center">
                                                <img src="{{ asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Prévia da foto do paciente" class="img-fluid rounded-circle border" style="width: 108px; height: 108px; object-fit: cover;" data-patient-photo-preview data-default-src="{{ asset('backend/assets/img/avatar/avatar-1.png') }}">
                                            </div>
                                            <div class="col-lg-6 col-md-8">
                                                <div class="form-group mb-0">
                                                    <label for="p_foto">Selecionar foto</label>
                                                    <input type="file" class="form-control-file" id="p_foto" name="foto" accept=".jpg,.jpeg,.png,.webp,image/*" data-patient-photo-input>
                                                    <small class="text-muted d-block mt-2">Opcional. Use JPG, PNG ou WEBP com até 2 MB.</small>
                                                    @error('foto')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border rounded p-3 mb-4">
                                        <h5 class="mb-3">Dados Pessoais</h5>
                                        <div class="row">
                                            <div class="col-lg-8 col-md-12 patient-create-name-col"><div class="form-group"><label for="p_nome">Nome completo *</label><input type="text" class="form-control" id="p_nome" name="nome" value="{{ old('nome') }}" autocomplete="off" required>@error('nome')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-lg-2 col-md-6 patient-create-cpf-col"><div class="form-group"><label for="p_cpf">CPF</label><input type="text" class="form-control" id="p_cpf" name="cpf" value="{{ old('cpf') }}" autocomplete="off">@error('cpf')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-lg-2 col-md-6 patient-create-sex-col"><div class="form-group"><label for="p_sexo">Sexo</label><select class="form-control" id="p_sexo" name="sexo"><option value="">Selecione</option><option value="feminino" {{ old('sexo') === 'feminino' ? 'selected' : '' }}>Feminino</option><option value="masculino" {{ old('sexo') === 'masculino' ? 'selected' : '' }}>Masculino</option><option value="outro" {{ old('sexo') === 'outro' ? 'selected' : '' }}>Outro</option></select></div></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6 patient-create-birth-col"><div class="form-group"><label for="p_data_nascimento">Data de Nascimento</label><input type="date" class="form-control" id="p_data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" autocomplete="off" max="{{ now()->format('Y-m-d') }}">@error('data_nascimento')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                        </div>
                                    </div>

                                    <div class="border rounded p-3 mb-4">
                                        <h5 class="mb-3">Contato</h5>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6"><div class="form-group"><label for="p_telefone">Celular (WhatsApp) *</label><input type="text" class="form-control" id="p_telefone" name="telefone" value="{{ old('telefone') }}" autocomplete="off" required>@error('telefone')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-lg-4 col-md-6"><div class="form-group"><label for="p_email">E-mail *</label><input type="email" class="form-control" id="p_email" name="email" value="{{ old('email') }}" autocomplete="off" required>@error('email')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                        </div>
                                    </div>

                                    <div class="border rounded p-3 mb-4">
                                        <h5 class="mb-3">Endereço</h5>
                                        <div class="row">
                                            <div class="col-md-4"><div class="form-group"><label for="p_endereco">Endereço</label><input type="text" class="form-control" id="p_endereco" name="endereco" value="{{ old('endereco') }}" autocomplete="off">@error('endereco')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-lg-2 col-md-2"><div class="form-group"><label for="p_numero_endereco">Número</label><input type="text" class="form-control" id="p_numero_endereco" name="numero_endereco" value="{{ old('numero_endereco') }}" autocomplete="off">@error('numero_endereco')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-lg-2 col-md-3"><div class="form-group"><label for="p_cep">CEP</label><input type="text" class="form-control" id="p_cep" name="cep" value="{{ old('cep') }}" autocomplete="off">@error('cep')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-3"><div class="form-group"><label for="p_bairro">Bairro</label><input type="text" class="form-control" id="p_bairro" name="bairro" value="{{ old('bairro') }}" autocomplete="off">@error('bairro')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4"><div class="form-group"><label for="p_tipo_moradia">Tipo de imóvel</label><select class="form-control" id="p_tipo_moradia" name="tipo_moradia"><option value="">Selecione</option><option value="casa" {{ old('tipo_moradia') === 'casa' ? 'selected' : '' }}>Casa</option><option value="apartamento" {{ old('tipo_moradia') === 'apartamento' ? 'selected' : '' }}>Apartamento</option><option value="condominio" {{ old('tipo_moradia') === 'condominio' ? 'selected' : '' }}>Condomínio</option><option value="sobrado" {{ old('tipo_moradia') === 'sobrado' ? 'selected' : '' }}>Sobrado</option><option value="comercial" {{ old('tipo_moradia') === 'comercial' ? 'selected' : '' }}>Comercial</option><option value="rural" {{ old('tipo_moradia') === 'rural' ? 'selected' : '' }}>Rural</option><option value="outro" {{ old('tipo_moradia') === 'outro' ? 'selected' : '' }}>Outro</option></select>@error('tipo_moradia')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-5"><div class="form-group mb-0"><label for="p_complemento">Complemento</label><input type="text" class="form-control" id="p_complemento" name="complemento" value="{{ old('complemento') }}" autocomplete="off">@error('complemento')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                        </div>
                                    </div>
                                    <div class="patient-create-actions">
                                        <button type="submit" class="btn btn-success btn-form-action">Cadastrar Paciente</button>
                                        <a href="{{ $returnUrl }}" class="btn btn-secondary btn-form-action">Cancelar</a>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var patients = @json($patients ?? []);
        var procedures = @json($procedureOptions ?? []);
        var professionals = @json($professionalOptions ?? []);
        var patientSearch = document.getElementById('patient_search');
        var patientSearchButton = document.getElementById('patient-search-button');
        var patientSearchFeedback = document.getElementById('patient-search-feedback');
        var procedureSelect = document.getElementById('procedure_id');
        var professionalSelect = document.getElementById('professional_id');
        var durationInput = document.getElementById('duracao_minutos');
        var durationPreview = document.getElementById('duracao_preview');
        var serviceHidden = document.getElementById('servico_nome');
        var professionalHidden = document.getElementById('medico_nome');
        var appointmentDateInput = document.getElementById('data_agendamento');
        var startTimeInput = document.getElementById('horario');
        var endTimeInput = document.getElementById('horario_final');
        var selectedProcedureId = '{{ old('procedure_id') }}';
        var clinicHours = @json($clinicHours ?? null);
        var occupiedAppointments = @json($occupiedAppointments ?? []);
        var initialStartTime = '{{ old('horario') }}';
        var initialEndTime = '{{ old('horario_final') }}';
        var availabilityFeedback = document.getElementById('professional-availability-feedback');
        var dateValidationFeedback = document.getElementById('date-validation-feedback');
        var timeValidationFeedback = document.getElementById('time-validation-feedback');
        var appointmentDayOverview = document.getElementById('appointment-day-overview');
        var endTimeGuidance = document.getElementById('end-time-guidance');
        var appointmentForm = document.querySelector('form[action="{{ route('admin.agendamentos.store') }}"]');
        var submitButton = appointmentForm ? appointmentForm.querySelector('button[type="submit"]') : null;
        var appointmentLockedFields = appointmentForm ? Array.from(appointmentForm.querySelectorAll('#nome, #email, #telefone, #professional_id, #procedure_id, #data_agendamento, #horario, #horario_final, #motivo_consulta')) : [];
        var timeSlotStep = 5;
        var currentEndTimeGuidanceMessage = 'Selecione um horário de término válido.';

        function onlyDigits(value) {
            return String(value || '').replace(/\D/g, '');
        }

        function setPatientSearchFeedback(message, type) {
            if (!patientSearchFeedback) {
                return;
            }

            if (!message) {
                patientSearchFeedback.className = 'small mt-2';
                patientSearchFeedback.textContent = '';
                return;
            }

            patientSearchFeedback.className = 'small mt-2 text-' + (type || 'muted');
            patientSearchFeedback.textContent = message;
        }

        function refreshEnhancedSelect(selectElement) {
            if (!selectElement || selectElement.dataset.nativeSelect === 'true' || !window.jQuery || !jQuery.fn || typeof jQuery.fn.selectric !== 'function') {
                return;
            }

            var wrappedSelect = jQuery(selectElement);

            if (wrappedSelect.data('selectric')) {
                wrappedSelect.selectric('destroy');
            }

            wrappedSelect.selectric();
        }

        function fillPatientFields(patient) {
            document.getElementById('patient_id').value = patient.id || '';
            document.getElementById('nome').value = patient.nome || '';
            document.getElementById('email').value = patient.email || '';
            document.getElementById('telefone').value = patient.telefone || '';

            if (patient && patient.id) {
                setAppointmentFieldsLocked(false);
            }
        }

        function clearPatientFields() {
            fillPatientFields({ id: '', nome: '', email: '', telefone: '' });
            setAppointmentFieldsLocked(true);
        }

        function setAppointmentFieldsLocked(locked) {
            appointmentLockedFields.forEach(function(field) {
                if (!field) {
                    return;
                }

                field.disabled = locked;
            });

            if (locked && appointmentDayOverview) {
                appointmentDayOverview.style.display = 'none';
                appointmentDayOverview.innerHTML = '';
            }
        }

        function findPatientByCpf() {
            var cpfDigits = onlyDigits(patientSearch ? patientSearch.value : '');

            if (cpfDigits.length !== 11) {
                clearPatientFields();
                setPatientSearchFeedback('Informe um CPF com 11 dígitos para buscar um paciente cadastrado.', 'warning');
                if (submitButton) {
                    submitButton.disabled = true;
                }
                updateSubmitState();
                return null;
            }

            var patient = patients.find(function(item) {
                return onlyDigits(item.cpf) === cpfDigits;
            }) || null;

            if (!patient) {
                clearPatientFields();
                setPatientSearchFeedback('Nenhum paciente foi encontrado para este CPF. É necessário cadastrar o paciente antes de fazer o agendamento.', 'danger');
                if (submitButton) {
                    submitButton.disabled = true;
                }
                updateSubmitState();
                return null;
            }

            fillPatientFields(patient);
            setAppointmentFieldsLocked(false);
            setPatientSearchFeedback('Paciente localizado: ' + (patient.nome || 'Paciente sem nome') + '.', 'success');
            updateSubmitState();
            return patient;
        }

        function renderProcedureOptions(selectedProfessionalId) {
            if (!procedureSelect) return;

            var previousValue = selectedProcedureId || procedureSelect.value || '';
            var filteredProcedures = procedures.filter(function(procedure) {
                if (!selectedProfessionalId) {
                    return false;
                }

                return String(procedure.professional_id || '') === String(selectedProfessionalId);
            });

            procedureSelect.innerHTML = '';

            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = selectedProfessionalId
                ? (filteredProcedures.length ? 'Selecione' : 'Nenhum procedimento cadastrado para este profissional')
                : 'Selecione um profissional';
            procedureSelect.appendChild(placeholder);

            filteredProcedures.forEach(function(procedure) {
                var option = document.createElement('option');
                option.value = procedure.id || '';
                option.textContent = procedure.nome || '';
                option.setAttribute('data-name', procedure.nome || '');
                option.setAttribute('data-duration', procedure.duracao || '30');
                option.setAttribute('data-price', procedure.valor || '0,00');
                option.setAttribute('data-tuss', procedure.codigo_tuss || '');

                if (String(previousValue) === String(procedure.id)) {
                    option.selected = true;
                }

                procedureSelect.appendChild(option);
            });

            if (!filteredProcedures.some(function(procedure) { return String(procedure.id) === String(previousValue); })) {
                procedureSelect.value = '';
            }

            refreshEnhancedSelect(procedureSelect);

            updateProcedureMeta();
            selectedProcedureId = '';
        }

        function updateProcedureMeta() {
            if (!procedureSelect) return;

            var selectedOption = procedureSelect.options[procedureSelect.selectedIndex];
            var duration = selectedOption ? selectedOption.getAttribute('data-duration') : '30';
            var name = selectedOption ? selectedOption.getAttribute('data-name') : '';

            if (durationInput) durationInput.value = duration || '30';
            if (durationPreview) durationPreview.textContent = (duration || '30') + ' min';
            if (serviceHidden) serviceHidden.value = name || '';

            updateTimeOptions();
            updateEndTimeFromProcedure();
        }

        function setEndTimeGuidance(message, type) {
            currentEndTimeGuidanceMessage = message || currentEndTimeGuidanceMessage;

            if (!endTimeGuidance) {
                return;
            }

            endTimeGuidance.className = 'd-block mt-2 text-' + (type || 'muted');
            endTimeGuidance.textContent = message;
        }

        function currentDateValue() {
            var now = new Date();
            var month = String(now.getMonth() + 1).padStart(2, '0');
            var day = String(now.getDate()).padStart(2, '0');

            return now.getFullYear() + '-' + month + '-' + day;
        }

        function minimumAppointmentDateValue() {
            var minimumDate = currentDateValue();
            var closingMinutes = clinicClosingMinutes();
            var nowMinutes = minutesFromTime(currentTimeValue());

            if (closingMinutes !== null && nowMinutes !== null && nowMinutes >= closingMinutes) {
                var tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);

                return tomorrow.getFullYear() + '-' + String(tomorrow.getMonth() + 1).padStart(2, '0') + '-' + String(tomorrow.getDate()).padStart(2, '0');
            }

            return minimumDate;
        }

        function currentTimeValue() {
            var now = new Date();
            return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        }

        function setFieldFeedback(element, message, type) {
            if (!element) {
                return;
            }

            if (!message) {
                element.className = 'planner-field-feedback';
                element.textContent = '';
                return;
            }

            element.className = 'planner-field-feedback is-visible is-' + (type || 'danger');
            element.textContent = message;
        }

        function validateAppointmentDateField() {
            if (!appointmentDateInput || !appointmentDateInput.value) {
                setFieldFeedback(dateValidationFeedback, '', 'danger');
                if (appointmentDateInput) {
                    appointmentDateInput.setCustomValidity('');
                }
                return true;
            }

            var minimumDate = minimumAppointmentDateValue();

            if (appointmentDateInput) {
                appointmentDateInput.min = minimumDate;
            }

            if (appointmentDateInput.value < minimumDate) {
                var dateMessage = minimumDate === currentDateValue()
                    ? 'A data do agendamento não pode ser anterior a hoje.'
                    : 'O horário da clínica já foi encerrado hoje. Novos agendamentos só podem ser marcados a partir de amanhã.';
                setFieldFeedback(dateValidationFeedback, dateMessage, 'danger');
                appointmentDateInput.setCustomValidity(dateMessage);
                return false;
            }

            setFieldFeedback(dateValidationFeedback, 'Data válida para agendamento.', 'success');
            appointmentDateInput.setCustomValidity('');
            return true;
        }

        function validateAppointmentTimeField() {
            if (!startTimeInput || !appointmentDateInput || !appointmentDateInput.value || !startTimeInput.value) {
                if (startTimeInput) {
                    startTimeInput.setCustomValidity('');
                }
                setFieldFeedback(timeValidationFeedback, '', 'danger');
                return true;
            }

            var selectedMinutes = minutesFromTime(startTimeInput.value);
            var nowMinutes = minutesFromTime(currentTimeValue());

            if (appointmentDateInput.value === currentDateValue() && selectedMinutes !== null && nowMinutes !== null && selectedMinutes < nowMinutes) {
                var timeMessage = 'O horário inicial não pode ser anterior ao horário atual.';
                startTimeInput.setCustomValidity(timeMessage);
                setFieldFeedback(timeValidationFeedback, timeMessage, 'danger');
                return false;
            }

            startTimeInput.setCustomValidity('');
            setFieldFeedback(timeValidationFeedback, 'Horário inicial válido.', 'success');
            return true;
        }

        function minutesFromTime(time) {
            if (!time || String(time).indexOf(':') === -1) {
                return null;
            }

            var parts = String(time).split(':');
            return (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
        }

        function timeFromMinutes(totalMinutes) {
            var hours = Math.floor(totalMinutes / 60);
            var minutes = totalMinutes % 60;

            return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
        }

        function ceilToStep(totalMinutes) {
            if (totalMinutes === null) {
                return null;
            }

            return Math.ceil(totalMinutes / timeSlotStep) * timeSlotStep;
        }

        function floorToStep(totalMinutes) {
            if (totalMinutes === null) {
                return null;
            }

            return Math.floor(totalMinutes / timeSlotStep) * timeSlotStep;
        }

        function fillTimeSelect(selectElement, startMinutes, endMinutes, selectedValue, placeholderText, optionStateResolver) {
            if (!selectElement) return;

            selectElement.innerHTML = '';

            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = placeholderText || 'Selecione';
            selectElement.appendChild(placeholder);

            if (startMinutes === null || endMinutes === null || endMinutes < startMinutes) {
                selectElement.value = '';
                refreshEnhancedSelect(selectElement);
                return;
            }

            for (var minutes = startMinutes; minutes <= endMinutes; minutes += timeSlotStep) {
                var time = timeFromMinutes(minutes);
                var option = document.createElement('option');
                option.value = time;
                option.textContent = time;

                if (typeof optionStateResolver === 'function') {
                    var optionState = optionStateResolver(time, minutes) || {};
                    if (optionState.hidden) {
                        continue;
                    }
                    if (optionState.disabled) {
                        option.disabled = true;
                        option.style.color = '#6c757d';
                        option.style.backgroundColor = '#eef1f4';
                        option.textContent = option.textContent + ' - ' + (optionState.reason || 'ocupado');
                    }
                }

                if (String(selectedValue || '') === time) {
                    option.selected = true;
                }

                selectElement.appendChild(option);
            }

            if (selectedValue && !Array.from(selectElement.options).some(function(option) { return option.value === selectedValue; })) {
                selectElement.value = '';
            }

            refreshEnhancedSelect(selectElement);
        }

        function firstEnabledTimeOptionValue(selectElement) {
            if (!selectElement) {
                return '';
            }

            var option = Array.from(selectElement.options).find(function(item) {
                return item.value && !item.disabled;
            });

            return option ? option.value : '';
        }

        function clinicOpeningMinutes() {
            return clinicHours && clinicHours.opening_time ? minutesFromTime(clinicHours.opening_time) : 0;
        }

        function clinicClosingMinutes() {
            return clinicHours && clinicHours.closing_time ? minutesFromTime(clinicHours.closing_time) : (23 * 60) + 59;
        }

        function clinicRestrictedInterval() {
            if (!clinicHours || !clinicHours.lunch_start_time || !clinicHours.lunch_end_time) {
                return null;
            }

            return {
                start: minutesFromTime(clinicHours.lunch_start_time),
                end: minutesFromTime(clinicHours.lunch_end_time)
            };
        }

        function currentSelectedProfessionalId() {
            if (!professionalSelect) {
                return '';
            }

            return professionalSelect.value || '';
        }

        function getPatientIdField() {
            return document.getElementById('patient_id');
        }

        function selectedProfessionalRecord() {
            var selectedProfessionalId = currentSelectedProfessionalId();

            return professionals.find(function(item) {
                return String(item.id || '') === String(selectedProfessionalId);
            }) || null;
        }

        function selectedAppointmentWeekday() {
            if (!appointmentDateInput || !appointmentDateInput.value) {
                return null;
            }

            var parts = appointmentDateInput.value.split('-');

            if (parts.length !== 3) {
                return null;
            }

            var date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
            var weekDay = date.getDay();

            return weekDay === 0 ? 7 : weekDay;
        }

        function availabilityWindowsForSelectedDate() {
            var professional = selectedProfessionalRecord();
            var weekDay = selectedAppointmentWeekday();

            if (!professional || weekDay === null || !Array.isArray(professional.schedules)) {
                return [];
            }

            var windows = professional.schedules
                .filter(function(schedule) {
                    return Number(schedule.day_of_week) === Number(weekDay);
                })
                .reduce(function(intervals, schedule) {
                    var start = minutesFromTime(schedule.start_time);
                    var end = minutesFromTime(schedule.end_time);
                    var breakStart = minutesFromTime(schedule.break_start_time);
                    var breakEnd = minutesFromTime(schedule.break_end_time);

                    if (start === null || end === null || end <= start) {
                        return intervals;
                    }

                    if (breakStart !== null && breakEnd !== null && breakStart > start && breakEnd < end) {
                        intervals.push({ start: start, end: breakStart });
                        intervals.push({ start: breakEnd, end: end });
                        return intervals;
                    }

                    intervals.push({ start: start, end: end });
                    return intervals;
                }, []);

            return subtractAbsenceWindows(windows, selectedProfessionalAbsencesForDate());
        }

        function selectedProfessionalAbsencesForDate() {
            var professional = selectedProfessionalRecord();
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';

            if (!professional || !selectedDate || !Array.isArray(professional.absences)) {
                return [];
            }

            return professional.absences
                .filter(function(absence) {
                    return String(absence.date || '') === selectedDate;
                })
                .map(function(absence) {
                    return {
                        start: minutesFromTime(absence.start_time),
                        end: minutesFromTime(absence.end_time)
                    };
                })
                .filter(function(interval) {
                    return interval.start !== null && interval.end !== null && interval.end > interval.start;
                });
        }

        function subtractAbsenceWindows(windows, absences) {
            return (windows || []).reduce(function(currentWindows, windowRange) {
                return (absences || []).reduce(function(splitWindows, absenceRange) {
                    return splitWindows.reduce(function(nextWindows, currentWindow) {
                        if (absenceRange.end <= currentWindow.start || absenceRange.start >= currentWindow.end) {
                            nextWindows.push(currentWindow);
                            return nextWindows;
                        }

                        if (absenceRange.start > currentWindow.start) {
                            nextWindows.push({ start: currentWindow.start, end: absenceRange.start });
                        }

                        if (absenceRange.end < currentWindow.end) {
                            nextWindows.push({ start: absenceRange.end, end: currentWindow.end });
                        }

                        return nextWindows;
                    }, []);
                }, [windowRange]);
            }, []).filter(function(windowRange) {
                return windowRange.end > windowRange.start;
            });
        }

        function rangeFitsProfessionalAvailability(startMinutes, endMinutes) {
            var windows = availabilityWindowsForSelectedDate();

            if (!windows.length || startMinutes === null || endMinutes === null) {
                return false;
            }

            return windows.some(function(windowRange) {
                return startMinutes >= windowRange.start && endMinutes <= windowRange.end;
            });
        }

        function setAvailabilityFeedback(message, type) {
            if (!availabilityFeedback) {
                return;
            }

            if (!message) {
                availabilityFeedback.style.display = 'none';
                availabilityFeedback.className = 'small mt-2';
                availabilityFeedback.textContent = '';
                return;
            }

            availabilityFeedback.style.display = 'block';
            availabilityFeedback.className = 'small mt-2 text-' + (type || 'muted');
            availabilityFeedback.textContent = message;
        }

        function updateSubmitState() {
            if (!submitButton) {
                return;
            }

            var professional = selectedProfessionalRecord();
            var patientIdField = getPatientIdField();
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';
            var hasAvailability = availabilityWindowsForSelectedDate().length > 0;
            var hasPatient = !!(patientIdField && patientIdField.value);
            var shouldDisable = !hasPatient || !professional || !selectedDate || !hasAvailability;

            submitButton.disabled = shouldDisable;
        }

        function renderAppointmentDayOverview() {
            if (!appointmentDayOverview) {
                return;
            }

            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';
            var windows = availabilityWindowsForSelectedDate();
            var occupied = occupiedIntervalsForSelection();
            var restrictedInterval = clinicRestrictedInterval();
            var patientIdField = getPatientIdField();

            if (!selectedDate) {
                appointmentDayOverview.style.display = 'none';
                appointmentDayOverview.innerHTML = '';
                return;
            }

            var availableHtml = windows.length
                ? windows.map(function(windowRange) {
                    return '<span class="appointment-chip available">Disponível: ' + timeFromMinutes(windowRange.start) + ' às ' + timeFromMinutes(windowRange.end) + '</span>';
                }).join('')
                : '<span class="appointment-overview-empty">Sem disponibilidade configurada para esta data.</span>';

            var occupiedHtml = occupied.length
                ? occupied.map(function(interval) {
                    return '<span class="appointment-chip occupied">Ocupado: ' + timeFromMinutes(interval.start) + ' às ' + timeFromMinutes(interval.end) + '</span>';
                }).join('')
                : '<span class="appointment-chip neutral">Nenhum horário preenchido até o momento.</span>';

            var intervalHtml = restrictedInterval
                ? '<span class="appointment-chip interval">Intervalo da clínica: ' + timeFromMinutes(restrictedInterval.start) + ' às ' + timeFromMinutes(restrictedInterval.end) + '</span>'
                : '<span class="appointment-chip neutral">Sem intervalo configurado.</span>';

            if (!occupied.length && !(patientIdField && patientIdField.value)) {
                occupiedHtml = '<span class="appointment-chip neutral">Busque um paciente por CPF para prosseguir com o agendamento.</span>';
            }

            appointmentDayOverview.style.display = 'block';
            appointmentDayOverview.innerHTML = '' +
                '<div class="appointment-day-overview-title">Resumo da agenda do dia</div>' +
                '<div class="appointment-chip-list mb-3">' + availableHtml + '</div>' +
                '<div class="appointment-chip-list mb-3">' + occupiedHtml + '</div>' +
                '<div class="appointment-chip-list">' + intervalHtml + '</div>';

            enhanceAppointmentDayOverviewLayout();
        }

        function enhanceAppointmentDayOverviewLayout() {
            if (!appointmentDayOverview || !appointmentDayOverview.innerHTML) {
                return;
            }

            var chipLists = appointmentDayOverview.querySelectorAll('.appointment-chip-list');

            if (chipLists.length !== 3) {
                return;
            }

            var availableHtml = chipLists[0].innerHTML;
            var occupiedHtml = chipLists[1].innerHTML;
            var intervalHtml = chipLists[2].innerHTML;

            appointmentDayOverview.innerHTML = '' +
                '<div class="appointment-day-overview-title">Resumo da agenda do dia</div>' +
                '<div class="appointment-day-overview-grid">' +
                    '<div class="appointment-overview-panel">' +
                        '<div class="appointment-overview-heading"><i class="fas fa-clock"></i><span>Disponibilidade</span></div>' +
                        '<div class="appointment-overview-list">' + availableHtml + '</div>' +
                    '</div>' +
                    '<div class="appointment-overview-panel">' +
                        '<div class="appointment-overview-heading"><i class="fas fa-user-check"></i><span>Agenda ocupada</span></div>' +
                        '<div class="appointment-overview-list">' + occupiedHtml + '</div>' +
                    '</div>' +
                    '<div class="appointment-overview-panel">' +
                        '<div class="appointment-overview-heading"><i class="fas fa-coffee"></i><span>Intervalo da Clínica</span></div>' +
                        '<div class="appointment-overview-list">' + intervalHtml + '</div>' +
                    '</div>' +
                '</div>';
        }

        function updateProfessionalAvailabilityFeedback() {
            var professional = selectedProfessionalRecord();
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';
            var windows = availabilityWindowsForSelectedDate();

            if (!professional) {
                setAvailabilityFeedback('Selecione um profissional para consultar os dias e horários de atendimento.', 'muted');
                renderAppointmentDayOverview();
                updateSubmitState();
                return;
            }

            if (!selectedDate) {
                setAvailabilityFeedback('Selecione uma data para validar a agenda do profissional.', 'muted');
                renderAppointmentDayOverview();
                updateSubmitState();
                return;
            }

            if (!windows.length) {
                setAvailabilityFeedback('O profissional selecionado não atende nesta data. Escolha outro dia para continuar.', 'danger');
                renderAppointmentDayOverview();
                updateSubmitState();
                return;
            }

            var formattedWindows = windows.map(function(windowRange) {
                return timeFromMinutes(windowRange.start) + ' às ' + timeFromMinutes(windowRange.end);
            }).join(' • ');

            setAvailabilityFeedback('Atendimento disponível nesta data: ' + formattedWindows + '.', 'success');
            renderAppointmentDayOverview();
            updateSubmitState();
        }

        function occupiedIntervalsForSelection() {
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';
            var selectedProfessionalId = currentSelectedProfessionalId();

            return occupiedAppointments.filter(function(item) {
                return selectedDate
                    && selectedProfessionalId
                    && String(item.date || '') === String(selectedDate)
                    && String(item.professional_id || '') === String(selectedProfessionalId);
            }).map(function(item) {
                return {
                    start: minutesFromTime(item.start_time),
                    end: minutesFromTime(item.end_time)
                };
            }).filter(function(item) {
                return item.start !== null && item.end !== null;
            });
        }

        function selectedProcedureDuration() {
            var selectedOption = procedureSelect ? procedureSelect.options[procedureSelect.selectedIndex] : null;
            var duration = parseInt(selectedOption ? (selectedOption.getAttribute('data-duration') || '30') : (durationInput ? durationInput.value : '30'), 10);

            return isNaN(duration) || duration <= 0 ? 30 : duration;
        }

        function overlapsOccupiedRange(startMinutes, endMinutes, occupiedIntervals) {
            return occupiedIntervals.some(function(interval) {
                return !(endMinutes <= interval.start || startMinutes >= interval.end);
            });
        }

        function overlapsClinicRestriction(startMinutes, endMinutes) {
            var restrictedInterval = clinicRestrictedInterval();

            if (!restrictedInterval) {
                return false;
            }

            return !(endMinutes <= restrictedInterval.start || startMinutes >= restrictedInterval.end);
        }

        function isWithinClinicRestriction(minutes) {
            var restrictedInterval = clinicRestrictedInterval();

            if (!restrictedInterval || minutes === null) {
                return false;
            }

            return minutes > restrictedInterval.start && minutes < restrictedInterval.end;
        }

        function latestValidStartMinutes(endMaximum, procedureDuration) {
            if (endMaximum === null) {
                return null;
            }

            return Math.max(clinicOpeningMinutes(), endMaximum - procedureDuration);
        }

        function updateTimeOptions() {
            if (!startTimeInput || !endTimeInput) return;

            var selectedProfessionalId = currentSelectedProfessionalId();
            var availabilityWindows = availabilityWindowsForSelectedDate();
            var startMinimum = ceilToStep(clinicOpeningMinutes());
            var endMaximum = floorToStep(clinicClosingMinutes());
            var latestStartMinutes = latestValidStartMinutes(endMaximum, selectedProcedureDuration());
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';
            var today = currentDateValue();
            var minimumDate = minimumAppointmentDateValue();
            var occupiedIntervals = occupiedIntervalsForSelection();
            var procedureDuration = selectedProcedureDuration();
            var availableEndOptions = [];
            var defaultEndGuidance = '';

            if (appointmentDateInput) {
                appointmentDateInput.min = minimumDate;

                if (appointmentDateInput.value && appointmentDateInput.value < minimumDate) {
                    appointmentDateInput.value = minimumDate;
                    selectedDate = minimumDate;
                }
            }

            if (!selectedProfessionalId || !selectedDate || !availabilityWindows.length) {
                fillTimeSelect(startTimeInput, null, null, '', selectedProfessionalId && selectedDate ? 'Profissional indisponível nesta data' : 'Selecione profissional e data');
                fillTimeSelect(endTimeInput, null, null, '', 'Selecione um horário inicial');
                endTimeInput.setCustomValidity('');
                setEndTimeGuidance(defaultEndGuidance, 'muted');
                initialStartTime = '';
                initialEndTime = '';
                updateSubmitState();
                return;
            }

            if (selectedDate === today) {
                var currentMinutes = minutesFromTime(currentTimeValue());
                if (currentMinutes !== null) {
                    startMinimum = Math.max(startMinimum, ceilToStep(currentMinutes));
                }
            }

            latestStartMinutes = floorToStep(latestStartMinutes);

            fillTimeSelect(startTimeInput, startMinimum, latestStartMinutes, startTimeInput.value || initialStartTime, 'Selecione', function(time, minutes) {
                var slotEnd = minutes + procedureDuration;
                var overlapsOccupied = overlapsOccupiedRange(minutes, slotEnd, occupiedIntervals);
                var overlapsRestriction = overlapsClinicRestriction(minutes, slotEnd);
                var withinProfessionalAvailability = rangeFitsProfessionalAvailability(minutes, slotEnd);

                return {
                    disabled: !withinProfessionalAvailability || overlapsRestriction || overlapsOccupied,
                    reason: !withinProfessionalAvailability ? 'fora da agenda do profissional' : (overlapsRestriction ? 'intervalo da clínica' : 'ocupado')
                };
            });

            if (!startTimeInput.value) {
                startTimeInput.value = firstEnabledTimeOptionValue(startTimeInput);
                refreshEnhancedSelect(startTimeInput);
            }

            var selectedStartMinutes = minutesFromTime(startTimeInput.value || initialStartTime);
            var endMinimum = selectedStartMinutes !== null ? selectedStartMinutes + timeSlotStep : startMinimum;
            var restrictedInterval = clinicRestrictedInterval();

            if (selectedStartMinutes !== null) {
                endMinimum = Math.max(endMinimum, selectedStartMinutes + selectedProcedureDuration());
            }

            endMinimum = ceilToStep(endMinimum);

            fillTimeSelect(endTimeInput, endMinimum, endMaximum, endTimeInput.value || initialEndTime, 'Selecione', function(time, minutes) {
                if (selectedStartMinutes === null) {
                    return { disabled: true };
                }

                var overlapsOccupied = overlapsOccupiedRange(selectedStartMinutes, minutes, occupiedIntervals);

                if (restrictedInterval && selectedStartMinutes < restrictedInterval.start && minutes > restrictedInterval.start) {
                    return { hidden: true };
                }

                if (!rangeFitsProfessionalAvailability(selectedStartMinutes, minutes)) {
                    return {
                        disabled: true,
                        reason: 'fora da agenda do profissional'
                    };
                }

                if (isWithinClinicRestriction(minutes)) {
                    return {
                        disabled: true,
                        reason: 'intervalo da clínica'
                    };
                }

                if (!overlapsOccupied) {
                    availableEndOptions.push(minutes);
                }

                return {
                    disabled: overlapsOccupied,
                    reason: 'ocupado'
                };
            });

            endTimeInput.setCustomValidity('');

            if (selectedStartMinutes === null) {
                endTimeInput.value = '';
                setEndTimeGuidance(defaultEndGuidance, 'muted');
            } else if (!availableEndOptions.length) {
                endTimeInput.value = '';
                var noAvailabilityMessage = 'Nao ha horario de termino disponivel para este inicio. Ajuste o horario inicial para terminar antes do intervalo da clinica ou escolha um horario apos ele.';
                setEndTimeGuidance(noAvailabilityMessage, 'danger');
                endTimeInput.setCustomValidity(noAvailabilityMessage);
            } else if (restrictedInterval && selectedStartMinutes < restrictedInterval.start) {
                updateEndTimeFromProcedure();
                setEndTimeGuidance('Este atendimento pode terminar exatamente as ' + timeFromMinutes(restrictedInterval.start) + ', mas nao pode avancar para dentro do intervalo da clinica.', 'muted');
            } else {
                updateEndTimeFromProcedure();
                setEndTimeGuidance(defaultEndGuidance, 'muted');
            }

            refreshEnhancedSelect(endTimeInput);

            initialStartTime = '';
            initialEndTime = '';
            validateAppointmentTimeField();
            updateSubmitState();
        }

        function enforceStartTimeMinimum() {
            updateTimeOptions();
        }

        function updateEndTimeFromProcedure() {
            if (!startTimeInput || !endTimeInput) return;

            var selectedOption = procedureSelect ? procedureSelect.options[procedureSelect.selectedIndex] : null;
            var duration = parseInt(selectedOption ? (selectedOption.getAttribute('data-duration') || '30') : '30', 10);
            var startValue = startTimeInput.value;

            if (!startValue || isNaN(duration) || duration <= 0) {
                updateDurationFromTimeRange();
                return;
            }

            var startParts = startValue.split(':');

            if (startParts.length < 2) {
                updateDurationFromTimeRange();
                return;
            }

            var startMinutes = (parseInt(startParts[0], 10) * 60) + parseInt(startParts[1], 10);

            if (isNaN(startMinutes)) {
                updateDurationFromTimeRange();
                return;
            }

            var endMinutes = startMinutes + duration;
            var endHours = Math.floor(endMinutes / 60) % 24;
            var endRemainderMinutes = endMinutes % 60;
            var endValue = String(endHours).padStart(2, '0') + ':' + String(endRemainderMinutes).padStart(2, '0');

            var matchingEndOption = Array.from(endTimeInput.options).find(function(option) {
                return option.value === endValue && !option.disabled;
            });

            if (matchingEndOption) {
                endTimeInput.value = endValue;
            } else {
                endTimeInput.value = firstEnabledTimeOptionValue(endTimeInput);
            }

            refreshEnhancedSelect(endTimeInput);
            if (durationInput) durationInput.value = String(duration);
            if (durationPreview) durationPreview.textContent = duration + ' min';
            updateDurationFromTimeRange();
        }

        function updateDurationFromTimeRange() {
            if (!durationInput || !durationPreview) return;

            var selectedOption = procedureSelect ? procedureSelect.options[procedureSelect.selectedIndex] : null;
            var fallbackDuration = selectedOption ? (selectedOption.getAttribute('data-duration') || '30') : '30';
            var startValue = startTimeInput ? startTimeInput.value : '';
            var endValue = endTimeInput ? endTimeInput.value : '';

            if (!startValue || !endValue) {
                durationInput.value = fallbackDuration;
                durationPreview.textContent = fallbackDuration + ' min';
                return;
            }

            var startParts = startValue.split(':');
            var endParts = endValue.split(':');

            if (startParts.length < 2 || endParts.length < 2) {
                durationInput.value = fallbackDuration;
                durationPreview.textContent = fallbackDuration + ' min';
                return;
            }

            var startMinutes = (parseInt(startParts[0], 10) * 60) + parseInt(startParts[1], 10);
            var endMinutes = (parseInt(endParts[0], 10) * 60) + parseInt(endParts[1], 10);

            if (isNaN(startMinutes) || isNaN(endMinutes) || endMinutes <= startMinutes) {
                durationInput.value = fallbackDuration;
                durationPreview.textContent = fallbackDuration + ' min';
                return;
            }

            var duration = endMinutes - startMinutes;
            durationInput.value = String(duration);
            durationPreview.textContent = duration + ' min';
        }

        function updateProfessionalMeta() {
            if (!professionalSelect) return;

            var selectedProfessionalId = professionalSelect.value || '';
            var selectedOption = professionalSelect.tagName === 'SELECT'
                ? professionalSelect.options[professionalSelect.selectedIndex]
                : null;

            if (professionalHidden) {
                if (selectedOption) {
                    professionalHidden.value = selectedOption.getAttribute('data-name') || '';
                } else {
                    var selectedProfessional = professionals.find(function(item) {
                        return String(item.id) === String(selectedProfessionalId);
                    });

                    professionalHidden.value = selectedProfessional ? (selectedProfessional.nome || '') : '';
                }
            }

            renderProcedureOptions(selectedProfessionalId);
            updateProfessionalAvailabilityFeedback();
            updateTimeOptions();
        }

        if (procedureSelect) {
            procedureSelect.addEventListener('change', updateProcedureMeta);
        }

        if (patientSearchButton) {
            patientSearchButton.addEventListener('click', function() {
                findPatientByCpf();
            });
        }

        if (patientSearch) {
            patientSearch.addEventListener('blur', function() {
                if (onlyDigits(patientSearch.value).length === 11) {
                    findPatientByCpf();
                    return;
                }

                var patientIdField = document.getElementById('patient_id');
                if (patientIdField) {
                    patientIdField.value = '';
                }

                clearPatientFields();

                if (submitButton) {
                    submitButton.disabled = true;
                }

                updateSubmitState();
            });

            patientSearch.addEventListener('input', function() {
                var patientIdField = document.getElementById('patient_id');
                if (patientIdField) {
                    patientIdField.value = '';
                }

                clearPatientFields();
                setPatientSearchFeedback('', 'muted');
                if (submitButton) {
                    submitButton.disabled = true;
                }
                updateSubmitState();
            });
        }

        if (appointmentForm) {
            appointmentForm.addEventListener('submit', function(event) {
                var patientIdField = document.getElementById('patient_id');

                if (patientIdField && !patientIdField.value) {
                    event.preventDefault();
                    setPatientSearchFeedback('Busque um paciente cadastrado pelo CPF antes de salvar o agendamento.', 'danger');
                    if (submitButton) {
                        submitButton.disabled = true;
                    }
                }
            });
        }

        if (professionalSelect) {
            professionalSelect.addEventListener('change', updateProfessionalMeta);
            updateProfessionalMeta();
        } else if (procedureSelect) {
            updateProcedureMeta();
        }

        if (startTimeInput) {
            startTimeInput.addEventListener('change', function() {
                updateTimeOptions();
                updateEndTimeFromProcedure();
                validateAppointmentTimeField();
            });
        }

        if (appointmentDateInput) {
            appointmentDateInput.min = minimumAppointmentDateValue();
            appointmentDateInput.addEventListener('change', function() {
                validateAppointmentDateField();
                updateProfessionalAvailabilityFeedback();
                enforceStartTimeMinimum();
                validateAppointmentTimeField();
            });
            validateAppointmentDateField();
            enforceStartTimeMinimum();
        }

        if (endTimeInput) {
            endTimeInput.addEventListener('change', function() {
                endTimeInput.setCustomValidity('');
                updateDurationFromTimeRange();
            });
            endTimeInput.addEventListener('invalid', function() {
                if (endTimeInput.validity.valueMissing) {
                    endTimeInput.setCustomValidity(currentEndTimeGuidanceMessage);
                }
            });
        }

        updateEndTimeFromProcedure();
        updateProfessionalAvailabilityFeedback();
        validateAppointmentTimeField();
        setAppointmentFieldsLocked(!(document.getElementById('patient_id') && document.getElementById('patient_id').value));
        if (document.getElementById('patient_id') && document.getElementById('patient_id').value) {
            setPatientSearchFeedback('Paciente já selecionado para este agendamento.', 'success');
        }
        updateSubmitState();

        if (patientSearch) {
            patientSearch.addEventListener('input', function() {
                var value = onlyDigits(this.value);

                if (!value) {
                    document.getElementById('patient_id').value = '';
                    return;
                }

                var match = patients.find(function(patient) {
                    return onlyDigits(patient.cpf) === value;
                });

                if (!match) {
                    document.getElementById('patient_id').value = '';
                    return;
                }

                fillPatientFields(match);
            });

            var selectedPatientId = '{{ old('patient_id', request('patient_id')) }}';
            if (selectedPatientId) {
                var selectedPatient = patients.find(function(patient) {
                    return String(patient.id) === String(selectedPatientId);
                });

                if (selectedPatient) {
                    patientSearch.value = selectedPatient.cpf || '';
                    fillPatientFields(selectedPatient);
                }
            }
        }

        function bindCepLookup(cepId, enderecoId, bairroId) {
            var cepField = document.getElementById(cepId);
            var enderecoField = document.getElementById(enderecoId);
            var bairroField = document.getElementById(bairroId);
            var lastFetchedCep = '';

            if (!cepField || !enderecoField || !bairroField) {
                return;
            }

            function fetchCepData() {
                var cep = (cepField.value || '').replace(/\D/g, '');

                if (cep.length !== 8 || cep === lastFetchedCep) {
                    return;
                }

                lastFetchedCep = cep;

                fetch('https://viacep.com.br/ws/' + cep + '/json/')
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Falha ao consultar CEP');
                        }

                        return response.json();
                    })
                    .then(function(data) {
                        if (data.erro) {
                            return;
                        }

                        enderecoField.value = data.logradouro || '';
                        bairroField.value = data.bairro || '';
                    })
                    .catch(function() {
                    });
            }

            cepField.addEventListener('blur', fetchCepData);
            cepField.addEventListener('input', function() {
                if ((cepField.value || '').replace(/\D/g, '').length === 8) {
                    fetchCepData();
                }
            });

            if ((cepField.value || '').replace(/\D/g, '').length === 8) {
                fetchCepData();
            }
        }

        function initPatientCompletion(formSelector, options) {
            var form = document.querySelector(formSelector);

            if (!form) {
                return;
            }

            var progressBar = form.querySelector(options.progressSelector);
            var progressText = form.querySelector(options.progressTextSelector);
            var missingList = form.querySelector(options.missingListSelector);

            if (!progressBar || !progressText || !missingList) {
                return;
            }

            var watchedFields = [
                { selector: '[name="nome"]', label: 'Nome completo', required: true },
                { selector: '[name="telefone"]', label: 'Celular', required: true },
                { selector: '[name="email"]', label: 'E-mail', required: true },
                { selector: '[name="cpf"]', label: 'CPF', required: false },
                { selector: '[name="sexo"]', label: 'Sexo', required: false },
                { selector: '[name="data_nascimento"]', label: 'Data de nascimento', required: false },
                { selector: '[name="cep"]', label: 'CEP', required: false },
                { selector: '[name="endereco"]', label: 'Endereço', required: false },
                { selector: '[name="bairro"]', label: 'Bairro', required: false },
                { selector: '[name="tipo_moradia"]', label: 'Tipo de imóvel', required: false }
            ].map(function(fieldConfig) {
                fieldConfig.element = form.querySelector(fieldConfig.selector);
                return fieldConfig;
            }).filter(function(fieldConfig) {
                return Boolean(fieldConfig.element);
            });

            function hasValue(element) {
                return String(element.value || '').trim() !== '';
            }

            function refreshCompletion() {
                var total = watchedFields.length;
                var completed = watchedFields.filter(function(fieldConfig) {
                    return hasValue(fieldConfig.element);
                }).length;
                var percent = total ? Math.round((completed / total) * 100) : 0;
                var missing = watchedFields.filter(function(fieldConfig) {
                    return !hasValue(fieldConfig.element);
                });

                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', String(percent));
                progressText.textContent = completed + ' de ' + total + ' campos preenchidos';

                if (!missing.length) {
                    missingList.textContent = 'Cadastro completo para prosseguir com mais segurança.';
                    return;
                }

                var requiredMissing = missing.filter(function(fieldConfig) {
                    return fieldConfig.required;
                }).map(function(fieldConfig) {
                    return fieldConfig.label;
                });
                var optionalMissing = missing.filter(function(fieldConfig) {
                    return !fieldConfig.required;
                }).map(function(fieldConfig) {
                    return fieldConfig.label;
                });
                var parts = [];

                if (requiredMissing.length) {
                    parts.push('Obrigatórios pendentes: ' + requiredMissing.join(', '));
                }

                if (optionalMissing.length) {
                    parts.push('Recomendados pendentes: ' + optionalMissing.join(', '));
                }

                missingList.textContent = parts.join('. ');
            }

            watchedFields.forEach(function(fieldConfig) {
                fieldConfig.element.addEventListener('input', refreshCompletion);
                fieldConfig.element.addEventListener('change', refreshCompletion);
            });

            refreshCompletion();
        }

        function bindPhotoPreview(formSelector) {
            var form = document.querySelector(formSelector);

            if (!form) {
                return;
            }

            var fileInput = form.querySelector('[data-patient-photo-input]');
            var preview = form.querySelector('[data-patient-photo-preview]');
            var objectUrl = null;

            if (!fileInput || !preview) {
                return;
            }

            fileInput.addEventListener('change', function () {
                var file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }

                if (!file) {
                    preview.setAttribute('src', preview.dataset.defaultSrc || '');
                    return;
                }

                objectUrl = URL.createObjectURL(file);
                preview.setAttribute('src', objectUrl);
            });
        }

        if (window.IMask) {
            var patientSearchInput = document.getElementById('patient_search');
            var cpfInput = document.getElementById('p_cpf');
            var phoneInput = document.getElementById('p_telefone');
            var appointmentPhoneInput = document.getElementById('telefone');
            var cepInput = document.getElementById('p_cep');

            if (patientSearchInput) {
                IMask(patientSearchInput, { mask: '000.000.000-00' });
            }

            if (cpfInput) {
                IMask(cpfInput, { mask: '000.000.000-00' });
            }

            if (phoneInput) {
                IMask(phoneInput, { mask: '(00) 00000-0000' });
            }

            if (appointmentPhoneInput) {
                IMask(appointmentPhoneInput, { mask: '(00) 00000-0000' });
            }

            if (cepInput) {
                IMask(cepInput, { mask: '00000-000' });
            }
        }

        bindCepLookup('p_cep', 'p_endereco', 'p_bairro');
        initPatientCompletion('#patient-create-form', {
            progressSelector: '[data-patient-progress-bar]',
            progressTextSelector: '[data-patient-progress-text]',
            missingListSelector: '[data-patient-missing-fields]'
        });
        bindPhotoPreview('#patient-create-form');

        if (appointmentForm) {
            appointmentForm.addEventListener('submit', function(event) {
                var dateValid = validateAppointmentDateField();
                var timeValid = validateAppointmentTimeField();

                if (!dateValid || !timeValid) {
                    event.preventDefault();
                }
            });
        }
    });
</script>
@endsection

