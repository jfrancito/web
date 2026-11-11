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
        @foreach($consolidadovendedor_jefe as $index=>$item)

          <tr>
            @for ($i = 0; $i < $item['cantidadarray']; $i++)

          @php $colspan    =   '0'; @endphp
          @php $creartd    =   '0'; @endphp
          @php $clscenter  =   '0'; @endphp
          @php $rowspan    =   '0'; @endphp
          @php $creartdro  =   '0'; @endphp
          @php $negrita    =   ''; @endphp
          @php $bacgraound    =   ''; @endphp

          @if(isset($item['negrita'.$i]))
              @php $negrita   =  $item['negrita'.$i]; @endphp
          @endif   

          @if(isset($item['colspan'.$i]))
            @if($item['colspan'.$i] != '-1') 
              @php $colspan   =  $item['colspan'.$i]; @endphp
              @php $clscenter   =  'center'; @endphp
            @endif
          @endif
          @if(isset($item['colspan'.$i]) && $item['colspan'.$i] == '-1')
            @php $creartd   =  1; @endphp
          @endif
          @if(isset($item['rowspan'.$i]))
            @php $clscenter   =  'ddddd'; @endphp
            @if($item['rowspan'.$i] != '-1') 
              @php $rowspan   =  $item['rowspan'.$i]; @endphp
              @php $clscenter   =  'center'; @endphp
            @endif
          @endif


          @if(isset($item['rowspan'.$i]) && $item['rowspan'.$i] == '-1')
            @php $creartdro   =  1; @endphp
          @endif


          @if(isset($item['center'.$i]))
              @php $clscenter   =  $item['center'.$i]; @endphp
          @endif 

          @if(isset($item['bacgraound'.$i]))
              @php $bacgraound   =  $item['bacgraound'.$i]; @endphp
          @endif 

                @if(isset($item['colspan'.$i]))
                  @if($creartd == 0) 
                    <td width="15" colspan="{{$colspan}}" class="{{$clscenter}} {{$negrita}} {{$bacgraound}}" >{{$item['item'.$i]}}</td>
                  @endif
                @else

                  @if(isset($item['rowspan'.$i]))
                    @if($creartdro == 0 && $i == 0) 
                      <td width="15" rowspan="{{$rowspan}}" class="{{$clscenter}} {{$negrita}} {{$bacgraound}}" >{{$item['item'.$i]}}</td>
                    @endif
                  @else
                      <td width="15" class='{{$clscenter}} {{$negrita}} {{$bacgraound}}'>{{$item['item'.$i]}}</td>
                  @endif
                @endif 
            @endfor
          </tr>                    

        @endforeach    

    </table>
    <br><br>

    <table>
        @foreach($reportecomisionperidos_jefe as $index=>$item)

          <tr>
            @for ($i = 0; $i < $item['cantidadarray']; $i++)

          @php $colspan    =   '0'; @endphp
          @php $creartd    =   '0'; @endphp
          @php $clscenter  =   '0'; @endphp
          @php $rowspan    =   '0'; @endphp
          @php $creartdro  =   '0'; @endphp
          @php $negrita    =   ''; @endphp
          @php $bacgraound    =   ''; @endphp

          @if(isset($item['negrita'.$i]))
              @php $negrita   =  $item['negrita'.$i]; @endphp
          @endif   

          @if(isset($item['colspan'.$i]))
            @if($item['colspan'.$i] != '-1') 
              @php $colspan   =  $item['colspan'.$i]; @endphp
              @php $clscenter   =  'center'; @endphp
            @endif
          @endif
          @if(isset($item['colspan'.$i]) && $item['colspan'.$i] == '-1')
            @php $creartd   =  1; @endphp
          @endif
          @if(isset($item['rowspan'.$i]))
            @php $clscenter   =  'ddddd'; @endphp
            @if($item['rowspan'.$i] != '-1') 
              @php $rowspan   =  $item['rowspan'.$i]; @endphp
              @php $clscenter   =  'center'; @endphp
            @endif
          @endif


          @if(isset($item['rowspan'.$i]) && $item['rowspan'.$i] == '-1')
            @php $creartdro   =  1; @endphp
          @endif


          @if(isset($item['center'.$i]))
              @php $clscenter   =  $item['center'.$i]; @endphp
          @endif 

          @if(isset($item['bacgraound'.$i]))
              @php $bacgraound   =  $item['bacgraound'.$i]; @endphp
          @endif 

                @if(isset($item['colspan'.$i]))
                  @if($creartd == 0) 
                    <td width="15" colspan="{{$colspan}}" class="{{$clscenter}} {{$negrita}} {{$bacgraound}}" >{{$item['item'.$i]}}</td>
                  @endif
                @else

                  @if(isset($item['rowspan'.$i]))
                    @if($creartdro == 0 && $i == 0) 
                      <td width="15" rowspan="{{$rowspan}}" class="{{$clscenter}} {{$negrita}} {{$bacgraound}}" >{{$item['item'.$i]}}</td>
                    @endif
                  @else
                      <td width="15" class='{{$clscenter}} {{$negrita}} {{$bacgraound}}'>{{$item['item'.$i]}}</td>
                  @endif
                @endif 
            @endfor
          </tr>                    

        @endforeach    

    </table>




</html>
