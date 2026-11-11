
<form method="POST" action="{{ url('/ingresar-calculo-vendedor/'.$idopcion.'/'.Hashids::encode(substr($periodobono->id, -8))) }}">
      {{ csrf_field() }}
	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			 {{$periodobono->codigo}} <span>({{$periodobono->anio}} - {{$periodobono->mes}})</span>
		</h3>
		<input type="hidden" name="cuota_detalle_id" id="cuota_detalle_id" value='{{$cuota_detalle_id}}'>
	</div>
	<div class="modal-body">
		<div  class="row regla-modal">
		    <div class="col-md-12">

		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		              <div class="form-group">
		                <label class="col-sm-12 control-label labelleft negrita" >Jefe venta :</label>
		                <div class="col-sm-12 abajocaja" >
		                  {!! Form::select( 'jefeventa_id', $combo_jv_pc, $defecto_jv,
		                                    [
		                                      'class'       => 'select3 form-control control input-xs combo' ,
		                                      'id'          => 'jefeventa_id',
		                                      'data-aw'     => '1',
		                                    ]) !!}
		                </div>
		              </div>
		        </div>

		    </div>
		    <div class="col-md-6">

		    </div>

		</div>
	</div>

	<div class="modal-footer">
	  <button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion">Calcular Bono</button>
	</div>
</form>
@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
		$(".select3").select2({
	      width: '100%'
	    });
    });
  </script>
@endif




