
<div class="panel-heading">Lista de ordenes de venta ({{$funcion->funciones->data_cliente($cuenta_id)->NOM_EMPR}})
  <div class="tools tooltiptop">
    <input type="hidden" name="cuenta_id_m_ov" id='cuenta_id_m_ov' value = {{$cuenta_id}}>
  </div>
</div>


<div class="modal-header" style = "padding: 0px !important;">
	<div class="panel-heading">

    <div class='filtrotabla row'>
      <div class="col-xs-12">
      </div>

    </div>

	</div>
</div>

<div class="modal-body" style="padding: 10px 20px 20px;">
  <div class="scroll_text ajax_lista_orden_venta">
      @include('regla.modal.ajax.listaordenventa')
  </div>
</div>

