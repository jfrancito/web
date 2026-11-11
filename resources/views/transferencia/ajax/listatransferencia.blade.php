<table id="tablepedido" class="table table-striped table-hover table-fw-widget dt-responsive nowrap" style='width: 100%;'>
  <thead>
    <tr> 
      <th>Codigo</th>
      <th>Estado</th>
      <th>Empresa (registro pedido)</th>
      <th>Fecha Pedido</th>
      <th>Fecha Entrega</th>
      <th>Hora Entrega</th>
      <th>Peso (Tn.)</th>
      <th>Cliente Opc.</th>
      <th>Destino</th>
      <th>Observación</th>
      <th>Opción</th>
    </tr>
  </thead>
  <tbody>
   @foreach($listapedidos as $item)
      <tr>
          <td class="cell-detail">
            <span>{{$item->codigo}}</span>
          </td>
        <td>

          @if($item->estado_id == 'EPP0000000000003') 
              <span class="badge badge-warning">{{$item->estado_nom}}</span> 
          @else
            @if($item->estado_id == 'EPP0000000000002') 
                <span class="badge badge-primary">{{$item->estado_nom}}</span>
            @else
              @if($item->estado_id == 'EPP0000000000004') 
                  <span class="badge badge-success">{{$item->estado_nom}}</span>
              @else
                @if($item->estado_id == 'EPP0000000000007') 
                    <span class="badge badge-info">{{$item->estado_nom}}</span>
                @else
                    <span class="badge badge-danger">{{$item->estado_nom}}</span>
                @endif    
              @endif
            @endif
          @endif

        </td>
        <td>{{$funcion->funciones->data_empresa($item->empresa_id)->NOM_EMPR}}</td>        
        <td>{{date_format(date_create($item->fecha_pedido), 'd-m-Y')}}</td>
        <td>{{date_format(date_create($item->fecha_entrega), 'd-m-Y')}}</td>             
        <td>{{$item->hora_entrega}}</td>
        <td>{{$item->peso_total/1000}}</td>
        <td>{{$item->cliente_nom}}</td>
        <td>{{$item->destino}}</td>
        <td>{{$item->observacion}}</td>
        <td class="rigth">
            <div class="btn-group btn-hspace">
              <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
              <ul role="menu" class="dropdown-menu pull-right">

                @if($opcion->parametros=='Cerrar')
                <li>
                  <a href="{{ url('/agregar-transferencia/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                    Modificar Transferencia
                  </a>                                  
                </li>
                <li>
                  <a href="#" class="btn-detalle-pedido-mobil" 
                    id="{{Hashids::encode(substr($item->id, -8))}}pedido"
                    data-id="{{Hashids::encode(substr($item->id, -8))}}"
                    id_opcion="{{$idopcion}}"
                    data-json-detalle="{{$funcion->funciones->json_detalle_pedido($item->id)}}"
                    m_accion='DELETE'>
                    Eliminar Transferencia
                  </a>                                  
                </li>
                @endif
                <li>
                  <a href="#" class="btn-detalle-pedido-mobil" 
                    id="{{Hashids::encode(substr($item->id, -8))}}pedido"
                    data-id="{{Hashids::encode(substr($item->id, -8))}}"
                    id_opcion="{{$idopcion}}"
                    data-json-detalle="{{$funcion->funciones->json_detalle_pedido($item->id)}}">
                    {{$opcion->parametros}} Transferencia
                  </a>                                  
                </li>
                @if($opcion->parametros<>'Cerrar')
                <li>
                  <a href="#" class="btn-detalle-pedido-mobil" 
                    id="{{Hashids::encode(substr($item->id, -8))}}pedido"
                    data-id="{{Hashids::encode(substr($item->id, -8))}}"
                    id_opcion="{{$idopcion}}"
                    data-json-detalle="{{$funcion->funciones->json_detalle_pedido($item->id)}}"
                    m_accion='DECLINE'>
                    Rechazar Transferencia
                  </a>                                  
                </li>
                @endif
                  <li>
                    <a href="{{ url('/imprimir-solicitud-transferencia/'.Hashids::encode(substr($item->id, -8))) }}" target="_blank">
                      Ver Transferencia
                    </a>                                  
                </li>
              </ul>
            </div>
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
