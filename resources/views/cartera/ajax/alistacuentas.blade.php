<div class="panel panel-default panel-table">
  <div class="panel-heading" style="display:flex; justify-content:space-between; align-items:center;">
    <span><b>Cartera Cuentas</b> (Total: {{ $cuentas->total() }})</span>
    <div>
      {!! $cuentas->appends(['buscar' => $buscar, 'criterio' => $criterio_raw, 'filtrojv' => $jv])->render() !!}
    </div>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover table-bordered">
        <thead>
          <tr class="success">
            <th style="width: 25%;">Cuenta / Cliente</th>
            <th>Representante</th>
            <th>Canal</th>
            <th>Subcanal</th>
            <th class="text-center">Condición Pago</th>
            <th class="text-right">Límite Crédito</th>
            <th class="text-center">Clasificación</th>
            <th class="text-center" style="width: 80px;">Acción</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cuentas as $cuenta)
            <tr>
              <td class="cell-detail">
                <b>{{ $cuenta->COD_CONTRATO }}</b>
                <span class="cell-detail-description" style="color:#555;">{{ $cuenta->CLIENTE }}</span>
              </td>
              <td>{{ $cuenta->RV }}</td>
              <td>{{ $cuenta->CANAL }}</td>
              <td>{{ $cuenta->SUBCANAL }}</td>
              <td class="text-center">
                <span class="label label-primary">{{ $cuenta->CP }}</span>
              </td>
              <td class="text-right">
                <b>S/ {{ number_format((float)$cuenta->CAN_LIMITE, 2, '.', ',') }}</b>
              </td>
              <td class="text-center">
                @if($cuenta->clasificacion == 'A')
                  <span class="badge badge-success">A</span>
                @elseif($cuenta->clasificacion == 'B')
                  <span class="badge badge-warning">B</span>
                @else
                  <span class="badge badge-danger">{{ $cuenta->clasificacion ?: '-' }}</span>
                @endif
              </td>
              <td class="text-center">
                <div class="btn-group btn-hspace">
                  <button type="button" data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" aria-expanded="false">
                    <span class="icon-dropdown mdi mdi-settings"></span> <span class="caret"></span>
                  </button>
                  <ul role="menu" class="dropdown-menu pull-right">
                    <li>
                      <a href="#" class="btn-editar-regla"
                         data-id="{{ $cuenta->id }}"
                         data-codcliente="{{ $cuenta->CODCLIENTE }}"
                         data-cliente="{{ $cuenta->CLIENTE }}"
                         data-limite="{{ (float)$cuenta->CAN_LIMITE }}"
                         data-condicion="{{ $cuenta->condicionpago_id }}"
                         data-clasificacion="{{ $cuenta->clasificacion }}">
                        <i class="icon mdi mdi-edit"></i> Cambiar límite de crédito
                      </a>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center" style="padding: 20px;">
                <b>No se encontraron cuentas con los criterios especificados.</b>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="text-right" style="padding: 10px 15px;">
      {!! $cuentas->appends(['buscar' => $buscar, 'criterio' => $criterio_raw, 'filtrojv' => $jv])->render() !!}
    </div>
  </div>
</div>

