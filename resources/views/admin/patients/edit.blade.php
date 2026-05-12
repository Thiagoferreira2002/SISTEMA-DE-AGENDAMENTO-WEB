@extends('admin.layouts.master')
@section('content')
<style>
    .patient-completion-card {
        border: 1px solid rgba(71, 195, 99, 0.14);
        background: linear-gradient(180deg, rgba(242, 252, 245, 0.96) 0%, rgba(234, 249, 239, 0.98) 100%);
        box-shadow: 0 14px 28px rgba(71, 195, 99, 0.08);
    }

    .patient-completion-card .progress {
        background: rgba(71, 195, 99, 0.14);
    }

    .patient-progress-bar {
        background: linear-gradient(90deg, #47c363 0%, #2f9e44 100%) !important;
    }

    html[data-theme="dark"] .patient-completion-card {
        background: linear-gradient(180deg, rgba(22, 47, 35, 0.96) 0%, rgba(19, 39, 31, 0.98) 100%);
        border-color: rgba(96, 216, 131, 0.14);
        box-shadow: 0 18px 30px rgba(2, 8, 15, 0.24);
    }

    html[data-theme="dark"] .patient-completion-card .progress {
        background: rgba(96, 216, 131, 0.16);
    }

    .patient-form-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .patient-form-actions .btn {
        width: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    @media (max-width: 991.98px) {
        .patient-personal-name-col {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .patient-personal-cpf-col,
        .patient-personal-sex-col,
        .patient-personal-birth-col {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 767.98px) {
        .patient-form-actions .btn {
            padding-left: 14px !important;
            padding-right: 14px !important;
        }

        .patient-personal-cpf-col,
        .patient-personal-sex-col,
        .patient-personal-birth-col {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
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

                        <div class="border rounded p-3 mb-4 patient-completion-card">
                            <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 10px;">
                                <div>
                                    <h5 class="mb-1">Qualidade do cadastro</h5>
                                    <p class="text-muted mb-0 small">Use este painel para identificar rapidamente o que ainda falta preencher.</p>
                                </div>
                                <span class="small text-muted" data-patient-progress-text>0 de 0 campos preenchidos</span>
                            </div>
                            <div class="progress mt-3" style="height: 10px; border-radius: 999px; overflow: hidden;">
                                <div class="progress-bar patient-progress-bar" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-patient-progress-bar></div>
                            </div>
                            <p class="small text-muted mt-2 mb-0" data-patient-missing-fields>Carregando campos do cadastro.</p>
                        </div>

                        @if(! empty($patient->cadastro_pendencias))
                            <div class="alert alert-warning">
                                <strong>Cadastro incompleto.</strong> Preencha os campos pendentes: {{ implode(', ', $patient->cadastro_pendencias) }}.
                            </div>
                        @endif

                        <form action="{{ route('admin.patients.update', $patient) }}" method="POST" enctype="multipart/form-data" data-patient-live-check="true" data-patient-duplicate-url="{{ route('admin.patients.duplicate-check') }}" data-patient-id="{{ $patient->id }}">
                            @csrf
                            @method('PUT')
                            <div class="border rounded p-3 mb-4">
                                <h5 class="mb-3">Foto do paciente</h5>
                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center mb-3 mb-md-0">
                                        <img src="{{ $patient->foto_url }}" alt="Foto do paciente" class="img-fluid rounded-circle border" style="width: 112px; height: 112px; object-fit: cover;" data-patient-photo-preview data-default-src="{{ $patient->foto_url }}">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="foto">Atualizar foto</label>
                                            <input type="file" class="form-control-file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp,image/*" data-patient-photo-input>
                                            <small class="text-muted d-block mt-2">Opcional. Envie JPG, PNG ou WEBP com até 2 MB.</small>
                                            @if($patient->foto)
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" type="checkbox" id="remove_foto" name="remove_foto" value="1">
                                                    <label class="form-check-label" for="remove_foto">Remover foto atual</label>
                                                </div>
                                            @endif
                                            @error('foto')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded p-3 mb-4">
                                <h5 class="mb-3">Dados Pessoais</h5>
                                <div class="row">
                                    <div class="col-lg-8 col-md-12 patient-personal-name-col"><div class="form-group"><label for="nome">Nome completo *</label><input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $patient->nome) }}" required>@error('nome')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                    <div class="col-lg-2 col-md-6 patient-personal-cpf-col"><div class="form-group"><label for="cpf">CPF</label><input type="text" class="form-control" id="cpf" name="cpf" value="{{ old('cpf', $patient->cpf) }}">@error('cpf')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
                                    <div class="col-lg-2 col-md-6 patient-personal-sex-col"><div class="form-group"><label for="sexo">Sexo</label><select class="form-control" id="sexo" name="sexo"><option value="">Selecione</option><option value="feminino" {{ old('sexo', $patient->sexo) === 'feminino' ? 'selected' : '' }}>Feminino</option><option value="masculino" {{ old('sexo', $patient->sexo) === 'masculino' ? 'selected' : '' }}>Masculino</option><option value="outro" {{ old('sexo', $patient->sexo) === 'outro' ? 'selected' : '' }}>Outro</option></select></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 patient-personal-birth-col"><div class="form-group"><label for="data_nascimento">Data de Nascimento</label><input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento', $patient->data_nascimento ? $patient->data_nascimento->format('Y-m-d') : '') }}" max="{{ now()->format('Y-m-d') }}">@error('data_nascimento')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
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
                            <div class="form-group patient-form-actions">
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
        function initPatientCompletion(formSelector, options) {
            var form = document.querySelector(formSelector);

            if (!form) {
                return;
            }

            var progressBar = form.querySelector(options.progressSelector) || document.querySelector(options.progressSelector);
            var progressText = form.querySelector(options.progressTextSelector) || document.querySelector(options.progressTextSelector);
            var missingList = form.querySelector(options.missingListSelector) || document.querySelector(options.missingListSelector);

            if (!progressBar || !progressText || !missingList) {
                return;
            }

            var watchedFields = [
                { selector: '[name="nome"]', label: 'Nome completo', required: true },
                { selector: '[name="telefone"]', label: 'Celular', required: true },
                { selector: '[name="email"]', label: 'E-mail', required: true },
                { selector: '[name="cpf"]', label: 'CPF', required: false },
                { selector: '[name="sexo"]', label: 'Sexo', required: false },
                { selector: '[name="data_nascimento"]', label: 'Data de nascimento', required: false },
                { selector: '[name="cep"]', label: 'CEP', required: false },
                { selector: '[name="endereco"]', label: 'Endereço', required: false },
                { selector: '[name="bairro"]', label: 'Bairro', required: false },
                { selector: '[name="tipo_moradia"]', label: 'Tipo de imóvel', required: false }
            ].map(function(fieldConfig) {
                fieldConfig.element = form.querySelector(fieldConfig.selector);
                return fieldConfig;
            }).filter(function(fieldConfig) {
                return Boolean(fieldConfig.element);
            });

            function hasValue(element) {
                return String(element.value || '').trim() !== '';
            }

            function refreshCompletion() {
                var total = watchedFields.length;
                var completed = watchedFields.filter(function(fieldConfig) {
                    return hasValue(fieldConfig.element);
                }).length;
                var percent = total ? Math.round((completed / total) * 100) : 0;
                var missing = watchedFields.filter(function(fieldConfig) {
                    return !hasValue(fieldConfig.element);
                });

                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', String(percent));
                progressText.textContent = completed + ' de ' + total + ' campos preenchidos';

                if (!missing.length) {
                    missingList.textContent = 'Cadastro completo para continuar o atendimento com contexto suficiente.';
                    return;
                }

                var requiredMissing = missing.filter(function(fieldConfig) {
                    return fieldConfig.required;
                }).map(function(fieldConfig) {
                    return fieldConfig.label;
                });
                var optionalMissing = missing.filter(function(fieldConfig) {
                    return !fieldConfig.required;
                }).map(function(fieldConfig) {
                    return fieldConfig.label;
                });
                var parts = [];

                if (requiredMissing.length) {
                    parts.push('Obrigatórios pendentes: ' + requiredMissing.join(', '));
                }

                if (optionalMissing.length) {
                    parts.push('Recomendados pendentes: ' + optionalMissing.join(', '));
                }

                missingList.textContent = parts.join('. ');
            }

            watchedFields.forEach(function(fieldConfig) {
                fieldConfig.element.addEventListener('input', refreshCompletion);
                fieldConfig.element.addEventListener('change', refreshCompletion);
            });

            refreshCompletion();
        }

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

        function bindPhotoPreview(formSelector) {
            var form = document.querySelector(formSelector);

            if (!form) {
                return;
            }

            var fileInput = form.querySelector('[data-patient-photo-input]');
            var preview = form.querySelector('[data-patient-photo-preview]');
            var removeInput = form.querySelector('[name="remove_foto"]');
            var objectUrl = null;

            if (!fileInput || !preview) {
                return;
            }

            fileInput.addEventListener('change', function () {
                var file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }

                if (!file) {
                    preview.setAttribute('src', preview.dataset.defaultSrc || '');
                    return;
                }

                if (removeInput) {
                    removeInput.checked = false;
                }

                objectUrl = URL.createObjectURL(file);
                preview.setAttribute('src', objectUrl);
            });

            if (removeInput) {
                removeInput.addEventListener('change', function () {
                    if (removeInput.checked) {
                        if (objectUrl) {
                            URL.revokeObjectURL(objectUrl);
                            objectUrl = null;
                        }

                        fileInput.value = '';
                        preview.setAttribute('src', '{{ asset('backend/assets/img/avatar/avatar-1.png') }}');
                        return;
                    }

                    preview.setAttribute('src', preview.dataset.defaultSrc || '');
                });
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
        bindPhotoPreview('form[data-patient-live-check="true"]');
        initPatientCompletion('form[data-patient-live-check="true"]', {
            progressSelector: '[data-patient-progress-bar]',
            progressTextSelector: '[data-patient-progress-text]',
            missingListSelector: '[data-patient-missing-fields]'
        });
    });
</script>
@endsection
