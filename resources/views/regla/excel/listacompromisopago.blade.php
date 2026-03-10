<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<table>
		<tr>
			<th colspan="16" style="text-align: center; font-size: 14pt; font-weight: bold;">REPORTE REGLA COMPROMISO PAGO</th>
		</tr>
		<tr>
			<th colspan="16" style="text-align: center; font-size: 11pt;">Empresa: {{$empresa}} | Sede: {{$centro}}</th>
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
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FECHA REGLA</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">FECHA COMPROMISO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">AUTORIZADO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">GLOSA</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">DIV</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">PAGO TOTALIDAD</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">MONTO TOTAL PAGADO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">PAGOS PERIODO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">MONTO PAGADO PERIODO</th>
	        <th style="background-color: #4285f4; color: #ffffff; font-weight: bold;">CANTIDAD PAGOS PERIODO</th>
	      </tr>
	    </thead>
	    <tbody>
	      @foreach($lista_reglas as $item)
	        <tr>
	          <td>{{$item->Sede}}</td>
	          <td>{{$item->Vendedor}}</td>
	          <td>{{$item->Orden}}</td>
	          <td>{{date_format(date_create($item->Fecha_Orden), 'd-m-Y')}}</td>
	          <td>{{number_format($item->Monto, 2, '.', '')}}</td>
	          <td>{{$item->Cliente}}</td>
	          <td>{{$item->Fecha_Regla ? date_format(date_create($item->Fecha_Regla), 'd-m-Y') : ''}}</td>
	          <td>{{$item->Fecha_Compromiso ? date_format(date_create($item->Fecha_Compromiso), 'd-m-Y') : ''}}</td>
	          <td>{{$item->Autorizado}}</td>
	          <td>{{$item->Glosa}}</td>
	          <td>{{$item->Div}}</td>
	          <td>{{$item->Pago_Totalidad}}</td>
	          <td>{{number_format($item->Monto_Total_Pagado, 2, '.', '')}}</td>
	          <td>{{$item->Hay_Pagos_En_Periodo}}</td>
	          <td>{{number_format($item->Monto_Pagado_En_Periodo, 2, '.', '')}}</td>
	          <td>{{$item->Cantidad_Pagos_En_Periodo}}</td>
	        </tr>
	      @endforeach
	    </tbody>
	</table>
</html>
