<div class="row detallecliente">
  <!-- DATOS DEL CLIENTE -->
  @if($idtransferencia!='x')
      <div class='col-sm-12 col-mobil-top'>
        <div class='panel panel-full'>
          <div class='panel-heading cell-detail'>              {{Session::get('empresas')->NOM_EMPR}}
            <span class='panel-subtitle cell-detail-description-producto'> {{Session::get('empresas')->COD_EMPR}}</span>
            <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Fecha de Pedido:</strong> <small>{{$transferencia->fecha_pedido}}</small></span>
            <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Fecha de Entrega:</strong> <small>{{$transferencia->fecha_entrega}}</small></span>
            <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Hora de Entrega :</strong> <small>{{$transferencia->hora_entrega}}</small></span>
            <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Centro Origen :</strong> <small>{{$transferencia->centro_origen}}</small></span>
            <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Destino :</strong> <small>{{$transferencia->destino}}</small></span>
            <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Cliente Referencial :</strong> <small>{{$transferencia->cliente_nom}}</small></span>
            <input type="hidden" name="iidtransferencia" id="iidtransferencia" value="{{$idtransferencia}}">
          </div>
        </div>
      </div>
  @endif
</div>

<div class="row detalleproducto">
  @if($idtransferencia!='x')
    @foreach($transferenciadetalle as $item)    
        <div class='col-sm-12 productoseleccion col-mobil-top'
              data_ipr ='{{Hashids::encode(substr($item->producto_id, -13))}}'
              data_ppr ='{{substr($item->producto_id, 0, 3)}}'
              data_npr ='{{$item->producto_nombre}}' 
              data_pqpr='{{$item->paquete}}' 
              data_ctpr='{{$item->cantidad}}' 
              data_pepr='{{$item->producto_peso}}' 
              data_upd=''>
           <div class='panel panel-default panel-contrast'>
              <div class='panel-heading cell-detail detalle-trans'>
                     {{$item->producto_nombre}}
                <div class='tools'>
                   <span class='icon mdi mdi-close mdi-close-pedido'></span>
                </div>
                <span class='panel-subtitle cell-detail-producto'>Cantidad : {{$item->cantidad}}</span>
                <span class='panel-subtitle cell-detail-producto'>Paquetes : {{$item->paquete}}</span>
                <span class='panel-subtitle cell-detail-producto'>Peso Total : {{$item->peso_total}} <strong> Peso Total {{$item->peso_total}} </strong></span>
               </div>
           </div>
      </div>
    @endforeach
  @endif       
</div>

<div class="col-xs-12" style="margin-top: 25px !important;" >

      <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Observación </label>
              <div class="col-xs-12 abajocaja">
               <textarea id="iobs" class="form-control" rows="5" value="{{$transferencia->observacion}}" ></textarea>
              </div>
      </div>
</div>
<div class='row-menu'>
  <div class='row'>
    <div class="col-sm-12 col-mobil-top">

      <div class="col-fr-2 col-atras">
        <span class="mdi mdi-arrow-left"></span>
      </div> 

      <div class="col-fr-8 col-total">
        <strong>Total : </strong> <strong class='total total-pedido'> 0.00</strong>
      </div> 
    </div>
  </div>
</div>



<form method="POST" action="{{ url('/agregar-transferencia/'.$idopcion.'/'.$idtransferencia) }}" class="form-horizontal group-border-dashed form-pedido" id = 'formpedido'>
  {{ csrf_field() }}

  <input type="hidden" name="cod_empr" id='cod_empr' value='{{$transferencia->empresa_id}}'>
  <input type="hidden" name="nom_empr" id='nom_empr'>
  <input type="hidden" name="fecha_pedido" id='fecha_pedido' value='{{$transferencia->fecha_pedido}}'>
  <input type="hidden" name="fecha_entrega" id='fecha_entrega' value='{{$transferencia->fecha_entrega}}'>
  <input type="hidden" name="hora_entrega" id='hora_entrega' value='{{$transferencia->hora_entrega}}'>
  <input type="hidden" name="peso_total" id='peso_total' value='{{$transferencia->peso_total}}'>
  <input type="hidden" name="centro_origen" id='centro_origen' value='{{$transferencia->centro_origen_id}}'>
  <input type="hidden" name="almacen_destino" id='almacen_destino' value='{{$transferencia->almacen_destino_id}}'>
  <input type="hidden" name="cliente_op" id='cliente_op' value='{{$transferencia->cliente_id}}'>
  <input type="hidden" name="productos" id='productos'>
  <input type="hidden" name="obs" id='obs' value='{{$transferencia->observacion}}'>
  <input type="hidden" name="idtrans" id='idtrans' value='{{$transferencia->id}}'>

  <button type="button" class="btn btn-space btn-success btn-big btn-guardar">
    <i class="icon mdi mdi-check"></i> Guardar
  </button>

</form>


@if(isset($ajax))

  <script type="text/javascript">
    $(document).ready(function(){
    
        $('.formato_numero').inputmask({ 'alias': 'numeric', 
        'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
        'digitsOptional': false, 
        'prefix': '', 
        'placeholder': '0'});       
    });
  </script> 
@endif