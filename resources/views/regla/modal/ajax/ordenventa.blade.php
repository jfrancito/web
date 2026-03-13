
<div class="panel-heading">Lista de ordenes de venta ({{$funcion->funciones->data_cliente($cuenta_id)->NOM_EMPR}})
  <div class="tools tooltiptop">
    <input type="hidden" name="cuenta_id_m_ov" id='cuenta_id_m_ov' value = {{$cuenta_id}}>
  </div>
</div>


<div class="modal-header" style = "padding: 0px !important;">
    <div class='filtrotabla row'>
      <div class="col-xs-12" style="background: #f1f5f9; padding: 15px; border-radius: 8px; border: 1px solid #cbd5e1; margin-bottom: 15px;">
        <h5 style="margin-top: 0; font-weight: 700; color: #475569;"><i class="mdi mdi-layers" style="margin-right: 5px;"></i> ASIGNACIÓN MASIVA</h5>
        <div class="row">
          <div class="col-md-3">
            <label style="font-size: 11px; font-weight: 600;">Regla:</label>
            {!! Form::select( 'regla_id_masivo', $comboregla, '', ['class' => 'form-control input-sm', 'id' => 'regla_id_masivo']) !!}
          </div>
          <div class="col-md-2">
            <label style="font-size: 11px; font-weight: 600;">Fecha Compromiso:</label>
            <div data-min-view="2" data-date-format="dd-mm-yyyy" class="input-group date datetimepicker">
              <input size="16" type="text" value="{{date('d-m-Y')}}" id='fecha_compromiso_masivo' class="form-control input-sm">
              <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
            </div>
          </div>
          <div class="col-md-3">
            <label style="font-size: 11px; font-weight: 600;">Autorizado Por:</label>
            {!! Form::select( 'autorizado_id_masivo', $comboautorizados, '', ['class' => 'form-control input-sm', 'id' => 'autorizado_id_masivo']) !!}
          </div>
          <div class="col-md-2">
            <label style="font-size: 11px; font-weight: 600;">Glosa:</label>
            <input type="text" id="glosa_masivo" class="form-control input-sm" placeholder="Glosa masiva...">
          </div>
          <div class="col-md-2" style="padding-top: 22px;">
            <button type="button" class="btn btn-success btn-block btn-sm btn-asignar-masivo" style="font-weight: 700;">
              <i class="mdi mdi-check-all"></i> MASIVO
            </button>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="modal-body" style="padding: 10px 20px 20px;">
  <div class="scroll_text ajax_lista_orden_venta">
      @include('regla.modal.ajax.listaordenventa')
  </div>
</div>

