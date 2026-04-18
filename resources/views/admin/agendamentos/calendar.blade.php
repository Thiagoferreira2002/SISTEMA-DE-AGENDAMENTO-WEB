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
                        <div class="card-header-action">
                            <a href="{{ route('admin.agendamentos.create') }}" class="btn btn-primary btn-sm">Novo Agendamento</a>
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

    document.addEventListener('DOMContentLoaded', function() {
        function initializeCalendar() {
            if (!window.jQuery || !window.jQuery.fn || typeof window.jQuery.fn.fullCalendar !== 'function') {
                document.getElementById('calendar').innerHTML = '<div class="alert alert-danger mb-0">Não foi possível inicializar o calendário.</div>';
                return;
            }

            var calendarEl = window.jQuery('#calendar');
            if (!calendarEl.length) return;

            calendarEl.fullCalendar({
                defaultView: 'agendaWeek',
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
                    error: function() {
                        calendarEl.html('<div class="alert alert-danger mb-0">Não foi possível carregar os eventos do calendário.</div>');
                    }
                },
                eventClick: function(event) {
                    var html = '<div class="agendamento-details text-left">' +
                        '<p><strong>Nome:</strong> ' + (event.nome || '-') + '</p>' +
                        '<p><strong>Médico:</strong> ' + (event.medico || '-') + '</p>' +
                        '<p><strong>Email:</strong> ' + (event.email || '-') + '</p>' +
                        '<p><strong>Telefone:</strong> ' + (event.telefone || '-') + '</p>' +
                        '<p><strong>Serviço:</strong> ' + (event.servico || '-') + '</p>' +
                        '<p><strong>Motivo:</strong> ' + (event.motivo || '-') + '</p>' +
                        '<p><strong>Horário:</strong> ' + (event.horario || '-') + ' às ' + (event.horario_final || '-') + '</p>' +
                        '<p><strong>Status:</strong> ' + (event.status || '-') + '</p>' +
                        '</div>';

                    if (window.Swal) {
                        window.Swal.fire({
                            title: 'Detalhes do Agendamento',
                            html: html,
                            showCloseButton: true,
                            showCancelButton: true,
                            cancelButtonText: 'Fechar',
                            confirmButtonText: 'Editar',
                            confirmButtonColor: '#f39c12',
                            cancelButtonColor: '#6c757d'
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                window.location.href = '/admin/agendamentos/' + event.agendamento_id + '/edit';
                            }
                        });
                    }
                    return false;
                },
                eventRender: function(event, element) {
                    element.attr('title', (event.telefone || '') + ' • ' + (event.motivo || ''));
                    element.find('.fc-time').text(event.horario + ' - ' + event.horario_final);
                }
            });
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
    .fc .fc-button-primary { background-color: #007bff; border-color: #007bff; }
    .fc .fc-button-primary:hover { background-color: #0056b3; border-color: #0056b3; }
    .fc .fc-button-primary.fc-button-active { background-color: #0056b3; border-color: #0056b3; }
    .fc .fc-event { cursor: pointer; border-radius: 4px; }
    .fc .fc-event:hover { opacity: 0.85; }
    #calendar { min-height: 650px; }
    .agendamento-details { padding: 15px; }
    .agendamento-details p { margin-bottom: 10px; line-height: 1.6; }
</style>
@endsection
