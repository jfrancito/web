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
.rigth{
  text-align: right;
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
  color: #ffffff;
}
  </style>

    <!-- titulo -->
    <table>
        <tr>
          <th class= 'blanco'></th>
        </tr>
        <tr>
          <th class= 'center tablaho'></th>
          <th class= 'center tablaho'>FECHA</th>             
          <th class= 'center tablaho'>EMPRESA</th>
          <th class= 'center tablaho'>TIPO</th>  
          <th class= 'center tablaho'>DIV</th>
          <th class= 'center tablaho'>DETALLE</th>
          <th class= 'center tablaho' >DETALLE</th>
          <th class= 'center tablaho' >CREDITO</th>
          <th class= 'center tablaho' >PAGO</th>
          <th class= 'center tablaho' >SALDO</th>
        </tr>
      @foreach($listadatos as $index => $item) 
          <tr>
                <td width="5">{{$index + 1}}</td>
                <td width="10">{{date_format(date_create($item['fec_habilitacion']), 'd-m-Y')}}</td>
                <td width="30">{{$item['empresa_id']}}</td>
                <td width="15">{{$item['accion']}}</td>
                <td width="30">{{$item['div']}}</td>
                <td width="30">{{$item['factura']}}</td>
                <td width="40">{{$item['dp_concat']}}</td>
                <td class="rigth" width="15">{{number_format($item['credito'], 2, '.', ',')}}</td>
                <td class="rigth" width="15">{{number_format($item['pago'], 2, '.', ',')}}</td>
                <td class="rigth" width="15">{{number_format($item['saldo'], 2, '.', ',')}}</td>
          </tr>
      @endforeach       

    </table>
</html>
