
<form method="POST" action="{{ url('/ingresar-cuotas/'.$idopcion.'/'.Hashids::encode(substr($cuota->id, -8))) }}">
      {{ csrf_field() }}

	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			 {{$cuota->codigo}} <span>({{$cuota->anio}} - {{$cuota->mes}})</span>
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


		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		              <div class="form-group">
		                <label class="col-sm-12 control-label labelleft negrita" >Canal :</label>
		                <div class="col-sm-12 abajocaja" >
		                  {!! Form::select( 'canal_id', $combo_canal_pc, $defecto_canal,
		                                    [
		                                      'class'       => 'select3 form-control control input-xs combo' ,
		                                      'id'          => 'canal_id',
		                                      'data-aw'     => '1',
		                                    ]) !!}
		                </div>
		              </div>
		        </div>

		        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ajax_subcanal">
		        	@include('bono.combo.subcanal', ['defecto_sub_canal' => $defecto_sub_canal])
		        </div>

		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="form-group">
					  <label class="col-sm-12 control-label labelleft negrita" >Cuota ( referente a 50kg) :</label>
					  <div class="col-sm-12">

					      <input  type="text"
					              id="cuota" name='cuota' 
					              value="@if(isset($cuotadetalle)){{old('cuota' ,$cuotadetalle->cuota)}}@endif" 
					              placeholder="Cuota"
					              autocomplete="off" class="form-control input-sm importe" data-aw="1"/>

					  </div>
					</div>
				</div>


		    </div>
		    <div class="col-md-6">

		    </div>

		</div>
	</div>

	<div class="modal-footer">
	  <button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion">Guardar</button>
	</div>
</form>
@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){

		$(".select3").select2({
	      width: '100%'
	    });

      $('.importe').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 3, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});

    });
  </script>
@endif




