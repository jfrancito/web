
<div class="panel-heading">Lista de clientes de {{$jefe->NOM_CATEGORIA}}
  <div class="tools tooltiptop">
    <input type="hidden" name="jefe_id_m_ov" id='jefe_id_m_ov' value = {{$jefe_id}}>
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
      @include('regla.modal.ajax.listaclientes')
  </div>
</div>

