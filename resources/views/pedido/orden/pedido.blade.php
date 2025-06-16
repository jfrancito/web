<div class="row detallecliente">
  <!-- DATOS DEL CLIENTE -->
</div>
<div class="row detalleproducto">
  <!-- DATOS DEL PRODUCTO -->
  
  <!--<div class="col-sm-12">
    <div class="panel panel-default panel-contrast">
      <div class="panel-heading cell-detail">
        Nombre Producto
        <div class="tools">
          <span class="icon mdi mdi-close"></span>
        </div>
        <span class="panel-subtitle cell-detail-description-producto">Unidad medida</span>
        <span class="panel-subtitle cell-detail-description-contrato">precio</span>
      </div>
    </div>
  </div> -->              
</div>
<div class="col-xs-12" style="margin-top: 25px !important;" >

      <div class="form-group">
          <label class="col-sm-12 control-label">
            Tipo Comprobante:
          </label>
          <div class="col-sm-12">
            <div class="input-group_mobil">
              {!! Form::select( 'tipo_documento', $combotipocom, array(''),
                                [
                                  'class'       => 'form-control control' ,
                                  'id'          => 'tipo_documento',
                                  'data-aw'     => '1',
                                ]) !!}
            </div>
          </div>
      </div>

      <div class="form-group">
          <label class="col-sm-12 control-label">
            Tipo Venta:
          </label>
          <div class="col-sm-12">
            <div class="input-group_mobil">
              {!! Form::select( 'tipo_venta', $combotipoorden, array($tipo_orden),
                                [
                                  'class'       => 'form-control control' ,
                                  'id'          => 'tipo_venta',
                                  'data-aw'     => '1',
                                ]) !!}
            </div>
          </div>
      </div>



      <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Orden CEN: </label>
              <div class="col-xs-12 abajocaja">
                <input id="iordencen" type="text" class="form-control input-sm" placeholder="" >
              </div>
      </div>

      <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Recibo de Conformidad: </label>
              <div class="col-xs-12 abajocaja">
                <input id="irecibo" type="number" class="form-control input-sm" placeholder="" >
              </div>
      </div>

      <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Contacto Gestion Transporte: </label>
              <div class="col-xs-12 abajocaja">
                <input id="contatogestiontransporte" type="text" class="form-control input-sm" placeholder="" >
              </div>
      </div>


      <div class="form-group">
           <label class="col-sm-12 control-label labelleft"> Observación </label>
              <div class="col-xs-12 abajocaja">
               <textarea id="iobs" class="form-control" rows="5"></textarea>
              </div>
      </div>
</div>
<div class='row-menu'>
  <div class='row'>
    <div class="col-sm-12 col-mobil-top">

      <div class="col-fr-2 col-atras">
        <span class="mdi mdi-arrow-left"></span>
      </div> 


      <div class="col-fr-2 col-actualizar-deuda"
            data_m_cliente_id=''>
        <span class="mdi mdi-refresh-alt  md-trigger"></span>
      </div> 

      <div class="col-fr-2 col-deuda"
            data_m_cliente_id=''>
        <span class="mdi mdi-money-off  md-trigger"></span>
      </div> 






      <div class="col-fr-8 col-total">
        <strong>Total : </strong> <strong class='total total-pedido'> 0.00</strong>
      </div> 
    </div>
  </div>
</div>



<form method="POST" action="{{ url('/agregar-orden-pedido/'.$idopcion) }}" class="form-horizontal group-border-dashed form-pedido">
  {{ csrf_field() }}

  <input type="hidden" name="cliente" id='cliente'>
  <input type="hidden" name="cuenta" id='cuenta'>
  <input type="hidden" name="direccion_entrega" id='direccion_entrega'>
  <input type="hidden" name="fecha_entrega" id='fecha_entrega'>
  <input type="hidden" name="condicion_pago" id='condicion_pago'>
  <input type="hidden" name="productos" id='productos'>
  <input type="hidden" name="obs" id='obs'>
  <input type="hidden" name="ordencen" id='ordencen'>
  <input type="hidden" name="recibo" id='recibo'>
  <input type="hidden" name="relacionadas" id='relacionadas'>
  <input type="hidden" name="c_limite_credito" id='c_limite_credito'>
  <input type="hidden" name="c_deuda_cliente_vencida" id='c_deuda_cliente_vencida'>
  <input type="hidden" name="c_deuda_cliente_general" id='c_deuda_cliente_general'>
  <input type="hidden" name="c_deuda_oryza" id='c_deuda_oryza'>
  <input type="hidden" name="c_adicional_limite_credito" id='c_adicional_limite_credito'>

  <input type="hidden" name="c_ind_regla_canal_subcanal" id='c_ind_regla_canal_subcanal'>

  <input type="hidden" name="c_tipo_documento" id='c_tipo_documento'>
  <input type="hidden" name="c_tipo_venta" id='c_tipo_venta'>
  
  <button type="submit" class="btn btn-space btn-success btn-big btn-guardar">
    <i class="icon mdi mdi-check"></i> Guardar
  </button>

</form>

