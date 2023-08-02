<div class="modal-header">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title modal-titulo-configuracion">RECHAZO DE PEDIDOS</h3>
</div>
<div class="modal-body">
    <div class="panel-body">
      <div class="col-xs-12 col-sm-12">
        <div class="form-group">
          <label class="col-sm-12 control-label">
            Motivo
          </label>
          <div class="col-sm-12">
              {!! Form::select( 'motivo_id', $combomotivos, array(),
                                [
                                  'class'       => 'form-control control input-sm' ,
                                  'id'          => 'motivo_id',
                                  'required'    => '',
                                  'data-aw'     => '7'
                                ]) !!}

          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-12">
        <div class="form-group">
          <label class="col-sm-12 control-label">
            Observacion
          </label>
          <div class="col-sm-12">

              <textarea name="observacion" id="observacion" cols="65" rows="5" style="resize: both;"></textarea>

          </div>
        </div>
      </div>
    </div>

</div>
<div class="modal-footer">

<form method="POST" id='formpedido' class='opciones' action="{{ url('/enviar-a-rechazar-siete-dias/'.$idopcion) }}" >
  {{ csrf_field() }}
  <input type="hidden" id='pedido' name='pedido'>
  <input type="hidden" id='motivo_id_n' name='motivo_id_n' >
  <input type="hidden" id='observacion_n' name='observacion_n' >
  <button type="submit" data-dismiss="modal" class="btn btn-success" id="rechazarpedidosmeses">Rechazar</button>
</form>
    
  	
</div>

<script type="text/javascript">
    $('.importe').inputmask({ 'alias': 'numeric', 
    'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
    'digitsOptional': false, 
    'prefix': '', 
    'placeholder': '0'});
</script> 