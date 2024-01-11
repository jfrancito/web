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
            <td class = 'subtitulos' colspan="4">{{$empresa}} - {{$centro}}</td>                    
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
        <tr>
            <th class= 'tabladp'>CLIENTE</th>
            <th class= 'tabladp'>PRODUCTO</th>
            @foreach($listadereglas as $index => $item) 
                <th class= 'center tablamar'>{{$item->nombre}}</th>
            @endforeach
            <th class= 'tabladp'>TOTAL</th>

        </tr>
        @php $contador    =   1; @endphp
        @foreach($listacliente as $index_c => $item_c) 
            @foreach($listadeproductos as $index => $item)
                @php
                  $lista_reglas_cliente    =   $funcion->funciones->lista_reglas_cliente($item_c->COD_CONTRATO,$item->producto_id);
                @endphp
                @if(count($lista_reglas_cliente)>0) 
                    <tr>
                        <td>{{$item_c->NOM_EMPR}}</td>
                        <td>{{$item->NOM_PRODUCTO}}</td>

                        @php $total    =   0; @endphp
                        @foreach($listadereglas as $indexr => $itemr) 
                            @php
                              $regla_cliente    =   $funcion->funciones->lista_reglas_cliente_total($item_c->COD_CONTRATO,$item->producto_id,$itemr->id);
                            @endphp
                            <td>
                                {{number_format($regla_cliente, 2, '.', ',')}}
                            </td>
                            @php $total    =   $total + $regla_cliente; @endphp
                        @endforeach
                        <td>{{$total}}</td>


                    </tr>
                    @php $contador    =   $contador + 1; @endphp
                @endif
            @endforeach
        @endforeach
    </table>
</html>
