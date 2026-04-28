@extends('admin.layouts.master')
@section('content')
<section class="section">
    @php
        $isClinicManager = auth()->user()?->isClinicManager();
        $messageTemplates = [
            'confirmacao_padrao' => 'Olá {nome}, estamos confirmando seu atendimento de {servico} no dia {data} às {horario}. Pode nos responder confirmando sua presença?',
            'lembrete_comparecimento' => 'Olá {nome}, este é um lembrete do seu atendimento de {servico} marcado para {data} às {horario}. Se precisar de suporte, fale conosco por aqui.',
            'confirmacao_objetiva' => 'Olá {nome}, tudo bem? Seu horário de {servico} está reservado para {data} às {horario}. Por favor, confirme o recebimento desta mensagem.',
        ];
    @endphp
    <style>
        .confirmation-contact-stack {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            min-width: 280px;
            max-width: 320px;
            margin: 0 auto;
        }

        .confirmation-contact-block {
            width: 100%;
            padding: 10px 12px;
            border-radius: 12px;
            background: #f7fbff;
            border: 1px solid #dbe9f7;
            text-align: left;
        }

        .confirmation-contact-label {
            display: block;
            margin-bottom: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #5b7895;
        }

        .confirmation-contact-stack .btn {
            align-self: center;
            min-width: 170px;
        }
    </style>
    <div class="section-header">
        <h1>Confirmações</h1>
    </div>

    <div class="section-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-hourglass-half"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Pendentes</h4></div>
                        <div class="card-body">{{ $summary['pendentes'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Painel operacional de confirmações</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.agendamentos.confirmations') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="q">Busca por nome, CPF ou data</label>
                                <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Digite o nome, CPF ou data do agendamento">
                            </div>
                            <div class="mb-3 d-flex flex-wrap" style="gap: 8px;">
                                @if(request()->filled('period'))
                                    <input type="hidden" name="period" value="{{ request('period') }}">
                                @endif
                                <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                                <a href="{{ route('admin.agendamentos.confirmations', request()->except('q')) }}" class="btn btn-light">Limpar</a>
                            </div>
                        </div>
                        <div class="col-md-8"></div>
                    </div>
                </form>

                <div class="mb-3 d-flex flex-wrap justify-content-end" style="gap: 8px;">
                    <a href="{{ route('admin.agendamentos.confirmations', array_merge(request()->except('page', 'period'), ['period' => 'dia'])) }}" class="btn btn-outline-primary btn-sm">Dia</a>
                    <a href="{{ route('admin.agendamentos.confirmations', array_merge(request()->except('page', 'period'), ['period' => 'semana'])) }}" class="btn btn-outline-primary btn-sm">Semana</a>
                    <a href="{{ route('admin.agendamentos.confirmations', array_merge(request()->except('page', 'period'), ['period' => 'mes'])) }}" class="btn btn-outline-primary btn-sm">Mês</a>
                    <a href="{{ route('admin.agendamentos.confirmations', request()->except('page', 'period')) }}" class="btn btn-light btn-sm">Ver todos</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">Paciente</th>
                                <th class="text-center">Data</th>
                                <th class="text-center">Serviço</th>
                                <th class="text-center">Tipo de confirmação</th>
                                <th class="text-center">Contato com o paciente</th>
                                @if(! $isClinicManager)
                                    <th class="text-center">Ações</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                <tr>
                                    <td class="text-center align-middle">{{ $appointment->nome }}</td>
                                    <td class="text-center align-middle">{{ $appointment->data_agendamento->format('d/m/Y') }} às {{ $appointment->horario }}</td>
                                    <td class="text-center align-middle">{{ $appointment->servico }}</td>
                                    <td class="text-center align-middle">
                                        <div class="confirmation-contact-block mx-auto" style="max-width: 320px;">
                                            <select class="form-control form-control-sm js-message-template" data-target="message-link-{{ $appointment->id }}" data-name="{{ $appointment->nome }}" data-service="{{ $appointment->servico }}" data-date="{{ $appointment->data_agendamento->format('d/m/Y') }}" data-time="{{ $appointment->horario }}" data-phone="{{ preg_replace('/\D+/', '', $appointment->telefone) }}">
                                                @foreach($messageTemplates as $templateKey => $templateText)
                                                    <option value="{{ $templateText }}">{{ str($templateKey)->replace('_', ' ')->title() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="confirmation-contact-block text-center mx-auto" style="max-width: 220px;">
                                            <a id="message-link-{{ $appointment->id }}" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success d-inline-flex align-items-center justify-content-center js-whatsapp-message">Enviar por WhatsApp</a>
                                        </div>
                                    </td>
                                    @if(! $isClinicManager)
                                        <td class="text-center align-middle">
                                            <div class="d-flex flex-wrap justify-content-center align-items-center" style="gap: 8px;">
                                                <form action="{{ route('admin.agendamentos.confirm', $appointment) }}" method="POST" class="mb-0 d-inline-block">
                                                @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Confirmar</button>
                                                </form>
                                                <form action="{{ route('admin.agendamentos.cancel', $appointment) }}" method="POST" class="mb-0 d-inline-block">
                                                @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Deseja cancelar e excluir este agendamento?');">Excluir</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isClinicManager ? 5 : 6 }}" class="text-center">Nenhum agendamento disponível para confirmação.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var templateFields = document.querySelectorAll('.js-message-template');

        function buildMessage(template, data) {
            return template
                .replaceAll('{nome}', data.name)
                .replaceAll('{servico}', data.service)
                .replaceAll('{data}', data.date)
                .replaceAll('{horario}', data.time);
        }

        function refreshMessageLink(field) {
            var linkId = field.getAttribute('data-target');
            var link = document.getElementById(linkId);

            if (!link) {
                return;
            }

            var phone = field.getAttribute('data-phone') || '';
            var template = field.value || '';
            var message = buildMessage(template, {
                name: field.getAttribute('data-name') || '',
                service: field.getAttribute('data-service') || '',
                date: field.getAttribute('data-date') || '',
                time: field.getAttribute('data-time') || ''
            });

            link.href = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
        }

        templateFields.forEach(function (field) {
            refreshMessageLink(field);
            field.addEventListener('change', function () {
                refreshMessageLink(field);
            });
        });
    });
</script>
@endsection
