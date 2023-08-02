<table id="tableordenventa" class="table table-striped table-hover table-fw-widget listatabla">
  <thead>
    <tr>
      <th>Cliente</th>
      <th>Nro Documento</th>
      <th>Regla</th>
      <th>Asignar</th>
    </tr>
  </thead>
  <tbody >
  @foreach($lista_clientes as $index => $item)
      <tr class='filacliente'
          data_cod_cliente='{{$item->id}}'
          >
        <td>{{$item->NOM_EMPR}}</td>
        <td>{{$item->NRO_DOCUMENTO}}</td>
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