<style>
  .table-premium {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    background: white;
  }
  .table-premium thead th {
    background-color: #f1f4f9;
    color: #444;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: 0.5px;
    padding: 12px 10px;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
  }
  .table-premium tbody td {
    padding: 10px;
    border-bottom: 1px solid #edf2f7;
    font-size: 12px;
    color: #4a5568;
    vertical-align: middle;
  }
  .table-premium tbody tr:hover {
    background-color: #f7fafc;
  }
  .badge-status {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 10px;
    font-weight: 700;
    display: inline-block;
    text-align: center;
    min-width: 45px;
  }
  .badge-si { background-color: #c6f6d5; color: #22543d; }
  .badge-no { background-color: #fed7d7; color: #822727; }
  .amount-positive { color: #2f855a; font-weight: 700; }
  .text-muted-xs { font-size: 10px; color: #718096; }
</style>

</style>

<div class="tab-container" style="margin-top: 20px;">
  <ul class="nav nav-tabs nav-tabs-primary" style="margin-bottom: 0; background: #f8fafc; border: 1px solid #e2e8f0; border-bottom: none; border-radius: 8px 8px 0 0;">
    <li class="active">
      <a href="#tab-consolidado" data-toggle="tab" style="font-weight: 700;">
        <i class="mdi mdi-layers" style="margin-right: 5px;"></i> CONSOLIDADO ({{ count($lista_consolidado) }})
      </a>
    </li>
    <li>
      <a href="#tab-detallado" data-toggle="tab" style="font-weight: 700;">
        <i class="mdi mdi-view-list" style="margin-right: 5px;"></i> DETALLADO ({{ count($lista_reglas) }})
      </a>
    </li>
  </ul>
  <div class="tab-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 0 0 8px 8px; padding: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
    
    <!-- TAB CONSOLIDADO -->
    <div id="tab-consolidado" class="tab-pane active animated fadeIn">
      <div class="table-responsive">
        <table id="tablereportecp_consolidado" class="table-premium">
          <thead>
            <tr>
              <th>Sede / Vendedor</th>
              <th>Orden / Fecha</th>
              <th>Monto</th>
              <th>Cliente / Glosa / Autorizado</th>
              <th>FEC. ORDEN / FEC. PAGO / DIAS</th>
              <th>F. Regla / F. Compr.</th>
              <th class="text-right">Pagos en Periodo</th>
              <th class="text-right">Pagado Total / Pago Tot.</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($lista_consolidado as $item)
              @php
                $dias = '-';
                if($item->Fecha_Orden && $item->FEC_PAGO){
                  $f_orden = date_create($item->Fecha_Orden);
                  $f_pago = date_create($item->FEC_PAGO);
                  $diff = date_diff($f_orden, $f_pago);
                  $dias = $diff->days;
                }
                $dias_regla = isset($item->diasregla) ? $item->diasregla : '-'; 
                $dias_compromiso = '-';
                if($item->Fecha_Regla && $item->Fecha_Compromiso){
                  $f_regla = date_create($item->Fecha_Regla);
                  $f_compromiso = date_create($item->Fecha_Compromiso);
                  $diff_c = date_diff($f_regla, $f_compromiso);
                  $dias_compromiso = $diff_c->days;
                }
              @endphp
              <tr>
                <td>
                  <div style="font-weight: 700; color: #2d3748;">{{$item->Sede}}</div>
                  <div class="text-muted-xs">{{$item->Vendedor}}</div>
                </td>
                <td>
                  <div style="font-weight: 600;">{{$item->Orden}}</div>
                  <div class="text-muted-xs">{{$item->Fecha_Orden ? date_format(date_create($item->Fecha_Orden), 'd-m-Y') : '-'}}</div>
                </td>
                <td class="text-right">
                  <span class="amount-positive">{{number_format($item->Monto, 2, '.', ',')}}</span>
                  <div class="text-muted-xs">{{$item->Div}}</div>
                </td>
                <td style="max-width: 280px;">
                  <div style="font-weight: 700; font-size: 11px; white-space: normal; color: #2d3748;">{{$item->Cliente}}</div>
                  <div class="text-muted-xs" style="white-space: normal; line-height: 1.2; margin-top: 4px; color: #718096;" title="{{$item->Glosa}}">
                    <strong>Glosa:</strong> {{ $item->Glosa }}
                  </div>
                  <div class="text-muted-xs" style="white-space: normal; line-height: 1.2; margin-top: 4px; color: #2d3748; font-weight: 600;">
                    <strong>Autorizado:</strong> {{ $item->Autorizado }}
                  </div>
                </td>
                <td>
                  <div style="color: #4a5568;">O: {{$item->Fecha_Orden ? date_format(date_create($item->Fecha_Orden), 'd-m-Y') : '-'}}</div>
                  <div style="color: #2b6cb0;">P: {{$item->FEC_PAGO ? date_format(date_create($item->FEC_PAGO), 'd-m-Y') : '-'}}</div>
                  <div style="margin-top: 6px; padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                    <div style="font-size: 9px; color: #1a202c; line-height: 1.4;">
                      <strong>Días Diff:</strong> {{ $dias }}
                    </div>
                  </div>
                </td>
                <td>
                  <div style="color: #4a5568;">R: {{$item->Fecha_Regla ? date_format(date_create($item->Fecha_Regla), 'd-m-Y') : '-'}}</div>
                  <div style="color: #4299e1; font-weight: 600;">C: {{$item->Fecha_Compromiso ? date_format(date_create($item->Fecha_Compromiso), 'd-m-Y') : '-'}}</div>
                  <div style="margin-top: 6px; padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                    <div style="font-size: 9px; color: #718096; line-height: 1.4;">
                      <strong>Días R:</strong> {{ $dias_regla }}
                    </div>
                    <div style="font-size: 9px; color: #2b6cb0; line-height: 1.4;">
                      <strong>Días C:</strong> {{ $dias_compromiso }}
                    </div>
                  </div>
                </td>
                <td class="text-right">
                  <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex-grow: 1;">
                      <div style="font-weight: 700; color: #2d3748;">
                        {{number_format($item->Pago_Realizado_En_Rango, 2, '.', ',')}}
                      </div>
                      <div style="margin-top: 4px;">
                        @if($item->Se_Pago_Todo_El_Saldo_En_Rango == 'SI')
                          <span class="badge-status badge-si" style="padding: 2px 8px; font-size: 9px;">SI</span>
                          @if(isset($item->Ultima_Fecha_Rango) && $item->Ultima_Fecha_Rango)
                            <div class="text-muted-xs" style="margin-top: 3px; font-weight: 600; color: #2c5282;">
                              F. Fin: {{ date_format(date_create($item->Ultima_Fecha_Rango), 'd-m-Y') }}
                            </div>
                          @endif
                        @else
                          <span class="badge-status badge-no" style="padding: 2px 8px; font-size: 9px;">NO</span>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td class="text-right">
                  <div style="font-weight: 700; color: #2c5282;">
                    {{number_format($item->Monto_Total_Pagado, 2, '.', ',')}}
                  </div>
                  <div style="margin-top: 4px;">
                    @if($item->Pago_Totalidad == 'SI')
                      <span class="badge-status badge-si" style="padding: 2px 8px; font-size: 9px;" title="Pago Totalidad">SI (Totalidad)</span>
                    @else
                      <span class="badge-status badge-no" style="padding: 2px 8px; font-size: 9px;" title="Pago Totalidad">NO (Totalidad)</span>
                    @endif
                  </div>
                </td>
                <td class="text-right">
                    <div>
                        <button type="button" 
                          class="btn btn-xs btn-primary btn-detalle-pagos" 
                          title="Ver Detalle de Pagos"
                          data_div="{{ $item->Div }}"
                          data-div="{{ $item->Div }}"
                          data-fecha_regla="{{ $item->Fecha_Regla ? Carbon\Carbon::parse($item->Fecha_Regla)->format('Y-m-d') : '' }}"
                          data-fecha_compromiso="{{ $item->Fecha_Compromiso ? Carbon\Carbon::parse($item->Fecha_Compromiso)->format('Y-m-d') : '' }}">
                          <i class="icon mdi mdi-eye" style="pointer-events: none;"></i>
                        </button>
                    </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB DETALLADO -->
    <div id="tab-detallado" class="tab-pane animated fadeIn">
      <div class="table-responsive">
        <table id="tablereportecp" class="table-premium">
          <thead>
            <tr>
              <th>Sede / Vendedor</th>
              <th>Orden / Fecha</th>
              <th>Monto</th>
              <th>Cliente / Glosa / Autorizado</th>
              <th>FEC. ORDEN / FEC. PAGO / DIAS</th>
              <th>F. Regla / F. Compr.</th>
              <th class="text-right">Pagos en Periodo</th>
              <th class="text-right">Pagado Total / Pago Tot.</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($lista_reglas as $item)
              @php
                $dias = '-';
                if($item->Fecha_Orden && $item->FEC_PAGO){
                  $f_orden = date_create($item->Fecha_Orden);
                  $f_pago = date_create($item->FEC_PAGO);
                  $diff = date_diff($f_orden, $f_pago);
                  $dias = $diff->days;
                }

                $dias_regla = isset($item->diasregla) ? $item->diasregla : '-'; 
                $dias_compromiso = '-';
                if($item->Fecha_Regla && $item->Fecha_Compromiso){
                  $f_regla = date_create($item->Fecha_Regla);
                  $f_compromiso = date_create($item->Fecha_Compromiso);
                  $diff_c = date_diff($f_regla, $f_compromiso);
                  $dias_compromiso = $diff_c->days;
                }
              @endphp
              <tr>
                <td>
                  <div style="font-weight: 700; color: #2d3748;">{{$item->Sede}}</div>
                  <div class="text-muted-xs">{{$item->Vendedor}}</div>
                </td>
                <td>
                  <div style="font-weight: 600;">{{$item->Orden}}</div>
                  <div class="text-muted-xs">{{$item->Fecha_Orden ? date_format(date_create($item->Fecha_Orden), 'd-m-Y') : '-'}}</div>
                </td>
                <td class="text-right">
                  <span class="amount-positive">{{number_format($item->Monto, 2, '.', ',')}}</span>
                  <div class="text-muted-xs">{{$item->Div}}</div>
                </td>
                <td style="max-width: 280px;">
                  <div style="font-weight: 700; font-size: 11px; white-space: normal; color: #2d3748;">{{$item->Cliente}}</div>
                  <div class="text-muted-xs" style="white-space: normal; line-height: 1.2; margin-top: 4px; color: #718096;" title="{{$item->Glosa}}">
                    <strong>Glosa:</strong> {{ $item->Glosa }}
                  </div>
                  <div class="text-muted-xs" style="white-space: normal; line-height: 1.2; margin-top: 4px; color: #2d3748; font-weight: 600;">
                    <strong>Autorizado:</strong> {{ $item->Autorizado }}
                  </div>
                </td>
                <td>
                  <div style="color: #4a5568;">O: {{$item->Fecha_Orden ? date_format(date_create($item->Fecha_Orden), 'd-m-Y') : '-'}}</div>
                  <div style="color: #2b6cb0;">P: {{$item->FEC_PAGO ? date_format(date_create($item->FEC_PAGO), 'd-m-Y') : '-'}}</div>
                  <div style="margin-top: 6px; padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                    <div style="font-size: 9px; color: #1a202c; line-height: 1.4;">
                      <strong>Días Diff:</strong> {{ $dias }}
                    </div>
                  </div>
                </td>
                <td>
                  <div style="color: #4a5568;">R: {{$item->Fecha_Regla ? date_format(date_create($item->Fecha_Regla), 'd-m-Y') : '-'}}</div>
                  <div style="color: #4299e1; font-weight: 600;">C: {{$item->Fecha_Compromiso ? date_format(date_create($item->Fecha_Compromiso), 'd-m-Y') : '-'}}</div>
                  <div style="margin-top: 6px; padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                    <div style="font-size: 9px; color: #718096; line-height: 1.4;">
                      <strong>Días R:</strong> {{ $dias_regla }}
                    </div>
                    <div style="font-size: 9px; color: #2b6cb0; line-height: 1.4;">
                      <strong>Días C:</strong> {{ $dias_compromiso }}
                    </div>
                  </div>
                </td>
                <td class="text-right">
                  <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex-grow: 1;">
                      <div style="font-weight: 700; color: #2d3748;">
                        {{number_format($item->Pago_Realizado_En_Rango, 2, '.', ',')}}
                      </div>
                      <div style="margin-top: 4px;">
    
                        @if($item->Se_Pago_Todo_El_Saldo_En_Rango == 'SI')
                          <span class="badge-status badge-si" style="padding: 2px 8px; font-size: 9px;">SI</span>
                          @if(isset($item->Ultima_Fecha_Rango) && $item->Ultima_Fecha_Rango)
                            <div class="text-muted-xs" style="margin-top: 3px; font-weight: 600; color: #2c5282;">
                              F. Fin: {{ date_format(date_create($item->Ultima_Fecha_Rango), 'd-m-Y') }}
                            </div>
                          @endif
                        @else
                          <span class="badge-status badge-no" style="padding: 2px 8px; font-size: 9px;">NO</span>
                        @endif
                      </div>
                    </div>
    
                  </div>
                </td>
                <td class="text-right">
                  <div style="font-weight: 700; color: #2c5282;">
                    {{number_format($item->Monto_Total_Pagado, 2, '.', ',')}}
                  </div>
                  <div style="margin-top: 4px;">
                    @if($item->Pago_Totalidad == 'SI')
                      <span class="badge-status badge-si" style="padding: 2px 8px; font-size: 9px;" title="Pago Totalidad">SI (Totalidad)</span>
                    @else
                      <span class="badge-status badge-no" style="padding: 2px 8px; font-size: 9px;" title="Pago Totalidad">NO (Totalidad)</span>
                    @endif
                  </div>
                </td>
                <td class="text-right">
                    <div>
                        <button type="button" 
                          class="btn btn-xs btn-primary btn-detalle-pagos" 
                          title="Ver Detalle de Pagos"
                          data_div="{{ $item->Div }}"
                          data-div="{{ $item->Div }}"
                          data-fecha_regla="{{ $item->Fecha_Regla ? Carbon\Carbon::parse($item->Fecha_Regla)->format('Y-m-d') : '' }}"
                          data-fecha_compromiso="{{ $item->Fecha_Compromiso ? Carbon\Carbon::parse($item->Fecha_Compromiso)->format('Y-m-d') : '' }}">
                          <i class="icon mdi mdi-eye" style="pointer-events: none;"></i>
                        </button>
                    </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
