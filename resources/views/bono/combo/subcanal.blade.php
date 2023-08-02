<div class="form-group">
<label class="col-sm-12 control-label labelleft negrita" >Sub Canal : </label>
<div class="col-sm-12 abajocaja" >
  {!! Form::select( 'subcanal_id', $combo_sub_canal_pc, $defecto_sub_canal,
                    [
                      'class'       => 'select3 form-control control input-xs combo' ,
                      'id'          => 'subcanal_id',
                      'data-aw'     => '1',
                    ]) !!}
</div>
</div>


@if(isset($ajax))
<script type="text/javascript">
	$(".select3").select2({
      width: '100%'
    });
</script> 
@endif
