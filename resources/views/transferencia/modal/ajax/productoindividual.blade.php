<div class="modal-header">
	<div class="panel-heading">
		Agregar Producto Individual
	</div>
</div>
<div class="modal-body">

<div class="row producto-modal">

    <div class="col-sm-12">

      <div class="form-group">
          <label class="col-sm-12 control-label">
            <div class="tooltipfr">Producto
              <span class="tooltiptext">Seleccione el producto.</span>
          </div>
        </label>

        <div class="col-sm-12 ">
                 {!! Form::select( 'producto_select', $combolistaproductos, array(),
                    [
                    'class'       => 'form-control control select2' ,
                    'id'          => 'producto_select',
                    'data-aw'     => '2',
                    ]) !!}
        </div>
      </div> 

      <div class="form-group">
        <label class="col-sm-12 control-label">
            <div class="tooltipfr">Cantidad <span class='requerido'>*</span>
            <span class="tooltiptext">Agregar cantidad de producto.</span>
          </div>
        </label>
        <div class="col-sm-12 abajocaja">
        
                <div class="input-group xs-mb-15">
          <span class="input-group-addon ssoles"></span>
            <input  type="text"
                    id="cantidad_pr" name='cantidad_pr' 
                    value="" 
                    placeholder="Ingrese Cantidad"
                    required = "" class="form-control input-sm importe" data-parsley-type="number"
                    autocomplete="off" data-aw="6"/>
                </div>

        </div>
      </div>

      <div class="col-xs-12">
        <p class="text-right">
          <button type="button" class="btn btn-space btn-primary btn_producto_individual">Guardar</button>
        </p>
      </div>

    </div>


</div>

</div>
<script>
	$('#producto_select').select2();

    $('.importe').inputmask({ 'alias': 'numeric', 
    'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
    'digitsOptional': false, 
    'prefix': '', 
    'placeholder': '0'});

</script>