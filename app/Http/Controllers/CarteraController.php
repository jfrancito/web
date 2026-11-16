<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use View;
use App\CMPContrato;
use App\CMPCategoria;
use Session;

class CarteraController extends Controller
{

	public function index($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion, 'Anadir');
		if ($validarurl <> 'true') {
			return $validarurl;
		}
		/******************************************************/

		$ojefeVenta = CMPCategoria::where('COD_ESTADO', '=', 1)
			->where('IND_ACTIVO', '=', 1)
			->where('TXT_GRUPO', '=', 'JEFE_VENTA')
			->where('TXT_ABREVIATURA', '=', Session::get('centros')->COD_CENTRO)
			->get();

		return View::make('contenido/gestionCuentas', [
			'idopcion'   => $idopcion,
			'ojefeVenta' => $ojefeVenta
		]);
	}

	public function ListarCuentas(Request $request)
	{
		if (!$request->ajax()) return redirect('/');
		$buscar = $request->buscar;
		$criterio_raw = $request->criterio ?: 'Cliente';
		$jv = $request->filtrojv;

		switch ($criterio_raw) {
			case 'codCliente':
				$criterio = 'E.COD_EMPR';
				$loe = 'e';
				break;
			case 'Cliente':
			default:
				$criterio = 'E.NOM_EMPR';
				$loe = 'l';
				break;
		}

		$cuentas = CMPContrato::from('CMP.CONTRATO AS CO')
			->select(
				'CO.COD_CONTRATO', 
				'E.NOM_EMPR AS CLIENTE', 
				'E.COD_EMPR AS CODCLIENTE',
				'RV.NOM_CATEGORIA AS RV', 
				'CN.NOM_CATEGORIA AS CANAL',
				'SB.NOM_CATEGORIA AS SUBCANAL', 
				'RCC.canlimitecredito AS CAN_LIMITE',
				'CP.NOM_CATEGORIA as CP',
				'RCC.clasificacion',
				'RCC.condicionpago_id',
				'RCC.id'
			)
			->leftjoin('CMP.CATEGORIA AS TC', 'TC.COD_CATEGORIA', '=', 'CO.COD_CATEGORIA_TIPO_CONTRATO')
			->leftjoin('STD.EMPRESA AS E', 'E.COD_EMPR', '=', 'CO.COD_EMPR_CLIENTE')
			->leftjoin('CMP.CATEGORIA AS CN', 'CN.COD_CATEGORIA', '=', 'CO.COD_CATEGORIA_CANAL_VENTA')
			->leftjoin('CMP.CATEGORIA AS SB', 'SB.COD_CATEGORIA', '=', 'CO.COD_CATEGORIA_SUB_CANAL')
			->leftjoin('CMP.CATEGORIA AS RV', 'RV.COD_CATEGORIA', '=', 'CO.COD_CATEGORIA_JEFE_VENTA')
			->leftjoin('WEB.reglacreditoclientes AS RCC', 'RCC.cliente_id', '=', 'E.COD_EMPR')
			->leftjoin('CMP.CATEGORIA AS CP', 'CP.COD_CATEGORIA', '=', 'RCC.condicionpago_id')
			->where('CO.COD_ESTADO', '=', 1)
			->where('CO.COD_CATEGORIA_TIPO_CONTRATO', 'TCO0000000000068')
			->where('CO.COD_CATEGORIA_ESTADO_CONTRATO', 'ECO0000000000001')
			->where('CO.COD_CENTRO', '=', Session::get('centros')->COD_CENTRO)
			->busquedaGenerica($criterio, $buscar, $loe)
			->busquedaGenerica('CO.COD_CATEGORIA_JEFE_VENTA', $jv, 'e')
			->paginate(15);

		// Si se pide formato JSON (por compatibilidad previa)
		if ($request->wantsJson()) {
			return [
				'pagination' => [
					'total'        => $cuentas->total(),
					'current_page' => $cuentas->currentPage(),
					'per_page'     => $cuentas->perPage(),
					'last_page'    => $cuentas->lastPage(),
					'from'         => $cuentas->firstItem(),
					'to'           => $cuentas->lastItem(),
				],
				'cuentas' => $cuentas
			];
		}

		// Retornar vista parcial renderizada para AJAX con jQuery
		return View::make('cartera/ajax/alistacuentas', [
			'cuentas'      => $cuentas,
			'buscar'       => $buscar,
			'criterio_raw' => $criterio_raw,
			'jv'           => $jv
		]);
	}

	public function modalDetalleCuenta(Request $request)
	{
		if (!$request->ajax()) return redirect('/');

		$codcliente    = $request->codcliente;
		$cliente       = $request->cliente;
		$idregla       = $request->id;
		$limitecredito = $request->limite;
		$condicionpago = $request->condicion;
		$clasificacion = $request->clasificacion;

		// Obtener saldos de la cuenta por tramos
		$saldos = DB::select('exec RPS.SALDO_TRAMO_CUENTA ?,?,?,?,?,?,?,?,?,?,?', array('', '', '', '', date("Y-m-d"), $codcliente, 'TCO0000000000068', '', '', '', ''));

		// Lista de condiciones de pago
		$combocondicion = CMPCategoria::where('TXT_GRUPO', '=', 'TIPO_PAGO')
			->where('COD_ESTADO', '=', 1)
			->orderBy('NOM_CATEGORIA', 'asc')
			->get();

		return View::make('cartera/ajax/adetallecuenta', [
			'codcliente'     => $codcliente,
			'cliente'        => $cliente,
			'idregla'        => $idregla,
			'limitecredito'  => $limitecredito,
			'condicionpago'  => $condicionpago,
			'clasificacion'  => $clasificacion,
			'saldos'         => $saldos,
			'combocondicion' => $combocondicion
		]);
	}

	public function SaldoCuenta(Request $request)
	{
		if (!$request->ajax()) return redirect('/');
		$cliente = $request->cliente;

		$saldocuenta = DB::select('exec RPS.SALDO_TRAMO_CUENTA ?,?,?,?,?,?,?,?,?,?,?', array('', '', '', '', date("Y-m-d"), $cliente, 'TCO0000000000068', '', '', '', ''));

		return [
			'saldocuenta' => $saldocuenta
		];
	}
}
