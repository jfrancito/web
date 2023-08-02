
<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$calculobono->jefeventa_nombre}}
		</h5>
	</div>

	<div class="col-xs-2">
		Alcance Inicial : {{$calculobono->alcance_inicial}} %
	</div>
	<div class="col-xs-2">
		Alcance Final : {{$calculobono->alcance_final}} %
	</div>	
	<div class="col-xs-2">
		Cuota : {{$calculobono->cuota}}
	</div>
	<div class="col-xs-2">
		Venta : {{$calculobono->venta}}
	</div>
	<div class="col-xs-2">
		Nota de credito : {{$calculobono->nc}}
	</div>
	<div class="col-xs-2">
		Alcance : {{$calculobono->alcance}}
	</div>	
	<div class="col-xs-2">
		Bono : {{$calculobono->bono}}
	</div>

</div>
<div class="modal-body">

	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 

	<table class="table table-condensed table-striped">
	    <thead>
	      <tr>
	      	<th>Jefe Venta</th>
	        <th>Canal</th>
	        <th>Subcanal</th>
	        <th>Cuota</th>
	        <th>Venta Det.</th>	        
	        <th>Venta</th>
	        <th>Nota de Credito Det.</th>	
	        <th>Nota de Credito</th>

	        <th>Alcance</th>
	        <th>Bono</th>
	      </tr>
	    </thead>
	    <tbody>
	    @foreach($listadcb as $index => $item)
	      	<tr>
	      	   <td>{{$item->jefeventa_nombre}}</td>
		       <td>{{$item->canal_nombre}}</td>
		       <td>{{$item->subcanal_nombre}}</td>
		       <td>{{number_format($item->cuota, 2, '.', ',')}}</td>
		       <td>{{number_format($item->venta, 2, '.', ',')}}</td>
		       @if($index==0)
		        <td rowspan="{{count($listadcb)}}" 
		        style='font-weight: bold;text-align: center;'>{{number_format($calculobono->venta, 2, '.', ',')}}</td>
		       @endif

		       <td>{{number_format($item->nc, 2, '.', ',')}}</td>
		       @if($index==0)
		        <td rowspan="{{count($listadcb)}}" style='font-weight: bold;text-align: center;'>{{number_format($calculobono->nc, 2, '.', ',')}}</td>
		       @endif		       
		       @if($index==0)
		        <td rowspan="{{count($listadcb)}}" style='font-weight: bold;text-align: center;'>{{number_format($calculobono->alcance, 2, '.', ',')}}</td>
		       @endif
		       @if($index==0)
		        <td rowspan="{{count($listadcb)}}" style='font-weight: bold;text-align: center;'>{{number_format($calculobono->bono, 2, '.', ',')}}</td>
		       @endif
	      	</tr>                  
	    @endforeach
	    </tbody>

	</table>
	</div>
</div>

<div class="modal-footer">

	<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
</div>




