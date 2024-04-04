
<input type="hidden" name="numero_mobil" id='numero_mobil' value='{{$numero_mobil}}'>
<input type="hidden" name="grupo" id='grupo' value='{{$grupo}}'>
<input type="hidden" name="correlativo" id='correlativo' value='{{$correlativo}}'>
<div class="main-content container-fluid" style = "padding: 0px;">
  <div class="row">
    <div class="col-sm-12" style = "padding-left: 0px;padding-right : 0px">
      <div class="panel panel-default panel-table">
        <div class="panel-heading">

          <div class='col-sm-3'>
            <b>Solicitud de pedido</b>
          </div>
          <div class='col-sm-4'>
             <span class="label">Pedido enviado a CHICLAYO</span>
          </div>

          <div class="tools dropdown show">
            <div class="dropdown">

              <span class="icon toggle-loading mdi mdi-save guardarcambios" style='color:#34a853;' title="Guardar cambios"></span>

              <span class="icon mdi mdi-more-vert dropdown-toggle" id="menudespacho" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></span>

              <ul class="dropdown-menu" aria-labelledby="menudespacho" style="margin: 7px -169px 0px;">

                <li><a href="#" class='crearmobil'>Crear mobil agrupados <span class="mdi mdi-check-square"></span></a> </li>
                <li><a href="#" class='crearmobilindividuales'>Crear mobil individuales <span class="mdi mdi-check-square"></span></a></li>
                <li><a href="#" class='crearmobil33palets'>Crear mobil 33 palets <span class="mdi mdi-check-circle"></span></a></li>
                <li><a href="#" class='cambiarfechaentrega'>Modificar fecha de entrega <span class="mdi mdi-check-circle"></span></a></li>
                <li><a href="#" class='cambiarfechaentregatotal'><b>Fecha de entrega total</b> <span class="mdi mdi-check-circle"></span></a></li>
              </ul>

            </div>
          </div>
        </div>
        <div class="panel-body">
          <div class="scroll_text_horizontal_padding" style = "padding: 0px !important;"> 

            <div style="width: 1680px;margin-bottom: 10px;">
              <table class="table table-pedidos-despachos" style='font-size: 0.85em;' id="tablepedidodespacho" >
                <thead>
                  <tr>
                    <th class='center' colspan="2">Mobil</th>
                    <th>Cliente</th>
                    <th>Producto</th>

                    <th class='center ocultar' >Muestra</th>
                    <th class='center ocultar'>Editar</th>

                    <th class='center'>Cantidad</th>
                    <th class='center'>Editar</th>
                    <th>Kilos</th>
                    <th>Sacos</th>
                    <th>Palet</th>
                    <th>Totales</th>

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

                    <th>Centro Origen</th>
                    <th>Empresa Transferencia</th>

                    <th class='center'>X</th>
                  </tr>
                </thead>
                <tbody>
                  @php $grupo         =   ""; @endphp
                  @php $grupo_movil   =   ""; @endphp

                  @foreach($array_detalle_producto as $index => $item)

                      @php
                        //agrupar por orden cen y producto
                        $array_respuesta   =   $funcion->funciones->crearrolwpan($item['grupo'],$index,$grupo);
                        $sw_crear          =   $array_respuesta['sw_crear'];
                        $grupo             =   $array_respuesta['grupo'];

                        //agrupar por grupo movil
                        $array_respuesta   =   $funcion->funciones->crearrolwpan($item['grupo_movil'],$index,$grupo_movil);
                        $sw_crear_movil    =   $array_respuesta['sw_crear'];
                        $grupo_movil       =   $array_respuesta['grupo'];

                      @endphp

                      <tr class='fila_pedido'
                          data_correlativo="{{$item['correlativo']}}"
                          data_producto="{{$item['producto_id']}}"
                          data_mobil="{{$item['grupo_movil']}}"
                          data_cantidad="{{$item['cantidad']}}"
                          fecha_entrega="{{$item['fecha_entrega']}}"
                          nombre_producto="{{$item['nombre_producto']}}"
                          centro_origen="{{$item['centro_atender_id']}}"
                          mobil_grupo="{{$item['grupo_movil']}}"
                        >


                        @if($sw_crear_movil == 1 and $item['grupo_movil'] <> '0') 
                          <td rowspan = "{{$item['grupo_orden_movil']}}" class='center fondogris'>

                            <div class="be-radio">
                              <input  type="radio"  name="rmobil" id="rad{{$item['grupo_movil']}}" 
                                      value="{{$item['grupo_movil']}}" 
                                      mobil_grupo_radio="{{$item['grupo_movil']}}">
                              <label for="rad{{$item['grupo_movil']}}"></label>
                            </div>

                          </td>
                        @else
                          @if($item['grupo_movil'] == '0') 
                            <td class='center'>
                            <div class="be-radio">
                              <input  type="radio"  name="rmobil" id="rad{{$item['grupo_movil']}}" 
                                      value="{{$item['grupo_movil']}}" 
                                      mobil_grupo_radio="{{$item['grupo_movil']}}">
                              <label for="rad{{$item['grupo_movil']}}"></label>
                            </div>
                            </td>
                          @endif
                        @endif



                        @if($sw_crear_movil == 1 and $item['grupo_movil'] <> '0') 
                          <td rowspan = "{{$item['grupo_orden_movil']}}" class='center fondogris'>
                            <b style="padding-right: 4px;">{{$item['grupo_movil']}}</b>
                          </td>
                        @else
                          @if($item['grupo_movil'] == '0') 
                            <td class='center'>
                              <b style="padding-right: 4px;">{{$item['grupo_movil']}}</b>
                            </td>
                          @endif
                        @endif


                        @if($sw_crear == 1) 
                        <td class="cell-detail relative" rowspan = "{{$item['grupo_orden']}}" > 
                          <span><b>Cliente</b> : {{$item['empresa_cliente_nombre']}}</span>
                          <span><b>Orden Cen</b> : {{$item['orden_cen']}}</span>


                          @if($item['tipo_grupo_oc'] == 'oc_grupo') 
                            <div class="text-center be-checkbox be-checkbox-sm has-primary absolute" style="bottom: 10px;right: 6px;" >
                              
                              <input  
                                type="checkbox"
                                class="{{$item['grupo']}}{{$item['orden_cen']}} input_asignar_gop"
                                id="{{$item['grupo']}}{{$item['orden_cen']}}" 
                                data_check_oc="{{$item['grupo']}}{{$item['orden_cen']}}">

                              <label  for="{{$item['grupo']}}{{$item['orden_cen']}}"
                                    data-atr = "ver"
                                    class = "checkbox checkbox_asignar_gop"                    
                                    name="{{$item['grupo']}}{{$item['orden_cen']}}"
                              ></label>

                            </div>
                          @endif


                        </td> 
                        @endif

                        <td class="cell-detail relative ">
                          <span>{{$item['nombre_producto']}}</span>
                          <span class="cell-detail-description-producto">
                          {{$item['nombre_unidad_medida']}} de  {{$item['presentacion_producto']}} kg 
                          </span>
                          <i class="mdi mdi-settings configuracion-despacho-cantidad"></i>
                        </td>
                        <td class='center ocultar'>
                            <b>{{number_format($item['muestra'], 2, '.', ',')}}</b>
                        </td>
                        <td class='ocultar'>
                            <input type="text"
                             id="muestra" 
                             name="muestra"
                             value="{{number_format($item['muestra'], 2, '.', ',')}}"
                             class="form-control input-sm dinero dineromuestra updatepricemuestrad"
                            >
                        </td>
                        <td class='center'>
                            <b>{{number_format($item['cantidad'], 2, '.', ',')}}</b>
                        </td>
                        <td>
                            <input type="text"
                             id="precio" 
                             name="precio"
                             value="{{number_format($item['cantidad'], 2, '.', ',')}}"
                             class="form-control input-sm dinero updatepriced"
                            >
                        </td>
                        <td class='center'>{{number_format($item['kilos'],4,'.',',')}}</td>
                        <td class='center'>{{number_format($item['cantidad_sacos'],4,'.',',')}}</td>
                        <td class='center'>{{number_format($item['palets'],4,'.',',')}}</td>

                        @if($sw_crear_movil == 1 and $item['grupo_movil'] <> '0') 
                          <td rowspan = "{{$item['grupo_orden_movil']}}" class='fondogris cell-detail'>
                              <span><b>Kilos</b> : 
                                {{number_format($funcion->funciones->totales_kilos_palets($array_detalle_producto,$item['grupo_movil'],'kilos'),4,'.',',')}}
                              </span>
                              <span><b>Palets</b> :
                                {{number_format($funcion->funciones->totales_kilos_palets($array_detalle_producto,$item['grupo_movil'],'palets'),4,'.',',')}}
                              </span>
                          </td>
                        @else
                          @if($item['grupo_movil'] == '0') 
                            <td class='cell-detail'>
                              <span><b>Kilos</b> : {{number_format($item['kilos'],4,'.',',')}}</span>
                              <span><b>Palets</b> : {{number_format($item['palets'],4,'.',',')}}</span>
                            </td>
                          @endif
                        @endif
                        <td>
                          <div class="text-center be-checkbox be-checkbox-sm has-primary">
                            <input  
                              type="checkbox"
                              class="{{$item['correlativo']}} input_asignar_lp @if($item['tipo_grupo_oc'] == 'oc_grupo') grupales @endif"
                              id="{{$item['correlativo']}}"
                              data_check_sel="{{$item['grupo']}}{{$item['orden_cen']}}"

                              @if($item["tipo_grupo_oc"] == "oc_grupo") disabled @endif>
                            <label  for="{{$item['correlativo']}}"
                                  data-atr = "ver"
                                  class = "checkbox checkbox_asignar_lp"                    
                                  name="{{$item['correlativo']}}"
                            ></label>
                          </div>
                        </td>
                        @if($sw_crear_movil == 1 and $item['grupo_movil'] <> '0') 
                          <td class="cell-detail" rowspan = "{{$item['grupo_orden_movil']}}">
                            <span><b>Pedido</b> : {{$item['fecha_pedido']}}</span>
                            <span><b>Entrega</b> : {{$item['fecha_entrega']}}</span>
                          </td>
                        @endif



                        @if($sw_crear_movil == 1 and $item['grupo_movil'] <> '0') 
                          <td rowspan = "{{$item['grupo_orden_movil']}}" class='center'>
                                {!! Form::select( 'centro_atender_id', $combo_lista_centros, array($item['centro_atender_id']),
                                [
                                  'class'       => 'select-centro' ,
                                  'id'          => 'centro_atender_id',
                                  'required'    => '',
                                  'data-aw'     => '1',
                                ]) !!}
                          </td>
                        @else
                          @if($item['grupo_movil'] == '0') 
                            <td class='center'>
                                {!! Form::select( 'centro_atender_id', $combo_lista_centros, array($item['centro_atender_id']),
                                [
                                  'class'       => 'select-centro' ,
                                  'id'          => 'centro_atender_id',
                                  'required'    => '',
                                  'data-aw'     => '1',
                                ]) !!}
                            </td>
                          @endif
                        @endif


                        @if($sw_crear_movil == 1 and $item['grupo_movil'] <> '0') 
                          <td rowspan = "{{$item['grupo_orden_movil']}}">
                                {{$item['empresa_atender_txt']}}
                          </td>
                        @else
                          @if($item['grupo_movil'] == '0') 
                            <td>
                                {{$item['empresa_atender_txt']}}
                            </td>
                          @endif
                        @endif

                        <td class='center'>
                          <span class="badge badge-danger cursor eliminar-producto-despacho">
                            <span class="mdi mdi-close" style='color: #fff;'></span>
                          </span>
                        </td>

                      </tr>
                  @endforeach
                </tbody>
              </table>

            </div>
          </div>

        </div>




      </div>
    </div>
    <br>
    <div class="col-xs-12" >

      <div class="col-xs-6">
        <div class="panel panel-default panel-table panel-muestra-pedido">
          <div class="panel-heading"><b>Muestras</b></div>
          <div class="panel-body">
            <table class="table tabla-producto-muestra" style="font-size: 0.85em;">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th class='center'>Muestra</th>
                  <th class='center'>Bolsas a sacos</th>
                  <th class='center'>Editar</th>
                </tr>
              </thead>
              <tbody>
                @foreach($array_detalle_producto_muestra as $index => $item)
                  <tr class='fila_pedido_muestras'
                            data_correlativo="{{$item['correlativo']}}"
                            data_producto="{{$item['producto_id']}}">

                    <td class="cell-detail relative">
                          <span>{{$item['nombre_producto']}}</span>
                          <span class="cell-detail-description-producto">
                          {{$item['nombre_unidad_medida']}} de  {{$item['presentacion_producto']}} kg 
                          </span>
                    </td>
                    <td class='center'>
                        <b>{{number_format($item['muestra'], 2, '.', ',')}}</b>
                    </td>
                    <td class='center'>
                        {{$funcion->funciones->data_producto($item['producto_id'])->CAN_BOLSA_SACO}}
                    </td>
            

                    <td class='center'>
                        <input type="text"
                         id="muestra" 
                         name="muestra"
                         value="{{number_format($item['muestra'], 2, '.', ',')}}"
                         class="form-control input-sm dinero dineromuestra updatepricemuestradseparado"
                        >
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>


      <div class="col-xs-6" style="text-align: right;">
          <form method="POST"  action="{{ url('/crear-orden-pedido-despacho/'.$opcion_id) }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
              {{ csrf_field() }}

              <div class="col-xs-12 col-sm-6 col-md-8 col-lg-10 cajareporte ajax_combo_tipo">
                @include('despacho.combo.combotipo')
              </div>
              <div class="col-xs-12 col-sm-6 col-md-8 col-lg-10 cajareporte ajax_combo_puntoentrega">
                @include('despacho.combo.combopuntoentrega')
              </div>

              <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12 cajareporte">
                <input type="hidden" name="array_detalle_producto" id='array_detalle_producto' value='{{json_encode($array_detalle_producto)}}'>
                <input type="hidden" name="ind_plantilla" id='ind_plantilla' value=''>
                <input type="hidden" name="array_detalle_producto_muestra" id='array_detalle_producto_muestra' value='{{json_encode($array_detalle_producto_muestra)}}'>
                <button type="submit" class="btn btn-space btn-primary btn-guardar-pedido">Guardar</button>

              </div>
          </form>
      </div>

    </div>

  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('.dinero').inputmask({ 'alias': 'numeric', 
    'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
    'digitsOptional': false, 
    'prefix': '', 
    'placeholder': '0'});
    $("#plantilla_valor").change();
  });
</script> 

