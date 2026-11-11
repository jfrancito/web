
<table id="tableprecios" class="table table-striped table-hover table-fw-widget">
  <thead>
    <tr>
      <th>CODIGO GUIA</th>
      <th>SERIE GUIA</th>
      <th>NRO GUIS</th>
      <th>FECHA EMISION</th>
      <th>EMPRESA</th>
      <th>CENTRO</th>
      <th>OPCION</th>      
    </tr>
  </thead>
  <tbody>

    @foreach($detraciongruia as $item)
      <tr>

        <td>{{$item->COD_DOCUMENTO_CTBLE}}</td>
        <td>{{$item->NRO_SERIE}}</td>
        <td>{{$item->NRO_DOC}}</td>
        <td>{{date_format(date_create($item->FEC_EMISION), 'd-m-Y H:i')}}</td>
        <td>{{$item->TXT_EMPR_EMISOR}}</td>
        <td>{{$item->NOM_CENTRO}}</td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="{{ url('/descargar-detraccion-guias/'.$item->COD_DOCUMENTO_CTBLE)}}" target="_blank">
                  Descargar Boletas
                </a>  
              </li>
            </ul>
          </div>
        </td>
      </tr>                    
    @endforeach

  </tbody>
</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif