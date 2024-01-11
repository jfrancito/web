<div class='row reporte'>
  
  <table id="tablereporte" class="table table-striped table-hover table-fw-widget">
    <thead>
      <tr>
        <th class= 'center tabladp' colspan='3'>DATOS</th>        
        <th class= 'center tablamar' colspan='2'>REGLAS</th>        
      </tr>

      <tr>
        <th class= 'tabladp'>ID</th>        
        <th class= 'tabladp'>CLIENTE</th>
        <th class= 'tabladp'>PRODUCTO</th>
        <th class= 'center tablamar'>PRECIO PRODUCTO</th>        
        <th class= 'center warning '>NOTA DE CREDITO</th>        
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

                  <!-- REGLAS DE LOS CLIENTES-->


                  <td class='negrita'>
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

                  <td class='negrita'>
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