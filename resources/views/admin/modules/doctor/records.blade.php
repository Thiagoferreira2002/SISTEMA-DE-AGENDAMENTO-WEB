@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Prontuário Eletrônico</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header"><h4>Pacientes</h4></div>
                    <div class="card-body" style="max-height: 420px; overflow-y: auto;">
                        <ul class="list-group">
                            @forelse($patients as $patient)
                                <li class="list-group-item">
                                    <strong>{{ $patient->nome }}</strong><br>
                                    <small>{{ $patient->email }}</small>
                                </li>
                            @empty
                                <li class="list-group-item text-center">Nenhum paciente cadastrado.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-12">
                <div class="card">
                    <div class="card-header"><h4>Linha do Tempo Clínica</h4></div>
                    <div class="card-body">
                        <div class="timeline-list">
                            @foreach($timelineEntries as $entry)
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-head">
                                            <strong>{{ $entry['titulo'] }}</strong>
                                            <span>{{ $entry['timestamp'] }}</span>
                                        </div>
                                        <p>{{ $entry['conteudo'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4>Exame Físico</h4></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3"><div class="metric-box"><span>Pressão</span><strong>{{ $examData['pressao'] }}</strong></div></div>
                            <div class="col-md-3"><div class="metric-box"><span>Peso</span><strong>{{ $examData['peso'] }} kg</strong></div></div>
                            <div class="col-md-3"><div class="metric-box"><span>Altura</span><strong>{{ number_format($examData['altura'], 2, ',', '.') }} m</strong></div></div>
                            <div class="col-md-3"><div class="metric-box"><span>IMC</span><strong>{{ $examData['imc'] }}</strong></div></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4>Modelos de Texto</h4></div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap" style="gap: 8px;">
                            @foreach($textTemplates as $template)
                                <button type="button" class="btn btn-light border">{{ $template }}</button>
                            @endforeach
                        </div>
                        <div class="alert alert-light mt-3 mb-0">Estrutura pronta para anamnese, evolução clínica e inserção rápida de textos padrão.</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4>Log de Acesso ao Prontuário</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Usuário</th>
                                        <th>Perfil</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accessLog as $entry)
                                        <tr>
                                            <td>{{ $entry['usuario'] }}</td>
                                            <td>{{ $entry['perfil'] }}</td>
                                            <td>{{ $entry['data'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    .timeline-list { position: relative; padding-left: 24px; }
    .timeline-list::before { content: ''; position: absolute; left: 9px; top: 0; bottom: 0; width: 2px; background: #e2e8f0; }
    .timeline-item { position: relative; margin-bottom: 18px; }
    .timeline-dot { position: absolute; left: -24px; top: 4px; width: 12px; height: 12px; border-radius: 50%; background: #6777ef; }
    .timeline-content { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; }
    .timeline-head { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 8px; }
    .timeline-head span { color: #64748b; font-size: 12px; }
    .timeline-content p { margin-bottom: 0; }
    .metric-box { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; }
    .metric-box span { display: block; color: #64748b; font-size: 12px; margin-bottom: 4px; }
    .metric-box strong { font-size: 20px; }
</style>
@endsection
