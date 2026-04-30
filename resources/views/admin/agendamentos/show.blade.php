@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Detalhes do Agendamento</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ $returnUrl }}">Agendamentos</a></div>
            <div class="breadcrumb-item">Detalhes</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Informações do Agendamento</h4>
                        <div class="card-header-action">
                            @if($canEditAppointment ?? true)
                                <a href="{{ route('admin.agendamentos.edit', ['agendamento' => $agendamento, 'return_to' => $returnUrl]) }}" class="btn btn-warning">Editar</a>
                            @endif
                            <a href="{{ $returnUrl }}" class="btn btn-secondary">Voltar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $startTimeValue = substr((string) $agendamento->horario, 0, 5);
                            $endTimeValue = optional($agendamento->data_agendamento)
                                ? $agendamento->data_agendamento->copy()->setTimeFromTimeString($startTimeValue)->addMinutes((int) ($agendamento->duracao_minutos ?: 30))->format('H:i')
                                : '-';
                        @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nome:</label>
                                    <p>{{ $agendamento->nome }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email:</label>
                                    <p>{{ $agendamento->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Telefone:</label>
                                    <p>{{ $agendamento->telefone }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Serviço:</label>
                                    <p>{{ $agendamento->servico }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Data do Agendamento:</label>
                                    <p>{{ $agendamento->data_agendamento->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Horário inicial:</label>
                                    <p>{{ $startTimeValue }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Horário final:</label>
                                    <p>{{ $endTimeValue }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <p>
                                        @if($agendamento->status == 'confirmado')
                                            <span class="badge badge-success">Confirmado</span>
                                        @elseif($agendamento->status == 'pendente')
                                            <span class="badge badge-warning">Pendente</span>
                                        @elseif($agendamento->status == 'cancelado')
                                            <span class="badge badge-danger">Cancelado</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Criado em:</label>
                                    <p>{{ $agendamento->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Descrição:</label>
                                    <p>{{ $agendamento->motivo_consulta ?: ($agendamento->descricao ?: '-') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
