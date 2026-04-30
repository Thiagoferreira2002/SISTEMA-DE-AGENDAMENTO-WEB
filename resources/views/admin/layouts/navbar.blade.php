<style>
        .main-navbar-cms {
          background: linear-gradient(135deg, #0f4f86 0%, #1a76c4 52%, #46a6ff 100%) !important;
            color: #fff !important;
            position: inherit;
            box-shadow: 0 14px 34px rgba(13, 51, 88, 0.22);
            padding: 8px 22px;
          border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .main-navbar-cms .nav-link,
        .main-navbar-cms .dropdown-toggle,
        .main-navbar-cms .fas,
        .main-navbar-cms .far {
          color: #f5ffff !important;
        }

        .main-navbar-cms .navbar-left-actions {
          display: flex;
          align-items: center;
          gap: 12px;
          flex: 1 1 auto;
        }

        .main-navbar-cms .navbar-brand-cms {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          min-height: 42px;
          min-width: 164px;
          padding: 2px 4px;
          border-radius: 14px;
          background: transparent;
          border: none;
          box-shadow: none;
          text-decoration: none !important;
        }

        .main-navbar-cms .navbar-brand-logo {
          width: 100%;
          max-width: 142px;
          height: auto;
          display: block;
          filter: drop-shadow(0 1px 8px rgba(255, 255, 255, 0.2));
        }

        @media (max-width: 767.98px) {
          .main-navbar-cms .navbar-brand-cms {
            min-width: 138px;
            padding: 1px 3px;
          }

          .main-navbar-cms .navbar-brand-logo {
            max-width: 124px;
          }
        }

        .main-navbar-cms .navbar-sidebar-toggle {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 12px;
          min-height: 48px;
          padding: 8px 14px 8px 10px;
          border-radius: 18px;
          background: linear-gradient(180deg, rgba(6, 27, 47, 0.42) 0%, rgba(11, 53, 92, 0.32) 100%);
          border: 1px solid rgba(255, 255, 255, 0.16);
          color: #ffffff !important;
          font-weight: 700;
          text-decoration: none !important;
          box-shadow: 0 10px 24px rgba(7, 30, 52, 0.16);
          transition: background-color .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        .main-navbar-cms .navbar-sidebar-toggle:hover,
        .main-navbar-cms .navbar-sidebar-toggle:focus {
          background: linear-gradient(180deg, rgba(7, 34, 58, 0.62) 0%, rgba(14, 73, 123, 0.42) 100%);
          transform: translateY(-1px);
          box-shadow: 0 14px 28px rgba(7, 30, 52, 0.2);
        }

        .main-navbar-cms .navbar-sidebar-toggle-icon {
          width: 34px;
          height: 34px;
          border-radius: 12px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          background: rgba(255, 255, 255, 0.14);
          border: 1px solid rgba(255, 255, 255, 0.12);
          flex: 0 0 34px;
        }

        .main-navbar-cms .navbar-sidebar-toggle-text {
          display: flex;
          flex-direction: column;
          align-items: flex-start;
          line-height: 1.1;
        }

        .main-navbar-cms .navbar-sidebar-toggle-title {
          font-size: 13px;
          font-weight: 700;
          letter-spacing: .02em;
          color: #ffffff;
        }

        .main-navbar-cms .navbar-sidebar-toggle-subtitle {
          font-size: 10px;
          text-transform: uppercase;
          letter-spacing: .08em;
          color: rgba(255, 255, 255, 0.7);
        }

        .main-navbar-cms .navbar-pill {
          display: inline-flex;
          align-items: center;
          gap: 10px;
          min-height: 48px;
          padding: 8px 14px;
          border-radius: 16px;
          background: rgba(255, 255, 255, 0.12);
          border: 1px solid rgba(255, 255, 255, 0.14);
          transition: background-color .2s ease, transform .2s ease;
        }

        .main-navbar-cms .navbar-pill:hover,
        .main-navbar-cms .navbar-pill:focus {
          background: rgba(255, 255, 255, 0.18);
          transform: translateY(-1px);
        }

        .main-navbar-cms .navbar-right {
          display: flex;
          align-items: center;
          gap: 16px;
        }

        .main-navbar-cms .navbar-action-item {
          width: 228px;
          display: flex;
          align-items: stretch;
        }

        .main-navbar-cms .navbar-action-item-compact {
          width: 176px;
        }

        .main-navbar-cms .navbar-action-link {
          width: 100%;
          min-width: 228px;
          height: 48px;
          justify-content: center;
        }

        .main-navbar-cms .navbar-action-link-compact {
          min-width: 176px;
        }

        .main-navbar-cms .theme-toggle-link {
          justify-content: center;
          gap: 12px;
          position: relative;
          overflow: hidden;
        }

        .main-navbar-cms .theme-toggle-icon {
          width: 54px;
          height: 32px;
          border-radius: 999px;
          display: inline-flex;
          align-items: center;
          justify-content: flex-start;
          padding: 0 4px;
          background: rgba(255, 255, 255, 0.14);
          border: 1px solid rgba(255, 255, 255, 0.14);
          flex: 0 0 54px;
          transition: background-color .25s ease, border-color .25s ease;
        }

        .main-navbar-cms .theme-toggle-thumb {
          width: 24px;
          height: 24px;
          border-radius: 999px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          background: #ffffff;
          color: #0f4f86;
          box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
          transform: translateX(0);
          transition: transform .28s ease, background-color .28s ease, color .28s ease, box-shadow .28s ease;
        }

        .main-navbar-cms .theme-toggle-link.is-dark .theme-toggle-thumb {
          transform: translateX(22px);
          background: #8fc5ff;
          color: #0a111a;
          box-shadow: 0 8px 18px rgba(143, 197, 255, 0.32);
        }

        .main-navbar-cms .theme-toggle-link.is-dark .theme-toggle-icon {
          background: rgba(118, 187, 255, 0.18);
          border-color: rgba(143, 197, 255, 0.24);
        }

        .main-navbar-cms .theme-toggle-copy {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          line-height: 1.1;
          text-align: center;
        }

        .main-navbar-cms .theme-toggle-label {
          font-size: 13px;
          font-weight: 700;
          color: #ffffff;
        }

        .main-navbar-cms .theme-toggle-subtitle {
          display: none;
        }

        html[data-theme="dark"] .main-navbar-cms {
          background: linear-gradient(135deg, #0a111a 0%, #12202f 52%, #1b2f45 100%) !important;
          border-bottom-color: rgba(143, 197, 255, 0.12);
        }

        html[data-theme="dark"] .main-navbar-cms .navbar-pill {
          background: rgba(255, 255, 255, 0.07);
          border-color: rgba(255, 255, 255, 0.1);
        }

        html[data-theme="light"] .main-navbar-cms .navbar-pill {
          background: rgba(255, 255, 255, 0.16);
          border-color: rgba(255, 255, 255, 0.18);
        }

        html[data-theme="light"] .main-navbar-cms .theme-toggle-icon,
        html[data-theme="light"] .main-navbar-cms .navbar-sidebar-toggle-icon {
          background: rgba(255, 255, 255, 0.18);
          border-color: rgba(255, 255, 255, 0.16);
        }

        html[data-theme="dark"] .main-navbar-cms .dropdown-list .dropdown-header,
        html[data-theme="dark"] .main-navbar-cms .dropdown-footer,
        html[data-theme="dark"] .main-navbar-cms .profile-dropdown-header,
        html[data-theme="dark"] .main-navbar-cms .logout-form {
          background: #16283b;
          color: #dbe7f4;
          border-color: rgba(138, 174, 209, 0.18);
        }

        html[data-theme="dark"] .main-navbar-cms .dropdown-list .dropdown-header a,
        html[data-theme="dark"] .main-navbar-cms .dropdown-item-desc .time {
          color: #8fc5ff !important;
        }

        @media (max-width: 991.98px) {
          .main-navbar-cms .navbar-action-item,
          .main-navbar-cms .navbar-action-item-compact,
          .main-navbar-cms .navbar-action-link,
          .main-navbar-cms .navbar-action-link-compact {
            width: auto;
            min-width: auto;
          }

          .main-navbar-cms .theme-toggle-copy,
          .main-navbar-cms .notification-toggle .d-lg-inline {
            display: none;
          }
        }

        .main-navbar-cms .notification-badge {
            min-width: 22px;
            height: 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #072640;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            margin-left: 2px;
          }

          .main-navbar-cms .dropdown-list {
            width: 370px;
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 22px 46px rgba(17, 39, 65, 0.22);
            opacity: 0;
            transform: translateY(10px) scale(.98);
            transform-origin: top right;
            transition: opacity .22s ease, transform .22s ease;
            display: block;
            pointer-events: none;
          }

          .main-navbar-cms .dropdown-list.show,
          .main-navbar-cms .profile-dropdown.show {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
          }

          .main-navbar-cms .dropdown-list .dropdown-header {
            padding: 16px 18px;
            font-size: 14px;
            font-weight: 700;
            color: #12344d;
            background: linear-gradient(180deg, #f7fbff 0%, #eef6ff 100%);
            border-bottom: 1px solid #e2edf8;
          }

          .main-navbar-cms .dropdown-list .dropdown-header a {
            color: #176fbe;
            font-weight: 700;
          }

          .main-navbar-cms .dropdown-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
          }

          .main-navbar-cms .dropdown-list-content {
            max-height: 380px;
            background: #ffffff;
          }

          .main-navbar-cms .dropdown-item {
            padding: 14px 16px;
            border-bottom: 1px solid #eef2f6;
          }

          .main-navbar-cms .dropdown-item:last-child {
            border-bottom: 0;
          }

          .main-navbar-cms .dropdown-item-unread {
            background: linear-gradient(90deg, rgba(30, 144, 255, 0.08), rgba(30, 144, 255, 0.02));
          }

          .main-navbar-cms .dropdown-item-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
        }

        .main-navbar-cms .dropdown-item-desc {
            white-space: normal;
            line-height: 1.45;
            margin-left: 14px;
          }

          .main-navbar-cms .dropdown-item-desc strong {
            color: #16344b;
          }

          .main-navbar-cms .dropdown-item-desc .time {
            font-weight: 700;
            color: #176fbe;
          }

          .main-navbar-cms .dropdown-footer {
            padding: 14px 16px;
            background: #f7fbff;
          }

          .main-navbar-cms .user-pill {
            min-width: 228px;
            justify-content: space-between;
          }

          .main-navbar-cms .user-pill-content {
            display: flex;
            align-items: center;
            gap: 12px;
          }

          .main-navbar-cms .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.28);
          }

          .main-navbar-cms .user-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
          }

          .main-navbar-cms .user-name {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
          }

          .main-navbar-cms .user-role {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, 0.74);
          }

          .main-navbar-cms .profile-dropdown {
            min-width: 270px;
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 22px 46px rgba(17, 39, 65, 0.22);
            opacity: 0;
            transform: translateY(10px) scale(.98);
            transform-origin: top right;
            transition: opacity .22s ease, transform .22s ease;
            display: block;
            pointer-events: none;
          }

          .main-navbar-cms .user-pill.dropdown-toggle::after {
            display: none !important;
          }

          .main-navbar-cms .profile-dropdown-header {
            padding: 18px;
            background: linear-gradient(180deg, #f8fbff 0%, #eef6ff 100%);
            border-bottom: 1px solid #e2edf8;
          }

          .main-navbar-cms .profile-dropdown-header .profile-name {
            display: block;
            font-size: 15px;
            font-weight: 700;
            color: #173b56;
          }

          .main-navbar-cms .profile-dropdown-header .profile-role {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            color: #58758b;
          }

          .main-navbar-cms .logout-form {
            padding: 16px;
            background: #ffffff;
          }

          .main-navbar-cms .logout-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px 14px;
            border: 0;
            border-radius: 12px;
            background: #f04f5f;
            color: #ffffff;
            font-weight: 700;
          }

          .main-navbar-cms .logout-button:hover {
            background: #db4352;
            color: #ffffff;
            text-decoration: none;
        }


    </style>

    <nav class="navbar navbar-expand-lg main-navbar main-navbar-cms" style="position:sticky;top:0;z-index:1050;">
        <div class="navbar-left-actions mr-auto">
          <button id="sidebarToggle" class="btn btn-light btn-sm mr-3 d-inline-flex d-lg-none align-items-center justify-content-center" style="border-radius:8px;min-width:40px;min-height:40px;box-shadow:0 2px 8px rgba(30,144,255,0.08);position:relative;top:0;left:0;z-index:1100;" onclick="document.body.classList.toggle('sidebar-mini')" title="Abrir/Fechar Menu">
            <i class="fas fa-bars"></i>
          </button>
          <a href="{{ route('admin.dashboard') }}" class="navbar-brand-cms" aria-label="CMS Consulta">
            <img src="{{ asset('backend/assets/img/cms-logo.svg') }}" alt="CMS Consulta" class="navbar-brand-logo">
          </a>
          <a href="#" data-toggle="sidebar" class="navbar-sidebar-toggle d-none d-lg-inline-flex" aria-label="Alternar lateral">
            <span class="navbar-sidebar-toggle-icon"><i class="fas fa-sliders-h"></i></span>
            <span class="navbar-sidebar-toggle-text">
              <span class="navbar-sidebar-toggle-title">Menu lateral</span>
              <span class="navbar-sidebar-toggle-subtitle">Expandir ou recolher</span>
            </span>
          </a>
          <a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a>
        </div>
        <ul class="navbar-nav navbar-right align-items-center" style="gap:18px;">
          @php
              $notificationOpenUrl = route('admin.notifications.read', ['redirect_to' => $navbarNotificationTargetUrl ?? route('admin.agendamentos.index')]);
              $notificationClearUrl = route('admin.notifications.read', ['redirect_to' => request()->fullUrl()]);
              $userDisplayName = trim((string) ((Auth::user()->nome ?? '') . ' ' . (Auth::user()->sobrenome ?? '')));
                $userRoleLabel = Auth::user()->roleLabel();
              $userAvatar = Auth::user()->profile_photo_url;
          @endphp
          <li class="dropdown dropdown-list-toggle navbar-action-item">
            <a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg navbar-pill navbar-action-link {{ ($navbarNotificationCount ?? 0) > 0 ? 'beep' : '' }}" style="display:flex;align-items:center;">
              <i class="far fa-bell"></i>
              <span class="d-none d-lg-inline">Notificações</span>
              @if(($navbarNotificationCount ?? 0) > 0)
                <span class="notification-badge">{{ $navbarNotificationCount }}</span>
              @endif
            </a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Notificações
                <div class="float-right dropdown-header-actions">
                  <a href="{{ $notificationClearUrl }}">Limpar alertas</a>
                  <a href="{{ $notificationOpenUrl }}">Ver agenda</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-icons">
                @forelse(($navbarNotifications ?? collect()) as $notification)
                  <a href="{{ route('admin.notifications.read', ['notification_id' => $notification->id, 'redirect_to' => $notification->navbar_target_url ?? ($navbarNotificationTargetUrl ?? route('admin.agendamentos.calendar'))]) }}" class="dropdown-item {{ !empty($notification->navbar_is_unread) ? 'dropdown-item-unread' : '' }}">
                    <div class="dropdown-item-icon bg-primary text-white">
                      <i class="far fa-calendar-check"></i>
                    </div>
                    <div class="dropdown-item-desc">
                      <strong>{{ $notification->nome ?: 'Agendamento' }}</strong>
                      <div>{{ $notification->servico ?: 'Serviço não informado' }}</div>
                      @if($notification->professional)
                        <div class="small text-muted">Profissional: {{ $notification->professional->nome }}</div>
                      @endif
                      <div class="time text-primary">{{ optional($notification->data_agendamento)->format('d/m/Y') }} às {{ $notification->horario }}</div>
                    </div>
                  </a>
                @empty
                  <div class="dropdown-item">
                    <div class="dropdown-item-icon bg-light text-dark">
                      <i class="far fa-bell-slash"></i>
                    </div>
                    <div class="dropdown-item-desc">
                      Nenhuma notificação de agendamento no momento.
                    </div>
                  </div>
                @endforelse
              </div>
              <div class="dropdown-footer text-center">
                <a href="{{ $notificationClearUrl }}" class="mr-3">Limpar alertas</a>
                <a href="{{ $notificationOpenUrl }}">Veja tudo <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li>
          <li class="navbar-action-item navbar-action-item-compact">
            <a href="#" class="nav-link nav-link-lg navbar-pill navbar-action-link navbar-action-link-compact theme-toggle-link" data-theme-toggle aria-pressed="false" title="Ativar modo escuro">
              <span class="theme-toggle-icon"><span class="theme-toggle-thumb" data-theme-thumb><i class="fas fa-moon" data-theme-icon></i></span></span>
              <span class="theme-toggle-copy">
                <span class="theme-toggle-label" data-theme-label>Modo Escuro</span>
                <span class="theme-toggle-subtitle" data-theme-subtitle>visual padrão ativo</span>
              </span>
            </a>
          </li>
          <li class="dropdown navbar-action-item">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user navbar-pill navbar-action-link user-pill" style="display:flex;align-items:center;">
              <div class="user-pill-content">
                <img src="{{ $userAvatar }}" class="user-avatar" alt="">
                <div class="user-meta">
                  <span class="user-name">{{ $userDisplayName }}</span>
                  <span class="user-role">{{ $userRoleLabel }}</span>
                </div>
              </div>
              <i class="fas fa-chevron-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
              <div class="profile-dropdown-header">
                <span class="profile-name">{{ $userDisplayName }}</span>
                <span class="profile-role">{{ $userRoleLabel }} com acesso ao painel administrativo</span>
              </div>
              <div class="p-3 bg-white border-bottom">
                <a href="{{ route('admin.account.edit') }}" class="btn btn-light btn-block border">Editar cadastro</a>
              </div>
              <form action="{{ route('logout') }}" method="post" class="logout-form">
                @csrf
                <button type="submit" class="logout-button">
                  <i class="fas fa-sign-out-alt"></i>
                  Encerrar sessão
                </button>
              </form>
            </div>
          </li>
        </ul>
      </nav>

