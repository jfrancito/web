
<table id="despacholocen" class="table table table-hover table-fw-widget dt-responsive nowrap lista_tabla_oc" style='width: 100%;'>
  <thead>
    <tr> 
      <th>Codigo</th>
      <th>Cliente</th>
      <th>Estado</th>
      <th>Fecha Orden</th>
      <th>Fecha Entrega</th>
      <th>Destino</th>
      <th>Total Venta</th>
      <th>Sel</th>
    </tr>
  </thead>
  <tbody>
   @foreach($listaventas as $item)
      <tr class="data-fila">
        <td class="cell-detail">
          <span>{{$item->COD_ORDEN}}</span>
        </td>
        <td class="cell-detail">
          <span>{{$item->TXT_EMPR_CLIENTE}}</span>
        </td>
        <td>
          @if($item->COD_CATEGORIA_ESTADO_ORDEN == 'EOR0000000000018') 
              <span class="badge badge-warning">{{$item->TXT_CATEGORIA_ESTADO_ORDEN}}</span> 
          @else
              <span class="badge badge-primary">{{$item->TXT_CATEGORIA_ESTADO_ORDEN}}</span>
          @endif
        </td>     
        <td>{{date_format(date_create($item->FEC_ORDEN), 'd-m-Y')}}</td>
        <td>{{date_format(date_create($item->FEC_ENTREGA), 'd-m-Y')}}</td>             
        <td> </td>
        <td>{{$item->CAN_TOTAL}}</td>  
        <td>
            <div class="text-center be-checkbox be-checkbox-sm" >
              <input  type="checkbox"
                      class="{{$item->COD_ORDEN}} input_check_pe_ov check{{$item->COD_ORDEN}}" 
                      id="check{{$item->COD_ORDEN}}" 
                      data_trans = "{{$item->COD_ORDEN}}"
                      data = "{{$item->detalleproducto}}" 
              >
              <label  for="check{{$item->COD_ORDEN}}"
                    data-atr = "ver"
                    class = "checkbox"                    
                    name="check{{$item->COD_ORDEN}}"
              ></label>
            </div>
          </td>
      </tr>                    
    @endforeach
  </tbody>

</table>


