
<div class="col-md-12">
  <div class="panel panel-border-color panel-border-color-success">
    <div class="panel-heading">INFORMACION</div>
    <div class="panel-body">

		<div class="col-sm-6">
			
			<div class="form-group">
				<label  class="col-sm-4 control-label" >
					<div class="tooltipfr">Nombre <span class='requerido'>*</span>
					  <span class="tooltiptext">Nombre de la regla para ampliar dias de vencimiento</span>
					</div>
				</label>
				<div class="col-sm-8">

				    <input  type="text"
				            id="nombre" name='nombre' 
				            value="@if(isset($regla)){{old('nombre' ,$regla->nombre)}}@else{{old('nombre')}}@endif"
				            placeholder="Nombre"
				            required = ""
				            maxlength="100"
				            autocomplete="off" class="form-control input-sm" data-aw="1"/>

				      		@include('error.erroresvalidate', [ 'id' => $errors->has('nombre')  , 
				                                          'error' => $errors->first('nombre', ':message') , 
				                                          'data' => '1'])

				</div>
			</div>


		</div>

    </div>
  </div>
</div>




<div class="col-md-12">
  <div class="panel panel-border-color panel-border-color-warning">
    <div class="panel-heading">CONDICIONES</div>
    <div class="panel-body">

		<div class="col-sm-6">

			<div class="form-group">
			  	<label class="col-sm-4 control-label">
			  		<div class="tooltipfr">Fecha Inicio <span class='requerido'>*</span>
					  <span class="tooltiptext">Fecha donde se iniciara la aplicación de la regla.</span>
					</div>
			  	</label>
				<div class="col-sm-8">
				    <div 	data-start-view="2"  
				    		data-date-format="dd-mm-yyyy hh:ii" 
				     		class="input-group date datetimepicker">
				      		<input size="16" type="text" 
				      		value="@if(isset($regla)){{old('fechainicio' ,date_format(date_create($regla->fechainicio), 'd-m-Y H:i'))}}@else{{old('fechainicio',$fechaactual)}}@endif"
				      		readonly="readonly"
				      		id='fechainicio' name='fechainicio' required = ""
				      		placeholder="Fecha Inicio" class="form-control input-sm">
				      		<span class="input-group-addon btn btn-primary">
				      			<i class="icon-th mdi mdi-calendar"></i>
				      		</span>
				    </div>
				</div>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="form-group">
			  	<label class="col-sm-4 control-label">
			  		<div class="tooltipfr">Fecha Fin <span class='requerido'>*</span>
					  <span class="tooltiptext">Fecha donde culminara la aplicación de la regla (si es vacio sera sin fecha fin).</span>
					</div>
				</label>
			  	<div class="col-sm-8">
				    <div 	data-start-view="2" 
				    		data-date-format="dd-mm-yyyy hh:ii" 
				     		class="input-group date datetimepicker">
				      		<input size="16" 
				      		type="text" 
				      		value="@if(isset($regla))@if($regla->fechafin == $fechavacia){{old('fechafin')}}@else{{old('fechafin' ,date_format(date_create($regla->fechafin), 'd-m-Y H:i'))}}@endif @else{{old('fechafin')}}@endif"
				      		id='fechafin' name='fechafin' 
				      		 required = ""
				      		placeholder="Fecha Fin" class="form-control input-sm">
				      		<span class="input-group-addon btn btn-primary">
				      			<i class="icon-th mdi mdi-calendar"></i>
				      		</span>

				    </div>
			  	</div>
			</div>
		</div>



    </div>
  </div>
</div>





<div class="col-md-12">
  <div class="panel panel-border-color panel-border-color-danger">
    <div class="panel-heading">ACCIONES</div>
    <div class="panel-body">


		<div class="col-sm-6">

			<div class="form-group">
			  <label class="col-sm-4 control-label">
			  		<div class="tooltipfr">Ampliacion de dias de vencimiento <span class='requerido'>*</span>
					  <span class="tooltiptext">Ampliar los dias de vencimiento.</span>
					</div>
			  </label>
			  <div class="col-sm-8 abajocaja">
				
                <div class="input-group xs-mb-15">
					<span class="input-group-addon">DV</span>
				    <input  type="text"
				            id="descuento" name='descuento' 
				            value="@if(isset($regla)){{old('descuento' ,$regla->descuento)}}@else{{old('descuento')}}@endif" 
				            placeholder="Ampliar los dias de vencimiento"
				            required = "" 
				            class="form-control input-sm importe" 
				            autocomplete="off" data-aw="6"/>
                </div>



			    @include('error.erroresvalidate', [ 'id' => $errors->has('descuento')  , 
			                                          'error' => $errors->first('descuento', ':message') , 
			                                          'data' => '6'])

			  </div>
			</div>

		</div>
    </div>
  </div>
</div>


<div class="row xs-pt-15">
<div class="col-xs-6">
    <div class="be-checkbox">

    </div>
</div>
<div class="col-xs-6">
  <p class="text-right">
    <button type="submit" class="btn btn-space btn-primary">Guardar</button>
  </p>
</div>
</div>