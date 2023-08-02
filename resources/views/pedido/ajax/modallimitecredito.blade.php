<div class="modal-header">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title"><strong>{{$pedido->empresa->NOM_EMPR}}</strong></h3>
  <h5 class="modal-title">{{$pedido->empresa->NRO_DOCUMENTO}} / {{$funcion->funciones->cuenta_cliente($pedido->cliente_id)}}</h5>
  <h5 class="modal-title"> Dirección entrega : {{$pedido->direccionentrega->NOM_DIRECCION}}</h5>
  <h5 class="modal-title"> Tipo de Pago : {{$funcion->funciones->data_categoria($pedido->tipopago_id)->NOM_CATEGORIA}}</h5>
  <h5 class="modal-title"> Glosa : {{$pedido->glosa}}</h5>
  <h5 class="modal-title"> Limite de credito : 
    @if(count($limite_credito)>0) 
        {{number_format($limite_credito->canlimitecredito, 2, '.', ',')}}
    @else
        -    
    @endif
  </h5>
  <input type="hidden" name="id_pedido_modal" id="id_pedido_modal" value="{{$pedido_id}}">

</div>
<div class="modal-body">
  <div class="scroll_text">


    <table class="table" style="font-size: 1em;">
        <thead>
          <tr>
            <th colspan="2" style="text-align: center;">Deuda</th>
            <th colspan="2" style="text-align: center;">Posible deuda</th>
          </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>Limite de credito</b></td>
                <td>{{number_format($l_c, 2, '.', ',')}}</td>
                <td><b>Limite de credito</b></td>
                <td>{{number_format($l_c, 2, '.', ',')}}</td>
            </tr> 
            <tr>
                <td><b>Osiris</b></td>
                <td>{{number_format($deuda_osiris, 2, '.', ',')}}</td>
                <td><b>Osiris</b></td>
                <td>{{number_format($deuda_osiris, 2, '.', ',')}}</td>
            </tr> 
            <tr>
                <td><b>Diferencia</b></td>
                <td><b>{{number_format($l_c - $deuda_osiris, 2, '.', ',')}}</b></td>
                <td><b>Oryza (Pedido autorizados)</b></td>
                <td>{{number_format($deuda_osyza, 2, '.', ',')}}</td>
            </tr> 
            <tr>
                <td><b></b></td>
                <td></td>
                <td><b>Pedido seleccionado ({{$pedido->codigo}})</b></td>
                <td>{{number_format($pedido->total, 2, '.', ',')}}</td>
            </tr> 
            <tr>
                <td><b></b></td>
                <td></td>
                <td><b>Diferencia</b></td>
                <td><b>{{number_format($l_c - $suma_posible, 2, '.', ',')}}<b></td>
            </tr> 

        </tbody>
    </table>



  </div>

</div>
<div class="modal-footer">
  <button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cancelar</button>
</div>