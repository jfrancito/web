  <div class="form-group">
    <label class="col-sm-12 control-label labelleft" >Cliente :</label>
    <div class="col-sm-12 abajocaja" >
      {!! Form::select( 'cliente_id', $combocliente, array($cliente_id),
                        [
                          'class'       => 'select3 form-control control input-sm' ,
                          'id'          => 'cliente_id',
                          'required'    => '',
                          'data-aw'     => '1',
                        ]) !!}
    </div>
  </div>
@if(isset($ajax))
    <script type="text/javascript">
        $(".select3").select2(); //reasignacion de estilos de clase
    </script>
@endif
