<div class="row">
  <div class="col-xs-12">
    <h4 style="margin-top:0;"><b>Cliente:</b> {{ $cliente }}</h4>
  </div>
  
  <div class="col-xs-12" style="margin-top: 10px;">
    <h5><b>Resumen de Saldos por Tramo</b></h5>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-condensed" style="font-size: 11px;">
        <thead>
          <tr class="info">
            <th>Cuenta</th>
            <th>Representante</th>
            <th class="text-right">MT</th>
            <th class="text-right">0-30</th>
            <th class="text-right">31-90</th>
            <th class="text-right">91-180</th>
            <th class="text-right">> 180</th>
            <th class="text-right">Total Saldo</th>
          </tr>
        </thead>
        <tbody>
          @php
            $tot_mt = 0; $tot_030 = 0; $tot_3190 = 0; $tot_9118 = 0; $tot_181 = 0; $tot_saldo = 0;
          @endphp
          @forelse($saldos as $sc)
            @php
              $tot_mt += (float)$sc->T_MT;
              $tot_030 += (float)$sc->T_030;
              $tot_3190 += (float)$sc->T_3190;
              $tot_9118 += (float)$sc->T_9118;
              $tot_181 += (float)$sc->T_181;
              $tot_saldo += (float)$sc->SALCON;
            @endphp
            <tr>
              <td><b>{{ $sc->COD_CONTRATO }}</b></td>
              <td>{{ $sc->NOM_REP_VEN }}</td>
              <td class="text-right">{{ number_format((float)$sc->T_MT, 2, '.', ',') }}</td>
              <td class="text-right">{{ number_format((float)$sc->T_030, 2, '.', ',') }}</td>
              <td class="text-right">{{ number_format((float)$sc->T_3190, 2, '.', ',') }}</td>
              <td class="text-right">{{ number_format((float)$sc->T_9118, 2, '.', ',') }}</td>
              <td class="text-right">{{ number_format((float)$sc->T_181, 2, '.', ',') }}</td>
              <td class="text-right"><b>{{ number_format((float)$sc->SALCON, 2, '.', ',') }}</b></td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center">No registra saldos pendientes.</td>
            </tr>
          @endforelse
          <tr class="warning" style="font-weight: bold;">
            <td colspan="2" class="text-right">TOTAL GENERAL:</td>
            <td class="text-right">{{ number_format($tot_mt, 2, '.', ',') }}</td>
            <td class="text-right">{{ number_format($tot_030, 2, '.', ',') }}</td>
            <td class="text-right">{{ number_format($tot_3190, 2, '.', ',') }}</td>
            <td class="text-right">{{ number_format($tot_9118, 2, '.', ',') }}</td>
            <td class="text-right">{{ number_format($tot_181, 2, '.', ',') }}</td>
            <td class="text-right">{{ number_format($tot_saldo, 2, '.', ',') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="col-xs-12" style="margin-top: 15px;">
    <div class="panel panel-default" style="border: 1px solid #ddd;">
      <div class="panel-body">
        <form id="form-actualizar-regla">
          <input type="hidden" id="modal_cliente_cod" value="{{ $codcliente }}">
          <input type="hidden" id="modal_reglacredito_id" value="{{ $idregla }}">
          <input type="hidden" id="modal_clasificacion" value="{{ $clasificacion }}">

          <div class="form-group row">
            <label class="col-sm-4 control-label"><b>Clasificación:</b></label>
            <div class="col-sm-8">
              @if($clasificacion == 'A')
                <span class="badge badge-success" style="font-size: 14px; padding: 4px 10px;">A</span>
              @elseif($clasificacion == 'B')
                <span class="badge badge-warning" style="font-size: 14px; padding: 4px 10px;">B</span>
              @else
                <span class="badge badge-danger" style="font-size: 14px; padding: 4px 10px;">{{ $clasificacion ?: 'M' }}</span>
              @endif
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 control-label"><b>Condición de Pago:</b></label>
            <div class="col-sm-8">
              <select id="modal_condicionpago" class="form-control input-sm" @if($clasificacion == 'M') disabled @endif>
                <option value="">Seleccione condición de pago</option>
                @foreach($combocondicion as $item)
                  <option value="{{ $item->COD_CATEGORIA }}" @if($item->COD_CATEGORIA == $condicionpago) selected @endif>
                    {{ $item->NOM_CATEGORIA }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 control-label"><b>Límite de Crédito:</b></label>
            <div class="col-sm-8">
              <input type="number" step="0.01" class="form-control input-sm" id="modal_limitecredito" value="{{ $limitecredito }}" @if($clasificacion == 'M') disabled @endif>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

