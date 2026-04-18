<style>
        .main-sidebar, .sidebar-style-2 li a{
            background-color:#4f066b;
            color:#fff!important;
        }

        .nav-link{
            background-color:#4f066b!important;
            color:#fff!important;
        }

        .main-sidebar li a:hover{
            color:#fff!important;
            background-color:#7c03ac!important;
        }

        .sidebar-placeholder{
          opacity:.75;
          cursor:not-allowed;
        }

        .sidebar-placeholder .badge{
          float:right;
          margin-top:2px;
          background-color:#f0ad4e;
          color:#fff;
          font-size:10px;
        }

    </style>
    @php
        $loggedUser = auth()->user();
        $dashboardRoute = $loggedUser?->nivel === 'admin' ? route('admin.dashboard') : route('cliente.dashboard');
    @endphp

    <div class="main-sidebar sidebar-style-2" >
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="{{ $dashboardRoute }}" style="color:#fff; font-weight:900; font-size:24px;">MSFLIX CMS</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ $dashboardRoute }}">MS</a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Painel</li>
            <li class="dropdown ">
              <a href="{{ $dashboardRoute }}" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Painel</span></a>
              <ul class="dropdown-menu">
                <li ><a class="nav-link painelms" href="{{ $dashboardRoute }}">Página Inicial</a></li>
              </ul>
            </li>

            @if($loggedUser?->canAccessSubmenu('agendamentos'))
              <li class="menu-header">Agendamentos</li>
              <li class="dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="far fa-calendar-check"></i> <span>Agendamentos</span></a>
                <ul class="dropdown-menu">
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.calendar') }}">Calendário</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.index') }}">Agenda Geral</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.create') }}">Novo Agendamento</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.confirmations') }}">Confirmações</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.patients.history') }}">Serviços Finalizados</a></li>
                </ul>
              </li>
            @endif

            @if($loggedUser?->canAccessSubmenu('pacientes'))
              <li class="menu-header">Pacientes</li>
              <li class="dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-user-injured"></i> <span>Pacientes</span></a>
                <ul class="dropdown-menu">
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.create', ['tab' => 'paciente']) }}">Cadastrar Novo Paciente</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.patients.index') }}">Listagem / Busca</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.patients.logs') }}">Logs de Pacientes</a></li>
                </ul>
              </li>
            @endif

            @if($loggedUser?->canAccessSubmenu('painel_doutor'))
              <li class="menu-header">Atendimento Médico</li>
              <li class="dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-user-md"></i> <span>Painel do Profissional</span></a>
                <ul class="dropdown-menu">
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.doctor.queue') }}">Fila de Espera</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.doctor.records') }}">Prontuário Eletrônico</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.doctor.prescriptions') }}">Prescrições / Receitas</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.doctor.reports') }}">Atestados e Laudos</a></li>
                </ul>
              </li>
            @endif

            @if($loggedUser?->nivel === 'admin')
              <li class="menu-header">Cadastros Base</li>
              <li class="dropdown">
                <a href="{{ route('admin.settings.index') }}" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-cogs"></i> <span>Cadastros Base</span></a>
                <ul class="dropdown-menu">
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.settings.clinic-hours') }}">Horário da Clínica</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.settings.professionals') }}">Profissionais de Saúde</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.settings.procedures') }}">Procedimentos (Serviços)</a></li>
                  @if($loggedUser->isPrimaryAdmin())
                    <li class=""><a class="nav-link painelms" href="{{ route('admin.settings.users') }}">Usuários e Permissões</a></li>
                    <li class=""><a class="nav-link painelms" href="{{ route('admin.settings.activity-logs') }}">Logs de Atividade</a></li>
                  @endif
                </ul>
              </li>
            @endif

            <br>
            <br>
            <br>
            <br>
        </ul>

        </aside>
      </div>
