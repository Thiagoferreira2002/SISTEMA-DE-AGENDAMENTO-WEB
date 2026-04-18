@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Atestados e Laudos</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header"><h4>Gerador de Atestados</h4></div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($reportTemplates as $template)
                                <li class="list-group-item">{{ $template }}</li>
                            @endforeach
                        </ul>
                        <hr>
                        <h6>Assinatura digital</h6>
                        <p class="mb-1"><strong>Status:</strong> {{ $digitalSignatureInfo['status'] }}</p>
                        <p class="mb-0"><strong>Integração:</strong> {{ $digitalSignatureInfo['provider'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-12">
                <div class="card">
                    <div class="card-header"><h4>CIDs Integrados e Laudos</h4></div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="cid_search">Busca CID</label>
                                    <input type="text" class="form-control" id="cid_search" placeholder="Ex: Gripe ou J11">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="exam_report">Laudo de exame</label>
                                    <input type="text" class="form-control" id="exam_report" placeholder="Descreva o laudo técnico do exame realizado">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cidCatalog as $cid)
                                        <tr>
                                            <td>{{ $cid['codigo'] }}</td>
                                            <td>{{ $cid['descricao'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">Nenhum CID disponível.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Data</th>
                                        <th>Serviço</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAppointments as $appointment)
                                        <tr>
                                            <td>{{ $appointment->nome }}</td>
                                            <td>{{ $appointment->data_agendamento->format('d/m/Y') }}</td>
                                            <td>{{ $appointment->servico }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Nenhum atendimento disponível.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
