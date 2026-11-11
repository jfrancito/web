<input type="hidden" name="h_array_productos_transferencia_pt" id="h_array_productos_transferencia_pt" value="{{json_encode($data_productos_tranferencia_pt)}}">
<input type="hidden" name="calcula_cantidad_peso" id = "calcula_cantidad_peso" value = "{{$calcula_cantidad_peso}}">

<div>
  <table class="table" style='font-size: 0.85em;' id="tablaproductotransferenciapt" >
    <thead>
      <tr>
        <th>Codigo</th>
        <th>Producto</th>
        <th>Unidad</th>
        <th>Almacen</th>
        <th>Lote</th>
        <th>Neto</th>
        <th>Cantidad</th>
        <th>Costo</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>

      @php $totales =   0.0; @endphp
      @foreach($data_productos_tranferencia_pt as $index => $item)
          @php $totales =   $item['total'] + $totales; @endphp
          <tr>
            <td class=''>{{$item['data_producto']}}</td>
            <td class=''>{{$item['nombre_producto']}}</td>
            <td class=''>{{$item['unidad_medida']}}</td>
            <td class=''>{{$item['almacen_nombre']}}</td>
            <td class=''>{{$item['lote_id']}}</td>
            <td>{{$item['neto']}}</td>
            <td class='center'>{{number_format($item['cantidad_atender'],4,'.',',')}}</td>
            <td class='center'>{{number_format($item['costo'],4,'.',',')}}</td>
            <td class=''>{{number_format($item['total'],4,'.',',')}}</td>
          </tr>
      @endforeach

      <tr>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>          
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'></td>
          <td class='despacho_totales'>{{number_format($totales,4,'.',',')}}</td>
      </tr>

    </tbody>
  </table>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $('.update_price_cantidad_servicio').val({{$calcula_cantidad_peso}});
  </script> 
@endif 

