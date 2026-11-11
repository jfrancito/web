<div class="row detallecliente">
  <div class="col-sm-12 col-mobil-top">     
    <div class="panel panel-full">         
      <div class="panel-heading cell-detail">{{$pedido->empresa->NOM_EMPR}}  ({{$pedido->codigo}})         
        <span class="panel-subtitle cell-detail-description-producto">{{$pedido->empresa->NRO_DOCUMENTO}}</span>             
        <span class="panel-subtitle cell-detail-description-contrato">{{$contrato->CONTRATO}}</span>             
        <span class="panel-subtitle cell-detail-direccion-entrega">
          <strong>Fecha de entrega:</strong> 
          <small>{{date_format(date_create($pedido->fecha_despacho), 'd-m-Y')}}</small></span>             
          <span class="panel-subtitle cell-detail-direccion-entrega">
            <strong>Dirección de entrega:</strong> <small>{{$pedido->direccionentrega->NOM_DIRECCION}}</small></span>             
          <span class="panel-subtitle cell-detail-direccion-entrega">
            <strong>Condición de pago :</strong> <small>{{$pedido->condicionpago->NOM_CATEGORIA}}</small>
          </span>         
       </div>     
    </div> 
  </div>
</div>
<div class="row detalleproducto">

  @foreach($pedido->detallepedido as $item)

    @if($item->estado_id != 'EPP0000000000005') 
      <div class="col-sm-12 productoseleccion col-mobil-top" 
           data_ipr="{{Hashids::encode(substr($item->producto->COD_PRODUCTO, -8))}}" 
           data_ppr="PRD" 
           data_prpr="{{$item->precio}}" 
           data_ctpr="{{$item->cantidad}}" 
           data_obq="{{$item->ind_obsequio}}"
           data_upd="0"
           data_ipo="{{$item->ind_producto_obsequio}}"
           >

          <div class="panel panel-default panel-contrast">         
            <div class="panel-heading cell-detail">{{$item->producto->NOM_PRODUCTO}}
              <span class="txtobsequio"> 
                @if($item->ind_obsequio == '1') 
                  (Obsequio) 
                @endif
              </span>   
              <span class='txt-danger txtrelacion'>{{$funcion->funciones->etiqueta_obsequio($item)}}</span>                   
              <span class="panel-subtitle cell-detail-producto">Cantidad : 
                {{$item->cantidad}} 
                {{$funcion->funciones->data_categoria($item->producto->COD_CATEGORIA_UNIDAD_MEDIDA)->NOM_CATEGORIA}}</span>             
              <span class="panel-subtitle cell-detail-producto">Precio : S/. {{$item->precio}} <strong> Importe {{$item->total}} </strong></span>         
            </div>     
          </div>
      </div>
    @endif
  @endforeach

       
</div>

<div class='row-menu'>
  <div class='row'>
    <div class="col-sm-12 col-mobil-top">
      <div class="col-fr-2 col-atras">
        <span class="mdi mdi-arrow-left"></span>
      </div> 
      <div class="col-fr-10 col-total">
        <strong>Total : </strong> <strong class='total'> {{$pedido->total}}</strong>
      </div> 
    </div>
  </div>
</div>

<form method="POST" action="{{ url('/obsequio-orden-pedido/'.Hashids::encode(substr($pedido->id, -8))).'/'.$idopcion }}" class="form-horizontal group-border-dashed form-pedido">
  {{ csrf_field() }}

  <input type="hidden" name="pedido_id" id='pedido_id' value = '{{$pedido->id}}'>
  <input type="hidden" name="productos" id='productos'>
  <input type="hidden" name="cliente" id='cliente' value='{{$pedido->cliente_id}}'>
  <input type="hidden" name="cuenta" id='cuenta' value='{{$pedido->cuenta_id}}'>
  <input type="hidden" name="relacionadas" id='relacionadas'>

  <button type="submit" class="btn btn-space btn-success btn-big btn-guardar-obsequio">
    <i class="icon mdi mdi-check"></i> Guardar
  </button>

</form>

