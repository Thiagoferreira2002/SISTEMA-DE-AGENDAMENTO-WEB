@extends('admin.layouts.master')
@section('content')
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
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Calendário</h4>
                        <div class="card-header-action d-flex align-items-center flex-wrap" style="gap: 10px;">
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
    var calendarFocusDate = @json(request('focus_date'));
    var calendarOpenAppointmentId = @json(request('open_agendamento'));
    var calendarShouldShowDetails = @json((bool) request('show_details'));
    var appointmentShowBaseUrl = '{{ url('admin/agendamentos') }}';
    var appointmentEditBaseUrl = '{{ url('admin/agendamentos') }}';
    var appointmentReturnUrl = @json(url()->full());
    var calendarCanEditAppointments = @json(optional(auth()->user())->canMutateOutsideCadastrosBase());

    document.addEventListener('DOMContentLoaded', function() {
        function initializeCalendar() {
            if (!window.jQuery || !window.jQuery.fn || typeof window.jQuery.fn.fullCalendar !== 'function') {
                document.getElementById('calendar').innerHTML = '<div class="alert alert-danger mb-0">Não foi possível inicializar o calendário.</div>';
                return;
            }

            var calendarEl = window.jQuery('#calendar');
            var professionalFilter = document.getElementById('calendar-professional-filter');
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

            calendarEl.fullCalendar({
                defaultView: 'agendaWeek',
                defaultDate: calendarFocusDate || undefined,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                firstDay: 0,
                height: 'auto',
                allDaySlot: false,
                slotDuration: '00:30:00',
                minTime: '06:00:00',
                maxTime: '22:00:00',
                timeFormat: 'H:mm',
                slotLabelFormat: 'H:mm',
                eventLimit: true,
                buttonText: {
                    today: 'Hoje',
                    month: 'Mes',
                    agendaWeek: 'Semana',
                    agendaDay: 'Dia'
                },
                events: {
                    url: calendarEventsUrl,
                    type: 'GET',
                    data: function() {
                        return {
                            professional_id: professionalFilter ? professionalFilter.value : '',
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
        }

        if (window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.fullCalendar === 'function') {
            initializeCalendar();
            return;
        }

        var script = document.createElement('script');
        script.src = fullCalendarScriptUrl;
        script.onload = initializeCalendar;
        script.onerror = function() {
            document.getElementById('calendar').innerHTML = '<div class="alert alert-danger mb-0">Não foi possível carregar a biblioteca do calendário.</div>';
        };
        document.body.appendChild(script);
    });
</script>

<style>
    .fc { font-family: inherit; }
    .card-header-action .form-control-sm { min-width: 230px; }
    .fc .fc-button-primary { background-color: #007bff; border-color: #007bff; }
    .fc .fc-button-primary:hover { background-color: #0056b3; border-color: #0056b3; }
    .fc .fc-button-primary.fc-button-active { background-color: #0056b3; border-color: #0056b3; }
    .fc .fc-event { cursor: pointer; border-radius: 4px; }
    .fc .fc-event:hover { opacity: 0.85; }
    #calendar { min-height: 650px; }
    .agendamento-details { padding: 15px; }
    .agendamento-details p { margin-bottom: 10px; line-height: 1.6; }
    .calendar-event-highlight {
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.75), 0 0 18px rgba(255, 193, 7, 0.45);
        transform: scale(1.02);
        z-index: 5;
    }
</style>
@endsection
