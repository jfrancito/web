
<div class="panel panel-contrast">
  <div class="panel-heading panel-heading-contrast">
      
      <strong class='c_nombre_cliente'>{{Session::get('empresas')->NOM_EMPR}}</strong>
      <span class="mdi mdi-close-circle mdi-close-cliente"></span>
      <span class="mdi mdi-check-circle mdi-check-cliente"
          data_codemp = '{{$empresa_reg->COD_EMPR}}'
          data_nomemp = '{{$empresa_reg->NOM_EMPR}}'
          ></span>    
  </div>   
</div>

<div class="panel-body">  
    <div class="col-xs-3 margen-top-filtro">
    </div>  
    <div class="col-xs-6 margen-top-filtro"  id="pedidocontainer">

         <div class="form-group">
              <label class="col-sm-12 control-label labelleft"> Fecha Pedido </label>
              <div class="col-xs-12 abajocaja">
                      <div data-min-view="2" data-date-format="yyyy-mm-dd" class="input-group date datetimepicker">
                        <input  id="fechapedido" size="16" type="text" value="{{$transferencia->fecha_pedido}}" class="form-control" disabled><span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                      </div>
              </div>
         </div>

         <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Fecha Entrega </label>
              <div class="col-xs-12 abajocaja">
                        <div data-min-view="2" data-date-format="yyyy-mm-dd" class="input-group date datetimepicker">
                          <input  id="fechaentrega" size="16" type="text" value="{{$transferencia->fecha_entrega}}" class="form-control"><span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                        </div>
              </div>
         </div>

        <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Hora Entrega </label>
              <div class="col-xs-12 abajocaja">
                        <!--div data-min-view="2" data-date-format="HH:mm" class="input-group date datetimepicker"-->
                          <input  name = "horaentrega" id="horaentrega" size="16" type="time" value="{{$transferencia->hora_entrega}}" class="form-control"><span class="input-group-addon btn btn-primary"></span>
                        <!--/div-->
              </div>
         </div>

         <div class="form-group">
            <label class="col-sm-12 control-label labelleft" > Centro Origen </label>
          <div class="col-sm-12 abajocaja" >

            {!! Form::select( 'centroorigen_select', $combo_centros, array($transferencia->centro_origen_id),
                              [
                                'class'       => 'form-control control' ,
                                'id'          => 'centroorigen_select',
                                'data-aw'     => '1',
                              ]) !!}
          </div>
        </div>

        <div class="form-group">
            <label class="col-sm-12 control-label labelleft" > Almacén Destino </label>
          <div class="col-sm-12 abajocaja" >

            {!! Form::select( 'almacen_select', $comboalmacen, array($transferencia->almacen_destino_id),
                              [
                                'class'       => 'form-control control' ,
                                'id'          => 'almacen_select',
                                'data-aw'     => '1',
                              ]) !!}
          </div>
        </div>

          <div class="form-group">
                <label class="col-sm-12 control-label"> Cliente Referencial </label>
                <div class="col-sm-12 abajocaja">
                  <select class="select2 input-sm" id="cliente_op_select" name='cliente_op_select' required = "">
                    <optgroup label="Clientes">
                      <option value="">Seleccione Cliente</option>
                      @foreach($listaclientes as $item)
                        <option value="{{$item->id}}" {{($transferencia->cliente_id == $item->id) ? 'selected' : ''}}>{{$item->nombres}}</option>
                      @endforeach
                    </optgroup>
                  </select>
                </div>
           </div>
        

    </div>
</div>