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
              <div class="panel panel-default panel-table">

                <div class="panel-heading">Comisiones - Vendedor
                  <div class="tools tooltiptop">

                    <a href="{{url('/comision-periodo-producto-excel')}}" 
                       class='tooltipcss'
                       target="_blank"
                       id="descargarcomisionperiodoproductoexcel" 
                       data-href="{{url('/comision-periodo-producto-excel')}}"
                       title="Descargar comision x periodo">
                       <span class="tooltiptext">Descargar precios de los productos x cliente </span>
                       <i class="fa fa-file-excel-o"></i>
                    </a>

                  </div>
                </div>
                <div class="panel-body selectfiltro">

                  <div class='filtrotabla row'>
                    <div class="col-xs-12">



                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">

                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" >Periodo Inicio :</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'periodo_inicio', $comboperiodoinicio, array(),
                                                [
                                                  'class'       => 'select2 form-control control input-sm' ,
                                                  'id'          => 'periodo_inicio',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div>


                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">

                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" >Periodo Fin :</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'periodo_fin', $comboperiodofin, array(),
                                                [
                                                  'class'       => 'select2 form-control control input-sm' ,
                                                  'id'          => 'periodo_fin',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div>

                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">

                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" >Vendedor :</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'vendedor_id', $combovendedores, array(),
                                                [
                                                  'class'       => 'select2 form-control control input-sm' ,
                                                  'id'          => 'vendedor_id',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div>





                  </div>

                  <div class="col-xs-12">
                    <div class='listaprecioproducto listajax reporteajax'>
                        <div class='ajaxvacio'>
                          Lista de ventas de los vendedores...

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


    <script type="text/javascript">
      $(document).ready(function(){
        //initialize the javascript
        App.init();
        App.formElements();
        App.dataTables();
        $('[data-toggle="tooltip"]').tooltip();
      });
    </script> 

    <script src="{{ asset('public/js/reporte/comision.js?v='.$version) }}" type="text/javascript"></script> 
@stop