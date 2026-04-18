<x-guest-layout>
    <div class="auth-header">
        <div class="logo-circle">P</div>
        <h1>Recuperar senha</h1>
        <p>Informe seu email para receber o link de redefinição.</p>
    </div>

    <x-auth-session-status class="message success" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="content">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="seu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="message error" />
        </div>

        <button type="submit" class="button-primary">Enviar link</button>
    </form>

    <div class="footer-link">
        <a href="{{ route('login') }}">Voltar ao login</a>
    </div>
</x-guest-layout>
