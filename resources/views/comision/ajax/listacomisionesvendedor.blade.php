@php
    // --- LÓGICA DEL CUADRO RESUMEN (PIVOT) ---
    $subfamilias = [];
    $resumen = [];
    $totales_horizontales = [];
    $totales_verticales = [];
    $total_general_peso = 0;
    $total_general_comision = 0;

    foreach($lista_comisiones as $item) {
        $subfam = $item->CAT_INF_NOM_CATEGORIA;
        if (!in_array($subfam, $subfamilias)) {
            $subfamilias[] = $subfam;
        }

        $key = $item->NOM_EMPR . '|' . $item->TXT_CATEGORIA_CANAL_VENTA . '|' . $item->TXT_CATEGORIA_SUB_CANAL;
        
        if (!isset($resumen[$key])) {
            $resumen[$key] = [
                'empresa' => $item->NOM_EMPR,
                'canal' => $item->TXT_CATEGORIA_CANAL_VENTA,
                'subcanal' => $item->TXT_CATEGORIA_SUB_CANAL,
                'data' => [],
                'total_peso' => 0,
                'total_comision' => 0
            ];
        }

        if (!isset($resumen[$key]['data'][$subfam])) {
            $resumen[$key]['data'][$subfam] = ['peso' => 0, 'comision' => 0];
        }

        $resumen[$key]['data'][$subfam]['peso'] += (float)$item->PESO_ORDEN_50;
        $resumen[$key]['data'][$subfam]['comision'] += (float)$item->MONTO_COMISION;
        
        $resumen[$key]['total_peso'] += (float)$item->PESO_ORDEN_50;
        $resumen[$key]['total_comision'] += (float)$item->MONTO_COMISION;

        // Totales Verticales por Subfamilia
        if (!isset($totales_verticales[$subfam])) {
            $totales_verticales[$subfam] = ['peso' => 0, 'comision' => 0];
        }
        $totales_verticales[$subfam]['peso'] += (float)$item->PESO_ORDEN_50;
        $totales_verticales[$subfam]['comision'] += (float)$item->MONTO_COMISION;

        $total_general_peso += (float)$item->PESO_ORDEN_50;
        $total_general_comision += (float)$item->MONTO_COMISION;
    }
    sort($subfamilias);
@endphp

{{-- PANEL INFORMATIVO: Planilla de Comisiones --}}
@if(!empty($info_planilla) && count($info_planilla) > 0)
<div style="margin-bottom: 24px; border-radius: 10px; overflow: hidden; border: 1px solid #d1fae5; box-shadow: 0 2px 8px rgba(16,185,129,0.1);">
    <div style="background: linear-gradient(135deg, #065f46, #10b981); padding: 12px 20px; display: flex; align-items: center; gap: 10px;">
        <i class="mdi mdi-check-circle" style="color: #fff; font-size: 20px;"></i>
        <span style="color: #fff; font-weight: 700; font-size: 14px;">Información de Planilla de Comisiones</span>
        <span style="background: rgba(255,255,255,0.2); color: #fff; border-radius: 20px; padding: 2px 10px; font-size: 12px; font-weight: 600;">{{ count($info_planilla) }} registro(s)</span>
    </div>
    <div style="background: #f0fdf4; padding: 16px 20px; overflow-x: auto;">
        <table class="table table-bordered" style="font-size: 12px; margin-bottom: 0; background: white;">
            <thead>
                <tr style="background-color: #d1fae5; color: #065f46; font-weight: 700;">
                    <th>CÓDIGO</th>
                    <th>JEFE VENTA</th>
                    <th>ESTADO</th>
                    <th>PROVIENE</th>
                    <th>USUARIO AUTORIZA</th>
                    <th>USUARIO EJECUTA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($info_planilla as $planilla)
                <tr>
                    <td>{{ $planilla->TXT_CODIGO }}</td>
                    <td>{{ $planilla->TXT_CATEGORIA_JEFE_VENTA }}</td>
                    <td>
                        @php $estado = strtoupper($planilla->TXT_ESTADO ?? ''); @endphp
                        @if(in_array($estado, ['APLICADO', 'APROBADO', 'COMPLETADO']))
                            <span class="label label-success">{{ $planilla->TXT_ESTADO }}</span>
                        @elseif(in_array($estado, ['PENDIENTE', 'EN PROCESO']))
                            <span class="label label-warning">{{ $planilla->TXT_ESTADO }}</span>
                        @else
                            <span class="label label-default">{{ $planilla->TXT_ESTADO }}</span>
                        @endif
                    </td>
                    <td>{{ $planilla->TXT_PROVIENE }}</td>
                    <td>{{ $planilla->TXT_USUARIO_AUTORIZA }}</td>
                    <td>{{ $planilla->TXT_USUARIO_EJECUTA }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@php
    $mostrar_boton_guardar = true;
    if(!empty($info_planilla) && count($info_planilla) > 0) {
        if($info_planilla[0]->COD_ESTADO == 'EPP0000000000004') {
            $mostrar_boton_guardar = false;
        }
    }
@endphp

@if($mostrar_boton_guardar)
    <div style="margin-bottom: 24px; text-align: right;">
        <button type="button" id="btn-aplicar-comisiones" class="btn btn-primary" style="background-color: #4f46e5; border: none; font-weight: 700; padding: 10px 25px; border-radius: 6px; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);">
            <i class="mdi mdi-content-save"></i> Aplicar Comisiones
        </button>
    </div>
@endif

<div class="summary-container" style="margin-bottom: 40px;">
    <h3 style="font-weight: 800; font-size: 16px; color: #111827; margin-bottom: 15px;">
        <i class="mdi mdi-view-quilt" style="color: #10b981;"></i> Resumen de Comisiones por Subfamilia
    </h3>
    <div class="table-responsive">
        <table class="table table-bordered" style="font-size: 11px; background: white;">
            <thead>
                <!-- Cabecera Superior -->
                <tr style="background-color: #2d6a4f; color: white; text-align: center;">
                    <th colspan="3" style="vertical-align: middle;">(COMISION DE LA VENTA)</th>
                    @foreach($subfamilias as $sub)
                        <th colspan="2" style="vertical-align: middle;">{{ $sub }}</th>
                    @endforeach
                    <th colspan="2" style="vertical-align: middle; background-color: #1b4332;">Total</th>
                </tr>
                <!-- Cabecera de Columnas -->
                <tr style="background-color: #f1f5f9; color: #475569;">
                    <th style="width: 200px;">EMPRESA</th>
                    <th style="width: 150px;">CANAL</th>
                    <th style="width: 150px;">SUBCANAL</th>
                    @foreach($subfamilias as $sub)
                        <th style="text-align: right; width: 80px;">Σ Saco 50kg</th>
                        <th style="text-align: right; width: 80px;">Σ Comisión</th>
                    @endforeach
                    <th style="text-align: right; width: 90px; background-color: #e2e8f0; font-weight: bold;">Σ Saco 50kg</th>
                    <th style="text-align: right; width: 90px; background-color: #e2e8f0; font-weight: bold;">Σ Comisión</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumen as $row)
                    <tr>
                        <td style="font-weight: 600;">{{ $row['empresa'] }}</td>
                        <td>{{ $row['canal'] }}</td>
                        <td>{{ $row['subcanal'] }}</td>
                        @foreach($subfamilias as $sub)
                            <td style="text-align: right;">{{ isset($row['data'][$sub]) ? number_format($row['data'][$sub]['peso'], 2) : '0.00' }}</td>
                            <td style="text-align: right;">{{ isset($row['data'][$sub]) ? number_format($row['data'][$sub]['comision'], 2) : '0.00' }}</td>
                        @endforeach
                        <td style="text-align: right; font-weight: 700; background-color: #f8fafc;">{{ number_format($row['total_peso'], 2) }}</td>
                        <td style="text-align: right; font-weight: 700; background-color: #f8fafc;">{{ number_format($row['total_comision'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: 800;">
                    <td colspan="3" style="text-align: right; font-size: 12px;">TOTALES</td>
                    @foreach($subfamilias as $sub)
                        <td style="text-align: right;">{{ number_format($totales_verticales[$sub]['peso'], 2) }}</td>
                        <td style="text-align: right;">{{ number_format($totales_verticales[$sub]['comision'], 2) }}</td>
                    @endforeach
                    <td style="text-align: right; color: #1e293b; background-color: #cbd5e1;">{{ number_format($total_general_peso, 2) }}</td>
                    <td style="text-align: right; color: #1e293b; background-color: #cbd5e1;">{{ number_format($total_general_comision, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<hr style="border-top: 2px solid #e5e7eb; margin: 40px 0;">

<h3 style="font-weight: 800; font-size: 16px; color: #111827; margin-bottom: 15px;">
    <i class="mdi mdi-format-list-bulleted" style="color: #4f46e5;"></i> Detalle de Comisiones
</h3>

<div class="table-responsive">
    <table id="table-comisiones-vendedor" class="table table-striped table-hover table-fw-widget" style="font-size: 11px;">
        <thead>
            <tr style="background-color: #f1f5f9; color: #475569;">
                <th>FECHA VENTA</th>
                <th>ORDEN</th>
                <th>DOCUMENTO</th>
                <th>CLIENTE</th>
                <th>PRODUCTO</th>
                <th>FAMILIA</th>
                <th>SUBFAMILIA</th>
                <th>UNIDAD</th>
                <th>MARCA</th>
                <th>CANTIDAD</th>
                <th>P.U.</th>
                <th>TOTAL P</th>
                <th>PESO 50kg</th>
                <th>CANAL</th>
                <th>SUBCANAL</th>
                <th>FECHA PAGO</th>
                <th>PRODUCTO COBRADO</th>
                <th>DIFF</th>
                <th>CONDICCION PAGO</th>
                <th>EVALUACIÓN</th>
                <th>JEFE VENTA</th>
                <th>COMISION</th>
                <th>TOTAL COMISION</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lista_comisiones as $item)
                <tr>
                    <td>{{ $item->FEC_ORDEN }}</td>
                    <td>{{ $item->COD_ORDEN }}</td>
                    <td>{{ $item->COD_DOCUMENTO_CTBLE }}</td>
                    <td>{{ $item->CLIENTE }}</td>
                    <td>{{ $item->PRODUCTO }}</td>
                    <td>{{ $item->CAT_SUP_NOM_CATEGORIA }}</td>
                    <td>{{ $item->CAT_INF_NOM_CATEGORIA }}</td>
                    <td>{{ $item->CAT_UNI_NOM_CATEGORIA }}</td>
                    <td>{{ $item->MARCA_NOM_CATEGORIA }}</td>
                    <td>{{ number_format($item->CAN_PRODUCTO, 2) }}</td>
                    <td>{{ number_format($item->CAN_PRECIO_UNIT, 2) }}</td>
                    <td>{{ number_format($item->TOTAL_P, 2) }}</td>
                    <td>{{ number_format($item->PESO_ORDEN_50, 2) }}</td>
                    <td>{{ $item->TXT_CATEGORIA_CANAL_VENTA }}</td>
                    <td>{{ $item->TXT_CATEGORIA_SUB_CANAL }}</td>
                    <td>{{ $item->FEC_HABILITACION }}</td>
                    <td>{{ number_format($item->TOTAL_COBRO, 2) }}</td>
                    <td>{{ $item->DIFF }}</td>
                    <td>{{ $item->PLAZO_PAGO }}</td>
                    <td>
                        @if($item->VAL == 'CANCELADO')
                            <span class="label label-success">{{ $item->VAL }}</span>
                        @else
                            <span class="label label-warning">{{ $item->VAL }}</span>
                        @endif
                    </td>
                    <td>{{ $item->TXT_CATEGORIA_JEFE_VENTA }}</td>
                    <td>{{ number_format($item->TASA_COMISION, 2) }}</td>
                    <td style="font-weight: 800; color: #4f46e5;">{{ number_format($item->MONTO_COMISION, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#table-comisiones-vendedor').DataTable({
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            "pageLength": 25,
            "order": []
        });

        $('#btn-aplicar-comisiones').on('click', function(e) {
            e.preventDefault();
            
            var cod_jefe    = $('#cod_jefe').val();
            var cod_periodo = $('#cod_periodo').val();
            var _token      = $('#token').val();

            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                type: 'POST',
                url: '{{ url("/ajax-aplicar-comisiones-vendedor") }}',
                data: {
                    _token: _token,
                    cod_jefe: cod_jefe,
                    cod_periodo: cod_periodo
                },
                success: function(data) {
                    btn.prop('disabled', false).html(originalText);
                    if (data.status == 'success') {
                        alertajax(data.message);
                        // Opcionalmente recargar la búsqueda para actualizar el panel informativo y ocultar el botón
                        $('#buscar-comisiones').trigger('click');
                    } else {
                        alerterrorajax(data.message);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    alerterrorajax("Error en el servidor al intentar aplicar las comisiones.");
                }
            });
        });
    });
</script>
