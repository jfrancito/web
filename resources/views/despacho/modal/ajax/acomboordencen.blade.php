<div class="col-sm-3 abajocaja" style="margin-top: 7px;">
  {!! Form::select( 'orden_cen_modal', $comboordencen, array(),
                    [
                      'class'       => 'select2 form-control control input-sm' ,
                      'id'          => 'orden_cen_modal',
                      'required'    => '',
                      'data-aw'     => '1',
                    ]) !!}
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
        $('.select2').select2();
    });
  </script> 
@endif