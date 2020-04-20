<div class="scroll_text_horizontal" style = "padding: 0px !important;"> 

  <div style="width: 1940px;margin-bottom: 10px;" >
    <table class="table table-pedidos-despachos" style='font-size: 0.88em;' id="tablepedidodespacho" >
    <thead>
      <tr>

        <th>Fechas</th>
        <th width="250px">Cliente</th>
        <th class='center'>M</th>
        <th>Producto</th>
        <th>Pedido</th>

        <th>Almacen</th>
        <th>Lote</th>
        <th>Stock</th>
        <th>Atender</th>
        <th class='center'>Editar</th>

        <th>
          <div class="text-center be-checkbox be-checkbox-sm has-primary">
            <input  type="checkbox"
                    class="todo_asignar input_asignar_lp"
                    id="todo_asignar"
            >
            <label  for="todo_asignar"
                    data-atr = "todas_asignar"
                    class = "checkbox_asignar_lp"                    
                    name="todo_asignar"
              ></label>
          </div>
        </th>

        <th class='center'>Guia Remitente</th>

        <th>Origen</th>
        <th>Transferencia PT</th>



        <th>Kilos</th>
        <th>Sacos</th>
        <th>Palet</th>
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

        $almacen_id_sel           =   $funcion->funciones->select_almacen_unidad_centro($unidad_medida,$ultimo_almacen_id);
        $combo_almacen_lote       =   $funcion->funciones->combo_almacen_lote($item->producto_id,$almacen_id_sel);
        $almacen_lote_group_id    =   $funcion->funciones->select_almacen_lote_group($item->producto_id,$almacen_id_sel,$item['cantidad_atender']);

        $stock_neto               =   $funcion->funciones->select_data_almacen_lote_group($item->producto_id,$almacen_id_sel,$almacen_lote_group_id,'STK_NETO');
        $stock_fisico             =   $funcion->funciones->select_data_almacen_lote_group($item->producto_id,$almacen_id_sel,$almacen_lote_group_id,'CAN_FIN_MAT');
        $costo                    =   $funcion->funciones->select_data_almacen_lote_group($item->producto_id,$almacen_id_sel,$almacen_lote_group_id,'CAN_COSTO');
        $centro_origen            =   $funcion->funciones->data_centro($item->centro_atender_id);
        $rowspan_mobil_producto   =   $funcion->funciones->rowspan_mobil_producto($item->ordendespacho_id,$item->grupo_movil);


        $color_stock              =   '';
        $background_stock         =   '';
        $sw_transferencia         =   0;
        $check_disableb           =   '';
        $color_tr                 =   '';

      @endphp


      @if((float)$stock_neto < (float)$item['cantidad_atender']) 
        @php 
          $color_stock          =   'color_rojo';
          $background_stock     =   'background_rojo';
        @endphp
      @endif

      @if($item->orden_transferencia_id <> "") 
        @php 
          $sw_transferencia     =   1;
          $check_disableb       =   'check_disableb';
          $color_tr             =   'label-transferenciapt'
        @endphp
      @endif

      @if(Session::get('centros')->COD_CENTRO <> $item->centro_atender_id) 
        @php 
          $sw_transferencia     =   1;
          $check_disableb       =   'check_disableb';
          $color_tr             =   'label-origen'
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

      >
          <td class="cell-detail">
            <span><b>Pedido</b> : {{date_format(date_create($item->fecha_pedido), 'd-m-Y')}} </span> 
            <span><b>Entrega</b> : {{date_format(date_create($item->fecha_entrega), 'd-m-Y')}} </span>
          </td>
          <td class="cell-detail">
            <span><b>Cliente</b> : 
              @if(trim($item->cliente_id) != '')
                {{$funcion->funciones->data_cliente_cliente_id($item->cliente_id)->NOM_EMPR}}
              @endif
            </span> 
            <span><b>Orden Cen</b> : {{substr($item->nro_orden_cen, 0, -1)}}</span>
          </td>

          @if($sw_crear_movil == 1 and $item->grupo_movil <> '0') 
            <td rowspan = "{{$item->grupo_orden_movil - $rowspan_mobil_producto}}" class='center fondogris'>
              <b>{{$item->grupo_movil}}</b>
            </td>
          @else
            @if($item->grupo_movil == '0') 
              <td class='center'>
                <b>{{$item->grupo_movil}}</b>
              </td>
            @endif
          @endif

          <td class="cell-detail">
            <span>{{$item->producto->NOM_PRODUCTO}}</span>
            <span class="cell-detail-description-producto">
            {{$unidad_medida}} de  {{$item->producto->CAN_PESO_SACO}} kg (group {{$item->restar_grupo_orden_movil+1}})
            </span>
          </td>
          <td class="cell-detail">
            <span><b>Cantidad</b> : {{number_format($item->cantidad, 2, '.', ',')}} </span> 
            <span><b>Muestra</b> : {{number_format($item->muestra, 2, '.', ',')}} </span>
          </td>
          <td>
              {!! Form::select( 'almacen_id', $combo_almacen, array($almacen_id_sel),
                                [
                                  'class'       => 'select-despacho select_tabla_almacen_id' ,
                                  'id'          => 'almacen_id',
                                  'required'    => '',
                                  'data-aw'     => '1',
                                ]) !!}
          </td>
          <td class='ajax_combo_lote'>

              {!! Form::select( 'lote_id', $combo_almacen_lote, $almacen_lote_group_id,
                                [
                                  'class'       => 'select-despacho select_tabla_lote_id' ,
                                  'id'          => 'lote_id',
                                  'required'    => '',
                                  'multiple'    => 'multiple',
                                  'data-aw'     => '1',
                                ]) !!}

          </td>


          <td class="cell-detail ajax_stock_almacen_lote">
            @include('despacho.ajax.astockalmacenlote')
          </td>

          <td class='center td_cantidad_atender {{$background_stock}}'>
              {{number_format($item['cantidad_atender'], 2, '.', ',')}}
          </td>
          <td>
              <input type="text"
               name="catidad_atender"
               value="{{number_format($item['cantidad_atender'], 2, '.', ',')}}"
               class="form-control input-sm dinero updatepriceatender {{$color_stock}}"
               @if($sw_transferencia == 1) disabled @endif
              >
          </td>
          <td>
            <div class="text-center be-checkbox be-checkbox-sm has-primary">
              <input  
                type="checkbox"
                class="{{$item->id}} input_asignar_lp {{$check_disableb}}"
                id="{{$item->id}}"
                @if($sw_transferencia == 1) disabled @endif
                >
              <label  for="{{$item->id}}"
                    data-atr = "ver"
                    class = "checkbox checkbox_asignar_lp"                    
                    name="{{$item->id}}"
                    style = 'margin-top:0px;'
              ></label>
            </div>
          </td>


          @if($sw_crear_guia == 1 and $item->grupo_guia <> '0') 
            <td rowspan = "{{$item->grupo_orden_guia}}">
              @include('despacho.ajax.aserienrodocumento')
            </td>
          @else
            @if($item->grupo_guia == '0') 
              <td class='center'>
                @include('despacho.ajax.aserienrodocumento')
              </td>
            @endif
          @endif

          <td>
            {{$centro_origen->NOM_CENTRO}}
          </td>
          <td>
            {{$item->orden_transferencia_id}}
          </td>
          <td>{{number_format($item->kilos, 4, '.', ',')}}</td>
          <td>{{number_format($item->cantidad_sacos, 4, '.', ',')}}</td>
          <td>{{number_format($item->palets, 4, '.', ',')}}</td>

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
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
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

      </tr>
        @php $grupo_movil_c =   0; @endphp
        @php $conteo_mobil  =   0; @endphp
      @endif
    @endforeach
    </tbody>
  </table> 
  </div>
</div>
@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();

      $('.scroll_text_horizontal').scrollLeft(368);

      $('.dinero').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});

      $('.dineronrodoc').inputmask({ 'alias': 'numeric', 
      'groupSeparator': '', 'autoGroup': true, 'digits': 0, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': ''});
      
      $('.select_tabla_lote_id').multiselect({
            buttonWidth: '100px',
            numberDisplayed: -1
      });
      
    });
  </script> 
@endif