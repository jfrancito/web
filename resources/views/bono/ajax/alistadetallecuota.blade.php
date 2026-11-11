<table id="tablebono" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Item</th>
      <th>Jefe Venta</th>
      <th>Canal</th>
      <th>Sub Canal</th>
      <th>Cuota</th>      
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadetallecuota as $index => $item)
      <tr>
        <td>{{$index + 1}}</td>
        <td>{{$item->jefeventa_nombre}}</td>
        <td>{{$item->canal_nombre}}</td>
        <td>{{$item->subcanal_nombre}}</td>
        <td>{{$item->cuota}}</td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
            @if($cuota->estado_id == 'EPP0000000000002')
              <li>
                <a href="#" 
                  class= 'modificardetallecuota' 
                  data_cuota_id = "{{$item->cuota_id}}"
                  data_detalle_cuota_id = "{{$item->id}}" >
                  Modificar
                </a>  
              </li>
            @endif

            </ul>
          </div>
        </td>
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