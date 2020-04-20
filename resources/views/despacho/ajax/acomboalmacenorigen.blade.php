<div class="form-group">
  <label class="col-sm-12 control-label labelleft" >Almacen : </label>
  <div class="col-sm-12 abajocaja" >
    
        {!! Form::select( 'origen_almacen', $combo_almacen_origen, array($almacen_combo_id),
                          [
                            'class'       => 'select2 form-control control input-sm' ,
                            'id'          => 'origen_almacen',
                            'required'    => '',
                            'data-aw'     => '2',
                          ]) !!}

  </div>
</div>