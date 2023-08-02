
<div class="modal-header">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<h3 class="modal-title">
		 Emitir Bono {{$cuota->anio}} - {{$cuota->mes}}
	</h3>
	<input type="hidden" name="cuota_id" id="cuota_id" value='{{$cuota_id}}'>
	<input type="hidden" name="idopcion" id="idopcion" value='{{$idopcion}}'>
</div>
<div class="modal-body">
	<div  class="row regla-modal">
	    <div class="col-md-6">
	    </div>
	</div>
</div>

<div class="modal-footer" style="text-align: center;">
  <button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-emitir">EMITIR</button>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
      App.formElements();
    });
  </script>
@endif




