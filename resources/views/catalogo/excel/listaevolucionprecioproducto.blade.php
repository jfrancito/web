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
            <td class = 'subtitulos' colspan="4">{{$empresa}} - {{$centro}}</td>                    
        </tr>
        <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <th class= 'center tabladp' colspan="2">DATOS</th>
            <th class= 'center tablaho' colspan="3">PRECIO ({{$fechafin}})</th>
            <th class= 'center tablaho'  >VENTA</th>  
        </tr>

        <tr>
            <th class= 'tabladp'>CLIENTE</th>
            <th class= 'tabladp'>PRODUCTO</th>             
            <th class= 'center tablaho'>PRECIO REGULAR</th> 
            <th class= 'center tablaho'>DESCUENTO</th>
            <th class= 'center tablaho'>PRECIO TOTAL</th> 
            <th class= 'center tablaho'>ORDEN CEN</th>
        </tr>

        @foreach($listacliente as $index_c => $item_c) 
            @foreach($listadeproductos as $index => $item) 

                @php
                  $precio_regular    =   0.0000;
                  $descuento         =   0.0000;
                  $ordencen          =   0.0000;


                  $precio_regular    =   $funcion->funciones->calculo_precio_regular_fecha($item_c,$item,$fechafin);
                  $descuento         =   $funcion->funciones->descuento_reglas_producto_fecha($item_c->COD_CONTRATO,$item->producto_id,$item_c->id,'',$fechafin);
                  $precio_descuento  =   (float)$precio_regular - (float)$descuento;


                  $ordencen         =    $funcion->funciones->calculo_precio_venta($item_c,$item,$fechafin);
                @endphp

                @if(($index % 2) == 0 ) 
                    @php  $color = 'tablafila1'; @endphp
                @else 
                    @php  $color = 'tablafila2'; @endphp
                @endif

                <tr>
                    <td width="50" class='{{$color}}'> {{$item_c->NOM_EMPR}}</td>
                    <td width="50" class='{{$color}}'>{{$item->NOM_PRODUCTO}}</td>
                    <td width="20" class='negrita {{$color}}'>
                        S/. {{$precio_regular}}
                    </td>
                    <td width="20" class='negrita {{$color}}'>
                        S/. {{$descuento}}
                    </td>
                    <td width="20" class='negrita {{$color}}'>
                        S/. {{$precio_descuento}}
                    </td> 


                      <td  width="20" class='negrita {{$color}}'>
                        @if($ordencen > 0) 
                          S/. {{$ordencen}} 
                        @else
                          - 
                        @endif       
                      </td>

                </tr>


            @endforeach
        @endforeach

    </table>
</html>
