<div class='row reporte'>
  
  <table id="tablereporte" class="table table-striped table-hover table-fw-widget" style="font-size: 12px;">
    <thead>
      <tr>
        <th class= 'center tabladp' colspan='3'>DATOS</th>        
        <th class= 'center tablaho' colspan='5'>PRECIO</th>        
      </tr>

      <tr>
        <th class= 'tabladp'>ID</th>        
        <th class= 'tabladp'>CLIENTE</th>
        <th class= 'tabladp'>PRODUCTO</th>
        <th class= 'center'>FECHA</th> 

        <th class= 'center tablaho'>DEPARTAMENTO</th>              
        <th class= 'center tablaho'>PRECIO REGULAR</th> 
        <th class= 'center tablaho'>DESCUENTO</th>
        <th class= 'center tablaho'>PRECIO TOTAL</th> 

      </tr>
    </thead>
    <tbody>
	  	@foreach($listacliente as $index_c => $item_c)

            @php
              $listadeproductos    =   $funcion->funciones->lista_productos_precio_favotitos($item_c->COD_CONTRATO);
            @endphp

            @foreach($listadeproductos as $index => $item) 
		        <tr>
		        	  <td class='negrita'>{{$index + 1}}</td>
		            <td>{{$item_c->NOM_EMPR}}</td>
                <td>{{$item->NOM_PRODUCTO}}</td>

                <!-- PRECIOS CON DEPARTAMENTOS-->
                @php
                  $lista_precio_regular_departamento    =   $funcion->funciones->lista_precio_regular_departamento($item_c->COD_CONTRATO,$item->producto_id);
                @endphp
                
                <td class='negrita'>
                    {{ date_format(date_create($funcion->funciones->calculo_fecha_regular($item_c,$item)), 'd-m-Y')}}
                    @foreach($lista_precio_regular_departamento as $index_pr => $item_pr) 
                    <br>
                    {{date_format(date_create($item_pr->fecha_crea), 'd-m-Y')}}
                    @endforeach
                </td>

                <td class='negrita'>
                  OTROS
                  <br>
                  @foreach($lista_precio_regular_departamento as $index_pr => $item_pr)
                  {{$funcion->funciones->departamento($item_pr->departamento_id)->NOM_CATEGORIA}}
                  <br>
                  @endforeach
                </td>

                <td class='left negrita columna_marcada1'>
                      S/. {{$funcion->funciones->calculo_precio_regular($item_c,$item)}}
                      @foreach($lista_precio_regular_departamento as $index_pr => $item_pr) 
                      <br>
                      S/. {{number_format($item_pr->descuento, 2, '.', ',')}}
                      @endforeach
                </td>

                <td class='left negrita'>
                      S/. {{$funcion->funciones->descuento_reglas_producto($item_c->COD_CONTRATO,$item->producto_id,$item_c->id,'')}}
                      @foreach($lista_precio_regular_departamento as $index_pr => $item_pr) 
                      <br>
                      S/. {{$funcion->funciones->descuento_reglas_producto($item_c->COD_CONTRATO,$item->producto_id,$item_c->id,$item_pr->departamento_id)}}
                      @endforeach
                </td>

                <td class='left negrita '>
                      S/. {{$funcion->funciones->precio_descuento_reglas_producto($item_c->COD_CONTRATO,$item->producto_id,$item_c->id,'')}}
                      @foreach($lista_precio_regular_departamento as $index_pr => $item_pr) 
                      <br>
                      S/. {{$funcion->funciones->precio_descuento_reglas_producto($item_c->COD_CONTRATO,$item->producto_id,$item_c->id,$item_pr->departamento_id)}}
                      @endforeach
                </td>

		        </tr>
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