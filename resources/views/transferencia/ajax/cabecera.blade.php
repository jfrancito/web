<div class="panel panel-contrast">
<div class="panel-heading panel-heading-contrast">
      <strong class='c_nombre_cliente'>{{$data_ncl}}</strong>
      <span class="panel-subtitle c_documento-cuenta">{{$data_dcl}}</span>
      <span class="panel-subtitle c_documento-cuenta">{{$data_ccl}}</span>                           
      <span class="mdi mdi-close-circle mdi-close-cliente"></span>
      <span class="mdi mdi-check-circle mdi-check-cliente"
          data_icl='{{$data_icl}}'
          data_pcl='{{$data_pcl}}'
          data_icu='{{$data_icu}}'
          data_pcu='{{$data_pcu}}'
          data_ncl='{{$data_ncl}}'
          data_dcl='{{$data_dcl}}'
          data_ccl='{{$data_ccl}}'
          data_lic='{{$limite_credito}}'
          data_decv='{{$deuda_cliente_vencida}}'
          data_decg='{{$deuda_cliente_general}}'
      ></span>
    
    
  </div>    
</div>

<div class="panel-body">  

    <div class="col-xs-12 margen-top-filtro"  id="pedidocontainer">

         <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Fecha Crea Pedido </label>
              <div class="col-xs-12 abajocaja">
                        <div data-min-view="2" data-date-format="yyyy-mm-dd" class="input-group date datetimepicker">
                          <input  id="fechades" size="16" type="text" value="" class="form-control"><span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                        </div>
              </div>
         </div>

         <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Fecha Entrega </label>
              <div class="col-xs-12 abajocaja">
                        <div data-min-view="2" data-date-format="yyyy-mm-dd" class="input-group date datetimepicker">
                          <input  id="fechades" size="16" type="text" value="" class="form-control"><span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                        </div>
              </div>
         </div>

        <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Hora Entrega </label>
              <div class="col-xs-12 abajocaja">
                        <div data-min-view="2" data-date-format="yyyy-mm-dd" class="input-group date datetimepicker">
                          <input  id="fechades" size="16" type="text" value="" class="form-control"><span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                        </div>
              </div>
         </div>

        <div class="form-group">
          <label class="col-sm-12 control-label labelleft" >Dirección de entrega:</label>
          <div class="col-sm-12 abajocaja" >

            {!! Form::select( 'direccion_select', $combodirecciones, array(),
                              [
                                'class'       => 'form-control control' ,
                                'id'          => 'direccion_select',
                                'data-aw'     => '1',
                              ]) !!}
          </div>
      
        </div>
        <div class="form-group">
        <label class="col-sm-12 control-label labelleft" >Tipo de pago:</label>
          <div class="col-sm-12 abajocaja" >

          {!! Form::select( 'tipopago_select', $combotipopago, array(),
                            [
                              'class'       => 'form-control control' ,
                              'id'          => 'tipopago_select',
                              'data-aw'     => '1',
                            ]) !!}
          </div>
        </div>
    </div>


</div>
<script src="{{ asset('public/js/pedido/app-deuda.js') }}" type="text/javascript"></script>

<script src="{{ asset('public/lib/moment.js/min/moment.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>  
<script type="text/javascript">
    $(document).ready(function(){
      //initialize the javascript
      App.init();
      App.formElements();
      App.ChartJs();

    });
</script> 