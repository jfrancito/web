<style>
  .columna_regla { background-color: #fce8e6 !important; color: #d93025; text-align: center; font-weight: bold; border: 1px solid #fad2cf; }
  .columna_orden { background-color: #e6f4ea !important; color: #1e8e3e; text-align: center; font-weight: bold; border: 1px solid #ceead6; }
  .thead_ajust { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
</style>
<table class="table table-striped table-hover table-bordered" style="text-align: left; background: white; border-radius: 8px; overflow: hidden;">
  <thead class="thead_ajust">
    <tr>
      <th colspan="5" class='columna_regla'>DATOS DE LA REGLA</th>
      <th colspan="5" class='columna_orden'>ORDEN DE VENTA ASIGNADA</th>
    </tr>
    <tr>
      <th>Código</th>
      <th>Nombre</th>
      <th>Días Ampliac.</th>
      <th>F. Inicio</th>
      <th>F. Fin</th>
      <th>O. Venta</th>
      <th>Cliente</th>
      <th>Fecha</th>
      <th>Tipo Pago</th>
      <th class="text-center">Acción</th>
    </tr>
  </thead>
  <tbody>
    @foreach($lista_reglas as $item)
      <tr>
        <td><b>{{$item->codigo}}</b></td>
        <td>{{$item->nombre}}</td>
        <td class="text-danger center"><b>{{$item->descuento}}</b></td>
        <td>{{date_format(date_create($item->fechainicio), 'd-m-Y')}}</td>
        <td>{{date_format(date_create($item->fechafin), 'd-m-Y')}}</td>


        <td class="text-primary center"><b>{{$item->COD_ORDEN}}</b></td>
        <td>{{$item->TXT_EMPR_CLIENTE}}</td>
        <td>{{date_format(date_create($item->FEC_ORDEN), 'd-m-Y')}}</td>

        <td>{{$item->CP}}</td>

        <td class="text-center">
          <a href="{{ url('/eliminar-regla-dias-vencimiento/'.$item->asignarregla_id.'/'.$idopcion) }}" 
          class="btn btn-sm btn-danger btn-social" title="Eliminar Asignación">
          <i class="icon mdi mdi-delete"></i></a></td>
      </tr>                    
    @endforeach
  </tbody>
</table>
