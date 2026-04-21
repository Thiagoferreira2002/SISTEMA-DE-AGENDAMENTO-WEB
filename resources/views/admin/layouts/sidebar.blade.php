<style>
    .main-sidebar,
    .main-sidebar #sidebar-wrapper,
    .sidebar-style-2 li a {
      background: linear-gradient(180deg, #0f3d6b 0%, #176fbe 42%, #1E90FF 100%);
      color:#f7ffff!important;
    }

    .main-sidebar {
      box-shadow: 8px 0 24px rgba(15, 61, 107, 0.12);
    }

    .main-sidebar .sidebar-brand,
    .main-sidebar .sidebar-brand a,
    .main-sidebar .menu-header,
    .main-sidebar .nav-link,
    .main-sidebar .nav-link i,
    .main-sidebar .dropdown-menu li a,
    .main-sidebar .dropdown-menu li a i {
      color:#f7ffff!important;
    }

    .main-sidebar .nav-link {
      background-color: rgba(255, 255, 255, 0.04) !important;
      border-radius: 10px;
      border: 1px solid transparent;
      transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
      box-shadow: none;
    }

    .main-sidebar .dropdown-menu {
      background: rgba(10, 46, 82, 0.16);
      border-radius: 10px;
      padding: 4px 0;
      backdrop-filter: blur(2px);
    }

    .main-sidebar .dropdown-menu li a {
      background: rgba(255, 255, 255, 0.05) !important;
      border-radius: 8px;
      margin: 4px 0;
      padding-left: 18px;
      font-weight: 500;
    }

    .main-sidebar li a:hover,
    .main-sidebar .nav-link.has-dropdown[aria-expanded="true"],
    .main-sidebar .dropdown.active > .nav-link,
    .main-sidebar .dropdown-menu li.active a {
      color:#ffffff!important;
      background: rgba(255, 255, 255, 0.12) !important;
      border-color: rgba(255, 255, 255, 0.18);
      box-shadow: 0 6px 14px rgba(12, 47, 82, 0.14);
    }

    .main-sidebar .dropdown-menu li a:hover {
      background: rgba(255, 255, 255, 0.14) !important;
      box-shadow: none;
    }

    .main-sidebar .menu-header {
      color: rgba(247, 255, 255, 0.72) !important;
      letter-spacing: .08em;
      font-weight: 700;
      margin-top: 10px;
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
      $dashboardRoute = ($loggedUser?->nivel === 'admin' || $loggedUser?->canAccessRouteName('admin.dashboard'))
            ? route('admin.dashboard')
            : route('cliente.dashboard');
        $isProfessionalAccount = $loggedUser?->normalizedRole() === 'profissional';
    @endphp

    <div class="main-sidebar sidebar-style-2" >
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="{{ $dashboardRoute }}" style="color:#fff; font-weight:900; font-size:24px;">CMS</a>
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
                <li ><a class="nav-link painelms" href="{{ route('admin.account.edit') }}">Minha Conta</a></li>
              </ul>
            </li>

            @if($loggedUser?->canAccessSubmenu('agendamentos'))
              <li class="menu-header">Agendamentos</li>
              <li class="dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="far fa-calendar-check"></i> <span>Agendamentos</span></a>
                <ul class="dropdown-menu">
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.calendar') }}">Calendário</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.index') }}">Agenda Geral</a></li>
                  @if(! $loggedUser?->isClinicManager())
                    <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.create') }}">Novo Agendamento</a></li>
                  @endif
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
                  @if(! $loggedUser?->isClinicManager())
                    <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.create', ['tab' => 'paciente']) }}">Cadastrar Novo Paciente</a></li>
                  @endif
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
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.agendamentos.calendar') }}">{{ $isProfessionalAccount ? 'Seu Calendário' : 'Calendário' }}</a></li>
                </ul>
              </li>
            @endif

            @if($loggedUser?->canAccessSubmenu('cadastros_base'))
              <li class="menu-header">Cadastros Base</li>
              <li class="dropdown">
                <a href="{{ route('admin.settings.index') }}" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-cogs"></i> <span>Cadastros Base</span></a>
                <ul class="dropdown-menu">
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.settings.clinic-hours') }}">Horário da Clínica</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.settings.professionals') }}">Profissionais de Saúde</a></li>
                  <li class=""><a class="nav-link painelms" href="{{ route('admin.settings.procedures') }}">Procedimentos (Serviços)</a></li>
                  @if($loggedUser->canManageCadastrosBase())
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
