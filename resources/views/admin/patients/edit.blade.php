@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Editar Paciente</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Pacientes</a></div>
            <div class="breadcrumb-item">Editar</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Formulário de Edição</h4>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                O paciente nao foi salvo porque existem campos invalidos ou ja vinculados a outro cadastro. Revise os campos destacados abaixo.
                            </div>
                        @endif

                        @if(! empty($patient->cadastro_pendencias))
                            <div class="alert alert-warning">
                                <strong>Cadastro incompleto.</strong> Preencha os campos pendentes: {{ implode(', ', $patient->cadastro_pendencias) }}.
                            </div>
                        @endif

                        <form action="{{ route('admin.patients.update', $patient) }}" method="POST" data-patient-live-check="true" data-patient-duplicate-url="{{ route('admin.patients.duplicate-check') }}" data-patient-id="{{ $patient->id }}">
                            @csrf
                            @method('PUT')
                            <div class="border rounded p-3 mb-4">
                                <h5 class="mb-3">Dados Pessoais</h5>
                                <div class="row">
                                    <div class="col-md-8"><div class="form-group"><label for="nome">Nome completo *</label><input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $patient->nome) }}" required>@error('nome')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                    <div class="col-md-2"><div class="form-group"><label for="cpf">CPF</label><input type="text" class="form-control" id="cpf" name="cpf" value="{{ old('cpf', $patient->cpf) }}">@error('cpf')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                    <div class="col-md-2"><div class="form-group"><label for="sexo">Sexo</label><select class="form-control" id="sexo" name="sexo"><option value="">Selecione</option><option value="feminino" {{ old('sexo', $patient->sexo) === 'feminino' ? 'selected' : '' }}>Feminino</option><option value="masculino" {{ old('sexo', $patient->sexo) === 'masculino' ? 'selected' : '' }}>Masculino</option><option value="outro" {{ old('sexo', $patient->sexo) === 'outro' ? 'selected' : '' }}>Outro</option></select></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><div class="form-group"><label for="data_nascimento">Data de Nascimento</label><input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento', $patient->data_nascimento ? $patient->data_nascimento->format('Y-m-d') : '') }}" max="{{ now()->format('Y-m-d') }}">@error('data_nascimento')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                </div>
                            </div>

                            <div class="border rounded p-3 mb-4">
                                <h5 class="mb-3">Contato</h5>
                                <div class="row">
                                    <div class="col-md-6"><div class="form-group"><label for="telefone">Celular (WhatsApp) *</label><input type="text" class="form-control" id="telefone" name="telefone" value="{{ old('telefone', $patient->telefone) }}" required>@error('telefone')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="email">E-mail *</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email', $patient->email) }}" required>@error('email')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                </div>
                            </div>

                            <div class="border rounded p-3 mb-4">
                                <h5 class="mb-3">Endereço</h5>
                                <div class="row">
                                    <div class="col-md-4"><div class="form-group"><label for="endereco">Endereço</label><input type="text" class="form-control" id="endereco" name="endereco" value="{{ old('endereco', $patient->endereco) }}">@error('endereco')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                    <div class="col-md-2"><div class="form-group"><label for="numero_endereco">Número</label><input type="text" class="form-control" id="numero_endereco" name="numero_endereco" value="{{ old('numero_endereco', $patient->numero_endereco) }}">@error('numero_endereco')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                    <div class="col-md-3"><div class="form-group"><label for="cep">CEP</label><input type="text" class="form-control" id="cep" name="cep" value="{{ old('cep', $patient->cep) }}">@error('cep')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                    <div class="col-md-3"><div class="form-group"><label for="bairro">Bairro</label><input type="text" class="form-control" id="bairro" name="bairro" value="{{ old('bairro', $patient->bairro) }}">@error('bairro')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><div class="form-group"><label for="tipo_moradia">Tipo de imóvel</label><select class="form-control" id="tipo_moradia" name="tipo_moradia"><option value="">Selecione</option><option value="casa" {{ old('tipo_moradia', $patient->tipo_moradia) === 'casa' ? 'selected' : '' }}>Casa</option><option value="apartamento" {{ old('tipo_moradia', $patient->tipo_moradia) === 'apartamento' ? 'selected' : '' }}>Apartamento</option><option value="condominio" {{ old('tipo_moradia', $patient->tipo_moradia) === 'condominio' ? 'selected' : '' }}>Condomínio</option><option value="sobrado" {{ old('tipo_moradia', $patient->tipo_moradia) === 'sobrado' ? 'selected' : '' }}>Sobrado</option><option value="comercial" {{ old('tipo_moradia', $patient->tipo_moradia) === 'comercial' ? 'selected' : '' }}>Comercial</option><option value="rural" {{ old('tipo_moradia', $patient->tipo_moradia) === 'rural' ? 'selected' : '' }}>Rural</option><option value="outro" {{ old('tipo_moradia', $patient->tipo_moradia) === 'outro' ? 'selected' : '' }}>Outro</option></select>@error('tipo_moradia')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                    <div class="col-md-5"><div class="form-group mb-0"><label for="complemento">Complemento</label><input type="text" class="form-control" id="complemento" name="complemento" value="{{ old('complemento', $patient->complemento) }}">@error('complemento')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Atualizar Paciente</button>
                                <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function bindCepLookup(cepId, enderecoId, bairroId) {
            var cepField = document.getElementById(cepId);
            var enderecoField = document.getElementById(enderecoId);
            var bairroField = document.getElementById(bairroId);
            var lastFetchedCep = '';

            if (!cepField || !enderecoField || !bairroField) {
                return;
            }

            function fetchCepData() {
                var cep = (cepField.value || '').replace(/\D/g, '');

                if (cep.length !== 8 || cep === lastFetchedCep) {
                    return;
                }

                lastFetchedCep = cep;

                fetch('https://viacep.com.br/ws/' + cep + '/json/')
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Falha ao consultar CEP');
                        }

                        return response.json();
                    })
                    .then(function(data) {
                        if (data.erro) {
                            return;
                        }

                        enderecoField.value = data.logradouro || '';
                        bairroField.value = data.bairro || '';
                    })
                    .catch(function() {
                    });
            }

            cepField.addEventListener('blur', fetchCepData);
            cepField.addEventListener('input', function() {
                if ((cepField.value || '').replace(/\D/g, '').length === 8) {
                    fetchCepData();
                }
            });

            if ((cepField.value || '').replace(/\D/g, '').length === 8) {
                fetchCepData();
            }
        }

        if (window.IMask) {
            IMask(document.getElementById('cpf'), { mask: '000.000.000-00' });
            IMask(document.getElementById('telefone'), { mask: '(00) 00000-0000' });
            if (document.getElementById('cep')) {
                IMask(document.getElementById('cep'), { mask: '00000-000' });
            }
        }

        bindCepLookup('cep', 'endereco', 'bairro');
    });
</script>
@endsection
