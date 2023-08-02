<div class="scroll_text_horizontal_padding" style="padding: 0px !important;"> 
    <div style="width: 1680px;margin-bottom: 10px;">
        <table class="table table-condensed table-hover table-bordered table-striped">  
          <tbody>
           @foreach($cuadrocomisiones as $index=>$item)
              	<tr>
            			@for ($i = 0; $i < $item['cantidadarray']; $i++)

                    @php $colspan    =   '0'; @endphp
                    @php $creartd    =   '0'; @endphp
                    @php $clscenter  =   '0'; @endphp
                    @php $rowspan    =   '0'; @endphp
                    @php $creartdro  =   '0'; @endphp
                    @php $negrita    =   ''; @endphp


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

                    @if(isset($item['negrita'.$i]))
                        @php $negrita   =  $item['negrita'.$i]; @endphp
                    @endif   

                    @if(isset($item['center'.$i]))
                        @php $clscenter   =  $item['center'.$i]; @endphp
                    @endif 


                    @if(isset($item['colspan'.$i]))
                      @if($creartd == 0) 
                        <td colspan="{{$colspan}}" class="{{$clscenter}} {{$negrita}}" >{{$item['item'.$i]}}</td>
                      @endif
                    @else

                      @if(isset($item['rowspan'.$i]))
                        @if($creartdro == 0 && $i == 0) 
                          <td rowspan="{{$rowspan}}" class="{{$clscenter}} {{$negrita}}" >{{$item['item'.$i]}}</td>
                        @endif
                      @else
                          <td class='{{$clscenter}} {{$negrita}}'>{{$item['item'.$i]}}</td>
                      @endif
                    @endif  
            			 
            			@endfor
              	</tr>                    
            @endforeach
          </tbody>
        </table>
  </div>
</div>
@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif

