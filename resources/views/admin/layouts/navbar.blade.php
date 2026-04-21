<style>
        .main-navbar-cms {
            background: linear-gradient(135deg, #0d3358 0%, #155c99 52%, #1e90ff 100%) !important;
            color: #fff !important;
            position: inherit;
            box-shadow: 0 14px 34px rgba(13, 51, 88, 0.22);
            padding: 8px 22px;
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
            min-width: 250px;
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

    <nav class="navbar navbar-expand-lg main-navbar main-navbar-cms">
        <div class="navbar-left-actions mr-auto">
          <a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a>
        </div>
        <ul class="navbar-nav navbar-right" >
          @php
              $notificationOpenUrl = route('admin.notifications.read', ['redirect_to' => $navbarNotificationTargetUrl ?? route('admin.agendamentos.index')]);
              $userDisplayName = trim((string) ((Auth::user()->nome ?? '') . ' ' . (Auth::user()->sobrenome ?? '')));
              $userRoleLabel = Auth::user()->normalizedRole() === 'profissional' ? 'Profissional' : 'Administrador';
              $userAvatar = Auth::user()->profile_photo_url;
          @endphp
          <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg navbar-pill {{ ($navbarNotificationCount ?? 0) > 0 ? 'beep' : '' }}"><i class="far fa-bell"></i><span class="d-none d-lg-inline">Notificações</span>@if(($navbarNotificationCount ?? 0) > 0)<span class="notification-badge">{{ $navbarNotificationCount }}</span>@endif</a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Notificações
                <div class="float-right">
                  <a href="{{ $notificationOpenUrl }}">Ver agenda</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-icons">
                @forelse(($navbarNotifications ?? collect()) as $notification)
                  <a href="{{ route('admin.notifications.read', ['redirect_to' => $notification->navbar_target_url ?? ($navbarNotificationTargetUrl ?? route('admin.agendamentos.calendar'))]) }}" class="dropdown-item {{ !empty($notification->navbar_is_unread) ? 'dropdown-item-unread' : '' }}">
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
                <a href="{{ $notificationOpenUrl }}">Veja tudo <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li>
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user navbar-pill user-pill">
            <div class="user-pill-content">
              <img src="{{ $userAvatar }}" class="user-avatar" alt="">
              <div class="user-meta">
                <span class="user-name">{{ $userDisplayName }}</span>
                <span class="user-role">{{ $userRoleLabel }}</span>
              </div>
            </div>
            <i class="fas fa-chevron-down"></i></a>
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

