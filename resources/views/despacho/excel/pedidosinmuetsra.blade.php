<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	{!! Html::style('public/css/excel/excel.css') !!}
    <!-- titulo -->


<!--     <table>
        <tr>
          <th class= 'center tablaho'>Empresa solicitante</th>  
          <th class= 'center tablaho'>Centro solicitante</th>           
          <th class= 'center tablaho'>Usuario solicitante</th>
          <th class= 'center tablaho'>Codigo</th> 
          <th class= 'center tablaho'>Fecha pedido</th>                                    
        </tr>
        <tr>
            <td>{{$pedido->empresa->NOM_EMPR}}</td>
            <td>{{$pedido->centro->NOM_CENTRO}}</td>
            <td>{{$funcion->data_usuario($pedido->usuario_crea)->nombre}}</td>
            <td>{{$pedido->codigo}}</td>
            <td>{{date_format(date_create($pedido->fecha_crea), 'd-m-Y H:i')}}</td>
        </tr>
    </table> -->


    <table>

        <tr>

          <th class= 'center tablaho'>Mobil</th> 
          <th class= 'center tablaho'>Fecha pedido</th>  
          <th class= 'center tablaho'>Fecha entrega</th>           
          <th class= 'center tablaho'>Cliente</th>
          <th class= 'center tablaho'>Orden Cen</th> 
          <th class= 'center tablaho'>Producto</th>
          <th class= 'center tablaho'>Unidad medida</th>
          <th class= 'center tablaho' >Cantidad</th>
          <th class= 'center tablaho' >Muestra</th>
          <th class= 'center tablaho' >Atender</th>

          <th class= 'center tablaho' >Origen</th>

          <th class= 'center tablaho' >Bolsas</th>
          <th class= 'center tablaho' >Presentacion</th>
          <th class= 'center tablaho' >Kilos</th>
          <th class= 'center tablaho' >Sacos</th>





        </tr>
        @php $grupo_movil   =   ""; @endphp
        @php $grupo_movil_ante   =   ""; @endphp

        @php $grupo_movil_sum   =   ""; @endphp

        @foreach($pedido->viewdetalleordendespachosinmuestra as $index => $item)
            @php

                //agrupar por grupo movil
                $almacen_lote_group_id    =   array();

                $array_respuesta          =   $funcion->crearrolwpan($item->grupo_movil,$index,$grupo_movil);
                $sw_crear_movil           =   $array_respuesta['sw_crear'];
                $grupo_movil              =   $array_respuesta['grupo'];
                $rowspan_mobil_producto   =   $funcion->rowspan_mobil_producto($item->ordendespacho_id,$item->grupo_movil);
                $unidad_medida            =   $funcion->data_categoria($item->producto->COD_CATEGORIA_UNIDAD_MEDIDA)->NOM_CATEGORIA;
                $centro_origen            =   $funcion->data_centro($item->centro_atender_id);
                $color_fila               =   '';
              
            @endphp

            @php $sw_totales_plan         =   0; @endphp
            @if((int)($index)==0)
              @php $grupo_movil_ante      =   $grupo_movil; @endphp
            @else
              @if($grupo_movil_ante==$grupo_movil)
                      @php $grupo_movil_ante      =   $grupo_movil; @endphp
              @else
                      @php $sw_totales_plan       =   1; @endphp
                      @php $grupo_movil_sum       =   $grupo_movil_ante; @endphp
                      @php $grupo_movil_ante      =   $grupo_movil; @endphp
              @endif
            @endif


            @if((int)($item->grupo_movil)%2==0)
                    @php $color_fila               =   'gris'; @endphp
            @else
                    @php $color_fila               =   'blanco'; @endphp
            @endif



            @if($sw_totales_plan == 1)
            <tr style="background: #f9d25e;">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class='center'></td>
                <td class='center'></td>
                <td class='center'></td>
                <td></td>
               

                <td style="text-align: right;">
                  <b style="text-align: right;">{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$grupo_movil_sum,'cantidad'),4,'.',',')}}</b>
                </td>
                <td>
                  <b style="text-align: right;">{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$grupo_movil_sum,'presentacion_producto'),4,'.',',')}}</b>
                </td>
                <td>
                  <b style="text-align: right;">{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$grupo_movil_sum,'kilos'),4,'.',',')}}</b>
                </td>
                <td>
                  <b style="text-align: right;">{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$grupo_movil_sum,'cantidad_sacos'),4,'.',',')}}</b>
                </td>


            </tr>
            @endif



            <tr>

              <td>{{$item->grupo_movil}} </td>
              <td>{{date_format(date_create($item->fecha_pedido), 'd-m-Y')}}</td>
              <td>{{date_format(date_create($item->fecha_entrega), 'd-m-Y')}}</td>
              <td>@if(trim($item->cliente_id) != '')
                    {{$funcion->nombre_cliente_despacho_cliente($item->cliente_id)}}
                  @endif</td>
              <td>{{$item->nro_orden_cen}}</td>

              <td>{{$item->producto->NOM_PRODUCTO}}</td>
              <td>{{$unidad_medida}}</td>

              <td class='center'>
                  {{number_format($item->cantidad, 2, '.', ',')}}
              </td>
              <td class='center'>
                  {{number_format($item->muestra, 2, '.', ',')}}
              </td>
              <td class='center'>
                  {{number_format($item->cantidad_atender, 2, '.', ',')}}
              </td>

              <td>
                {{$centro_origen->NOM_CENTRO}}
              </td>

              <td class='left'>
                  {{number_format($item->cantidad, 2, '.', ',')}}
              </td>
               <td class='left'>
                  {{number_format($item->presentacion_producto, 2, '.', ',')}}
              </td>
              <td style="text-align: right;">{{number_format($item->kilos, 4, '.', ',')}}</td>
              <td class='left'>{{number_format($item->cantidad_sacos, 4, '.', ',')}}</td>

            </tr>


            @if($index == (count($pedido->viewdetalleordendespachosinmuestra) - 1))
            <tr style="background: #f9d25e;">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class='center'></td>
                <td class='center'></td>
                <td class='center'></td>
                <td></td>
               

                <td style="text-align: right;">
                  <b style="text-align: right;">{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'cantidad'),4,'.',',')}}</b>
                </td>
                <td>
                  <b style="text-align: right;">{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'presentacion_producto'),4,'.',',')}}</b>
                </td>
                <td>
                  <b style="text-align: right;">{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'kilos'),4,'.',',')}}</b>
                </td>
                <td>
                  <b style="text-align: right;">{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'cantidad_sacos'),4,'.',',')}}</b>
                </td>


            </tr>
            @endif

        @endforeach
     




    </table>



      <table class="table demo" >

          <tr><th></th></tr>  
          <tr>
            <th>TIPO : </th>
            <th>{{$pedido->tipo_nombre}}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>  
          <tr>
            <th>LUGAR ENTREGA :</th>
            <th>{{$pedido->lugarentrega_nombre}}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>                            
      </table>


</html>
