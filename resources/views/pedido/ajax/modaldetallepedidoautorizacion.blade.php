<div class="modal-header">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title"><strong>{{$pedido->empresa->NOM_EMPR}}</strong></h3>
  <h5 class="modal-title">{{$pedido->empresa->NRO_DOCUMENTO}} / {{$funcion->funciones->cuenta_cliente($pedido->cliente_id)}}</h5>

  <h5 class="modal-title"> Dirección entrega : {{$pedido->direccionentrega->NOM_DIRECCION}}</h5>
  <h5 class="modal-title"> Glosa : {{$pedido->glosa}}</h5>
</div>
<div class="modal-body">

  	<table class="table lista_detalle_pedido">
	    <thead>
	      <tr>
	        <th>Producto</th>
	        <th>Estado</th>
		    <th>Cantidad</th>
	        <th>Precio</th>
	        <th>Importe</th>
      		<th>Rechazar</th>
	      </tr>
	    </thead>
	    <tbody>

	   @foreach($pedido->detallepedido as $item)


   			@php $disabled  =   "disabled"; @endphp
            @if(is_null($item->estado_id) or $item->estado_id == '' or $item->estado_id == 'EPP0000000000002')
            	@php $disabled  =   ""; @endphp
            @endif
		    <tr 	class= 'fila_producto'
		      		data_detallepedido = "{{$item->id}}" 
		      		data_pedido = "{{$item->pedido_id}}"
		      		data_ind_producto_obsequio = "{{$item->ind_producto_obsequio}}"
		      		data_ind_obsequio = "{{$item->ind_obsequio}}"
		      		dis
		      		>
		        <td>{{$item->producto->NOM_PRODUCTO}}

		        	@if($item->ind_obsequio == 1)
			         <span class="badge badge-danger">OBSEQUIO </span>
			        @endif
			        <span class='txt-danger'>{{$funcion->funciones->etiqueta_obsequio($item)}}</span>
		        </td>

		        <td class='estado_detalle_pedido'>

		            @if($item->estado_id == 'EPP0000000000003') 
		              	<span class="badge badge-warning"> 
		              		{{$funcion->funciones->data_categoria($item->estado_id)->NOM_CATEGORIA}}
						</span> 
		            @else
			            @if($item->estado_id == 'EPP0000000000005') 
			              <span class="badge badge-danger">
			              	{{$funcion->funciones->data_categoria($item->estado_id)->NOM_CATEGORIA}}
			              </span> 
			            @else
				            @if($item->estado_id == 'EPP0000000000004') 
				              <span class="badge badge-success">{{$funcion->funciones->data_categoria($item->estado_id)->NOM_CATEGORIA}}
				              </span> 
				            @else
				                <span class="badge badge-primary">GENERADO</span>
				            @endif
			            @endif
		            @endif
		        </td>


		        <td class= 'columna-cantidad'>

		            <input type="text"  
		                   id="cantidad" 
		                   name="cantidad"
		                   value='{{$item->cantidad}}'
		                   class="form-control input-sm dinero updatecantidad"
		                   {{$disabled}}
		                   >

		        </td>
		        <td class= 'columna-precio'>

		            <input type="text"  
		                   id="precio" 
		                   name="precio"
		                   value='{{$item->precio}}'
		                   class="form-control input-sm dinero updateprice"
		                   {{$disabled}}
		                   >

		        	
		        </td>
		        <td class= 'columna-importe'>
		        	{{$item->total}}
		        </td>

		        <td class = 'rechazar'>

		            @if(is_null($item->estado_id) or $item->estado_id == '' or $item->estado_id == 'EPP0000000000002') 
				        <span class="badge badge-danger btn-eyes btn-detalle-pedido-rechazar" 
				                data-id="{{$item->id}}"
				                data_ind_producto_obsequio = "{{$item->ind_producto_obsequio}}"
		      					data_ind_obsequio = "{{$item->ind_obsequio}}"
				                >
				            <span style='color:#fff' class="mdi mdi-close md-trigger"></span>
				        </span>
		            @endif

		        </td>

		      </tr>                    
	    @endforeach

	    </tbody>
	    <tfooter>
	      <tr>
		    <th colspan="4"></th>
	        <th class='p{{$pedido->id}}'>{{$pedido->total}}</th>
	      </tr>
	    </tfooter>
  	</table>


</div>
<div class="modal-footer">
  <button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cancelar</button>
</div>

<script>

  $('.dinero').inputmask({ 'alias': 'numeric', 
  'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
  'digitsOptional': false, 
  'prefix': '', 
  'placeholder': '0'});

</script>