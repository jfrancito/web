<div style="margin-bottom: 10px;" >
  <table class="table" style='font-size: 13px;' id="" >
  <thead>
    <tr>
      <th>Cod Orden</th>
      <th>Fecha Orden</th>
      <th>Almacen Origen</th>
      <th>Almacen Destino</th>
      <th>Proveedor</th>
      <th>Estado</th>
      <th>Glosa</th>
    </tr>
  </thead>
  <tbody>
  @foreach($listatranferenciaspt as $index => $item)
      @php
        $lista_orden   =   $funcion->funciones->lista_orden($item->orden_transferencia_id,'','');
      @endphp

      @while ($row = $lista_orden->fetch())
        <tr>
            <td>
              {{$row['COD_ORDEN']}}
            </td>
            <td>
              {{date_format(date_create($row['FEC_ORDEN']), 'd-m-Y')}}
            </td>
            <td>
              {{$row['ALMACEN_ORIGEN']}}
            </td>
            <td>
              {{$row['ALMACEN_DESTINO']}}
            </td>
            <td>
              {{$row['TXT_EMPR_CLIENTE']}}
            </td>
            <td>
              {{$row['TXT_CATEGORIA_ESTADO_ORDEN']}}
            </td>
            <td>
              {{$row['TXT_GLOSA']}}
            </td>
        </tr>                    
      @endwhile
      

  @endforeach
  </tbody>
</table> 
</div>
 
@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();      
    });
  </script> 
@endif