@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Convênios Aceitos</h1>
    </div>

    <div class="section-body">
        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(!empty($setupWarning))
            <div class="alert alert-warning">{{ $setupWarning }}</div>
        @endif

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><h4>Novo convênio</h4></div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.insurance.store') }}" method="POST" data-draft-form="true" data-draft-key="admin.settings.insurance.create">
                            @csrf
                            <div class="form-group"><label>Nome *</label><input type="text" class="form-control" name="nome" required></div>
                            <div class="form-group"><label>ANS</label><input type="text" class="form-control" name="ans"></div>
                            <div class="form-group"><label>CNPJ</label><input type="text" class="form-control" name="cnpj"></div>
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="requires_guide" name="requires_guide" value="1">
                                <label class="custom-control-label" for="requires_guide">Exige número da guia</label>
                            </div>
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="requires_authorization" name="requires_authorization" value="1">
                                <label class="custom-control-label" for="requires_authorization">Exige autorização prévia</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Cadastrar convênio</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><h4>Novo plano</h4></div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.insurance.plans.store') }}" method="POST" data-draft-form="true" data-draft-key="admin.settings.insurance.plans.create">
                            @csrf
                            <div class="form-group">
                                <label>Convênio *</label>
                                <select class="form-control" name="insurance_id" required>
                                    <option value="">Selecione</option>
                                    @foreach($insurances as $insurance)
                                        <option value="{{ $insurance->id }}">{{ $insurance->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Nome do plano *</label><input type="text" class="form-control" name="nome" required></div>
                            <div class="form-group"><label>Código do plano</label><input type="text" class="form-control" name="codigo"></div>
                            <button type="submit" class="btn btn-primary">Cadastrar plano</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><h4>Tabela de preços</h4></div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.insurance.prices.store') }}" method="POST" data-draft-form="true" data-draft-key="admin.settings.insurance.prices.create">
                            @csrf
                            <div class="form-group">
                                <label>Procedimento *</label>
                                <select class="form-control" name="procedure_id" required>
                                    <option value="">Selecione</option>
                                    @foreach($procedures as $procedure)
                                        <option value="{{ $procedure->id }}">{{ $procedure->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Convênio *</label>
                                <select class="form-control" name="insurance_id" required>
                                    <option value="">Selecione</option>
                                    @foreach($insurances as $insurance)
                                        <option value="{{ $insurance->id }}">{{ $insurance->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Plano</label>
                                <select class="form-control" name="insurance_plan_id">
                                    <option value="">Todos os planos / geral do convênio</option>
                                    @foreach($insurances as $insurance)
                                        @foreach($insurance->plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $insurance->nome }} - {{ $plan->nome }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Valor *</label><input type="number" step="0.01" min="0" class="form-control" name="valor" required></div>
                            <button type="submit" class="btn btn-primary">Salvar preço</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4>Tabelas, planos e exigências</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Convênio</th>
                                <th>ANS / CNPJ</th>
                                <th>Planos</th>
                                <th>Guia / Autorização</th>
                                <th>Tabela vinculada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($insurances as $insurance)
                                <tr>
                                    <td>{{ $insurance->nome }}</td>
                                    <td>
                                        <div>ANS: {{ $insurance->ans ?: 'Não informado' }}</div>
                                        <small class="text-muted">CNPJ: {{ $insurance->cnpj ?: 'Não informado' }}</small>
                                    </td>
                                    <td>
                                        @forelse($insurance->plans as $plan)
                                            <span class="badge badge-light border mr-1 mb-1">{{ $plan->nome }}</span>
                                        @empty
                                            <span class="text-muted">Sem planos</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $insurance->requires_guide ? 'warning' : 'secondary' }}">Guia {{ $insurance->requires_guide ? 'obrigatória' : 'não' }}</span>
                                        <span class="badge badge-{{ $insurance->requires_authorization ? 'warning' : 'secondary' }}">Autorização {{ $insurance->requires_authorization ? 'obrigatória' : 'não' }}</span>
                                    </td>
                                    <td>
                                        @forelse($insurance->procedurePrices as $price)
                                            <div>{{ $price->procedure?->nome }}: R$ {{ number_format((float) $price->valor, 2, ',', '.') }}</div>
                                        @empty
                                            <span class="text-muted">Sem preços vinculados</span>
                                        @endforelse
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Nenhum convênio cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
