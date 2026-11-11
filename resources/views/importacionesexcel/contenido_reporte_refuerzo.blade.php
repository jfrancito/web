<table>
    <thead>
    <tr>
        <th style="background: #1c005b;border: 1px solid #000000;text-align: center; justify-content: center; color: #ffffff" colspan="{{$contador_general}}">{{$producto}}</th>
    </tr>
    <tr>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center; color: #ffffff">N°</th>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center; color: #ffffff">COD</th>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center; color: #ffffff">TIENDA</th>
        <th style="background: #6863cf;border-left: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center; color: #ffffff">V. prom (m)</th>
        <th style="background: #6863cf;border-left: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center; color: #ffffff">V. prom semanal</th>
        <th style="background: #6863cf;border-left: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center; color: #ffffff">V. prom diaria</th>
        <th style="background: #6863cf;border: 1px solid #000000;text-align: center; color: #ffffff" colspan="{{intval($contador_listadias)}}">STOCK</th>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center; color: #ffffff">REFUERZO</th>
    </tr>
    <tr>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #6863cf;border-left: 1px solid #000000;border-right: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #6863cf;border-left: 1px solid #000000;border-right: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #6863cf;border-left: 1px solid #000000;border-right: 1px solid #000000;text-align: center; color: #ffffff"></th>
        @foreach($lista_meses as $item1)
            <th style="background: #4c45d5;border: 1px solid #000000;text-align: center; color: #ffffff" colspan="{{$item1[2]}}">{{$item1[0]}}</th>
        @endforeach
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;text-align: center; color: #ffffff"></th>
    </tr>
    <tr>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #6863cf;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #6863cf;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;text-align: center; color: #ffffff"></th>
        <th style="background: #6863cf;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;text-align: center; color: #ffffff"></th>
        @foreach($lista_dias as $item2)
            <th style="background: #4c45d5;border: 1px solid #000000;text-align: center; color: #ffffff">{{$item2[2]}}</th>
        @endforeach
        <th style="background: #4c45d5;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;text-align: center; color: #ffffff"></th>
    </tr>
    </thead>
    <tbody>
    @foreach($lista_migracion as $item3)
        <tr>
            <td style="background: #9d9ade;border: 1px solid #000000;text-align: left; color: #000000">{{$item3[0]}}</td>
            <td style="background: #9d9ade;border: 1px solid #000000;text-align: left; color: #000000">{{$item3[1]}}</td>
            <td style="background: #9d9ade;border: 1px solid #000000;text-align: left; color: #000000">{{$item3[2]}}</td>
            <td style="background: #6863cf; border: 1px solid #000000;text-align: right; color: #000000">{{$item3[3]}}</td>
            <td style="background: #6863cf; border: 1px solid #000000;text-align: right; color: #000000">{{$item3[4]}}</td>
            <td style="background: #6863cf; border: 1px solid #000000;text-align: right; color: #000000">{{$item3[5]}}</td>
            @for ($i = 1; $i <= $contador_listadias; $i++)
                <td style="border: 1px solid #000000;
                text-align: right;
                background: @if($item3[$i+5]<=0.0000) #ff0000 @else @if($item3[$i+5]>0.0000 and $item3[$i+5]<=20.0000) #fca1a1 @else #ffffff @endif @endif;
                font-weight: @if($item3[$i+5]<=20.0000) bold @else normal @endif;
                color: @if($item3[$i+5]<=0.0000) #000000 @else @if($item3[$i+5]>0.0000 and $item3[$i+5]<=20.0000) #5b0000 @else #000000 @endif @endif;
                ">{{$item3[$i+5]}}</td>
            @endfor
            <td style="background: #d7d703; border: 1px solid #000000;text-align: right; color: #000000">0.0000</td>
        </tr>
    @endforeach
    <tr>
        <td style="font-weight: bold; background: #4c45d5;border: 1px solid #000000;text-align: left; color: #ffffff" colspan="3">TOTAL</td>
        @foreach($lista_totales as $item4)
            <td style="font-weight: bold; background: #4c45d5;border: 1px solid #000000;text-align: right; color: #ffffff">{{$item4}}</td>
        @endforeach
    </tr>
    </tbody>
</table>