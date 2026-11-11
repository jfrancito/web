<div class="scroll_text_horizontal_canti" style = "padding: 0px !important;"> 
	@if(count($array_detalle_cantidad)>0)
  		<div style="width: 700px;margin-bottom: 10px;" >
  		  <h3>Lista de Productos con errores (<span class="icon mdi mdi-close"></span>)</h3>
		  <table id="dtitf" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatablageneral">
		    <thead>
		      <tr>
		        <th>PRODUCTO</th>
		        <th>EAN14</th>
		        <th>EAN13</th>
		      </tr>
		    </thead>
		    <tbody>
		      @foreach($array_detalle_cantidad as $index => $item)
		        <tr>
		            <td>
		            	{{$item['nombre_producto']}}
		            </td>
		            <td>
		            	@if($item['ean14']=='')
		            		<span class="icon mdi mdi-close"></span>
		            	@else
		            		<span class="icon mdi mdi-check"></span>
		            	@endif
		        	</td> 
		            <td>
		            	@if($item['ean13']=='')
		            		<span class="icon mdi mdi-close"></span>
		            	@else
		            		<span class="icon mdi mdi-check"></span>
		            	@endif
		        	</td>       
		        </tr>  
		      @endforeach
		    </tbody>
		  </table>
		</div>
	@else

	  	<div style="width: 800px;margin-bottom: 10px;" >
		    <table class="table table-pedidos-despachos" style='font-size: 0.88em;' id="tablepedidodespacho" >
		    <thead>
		      <tr>
		      	<th>Item</th>
		        <th>Nro_orden_cen</th>
		        <th>Producto_nombre</th>
		        <th>Ean14</th>
		        <th>Ean13</th>
		      </tr>
		    </thead>
		    <tbody>
				@foreach($listaxcantidad as $index => $item)
			    <tr>
			    	<td>{{$index + 1}}</td>
			        <td>{{$item->nro_orden_cen}}</td>
			        <td>{{$item->producto_nombre}}</td>
			        <td>
		            	@if($item->ind_ean13!=1)
		            		{{$item->ean14}}
		            	@endif
			        </td>
			        <td>
		            	@if($item->ind_ean13==1)
		            		{{$item->ean14}}
		            	@endif
			        </td>
			    </tr>
			    @endforeach
		    </tbody>
		  	</table> 
		</div>
    @endif

</div>