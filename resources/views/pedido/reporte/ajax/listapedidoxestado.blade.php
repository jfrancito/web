<div class='row reporte'>

  <table id="tablereporte" class="table table-striped table-hover table-fw-widget">
    <thead>
      <tr>
        <th class= 'center tablaho'>CENTRO</th> 
        <th class= 'center tablaho'>CODIGO</th>             
        <th class= 'center tablaho'>FECHA</th> 
        <th class= 'center tablaho'>VENDEDOR</th>
        <th class= 'center tablaho'>CLIENTE</th>
        <th class= 'center tablaho' >PRODUCTO</th>
        <th class= 'center tablaho' >CANTIDAD</th>
        <th class= 'center tablaho' >ATENDIDO</th>
        <th class= 'center tablaho' >PRECIO</th>  
        <th class= 'center tablaho' >DESTINO</th>
        <th class= 'center tablaho' >ESTADO</th> 
        <th class= 'center tablaho' >AUTORIZADO</th> 

      </tr>
    </thead>
    <tbody>

      @foreach($listapedidos as $index => $item) 
            <tr>
                <td>{{$item->NOM_CENTRO}}</td>
                <td>{{$item->codigo}}</td>
                <td>{{date_format(date_create($item->fecha_venta), 'd-m-Y')}}</td>
                <td>{{$funcion->funciones->data_usuario($item->usuario_crea)->nombre}}</td>
                <td>{{$funcion->funciones->data_empresa($item->cliente_id)->NOM_EMPR}}</td>
                <td>{{$item->producto->NOM_PRODUCTO}}</td>
                <td>{{$item->cantidad}}</td>
                <td>
                  @if(empty($item->atendido))
                    0.0000
                  @else
                    {{$item->atendido}}
                  @endif
                </td>
                <td>{{$item->precio}}</td>
                <td>
                   {{$funcion->funciones->data_direccion($item->direccion_entrega_id)->NOM_DIRECCION}}
                </td>

                <td>{{$item->NOM_CATEGORIA}}</td>   
                <td>{{$item->nombre}}</td>
                              
            </tr>
      @endforeach       

    </tbody>
  </table>

</div>

<script type="text/javascript">
  $(document).ready(function(){
     App.dataTables();
  });
</script> 