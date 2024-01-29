<div class='row reporte'>
  
  <table id="tablereporte" class="table table-striped table-hover table-fw-widget">
    <thead>
      <tr>
        <th class= 'tabladp'>Fecha</th>             
        <th class= 'center tabladp'>Doc. Referencia</th> 
        <th class= 'center tabladp'>Tipo</th>
        <th class= 'center tabladp'>Detracción</th>
      </tr>
    </thead>
    <tbody>

      @php
          $total          =   0.0000;
      @endphp

      @foreach($listadetraccion as $item) 
		        <tr>

                <td>{{$item->FEC_DETRACCION}}</td>

                <td class='left negrita'>
                    {{$item->DOC_REFERENCIA}}
                </td>

                <td class='left negrita'>
                    @if($item->IND_DOC == 'GRR') 
                        GUÍA
                    @else
                        FACTURA 
                    @endif                     
                </td>

                <td class='left negrita'>
                      S/. {{ number_format($item->CAN_DETRACCION,2,'.',',') }}
                </td>

                @php
                      $total     =   $total + $item->CAN_DETRACCION;
                @endphp

		        </tr>
      @endforeach       

      <tr>
        <th></th>             
        <th></th> 
        <th>TOTAL</th>
        <th>S/. {{ number_format($total,2,'.',',') }}</th>
      </tr>

    </tbody>
  </table>

</div>

<script type="text/javascript">
  $(document).ready(function(){
     App.dataTables();
  });
</script> 