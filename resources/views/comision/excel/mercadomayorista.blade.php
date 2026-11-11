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
          <th class= 'center tablaho' colspan="3">ORIGEN (ORDEN DE VENTA)</th>
          @foreach($grupodos as $index => $item)
            <th class= 'center tablaho' colspan="2">{{$item->CAT_INF_NOM_CATEGORIA}}</th>
          @endforeach 
          <th class= 'center tablaho' colspan="2">TOTAL</th>
        </tr>
        <tr>
          <th class= 'center tablaho'>EMPRESA</th>
          <th class= 'center tablaho'>CANAL</th>
          <th class= 'center tablaho'>SUBCANAL</th>
          @foreach($grupodos as $index => $item)
            <th class= 'center tablaho'>Σ Saco 50kg</th>
            <th class= 'center tablaho'>Σ Comisión</th>
          @endforeach 
          <th class= 'center tablaho'>Σ Saco 50kg</th>
          <th class= 'center tablaho'>Σ Comisión</th>
        </tr>
        @php 
          $ts50               =  0;
          $tc50               =  0;
        @endphp
        @foreach($grupouno as $index => $item)
           <tr>
              <td width="15">{{$item->NOM_EMPR}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_CANAL_VENTA}}</td>
              <td width="25">{{$item->TXT_CATEGORIA_SUB_CANAL}}</td>

              @foreach($grupodos as $index2 => $item2)

              @php 
                $suma50               =  $funcion->funciones->sacos_50_comision('PESO_ORDEN_50',$codperiodo,$codcategoriajefe,$proviene,$item->NOM_EMPR,$item->TXT_CATEGORIA_CANAL_VENTA,$item->TXT_CATEGORIA_SUB_CANAL,$item2->CAT_INF_NOM_CATEGORIA);
                $comi50               =  $funcion->funciones->sacos_50_comision('TOTAL_COMISION',$codperiodo,$codcategoriajefe,$proviene,$item->NOM_EMPR,$item->TXT_CATEGORIA_CANAL_VENTA,$item->TXT_CATEGORIA_SUB_CANAL,$item2->CAT_INF_NOM_CATEGORIA);
              @endphp
                <td width="15">{{$suma50}}</td>
                <td width="15">{{$comi50}}</td>
              @endforeach

              @php    
                $suma50_t               =  $funcion->funciones->sacos_50_comision_t('PESO_ORDEN_50',$codperiodo,$codcategoriajefe,$proviene,$item->NOM_EMPR,$item->TXT_CATEGORIA_CANAL_VENTA,$item->TXT_CATEGORIA_SUB_CANAL);
                $comi50_t               =  $funcion->funciones->sacos_50_comision_t('TOTAL_COMISION',$codperiodo,$codcategoriajefe,$proviene,$item->NOM_EMPR,$item->TXT_CATEGORIA_CANAL_VENTA,$item->TXT_CATEGORIA_SUB_CANAL);
              @endphp

              <td width="15">{{$suma50_t}}</td>
              <td width="15">{{$comi50_t}}</td>

              @php 
                $ts50               =  $ts50 + $suma50_t;
                $tc50               =  $tc50 + $comi50_t;
              @endphp
            </tr>   
        @endforeach 
           <tr>
              <td width="15" colspan="3">TOTALES</td>

              @foreach($grupodos as $index2 => $item2)

              @php 
                $suma50               =  $funcion->funciones->sacos_50_comision_totales('PESO_ORDEN_50',$codperiodo,$codcategoriajefe,$proviene,$item2->CAT_INF_NOM_CATEGORIA);
                $comi50               =  $funcion->funciones->sacos_50_comision_totales('TOTAL_COMISION',$codperiodo,$codcategoriajefe,$proviene,$item2->CAT_INF_NOM_CATEGORIA);
              @endphp
                <td width="15">{{$suma50}}</td>
                <td width="15">{{$comi50}}</td>
              @endforeach

              <td width="15" style="font-weight: bold;">{{$ts50}}</td>
              <td width="15" style="font-weight: bold;">{{$tc50}}</td>
            </tr>  
    </table>

    @if(count($grupouno_nc ) >0 )
      <table>
        <tr>
          <th class= 'center tablamar' colspan="3">ORIGEN (NOTA DE CREDITO)</th>
          @foreach($grupodos as $index => $item)
            <th class= 'center tablamar' colspan="2">{{$item->CAT_INF_NOM_CATEGORIA}}</th>
          @endforeach 
          <th class= 'center tablamar' colspan="2">TOTAL</th>
        </tr>
        <tr>
          <th class= 'center tablamar'>EMPRESA</th>
          <th class= 'center tablamar'>CANAL</th>
          <th class= 'center tablamar'>SUBCANAL</th>
          @foreach($grupodos as $index => $item)
            <th class= 'center tablamar'>Σ Saco 50kg</th>
            <th class= 'center tablamar'>Σ Comisión</th>
          @endforeach 
          <th class= 'center tablamar'>Σ Saco 50kg</th>
          <th class= 'center tablamar'>Σ Comisión</th>
        </tr>
        @php 
          $ts50               =  0;
          $tc50               =  0;
        @endphp
        @foreach($grupouno_nc as $index => $item)
           <tr>
              <td width="15">{{$item->NOM_EMPR}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_CANAL_VENTA}}</td>
              <td width="25">{{$item->TXT_CATEGORIA_SUB_CANAL}}</td>

              @foreach($grupodos_nc as $index2 => $item2)

              @php 
                $suma50               =  $funcion->funciones->sacos_50_comision_nc('PESO_ORDEN_50',$codperiodo,$codcategoriajefe,$proviene,$item->NOM_EMPR,$item->TXT_CATEGORIA_CANAL_VENTA,$item->TXT_CATEGORIA_SUB_CANAL,$item2->CAT_INF_NOM_CATEGORIA);
                $comi50               =  $funcion->funciones->sacos_50_comision_nc('TOTAL_COMISION',$codperiodo,$codcategoriajefe,$proviene,$item->NOM_EMPR,$item->TXT_CATEGORIA_CANAL_VENTA,$item->TXT_CATEGORIA_SUB_CANAL,$item2->CAT_INF_NOM_CATEGORIA);
              @endphp
                <td width="15">{{$suma50}}</td>
                <td width="15">{{$comi50}}</td>
              @endforeach

              @php    
                $suma50_t               =  $funcion->funciones->sacos_50_comision_t_nc('PESO_ORDEN_50',$codperiodo,$codcategoriajefe,$proviene,$item->NOM_EMPR,$item->TXT_CATEGORIA_CANAL_VENTA,$item->TXT_CATEGORIA_SUB_CANAL);
                $comi50_t               =  $funcion->funciones->sacos_50_comision_t_nc('TOTAL_COMISION',$codperiodo,$codcategoriajefe,$proviene,$item->NOM_EMPR,$item->TXT_CATEGORIA_CANAL_VENTA,$item->TXT_CATEGORIA_SUB_CANAL);
              @endphp

              <td width="15">{{$suma50_t}}</td>
              <td width="15">{{$comi50_t}}</td>

              @php 
                $ts50               =  $ts50 + $suma50_t;
                $tc50               =  $tc50 + $comi50_t;
              @endphp
            </tr>   
        @endforeach 
           <tr>
              <td width="15" colspan="3">TOTALES</td>
              @foreach($grupodos_nc as $index2 => $item2)
              @php 
                $suma50               =  $funcion->funciones->sacos_50_comision_totales_nc('PESO_ORDEN_50',$codperiodo,$codcategoriajefe,$proviene,$item2->CAT_INF_NOM_CATEGORIA);
                $comi50               =  $funcion->funciones->sacos_50_comision_totales_nc('TOTAL_COMISION',$codperiodo,$codcategoriajefe,$proviene,$item2->CAT_INF_NOM_CATEGORIA);
              @endphp
                <td width="15">{{$suma50}}</td>
                <td width="15">{{$comi50}}</td>
              @endforeach

              <td width="15" style="font-weight: bold;">{{$ts50}}</td>
              <td width="15" style="font-weight: bold;">{{$tc50}}</td>
            </tr>  
    </table>
    @endif

    <!-- titulo -->

    @if(count($detalleuno ) >0 ) 
        @php 
          $total          =   0;
        @endphp

    <table>

        <tr>
          <th class= 'center tablaho'>VENDEDOR</th>
          <th class= 'center tablaho'>SUBFAMILA</th>
          <th class= 'center tablaho'>PESO 50KG</th>
          <th class= 'center tablaho'>COMISION</th>
          <th class= 'center tablaho'>TOTAL COMISION</th>
        </tr>
        @foreach($detalleuno as $index => $item)
          @php 
            $total          =   $total + $item->TOTAL_COMISION;
          @endphp
           <tr>
              <td width="15">{{$item->TXT_CATEGORIA_JEFE_VENTA_ASIMILADO}}</td>
              <td width="15">{{$item->CAT_INF_NOM_CATEGORIA}}</td>
              <td width="15">{{$item->PESO_ORDEN_50}}</td>
              <td width="15">{{$item->COMISION}}</td>
              <td width="15">{{$item->TOTAL_COMISION}}</td>
            </tr>     
        @endforeach 

           <tr>
              <td width="15"></td>
              <td width="15"></td>
              <td width="15"></td>
              <td width="15"></td>
              <td width="15" style="font-weight: bold;">{{$total}}</td>
            </tr> 

    </table>
    @endif


    <table>

        <tr>
          <th class= 'center tablaho'>FECHA VENTA</th>
          <th class= 'center tablaho'>ORDEN</th>
          <th class= 'center tablaho'>DOCUMENTO</th>
          <th class= 'center tablaho'>CLIENTE</th>
          <th class= 'center tablaho'>PRODUCTO</th>

          <th class= 'center tablaho'>FAMILIA</th>
          <th class= 'center tablaho'>SUB FAMILIA</th>
          <th class= 'center tablaho'>UNIDAD</th>
          <th class= 'center tablaho'>CANTIDAD</th>
          <th class= 'center tablaho'>P.U.</th>

          <th class= 'center tablaho'>TOTAL P.</th>
          <th class= 'center tablaho'>PESO 50KG</th>
          <th class= 'center tablaho'>CANAL</th>
          <th class= 'center tablaho'>SUB CANAL</th>
          <th class= 'center tablaho'>FECHA PAGO</th>

          <th class= 'center tablaho'>PRODUCTO COBRADO</th>
          <th class= 'center tablaho'>DIFF</th>
          <th class= 'center tablaho'>CONDICION PAGO</th>

          <th class= 'center tablaho'>EVALUACION</th>
          <th class= 'center tablaho'>TXTCATEGORIAJEFEVENTA</th>
          <th class= 'center tablaho'>COMISION</th>
          <th class= 'center tablaho'>TOTAL COMISION</th>
        </tr>
        @foreach($detalledos as $index => $item)
           <tr>
              <td width="15">{{$item->FEC_ORDEN}}</td>
              <td width="15">{{$item->COD_ORDEN}}</td>
              <td width="15">{{$item->COD_DOCUMENTO}}</td>
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

              <td width="15">{{$item->PLAZO_PAGO}}</td>

              <td width="15">{{$item->VAL}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_JEFE_VENTA}}</td>
              <td width="15">{{$item->COMISION}}</td>
              <td width="15">{{$item->TOTAL_COMISION}}</td>
            </tr>     
        @endforeach     
    </table>

    @if(count($detalledos_nc ) >0 ) 
    <table>

        <tr>
          <th class= 'center tablamar'>FECHA VENTA</th>
          <th class= 'center tablamar'>ORDEN</th>
          <th class= 'center tablamar'>DOCUMENTO</th>
          <th class= 'center tablamar'>CLIENTE</th>
          <th class= 'center tablamar'>PRODUCTO</th>

          <th class= 'center tablamar'>FAMILIA</th>
          <th class= 'center tablamar'>SUB FAMILIA</th>
          <th class= 'center tablamar'>UNIDAD</th>
          <th class= 'center tablamar'>CANTIDAD</th>
          <th class= 'center tablamar'>P.U.</th>

          <th class= 'center tablamar'>TOTAL P.</th>
          <th class= 'center tablamar'>PESO 50KG</th>
          <th class= 'center tablamar'>CANAL</th>
          <th class= 'center tablamar'>SUB CANAL</th>
          <th class= 'center tablamar'>FECHA PAGO</th>

          <th class= 'center tablamar'>PRODUCTO COBRADO</th>
          <th class= 'center tablamar'>DIFF</th>
          <th class= 'center tablamar'>CONDICION PAGO</th>


          <th class= 'center tablamar'>EVALUACION</th>
          <th class= 'center tablamar'>TXTCATEGORIAJEFEVENTA</th>
          <th class= 'center tablamar'>COMISION</th>
          <th class= 'center tablamar'>TOTAL COMISION</th>
        </tr>
        @foreach($detalledos_nc as $index => $item)
           <tr>
              <td width="15">{{$item->FEC_ORDEN}}</td>
              <td width="15">{{$item->COD_ORDEN}}</td>
              <td width="15">{{$item->COD_DOCUMENTO}}</td>
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
              <td width="15">{{$item->PLAZO_PAGO}}</td>
              
              <td width="15">{{$item->VAL}}</td>
              <td width="15">{{$item->TXT_CATEGORIA_JEFE_VENTA}}</td>
              <td width="15">{{$item->COMISION}}</td>
              <td width="15">{{$item->TOTAL_COMISION}}</td>
            </tr>     
        @endforeach     
    </table>
    @endif
</html>
