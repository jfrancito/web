<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <style type="text/css">
        	section{
        		width: 100%;
        		background: #E8E8E8;
        		padding: 0px;
        		margin: 0px;
        	}

        	.panelcontainer{
        		width: 50%;
        		background: #fff;
        		margin: 0 auto;


        	}
        	.panelhead{
        		background: #eb6357;
        		padding-top: 10px;
        		padding-bottom: 10px;
        		color: #fff;
        		text-align: center;
        		font-size: 1.2em;
        	}
        	.panelbody,.panelbodycodigo{
        		padding-left: 15px;
        		padding-right: 15px;
        	}
            .panelbodycodigo h3 small{
                color: #08257C;
            }

            table, td, th {    
                border: 1px solid #ddd;
                text-align: left;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            th, td {
                padding: 15px;
                font-size: 12px;
            }

        </style>

    </head>


    <body>
    	<section>
    		<div class='panelcontainer'>
    			<div class="panel">
                    <div class='panelbodycodigo'>
                        	<h3>Pedido de Despacho  {{$codigo}}  </h3>
                    </div>
    				<div class="panelhead">Pedido</div>
    				<div class='panelbody'>
                            <table  class="table demo" >
                                <tr>
                                    <th>
                                        Empresa Solicitante
                                    </th>
                                    <th>
                                        Centro Solicitante
                                    </th>
                                    <th>
                                        Usuario Solicitante
                                    </th>
                                    <th>
                                        Codigo
                                    </th>
                                    <th>
                                        Fecha Pedido
                                    </th>                                       
                                </tr>
                                <tr>
	                                <td>{{$pedido->empresa->NOM_EMPR}}</td>
                                    <td>{{$pedido->centro->NOM_CENTRO}}</td>
                                    <td>{{$funcion->data_usuario($pedido->usuario_crea)->nombre}}</td>
                                    <td>{{$pedido->codigo}}</td>
                                    <td>{{date_format(date_create($pedido->fecha_crea), 'd-m-Y H:i')}}</td>
                                </tr>
                            </table>
    				</div>
    			</div>
    		</div>
		</section>
    </body>

</html>


