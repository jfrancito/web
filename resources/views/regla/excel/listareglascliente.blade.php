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
            <th class= 'center tablamar' colspan="2">REGLAS</th>  
        </tr>

        <tr>
            <th class= 'tabladp'>CLIENTE</th>
            <th class= 'tabladp'>PRODUCTO</th>
            <th class= 'center tablamar'>PRECIO PRODUCTO</th>              
            <th class= 'center warning'>NOTA DE CREDITO</th> 
        </tr>
        @php $contador    =   1; @endphp
        @foreach($listacliente as $index_c => $item_c) 
            @foreach($listadeproductos as $index => $item) 

                <!-- REGLAS DE LOS CLIENTES-->
                @php
                  $lista_reglas_cliente    =   $funcion->funciones->lista_reglas_cliente($item_c->COD_CONTRATO,$item->producto_id);
                @endphp

                @if(count($lista_reglas_cliente)>0) 

                    <tr>
                        <td>{{$item_c->NOM_EMPR}}</td>
                        <td>{{$item->NOM_PRODUCTO}}</td>
                        <td>

                              @foreach($lista_reglas_cliente as $index => $item)
                                @if ($item->tiporegla == 'POV') 
                                    @if($item->tipodescuento == 'POR') 
                                      %
                                    @else 
                                      S/.
                                    @endif
                                    {{number_format($item->descuento, 4, '.', ',')}} |
                                @endif
                              @endforeach

                        </td>
                        <td>

                              @foreach($lista_reglas_cliente as $index => $item)
                                @if ($item->tiporegla == 'PNC') 
                                    @if($item->tipodescuento == 'POR') 
                                      %
                                    @else 
                                      S/.
                                    @endif
                                    {{number_format($item->descuento, 4, '.', ',')}} |
                                @endif
                              @endforeach


                        </td>
                    </tr>
                    @php $contador    =   $contador + 1; @endphp

<!--                 @else 
                    <tr>
                        <td>{{$item_c->NOM_EMPR}}</td>
                        <td>{{$item->NOM_PRODUCTO}}</td>
                        <td></td>
                        <td></td>
                    </tr> -->
                @endif

            @endforeach
        @endforeach

    </table>
</html>
