
<table id="tableprecios" class="table table-striped table-hover table-fw-widget">
  <thead>
    <tr>
<!--       <th>Sel</th> -->
      <th>Empresa Solicitante</th>
      <th>Centro Solicitante</th>
      <th>Usuario Solicitante</th>
      <th>Codigo</th>
      <th>Fecha Pedido</th>
      <th>Estado</th>
      <th>Opción</th>
    </tr>
  </thead>
  <tbody>

    @foreach($listaordenatender as $index=>$item)
      <tr data_pedido_id = '{{$item->id}}' class='dobleclickpc seleccionar'>

<!--         <td>

            <div class="be-radio">
              <input type="radio" checked="" name="rad2" id="rad{{$index}}">
              <label for="rad{{$index}}">Option 1</label>
            </div>


            <div class="text-center be-checkbox be-checkbox-sm has-primary">
              <input  type="checkbox"
                class="{{$index}} input_asignar_im"
                data_pedido_id = '{{$item->id}}'
                id="{{$index}}" >

              <label  for="{{$index}}"
                    data-atr = "ver"
                    class = "checkbox checkbox_asignar"                    
                    name="{{$index}}"
              ></label>
            </div>
        </td> -->

        <td class="cell-detail"> 
          <span>{{$item->empresa->NOM_EMPR}}</span>
        </td>
        <td class="cell-detail"> 
          <span>{{$item->centro->NOM_CENTRO}}</span>
        </td>

        <td class="cell-detail"> 
          <span>{{$funcion->funciones->data_usuario($item->usuario_crea)->nombre}}</span>
        </td>

        <td>{{$item->codigo}}</td>
        <td>{{date_format(date_create($item->fecha_crea), 'd-m-Y H:i')}}</td>

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


        </td>

        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
<!--               <li>
                <a href="{{ url('/atender-orden-despacho/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Atender Pedido
                </a>  
              </li> -->
              <li>
                <a href="{{ url('/excel-orden-despacho/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Descargar Pedido Excel
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