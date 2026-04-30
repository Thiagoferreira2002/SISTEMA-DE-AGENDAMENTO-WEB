@extends('admin.layouts.master')

@section('content')
@php
    $loggedUser = auth()->user();
    $tutorialSections = [
        [
            'id' => 'painel',
            'title' => 'Painel',
            'icon' => 'fas fa-th-large',
            'summary' => 'Mostra indicadores gerais, atalhos rápidos e o ponto de entrada do sistema.',
            'modules' => [
                ['name' => 'Página Inicial', 'description' => 'Resume a operação do dia, com visão rápida do que precisa de atenção imediata.', 'route' => 'admin.dashboard'],
                ['name' => 'Tutorial do Sistema', 'description' => 'Explica a função de cada área e ajuda novos usuários a se orientarem no painel.', 'route' => 'admin.tutorial'],
                ['name' => 'Minha Conta', 'description' => 'Permite revisar e atualizar os dados do próprio usuário sem alterar cadastros globais.', 'route' => 'admin.account.edit'],
            ],
        ],
        [
            'id' => 'pacientes',
            'title' => 'Pacientes',
            'icon' => 'fas fa-user-injured',
            'summary' => 'Centraliza cadastro, busca e histórico operacional dos pacientes.',
            'modules' => [
                ['name' => 'Cadastrar Novo Paciente', 'description' => 'Abre o formulário para inserir um novo paciente quando ele ainda não existe no sistema.', 'route' => 'admin.agendamentos.create', 'params' => ['tab' => 'paciente']],
                ['name' => 'Listagem / Busca', 'description' => 'Localiza pacientes por nome, CPF e outros filtros para editar, visualizar ou confirmar cadastro existente.', 'route' => 'admin.patients.index'],
                ['name' => 'Logs de Pacientes', 'description' => 'Mostra alterações relevantes feitas nos registros dos pacientes para auditoria.', 'route' => 'admin.patients.logs'],
            ],
        ],
        [
            'id' => 'agendamentos',
            'title' => 'Agendamentos',
            'icon' => 'far fa-calendar-alt',
            'summary' => 'Cuida da agenda da clínica, do cadastro do agendamento até o histórico finalizado.',
            'modules' => [
                ['name' => 'Calendário', 'description' => 'Exibe os compromissos em formato visual por período para acompanhamento rápido da agenda.', 'route' => 'admin.agendamentos.calendar'],
                ['name' => 'Agenda Geral', 'description' => 'Lista os agendamentos com filtros por data, status e profissional para gestão diária.', 'route' => 'admin.agendamentos.index'],
                ['name' => 'Novo Agendamento', 'description' => 'Cria um novo atendimento usando paciente já cadastrado, profissional, procedimento, data e horário.', 'route' => 'admin.agendamentos.create'],
                ['name' => 'Confirmações', 'description' => 'Organiza os agendamentos pendentes para confirmar, pendenciar ou cancelar conforme retorno do paciente.', 'route' => 'admin.agendamentos.confirmations'],
                ['name' => 'Agendamentos Finalizados', 'description' => 'Consulta os atendimentos concluídos e o histórico operacional já encerrado.', 'route' => 'admin.agendamentos.completed'],
            ],
        ],
        [
            'id' => 'atendimento-medico',
            'title' => 'Painel do Profissional',
            'icon' => 'fas fa-user-md',
            'summary' => 'Acompanha a execução do atendimento pelo profissional e os casos que exigem ação imediata.',
            'modules' => [
                ['name' => 'Seu Calendário', 'description' => 'Mostra ao profissional apenas a própria agenda para organização individual.', 'route' => 'admin.agendamentos.calendar'],
                ['name' => 'Fila de Espera', 'description' => 'Apresenta pacientes aguardando atendimento e ajuda a controlar a ordem de chamada.', 'route' => 'admin.doctor.queue'],
                ['name' => 'Atendimentos em Atraso', 'description' => 'Separa os casos cujo horário final já passou e ainda exigem definição operacional.', 'route' => 'admin.doctor.pending-finalization'],
                ['name' => 'Agendamentos Finalizados', 'description' => 'Permite ao profissional revisar o histórico de atendimentos já concluídos.', 'route' => 'admin.agendamentos.completed'],
            ],
        ],
        [
            'id' => 'cadastros-base',
            'title' => 'Cadastros Base',
            'icon' => 'fas fa-cogs',
            'summary' => 'Define a estrutura da clínica e as regras usadas pelos demais módulos.',
            'modules' => [
                ['name' => 'Horário da Clínica', 'description' => 'Configura a janela geral de atendimento e intervalos válidos da clínica.', 'route' => 'admin.settings.clinic-hours'],
                ['name' => 'Profissionais de Saúde', 'description' => 'Gerencia os profissionais disponíveis, vínculos e informações usadas na agenda.', 'route' => 'admin.settings.professionals'],
                ['name' => 'Procedimentos (Serviços)', 'description' => 'Mantém os serviços oferecidos, duração padrão e profissional vinculado.', 'route' => 'admin.settings.procedures'],
                ['name' => 'Usuários e Permissões', 'description' => 'Controla contas de acesso e quais submódulos cada perfil pode usar.', 'route' => 'admin.settings.users'],
                ['name' => 'Logs de Atividade', 'description' => 'Centraliza a trilha de auditoria das ações administrativas e operacionais.', 'route' => 'admin.settings.activity-logs'],
            ],
        ],
        [
            'id' => 'notificacoes',
            'title' => 'Notificações',
            'icon' => 'far fa-bell',
            'summary' => 'Reúne alertas e avisos importantes ligados ao andamento dos agendamentos.',
            'modules' => [
                ['name' => 'Avisos Operacionais', 'description' => 'Informa mudanças relevantes da rotina, como ajustes de agendamento e pendências que pedem atenção.'],
            ],
        ],
    ];
@endphp

<style>
    .tutorial-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(265px, 1fr));
        gap: 18px;
    }

    .tutorial-card {
        border: 1px solid #e5eef8;
        border-radius: 18px;
        background: linear-gradient(180deg, var(--surface-primary) 0%, var(--surface-secondary) 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        padding: 22px;
        height: 100%;
        display: flex;
        flex-direction: column;
        width: 100%;
        text-align: left;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        overflow: hidden;
    }

    .tutorial-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        flex-wrap: nowrap;
        overflow: hidden;
        min-height: 44px;
    }

    .tutorial-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0d3358 0%, #176fbe 100%);
        color: #ffffff;
        font-size: 18px;
        box-shadow: 0 10px 20px rgba(13, 51, 88, 0.18);
        flex: 0 0 auto;
    }

    .tutorial-card h4 {
        margin-bottom: 10px;
        color: var(--text-primary);
        white-space: normal;
        overflow: visible;
        text-overflow: initial;
        font-size: 16px;
        line-height: 1.2;
    }

    .tutorial-card p {
        color: var(--text-secondary);
        margin-bottom: 0;
        overflow-wrap: anywhere;
        min-height: 58px;
    }

    .tutorial-card:hover,
    .tutorial-card:focus {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.1);
        border-color: var(--border-strong);
        outline: none;
    }

    .tutorial-card-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: var(--surface-tertiary);
        color: var(--accent-primary);
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .tutorial-card-action {
        display: inline-flex;
        align-items: center;
        margin-top: auto;
        padding-top: 14px;
        color: var(--accent-primary);
        font-weight: 700;
        font-size: 13px;
        white-space: nowrap;
    }

    .tutorial-highlight {
        border-radius: 20px;
        padding: 24px;
        background: linear-gradient(135deg, #0d3358 0%, #176fbe 100%);
        color: #ffffff;
        box-shadow: 0 18px 36px rgba(13, 51, 88, 0.18);
    }

    .tutorial-highlight p:last-child {
        margin-bottom: 0;
    }

    .tutorial-steps {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .tutorial-step {
        border-radius: 16px;
        background: var(--surface-primary);
        border: 1px solid var(--border-soft);
        padding: 18px;
    }

    .tutorial-step-number {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #176fbe;
        color: #ffffff;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .tutorial-details-card {
        border: 1px solid var(--border-soft);
        border-radius: 18px;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .tutorial-details-card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .tutorial-submodule-list {
        display: grid;
        gap: 14px;
    }

    .tutorial-submodule-item {
        border-radius: 14px;
        border: 1px solid var(--border-soft);
        background: var(--surface-secondary);
        padding: 16px 18px;
        overflow: hidden;
    }

    .tutorial-submodule-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }

    .tutorial-submodule-item h6 {
        margin-bottom: 6px;
        color: var(--text-primary);
        font-size: 15px;
        overflow-wrap: anywhere;
    }

    .tutorial-submodule-item p {
        margin-bottom: 0;
        color: var(--text-secondary);
        overflow-wrap: anywhere;
    }

    .tutorial-submodule-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    @media (max-width: 767.98px) {
        .tutorial-card h4 {
            font-size: 15px;
        }

        .tutorial-card p {
            min-height: 0;
        }

        .tutorial-submodule-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<section class="section">
    <div class="section-header">
        <h1>Tutorial do Sistema</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Tutorial</div>
        </div>
    </div>

    <div class="section-body">
        <div class="tutorial-highlight mb-4">
            <h3 class="mb-2">O que cada área faz</h3>
            <p class="mb-2">Este guia resume a função de cada módulo do painel para facilitar o uso diário da clínica.</p>
            <p>Use esta página como referência rápida para saber onde cadastrar, consultar, editar ou acompanhar cada etapa do atendimento.</p>
        </div>

        <div class="tutorial-grid mb-4">
            @foreach($tutorialSections as $section)
                <button
                    type="button"
                    class="tutorial-card"
                    data-toggle="collapse"
                    data-target="#tutorial-{{ $section['id'] }}"
                    data-parent="#tutorialAccordion"
                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                    aria-controls="tutorial-{{ $section['id'] }}"
                >
                    <span class="tutorial-card-badge">Clique para abrir os submódulos</span>
                    <div class="tutorial-card-header">
                        <span class="tutorial-card-icon"><i class="{{ $section['icon'] }}"></i></span>
                        <h4 class="mb-0">{{ $section['title'] }}</h4>
                    </div>
                    <p>{{ $section['summary'] }}</p>
                    <span class="tutorial-card-action">Ver tutorial detalhado</span>
                </button>
            @endforeach
        </div>

        <div class="mb-4" id="tutorialAccordion">
            @foreach($tutorialSections as $section)
                <div class="collapse {{ $loop->first ? 'show' : '' }}" id="tutorial-{{ $section['id'] }}" data-parent="#tutorialAccordion">
                    <div class="card tutorial-details-card mb-3">
                        <div class="card-header">
                            <h4 class="mb-0">{{ $section['title'] }}: o que cada submódulo faz</h4>
                            <span class="badge badge-primary">{{ count($section['modules']) }} submódulo(s)</span>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4">{{ $section['summary'] }}</p>
                            <div class="tutorial-submodule-list">
                                @foreach($section['modules'] as $module)
                                    <div class="tutorial-submodule-item">
                                        @php
                                            $routeName = $module['route'] ?? null;
                                            $routeParams = $module['params'] ?? [];
                                            $canAccess = $routeName ? $loggedUser?->canAccessRouteName($routeName) : false;
                                        @endphp
                                        <div class="tutorial-submodule-head">
                                            <h6>{{ $module['name'] }}</h6>
                                            @if($routeName && $canAccess)
                                                <a class="btn btn-outline-primary btn-sm tutorial-submodule-link" href="{{ route($routeName, $routeParams) }}" data-open-section="tutorial-{{ $section['id'] }}" onclick="event.stopPropagation();">
                                                    Abrir módulo
                                                </a>
                                            @elseif($routeName)
                                                <span class="badge badge-light">Sem acesso neste perfil</span>
                                            @endif
                                        </div>
                                        <p>{{ $module['description'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4>Fluxo básico de uso</h4>
            </div>
            <div class="card-body">
                <div class="tutorial-steps">
                    <div class="tutorial-step">
                        <div class="tutorial-step-number">1</div>
                        <h5>Cadastre o paciente</h5>
                        <p>Antes de agendar, confirme se o paciente já existe pelo CPF. Se não existir, faça o cadastro na área de Pacientes.</p>
                    </div>
                    <div class="tutorial-step">
                        <div class="tutorial-step-number">2</div>
                        <h5>Escolha profissional e procedimento</h5>
                        <p>No agendamento, selecione o profissional responsável e o procedimento para carregar a duração e a agenda correta.</p>
                    </div>
                    <div class="tutorial-step">
                        <div class="tutorial-step-number">3</div>
                        <h5>Defina data e horário</h5>
                        <p>O sistema valida disponibilidade do profissional, horário da clínica e conflito com outros atendimentos.</p>
                    </div>
                    <div class="tutorial-step">
                        <div class="tutorial-step-number">4</div>
                        <h5>Acompanhe o atendimento</h5>
                        <p>Depois do agendamento, acompanhe confirmações, fila de espera, atrasos e finalizações conforme o perfil de acesso.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Quem usa cada área</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Área</th>
                                <th>Uso principal</th>
                                <th>Perfis comuns</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Painel</td>
                                <td>Visão geral e atalhos</td>
                                <td>Admin, Recepcionista, Gestor, Profissional</td>
                            </tr>
                            <tr>
                                <td>Pacientes</td>
                                <td>Cadastro e consulta de pacientes</td>
                                <td>Admin, Recepcionista, Gestor</td>
                            </tr>
                            <tr>
                                <td>Agendamentos</td>
                                <td>Criação, edição e acompanhamento da agenda</td>
                                <td>Admin, Recepcionista, Gestor</td>
                            </tr>
                            <tr>
                                <td>Painel do Profissional</td>
                                <td>Execução do atendimento e finalização</td>
                                <td>Profissional, Admin</td>
                            </tr>
                            <tr>
                                <td>Cadastros Base</td>
                                <td>Configuração da clínica</td>
                                <td>Admin, Gestor</td>
                            </tr>
                            <tr>
                                <td>Minha Conta</td>
                                <td>Atualização de dados do próprio usuário</td>
                                <td>Todos os perfis</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var storageKey = 'adminTutorialOpenSection';
        var accordion = document.getElementById('tutorialAccordion');

        if (! accordion) {
            return;
        }

        var openSectionId = window.sessionStorage.getItem(storageKey);

        if (openSectionId) {
            accordion.querySelectorAll('.collapse.show').forEach(function (panel) {
                if (panel.id !== openSectionId) {
                    panel.classList.remove('show');
                }
            });

            var targetPanel = document.getElementById(openSectionId);

            if (targetPanel) {
                targetPanel.classList.add('show');

                document.querySelectorAll('[data-target]').forEach(function (button) {
                    button.setAttribute('aria-expanded', button.getAttribute('data-target') === '#' + openSectionId ? 'true' : 'false');
                });
            }
        }

        accordion.querySelectorAll('.collapse').forEach(function (panel) {
            panel.addEventListener('shown.bs.collapse', function () {
                window.sessionStorage.setItem(storageKey, panel.id);
            });
        });

        document.querySelectorAll('.tutorial-submodule-link[data-open-section]').forEach(function (link) {
            link.addEventListener('click', function () {
                window.sessionStorage.setItem(storageKey, link.getAttribute('data-open-section'));
            });
        });
    });
</script>
@endsection
