@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/buttons.bootstrap.min.css') }} "/>
@stop
@section('section')

<div class="be-content">
	<div class="main-content container-fluid">
          <div class="content">
            <div class="panel panel-default">
              <div class="panel-heading panel-heading-divider" style="background-color: #f5f5f5; border-bottom: 2px solid #4285f4; color: #333; font-weight: bold; font-size: 1.2em;">
                <i class="icon mdi mdi-assignment-check" style="color: #4285f4; font-size: 1.5em; vertical-align: middle; margin-right: 10px;"></i>
                REGLA COMPROMISO PAGO
                <span class="panel-subtitle">Reporte detallado de compromisos de pago por orden</span>
              </div>
              <div class="panel-body" style="padding-top: 20px;">
                <div class="col-xs-12">
                  <div class="panel panel-default border-gradient" style="border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div class="panel-body" style="padding: 20px;">
                      <form method="POST" action="{{ url('/gestion-de-regla-compromiso-pago/'.$idopcion) }}" style="display: flex; align-items: flex-end; flex-wrap: wrap; gap: 10px;">
                        {{ csrf_field() }}

                        <div class="form-group" style="margin-bottom: 0;">
                          <label style="font-weight: 600; color: #555; display: block; margin-bottom: 8px;">Fecha Inicio:</label>
                          <div class="input-group date datetimepicker">
                            <input size="16" type="text" value="{{$fechainicio}}" name="fechainicio" id="fechainicio" class="form-control input-sm" style="border-radius: 4px 0 0 4px;">
                            <span class="input-group-addon btn btn-primary" style="background-color: #4285f4; border-color: #4285f4; color: white;"><i class="icon-th mdi mdi-calendar"></i></span>
                          </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                          <label style="font-weight: 600; color: #555; display: block; margin-bottom: 8px;">Fecha Fin:</label>
                          <div class="input-group date datetimepicker">
                            <input size="16" type="text" value="{{$fechafin}}" name="fechafin" id="fechafin" class="form-control input-sm" style="border-radius: 4px 0 0 4px;">
                            <span class="input-group-addon btn btn-primary" style="background-color: #4285f4; border-color: #4285f4; color: white;"><i class="icon-th mdi mdi-calendar"></i></span>
                          </div>
                        </div>

                        <input type="hidden" name="idopcion" id="idopcion" value="{{$idopcion}}">

                        <div class="form-group" style="margin-bottom: 0;">
                          <button type="button" id="buscarreglacompromiso" class="btn btn-space btn-primary" style="background-color: #4285f4; border-color: #4285f4; font-weight: 600; padding: 6px 20px; border-radius: 4px; transition: all 0.3s;">
                            <i class="icon mdi mdi-search" style="margin-right: 5px;"></i> Buscar
                          </button>
                          <button type="button" id="descargarexcelcompromiso" class="btn btn-space btn-success" style="background-color: #34a853; border-color: #34a853; font-weight: 600; padding: 6px 20px; border-radius: 4px; transition: all 0.3s;">
                            <i class="icon mdi mdi-file-excel" style="margin-right: 5px;"></i> Excel
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-xs-12 listaregla_compromiso_pago">
                  <div class='listajax reporteajax'>
                    @include('regla.ajax.listacompromisopago')
                  </div>
                </div>
              </div>
            </div>
          </div>
	</div>
</div>

@stop

@section('script')

  <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.html5.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.flash.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.print.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.colVis.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.bootstrap.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/js/app-tables-datatables.js?v='.$version) }}" type="text/javascript"></script>

  <script type="text/javascript">
    $(document).ready(function(){
      //initialize the responsive datatables helper
      App.init();
      App.dataTables();
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
  <script src="{{ asset('public/js/regla/compromisopago.js?v='.$version) }}" type="text/javascript"></script>

@stop
