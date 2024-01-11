<div class='row reporte'>
  
  <table id="tablereporte" class="table table-striped table-hover table-fw-widget">
    <thead>
      <tr>
        <th class= 'tabladp'>ID</th>        
        <th class= 'tabladp'>CLIENTE</th>
        <th class= 'tabladp'>PRODUCTO</th>
        @foreach($listadereglas as $index => $item) 
            <th class= 'warning'>{{$item->nombre}}</th>
        @endforeach
        <th class= 'tabladp'>TOTAL</th>

      </tr>
    </thead>
    <tbody>
      @php $contador    =   1; @endphp

	  	@foreach($listacliente as $index_c => $item_c)
            @foreach($listadeproductos as $index => $item) 
            @php
              $lista_reglas_cliente    =   $funcion->funciones->lista_reglas_cliente($item_c->COD_CONTRATO,$item->producto_id);
            @endphp

            @if(count($lista_reglas_cliente)>0)

  		        <tr data-producto = '{{$item->NOM_PRODUCTO}}'>
  		        	  <td class='negrita'>{{$contador}}</td>
  		            <td>{{$item_c->NOM_EMPR}}</td>
                  <td>{{$item->NOM_PRODUCTO}}</td>

                  @php $total    =   0; @endphp
                  @foreach($listadereglas as $indexr => $itemr) 
                      @php
                        $regla_cliente    =   $funcion->funciones->lista_reglas_cliente_total($item_c->COD_CONTRATO,$item->producto_id,$itemr->id);
                      @endphp
                      <td>
                          {{$regla_cliente}}
                      </td>
                      @php $total    =   $total + $regla_cliente; @endphp
                  @endforeach
                  <td>{{$total}}</td>

  		        </tr>
              @php $contador    =   $contador + 1; @endphp
            @endif

            @endforeach       
	  	@endforeach
    </tbody>
  </table>
</div>

<script type="text/javascript">
  $(document).ready(function(){
     App.dataTables();
  });
</script> 