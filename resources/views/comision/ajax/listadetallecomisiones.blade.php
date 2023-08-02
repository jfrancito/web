<table id="table1" class="table table-striped table-hover table-fw-widget dt-responsive nowrap listatabla" 
style='width: 100%;font-size: 0.85em;'>
  <thead>
    <tr> 
      <th>Sel</th>
      <th>Comisionista</th>
      <th>Lugar</th>
      <th>Periodo</th>
      <th>Fechas</th>
      <th>Importe a Pagar</th>
      <th>Estado</th>
      <th>Usurio Autoriza</th>
      <th>Usurio Ejecuta</th>
      <th>Ver</th>
    </tr>
  </thead>
  <tbody>
   @foreach($listaplanilladetalle as $item)
      <tr class= 'filacomision'>
        <td>  

          <div class="text-center be-checkbox be-checkbox-sm">
            <input  type="checkbox"
                    class="{{$item->COD_PERIODO}}{{$item->COD_CATEGORIA_JEFE_VENTA}}{{$item->TXT_PROVIENE}}" 
                    data_codperiodo = '{{$item->COD_PERIODO}}'
                    data_codvendedor = '{{$item->COD_CATEGORIA_JEFE_VENTA}}'
                    data_proviene = '{{$item->TXT_PROVIENE}}'
                    id="{{$item->COD_PERIODO}}{{$item->COD_CATEGORIA_JEFE_VENTA}}{{$item->TXT_PROVIENE}}" 
                    @if($item->COD_ESTADO == 'EPP0000000000004') disabled @endif>

            <label  for="{{$item->COD_PERIODO}}{{$item->COD_CATEGORIA_JEFE_VENTA}}{{$item->TXT_PROVIENE}}"
                  data-atr = "ver"
                  class = "checkbox"                    
                  name="{{$item->COD_PERIODO}}{{$item->COD_CATEGORIA_JEFE_VENTA}}"
            ></label>
          </div>
        </td>

        <td>{{$item->TXT_CATEGORIA_JEFE_VENTA}}</td>
        <td>{{$item->TXT_PROVIENE}}</td>

        <td>{{$item->TXT_CODIGO}}</td>

        <td>{{date_format(date_create($item->FEC_INICIO), 'd-m-Y')}} al 
          {{date_format(date_create($item->FEC_FIN), 'd-m-Y')}}</td>
        <td>{{$funcion->funciones->importe_pagar_comision_vendedor($item->COD_CATEGORIA_JEFE_VENTA,$item->COD_PERIODO,$item->TXT_PROVIENE)}}</td>
        <td>          
          @if($item->COD_ESTADO == 'EPP0000000000003') 
              <span class="badge badge-warning">{{$item->TXT_ESTADO}}</span> 
          @else
            @if($item->COD_ESTADO == 'EPP0000000000002') 
                <span class="badge badge-primary">{{$item->TXT_ESTADO}}</span>
            @else
              @if($item->COD_ESTADO == 'EPP0000000000004') 
                  <span class="badge badge-success">{{$item->TXT_ESTADO}}</span>
              @else
                  <span class="badge badge-danger">{{$item->TXT_ESTADO}}</span>
              @endif
            @endif
          @endif

        </td>
        <td>{{$item->TXT_USUARIO_AUTORIZA}}</td>
        <td>{{$item->TXT_USUARIO_EJECUTA}}</td>
        <td class="rigth">
            <div class="btn-group btn-hspace">
              <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
              <ul role="menu" class="dropdown-menu pull-right">
                <li>
                  <a href="{{ url('/descargar-excel-comisiones/'.$idopcion.'/'.$item->COD_PERIODO.'/'.$item->COD_CATEGORIA_JEFE_VENTA.'/'.$item->TXT_PROVIENE) }}">
                    Descargar Excel
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

