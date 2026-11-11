@extends('template')
@section('style')
@stop
@section('section')

<div class="be-content">
	<div class="main-content container-fluid">
          <div class="content">
            <div class="panel panel-default">
              <div class="panel-heading panel-heading-divider" style="background-color: #f5f5f5; border-bottom: 2px solid #4285f4; color: #333; font-weight: bold; font-size: 1.2em;">
                <i class="icon mdi mdi-cloud-upload" style="color: #4285f4; font-size: 1.5em; vertical-align: middle; margin-right: 10px;"></i>
                CARGA MASIVA GRANDE
                <span class="panel-subtitle">Sube el excel y se procesará listando por LPN para guardar en la ruta de red</span>
              </div>
              <div class="panel-body" style="padding-top: 20px;">

                @if (Session::has('bienhecho'))
                  <div class="alert alert-success alert-dismissible" role="alert" style="border-radius: 4px;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>¡Éxito!</strong> {{ Session::get('bienhecho') }}
                  </div>
                @endif
                @if (Session::has('error'))
                  <div class="alert alert-danger alert-dismissible" role="alert" style="border-radius: 4px;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>¡Error!</strong> {{ Session::get('error') }}
                  </div>
                @endif

                <div class="col-xs-12">
                  <div class="panel panel-default" style="border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div class="panel-body" style="padding: 20px;">
                      <form method="POST" action="{{ url('/procesar-barra-masivo-grande/'.$idopcion) }}" enctype="multipart/form-data" onSubmit="$('#btnprocesar').html('<i class=\'icon mdi mdi-spinner mdi-spin\'></i> Procesando...').attr('disabled', 'disabled');">
                        {{ csrf_field() }}

                        <div class="form-group" style="margin-bottom: 20px;">
                          <label style="font-weight: 600; color: #555; display: block; margin-bottom: 8px;">Seleccione el archivo Excel (.xls o .xlsx):</label>
                          <input type="file" name="select_file" id="select_file" class="form-control" accept=".xlsx, .xls" required style="border-radius: 4px;">
                          <small style="color: #777; margin-top: 8px; display: block;">
                              <i class="mdi mdi-info-outline text-primary"></i> 
                              Asegúrate de que el Excel contenga las columnas <strong>lpn</strong>, <strong>nro_orden</strong>, <strong>cod_ean</strong>, <strong>cod_tienda</strong>, <strong>producto</strong>, <strong>cantidad</strong>, <strong>embolsado</strong>.
                          </small>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                          <button type="submit" id="btnprocesar" class="btn btn-space btn-primary" style="background-color: #4285f4; border-color: #4285f4; font-weight: 600; padding: 8px 25px; border-radius: 4px; transition: all 0.3s; font-size: 16px;">
                            <i class="icon mdi mdi-settings" style="margin-right: 5px;"></i> Procesar Excel
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
	</div>
</div>

@stop

@section('script')
  <script type="text/javascript">
    $(document).ready(function(){
      // Initialization
    });
  </script>
@stop
