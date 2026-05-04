<!-- Menú Completo Móvil (Solo visible en <1000px) -->
<div class="mobile-only-nav">
  <!-- Barra Superior Móvil Independiente -->
  <div class="custom-mobile-topbar {{Session::get('color')}}">
    <div class="mobile-brand-toggle" id="mobile-top-trigger">
      <div class="mobile-avatar-icon">
        <i class="mdi mdi-home"></i>
      </div>
      <span class="mobile-user-name">{{Session::get('usuario')->nombre ?? Session::get('usuario')->name}}</span>
      <i class="mdi mdi-menu mobile-caret"></i>
    </div>
  </div>


  <!-- Contenedor Desplegable -->
  <div class="mobile-accordion-menu">

  <div class="mobile-menu-dropdown" id="mobile-menu-dropdown">
    <div class="mobile-menu-header">
      <h3 class="empresa-nombre">{{Session::get('empresas')->NOM_EMPR}}</h3>
      <p class="centro-nombre"><i class="mdi mdi-pin"></i> {{Session::get('centros')->NOM_CENTRO}}</p>
    </div>
    
    <ul class="mobile-menu-list">
      <li class="{{ Request::is('bienvenido') ? 'active' : '' }}">
        <a href="{{ url('/bienvenido') }}">
          <i class="icon mdi mdi-home"></i><span>Inicio</span>
        </a>
      </li>

      @foreach(Session::get('listamenu') as $grupo)
        @if($grupo->orden == 100)
          <li class="divider">Reportes</li>
        @endif

        @php
          $isGroupActive = false;
          $optionsHtml = '';
          foreach($grupo->opcion as $opcion) {
            if (in_array($opcion->id, Session::get('listaopciones'))) {
              $opcionUrl = url('/'.$opcion->pagina.'/'.Hashids::encode(substr($opcion->id, -8)));
              $isActiveOp = (Request::url() == $opcionUrl);
              if ($isActiveOp) $isGroupActive = true;
              
              $optionsHtml .= '<li class="'. ($isActiveOp ? 'active' : '') .'">
                                <a href="'. $opcionUrl .'">
                                  <span class="option-name">'. $opcion->nombre .'</span>
                                </a>
                              </li>';
            }
          }
        @endphp

        @if(!empty($optionsHtml))
          <li class="mobile-parent {{ $isGroupActive ? 'active open' : '' }}">
            <a href="#">
              <i class="icon mdi {{$grupo->icono}}"></i><span>{{$grupo->nombre}}</span>
              <i class="mdi mdi-chevron-down expand-icon"></i>
            </a>
            <ul class="mobile-sub-menu" style="{{ $isGroupActive ? 'display:block;' : 'display:none;' }}">
              {!! $optionsHtml !!}
            </ul>
          </li>
        @endif
      @endforeach
    </ul>

    <div class="mobile-menu-footer">
      <a href="{{ url('/cambiarperfil/') }}" class="btn btn-primary btn-block mobile-action-btn">
        <i class="mdi mdi-settings"></i> Cambiar de Perfil
      </a>
      <a href="{{ url('/cerrarsession') }}" class="btn btn-danger btn-block mobile-action-btn" style="margin-top: 10px;">
        <i class="mdi mdi-power"></i> Cerrar Sesión
      </a>
    </div>
    </div>
  </div>
</div>
<!-- /Fin de mobile-only-nav -->

<style>
  .mobile-only-nav {
    display: none;
  }

  @media (max-width: 1000px) {
    .mobile-only-nav {
      display: block;
    }

    .custom-mobile-topbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 60px;
      z-index: 1030;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      background-color: #b03f3f; /* Fallback (color-iin) */
    }

    /* Colores exactos de las empresas para la barra móvil */
    .custom-mobile-topbar.color-iin { background-color: #b03f3f !important; color: white !important; }
    .custom-mobile-topbar.color-ico { background-color: #34a853 !important; color: white !important; }
    .custom-mobile-topbar.color-itr { background-color: #7f8c8d !important; color: white !important; }
    .custom-mobile-topbar.color-ich { background-color: #0ea5e9 !important; color: white !important; }
    .custom-mobile-topbar.color-iaa { background-color: #6366f1 !important; color: white !important; }

    .mobile-brand-toggle {
      display: flex;
      align-items: center;
      width: 100%;
      height: 60px;
      padding: 0 15px;
      color: #ffffff;
      cursor: pointer;
      font-size: 15px;
    }


    .mobile-avatar-icon {
      background: #ffffff;
      color: #6366f1; /* Ajustar según sea necesario */
      width: 35px;
      height: 35px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 20px;
      margin-right: 12px;
    }

    .mobile-user-name {
      flex: 1;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      font-weight: 500;
    }

    .mobile-caret {
      font-size: 28px; /* Ícono de hamburguesa más grande */
      color: #ffffff;
    }
  }

  .mobile-accordion-menu {
    display: none;
    position: absolute;
    top: 60px;
    left: 0;
    width: 100%;
    z-index: 1000;
  }

  @media (max-width: 1000px) {
    .desktop-brand {
      display: none !important;
    }
    .mobile-accordion-menu {
      display: block;
    }
  }

  .mobile-menu-dropdown {
    display: none;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    width: 100%;
    max-height: calc(100vh - 60px);
    overflow-y: auto;
  }

  .mobile-menu-header {
    padding: 15px 25px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
  }

  .mobile-menu-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: bold;
    color: #1e293b;
  }

  .mobile-menu-header p {
    margin: 4px 0 0;
    font-size: 11px;
    color: #64748b;
  }

  .mobile-menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .mobile-menu-list > li > a {
    display: flex;
    align-items: center;
    padding: 14px 25px;
    color: #475569;
    text-decoration: none;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 500;
  }

  .mobile-menu-list > li.active > a,
  .mobile-menu-list > li > a:hover {
    background: #f8fafc;
    color: #0f172a;
  }

  .mobile-menu-list > li .icon {
    font-size: 20px;
    margin-right: 15px;
    color: #64748b;
  }

  .mobile-menu-list > li.active > a .icon {
    color: #6366f1;
  }

  .mobile-parent > a {
    justify-content: space-between;
  }

  .mobile-parent > a span {
    flex: 1;
  }

  .expand-icon {
    transition: transform 0.3s;
  }

  .mobile-parent.open > a .expand-icon {
    transform: rotate(180deg);
  }

  .mobile-sub-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    background: #f8fafc;
  }

  .mobile-sub-menu li a {
    display: block;
    padding: 12px 25px 12px 60px;
    color: #64748b;
    text-decoration: none;
    font-size: 13px;
    border-bottom: 1px solid #f1f5f9;
  }

  .mobile-sub-menu li.active a,
  .mobile-sub-menu li a:hover {
    color: #6366f1;
    background: #f1f5f9;
    font-weight: 600;
  }

  .mobile-menu-footer {
    padding: 20px 25px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
  }

  .mobile-action-btn {
    padding: 12px 15px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .mobile-action-btn i {
    margin-right: 8px;
    font-size: 20px;
  }

  .divider {
    padding: 12px 25px;
    background: #f1f5f9;
    font-size: 10px;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Manejar el despliegue del menú principal móvil
    const mainBar = document.getElementById('mobile-top-trigger');
    const dropdown = document.getElementById('mobile-menu-dropdown');
    
    if (mainBar && dropdown) {
      mainBar.addEventListener('click', function() {
        this.classList.toggle('open');
        $(dropdown).slideToggle(300);
      });
    }

    // Manejar submenús internos
    const mobileParents = document.querySelectorAll('.mobile-parent > a');
    mobileParents.forEach(function(parentLink) {
      parentLink.addEventListener('click', function(e) {
        e.preventDefault();
        const li = this.parentElement;
        const subMenu = li.querySelector('.mobile-sub-menu');
        
        if (li.classList.contains('open')) {
          li.classList.remove('open');
          $(subMenu).slideUp(200);
        } else {
          // Cerrar otros
          document.querySelectorAll('.mobile-parent.open').forEach(function(openLi) {
            openLi.classList.remove('open');
            $(openLi.querySelector('.mobile-sub-menu')).slideUp(200);
          });
          
          li.classList.add('open');
          $(subMenu).slideDown(200);
        }
      });
    });
  });
</script>

