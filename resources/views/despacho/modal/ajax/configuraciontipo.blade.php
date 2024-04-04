<div class="modal-header">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title modal-titulo-configuracion">AGREGAR VALOR TIPO </h3>
</div>
<div class="modal-body">
    <div class="panel-body">
      <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2  col-lg-8 col-lg-offset-2">
        <div class="form-group">
          <label class="col-sm-12 control-label">
            TIPO
          </label>
          <div class="col-sm-12">

              <input  type="text"
                      id="lbltipo" 
                      name='lbltipo' 
                      value="" 
                      placeholder="Tipo"
                      required = "" class="form-control input-sm"
                      autocomplete="off" data-aw="1"/>

          </div>
        </div>
      </div>

    </div>

</div>
<div class="modal-footer">
  	<button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cancelar</button>
  	<button type="button" data-dismiss="modal" class="btn btn-success" id="modificarconfiguraciontipo">Modificar</button>
</div>

<script type="text/javascript">
    $('.importe').inputmask({ 'alias': 'numeric', 
    'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
    'digitsOptional': false, 
    'prefix': '', 
    'placeholder': '0'});
</script> 