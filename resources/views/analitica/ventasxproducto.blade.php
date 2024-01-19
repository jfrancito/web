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
                <div class="panel-heading" ><b style="font-style: italic;">Venta de Cliente</b>
                </div>
                <div class="panel-body selectfiltro">
                  <div class='filtrotabla row'>
                    <div class="col-xs-12">

                      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 cajareporte">

                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" >Clientes :</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'empresa_nombre', $comboempresa, array($empresa_nombre),
                                                [
                                                  'class'       => ' form-control control input-sm' ,
                                                  'id'          => 'empresa_nombre',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div>


                      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 cajareporte">

                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" >Periodo :</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'periodo', $comboperiodo, array($periodo_sel),
                                                [
                                                  'class'       => ' form-control control input-sm' ,
                                                  'id'          => 'periodo',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div>


                      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 cajareporte">

                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" >Tipo Marca :</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'tipomarca', $combotipomarca, array($tipomarca_sel),
                                                [
                                                  'class'       => ' form-control control input-sm' ,
                                                  'id'          => 'tipomarca',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div>



                  </div>

                  <div class="col-xs-12">

                    <div class='listaanaitica listajax reporteajax' style="padding-top: 20px;">

                    <div class="tab-container">
                      <ul class="nav nav-tabs">
                        <li class="active"><a href="#vg" data-toggle="tab">Ventas Atendidas</a></li>
                        <li><a href="#va" data-toggle="tab">Ventas Generales</a></li>
                      </ul>
                      <div class="tab-content">
                        <div id="vg" class="tab-pane active cont">
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="margin-top: 15px;">
                                <h4 class="titulochar">S/. {{number_format($totalimporte_s, 2, '.', ',')}}</h4>
                                <div id="chart02" >
                                </div>
                            </div>  

                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="margin-top: 20px;">
                                <h4 class="titulochar">{{number_format($totalimporte_s, 2, '.', ',')}}</h4>
                                <div id="chart_b" >
                                </div>
                            </div>
                        </div>
                        <div id="va" class="tab-pane cont">
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 " style="margin-top: 15px;">
                                <h4 class="titulochar">{{number_format($totalimporte, 2, '.', ',')}}</h4>
                                <div id="chart01" >
                                </div>
                            </div>  
                        </div>
                      </div>
                    </div>






                        <input type="text" name="anio" id="anio" value='{{$anio}}' class='ocultar'>
                        <div id="meses" class='ocultar'>{{$meses}}</div>
                        <div id="anio" class='ocultar'>{{$anio}}</div>
                        <div id="mes" class='ocultar'>{{$mes}}</div>
                        <div id="empresa_nombre_text" class='ocultar'>{{$empresa_nombre}}</div>
                        <div id="periodo_sel" class='ocultar'>{{$periodo_sel}}</div>
                        <div id="tipomarca_txt" class='ocultar'>{{$tipomarca_txt}}</div>

                        <div id="ventas" class='ocultar'>{{$ventas}}</div>
                        <div id="tnc" class='ocultar'>{{$tnc}}</div>
                        <div id="prod" class='ocultar'>{{$jprod}}</div>
                        <div id="color" class='ocultar'>{{$jcol}}</div>

                        <div id="ventas_s" class='ocultar'>{{$ventas_s}}</div>
                        <div id="tnc_s" class='ocultar'>{{$tnc_s}}</div>
                        <div id="prod_s" class='ocultar'>{{$jprod_s}}</div>
                        <div id="color_s" class='ocultar'>{{$jcol_s}}</div>
                        
                        <div id="costos_s" class='ocultar'>{{$jcostos_s}}</div>
                        <div id="utilidad_s" class='ocultar'>{{$jutilidad_s}}</div>
                        <div id="jtotal_s" class='ocultar'>{{$jtotal_s}}</div>


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
          <div class="col-fr-2 col-atras">
            <span class="mdi mdi-arrow-left"></span>
          </div> 
          <div class="col-fr-10 col-total">
            <strong></strong> <strong class="">INDUAMERICA</strong>
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
    $(document).ready(function(){

      App.init();
      App.formElements();
      $('.scroll_text_horizontal_analitica').scrollLeft(500);

    });
  </script>

  <script src="{{ asset('public/js/analitica/ventasxproducto.js?v='.$version) }}" type="text/javascript"></script> 

@stop