@extends('templateanalitica')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>

@stop
@section('section')
  <div class="be-content contenido crearpedido">
    <div class="main-content container-fluid" style="padding: 1px;">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-table">
                <div class="panel-heading">Analitica de Ventas x Producto
<!--                   <div class="tools tooltiptop">
                    <a href="#" class="tooltipcss" id='buscarempresa' >
                      <span class="tooltiptext">Buscar</span>
                      <span class="icon mdi mdi-search"></span>
                    </a>

                  </div> -->
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
                          <div id="chart01" >
                          </div>
                          <input type="text" name="anio" id="anio" value='{{$anio}}' class='ocultar'>
                          <div id="meses" class='ocultar'>{{$meses}}</div>
                          <div id="ventas" class='ocultar'>{{$ventas}}</div>
                          <div id="tnc" class='ocultar'>{{$tnc}}</div>
                          <div id="prod" class='ocultar'>{{$jprod}}</div>
                          <div id="color" class='ocultar'>{{$jcol}}</div>
                          <div id="anio" class='ocultar'>{{$anio}}</div>
                          <div id="mes" class='ocultar'>{{$mes}}</div>
                          <div id="empresa_nombre_text" class='ocultar'>{{$empresa_nombre}}</div>
                          <div id="periodo_sel" class='ocultar'>{{$periodo_sel}}</div>
                          <div id="tipomarca_txt" class='ocultar'>{{$tipomarca_txt}}</div>
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
            <strong>Total : </strong> <strong class="total total-pedido"> S/. {{number_format($totalimporte, 2, '.', ',')}}</strong>
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