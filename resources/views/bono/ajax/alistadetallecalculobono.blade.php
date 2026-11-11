<table id="tablebono" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>

      <th>Item</th>

      <th>Periodo</th> 
      <th>Jefe Venta</th>
      <th>Alcance Inicial</th>
      <th>Alcance Final</th>

      <th>Cuota</th>
      <th>Venta</th>
      <th>NC</th>
      <th>Alcance</th>
      <th>Bono</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadetallecalculobono as $index => $item)
      <tr data_calculobono_id = "{{$item->id}}" 
        class='dobleclickpc seleccionar'
        style="cursor: pointer;">

        <td>{{$index + 1}}</td>
        <td>{{$item->anio}}-{{$item->mes}}</td>
        <td>{{$item->jefeventa_nombre}}</td>
        <td>{{$item->alcance_inicial}}%</td>        
        <td>{{$item->alcance_final}}%</td>
        <td><b>{{number_format($item->cuota, 2, '.', ',')}}</b></td>
        <td>{{number_format($item->venta, 2, '.', ',')}}</td>
        <td>{{number_format($item->nc, 2, '.', ',')}}</td>
        <td><b>{{number_format($item->alcance, 2, '.', ',')}}</b></td>
        <td><b>{{number_format($item->bono, 2, '.', ',')}}</b></td>
      </tr>                    
    @endforeach
  </tbody>
</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif