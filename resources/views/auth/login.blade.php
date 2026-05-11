<x-guest-layout>
    <div class="auth-header">
        <div class="logo-container">
            <img src="{{ asset('backend/assets/img/cms-logo.svg') }}" alt="CMS Consulta" class="auth-logo-image">
        </div>
        <h1>Bem-vindo de volta</h1>
        <p>Entre na sua conta para acessar o painel administrativo</p>
    </div>

    <x-auth-session-status class="message success" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="content">
        @csrf

        <div class="form-group">
            <label for="cpf">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-icon">
                    <path d="M4 7a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V7z"/>
                    <path d="M8 11h8"/>
                    <path d="M8 15h5"/>
                </svg>
                CPF
            </label>
            <input id="cpf" class="form-control" type="text" name="cpf" value="{{ old('cpf') }}" required autofocus autocomplete="username" inputmode="numeric" maxlength="14" placeholder="000.000.000-00" />
            <x-input-error :messages="$errors->get('cpf')" class="message error" />
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
                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="Digite sua senha" />
                <button type="button" class="password-toggle" data-target="password" title="Mostrar senha"></button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="message error" />
        </div>

        <div class="form-options">
            <label class="checkbox-container">
                <input id="remember_me" type="checkbox" name="remember" checked>
                <span class="checkmark"></span>
                Lembrar-me
            </label>
        </div>

        <button type="submit" class="button-primary">
            <span>Entrar na conta</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="button-icon">
                <path d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var cpfInput = document.getElementById('cpf');
            var rememberInput = document.getElementById('remember_me');
            var loginForm = document.querySelector('form[action="{{ route('login') }}"]');
            var rememberedCpfKey = 'auth.last-remembered-cpf';

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

            if (!cpfInput.value && rememberInput && window.localStorage) {
                var rememberedCpf = window.localStorage.getItem(rememberedCpfKey);

                if (rememberedCpf) {
                    cpfInput.value = formatCpf(rememberedCpf);
                    rememberInput.checked = true;
                }
            }

            cpfInput.addEventListener('input', function () {
                cpfInput.value = formatCpf(cpfInput.value);
            });

            if (loginForm && rememberInput && window.localStorage) {
                loginForm.addEventListener('submit', function () {
                    if (rememberInput.checked) {
                        window.localStorage.setItem(rememberedCpfKey, String(cpfInput.value || '').replace(/\D/g, ''));
                        return;
                    }

                    window.localStorage.removeItem(rememberedCpfKey);
                });
            }
        });
    </script>
</x-guest-layout>
