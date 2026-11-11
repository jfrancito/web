  <input type="hidden" id='count_servicio' value='{{$count_servicio}}'>
  <div style="margin-bottom: 10px;width: 1900px;">
    <table class="table listaservicios" style='font-size: 11px;' id="" >
    <thead>
      <tr>
        <th class='center'>X</th>
        <th>Estado</th>
        <th>Codigo</th>
        <th>Servicio</th>
        <th>Costo</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Ind IGV</th>        
        <th>Subtotal</th>
        <th>Igv</th>
        <th>Total</th>
        <th>Empresa Servicio</th>
        <th>Cuenta</th>
        <th>RC</th>
        <th>Tipo Documento</th>
      </tr>
    </thead>
    <tbody>
    @foreach($lista_de_servicios as $index => $item)
        @php
          $lista_servicio       =   $funcion->funciones->lista_producto($item['servicio']);
        @endphp
        @while ($row = $lista_servicio->fetch())

          @php
            $tipo_documento_id    =   $funcion->funciones->tipo_documento_servicio($row['COD_CATEGORIA_SERVICIO']);
            // @DPZ1
            $ind_IGV              =   $funcion->funciones->configuracion_producto($item['servicio']);
          @endphp

          <tr class='fila_servicio'>
              <td class='center'>
                <span class="badge badge-danger cursor eliminar-servicio-despacho">
                  <span class="mdi mdi-close" style='color: #fff;'></span>
                </span>
              </td>
              <td class='nombre_estado'>GENERADO</td>
              <td class='producto_id'>{{$row['COD_PRODUCTO']}}</td>
              <td class='nombre_porducto_id'>{{$row['NOM_PRODUCTO']}}</td>
              <td class='costo'>0.00</td>

              <td>
                <input type="text"
                 name="catidad_servicio"
                 value="0.0000"
                 class="form-control input-sm dinero_4_digitos update_price_cantidad_servicio"
                >
              </td>
              <td>
                <input type="text"
                 name="precio_servicio"
                 value="0.0000"
                 class="form-control input-sm dinero_4_digitos update_price_precio_servicio"
                >
              </td>

              <td>
                  
                <div class="text-center be-checkbox be-checkbox-sm has-primary">
                  <input  
                    type="checkbox"
                    class="{{$item['servicio']}} input_asignar_ser"
                    id="{{$item['servicio']}}"
                    @if ($ind_IGV == 1) checked @endif
                    >
                  <label  for="{{$item['servicio']}}"
                        data-atr = "ver"
                        class = "checkbox checkbox_asignar_ser"
                        style="margin-top: 0px;"                    
                        name="{{$item['servicio']}}"
                  ></label>
                </div>

              </td>

              <td class='subtotal'>0.00</td>
              <td class='igv'>0.00</td>

              <td>
                <input type="text"
                 name="total_servicio"
                 value="0.0000"
                 class="form-control input-sm dinero_4_digitos update_price_total_servicio"
                >
              </td>
              <td>
                {!! Form::select( 'empresa_servicio'.$item['servicio'], $combo_empresas_servicios, array("VACIO"),
                                  [
                                    'class'       => 'select2 form-control control input-xs empresa_servicio_select' ,
                                    'id'          => 'empresa_servicio'.$item['servicio'],
                                    'data-aw'     => '2',
                                  ]) !!}
              </td>
              <td class='ajax_cuenta_servicio'>
                @include('despacho.ajax.acombocuentaservicio')
              </td>
              <td class='contrato_cuenta'></td>
              <td class='tipo_documento_id'>{{$tipo_documento_id}}</td> 
          </tr>                    
        @endwhile
    @endforeach
    </tbody>
  </table> 
  </div>

@if(isset($ajax))
  <script type="text/javascript">
    $('.update_price_cantidad_servicio').val({{$calcula_cantidad_peso}});
    $(document).ready(function(){
       App.dataTables();

      $('.dinero_4_digitos').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});
      
    });

      $('.empresa_servicio_select').select2({
          // Activamos la opcion "Tags" del plugin
          placeholder: 'Seleccione una empresa',
          language: "es",
          tags: true,
          tokenSeparators: [','],
          ajax: {
              dataType: 'json',
              url: '{{url("buscarempresaserviciodespacho")}}',
              delay: 100,
              data: function(params) {
                  return {
                      term: params.term
                  }
              },
              processResults: function (data, page) {
                return {
                  results: data
                };
              },
          }
      });

  </script> 
@endif
