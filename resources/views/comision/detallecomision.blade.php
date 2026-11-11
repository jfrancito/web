@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')


  <div class="be-content listapedidoosiris">
    <div class="main-content container-fluid main-content-mobile">
          <div class="row">
            <div class="col-sm-12 col-mobil">
              <div class="panel panel-default panel-table">
                <div class="panel-heading">Lista de vendedores con su comsiones (COMISIONES)
                  <div class="tools tooltiptop">


                    <form method="POST" id='formpedido' class='opciones' action="{{ url('/cambiar-estado-comsion/'.$idopcion) }}" style="display: inline-block;" >
                      {{ csrf_field() }}
                      <input type="hidden" id='pedido' name='pedido'>
                      <input type="hidden" id='cod_periodo' name='cod_periodo' value='{{$codperiodo}}'>
                      <input type="hidden" id='cod_estado_re' name='cod_estado_re' >

                      @if(Session::get('usuario')->id == '1CIX00000001' or Session::get('usuario')->id == '1CIX00000046' or Session::get('usuario')->id == '1CIX00000047') 
                        <a href="#" class="tooltipcss enviarcomosionautorizacion" id='enviarcomosionautorizacion' data_estado='EPP0000000000004'>
                          <span class="tooltiptext">Ejecutar</span>
                          <span class="icon mdi mdi-check-all " style="color: #34a853;"></span>
                        </a>
                      @else

                      <a href="#" class="tooltipcss enviarcomosionautorizacion" id='enviarcomosionautorizacion' data_estado='EPP0000000000003'>
                        <span class="tooltiptext">Autorizar Comision</span>
                        <span class="icon mdi mdi-mail-send"></span>
                      </a>
                      <a href="#" class="tooltipcss enviarcomosionautorizacion" id='enviarcomosionautorizacion' data_estado='EPP0000000000002'>
                        <span class="tooltiptext">Regresar Estado Comision</span>
                        <span class="icon mdi mdi-block-alt "></span>
                      </a>
                      
                      <a href="#" class="tooltipcss enviarcomosionautorizacion" id='enviarcomosionautorizacion' data_estado='EPP0000000000001'>
                        <span class="tooltiptext">Eliminar</span>
                        <span class="icon mdi mdi-close-circle-o " style="color: red;"></span>
                      </a>

                      @endif


                    </form>

                  </div>
                </div>
                <div class="panel-body">


                  <div class='filtrotabla row'>


                  </div>


                  <div class='listatablapedido listajax'>

                    @include('comision.ajax.listadetallecomisiones')

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

        
        $('.dinero').inputmask({ 'alias': 'numeric', 
          'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
          'digitsOptional': false, 
          'prefix': '', 
          'placeholder': '0'});

      });
    </script> 

  <script src="{{ asset('public/js/comision/comision.js?v='.$version) }}" type="text/javascript"></script>

@stop