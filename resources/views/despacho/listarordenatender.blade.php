@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')

  <div class="be-content listadespacho">
    <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-table">
                <div class="panel-heading">Lista de pedidos para atender
                  <div class="tools tooltiptop">

                    <div class="dropdown">
                      <span class="icon toggle-loading mdi mdi-search guardarcambios"  id= 'buscarpedidoatender' style='color:#34a853;' title="Guardar cambios"></span>
<!--                       <span class="icon mdi mdi-more-vert dropdown-toggle" id="menudespacho" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></span> -->
<!--                       <ul class="dropdown-menu" aria-labelledby="menudespacho" style="margin: 7px -169px 0px;">
                        <li><a href="#"  id='imprimirpedidoatender'>Agregar Pedido Imprimir <span></span></a></li>
                        <li><a href="#" id='verimpresion'>Ver Impresion</a></li>
                        <li><a href="#" id='impresion'>Impresion</a></li>
                        <li><a href="#" id='limpiarimpresion'>Limpiar Impresion</a></li>
                      </ul> -->
                    </div>  
                    <input type="hidden" name="opcion_id" id= 'opcion_id' value = '{{$idopcion}}'>
                  </div>

                  <span class="panel-subtitle">Lista de todos los pedidos para atender que se realizarón  </span>
                </div>
                <div class="panel-body">

                  <div class='filtrotabla row'>
                    <div class="col-xs-12">


                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">

                          <div class="form-group ">
                            <label class="col-sm-12 control-label labelleft" >Fecha Inicio:</label>
                            <div class="col-sm-12 abajocaja" >
                              <div data-min-view="2" 
                                     data-date-format="dd-mm-yyyy"  
                                     class="input-group date datetimepicker " style = 'padding: 0px 0;margin-top: -3px;'>
                                     <input size="16" type="text" 
                                            value="{{$fechainicio}}" 
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

                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">

                          <div class="form-group ">
                            <label class="col-sm-12 control-label labelleft" >Fecha Fin:</label>
                            <div class="col-sm-12 abajocaja" >
                              <div data-min-view="2" 
                                     data-date-format="dd-mm-yyyy"  
                                     class="input-group date datetimepicker " style = 'padding: 0px 0;margin-top: -3px;'>
                                     <input size="16" type="text" 
                                            value="{{$fechafin}}" 
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
                    </div>
                  </div>



                  <div class='listatablapedidosatender listajax' >

                    @include('despacho.ajax.alistarpedidoatender')

                  </div>


                </div>
              </div>
            </div>
          </div>
    </div>
    @include('despacho.modal.mdetalleimprimir')

  </div>

@stop

@section('script')


  <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/jszipoo.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/pdfmake.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/vfs_fonts.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.html5.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.flash.js') }}" type="text/javascript"></script>
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
  <script src="{{ asset('public/js/despacho/despacho.js?v='.$version) }}" type="text/javascript"></script>

@stop