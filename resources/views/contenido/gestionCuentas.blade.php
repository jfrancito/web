@extends('template')

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }}"/>
  <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }}"/>
@stop

@section('section')
<div class="be-content contenido cartera">
  <div class="main-content container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-default panel-border-color panel-border-color-primary">
          <div class="panel-heading">
            Gestión de Cuentas - Cartera
          </div>
          <div class="panel-body">
            <!-- Filtros -->
            <div class="row" style="margin-bottom: 20px;">
              <div class="col-xs-12 col-md-3">
                <label><b>Jefe de Venta:</b></label>
                <select id="filtro_jv" class="form-control input-sm select2">
                  <option value="">TODOS</option>
                  @foreach($ojefeVenta as $jv)
                    <option value="{{ $jv->COD_CATEGORIA }}">{{ $jv->NOM_CATEGORIA }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-xs-12 col-md-3">
                <label><b>Criterio de búsqueda:</b></label>
                <select id="filtro_criterio" class="form-control input-sm">
                  <option value="Cliente">Nombre de Cliente</option>
                  <option value="codCliente">Código de Cliente</option>
                </select>
              </div>

              <div class="col-xs-12 col-md-4">
                <label><b>Buscar:</b></label>
                <div class="input-group input-group-sm">
                  <input type="text" id="filtro_buscar" placeholder="Escriba aquí para buscar..." class="form-control">
                  <span class="input-group-btn">
                    <button type="button" id="btn-buscar-cuentas" class="btn btn-primary">
                      <i class="icon mdi mdi-search"></i> Buscar
                    </button>
                  </span>
                </div>
              </div>
            </div>

            <!-- Contenedor AJAX de la tabla -->
            <div class="listacuentas-container">
              <!-- Se carga vía AJAX -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Editar Regla de Crédito -->
  <div id="modal-regla-credito" tabindex="-1" role="dialog" class="modal fade colored-header colored-header-primary">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" data-dismiss="modal" aria-hidden="true" class="close">
            <span class="mdi mdi-close"></span>
          </button>
          <h3 class="modal-title">Gestión de Límite y Crédito</h3>
        </div>
        <div class="modal-body" id="modal-regla-body">
          <!-- Contenido cargado vía AJAX -->
        </div>
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn btn-default">Cancelar</button>
          <button type="button" id="btn-guardar-regla" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </div>
    </div>
  </div>

</div>
@stop

@section('script')
  <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/js/cartera/cartera.js?v='.$version) }}" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      if($.fn.select2) {
        $('.select2').select2();
      }
    });
  </script>
@stop
