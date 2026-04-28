@extends('admin.layouts.master')
@section('content')
<style>
    .user-edit-modal {
        z-index: 10060;
    }

    .users-page-alert {
        margin-top: 72px;
    }

    .users-actions {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .users-actions form {
        margin: 0;
    }

    .users-actions .btn,
    .users-actions .text-muted {
        white-space: nowrap;
    }

    .user-edit-modal + .modal-backdrop,
    .modal-backdrop.show {
        z-index: 10050;
    }

    .user-edit-modal .modal-dialog,
    .user-edit-modal .modal-content {
        pointer-events: auto;
    }

    .password-toggle-group {
        position: relative;
    }

    .password-toggle-group .form-control {
        padding-right: 44px;
    }

    .password-toggle-btn {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #6c757d;
        padding: 0;
        line-height: 1;
        cursor: pointer;
    }

</style>

<section class="section">
    <div class="section-header">
        <h1>Usuários e Permissões</h1>
    </div>

    @php
        $authenticatedUser = auth()->user();
        $roleLabels = [
            'medico' => 'Profissional',
            'profissional' => 'Profissional',
            'recepcionista' => 'Recepcionista',
            'gestor_clinica' => 'Gestor da Clínica',
            'admin' => 'Administrador',
        ];

        $statusLabels = [
            'ativo' => 'Ativo',
            'cancelado' => 'Inativo',
        ];

        $permissionLabels = [
            'agendamentos' => 'Agendamentos',
            'pacientes' => 'Pacientes',
            'painel_doutor' => 'Painel do Profissional',
            'cadastros_base' => 'Cadastros Base',
            'minha_conta' => 'Minha Conta',
        ];

        $renderPermissionBadges = function ($permissions) use ($permissionLabels) {
            $permissions = is_array($permissions) ? $permissions : [];

            if (empty($permissions)) {
                return '<span class="text-muted">Nenhum módulo liberado.</span>';
            }

            return collect($permissions)
                ->map(fn ($permission) => '<span class="badge badge-light border mr-1 mb-1">'.e($permissionLabels[$permission] ?? $permission).'</span>')
                ->implode('');
        };

        $formatPhone = function ($value) {
            $digits = preg_replace('/\D/', '', (string) $value);

            if (strlen($digits) === 11) {
                return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $digits);
            }

            if (strlen($digits) === 10) {
                return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $digits);
            }

            return $value ?: 'Não informado';
        };

        $formatCpf = function ($value) {
            $digits = preg_replace('/\D/', '', (string) $value);

            if (strlen($digits) === 11) {
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
            }

            return $value ?: 'Não informado';
        };

    @endphp

    <div class="section-body">
        @if($errors->any())
            <div class="alert alert-danger users-page-alert">
                <strong>Não foi possível salvar o usuário.</strong>
                <ul class="mb-0 mt-2 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger users-page-alert">{{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning users-page-alert">{{ session('warning') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success users-page-alert">{{ session('success') }}</div>
        @endif
        @if(!empty($setupWarning))
            <div class="alert alert-warning users-page-alert">{{ $setupWarning }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-header"><h4>Novo usuário</h4></div>
            <div class="card-body">
                <form action="{{ route('admin.settings.users.store') }}" method="POST" data-draft-form="true" data-draft-key="admin.settings.users.create">
                    @csrf
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Nome *</label><input type="text" class="form-control @error('nome') is-invalid @enderror" name="nome" value="{{ old('nome') }}" required>@error('nome')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Sobrenome</label><input type="text" class="form-control @error('sobrenome') is-invalid @enderror" name="sobrenome" value="{{ old('sobrenome') }}">@error('sobrenome')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>CPF</label><input type="text" class="form-control cpf-mask @error('cpf') is-invalid @enderror" id="new-user-cpf" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00" maxlength="14" inputmode="numeric">@error('cpf')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Telefone</label><input type="text" class="form-control phone-mask @error('fone') is-invalid @enderror" id="new-user-phone" name="fone" value="{{ old('fone') }}" placeholder="(11) 99999-9999" maxlength="15" inputmode="numeric">@error('fone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Status *</label><select class="form-control @error('status') is-invalid @enderror" name="status" required><option value="ativo" {{ old('status', 'ativo') === 'ativo' ? 'selected' : '' }}>Ativo</option><option value="cancelado" {{ old('status') === 'cancelado' ? 'selected' : '' }}>Inativar</option></select>@error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>E-mail *</label><input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>@error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Senha *</label>
                                <div class="password-toggle-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="new-user-password" required>
                                    <button type="button" class="password-toggle-btn" data-target="#new-user-password" aria-label="Mostrar senha">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Confirmar senha *</label>
                                <div class="password-toggle-group">
                                    <input type="password" class="form-control" name="password_confirmation" id="new-user-password-confirmation" required>
                                    <button type="button" class="password-toggle-btn" data-target="#new-user-password-confirmation" aria-label="Mostrar confirmação de senha">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3"><div class="form-group"><label>Papel *</label><select class="form-control @error('role') is-invalid @enderror" id="new-user-role" name="role" required><option value="recepcionista" {{ old('role', 'recepcionista') === 'recepcionista' ? 'selected' : '' }}>Recepcionista</option><option value="profissional" {{ in_array(old('role'), ['profissional', 'medico'], true) ? 'selected' : '' }}>Profissional</option><option value="gestor_clinica" {{ old('role') === 'gestor_clinica' ? 'selected' : '' }}>Gestor da Clínica</option></select>@error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                    </div>

                    <button type="submit" class="btn btn-primary">Cadastrar usuário</button>
                </form>
            </div>
        </div>

        <div class="card" id="usuarios-permissoes">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
                <h4 class="mb-0">Perfis de acesso e permissões de Módulo</h4>
                <form action="{{ route('admin.settings.users') }}" method="GET" class="d-flex flex-wrap align-items-end" style="gap: 10px;">
                    <div class="form-group mb-0">
                        <label for="cpf-search" class="mb-1">Pesquisar por CPF ou nome</label>
                        <input type="text" class="form-control" id="cpf-search" name="cpf_search" value="{{ $userSearch ?? '' }}" placeholder="Digite o CPF ou o nome do usuário" style="min-width: 360px;">
                    </div>
                    <div class="form-group mb-0">
                        <label for="role-filter" class="mb-1">Filtrar papel</label>
                        <select class="form-control" id="role-filter" name="role_filter" style="min-width: 220px;">
                            <option value="">Todos os perfis</option>
                            <option value="recepcionista" {{ ($roleFilter ?? '') === 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                            <option value="profissional" {{ ($roleFilter ?? '') === 'profissional' ? 'selected' : '' }}>Profissional</option>
                            <option value="gestor_clinica" {{ ($roleFilter ?? '') === 'gestor_clinica' ? 'selected' : '' }}>Gestor da Clínica</option>
                        </select>
                    </div>
                    <div class="d-flex" style="gap: 8px;">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                        @if(!empty($userSearch) || !empty($roleFilter))
                            <a href="{{ route('admin.settings.users') }}" class="btn btn-light border">Limpar</a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Papel</th>
                                <th>Permissões</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php
                                    $isAuthenticatedClinicManagerOwnAccount = $authenticatedUser?->isClinicManager()
                                        && (int) $authenticatedUser->id === (int) $user->id;
                                @endphp
                                <tr>
                                    <td>
                                        {{ trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')) }}
                                    </td>
                                    <td>{{ $authenticatedUser?->isClinicManager() && $user->isPrimaryAdmin() ? 'Protegido' : $formatCpf($user->cpf) }}</td>
                                    <td>
                                        @if($user->isPrimaryAdmin())
                                            <div class="d-flex justify-content-center">
                                                <span class="badge badge-dark">Admin</span>
                                            </div>
                                        @else
                                            <div class="text-center font-weight-600">{{ $roleLabels[$user->normalizedRole()] ?? ucfirst($user->normalizedRole()) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->isPrimaryAdmin())
                                            <div class="text-center">
                                                <span class="text-muted">Acesso total fixo</span>
                                            </div>
                                        @else
                                            <div class="d-flex flex-wrap">
                                                {!! $renderPermissionBadges($user->submenuPermissions()) !!}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->status === 'ativo' ? 'success' : 'danger' }}">{{ ucfirst($user->status) }}</span>
                                    </td>
                                    <td>
                                        @if($user->isPrimaryAdmin())
                                            <div class="users-actions">
                                                <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#view-user-modal-{{ $user->id }}">Ver</button>
                                                @if($authenticatedUser?->isPrimaryAdmin())
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#edit-user-modal-{{ $user->id }}">Editar</button>
                                                @else
                                                    <span class="text-muted">Protegido</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="users-actions">
                                                <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#view-user-modal-{{ $user->id }}">Ver</button>
                                                @if(! $isAuthenticatedClinicManagerOwnAccount)
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#edit-user-modal-{{ $user->id }}">Editar</button>
                                                @endif
                                                @if(! $isAuthenticatedClinicManagerOwnAccount)
                                                    <form action="{{ route('admin.settings.users.status', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-{{ $user->status === 'ativo' ? 'warning' : 'success' }}">{{ $user->status === 'ativo' ? 'Inativar' : 'Ativar' }}</button>
                                                    </form>
                                                @endif
                                                @if(! $isAuthenticatedClinicManagerOwnAccount)
                                                    <form action="{{ route('admin.settings.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este usuário?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">Nenhum usuário encontrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->fragment('usuarios-permissoes')->links('vendor.pagination.patients-blocks') }}
                    </div>
                @endif

            </div>
        </div>

        @foreach($users as $user)
            <div class="modal fade user-edit-modal" id="view-user-modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewUserModalLabel{{ $user->id }}">Detalhes do usuário</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">Nome completo</div>
                                        <div class="font-weight-bold mt-1">{{ trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')) ?: 'Não informado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">E-mail</div>
                                        <div class="font-weight-bold mt-1">{{ $user->email ?: 'Não informado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">CPF</div>
                                        <div class="mt-1">{{ $authenticatedUser?->isClinicManager() && $user->isPrimaryAdmin() ? 'Protegido' : $formatCpf($user->cpf) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">Telefone</div>
                                        <div class="mt-1">{{ $formatPhone($user->fone) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">Status</div>
                                        <div class="mt-1">{{ $statusLabels[$user->status] ?? ucfirst($user->status ?? 'Não informado') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">Papel</div>
                                        <div class="mt-1">{{ $roleLabels[$user->normalizedRole()] ?? ucfirst($user->normalizedRole() ?: ($user->nivel ?? 'Não informado')) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">Nível interno</div>
                                        <div class="mt-1">{{ $user->nivel ?: 'Não informado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">Código do usuário</div>
                                        <div class="mt-1">{{ $user->id }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">Cadastro</div>
                                        <div class="mt-1">{{ $user->created_at?->format('d/m/Y H:i') ?: 'Não informado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="border rounded p-3 h-100 bg-white">
                                        <div class="text-muted small text-uppercase">Permissões de módulo</div>
                                        <div class="mt-2">
                                            @if(!empty($user->submenuPermissions()))
                                                {{ collect($user->submenuPermissions())->map(fn ($permission) => $permissionLabels[$permission] ?? $permission)->implode(', ') }}
                                            @elseif($user->isPrimaryAdmin())
                                                Acesso total do administrador principal.
                                            @else
                                                Nenhum módulo liberado.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="text-muted small text-uppercase">Senha</div>
                                        <div class="mt-2 font-weight-bold">Protegida por segurança</div>
                                        <div class="small text-muted mt-1">A senha original não pode ser exibida porque o sistema a armazena de forma criptografada. Se necessário, use a opção de editar para definir uma nova senha.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            @if(! $user->isPrimaryAdmin() || $authenticatedUser?->isPrimaryAdmin())
                <div class="modal fade user-edit-modal" id="edit-user-modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Editar usuário</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('admin.settings.users.update', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6"><div class="form-group"><label>Nome *</label><input type="text" class="form-control" name="nome" value="{{ old('nome', $user->nome) }}" required></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>Sobrenome</label><input type="text" class="form-control" name="sobrenome" value="{{ old('sobrenome', $user->sobrenome) }}"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>CPF</label><input type="text" class="form-control user-edit-cpf-mask" name="cpf" value="{{ old('cpf', $formatCpf($user->cpf)) }}" placeholder="000.000.000-00" maxlength="14" inputmode="numeric"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>Telefone</label><input type="text" class="form-control user-edit-phone-mask" name="fone" value="{{ old('fone', $user->fone) }}" placeholder="(11) 99999-9999" maxlength="15" inputmode="numeric"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>E-mail *</label><input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required></div></div>
                                        <div class="col-md-4"><div class="form-group"><label>Status *</label><select class="form-control" name="status" required {{ $user->isPrimaryAdmin() ? 'disabled' : '' }}><option value="ativo" {{ old('status', $user->status) === 'ativo' ? 'selected' : '' }}>Ativo</option><option value="cancelado" {{ old('status', $user->status) === 'cancelado' ? 'selected' : '' }}>Inativar</option></select>@if($user->isPrimaryAdmin())<input type="hidden" name="status" value="{{ old('status', $user->status) }}">@endif</div></div>
                                        <div class="col-md-4"><div class="form-group"><label>Papel *</label><select class="form-control" name="role" required {{ $user->isPrimaryAdmin() ? 'disabled' : '' }}>@if($user->isPrimaryAdmin())<option value="admin" selected>Administrador</option>@else @foreach($roles as $role)<option value="{{ $role }}" {{ ($role === 'profissional' ? in_array(old('role', $user->role ?? $user->nivel), ['profissional', 'medico'], true) : old('role', $user->role ?? $user->nivel) === $role) ? 'selected' : '' }}>{{ $roleLabels[$role] ?? ucfirst($role) }}</option>@endforeach @endif</select>@if($user->isPrimaryAdmin())<input type="hidden" name="role" value="admin">@endif</div></div>
                                        <div class="col-md-4"><div class="form-group"><label>Nova senha</label><input type="password" class="form-control" name="password" placeholder="Preencha apenas se quiser alterar"></div></div>
                                        <div class="col-md-4"><div class="form-group"><label>Confirmar nova senha</label><input type="password" class="form-control" name="password_confirmation"></div></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

    </div>
</section>

@push('scripts')
<script>
    $(function () {
        function formatPhone(value) {
            var digits = String(value || '').replace(/\D/g, '').slice(0, 11);

            if (!digits.length) {
                return '';
            }

            if (digits.length <= 2) {
                return '(' + digits;
            }

            if (digits.length <= 7) {
                return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
            }

            if (digits.length <= 10) {
                return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 6) + '-' + digits.slice(6);
            }

            return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7);
        }

        function formatCpf(value) {
            var digits = String(value || '').replace(/\D/g, '').slice(0, 11);

            if (!digits.length) {
                return '';
            }

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
        }

        function bindPhoneFormatter(selector) {
            $(selector).each(function () {
                var input = $(this);

                input.val(formatPhone(input.val()));

                input.on('input', function () {
                    var cursorAtEnd = this.selectionStart === this.value.length;
                    this.value = formatPhone(this.value);

                    if (cursorAtEnd) {
                        this.setSelectionRange(this.value.length, this.value.length);
                    }
                });
            });
        }

        function bindCpfFormatter(selector) {
            $(selector).each(function () {
                var input = $(this);

                input.val(formatCpf(input.val()));

                input.on('input', function () {
                    var cursorAtEnd = this.selectionStart === this.value.length;
                    this.value = formatCpf(this.value);

                    if (cursorAtEnd) {
                        this.setSelectionRange(this.value.length, this.value.length);
                    }
                });
            });
        }

        $('.user-edit-modal').each(function () {
            var modal = $(this);

            if (!modal.parent().is('body')) {
                modal.appendTo('body');
            }
        });

        bindPhoneFormatter('#new-user-phone');
        bindPhoneFormatter('.user-edit-phone-mask');
        bindCpfFormatter('#new-user-cpf');
        bindCpfFormatter('.user-edit-cpf-mask');
        bindCpfFormatter('#cpf-search');

        function syncPasswordToggle(button) {
            var input = $(button.data('target'));
            var icon = button.find('i');
            var isVisible = input.attr('type') === 'text';

            icon.toggleClass('fa-eye', isVisible);
            icon.toggleClass('fa-eye-slash', !isVisible);
            button.attr('aria-label', isVisible ? 'Ocultar senha' : 'Mostrar senha');
        }

        $('.password-toggle-btn').each(function () {
            syncPasswordToggle($(this));
        });

        $('.password-toggle-btn').on('click', function () {
            var button = $(this);
            var input = $(button.data('target'));
            var showPassword = input.attr('type') === 'password';

            input.attr('type', showPassword ? 'text' : 'password');
            syncPasswordToggle(button);
        });
    });
</script>
@endpush
@endsection
