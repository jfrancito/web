<input type="hidden" name="h_idpicking" id='idpicking' value="{{$picking->id}}">
<input type="hidden" name="h_group_detalle_picking" id='h_group_detalle_picking' value="{{json_encode($group_detalle_picking)}}">

<div class="col-md-12">
  	<div class="panel">    
		<div class="panel-body">

			<div class="panel panel-border-color col-sm-6">
				<div class="form-group">
					<div class="col-sm-12" style = ""> 
						<table class="table table-detraccion" style='font-size: 0.98em;' id="" >
							<thead>
								<tr>
									<th>
										<div class="text-center be-checkbox be-checkbox-sm has-primary">
										Sel.
										</div>
									</th>
									<th>Solicitud Trans./ Orden Venta</th>		
									<th>Opciones</th>			
								</tr>
							</thead>

							<tbody>
								@foreach($group_detalle_picking as $key => $item)	
									<tr
										data_producto="{{$item['transferencia_id']}}"
									>
									<td>
										<div class="text-center be-checkbox be-checkbox-sm has-primary">
										<input  
											type="checkbox"	
											class="input_asignar_lp"	
											id="{{$picking->id}}{{$item['transferencia_id']}}"			
											name="{{$picking->id}}-{{$item['transferencia_id']}}" 	
											@if($item['tiene_detraccion'] == 1) disabled @endif
											>
										<label  for="{{$picking->id}}{{$item['transferencia_id']}}"
												data-atr = "ver"
												class = "checkbox checkbox_asignar_lp"                    
												name="{{$picking->id}}{{$item['transferencia_id']}}"
												style = 'margin-top:0px;'
										></label>
										</div>
									</td>					
									<td class='center'>
										<span>{{$item['transferencia_id']}}</span>
									</td>
									<td>
										<div class="{{$item['transferencia_id']}}">
											<div class="be-radio has-success inline">
												<input type="radio" value='1' name="det-{{$item['transferencia_id']}}" 
														class="input_check_grr"	
														id="GRR-{{$item['transferencia_id']}}" 
														@if($item['tipo_operacion'] == 'TRANSFERENCIA') disabled @endif
														checked>
												<label for="GRR-{{$item['transferencia_id']}}">GRR</label>
											</div>
											<div class="be-radio has-danger inline">
												<input type="radio" value='0' name="det-{{$item['transferencia_id']}}"  
														class="input_check_fac"	
														id="FAC-{{$item['transferencia_id']}}"
														@if($item['tipo_operacion'] == 'TRANSFERENCIA') disabled @endif>
												<label for="FAC-{{$item['transferencia_id']}}">Factura</label>
											</div>
										</div>
										
									</td>					
								@endforeach
							</tbody>
						</table> 
					</div>
				</div>
			</div>
			
			<div class="col-sm-1"></div>

			<div class="panel panel-border-color col-sm-5">
				
				<div class="form-group">
					<label  class="col-sm-3 control-label" >
						<div class="tooltipfr">Doc. de Referencia
						<span class="tooltiptext">(Número de Factura o GRR)</span>
						</div>
					</label>
					<div class="col-sm-2">
					<input  type="text"
							id="serie_grr" name='serie_grr' value="" 
							required = "" minlength="4" maxlength="4" style="text-transform:uppercase"
							autocomplete="off" class="form-control input-sm" data-aw="6"/>
					</div>
					<div class="col-sm-3">
					<input  type="text"
							id="corr_grr" name='corr_grr' value="" 
							required = "" 
							maxlength="8"
							onblur="ponerCeros(this)" 
							autocomplete="off" class="form-control input-sm" data-aw="6"/>
					</div>
				</div>

				<div class="form-group">
					<label  class="col-sm-3 control-label" >
						<div class="tooltipfr">Monto
						<span class="tooltiptext">Total de detracción</span>
						</div>
					</label>
					<div class="col-sm-5">

						<input  type="text"
							id="monto" name='monto' placeholder="Monto"
							required = "" class="form-control input-sm importe" readonly 
							autocomplete="off" data-aw="6"/>

					</div>
				</div>			

				<div class="form-group">
					<div class="col-xs-2"></div>
					<div class="col-xs-6">
						<p class="text-right">							
							<button type="submit" class="btn btn-space btn-primary btn_guardar_detraccion" >Guardar</button>
						</p>
					</div>
				</div>

			</div>
		</div>

		<div class="panel panel-border-color panel-border-color-warning">    	

			<div class="panel-heading">DETALLE</div>

			<div class='ajax_lista_producto_detraccion'></div>
		</div>

  	</div>
</div>

<script type="text/javascript">
	function ponerCeros(num) {
	while (num.value.length<8)
		num.value = '0'+num.value;
	}
</script>