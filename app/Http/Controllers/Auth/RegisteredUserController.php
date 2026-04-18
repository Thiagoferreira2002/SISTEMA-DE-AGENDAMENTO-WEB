<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $normalizedPhone = preg_replace('/\D/', '', (string) $request->input('fone'));

        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
            'fone' => $normalizedPhone !== '' ? $normalizedPhone : null,
        ]);

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sobrenome' => ['required', 'string', 'max:255'],
            'fone' => ['required', 'string', 'regex:/^\d{10,11}$/', 'unique:users,fone'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'fone.regex' => 'O telefone deve conter entre 10 e 11 dígitos.',
            'fone.unique' => 'Este telefone já está cadastrado para outro usuário.',
            'email.unique' => 'Este e-mail já está cadastrado para outro usuário.',
        ]);

        $user = User::create([
            'nome' => $request->nome,
            'sobrenome' => $request->sobrenome,
            'fone' => $request->fone,
            'nivel' => 'user',
            'status' => 'ativo',
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('cliente.dashboard'));
    }
}
