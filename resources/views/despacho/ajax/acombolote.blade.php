  {!! Form::select( 'lote_id', $combo_almacen_lote, $almacen_lote_group_id,
                    [
                      'class'       => 'select-despacho select_tabla_lote_id' ,
                      'id'          => 'lote_id',
                      'required'    => '',
                      'multiple'    => 'multiple',
                      'data-aw'     => '1',
                    ]) !!}

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
      $('.select_tabla_lote_id').multiselect({
            buttonWidth: '150px',
            numberDisplayed: 1
      });
    });
  </script> 
@endif     