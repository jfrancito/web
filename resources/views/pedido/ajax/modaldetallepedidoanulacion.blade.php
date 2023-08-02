<div class="modal-header">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title"><strong>{{$pedido->empresa->NOM_EMPR}} ({{$pedido->codigo}})</strong></h3>
  <h5 class="modal-title">{{$pedido->empresa->NRO_DOCUMENTO}} / {{$funcion->funciones->cuenta_cliente($pedido->cliente_id)}}</h5>
  <h5 class="modal-title"> Dirección entrega : {{$pedido->direccionentrega->NOM_DIRECCION}}</h5>
  <h5 class="modal-title"> Tipo de Pago : {{$funcion->funciones->data_categoria($pedido->tipopago_id)->NOM_CATEGORIA}}</h5>
  <h5 class="modal-title"> Glosa : {{$pedido->glosa}}</h5>

  <input type="hidden" name="id_pedido_modal" id="id_pedido_modal" value="{{$pedido_id}}">

</div>
<div class="modal-body">
	  <h5 class="modal-title" style="color: red;"> Mensaje : {{$mensaje}}</h5>

  <div class="scroll_text">


  	<table class="table listatabledetalle">
	    <thead>
	      <tr>

			    <th>Cantidad</th>
			    <th>Atendido</th>
		        <th>Producto</th>
		        <th>Estado</th>
		        <th>Obsequio</th>
		        <th>Precio</th>
		        <th>Importe</th>
	      </tr>
	    </thead>
	    <tbody>

	   @foreach($detalle_pedido as $item)

            @php
              	$key              		=   array_search($item->id, array_column($array_detalle_pedido, 'detalle_pedido_id'));
              	$checked       			=   $array_detalle_pedido[$key]->checked;
              	$empresa_id       		=   $array_detalle_pedido[$key]->empresa_id;
			  	$estado_id        		=   $array_detalle_pedido[$key]->estado_id;

			  	$disabled         		=   '';
			  	if($item->atendido > 0){$disabled  =   'disabled';}

			  	$empresa_receptora_id 	=   $empresa_id;
			  	if(trim($item->empresa_receptora_id) != ''){$empresa_receptora_id  =   $item->empresa_receptora_id;}
			  	$orden_obsequio_id 	=   '';
				if(trim($item->orden_referencia_obsequio_id) != ''){$orden_obsequio_id  =   $item->orden_referencia_obsequio_id;}

            @endphp



	      	<tr class='detalle_pedido'
	      		data_detalle_pedido_id = '{{$item->id}}'
	      		data_estado_id = '{{$item->estado_id}}'
	      		data_obsequio = '{{$item->ind_obsequio}}'
	      		data_cantidad = '{{$item->cantidad}}'
	      		data_atendido = '@if(empty($item->atendido)) 0.000 @else {{$item->atendido}} @endif'
	      		data_nombre = '{{$item->producto->NOM_PRODUCTO}}'
	      		>

		        <td><b>{{$item->cantidad}}</b></td>
		        <td><b>
		        	@if(empty($item->atendido))
		        		0.0000
		        	@else
		        		{{$item->atendido}}
			        @endif
		        </b></td>



		        <td>
		        	{{$item->producto->NOM_PRODUCTO}}
		        </td>

		        <td>

		            @if($item->estado_id == 'EPP0000000000003') 
		              <span class="badge badge-warning">{{$item->NOM_CATEGORIA}}</span> 
		            @else
			            @if($item->estado_id == 'EPP0000000000004') 
			              <span class="badge badge-success">{{$item->NOM_CATEGORIA}}</span> 
			            @else


				            @if($item->estado_id == 'EPP0000000000006') 
				              <span class="badge badge-success">{{$item->NOM_CATEGORIA}}</span> 
				            @else
				                <span class="badge badge-danger">{{$item->NOM_CATEGORIA}}</span>
				            @endif



			            @endif

		            @endif
		        </td>
		        <td>
		     		@if($item->ind_obsequio == 1)
			         	<span class="badge badge-danger">OBSEQUIO</span>
			        @endif
		        </td>

		        <td>{{$item->precio}}</td>
		        <td>{{$item->total}}</td>






	      	</tr>                    
	    @endforeach

	    </tbody>
	    <tfooter>
	      <tr>
		    <th colspan="6"></th>
	        <th>{{$pedido->total}}</th>
	        <th></th>
	      </tr>
	    </tfooter>
  	</table>
  </div>

</div>


<script>

  $('.dinero').inputmask({ 'alias': 'numeric', 
  'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
  'digitsOptional': false, 
  'prefix': '', 
  'placeholder': '0'});

</script>