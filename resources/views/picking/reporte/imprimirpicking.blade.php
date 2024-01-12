<!DOCTYPE html>

<html lang="es">

<head>
	<title>Picking ({{$picking->codigo}}) </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/x-icon" href="{{ asset('public/favicon.ico') }}"> 
	<style>
		.izquierda{
			text-align: right;
		}

		.menu{
		    overflow:hidden;
		    width 	: 730px;
		    display : table;
		    /*border 	: 1px solid black;*/
		}

		.menu .left{
		    width	: 	50%
		    float	:	left;
		    display : 	table-cell; 
		    text-align: center;     
		}


		.menu .right{
		    width	: 	50%
		    float	:	left;
		    border  :	1px solid black; 
		    display : 	table-cell; 
		    text-align: center; 
		    border-radius: 4px ;    
		}

		.menu .left h1{
			font-size:  1.2em;
			/*border   :  1px solid red;*/
		}
		.menu .left h3{
			font-size:  0.8em;
			font-weight: normal;
			/*border   :  1px solid red;*/
		}
		.menu .left h4{
			font-size:  0.8em;
			font-weight: normal;	
			/*border: 1px solid blue;*/
		}

		.top .det1{
			width: 718px;
			font-size: 0.8em;
			margin-top: 5px;
			border: 1px solid #000;
			border-radius: 4px;
			padding: 5px;

		}
		.top .det1 p{
			margin-top: 1px;
			margin-bottom: 3px;
		}

		.det2{
			margin-top: 5px;
		    overflow:hidden;
		    width 	: 730px;
		    display : table;
			border: 1px solid #000;
			border-radius: 4px;
		    font-size: 0.8em;
		    padding: 5px;
		}

		.det2 .d1,.det2 .d2,.det2 .d3{
		    width	: 	50%
		    float	:	left;
		    display : 	table-cell;     
		}

		table {
		    border-collapse: collapse;
		    width 	: 730px;
			margin-top: 15px;
		    font-size: 0.7em;    
		}

		th, td {
		    padding: 8px;
		    text-align: left;
		    border-bottom: 1px solid #ddd;
		}

		.titulo{
			text-align: center;
			font-size: 0.90em;
		}
		.codigo{
			width: 50px;
		}
		.item{
			width: 8px;
		}
		.descripcion{
			text-align: left;
			font-size: 0.90em;
		}
		
		footer .observacion{
		    border-top: 1px solid #000;
		    border-bottom:  1px solid #000;
		}
		footer .observacion h3 {
		    /*border: 1px solid red;*/
		    margin-top: 2px;
		    margin-bottom: 2px;
		    font-size: 0.9em;
		}
		footer .observacion p {
		    /*border: 1px solid red;*/
		    margin-top: 0px;
		    margin-bottom: 2px;    
		    font-size: 0.8em;
		}

		.badge-warning {
		    background-color: #f6c163;
		}

		.badge-danger {
		    background-color: #eb6357;
		}

		.badge-default {
		    background-color: #d9d9d9;
		}

		.badge-success{
			background-color: #37b358;
		}


	</style>


</head>

<body>
    <header>
	<div class="menu">
	    <div class="left">
	    		<h1>{{$funcion->funciones->data_empresa($picking->empresa_id)->NOM_EMPR}}</h1>	    		
                <h3>{{$funcion->funciones->data_centro($picking->centro_id)->NOM_CENTRO}}</h3>	    		
	    </div>
	    <div class="right">
	    		<h3>{{$titulo}} : {{$picking->codigo}}</h3>
                <h4>Fecha: {{date_format(new DateTime($picking->fecha_picking),'d/m/Y')}}</h4>
	    </div>
	</div>
    </header>

    <section>
       
        <article>

		  <table>
		    <tr>
			  <th class='titulo'>NRO° ITEM</th>  
		      <th class='titulo'>REFERENCIA</th>
		      <th class='titulo'>PRODUCTO</th>
              <th class='titulo'>CANTIDAD</th>
			  <th class='titulo'>PAQUETES</th>
			  <th class='titulo'>PESO</th>
			 
		    </tr>
            @php 
				$nro   =   0; 
				$total   =   0; 			
			@endphp

            @foreach($pickingdetalle as $item)

                @php $color   =   ''; @endphp
                @php $nro = $nro + 1; @endphp

                @if($item->orden_id <> '')
                    @php $color   =   'badge-white'; @endphp
                @else
                    @php $color   =   'badge-warning'; @endphp 
                @endif

                <tr class='{{$color}}'>
                    <td class='titulo'>{{$nro}}</td>
                    <td class='titulo'>{{$item->transferencia_id}}</td>
                    <td class='descripcion'>{{$item->producto->NOM_PRODUCTO}}</td>
                    <td class='titulo'>{{number_format($item->cantidad,2,'.','')}}</td>
					<td class='titulo'>{{number_format($item->paquete,2,'.','')}}</td>
					<td class='titulo'>{{number_format($item->peso_total,2,'.','')}}</td>
                </tr>
				@php $total += $item->peso_total; @endphp
            @endforeach	
		   	
				<tr>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
					<td class='titulo'>Total : </td>
					<td class='titulo'>{{number_format($total,2,'.','')}}</td>
                </tr>
				@php 
					$total_palets   =   $picking->palets * $palets_peso; 			
				@endphp
				<tr>
                    <td ></td>
                    <td ></td>
                    <td class='descripcion'>PALETS</td>
                    <td class='titulo'>Cantidad : {{number_format($picking->palets,2,'.','')}}</td>
					<td class='titulo'>Peso : {{number_format($palets_peso,2,'.','')}}</td>
					<td class='titulo'>{{number_format($total_palets,2,'.','')}}</td>
                </tr>
				<tr>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
					<td class='titulo'>PESO TOTAL : </td>
					<td class='titulo'>{{number_format($total + $total_palets,2,'.','')}}</td>
                </tr>
		  </table>
        </article>

    </section>


</body>
</html>