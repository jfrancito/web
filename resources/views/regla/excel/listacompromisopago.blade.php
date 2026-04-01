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
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">CLIENTE</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">AUTORIZADO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">ORDEN.OV</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FECHA.OV</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FEC. PAGO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">CONDICION</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FECHA REGLA</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FECHA COMPROMISO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">MONTO COMPROMISO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">PAGO REALIZADO EN RANGO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">SE PAGO TODO EL SALDO EN RANGO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">GLOSA</th>
	      </tr>
	    </thead>
	    <tbody>
	      @foreach($lista_reglas as $item)
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

	        <tr>
	          <td>{{$item->Sede}}</td>
	          <td>{{$item->Vendedor}}</td>
	          <td>{{$item->Cliente}}</td>
	          <td>{{$item->Autorizado}}</td>
	          <td>{{$item->Orden}}</td>
	          <td>{{$item->Fecha_Orden ? date_format(date_create($item->Fecha_Orden), 'd-m-Y') : ''}}</td>
	          <td>{{$item->FEC_PAGO ? date_format(date_create($item->FEC_PAGO), 'd-m-Y') : ''}}</td>
	          <td>{{$dias}}</td>
	          <td>{{$item->Fecha_Regla ? date_format(date_create($item->Fecha_Regla), 'd-m-Y') : ''}}</td>
	          <td>{{$item->Fecha_Compromiso ? date_format(date_create($item->Fecha_Compromiso), 'd-m-Y') : ''}}</td>
	          <td>{{number_format($item->Saldo_Pendiente_Al_Crear_Regla, 2, '.', '')}}</td>
	          <td>{{number_format($item->Pago_Realizado_En_Rango, 2, '.', '')}}</td>
	          @php
                $mostrar_fecha_excel = ($item->Se_Pago_Todo_El_Saldo_En_Rango == 'SI') || (round((float)$item->Pago_Realizado_En_Rango, 2) == round((float)$item->Saldo_Pendiente_Al_Crear_Regla, 2) && (float)$item->Pago_Realizado_En_Rango > 0);
              @endphp
              <td>{{ $mostrar_fecha_excel ? 'SI' : $item->Se_Pago_Todo_El_Saldo_En_Rango }} 
                @if($mostrar_fecha_excel && isset($item->Ultima_Fecha_Rango) && $item->Ultima_Fecha_Rango)
                  ({{ date_format(date_create($item->Ultima_Fecha_Rango), 'd-m-Y') }})
                @endif
            </td>
	          <td>{{$item->Glosa}}</td>
	        </tr>
	      @endforeach
	    </tbody>
	</table>
</html>
