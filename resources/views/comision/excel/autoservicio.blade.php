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
          <th class= 'center tablaho'>VENDEDOR</th>
          <th class= 'center tablaho'>DESCRIPCION</th>
          <th class= 'center tablaho'>COMISION</th>
          <th class= 'center tablaho'>TOTAL</th>
        </tr>
        @foreach($detalleuno as $index => $item)

           <tr>
              <td width="15">{{$item->TXT_CATEGORIA_JEFE_VENTA}}</td>
              <td width="15">{{$item->TIPO_PAGO}}</td>
              <td width="15">{{$item->CAN_CAPITAL_SALDO}}</td>
              <td width="15">{{$item->CAN_SALDO}}</td>
            </tr>     
        @endforeach 

        <tr>
            <td width="15" colspan="3">CUOTA AL 100%</td>
            <td width="15">{{$cabecerauno->TOTAL_COMISION}}</td>
        </tr> 
        <tr>
            <td width="15" colspan="3">TOTAL VENTAS</td>
            <td width="15">{{$cabecerauno->TOTAL_COBRO}}</td>
        </tr> 
        <tr>
            <td width="15" colspan="3">PORCENTAJE DE VENTAS REFERENTE A COMISON</td>
            <td width="15">{{$cabecerauno->COMISION}}</td>
        </tr> 
        <tr>
            <td width="15" colspan="3">COMISION TOTAL</td>
            <td width="15" style="font-weight: bold;">{{$cabecerauno->CAN_SALDO}}</td>
        </tr> 


    </table>



    <table>

        <tr>
          <th class= 'center tablaho'>CENTRO</th>
          <th class= 'center tablaho'>FECHA</th>
          <th class= 'center tablaho'>NRO. ORDEN</th>
          <th class= 'center tablaho'>TIPO</th>
          <th class= 'center tablaho'>TIPO PAGO</th>

          <th class= 'center tablaho'>ESTADO ORDEN</th>
          <th class= 'center tablaho'>FECHA MODIF</th>
          <th class= 'center tablaho'>JEFE</th>
          <th class= 'center tablaho'>CANAL</th>
          <th class= 'center tablaho'>SUB CANAL</th>


          <th class= 'center tablaho'>REG COMERCIAL</th>
          <th class= 'center tablaho'>CLIENTE</th>
          <th class= 'center tablaho'>DIV</th>
          <th class= 'center tablaho'>REG COMERCIAL</th>
          <th class= 'center tablaho'>MONEDA</th>


          <th class= 'center tablaho'>T.C.</th>
          <th class= 'center tablaho'>SUB FAMILIA</th>
          <th class= 'center tablaho'>NOMBRE</th>
          <th class= 'center tablaho'>UNIDAD DE MEDIDA</th>
          <th class= 'center tablaho'>CANTIDAD</th>

          <th class= 'center tablaho'>PESO</th>
          <th class= 'center tablaho'>CANTIDAD EN 50KG</th>
          <th class= 'center tablaho'>VALOR (S. IGV)</th>
          <th class= 'center tablaho'>PRECIO</th>
          <th class= 'center tablaho'>TOTAL</th>

        </tr>
        @foreach($detalledos as $index => $item)
           <tr>
              <td width="15">{{$item->CENTRO}}</td>
              <td width="15">{{$item->FECHA}}</td>
              <td width="15">{{$item->ORDEN}}</td>
              <td width="15">{{$item->TIPO_VENTA}}</td>
              <td width="15">{{$item->TIPO_PAGO}}</td>

              <td width="15">{{$item->TXT_CATEGORIA_ESTADO_ORDEN}}</td>
              <td width="15">{{$item->FEC_USUARIO_MODIF_AUD}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_JEFE_VENTA}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_CANAL_VENTA}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_SUB_CANAL}}</td>

              <td width="15">{{$item->REG_COMERCIAL}}</td>
              <td width="15">{{$item->CLIENTE}}</td>
              <td width="15">{{$item->DIV}}</td>
              <td width="15">{{$item->REG_COMERCIAL}}</td>
              <td width="15">{{$item->MONEDA}}</td>

              <td width="15">{{$item->CAN_TIPO_CAMBIO}}</td>
              <td width="15">{{$item->NOM_SUBFAMILIA_PRODUCTO}}</td>
              <td width="15">{{$item->NOM_PRODUCTO}}</td>
              <td width="15">{{$item->UM_OV}}</td>
              <td width="15">{{$item->CAN_PRODUCTO}}</td>
              
              
              <td width="15">{{$item->KG_OV}}</td>
              <td width="15">    
                    @if($item->COD_SUBFAMILIA_PRODUCTO == "SFM0000000000025") 
                        {{$item->KG_OV*0.549/50}}
                    @else
                        {{$item->KG_OV/50}}
                    @endif
              </td>
              <td width="15">{{$item->CAN_PRECIO_UNIT}}</td>
              <td width="15">{{$item->CAN_PRECIO_UNIT_IGV}}</td>
              <td width="15">{{$item->CAN_PRECIO_UNIT_IGV * $item->CAN_PRODUCTO}}</td>

            </tr>     
        @endforeach     
    </table>
</html>
