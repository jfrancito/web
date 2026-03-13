<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<table>
		<tr>
			<th colspan="19" style="text-align: center; font-size: 14pt; font-weight: bold;">REPORTE REGLA COMPROMISO PAGO</th>
		</tr>
		<tr>
			<th colspan="19" style="text-align: center; font-size: 11pt;">Empresa: {{$empresa}} | Sede: {{$centro}}</th>
		</tr>
		<tr></tr>
	    <thead>
	      <tr>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">SEDE</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">VENDEDOR</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">ORDEN</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FECHA ORDEN</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">MONTO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">CLIENTE</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FEC. ORDEN</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FEC. PAGO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">DIAS DIFF</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FECHA REGLA</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FECHA COMPROMISO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">DIAS REGLA</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">DIAS COMPROMISO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">AUTORIZADO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">GLOSA</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">DIV</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">PAGO REALIZADO EN RANGO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">SE PAGO TODO EL SALDO EN RANGO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">PAGO TOTALIDAD</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">MONTO TOTAL PAGADO</th>
	      </tr>
	    </thead>
	    <tbody>
	      @foreach($lista_reglas as $item)
	        <tr>
	          <td>{{$item->Sede}}</td>
	          <td>{{$item->Vendedor}}</td>
	          <td>{{$item->Orden}}</td>
	          <td>{{$item->Fecha_Orden ? date_format(date_create($item->Fecha_Orden), 'd-m-Y') : ''}}</td>
	          <td>{{number_format($item->Monto, 2, '.', '')}}</td>
	          <td>{{$item->Cliente}}</td>
	          @php
	            $dias = '';
	            if($item->Fecha_Orden && $item->FEC_PAGO){
	              $f_orden = date_create($item->Fecha_Orden);
	              $f_pago = date_create($item->FEC_PAGO);
	              $diff = date_diff($f_orden, $f_pago);
	              $dias = $diff->days;
	            }

                $dias_regla = isset($item->diasregla) ? $item->diasregla : '';
                $dias_compromiso = '';
                if($item->Fecha_Regla && $item->Fecha_Compromiso){
                  $f_regla = date_create($item->Fecha_Regla);
                  $f_compromiso = date_create($item->Fecha_Compromiso);
                  $diff_c = date_diff($f_regla, $f_compromiso);
                  $dias_compromiso = $diff_c->days;
                }
	          @endphp
	          <td>{{$item->Fecha_Orden ? date_format(date_create($item->Fecha_Orden), 'd-m-Y') : ''}}</td>
	          <td>{{$item->FEC_PAGO ? date_format(date_create($item->FEC_PAGO), 'd-m-Y') : ''}}</td>
	          <td>{{$dias}}</td>
	          <td>{{$item->Fecha_Regla ? date_format(date_create($item->Fecha_Regla), 'd-m-Y') : ''}}</td>
	          <td>{{$item->Fecha_Compromiso ? date_format(date_create($item->Fecha_Compromiso), 'd-m-Y') : ''}}</td>
	          <td>{{$dias_regla}}</td>
	          <td>{{$dias_compromiso}}</td>
	          <td>{{$item->Autorizado}}</td>
	          <td>{{$item->Glosa}}</td>
	          <td>{{$item->Div}}</td>
	          <td>{{number_format($item->Pago_Realizado_En_Rango, 2, '.', '')}}</td>
	          <td>{{$item->Se_Pago_Todo_El_Saldo_En_Rango}} 
                @if($item->Se_Pago_Todo_El_Saldo_En_Rango == 'SI' && isset($item->Ultima_Fecha_Rango) && $item->Ultima_Fecha_Rango)
                  ({{ date_format(date_create($item->Ultima_Fecha_Rango), 'd-m-Y') }})
                @endif
              </td>
	          <td>{{$item->Pago_Totalidad}}</td>
	          <td>{{number_format($item->Monto_Total_Pagado, 2, '.', '')}}</td>
	        </tr>
	      @endforeach
	    </tbody>
	</table>
</html>
