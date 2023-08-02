
<table id="despacholov" class="table table table-hover table-fw-widget dt-responsive nowrap lista_tabla_ov" style='width: 100%;'>
  <thead>
    <tr> 
      <th>Codigo</th>
      <th>Destino</th>
      <th>Estado</th>
      <th>Fecha Pedido</th>
      <th>Fecha Entrega</th>
      <th>Hora Entrega</th>
      <th>Sel</th>
    </tr>
  </thead>
  <tbody>
   @foreach($listapedidos as $item)
      <tr class="data-fila">
        <td class="cell-detail">
            <span>{{$item->codigo}}</span>
        </td>
          
        <td>{{$item->destino}}</td>
        
        <td>
          @if($item->estado_id == 'EPP0000000000003') 
              <span class="badge badge-warning">{{$item->estado_nom}}</span> 
          @else
              <span class="badge badge-primary">{{$item->estado_nom}}</span>
          @endif
        </td>     
        <td>{{date_format(date_create($item->fecha_pedido), 'd-m-Y')}}</td>
        <td>{{date_format(date_create($item->fecha_entrega), 'd-m-Y')}}</td>             
        <td>{{$item->hora_entrega}}</td>
        <td>
            <div class="text-center be-checkbox be-checkbox-sm" >
              <input  type="checkbox"
                      class="{{$item->id}} input_check_pe_ln check{{$item->id}}" 
                      id="check{{$item->id}}" 
                      data_trans = "{{$item->codigo}}"
                      data = "{{$item->transferenciadetalle}}" 
              >
              <label  for="check{{$item->id}}"
                    data-atr = "ver"
                    class = "checkbox"                    
                    name="check{{$item->id}}"
              ></label>
            </div>
          </td>
      </tr>                    
    @endforeach
  </tbody>

</table>


