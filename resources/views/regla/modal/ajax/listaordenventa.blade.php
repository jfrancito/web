<table id="tableordenventa" class="table table-striped table-hover table-fw-widget listatabla">
  <thead>
    <tr>
      <th class="text-center" style="width: 40px;">
        <div class="be-checkbox be-checkbox-sm">
          <input id="check_all_ov" type="checkbox">
          <label for="check_all_ov"></label>
        </div>
      </th>
      <th>CodOrden</th>
      <th>Fecha Orden</th>
      <th>Proveedor</th>
      <th>Regla</th>
      <th>Fecha Compromiso</th>
      <th>Autorizado Por</th>
      <th>Glosa</th>
      <th style="width: 100px;">Asignar</th>
    </tr>
  </thead>
  <tbody>
  @foreach($lista_deuda as $index => $item)
    @if (!in_array($item['COD_ORDEN'], $array_orden))
      @if($item['DIAS_MOROSO'] > 0)
          <tr class='filaorden' data_cod_orden_venta="{{$item['COD_ORDEN']}}">
            <td class="text-center">
              <div class="be-checkbox be-checkbox-sm">
                <input id="check_{{$item['COD_ORDEN']}}" type="checkbox" class="check_ov" value="{{$item['COD_ORDEN']}}">
                <label for="check_{{$item['COD_ORDEN']}}"></label>
              </div>
            </td>
            <td class="text-secondary">#{{$item['COD_ORDEN']}}</td>
            <td>
              {{date_format(date_create($item['FEC_EMISION']), 'd-m-Y')}}
              @if($item['DIAS_MOROSO'] > 0)
                <br><span class="label label-danger" style="font-size: 0.75em;">{{$item['DIAS_MOROSO']}} días moroso</span>
              @endif
            </td>
            <td><b>{{$item['CLIENTE']}}</b></td>
            <td class="seledtregla">
              {!! Form::select( 'regla_id', $comboregla, '', ['class' => 'form-control input-sm select_regla']) !!}
            </td>
            <td class="sedfecha_compromiso">
              <div data-min-view="2" data-date-format="dd-mm-yyyy" class="input-group date datetimepicker" style="width: 140px;">
                <input size="16" type="text" value="{{date('d-m-Y')}}" name='fecha_compromiso' class="form-control input-sm fecha_compromiso">
                <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
              </div>
            </td>
            <td class="sedautorizado">
              {!! Form::select( 'autorizado_id', $comboautorizados, '', ['class' => 'form-control input-sm select_autorizado', 'style' => 'width: 180px;']) !!}
            </td>
            <td class="sedglosa">
              <input type="text" name="glosa" class="form-control input-sm glosa" placeholder="Ingresar glosa..." style="width: 150px;">
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-asignar asignar_regla" title="Asignar Regla">
                <i class="icon mdi mdi-plus-circle"></i>
              </button>
            </td>
          </tr>
        @endif
    @endif
  @endforeach
  </tbody>
</table>

<script type="text/javascript">
      $(document).ready(function(){
        App.dataTables();
        App.formElements();
        $('form').parsley();
      });
</script> 