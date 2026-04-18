@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Prescrições e Receitas</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header"><h4>Favoritos e Modelos</h4></div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($templates as $template)
                                <li class="list-group-item">{{ $template }}</li>
                            @endforeach
                        </ul>
                        <hr>
                        <h6>Combos favoritos</h6>
                        <ul class="list-group">
                            @foreach($favoriteCombos as $combo)
                                <li class="list-group-item">
                                    <strong>{{ $combo['nome'] }}</strong><br>
                                    <small>{{ $combo['itens'] }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-12">
                <div class="card">
                    <div class="card-header"><h4>Banco de Medicamentos</h4></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="medication_search">Busca rápida</label>
                            <input type="text" class="form-control" id="medication_search" placeholder="Buscar por remédio ou princípio ativo">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Princípio ativo</th>
                                        <th>Posologia sugerida</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($medications as $medication)
                                        <tr>
                                            <td>{{ $medication['nome'] }}</td>
                                            <td>{{ $medication['principio_ativo'] }}</td>
                                            <td>{{ $medication['posologia'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Nenhum medicamento disponível.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4>Receituário Especial</h4></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6"><div class="form-group"><label>Fornecedor</label><input type="text" class="form-control" value="{{ $specialPrescriptionGuidance['fornecedor'] }}"></div></div>
                            <div class="col-md-6"><div class="form-group"><label>Comprador</label><input type="text" class="form-control" value="{{ $specialPrescriptionGuidance['comprador'] }}"></div></div>
                        </div>
                        <div class="alert alert-light mb-0">Estrutura pronta para receitas de controle especial com dados complementares de fornecedor e comprador.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
