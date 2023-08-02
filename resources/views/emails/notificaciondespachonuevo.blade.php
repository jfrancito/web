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
            .fondogris{
                background: #cce6fd;
                text-align: center;
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
                    <div class='panelbodycodigo'>
                        <h3>Detalle Pedido</h3>
                    </div>

                    <div class="panelhead">Detalle del pedido</div>
                    <div class='panelbody'>
                        <table  class="table demo" >
                            <tr>
                                <th>
                                    Fechas
                                </th>
                                <th>
                                    Cliente
                                </th>
                                <th>
                                    Mobil
                                </th>
                                <th>
                                    Producto
                                </th>
                                <th>
                                    Muestra
                                </th>
                                <th>
                                    Cantidad
                                </th>
                                <th>Origen</th>
                                <th>Kilos</th>
                                <th>Sacos</th>
                                <th>Palet</th>
                                <th>Totales</th>

                            </tr>
                        @php $grupo_movil   =   ""; @endphp
                        @foreach($pedido->viewdetalleordendespacho as $index => $item)
                            @php

                                //agrupar por grupo movil
                                $almacen_lote_group_id    =   array();
                                $array_respuesta          =   $funcion->crearrolwpan($item->grupo_movil,$index,$grupo_movil);
                                $sw_crear_movil           =   $array_respuesta['sw_crear'];
                                $grupo_movil              =   $array_respuesta['grupo'];
                                $rowspan_mobil_producto   =   $funcion->rowspan_mobil_producto($item->ordendespacho_id,$item->grupo_movil);
                                $unidad_medida            =   $funcion->data_categoria($item->producto->COD_CATEGORIA_UNIDAD_MEDIDA)->NOM_CATEGORIA;
                                $centro_origen            =   $funcion->data_centro($item->centro_atender_id);
                            @endphp

                            <tr>
                              <td>
                                <span><b>Pedido</b> : {{date_format(date_create($item->fecha_pedido), 'd-m-Y')}}</span> <br>
                                <span><b>Entrega</b> : {{date_format(date_create($item->fecha_entrega), 'd-m-Y')}} </span>
                              </td>
                              <td>

                                <span><b>Cliente</b> : 
                                  @if(trim($item->cliente_id) != '')
                                    {{$funcion->nombre_cliente_despacho_cliente($item->cliente_id)}}
                                  @endif</span> <br>
                                <span><b>Orden Cen</b> : {{substr($item->nro_orden_cen, 0, -1)}} </span>

                              </td>

                              @if($sw_crear_movil == 1 and $item->grupo_movil <> '0') 
                                <td rowspan = "{{$item->grupo_orden_movil - $rowspan_mobil_producto}}" class='fondogris'>
                                    <b>{{$item->grupo_movil}}</b>
                                </td>
                              @endif

                              <td class="cell-detail">
                                <span>{{$item->producto->NOM_PRODUCTO}}</span><br>
                                <span class="cell-detail-description-producto">
                                {{$unidad_medida}} de  {{$item->producto->CAN_PESO_SACO}} kg (group {{$item->restar_grupo_orden_movil+1}})
                                </span>
                              </td>

                              <td class='center'>
                                  {{number_format($item->muestra, 2, '.', ',')}}
                              </td>

                              <td class='center'>
                                  {{number_format($item->cantidad, 2, '.', ',')}}
                              </td>


                              <td>
                                {{$centro_origen->NOM_CENTRO}}
                              </td>
                              <td>{{number_format($item->kilos, 4, '.', ',')}}</td>
                              <td>{{number_format($item->cantidad_sacos, 4, '.', ',')}}</td>
                              <td>{{number_format($item->palets, 4, '.', ',')}}</td>

                              @if($sw_crear_movil == 1 and $item->grupo_movil <> '0') 
                                <td rowspan = "{{$item->grupo_orden_movil - $rowspan_mobil_producto}}" class='fondogris'>

                                <span><b>Kilos</b> : 
                                    {{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'kilos'),4,'.',',')}}
                                </span> <br>
                                <span><b>Sacos</b> : 
                                    {{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'cantidad_sacos'),4,'.',',')}} 
                                </span> <br>
                                <span><b>Palets</b> : 
                                    {{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'palets'),4,'.',',')}}
                                </span>

                                </td>
                              @endif


                            </tr>
                        @endforeach

                        </table>
                    </div>


                </div>
            </div>
        </section>
    </body>

</html>


