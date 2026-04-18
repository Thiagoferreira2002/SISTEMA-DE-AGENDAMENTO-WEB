@extends('admin.layouts.auth')

@section('content')
    <div class="logo">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: inline-block; line-height: 80px; color: white; font-size: 36px; font-weight: bold; text-align: center;">P</div>
    </div>
    <h2>Login do Administrador</h2>

    @if (session('status'))
        <div class="success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="cpf">CPF</label>
            <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}" required autofocus inputmode="numeric" maxlength="14" placeholder="Digite seu CPF">
            @error('cpf')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required placeholder="Digite sua senha">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="checkbox-group">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Lembrar-me</label>
        </div>

        <button type="submit" class="btn">Entrar no Sistema</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var cpfInput = document.getElementById('cpf');

            if (!cpfInput) {
                return;
            }

            var formatCpf = function (value) {
                var digits = String(value || '').replace(/\D/g, '').slice(0, 11);

                if (digits.length <= 3) {
                    return digits;
                }

                if (digits.length <= 6) {
                    return digits.slice(0, 3) + '.' + digits.slice(3);
                }

                if (digits.length <= 9) {
                    return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6);
                }

                return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6, 9) + '-' + digits.slice(9);
            };

            cpfInput.value = formatCpf(cpfInput.value);
            cpfInput.addEventListener('input', function () {
                cpfInput.value = formatCpf(cpfInput.value);
            });
        });
    </script>
@endsection
