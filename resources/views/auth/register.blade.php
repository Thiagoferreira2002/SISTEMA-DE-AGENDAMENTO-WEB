<x-guest-layout>
    <button class="back-button" onclick="history.back()" title="Voltar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
    </button>

    <div class="auth-header">
        <div class="logo-container">
            <div class="logo-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="logo-icon">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div class="logo-text">painelCms</div>
        </div>
        <h1>Criar conta</h1>
        <p>Preencha os dados para se cadastrar no painel</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="content">
        @csrf

        <div class="form-group">
            <label for="nome">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-icon">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Nome
            </label>
            <input id="nome" class="form-control" type="text" name="nome" value="{{ old('nome') }}" required autofocus autocomplete="given-name" placeholder="Seu primeiro nome" />
            <x-input-error :messages="$errors->get('nome')" class="message error" />
        </div>

        <div class="form-group">
            <label for="sobrenome">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-icon">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Sobrenome
            </label>
            <input id="sobrenome" class="form-control" type="text" name="sobrenome" value="{{ old('sobrenome') }}" required autocomplete="family-name" placeholder="Seu sobrenome" />
            <x-input-error :messages="$errors->get('sobrenome')" class="message error" />
        </div>

        <div class="form-group">
            <label for="fone">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-icon">
                    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                </svg>
                Telefone
            </label>
            <input id="fone" class="form-control phone-mask" type="text" name="fone" value="{{ old('fone') }}" required autocomplete="tel" placeholder="(55) 11 99999-9999" maxlength="19" />
            <x-input-error :messages="$errors->get('fone')" class="message error" />
        </div>

        <div class="form-group">
            <label for="email">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-icon">
                    <path d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email
            </label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="seu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="message error" />
        </div>

        <div class="form-group">
            <label for="password">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-icon">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <circle cx="12" cy="16" r="1"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Senha
            </label>
            <div class="password-input-wrapper">
                <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" placeholder="Digite sua senha" />
                <button type="button" class="password-toggle" data-target="password" title="Mostrar senha"></button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="message error" />
        </div>

        <div class="form-group">
            <label for="password_confirmation">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-icon">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <circle cx="12" cy="16" r="1"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Confirmar Senha
            </label>
            <div class="password-input-wrapper">
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirme sua senha" />
                <button type="button" class="password-toggle" data-target="password_confirmation" title="Mostrar senha"></button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="message error" />
        </div>

        <button type="submit" class="button-primary">
            <span>Criar conta</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="button-icon">
                <path d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </button>
    </form>

    <div class="auth-footer">
        <p>Já possui conta? <a href="{{ route('login') }}">Entrar agora</a></p>
    </div>
</x-guest-layout>
