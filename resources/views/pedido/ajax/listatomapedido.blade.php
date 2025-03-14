<table id="tablatomapedido" class="table table-striped table-hover table-fw-widget dt-responsive nowrap listatabla" style='width: 100%;'>
  <thead>
    <tr> 

      <th style="display: none;" class='ocultar'>
        <div class="text-center be-checkbox be-checkbox-sm" >
          <input  type="checkbox"
                  class="todo"
                  id="todo"
          >
          <label  for="todo"
                  data-atr = "todas"
                  class = "checkbox"                    
                  name="todo"
            ></label>
        </div>
      </th>

      <th>Codigo</th>
      <th>Fecha Venta</th>
      <th>Cliente</th>
      <th>Estado</th>
      <th>Total</th>
      <th>Registro pedido</th>
      <th>Ver</th>

    </tr>
  </thead>
  <tbody>
   @foreach($listapedidos as $item)
      <tr>
        <td style="display: none;" class='ocultar'>  
          <div class="text-center be-checkbox be-checkbox-sm" >
            <input  type="checkbox"
                    class="{{Hashids::encode(substr($item->id, -8))}} input_check_pe_ln check{{Hashids::encode(substr($item->id, -8))}}" 
                    id="{{Hashids::encode(substr($item->id, -8))}}" 
                    @if($item->COD_CATEGORIA != 'EPP0000000000003') disabled @endif>

            <label  for="{{Hashids::encode(substr($item->id, -8))}}"
                  data-atr = "ver"
                  class = "checkbox"                    
                  name="{{Hashids::encode(substr($item->id, -8))}}"
            ></label>
          </div>
        </td>

        <td>{{$item->codigo}}</td>
        <td>{{date_format(date_create($item->fecha_time_venta), 'd-m-Y H:i')}}</td>

        <td class="cell-detail"> 
          <span>{{$item->empresa->NOM_EMPR}}</span>
          <span class="cell-detail-description"><b>Tipo Pago : </b> {{$funcion->funciones->data_categoria($item->tipopago_id)->NOM_CATEGORIA}}</span>
          <span class="cell-detail-description"><b>Vendedor : </b> {{$funcion->funciones->data_usuario($item->usuario_crea)->nombre}}</span>
          <span class="cell-detail-description"><b>Empresa (registro pedido): </b> {{$funcion->funciones->data_empresa($item->empresa_id)->NOM_EMPR}}</span>
          <span class="cell-detail-description"><b>Centro : </b> {{$funcion->funciones->data_centro($item->centro_id)->NOM_CENTRO}}</span>
          <span class="cell-detail-description"><b>Documento : </b> {{$item->empresa->NRO_DOCUMENTO}}</span>
          <span class="cell-detail-description"><b>Orden CEN : </b> {{$item->nro_orden_cen}}</span>
          <span class="cell-detail-description"><b>Tipo Documento : </b> {{$item->tipo_documento}}</span>
                    <span class="cell-detail-description"><b>Tipo Venta : </b> {{$item->tipo_venta}}</span>
        </td>

        <td>
            @if($item->COD_CATEGORIA == 'EPP0000000000003') 
              @if($funcion->funciones->pedido_producto_registrado_atendido($item) == '0') 
                <span class="badge badge-warning">{{$item->NOM_CATEGORIA}}</span> 
              @else
                  <span class="badge badge-success">PARCIALMENTE ATENDIDA</span> 
              @endif
            @else
              @if($item->COD_CATEGORIA == 'EPP0000000000005') 
                  <span class="badge badge-danger">{{$item->NOM_CATEGORIA}}</span>
              @else
                  <span class="badge badge-success">{{$item->NOM_CATEGORIA}}</span>
              @endif
            @endif
            <br>
            <span class="badge badge-defaul" style='margin-top:3.5px'>
              <a href="{{ url('/imprimir-pedido/'.Hashids::encode(substr($item->id, -8))) }}" target="_blank">
                Imprimir
              </a>
            </span>
            <br>

            <span class="badge badge-primary btn-eyes btn-detalle-pedido-transportista"
                  id="{{Hashids::encode(substr($item->id, -8))}}pedidotransportista"
                  data-id="{{Hashids::encode(substr($item->id, -8))}}"
                  style="margin-top: 4px;">
              <span class="mdi mdi-truck  md-trigger"> Transportista</span> 
            </span>




        </td>
        <td><b>{{number_format($item->total, 2, '.', ',')}}</b></td>

        <td class="cell-detail">

          <span class="cell-detail-description"><b>Total Producto : </b> {{$funcion->funciones->pedido_producto_total($item)}}</span>
          <span class="cell-detail-description"><b>Producto Registrados : </b> {{$funcion->funciones->pedido_producto_registrado($item)}}</span>
          <span class="cell-detail-description"><b>Producto Rechazados : </b> {{$funcion->funciones->pedido_producto_rechazado($item)}}</span>
        
          <span class="cell-detail-description"><b>Obsequio : </b> {{$funcion->funciones->pedido_producto_obsequio($item)}}</span>


        </td>


        <td>
          <span class="badge badge-primary btn-eyes btn-detalle-pedido"
                id="{{Hashids::encode(substr($item->id, -8))}}pedido"
                data-id="{{Hashids::encode(substr($item->id, -8))}}"
                data-json-detalle="{{$funcion->funciones->json_detalle_pedido($item->id)}}">
            <span class="mdi mdi-eye  md-trigger"></span>
          </span>
        </td>

      </tr>                    
    @endforeach

  </tbody>
</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif

