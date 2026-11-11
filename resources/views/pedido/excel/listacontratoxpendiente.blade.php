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
          <th class= 'center tablaho'>SEDE</th>
          <th class= 'center tablaho'>EMPRESA</th>
          <th class= 'center tablaho'>FEC. EMISION</th>
          <th class= 'center tablaho'>DIAS TRANSCURRIDOS</th>
          <th class= 'center tablaho'>COD. DOCUMENTO</th>
          <th class= 'center tablaho'>NRO. CONTRATO</th>
          <th class= 'center tablaho'>GUIA ASOCIADA</th>
          <th class= 'center tablaho'>EMISOR</th>
          <th class= 'center tablaho'>CAN. PRODUCTO</th>
          <th class= 'center tablaho'>COSTO</th>
          <th class= 'center tablaho'>IMPORTE</th>
          <th class= 'center tablaho'>SALDO</th>
          <th class= 'center tablaho'>ESTADO</th>
          <th class= 'center tablaho'>TRABAJADOR</th>
        </tr>
        @while ($row = $lista->fetch())
           <tr>
              <td width="15">{{$row['NOM_CENTRO']}}</td>
              <td width="15">{{$row['NOM_EMPR']}}</td>
              <td width="15">{{$row['FEC_EMISION']}}</td>
              <td width="15">{{$row['DIAS_TRANSCURRIDOS']}}</td>
              <td width="15">{{$row['COD_DOCUMENTO_CTBLE']}}</td>
              <td width="15">{{$row['NRO_CONTRATO']}}</td>
              <td width="15">{{$row['GRR_ASOC']}}</td>             
              <td width="15">{{$row['TXT_EMPR_EMISOR']}}</td>
              <td width="15">{{$row['CAN_PRODUCTO']}}</td>
              <td width="15">{{$row['COSTO']}}</td>
              <td width="15">{{$row['IMPORTE']}}</td>
              <td width="15">{{$row['SALDO_HAB']}}</td>
              <td width="15">{{$row['ESTADO_DOC']}}</td>
              <td width="15">{{$row['NOM_TRABAJADOR']}}</td>
            </tr>     
        @endwhile     
    </table>
</html>
