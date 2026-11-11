<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			<b>CLIENTE :</b>  {{$cliente->NOM_EMPR}}
		</h5>
	</div>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			<b>LIMITE CREDITO :</b> {{number_format($limite_credito, 2, '.', ',')}}
		</h5>
	</div>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			<b>DEUDA GENERAL:</b> {{number_format($deuda_cliente_general, 2, '.', ',')}}
		</h5>
	</div>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			<b>DEUDA VENCIDA:</b> {{number_format($deuda_cliente_vencida, 2, '.', ',')}}
		</h5>
	</div>

</div>
<div class="modal-body">
	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 

	<div style="width: 2500px">
	<table class="table table-condensed table-striped">
	    <thead>
	      <tr>
	      	<th>OREDN VENTA</th>

	        <th>DIAS PARA REALIZAR PAGO</th>
	        <th>DIAS TRANSCURRIDOS HASTA LA FECHA ACTUAL</th>


	      	<th>EMPRESA</th>
	        <th>CLIENTE</th>

	        <th>RESPONSABLE</th>
	        <th>DOCUMENTO ASOCIADO</th>
	        <th>FECHA EMISION</th>
	        <th>FECHA VENCIMIENTO</th>
	        <th>CARGO</th>
	        <th>ABONO</th>
	        <th>CARTA</th>
	        <th>CAN SALDO</th>
	        <th>FECHA CORTE</th>

	      </tr>
	    </thead>
	    <tbody>
	    @foreach($lista_deuda as $index => $item)

	    	@php $backgraund  =   ''; @endphp
      	   	@if($item['DIAS_MOROSO'] > 0)
      	   		@php $backgraund  =   'background_rojo-fila-tr'; @endphp
      	   	@endif

	      	<tr class='{{$backgraund}}'>
	      	   <td>{{$item['COD_ORDEN']}}</td>

			   <td class="cell-detail"> 
			   		<span><b>CONDICCION DE PAGO :</b> {{$item['COND_PAGO']}}</span>
			   		<span><b>AMPLIAR DIAS DE PAGO :</b> {{number_format($item['AMPLIAR_DIAS'], 0, '.', ',')}}</span>
			   		<span><b>TOTAL DIAS DE PAGO :</b> {{number_format($item['PLAZO_PAGO'], 0, '.', ',')}}</span>
			   </td>

			   <td class="cell-detail"> 
			   		<span><b>DIAS TRASCURRIDOS :</b> {{$item['PLAZO_DEUDA']}}</span>
			   		<span><b>DIAS MOROSOS :</b> {{number_format($item['DIAS_MOROSO'], 0, '.', ',')}}</span>
			   </td>

	      	   <td>{{$item['NOM_EMPR']}}</td>
	      	   <td>{{$item['CLIENTE']}}</td>

	      	   <td>{{$item['JEFE_VENTA']}}</td>
	      	   <td>{{$item['DOCUMENTO_ASOC']}}</td>
	      	   <td>{{date_format(date_create($item['FEC_EMISION']), 'd-m-Y')}}</td>
	      	   <td>{{date_format(date_create($item['FEC_VENCIMIENTO']), 'd-m-Y')}}</td>
	      	   <td>
	      	   	@if($item['INDAC'] == 'C')
	      	   		{{number_format($item['CAPITAL'], 2, '.', ',')}}
	      	   	@endif
	      	   </td>
	      	   <td>
	      	   	@if($item['INDAC'] == 'A')
	      	   		{{number_format($item['CAPITAL'], 2, '.', ',')}}
	      	   	@endif
				</td>
	      	   <td>{{number_format($item['CARTA'], 2, '.', ',')}}</td>
	      	   <td>{{number_format($item['CAN_SALDO'], 2, '.', ',')}}</td>
	      	   <td>{{date_format(date_create($item['FEC_CORTE']), 'd-m-Y')}}</td>

	      	</tr>                  
	    @endforeach
	    </tbody>

	</table>	
	</div>

	</div>
</div>

<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
</div>




