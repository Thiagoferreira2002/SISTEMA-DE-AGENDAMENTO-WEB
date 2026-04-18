@extends('admin.layouts.master')
@section('content')
<section class="section">
    @php
        $activeTab = old('tab', request('tab', 'agendamento'));
        $isPatientForm = $activeTab === 'paciente';
    @endphp

    <div class="section-header">
        <h1>{{ $isPatientForm ? 'Cadastro de Pacientes' : 'Novo Agendamento' }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.agendamentos.index') }}">Agendamentos</a></div>
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
                        @if(!empty($clinicHours))
                            <div class="alert alert-light border">
                                Horário da clínica: {{ $clinicHours['opening_time'] }} às {{ $clinicHours['closing_time'] }}.
                            </div>
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

                        <div class="border rounded p-3">
                            @if(! $isPatientForm)
                                <form action="{{ route('admin.agendamentos.store') }}" method="POST" novalidate>
                                    @csrf
                                    <input type="hidden" name="tab" value="agendamento">
                                    <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $preselectedPatient?->id) }}">
                                    <input type="hidden" name="duracao_minutos" id="duracao_minutos" value="{{ old('duracao_minutos', 30) }}">
                                    <input type="hidden" name="servico" id="servico_nome" value="{{ old('servico') }}">
                                    <input type="hidden" name="medico" id="medico_nome" value="{{ old('medico', $lockedProfessional->nome ?? '') }}">

                                    <div class="alert alert-light border mb-4">
                                        <strong>Busca de paciente por CPF</strong>
                                        <div class="mt-2">
                                            <input type="text" class="form-control" id="patient_search" placeholder="Digite o CPF do paciente" autocomplete="off" inputmode="numeric" value="{{ old('patient_search', $preselectedPatient?->cpf) }}">
                                            <small class="text-muted">Informe o CPF para localizar um paciente existente e preencher Nome, E-mail e Telefone automaticamente.</small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nome">Nome *</label>
                                                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $preselectedPatient?->nome) }}" required>
                                                @error('nome')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email *</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $preselectedPatient?->email) }}" required>
                                                @error('email')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="medico">Profissional *</label>
                                                @if($lockedProfessional)
                                                    <input type="hidden" id="professional_id" name="professional_id" value="{{ old('professional_id', $lockedProfessional->id) }}">
                                                    <input type="text" class="form-control" value="{{ $lockedProfessional->nome }}" readonly>
                                                @else
                                                    <select class="form-control @error('professional_id') is-invalid @enderror" id="professional_id" name="professional_id">
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
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefone">Telefone *</label>
                                                <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone', $preselectedPatient?->telefone) }}" required>
                                                @error('telefone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="procedure_id">Procedimento *</label>
                                                <select class="form-control @error('procedure_id') is-invalid @enderror" id="procedure_id" name="procedure_id">
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_agendamento">Data *</label>
                                                <input type="date" class="form-control @error('data_agendamento') is-invalid @enderror" id="data_agendamento" name="data_agendamento" value="{{ old('data_agendamento') }}" required>
                                                @error('data_agendamento')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="horario">Horário inicial *</label>
                                                <select class="form-control @error('horario') is-invalid @enderror" id="horario" name="horario" required>
                                                    <option value="">Selecione</option>
                                                </select>
                                                @error('horario')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="horario_final">Horário final *</label>
                                                <select class="form-control @error('horario_final') is-invalid @enderror" id="horario_final" name="horario_final" required>
                                                    <option value="">Selecione</option>
                                                </select>
                                                <small class="text-muted d-block mt-2">Preenchido automaticamente com base na duração média do procedimento selecionado.</small>
                                                @error('horario_final')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="motivo_consulta">Motivo do Agendamento</label>
                                        <textarea class="form-control @error('motivo_consulta') is-invalid @enderror" id="motivo_consulta" name="motivo_consulta" rows="3" placeholder="Descreva o motivo do agendamento">{{ old('motivo_consulta') }}</textarea>
                                        @error('motivo_consulta')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Salvar</button>
                                        <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary">Cancelar</a>
                                    </div>
                                </form>
                            @else
                                <form action="{{ route('admin.patients.store') }}" method="POST" id="patient-create-form" autocomplete="off" data-patient-live-check="true" data-patient-duplicate-url="{{ route('admin.patients.duplicate-check') }}">
                                    @csrf
                                    <input type="hidden" name="draft_key" value="admin.patients.create.inline">
                                    <input type="hidden" name="origem" value="agendamento">
                                    <input type="hidden" name="tab" value="paciente">
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            O cadastro do paciente nao foi salvo. Verifique os campos destacados abaixo.
                                        </div>
                                    @endif

                                    <div class="border rounded p-3 mb-4">
                                        <h5 class="mb-3">Dados Pessoais</h5>
                                        <div class="row">
                                            <div class="col-md-8"><div class="form-group"><label for="p_nome">Nome completo *</label><input type="text" class="form-control" id="p_nome" name="nome" value="{{ old('nome') }}" autocomplete="off" required>@error('nome')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-2"><div class="form-group"><label for="p_cpf">CPF</label><input type="text" class="form-control" id="p_cpf" name="cpf" value="{{ old('cpf') }}" autocomplete="off">@error('cpf')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-2"><div class="form-group"><label for="p_sexo">Sexo</label><select class="form-control" id="p_sexo" name="sexo"><option value="">Selecione</option><option value="feminino" {{ old('sexo') === 'feminino' ? 'selected' : '' }}>Feminino</option><option value="masculino" {{ old('sexo') === 'masculino' ? 'selected' : '' }}>Masculino</option><option value="outro" {{ old('sexo') === 'outro' ? 'selected' : '' }}>Outro</option></select></div></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"><div class="form-group"><label for="p_data_nascimento">Data de Nascimento</label><input type="date" class="form-control" id="p_data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" autocomplete="off" max="{{ now()->format('Y-m-d') }}">@error('data_nascimento')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                        </div>
                                    </div>

                                    <div class="border rounded p-3 mb-4">
                                        <h5 class="mb-3">Contato</h5>
                                        <div class="row">
                                            <div class="col-md-6"><div class="form-group"><label for="p_telefone">Celular (WhatsApp) *</label><input type="text" class="form-control" id="p_telefone" name="telefone" value="{{ old('telefone') }}" autocomplete="off" required>@error('telefone')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-6"><div class="form-group"><label for="p_email">E-mail *</label><input type="email" class="form-control" id="p_email" name="email" value="{{ old('email') }}" autocomplete="off" required>@error('email')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                        </div>
                                    </div>

                                    <div class="border rounded p-3 mb-4">
                                        <h5 class="mb-3">Endereço</h5>
                                        <div class="row">
                                            <div class="col-md-4"><div class="form-group"><label for="p_endereco">Endereço</label><input type="text" class="form-control" id="p_endereco" name="endereco" value="{{ old('endereco') }}" autocomplete="off">@error('endereco')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-2"><div class="form-group"><label for="p_numero_endereco">Número</label><input type="text" class="form-control" id="p_numero_endereco" name="numero_endereco" value="{{ old('numero_endereco') }}" autocomplete="off">@error('numero_endereco')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-3"><div class="form-group"><label for="p_cep">CEP</label><input type="text" class="form-control" id="p_cep" name="cep" value="{{ old('cep') }}" autocomplete="off">@error('cep')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-3"><div class="form-group"><label for="p_bairro">Bairro</label><input type="text" class="form-control" id="p_bairro" name="bairro" value="{{ old('bairro') }}" autocomplete="off">@error('bairro')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"><div class="form-group"><label for="p_tipo_moradia">Tipo de imóvel</label><select class="form-control" id="p_tipo_moradia" name="tipo_moradia"><option value="">Selecione</option><option value="casa" {{ old('tipo_moradia') === 'casa' ? 'selected' : '' }}>Casa</option><option value="apartamento" {{ old('tipo_moradia') === 'apartamento' ? 'selected' : '' }}>Apartamento</option><option value="condominio" {{ old('tipo_moradia') === 'condominio' ? 'selected' : '' }}>Condomínio</option><option value="sobrado" {{ old('tipo_moradia') === 'sobrado' ? 'selected' : '' }}>Sobrado</option><option value="comercial" {{ old('tipo_moradia') === 'comercial' ? 'selected' : '' }}>Comercial</option><option value="rural" {{ old('tipo_moradia') === 'rural' ? 'selected' : '' }}>Rural</option><option value="outro" {{ old('tipo_moradia') === 'outro' ? 'selected' : '' }}>Outro</option></select>@error('tipo_moradia')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-5"><div class="form-group mb-0"><label for="p_complemento">Complemento</label><input type="text" class="form-control" id="p_complemento" name="complemento" value="{{ old('complemento') }}" autocomplete="off">@error('complemento')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">Cadastrar Paciente</button>
                                        <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary">Cancelar</a>
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

        function onlyDigits(value) {
            return String(value || '').replace(/\D/g, '');
        }

        function fillPatientFields(patient) {
            document.getElementById('patient_id').value = patient.id || '';
            document.getElementById('nome').value = patient.nome || '';
            document.getElementById('email').value = patient.email || '';
            document.getElementById('telefone').value = patient.telefone || '';
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

            updateEndTimeFromProcedure();
        }

        function currentDateValue() {
            var now = new Date();
            var month = String(now.getMonth() + 1).padStart(2, '0');
            var day = String(now.getDate()).padStart(2, '0');

            return now.getFullYear() + '-' + month + '-' + day;
        }

        function currentTimeValue() {
            var now = new Date();
            return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
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

        function fillTimeSelect(selectElement, startMinutes, endMinutes, selectedValue, placeholderText, optionStateResolver) {
            if (!selectElement) return;

            selectElement.innerHTML = '';

            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = placeholderText || 'Selecione';
            selectElement.appendChild(placeholder);

            if (startMinutes === null || endMinutes === null || endMinutes < startMinutes) {
                selectElement.value = '';
                return;
            }

            for (var minutes = startMinutes; minutes <= endMinutes; minutes++) {
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

        function updateTimeOptions() {
            if (!startTimeInput || !endTimeInput) return;

            var startMinimum = clinicOpeningMinutes();
            var endMaximum = clinicClosingMinutes();
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';
            var today = currentDateValue();
            var occupiedIntervals = occupiedIntervalsForSelection();
            var procedureDuration = selectedProcedureDuration();

            if (selectedDate === today) {
                var currentMinutes = minutesFromTime(currentTimeValue());
                if (currentMinutes !== null) {
                    startMinimum = Math.max(startMinimum, currentMinutes);
                }
            }

            fillTimeSelect(startTimeInput, startMinimum, endMaximum, startTimeInput.value || initialStartTime, 'Selecione', function(time, minutes) {
                var slotEnd = minutes + procedureDuration;
                var overlapsOccupied = overlapsOccupiedRange(minutes, slotEnd, occupiedIntervals);

                return {
                    hidden: overlapsClinicRestriction(minutes, slotEnd),
                    disabled: overlapsOccupied || slotEnd > endMaximum,
                    reason: 'ocupado'
                };
            });

            var selectedStartMinutes = minutesFromTime(startTimeInput.value || initialStartTime);
            var endMinimum = selectedStartMinutes !== null ? selectedStartMinutes + 1 : startMinimum;

            fillTimeSelect(endTimeInput, endMinimum, endMaximum, endTimeInput.value || initialEndTime, 'Selecione', function(time, minutes) {
                if (selectedStartMinutes === null) {
                    return { disabled: true };
                }

                return {
                    hidden: overlapsClinicRestriction(selectedStartMinutes, minutes),
                    disabled: overlapsOccupiedRange(selectedStartMinutes, minutes, occupiedIntervals),
                    reason: 'ocupado'
                };
            });

            initialStartTime = '';
            initialEndTime = '';
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

            endTimeInput.value = endValue;
            if (durationInput) durationInput.value = String(duration);
            if (durationPreview) durationPreview.textContent = duration + ' min';
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
            updateTimeOptions();
        }

        if (procedureSelect) {
            procedureSelect.addEventListener('change', updateProcedureMeta);
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
            });
        }

        if (appointmentDateInput) {
            appointmentDateInput.addEventListener('change', enforceStartTimeMinimum);
            enforceStartTimeMinimum();
        }

        if (endTimeInput) {
            endTimeInput.addEventListener('change', updateDurationFromTimeRange);
        }

        updateEndTimeFromProcedure();

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

        if (window.IMask) {
            var patientSearchInput = document.getElementById('patient_search');
            var cpfInput = document.getElementById('p_cpf');
            var phoneInput = document.getElementById('p_telefone');
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

            if (cepInput) {
                IMask(cepInput, { mask: '00000-000' });
            }
        }

        bindCepLookup('p_cep', 'p_endereco', 'p_bairro');
    });
</script>
@endsection
