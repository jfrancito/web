@extends('template')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jqvmap/jqvmap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />
    <style type="text/css">
        
        #chart {
          max-width: 650px;
          margin: 35px auto;
        }

    </style>

@stop

@section('section')

  <div class="be-content premium-dashboard-bg">
    <div class="main-content container-fluid">
        <div class="row" style="text-align: left !important;">
            <div class="col-xs-12 col-md-8 col-lg-5">
                <a href="{{ url('/gestion-de-toma-de-pedido/vm') }}" class="premium-card-link">
                    <div class="premium-widget-modern">
                      <div class="icon-bg"></div>
                      
                      <div class="card-content">
                        <span class="card-label">Pedidos del Sistema</span>
                        <div class="card-value">
                            <span data-toggle="counter" data-end="{{$countpedidos}}" class="number">0</span>
                            <span class="trend">Activos</span>
                        </div>
                      </div>

                      <div class="card-footer">
                        <span>Ver todos los pedidos</span>
                        <div class="arrow-circle">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                      </div>
                    </div>
                </a>
            </div>
        </div>



    </div>
  </div>

@stop 

@section('script')

    {{-- Librerías del dashboard antiguo removidas (app-dashboard.js no se usa) --}}
    {{-- Si en el futuro se necesitan gráficas Flot, se deberán agregar aquí --}}

    <script type="text/javascript">
      $(document).ready(function(){
        App.init();
        // App.dashboard() fue removido porque los contenedores de gráficas 
        // del tema antiguo ya no existen en el dashboard premium actual.
      });
    </script>   

@stop
