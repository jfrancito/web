@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>

@stop
@section('section')

    <div class="be-content">
        <div class="main-content container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default panel-border-color panel-border-color-success">
                        <div class="panel-heading">OPTIMIZADOR EXCEL
                            <span class="panel-subtitle">{{Session::get('empresas')->NOM_EMPR}}</span>
                        </div>
                        <div class="panel-body">
                            <form method="post" enctype="multipart/form-data" action="{{ url('/gestion-import-excel/validate') }}">
                                {{ csrf_field() }}
                                <div class="form-group row filtrotabla">
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                        <select required class="select2 form-control control input-xs" name="autoservicio" @if(!$estado_validar) disabled @endif>
                                            <option value="CENCOSUD" @if($autoservicio == 'CENCOSUD') selected @endif>CENCOSUD</option>
                                            <option value="TOTTUS" @if($autoservicio == 'TOTTUS') selected @endif>TOTTUS</option>
                                            <option value="MAYORSA" @if($autoservicio == 'MAYORSA') selected @endif>MAYORSA</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                        <input required name="startDate" class="form-control control input-xs" type="date" value="{{$fecha}}" @if(!$estado_validar) disabled @endif>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
                                        <input id="validar" type="submit" name="validar" class="btn btn-primary btn-lg btn-block" value="VALIDAR STOCK AUTOSERVICIO" @if(!$estado_validar) disabled @endif>
                                    </div>
                                </div>
                            </form>
                            <br />
                            <form method="post" enctype="multipart/form-data" action="{{ url('/gestion-import-excel/import/'.$autoservicio.'/'.$fecha) }}">
                                {{ csrf_field() }}
                                <div class="form-group row filtrotabla">
                                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                        <input required type="file" class="form-control" name="select_file" id="formFile" @if(!$estado) disabled @endif>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <input type="submit" id="upload" name="upload" class="btn btn-primary btn-lg btn-block" value="SUBIR ARCHIVO AUTOSERVICIO" @if(!$estado) disabled @endif>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12" @if(empty($mensaje_retail)) hidden @endif id="mostrar">
                    <div class="alert {{$tipo_mensaje_retail}}" role="alert">
                        {{$mensaje_retail}}
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
        }, 10000);

        const btn_subir = document.getElementById("upload");

        btn_subir.addEventListener("click", () => {
            abrircargando();
        });

        const btn_validar = document.getElementById("validar");

        btn_validar.addEventListener("click", () => {
            abrircargando();
            setTimeout(cerrarcargando, 5000);
        });

    </script>

    <script src="{{ asset('public/js/asiento/asiento.js?v='.$version) }}" type="text/javascript"></script>

@stop