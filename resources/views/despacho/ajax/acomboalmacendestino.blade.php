<div class="form-group">
  <label class="col-sm-12 control-label labelleft" >Almacen : </label>
  <div class="col-sm-12 abajocaja" >
    
        {!! Form::select( 'destino_almacen', $combo_almacen_destino, array(),
                          [
                            'class'       => 'select2 form-control control input-sm almacen_select' ,
                            'id'          => 'destino_almacen',
                            'required'    => '',
                            'data-aw'     => '2',
                          ]) !!}

  </div>
</div>