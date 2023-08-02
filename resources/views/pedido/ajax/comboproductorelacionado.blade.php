<div class="form-group">
  <label class="col-sm-12 control-label">
    Relacionar obsequio
  </label>
  <div class="col-sm-12">
    <div class="input-group_mobil">
      {!! Form::select( 'ind_producto_obsequio', $arraycombo, array(),
                        [
                          'class'       => 'form-control control' ,
                          'id'          => 'ind_producto_obsequio',
                          'data-aw'     => '1',
                        ]) !!}
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    App.formElements();
  });
</script> 