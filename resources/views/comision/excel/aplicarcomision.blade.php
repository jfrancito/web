<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

@php
    function safe_float($val) {
        if (is_numeric($val)) return (float)$val;
        if (is_string($val)) {
            $val = str_replace(',', '', $val);
            if (is_numeric($val)) return (float)$val;
        }
        return 0.0;
    }

    // --- LÓGICA DEL CUADRO RESUMEN (PIVOT) ---
    $subfamilias = [];
    $resumen = [];
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

        $peso_val = safe_float($item->PESO_ORDEN_50);
        $comision_val = safe_float($item->MONTO_COMISION);

        $resumen[$key]['data'][$subfam]['peso'] += $peso_val;
        $resumen[$key]['data'][$subfam]['comision'] += $comision_val;
        
        $resumen[$key]['total_peso'] += $peso_val;
        $resumen[$key]['total_comision'] += $comision_val;

        if (!isset($totales_verticales[$subfam])) {
            $totales_verticales[$subfam] = ['peso' => 0, 'comision' => 0];
        }
        $totales_verticales[$subfam]['peso'] += $peso_val;
        $totales_verticales[$subfam]['comision'] += $comision_val;

        $total_general_peso += $peso_val;
        $total_general_comision += $comision_val;
    }
    sort($subfamilias);
@endphp

<!-- CABECERA INFORMATIVA -->
<table>
    <tr>
        <td colspan="3" style="font-weight: bold; font-size: 14px;">REPORTE DE APLICACIÓN DE COMISIONES</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Jefe de Ventas:</td>
        <td colspan="2">{{ $jefe }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Periodo:</td>
        <td colspan="2">{{ $periodo }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Rango de Fechas:</td>
        <td colspan="2" style="mso-number-format:'\@';">{{ $inicio }} al {{ $fin }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Fecha de Exportación:</td>
        <td colspan="2" style="mso-number-format:'\@';">{{ date('d-m-Y H:i:s') }}</td>
    </tr>
</table>

<br>

<!-- RESUMEN -->
<table>
    <thead>
        <tr>
            <th colspan="{{ 5 + (count($subfamilias) * 2) }}" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #2d6a4f; color: #ffffff;">
                RESUMEN DE COMISIONES POR SUBFAMILIA
            </th>
        </tr>
        <tr>
            <th colspan="3" style="background-color: #2d6a4f; color: #ffffff; border: 1px solid #000000; text-align: center; vertical-align: middle;">(COMISION DE LA VENTA)</th>
            @foreach($subfamilias as $sub)
                <th colspan="2" style="background-color: #2d6a4f; color: #ffffff; border: 1px solid #000000; text-align: center;">{{ $sub }}</th>
            @endforeach
            <th colspan="2" style="background-color: #1b4332; color: #ffffff; border: 1px solid #000000; text-align: center;">Total</th>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; border: 1px solid #000000; font-weight: bold;">EMPRESA</th>
            <th style="background-color: #f1f5f9; border: 1px solid #000000; font-weight: bold;">CANAL</th>
            <th style="background-color: #f1f5f9; border: 1px solid #000000; font-weight: bold;">SUBCANAL</th>
            @foreach($subfamilias as $sub)
                <th style="background-color: #f1f5f9; border: 1px solid #000000; font-weight: bold;">Σ Saco 50kg</th>
                <th style="background-color: #f1f5f9; border: 1px solid #000000; font-weight: bold;">Σ Comisión</th>
            @endforeach
            <th style="background-color: #cbd5e1; border: 1px solid #000000; font-weight: bold;">Σ Saco 50kg</th>
            <th style="background-color: #cbd5e1; border: 1px solid #000000; font-weight: bold;">Σ Comisión</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resumen as $row)
            <tr>
                <td style="border: 1px solid #000000;">{{ $row['empresa'] }}</td>
                <td style="border: 1px solid #000000;">{{ $row['canal'] }}</td>
                <td style="border: 1px solid #000000;">{{ $row['subcanal'] }}</td>
                @foreach($subfamilias as $sub)
                    <td style="border: 1px solid #000000;">{{ isset($row['data'][$sub]) ? $row['data'][$sub]['peso'] : 0 }}</td>
                    <td style="border: 1px solid #000000;">{{ isset($row['data'][$sub]) ? $row['data'][$sub]['comision'] : 0 }}</td>
                @endforeach
                <td style="background-color: #f8fafc; border: 1px solid #000000; font-weight: bold;">{{ $row['total_peso'] }}</td>
                <td style="background-color: #f8fafc; border: 1px solid #000000; font-weight: bold;">{{ $row['total_comision'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="background-color: #f8fafc; border: 1px solid #000000; font-weight: bold; text-align: right;">TOTALES</td>
            @foreach($subfamilias as $sub)
                <td style="background-color: #f8fafc; border: 1px solid #000000; font-weight: bold;">{{ $totales_verticales[$sub]['peso'] }}</td>
                <td style="background-color: #f8fafc; border: 1px solid #000000; font-weight: bold;">{{ $totales_verticales[$sub]['comision'] }}</td>
            @endforeach
            <td style="background-color: #cbd5e1; border: 1px solid #000000; font-weight: bold;">{{ $total_general_peso }}</td>
            <td style="background-color: #cbd5e1; border: 1px solid #000000; font-weight: bold;">{{ $total_general_comision }}</td>
        </tr>
    </tfoot>
</table>

<br>
<br>

<!-- DETALLE -->
<table>
    <thead>
        <tr>
            <th colspan="23" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #4f46e5; color: #ffffff;">
                DETALLE DE COMISIONES
            </th>
        </tr>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <th style="border: 1px solid #000000;">FECHA VENTA</th>
            <th style="border: 1px solid #000000;">ORDEN</th>
            <th style="border: 1px solid #000000;">DOCUMENTO</th>
            <th style="border: 1px solid #000000;">CLIENTE</th>
            <th style="border: 1px solid #000000;">PRODUCTO</th>
            <th style="border: 1px solid #000000;">FAMILIA</th>
            <th style="border: 1px solid #000000;">SUBFAMILIA</th>
            <th style="border: 1px solid #000000;">UNIDAD</th>
            <th style="border: 1px solid #000000;">MARCA</th>
            <th style="border: 1px solid #000000;">CANTIDAD</th>
            <th style="border: 1px solid #000000;">P.U.</th>
            <th style="border: 1px solid #000000;">TOTAL P</th>
            <th style="border: 1px solid #000000;">PESO 50kg</th>
            <th style="border: 1px solid #000000;">CANAL</th>
            <th style="border: 1px solid #000000;">SUBCANAL</th>
            <th style="border: 1px solid #000000;">FECHA PAGO</th>
            <th style="border: 1px solid #000000;">PRODUCTO COBRADO</th>
            <th style="border: 1px solid #000000;">DIFF</th>
            <th style="border: 1px solid #000000;">CONDICCION PAGO</th>
            <th style="border: 1px solid #000000;">EVALUACIÓN</th>
            <th style="border: 1px solid #000000;">JEFE VENTA</th>
            <th style="border: 1px solid #000000;">COMISION</th>
            <th style="border: 1px solid #000000;">TOTAL COMISION</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lista_comisiones as $item)
            <tr>
                <td style="border: 1px solid #000000;">{{ $item->FEC_ORDEN }}</td>
                <td style="border: 1px solid #000000;">{{ $item->COD_ORDEN }}</td>
                <td style="border: 1px solid #000000;">{{ $item->COD_DOCUMENTO_CTBLE }}</td>
                <td style="border: 1px solid #000000;">{{ $item->CLIENTE }}</td>
                <td style="border: 1px solid #000000;">{{ $item->PRODUCTO }}</td>
                <td style="border: 1px solid #000000;">{{ $item->CAT_SUP_NOM_CATEGORIA }}</td>
                <td style="border: 1px solid #000000;">{{ $item->CAT_INF_NOM_CATEGORIA }}</td>
                <td style="border: 1px solid #000000;">{{ $item->CAT_UNI_NOM_CATEGORIA }}</td>
                <td style="border: 1px solid #000000;">{{ $item->MARCA_NOM_CATEGORIA }}</td>
                <td style="border: 1px solid #000000;">{{ safe_float($item->CAN_PRODUCTO) }}</td>
                <td style="border: 1px solid #000000;">{{ safe_float($item->CAN_PRECIO_UNIT) }}</td>
                <td style="border: 1px solid #000000;">{{ safe_float($item->TOTAL_P) }}</td>
                <td style="border: 1px solid #000000;">{{ safe_float($item->PESO_ORDEN_50) }}</td>
                <td style="border: 1px solid #000000;">{{ $item->TXT_CATEGORIA_CANAL_VENTA }}</td>
                <td style="border: 1px solid #000000;">{{ $item->TXT_CATEGORIA_SUB_CANAL }}</td>
                <td style="border: 1px solid #000000;">{{ $item->FEC_HABILITACION }}</td>
                <td style="border: 1px solid #000000;">{{ safe_float($item->TOTAL_COBRO) }}</td>
                <td style="border: 1px solid #000000;">{{ $item->DIFF }}</td>
                <td style="border: 1px solid #000000;">{{ $item->PLAZO_PAGO }}</td>
                <td style="border: 1px solid #000000; {{ $item->VAL == 'CANCELADO' ? 'color: #2d6a4f;' : 'color: #856404;' }}">{{ $item->VAL }}</td>
                <td style="border: 1px solid #000000;">{{ $item->TXT_CATEGORIA_JEFE_VENTA }}</td>
                <td style="border: 1px solid #000000;">{{ safe_float($item->TASA_COMISION) }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; color: #4f46e5;">{{ safe_float($item->MONTO_COMISION) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</html>
