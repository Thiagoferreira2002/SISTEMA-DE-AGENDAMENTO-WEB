@extends('admin.layouts.master')
@section('content')
<style>
    .appointment-planner-shell {
        border: 1px solid rgba(30, 144, 255, 0.14);
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(244,249,255,.94));
        box-shadow: 0 12px 28px rgba(18, 58, 99, 0.06);
    }

    .appointment-planner-shell .planner-select,
    .appointment-planner-shell .planner-date {
        min-height: 48px;
        font-weight: 600;
        border-radius: 12px;
    }

    .appointment-planner-shell .planner-select option:disabled {
        color: #6c757d;
        background: #eef1f4;
    }

    .appointment-day-overview {
        border: 1px solid rgba(30, 144, 255, 0.18);
        border-radius: 16px;
        background: rgba(248, 251, 255, 0.95);
        padding: 18px;
    }

    .appointment-day-overview-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #4d6d8a;
        margin-bottom: 12px;
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
</style>
<section class="section">
    @php
        $startTimeValue = old('horario', substr((string) $agendamento->horario, 0, 5));
        $endTimeValue = old('horario_final', optional($agendamento->data_agendamento)
            ? $agendamento->data_agendamento->copy()->setTimeFromTimeString(substr((string) $agendamento->horario, 0, 5))->addMinutes((int) ($agendamento->duracao_minutos ?: 30))->format('H:i')
            : '');
        $selectedProcedureId = old('procedure_id', $agendamento->procedure_id);
        $selectedProfessionalId = old('professional_id', $agendamento->professional_id);
    @endphp

    <div class="section-header">
        <h1>Editar Agendamento</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ $returnUrl }}">Agendamentos</a></div>
            <div class="breadcrumb-item">Editar</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar Agendamento</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>Nao foi possivel atualizar o agendamento.</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="border rounded p-3 appointment-planner-shell">
                            <form action="{{ route('admin.agendamentos.update', $agendamento) }}" method="POST" novalidate>
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="return_to" value="{{ $returnUrl }}">
                                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $agendamento->patient_id) }}">
                                <input type="hidden" name="unit_id" value="{{ old('unit_id', $agendamento->unit_id) }}">
                                <input type="hidden" name="room_id" value="{{ old('room_id', $agendamento->room_id) }}">
                                <input type="hidden" name="insurance_id" value="{{ old('insurance_id', $agendamento->insurance_id) }}">
                                <input type="hidden" name="insurance_plan_id" value="{{ old('insurance_plan_id', $agendamento->insurance_plan_id) }}">
                                <input type="hidden" name="unidade" value="{{ old('unidade', $agendamento->unidade) }}">
                                <input type="hidden" name="convenio" value="{{ old('convenio', $agendamento->convenio) }}">
                                <input type="hidden" name="numero_guia" value="{{ old('numero_guia', $agendamento->numero_guia) }}">
                                <input type="hidden" name="numero_autorizacao" value="{{ old('numero_autorizacao', $agendamento->numero_autorizacao) }}">
                                <input type="hidden" name="observacao_alerta" value="{{ old('observacao_alerta', $agendamento->observacao_alerta) }}">
                                <input type="hidden" name="prioridade" value="{{ old('prioridade', $agendamento->prioridade ?? 0) }}">
                                <input type="hidden" name="preferencia_turno" value="{{ old('preferencia_turno', $agendamento->preferencia_turno) }}">
                                <input type="hidden" name="data_limite_espera" value="{{ old('data_limite_espera', optional($agendamento->data_limite_espera)->format('Y-m-d')) }}">
                                <input type="hidden" name="duracao_minutos" id="duracao_minutos" value="{{ old('duracao_minutos', $agendamento->duracao_minutos ?: 30) }}">
                                <input type="hidden" name="servico" id="servico_nome" value="{{ old('servico', $agendamento->servico) }}">
                                <input type="hidden" name="medico" id="medico_nome" value="{{ old('medico', $agendamento->professional?->nome ?: $agendamento->medico) }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nome">Nome *</label>
                                            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $agendamento->nome) }}" required>
                                            @error('nome')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $agendamento->email) }}" required>
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
                                                        <option value="{{ $professional['id'] }}" data-name="{{ $professional['nome'] }}" data-color="{{ $professional['cor'] }}" {{ (string) $selectedProfessionalId === (string) $professional['id'] ? 'selected' : '' }}>{{ $professional['nome'] }} - {{ $professional['especialidade'] }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                            @error('professional_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefone">Telefone *</label>
                                            <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone', $agendamento->telefone) }}" required>
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
                                            <small class="text-muted d-block mt-2">Tempo estimado: <span id="duracao_preview">{{ old('duracao_minutos', $agendamento->duracao_minutos ?: 30) }} min</span></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="data_agendamento">Data *</label>
                                            <input type="date" class="form-control planner-date @error('data_agendamento') is-invalid @enderror" id="data_agendamento" name="data_agendamento" value="{{ old('data_agendamento', $agendamento->data_agendamento->format('Y-m-d')) }}" required>
                                            <div id="professional-availability-feedback" class="small mt-2" style="display:none;"></div>
                                            @error('data_agendamento')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="horario">Horário inicial *</label>
                                            <select class="form-control planner-select @error('horario') is-invalid @enderror" id="horario" name="horario" required>
                                                <option value="">Selecione</option>
                                            </select>
                                            @error('horario')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="horario_final">Horário final *</label>
                                            <select class="form-control planner-select @error('horario_final') is-invalid @enderror" id="horario_final" name="horario_final" required>
                                                <option value="">Selecione</option>
                                            </select>
                                            <small id="end-time-guidance" class="text-muted d-block mt-2">Preenchido automaticamente com base na duração média do procedimento selecionado. Você pode encerrar exatamente no início do intervalo da clínica, mas não pode avançar para dentro dele.</small>
                                            @error('horario_final')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="pendente" {{ old('status', $agendamento->status) === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                                <option value="confirmado" {{ old('status', $agendamento->status) === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                                                <option value="cancelado" {{ old('status', $agendamento->status) === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                                <option value="concluido" {{ old('status', $agendamento->status) === 'concluido' ? 'selected' : '' }}>Concluído</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div id="appointment-day-overview" class="appointment-day-overview mt-2" style="display:none;"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="motivo_consulta">Motivo do Agendamento</label>
                                    <textarea class="form-control @error('motivo_consulta') is-invalid @enderror" id="motivo_consulta" name="motivo_consulta" rows="3" placeholder="Descreva o motivo do agendamento">{{ old('motivo_consulta', $agendamento->motivo_consulta ?: $agendamento->descricao) }}</textarea>
                                    @error('motivo_consulta')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Atualizar</button>
                                    <a href="{{ $returnUrl }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
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
        var procedures = @json($procedureOptions ?? []);
        var professionals = @json($professionalOptions ?? []);
        var procedureSelect = document.getElementById('procedure_id');
        var professionalSelect = document.getElementById('professional_id');
        var durationInput = document.getElementById('duracao_minutos');
        var durationPreview = document.getElementById('duracao_preview');
        var serviceHidden = document.getElementById('servico_nome');
        var professionalHidden = document.getElementById('medico_nome');
        var appointmentDateInput = document.getElementById('data_agendamento');
        var startTimeInput = document.getElementById('horario');
        var endTimeInput = document.getElementById('horario_final');
        var selectedProcedureId = '{{ $selectedProcedureId }}';
        var clinicHours = @json($clinicHours ?? null);
        var occupiedAppointments = @json($occupiedAppointments ?? []);
        var initialStartTime = '{{ $startTimeValue }}';
        var initialEndTime = '{{ $endTimeValue }}';
        var availabilityFeedback = document.getElementById('professional-availability-feedback');
        var appointmentDayOverview = document.getElementById('appointment-day-overview');
        var endTimeGuidance = document.getElementById('end-time-guidance');
        var appointmentForm = document.querySelector('form[action="{{ route('admin.agendamentos.update', $agendamento) }}"]');
        var submitButton = appointmentForm ? appointmentForm.querySelector('button[type="submit"]') : null;
        var timeSlotStep = 5;
        var currentEndTimeGuidanceMessage = 'Selecione um horário de término válido. Você pode encerrar exatamente no início do intervalo da clínica, mas não pode avançar para dentro dele.';

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

            return professional.schedules
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

        function setEndTimeGuidance(message, type) {
            currentEndTimeGuidanceMessage = message || currentEndTimeGuidanceMessage;

            if (!endTimeGuidance) {
                return;
            }

            endTimeGuidance.className = 'd-block mt-2 text-' + (type || 'muted');
            endTimeGuidance.textContent = message;
        }

        function updateSubmitState() {
            if (!submitButton) {
                return;
            }

            var professional = selectedProfessionalRecord();
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';
            var hasAvailability = availabilityWindowsForSelectedDate().length > 0;
            var shouldDisable = !professional || !selectedDate || !hasAvailability;

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

            if (!selectedDate) {
                appointmentDayOverview.style.display = 'none';
                appointmentDayOverview.innerHTML = '';
                return;
            }

            var availableHtml = windows.length
                ? windows.map(function(windowRange) {
                    return '<span class="appointment-chip available">Disponível: ' + timeFromMinutes(windowRange.start) + ' às ' + timeFromMinutes(windowRange.end) + '</span>';
                }).join('')
                : '<span class="appointment-chip neutral">Sem disponibilidade configurada para esta data.</span>';

            var occupiedHtml = occupied.length
                ? occupied.map(function(interval) {
                    return '<span class="appointment-chip occupied">Ocupado: ' + timeFromMinutes(interval.start) + ' às ' + timeFromMinutes(interval.end) + '</span>';
                }).join('')
                : '<span class="appointment-chip neutral">Nenhum horário preenchido até o momento.</span>';

            var intervalHtml = restrictedInterval
                ? '<span class="appointment-chip interval">Intervalo da clínica: ' + timeFromMinutes(restrictedInterval.start) + ' às ' + timeFromMinutes(restrictedInterval.end) + '</span>'
                : '<span class="appointment-chip neutral">Sem intervalo configurado.</span>';

            appointmentDayOverview.style.display = 'block';
            appointmentDayOverview.innerHTML = '' +
                '<div class="appointment-day-overview-title">Resumo da agenda do dia</div>' +
                '<div class="appointment-chip-list mb-3">' + availableHtml + '</div>' +
                '<div class="appointment-chip-list mb-3">' + occupiedHtml + '</div>' +
                '<div class="appointment-chip-list">' + intervalHtml + '</div>';
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

        function updateTimeOptions() {
            if (!startTimeInput || !endTimeInput) return;

            var selectedProfessionalId = currentSelectedProfessionalId();
            var availabilityWindows = availabilityWindowsForSelectedDate();
            var startMinimum = ceilToStep(clinicOpeningMinutes());
            var endMaximum = floorToStep(clinicClosingMinutes());
            var latestStartMinutes = latestValidStartMinutes(endMaximum, selectedProcedureDuration());
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';
            var today = currentDateValue();
            var occupiedIntervals = occupiedIntervalsForSelection();
            var procedureDuration = selectedProcedureDuration();
            var availableEndOptions = [];
            var defaultEndGuidance = 'Preenchido automaticamente com base na duração média do procedimento selecionado. Você pode encerrar exatamente no início do intervalo da clínica, mas não pode avançar para dentro dele.';

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
                setEndTimeGuidance(defaultEndGuidance, 'muted');
            } else if (!availableEndOptions.length) {
                var noAvailabilityMessage = 'Nao ha horario de termino disponivel para este inicio. Ajuste o horario inicial para terminar antes do intervalo da clinica ou escolha um horario apos ele.';
                setEndTimeGuidance(noAvailabilityMessage, 'danger');
                endTimeInput.setCustomValidity(noAvailabilityMessage);
            } else if (restrictedInterval && selectedStartMinutes < restrictedInterval.start) {
                setEndTimeGuidance('Este atendimento pode terminar exatamente as ' + timeFromMinutes(restrictedInterval.start) + ', mas nao pode avancar para dentro do intervalo da clinica.', 'muted');
            } else {
                setEndTimeGuidance(defaultEndGuidance, 'muted');
            }

            initialStartTime = '';
            initialEndTime = '';
            updateSubmitState();
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
            updateProfessionalAvailabilityFeedback();
            updateTimeOptions();
        }

        if (procedureSelect) {
            procedureSelect.addEventListener('change', updateProcedureMeta);
        }

        if (professionalSelect) {
            professionalSelect.addEventListener('change', updateProfessionalMeta);
            updateProfessionalMeta();
        }

        if (startTimeInput) {
            startTimeInput.addEventListener('change', function() {
                updateTimeOptions();
                updateEndTimeFromProcedure();
            });
        }

        if (appointmentDateInput) {
            appointmentDateInput.addEventListener('change', function() {
                updateProfessionalAvailabilityFeedback();
                updateTimeOptions();
            });
            updateProfessionalAvailabilityFeedback();
            updateTimeOptions();
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

        if (window.IMask) {
            IMask(document.getElementById('telefone'), { mask: [{ mask: '(00) 00000-0000' }, { mask: '(00) 0000-0000' }] });
        }
    });
</script>
@endsection
