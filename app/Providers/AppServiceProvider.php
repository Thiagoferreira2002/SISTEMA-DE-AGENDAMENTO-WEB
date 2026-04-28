<?php

namespace App\Providers;

use App\Models\Agendamento;
use App\Models\Professional;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
            if ($user) {
                $notificationQuery = Agendamento::query()->with('professional');
                $notificationQuery->whereDate('data_agendamento', '>=', now()->toDateString())
                    ->where(function ($query) {
                        $query->whereNull('status')
                            ->orWhereIn('status', ['pendente', 'confirmado']);
                    });

                $viewerProfessional = null;
                $isProfessionalUser = $user->normalizedRole() === 'profissional';

                if ($isProfessionalUser) {
                    $viewerProfessional = $this->resolveProfessionalFromUser($user);

                    if ($viewerProfessional) {
                        $this->applyProfessionalScope($notificationQuery, $viewerProfessional, $user);
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
                    ->limit(6)
                    ->get()
                    ->map(function ($notification) use ($user, $viewerProfessional, $isProfessionalUser) {
                        $readBy = collect($notification->notification_read_by ?? [])->map(fn ($value) => (int) $value)->all();
                        $notification->navbar_is_unread = ! in_array((int) $user->id, $readBy, true);
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

    private function applyProfessionalScope(Builder $query, Professional $professional, $user): void
    {
        $nameCandidates = collect([
            $professional->nome,
            $user?->full_name,
            trim((string) (($user?->nome ?? '') . ' ' . ($user?->sobrenome ?? ''))),
            $user?->nome,
        ])
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->values()
            ->all();

        $query->where(function ($scopedQuery) use ($professional, $nameCandidates) {
            $scopedQuery->where('professional_id', $professional->id);

            foreach ($nameCandidates as $name) {
                $scopedQuery->orWhereRaw('LOWER(TRIM(medico)) = ?', [mb_strtolower($name)]);
            }
        });
    }
}
