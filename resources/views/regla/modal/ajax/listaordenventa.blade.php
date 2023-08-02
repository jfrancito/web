<table id="tableordenventa" class="table table-striped table-hover table-fw-widget listatabla">
  <thead>
    <tr>
      <th>CodOrden</th>
      <th>Fecha Orden</th>
      <th>Proveedor</th>

      <th>Regla</th>
      <th>Asignar</th>
    </tr>
  </thead>
  <tbody >
  @foreach($lista_deuda as $index => $item)

    @if (!in_array($item['COD_ORDEN'], $array_orden))
      @if($item['DIAS_MOROSO'] > 0)
          <tr class='filaorden'
            data_cod_orden_venta="{{$item['COD_ORDEN']}}"
            >
            <td>{{$item['COD_ORDEN']}}</td>
            <td>{{date_format(date_create($item['FEC_EMISION']), 'd-m-Y')}}</td>
            <td>{{$item['CLIENTE']}}</td>
            <td class="seledtregla">
              {!! Form::select( 'regla_id', $comboregla, '',
                      [
                        'class'       => 'select2 form-control control input-sm select_regla' ,
                        'id'          => 'regla_id',
                        'required'    => '',
                        'data-aw'     => '1',
                      ]) !!}
            </td>
            <td><button type="submit" style="margin-top: 10px;" class="btn btn-space btn-primary asignar_regla">Asignar</button></td>
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