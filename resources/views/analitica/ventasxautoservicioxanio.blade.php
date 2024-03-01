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
                <div class="panel-heading" ><b style="font-style: italic;">REPORTE VENTA DE AUTOSERVICIO ENTRE AÑOS</b>
                  <div class="tools tooltiptop">
<!--                     <a href="#" class="tooltipcss" id='buscarautoservicioanio' >
                      <span class="tooltiptext">Buscar</span>
                      <span class="icon mdi mdi-search" style="font-size: 40px;"></span>
                    </a> -->
                  </div>
                </div>
                <div class="panel-body selectfiltro">
                  <div class='filtrotabla row'>
                    <div class="col-xs-12">

                      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 cajareporte">
                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" style="margin-bottom:5px;">Año 01:</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'selec_anioini', $comboanioinicio, array($selec_anioini),
                                                [
                                                  'class'       => 'form-control control input-sm' ,
                                                  'id'          => 'selec_anioini',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div> 

                      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 cajareporte">
                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" style="margin-bottom:5px;">Año 02:</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'selec_aniofin', $comboaniofin, array($selec_aniofin),
                                                [
                                                  'class'       => 'form-control control input-sm' ,
                                                  'id'          => 'selec_aniofin',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div> 

                      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 cajareporte">
                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" style="margin-bottom:5px;">Autoservicio:</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'selec_cliente', $combocliente, array($selec_cliente),
                                                [
                                                  'class'       => 'form-control control input-sm' ,
                                                  'id'          => 'selec_cliente',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div> 

                      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 cajareporte">
                          <div class="form-group">
                            <a href="#" class="tooltipcss btn btn-space btn-success" id='buscarautoservicio' style="margin-top:32px;margin-left: 15px;" >
                              BUSCAR
                            </a>
                          </div>
                      </div> 


                  </div>
                  <div class="col-xs-12">
                    <div class='todoschart'>
                        @include('analitica.ajax.aventasaniotodos')
                      <div class='listaanaitica listajax reporteajax' style="padding-top: 20px;">
                        @include('analitica.ajax.aventasxautoservicoanio')
                      </div>
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
  <script src="{{ asset('public/js/analitica/generalanio.js?v='.$version) }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/analitica/ventasxautoservicioanio.js?v='.$version) }}" type="text/javascript"></script>  

@stop