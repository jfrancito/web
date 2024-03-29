<div class="scroll_text_horizontal" style = "padding: 0px !important;"> 
  <div style="width: 1150px;margin-bottom: 10px;" >
    <table class="table table-pedidos-despachos listatabla" style='font-size: 0.88em;' id="tablepedidodespacho" >
    <thead>
      <tr>
        <th>Fechas</th>
        <th width="250px">Cliente</th>
        <th class='center'>Mobil</th>
        <th>Producto</th>
      	<th>
	        <div class="text-center be-checkbox be-checkbox-sm has-primary">
	          <input  type="checkbox"
	                  class="todo_asignar input_asignar"
	                  id="todo_asignar"
	                  data_despacho_id = '{{$ordendespacho->id}}'
	                  data_detalle_id = 'todo'
	                  @if($ind_todo == 1) checked @endif
	          >
	          <label  for="todo_asignar"
	                  data-atr = "todas_asignar"
	                  class = "checkbox_asignar"                    
	                  name="todo_asignar"
	                  data_detalle_id = 'todo'
	                  data_despacho_id = '{{$ordendespacho->id}}'
	            ></label>
	        </div>
      	</th>
        <th>Cantidad</th>
        <th>Kilos</th>
        <th>Sacos</th>
        <th>Palet</th>
        <th>Total Palet</th>
      </tr>
    </thead>
    <tbody>

    @php $grupo         =   ""; @endphp
    @php $grupo_movil   =   ""; @endphp
    @php $grupo_guia    =   ""; @endphp
    @php $conteo_mobil  =   0; @endphp
    @php $grupo_movil_c =   0; @endphp

    @foreach($ordendespacho->viewdetalleordendespacho as $index => $item)
      @php

        //agrupar por grupo movil
        $almacen_lote_group_id    =   array();
        $array_respuesta          =   $funcion->funciones->crearrolwpan($item->grupo_movil,$index,$grupo_movil);
        $sw_crear_movil           =   $array_respuesta['sw_crear'];
        $grupo_movil              =   $array_respuesta['grupo'];

        //agrupar por grupo guia
        $array_respuesta          =   $funcion->funciones->crearrolwpan($item->grupo_guia.$item->grupo_movil,$index,$grupo_guia);
        $sw_crear_guia            =   $array_respuesta['sw_crear'];
        $grupo_guia               =   $array_respuesta['grupo'];

        $mobil_cero               =   $funcion->funciones->cantidad_mobil_cero($ordendespacho->id);
        $unidad_medida            =   $funcion->funciones->data_categoria($item->producto->COD_CATEGORIA_UNIDAD_MEDIDA)->NOM_CATEGORIA;
        $centro_origen            =   $funcion->funciones->data_centro($item->centro_atender_id);
        $rowspan_mobil_producto   =   $funcion->funciones->rowspan_mobil_producto($item->ordendespacho_id,$item->grupo_movil);

        $color_stock              =   '';
        $background_stock         =   '';
        $sw_transferencia         =   0;
        $check_disableb           =   '';
        $color_tr                 =   '';
        $disabled_transferencia   =   '';
        $disabled_guia            =   '';
        $disabled_origen          =   '';
        $sw_nocarga_lotes         =   '0';
        $stock_neto               =   0.0;
      @endphp


      @if($item->documento_guia_id <> "") 
        @php 
          $disabled_guia   =   'disabled';
        @endphp
      @endif


      @if($item->orden_transferencia_id <> "") 
        @php 
          $sw_transferencia         =   1;
          $check_disableb           =   'check_disableb';
          $color_tr                 =   'label-transferenciapt';
          $disabled_transferencia   =   'disabled';
          $almacen_id_sel           =   '';
          $combo_almacen_lote       =    array();
          $almacen_lote_group_id    =   '';
          $stock_neto               =   0.0;
          $stock_fisico             =   0.0;
          $costo                    =   0.0;
          $sw_nocarga_lotes         =   '1';
        @endphp
      @endif

      @if(Session::get('centros')->COD_CENTRO <> $item->centro_atender_id) 
        @php 
          $sw_transferencia         =   1;
          $check_disableb           =   'check_disableb';
          $disabled_origen          =   'disabled';
          $disabled_guia            =   'disabled';
          $almacen_id_sel           =   '';
          $combo_almacen_lote       =    array();
          $almacen_lote_group_id    =   '';
          $stock_neto               =   0.0;
          $stock_fisico             =   0.0;
          $costo                    =   0.0;
          $sw_nocarga_lotes         =   '1';
        @endphp
      @endif

      @if((float)$stock_neto < (float)$item['cantidad_atender'] and $sw_transferencia == 0) 
        @php 
          $color_stock          =   'color_rojo';
          $background_stock     =   'background_rojo';
        @endphp
      @endif
      @if((int)$item->grupo_movil > 0 or $grupo_movil_c = $item->grupo_movil)
        @php 
          $conteo_mobil      =   $conteo_mobil + 1 + $item->restar_grupo_orden_movil;
          $grupo_movil_c     =   $item->grupo_movil;
        @endphp
      @else
        @if($item->grupo_movil == '0' or $grupo_movil_c = $mobil_cero) 
          @php 
            $conteo_mobil      =   $conteo_mobil + 1 + $item->restar_grupo_orden_movil;
            $grupo_movil_c     =   $mobil_cero;
          @endphp
        @endif
      @endif

      <tr
        class='fila_pedido {{$color_tr}}'
        data_detalle_orden_despacho='{{substr($item->id, 0, -1)}}'
        data_producto='{{$item->producto_id}}'
        nombre_producto='{{$item->producto->NOM_PRODUCTO}}'
        unidad_medida='{{$unidad_medida}}'
        mobil_grupo='{{$item->grupo_movil}}'
        centro_origen_id='{{$item->centro_atender_id}}'
        guia_remision_id="{{$item->documento_guia_id}}"
        nro_serie="{{$item->nro_serie}}"
        nro_documento="{{$item->nro_documento}}"
        orden_transferencia_id="{{$item->orden_transferencia_id}}"
      >
          <td class="cell-detail">
            <span><b>Pedido</b> : {{date_format(date_create($item->fecha_pedido), 'd-m-Y')}}</span> 
            <span><b>Entrega</b> : {{date_format(date_create($item->fecha_entrega), 'd-m-Y')}} </span>
          </td>
          <td class="cell-detail">
            <span><b>Cliente</b> : 
              @if(trim($item->cliente_id) != '')
                {{$funcion->funciones->nombre_cliente_despacho_cliente($item->cliente_id)}}
              @endif
            </span> 
            <span><b>Orden Cen</b> : {{substr($item->nro_orden_cen, 0, -1)}}</span>
          </td>



          @if($sw_crear_movil == 1 and $item->grupo_movil <> '0') 
            <td rowspan = "{{$item->grupo_orden_movil - $rowspan_mobil_producto}}" class='fondogris' >
              <b style="padding-right: 4px;">{{$item->grupo_movil}}</b>

              <div class="text-center be-checkbox be-checkbox-sm has-primary">
                <input
                  type="checkbox"
                    class="{{$index}}{{$item->grupo_movil}} input_asignar_grupo"
                    id="{{$index}}{{$item->grupo_movil}}"
                    data_grupo = '{{$item->grupo_movil}}'
                    data_despacho_id = '{{$ordendespacho->id}}'
                    @if($item->ind_segmento == '1') checked @endif
                  >
                <label  for="{{$index}}{{$item->grupo_movil}}"
                      data-atr = "ver"
                      class = "checkbox checkbox_asignar_grupo"                    
                      name="{{$index}}{{$item->grupo_movil}}"
                      data_grupo = '{{$item->grupo_movil}}'
                      data_despacho_id = '{{$ordendespacho->id}}'
                ></label>
              </div>


            </td>
          @else
            @if($item->grupo_movil == '0') 
              <td>
                <b style="padding-right: 4px;">{{$item->grupo_movil}}</b>
              </td>
            @endif
          @endif




          <td class="cell-detail">
            <span>{{$item->producto->NOM_PRODUCTO}}</span>
            <span class="cell-detail-description-producto">
            {{$unidad_medida}} de  {{$item->producto->CAN_PESO_SACO}} kg (group {{$item->restar_grupo_orden_movil+1}})
            </span>
          </td>


          <td>
              <div class="text-center be-checkbox be-checkbox-sm has-primary">
                <input
                	type="checkbox"
                  	class="{{$index}}{{$item->id_detalle}} input_asignar"
                  	id="{{$index}}{{$item->id_detalle}}"
                  	data_detalle_id = '{{$item->id_detalle}}'
                  	data_despacho_id = '{{$ordendespacho->id}}'
                  	@if($item->ind_segmento == '1') checked @endif
                  >
                <label  for="{{$index}}{{$item->id_detalle}}"
                      data-atr = "ver"
                      class = "checkbox checkbox_asignar"                    
                      name="{{$index}}{{$item->id_detalle}}"
                      data_detalle_id = '{{$item->id_detalle}}'
                      data_despacho_id = '{{$ordendespacho->id}}'
                ></label>
              </div>

          </td>


          <td class='center'>
              {{number_format($item['cantidad'], 2, '.', ',')}}
          </td>
          <td>{{number_format($item->kilos, 4, '.', ',')}}</td>
          <td>{{number_format($item->cantidad_sacos, 4, '.', ',')}}</td>
          <td>{{number_format($item->palets, 4, '.', ',')}}</td>

          <td>{{$item->segmento_palets}}</td>

      </tr>
      @if(
          ($conteo_mobil == $mobil_cero  and $item->grupo_orden_movil == 0)
          or
          ($conteo_mobil == $item->grupo_orden_movil and $item->grupo_orden_movil >= 0)
         ) 
      <tr>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>          
          <td class='despacho_totales'>
            {{number_format($funcion->funciones->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'kilos'),4,'.',',')}}
          </td>
          <td class='despacho_totales'>
            {{number_format($funcion->funciones->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'cantidad_sacos'),4,'.',',')}}
          </td>
          <td class='despacho_totales'>
            {{number_format($funcion->funciones->totales_kilos_palets_tabla($item->ordendespacho_id,$item->grupo_movil,'palets'),4,'.',',')}}
          </td>
          <td class='despacho_totales'></td>

      </tr>
        @php $grupo_movil_c =   0; @endphp
        @php $conteo_mobil  =   0; @endphp
      @endif
    @endforeach
    </tbody>
  </table> 
  </div>

	</div>
</div>