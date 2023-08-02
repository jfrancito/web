<div class='listaordencompra'>
  <table id="tablesolicitud" class="table table table-hover table-fw-widget dt-responsive nowrap" style='width: 100%;'>
    <thead>
      <tr> 
        <th></th>
        <th>Id</th>
        <th>Placa</th>
        <th>Hora / Fecha</th>
        <th>Chofer</th>
        <th>Estado</th>
        <th>Accion</th>

      </tr>
    </thead>
    <tbody>
      @while ($row = $listacarros->fetch())

        <tr>
          <td></td>
          <td
          class='filaocm'
          data_ioc="{{$row['COD_CARRO_INGRESO_SALIDA']}}"
          >
            {{$row['COD_CARRO_INGRESO_SALIDA']}}
          </td>

          <td>{{$row['TXT_PLACA']}}</td>
          <td>{{date_format(date_create($row['FEC_USUARIO_CREA_AUD']), 'd-m-Y H:i:s')}}</td>
          <td>{{$row['NOM_CHOFER']}}</td>
          <td>{{$row['NOM_CATEGORIA_ESTADO_CARRO']}}</td>
          <td>           

            <span class="badge badge-primary mdi mdi-edit btn-edit-estado-carro" 
                  data-id="{{$row['COD_CARRO_INGRESO_SALIDA']}}">
              Editar
            </span>

          </td>
        </tr>                    
        @endwhile
    </tbody>
  </table>
</div>


@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif
