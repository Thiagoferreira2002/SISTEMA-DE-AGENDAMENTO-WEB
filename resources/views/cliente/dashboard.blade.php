<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard - painelCms</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-content h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .header-content p {
            color: #6b7280;
            font-size: 14px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }

        .logout-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px;
            font-size: 20px;
            font-weight: 700;
        }

        .card-body {
            padding: 24px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
        }

        tbody tr {
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-admin {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-user {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-ativo {
            background: #dcfce7;
            color: #166534;
        }

        .badge-inativo {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-dot.active {
            background: #10b981;
        }

        .status-dot.inactive {
            background: #ef4444;
        }

        .empty-state {
            text-align: center;
            padding: 60px 24px;
            color: #6b7280;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }

        .action-btn-view {
            background: #dbeafe;
            color: #1e40af;
        }

        .action-btn-view:hover {
            background: #bfdbfe;
        }

        .action-btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-btn-delete:hover {
            background: #fecaca;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 14px;
        }

        .admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #dc2626 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .user-info {
                margin-top: 16px;
                justify-content: center;
                width: 100%;
            }

            .card-body {
                padding: 16px;
            }

            th, td {
                padding: 12px 8px;
                font-size: 12px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .admin-badge {
                margin-top: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div>
                        <h1>Bem-vindo, {{ Auth::user()->nome }}! 👋</h1>
                        <p>Painel de controle - Gerencie seus dados e informações</p>
                    </div>
                    @if(Auth::user()->nivel === 'admin')
                        <div class="admin-badge">👑 Administrador</div>
                    @endif
                </div>
            </div>
            <div class="user-info">
                <div class="avatar">{{ substr(Auth::user()->nome, 0, 1) }}</div>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">Sair</button>
                </form>
            </div>
        </div>

        <!-- Stats -->
        @if(Auth::user()->nivel === 'admin')
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">{{ $usuarios->count() }}</div>
                <div class="stat-label">Total de Usuários</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $usuarios->where('nivel', 'admin')->count() }}</div>
                <div class="stat-label">Administradores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $usuarios->where('nivel', 'user')->count() }}</div>
                <div class="stat-label">Usuários Comuns</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $usuarios->where('status', 'ativo')->count() }}</div>
                <div class="stat-label">Usuários Ativos</div>
            </div>
        </div>
        @else
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">{{ Auth::user()->created_at->diffInDays(now()) }}</div>
                <div class="stat-label">Dias Cadastrado</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ Auth::user()->status === 'ativo' ? 'Ativo' : 'Inativo' }}</div>
                <div class="stat-label">Status da Conta</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ Auth::user()->nivel === 'admin' ? 'Admin' : 'Usuário' }}</div>
                <div class="stat-label">Nível de Acesso</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ Auth::user()->created_at->format('d/m/Y') }}</div>
                <div class="stat-label">Data de Cadastro</div>
            </div>
        </div>
        @endif

        <!-- Usuários Table / Perfil -->
        @if(Auth::user()->nivel === 'admin')
        <div class="card">
            <div class="card-header">
                📋 Todos os Usuários Cadastrados
            </div>
            <div class="card-body">
                @if ($usuarios->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome Completo</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Nível</th>
                                <th>Status</th>
                                <th>Data Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $usuario)
                                <tr>
                                    <td><strong>#{{ $usuario->id }}</strong></td>
                                    <td>{{ $usuario->nome }} {{ $usuario->sobrenome }}</td>
                                    <td>
                                        <a href="mailto:{{ $usuario->email }}" style="color: #667eea; text-decoration: none;">
                                            {{ $usuario->email }}
                                        </a>
                                    </td>
                                    <td>{{ $usuario->fone }}</td>
                                    <td>
                                        <span class="badge {{ $usuario->nivel === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                            {{ $usuario->nivel === 'admin' ? '👑 Admin' : '👤 User' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-dot {{ $usuario->status === 'ativo' ? 'active' : 'inactive' }}"></span>
                                        <span class="badge {{ $usuario->status === 'ativo' ? 'badge-ativo' : 'badge-inativo' }}">
                                            {{ $usuario->status }}
                                        </span>
                                    </td>
                                    <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="actions">
                                            <button class="action-btn action-btn-view" onclick="alert('Detalhes de: {{ $usuario->nome }}')">
                                                Ver
                                            </button>
                                            @if (Auth::user()->nivel === 'admin')
                                                <button class="action-btn action-btn-delete" onclick="confirm('Tem certeza que deseja deletar {{ $usuario->nome }}?')">
                                                    Del
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <p>Nenhum usuário cadastrado</p>
                    </div>
                @endif
            </div>
        </div>
        @else
        <!-- Perfil do Usuário -->
        <div class="card">
            <div class="card-header">
                👤 Meu Perfil
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="stat-card" style="border-left-color: #10b981;">
                        <div class="stat-number" style="font-size: 24px;">{{ Auth::user()->nome }} {{ Auth::user()->sobrenome }}</div>
                        <div class="stat-label">Nome Completo</div>
                    </div>
                    <div class="stat-card" style="border-left-color: #3b82f6;">
                        <div class="stat-number" style="font-size: 18px;">{{ Auth::user()->email }}</div>
                        <div class="stat-label">Email</div>
                    </div>
                    <div class="stat-card" style="border-left-color: #8b5cf6;">
                        <div class="stat-number" style="font-size: 18px;">{{ Auth::user()->fone }}</div>
                        <div class="stat-label">Telefone</div>
                    </div>
                    <div class="stat-card" style="border-left-color: #f59e0b;">
                        <div class="stat-number" style="font-size: 18px;">
                            <span class="badge {{ Auth::user()->nivel === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                {{ Auth::user()->nivel === 'admin' ? '👑 Admin' : '👤 User' }}
                            </span>
                        </div>
                        <div class="stat-label">Nível de Acesso</div>
                    </div>
                    <div class="stat-card" style="border-left-color: #ef4444;">
                        <div class="stat-number" style="font-size: 18px;">
                            <span class="status-dot {{ Auth::user()->status === 'ativo' ? 'active' : 'inactive' }}"></span>
                            <span class="badge {{ Auth::user()->status === 'ativo' ? 'badge-ativo' : 'badge-inativo' }}">
                                {{ Auth::user()->status }}
                            </span>
                        </div>
                        <div class="stat-label">Status da Conta</div>
                    </div>
                    <div class="stat-card" style="border-left-color: #6b7280;">
                        <div class="stat-number" style="font-size: 18px;">{{ Auth::user()->created_at->format('d/m/Y H:i') }}</div>
                        <div class="stat-label">Data de Cadastro</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</body>
</html>
