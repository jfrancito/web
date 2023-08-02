<div class="modal-header">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title"><strong>{{$transferencia->empresa_id}} ({{$transferencia->codigo}}) </strong></h3>
  <h5 class="modal-title"> Fecha Pedido : {{$transferencia->fecha_pedido}}</h5>
  <h5 class="modal-title"> Fecha Entrega : {{$transferencia->fecha_entrega}}</h5>
  <h5 class="modal-title"> Hora Entrega : {{$transferencia->hora_entrega}}</h5>
  <h5 class="modal-title"> Centro Origen : {{$transferencia->centro_origen}}</h5>
  <h5 class="modal-title"> Destino : {{$transferencia->destino}}</h5>
  <h5 class="modal-title"> Cliente Opc. : {{$transferencia->cliente_nom}}</h5>
  <h5 class="modal-title"> Observación : {{$transferencia->observacion}}</h5>
  <input type="hidden" name="id_transferencia_modal" id="id_transferencia_modal" value="{{$transferencia->id}}">
  <input type="hidden" name="accion" id="accion" value="{{$opcion->parametros}}">
  <input type="hidden" name="estado_id" id="estado_id" value="{{$transferencia->estado_id}}">  
  <input type="hidden" name="id_opcion" id="id_opcion" value="{{$idopcion}}">
  <input type="hidden" name="m_accion" id="m_accion" value="{{$m_accion}}">
</div>

<div class="modal-body">
  <div class="scroll_text">

  	@php
		$total = 0;
	@endphp

  	<table class="table listatabledetalle">
	    <thead>
	      <tr>
		      	<th></th>
			    <th>Producto</th>
			    <th>Cantidad</th>		        
			    <th>Peso</th>		        
			    <th>Peso Total </th>		
			    <th>N° Paquetes</th>		
	      </tr>
	    </thead>
	    <tbody>

	   @foreach($transferencia_detalle as $item)    
	      	<tr>
		        <td> </td>
		        <td>{{$item->producto_nombre}} <span class='txt-danger'></td>
		        <td><b class='formato_numero'>{{$item->cantidad}}</b></td>
		        <td><b class='formato_numero'>{{$item->producto_peso}}</b></td>
		        <td><b class='formato_numero'>{{$item->peso_total}}</b></td>
		        <td><b class='formato_numero'>{{$item->paquete}}</b></td>
				@php
					$total = $total + ($item->peso_total);
				@endphp
	      	</tr>                    
	    @endforeach
	
	    </tbody>
	    <tfooter>
		<tr>
		    <th></th>
			<th></th>
			<th></th>
			<th>Total Kg.</th>
	        <th><b class='formato_numero'>{{$total}}</b></th>
	        <th></th>
	      </tr>
	    </tfooter>
  	</table>
  </div>
</div>

<div class="modal-footer">
  <button type="button" data-dismiss="modal" class="btn btn-success btn_guardar_detalle">
  {{$titulo_boton}} transferencia</button>
</div>

<script>

  $('.formato_numero').inputmask({ 'alias': 'numeric', 
	'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
	'digitsOptional': false, 
	'prefix': '', 
	'placeholder': '0'});

</script>