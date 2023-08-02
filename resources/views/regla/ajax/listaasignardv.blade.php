<table class="table table-striped table-borderless" style="text-align: left;">
  <thead>

    <tr>
      <th colspan="5" class='columna_2'>Regla</th>
      <th colspan="5" class='success'>Orden Venta</th>
    </tr>

    <tr>
      <th>Codigo</th>
      <th>Nombre</th>
      <th class='columna_2'>Ampliar dias</th>
      <th>F. Inicio</th>
      <th>F. Fin</th>
      <th>Orden Venta</th>
      <th>Cliente</th>
      <th>Fecha</th>
      <th>Tipo de Pago</th>
      <th>Eliminar</th>

    </tr>
  </thead>
  <tbody>
    @foreach($lista_reglas as $item)
      <tr>
        <td>{{$item->codigo}}</td>
        <td>{{$item->nombre}}</td>
        <td><b>{{$item->descuento}}</b></td>
        <td>{{date_format(date_create($item->fechainicio), 'd-m-Y')}}</td>
        <td>{{date_format(date_create($item->fechafin), 'd-m-Y')}}</td>


        <td><b>{{$item->COD_ORDEN}}</b></td>
        <td>{{$item->TXT_EMPR_CLIENTE}}</td>
        <td>{{date_format(date_create($item->FEC_ORDEN), 'd-m-Y')}}</td>

        <td>{{$item->CP}}</td>

        <td>
          <a href="{{ url('/eliminar-regla-dias-vencimiento/'.$item->asignarregla_id.'/'.$idopcion) }}" 
          class="btn btn-space btn-default btn-social">
          <i class="icon mdi mdi-delete"></i></a></td>
      </tr>                    
    @endforeach
  </tbody>
</table>
