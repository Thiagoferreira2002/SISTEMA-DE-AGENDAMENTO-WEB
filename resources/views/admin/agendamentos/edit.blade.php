@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Editar Agendamento</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.agendamentos.index') }}">Agendamentos</a></div>
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
                        @if(!empty($clinicHours))
                            <div class="alert alert-light border">
                                Horário da clínica: {{ $clinicHours['opening_time'] }} às {{ $clinicHours['closing_time'] }}.
                            </div>
                        @endif
                        @php
                            $startTimeValue = old('horario', substr((string) $agendamento->horario, 0, 5));
                            $endTimeValue = old('horario_final', optional($agendamento->data_agendamento)
                                ? $agendamento->data_agendamento->copy()->setTimeFromTimeString(substr((string) $agendamento->horario, 0, 5))->addMinutes((int) ($agendamento->duracao_minutos ?: 30))->format('H:i')
                                : '');
                        @endphp
                        <form action="{{ route('admin.agendamentos.update', $agendamento) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="medico" value="{{ old('medico', $agendamento->medico) }}">
                            <input type="hidden" name="duracao_minutos" id="duracao_minutos" value="{{ old('duracao_minutos', $agendamento->duracao_minutos ?: 30) }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nome">Nome *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $agendamento->nome) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $agendamento->email) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefone">Telefone *</label>
                                        <input type="text" class="form-control" id="telefone" name="telefone" value="{{ old('telefone', $agendamento->telefone) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="servico">Serviço *</label>
                                        <select class="form-control" id="servico" name="servico" required>
                                            <option value="">Selecione</option>
                                            <option value="Consulta" {{ old('servico', $agendamento->servico) == 'Consulta' ? 'selected' : '' }}>Consulta</option>
                                            <option value="Exame" {{ old('servico', $agendamento->servico) == 'Exame' ? 'selected' : '' }}>Exame</option>
                                            <option value="Procedimento" {{ old('servico', $agendamento->servico) == 'Procedimento' ? 'selected' : '' }}>Procedimento</option>
                                            <option value="Retorno" {{ old('servico', $agendamento->servico) == 'Retorno' ? 'selected' : '' }}>Retorno</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="data_agendamento">Data *</label>
                                        <input type="date" class="form-control" id="data_agendamento" name="data_agendamento" value="{{ old('data_agendamento', $agendamento->data_agendamento->format('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="horario">Horário inicial *</label>
                                        <select class="form-control" id="horario" name="horario" required>
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
                                        <select class="form-control" id="horario_final" name="horario_final" required>
                                            <option value="">Selecione</option>
                                        </select>
                                        @error('horario_final')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="pendente" {{ old('status', $agendamento->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                            <option value="confirmado" {{ old('status', $agendamento->status) == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                                            <option value="cancelado" {{ old('status', $agendamento->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="motivo_consulta">Descrição</label>
                                        <textarea class="form-control" id="motivo_consulta" name="motivo_consulta" rows="4">{{ old('motivo_consulta', $agendamento->motivo_consulta ?: $agendamento->descricao) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Atualizar</button>
                                <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var appointmentDateInput = document.getElementById('data_agendamento');
        var startTimeInput = document.getElementById('horario');
        var endTimeInput = document.getElementById('horario_final');
        var durationInput = document.getElementById('duracao_minutos');
        var clinicHours = @json($clinicHours ?? null);
        var occupiedAppointments = @json($occupiedAppointments ?? []);
        var initialStartTime = '{{ $startTimeValue }}';
        var initialEndTime = '{{ $endTimeValue }}';
        var currentProfessionalId = '{{ $agendamento->professional_id }}';
        var currentProfessionalName = @json($agendamento->professional?->nome ?: $agendamento->medico);

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

        function fillTimeSelect(selectElement, startMinutes, endMinutes, selectedValue, optionStateResolver) {
            if (!selectElement) return;

            selectElement.innerHTML = '';

            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Selecione';
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

        function occupiedIntervalsForSelection() {
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';

            return occupiedAppointments.filter(function(item) {
                return selectedDate
                    && String(item.date || '') === String(selectedDate)
                    && (
                        (currentProfessionalId && String(item.professional_id || '') === String(currentProfessionalId))
                        || (!currentProfessionalId && String(item.professional_name || '') === String(currentProfessionalName || ''))
                    );
            }).map(function(item) {
                return {
                    start: minutesFromTime(item.start_time),
                    end: minutesFromTime(item.end_time)
                };
            }).filter(function(item) {
                return item.start !== null && item.end !== null;
            });
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
            var startMinimum = clinicOpeningMinutes();
            var endMaximum = clinicClosingMinutes();
            var selectedDate = appointmentDateInput ? appointmentDateInput.value : '';
            var occupiedIntervals = occupiedIntervalsForSelection();
            var currentDuration = parseInt(durationInput ? durationInput.value : '30', 10);

            if (isNaN(currentDuration) || currentDuration <= 0) {
                currentDuration = 30;
            }

            if (selectedDate === currentDateValue()) {
                var currentMinutes = minutesFromTime(currentTimeValue());
                if (currentMinutes !== null) {
                    startMinimum = Math.max(startMinimum, currentMinutes);
                }
            }

            fillTimeSelect(startTimeInput, startMinimum, endMaximum, startTimeInput.value || initialStartTime, function(time, minutes) {
                var slotEnd = minutes + currentDuration;
                var overlapsOccupied = overlapsOccupiedRange(minutes, slotEnd, occupiedIntervals);

                return {
                    hidden: overlapsClinicRestriction(minutes, slotEnd),
                    disabled: overlapsOccupied || slotEnd > endMaximum,
                    reason: 'ocupado'
                };
            });

            var selectedStartMinutes = minutesFromTime(startTimeInput.value || initialStartTime);
            var endMinimum = selectedStartMinutes !== null ? selectedStartMinutes + 1 : startMinimum;

            fillTimeSelect(endTimeInput, endMinimum, endMaximum, endTimeInput.value || initialEndTime, function(time, minutes) {
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

        function updateDurationFromTimeRange() {
            if (!startTimeInput || !endTimeInput || !durationInput) {
                return;
            }

            var startValue = startTimeInput.value;
            var endValue = endTimeInput.value;

            if (!startValue || !endValue) {
                return;
            }

            var startParts = startValue.split(':');
            var endParts = endValue.split(':');

            if (startParts.length < 2 || endParts.length < 2) {
                return;
            }

            var startMinutes = (parseInt(startParts[0], 10) * 60) + parseInt(startParts[1], 10);
            var endMinutes = (parseInt(endParts[0], 10) * 60) + parseInt(endParts[1], 10);

            if (isNaN(startMinutes) || isNaN(endMinutes) || endMinutes <= startMinutes) {
                return;
            }

            durationInput.value = String(endMinutes - startMinutes);
        }

        if (startTimeInput) {
            startTimeInput.addEventListener('change', function() {
                updateTimeOptions();
                updateDurationFromTimeRange();
            });
        }

        if (appointmentDateInput) {
            appointmentDateInput.addEventListener('change', updateTimeOptions);
        }

        if (endTimeInput) {
            endTimeInput.addEventListener('change', updateDurationFromTimeRange);
        }

        updateTimeOptions();
        updateDurationFromTimeRange();
    });
</script>
@endsection
