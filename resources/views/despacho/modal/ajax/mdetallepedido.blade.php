
<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			ORDEN DE PEDIDO N° : {{$ordendespacho->codigo}}
		</h5>
	</div>

</div>
<div class="modal-body">
    <div class="tab-container">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#pedidol" data-toggle="tab">Lista Pedido</a></li>
        <li><a href="#xcantidad" data-toggle="tab">x Codigo Barra</b></a></li>
        <li><a href="#xpalets" data-toggle="tab">x LPN</b></a></li>
      </ul>
      <div class="tab-content" style="margin-bottom: 0px;">
        <div id="pedidol" class="tab-pane active cont">
        	<div class='ajax_pedido_qr'>
        		@include('despacho.ajax.alistadetallepedido')
        	</div>
        <div id="xcantidad" class="tab-pane cont">
          @include('despacho.ajax.listaxcantidad')
        </div> 
        <div id="xpalets" class="tab-pane cont">
          @include('despacho.ajax.listaxpalets')
        </div>
      </div>
      
    </div>







<div class="modal-footer">


	<button type="button"  class="btn btn-success btn-space" 
			data_pedido_id = '{{$ordendespacho->id}}' 
			id='imprimirporcantidad' style="margin-top: 5px;">Por Codigo Barra</button>
	<button type="button"  class="btn btn-success btn-space" data_pedido_id = '{{$ordendespacho->id}}'  id='imprimirporpalets'>Por LPN</button>
	<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
</div>

	


