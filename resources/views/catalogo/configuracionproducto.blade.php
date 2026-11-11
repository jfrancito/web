@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
@stop
@section('section')
  <div class="be-content configuracionproducto">
    <div class="main-content container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <!--Dropdowns-->
          <div class="panel panel-default">
            <div class="panel-heading panel-heading-divider">Configuracion del producto
              <span class="panel-subtitle">Se realizara la modificación de cualquier producto</span>
            </div>


            <!--Tabs-->
            <div class="row">
              <!--Success Tabs-->
              <div class="col-sm-12">
                <div class="panel panel-default">
                  <div class="panel-heading">

                    Lista de Productos
                    <div class="tools dropdown show">
                      <div class="dropdown">
                        <span class="icon toggle-loading mdi mdi-save guardarcambios" style='color:#34a853;' title="Guardar cambios"> Guardar</span>
                      </div>
                    </div>
                  </div>
                  <div class="tab-container">
                    <ul class="nav nav-tabs nav-tabs-success">
                      <li class="active"><a href="#producto" data-toggle="tab">Productos</a></li>
                    </ul>
                    <div class="tab-content">
                      <div id="producto" class="tab-pane active cont">
                        <div class='ajax_lista_configuracion_producto'>
                          
                          @include('catalogo.ajax.listaconfiguracionproducto')

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
    <script src="{{ asset('public/js/app-tables-datatables.js') }}" type="text/javascript"></script>




    <script type="text/javascript">
      $(document).ready(function(){
        //initialize the javascript
        App.init();
        App.dataTables();
        $('[data-toggle="tooltip"]').tooltip(); 

        $('.dinero').inputmask({ 'alias': 'numeric', 
        'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
        'digitsOptional': false, 
        'prefix': '', 
        'placeholder': '0'});

      });
    </script>


    <script src="{{ asset('public/js/catalogo/producto.js?v='.$version) }}" type="text/javascript"></script> 
@stop