@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')

  <div class='gestioncontacto'>

    <div class="be-content">
        <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-table">
                <div class="panel-heading">Gestion de Contactos
                </div>
                <div class="panel-body selectfiltro">

                  <div class='filtrotabla row'>
                    <div class="col-xs-12">


                      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 cajareporte">

                          <div class="form-group">
                              <label class="col-sm-12 control-label labelleft" >Nombre :</label>
                              <div class="col-sm-12 input-group xs-mb-15">

                                <input  type="text"
                                        id="nombre" name='nombre' value="" placeholder="Nombre Completo"
                                        required = ""
                                        autocomplete="off" class="form-control input-md" data-aw="4"/>


                              </div>
                          </div>
                      </div> 


                      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 cajareporte">

                          <div class="form-group">
                              <label class="col-sm-12 control-label labelleft" >Whatsapp :</label>
                              <div class="col-sm-12 input-group xs-mb-15">

                                <input  type="number"
                                        id="celular" name='celular' value="" placeholder="Whatsapp"
                                        required = ""
                                        autocomplete="off" class="form-control input-md" data-aw="4"/>

                                <span class="input-group-btn">
                                      <button id="asignarcelular" type="button" class="btn btn-primary ">
                                        <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Asignar</font></font>
                                      </button>
                                </span>
                                
                              </div>
                          </div>
                      </div> 


                  </div>

                  <div class="col-xs-12">
                    <div class='listacontratomasiva listajax reporteajax'>
                        <div class='ajaxvacio'>
                          Seleccione un responsable y producto ...

                        </div>
                    </div>
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


  <script src="{{ asset('public/js/general/inputmask/inputmask.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/inputmask.extensions.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/inputmask.numeric.extensions.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/inputmask.date.extensions.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/jquery.inputmask.js') }}" type="text/javascript"></script>

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

  <script src="{{ asset('public/js/campania/contacto.js?v='.$version) }}" type="text/javascript"></script> 
@stop