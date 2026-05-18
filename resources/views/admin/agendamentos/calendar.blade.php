@extends('admin.layouts.master')
@section('content')
<style>
    .calendar-shell {
        border: 1px solid #d2dbe6;
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(244,249,255,.96));
        box-shadow: inset 0 0 0 1px #d2dbe6, 0 16px 34px rgba(18, 58, 99, 0.08);
        overflow: hidden;
    }

    html[data-theme="dark"] .calendar-shell {
        border-color: #000000;
        background: linear-gradient(180deg, rgba(22,40,59,.98), rgba(19,33,49,.98));
        box-shadow: inset 0 0 0 1px #000000, 0 22px 44px rgba(2, 8, 15, 0.34);
    }

    .calendar-shell .form-control-sm {
        min-height: 40px;
        border-radius: 999px;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .calendar-board {
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }

    .calendar-board > #calendar {
        flex: 1 1 auto;
        min-width: 0;
    }

    .calendar-clinic-sidebar {
        flex: 0 0 188px;
        position: sticky;
        top: 14px;
        z-index: 3;
    }

    .calendar-clinic-sidebar-card {
        padding: 14px 14px 12px;
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(243, 249, 255, 0.98) 0%, rgba(232, 242, 252, 0.98) 100%);
        border: 1px solid #d2dbe6;
        box-shadow: inset 0 0 0 1px #d2dbe6, 0 12px 28px rgba(15, 61, 107, 0.08);
    }

    .calendar-clinic-sidebar-kicker {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #6a879f;
    }

    .calendar-clinic-sidebar-range {
        margin-top: 7px;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.1;
        color: #1a4f7b;
        white-space: nowrap;
    }

    .calendar-clinic-sidebar-break {
        margin-top: 8px;
        padding: 7px 10px;
        border-radius: 12px;
        background: rgba(23, 111, 190, 0.08);
        color: #35536e;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.35;
        white-space: nowrap;
    }

    .calendar-clinic-sidebar-break-muted {
        background: rgba(95, 115, 136, 0.08);
        color: #5f7388;
    }

    .calendar-clinic-sidebar-hours {
        margin: 12px 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 8px;
    }

    .calendar-clinic-sidebar-hour {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(23, 111, 190, 0.12);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
        color: #35536e;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
    }

    .calendar-clinic-sidebar-hour.is-break {
        background: linear-gradient(180deg, rgba(255, 243, 214, 0.96) 0%, rgba(255, 236, 193, 0.94) 100%);
        border-color: rgba(223, 159, 32, 0.2);
        color: #8a5a00;
    }

    .calendar-clinic-sidebar-hour.is-disabled {
        cursor: default;
        transform: none !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
    }

    .calendar-clinic-sidebar-hour.is-unavailable {
        background: rgba(243, 246, 250, 0.9);
        border-style: dashed;
        color: #8aa0b5;
    }

    .calendar-clinic-sidebar-hour:hover,
    .calendar-clinic-sidebar-hour:focus {
        transform: translateX(2px);
        border-color: rgba(23, 111, 190, 0.24);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7), 0 8px 16px rgba(15, 61, 107, 0.08);
        outline: none;
    }

    .calendar-clinic-sidebar-hour.is-disabled:hover,
    .calendar-clinic-sidebar-hour.is-disabled:focus {
        border-color: rgba(23, 111, 190, 0.12);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
        outline: none;
    }

    .calendar-clinic-sidebar-hour.is-active {
        background: linear-gradient(180deg, rgba(23, 111, 190, 0.12) 0%, rgba(23, 111, 190, 0.08) 100%);
        border-color: rgba(23, 111, 190, 0.28);
        color: #176fbe;
        box-shadow: inset 0 0 0 1px rgba(23, 111, 190, 0.12), 0 10px 18px rgba(15, 61, 107, 0.08);
    }

    .calendar-sidebar-hour-highlight {
        box-shadow: 0 0 0 3px rgba(23, 111, 190, 0.22), 0 0 22px rgba(23, 111, 190, 0.18) !important;
        transform: translateY(-1px);
        z-index: 6;
    }
    .calendar-sidebar-hour-hidden {
        display: none !important;
    }

    .calendar-shell .form-control-sm:hover {
        border-color: rgba(23, 111, 190, 0.28);
    }

    .calendar-shell .form-control-sm:focus {
        border-color: rgba(23, 111, 190, 0.4) !important;
        box-shadow: 0 0 0 3px rgba(23, 111, 190, 0.12) !important;
    }

    .calendar-shell .form-control-sm.calendar-filter-required {
        border-color: #dc3545 !important;
        background: linear-gradient(180deg, rgba(255, 241, 243, 0.98), rgba(255, 233, 236, 0.98));
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.18) !important;
    }

    .calendar-shell select.form-control-sm {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 42px;
        background-image: linear-gradient(45deg, transparent 50%, #5f7388 50%), linear-gradient(135deg, #5f7388 50%, transparent 50%);
        background-position: calc(100% - 18px) calc(50% - 2px), calc(100% - 12px) calc(50% - 2px);
        background-size: 6px 6px, 6px 6px;
        background-repeat: no-repeat;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Calendário</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Agendamentos</div>
            <div class="breadcrumb-item">Calendário</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card calendar-shell">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Calendário</h4>
                        <div class="card-header-action d-flex align-items-center flex-wrap" style="gap: 10px;">
                            <div>
                                <label for="calendar-date-filter" class="sr-only">Filtrar data</label>
                                <input type="date" id="calendar-date-filter" class="form-control form-control-sm" value="{{ $selectedCalendarDate ?? '' }}">
                            </div>
                            @if(!($hideProfessionalFilter ?? false))
                                <div>
                                    <label for="calendar-professional-filter" class="sr-only">Filtrar profissional</label>
                                    <select id="calendar-professional-filter" class="form-control form-control-sm">
                                        <option value="" data-all-professionals="true">Todos os profissionais</option>
                                        @foreach(($professionalOptions ?? []) as $professionalOption)
                                            <option value="{{ $professionalOption['id'] }}" {{ (string) ($selectedProfessionalId ?? '') === (string) $professionalOption['id'] ? 'selected' : '' }}>
                                                {{ $professionalOption['nome'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small id="calendar-professional-help" class="text-danger d-none mt-1">Selecione aqui o profissional para usar Semana e Dia.</small>
                                </div>
                            @endif
                            <div>
                                <label for="calendar-procedure-filter" class="sr-only">Filtrar procedimento</label>
                                <select id="calendar-procedure-filter" class="form-control form-control-sm">
                                    <option value="">Todos os procedimentos</option>
                                </select>
                            </div>
                            @if(!($hideProfessionalFilter ?? false))
                                <a href="{{ route('admin.agendamentos.create', ['return_to' => $returnUrl]) }}" class="btn btn-primary btn-sm">Novo Agendamento</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="calendar-legend">
                            <span class="calendar-legend-item"><i class="calendar-month-event-status-dot calendar-month-event-status-dot-confirmado"></i>Confirmado</span>
                            <span class="calendar-legend-item"><i class="calendar-month-event-status-dot calendar-month-event-status-dot-pendente"></i>Pendente</span>
                            <span class="calendar-legend-item"><i class="calendar-month-event-status-dot calendar-month-event-status-dot-finalizado"></i>Finalizado</span>
                        </div>

                        <div class="calendar-board">
                            <aside id="calendar-clinic-sidebar" class="calendar-clinic-sidebar d-none" aria-label="Horários da clínica"></aside>
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<link rel="stylesheet" href="{{ asset('backend/assets/modules/fullcalendar/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/modules/fullcalendar/fullcalendar.print.min.css') }}" media="print">

<script>
    var calendarEventsUrl = '{{ route('admin.agendamentos.calendar.events') }}';
    var fullCalendarScriptUrl = '{{ asset('backend/assets/modules/fullcalendar/fullcalendar.min.js') }}';
    var fullCalendarLocaleScriptUrl = '{{ asset('backend/assets/modules/fullcalendar/locale/pt-br.js') }}';
    var clinicOpeningTime = @json(($clinicHours['opening_time'] ?? '07:00') . ':00');
    var calendarInitialScrollTime = @json(($clinicHours['opening_time'] ?? '07:00') . ':00');
    var clinicClosingTime = @json(($clinicHours['closing_time'] ?? '19:00') . ':00');
    var clinicClosingDisplayTime = @json(optional(\Carbon\Carbon::createFromFormat('H:i', $clinicHours['closing_time'] ?? '19:00')->addHour())->format('H:i:s'));
    var clinicLunchStartTime = @json(!empty($clinicHours['lunch_start_time']) ? $clinicHours['lunch_start_time'] . ':00' : null);
    var clinicLunchEndTime = @json(!empty($clinicHours['lunch_end_time']) ? $clinicHours['lunch_end_time'] . ':00' : null);
    var calendarRequestedView = @json(request('calendar_view'));
    var calendarFocusDate = @json(request('focus_date') ?: ($selectedCalendarDate ?? null));
    var calendarOpenAppointmentId = @json(request('open_agendamento'));
    var calendarShouldShowDetails = @json((bool) request('show_details'));
    var selectedCalendarDate = @json($selectedCalendarDate ?? '');
    var selectedProcedureId = @json($selectedProcedureId ?? '');
    var selectedCalendarProfessionalId = @json((string) ($selectedProfessionalId ?? ''));
    var calendarProcedureOptions = @json($procedureOptions ?? []);
    var calendarProfessionalOptions = @json($professionalOptions ?? []);
    var calendarViewStorageKey = 'admin.agendamentos.calendar.view';
    var appointmentShowBaseUrl = '{{ url('admin/agendamentos') }}';
    var appointmentEditBaseUrl = '{{ url('admin/agendamentos') }}';
    var appointmentReturnUrl = @json(url()->full());
    var calendarCanEditAppointments = @json(optional(auth()->user())->canMutateOutsideCadastrosBase());

    document.addEventListener('DOMContentLoaded', function() {
        function loadScript(src, onLoad, onError) {
            var script = document.createElement('script');
            script.src = src;
            script.onload = onLoad;
            script.onerror = onError;
            document.body.appendChild(script);
        }

        function initializeCalendar() {
            if (!window.jQuery || !window.jQuery.fn || typeof window.jQuery.fn.fullCalendar !== 'function') {
                document.getElementById('calendar').innerHTML = '<div class="alert alert-danger mb-0">Não foi possível inicializar o calendário.</div>';
                return;
            }

            if (window.moment && typeof window.moment.locale === 'function') {
                window.moment.locale('pt-br');
            }

            var calendarEl = window.jQuery('#calendar');
            var clinicSidebar = document.getElementById('calendar-clinic-sidebar');
            var professionalFilter = document.getElementById('calendar-professional-filter');
            var professionalHelp = document.getElementById('calendar-professional-help');
            var procedureFilter = document.getElementById('calendar-procedure-filter');
            var dateFilter = document.getElementById('calendar-date-filter');
            var pendingAutoOpenId = calendarOpenAppointmentId ? String(calendarOpenAppointmentId) : '';
            var hasAutoOpenedAppointment = false;
            var calendarLayoutSyncInProgress = false;
            if (!calendarEl.length) return;

            function normalizeStatusClass(statusLabel) {
                var normalized = String(statusLabel || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');

                if (normalized === 'concluido') {
                    return 'finalizado';
                }

                return normalized || 'pendente';
            }

            function refreshProcedureFilterOptions() {
                if (!procedureFilter) {
                    return;
                }

                var selectedProfessional = professionalFilter
                    ? String(professionalFilter.value || '')
                    : String(selectedCalendarProfessionalId || '');
                var currentValue = String(procedureFilter.value || selectedProcedureId || '');
                var procedureOptions = Array.isArray(calendarProcedureOptions) ? calendarProcedureOptions : [];
                var filteredOptions = procedureOptions.filter(function(procedure) {
                    if (!selectedProfessional) {
                        return true;
                    }

                    return String(procedure.professional_id || '') === selectedProfessional;
                });

                procedureFilter.innerHTML = '';

                var placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = selectedProfessional ? 'Todos os procedimentos do profissional' : 'Todos os procedimentos';
                procedureFilter.appendChild(placeholder);

                filteredOptions.forEach(function(procedure) {
                    var option = document.createElement('option');
                    option.value = procedure.id || '';
                    option.textContent = procedure.nome || '';

                    if (String(option.value) === currentValue) {
                        option.selected = true;
                    }

                    procedureFilter.appendChild(option);
                });

                if (currentValue && !filteredOptions.some(function(procedure) {
                    return String(procedure.id || '') === currentValue;
                })) {
                    procedureFilter.value = '';
                }
            }

            function clearProfessionalWarning() {
                if (professionalFilter) {
                    professionalFilter.classList.remove('calendar-filter-required');
                }

                if (professionalHelp) {
                    professionalHelp.classList.add('d-none');
                }
            }

            function highlightProfessionalField() {
                if (!professionalFilter) {
                    return;
                }

                professionalFilter.classList.add('calendar-filter-required');

                if (professionalHelp) {
                    professionalHelp.classList.remove('d-none');
                }

                professionalFilter.focus();
            }

            function syncProfessionalFilterByView(viewName) {
                if (!professionalFilter) {
                    return;
                }

                var allProfessionalsOption = professionalFilter.querySelector('[data-all-professionals="true"]');

                if (!allProfessionalsOption) {
                    return;
                }

                if (viewName === 'month') {
                    allProfessionalsOption.hidden = false;
                    allProfessionalsOption.disabled = false;
                    return;
                }

                allProfessionalsOption.hidden = true;
                allProfessionalsOption.disabled = true;
            }

            function hasSelectedProfessional() {
                if (!professionalFilter) {
                    return String(selectedCalendarProfessionalId || '').trim() !== '';
                }

                return String(professionalFilter.value || '').trim() !== '';
            }

            function showProfessionalSelectionMessage() {
                if (!professionalFilter) {
                    return;
                }

                var message = 'Selecione um profissional para visualizar o calendário nos modos Semana e Dia.';

                highlightProfessionalField();
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'info',
                        title: 'Selecione um profissional',
                        text: message,
                        confirmButtonText: 'Entendi',
                        confirmButtonColor: '#176fbe'
                    });

                    return;
                }

                window.alert(message);
            }

            function ensureDetailedViewAccess(targetView) {
                syncProfessionalFilterByView(targetView);
                if ((targetView !== 'compactWeek' && targetView !== 'compactDay') || hasSelectedProfessional()) {
                    return true;
                }

                showProfessionalSelectionMessage();
                calendarEl.fullCalendar('changeView', 'month');
                return false;
            }

            function buildAppointmentDetailsHtml(event) {
                return '<div class="agendamento-details text-left">' +
                    '<p><strong>Nome:</strong> ' + (event.nome || '-') + '</p>' +
                    '<p><strong>Médico:</strong> ' + (event.medico || '-') + '</p>' +
                    '<p><strong>Email:</strong> ' + (event.email || '-') + '</p>' +
                    '<p><strong>Telefone:</strong> ' + (event.telefone || '-') + '</p>' +
                    '<p><strong>Serviço:</strong> ' + (event.servico || '-') + '</p>' +
                    '<p><strong>Motivo:</strong> ' + (event.motivo || '-') + '</p>' +
                    '<p><strong>Horário:</strong> ' + (event.horario || '-') + ' às ' + (event.horario_final || '-') + '</p>' +
                    '<p><strong>Status:</strong> ' + (event.status || '-') + '</p>' +
                    '</div>';
            }

            function showAppointmentInCalendar(appointmentId) {
                if (!appointmentId) {
                    return;
                }

                window.jQuery('.calendar-event-highlight').removeClass('calendar-event-highlight');

                window.setTimeout(function() {
                    var highlightedElements = window.jQuery('[data-agendamento-id="' + appointmentId + '"]');

                    highlightedElements.addClass('calendar-event-highlight');

                    if (highlightedElements.length && highlightedElements.get(0).scrollIntoView) {
                        highlightedElements.get(0).scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                    }
                }, 120);
            }

            function detailsUrlFor(event) {
                return appointmentShowBaseUrl + '/' + event.agendamento_id + '?return_to=' + encodeURIComponent(appointmentReturnUrl);
            }

            function editUrlFor(event) {
                return appointmentEditBaseUrl + '/' + event.agendamento_id + '/edit?return_to=' + encodeURIComponent(appointmentReturnUrl);
            }

            function openAppointmentModal(event) {
                var html = buildAppointmentDetailsHtml(event);

                showAppointmentInCalendar(String(event.agendamento_id || ''));

                if (window.Swal) {
                    var options = {
                        title: 'Detalhes do Agendamento',
                        html: html,
                        showCloseButton: true,
                        showCancelButton: true,
                        cancelButtonText: 'Fechar',
                        confirmButtonText: calendarCanEditAppointments ? 'Editar' : 'Ver detalhes',
                        confirmButtonColor: calendarCanEditAppointments ? '#f39c12' : '#0d6efd',
                        cancelButtonColor: '#6c757d'
                    };

                    if (calendarCanEditAppointments) {
                        options.showDenyButton = true;
                        options.denyButtonText = 'Ver detalhes';
                        options.denyButtonColor = '#0d6efd';
                    }

                    window.Swal.fire(options).then(function(result) {
                        if (result.isConfirmed) {
                            window.location.href = calendarCanEditAppointments ? editUrlFor(event) : detailsUrlFor(event);
                            return;
                        }

                        if (result.isDenied) {
                            window.location.href = detailsUrlFor(event);
                        }
                    });

                    return false;
                }

                window.location.href = detailsUrlFor(event);
                return false;
            }

            function shortProfessionalName(name) {
                var value = String(name || '').trim();

                if (!value) {
                    return 'Equipe';
                }

                var parts = value.split(/\s+/).filter(Boolean);

                if (parts.length === 1) {
                    return parts[0];
                }

                return parts[0] + ' ' + parts[parts.length - 1].charAt(0) + '.';
            }

            function formatTimeLabel(timeValue) {
                return String(timeValue || '').slice(0, 5);
            }

            function timeToMinutes(timeValue) {
                var normalized = formatTimeLabel(timeValue);
                var parts = normalized.split(':');

                if (parts.length < 2) {
                    return 0;
                }

                return (parseInt(parts[0], 10) || 0) * 60 + (parseInt(parts[1], 10) || 0);
            }

            function viewDatesForSidebar(view) {
                if (!view) {
                    return [];
                }

                if (view.name === 'compactDay') {
                    return [view.intervalStart ? view.intervalStart.clone() : view.start.clone()];
                }

                if (view.name === 'compactWeek') {
                    var firstDate = view.intervalStart ? view.intervalStart.clone() : view.start.clone();

                    return Array.from({ length: 7 }, function(_, index) {
                        return firstDate.clone().add(index, 'days');
                    });
                }

                return [];
            }

            function buildClinicHoursSidebarHtml(view) {
                var opening = formatTimeLabel(clinicOpeningTime);
                var closing = formatTimeLabel(clinicClosingTime);
                var lunchStart = clinicLunchStartTime ? formatTimeLabel(clinicLunchStartTime) : '';
                var lunchEnd = clinicLunchEndTime ? formatTimeLabel(clinicLunchEndTime) : '';
                var openingMinutes = timeToMinutes(clinicOpeningTime);
                var closingMinutes = timeToMinutes(clinicClosingTime);
                var hourItems = [];

                for (var minutes = openingMinutes; minutes <= closingMinutes; minutes += 60) {
                    var hour = String(Math.floor(minutes / 60)).padStart(2, '0') + ':00';
                    var hourAvailability = hourAvailabilityForView(minutes, view);
                    var isLunchHour = hourAvailability.isBreak || (lunchStart && lunchEnd && minutes >= timeToMinutes(clinicLunchStartTime) && minutes < timeToMinutes(clinicLunchEndTime));
                    var isClickableHour = hourAvailability.isAvailable && !isLunchHour;
                    var hourClasses = 'calendar-clinic-sidebar-hour';

                    if (isLunchHour) {
                        hourClasses += ' is-break is-disabled';
                    } else if (!hourAvailability.isAvailable) {
                        hourClasses += ' is-disabled is-unavailable';
                    }

                    hourItems.push(
                        '<li class="calendar-clinic-sidebar-hour' + (isLunchHour ? ' is-break' : '') + '" data-hour-start="' + hour + '" tabindex="0" role="button" aria-label="Ver agendamentos às ' + hour + '">' +
                            '<span>' + hour + '</span>' +
                        '</li>'
                    );
                    hourItems[hourItems.length - 1] =
                        '<li class="' + hourClasses + '" data-hour-start="' + hour + '"' +
                            (isClickableHour ? ' tabindex="0" role="button" aria-label="Ver agendamentos às ' + hour + '"' : ' aria-disabled="true"') + '>' +
                            '<span>' + hour + '</span>' +
                        '</li>';
                }

                return '' +
                    '<div class="calendar-clinic-sidebar-card">' +
                        '<div class="calendar-clinic-sidebar-kicker">Horário da clínica</div>' +
                        '<div class="calendar-clinic-sidebar-range">' + opening + ' - ' + closing + '</div>' +
                        (lunchStart && lunchEnd
                            ? '<div class="calendar-clinic-sidebar-break">Intervalo ' + lunchStart + ' - ' + lunchEnd + '</div>'
                            : '<div class="calendar-clinic-sidebar-break calendar-clinic-sidebar-break-muted">Sem intervalo cadastrado</div>') +
                    '</div>' +
                    '<ul class="calendar-clinic-sidebar-hours">' + hourItems.join('') + '</ul>';
            }

            function compactWeekAvailabilityMeta(eventCount) {
                if (eventCount === 0) {
                    return {
                        label: 'Livre',
                        className: 'is-open'
                    };
                }

                if (eventCount <= 3) {
                    return {
                        label: 'Disponível',
                        className: 'is-medium'
                    };
                }

                return {
                    label: 'Movimento alto',
                    className: 'is-busy'
                };
            }

            function buildCompactWeekEventCountMap() {
                var eventCountByDate = {};
                var events = calendarEl.fullCalendar('clientEvents') || [];

                events.forEach(function(event) {
                    if (!event.start || typeof event.start.format !== 'function') {
                        return;
                    }

                    var dateKey = event.start.format('YYYY-MM-DD');
                    eventCountByDate[dateKey] = (eventCountByDate[dateKey] || 0) + 1;
                });

                return eventCountByDate;
            }

            function buildCompactWeekOccupiedMinutesMap() {
                var occupiedMinutesByDate = {};
                var events = calendarEl.fullCalendar('clientEvents') || [];

                events.forEach(function(event) {
                    if (!event.start || !event.end || typeof event.start.format !== 'function' || typeof event.end.diff !== 'function') {
                        return;
                    }

                    var dateKey = event.start.format('YYYY-MM-DD');
                    occupiedMinutesByDate[dateKey] = (occupiedMinutesByDate[dateKey] || 0) + Math.max(0, event.end.diff(event.start, 'minutes'));
                });

                return occupiedMinutesByDate;
            }

            function selectedProfessionalSchedules() {
                var selectedProfessionalId = professionalFilter
                    ? String(professionalFilter.value || '').trim()
                    : String(selectedCalendarProfessionalId || '').trim();

                if (!selectedProfessionalId) {
                    return [];
                }

                var professional = (Array.isArray(calendarProfessionalOptions) ? calendarProfessionalOptions : []).find(function(option) {
                    return String(option.id || '') === selectedProfessionalId;
                });

                return professional && Array.isArray(professional.schedules) ? professional.schedules : [];
            }

            function scheduleIntervalsForView(view) {
                var schedules = selectedProfessionalSchedules();
                var visibleDates = viewDatesForSidebar(view);
                var intervals = [];

                visibleDates.forEach(function(dateMoment) {
                    var isoDay = dateMoment.isoWeekday();

                    schedules.forEach(function(schedule) {
                        if (Number(schedule.day_of_week || 0) !== isoDay) {
                            return;
                        }

                        var startMinutes = timeToMinutes(schedule.start_time || '');
                        var endMinutes = timeToMinutes(schedule.end_time || '');

                        if (endMinutes > startMinutes) {
                            intervals.push({
                                start: startMinutes,
                                end: endMinutes,
                                isBreak: false
                            });
                        }

                        if (schedule.break_start_time && schedule.break_end_time) {
                            var breakStartMinutes = timeToMinutes(schedule.break_start_time);
                            var breakEndMinutes = timeToMinutes(schedule.break_end_time);

                            if (breakEndMinutes > breakStartMinutes) {
                                intervals.push({
                                    start: breakStartMinutes,
                                    end: breakEndMinutes,
                                    isBreak: true
                                });
                            }
                        }
                    });
                });

                return intervals;
            }

            function hourAvailabilityForView(hourStartMinutes, view) {
                var intervals = scheduleIntervalsForView(view);
                var hourEndMinutes = hourStartMinutes + 60;
                var hasWorkCoverage = false;
                var isBreakHour = false;

                intervals.forEach(function(interval) {
                    if (interval.start < hourEndMinutes && interval.end > hourStartMinutes) {
                        if (interval.isBreak) {
                            isBreakHour = true;
                            return;
                        }

                        hasWorkCoverage = true;
                    }
                });

                return {
                    isAvailable: hasWorkCoverage,
                    isBreak: isBreakHour
                };
            }

            function syncCalendarStateInUrl() {
                if (!window.history || !window.history.replaceState || !window.URL) {
                    return;
                }

                try {
                    var nextUrl = new window.URL(window.location.href);
                    var currentView = calendarEl.fullCalendar('getView');
                    var currentViewName = currentView ? currentView.name : resolveInitialCalendarView();
                    var currentDateValue = dateFilter ? String(dateFilter.value || '') : '';
                    var currentProfessionalValue = professionalFilter
                        ? String(professionalFilter.value || '')
                        : String(selectedCalendarProfessionalId || '');
                    var currentProcedureValue = procedureFilter ? String(procedureFilter.value || '') : '';

                    if (currentViewName === 'month' || currentViewName === 'compactWeek' || currentViewName === 'compactDay') {
                        nextUrl.searchParams.set('calendar_view', currentViewName);
                    } else {
                        nextUrl.searchParams.delete('calendar_view');
                    }

                    if (currentProfessionalValue) {
                        nextUrl.searchParams.set('professional_id', currentProfessionalValue);
                    } else {
                        nextUrl.searchParams.delete('professional_id');
                    }

                    if (currentProcedureValue) {
                        nextUrl.searchParams.set('procedure_id', currentProcedureValue);
                    } else {
                        nextUrl.searchParams.delete('procedure_id');
                    }

                    if (currentDateValue) {
                        nextUrl.searchParams.set('calendar_date', currentDateValue);
                    } else {
                        nextUrl.searchParams.delete('calendar_date');
                    }

                    window.history.replaceState({}, '', nextUrl.toString());
                } catch (error) {
                    return;
                }
            }

            function scheduleCoverageForDate(dateMoment) {
                var isoDay = dateMoment.isoWeekday();
                var schedules = selectedProfessionalSchedules().filter(function(schedule) {
                    return Number(schedule.day_of_week || 0) === isoDay;
                });

                if (!schedules.length) {
                    return null;
                }

                var totalMinutes = schedules.reduce(function(total, schedule) {
                    var startMinutes = timeToMinutes(schedule.start_time || '');
                    var endMinutes = timeToMinutes(schedule.end_time || '');
                    var breakStartMinutes = schedule.break_start_time ? timeToMinutes(schedule.break_start_time) : null;
                    var breakEndMinutes = schedule.break_end_time ? timeToMinutes(schedule.break_end_time) : null;
                    var scheduleMinutes = Math.max(0, endMinutes - startMinutes);

                    if (breakStartMinutes !== null && breakEndMinutes !== null && breakEndMinutes > breakStartMinutes) {
                        scheduleMinutes -= Math.max(0, breakEndMinutes - breakStartMinutes);
                    }

                    return total + Math.max(0, scheduleMinutes);
                }, 0);

                return {
                    schedules: schedules,
                    totalMinutes: totalMinutes
                };
            }

            function compactWeekAvailabilityMetaForDate(dateMoment, eventCount, occupiedMinutes) {
                var coverage = scheduleCoverageForDate(dateMoment);

                if (!coverage || coverage.totalMinutes <= 0) {
                    return {
                        label: 'Não atende',
                        className: 'is-off',
                        note: 'Não atende',
                        emptyLabel: 'Indisponível',
                        emptyText: 'Profissional sem atendimento neste dia'
                    };
                }

                var freeMinutes = Math.max(0, coverage.totalMinutes - (occupiedMinutes || 0));
                var occupancyRate = coverage.totalMinutes > 0 ? (occupiedMinutes || 0) / coverage.totalMinutes : 1;

                if (eventCount === 0 || occupiedMinutes === 0) {
                    return {
                        label: 'Livre',
                        className: 'is-open',
                        note: 'Atende',
                        emptyLabel: 'Disponível',
                        emptyText: 'Dia com agenda aberta para atendimento'
                    };
                }

                if (freeMinutes <= 0 || occupancyRate >= 0.98) {
                    return {
                        label: 'Lotado',
                        className: 'is-off',
                        note: 'Atende',
                        emptyLabel: 'Lotado',
                        emptyText: 'Sem horários livres na agenda deste dia'
                    };
                }

                if (occupancyRate >= 0.7) {
                    return {
                        label: 'Poucas vagas',
                        className: 'is-busy',
                        note: 'Atende',
                        emptyLabel: 'Poucas vagas',
                        emptyText: 'Agenda quase cheia para este dia'
                    };
                }

                return {
                    label: 'Disponível',
                    className: 'is-medium',
                    note: 'Atende',
                    emptyLabel: 'Disponível',
                    emptyText: 'Ainda há horários disponíveis neste dia'
                };
            }

            function enhanceCompactWeekAvailability() {
                var currentView = calendarEl.fullCalendar('getView');

                if (!currentView || (currentView.name !== 'compactWeek' && currentView.name !== 'compactDay')) {
                    return;
                }

                var eventCountByDate = buildCompactWeekEventCountMap();
                var occupiedMinutesByDate = buildCompactWeekOccupiedMinutesMap();
                var viewClassName = currentView.name === 'compactDay' ? '.fc-compactDay-view' : '.fc-compactWeek-view';
                var headerCells = calendarEl.find(viewClassName + ' .fc-day-header');
                var contentCells = calendarEl.find(viewClassName + ' .fc-content-skeleton table tbody tr').first().children('td');
                var startDate = currentView.intervalStart ? currentView.intervalStart.clone() : currentView.start.clone();
                var localeData = window.moment && typeof window.moment.localeData === 'function'
                    ? window.moment.localeData('pt-br')
                    : null;

                headerCells.each(function(index, cell) {
                    var currentDate = startDate.clone().add(index, 'days');
                    var dateKey = currentDate.format('YYYY-MM-DD');
                    var eventCount = eventCountByDate[dateKey] || 0;
                    var occupiedMinutes = occupiedMinutesByDate[dateKey] || 0;
                    var meta = compactWeekAvailabilityMetaForDate(currentDate, eventCount, occupiedMinutes);
                    var weekdayLabel = localeData
                        ? localeData.weekdaysShort(currentDate).replace('.', '')
                        : currentDate.format('ddd');
                    var title = weekdayLabel + ' ' + currentDate.format('D/M');

                    window.jQuery(cell).html(
                        '<div class="calendar-compact-week-header">' +
                            '<span class="calendar-compact-week-header-title">' + title + '</span>' +
                            (meta.note ? '<span class="calendar-compact-week-header-note ' + meta.className + '">' + meta.note + '</span>' : '') +
                        '</div>'
                    );
                });

                contentCells.each(function(index, cell) {
                    var container = window.jQuery(cell);

                    container.find('.calendar-compact-week-day-empty').remove();
                });
            }

            function syncClinicSidebar(viewName) {
                if (!clinicSidebar) {
                    return;
                }

                var wasHidden = clinicSidebar.classList.contains('d-none');

                if (viewName === 'compactWeek' || viewName === 'compactDay') {
                    clinicSidebar.innerHTML = buildClinicHoursSidebarHtml(calendarEl.fullCalendar('getView'));
                    clinicSidebar.classList.remove('d-none');

                    if (wasHidden && !calendarLayoutSyncInProgress) {
                        calendarLayoutSyncInProgress = true;
                        window.requestAnimationFrame(function() {
                            calendarEl.fullCalendar('render');
                            calendarLayoutSyncInProgress = false;
                        });
                    }

                    return;
                }

                var wasVisible = !clinicSidebar.classList.contains('d-none');
                clinicSidebar.classList.add('d-none');
                clinicSidebar.innerHTML = '';

                if (wasVisible && !calendarLayoutSyncInProgress) {
                    calendarLayoutSyncInProgress = true;
                    window.requestAnimationFrame(function() {
                        calendarEl.fullCalendar('render');
                        calendarLayoutSyncInProgress = false;
                    });
                }
            }

            function buildMonthEventTitle(event) {
                var patientName = String(event.nome || 'Agendamento').trim();
                var firstName = patientName.split(/\s+/)[0] || patientName;

                return firstName + ' • ' + shortProfessionalName(event.medico || '');
            }

            function buildAgendaEventMarkup(event, options) {
                options = options || {};

                var startTime = String(event.horario || '').trim();
                var endTime = String(event.horario_final || '').trim();
                var status = String(event.status || '').trim();
                var statusClass = normalizeStatusClass(status);
                var service = String(event.servico || 'Consulta').trim();
                var patientName = String(event.nome || 'Paciente').trim();
                var timeLabel = startTime + (endTime ? ' - ' + endTime : '');
                var showPatient = options.showPatient !== false;
                var showStatus = options.showStatus !== false;
                var showService = options.showService !== false;
                var detailsMarkup = '';

                if (showService) {
                    detailsMarkup += '<div class="calendar-agenda-event-details">';

                    if (showService) {
                        detailsMarkup += '' +
                            '<div class="calendar-agenda-event-meta calendar-agenda-event-meta-service">' +
                                '<span class="calendar-agenda-event-label">Procedimento</span>' +
                                '<span class="calendar-agenda-event-value">' + service + '</span>' +
                            '</div>';
                    }

                    detailsMarkup += '</div>';
                }

                return '' +
                    '<div class="calendar-agenda-event-card">' +
                        '<div class="calendar-agenda-event-topline">' +
                            '<div class="calendar-agenda-event-time">' + timeLabel + '</div>' +
                            (showPatient ? '<div class="calendar-agenda-event-patient">' + patientName + '</div>' : '') +
                            (showStatus ? '<div class="calendar-agenda-event-status"><span class="calendar-agenda-event-status-dot calendar-agenda-event-status-dot-' + statusClass + '"></span>' + status + '</div>' : '') +
                        '</div>' +
                        detailsMarkup +
                    '</div>';
            }

            function readStoredCalendarView() {
                var requestedView = String(calendarRequestedView || '').trim();

                if (requestedView === 'agendaWeek') {
                    return 'compactWeek';
                }

                if (requestedView === 'agendaDay') {
                    return 'compactDay';
                }

                if (requestedView === 'month' || requestedView === 'compactWeek' || requestedView === 'compactDay') {
                    return requestedView;
                }

                try {
                    var storedView = window.localStorage.getItem(calendarViewStorageKey);

                    if (storedView === 'agendaWeek') {
                        return 'compactWeek';
                    }

                    if (storedView === 'agendaDay') {
                        return 'compactDay';
                    }

                    if (storedView === 'month' || storedView === 'compactWeek' || storedView === 'compactDay') {
                        return storedView;
                    }
                } catch (error) {
                    return null;
                }

                return null;
            }

            function persistCalendarView(viewName) {
                try {
                    if (viewName === 'month' || viewName === 'compactWeek' || viewName === 'compactDay') {
                        window.localStorage.setItem(calendarViewStorageKey, viewName);
                    }
                } catch (error) {
                }

                syncCalendarStateInUrl();
            }

            function resolveInitialCalendarView() {
                var storedView = readStoredCalendarView();

                if (storedView === 'month') {
                    return 'month';
                }

                if (storedView === 'compactWeek' || storedView === 'compactDay') {
                    return hasSelectedProfessional() ? storedView : 'month';
                }

                return hasSelectedProfessional() ? 'compactWeek' : 'month';
            }

            function applyStoredCalendarView() {
                var targetView = resolveInitialCalendarView();
                var currentView = calendarEl.fullCalendar('getView');

                if (!currentView || currentView.name === targetView) {
                    return;
                }

                if ((targetView === 'compactWeek' || targetView === 'compactDay') && !hasSelectedProfessional()) {
                    return;
                }

                calendarEl.fullCalendar('changeView', targetView);
            }

            function clearSidebarHourFilter() {
                clinicSidebar.querySelectorAll('.calendar-clinic-sidebar-hour').forEach(function(item) {
                    item.classList.remove('is-active');
                });

                window.jQuery('.calendar-sidebar-hour-highlight').removeClass('calendar-sidebar-hour-highlight');
                window.jQuery('.calendar-sidebar-hour-hidden').removeClass('calendar-sidebar-hour-hidden');
            }

            function showAppointmentsForSidebarHour(hourLabel) {
                var currentView = calendarEl.fullCalendar('getView');

                if (!currentView || (currentView.name !== 'compactWeek' && currentView.name !== 'compactDay')) {
                    return;
                }

                var activeSidebarHour = clinicSidebar.querySelector('.calendar-clinic-sidebar-hour.is-active');

                if (activeSidebarHour && activeSidebarHour.getAttribute('data-hour-start') === hourLabel) {
                    clearSidebarHourFilter();
                    return;
                }

                var hourStartMinutes = timeToMinutes(hourLabel);
                var hourEndMinutes = hourStartMinutes + 60;
                var matchingEvents = (calendarEl.fullCalendar('clientEvents') || []).filter(function(event) {
                    if (!event.start || !event.end || typeof event.start.hours !== 'function' || typeof event.end.hours !== 'function') {
                        return false;
                    }

                    var eventStartMinutes = event.start.hours() * 60 + event.start.minutes();
                    var eventEndMinutes = event.end.hours() * 60 + event.end.minutes();

                    return eventStartMinutes < hourEndMinutes && eventEndMinutes > hourStartMinutes;
                });

                clinicSidebar.querySelectorAll('.calendar-clinic-sidebar-hour').forEach(function(item) {
                    item.classList.toggle('is-active', item.getAttribute('data-hour-start') === hourLabel);
                });

                window.jQuery('.calendar-sidebar-hour-highlight').removeClass('calendar-sidebar-hour-highlight');
                window.jQuery('.calendar-sidebar-hour-hidden').removeClass('calendar-sidebar-hour-hidden');

                var matchingIds = matchingEvents.map(function(event) {
                    return String(event.agendamento_id);
                });

                window.jQuery('[data-agendamento-id]').each(function(_, element) {
                    var eventElement = window.jQuery(element);
                    var appointmentId = String(eventElement.attr('data-agendamento-id') || '');
                    var isMatch = matchingIds.indexOf(appointmentId) !== -1;

                    eventElement.toggleClass('calendar-sidebar-hour-highlight', isMatch);
                    eventElement.toggleClass('calendar-sidebar-hour-hidden', !isMatch);
                });

                matchingEvents.forEach(function(event) {
                    window.jQuery('[data-agendamento-id="' + event.agendamento_id + '"]').addClass('calendar-sidebar-hour-highlight');
                });
            }

            function buildMonthEventMarkup(event) {
                var startTime = String(event.horario || '').trim();
                var patientName = String(event.nome || 'Paciente').trim();
                var status = String(event.status || '').trim();
                var statusClass = normalizeStatusClass(status);

                return '' +
                    '<div class="calendar-month-event-layout">' +
                        '<div class="calendar-month-event-row">' +
                            '<div class="calendar-month-event-time">' + startTime + '</div>' +
                            '<div class="calendar-month-event-name">' + patientName + '</div>' +
                        '</div>' +
                        '<div class="calendar-month-event-status"><span class="calendar-month-event-status-dot calendar-month-event-status-dot-' + statusClass + '"></span>' + status + '</div>' +
                    '</div>';
            }

            calendarEl.fullCalendar({
                defaultView: resolveInitialCalendarView(),
                defaultDate: calendarFocusDate || undefined,
                locale: 'pt-br',
                lang: 'pt-br',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,compactWeek,compactDay'
                },
                firstDay: 1,
                height: 'auto',
                allDaySlot: false,
                slotDuration: '00:05:00',
                snapDuration: '00:05:00',
                slotLabelInterval: '01:00:00',
                minTime: clinicOpeningTime,
                maxTime: clinicClosingDisplayTime || clinicClosingTime,
                scrollTime: calendarInitialScrollTime,
                scrollTimeReset: false,
                slotEventOverlap: false,
                eventOverlap: false,
                selectOverlap: false,
                timeFormat: 'H:mm',
                slotLabelFormat: 'H:mm',
                eventLimit: true,
                eventLimitClick: 'popover',
                eventLimitText: 'mais',
                displayEventEnd: true,
                fixedWeekCount: false,
                monthNames: ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'],
                monthNamesShort: ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'],
                dayNames: ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'],
                dayNamesShort: ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'],
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    compactWeek: 'Semana',
                    compactDay: 'Dia'
                },
                views: {
                    month: {
                        titleFormat: 'MMMM [de] YYYY',
                        columnHeaderFormat: 'ddd',
                        eventLimit: 2
                    },
                    compactWeek: {
                        type: 'basicWeek',
                        columnHeaderFormat: 'ddd D/M',
                        eventLimit: false,
                        height: 'auto'
                    },
                    compactDay: {
                        type: 'basicDay',
                        titleFormat: 'dddd, D [de] MMMM [de] YYYY',
                        columnHeaderFormat: 'dddd D/M',
                        eventLimit: false,
                        height: 'auto'
                    }
                },
                events: {
                    url: calendarEventsUrl,
                    type: 'GET',
                    data: function() {
                        return {
                            professional_id: professionalFilter ? professionalFilter.value : selectedCalendarProfessionalId,
                            procedure_id: procedureFilter ? procedureFilter.value : '',
                            calendar_date: dateFilter ? dateFilter.value : '',
                            open_agendamento: pendingAutoOpenId || ''
                        };
                    },
                    error: function() {
                        calendarEl.html('<div class="alert alert-danger mb-0">Não foi possível carregar os eventos do calendário.</div>');
                    }
                },
                eventClick: function(event) {
                    return openAppointmentModal(event);
                },
                eventRender: function(event, element) {
                    var activeView = calendarEl.fullCalendar('getView').name;
                    var eventMinutes = 0;
                    var statusClass = normalizeStatusClass(event.status || '');

                    if (event.start && event.end && typeof event.end.diff === 'function') {
                        eventMinutes = event.end.diff(event.start, 'minutes');
                    }
                    element.attr('data-agendamento-id', event.agendamento_id);
                    element.attr('data-status', statusClass);
                    element.addClass('calendar-status-' + statusClass);
                    element.attr('title', (event.telefone || '') + ' • ' + (event.motivo || ''));

                    if (activeView === 'month') {
                        element.addClass('calendar-month-event-card');
                        element.find('.fc-content').html(buildMonthEventMarkup(event));
                    } else if (activeView === 'compactWeek' || activeView === 'compactDay') {
                        element.addClass('calendar-agenda-event calendar-agenda-event-week');
                        element.find('.fc-content').html(buildAgendaEventMarkup(event, {
                            showPatient: true,
                            showStatus: true,
                            showService: true
                        }));
                    } else {
                        element.addClass('calendar-agenda-event');
                        if (eventMinutes > 0 && eventMinutes <= 120) {
                            element.addClass('calendar-agenda-event-short');
                        }
                        element.find('.fc-content').html(buildAgendaEventMarkup(event));
                    }

                    if (event.is_finalized) {
                        element.addClass('calendar-event-finalized');

                        var titleElement = element.find('.fc-title');

                        if (titleElement.length) {
                            titleElement.append(' • Finalizado');
                        }
                    }

                    if (pendingAutoOpenId && String(event.agendamento_id) === pendingAutoOpenId) {
                        element.addClass('calendar-event-highlight');
                    }
                },
                eventAfterAllRender: function() {
                    var currentView = calendarEl.fullCalendar('getView');

                    enhanceCompactWeekAvailability();

                    if (!pendingAutoOpenId || hasAutoOpenedAppointment === true || !calendarShouldShowDetails) {
                        return;
                    }

                    var targetEvent = calendarEl.fullCalendar('clientEvents', function(event) {
                        return String(event.agendamento_id) === pendingAutoOpenId;
                    });

                    if (!targetEvent.length) {
                        return;
                    }

                    hasAutoOpenedAppointment = true;
                    showAppointmentInCalendar(pendingAutoOpenId);
                    openAppointmentModal(targetEvent[0]);
                },
                viewRender: function(view) {
                    persistCalendarView(view.name);
                    syncClinicSidebar(view.name);
                    ensureDetailedViewAccess(view.name);
                }
            });

            window.setTimeout(function() {
                applyStoredCalendarView();
            }, 0);

            if (professionalFilter) {
                professionalFilter.addEventListener('change', function() {
                    clearProfessionalWarning();
                    selectedProcedureId = '';
                    refreshProcedureFilterOptions();
                    syncCalendarStateInUrl();
                    var currentView = calendarEl.fullCalendar('getView');
                    if (currentView) {
                        syncClinicSidebar(currentView.name);
                    }

                    calendarEl.fullCalendar('refetchEvents');
                });
            }

            if (procedureFilter) {
                refreshProcedureFilterOptions();

                procedureFilter.addEventListener('change', function() {
                    selectedProcedureId = String(procedureFilter.value || '');
                    syncCalendarStateInUrl();
                    var currentView = calendarEl.fullCalendar('getView');
                    if (currentView) {
                        syncClinicSidebar(currentView.name);
                    }
                    calendarEl.fullCalendar('refetchEvents');
                });
            }

            if (dateFilter) {
                dateFilter.addEventListener('change', function() {
                    if (dateFilter.value) {
                        calendarEl.fullCalendar('gotoDate', dateFilter.value);
                    }

                    syncCalendarStateInUrl();
                    var currentView = calendarEl.fullCalendar('getView');
                    if (currentView) {
                        syncClinicSidebar(currentView.name);
                    }
                    calendarEl.fullCalendar('refetchEvents');
                });
            }

            window.jQuery(document).on('click', '.fc-compactWeek-button, .fc-compactDay-button', function() {
                window.setTimeout(function() {
                    var currentView = calendarEl.fullCalendar('getView');

                    if (currentView) {
                        ensureDetailedViewAccess(currentView.name);
                    }
                }, 0);
            });

            clinicSidebar.addEventListener('click', function(event) {
                var target = event.target.closest('.calendar-clinic-sidebar-hour[data-hour-start]');

                if (!target || target.classList.contains('is-disabled')) {
                    return;
                }

                showAppointmentsForSidebarHour(String(target.getAttribute('data-hour-start') || ''));
            });

            clinicSidebar.addEventListener('keydown', function(event) {
                var target = event.target.closest('.calendar-clinic-sidebar-hour[data-hour-start]');

                if (!target || target.classList.contains('is-disabled') || (event.key !== 'Enter' && event.key !== ' ')) {
                    return;
                }

                event.preventDefault();
                showAppointmentsForSidebarHour(String(target.getAttribute('data-hour-start') || ''));
            });
        }

        if (window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.fullCalendar === 'function') {
            if (window.jQuery.fullCalendar && window.jQuery.fullCalendar.locales && window.jQuery.fullCalendar.locales['pt-br']) {
                initializeCalendar();
                return;
            }

            loadScript(fullCalendarLocaleScriptUrl, initializeCalendar, initializeCalendar);
            return;
        }

        loadScript(fullCalendarScriptUrl, function() {
            loadScript(fullCalendarLocaleScriptUrl, initializeCalendar, initializeCalendar);
        }, function() {
            document.getElementById('calendar').innerHTML = '<div class="alert alert-danger mb-0">Não foi possível carregar a biblioteca do calendário.</div>';
        });
    });
</script>

<style>
    .fc { font-family: inherit; }
    .card-header-action .form-control-sm { min-width: 190px; }
    .calendar-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }
    .calendar-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(23, 111, 190, 0.08);
        color: #35536e;
        font-size: 12px;
        font-weight: 700;
    }
    .calendar-legend-item .calendar-month-event-status-dot {
        width: 8px;
        height: 8px;
        margin-top: 0;
        vertical-align: middle;
    }
    .calendar-legend-swatch {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .fc .fc-button-primary { background-color: #007bff; border-color: #007bff; }
    .fc .fc-button-primary:hover { background-color: #0056b3; border-color: #0056b3; }
    .fc .fc-button-primary.fc-button-active { background-color: #0056b3; border-color: #0056b3; }
    .fc .fc-event { cursor: pointer; border-radius: 4px; }
    .fc .fc-event:hover { opacity: 0.85; }
    .fc .calendar-agenda-event {
        position: relative;
        border: 1px solid #111111 !important;
        border-radius: 14px !important;
        box-shadow: 0 10px 22px rgba(15, 61, 107, 0.16);
        padding: 0 !important;
        overflow: hidden;
    }
    .fc .calendar-agenda-event .fc-content {
        padding: 12px 13px;
    }
    .fc .calendar-agenda-event::after,
    .fc-month-view .calendar-month-event-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        pointer-events: none;
        opacity: .48;
        animation: calendarStatusPulse 2.8s ease-in-out infinite;
    }
    .fc .calendar-status-confirmado::after,
    .fc-month-view .calendar-status-confirmado::after {
        box-shadow: inset 0 0 0 1px rgba(40, 167, 69, 0.55), 0 0 16px rgba(40, 167, 69, 0.22);
    }
    .fc .calendar-status-pendente::after,
    .fc-month-view .calendar-status-pendente::after {
        box-shadow: inset 0 0 0 1px rgba(255, 193, 7, 0.62), 0 0 18px rgba(255, 193, 7, 0.24);
    }
    .fc .calendar-status-finalizado::after,
    .fc-month-view .calendar-status-finalizado::after {
        box-shadow: inset 0 0 0 1px rgba(95, 107, 122, 0.62), 0 0 16px rgba(95, 107, 122, 0.24);
    }
    @keyframes calendarStatusPulse {
        0%, 100% {
            opacity: .34;
            transform: scale(1);
        }
        50% {
            opacity: .62;
            transform: scale(1.008);
        }
    }
    .calendar-agenda-event-card {
        display: grid;
        gap: 4px;
        line-height: 1.2;
        height: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }
    .calendar-agenda-event-topline {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .calendar-agenda-event-time {
        display: inline-flex;
        align-items: center;
        flex: 0 0 auto;
        padding: 2px 7px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.2);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .02em;
        line-height: 1.1;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
    }
    .calendar-agenda-event-patient {
        flex: 1 1 auto;
        min-width: 0;
        font-size: 12px;
        font-weight: 800;
        white-space: normal;
        line-height: 1.15;
        word-break: break-word;
    }
    .calendar-agenda-event-details {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, .92fr);
        gap: 6px;
        width: 100%;
    }
    .calendar-agenda-event-details > * {
        min-width: 0;
    }
    .calendar-agenda-event-meta {
        display: grid;
        gap: 3px;
        min-width: 0;
        width: auto;
        max-width: 100%;
        padding: 5px 7px;
        box-sizing: border-box;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.12);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
        overflow: hidden;
    }
    .calendar-agenda-event-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        opacity: .76;
        line-height: 1.3;
    }
    .calendar-agenda-event-value {
        font-size: 10.5px;
        font-weight: 700;
        line-height: 1.15;
        word-break: break-word;
    }
    .calendar-agenda-event-meta-service .calendar-agenda-event-value {
        font-size: 11px;
    }
    .calendar-agenda-event-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        flex: 0 0 auto;
        padding: 2px 7px;
        border-radius: 999px;
        background: rgba(12, 32, 53, 0.16);
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        line-height: 1.1;
        white-space: nowrap;
    }
    .calendar-agenda-event-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        display: inline-block;
        flex: 0 0 auto;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.08);
    }
    .calendar-agenda-event-status-dot-confirmado {
        background: #28a745;
    }
    .calendar-agenda-event-status-dot-pendente {
        background: #ffc107;
    }
    .calendar-agenda-event-status-dot-finalizado {
        background: #5f6b7a;
    }
    #calendar { min-height: 780px; }
    .fc-agendaWeek-view .fc-day-header,
    .fc-agendaWeek-view .fc-widget-header,
    .fc-agendaDay-view .fc-day-header,
    .fc-agendaDay-view .fc-widget-header,
    .fc-month-view .fc-day-header,
    .fc-month-view .fc-widget-header {
        background: linear-gradient(180deg, rgba(244, 249, 255, 0.98) 0%, rgba(233, 242, 252, 0.98) 100%);
        border-bottom: 1px solid rgba(23, 111, 190, 0.16) !important;
    }

    .fc-agendaWeek-view .fc-day-header,
    .fc-agendaDay-view .fc-day-header,
    .fc-month-view .fc-day-header {
        padding: 12px 8px;
        font-weight: 700;
        color: #35536e;
    }

    .fc-agendaWeek-view .fc-day-header:not(:last-child),
    .fc-agendaDay-view .fc-day-header:not(:last-child),
    .fc-month-view .fc-day-header:not(:last-child),
    .fc-agendaWeek-view .fc-widget-content:not(:last-child),
    .fc-agendaDay-view .fc-widget-content:not(:last-child),
    .fc-month-view .fc-day:not(:last-child),
    .fc-agendaWeek-view .fc-time-grid .fc-slats td:not(:last-child),
    .fc-agendaWeek-view .fc-bg td:not(:last-child),
    .fc-month-view .fc-bg td:not(:last-child),
    .fc-month-view .fc-content-skeleton td:not(:last-child) {
        border-right: 1px solid rgba(23, 111, 190, 0.22) !important;
    }

    .fc-agendaWeek-view .fc-day-header:not(:last-child),
    .fc-agendaWeek-view .fc-bg td:not(:last-child),
    .fc-agendaWeek-view .fc-content-skeleton td:not(:last-child),
    .fc-agendaWeek-view .fc-time-grid .fc-slats td:not(:last-child),
    .fc-agendaWeek-view .fc-widget-content:not(:last-child) {
        box-shadow: inset -2px 0 0 rgba(23, 111, 190, 0.22);
    }

    .fc-agendaWeek-view .fc-time-grid .fc-slats .fc-minor td,
    .fc-agendaDay-view .fc-time-grid .fc-slats .fc-minor td {
        border-top-color: transparent !important;
        opacity: .25;
    }

    .fc-agendaWeek-view .fc-axis,
    .fc-agendaDay-view .fc-axis {
        font-size: 12px;
        font-weight: 700;
        color: #5a7186;
        white-space: nowrap;
        position: relative;
        z-index: 6;
        background: rgba(247, 251, 255, 0.98);
        padding-right: 10px;
        text-align: right;
    }

    .fc-agendaWeek-view .fc-slats .fc-minor .fc-axis,
    .fc-agendaDay-view .fc-slats .fc-minor .fc-axis {
        opacity: 0;
    }

    .fc-month-view .fc-day,
    .fc-month-view .fc-widget-content,
    .fc-month-view .fc-bg td {
        background: rgba(255, 255, 255, 0.98);
        border-color: rgba(23, 111, 190, 0.18) !important;
    }

    .fc-month-view .fc-day-top {
        padding: 8px 10px 4px;
        border-bottom: 1px solid rgba(23, 111, 190, 0.1);
        text-align: right;
    }

    .fc-month-view .fc-day-number {
        font-weight: 700;
        color: #35536e;
    }

    .fc-month-view .fc-content-skeleton td {
        padding: 4px 6px 8px;
        vertical-align: top;
    }

    .fc-month-view .fc-day-grid-event,
    .fc-month-view .calendar-month-event-card {
        position: relative;
        margin: 3px 4px 0;
        border-radius: 8px;
        border: 1px solid #111111 !important;
        box-shadow: 0 6px 14px rgba(15, 61, 107, 0.12);
    }

    .fc-month-view .calendar-month-event-card .fc-content {
        display: block;
        padding: 4px 6px 5px;
        line-height: 1.2;
    }

    .calendar-month-event-layout {
        display: grid;
        gap: 3px;
    }

    .calendar-month-event-row {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        align-items: center;
        gap: 6px;
    }

    .calendar-month-event-time {
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .calendar-month-event-name {
        font-size: 10px;
        font-weight: 700;
        text-align: center;
        white-space: normal;
        word-break: break-word;
    }

    .calendar-month-event-status {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        font-size: 9px;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: .06em;
        opacity: .92;
    }
    .calendar-month-event-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        display: inline-block;
        flex: 0 0 auto;
    }
    .calendar-month-event-status-dot-confirmado {
        background: #28a745;
    }
    .calendar-month-event-status-dot-pendente {
        background: #ffc107;
    }
    .calendar-month-event-status-dot-finalizado {
        background: #5f6b7a;
    }

    .fc-month-view .fc-more {
        display: inline-block;
        margin: 4px 6px 2px;
        padding: 3px 8px;
        border-radius: 999px;
        background: rgba(23, 111, 190, 0.1);
        color: #176fbe;
        font-weight: 700;
        font-size: 11px;
    }

    .fc-popover {
        border: 1px solid rgba(23, 111, 190, 0.18);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 34px rgba(15, 61, 107, 0.16);
    }

    .fc-popover .fc-header {
        padding: 10px 14px;
        background: linear-gradient(180deg, rgba(244, 249, 255, 0.98) 0%, rgba(233, 242, 252, 0.98) 100%);
        color: #35536e;
        font-weight: 700;
    }

    .fc-popover .fc-body {
        padding: 8px 6px 10px;
        background: rgba(255, 255, 255, 0.98);
    }

    .fc-month-view .fc-today {
        background: linear-gradient(180deg, rgba(23, 111, 190, 0.12) 0%, rgba(23, 111, 190, 0.06) 100%) !important;
    }

    .fc-compactWeek-view .fc-day-header,
    .fc-compactWeek-view .fc-widget-header,
    .fc-compactDay-view .fc-day-header,
    .fc-compactDay-view .fc-widget-header {
        background: linear-gradient(180deg, rgba(244, 249, 255, 0.98) 0%, rgba(233, 242, 252, 0.98) 100%);
        border-bottom: 1px solid rgba(23, 111, 190, 0.16) !important;
        padding: 12px 8px;
        font-weight: 700;
        color: #35536e;
        box-sizing: border-box;
    }

    .fc-compactWeek-view .fc-day-header,
    .fc-compactDay-view .fc-day-header {
        vertical-align: top;
    }

    .fc-compactWeek-view table,
    .fc-compactWeek-view .fc-row,
    .fc-compactWeek-view .fc-content-skeleton,
    .fc-compactWeek-view .fc-day-grid-container,
    .fc-compactDay-view table,
    .fc-compactDay-view .fc-row,
    .fc-compactDay-view .fc-content-skeleton,
    .fc-compactDay-view .fc-day-grid-container {
        width: 100% !important;
        table-layout: fixed;
        box-sizing: border-box;
    }

    .calendar-compact-week-header {
        display: grid;
        gap: 8px;
        justify-items: center;
        width: 100%;
        min-height: 72px;
        padding: 16px 10px;
        box-sizing: border-box;
    }

    .calendar-compact-week-header-title {
        font-weight: 700;
    }

    .calendar-compact-week-header-note {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        border: 1px solid transparent;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
    }

    .calendar-compact-week-header-note.is-open,
    .calendar-compact-week-header-note.is-medium,
    .calendar-compact-week-header-note.is-busy {
        background: linear-gradient(180deg, rgba(231, 246, 236, 0.98) 0%, rgba(219, 240, 226, 0.98) 100%);
        border-color: rgba(54, 145, 87, 0.18);
        color: #2f7d49;
    }

    .calendar-compact-week-header-note.is-off {
        background: linear-gradient(180deg, rgba(244, 247, 250, 0.98) 0%, rgba(235, 240, 245, 0.98) 100%);
        border-color: rgba(108, 127, 146, 0.22);
        color: #617487;
    }

    .calendar-compact-week-availability {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .calendar-compact-week-availability.is-open {
        background: rgba(40, 167, 69, 0.12);
        border-color: rgba(40, 167, 69, 0.18);
        color: #1f7c35;
    }

    .calendar-compact-week-availability.is-medium {
        background: rgba(23, 111, 190, 0.1);
        border-color: rgba(23, 111, 190, 0.16);
        color: #176fbe;
    }

    .calendar-compact-week-availability.is-busy {
        background: rgba(255, 193, 7, 0.16);
        border-color: rgba(255, 193, 7, 0.22);
        color: #9a6b00;
    }

    .calendar-compact-week-availability.is-off {
        background: rgba(95, 107, 122, 0.14);
        border-color: rgba(95, 107, 122, 0.2);
        color: #526171;
    }

    .fc-compactWeek-view .fc-day-header:not(:last-child),
    .fc-compactWeek-view .fc-widget-content:not(:last-child),
    .fc-compactWeek-view .fc-bg td:not(:last-child),
    .fc-compactWeek-view .fc-content-skeleton td:not(:last-child),
    .fc-compactDay-view .fc-day-header:not(:last-child),
    .fc-compactDay-view .fc-widget-content:not(:last-child),
    .fc-compactDay-view .fc-bg td:not(:last-child),
    .fc-compactDay-view .fc-content-skeleton td:not(:last-child) {
        border-right: 1px solid rgba(23, 111, 190, 0.22) !important;
    }

    .fc-compactWeek-view .fc-day-header,
    .fc-compactDay-view .fc-day-header {
        padding: 0 !important;
        vertical-align: top;
        box-sizing: border-box;
        overflow: hidden;
        position: relative;
        background-clip: padding-box !important;
    }

    .fc-compactWeek-view .fc-day,
    .fc-compactWeek-view .fc-widget-content,
    .fc-compactWeek-view .fc-bg td,
    .fc-compactDay-view .fc-day,
    .fc-compactDay-view .fc-widget-content,
    .fc-compactDay-view .fc-bg td {
        background: rgba(255, 255, 255, 0.98);
        border-color: rgba(23, 111, 190, 0.18) !important;
        vertical-align: top;
        box-sizing: border-box;
    }

    .fc-compactWeek-view .fc-content-skeleton td,
    .fc-compactDay-view .fc-content-skeleton td {
        padding: 8px 8px 10px;
        vertical-align: top;
        box-sizing: border-box;
    }

    .calendar-compact-week-day-empty {
        margin: 10px 6px 0;
        padding: 14px 12px;
        border-radius: 16px;
        border: 1px dashed rgba(40, 167, 69, 0.26);
        background: linear-gradient(180deg, rgba(244, 255, 247, 0.98) 0%, rgba(235, 251, 240, 0.98) 100%);
        text-align: center;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .calendar-compact-week-day-empty-label {
        display: block;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #2f8f47;
    }

    .calendar-compact-week-day-empty-text {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.35;
        color: #35536e;
    }

    .calendar-compact-week-day-empty[data-state="off"] {
        border-color: rgba(95, 107, 122, 0.2);
        background: linear-gradient(180deg, rgba(247, 249, 251, 0.98) 0%, rgba(239, 243, 247, 0.98) 100%);
    }

    .fc-compactWeek-view .fc-day-grid-event,
    .fc-compactWeek-view .calendar-agenda-event-week,
    .fc-compactDay-view .fc-day-grid-event,
    .fc-compactDay-view .calendar-agenda-event-week {
        position: relative;
        display: block;
        margin: 8px 6px 0 !important;
        border-radius: 16px;
        border: 1px solid #111111 !important;
        box-shadow: 0 12px 22px rgba(15, 61, 107, 0.14);
    }

    .fc-compactWeek-view .calendar-agenda-event-week .fc-content,
    .fc-compactDay-view .calendar-agenda-event-week .fc-content {
        display: block;
        padding: 10px 11px;
    }

    .fc-compactWeek-view .calendar-agenda-event-week .calendar-agenda-event-card,
    .fc-compactDay-view .calendar-agenda-event-week .calendar-agenda-event-card {
        gap: 6px;
    }

    .fc-compactWeek-view .calendar-agenda-event-week .calendar-agenda-event-topline,
    .fc-compactDay-view .calendar-agenda-event-week .calendar-agenda-event-topline {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 8px;
    }

    .fc-compactWeek-view .calendar-agenda-event-week .calendar-agenda-event-time,
    .fc-compactWeek-view .calendar-agenda-event-week .calendar-agenda-event-status,
    .fc-compactDay-view .calendar-agenda-event-week .calendar-agenda-event-time,
    .fc-compactDay-view .calendar-agenda-event-week .calendar-agenda-event-status {
        padding: 3px 8px;
        font-size: 10px;
    }

    .fc-compactWeek-view .calendar-agenda-event-week .calendar-agenda-event-patient,
    .fc-compactDay-view .calendar-agenda-event-week .calendar-agenda-event-patient {
        order: 3;
        flex: 1 0 100%;
        width: 100%;
        font-size: 12.5px;
        line-height: 1.2;
        text-align: center;
        white-space: normal;
        word-break: normal;
        overflow-wrap: anywhere;
    }

    .fc-compactWeek-view .calendar-agenda-event-week .calendar-agenda-event-details,
    .fc-compactDay-view .calendar-agenda-event-week .calendar-agenda-event-details {
        grid-template-columns: minmax(0, 1fr);
        gap: 6px;
    }

    .fc-compactWeek-view .calendar-agenda-event-week .calendar-agenda-event-meta,
    .fc-compactDay-view .calendar-agenda-event-week .calendar-agenda-event-meta {
        justify-items: center;
        gap: 6px;
        padding: 6px 8px;
        text-align: center;
    }

    .fc-compactWeek-view .fc-today,
    .fc-compactDay-view .fc-today {
        background: linear-gradient(180deg, rgba(23, 111, 190, 0.12) 0%, rgba(23, 111, 190, 0.06) 100%) !important;
        background-clip: padding-box !important;
    }

    .agendamento-details { padding: 15px; }
    .agendamento-details p { margin-bottom: 10px; line-height: 1.6; }
    .calendar-event-highlight {
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.75), 0 0 18px rgba(255, 193, 7, 0.45);
        transform: scale(1.02);
        z-index: 5;
    }
    .fc .calendar-event-finalized {
        opacity: .78;
        border-style: dashed !important;
    }
    .fc .calendar-event-finalized .fc-time,
    .fc .calendar-event-finalized .fc-title {
        text-decoration: line-through;
    }
    .fc-agendaDay-view .fc-time-grid-event .fc-content {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 4px 8px;
    }
    .fc-agendaDay-view .fc-time-grid-event .fc-time {
        min-width: 64px;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.2;
    }
    .fc-agendaDay-view .fc-time-grid-event .fc-title {
        font-size: 16px;
        line-height: 1.35;
        white-space: normal;
    }
    .fc-agendaDay-view .fc-axis {
        font-size: 15px;
        font-weight: 700;
    }
    .fc-agendaDay-view .calendar-agenda-event .fc-content {
        display: block;
        padding: 12px 14px;
    }
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-card {
        gap: 8px;
    }
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-topline {
        align-items: center;
        gap: 10px;
    }
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-time,
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-status {
        padding: 5px 10px;
        font-size: 11px;
    }
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-patient {
        text-align: center;
        font-size: 15px;
        line-height: 1.35;
    }
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-details {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-meta {
        align-items: center;
        justify-items: center;
        gap: 10px;
        padding: 8px 11px;
        text-align: center;
        overflow: hidden;
    }
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-label {
        font-size: 9.5px;
    }
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-value {
        max-width: 100%;
        font-size: 12.5px;
        line-height: 1.4;
        overflow-wrap: anywhere;
        text-align: center;
    }
    .fc-agendaDay-view .calendar-agenda-event .calendar-agenda-event-meta-service .calendar-agenda-event-value {
        font-size: 13px;
    }
    .fc-agendaDay-view .calendar-agenda-event-short .fc-content {
        padding: 6px 8px;
    }
    .fc-agendaDay-view .calendar-agenda-event-short .calendar-agenda-event-card {
        gap: 4px;
    }
    .fc-agendaDay-view .calendar-agenda-event-short .calendar-agenda-event-details {
        display: none;
    }
    .fc-agendaDay-view .calendar-agenda-event-short .calendar-agenda-event-time,
    .fc-agendaDay-view .calendar-agenda-event-short .calendar-agenda-event-status {
        padding: 3px 8px;
        font-size: 10px;
    }
    .fc-agendaDay-view .calendar-agenda-event-short .calendar-agenda-event-patient {
        font-size: 12px;
        line-height: 1.2;
    }
    .fc-time-grid-event {
        margin-right: 4px;
        overflow: hidden;
    }
    .fc-time-grid-event .fc-content {
        padding: 6px 8px;
        height: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }
    .fc-time-grid .fc-event-container {
        margin-right: 2px;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week {
        border-radius: 16px !important;
        box-shadow: 0 12px 22px rgba(15, 61, 107, 0.14);
    }

    .fc-agendaWeek-view .calendar-agenda-event-week .fc-content {
        display: block;
        padding: 8px 9px;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week .calendar-agenda-event-card {
        gap: 6px;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week .calendar-agenda-event-topline {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 8px;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week .calendar-agenda-event-time,
    .fc-agendaWeek-view .calendar-agenda-event-week .calendar-agenda-event-status {
        padding: 3px 8px;
        font-size: 10px;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week .calendar-agenda-event-patient {
        order: 3;
        flex: 1 0 100%;
        width: 100%;
        align-self: center;
        font-size: 12.5px;
        line-height: 1.2;
        text-align: center;
        white-space: normal;
        word-break: normal;
        overflow-wrap: anywhere;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week .calendar-agenda-event-details {
        grid-template-columns: minmax(0, 1fr);
        gap: 6px;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week .calendar-agenda-event-meta {
        justify-items: center;
        gap: 6px;
        padding: 6px 8px;
        text-align: center;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week .calendar-agenda-event-label {
        font-size: 8.5px;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week .calendar-agenda-event-value {
        font-size: 11px;
        line-height: 1.25;
        text-align: center;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week.calendar-agenda-event-short .fc-content {
        padding: 5px 7px;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week.calendar-agenda-event-short .calendar-agenda-event-card {
        gap: 4px;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week.calendar-agenda-event-short .calendar-agenda-event-details {
        display: none;
    }

    .fc-agendaWeek-view .calendar-agenda-event-week.calendar-agenda-event-short .calendar-agenda-event-patient {
        font-size: 11px;
        line-height: 1.1;
        flex-basis: 100%;
    }

    html[data-theme="dark"] .calendar-shell .card-header,
    html[data-theme="dark"] .calendar-shell .card-body {
        background: transparent !important;
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-card {
        background: linear-gradient(180deg, rgba(24, 42, 61, 0.98) 0%, rgba(18, 32, 48, 0.98) 100%);
        border-color: #000000;
        box-shadow: inset 0 0 0 1px #000000, 0 18px 34px rgba(2, 8, 15, 0.28);
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-kicker {
        color: #94b9db;
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-range {
        color: #eef6ff;
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-break {
        background: rgba(143, 197, 255, 0.12);
        color: #d7eaff;
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-break-muted {
        background: rgba(255, 255, 255, 0.06);
        color: #aac5de;
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-hour {
        background: rgba(21, 36, 52, 0.94);
        border-color: rgba(143, 197, 255, 0.16);
        color: #d7eaff;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-hour.is-break {
        background: linear-gradient(180deg, rgba(108, 84, 25, 0.72) 0%, rgba(90, 68, 18, 0.72) 100%);
        border-color: rgba(255, 214, 112, 0.24);
        color: #ffe9ae;
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-hour.is-unavailable {
        background: rgba(28, 41, 55, 0.9);
        border-style: dashed;
        color: #7f97ad;
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-hour:hover,
    html[data-theme="dark"] .calendar-clinic-sidebar-hour:focus {
        border-color: rgba(143, 197, 255, 0.26);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04), 0 10px 18px rgba(2, 8, 15, 0.24);
    }

    html[data-theme="dark"] .calendar-clinic-sidebar-hour.is-active {
        background: linear-gradient(180deg, rgba(143, 197, 255, 0.16) 0%, rgba(143, 197, 255, 0.1) 100%);
        border-color: rgba(143, 197, 255, 0.3);
        color: #eef6ff;
        box-shadow: inset 0 0 0 1px rgba(143, 197, 255, 0.14), 0 12px 22px rgba(2, 8, 15, 0.22);
    }

    html[data-theme="dark"] .calendar-legend-item {
        background: rgba(143, 197, 255, 0.12);
        color: #d7eaff;
    }

    html[data-theme="dark"] .calendar-agenda-event-time {
        background: rgba(255, 255, 255, 0.08);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.14);
    }

    html[data-theme="dark"] .calendar-agenda-event-meta {
        background: rgba(8, 19, 31, 0.18);
        box-shadow: inset 0 0 0 1px rgba(167, 212, 255, 0.12);
    }

    html[data-theme="dark"] .calendar-agenda-event-status {
        background: rgba(8, 19, 31, 0.26);
    }

    html[data-theme="dark"] .calendar-shell .form-control-sm {
        border-radius: 14px;
        min-height: 42px;
        background: linear-gradient(180deg, #24415f 0%, #17304a 100%) !important;
        border-color: rgba(143, 197, 255, 0.18) !important;
        color: #eef5fc !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03), 0 8px 18px rgba(0, 0, 0, 0.14) !important;
    }

    html[data-theme="dark"] .calendar-shell .form-control-sm:hover {
        background: linear-gradient(180deg, #2a4969 0%, #1c3956 100%) !important;
        border-color: rgba(158, 208, 255, 0.3) !important;
    }

    html[data-theme="dark"] .calendar-shell .form-control-sm:focus {
        background: linear-gradient(180deg, #315477 0%, #21405f 100%) !important;
        border-color: rgba(177, 219, 255, 0.36) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04), 0 0 0 3px rgba(143, 197, 255, 0.18), 0 10px 22px rgba(0, 0, 0, 0.18) !important;
    }

    html[data-theme="dark"] .calendar-shell select.form-control-sm {
        padding-right: 50px;
        background-image: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0) 42%), linear-gradient(180deg, #24415f 0%, #17304a 100%), linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.08)), linear-gradient(45deg, transparent 50%, #b8dcff 50%), linear-gradient(135deg, #b8dcff 50%, transparent 50%) !important;
        background-position: 0 0, 0 0, calc(100% - 40px) 50%, calc(100% - 18px) calc(50% - 2px), calc(100% - 12px) calc(50% - 2px) !important;
        background-size: 100% 100%, 100% 100%, 1px 18px, 6px 6px, 6px 6px !important;
        background-repeat: no-repeat !important;
    }

    html[data-theme="dark"] .fc {
        color: #eef5fc;
    }

    html[data-theme="dark"] .fc-toolbar h2 {
        color: #eef5fc;
        font-weight: 700;
        letter-spacing: .01em;
    }

    html[data-theme="dark"] .fc .fc-button,
    html[data-theme="dark"] .fc button {
        background: linear-gradient(180deg, #20354b 0%, #172838 100%);
        border-color: rgba(143, 197, 255, 0.24);
        color: #eef5fc;
        box-shadow: none;
    }

    html[data-theme="dark"] .fc .fc-button:hover,
    html[data-theme="dark"] .fc .fc-button:focus,
    html[data-theme="dark"] .fc button:hover,
    html[data-theme="dark"] .fc button:focus {
        background: linear-gradient(180deg, #294664 0%, #1d344b 100%);
        border-color: rgba(158, 208, 255, 0.34);
        color: #ffffff;
    }

    html[data-theme="dark"] .fc-state-active,
    html[data-theme="dark"] .fc-button.fc-state-active,
    html[data-theme="dark"] .fc button.fc-state-active {
        background: linear-gradient(180deg, #6f7ef6 0%, #5568ee 100%) !important;
        border-color: rgba(141, 153, 255, 0.54) !important;
        color: #ffffff !important;
    }

    html[data-theme="dark"] .fc-unthemed td,
    html[data-theme="dark"] .fc-unthemed th,
    html[data-theme="dark"] .fc-divider,
    html[data-theme="dark"] .fc-popover,
    html[data-theme="dark"] .fc-row,
    html[data-theme="dark"] .fc-content,
    html[data-theme="dark"] .fc-helper-skeleton,
    html[data-theme="dark"] .fc-widget-content,
    html[data-theme="dark"] .fc-widget-header {
        border-color: rgba(143, 197, 255, 0.2) !important;
    }

    html[data-theme="dark"] .fc-widget-header,
    html[data-theme="dark"] .fc-head-container {
        background: rgba(20, 34, 50, 0.96);
        color: #9bb4ca;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-day-header,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-widget-header,
    html[data-theme="dark"] .fc-agendaDay-view .fc-day-header,
    html[data-theme="dark"] .fc-agendaDay-view .fc-widget-header,
    html[data-theme="dark"] .fc-month-view .fc-day-header,
    html[data-theme="dark"] .fc-month-view .fc-widget-header {
        background: linear-gradient(180deg, rgba(23, 40, 59, 0.98) 0%, rgba(19, 33, 49, 0.98) 100%) !important;
        color: #c4d9ed !important;
        border-bottom: 1px solid rgba(143, 197, 255, 0.26) !important;
        text-shadow: none;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-day-header,
    html[data-theme="dark"] .fc-agendaDay-view .fc-day-header,
    html[data-theme="dark"] .fc-month-view .fc-day-header {
        padding: 14px 8px;
    }

    html[data-theme="dark"] .fc-agendaDay-view .fc-day-header:not(:last-child),
    html[data-theme="dark"] .fc-month-view .fc-day-header:not(:last-child),
    html[data-theme="dark"] .fc-agendaDay-view .fc-widget-content:not(:last-child),
    html[data-theme="dark"] .fc-month-view .fc-day:not(:last-child),
    html[data-theme="dark"] .fc-month-view .fc-bg td:not(:last-child) {
        border-right: 1px solid rgba(143, 197, 255, 0.3) !important;
    }

    html[data-theme="dark"] .fc-time-grid,
    html[data-theme="dark"] .fc-time-grid-container,
    html[data-theme="dark"] .fc-view,
    html[data-theme="dark"] .fc-agenda-view,
    html[data-theme="dark"] .fc-body,
    html[data-theme="dark"] .fc-bg,
    html[data-theme="dark"] .fc-slats,
    html[data-theme="dark"] .fc-content-skeleton,
    html[data-theme="dark"] .fc-day-grid-container {
        background: #16283b;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-slats td,
    html[data-theme="dark"] .fc-agendaDay-view .fc-slats td,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-slats .fc-minor td,
    html[data-theme="dark"] .fc-agendaDay-view .fc-time-grid .fc-slats .fc-minor td,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-bg td,
    html[data-theme="dark"] .fc-agendaDay-view .fc-bg td,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-day,
    html[data-theme="dark"] .fc-agendaDay-view .fc-day {
        background: #1c2f43 !important;
        color: #9bb4ca;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-axis,
    html[data-theme="dark"] .fc-agendaDay-view .fc-axis {
        background: #16283b !important;
        color: #c8def3 !important;
        border-right: 1px solid rgba(143, 197, 255, 0.2) !important;
        position: relative;
        z-index: 10;
        box-shadow: none !important;
        padding-right: 12px;
        text-align: right;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-slats .fc-minor .fc-axis,
    html[data-theme="dark"] .fc-agendaDay-view .fc-slats .fc-minor .fc-axis {
        opacity: 0;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-day-header,
    html[data-theme="dark"] .fc-agendaDay-view .fc-day-header,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-widget-header,
    html[data-theme="dark"] .fc-agendaDay-view .fc-widget-header {
        background: linear-gradient(180deg, rgba(28, 47, 67, 0.98) 0%, rgba(24, 40, 57, 0.98) 100%) !important;
        color: #d2e4f5 !important;
        border-bottom: 1px solid rgba(143, 197, 255, 0.18) !important;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-widget-content,
    html[data-theme="dark"] .fc-agendaDay-view .fc-widget-content,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-bg td,
    html[data-theme="dark"] .fc-agendaDay-view .fc-bg td {
        border-color: rgba(143, 197, 255, 0.18) !important;
        box-shadow: none !important;
    }

    html[data-theme="dark"] .fc-month-view .fc-day,
    html[data-theme="dark"] .fc-month-view .fc-widget-content,
    html[data-theme="dark"] .fc-month-view .fc-bg td,
    html[data-theme="dark"] .fc-compactWeek-view .fc-day,
    html[data-theme="dark"] .fc-compactWeek-view .fc-widget-content,
    html[data-theme="dark"] .fc-compactWeek-view .fc-bg td,
    html[data-theme="dark"] .fc-compactDay-view .fc-day,
    html[data-theme="dark"] .fc-compactDay-view .fc-widget-content,
    html[data-theme="dark"] .fc-compactDay-view .fc-bg td,
    html[data-theme="dark"] .fc-month-view .fc-content-skeleton td {
        background: #16283b !important;
        border-color: rgba(143, 197, 255, 0.24) !important;
    }

    html[data-theme="dark"] .fc-compactWeek-view .fc-day-header,
    html[data-theme="dark"] .fc-compactWeek-view .fc-widget-header,
    html[data-theme="dark"] .fc-compactDay-view .fc-day-header,
    html[data-theme="dark"] .fc-compactDay-view .fc-widget-header {
        background: linear-gradient(180deg, rgba(23, 40, 59, 0.98) 0%, rgba(19, 33, 49, 0.98) 100%) !important;
        color: #c4d9ed !important;
        border-bottom: 1px solid rgba(143, 197, 255, 0.26) !important;
    }

    html[data-theme="dark"] .fc-compactWeek-view .fc-day-header,
    html[data-theme="dark"] .fc-compactDay-view .fc-day-header {
        padding: 0 !important;
        overflow: hidden;
        background-clip: padding-box !important;
    }

    html[data-theme="dark"] .fc-compactWeek-view .fc-day-header:not(:last-child),
    html[data-theme="dark"] .fc-compactWeek-view .fc-widget-content:not(:last-child),
    html[data-theme="dark"] .fc-compactWeek-view .fc-bg td:not(:last-child),
    html[data-theme="dark"] .fc-compactWeek-view .fc-content-skeleton td:not(:last-child),
    html[data-theme="dark"] .fc-compactDay-view .fc-day-header:not(:last-child),
    html[data-theme="dark"] .fc-compactDay-view .fc-widget-content:not(:last-child),
    html[data-theme="dark"] .fc-compactDay-view .fc-bg td:not(:last-child),
    html[data-theme="dark"] .fc-compactDay-view .fc-content-skeleton td:not(:last-child) {
        border-right: 1px solid rgba(143, 197, 255, 0.24) !important;
    }

    html[data-theme="dark"] .fc-compactWeek-view .fc-today,
    html[data-theme="dark"] .fc-compactWeek-view .fc-state-highlight,
    html[data-theme="dark"] .fc-compactDay-view .fc-today,
    html[data-theme="dark"] .fc-compactDay-view .fc-state-highlight {
        background: linear-gradient(180deg, rgba(255, 230, 154, 0.18) 0%, rgba(255, 230, 154, 0.1) 100%) !important;
    }

    html[data-theme="dark"] .calendar-compact-week-availability.is-open {
        background: rgba(72, 201, 110, 0.16);
        border-color: rgba(72, 201, 110, 0.22);
        color: #b9f5c8;
    }

    html[data-theme="dark"] .calendar-compact-week-availability.is-medium {
        background: rgba(143, 197, 255, 0.16);
        border-color: rgba(143, 197, 255, 0.22);
        color: #d7eaff;
    }

    html[data-theme="dark"] .calendar-compact-week-availability.is-busy {
        background: rgba(255, 214, 112, 0.16);
        border-color: rgba(255, 214, 112, 0.24);
        color: #ffe7a2;
    }

    html[data-theme="dark"] .calendar-compact-week-availability.is-off {
        background: rgba(123, 141, 160, 0.16);
        border-color: rgba(123, 141, 160, 0.22);
        color: #d4dfeb;
    }

    html[data-theme="dark"] .calendar-compact-week-header-note.is-open,
    html[data-theme="dark"] .calendar-compact-week-header-note.is-medium,
    html[data-theme="dark"] .calendar-compact-week-header-note.is-busy {
        background: linear-gradient(180deg, rgba(32, 76, 53, 0.96) 0%, rgba(27, 63, 45, 0.96) 100%);
        border-color: rgba(102, 196, 136, 0.24);
        color: #c8f0d4;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
    }

    html[data-theme="dark"] .calendar-compact-week-header-note.is-off {
        background: linear-gradient(180deg, rgba(52, 63, 76, 0.96) 0%, rgba(43, 53, 66, 0.96) 100%);
        border-color: rgba(169, 197, 223, 0.18);
        color: #c7d7e6;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
    }

    html[data-theme="dark"] .calendar-compact-week-day-empty {
        background: linear-gradient(180deg, rgba(24, 53, 38, 0.92) 0%, rgba(20, 43, 31, 0.92) 100%);
        border-color: rgba(72, 201, 110, 0.22);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    html[data-theme="dark"] .calendar-compact-week-day-empty-label {
        color: #9be8b1;
    }

    html[data-theme="dark"] .calendar-compact-week-day-empty-text {
        color: #d7eaff;
    }

    html[data-theme="dark"] .calendar-compact-week-day-empty[data-state="off"] {
        background: linear-gradient(180deg, rgba(33, 43, 55, 0.94) 0%, rgba(26, 34, 44, 0.94) 100%);
        border-color: rgba(123, 141, 160, 0.24);
    }

    html[data-theme="dark"] .fc-month-view .fc-day-top {
        border-bottom-color: rgba(143, 197, 255, 0.18);
    }

    html[data-theme="dark"] .fc-month-view .fc-day-number {
        color: #d7e9f8;
    }

    html[data-theme="dark"] .fc-month-view .fc-day-grid-event,
    html[data-theme="dark"] .fc-month-view .calendar-month-event-card {
        box-shadow: 0 10px 18px rgba(2, 8, 15, 0.24);
    }

    html[data-theme="dark"] .fc-month-view .fc-more {
        background: rgba(158, 208, 255, 0.14);
        color: #9ed0ff;
    }

    html[data-theme="dark"] .fc-popover {
        background: #16283b;
        border-color: rgba(143, 197, 255, 0.24) !important;
        box-shadow: 0 22px 42px rgba(2, 8, 15, 0.36);
    }

    html[data-theme="dark"] .fc-popover .fc-header {
        background: linear-gradient(180deg, rgba(23, 40, 59, 0.98) 0%, rgba(19, 33, 49, 0.98) 100%);
        color: #d7e9f8;
        border-bottom: 1px solid rgba(143, 197, 255, 0.2);
    }

    html[data-theme="dark"] .fc-popover .fc-body {
        background: #16283b;
    }

    html[data-theme="dark"] .fc-month-view .fc-today,
    html[data-theme="dark"] .fc-month-view .fc-state-highlight {
        background: linear-gradient(180deg, rgba(255, 230, 154, 0.18) 0%, rgba(255, 230, 154, 0.1) 100%) !important;
    }

    html[data-theme="dark"] .fc-compactWeek-view .fc-today,
    html[data-theme="dark"] .fc-compactWeek-view .fc-state-highlight,
    html[data-theme="dark"] .fc-compactDay-view .fc-today,
    html[data-theme="dark"] .fc-compactDay-view .fc-state-highlight {
        background: linear-gradient(180deg, rgba(35, 50, 66, 0.98) 0%, rgba(29, 42, 56, 0.98) 100%) !important;
        background-clip: padding-box !important;
    }

    html[data-theme="dark"] .fc-today,
    html[data-theme="dark"] .fc-state-highlight {
        background: linear-gradient(180deg, rgba(255, 230, 154, 0.18) 0%, rgba(255, 230, 154, 0.1) 100%) !important;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-today,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-state-highlight,
    html[data-theme="dark"] .fc-agendaDay-view .fc-today,
    html[data-theme="dark"] .fc-agendaDay-view .fc-state-highlight {
        background: transparent !important;
    }

    html[data-theme="dark"] .fc-time-grid-event,
    html[data-theme="dark"] .fc-event {
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 12px 24px rgba(2, 8, 15, 0.2);
    }

    html[data-theme="dark"] .fc-time-grid-event .fc-content,
    html[data-theme="dark"] .fc-event .fc-content,
    html[data-theme="dark"] .fc-time-grid-event .fc-time,
    html[data-theme="dark"] .fc-time-grid-event .fc-title {
        color: #f8fbff;
        text-shadow: none;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-axis,
    html[data-theme="dark"] .fc-agendaDay-view .fc-axis {
        font-weight: 700;
        color: #bcd4ea;
        background: #16283b;
        position: relative;
        z-index: 8;
        min-width: 88px !important;
        width: 88px !important;
        padding-right: 12px;
        text-align: right;
        box-shadow: none !important;
    }

    @media (max-width: 767.98px) {
        .calendar-board {
            flex-direction: column;
            gap: 12px;
        }

        .calendar-clinic-sidebar {
            position: static;
            width: 100%;
            flex-basis: auto;
        }

        .calendar-clinic-sidebar-hours {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .card-header-action .form-control-sm {
            min-width: 0;
            width: 100%;
        }

        #calendar {
            min-height: 620px;
        }

        .fc-time-grid,
        .fc-time-grid-container,
        .fc-view.fc-agendaWeek-view,
        .fc-view.fc-agendaDay-view {
            min-height: 700px;
        }

        .fc-time-grid-event,
        .fc-agendaWeek-view .fc-time-grid-event {
            min-height: 0;
        }

        .calendar-agenda-event-patient,
        .fc-agendaDay-view .fc-time-grid-event .fc-title {
            font-size: 13px;
        }

        .calendar-agenda-event-time,
        .calendar-agenda-event-status,
        .calendar-agenda-event-value {
            font-size: 10px;
        }

        .calendar-agenda-event-label {
            font-size: 8.5px;
        }

        .calendar-agenda-event-meta {
            padding: 6px 7px;
        }

        .calendar-agenda-event-details {
            grid-template-columns: minmax(0, 1fr);
            gap: 6px;
        }

        .fc-toolbar {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .fc-toolbar .fc-left,
        .fc-toolbar .fc-center,
        .fc-toolbar .fc-right {
            float: none;
            width: 100%;
            text-align: center;
        }

        .fc-toolbar .fc-button-group,
        .fc-toolbar button {
            margin-bottom: 6px;
        }
    }
</style>
@endsection
