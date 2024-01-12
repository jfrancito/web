<div class="modal-header">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title"><strong>Código ({{$picking->codigo}}) </strong></h3>
  <h5 class="modal-title"> Fecha Picking : {{$picking->fecha_picking}}</h5>
  <h5 class="modal-title"> Centro Origen : {{$picking->centro_origen}}</h5>
  <!-- @DPZ3 -->
  <h5 class="modal-title"> Cantidad Palets : {{number_format($picking->palets,4,'.',',')}} <span>(Peso: {{$palets_peso}} Kg.)</span></h5>
  <h5 class="modal-title"> Estado 		 : {{$picking->estado_nom}}</h5>
  <input type="hidden" name="id_picking_modal" id="id_picking_modal" value="{{$picking->id}}">
  <input type="hidden" name="pk_estado_id" id="pk_estado_id" value="{{$picking->estado_id}}">  
  <input type="hidden" name="id_opcion" id="id_opcion" value="{{$idopcion}}">
  <input type="hidden" name="m_accion" id="m_accion" value="{{$m_accion}}">
</div>

<div class="modal-body">
  <div class="scroll_text">

  	<table class="table listatabledetalle">
	    <thead>
	      <tr>
		    <th class='center'>Tipo</th>
		    <th class='center'>Código</th>
            <th>Cliente</th>
            <th>Producto</th>                   
            <th class='center'>Atender</th>    
            <th class='center'>Excedente</th>
            <th>Peso</th>
            <th>PQT</th>                
            <th>Fecha Entrega</th>
            <th>Destino</th>
            <th>Dirección Cliente</th>
	      </tr>
	    </thead>
			@php
				$total_peso = 0;
			@endphp
	    <tbody>
	    	@foreach($pickingdetalle as $item)			
					@php
                      $total_peso =  $total_peso + $item['peso_total'] ;
                    @endphp
	             <tr class='fila_pedido'>

	             <td class='center'>
	                  <b style="padding-right: 4px;">{{$item['tipo_operacion']}}</b>
	              </td>

	              <td class='center'>
	                  <b style="padding-right: 4px;">{{$item['transferencia_id']}}</b>
	              </td>

	              <td class="cell-detail relative"> 
	                  <span>{{$item['cliente']}}</span>
	              </td>

	              <td class="cell-detail relative" rowspan = "" > 
	                  <span>{{$item['producto_nombre']}}</span>
	                  <span class="cell-detail-description-producto">
	                  </span>
	              </td>
	                      
	              <td > {{number_format($item['cantidad'],4,'.',',')}} </td>

				  <td > {{number_format($item['cantidad_excedente'],4,'.',',')}} </td>

	              <td class='center'>{{number_format($item['peso_total'],4,'.',',')}}</td>
	             
	              <td class='center'>{{number_format($item['paquete'],4,'.',',')}}</td>

	              <td class="cell-detail"><span>{{$item['fecha_entrega']}}</span></td>

	              <td class="cell-detail">
	                  <span><b>Departamento</b> : {{$item['departamento_nom']}}</span>
	                  <span><b>Provincia</b> : {{$item['provincia_nom']}}</span>
	                  <span><b>Distrito</b> : {{$item['distrito_nom']}}</span>
	              </td>

	              <td><span>{{$item['direccion']}}</span></td>
	            </tr>

            @endforeach

	    </tbody>
	   
		<tfooter>
			<tr>
				<!-- @DPZ3 -->
				@php
                    $total_peso =  $total_peso + ($palets_peso * $picking->palets);
                @endphp
				<th colspan="5"></th>
				<th style='text-align: right'> Total Kg.:</th>
				<th class='total_peso_t'>{{ number_format($total_peso,4,'.',',') }}</th>
				<th colspan="4"></th>
			</tr>
		</tfooter>
  	</table>
  </div>
</div>

<div class="modal-footer">
  <button type="button" data-dismiss="modal" class="btn btn-success btn_guardar_detalle_picking">
  {{$titulo_boton}}</button>
</div>

<script>

  $('.dinero').inputmask({ 'alias': 'numeric', 
  'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
  'digitsOptional': false, 
  'prefix': '', 
  'placeholder': '0'});

</script>