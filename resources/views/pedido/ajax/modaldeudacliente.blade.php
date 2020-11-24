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


                             <table class='table'>
                             <tbody>
                                 <tr>  
                                     <td width=  "100"  style=  "font-size:13px;font-weight:bold;text-align: center;" colspan="4">  Venta mas antigua por pagar:  </td>
                                 </tr>
                                @php 
                                    $sfi =  0;
                                    $shr =  0;
                                    $sia =  0;
                                    $sic =  0;
                                    $sii =  0;
                                    $sin =  0;
                                    $sis =  0;
                                    $sit =  0;
                                @endphp

                                @foreach($deuda_antigua as $deu)
                                    @if($deu->NOM_EMPR == 'FI' && $sfi ==0)
                                        <tr> 
                                            <td style=  "font-size:13px;">
                                            	<strong>Empresa:</strong> <small style="font-size:13px;color:#191970">{{$deu->NOM_EMPR}}</small>
                                            </td>
                                            <td style=  "font-size:13px;"><strong>Documento:</strong> <small style="font-size:13px;color:#191970">{{$deu->NroDocumento}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Días transcurrido:</strong> <small style="font-size:13px;color:#191970">{{$deu->diasTranscurridos}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Saldo a pagar:</strong> <small style="font-size:13px;color:#191970">{{number_format($deu->CAN_SALDO, 2, '.', ',')}}</small></td>
                                        </tr>
                                        @php $sfi =  1; @endphp
                                    @endif

                                    @if($deu->NOM_EMPR == 'HR' && $shr ==0)
                                        <tr> 
                                            <td style=  "font-size:13px;"><strong>Empresa:</strong> <small style="font-size:13px;color:#191970">{{$deu->NOM_EMPR}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Documento:</strong> <small style="font-size:13px;color:#191970">{{$deu->NroDocumento}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Días transcurrido:</strong> <small style="font-size:13px;color:#191970">{{$deu->diasTranscurridos}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Saldo a pagar:</strong> <small style="font-size:13px;color:#191970">{{number_format($deu->CAN_SALDO, 2, '.', ',')}}</small></td>
                                        </tr>
                                        @php $shr =  1; @endphp
                                    @endif

                                    @if($deu->NOM_EMPR == 'IA' && $sia ==0)
                                        <tr> 
                                            <td style=  "font-size:13px;"><strong>Empresa:</strong> <small style="font-size:13px;color:#191970">{{$deu->NOM_EMPR}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Documento:</strong> <small style="font-size:13px;color:#191970">{{$deu->NroDocumento}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Días transcurrido:</strong> <small style="font-size:13px;color:#191970">{{$deu->diasTranscurridos}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Saldo a pagar:</strong> <small style="font-size:13px;color:#191970">{{number_format($deu->CAN_SALDO, 2, '.', ',')}}</small></td>
                                        </tr>
                                        @php $sia =  1; @endphp
                                    @endif

                                    @if($deu->NOM_EMPR == 'IC' && $sic ==0)
                                        <tr> 
                                            <td style=  "font-size:13px;"><strong>Empresa:</strong> <small style="font-size:13px;color:#191970">{{$deu->NOM_EMPR}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Documento:</strong> <small style="font-size:13px;color:#191970">{{$deu->NroDocumento}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Días transcurrido:</strong> <small style="font-size:13px;color:#191970">{{$deu->diasTranscurridos}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Saldo a pagar:</strong> <small style="font-size:13px;color:#191970">{{number_format($deu->CAN_SALDO, 2, '.', ',')}}</small></td>
                                        </tr>
                                        @php $sic =  1; @endphp
                                    @endif

                                    @if($deu->NOM_EMPR == 'II' && $sii ==0)
                                        <tr> 
                                            <td style=  "font-size:13px;"><strong>Empresa:</strong> <small style="font-size:13px;color:#191970">{{$deu->NOM_EMPR}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Documento:</strong> <small style="font-size:13px;color:#191970">{{$deu->NroDocumento}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Días transcurrido:</strong> <small style="font-size:13px;color:#191970">{{$deu->diasTranscurridos}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Saldo a pagar:</strong> <small style="font-size:13px;color:#191970">{{number_format($deu->CAN_SALDO, 2, '.', ',')}}</small></td>
                                        </tr>
                                        @php $sii =  1; @endphp
                                    @endif

                                    @if($deu->NOM_EMPR == 'IN' && $sin ==0)
                                        <tr> 
                                            <td style=  "font-size:13px;"><strong>Empresa:</strong> <small style="font-size:13px;color:#191970">{{$deu->NOM_EMPR}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Documento:</strong> <small style="font-size:13px;color:#191970">{{$deu->NroDocumento}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Días transcurrido:</strong> <small style="font-size:13px;color:#191970">{{$deu->diasTranscurridos}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Saldo a pagar:</strong> <small style="font-size:13px;color:#191970">{{number_format($deu->CAN_SALDO, 2, '.', ',')}}</small></td>
                                        </tr>
                                        @php $sin =  1; @endphp
                                    @endif

                                    @if($deu->NOM_EMPR == 'IS' && $sis ==0)
                                        <tr> 
                                            <td style=  "font-size:13px;"><strong>Empresa:</strong> <small style="font-size:13px;color:#191970">{{$deu->NOM_EMPR}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Documento:</strong> <small style="font-size:13px;color:#191970">{{$deu->NroDocumento}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Días transcurrido:</strong> <small style="font-size:13px;color:#191970">{{$deu->diasTranscurridos}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Saldo a pagar:</strong> <small style="font-size:13px;color:#191970">{{number_format($deu->CAN_SALDO, 2, '.', ',')}}</small></td>
                                        </tr>
                                        @php $sis =  1; @endphp
                                    @endif

                                    @if($deu->NOM_EMPR == 'IT' && $sit ==0)
                                        <tr> 
                                            <td style=  "font-size:13px;"><strong>Empresa:</strong> <small style="font-size:13px;color:#191970">{{$deu->NOM_EMPR}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Documento:</strong> <small style="font-size:13px;color:#191970">{{$deu->NroDocumento}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Días transcurrido:</strong> <small style="font-size:13px;color:#191970">{{$deu->diasTranscurridos}}</small></td>
                                            <td style=  "font-size:13px;"><strong>Saldo a pagar:</strong> <small style="font-size:13px;color:#191970">{{number_format($deu->CAN_SALDO, 2, '.', ',')}}</small></td>
                                        </tr>
                                        @php $sit =  1; @endphp
                                    @endif

                                @endforeach
                             </tbody>
                             </table>


  	<table class="table" style="font-size: 0.85em;">
	    <thead>
	      <tr>
	        <th>Documento</th>
	        <th>Fecha</th>
	        <th>Saldo</th>
	        <th>Plazo de deuda</th>
	        <th>DIAS</th> 
	      </tr>
	    </thead>
	    <tbody>

	    @while ($row = $lista_deuda_cliente->fetch())

		    <tr>
		        <td class="cell-detail">
	              <span><b>DIV : </b>{{$row['NUMDOC']}}</span>
	              <span><b>DOCUMENTO CTBL : </b>{{$row['COD_DOCUMENTO_CTBLE']}}</span>
	              <span><b>TIPO DE DOCUMENTO : </b>{{$row['TIPDOC']}}</span>
	            </td>

	            <td class="cell-detail">
	              <span><b>Emision : </b>{{date_format(date_create($row['FEC_EMISION']), 'd-m-Y')}}</span>
	              <span><b>Vencimiento : </b>{{date_format(date_create($row['FEC_VENCIMIENTO']), 'd-m-Y')}}</span>
	              <span><b>Tipo de pago : </b>{{$funcion->funciones->data_documento_ctbl($row['COD_DOCUMENTO_CTBLE'])->TXT_CATEGORIA_TIPO_PAGO}}</span>
	            </td>


		        <td>{{number_format($row['CAN_SALDO'], 2, '.', ',')}}</td>
		        <td class= 'center'>
		        	<span class="badge badge-primary btn-eyes">
		            	{{$row['PLAZO_DEUDA']}}
		          	</span>
		        	
		    	</td>
	            <td class="cell-detail">
	              <span><b>DE 0 A 30 DIAS : </b>{{number_format($row['PD030'], 2, '.', ',')}}</span>
	              <span><b>DE 31 A 90 DIAS : </b>{{number_format($row['PD3190'], 2, '.', ',')}}</span>
	              <span><b>DE 91 A 180 DIAS : </b>{{number_format($row['PD91180'], 2, '.', ',')}}</span>
	              <span><b>DE 181 A MAS DIAS : </b>{{number_format($row['PD181M'], 2, '.', ',')}}</span>
		    	</td>
		    </tr> 

	      @endwhile

	    </tbody>
  	</table>
  </div>

</div>
<div class="modal-footer">
  <button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cancelar</button>
</div>