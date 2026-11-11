<div class="modal-header">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title"><strong>{{$pedido->empresa->NOM_EMPR}}</strong></h3>
  <h5 class="modal-title">{{$pedido->empresa->NRO_DOCUMENTO}} / {{$funcion->funciones->cuenta_cliente($pedido->cliente_id)}}</h5>
  <h5 class="modal-title"> Dirección entrega : {{$pedido->direccionentrega->NOM_DIRECCION}}</h5>
  <h5 class="modal-title"> Tipo de Pago : {{$funcion->funciones->data_categoria($pedido->tipopago_id)->NOM_CATEGORIA}}</h5>
  <h5 class="modal-title"> Glosa : {{$pedido->glosa}}</h5>

</div>
<div class="modal-body">
  <div class="scroll_text">


  	<table class="table listatabledetalletransportista">
	    <thead>
	      <tr>
	      	<th>
	      	</th>
		    <th>Cantidad</th>
	        <th>Producto</th>
	        <th>Precio</th>
	        <th>Importe</th>
	        <th>Orden venta</th>
	      </tr>
	    </thead>
	    <tbody>

	   @foreach($detalle_pedido as $item)


	      	<tr class='detalle_pedido_transportista'
	      		data_detalle_pedido_id = '{{$item->id}}'
	      		data_cantidad = '{{$item->cantidad}}'
	      		data_precio = '{{$item->precio}}'
	      		data_nombre_producto = '{{$item->producto_id}}'
	      		data_nombre_total = '{{$item->total}}'
	      		data_obsequio = '{{$item->ind_obsequio}}'
	      		>
		        <td>  

		          <div class="text-center be-checkbox be-checkbox-sm">
		            <input  type="checkbox"
		                    class="input_check" 
		                    id="{{Hashids::encode(substr($item->id, -8))}}trans" 
		                    checked = 'checked'>

		            <label  for="{{Hashids::encode(substr($item->id, -8))}}trans"
		                  data-atr = "ver"
		                  class = "checkbox"                    
		                  name="{{Hashids::encode(substr($item->id, -8))}}trans"
		            ></label>
		          </div>
		        </td>

		        <td>
					<input type="text"  
		                   id="cantidad" 
		                   name="cantidad"
		                   value='{{$item->cantidad}}'
		                   class="form-control input-sm dinero"
							                   >
		        	
		        </td>

		        <td>
		        	{{$item->producto->NOM_PRODUCTO}}
		        	@if($item->ind_obsequio == 1)
		        		<br>
			         	<span class="badge badge-danger">OBSEQUIO</span>
			        @endif
			        <br>
		            @if($item->estado_id == 'EPP0000000000003') 
		              <span class="badge badge-warning">{{$item->NOM_CATEGORIA}}</span> 
		            @else
			            @if($item->estado_id == 'EPP0000000000004') 
			              <span class="badge badge-success">{{$item->NOM_CATEGORIA}}</span> 
			            @else
			                <span class="badge badge-danger">{{$item->NOM_CATEGORIA}}</span>
			            @endif

		            @endif
		        </td>
		        <td>{{$item->precio}}</td>
		        <td>{{$item->total}}</td>
		        <td>{{$item->orden_id}}</td>

	      	</tr>                    
	    @endforeach

	    </tbody>
	    <tfooter>
	      <tr>
		    <th colspan="4"></th>
	        <th>{{$pedido->total}}</th>
	        <th></th>
	      </tr>
	    </tfooter>
  	</table>
  </div>

</div>
<div class="modal-footer">

  <a 	href="{{ url('/imprimir-pedido-transportista/'.Hashids::encode(substr($pedido->id, -8))).'/cp' }}" 
  		target="_blank"
  		data_href = "{{ url('/imprimir-pedido-transportista/'.Hashids::encode(substr($pedido->id, -8))).'/cp/' }}" 
  		class="btn btn-success transportitas_reporte">Con precio</a>


  <a 	href="{{ url('/imprimir-pedido-transportista/'.Hashids::encode(substr($pedido->id, -8))).'/sp' }}" 
  		target="_blank"
  		data_href = "{{ url('/imprimir-pedido-transportista/'.Hashids::encode(substr($pedido->id, -8))).'/sp/' }}" 
  		class="btn btn-success transportitas_reporte">Sin precio</a>


</div>

<input type="hidden" name="array_detalle_producto_transportista" id='array_detalle_producto_transportista' value='[]'>

<script>
  $('.dinero').inputmask({ 'alias': 'numeric', 
  'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
  'digitsOptional': false, 
  'prefix': '', 
  'placeholder': '0'});
</script>