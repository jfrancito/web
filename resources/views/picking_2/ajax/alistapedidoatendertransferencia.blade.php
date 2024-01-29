<!--@DPZ0002 div class="scroll_text_horizontal_padding" style = "padding: 0px !important;">--> 
<div style = "padding: 0px !important;"> 
  <!--@DPZ0002-->
  <div style="margin-bottom: 10px;" >
    <table class="table table-pedidos-despachos" style='font-size: 0.88em;' id="tablepedidodespacho" >
    <thead>
      <tr>
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
        <th>Fechas</th>
        <th width="250px">Cliente</th>
        <th>Producto</th>
        <th>Pedido</th>
        <th>Almacen</th>
        <th>Lote</th>
        <th>Stock</th>
        <th class='center'>Atender</th>
        <th>Transferencia PT / Orden Salida</th>
        <th>Kilos</th>
      </tr>
    </thead>
    <tbody>

    @php $grupo         =   ""; @endphp
    @php $grupo_movil   =   ""; @endphp
    @php $grupo_guia    =   ""; @endphp
    @php $conteo_mobil  =   0; @endphp
    @php $grupo_movil_c =   0; @endphp


    @foreach($picking->pickingdetalle as $index => $item)
       @php

        $unidad_medida            =   $funcion->funciones->data_categoria($item->producto->COD_CATEGORIA_UNIDAD_MEDIDA)->NOM_CATEGORIA;

        $color_stock              =   '';
        $background_stock         =   '';
        $sw_transferencia         =   0;
        $check_disableb           =   '';
        $color_tr                 =   '';
        $disabled_transferencia   =   '';
        $disabled_guia            =   '';
        $disabled_origen          =   '';
        $sw_nocarga_lotes         =   '0';
        $cliente                  =   '';
        $total_item               =   $item->cantidad + $item->cantidad_excedente;  

        
      @endphp

      @if(trim($item->orden_id) <> "") 
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
          $cliente                  =   '';
        @endphp
      @endif


      @if(Session::get('centros')->COD_CENTRO <> $picking->centro_origen_id) 
        @php 
          $sw_transferencia         =   1;
          $check_disableb           =   'check_disableb';
          $color_tr                 =   'label-origen';
          $disabled_origen          =   'disabled';
          $disabled_guia            =   'disabled';
          $almacen_id_sel           =   '';
          $combo_almacen_lote       =    array();
          $almacen_lote_group_id    =   '';
          $stock_neto               =   0.0;
          $stock_fisico             =   0.0;
          $costo                    =   0.0;
          $sw_nocarga_lotes         =   '1';
          $cliente                  =   '';
        @endphp
      @endif

      @if($sw_nocarga_lotes == "0") 
        @php
          $almacen_id_sel           =   $funcion->funciones->select_almacen_unidad_centro($unidad_medida,$ultimo_almacen_id);
          $combo_almacen_lote       =   $funcion->funciones->combo_almacen_lote($item->producto_id,$almacen_id_sel);
          $almacen_lote_group_id    =   $funcion->funciones->select_almacen_lote_group($item->producto_id,$almacen_id_sel,$total_item);
          $stock_neto               =   $funcion->funciones->select_data_almacen_lote_group($item->producto_id,$almacen_id_sel,$almacen_lote_group_id,'STK_NETO');
          $stock_fisico             =   $funcion->funciones->select_data_almacen_lote_group($item->producto_id,$almacen_id_sel,$almacen_lote_group_id,'CAN_FIN_MAT');
          $costo                    =   $funcion->funciones->select_data_almacen_lote_group($item->producto_id,$almacen_id_sel,$almacen_lote_group_id,'CAN_COSTO'); 
        
         if(trim($item->cliente_id) != '')            
            $cliente            = $funcion->funciones->nombre_cliente_despacho_cliente($item->cliente_id);
             
        @endphp
      @endif

      <tr
        class='fila_pedido {{$color_tr}}'
        data_detalle_orden_despacho= '{{$item->picking_id."-"}}{{$item->transferencia_id."-"}}{{$item->producto_id}}'
        data_producto='{{$item->producto_id}}'
        nombre_producto='{{$item->producto->NOM_PRODUCTO}}'
        unidad_medida='{{$unidad_medida}}'
        cod_orden='{{$item->transferencia_id}}'
        fec_orden='{{date_format(date_create($item->fecha_entrega), "d-m-Y")}}'
        cliente='{{$cliente}}'
      >
        <td>
          <div class="text-center be-checkbox be-checkbox-sm has-primary">
            <input  
              type="checkbox"
              class="{{$item->picking_id}}{{$item->transferencia_id}}{{$item->producto_id}} input_asignar_lp {{$check_disableb}}"
              id="{{$item->picking_id}}{{$item->transferencia_id}}{{$item->producto_id}}"
              @if($sw_transferencia == 1) disabled @endif
              >
            <label  for="{{$item->picking_id}}{{$item->transferencia_id}}{{$item->producto_id}}"
                  data-atr = "ver"
                  class = "checkbox checkbox_asignar_lp"                    
                  name="{{$item->picking_id}}{{$item->transferencia_id}}{{$item->producto_id}}"
                  style = 'margin-top:0px;'
            ></label>
          </div>
        </td>
        <td class="cell-detail">
          <span><b>Picking</b> : {{date_format(date_create($picking->fecha_picking), 'd-m-Y')}}</span> 
          <span><b>Entrega</b> : {{date_format(date_create($item->fecha_entrega), 'd-m-Y')}} </span>
        </td>

        <td class="cell-detail">
          <span><b>Cliente</b> : {{$cliente}} </span> 
          <span>
            @if(strlen(trim($item->transferencia_id)) == 16)
              <b>Orden Venta</b> :
            @else
              <b>Transferencia</b> :
            @endif
            {{$item->transferencia_id}}
          </span>
        </td>

        <td class="cell-detail">
            <span>{{$item->producto_nombre}}</span>
            <span class="cell-detail-description-producto">
            {{$unidad_medida}} de  {{$item->producto_peso}} kg
            </span>
        </td>
        
        <td class="cell-detail">
          <span><b>Cantidad</b> : {{number_format($item->cantidad, 2, '.', ',') }} </span> 
          <span class="cell-detail-description-producto"><b>Excedente</b> : {{number_format($item->cantidad_excedente, 2, '.', ',') }} </span> 
        </td>

        <td>
          {!! Form::select( 'almacen_id', $combo_almacen, array($almacen_id_sel),
                            [
                              'class'       => 'select-despacho select_tabla_almacen_id' ,
                              'id'          => 'almacen_id',
                              'required'    => '',
                              'data-aw'     => '1',
                              $disabled_origen => $disabled_origen,
                              $disabled_transferencia => $disabled_origen
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
                                $disabled_transferencia
                              ]) !!}
        </td>

        <td class="cell-detail ajax_stock_almacen_lote">
           @include('despacho.ajax.astockalmacenlote')
        </td>

        <td>
            <input type="text"
             name="catidad_atender"
             value="{{number_format($total_item, 2, '.', ',')}}"
             class="form-control input-sm dinero updatepriceatender {{$color_stock}}"
             disabled
            >
        </td>
        <td class='center'>
          <span>{{$item->orden_id}}</span>
        </td>

        <td>{{number_format($item->peso_total, 4, '.', ',')}}</td>
      </tr>       
        @php $grupo_movil_c =   0; @endphp
        @php $conteo_mobil  =   0; @endphp
      
    @endforeach
    </tbody>
  </table> 
  </div>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();

      $('.scroll_text_horizontal').scrollLeft(365);

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