
<table id="tablereglamasivo" class="table table-striped table-hover table-fw-widget listatabla">
  <thead>

    <tr> 
      <th class= 'tabladp'>CLIENTE</th>
      <th class= 'tabladp'>RESPONSABLE</th>
      <th class= 'tabladp'>CANAL</th>
      <th class= 'tabladp'>SUB CANAL</th>
      <th class= 'tabladp'>PRODUCTO</th>
      <th class= 'center tablamar' width="120px;">REGLA</th> 

      <th>
        <div class="text-center be-checkbox be-checkbox-sm has-danger">
          <input  type="checkbox"
                  class="todo_eliminar input_eliminar"
                  id="todo_eliminar"
          >
          <label  for="todo_eliminar"
                  data-atr = "todas_eliminar"
                  class = "checkbox_eliminar"                    
                  name="todo_eliminar"
            ></label>
        </div>
      </th>

      <th>
        <div class="text-center be-checkbox be-checkbox-sm has-primary">
          <input  type="checkbox"
                  class="todo_asignar input_asignar"
                  id="todo_asignar"
          >
          <label  for="todo_asignar"
                  data-atr = "todas_asignar"
                  class = "checkbox_asignar"                    
                  name="todo_asignar"
            ></label>
        </div>
      </th>

    </tr>
  </thead>
  <tbody>
    @foreach($listadeproductos as $indexp => $itemproducto)
      @foreach($listacliente as $index => $item)


          @php $sw                            =   0; @endphp
          @php $regla_producto_clinete_id     =   ''; @endphp

          @foreach($listareglaproductoclientes as $itemr)
                @if ($itemr->producto_id == $itemproducto->COD_PRODUCTO && $itemr->cliente_id == $item->id && $itemr->contrato_id == $item->COD_CONTRATO)
                  @php $sw                            =   1; @endphp
                  @php $regla_producto_clinete_id     =   $itemr->id; @endphp
                @endif
          @endforeach

          <tr class='fila_regla'
              data_producto='{{$itemproducto->COD_PRODUCTO}}'
              data_cliente='{{$item->id}}'
              data_contrato='{{$item->COD_CONTRATO}}'>

              <td class="cell-detail">
                <span>{{$item->NOM_EMPR}}</span>
                <span class="cell-detail-description-producto">{{$funcion->funciones->data_empresa($item->COD_EMPR)->NOM_EMPR}}</span>
                <span class="cell-detail-description-contrato">{{$item->COD_CONTRATO}}</span>
              </td>
              <td>{{$item->TXT_CATEGORIA_JEFE_VENTA}}</td>
              <td>{{$item->TXT_CATEGORIA_CANAL_VENTA}}</td>
              <td>{{$item->TXT_CATEGORIA_SUB_CANAL}}</td>
              <td>{{$itemproducto->NOM_PRODUCTO}}</td>
              <td class="relative">
                  <div>

                      @include('regla.listado.ajax.etiquetasmasiva',
                               [
                                'color'                           => 'primary',
                                'sw'                              => $sw,
                                'regla_producto_clinete_id'       => $regla_producto_clinete_id,
                               ])

                  </div>
              </td>


              <td>
                     @include('regla.gestion.ajax.checkbox_eliminar',
                               [
                                'color'                           => 'primary',
                                'sw'                              => $sw
                               ])

              </td>

              <td>

                    @include('regla.gestion.ajax.checkbox',
                             [
                              'color'                           => 'primary',
                              'sw'                              => $sw
                             ])

              </td>


          </tr>
      @endforeach       
    @endforeach
  </tbody>
</table>


<script type="text/javascript">
  $(document).ready(function(){
     App.dataTables();
  });
</script> 