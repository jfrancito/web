@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>

@stop
@section('section')

    <div class="be-content contenido archivoautoservicio">
        <div class="main-content container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default panel-border-color panel-border-color-success">
                        <div class="panel-heading">OPTIMIZADOR EXCEL
                            <div class="tools tooltiptop">

                                <div class="dropdown">

                                    <a href="#" class="tooltipcss opciones buscararchivo">
                                        <span class="tooltiptext">Buscar archivo autoservicio</span>
                                        <span class="icon mdi mdi-search"></span>
                                    </a>

                                </div>

                            </div>
                            <span class="panel-subtitle">{{Session::get('empresas')->NOM_EMPR}}</span>
                        </div>
                        <div class="panel-body">
                            <form method="post" enctype="multipart/form-data" action="{{ url('/gestion-import-excel/'.$idopcion.'/validate') }}">
                                {{ csrf_field() }}
                                <div class="form-group row filtrotabla">
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                        <select required class="select2 form-control control input-xs" name="autoservicio" id="autoservicio" @if(!$estado_validar) disabled @endif>
                                            <option value="CENCOSUD" @if($autoservicio == 'CENCOSUD') selected @endif>CENCOSUD</option>
                                            <option value="TOTTUS" @if($autoservicio == 'TOTTUS') selected @endif>TOTTUS</option>
                                            <option value="MAYORSA" @if($autoservicio == 'MAYORSA') selected @endif>MAYORSA</option>
                                            <option value="SPSA" @if($autoservicio == 'SPSA') selected @endif>SPSA</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                        <input required name="startDate" class="form-control control input-sm" type="date" value="{{$fecha}}" @if(!$estado_validar) disabled @endif>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                        <input id="validar" type="submit" name="validar" class="btn btn-primary btn-lg btn-block" value="VALIDAR ARCHIVO" @if(!$estado_validar) disabled @endif>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                        <input id="eliminar" type="submit" name="eliminar" class="btn btn-danger btn-lg btn-block" value="ELIMINAR ARCHIVO" @if($estado_eliminar) disabled @endif>
                                    </div>
                                </div>
                            </form>
                            <br />
                            <form method="post" enctype="multipart/form-data" action="{{ url('/gestion-import-excel/'.$idopcion.'/import') }}">
                                {{ csrf_field() }}
                                <div class="form-group row filtrotabla">
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <input required type="file" class="form-control control input-sm" name="select_file" id="formFile" @if(!$estado) disabled @endif>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                        <input type="submit" id="upload" name="upload" class="btn btn-primary btn-lg btn-block" value="SUBIR ARCHIVO" @if(!$estado) disabled @endif>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                        <input type="submit" id="return" name="return" class="btn btn-warning btn-lg btn-block" value="REGRESAR" @if(!$estado) disabled @endif>
                                    </div>
                                    <input type="text" name="autoservice" id="autoservice" value="{{$autoservicio}}" hidden>
                                    <input type="text" name="date" id="date" value="{{$fecha}}" hidden>
                                </div>
                            </form>
                            <br />
                            <form method="POST"
                                  id="formdescargar"
                                  target="_blank"
                                  action="{{ url('/descargar-reporte-refuerzo/'.$idopcion) }}"
                                  style="border-radius: 0px;"
                            >
                                {{ csrf_field() }}
                                <div class="form-group row filtrotabla">
                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                        <label class="col-sm-12 control-label labelleft" >AUTOSERVICIO :</label>
                                        <select required class="select2 form-control control input-xs" name="autoservicio_reporte" id="autoservicio_reporte">
                                            <option value="CENCOSUD" @if($autoservicio == 'CENCOSUD') selected @endif>CENCOSUD</option>
                                            <option value="TOTTUS" @if($autoservicio == 'TOTTUS') selected @endif>TOTTUS</option>
                                            <option value="MAYORSA" @if($autoservicio == 'MAYORSA') selected @endif>MAYORSA</option>
                                            <option value="SPSA" @if($autoservicio == 'SPSA') selected @endif>SPSA</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 cajareporte">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label labelleft" >Año :</label>
                                            <div class="col-sm-12 abajocaja" >
                                                {!! Form::select( 'anio', $combo_anio_pc, $anio,
                                                                  [
                                                                    'class'       => 'select2 form-control control input-xs' ,
                                                                    'id'          => 'anio',
                                                                    'required'    => '',
                                                                    'data-aw'     => '1',
                                                                  ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 cajareporte ajax_anio">
                                        @include('importacionesexcel/combo/cperiodo', ['sel_periodo' => $sel_periodo])

                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                        <input id="refuerzo" type="submit" name="refuerzo" class="btn btn-success btn-lg btn-block" value="REPORTE REFUERZO">
                                    </div>
                                </div>
                            </form>
                            <div class="col-sm-12" @if(empty($mensaje_retail)) hidden @endif id="mostrar">
                                <div class="alert {{$tipo_mensaje_retail}}" role="alert">
                                    {{$mensaje_retail}}
                                </div>
                            </div>
                            <div class='listajax'>
                                @include('importacionesexcel.alistaarchivo')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('script')
    
    <script src="{{ asset('public/js/general/inputmask/inputmask.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/general/inputmask/inputmask.extensions.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/general/inputmask/inputmask.numeric.extensions.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/general/inputmask/inputmask.date.extensions.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/general/inputmask/jquery.inputmask.js') }}" type="text/javascript"></script>

    <script src="{{ asset('public/lib/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery.nestable/jquery.nestable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/moment.js/min/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/bootstrap-slider/js/bootstrap-slider.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/app-form-elements.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/parsley/parsley.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery.niftymodals/dist/jquery.niftymodals.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

        $.fn.niftyModal('setDefaults',{
            overlaySelector: '.modal-overlay',
            closeSelector: '.modal-close',
            classAddAfterOpen: 'modal-show',
        });

        $(document).ready(function(){
            //initialize the javascript
            App.init();
            App.formElements();
            $('form').parsley();


            $('.dinero').inputmask({ 'alias': 'numeric',
                'groupSeparator': ',', 'autoGroup': true, 'digits': 4,
                'digitsOptional': false,
                'prefix': '',
                'placeholder': '0'});

            $('.datetimepicker2').datetimepicker({
                autoclose: true,
                pickerPosition: "bottom-left",
                componentIcon: '.mdi.mdi-calendar',
                navIcons:{
                    rightIcon: 'mdi mdi-chevron-right',
                    leftIcon: 'mdi mdi-chevron-left'
                },

            })
                .on('changeDate', function (ev) {
                    event.preventDefault();
                    var fechadocumento       =   $('#fechadocumento').val();
                    var _token               =   $('#token').val();

                    data                    =   {
                        _token               : _token,
                        fechadocumento       : fechadocumento
                    };

                    ajax_normal_combo(data,"/ajax-input-tipo-cambio","ajax_tipocambio");
                });

        });

        setTimeout(function(){
            const page = document.getElementById("mostrar");
            page.setAttribute("hidden","");
        }, 15000);

        $('#periodo_id').removeAttr("required");

        const btn_subir = document.getElementById("upload");

        btn_subir.addEventListener("click", () => {
            abrircargando();
        });

        const btn_regresar = document.getElementById("return");
        btn_regresar.addEventListener("click", () => {
            document.querySelector('#formFile').required = false;
        });

        const btn_validar = document.getElementById("validar");

        btn_validar.addEventListener("click", () => {
            abrircargando();
            setTimeout(cerrarcargando, 100000);
        });

    </script>

    <script src="{{ asset('public/js/importacionesexcel/importacionesexcel.js?v='.$version) }}" type="text/javascript"></script>

@stop