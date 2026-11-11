@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/multiselect/css/bootstrap-multiselect.css') }} "/>


@stop
@section('section')

  <div class='despacho'>

    <div class="main-content container-fluid" style = "padding-top: 0px; padding-bottom: 0px;">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default panel-table" style = "margin-bottom: 0px;">
                    <div class="titulo_regla" >
                        <h4 class='center'><strong>Picking ({{$picking->codigo}})</strong></h4>
                         <input type="hidden" value="{{$picking->id}}" id='picking_id'/>
                    </div>
                    <div class="descuento_regla center" >
                        {{date_format(date_create($picking->fecha_picking), 'd-m-Y')}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="be-content">

    <div class="main-content container-fluid">
      <div class="row">
        <div class="col-sm-12">

          <div class="tab-container">
            <ul class="nav nav-tabs">
              <li class="seltab active" data_tab='ocen'>
                <a href="#atenderpicking" data-toggle="tab">Atender Picking</a>
              </li>
              <li class="seltab" data_tab='prod'>
                <a href="#tranferenciapt" data-toggle="tab">Transferencia PT</a>
              </li>
              <li class="seltab" data_tab='salida'>
                <a href="#ordensalida" data-toggle="tab">Orden Salida</a>
              </li>
            </ul>
            <div class="tab-content" style = "padding: 0px !important;">

              <div id="atenderpicking" class="tab-pane active cont">
                @include('picking.tab.tabatenderpicking')                
              </div>

              <div id="tranferenciapt" class="tab-pane  cont">
                 @include('picking.tab.tabtransferenciapt')   
              </div>

              <div id="ordensalida" class="tab-pane  cont">
                 @include('picking.tab.tabordensalida')   
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
    </div>
    @include('despacho.modal.mlistaproductoordencen')

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

  <script src="{{ asset('public/lib/multiselect/js/bootstrap-multiselect.js?v='.$version) }}" type="text/javascript"></script>

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

      $('.select_tabla_lote_id').multiselect({
            buttonWidth: '100px',
            numberDisplayed: -1
      });

      $('.scroll_text_horizontal').scrollLeft(365);

      $('[data-toggle="tooltip"]').tooltip();
      $('form').parsley();


      $('.despacho .dinero').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});


      $('.dineronrodoc').inputmask({ 'alias': 'numeric', 
      'groupSeparator': '', 'autoGroup': true, 'digits': 0, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': ''});


      $('.dinero_4_digitos').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});

      $('.empresa_select').select2({
          // Activamos la opcion "Tags" del plugin
          placeholder: 'Seleccione una empresa',
          language: "es",
          tags: true,
          tokenSeparators: [','],
          ajax: {
              dataType: 'json',
              url: '{{url("buscarempresadespacho")}}',
              delay: 100,
              data: function(params) {
                  return {
                      term: params.term
                  }
              },
              processResults: function (data, page) {
                return {
                  results: data
                };
              },
          }
      });



      $('.empresa_servicio_select').select2({
          // Activamos la opcion "Tags" del plugin
          placeholder: 'Seleccione una empresa',
          language: "es",
          tags: true,
          tokenSeparators: [','],
          ajax: {
              dataType: 'json',
              url: '{{url("buscarempresaserviciodespacho")}}',
              delay: 100,
              data: function(params) {
                  return {
                      term: params.term
                  }
              },
              processResults: function (data, page) {
                return {
                  results: data
                };
              },
          }
      });


    });
  </script> 

  <!-- <script src="{{ asset('public/js/despacho/atenderdespacho.js?v='.$version) }}" type="text/javascript"></script>  -->
  <script src="{{ asset('public/js/transferencia/picking.js?v='.$version) }}" type="text/javascript"></script> 
@stop