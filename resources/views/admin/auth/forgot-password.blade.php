@extends('admin.layouts.auth')

@section('content')
    <div class="logo">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: inline-block; line-height: 80px; color: white; font-size: 36px; font-weight: bold; text-align: center;">P</div>
    </div>
    <h2>Recuperar Senha</h2>

    <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 14px;">Digite seu email para receber um link de redefinição de senha.</p>

    @if (session('status'))
        <div class="success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Digite seu email">
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn">Enviar Link de Redefinição</button>
    </form>

    <div class="links">
        <a href="{{ route('admin.login') }}">Voltar ao Login</a>
    </div>
@endsection