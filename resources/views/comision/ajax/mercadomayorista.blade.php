@if($es_jefe_flag)
    <div class="table-responsive">
        <table class="table table-hover table-dynamic table-bordered">
            <thead>
                <tr>
                    <th>VENDEDOR</th>
                    <th>SUBFAMILIA</th>
                    <th style="text-align: right;">PESO 50KG</th>
                    <th style="text-align: right;">COMISIÓN</th>
                    <th style="text-align: right;">TOTAL COMISIÓN</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $t_peso = 0; 
                    $t_comision = 0; 
                @endphp
                @foreach($data as $item)
                    @php
                        $t_peso += (float)$item->PESO_ORDEN_50;
                        $t_comision += (float)$item->TOTAL_COMISION;
                    @endphp
                    <tr>
                        <td>{{ $item->TXT_CATEGORIA_JEFE_VENTA_ASIMILADO }}</td>
                        <td>{{ $item->CAT_INF_NOM_CATEGORIA }}</td>
                        <td style="text-align: right;">{{ number_format((float)$item->PESO_ORDEN_50, 2) }}</td>
                        <td style="text-align: right;">{{ number_format((float)$item->TASA_COMISION, 4) }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format((float)$item->TOTAL_COMISION, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc;">
                    <th colspan="2" style="text-align: right; font-weight: bold;">TOTAL:</th>
                    <th style="text-align: right; font-weight: bold;">{{ number_format($t_peso, 2) }}</th>
                    <th></th>
                    <th style="text-align: right; font-weight: bold;">{{ number_format($t_comision, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 15px;"><i class="fa fa-pie-chart" style="color: var(--primary);"></i> Resumen de Comisiones</h3>
    <div class="table-responsive" style="margin-bottom: 30px;">
        <table class="table table-bordered table-hover" style="white-space: nowrap;">
            <thead>
                <tr>
                    <th rowspan="2" style="vertical-align: middle; background: #2d6a4f; color: white;">EMPRESA</th>
                    <th rowspan="2" style="vertical-align: middle; background: #2d6a4f; color: white;">CANAL</th>
                    <th rowspan="2" style="vertical-align: middle; background: #2d6a4f; color: white;">SUBCANAL</th>
                    @foreach($subfamilias as $s)
                        <th colspan="2" style="text-align: center; background: #2d6a4f; color: white;">{{ $s }}</th>
                    @endforeach
                    <th colspan="2" style="text-align: center; background: #2d6a4f; color: white;">Total</th>
                </tr>
                <tr>
                    @foreach($subfamilias as $s)
                        <th style="text-align: right; background: #52b788; color: white;">Σ Saco 50kg</th>
                        <th style="text-align: right; background: #52b788; color: white;">Σ Comisión</th>
                    @endforeach
                    <th style="text-align: right; background: #52b788; color: white;">Σ Saco 50kg</th>
                    <th style="text-align: right; background: #52b788; color: white;">Σ Comisión</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumen as $r)
                    <tr>
                        <td>{{ $r['empresa'] }}</td>
                        <td>{{ $r['canal'] }}</td>
                        <td>{{ $r['subcanal'] }}</td>
                        @foreach($subfamilias as $s)
                            <td style="text-align: right;">{{ isset($r['data'][$s]) ? number_format($r['data'][$s]['peso'], 2) : '0.00' }}</td>
                            <td style="text-align: right;">{{ isset($r['data'][$s]) ? number_format($r['data'][$s]['com'], 2) : '0.00' }}</td>
                        @endforeach
                        <td style="text-align: right; font-weight: bold; background: #f1f5f9;">{{ number_format($r['tp'], 2) }}</td>
                        <td style="text-align: right; font-weight: bold; background: #f1f5f9;">{{ number_format($r['tc'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #cbd5e1; font-weight: bold;">
                    <th colspan="3" style="text-align: right;">TOTALES:</th>
                    @foreach($subfamilias as $s)
                        <th style="text-align: right;">{{ number_format($totales_v[$s]['peso'], 2) }}</th>
                        <th style="text-align: right;">{{ number_format($totales_v[$s]['com'], 2) }}</th>
                    @endforeach
                    <th style="text-align: right;">{{ number_format($t_peso, 2) }}</th>
                    <th style="text-align: right;">{{ number_format($t_com, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 15px;"><i class="fa fa-list-alt" style="color: var(--primary);"></i> Detalle de Comisiones</h3>
    <div class="table-responsive">
        <table class="table table-hover table-dynamic table-bordered" style="white-space: nowrap;">
            <thead>
                <tr style="background: #4f46e5; color: white;">
                    <th>FECHA VENTA</th>
                    <th>ORDEN</th>
                    <th>DOCUMENTO</th>
                    <th>CLIENTE</th>
                    <th>PRODUCTO</th>
                    <th>FAMILIA</th>
                    <th>SUBFAMILIA</th>
                    <th>UNIDAD</th>
                    <th>MARCA</th>
                    <th style="text-align: right;">CANTIDAD</th>
                    <th style="text-align: right;">P.U.</th>
                    <th style="text-align: right;">TOTAL P</th>
                    <th style="text-align: right;">PESO 50kg</th>
                    <th>CANAL</th>
                    <th>SUBCANAL</th>
                    <th>FECHA PAGO</th>
                    <th style="text-align: right;">PRODUCTO COBRADO</th>
                    <th style="text-align: right;">DIFF</th>
                    <th>CONDICCION PAGO</th>
                    <th>EVALUACIÓN</th>
                    <th>JEFE VENTA</th>
                    <th style="text-align: right;">COMISION</th>
                    <th style="text-align: right;">TOTAL COMISION</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                    <tr @if(trim($item->TIPO_COMPROBANTE) == 'NOTA_CREDITO') style="background-color: #FFC7CE; color: #9C0006;" @endif>
                        <td>{{ !empty($item->FEC_ORDEN) ? date('d-m-Y', strtotime($item->FEC_ORDEN)) : '' }}</td>
                        <td>{{ $item->COD_ORDEN }}</td>
                        <td>{{ $item->COD_DOCUMENTO_CTBLE }}</td>
                        <td>{{ $item->CLIENTE }}</td>
                        <td>{{ $item->PRODUCTO }}</td>
                        <td>{{ $item->CAT_SUP_NOM_CATEGORIA }}</td>
                        <td>{{ $item->CAT_INF_NOM_CATEGORIA }}</td>
                        <td>{{ $item->CAT_UNI_NOM_CATEGORIA }}</td>
                        <td>{{ $item->MARCA_NOM_CATEGORIA }}</td>
                        <td style="text-align: right;">{{ is_numeric($item->CAN_PRODUCTO) ? number_format((float)$item->CAN_PRODUCTO, 2) : '0.00' }}</td>
                        <td style="text-align: right;">{{ is_numeric($item->CAN_PRECIO_UNIT) ? number_format((float)$item->CAN_PRECIO_UNIT, 2) : '0.00' }}</td>
                        <td style="text-align: right;">{{ is_numeric($item->TOTAL_P) ? number_format((float)$item->TOTAL_P, 2) : '0.00' }}</td>
                        <td style="text-align: right;">{{ is_numeric($item->PESO_ORDEN_50) ? number_format((float)$item->PESO_ORDEN_50, 2) : '0.00' }}</td>
                        <td>{{ $item->TXT_CATEGORIA_CANAL_VENTA }}</td>
                        <td>{{ $item->TXT_CATEGORIA_SUB_CANAL }}</td>
                        <td>{{ !empty($item->FEC_HABILITACION) ? date('d-m-Y', strtotime($item->FEC_HABILITACION)) : '' }}</td>
                        <td style="text-align: right;">{{ is_numeric($item->TOTAL_COBRO) ? number_format((float)$item->TOTAL_COBRO, 2) : '0.00' }}</td>
                        <td style="text-align: right;">{{ $item->DIFF }}</td>
                        <td>{{ $item->PLAZO_PAGO }}</td>
                        <td>{{ $item->VAL }}</td>
                        <td>{{ $item->TXT_CATEGORIA_JEFE_VENTA }}</td>
                        <td style="text-align: right;">{{ is_numeric($item->TASA_COMISION) ? number_format((float)$item->TASA_COMISION, 2) : '0.00' }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ is_numeric($item->TOTAL_COMISION) ? number_format((float)$item->TOTAL_COMISION, 2) : '0.00' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
