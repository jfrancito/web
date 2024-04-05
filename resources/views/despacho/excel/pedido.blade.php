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
          <th class= 'center tablaho'>Alias</th>


          <th class= 'center tablaho'>Producto</th>
          <th class= 'center tablaho'>Unidad medida</th>
          <th class= 'center tablaho' >Cantidad</th>
          <th class= 'center tablaho' >Muestra</th>
          <th class= 'center tablaho' >Atender</th>

          <th class= 'center tablaho' >Origen</th>  
          <th class= 'center tablaho' >Kilos</th>
          <th class= 'center tablaho' >Sacos</th>
          <th class= 'center tablaho' >Palet</th>

          <th class= 'center tablaho'>Mobil</th>
          <th class= 'center tablaho' >Total Kilos</th>
          <th class= 'center tablaho' >Total Sacos</th>
          <th class= 'center tablaho' >Total Palets</th>

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
                $color_fila               =   '';
            @endphp


            @if((int)($item->grupo_movil)%2==0)
                    @php $color_fila               =   'gris'; @endphp
            @else
                    @php $color_fila               =   'blanco'; @endphp
            @endif

            <tr class='{{$color_fila}}'>

              <td>{{$item->grupo_movil}}</td>
              <td>{{date_format(date_create($item->fecha_pedido), 'd-m-Y')}}</td>
              <td>{{date_format(date_create($item->fecha_entrega), 'd-m-Y')}}</td>
              <td>@if(trim($item->cliente_id) != '')
                    {{$funcion->nombre_cliente_despacho_cliente($item->cliente_id)}}
                  @endif</td>
              <td>{{substr($item->nro_orden_cen, 0, -1)}}</td>
              <td>{{$item->alias_nombre}}</td>
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
              <td>{{number_format($item->kilos, 4, '.', ',')}}</td>
              <td>{{number_format($item->cantidad_sacos, 4, '.', ',')}}</td>
              <td>{{number_format($item->palets, 4, '.', ',')}}</td>

              @if($sw_crear_movil == 1 and $item->grupo_movil <> '0') 
                <td rowspan = "{{$item->grupo_orden_movil - $rowspan_mobil_producto}}" class='fondogris vcent'>
                    <b>{{$item->grupo_movil}}</b>
                </td>
              @endif

              @if($sw_crear_movil == 1 and $item->grupo_movil <> '0') 
                <td rowspan = "{{$item->grupo_orden_movil - $rowspan_mobil_producto}}" class='fondogris vcent'>
                    <b>{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'kilos'),4,'.',',')}}</b>
                </td>
              @endif
              @if($sw_crear_movil == 1 and $item->grupo_movil <> '0') 
                <td rowspan = "{{$item->grupo_orden_movil - $rowspan_mobil_producto}}" class='fondogris vcent'>
                    <b>{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'cantidad_sacos'),4,'.',',')}}</b>
                </td>
              @endif

              @if($sw_crear_movil == 1 and $item->grupo_movil <> '0') 
                <td rowspan = "{{$item->grupo_orden_movil - $rowspan_mobil_producto}}" class='fondogris vcent'>
                    <b>{{number_format($funcion->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'palets'),4,'.',',')}}</b>
                </td>
              @endif
            </tr>
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



      <table class="table demo" >
          <tr><th></th></tr>  
          <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Muestras</th>
          </tr>                            
      </table>


      <table>
          <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th class= 'center tablaho'>Producto</th>
              <th class= 'center tablaho'>Unidad de medida</th>
              <th class= 'center tablaho'>muestra</th>
          </tr>
          @foreach($muestras as $index => $item)
              <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>{{$item->producto->NOM_PRODUCTO}}</td>
                  <td>{{$item->producto->unidadmedida->NOM_CATEGORIA}}</td>
                  <td>{{$item->muestra}}</td>
              </tr>
          @endforeach
      </table>

</html>
