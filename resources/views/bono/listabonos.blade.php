@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')

  <div class="be-content contenido bono">
    <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-border-color panel-border-color-success">
                <div class="panel-heading">Bono
                  <div class="tools tooltiptop">




                    <a href="#" class="tooltipcss opciones buscarasientomodelo">
                      <span class="tooltiptext">Buscar Bonos</span>
                      <span class="icon mdi mdi-search"></span>
                    </a>

                    <a href="{{ url('/agregar-bono/'.$idopcion) }}" class="tooltipcss">
                      <span class="tooltiptext">Crear Bono</span>
                      <span class="icon mdi mdi-plus-circle-o"></span>
                    </a>

                    
                  </div>

                </div>

                <div class="panel-body">
                  <div class='filtrotabla row'>
                    <div class="col-xs-12">
                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">
                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" >Año :</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'anio', $combo_anio_pc, array($anio),
                                                [
                                                  'class'       => 'select2 form-control control input-xs' ,
                                                  'id'          => 'anio',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div>

                      
                      <input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>
                    </div>

 
                    <div class="col-xs-12 widget-chart-container">
                      <div class="widget-chart-info">
                        <ul class="chart-legend-horizontal">
                          <li><span class="main-chart-color1"></span> Generado</li>
                          <li><span class="main-chart-color2"></span> Ejecutado</li>
                        </ul>
                      </div>
                    </div>


                  </div>


                  <div class='listajax'>
                    @include('bono.ajax.alistabonos')
                  </div>


                </div>
              </div>
            </div>
          </div>
    </div>
  @include('bono.modal.mcuota')
  </div>

@stop

@section('script')


  <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>

  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.print.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.colVis.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.bootstrap.js') }}" type="text/javascript"></script>
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
      //initialize the javascript
      App.init();
      App.formElements();
      App.dataTables();
      $('[data-toggle="tooltip"]').tooltip();
      $('form').parsley();

    });

  </script>
  <script src="{{ asset('public/js/bono/bono.js?v='.$version) }}" type="text/javascript"></script>

@stop