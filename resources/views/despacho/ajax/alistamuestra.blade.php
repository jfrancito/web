<table class="table tabla-producto-muestra" style="font-size: 12px;">
  <thead>
    <tr>
      <th>Producto</th>
      <th class='center'>Muestra</th>
      <th class='center'>Editar</th>
      <th class='center'>Mobil</th>
    </tr>
  </thead>
  <tbody>
    @foreach($muestras as $index => $item)

    @php $mobil =   $funcion->funciones->producto_asignado_mobil($item->orden_id); @endphp
      <tr class='fila_pedido_muestras'
          data_detalle_orden_id="{{$item->id}}"
          data_mobil="{{$mobil}}">

        <td class="cell-detail relative">
              <span>{{$item->producto->NOM_PRODUCTO}}</span>
              <span class="cell-detail-description-producto">
              {{$item->producto->unidadmedida->NOM_CATEGORIA}} de  {{$item->presentacion_producto}} kg 
              </span>
        </td>
        <td class='center'>
            <b>{{number_format($item->muestra, 2, '.', ',')}}</b>
        </td>
        <td class='center'>
            <input type="text"
             id="muestra" 
             name="muestra"
             value="{{number_format($item->muestra, 2, '.', ',')}}"
             class="form-control input-sm dinero dineromuestra updatepricemuestradseparado"
             @if($mobil<>'') disabled @endif
            >
        </td>
        <td>{{$mobil}}</td>
      </tr>
    @endforeach
  </tbody>
</table> 
@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
      $('.despacho .dinero').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});
    });
  </script> 
@endif
