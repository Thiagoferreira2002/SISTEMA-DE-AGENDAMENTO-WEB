@extends('admin.layouts.master')
@section('content')
<style>
    .calendar-shell {
        border: 1px solid rgba(30, 144, 255, 0.14);
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(244,249,255,.96));
        box-shadow: 0 16px 34px rgba(18, 58, 99, 0.08);
        overflow: hidden;
    }

    html[data-theme="dark"] .calendar-shell {
        border-color: rgba(143, 197, 255, 0.16);
        background: linear-gradient(180deg, rgba(22,40,59,.98), rgba(19,33,49,.98));
        box-shadow: 0 22px 44px rgba(2, 8, 15, 0.34);
    }

    .calendar-shell .form-control-sm {
        min-height: 40px;
        border-radius: 999px;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .calendar-shell .form-control-sm:hover {
        border-color: rgba(23, 111, 190, 0.28);
    }

    .calendar-shell .form-control-sm:focus {
        border-color: rgba(23, 111, 190, 0.4) !important;
        box-shadow: 0 0 0 3px rgba(23, 111, 190, 0.12) !important;
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
                                        <option value="">Todos os profissionais</option>
                                        @foreach(($professionalOptions ?? []) as $professionalOption)
                                            <option value="{{ $professionalOption['id'] }}" {{ (string) ($selectedProfessionalId ?? '') === (string) $professionalOption['id'] ? 'selected' : '' }}>
                                                {{ $professionalOption['nome'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            @if(!($hideProfessionalFilter ?? false))
                                <a href="{{ route('admin.agendamentos.create', ['return_to' => $returnUrl]) }}" class="btn btn-primary btn-sm">Novo Agendamento</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div id="calendar"></div>
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
    var clinicClosingTime = @json(($clinicHours['closing_time'] ?? '19:00') . ':00');
    var clinicClosingDisplayTime = @json(optional(\Carbon\Carbon::createFromFormat('H:i', $clinicHours['closing_time'] ?? '19:00')->addHour())->format('H:i:s'));
    var calendarFocusDate = @json(request('focus_date') ?: ($selectedCalendarDate ?? null));
    var calendarOpenAppointmentId = @json(request('open_agendamento'));
    var calendarShouldShowDetails = @json((bool) request('show_details'));
    var selectedCalendarDate = @json($selectedCalendarDate ?? '');
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
            var professionalFilter = document.getElementById('calendar-professional-filter');
            var dateFilter = document.getElementById('calendar-date-filter');
            var pendingAutoOpenId = calendarOpenAppointmentId ? String(calendarOpenAppointmentId) : '';
            var hasAutoOpenedAppointment = false;
            if (!calendarEl.length) return;

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

            function buildMonthEventTitle(event) {
                var patientName = String(event.nome || 'Agendamento').trim();
                var firstName = patientName.split(/\s+/)[0] || patientName;

                return firstName + ' • ' + shortProfessionalName(event.medico || '');
            }

            calendarEl.fullCalendar({
                defaultView: 'agendaWeek',
                defaultDate: calendarFocusDate || undefined,
                locale: 'pt-br',
                lang: 'pt-br',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                firstDay: 1,
                height: 'auto',
                allDaySlot: false,
                slotDuration: '00:30:00',
                minTime: clinicOpeningTime,
                maxTime: clinicClosingDisplayTime || clinicClosingTime,
                scrollTime: clinicOpeningTime,
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
                    agendaWeek: 'Semana',
                    agendaDay: 'Dia'
                },
                views: {
                    month: {
                        titleFormat: 'MMMM [de] YYYY',
                        columnHeaderFormat: 'ddd',
                        eventLimit: 2
                    },
                    agendaWeek: {
                        columnHeaderFormat: 'ddd D/M'
                    },
                    agendaDay: {
                        titleFormat: 'dddd, D [de] MMMM [de] YYYY',
                        columnHeaderFormat: 'dddd D/M'
                    }
                },
                events: {
                    url: calendarEventsUrl,
                    type: 'GET',
                    data: function() {
                        return {
                            professional_id: professionalFilter ? professionalFilter.value : '',
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
                    element.attr('data-agendamento-id', event.agendamento_id);
                    element.attr('title', (event.telefone || '') + ' • ' + (event.motivo || ''));

                    if (calendarEl.fullCalendar('getView').name === 'month') {
                        element.addClass('calendar-month-event-card');
                        element.find('.fc-time').text((event.horario || '').trim());
                        element.find('.fc-title').text(buildMonthEventTitle(event));
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
                }
            });

            if (professionalFilter) {
                professionalFilter.addEventListener('change', function() {
                    calendarEl.fullCalendar('refetchEvents');
                });
            }

            if (dateFilter) {
                dateFilter.addEventListener('change', function() {
                    if (dateFilter.value) {
                        calendarEl.fullCalendar('gotoDate', dateFilter.value);
                    }

                    calendarEl.fullCalendar('refetchEvents');
                });
            }
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
    .fc .fc-button-primary { background-color: #007bff; border-color: #007bff; }
    .fc .fc-button-primary:hover { background-color: #0056b3; border-color: #0056b3; }
    .fc .fc-button-primary.fc-button-active { background-color: #0056b3; border-color: #0056b3; }
    .fc .fc-event { cursor: pointer; border-radius: 4px; }
    .fc .fc-event:hover { opacity: 0.85; }
    #calendar { min-height: 650px; }
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

    .fc-agendaWeek-view .fc-axis,
    .fc-agendaDay-view .fc-axis {
        background: rgba(247, 251, 255, 0.98);
        color: #5a7186;
        font-weight: 700;
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
        margin: 3px 4px 0;
        border-radius: 8px;
        border: 0 !important;
        box-shadow: 0 6px 14px rgba(15, 61, 107, 0.12);
    }

    .fc-month-view .calendar-month-event-card .fc-content {
        display: block;
        padding: 4px 6px;
        line-height: 1.2;
    }

    .fc-month-view .calendar-month-event-card .fc-time {
        display: block;
        font-size: 10px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .fc-month-view .calendar-month-event-card .fc-title {
        display: block;
        font-size: 10px;
        font-weight: 700;
        white-space: normal;
        word-break: break-word;
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
        min-width: 54px;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
    }
    .fc-agendaDay-view .fc-time-grid-event .fc-title {
        font-size: 15px;
        line-height: 1.25;
        white-space: normal;
    }
    .fc-agendaDay-view .fc-axis {
        font-size: 14px;
        font-weight: 700;
    }
    .fc-time-grid-event {
        margin-right: 4px;
        min-height: 38px;
    }
    .fc-time-grid-event .fc-content {
        padding: 3px 6px;
    }
    .fc-time-grid .fc-event-container {
        margin-right: 2px;
    }

    html[data-theme="dark"] .calendar-shell .card-header,
    html[data-theme="dark"] .calendar-shell .card-body {
        background: transparent !important;
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

    html[data-theme="dark"] .fc-agendaWeek-view .fc-day-header:not(:last-child),
    html[data-theme="dark"] .fc-agendaDay-view .fc-day-header:not(:last-child),
    html[data-theme="dark"] .fc-month-view .fc-day-header:not(:last-child),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-widget-content:not(:last-child),
    html[data-theme="dark"] .fc-agendaDay-view .fc-widget-content:not(:last-child),
    html[data-theme="dark"] .fc-month-view .fc-day:not(:last-child),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-slats td:not(:last-child),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-bg td:not(:last-child),
    html[data-theme="dark"] .fc-month-view .fc-bg td:not(:last-child),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-content-skeleton td:not(:last-child) {
        border-right: 1px solid rgba(143, 197, 255, 0.3) !important;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-day-header:not(:last-child),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-bg td:not(:last-child),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-content-skeleton td:not(:last-child),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-slats td:not(:last-child),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-widget-content:not(:last-child) {
        box-shadow: inset -2px 0 0 rgba(143, 197, 255, 0.34);
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-slats .fc-widget-content,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-content-skeleton .fc-widget-content,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-content-col,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-bg .fc-day,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-day,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-event-container,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-bgevent-container,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-highlight-container {
        border-right: 1px solid rgba(143, 197, 255, 0.28) !important;
        box-shadow: inset -1px 0 0 rgba(143, 197, 255, 0.26) !important;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-content-col:last-child,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-bg .fc-day:last-child,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-day:last-child,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-event-container:last-child,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-bgevent-container:last-child,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-highlight-container:last-child {
        border-right: 0 !important;
        box-shadow: none !important;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-content-col {
        background-image: linear-gradient(180deg, rgba(143, 197, 255, 0.09) 0%, rgba(143, 197, 255, 0.02) 100%);
        background-clip: padding-box;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-content-col:nth-child(odd),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-bg td:nth-child(odd) {
        background-color: rgba(143, 197, 255, 0.05) !important;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-content-col:nth-child(even),
    html[data-theme="dark"] .fc-agendaWeek-view .fc-bg td:nth-child(even) {
        background-color: rgba(255, 255, 255, 0.015) !important;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-time-grid .fc-content-col:first-child,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-bg td:first-child,
    html[data-theme="dark"] .fc-agendaWeek-view .fc-day-header:first-child {
        border-left: 1px solid rgba(143, 197, 255, 0.34) !important;
        box-shadow: inset 1px 0 0 rgba(143, 197, 255, 0.28), inset -1px 0 0 rgba(143, 197, 255, 0.26) !important;
    }

    html[data-theme="dark"] .fc-agendaWeek-view .fc-day-header:not(:last-child) {
        border-right: 1px solid rgba(143, 197, 255, 0.38) !important;
        box-shadow: inset -2px 0 0 rgba(143, 197, 255, 0.3) !important;
    }

    html[data-theme="dark"] .fc-time-grid,
    html[data-theme="dark"] .fc-time-grid-container,
    html[data-theme="dark"] .fc-view,
    html[data-theme="dark"] .fc-agenda-view,
    html[data-theme="dark"] .fc-body,
    html[data-theme="dark"] .fc-bg,
    html[data-theme="dark"] .fc-slats,
    html[data-theme="dark"] .fc-content-skeleton,
    html[data-theme="dark"] .fc-time-grid-container,
    html[data-theme="dark"] .fc-day-grid-container {
        background: #16283b;
    }

    html[data-theme="dark"] .fc-day,
    html[data-theme="dark"] .fc-time-area,
    html[data-theme="dark"] .fc-axis,
    html[data-theme="dark"] .fc-slats td,
    html[data-theme="dark"] .fc-time-grid .fc-slats .fc-minor td {
        background: #16283b;
        color: #9bb4ca;
    }

    html[data-theme="dark"] .fc-month-view .fc-day,
    html[data-theme="dark"] .fc-month-view .fc-widget-content,
    html[data-theme="dark"] .fc-month-view .fc-bg td,
    html[data-theme="dark"] .fc-month-view .fc-content-skeleton td {
        background: #16283b !important;
        border-color: rgba(143, 197, 255, 0.24) !important;
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

    html[data-theme="dark"] .fc-agendaWeek-view .fc-axis {
        background: #132131;
        color: #a9c5df;
        border-right: 1px solid rgba(143, 197, 255, 0.22) !important;
    }

    html[data-theme="dark"] .fc-agendaDay-view .fc-axis {
        background: #132131;
        color: #a9c5df;
        border-right: 1px solid rgba(143, 197, 255, 0.22) !important;
    }

    html[data-theme="dark"] .fc-month-view .fc-today,
    html[data-theme="dark"] .fc-month-view .fc-state-highlight {
        background: linear-gradient(180deg, rgba(255, 230, 154, 0.18) 0%, rgba(255, 230, 154, 0.1) 100%) !important;
    }

    html[data-theme="dark"] .fc-today,
    html[data-theme="dark"] .fc-state-highlight {
        background: linear-gradient(180deg, rgba(255, 230, 154, 0.18) 0%, rgba(255, 230, 154, 0.1) 100%) !important;
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
        color: #9bb4ca;
    }

    @media (max-width: 767.98px) {
        .card-header-action .form-control-sm {
            min-width: 0;
            width: 100%;
        }

        #calendar {
            min-height: 540px;
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
