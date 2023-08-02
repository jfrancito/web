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
  <div class="scroll_text">


  	<table class="table listatabledetalle">
	    <thead>
	      <tr>
		      	<th>
		      	</th>
			    <th>Cantidad</th>
			    <th>Atendido</th>
			    <th>Atender</th>
		        <th>Producto</th>
		        <th>Precio</th>
		        <th>Importe</th>
		        <th>Empresa Recepción</th>
				<th style="display: none;">Venta Asociada</th>
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
	      		data_ind_producto_obsequio = '{{$item->ind_producto_obsequio}}'
	      		>
		        <td>  

		          <div class="text-center be-checkbox be-checkbox-sm">
		            <input  type="checkbox"
		                    class="{{Hashids::encode(substr($item->id, -8))}}p input_check_pe" 
		                    id="{{Hashids::encode(substr($item->id, -8))}}p" 
		                    {{$checked}}
		                    @if($estado_id == 'EPP0000000000004' or $estado_id == 'EPP0000000000005' ) disabled @endif>

		            <label  for="{{Hashids::encode(substr($item->id, -8))}}p"
		                  data-atr = "ver"
		                  class = "checkbox"                    
		                  name="{{Hashids::encode(substr($item->id, -8))}}p"
		            ></label>
		          </div>
		        </td>

		        <td><b>{{$item->cantidad}}</b></td>
		        <td><b>
		        	@if(empty($item->atendido))
		        		0.0000
		        	@else
		        		{{$item->atendido}}
			        @endif
		        </b></td>
		        <td>
		            <input type="text"  
		                   id="atender" 
		                   name="atender"
		                   value='{{$item->cantidad - $item->atendido}}'
		                   class="form-control input-sm dinero"
		                   >
		        </td>


		        <td>
		        	{{$item->producto->NOM_PRODUCTO}} <span class='txt-danger'>{{$funcion->funciones->etiqueta_obsequio($item)}}</span>
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


				            @if($item->estado_id == 'EPP0000000000006') 
				              <span class="badge badge-success">{{$item->NOM_CATEGORIA}}</span> 
				            @else
				                <span class="badge badge-danger">{{$item->NOM_CATEGORIA}}</span>
				            @endif



			            @endif

		            @endif


		        </td>

		        <td>{{$item->precio}}</td>
		        <td>{{$item->total}}</td>
		        <td width="250px" class='select_empresa'>
		        	<div class="form-group">
                        <div class="col-sm-12 abajocaja" >

                          {!! Form::select( 'empresa_id', $comboempresas, array($empresa_receptora_id),
                                            [
                                              'class'       => 'select2 form-control control input-sm' ,
                                              'id'          => 'empresa_id',
                                              'required'    => '',
                                              $disabled,
                                              'data-aw'     => '1',
                                            ]) !!}

                        </div>
                    </div>
		        </td>

		        <td width="250px" class='select_orden_detalle_pedido_id' style="display: none;">
		        	<div class="form-group">
                        <div class="col-sm-12 abajocaja" >

			        	@if($item->ind_obsequio == 1)
                          	{!! Form::select( 'orden_detalle_pedido_id', $comboorden_detalle, array($orden_obsequio_id),
                                            [
                                              'class'       => 'select2 form-control control input-sm' ,
                                              'id'          => 'orden_detalle_pedido_id',
                                              'required'    => '',
                                              $disabled,
                                              'data-aw'     => '1',
                                            ]) !!}
				        @endif

 

                        </div>
                    </div>
		        </td>



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
  <button type="button" data-dismiss="modal" class="btn btn-success btn_guardar_detalle">Enviar pedido al osiris</button>
</div>

<script>

  $('.dinero').inputmask({ 'alias': 'numeric', 
  'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
  'digitsOptional': false, 
  'prefix': '', 
  'placeholder': '0'});

</script>