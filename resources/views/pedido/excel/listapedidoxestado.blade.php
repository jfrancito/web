<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	{!! Html::style('public/css/excel/excel.css') !!}
    <!-- titulo -->
    <table>

        <tr>
          <th class= 'center tablaho'>CENTRO</th>

          <th class= 'center tablaho'>CODIGO</th>             
          <th class= 'center tablaho'>FECHA</th> 
          <th class= 'center tablaho'>VENDEDOR</th>
          <th class= 'center tablaho'>CLIENTE</th>
          <th class= 'center tablaho' >PRODUCTO</th>
          <th class= 'center tablaho' >CANTIDAD</th>
          <th class= 'center tablaho' >PRECIO</th>  
          <th class= 'center tablaho' >DESTINO</th>
          <th class= 'center tablaho' >ESTADO</th>
        </tr>

      @foreach($listapedidos as $index => $item) 
          <tr>
                <td width="15">{{$item->NOM_CENTRO}}</td>
                <td width="15">{{$item->codigo}}</td>
                <td width="15">{{date_format(date_create($item->fecha_venta), 'd-m-Y')}}</td>
                <td width="25">{{$funcion->funciones->data_usuario($item->usuario_crea)->nombre}}</td>
                <td width="40">{{$funcion->funciones->data_empresa($item->cliente_id)->NOM_EMPR}}</td>
                <td width="60">{{$item->producto->NOM_PRODUCTO}}</td>
                <td width="15">{{$item->cantidad}}</td>
                <td width="15">{{$item->precio}}</td>
                <td width="40">
                   {{$funcion->funciones->data_direccion($item->direccion_entrega_id)->NOM_DIRECCION}}
                  
                </td>
                <td width="15">{{$item->NOM_CATEGORIA}}</td>  
          </tr>
      @endforeach       

    </table>
</html>
