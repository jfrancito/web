<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	{!! Html::style('public/css/excel/excel.css') !!}
    <!-- titulo -->
    <table>

        <tr>
          <th class= 'center tablaho'>COD_BOLETA</th>             
          <th class= 'center tablaho'>BOLETA</th> 
          <th class= 'center tablaho'>COD_GRR</th>
          <th class= 'center tablaho'>GRR</th>
          <th class= 'center tablaho' >CAN_TOTAL_GRR</th>
        </tr>

      @foreach($listadetracciones as $index => $item) 
          <tr>
                <td width="20">{{$item->CODIGO}}</td>
                <td width="20">{{$item->SERIE}} - {{$item->DOC}}</td>
                <td width="20">{{$item->COD_DOC_ASOC}}</td>
                <td width="20">{{$item->DOC_ASOC}}</td>
                <td width="15">{{$item->CAN_TOTAL}}</td>  
          </tr>
      @endforeach       

    </table>
</html>
