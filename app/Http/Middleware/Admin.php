<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->status !== 'ativo') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('warning', 'Seu usuário está inativo.');
        }

        if ($user->nivel === 'admin') {
            return $next($request);
        }

        if ($user->nivel === 'user' && $user->canAccessRouteName($request->route()?->getName())) {
            if ($user->isClinicManager() && ! $this->canClinicManagerProceed($request)) {
                return redirect()->back()->with('layout_warning', 'O Gestor da Clínica pode editar apenas Cadastros Base e Minha Conta. Nos demais módulos o acesso é somente para visualização.');
            }

            return $next($request);
        }

        return redirect()->route('cliente.dashboard')->with('layout_warning', 'Seu perfil não possui acesso a este módulo.');
    }

    private function canClinicManagerProceed(Request $request): bool
    {
        $routeName = (string) $request->route()?->getName();

        if ($routeName === '') {
            return false;
        }

        if (str_starts_with($routeName, 'admin.account.')) {
            return true;
        }

        if (str_starts_with($routeName, 'admin.settings.')) {
            return true;
        }

        if (! $request->isMethod('get')) {
            return false;
        }

        return ! $this->isRestrictedReadOnlyGetRoute($routeName);
    }

    private function isRestrictedReadOnlyGetRoute(string $routeName): bool
    {
        return str_ends_with($routeName, '.create') || str_ends_with($routeName, '.edit');
    }
}
