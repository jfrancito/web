<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBListaCliente,App\STDEmpresa;
use View;
use Session;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class EstadoCuentaReporteController extends Controller
{


	public function actionEstadoCuentaVendedor($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $array_id 		= 	STDEmpresa::join('CMP.CONTRATO', function ($join) {
							            $join->on('CMP.CONTRATO.COD_EMPR_CLIENTE', '=', 'STD.EMPRESA.COD_EMPR')
							            ->where('CMP.CONTRATO.COD_ESTADO','=',1)
							            ->where('CMP.CONTRATO.COD_CATEGORIA_TIPO_CONTRATO','=','TCO0000000000068')
							            ->where('CMP.CONTRATO.COD_CATEGORIA_JEFE_VENTA','=',Session::get('usuario')->fuerzaventa_id);
							        })

							->where('STD.EMPRESA.COD_ESTADO','=',1)
							->whereIn('CMP.CONTRATO.COD_CATEGORIA_ESTADO_CONTRATO',['ECO0000000000001' ,'ECO0000000000002'])
							->groupBy('STD.EMPRESA.COD_EMPR')
							->pluck('STD.EMPRESA.COD_EMPR')
							->toArray();


	    $listaclientes 		= 	STDEmpresa::whereIn('COD_EMPR',$array_id)
								->orderBy('NOM_EMPR', 'asc')
								->pluck('NOM_EMPR','COD_EMPR')
								->toArray();

		$combocliente  		=   array('' => "Seleccione cliente") + $listaclientes;

		return View::make('estadocuenta/reporte/estadocuentavendedor',
						 [
						 	'idopcion' 					=> $idopcion,
						 	'combocliente' 				=> $combocliente,
							'inicio'					=> $this->inicio,
							'hoy'						=> $this->fin,
						 ]);

	}


}
