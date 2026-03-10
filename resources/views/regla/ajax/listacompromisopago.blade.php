<div class="table-responsive" style="border-radius: 8px; overflow: hidden; border: 1px solid #eee;">
  <table id="tablereportecp" class="table table-striped table-hover table-fw-widget" style="width: 100%;">
    <thead style="background-color: #f8f9fa;">
      <tr>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Sede</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Vendedor</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Orden</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">F. Orden</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Monto</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Cliente</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">F. Regla</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">F. Compromiso</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Autorizado</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Glosa</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Div</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Pago Tot.</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">M. Tot. Pagado</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Pagos Per.</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">M. Pag. Per.</th>
        <th style="color: #4285f4; font-weight: bold; border-bottom: 2px solid #4285f4;">Cant. Pag. Per.</th>
      </tr>
    </thead>
    <tbody>
      @foreach($lista_reglas as $item)
        <tr style="transition: background 0.2s;">
          <td style="font-size: 0.9em;">{{$item->Sede}}</td>
          <td style="font-size: 0.9em;">{{$item->Vendedor}}</td>
          <td>{{$item->Orden}}</td>
          <td>{{date_format(date_create($item->Fecha_Orden), 'd-m-Y')}}</td>
          <td class="text-right" style="font-weight: 500;">{{number_format($item->Monto, 2, '.', ',')}}</td>
          <td style="font-size: 0.85em;">{{$item->Cliente}}</td>
          <td>{{$item->Fecha_Regla ? date_format(date_create($item->Fecha_Regla), 'd-m-Y') : ''}}</td>
          <td>{{$item->Fecha_Compromiso ? date_format(date_create($item->Fecha_Compromiso), 'd-m-Y') : ''}}</td>
          <td>{{$item->Autorizado}}</td>
          <td style="font-size: 0.85em; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{$item->Glosa}}">{{$item->Glosa}}</td>
          <td class="text-center">{{$item->Div}}</td>
          <td class="text-center">
            @if($item->Pago_Totalidad == 'SI')
              <span class="label label-success" style="padding: 4px 8px; border-radius: 12px; font-weight: 600;">SI</span>
            @else
              <span class="label label-danger" style="padding: 4px 8px; border-radius: 12px; font-weight: 600;">NO</span>
            @endif
          </td>
          <td class="text-right" style="font-weight: 600; color: #2e7d32;">{{number_format($item->Monto_Total_Pagado, 2, '.', ',')}}</td>
          <td class="text-center">
            @if($item->Hay_Pagos_En_Periodo == 'SI')
              <span class="label label-primary" style="padding: 4px 8px; border-radius: 12px; font-weight: 600;">SI</span>
            @else
              <span class="label label-default" style="padding: 4px 8px; border-radius: 12px; font-weight: 600;">NO</span>
            @endif
          </td>
          <td class="text-right">{{number_format($item->Monto_Pagado_En_Periodo, 2, '.', ',')}}</td>
          <td class="text-center">{{$item->Cantidad_Pagos_En_Periodo}}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
