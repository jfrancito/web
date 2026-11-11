<table id="tablatomapedido" class="table table-striped table-hover table-fw-widget dt-responsive nowrap listatabla" 
style='width: 100%;font-size: 0.85em;'>
  <thead>
    <tr> 
      <th>Item</th>
      <th>Periodo</th>
      <th>Estado</th>
      <th>Ver</th>
    </tr>
  </thead>
  <tbody>
   @foreach($listaplanilla as $index=>$item)
      <tr>
        <td>{{$index + 1}}</td>
        <td>{{$item->TXT_CODIGO}}</td>
        <td>

          @if($funcion->funciones->estado_comision_general($item->COD_PERIODO) == 'AUTORIZADO') 
              <span class="badge badge-warning">AUTORIZADO</span> 
          @else
            @if($funcion->funciones->estado_comision_general($item->COD_PERIODO) == 'GENERADO') 
                <span class="badge badge-primary">GENERADO</span>
            @else
              @if($funcion->funciones->estado_comision_general($item->COD_PERIODO) == 'EJECUTADO') 
                  <span class="badge badge-success">EJECUTADO</span>
              @else
                  <span class="badge badge-success">ATENDIDO PARCIALMENTE</span>
              @endif
            @endif
          @endif

          
        </td>
        <td class="rigth">
            <div class="btn-group btn-hspace">
              <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
              <ul role="menu" class="dropdown-menu pull-right">
                <li>
                  <a href="{{ url('/ver-detalle-comisiones/'.$idopcion.'/'.$item->COD_PERIODO ) }}">
                    Ver detalle
                  </a>
                </li>
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

