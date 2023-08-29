

<input type="hidden" name="correlativo" id='correlativo' value='{{$correlativo}}'>

<div class="main-content container-fluid" style = "padding: 0px;">
  <div class="row">
    <div class="col-sm-12" style = "padding-left: 0px;padding-right : 0px">
      <div class="panel panel-default panel-table">
        <div class="panel-heading">
            <div class='col-sm-6'>
              <b>Registro de Picking</b>
            </div>
          </div>
        </div>

        <div id="table1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
            <div class="row be-datatable-header">
              <div class="col-sm-6">
                  <div class="dataTables_length" id="table1_length">
                  <label>Cantidad Palets : <input type="text" class="form-control input-sm importe" 
                    placeholder="" aria-controls="table1" value='{{$palets}}' id="palets"></label>
                  
                  </div>
                </div>
                <div class="col-sm-6"></div>
            </div>
        </div>

        <div class="panel-body">
          <div style = "padding: 0px !important;"> 
            <div style="margin-bottom: 10px;">
                @php
                  $total_peso = 0;
                @endphp
              <table class="table table-pedidos-despachos" style='font-size: 0.85em;' id="tablepedidodespacho" >
                <thead>
                  <tr>
                    <th class='center'>Tipo</th>
                    <th class='center'>Código</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th class='center'>Cant. Pendiente</th>                    
                    <th class='center'>Atender</th>
                    <th class='center'>Excedente</th>
                    <th>Peso</th>   
                    <th>Fecha Entrega</th>
                    <th>Destino</th>
                    <th class='center'>X</th>
                  </tr>
                </thead>
            
                <tbody>

                  @foreach($array_detalle_producto as $index => $item)

                    @php
                      $total_peso =  $total_peso + $item['peso_total'] ;
                    @endphp
                  <tr class='fila_pedido'
                      
                      data_producto="{{$item['producto_id']}}"
                      data_cantidad="{{$item['cantidad_atender']}}"
                      fecha_entrega="{{$item['fecha_entrega']}}"
                      nombre_producto="{{$item['producto_nombre']}}"
                      data_correlativo="{{$item['correlativo']}}"
                  >

                  <td class='center'>
                      <b style="padding-right: 4px;">{{$item['tipo_operacion']}}</b>
                  </td>

                  <td class='center'>
                      <b style="padding-right: 4px;">{{$item['transferencia_id']}}</b>
                  </td>

                  <td class="cell-detail relative"> 
                      <span>{{$item['cliente_nom']}}</span>
                  </td>

                  <td class="cell-detail relative" rowspan = "" > 
                      <span>{{$item['producto_nombre']}}</span>
                      <span class="cell-detail-description-producto">
                      {{$item['nombre_unidad_medida']}} de  {{$item['producto_peso']}} kg 
                      </span>
                  </td>
                          
                   <td class='center'>
                       <b>{{number_format($item['cantidad_pendiente'], 2, '.', ',')}}</b>
                  </td>

                  <td >
                     <input type="text"
                         id="atender" 
                         name="atender"
                         value="{{number_format($item['cantidad_atender'], 2, '.', ',')}}"
                         class="form-control input-sm dinero updatepriced"
                         readonly>
                  </td>

                  <td >
                     <input type="text"
                         id="excedente" 
                         name="excedente"
                         value="{{number_format($item['cantidad_excedente'], 2, '.', ',')}}"
                         class="form-control input-sm dinero"
                         readonly>
                  </td>

                  <td class='center'>{{number_format($item['peso_total'],4,'.',',')}}</td>
                
                  <td class="cell-detail">
                      <span><b>Día</b> : {{$item['fecha_entrega']}}</span>
                      <span><b>Hora</b> : {{$item['hora_entrega']}}</span>
                  </td>

                  <td class="cell-detail">
                      <span><b>Departamento</b> : {{$item['departamento_nom']}}</span>
                      <span><b>Provincia</b> : {{$item['provincia_nom']}}</span>
                      <span><b>Distrito</b> : {{$item['distrito_nom']}}</span>
                  </td>

                  <td class='center'>
                     <span class="badge badge-danger cursor eliminar-producto-picking">
                      <span class="mdi mdi-close" style='color: #fff;'></span>
                    </span>
                  </td>

                  @endforeach

                </tbody>

                <tfooter>
                  <tr>
                    <th colspan="6"></th>
                    <th style='text-align: right'> Total Peso Kg.:</th>
                    <th class='total_peso_t'>{{ number_format($total_peso,4,'.',',') }}</th>
                    <th colspan="4"></th>
                  </tr>
                </tfooter>
                
              </table>

            </div>
          </div>

        </div>

      </div>
    </div>
    <br>

    <div class="col-xs-12" >    
      <div class="col-xs-6" style="text-align: right;">
          <form method="POST"  id='formguardarpicking' action="{{ url('/agregar-picking/'.$opcion_id.'/'.$idpicking) }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
              {{ csrf_field() }}
            <input type="hidden" name="array_detalle_producto" id='array_detalle_producto' value='{{json_encode($array_detalle_producto)}}'>
            <input type="hidden" name="cantidad_palets" id='cantidad_palets' value=''>
            <input type="hidden" name="centro_origen_id" id='centro_origen_id' value=''>
            
            <button type="button" class="btn btn-space btn-primary btn-guardar-pedido">Guardar</button>
          </form>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){

    $('.importe').inputmask({ 'alias': 'numeric', 
        'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
        'digitsOptional': false, 
        'prefix': '', 
        'placeholder': '0'
    });

  });
</script> 

