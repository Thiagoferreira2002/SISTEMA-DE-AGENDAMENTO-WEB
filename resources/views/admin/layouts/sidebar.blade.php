<style>
    .main-sidebar,
    .main-sidebar #sidebar-wrapper,
    .sidebar-style-2 li a {
      color:#15344d!important;
    }

    .main-sidebar {
      background: linear-gradient(180deg, #f8fbff 0%, #eef5fb 48%, #e6f0f9 100%);
      box-shadow: 10px 0 28px rgba(15, 23, 42, 0.08);
      border-right: 1px solid rgba(23, 111, 190, 0.12);
      padding-top: 0;
      top: 70px;
      height: calc(100vh - 70px);
      bottom: 0;
      z-index: 1000;
      overflow: hidden;
    }

    .main-sidebar .sidebar-brand,
    .main-sidebar .sidebar-brand a,
    .main-sidebar .menu-header,
    .main-sidebar .nav-link,
    .main-sidebar .nav-link i,
    .main-sidebar .dropdown-menu li a,
    .main-sidebar .dropdown-menu li a i {
      color:#15344d!important;
    }

    .main-sidebar .sidebar-menu li a {
      display: flex;
      position: relative;
      background-color: rgba(255, 255, 255, 0.76) !important;
      border-radius: 14px;
      border: 1px solid rgba(23, 111, 190, 0.1);
      transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease, transform .18s ease;
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.035);
      min-height: 50px;
      height: auto;
      padding: 13px 46px 13px 18px;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .main-sidebar .sidebar-menu li.menu-header {
      text-align: center;
      padding-left: 0;
      padding-right: 0;
      margin: 14px 0 8px;
    }

    .main-sidebar .sidebar-menu li a span {
      flex: 1 1 auto;
      margin-top: 0;
      line-height: 1.25;
      white-space: normal;
      text-align: center;
      width: 100%;
      font-weight: 700;
      font-size: 14px;
    }

    .main-sidebar .sidebar-menu li a i {
      display: none !important;
    }

    .main-sidebar .dropdown-menu {
      background: rgba(232, 243, 254, 0.88) !important;
      background-color: rgba(232, 243, 254, 0.88) !important;
      border: 1px solid rgba(23, 111, 190, 0.1);
      border-radius: 18px;
      padding: 8px 8px 3px;
      margin: 0;
      backdrop-filter: none;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28), 0 10px 22px rgba(15, 23, 42, 0.045) !important;
    }

    .main-sidebar .dropdown-menu li {
      padding: 0 4px;
      display: flex;
      justify-content: center;
      width: 100%;
    }

    .main-sidebar .dropdown-menu li a {
      display: flex;
      flex: 1 1 auto;
      background: rgba(255, 255, 255, 0.58) !important;
      border: 1px solid rgba(23, 111, 190, 0.08);
      border-radius: 12px;
      margin: 0 0 5px;
      padding: 10px 14px;
      min-height: 42px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      line-height: 1.3;
      white-space: normal;
      word-break: break-word;
      text-align: center;
      width: 100%;
      max-width: 100%;
      font-size: 12px;
      box-shadow: 0 1px 0 rgba(255, 255, 255, 0.32);
    }

    .main-sidebar .dropdown-menu li a:focus,
    .main-sidebar .dropdown-menu li a:active,
    .main-sidebar .dropdown-menu li.active > a,
    .main-sidebar .dropdown-menu li.active > a:hover {
      color: #ffffff !important;
      background: linear-gradient(135deg, #176fbe 0%, #2084d7 100%) !important;
      border-color: rgba(23, 111, 190, 0.4);
      box-shadow: 0 8px 18px rgba(23, 111, 190, 0.18);
    }

    .main-sidebar .dropdown-menu {
      text-align: center;
    }

    .main-sidebar .dropdown-menu li a.submenu-nowrap {
      white-space: normal;
      word-break: break-word;
      font-size: 11.5px;
      letter-spacing: 0;
    }

    .main-sidebar .nav-link.has-dropdown > span.submenu-nowrap-label {
      white-space: nowrap;
    }

    .main-sidebar .nav-link.has-dropdown::after {
      right: 18px;
      font-size: 12px;
    }

    .main-sidebar li a:hover,
    .main-sidebar .nav-link.has-dropdown[aria-expanded="true"],
    .main-sidebar .dropdown.active > .nav-link,
    .main-sidebar .dropdown-menu li.active a {
      color:#0e4b80!important;
      background: rgba(214, 234, 252, 0.92) !important;
      border-color: rgba(23, 111, 190, 0.24);
      box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
      transform: translateY(-1px);
    }

    .main-sidebar .dropdown-menu li a:hover {
      background: rgba(214, 234, 252, 0.96) !important;
      box-shadow: 0 4px 14px rgba(23, 111, 190, 0.08);
    }

    .main-sidebar .menu-header {
      color: #5f7c95 !important;
      letter-spacing: .12em;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .main-sidebar .sidebar-brand,
    .main-sidebar .sidebar-brand-sm {
      display: none !important;
    }

    .main-sidebar .sidebar-logo-shell {
      display: none;
    }

    .main-sidebar #sidebar-wrapper {
      display: flex;
      flex-direction: column;
      padding-top: 0;
      height: 100%;
      overflow: hidden;
      border-radius: 0 20px 20px 0;
    }

    .main-sidebar .sidebar-menu-shell {
      flex: 1 1 auto;
      min-height: 0;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 0 2px 12px 0;
      scrollbar-gutter: stable;
    }

    .main-sidebar .sidebar-menu-shell::-webkit-scrollbar {
      width: 8px;
    }

    .main-sidebar .sidebar-menu-shell::-webkit-scrollbar-thumb {
      background: rgba(23, 111, 190, 0.22);
      border-radius: 999px;
    }

    .main-sidebar .sidebar-menu-shell::-webkit-scrollbar-track {
      background: transparent;
    }

    .main-sidebar .sidebar-menu {
      flex: 1 1 auto;
      padding: 6px 12px 28px;
      margin: 0;
    }

    .main-sidebar .sidebar-menu > li {
      margin-bottom: 10px;
    }

    .main-sidebar .sidebar-menu > li > a.nav-link {
      margin-bottom: 8px;
    }

    .main-sidebar .sidebar-menu > li > ul.dropdown-menu {
      padding-top: 10px;
    }

    body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu > li.active > a {
      padding-left: 18px;
      background: rgba(214, 234, 252, 0.92) !important;
      border-color: rgba(23, 111, 190, 0.24) !important;
    }

    body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu > li.active > a::before {
      background: #176fbe;
      border-radius: 999px;
      left: 7px;
    }

    body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li.active ul.dropdown-menu,
    body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li.active ul.dropdown-menu li a,
    body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li ul.dropdown-menu li a {
      padding-left: 16px;
      color: #15344d !important;
    }

    body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li ul.dropdown-menu li.active > a,
    body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li ul.dropdown-menu li.active > a:hover,
    body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li ul.dropdown-menu li a:hover {
      background: linear-gradient(135deg, #176fbe 0%, #2084d7 100%) !important;
      color: #ffffff !important;
    }

    html[data-theme="dark"] .main-sidebar,
    html[data-theme="dark"] .main-sidebar #sidebar-wrapper,
    html[data-theme="dark"] .sidebar-style-2 li a {
      color:#f7ffff!important;
    }

    html[data-theme="dark"] .main-sidebar {
      background: linear-gradient(180deg, #0a1623 0%, #102334 46%, #173249 100%);
      box-shadow: 10px 0 28px rgba(2, 8, 15, 0.32);
      border-right-color: rgba(143, 197, 255, 0.1);
    }

    html[data-theme="dark"] .main-sidebar .sidebar-brand,
    html[data-theme="dark"] .main-sidebar .sidebar-brand a,
    html[data-theme="dark"] .main-sidebar .menu-header,
    html[data-theme="dark"] .main-sidebar .nav-link,
    html[data-theme="dark"] .main-sidebar .nav-link i,
    html[data-theme="dark"] .main-sidebar .dropdown-menu li a,
    html[data-theme="dark"] .main-sidebar .dropdown-menu li a i {
      color:#f7ffff!important;
    }

    html[data-theme="dark"] .main-sidebar .sidebar-menu li a {
      background-color: rgba(255, 255, 255, 0.06) !important;
      border-color: rgba(255, 255, 255, 0.08);
      box-shadow: none;
    }

    html[data-theme="dark"] .main-sidebar .dropdown-menu {
      background: rgba(6, 34, 61, 0.3) !important;
      background-color: rgba(6, 34, 61, 0.3) !important;
      border-color: rgba(255, 255, 255, 0.08);
      box-shadow: inset 0 1px 0 rgba(184, 224, 255, 0.08), 0 12px 24px rgba(4, 12, 22, 0.22) !important;
    }

    html[data-theme="dark"] .main-sidebar .dropdown-menu li a {
      background: rgba(9, 44, 77, 0.42) !important;
      border-color: rgba(255, 255, 255, 0.08);
      box-shadow: inset 0 1px 0 rgba(184, 224, 255, 0.04);
    }

    html[data-theme="dark"] .main-sidebar .dropdown-menu li a:focus,
    html[data-theme="dark"] .main-sidebar .dropdown-menu li a:active,
    html[data-theme="dark"] .main-sidebar .dropdown-menu li.active > a,
    html[data-theme="dark"] .main-sidebar .dropdown-menu li.active > a:hover {
      color: #ffffff !important;
      background: rgba(32, 132, 215, 0.96) !important;
      border-color: rgba(117, 212, 255, 0.32);
      box-shadow: 0 8px 18px rgba(10, 42, 73, 0.18);
    }

    html[data-theme="dark"] .main-sidebar li a:hover,
    html[data-theme="dark"] .main-sidebar .nav-link.has-dropdown[aria-expanded="true"],
    html[data-theme="dark"] .main-sidebar .dropdown.active > .nav-link,
    html[data-theme="dark"] .main-sidebar .dropdown-menu li.active a {
      color:#ffffff!important;
      background: rgba(255, 255, 255, 0.14) !important;
      border-color: rgba(164, 225, 255, 0.34);
      box-shadow: 0 10px 22px rgba(6, 26, 46, 0.16);
    }

    html[data-theme="dark"] .main-sidebar .dropdown-menu li a:hover {
      background: rgba(255, 255, 255, 0.14) !important;
      box-shadow: 0 6px 16px rgba(6, 26, 46, 0.2);
    }

    html[data-theme="dark"] .main-sidebar .menu-header {
      color: rgba(228, 244, 255, 0.66) !important;
    }

    html[data-theme="dark"] .main-sidebar .sidebar-menu-shell::-webkit-scrollbar-thumb {
      background: rgba(199, 232, 255, 0.28);
    }

    html[data-theme="dark"] body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu > li.active > a {
      background: rgba(255, 255, 255, 0.14) !important;
      border-color: rgba(164, 225, 255, 0.34) !important;
    }

    html[data-theme="dark"] body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu > li.active > a::before {
      background: #8ed8ff;
    }

    html[data-theme="dark"] body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li.active ul.dropdown-menu,
    html[data-theme="dark"] body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li.active ul.dropdown-menu li a,
    html[data-theme="dark"] body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li ul.dropdown-menu li a {
      color: #f7ffff !important;
    }

    html[data-theme="dark"] body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li ul.dropdown-menu li.active > a,
    html[data-theme="dark"] body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li ul.dropdown-menu li.active > a:hover,
    html[data-theme="dark"] body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu li ul.dropdown-menu li a:hover {
      background: rgba(32, 132, 215, 0.96) !important;
      color: #ffffff !important;
    }

    body.sidebar-mini .main-sidebar {
      top: 70px;
      height: calc(100vh - 70px);
    }

    body.sidebar-mini .main-sidebar .sidebar-logo-shell {
      display: none;
    }

    @media (max-width: 1024px) {
      .main-sidebar {
        top: 60px;
        bottom: 0;
        height: calc(100vh - 60px);
        width: min(274px, calc(100vw - 28px));
        transform: translate3d(var(--mobile-sidebar-drag-x, 0), 0, 0);
        transition: transform .24s ease, box-shadow .24s ease, visibility .24s ease;
        will-change: transform;
        border-radius: 0 18px 18px 0;
      }

      .main-sidebar.is-dragging {
        transition: none;
      }

      body.sidebar-gone .main-sidebar {
        transform: translate3d(calc(-100% - 18px), 0, 0);
        box-shadow: none;
        visibility: hidden;
      }

      body.sidebar-show .main-sidebar {
        transform: translate3d(0, 0, 0);
        visibility: visible;
      }

      body.sidebar-mini .main-sidebar {
        height: calc(100vh - 60px);
      }

      .main-sidebar .sidebar-menu {
        padding: 12px 8px 20px;
      }

      .main-sidebar .sidebar-menu-shell {
        padding-top: 18px;
      }

      .main-sidebar .sidebar-menu li a {
        min-height: 42px;
        padding: 10px 36px 10px 14px;
        border-radius: 12px;
      }

      .main-sidebar .dropdown-menu li a {
        min-height: 38px;
        font-size: 11px;
        padding: 9px 12px;
      }
    }

    @media (max-width: 767.98px) {
      .main-sidebar .sidebar-menu-shell {
        padding-top: 20px;
      }

      .main-sidebar .sidebar-menu > li {
        margin-bottom: 10px;
      }

      .main-sidebar .sidebar-menu li.menu-header {
        margin: 6px 0 6px;
      }

      .main-sidebar .sidebar-menu li a span {
        font-size: 12px;
      }

      .main-sidebar .menu-header {
        margin: 10px 0 6px;
        font-size: 9px;
      }

      .main-sidebar .dropdown-menu {
        padding-top: 6px;
      }

      .main-sidebar .dropdown-menu li {
        padding: 0 6px;
      }
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
      $isPatientTabOnCreate = request()->routeIs('admin.agendamentos.create') && request()->query('tab') === 'paciente';
      $isProfessionalCalendarRoute = $isProfessionalAccount && request()->routeIs('admin.agendamentos.calendar');
      $isDoctorCompletedRoute = $isProfessionalAccount
        && request()->routeIs('admin.agendamentos.completed')
        && request()->query('source') === 'doctor';
      $isAdministrativeAbsenceRoute = ! $isProfessionalAccount && request()->routeIs('admin.doctor.absences*');
      $isDashboardMenuOpen = request()->routeIs('admin.dashboard') || request()->routeIs('admin.account.*') || request()->routeIs('admin.tutorial');
      $isPatientsMenuOpen = request()->routeIs('admin.patients.*') || $isPatientTabOnCreate;
      $isAgendamentosMenuOpen = $isAdministrativeAbsenceRoute || (request()->routeIs('admin.agendamentos.*') && ! $isPatientTabOnCreate && ! $isProfessionalCalendarRoute && ! $isDoctorCompletedRoute);
      $isDoctorMenuOpen = ($isProfessionalAccount && request()->routeIs('admin.doctor.*')) || $isProfessionalCalendarRoute || $isDoctorCompletedRoute;
      $isCadastrosMenuOpen = request()->routeIs('admin.settings.*');
    @endphp

    <div class="main-sidebar sidebar-style-2" >
        <aside id="sidebar-wrapper">
          <div class="sidebar-logo-shell">
            <a href="{{ $dashboardRoute }}" class="sidebar-logo-link" aria-label="CMS">
              <img src="{{ asset('backend/assets/img/cms-logo.svg') }}" alt="CMS Consulta" class="sidebar-logo-image">
            </a>
            <div class="sidebar-logo-divider"></div>
          </div>
          <div class="sidebar-menu-shell">
          <ul class="sidebar-menu">
            <li class="menu-header">Painel</li>
            <li class="dropdown {{ $isDashboardMenuOpen ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown" aria-expanded="{{ $isDashboardMenuOpen ? 'true' : 'false' }}"><span>Painel</span></a>
              <ul class="dropdown-menu" style="{{ $isDashboardMenuOpen ? 'display:block;' : '' }}">
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ $dashboardRoute }}">Página Inicial</a></li>
                <li class="{{ request()->routeIs('admin.tutorial') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.tutorial') }}">Tutorial do Sistema</a></li>
                <li class="{{ request()->routeIs('admin.account.*') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.account.edit') }}">Minha Conta</a></li>
              </ul>
            </li>

            @if($loggedUser?->canAccessSubmenu('pacientes'))
              <li class="menu-header">Pacientes</li>
              <li class="dropdown {{ $isPatientsMenuOpen ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown" aria-expanded="{{ $isPatientsMenuOpen ? 'true' : 'false' }}"><span>Pacientes</span></a>
                <ul class="dropdown-menu" style="{{ $isPatientsMenuOpen ? 'display:block;' : '' }}">
                  @if(! $loggedUser?->isClinicManager())
                    <li class="{{ $isPatientTabOnCreate ? 'active' : '' }}"><a class="nav-link painelms submenu-nowrap" href="{{ route('admin.agendamentos.create', ['tab' => 'paciente']) }}">Cadastrar Novo Paciente</a></li>
                  @endif
                  <li class="{{ request()->routeIs('admin.patients.index') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.patients.index') }}">Listagem / Busca</a></li>
                  @if($loggedUser?->canManageCadastrosBase())
                    <li class="{{ request()->routeIs('admin.patients.logs') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.patients.logs') }}">Logs de Pacientes</a></li>
                  @endif
                </ul>
              </li>
            @endif

            @if($loggedUser?->canAccessSubmenu('agendamentos'))
              <li class="menu-header">Agendamentos</li>
              <li class="dropdown {{ $isAgendamentosMenuOpen ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown" aria-expanded="{{ $isAgendamentosMenuOpen ? 'true' : 'false' }}"><span>Agendamentos</span></a>
                <ul class="dropdown-menu" style="{{ $isAgendamentosMenuOpen ? 'display:block;' : '' }}">
                  @if($loggedUser?->normalizedRole() !== 'profissional')
                    <li class="{{ request()->routeIs('admin.agendamentos.calendar') && ! $isProfessionalCalendarRoute ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.agendamentos.calendar') }}">Calendário</a></li>
                  @endif
                  <li class="{{ request()->routeIs('admin.agendamentos.index') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.agendamentos.index') }}">Agenda Geral</a></li>
                  @if(! $loggedUser?->isClinicManager())
                    <li class="{{ request()->routeIs('admin.agendamentos.create') && ! $isPatientTabOnCreate ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.agendamentos.create') }}">Novo Agendamento</a></li>
                  @endif
                  <li class="{{ request()->routeIs('admin.agendamentos.confirmations') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.agendamentos.confirmations') }}">Confirmações</a></li>
                  @if($loggedUser?->normalizedRole() === 'recepcionista')
                    <li class="{{ request()->routeIs('admin.agendamentos.delayed-appointments') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.agendamentos.delayed-appointments') }}">Atendimentos em Atraso</a></li>
                  @endif
                  @if(in_array($loggedUser?->normalizedRole(), ['admin', 'gestor_clinica', 'recepcionista'], true))
                  @endif
                  @if($loggedUser?->normalizedRole() !== 'profissional')
                    <li class="{{ request()->routeIs('admin.agendamentos.completed') && ! $isProfessionalCalendarRoute ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.agendamentos.completed') }}">Agendamentos Finalizados</a></li>
                  @endif
                </ul>
              </li>
            @endif

            @if($loggedUser?->canAccessSubmenu('painel_doutor'))
              <li class="menu-header">Atendimento Médico</li>
              <li class="dropdown {{ $isDoctorMenuOpen ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown" aria-expanded="{{ $isDoctorMenuOpen ? 'true' : 'false' }}"><span class="submenu-nowrap-label">Painel do Profissional</span></a>
                <ul class="dropdown-menu" style="{{ $isDoctorMenuOpen ? 'display:block;' : '' }}">
                  @if($loggedUser?->normalizedRole() === 'profissional')
                    <li class="{{ request()->routeIs('admin.agendamentos.calendar') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.agendamentos.calendar') }}">Seu Calendário</a></li>
                  @endif
                  <li class="{{ request()->routeIs('admin.doctor.queue') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.doctor.queue') }}">Fila de Espera</a></li>
                  <li class="{{ request()->routeIs('admin.doctor.pending-finalization') ? 'active' : '' }}"><a class="nav-link painelms submenu-nowrap" href="{{ route('admin.doctor.pending-finalization') }}">Atendimentos em Atraso</a></li>
                  @if($loggedUser?->normalizedRole() === 'profissional')
                    <li class="{{ request()->routeIs('admin.doctor.absences*') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.doctor.absences') }}">Aus&ecirc;ncias</a></li>
                    <li class="{{ $isDoctorCompletedRoute ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.agendamentos.completed', ['source' => 'doctor']) }}">Agendamentos Finalizados</a></li>
                  @endif
                </ul>
              </li>
            @endif

            @if($loggedUser?->canAccessSubmenu('cadastros_base'))
              <li class="menu-header">Cadastros Base</li>
              <li class="dropdown {{ $isCadastrosMenuOpen ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown" aria-expanded="{{ $isCadastrosMenuOpen ? 'true' : 'false' }}"><span>Cadastros Base</span></a>
                <ul class="dropdown-menu" style="{{ $isCadastrosMenuOpen ? 'display:block;' : '' }}">
                  @if($loggedUser->canManageCadastrosBase())
                    <li class="{{ request()->routeIs('admin.settings.users') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.settings.users') }}">Usuários e Permissões</a></li>
                  @endif
                  <li class="{{ request()->routeIs('admin.settings.professionals') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.settings.professionals') }}">Profissionais de Saúde</a></li>
                  <li class="{{ request()->routeIs('admin.settings.procedures') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.settings.procedures') }}">Procedimentos</a></li>
                  <li class="{{ request()->routeIs('admin.settings.clinic-hours') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.settings.clinic-hours') }}">Horário da Clínica</a></li>
                  @if($loggedUser->canManageCadastrosBase())
                    <li class="{{ request()->routeIs('admin.settings.activity-logs') ? 'active' : '' }}"><a class="nav-link painelms" href="{{ route('admin.settings.activity-logs') }}">Logs de Atividade</a></li>
                  @endif
                </ul>
              </li>
            @endif

        </ul>
          </div>

        </aside>
      </div>
