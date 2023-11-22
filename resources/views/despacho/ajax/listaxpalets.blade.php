<div class="scroll_text_horizontal_pallets" style = "padding: 0px !important;"> 
	@if(count($array_detalle_palets)>0)
  		<div style="width: 700px;margin-bottom: 10px;" >
  		  <h3>Lista de Productos con errores (<span class="icon mdi mdi-close"></span>)</h3>
		  <table id="dtitf" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatablageneral">
		    <thead>
		      <tr>
		      	<th>ITEM</th>
		        <th>PRODUCTO</th>
		        <th>EAN13</th>
		        <th>EAN14</th>
		        <th>SKU</th>
		      </tr>
		    </thead>
		    <tbody>
		      @foreach($array_detalle_palets as $index => $item)
		        <tr>
		        	<td>{{$index+1}}</td>
		            <td>
		            	{{$item['nombre_producto']}}
		            </td>
		            <td>
		            	@if($item['ean13']=='')
		            		<span class="icon mdi mdi-close"></span>
		            	@else
		            		<span class="icon mdi mdi-check"></span>
		            	@endif
		        	</td>
		            <td>
		            	@if($item['ean14']=='')
		            		<span class="icon mdi mdi-close"></span>
		            	@else
		            		<span class="icon mdi mdi-check"></span>
		            	@endif
		        	</td>       
		            <td>
		            	@if($item['sku']=='')
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
	  <div style="width: 1000px;margin-bottom: 10px;" >
		    <table class="table table-pedidos-despachos" style='font-size: 0.88em;' id="tablepedidodespacho" >
		    <thead>
		      <tr>
		      	<th>ITEM</th>


		        <th>Ean13</th>
		        <th>Fecha Ent.</th>
		        <th>Orden Cen</th>
		        <th>Ean14</th>
		        <th>Sku</th>
		        <th>Producto</th>
		        <th>Empaquetexpallet</th>
		        <th>Skuxpallet</th>
		        <th>Lpn</th>

		      </tr>
		    </thead>
		    <tbody>
				@foreach($listaxpalest as $index => $item)
			    <tr>

		        	<td>{{$index+1}}</td>

			        <td>{{$item->ean13}}</td>
			        <td>{{$item->fecha_entrega}}</td>
			        <td>{{$item->nro_orden_cen}}</td>
			        <td>{{$item->ean14}}</td>
			        <td>{{$item->sku}}</td>
			        <td>{{$item->producto_nombre}}</td>
			        <td>{{$item->empaquetexpallet}}</td>
			        <td>{{$item->skuxpallet}}</td>
			        <td>{{$item->lpn}}</td>
			    </tr>
			    @endforeach
		    </tbody>
		  	</table> 
		</div>
    @endif
</div>