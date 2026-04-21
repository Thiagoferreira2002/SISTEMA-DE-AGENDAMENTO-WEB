@extends('admin.layouts.master')
@section('content')
<style>
    .account-shell {
        padding: 28px;
        border-radius: 24px;
        background: linear-gradient(180deg, rgba(244,249,255,.96), rgba(255,255,255,.98));
        box-shadow: 0 16px 34px rgba(18, 58, 99, 0.08);
    }

    .account-hero {
        border: 0;
        border-radius: 22px;
        background: linear-gradient(135deg, #0d3358 0%, #155c99 52%, #1e90ff 100%);
        color: #ffffff;
        overflow: hidden;
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
        border: 1px solid rgba(30, 144, 255, 0.16);
        border-radius: 20px;
        box-shadow: 0 14px 30px rgba(18, 58, 99, 0.06);
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
</style>
<section class="section">
    @php
        $avatarUrl = $user->profile_photo_url;
        $roleLabel = $user->normalizedRole() === 'profissional' ? 'Profissional' : ($user->isClinicManager() ? 'Gestor da Clínica' : 'Administrador');
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
                            <div style="opacity: .88;">{{ $roleLabel }} com acesso ao painel administrativo.</div>
                        </div>
                    </div>
                    <div class="text-left text-lg-right">
                        @if($user->normalizedRole() !== 'admin')
                            <div class="small text-uppercase" style="letter-spacing: .08em; opacity: .82;">Segurança</div>
                            <div>A senha não pode ser alterada aqui. Apenas um administrador pode atualizar essa informação.</div>
                        @endif
                    </div>
                </div>
            </div>

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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nome">Nome *</label>
                                            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $user->nome) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sobrenome">Sobrenome</label>
                                            <input type="text" class="form-control @error('sobrenome') is-invalid @enderror" id="sobrenome" name="sobrenome" value="{{ old('sobrenome', $user->sobrenome) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cpf">CPF</label>
                                            <input type="text" class="form-control @error('cpf') is-invalid @enderror" id="cpf" name="cpf" value="{{ old('cpf', $user->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', preg_replace('/\D+/', '', $user->cpf)) : '') }}" maxlength="14">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fone">Telefone</label>
                                            <input type="text" class="form-control @error('fone') is-invalid @enderror" id="fone" name="fone" value="{{ old('fone', $user->fone) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="email">E-mail *</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nível de acesso</label>
                                            <input type="text" class="form-control" value="{{ $roleLabel }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                @if($user->normalizedRole() !== 'admin')
                                    <div class="alert alert-light border mt-2 mb-0">
                                        Senha: por segurança, a troca de senha não está disponível nesta área. Solicite a atualização a um administrador.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 d-flex flex-wrap" style="gap: 10px;">
                            <button type="submit" class="btn btn-primary">Salvar alterações</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Voltar</a>
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
            IMask(phoneInput, { mask: [{ mask: '(00) 00000-0000' }, { mask: '(00) 0000-0000' }] });
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
