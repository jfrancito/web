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
          <th class= 'center tablaho'>EMPR_EMISOR</th>
          <th class= 'center tablaho'>COD_DOCUMENTO_CTBLE</th>             
          <th class= 'center tablaho'>TIPO_DOCTIPO_DOC</th> 
          <th class= 'center tablaho'>NRO_SERIE</th>
          <th class= 'center tablaho'>NRO_DOC</th>
          <th class= 'center tablaho' >CLIENTE</th>
          <th class= 'center tablaho' >FEC_EMISION</th>
          <th class= 'center tablaho' >ESTADO_DOC_CTBLE</th>
          <th class= 'center tablaho' >NOM_TRABAJADOR</th>
        </tr>

      @foreach($lista_documento as $index => $item) 
          <tr>

                <td width="25">{{$item->EMPR_EMISOR}}</td>
                <td width="25">{{$item->COD_DOCUMENTO_CTBLE}}</td>
                <td width="25">{{$item->TIPO_DOC}}</td>
                <td width="25">{{$item->NRO_SERIE}}</td>
                <td width="25">{{$item->NRO_DOC}}</td>
                <td width="25">{{$item->CLIENTE}}</td>
                <td width="25">{{$item->FEC_EMISION}}</td>
                <td width="25">{{$item->ESTADO_DOC_CTBLE}}</td>
                <td width="25">{{$item->NOM_TRABAJADOR}}</td> 
          </tr>
      @endforeach       

    </table>
</html>
