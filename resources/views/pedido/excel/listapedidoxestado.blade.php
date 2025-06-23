<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
    
    h1{
  text-align: center;
}
.subtitulos{
  font-weight: bold;
  font-style: italic;
}
.titulotabla{
  background: #4285f4;
  color: #fff;
  font-weight: bold;
}
.tabladp{
    background: #bababa;
    color:#fff;
}
.tablaho{
  background: #37b358;
    color:#fff;
}
.tablamar{
    background: #4285f4;
    color:#fff;
}
.tablaagrupado{
    background: #ea4335;
    color:#fff;
}
.negrita{
    font-weight: bold;
}
.center{
  text-align: center;
}
.reportevacadesc{
        background: #ea4335;
    color: #fff;
    font-weight: bold;
}
.tablafila2{
  background: #f5f5f5;
}
.tablafila1{
  background: #ffffff;
}
.warning{
  background-color: #f6c163 !important;
}

/*.vcent { display: table;  }*/

.vcent{ display: table-cell; vertical-align:middle;text-align: center;}

.gris{
    background: #C8C9CA;
}
.blanco{
  background: #ffffff;
}
  </style>

    <!-- titulo -->
    <table>

        <tr>
          <th class= 'center tablaho'>CENTRO</th>

          <th class= 'center tablaho'>CODIGO</th>             
          <th class= 'center tablaho'>FECHA</th>
          <th class= 'center tablaho'>FECHA ENTREGA</th>  
          <th class= 'center tablaho'>VENDEDOR</th>
          <th class= 'center tablaho'>CLIENTE</th>
          <th class= 'center tablaho' >PRODUCTO</th>
          <th class= 'center tablaho' >CANTIDAD</th>
          <th class= 'center tablaho' >ATENDIDO</th>
          <th class= 'center tablaho' >PRECIO</th>
          <th class= 'center tablaho' >TOTAL</th>
          <th class= 'center tablaho' >DESTINO</th>
          <th class= 'center tablaho' >ESTADO</th>
        </tr>

      @foreach($listapedidos as $index => $item) 
          <tr>
                <td width="15">{{$item->NOM_CENTRO}}</td>
                <td width="15">{{$item->codigo}}</td>
                <td width="15">{{date_format(date_create($item->fecha_venta), 'd-m-Y')}}</td>
                <td width="15">{{date_format(date_create($item->fecha_despacho), 'd-m-Y')}}</td>
                <td width="25">{{$funcion->funciones->data_usuario($item->usuario_crea)->nombre}}</td>
                <td width="40">{{$funcion->funciones->data_empresa($item->cliente_id)->NOM_EMPR}}</td>
                <td width="60">{{$item->producto->NOM_PRODUCTO}}</td>
                <td width="15">{{$item->cantidad}}</td>
                <td width="15">
                  @if(empty($item->atendido))
                    0.0000
                  @else
                    {{$item->atendido}}
                  @endif
                </td>
                <td width="15">{{$item->precio}}</td>
                <td width="15">{{$item->total}}</td>
                <td width="40">
                   {{$funcion->funciones->data_direccion($item->direccion_entrega_id)->NOM_DIRECCION}}
                  
                </td>
                <td width="15">{{$item->NOM_CATEGORIA}}</td>  
          </tr>
      @endforeach       

    </table>
</html>
