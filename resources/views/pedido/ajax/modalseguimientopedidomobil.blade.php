<div class="modal-header">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title"><strong>{{$pedido->empresa->NOM_EMPR}}</strong></h3>
  <h5 class="modal-title">{{$pedido->empresa->NRO_DOCUMENTO}} / {{$funcion->funciones->cuenta_cliente($pedido->cliente_id)}}</h5>
  <h5 class="modal-title"> Dirección entrega : {{$pedido->direccionentrega->NOM_DIRECCION}}</h5>
  <h5 class="modal-title"> Glosa : {{$pedido->glosa}}</h5>
</div>
<div class="modal-body">
  	<div class="scroll_text">
	   	@foreach($ordenes as $index => $item)
          <div class="row">
            <div class="col-sm-12">
              <div id="accordion1" class="panel-group accordion">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion1" href="#collapseOne"><i class="icon mdi mdi-chevron-down"></i> {{$item->COD_ORDEN}}</a></h4>
                  </div>
                  <div id="collapseOne" class="panel-collapse collapse in">
                    <div class="panel-body">
						            @php
						              	$lista_productos    =   $funcion->op_lista_detalle_producto($item->COD_ORDEN);
						              	$lista_documentos   =   $funcion->op_lista_detalle_documentos($item->COD_ORDEN);
						            @endphp

			                  <table class="table">
			                    <thead>
			                      <tr>
			                        <th>NOMBRE PRODUCTO</th>
			                        <th>CANTIDAD PRODUCTO</th>
			                        <th>TOTAL VENTA</th>
			                      </tr>
			                    </thead>
			                    <tbody>
			                    	@foreach($lista_productos as $indexp => $itemp)
			                      <tr>
			                        <td>{{$itemp->TXT_NOMBRE_PRODUCTO}}</td>
			                        <td>{{$itemp->CAN_PRODUCTO}}</td>
			                        <td>{{$itemp->CAN_VALOR_VTA}}</td>
			                      </tr>
			                      @endforeach
			                    </tbody>
			                  </table>

			                  <table class="table">
			                    <thead>
			                      <tr>
			                        <th>ORDEN</th>
			                        <th>TIPO DOCUMENTO</th>
			                        <th>DOCUMENTO</th>
			                        <th>FECHA</th>
			                        <th>ESTADO</th>
			                      </tr>
			                    </thead>
			                    <tbody>
			                    	@foreach($lista_documentos as $indexd => $itemd)
			                      <tr>
			                        <td>{{$itemd->ORDEN}}</td>
			                        <td>{{$itemd->TIPO_DOC}}</td>
			                        <td>{{$itemd->DOCUMENTO}}</td>
			                        <td>{{$itemd->FECHA}}</td>
			                        <td>{{$itemd->ESTADO_DOC}}</td>
			                      </tr>
			                      @endforeach
			                    </tbody>
			                  </table>

                  	</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
	    @endforeach
	</div>
</div>
<div class="modal-footer">
  <span class='panel-subtitle cell-detail-producto'><strong> TOTAL : {{$pedido->total}} </strong></span>	
  <button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cancelar</button>
</div>