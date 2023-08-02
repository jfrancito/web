<table id="tablebono" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>

      <th>Sel</th>
      <th>Id</th>

      <th>Codigo</th>
      <th>Fecha Registro</th>
      <th>Anio</th>
      <th>Mes</th>
      <th>Estado</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listabonos as $index => $item)
      <tr data_cuota_id="{{$item->id}}" 
          class="@if($item->estado_id == 'EPP0000000000002') dobleclickpc seleccionar @endif" 

          style="cursor: pointer;" role="row">

        <td>  
          <div class="text-center be-checkbox be-checkbox-sm">
            <input  type="checkbox"
                    class="{{$item->id}}" 
                    data_calculobono_id = '{{$item->id}}'
                    id="{{$item->id}}" 
                    @if($item->estado_id == 'EPP0000000000004') disabled @endif>

            <label  for="{{$item->id}}"
                  data-atr = "ver"
                  class = "checkbox"                    
                  name="{{$item->id}}"
            ></label>
          </div>
        </td>

        <td>{{$index + 1 }}</td>


        <td>{{$item->codigo}}</td>
        <td>{{$item->fecha_crea}}</td>
        <td>{{$item->anio}}</td>
        <td>{{$item->mes}}</td>
        <td>
          @if($item->estado_id == 'EPP0000000000002') 
            <span class="badge badge-default">{{$item->estado_nombre}}</span> 
          @else
              <span class="badge badge-success">{{$item->estado_nombre}}</span>
          @endif          
        </td>

        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="{{ url('/ingresar-calculo-vendedor/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Calculo x Vendedor
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