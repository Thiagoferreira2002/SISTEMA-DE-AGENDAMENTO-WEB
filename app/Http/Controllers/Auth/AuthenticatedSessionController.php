<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Exibe a view de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Manipula uma tentativa de autenticação.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Tenta autenticar os dados (CPF e senha)
        $request->authenticate();

        if (Auth::user()?->status !== 'ativo') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'cpf' => 'Este usuário está inativo e não pode mais acessar o sistema.',
            ])->onlyInput('cpf');
        }

        // 2. Regenera a sessão para evitar ataques de fixação de sessão
        $request->session()->regenerate();

        // 3. Redireciona para o dashboard correto por tipo de usuário
        $user = Auth::user();

        $redirect = $user && $user->canAccessRouteName('admin.dashboard')
            ? route('admin.dashboard')
            : route('cliente.dashboard');

        return redirect()->intended($redirect);
    }

    /**
     * Encerra a sessão autenticada.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
