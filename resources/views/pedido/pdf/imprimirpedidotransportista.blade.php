<!DOCTYPE html>

<html lang="es">

<head>
	<title>Pedido ({{$pedido->codigo}}) </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/x-icon" href="{{ asset('public/favicon.ico') }}"> 

	<style>
		.izquierda{
			text-align: right;
		}

		.menu{
		    /*overflow:hidden;*/
		    width 	: 730px;
		    /*display : table;*/
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
			font-size: 0.9em;
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


		.codigo{
			width: 50px;
		}
		.descripcion{
			width: 250px;
		}
		.unidad{
			width: 40px;
		}
		.cantidad{
			width: 40px;
		}
		.precio{
			width: 80px;
		}
		.importe{
			width: 100px;
		}


		.totales{
			margin-top: 10px;
		    overflow:hidden;
		    width 	: 730px;
		    display : table;
		    /*border 	: 1px solid black;*/
		}

		.totales .left{
		    width	: 	65%
		    float	:	left;
		    display : 	table-cell;  
		   	/*border      : 1px solid red;  */ 
		}


		.totales .right{
		    width	: 	35%
		    float	:	left;
		    /*border  :	1px solid black; */
		    display : 	table-cell; 
		      
		}

		.totales .right p{
			font-size 	: 0.75em;
			margin-top	: 0px;
			margin-bottom 	: 1px;	

		}

		.totales .right .descripcion{
			display 	: inline-block;
			width 		: 55%;

		}
		.totales .right .monto{
			display 	: inline-block;
			width 		: 40%;

		}

		.totales .left .uno{
		    display     : inline-block;
		    width       : 25%;
		}
		.totales .left .dos{
		    /*border: 1px solid blue;   */ 
		    display     : inline-block;
		    width       : 70%;
		    font-size   : 0.75em;

		}
		.totales .left .dos p{
		    margin-top: 5px;
		    margin-bottom: 5px;
		}
		.totales .left .derecha{
		    margin-top: 55px;
		}
		.totales .left .uno img{
		    /*border: 1px solid red;*/
		    width: 100px;
		    position: absolute;
		    top: -87px;

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
		.center{
			text-align:  center;
		}
		.firma{
			margin-top: 30px;
			border-top: 1px solid black;
			width: 40%;
			font-size: 0.85em;
			float: right;
		}

		p,table{
		  font-family: 'Courier New', monospace;
		  font-size: 1em;
		}
		.titulo{
			text-align: center;
		}
		.menu{
			margin-top: -20px;
			padding: 0px;
		}
		.menu p {
			padding: 0px;
			margin: 0px;
		}
		table th,table td{
			font-size: 0.8em;
			padding-top:  2px;
			padding-bottom: 2px;
		}
	</style>
</head>

<body>
    <header>
	<div class="menu">
		<p><b>Observacion :  {{$pedido->glosa}}</b></p>
		<p class="center"><b>NOTA DE PEDIDO : {{$pedido->codigo}}</b></p>
	</div>
    </header>


    <section>
        <article>

			<div class="top">

			    <div class="det1">
	   				<p>
	   					<strong>Autorizado :{{$funcion->funciones->data_usuario($pedido->usuario_autorizacion)->nombre}}</strong> 
	   				</p>
	   				<p >
	   					<strong>Solicitud  : {{$funcion->funciones->data_usuario($pedido->usuario_crea)->nombre}}</strong>
	   				</p>

	   				<p>
	   					<strong>CLiente : {{$funcion->funciones->data_cliente($pedido->cuenta_id)->NOM_EMPR}}</strong> 
	   				</p>
<!-- 	   				<p >
	   					<strong>Condicción : {{$funcion->funciones->data_categoria($pedido->tipopago_id)->NOM_CATEGORIA}}</strong>
	   				</p> -->
	   				<p>
	   					<strong>Fecha de Entrega : {{date_format(date_create($pedido->fecha_despacho), 'd/m/Y')}}</strong>
	   				</p>  
	   				<p>
	   					<strong>Dirección de Entrega: {{$funcion->funciones->data_direccion($pedido->direccion_entrega_id)->NOM_DIRECCION}}</strong>
	   				</p>

			    </div>

			</div>
        </article>


        <article>

		  <table>
		    <tr>
		   		<th class='titulo descripcion'>PRODUCTO</th>
		      	<th class='titulo codigo'>CANTIDAD</th>
		      	<th class='titulo cantidad'>PRECIO</th>
		      	<th class='titulo precio'>TOTAL</th>
		    </tr>

		    @foreach($array_detalle_producto_request as $key => $item)

			    <tr class=''>
				    
				    <td><strong>
				    	{{$funcion->funciones->data_producto($item['data_nombre_producto'])->NOM_PRODUCTO}}
			    		@if($item['data_obsequio'] == '1')
			    		 (OBSEQUIO)
			    		@endif
			    		</strong>
				    </td>
					<td class='titulo'><strong>
						{{$item['data_cantidad']}}</strong>
					</td>
				    <td class='titulo'><strong>
			            @if($accion == 'cp') 
			              	{{number_format($item['data_precio'],4,'.','')}} 
			            @else
			                0.00
			            @endif</strong>
				    </td>
				    <td class='titulo'><strong>
				    	@if($accion == 'cp')
				    		@if($item['data_obsequio'] == '0')
				    			{{number_format($item['data_precio']*$item['data_cantidad'],2,'.','')}}
				    		@else
				    			0.00
				    		@endif
			            @else
			                0.00
			            @endif</strong>
				    </td>
			    </tr>
		    @endforeach		    
		  </table>
        </article>

    </section>


        <article>

        	<div>
        		<p class='firma'>
        			<strong>Nombre y apellidos:</strong><br>
					<strong>DNI:</strong><br>
					<strong>Teléfono:</strong><br>
					<strong>Cargo:</strong><br>

<!--         			{{$funcion->funciones->data_cliente($pedido->cuenta_id)->NOM_EMPR}}<br>
        			Nro Doc : {{$funcion->funciones->data_cliente($pedido->cuenta_id)->NRO_DOCUMENTO}} -->
        		</p>
        	</div>

        </article>



</body>
</html>