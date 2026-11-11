
<table id="tableprecios" class="table table-striped table-hover table-fw-widget">
  <thead>
    <tr>
      <th>Código</th>
      <th>Fecha</th>
      <th>Centro Origen</th>
      <th>Estado</th>
      <th>Opción</th>      
    </tr>
  </thead>
  <tbody>

     @foreach($listapicking as $item)
      @php
        $tiene_detraccion            =   $funcion->funciones->picking_detraccion_calculada($item->id);
      @endphp

      <tr>

        <td class="cell-detail"> <span>{{$item->codigo}}</span> </td>

        <td>{{date_format(date_create($item->fecha_picking), 'd-m-Y')}} </td>

        <td class="cell-detail"> {{$item->NOM_CENTRO}} </td>

        <td>

          @if($item->COD_CATEGORIA == 'EPP0000000000003') 
              <span class="badge badge-warning">{{$item->NOM_CATEGORIA}}</span> 
          @else
            @if($item->COD_CATEGORIA == 'EPP0000000000002' or $item->COD_CATEGORIA == 'EPP0000000000006') 
                <span class="badge badge-primary">{{$item->NOM_CATEGORIA}}</span>
            @else
              @if($item->COD_CATEGORIA == 'EPP0000000000004') 
                  <span class="badge badge-success">{{$item->NOM_CATEGORIA}}</span>
              @else
                  <span class="badge badge-danger">{{$item->NOM_CATEGORIA}}</span>
              @endif
            @endif
          @endif

          @if($tiene_detraccion == 1) 
              <span class="badge badge-success">DETRACCIÓN GENERADA</span>
          @endif

          
        </td>

        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">              
              <li>             
                 <a class="btn-generar-detraccion"  href="{{ url('/generar-detraccion-picking/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                    Generar Detracción
                  </a>                         
              </li>              
              <li>             
                 <a class="btn-atender-picking"  href="{{ url('/atender-picking/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                    Atender
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