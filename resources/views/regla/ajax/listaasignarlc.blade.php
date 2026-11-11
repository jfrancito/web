<table class="table table-striped table-borderless" style="text-align: left;">
  <thead>

    <tr>
      <th colspan="5" class='columna_2'>Regla</th>
      <th colspan="4" class='success'>Cliente</th>
    </tr>

    <tr>
      <th>Codigo</th>
      <th>Nombre</th>
      <th class='columna_2'>Ampliar limite</th>
      <th>F. Inicio</th>
      <th>F. Fin</th>

      <th>Cliente</th>
      <th>Nro Documento</th>
      <th>Limite Credito</th>


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


        <td><b>{{$item->NOM_EMPR}}</b></td>
        <td>{{$item->NRO_DOCUMENTO}}</td>
        <td>{{$item->canlimitecredito}}</td>
        <td>
          <a href="{{ url('/eliminar-regla-limite-credito/'.$item->asignarregla_id.'/'.$idopcion) }}" 
          class="btn btn-space btn-default btn-social">
          <i class="icon mdi mdi-delete"></i></a></td>
      </tr>                    
    @endforeach
  </tbody>
</table>
