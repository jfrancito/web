<table>
    <thead>
    <tr>
        <th style="background: #0b81bb;border: 1px solid #000000;text-align: center; color: #ffffff">SUCURSAL</th>
        @foreach($lista_cabecera as $item)
            <th style="background: #0b81bb;border: 1px solid #000000;text-align: center; color: #ffffff">{{$item[0]}}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>

    @foreach($lista_migracion as $item1)
        <tr >
            <td style="border: 1px solid #000000;text-align: left; color: #000000">{{$item1[0]}}</td>
            @for ($i = 1; $i <= $contador; $i++)
                <td style="border: 1px solid #000000;text-align: right; background: @if($item1[$i]<=0.0000) #ff0000 @else #ffffff @endif">{{$item1[$i]}}</td>
            @endfor
        </tr>
    @endforeach
    </tbody>
</table>