@extends('templateanalitica')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')
  <div class="be-content contenido crearpedido" style="background-color: #fff;">
    <div class="main-content container-fluid" style="padding: 1px;">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-table">
                <div class="panel-heading" ><b style="font-style: italic;">REPORTE VENTA DE AUTOSERVICIO</b>
                  <div class="tools tooltiptop">
<!--                     <a href="#" class="tooltipcss" id='buscarautoservicio' >
                      <span class="tooltiptext">Buscar</span>
                      <span class="icon mdi mdi-search" style="font-size: 40px;"></span>
                    </a> -->
                  </div>
                </div>
                <div class="panel-body selectfiltro">
                  <div class='filtrotabla row'>
                    <div class="col-xs-12">
                      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 cajareporte">
                          <div class="form-group ">
                            <label class="col-sm-12 control-label labelleft" >Fecha Inicio:</label>
                            <div class="col-sm-12 abajocaja" >
                              <div data-min-view="2" 
                                     data-date-format="dd-mm-yyyy"  
                                     class="input-group date datetimepicker" style = 'padding: 0px 0;margin-top: -3px;'>
                                     <input size="16" type="text" 
                                            value="{{$inicio}}" 
                                            placeholder="Fecha Inicio"
                                            id='fechainicio' 
                                            name='fechainicio' 
                                            required = ""
                                            class="form-control"/>
                                      <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                          </div>
                      </div>

                      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 cajareporte">
                          <div class="form-group ">
                            <label class="col-sm-12 control-label labelleft" >Fecha Fin:</label>
                            <div class="col-sm-12 abajocaja" >
                              <div data-min-view="2" 
                                     data-date-format="dd-mm-yyyy"  
                                     class="input-group date datetimepicker" style = 'padding: 0px 0;margin-top: -3px;'>
                                     <input size="16" type="text" 
                                            value="{{$hoy}}" 
                                            placeholder="Fecha Fin"
                                            id='fechafin' 
                                            name='fechafin' 
                                            required = ""
                                            class="form-control"/>
                                      <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                          </div>
                      </div> 



                      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 cajareporte">

                        <div class="input-group my-group">
                            <label class="col-sm-12 control-label labelleft" >Ventas Equivalente en:</label>

                              {!! Form::select( 'tiporeporte', $combotr, array($tiporeporte),
                                                [
                                                  'class'       => 'select2 form-control control input-sm' ,
                                                  'id'          => 'tiporeporte',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                              <span class="input-group-btn">
                                <button class="btn btn-primary"
                                        type="button" 
                                        id='buscarautoservicio'
                                        style="margin-top: 26px;height: 48px;"
                                        >
                                        <span class="mdi mdi-search"></span></button>
                              </span>
                        </div>
                      </div> 

                  </div>
                  <div class="col-xs-12">
                    <div class='todoschart'>
                        @include('analitica.ajax.aventastodos')
                      <div class='listaanaitica listajax reporteajax' style="padding-top: 20px;">
                        @include('analitica.ajax.aventasxautoservico')
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
    </div>
    <div class="row-menu" style="width: 99.9%;">
      <div class="row">
        <div class="col-sm-12 col-mobil-top">
          <div class="col-fr-2 col-inicio">
            <span class="mdi mdi-home"></span>
          </div> 

          <div class="col-fr-8 col-total">
            <strong></strong> <strong class=""></strong>
          </div>
          <div class="col-fr-2 col-atras"
            data_posicion = '00'
          >
            <span class="mdi mdi-undo"></span>
          </div>
        </div>
      </div>
    </div>
    @include('analitica.modal.manalitica')
  </div>
@stop
@section('script')
  <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/responsive.bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/js/app-tables-datatables.js?v='.$version) }}" type="text/javascript"></script>
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
      App.init();
      App.formElements();
      $('.scroll_text_horizontal_analitica').scrollLeft(500);
    });
  </script>
  <script src="{{ asset('public/js/analitica/general.js?v='.$version) }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/analitica/ventasxautoservicio.js?v='.$version) }}" type="text/javascript"></script>  

@stop