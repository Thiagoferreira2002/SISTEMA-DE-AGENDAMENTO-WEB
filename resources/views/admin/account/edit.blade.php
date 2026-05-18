@extends('admin.layouts.master')
@section('content')
<style>
    .account-shell {
        padding: 28px;
        border-radius: 24px;
        background: linear-gradient(180deg, rgba(244,249,255,.96), rgba(255,255,255,.98));
        box-shadow: 0 16px 34px rgba(18, 58, 99, 0.08);
    }

    html[data-theme="dark"] .account-shell {
        background: linear-gradient(180deg, rgba(19,33,49,.98), rgba(16,29,42,.98));
        box-shadow: 0 18px 40px rgba(2, 8, 15, 0.34);
    }

    .account-hero {
        border: 1px solid #d2dbe6 !important;
        border-radius: 22px;
        background: linear-gradient(135deg, #0d3358 0%, #155c99 52%, #1e90ff 100%);
        color: #ffffff;
        overflow: hidden;
        box-shadow: inset 0 0 0 1px #d2dbe6;
    }

    html[data-theme="dark"] .account-hero {
        border-color: #000000 !important;
        box-shadow: inset 0 0 0 1px #000000;
    }

    .account-hero .card-body {
        padding: 28px;
    }

    .account-avatar {
        width: 92px;
        height: 92px;
        border-radius: 24px;
        object-fit: cover;
        border: 4px solid rgba(255,255,255,.28);
        box-shadow: 0 10px 26px rgba(8, 30, 52, 0.18);
    }

    .account-form-card {
        border: 1px solid #d2dbe6 !important;
        border-radius: 20px;
        box-shadow: inset 0 0 0 1px #d2dbe6, 0 14px 30px rgba(18, 58, 99, 0.06);
    }

    html[data-theme="dark"] .account-form-card {
        background: linear-gradient(180deg, rgba(22,40,59,.98), rgba(19,33,49,.98));
        border-color: #000000 !important;
        box-shadow: inset 0 0 0 1px #000000, 0 18px 34px rgba(2, 8, 15, 0.28);
    }

    .account-form-card .card-body {
        padding: 26px;
    }

    .account-form-card .form-control {
        min-height: 46px;
        border-radius: 12px;
    }

    .account-photo-preview {
        width: 124px;
        height: 124px;
        border-radius: 28px;
        object-fit: cover;
        border: 1px solid rgba(30, 144, 255, 0.16);
        background: #eef6ff;
    }

    html[data-theme="dark"] .account-photo-preview {
        background: #16283b;
        border-color: rgba(143, 197, 255, 0.18);
    }

    .account-access-card {
        border: 1px solid #d2dbe6;
        border-radius: 16px;
        background: #f8fbff;
        padding: 16px 16px 8px;
        max-width: 540px;
    }

    html[data-theme="dark"] .account-access-card {
        background: rgba(22, 40, 59, 0.92);
        border-color: #000000;
    }

    .account-access-card ul {
        padding-left: 18px;
        margin-bottom: 0;
    }

    .account-phone-input {
        letter-spacing: 0.04em;
        font-variant-numeric: tabular-nums;
    }

    .account-feedback-stack {
        margin: 0 0 24px;
    }

    .account-feedback-stack .alert {
        margin-bottom: 0;
    }

    .account-form-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .account-form-actions .btn {
        width: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    @media (max-width: 767.98px) {
        .account-shell {
            padding: 18px;
            border-radius: 18px;
        }

        .account-hero .card-body,
        .account-form-card .card-body {
            padding: 18px;
        }

        .account-avatar {
            width: 76px;
            height: 76px;
            border-radius: 20px;
        }

        .account-photo-preview {
            width: 104px;
            height: 104px;
            border-radius: 22px;
        }

        .account-access-card {
            max-width: 100%;
        }

        .account-form-actions .btn {
            padding-left: 14px !important;
            padding-right: 14px !important;
        }
    }
</style>
<section class="section">
    @php
        $avatarUrl = $user->profile_photo_url;
        $roleLabel = $user->roleLabel();
        $roleSummary = $user->roleCapabilitySummary();
        $canEditCpf = $user->canEditCpf();
    @endphp

    <div class="section-header">
        <h1>Minha Conta</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Minha Conta</div>
        </div>
    </div>

    <div class="section-body">
        <div class="account-shell">
            <div class="card account-hero mb-4">
                <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between" style="gap: 18px;">
                    <div class="d-flex align-items-center" style="gap: 18px;">
                        <img src="{{ $avatarUrl }}" alt="Foto do perfil" class="account-avatar">
                        <div>
                            <div class="text-uppercase small" style="letter-spacing: .08em; opacity: .82;">Configurações do perfil</div>
                            <h2 class="mb-1" style="font-weight: 700;">{{ $user->full_name }}</h2>
                            <div style="opacity: .88;">{{ $roleLabel }} com permissões automáticas conforme o perfil vinculado à conta.</div>
                        </div>
                    </div>
                    <div class="text-left text-lg-right"></div>
                </div>
            </div>

            @if(session('success') || $errors->any())
                <div class="account-feedback-stack">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Não foi possível atualizar sua conta.</strong>
                            <ul class="mb-0 mt-2 pl-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <div class="card account-form-card">
                <div class="card-body">
                    <form action="{{ route('admin.account.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row align-items-start">
                            <div class="col-lg-4 mb-4 mb-lg-0">
                                <label class="font-weight-bold d-block">Foto do perfil</label>
                                <img src="{{ $avatarUrl }}" alt="Prévia da foto" class="account-photo-preview mb-3" id="account-photo-preview">
                                <input type="file" class="form-control-file" name="capa" id="capa" accept=".jpg,.jpeg,.png,.webp">
                                <small class="text-muted d-block mt-2">Envie uma imagem JPG, PNG ou WEBP com até 2 MB.</small>
                            </div>

                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-group">
                                            <label for="nome">Nome *</label>
                                            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $user->nome) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6">
                                        <div class="form-group">
                                            <label for="sobrenome">Sobrenome</label>
                                            <input type="text" class="form-control @error('sobrenome') is-invalid @enderror" id="sobrenome" name="sobrenome" value="{{ old('sobrenome', $user->sobrenome) }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4">
                                        <div class="form-group">
                                            <label for="cpf">CPF</label>
                                            <input type="text" class="form-control @error('cpf') is-invalid @enderror" id="cpf" name="cpf" value="{{ old('cpf', $user->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', preg_replace('/\D+/', '', $user->cpf)) : '') }}" maxlength="14" {{ $canEditCpf ? '' : 'readonly' }}>
                                            @if(! $canEditCpf)
                                                <small class="text-muted d-block mt-2">O CPF só pode ser alterado por Administrador ou Gestor da Clínica.</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4">
                                        <div class="form-group">
                                            <label for="fone">Telefone</label>
                                            <input type="text" class="form-control account-phone-input @error('fone') is-invalid @enderror" id="fone" name="fone" value="{{ old('fone', $user->fone) }}" placeholder="(75) 98888 - 8297">
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-8">
                                        <div class="form-group">
                                            <label for="email">E-mail *</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4">
                                        <div class="form-group">
                                            <label>Nível de acesso</label>
                                            <input type="text" class="form-control" value="{{ $roleLabel }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="account-access-card mt-2">
                                            <label class="font-weight-bold d-block">Funções desta conta</label>
                                            <ul>
                                                @foreach($roleSummary as $summaryItem)
                                                    <li>{{ $summaryItem }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="mt-4 account-form-actions">
                            <button type="submit" class="btn btn-primary px-4">Salvar alterações</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-4">Voltar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var cpfInput = document.getElementById('cpf');
        var phoneInput = document.getElementById('fone');
        var photoInput = document.getElementById('capa');
        var photoPreview = document.getElementById('account-photo-preview');

        if (window.IMask && cpfInput) {
            IMask(cpfInput, { mask: '000.000.000-00' });
        }

        if (window.IMask && phoneInput) {
            IMask(phoneInput, { mask: [{ mask: '(00) 00000 - 0000' }, { mask: '(00) 0000 - 0000' }] });
        }

        if (photoInput && photoPreview) {
            photoInput.addEventListener('change', function () {
                var file = photoInput.files && photoInput.files[0] ? photoInput.files[0] : null;

                if (!file) {
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (event) {
                    photoPreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endsection
