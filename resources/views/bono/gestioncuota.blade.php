@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>

@stop
@section('section')

<div class="be-content cuota">
  <div class="main-content container-fluid">
    <!--Basic forms-->
    <div class="row">
      <div class="col-md-12">
        <div class="panel  panel-border-color panel-border-color-primary">
          <div class="panel-heading panel-heading-divider">Cuotas
            <div class="tools tooltiptop">
              @if($cuota->estado_id == 'EPP0000000000002')
                <a href="#" class="tooltipcss opciones clonarfechaanteriores"                     
                    data_cuota='{{ $cuota->id }}'
                    data_opcion='{{ $idopcion }}'>
                  <span class="tooltiptext">Clonar Configuracion mes anterior</span>
                  <span class="icon mdi mdi-collection-plus"              
                  ></span>
                </a>
                <a href="#" class="tooltipcss opciones agregacuota" 
                   data_cuota_id = '{{$cuota->id}}'>
                  <span class="tooltiptext">Agregar cuotas</span>
                  <span class="icon mdi mdi-plus-circle-o"></span>
                </a>
              @endif   

            </div>
            <span class="panel-subtitle">Configurar Bono</span>
            <span class="panel-subtitle">Codigo : {{$cuota->codigo}}</span>
            <span class="panel-subtitle">Anio : {{$cuota->anio}}</span>
            <span class="panel-subtitle">Mes : {{$cuota->mes}}</span>

            @if($cuota->estado_id == 'EPP0000000000002') 
              <span class="badge badge-default">{{$cuota->estado_nombre}}</span> 
            @else
                <span class="badge badge-success">{{$cuota->estado_nombre}}</span>
            @endif   


            <input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>
          </div>

          <div class="panel-body">

            <div class='listajax'>
              @include('bono.ajax.alistadetallecuota')
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


  <script src="{{ asset('public/js/general/inputmask/inputmask.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/inputmask.extensions.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/inputmask.numeric.extensions.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/inputmask.date.extensions.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/jquery.inputmask.js') }}" type="text/javascript"></script>


  <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>

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

      $('.importe').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 3, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});


    });

  </script>
  <script src="{{ asset('public/js/bono/bono.js?v='.$version) }}" type="text/javascript"></script>

@stop