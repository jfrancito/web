<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait EstadoCuentaTraits
{
		private function obtenerNombreTipo($abreviatura)
		{
		    $mapeoTipos = [
		    	'IS' => 'INDUAMERICA COMERCIAL SAC',
		        'IC' => 'INDUAMERICA CHICLAYO SAC',
		        'IE' => 'INDUAMERICA ALTOMAYO SA',
		        'IA' => 'INDUSTRIA ARROCERA DE AMERICA SAC',
		        'II' => 'INDUAMERICA INTERNACIONAL SAC', 
		        'IT' => 'INDUAMERICA TRADE S.A',
		    ];
		    
		    return $mapeoTipos[$abreviatura] ?? $abreviatura;
		}


public function est_resumen_estado_cuenta($fec_ini, $fec_fin, $cod_empr_cliente, $cod_empresa)
{
    $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.SALDO_CUENTA
                                                        @fec_ini = ?,
                                                        @FEC_FIN = ?,
                                                        @COD_EMPR_CLIENTE = ?,
                                                        @COD_EMPR = ?');
    $stmt->bindParam(1, $fec_ini, PDO::PARAM_STR);
    $stmt->bindParam(2, $fec_fin, PDO::PARAM_STR);
    $stmt->bindParam(3, $cod_empr_cliente, PDO::PARAM_STR);
    $stmt->bindParam(4, $cod_empresa, PDO::PARAM_STR);
    $stmt->execute();

    $resultadosOriginales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $datosManipulados = [];

    $saldoAcumulado = 0;

    foreach ($resultadosOriginales as $index => $fila) {

        $texto = $fila['NOM_PRODUCTO'];
        $primerasDosLetras = substr($texto, 0, 2);
        $nombreempresa = $this->obtenerNombreTipo($primerasDosLetras); 

        $text = $fila['TXT_DOC_ASOC'];
        $div = '';
        $documento = '';

        // Caso 1: DIV: ... //FC: ...
        if (preg_match('/DIV:\s*([\d\-]+).*FC:([A-Z0-9\-]+)/', $text, $matches)) {
            $div = trim($matches[1]);
            $documento = trim($matches[2]);
            if (preg_match('/^([A-Z0-9]+)-\1-(\d+)$/', $documento, $m)) {
                $documento = $m[1] . '-' . $m[2];
            }
        }
        // Caso 2: DOCUMENTO INTERNO VENTAS:
        elseif (preg_match('/DOCUMENTO\s+INTERNO\s+VENTAS:\s*([\d\-]+)/i', $text, $matches)) {
            $div = $matches[1];
            $documento = '';
        }

        // Campos de control
        $IND_ABONO_CARGO = $fila['IND_ABONO_CARGO'] ?? '';
        $CAN_IMPORTE_C   = floatval($fila['CAN_IMPORTE_C'] ?? 0);
        $CAN_IMPORTE     = floatval($fila['CAN_IMPORTE'] ?? 0);
        $CAN_IMPORTE_A   = floatval($fila['CAN_IMPORTE_A'] ?? 0);

        // Cálculo de crédito/pago
        $credito = ($IND_ABONO_CARGO == 'SI')
            ? $CAN_IMPORTE_C
            : (($IND_ABONO_CARGO == 'C') ? $CAN_IMPORTE : 0);

        $pago = ($IND_ABONO_CARGO == 'SI')
            ? $CAN_IMPORTE_A
            : (($IND_ABONO_CARGO == 'A') ? $CAN_IMPORTE : 0);

        $saldoAcumulado += ($credito - $pago);

        $dp_concat = $fila['DP_CONCAT'];
        // Extraer la operación (ejemplo: "OP. 123049")
        $operacion = '';
        if (preg_match('/OP\.\s*\d+/', $dp_concat, $m)) {
            $operacion = trim($m[0]);
        }

        // Guardar la fila individual con el saldo acumulado correcto
        $datosManipulados[] = [
            'item' => $index + 1,
            'fec_habilitacion' => $fila['FEC_HABILITACION'] ?? '',
            'empresa_id' => $nombreempresa,
            'accion' => $fila['ACCION'] ?? '',
            'div' => $div,
            'factura' => $documento,
            'dp_concat' => $dp_concat,
            'operacion' => $operacion,
            'credito' => $credito,
            'pago' => $pago,
            'saldo' => $saldoAcumulado, // 👈 SALDO ACUMULADO CORRECTO
            'saldo_individual' => $credito - $pago // 👈 Para cálculos internos
        ];
    }

    // --- AGRUPAR POR OPERACIÓN ("OP. ...") ---
    $agrupado = [];
    $saldoAcumuladoAgrupado = 0;
    
    foreach ($datosManipulados as $fila) {
        $clave = $fila['operacion'] ?: uniqid('sin_op_');

        if (!isset($agrupado[$clave])) {
            $agrupado[$clave] = [
                'item' => $fila['item'],
                'fec_habilitacion' => $fila['fec_habilitacion'],
                'empresa_id' => $fila['empresa_id'],
                'accion' => $fila['accion'],
                'div' => $fila['div'],
                'factura' => $fila['factura'],
                'dp_concat' => $fila['dp_concat'],
                'credito' => $fila['credito'],
                'pago' => $fila['pago'],
                'saldo' => $fila['saldo'], // 👈 Mantenemos el saldo acumulado original
                'operacion' => $fila['operacion'],
                'saldo_individual_sum' => $fila['saldo_individual']
            ];
        } else {
            // concatenar div y factura
            $agrupado[$clave]['div'] .= ', ' . $fila['div'];
            $agrupado[$clave]['factura'] .= ', ' . $fila['factura'];

            // sumar totales de crédito y pago
            $agrupado[$clave]['credito'] += $fila['credito'];
            $agrupado[$clave]['pago'] += $fila['pago'];
            $agrupado[$clave]['saldo_individual_sum'] += $fila['saldo_individual'];
            
            // 👇 ACTUALIZAR EL SALDO ACUMULADO CORRECTO
            // Buscamos el saldo acumulado máximo entre todas las filas agrupadas
            // Esto asegura que el saldo refleje el acumulado hasta la última transacción del grupo
            if ($fila['saldo'] > $agrupado[$clave]['saldo']) {
                $agrupado[$clave]['saldo'] = $fila['saldo'];
            }
        }
    }

    // Recalcular el orden correcto de los saldos acumulados
    $resultadoFinal = array_values($agrupado);
    
    // Ordenar por el item original para mantener la secuencia temporal
    usort($resultadoFinal, function($a, $b) {
        return $a['item'] - $b['item'];
    });

    // Recalcular saldos acumulados correctamente
    $saldoFinal = 0;
    foreach ($resultadoFinal as &$fila) {
        $saldoFinal += $fila['saldo_individual_sum'];
        $fila['saldo'] = $saldoFinal; // 👈 Saldo acumulado correcto
        unset($fila['saldo_individual_sum']); // Limpiar campo temporal
    }

    return $resultadoFinal;
}





}