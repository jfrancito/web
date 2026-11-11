<div class="modal-header" style="background-color: #4285f4; color: #fff;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close" style="color: #fff; opacity: 0.8;"><span class="mdi mdi-close"></span></button>
	<h3 class="modal-title" style="color: #fff; font-weight: 600;">
		<i class="icon mdi mdi-assignment-check" style="margin-right: 12px; font-size: 1.3em; vertical-align: middle;"></i>
		Detalle de Pagos: <span style="font-weight: 800; text-decoration: underline;">{{ $div }}</span>
		<div style="font-size: 9px; margin-top: 5px; opacity: 0.9; font-weight: 400;">
			<i class="mdi mdi-calendar-note"></i> Rango Evaluación: 
			<strong>{{ $fecha_regla ? date_format(date_create($fecha_regla), 'd-m-Y') : '-' }}</strong> 
			al 
			<strong>{{ $fecha_compromiso ? date_format(date_create($fecha_compromiso), 'd-m-Y') : '-' }}</strong>
		</div>
	</h3>
</div>
<div class="modal-body" style="max-height: 450px; overflow-y: auto;">
	<div class="table-responsive">
		<table class="table table-striped table-hover table-fw-widget" style="width: 100%;">
			<thead style="background-color: #f5f5f5;">
				<tr>
					<th>Nro. Habilitación</th>
					<th>Fec. Habilitación</th>
					<th>Fec. Crea Aud.</th>
					<th class="text-right">Importe</th>
					<th class="text-center">RN</th>
				</tr>
			</thead>
			<tbody>
				@php $total_pago = 0; @endphp
				@forelse($detalle as $index => $item)
					@php 
						$total_pago += $item->CAN_IMPORTE;
						$highlight = false;
						if($fecha_regla && $fecha_compromiso){
							$f_hab = date_format(date_create($item->FEC_HABILITACION), 'Y-m-d');
							$f_reg = date_format(date_create($fecha_regla), 'Y-m-d');
							$f_com = date_format(date_create($fecha_compromiso), 'Y-m-d');
							if($f_hab >= $f_reg && $f_hab <= $f_com){
								$highlight = true;
							}
						}
					@endphp
					<tr style="{{ $highlight ? 'background-color: #fff9c4 !important; border-left: 4px solid #fbc02d;' : '' }}">
						<td>{{ $item->COD_HABILITACION }}</td>
						<td>
							{{ date_format(date_create($item->FEC_HABILITACION), 'd-m-Y') }}
							@if($highlight)
								<span class="label label-warning" style="font-size: 8px; margin-left: 5px;">RANGO</span>
							@endif
						</td>
						<td>{{ date_format(date_create($item->FEC_USUARIO_CREA_AUD), 'd-m-Y H:i:s') }}</td>
						<td class="text-right"><strong>{{ number_format($item->CAN_IMPORTE, 2, '.', ',') }}</strong></td>
						<td class="text-center">{{ $item->RN_DETRACCION }}</td>
					</tr>
				@empty
					<tr>
						<td colspan="5" class="text-center">No se encontraron registros de habilitación.</td>
					</tr>
				@endforelse
			</tbody>
			@if(count($detalle) > 0)
			<tfoot style="background-color: #f9f9f9; font-weight: bold; border-top: 2px solid #eee;">
				<tr>
					<td colspan="3" class="text-right">TOTALIZADO:</td>
					<td class="text-right" style="font-size: 1.1em; color: #1a202c;">{{ number_format($total_pago, 2, '.', ',') }}</td>
					<td></td>
				</tr>
			</tfoot>
			@endif
		</table>
	</div>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cerrar</button>
</div>
