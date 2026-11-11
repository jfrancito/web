
<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			ORDEN DE PEDIDO N° : {{$codigo}}
		</h5>
	</div>

</div>
<div class="modal-body">

	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 

	<table id="pled" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
	    <thead>
	      <tr>
	      	<th>Cliente</th>
	      	<th>Producto</th>
	        <th>Orden Cen</th>
	        <th>Empaque X Pallet</th>
	        <th>Sku X Empaque</th>
	        <th>Bls/Sc X Pallet</th>	        
	        <th>Sku</th>
	        <th>Costo Sku</th>
	        <th>Ean13</th>
	        <th>Ean14</th>
	        <th>Lpn</th>
	      </tr>
	    </thead>
	    <tbody>
	    @foreach($listaimprimir as $index => $item)
	      	<tr>
	      	   <td>{{$item->NOM_EMPR}}</td>
	      	   <td>{{$item->NOM_PRODUCTO}}</td>
		       <td>{{$item->nro_orden_cen}}</td>
		       <td>{{$item->empaquetexpallet}}</td>
	      	   <td>{{$item->skuxpallet}}</td>
	      	   <td>{{$item->blsscxpallet}}</td>
		       <td>{{$item->sku}}</td>
		       <td>{{$item->costosku}}</td>
	      	   <td>{{$item->ean13}}</td>
	      	   <td>{{$item->ean14}}</td>
		       <td>{{$item->lpn}}</td>
	      	</tr>                  
	    @endforeach
	    </tbody>

	</table>
	</div>
</div>
<div class="modal-footer">

	<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){

	    $("#pled").dataTable({
	        dom: 'Bfrtip',
	        "scrollX": true,
	        buttons: [
	            'csv', 'excel', 'pdf'
	        ],
	        "lengthMenu": [[250, 500, -1], [250, 500, "All"]],
	        columnDefs:[{
	            targets: "_all",
	            sortable: false
	        }]
	    });

    });
  </script> 
@endif



