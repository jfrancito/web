<div class="be-left-sidebar">
  <div class="left-sidebar-wrapper"><a href="#" class="left-sidebar-toggle">Inicio</a>
    <div class="left-sidebar-spacer">
      <div class="left-sidebar-scroll">
        <div class="left-sidebar-content">
          <div class="sidebar-brand-container">
            <div class="sidebar-header-toggle">
              <div class="brand-info">
                <h3>{{Session::get('empresas')->NOM_EMPR}}</h3>
                <p>{{Session::get('centros')->NOM_CENTRO}}</p>
              </div>
              <a href="#" class="be-toggle-left-sidebar">
                <span class="icon mdi mdi-menu"></span>
              </a>
            </div>
          </div>
          <ul class="sidebar-elements">
            <li class="divider">Menú</li>
            <li class="{{ Request::is('bienvenido') ? 'active' : '' }}"><a href="{{ url('/bienvenido') }}"><i class="icon mdi mdi-home"></i><span>Inicio</span></a>
            </li>
            @foreach(Session::get('listamenu') as $grupo)

                @if($grupo->orden == 100)
                    <li class="divider">Reportes</li>
                @endif
          

                @php
                  $isGroupActive = false;
                  foreach($grupo->opcion as $opcion) {
                    if (in_array($opcion->id, Session::get('listaopciones'))) {
                      $opcionUrl = url('/'.$opcion->pagina.'/'.Hashids::encode(substr($opcion->id, -8)));
                      if (Request::url() == $opcionUrl) {
                        $isGroupActive = true;
                        break;
                      }
                    }
                  }
                @endphp

                <li class="parent {{ $isGroupActive ? 'active open' : '' }}">
                  <a href="#">
                    <i class="icon mdi {{$grupo->icono}}"></i><span>{{$grupo->nombre}}</span>
                  </a>
                  <ul class="sub-menu">
                    @foreach($grupo->opcion as $opcion)
                      @if(in_array($opcion->id, Session::get('listaopciones')))
                        @php 
                          $currentOpUrl = url('/'.$opcion->pagina.'/'.Hashids::encode(substr($opcion->id, -8)));
                          $isActiveOp = (Request::url() == $currentOpUrl);
                        @endphp
                        <li class="{{ $isActiveOp ? 'active' : '' }}">
                          <a href="{{ $currentOpUrl }}" data-toggle="tooltip" data-container="body" data-placement="right" title="{{$opcion->nombre}}">
                            <span class="mdi mdi-chevron-right" style="font-size: 10px; margin-right: 5px;"></span>
                            {{$opcion->nombre}}
                          </a>
                        </li>
                      @endif
                    @endforeach
                  </ul>
                </li>
            @endforeach
          </ul>

          {{-- Sidebar Footer --}}
          <div class="sidebar-footer">
            <div class="sidebar-footer-content">
              <div class="footer-user-avatar">
                <i class="mdi mdi-account-circle"></i>
              </div>
              <div class="footer-user-info">
                <span class="footer-username">{{ Session::get('usuario')->nombre ?? Session::get('usuario')->name }}</span>
                <span class="footer-role">{{ Session::get('usuario')->rol->nombre ?? 'Usuario' }}</span>
              </div>
            </div>
            <a href="{{ url('/cerrarsession') }}" class="footer-logout" data-toggle="tooltip" data-container="body" data-placement="right" title="Cerrar Sesión">
              <i class="mdi mdi-power"></i>
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

