<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        'capa',
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

    public function getProfilePhotoUrlAttribute(): string
    {
        $path = trim((string) $this->capa);

        if ($path === '') {
            return asset('backend/assets/img/avatar/avatar-1.png');
        }

        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

        if (Str::startsWith($normalizedPath, ['http://', 'https://'])) {
            return $normalizedPath;
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            return asset('storage/' . $normalizedPath);
        }

        if (is_file(public_path($normalizedPath))) {
            return asset($normalizedPath);
        }

        if (is_file(public_path('storage/' . $normalizedPath))) {
            return asset('storage/' . $normalizedPath);
        }

        return asset('backend/assets/img/avatar/avatar-1.png');
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

    public function roleLabel(): string
    {
        return match ($this->normalizedRole()) {
            'admin' => 'Administrador',
            'recepcionista' => 'Recepcionista',
            'profissional' => 'Profissional',
            'gestor_clinica' => 'Gestor da Clínica',
            default => 'Usuário do sistema',
        };
    }

    public function roleCapabilitySummary(): array
    {
        return match ($this->normalizedRole()) {
            'admin' => [
                'Acesso completo a agendamentos, pacientes e cadastros base.',
                'Gerenciamento de usuários, permissões e configurações do sistema.',
                'Edição de dados sensíveis, incluindo CPF e perfis de acesso.',
            ],
            'recepcionista' => [
                'Gestão de agenda, confirmações e cadastro de pacientes.',
                'Acompanhamento de notificações e atualização da própria conta.',
                'Sem acesso administrativo aos cadastros base avançados.',
            ],
            'profissional' => [
                'Visualização da própria agenda, fila de atendimento e serviços finalizados.',
                'Acompanhamento de notificações vinculadas ao próprio atendimento.',
                'Atualização da própria conta sem alterar dados sensíveis restritos.',
            ],
            'gestor_clinica' => [
                'Acompanhamento operacional da clínica, agenda e pacientes.',
                'Acesso aos cadastros base e ajustes operacionais do sistema.',
                'Permissão para atualizar CPF e dados cadastrais estratégicos.',
            ],
            default => ['Acesso limitado conforme permissões atribuídas.'],
        };
    }

    public function canEditCpf(): bool
    {
        return $this->normalizedRole() === 'admin' || $this->isClinicManager();
    }

    public function isClinicManager(): bool
    {
        return $this->normalizedRole() === 'gestor_clinica';
    }

    public function privilegeRank(): int
    {
        if ($this->nivel === 'admin' || $this->role === 'admin') {
            return 100;
        }

        return match ($this->normalizedRole()) {
            'gestor_clinica' => 80,
            'profissional' => 60,
            'recepcionista' => 40,
            default => 0,
        };
    }

    public function canManageUser(self $target, bool $allowSelf = false): bool
    {
        if ($target->isPrimaryAdmin()) {
            return false;
        }

        if ((int) $this->id === (int) $target->id) {
            return $allowSelf;
        }

        if ($this->nivel === 'admin' || $this->role === 'admin') {
            return true;
        }

        if (! $this->isClinicManager()) {
            return false;
        }

        return $target->privilegeRank() < $this->privilegeRank();
    }

    public static function submenuPermissionLabels(): array
    {
        return [
            'agendamentos' => 'Agendamentos',
            'pacientes' => 'Pacientes',
            'painel_doutor' => 'Painel do Profissional',
            'cadastros_base' => 'Cadastros Base',
            'minha_conta' => 'Minha Conta',
        ];
    }

    public static function submenuPermissionsForRole(?string $role): array
    {
        $normalizedRole = $role === 'medico' ? 'profissional' : ($role ?? '');

        return match ($normalizedRole) {
            'recepcionista' => ['agendamentos', 'pacientes'],
            'profissional' => ['agendamentos', 'pacientes', 'painel_doutor'],
            'gestor_clinica' => array_keys(static::submenuPermissionLabels()),
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
            'cadastros_base' => ['admin.settings.'],
            'minha_conta' => ['admin.account.', 'admin.tutorial'],
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

        if ($routeName === 'admin.dashboard' && ($this->isClinicManager() || $this->submenuPermissions() !== [])) {
            return true;
        }

        if ($routeName === 'admin.notifications.read') {
            return $this->submenuPermissions() !== [];
        }

        if ($routeName === 'admin.tutorial') {
            return $this->submenuPermissions() !== [];
        }

        if (str_starts_with($routeName, 'admin.account.')) {
            return true;
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

    public function canManageCadastrosBase(): bool
    {
        return $this->nivel === 'admin' || $this->role === 'admin' || $this->isClinicManager();
    }

    public function canMutateOutsideCadastrosBase(): bool
    {
        return $this->nivel === 'admin' || $this->role === 'admin';
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
