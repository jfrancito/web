<table id="tablepedido" class="table table-striped table-hover table-fw-widget dt-responsive nowrap" style='width: 100%;'>
  <thead>
    <tr> 
      <th>Codigo</th>
      <th>Estado</th>
      <th>Cliente</th>
      <th>Fecha Venta</th>
      <th>Fecha Despacho</th>
      <th>Igv</th>
      <th>Subtotal</th>
      <th>Total</th>
      <th>Empresa (registro pedido)</th>
      <th>Orden Cen</th>
      <th>Detalle</th>
      <th>Obsequio</th>
      <th>Seguimiento</th>
    </tr>
  </thead>
  <tbody>
   @foreach($listapedidos as $item)
      <tr>
          <td class="cell-detail">
            <span><?php echo wordwrap($item->empresa->NOM_EMPR,20,"<br>\n"); ?></span> 
            <span>{{$item->codigo}}</span>
          </td>
        <td>
          @if($item->COD_CATEGORIA == 'EPP0000000000003') 
            @if($funcion->funciones->pedido_producto_registrado($item) == '0') 
              <span class="badge badge-warning">{{$item->NOM_CATEGORIA}}</span> 
            @else
                <span class="badge badge-success">PARCIALMENTE ATENDIDA</span> 
            @endif
          @else
            @if($item->COD_CATEGORIA == 'EPP0000000000002') 
                <span class="badge badge-primary">{{$item->NOM_CATEGORIA}}</span>
            @else
              @if($item->COD_CATEGORIA == 'EPP0000000000004') 
                  <span class="badge badge-success">{{$item->NOM_CATEGORIA}}</span>
              @else
                  <span class="badge badge-danger">{{$item->NOM_CATEGORIA}}</span>
              @endif
            @endif
          @endif
        </td>
        <td> 
          {{$item->empresa->NOM_EMPR}}
        </td>
        <td>{{date_format(date_create($item->fecha_time_venta), 'd-m-Y H:i')}}</td>
        <td>{{date_format(date_create($item->fecha_despacho), 'd-m-Y')}}</td>
       
        <!-- <td>{{$item->empresa->NRO_DOCUMENTO}}</td> -->
      
        <td>{{$item->igv}}</td>
        <td>{{$item->subtotal}}</td>
        <td>{{$item->total}}</td>
        <td>{{$funcion->funciones->data_empresa($item->empresa_id)->NOM_EMPR}}</td>
        <td>{{$item->nro_orden_cen}}</td>
        <td>
            <span class="badge badge-primary btn-eyes btn-detalle-pedido-mobil" 
                  data-id="{{Hashids::encode(substr($item->id, -8))}}">
              ver detalle
            </span>
        </td>
        <td>
          <a href="{{ url('/obsequio-orden-pedido/'.Hashids::encode(substr($item->id, -8))).'/'.$idopcion }}" class="tooltipcss opciones">
            <span class="badge badge-primary btn-eyes">
              agregar obsequios
            </span>
          </a>
        </td>

        <td>
            <span class="badge badge-primary btn-eyes btn-seguimiento-pedido-mobil" 
                  data-id="{{Hashids::encode(substr($item->id, -8))}}">
              Seguimiento Pedido
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
