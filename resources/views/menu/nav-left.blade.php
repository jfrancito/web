<div class="be-left-sidebar premium-sidebar">
  <div class="left-sidebar-wrapper">
    <div class="left-sidebar-spacer">
      <div class="left-sidebar-scroll be-left-sidebar-scroll">
        <div class="left-sidebar-content">
          
          {{-- Header del Sidebar --}}
          <div class="sidebar-brand-container">
            <div class="brand-info">
              <h3 class="empresa-nombre">{{Session::get('empresas')->NOM_EMPR}}</h3>
              <p class="centro-nombre"><i class="mdi mdi-pin"></i> {{Session::get('centros')->NOM_CENTRO}}</p>
            </div>
            <a href="javascript:void(0);" id="sidebar-toggle-btn" class="be-toggle-left-sidebar sidebar-toggle" onclick="toggleSidebarInternal()">
              <span class="icon mdi mdi-menu"></span>
            </a>
          </div>

          {{-- Buscador de Menú --}}
          <div class="sidebar-search">
            <div class="search-container">
              <i class="mdi mdi-search"></i>
              <input type="text" id="menu-search" placeholder="Buscar opción..." autocomplete="off">
            </div>
          </div>

          <ul class="sidebar-elements">
            <li class="divider">Navegación</li>
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
                  <li class="parent {{ $isGroupActive ? 'active open' : '' }}">
                    <a href="#">
                      <i class="icon mdi {{$grupo->icono}}"></i><span>{{$grupo->nombre}}</span>
                    </a>
                    <ul class="sub-menu">
                      {!! $optionsHtml !!}
                    </ul>
                  </li>
                @endif
            @endforeach
          </ul>

        </div>
      </div>
      {{-- Sidebar Footer --}}
      <div class="sidebar-footer">
        <div class="footer-user-info">
          <div class="user-avatar">
            {{ substr(Session::get('usuario')->nombre ?? 'U', 0, 1) }}
          </div>
          <div class="user-details">
            <span class="username">{{ Session::get('usuario')->nombre ?? Session::get('usuario')->name }}</span>
            <span class="role">{{ Session::get('usuario')->rol->nombre ?? 'Usuario' }}</span>
          </div>
        </div>
        <a href="{{ url('/cerrarsession') }}" class="logout-btn" title="Cerrar Sesión">
          <i class="mdi mdi-power"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<style>
  .premium-sidebar {
    background: #0f172a !important; /* Slate 900 */
    border-right: 1px solid rgba(255,255,255,0.05);
    height: 100vh !important;
    position: fixed !important;
    transition: width 0.3s ease !important;
    z-index: 1000;
  }

  /* Anular el pseudo-elemento obsoleto de Beagle que causa conflictos visuales */
  .be-left-sidebar.premium-sidebar::before {
    display: none !important;
  }

  .be-wrapper.sidebar-collapsed .premium-sidebar {
    width: 60px !important;
  }

  .be-wrapper.sidebar-collapsed .premium-sidebar .sidebar-brand-container .brand-info,
  .be-wrapper.sidebar-collapsed .premium-sidebar .sidebar-search,
  .be-wrapper.sidebar-collapsed .premium-sidebar .sidebar-elements li a span,
  .be-wrapper.sidebar-collapsed .premium-sidebar .sidebar-elements li a::after,
  .be-wrapper.sidebar-collapsed .premium-sidebar .sidebar-footer .user-details {
    display: none !important;
  }

  .be-wrapper.sidebar-collapsed .premium-sidebar .sidebar-elements li a {
    justify-content: center !important;
    padding: 10px 0 !important;
  }

  .be-wrapper.sidebar-collapsed .premium-sidebar .sidebar-elements li .icon {
    margin-right: 0 !important;
    font-size: 20px !important;
  }

  .be-wrapper.sidebar-collapsed .be-content {
    margin-left: 60px !important;
  }

  /* Responsive - Mobile & Tablet (Abajo de 1000px) */
  @media (max-width: 1000px) {
    .be-left-sidebar {
      display: none !important;
    }
  }

  @media (min-width: 1001px) {
    .mobile-toggle {
      display: none !important;
    }
  }

  .sidebar-brand-container {
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255,255,255,0.02);
  }

  .empresa-nombre {
    font-size: 14px !important;
    font-weight: 800 !important;
    color: #fff !important;
    margin: 0 !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .centro-nombre {
    font-size: 11px !important;
    color: #94a3b8 !important; /* Slate 400 */
    margin: 4px 0 0 0 !important;
  }

  .sidebar-toggle {
    color: #94a3b8 !important;
    font-size: 20px;
    transition: all 0.3s;
  }

  .sidebar-toggle:hover {
    color: #fff !important;
    transform: scale(1.1);
  }

  /* Buscador de Menú */
  .sidebar-search {
    padding: 15px 20px;
  }

  .search-container {
    position: relative;
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
    padding: 0 10px 0 35px;
    height: 36px;
    display: flex;
    align-items: center;
    border: 1px solid transparent;
    transition: all 0.2s;
  }

  /* Custom Scrollbar Premium */
  .be-left-sidebar-scroll {
    height: calc(100vh - 150px) !important; /* Ajustar según cabecera/pie */
    overflow-y: auto !important;
    overflow-x: hidden !important;
  }

  .be-left-sidebar-scroll::-webkit-scrollbar {
    width: 5px;
  }

  .be-left-sidebar-scroll::-webkit-scrollbar-track {
    background: transparent;
  }

  .be-left-sidebar-scroll::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
  }

  .be-left-sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
  }

  .search-container:focus-within {
    background: rgba(255,255,255,0.08);
    border-color: #6366f1; /* Indigo 500 */
  }

  .search-container i {
    position: absolute;
    left: 10px;
    color: #64748b;
    font-size: 18px;
  }

  .search-container input {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 13px;
    width: 100%;
    outline: none;
  }

  .search-container input::placeholder {
    color: #475569;
  }

  .sidebar-elements {
    margin: 0 !important;
    padding-bottom: 120px !important; /* Aumentamos el margen para asegurar visibilidad total */
  }

  .sidebar-elements .divider {
    color: #475569 !important;
    font-size: 10px !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 700;
    padding: 20px 25px 10px !important;
    background: transparent !important;
  }

  .sidebar-elements li a {
    padding: 7px 25px !important;
    transition: all 0.2s !important;
    display: flex !important;
    align-items: center;
    border-left: 3px solid transparent;
  }

  .sidebar-elements li a, 
  .sidebar-elements li.parent.open > a,
  .sidebar-elements li.parent > a:hover,
  .sidebar-elements li.parent > a:hover > span,
  .sidebar-elements li.parent > a:hover > i,
  .sidebar-elements li > a:hover > span,
  .sidebar-elements li > a:hover > i {
    color: #ffffff !important;
  }

  .sidebar-elements li.active > a {
    background: rgba(99, 102, 241, 0.15) !important;
    color: #818cf8 !important;
    border-left-color: #6366f1;
  }

  .sidebar-elements li.active > a .icon {
    color: #818cf8 !important;
  }

  .be-left-sidebar.premium-sidebar .sidebar-elements > li > a:hover,
  .sidebar-elements li a:hover {
    background: #1e293b !important; /* Slate 800 - Acorde al fondo #0f172a */
    position: relative;
  }

  .sidebar-elements li.parent > a {
    position: relative;
  }

  .sidebar-elements li a span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-block;
    max-width: 160px; /* Ajustar para que no tape la flecha */
  }

  .sidebar-elements li.parent > a::after {
    content: '\f2fb'; /* mdi-chevron-right */
    font-family: 'Material Icons';
    position: absolute;
    right: 15px;
    font-size: 18px;
    color: rgba(255,255,255,0.4);
    transition: all 0.3s;
  }

  .sidebar-elements li.parent.open > a::after {
    content: '\f2f9'; /* mdi-chevron-down */
    color: #fff;
  }

  .sidebar-elements li.parent.active > a::after {
    color: #818cf8;
  }

  .sidebar-elements li.parent.open > a {
    background: transparent !important;
  }

  .sidebar-elements li.parent.open > a:hover {
    background: #1e293b !important;
  }

  .sidebar-elements li .icon {
    font-size: 18px !important;
    margin-right: 1px !important;
  }

  /* Submenú Styles */
  .sub-menu {
    background: rgba(0,0,0,0.2) !important;
    padding: 5px 0 !important;
  }

  .sub-menu li a {
    padding: 8px 25px 8px 35px !important;
    font-size: 13px !important;
    color: rgba(255,255,255,0.8) !important;
    display: block !important;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .option-name {
    display: inline-block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .sub-menu li a:hover {
    color: #fff !important;
  }

  .sub-icon {
    font-size: 14px;
    margin-right: 1px;
    opacity: 0.8;
    color: #fff;
  }

  .left-sidebar-wrapper {
    position: relative !important;
    height: 100% !important;
    display: flex;
    flex-direction: column;
  }

  /* Footer Section */
  .sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #020617 !important; /* Slate 950 */
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid rgba(255,255,255,0.05);
    z-index: 9999 !important;
  }

  .footer-user-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .user-avatar {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 16px;
  }

  .user-details {
    display: flex;
    flex-direction: column;
  }

  .username {
    color: #f1f5f9;
    font-size: 13px;
    font-weight: 600;
    max-width: 120px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .role {
    color: #64748b;
    font-size: 11px;
  }

  .logout-btn {
    color: #ef4444;
    font-size: 20px;
    transition: all 0.3s;
    padding: 5px;
  }

  .logout-btn:hover {
    color: #f87171;
    transform: scale(1.1);
  }

  /* Robust Toggle Patch */
  .parent.open > .sub-menu {
    display: block !important;
  }
</style>

<script>
  (function() {
    // Función global para el toggle (Respaldo total)
    window.toggleSidebarInternal = function() {
      $('.be-wrapper').toggleClass('sidebar-collapsed');
      $('body').toggleClass('be-offcanvas-menu');
    };

    function initSidebarHandlers() {
      // 1. Buscador de Menú
      const menuSearch = document.getElementById('menu-search');
      if (menuSearch) {
        $(menuSearch).on('keyup', function() {
          const term = this.value.toLowerCase();
          $('.sidebar-elements > li:not(.divider)').each(function() {
            const $li = $(this);
            const text = $li.text().toLowerCase();
            if (text.indexOf(term) > -1) {
              $li.show();
              if (term.length > 0 && $li.hasClass('parent')) {
                $li.addClass('open');
                $li.find('.sub-menu').show();
              }
            } else {
              $li.hide();
            }
          });
          if (term.length === 0) {
            $('.parent:not(.active)').removeClass('open').find('.sub-menu').hide();
            $('.parent.active').addClass('open').find('.sub-menu').show();
          }
        });
      }

      // 2. Manejador de Clics ROBUSTO e INDEPENDIENTE
      $(document).off('click', '.sidebar-elements li.parent > a').on('click', '.sidebar-elements li.parent > a', function(e) {
        if (!$('.be-wrapper').hasClass('sidebar-collapsed')) {
          e.preventDefault();
          e.stopPropagation();
          const $li = $(this).parent();
          const $subMenu = $li.find('> .sub-menu');
          if ($li.hasClass('open')) {
            $subMenu.slideUp(200, function() { $li.removeClass('open'); $(this).removeAttr('style'); });
          } else {
            $li.siblings('.parent.open').each(function() {
              $(this).find('> .sub-menu').slideUp(200, function() { $(this).parent().removeClass('open'); $(this).removeAttr('style'); });
            });
            $subMenu.slideDown(200, function() { $li.addClass('open'); $(this).removeAttr('style'); });
          }
        }
      });

      // 3. Toggle de Sidebar (Botón Específico e ID)
      $(document).off('click', '.be-toggle-left-sidebar').on('click', '.be-toggle-left-sidebar', function(e) {
        e.preventDefault(); e.stopPropagation();
        window.toggleSidebarInternal();
      });

      // 4. Cerrar menú al hacer clic en el backdrop (Móvil)
      $(document).on('click', 'body.be-offcanvas-menu', function(e) {
        if (e.target === this) { window.toggleSidebarInternal(); }
      });
    }

    if (window.jQuery) { $(document).ready(initSidebarHandlers); } 
    else { window.addEventListener('load', initSidebarHandlers); }
  })();
</script>

