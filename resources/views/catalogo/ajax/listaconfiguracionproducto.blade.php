<table id="table1" class="tablaproducto table table-striped table-striped dt-responsive nowrap listatabla" style='width: 100%;'>
  <thead>
    <tr>
      <th>Producto</th>
      <th>Bolsas x sacos</th>
      <th>Editar</th>
      <th>Sacos x palet</th>
      <th>Editar</th>  
    </tr>
  </thead>
  <tbody>
    @foreach($listaproductos as $item)

      <tr data_producto_id ="{{$item->COD_PRODUCTO}}">
      <td class="cell-detail relative">
        <span>{{$item->NOM_PRODUCTO}}</span>
        <span class="cell-detail-description-producto">
         {{$item->NOM_UNIDAD_MEDIDA}}
        </span>
      </td>
      <td class='center'>
          <b>{{number_format($item->CAN_BOLSA_SACO, 4, '.', ',')}}</b>
      </td>
      <td>
        <input type="text"  
               id="can_bolsa_saco" 
               name="can_bolsa_saco"
               value="{{number_format($item->CAN_BOLSA_SACO, 4, '.', ',')}}"
               class="form-control input-sm dinero"
               >
      </td>
      <td class='center'>
          <b>{{number_format($item->CAN_SACO_PALET, 4, '.', ',')}}</b>
      </td>
      <td>
        <input type="text"  
               id="can_saco_palet" 
               name="can_saco_palet"
               value="{{number_format($item->CAN_SACO_PALET, 4, '.', ',')}}"
               class="form-control input-sm dinero"
               >
      </td>
      </tr>                    
    @endforeach
  </tbody>
</table>


@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){

      App.dataTables();
      $('.dinero').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});
      
    });
  </script> 
@endif