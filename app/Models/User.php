<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = 'users';
    protected $fillable = [
        'nome',
        'sobrenome',
        'cpf',
        'fone',
        'nivel',
        'role',
        'permissions',
        'status',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->nome ?? '') . ' ' . ($this->sobrenome ?? ''));
    }

    public function isPrimaryAdmin(): bool
    {
        $normalized = Str::of($this->full_name)
            ->ascii()
            ->lower()
            ->squish()
            ->toString();

        $isAdminRole = $this->nivel === 'admin' || $this->role === 'admin';

        return $isAdminRole && str_contains($normalized, 'thiago') && str_contains($normalized, 'ferreira');
    }

    public function normalizedRole(): string
    {
        $role = $this->role ?? $this->nivel ?? '';

        return $role === 'medico' ? 'profissional' : $role;
    }

    public static function submenuPermissionLabels(): array
    {
        return [
            'agendamentos' => 'Agendamentos',
            'pacientes' => 'Pacientes',
            'painel_doutor' => 'Painel do Profissional',
        ];
    }

    public static function submenuPermissionsForRole(?string $role): array
    {
        $normalizedRole = $role === 'medico' ? 'profissional' : ($role ?? '');

        return match ($normalizedRole) {
            'recepcionista' => ['agendamentos', 'pacientes'],
            'profissional' => ['agendamentos', 'pacientes', 'painel_doutor'],
            'admin' => array_keys(static::submenuPermissionLabels()),
            default => [],
        };
    }

    public static function submenuRouteMap(): array
    {
        return [
            'agendamentos' => ['admin.agendamentos.'],
            'pacientes' => ['admin.patients.', 'admin.agendamentos.create'],
            'painel_doutor' => ['admin.doctor.'],
        ];
    }

    public function submenuPermissions(): array
    {
        if ($this->nivel === 'admin' || $this->role === 'admin') {
            return array_keys(static::submenuPermissionLabels());
        }

        return static::submenuPermissionsForRole($this->normalizedRole());
    }

    public function canAccessSubmenu(string $submenu): bool
    {
        if ($this->nivel === 'admin' || $this->role === 'admin') {
            return true;
        }

        return in_array($submenu, $this->submenuPermissions(), true);
    }

    public function canAccessRouteName(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        if ($this->nivel === 'admin' || $this->role === 'admin') {
            return true;
        }

        foreach (static::submenuRouteMap() as $submenu => $routePrefixes) {
            foreach ($routePrefixes as $routePrefix) {
                if (str_starts_with($routeName, $routePrefix) && $this->canAccessSubmenu($submenu)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->nivel === 'admin' || $this->role === 'admin') {
            return true;
        }

        if (array_key_exists($permission, static::submenuPermissionLabels())) {
            return $this->canAccessSubmenu($permission);
        }

        return in_array($permission, $this->permissions ?? $this->submenuPermissions(), true);
    }
}
