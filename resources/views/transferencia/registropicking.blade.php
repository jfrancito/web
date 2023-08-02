@extends('template')
@section('style')

    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')

<div id="modal-producto-individual" class="modal-container colored-header modal-effect-8 producto-individual">
  <div class="modal-content">
    <div class='modal-producto-individual-container'>    
    </div>
    <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cerrar</button>
    </div>
  </div>
</div>

  <div class="be-content despacho">
    <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-table">

                <div class="panel-heading">Crear Picking
                  <div class="tools tooltiptop">

                    <a href="#" class="tooltipcss" id='agregarproductoindividual'>
                      <span class="tooltiptext">Agregar Producto</span>
                      <span class="icon mdi mdi-plus-box"></span>
                    </a>

                    <a href="#" class="tooltipcss" id='buscarordenpedidodespacho'>
                      <span class="tooltiptext">Agregar Transferencia</span>
                      <span class="icon mdi mdi-plus-circle-o"></span>
                    </a>

                  </div>
                </div>
                <div class="panel-body selectfiltro">

                  <div class='filtrotabla row'>
                    <div class="col-xs-12">

                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">

                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" >Centro Origen :</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'centroorigen_id', $combo_lista_centros, array($picking->centro_origen_id),
                                                [
                                                  'class'       => 'select2 form-control control input-sm' ,
                                                  'id'          => 'centroorigen_id',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div> 

                      <input type="hidden" name="opcion" id='opcion' value='{{$idopcion}}'>
                      <input type="hidden" name="idpicking" id='idpicking' value='{{$idpicking}}'>
                      <input type="hidden" name="buscar" id='buscar' value='1'>

                  </div>

                  <div class="col-xs-12">
                    <div class='lista_pedidos_despacho listajax reporteajax'>

                        <input type="hidden" name="array_detalle_producto" id='array_detalle_producto' value='[]'>
                        <input type="hidden" name="correlativo" id='correlativo' value='{{$correlativo}}'>

                        <div class='ajaxvacio'>
                          Lista para agregar productos ...

                        </div>

                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
    </div>

  @include('transferencia.modal.mlistaproductopicking')

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
        $('[data-toggle="tooltip"]').tooltip();
        $('form').parsley();
        $('.dinero').inputmask({ 'alias': 'numeric', 
        'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
        'digitsOptional': false, 
        'prefix': '', 
        'placeholder': '0'});
      });
    </script> 

    <script src="{{ asset('public/js/transferencia/transferencia.js?v='.$version) }}" type="text/javascript"></script> 
@stop