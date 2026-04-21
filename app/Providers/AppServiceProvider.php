<?php

namespace App\Providers;

use App\Models\Agendamento;
use App\Models\Professional;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        View::composer('admin.layouts.navbar', function ($view) {
            $user = Auth::user();
            $notifications = collect();
            $notificationTargetUrl = route('admin.agendamentos.calendar');
            $seenAt = session('navbar_notifications_seen_at');
            $seenAtCarbon = $seenAt ? Carbon::parse($seenAt) : null;

            if ($user) {
                $notificationQuery = Agendamento::query()->with('professional');

                $viewerProfessional = null;
                $isProfessionalUser = $user->normalizedRole() === 'profissional';

                if ($isProfessionalUser) {
                    $viewerProfessional = $this->resolveProfessionalFromUser($user);

                    if ($viewerProfessional) {
                        $notificationQuery->where('professional_id', $viewerProfessional->id)
                            ->whereDate('data_agendamento', '>=', now()->toDateString())
                            ->whereNotIn('status', ['cancelado', 'concluido'])
                            ->limit(6);
                    } else {
                        $notificationQuery->whereRaw('1 = 0');
                    }
                }

                $notificationTargetUrl = $viewerProfessional
                    ? route('admin.agendamentos.calendar', ['professional_id' => $viewerProfessional->id])
                    : route('admin.agendamentos.calendar');

                $notifications = $notificationQuery
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->get()
                    ->map(function ($notification) use ($seenAtCarbon, $viewerProfessional, $isProfessionalUser) {
                        $referenceDate = $notification->updated_at ?? $notification->created_at;
                        $notification->navbar_is_unread = ! $seenAtCarbon || ($referenceDate && $referenceDate->greaterThan($seenAtCarbon));
                        $notification->navbar_target_url = $this->buildNotificationTargetUrl($notification, $viewerProfessional, $isProfessionalUser);

                        return $notification;
                    });
            }

            $unreadNotificationCount = $notifications->where('navbar_is_unread', true)->count();

            $view->with('navbarNotifications', $notifications)
                ->with('navbarNotificationCount', $unreadNotificationCount)
                ->with('navbarNotificationTargetUrl', $notificationTargetUrl);
        });
    }

    private function resolveProfessionalFromUser($user): ?Professional
    {
        $professional = Professional::query()
            ->where('user_id', $user->id)
            ->where('ativo', true)
            ->first();

        if ($professional) {
            return $professional;
        }

        $userCpf = preg_replace('/\D+/', '', (string) ($user->cpf ?? ''));

        if ($userCpf !== '') {
            $professional = Professional::query()
                ->where('ativo', true)
                ->get()
                ->first(function (Professional $item) use ($userCpf) {
                    return preg_replace('/\D+/', '', (string) ($item->cpf ?? '')) === $userCpf;
                });

            if ($professional) {
                return $professional;
            }
        }

        $fullName = mb_strtolower(trim((string) ($user->full_name ?? '')));

        if ($fullName === '') {
            return null;
        }

        return Professional::query()
            ->where('ativo', true)
            ->get()
            ->first(function (Professional $item) use ($fullName) {
                return mb_strtolower(trim((string) $item->nome)) === $fullName;
            });
    }

    private function buildNotificationTargetUrl(Agendamento $notification, ?Professional $viewerProfessional, bool $isProfessionalUser): string
    {
        $parameters = [
            'focus_date' => optional($notification->data_agendamento)->format('Y-m-d'),
            'open_agendamento' => $notification->id,
            'show_details' => 1,
        ];

        if (! $isProfessionalUser && ! empty($notification->professional_id)) {
            $parameters['professional_id'] = $notification->professional_id;
        }

        if ($viewerProfessional && $isProfessionalUser) {
            $parameters['professional_id'] = $viewerProfessional->id;
        }

        return route('admin.agendamentos.calendar', array_filter($parameters, fn ($value) => $value !== null && $value !== ''));
    }
}
