<style>
        .btn-primary{
            background-color:#4f066b;
            border:none;
        }

        .btn-primary:hover{
            background-color:#7c03ac!important;
            border:none;
        }


    </style>

    <nav class="navbar navbar-expand-lg main-navbar" style="background-color:#4f066b!important;
            color:#fff!important; position:inherit;">
        <form class="form-inline mr-auto ">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right" >
          <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle beep"><i class="far fa-envelope"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Mensagens
                <div class="float-right">
                  <a href="#">Leia todas</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-message">
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('backend/assets/img/avatar/avatar-1.png') }}" class="rounded-circle">
                    <div class="is-online"></div>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Maykon</b>
                    <p>Fala dev!</p>
                    <div class="time">07 Setembro 2025</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('backend/assets/img/avatar/avatar-2.png') }}" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Maykon</b>
                    <p>Fala dev!</p>
                    <div class="time">07 Setembro 2025</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('backend/assets/img/avatar/avatar-3.png') }}" class="rounded-circle">
                    <div class="is-online"></div>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Maykon</b>
                    <p>Fala dev!</p>
                    <div class="time">07 Setembro 2025</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('backend/assets/img/avatar/avatar-4.png') }}" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Maykon</b>
                    <p>Fala dev!</p>
                    <div class="time">07 Setembro 2025</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('backend/assets/img/avatar/avatar-5.png') }}" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Maykon</b>
                    <p>Fala dev!</p>
                    <div class="time">07 Setembro 2025</div>
                  </div>
                </a>
              </div>
              <div class="dropdown-footer text-center">
                <a href="#">Veja todas <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li>
          <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg beep"><i class="far fa-bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Notificações
                <div class="float-right">
                  <a href="#">Leia tudo</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-icons">
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-icon bg-primary text-white">
                    <i class="fas fa-code"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Sou msdev 3
                    <div class="time text-primary">2 Min</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-info text-white">
                    <i class="far fa-user"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Sou</b> msdev 3 <b>do canal</b>Maykon Silveira
                    <div class="time">10 horas</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-success text-white">
                    <i class="fas fa-check"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Sou</b> msdev 3 <b>do canal</b>Maykon Silveira
                    <div class="time">10 horas</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-danger text-white">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Sou</b> msdev 3 <b>do canal</b>Maykon Silveira
                    <div class="time">10 horas</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-info text-white">
                    <i class="fas fa-bell"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Sou</b> msdev 3 <b>do canal</b>Maykon Silveira
                    <div class="time">10 horas</div>
                  </div>
                </a>
              </div>
              <div class="dropdown-footer text-center">
                <a href="#">Veja tudo <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li>
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">

            <img  src="{{ asset('backend/assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1" alt="">


            <div class="d-sm-none d-lg-inline-block">{{ Auth::user()->nome }} {{ Auth::user()->sobrenome }}</div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title"></div>
              <a href="" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Perfil
              </a>
              <a href="redes-sociais.index" class="dropdown-item has-icon">
                <i class="fas fa-bolt"></i> Redes Sociais
              </a>
              <a href="configura-email.index" class="dropdown-item has-icon">
                <i class="fas fa-cog"></i> Configurações
              </a>
              <div class="dropdown-divider"></div>
              <!-- Authentication -->
              <form action="{{ route('logout') }}" method="post">
                @csrf
              <a href="{{ csrf_token() }}" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Sair
              </a>
            </form>
            </div>
          </li>
        </ul>
      </nav>
