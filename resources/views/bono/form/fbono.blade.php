
<div class="form-group">
  <label class="col-sm-3 control-label">Año (*)</label>
  <div class="col-sm-6">
    {!! Form::select( 'anio', $combo_anio_pc, array($anio),
                      [
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'anio',
                        'required'    => '',
                        'data-aw'     => '1',
                      ]) !!}
  </div>
</div>


<div class="form-group">

  <label class="col-sm-3 control-label">Mes (*)</label>
  <div class="col-sm-6">
    {!! Form::select( 'mes', $combo_mes_pc, array($mes),
                      [
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'mes',
                        'required'    => '',
                        'data-aw'     => '1',
                      ]) !!}

  </div>
</div>

<div class="row xs-pt-15">
  <div class="col-xs-6">
      <div class="be-checkbox">

      </div>
  </div>
  <div class="col-xs-6">
    <p class="text-right">
      <button type="submit" class="btn btn-space btn-primary">Guardar</button>
    </p>
  </div>
</div>