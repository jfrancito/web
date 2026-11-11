<div class="form-group">
  <label class="col-sm-4 control-label">
    <div class="">Lugar de Entrega :
    </div>  
  </label>
  <div class="col-sm-8">
        <div class="input-group">
            {!! Form::select( 'lugarentrega', $combole, array(),
                              [
                                'class'       => 'select3 form-control control input-sm configuracion-lugar-entrega' ,
                                'id'          => 'lugarentrega',
                                'data-aw'     => '1',
                              ]) !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-success input-sm configuracion-lugar-entrega" style="height: 48px !important;">
                  <i class="mdi mdi-swap-vertical"></i> Agregar
                </button>
            </span>       
        </div>
  </div>
</div>

<script type="text/javascript">
  $(".select3").select2({
      width: '100%'
    });
</script> 