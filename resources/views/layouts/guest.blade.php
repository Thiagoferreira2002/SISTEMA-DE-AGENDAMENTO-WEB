<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'painelCms') }}</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top, #e0e7ff 0%, #f8fafc 55%, #eef2ff 100%);
            color: #1f2937;
        }
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.12);
            padding: 36px;
            overflow: hidden;
            position: relative;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #4f46e5, #8b5cf6);
        }
        .auth-card h1,
        .auth-card p {
            margin: 0;
        }
        .auth-card h1 {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
        }
        .auth-card p {
            margin-top: 8px;
            color: #4b5563;
            line-height: 1.75;
        }
        .content {
            margin-top: 30px;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .auth-header .logo-circle {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #8b5cf6);
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            box-shadow: 0 8px 32px rgba(79, 70, 229, 0.3);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 24px;
            display: flex;
            justify-content: center;
        }
        .auth-logo-image {
            width: min(100%, 220px);
            height: auto;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 10px 22px rgba(15, 77, 152, 0.14));
        }
        .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: #4f46e5;
            margin-top: 8px;
            letter-spacing: -0.02em;
        }
        .logo-icon {
            width: 32px;
            height: 32px;
        }
        .auth-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            color: #111827;
        }
        .auth-header p {
            margin: 0;
            color: #4b5563;
            line-height: 1.7;
            font-size: 15px;
        }
        .back-button {
            position: absolute;
            top: 24px;
            left: 24px;
            background: white;
            border: 2px solid #e5e7eb;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #6b7280;
            padding: 0;
        }
        .back-button:hover {
            background: #f3f4f6;
            border-color: #4f46e5;
            color: #4f46e5;
            transform: translateX(-2px);
        }
        .back-button svg {
            width: 20px;
            height: 20px;
        }
        .form-group {
            margin-bottom: 20px;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }
        .field-icon {
            width: 16px;
            height: 16px;
            color: #6b7280;
            flex-shrink: 0;
        }
        .form-control {
            width: 100%;
            padding: 14px 50px 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            font-size: 15px;
            color: #111827;
            background: #f8fafc;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
            box-sizing: border-box;
            position: relative;
        }
        .form-control:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
            background: #ffffff;
        }
        .password-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            padding: 6px 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 36px;
            width: 36px;
            top: 50%;
            transform: translateY(-50%);
        }
        .password-toggle:hover {
            color: #374151;
            background: #f3f4f6;
        }
        .password-toggle svg {
            width: 20px;
            height: 20px;
            transition: all 0.2s ease;
        }
        .password-toggle.active svg {
            color: #4f46e5;
        }
        .checkbox-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .checkbox-row label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #4b5563;
        }
        .checkbox-row input[type='checkbox'] {
            width: 18px;
            height: 18px;
            accent-color: #4f46e5;
        }
        .link-text {
            font-size: 14px;
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
        }
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #4b5563;
            cursor: pointer;
            position: relative;
        }
        .checkbox-container input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            background: #ffffff;
            position: relative;
            transition: all 0.2s ease;
        }
        .checkbox-container input:checked ~ .checkmark {
            background: #4f46e5;
            border-color: #4f46e5;
        }
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .checkbox-container input:checked ~ .checkmark:after {
            display: block;
        }
        .forgot-link {
            font-size: 14px;
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .forgot-link:hover {
            color: #3730a3;
            text-decoration: underline;
        }
        .button-primary {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #4f46e5, #8b5cf6);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }
        .button-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        .button-primary:hover::before {
            left: 100%;
        }
        .button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.3);
        }
        .button-icon {
            width: 18px;
            height: 18px;
            transition: transform 0.2s ease;
        }
        .button-primary:hover .button-icon {
            transform: translateX(2px);
        }
        .footer-link {
            margin-top: 24px;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }
        .footer-link a {
            color: #4f46e5;
            font-weight: 700;
            text-decoration: none;
        }
        .auth-footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .auth-footer p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }
        .auth-footer a {
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .auth-footer a:hover {
            color: #3730a3;
            text-decoration: underline;
        }
        @media (max-width: 640px) {
            .page-wrapper {
                padding: 16px;
            }
            .auth-card {
                padding: 24px;
                border-radius: 16px;
            }
            .auth-logo-image {
                width: min(100%, 180px);
            }
            .auth-header h1 {
                font-size: 24px;
            }
            .logo-circle {
                width: 64px;
                height: 64px;
            }
            .logo-icon {
                width: 24px;
                height: 24px;
            }
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            .button-primary {
                padding: 14px;
                font-size: 15px;
            }
        }
        .message {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            font-weight: 600;
        }
        .message.success { background: #ecfdf5; color: #166534; }
        .message.error { background: #fef2f2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="auth-card">
            {{ $slot }}
        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para toggle de senha
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';

            // Atualizar ícone
            button.innerHTML = isPassword ?
                `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    <path d="M3 3l18 18"/>
                </svg>` :
                `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>`;

            button.classList.toggle('active', isPassword);
        }

        // Adicionar event listeners para botões de toggle
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const inputId = this.getAttribute('data-target');
                togglePassword(inputId, this);
            });
        });

        // Inicializar ícones
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>`;
        });

        // Gerenciar checkbox "Lembrar-me"
        const rememberCheckbox = document.getElementById('remember_me');
        if (rememberCheckbox) {
            // Recuperar estado salvo do localStorage
            const savedRemember = localStorage.getItem('rememberMe');
            if (savedRemember === 'true') {
                rememberCheckbox.checked = true;
            }

            // Salvar estado quando checkbox mudar
            rememberCheckbox.addEventListener('change', function() {
                localStorage.setItem('rememberMe', this.checked);
            });
        }

        // Máscara para telefone (BR): (55) DD 99999-9999
        const phoneInput = document.querySelector('.phone-mask');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');

                if (value.length > 13) {
                    value = value.substring(0, 13);
                }

                let formatted = '';
                if (value.length > 0) {
                    formatted = '(' + value.substring(0, 2);
                }
                if (value.length > 2) {
                    formatted += ') ' + value.substring(2, 4);
                }
                if (value.length > 4) {
                    formatted += ' ' + value.substring(4, 9);
                }
                if (value.length > 9) {
                    formatted += '-' + value.substring(9, 13);
                }

                e.target.value = formatted;
            });
        }
    });
</script>
</html>
