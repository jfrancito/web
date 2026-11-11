<!-- Menú Superior (Solo Escritorio) -->
<nav class="navbar navbar-default navbar-fixed-top be-top-header {{Session::get('color')}} desktop-only-nav">
  <div class="container-fluid">
    <div class="navbar-header">
      <a href="{{ url('/bienvenido') }}" class="navbar-brand premium-navbar-brand">
        <span class="brand-text-main">INDUAMERICA</span>
        <span class="brand-text-sub">SISTEMA DE VENTAS</span>
      </a>
    </div>

    <div class="be-right-navbar {{Session::get('color')}}">
      <ul class="nav navbar-nav navbar-left hide-mobile">
        <li class="nav-welcome-text">
          Bienvenido al Portal Comercial de Induamerica
        </li>
      </ul>

      <ul class="nav navbar-nav navbar-right be-user-nav">
        <li class="date-display hide-mobile">
          <span class="mdi mdi-calendar-alt"></span>
          {{ date('d-m-Y') }}
        </li>


        <li class="dropdown">
          <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle"><img src="{{ asset('public/img/avatar1.png') }}" alt="Avatar"><span class="user-name">{{Session::get('usuario')->nombre}}</span></a>
          <ul role="menu" class="dropdown-menu">
            <li>
              <div class="user-info">
                <div class="user-name">{{Session::get('usuario')->nombre}}</div>
                <div class="user-position online">disponible</div>
              </div>
            </li>
            <li><a href="{{ url('/cambiarperfil/') }}"><span class="icon mdi mdi-settings"></span> Cambiar de perfil</a></li>
            <li><a href="{{ url('/cerrarsession') }}"><span class="icon mdi mdi-power"></span> Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
      <!-- <div class="panel-heading" align="center">
        <h3 class="media-heading">
            <div class="ubicacion">
            </div>
        </h3>
      </div> -->

    <div id="be-navbar-collapse" class="navbar-collapse collapse"></div>

  </div>
</nav>