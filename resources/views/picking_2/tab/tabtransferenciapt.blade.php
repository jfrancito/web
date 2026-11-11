
<div class="panel panel-default panel-table">

    <div class="panel-body">
      <div class='cuerpo_transferencia_pt'>
            <br>
            <div class="row">
              <div class="col-md-4">
                <div class="panel panel-contrast">
                  <div class="panel-heading panel-heading-contrast">Transferencia PT</div>
                  <div class="panel-body">
                    <div class="col-xs-12">
                      <div class="form-group">
                        <label class="col-sm-12 control-label labelleft" >Glosa : </label>
                        <div class="col-sm-12 abajocaja" >
                          
                          <input  type="text"
                                  id="glosa" name='glosa' 
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
              <div class="col-md-4">
                <div class="panel panel-contrast ">
                  <div class="panel-heading panel-heading-contrast">Origen</div>
                  <div class="panel-body">

                    <div class="col-xs-12">
                      <div class="form-group">
                        <label class="col-sm-12 control-label labelleft" >Propietario : </label>
                        <div class="col-sm-12 abajocaja" >
                          
                              {!! Form::select( 'origen_propietario', $combo_empresas, array($data_empresa->NOM_EMPR),
                                                [
                                                  'class'       => 'select2 form-control control input-sm' ,
                                                  'id'          => 'origen_propietario',
                                                  'disabled'    => true,                                                  
                                                  'data-aw'     => '1',
                                                ]) !!} 

                        </div>
                      </div>
                    </div>

                    <div class="col-xs-12">
                      <div class="form-group">
                        <label class="col-sm-12 control-label labelleft" >Servicio : </label>
                        <div class="col-sm-12 abajocaja" >
                          
                              {!! Form::select( 'origen_servicio', $combo_empresas, array($data_empresa->NOM_EMPR),
                                                [
                                                  'class'       => 'select2 form-control control input-sm' ,
                                                  'id'          => 'origen_servicio',
                                                  'disabled'    => true,                                                  
                                                  'data-aw'     => '2',
                                                ]) !!}

                        </div>
                      </div>
                    </div>


                    <div class="col-xs-12">
                      <div class="form-group">
                        <label class="col-sm-12 control-label labelleft" >Centro : </label>
                        <div class="col-sm-12 abajocaja" >
                          
                              {!! Form::select( 'origen_centro', $combo_centro, array($data_centro->COD_CENTRO),
                                                [
                                                  'class'       => 'select2 form-control control input-sm centro_select' ,
                                                  'id'          => 'origen_centro',
                                                  'disabled'    => true,
                                                  'data-aw'     => '2',
                                                ]) !!}

                        </div>
                      </div>
                    </div>

                    <div class="col-xs-12 ajax_almacen_origen">
                      @include('despacho.ajax.acomboalmacenorigen')
                    </div>

                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="panel panel-contrast">
                  <div class="panel-heading panel-heading-contrast">Destino</div>
                  <div class="panel-body">

                    <div class="col-xs-12">
                      <div class="form-group">
                        <label class="col-sm-12 control-label labelleft" >Propietario : </label>
                        <div class="col-sm-12 abajocaja" >
                          
                              {!! Form::select( 'destino_propietario', $combo_empresas, array($data_empresa->NOM_EMPR),
                                                [
                                                  'class'       => 'select2 form-control control input-xs empresa_select',
                                                  'id'          => 'destino_propietario',
                                                  'data-aw'     => '1',
                                                ]) !!}

                        </div>
                      </div>
                    </div>

                    <div class="col-xs-12">
                      <div class="form-group">
                        <label class="col-sm-12 control-label labelleft" >Servicio : </label>
                        <div class="col-sm-12 abajocaja" >
                          
                              {!! Form::select( 'destino_servicio', $combo_empresas, array($data_empresa->NOM_EMPR),
                                                [
                                                  'class'       => 'select2 form-control control input-xs empresa_select',
                                                  'id'          => 'destino_servicio',
                                                  'data-aw'     => '2',
                                                ]) !!}

                        </div>
                      </div>
                    </div>


                    <div class="col-xs-12">
                      <div class="form-group">
                        <label class="col-sm-12 control-label labelleft" >Centro : </label>
                        <div class="col-sm-12 abajocaja" >
                          
                              {!! Form::select( 'destino_centro', $combo_sin_centro, array($centrodestino),
                                                [
                                                  'class'       => 'select2 form-control control input-sm centro_select' ,
                                                  'id'          => 'destino_centro',
                                                  'data-aw'     => '2'
                                                ]) !!}
                        </div>
                      </div>
                    </div>

                    <div class="col-xs-12 ajax_almacen_destino">
                      @include('despacho.ajax.acomboalmacendestino')
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
            <a href="#tmateriales" data-toggle="tab">Materiales</a>
          </li>

          <li class="seltab" data_tab='ocen'>
            <a href="#tservicios" data-toggle="tab">Servicios</a>
          </li>

        </ul>


        <div class="tab-content" style = "padding: 0px !important;">
          <div id="tmateriales" class="tab-pane active cont">

            <div class='ajax_lista_producto_tp'>
                <input type="hidden" name="h_array_productos_transferencia_pt" id="h_array_productos_transferencia_pt" value="{{json_encode($data_productos_tranferencia_pt)}}">
                <input type="hidden" name="calcula_cantidad_peso" id = "calcula_cantidad_peso" value = "{{$calcula_cantidad_peso}}">
            </div>

          </div>
          <div id="tservicios" class="tab-pane  cont">

            <div class="panel-heading">
              <div class="tools dropdown show">
                <div class="dropdown">
                  <span class="icon toggle-loading mdi mdi-plus-circle-o agregar_servicio" style='color:#34a853;' title="Agregar Servicio"></span>
                </div>
              </div>
            </div>


            <div class="scroll_text_horizontal ajax_lista_servicio" style = "padding: 0px !important;">
              @include('picking.tab.tablas.listaserviciospt')
            </div>
          </div>
        </div>


      </div>

      <form method="POST"  action="{{ url('/crear-transferencia-picking/'.$idopcion.'/'.$idpicking) }}">
        {{ csrf_field() }}

        <input type="hidden" name="h_glosa" id="h_glosa">
        <input type="hidden" name="h_origen_propietario" id="h_origen_propietario">
        <input type="hidden" name="h_origen_servicio" id="h_origen_servicio">
        <input type="hidden" name="h_origen_almacen" id="h_origen_almacen">
        <input type="hidden" name="h_destino_propietario" id="h_destino_propietario">
        <input type="hidden" name="h_destino_servicio" id="h_destino_servicio">
        <input type="hidden" name="h_destino_centro" id="h_destino_centro">
        <input type="hidden" name="h_destino_almacen" id="h_destino_almacen">
        <input type="hidden" name="array_productos_transferencia_pt_h" id="array_productos_transferencia_pt_h">
        <input type="hidden" name="array_servicio_transferencia_pt_h" id="array_servicio_transferencia_pt_h">
        
        <div class="row xs-pt-15">
          <div class="col-xs-6">
              <div class="be-checkbox">
              </div>
          </div>
          <div class="col-xs-6">
            <p class="text-right">
              <button type="submit" class="btn btn-space btn-primary btn_guardar_transferencia_pt">Guardar</button>
            </p>
          </div>
        </div>
      </form>



    </div>


</div>