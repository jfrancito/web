<table id="table1" class="table table-striped table-hover table-fw-widget dt-responsive nowrap listatabla" style='width: 100%;'>
  <thead>
    <tr> 
      <th></th>
      <th>FECHA</th>
      <th>EMPRESA</th>
      <th>TIPO</th>
      <th>DIV</th>
      <th style="width: 200px;">DETALLE</th> <!-- 👈 Ancho fijo -->
      <th>DETALLE</th>
      <th>CREDITO</th>
      <th>PAGO</th>
      <th>SALDO</th>

    </tr>
  </thead>
  <tbody>
   @foreach($listadatos as $index=>$item)
      <tr>
        <td>{{$index + 1}}</td>
        <td>{{date_format(date_create($item['fec_habilitacion']), 'd-m-Y')}}</td>
        <td>{{$item['empresa_id']}}</td>
        <td>{{$item['accion']}}</td>
        <td>{{$item['div']}}</td>
        <td>{{$item['factura']}}</td>
        <td style="word-wrap: break-word; white-space: normal;">{{$item['dp_concat']}}</td>
        <td>{{number_format($item['credito'], 2, '.', ',')}}</td>        
        <td>{{number_format($item['pago'], 2, '.', ',')}}</td>
        <td>{{number_format($item['saldo'], 2, '.', ',')}}</td>  
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

