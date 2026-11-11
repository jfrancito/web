
<div class="panel panel-default panel-table">

<div class="panel-body">
  <div class='cuerpo_orden_salida'>
        <br>
        <div class="row">

          <div class="col-md-4">
            <div class="panel panel-contrast ">
              <div class="panel-heading panel-heading-contrast">Datos Venta</div>
              <div class="panel-body">

                <div class="col-xs-12">
                  <div class="form-group">
                    <label class="col-sm-12 control-label labelleft" >Nro. Orden : </label>
                    <div class="col-sm-12 abajocaja" >
                      
                      <input  type="text"
                              id="cod_orden" name='cod_orden' 
                              value=""
                              class="form-control input-md"
                              autocomplete="off"
                              readonly/>

                    </div>
                  </div>
                </div>

                <div class="col-xs-12">
                  <div class="form-group">
                    <label class="col-sm-12 control-label labelleft" >Cliente : </label>
                    <div class="col-sm-12 abajocaja" >
                      
                      <input  type="text"
                              id="txt_cliente" name='txt_cliente' 
                              value=""
                              class="form-control input-md"
                              autocomplete="off"
                              readonly/>

                    </div>
                  </div>
                </div>


                <div class="col-xs-12">
                  <div class="form-group">
                    <label class="col-sm-12 control-label labelleft" >Fecha Orden : </label>
                    <div class="col-sm-12 abajocaja" >
                      
                      <input  type="text"
                              id="fec_orden" name='fec_orden' 
                              value=""
                              class="form-control input-md"
                              autocomplete="off"
                              readonly/>

                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>   

          <div class="col-md-4">
            <div class="panel panel-contrast">
              <div class="panel-heading panel-heading-contrast">Datos Salida</div>
              <div class="panel-body">

              <div class="col-xs-12">
                  <div class="form-group">
                    <label class="col-sm-12 control-label labelleft" >Propietario : </label>
                    <div class="col-sm-12 abajocaja" >
                      
                          {!! Form::select( 'empresa_propietario', $combo_empresas, array($data_empresa->NOM_EMPR),
                                            [
                                              'class'       => 'select2 form-control control input-xs' ,
                                              'id'          => 'empresa_propietario',                                                 
                                              'data-aw'     => '1',
                                            ]) !!} 

                    </div>
                  </div>
                </div>

                <div class="col-xs-12">
                  <div class="form-group">
                    <label class="col-sm-12 control-label labelleft" >Servicio : </label>
                    <div class="col-sm-12 abajocaja" >
                      
                          {!! Form::select( 'empresa_servicio', $combo_empresas, array($data_empresa->NOM_EMPR),
                                            [
                                              'class'       => 'select2 form-control control input-xs',
                                              'id'          => 'empresa_servicio',
                                              'data-aw'     => '2',
                                            ]) !!}

                    </div>
                  </div>
                </div>

                <div class="col-xs-12">
                  <div class="form-group">
                    <label class="col-sm-12 control-label labelleft" >Glosa : </label>
                    <div class="col-sm-12 abajocaja" >
                      
                      <input  type="text"
                              id="txt_glosa" name='txt_glosa' 
                              value=""
                              placeholder="Glosa"
                              class="form-control input-md"
                              autocomplete="off"/>
                    </div>
                  </div>                  
                </div>    

              </div>
            </div>
          </div>

                 
        </div>
  </div>
</div>

<div class="panel-body">




  <div class="tab-container">
    <ul class="nav nav-tabs">
      <li class="seltab active" data_tab='ocen'>
        <a href="#tmaterialesv" data-toggle="tab">Materiales</a>
      </li>

      <li class="seltab" data_tab='ocen'>
        <a href="#tserviciosv" data-toggle="tab">Servicios</a>
      </li>

    </ul>


    <div class="tab-content" style = "padding: 0px !important;">
      <div id="tmaterialesv" class="tab-pane active cont">

        <div class='ajax_lista_producto_ordensalida'>
            <input type="hidden" name="h_array_productos_ordensalida" id="h_array_productos_ordensalida" value="{{json_encode($data_productos_tranferencia_pt)}}">
            <input type="hidden" name="calcula_cantidad_peso" id = "calcula_cantidad_peso" value = "{{$calcula_cantidad_peso}}">
        </div>

      </div>

      <div id="tserviciosv" class="tab-pane  cont">

        <div class="panel-heading">
          <div class="tools dropdown show">
            <div class="dropdown">
              <span class="icon toggle-loading mdi mdi-plus-circle-o agregar_serviciov" style='color:#34a853;' title="Agregar Servicio"></span>
            </div>
          </div>
        </div>

        <div class="scroll_text_horizontal ajax_lista_serviciov" style = "padding: 0px !important;">
            @include('picking.tab.tablas.listaserviciossalida')
        </div>            
      </div>

    </div>


  </div>

  <form method="POST"  action="{{ url('/crear-ordensalida-picking/'.$idopcion.'/'.$idpicking) }}">
    {{ csrf_field() }}

    <input type="hidden" name="h_glosa" id="h_glosa">
    <input type="hidden" name="h_empresa_propietario" id="h_empresa_propietario">
    <input type="hidden" name="h_empresa_servicio" id="h_empresa_servicio">
    <input type="hidden" name="array_productos_ordensalida_h" id="array_productos_ordensalida_h">
    <input type="hidden" name="array_servicio_ordensalida_h" id="array_servicio_ordensalida_h">
    
    <div class="row xs-pt-15">
      <div class="col-xs-6">
          <div class="be-checkbox">
          </div>
      </div>
      <div class="col-xs-6">
        <p class="text-right">
          <button type="submit" class="btn btn-space btn-primary btn_guardar_ordensalida">Guardar</button>
        </p>
      </div>
    </div>
  </form>



</div>


</div>
