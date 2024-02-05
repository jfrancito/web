<div class="modal-header" style="padding: 8px 5px;font-style: italic;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<h3 class="modal-title" style="font-size: 14px; text-align: center;"> 
		<span>{{$empresa}}</span><br>
		<span>({{$inicio}} / {{$hoy}})</span><br>
		<span>{{$tituloban}}</span>

		
	</h3>
</div>
<div class="modal-body">
	<div  class="row regla-modal">
	    <div class="col-md-12">
            <div class="panel panel-default">
			  	<div class="tab-container">
				    <ul class="nav nav-tabs">
				      	<li class="active"><a href="#tm" data-toggle="tab">TIPO MARCA</a></li>
				      	<li><a href="#an" data-toggle="tab">AÑO</a></li>
				    </ul>
				    <div class="tab-content" style="margin-top: 15px;">
				      	<div id="tm" class="tab-pane active cont">


				          	<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 cajareporte">
	                        	<div class="col-sm-12 abajocaja" >
					                <div class="input-group my-group">
										<label class="col-sm-12 control-label labelleft" >Tipo Marca :</label>
						                  {!! Form::select( 'tipomarca', $combotipomarca, array($tipomarca_sel),
						                                    [
						                                      'class'       => 'form-control control input-sm' ,
						                                      'id'          => 'tipomarca',
						                                      'required'    => '',
						                                      'data-aw'     => '1',
						                                    ]) !!}

					                      <span class="input-group-btn">
					                        <button class="btn btn-primary btn-autoservicio-marca"
					                                type="button" 
					                                style="margin-top: 27px;height: 37px;"
					                                data_nombre_empresa = '{{$empresa}}'
					                                >
					                                <span class="mdi mdi-search"></span></button>
					                      </span>
					                </div>
	                        	</div>
				          	</div>


				      	</div>
				      	<div id="an" class="tab-pane cont">
				          	<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 cajareporte">
				                <div class="input-group my-group">
									<label class="col-sm-12 control-label labelleft" >AÑO:</label>

					                  {!! Form::select( 'anio', $comboanio, array($tipomarca_sel),
					                                    [
					                                      'class'       => 'form-control control input-sm' ,
					                                      'id'          => 'anio',
					                                      'required'    => '',
					                                      'data-aw'     => '1',
					                                    ]) !!}
				                      <span class="input-group-btn">
				                        <button class="btn btn-primary btn-autoservicio-anio"
				                                type="button" 
				                                style="margin-top: 27px;height: 37px;"
				                                data_nombre_empresa = '{{$empresa}}'
				                                >
				                                <span class="mdi mdi-search"></span></button>
				                      </span>
				                </div>
				          	</div>
				      	</div>
				    </div>
			  	</div>
            </div>
	    </div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
</div>
@if(isset($ajax))
  <script type="text/javascript">

  	$(".select3").select2({
      width: '100%'
    });

  </script>
@endif




