

<div class="col-sm-3 abajocaja" style="margin-top: 7px;">
  {!! Form::select( 'tipo_grupo', $combotipogrupo, array(),
                    [
                      'class'       => 'select2 form-control control input-sm' ,
                      'id'          => 'tipo_grupo',
                      'required'    => '',
                      'data-aw'     => '1',
                    ]) !!}
</div>

<table id="despacholocen" class="table table table-hover table-fw-widget dt-responsive nowrap lista_tabla_oc" style='width: 100%;'>
  <thead>
    <tr> 
      <th>Cod. Orden</th>
      <th>Orden Cen</th>
      <th>Fecha Orden</th>
      <th>Cliente</th>
      <th>Estado Orden</th>
      <th>Sel</th>
    </tr>
  </thead>
  <tbody>

    @if($cliente_id <> '')
      @while ($row = $listaordencen->fetch())
        <tr
          class='filaoc'
          data_orden_id="{{$row['COD_ORDEN']}}"
        >
          <td>
            {{$row['COD_ORDEN']}}
          </td>
          <td>{{$row['NRO_ORDEN_CEN']}}</td>
          <td>{{date_format(date_create($row['FEC_ORDEN']), 'd-m-Y')}}</td>
          <td>{{$row['TXT_EMPR_CLIENTE']}}</td>
          <td>{{$row['TXT_CATEGORIA_ESTADO_ORDEN']}}</td>
          <td>
            <div class="text-center be-checkbox be-checkbox-sm has-primary">
              <input  
                type="checkbox"
                class="{{$row['COD_ORDEN']}} input_asignar_oc"
                id="{{$row['COD_ORDEN']}}" >

              <label  for="{{$row['COD_ORDEN']}}"
                    data-atr = "ver"
                    class = "checkbox checkbox_asignar_oc"                    
                    name="{{$row['COD_ORDEN']}}"
              ></label>
            </div>
          </td>

        </tr>                    
      @endwhile
    @endif



  </tbody>
</table>


