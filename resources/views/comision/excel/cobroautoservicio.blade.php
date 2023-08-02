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


    <table>

        <tr>
          <th class= 'center tablaho'>NOMBRE</th>
          <th class= 'center tablaho'>TOTAL</th>
          <th class= 'center tablaho'>PORCENTAJE</th>
          <th class= 'center tablaho'>PAGAR</th>
        </tr>
        @foreach($detalleuno as $index => $item)

           <tr>
              <td width="15">{{$item->PRODUCTO}}</td>
              <td width="15">{{$item->TOTAL_P}}</td>
              <td width="15">{{$item->CAN_PRODUCTO}}</td>
              <td width="15">{{$item->TOTAL_COMISION}}</td>
            </tr>     
        @endforeach 


    </table>

    <table>

        <tr>
          <th class= 'center tablaho'>FEC ORDEN</th>
          <th class= 'center tablaho'>COD ORDEN</th>
          <th class= 'center tablaho'>DOCUMENTO</th>
          <th class= 'center tablaho'>CLIENTE</th>
          <th class= 'center tablaho'>PRODUCTO</th>

          <th class= 'center tablaho'>FAMILIA</th>
          <th class= 'center tablaho'>SUN FAMILIA</th>
          <th class= 'center tablaho'>UNIDAD</th>
          <th class= 'center tablaho'>CANTIDAD</th>
          <th class= 'center tablaho'>P.U.</th>

          <th class= 'center tablaho'>TOTAL P</th>
          <th class= 'center tablaho'>PESO 50KG</th>
          <th class= 'center tablaho'>CANAL</th>
          <th class= 'center tablaho'>SUB CANAL</th>
          <th class= 'center tablaho'>FECHA PAGO</th>

          <th class= 'center tablaho'>TOTAL COBRO</th>
          <th class= 'center tablaho'>DIFF</th>
          <th class= 'center tablaho'>VAL</th>
          <th class= 'center tablaho'>JEFE DE VENTA</th>

        </tr>
        @foreach($detalledos as $index => $item)
           <tr>
              <td width="15">{{$item->FEC_ORDEN}}</td>
              <td width="15">{{$item->COD_ORDEN}}</td>
              <td width="15">{{$item->COD_DOCUMENTO_CTBLE}}</td>
              <td width="15">{{$item->CLIENTE}}</td>
              <td width="15">{{$item->PRODUCTO}}</td>

              <td width="15">{{$item->CAT_SUP_NOM_CATEGORIA}}</td>
              <td width="15">{{$item->CAT_INF_NOM_CATEGORIA}}</td>
              <td width="15">{{$item->CAT_UNI_NOM_CATEGORIA}}</td>
              <td width="15">{{$item->CAN_PRODUCTO}}</td>
              <td width="15">{{$item->CAN_PRECIO_UNIT}}</td>

              <td width="15">{{$item->TOTAL_P}}</td>
              <td width="15">{{$item->PESO_ORDEN_50}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_CANAL_VENTA}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_SUB_CANAL}}</td>
              <td width="15">{{$item->FEC_HABILITACION}}</td>

              <td width="15">{{$item->TOTAL_COBRO}}</td>
              <td width="15">{{$item->DIFF}}</td>
              <td width="15">{{$item->VAL}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_JEFE_VENTA}}</td>

              
          

            </tr>     
        @endforeach     
    </table>
</html>
