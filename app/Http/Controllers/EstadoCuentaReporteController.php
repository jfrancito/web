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
use App\Traits\EstadoCuentaTraits;

class EstadoCuentaReporteController extends Controller
{

	use EstadoCuentaTraits;


	public function actionEstadoCuentaVendedorPDF($fechainicio,$fechafin,$jefeventa_id,$cliente_id,Request $request)
	{

		set_time_limit(0);
		$listadatos 				=	$this->est_resumen_estado_cuenta($fechainicio, $fechafin, $cliente_id,'');
		$cliente 					= 	DB::table('STD.EMPRESA')
									    ->where('COD_EMPR', $cliente_id)
										->first();

		$jefeventa 					= 	DB::table('CMP.CATEGORIA')
									    ->where('COD_CATEGORIA', $jefeventa_id)
										->first();


		$titulo 					=   'Estado Cuenta '.$cliente->NOM_EMPR;
		$funcion 					= 	$this->funciones;

		$pdf 						= 	PDF::loadView('estadocuenta.pdf.pdfestadocuenta', 
												[
													'listadatos' 	  	=> $listadatos,
													'fechainicio' 	  	=> $fechainicio,
													'fechafin' 	  		=> $fechafin,
													'cliente_id' 	  	=> $cliente_id,
													'cliente' 	  		=> $cliente,
													'jefeventa' 	  	=> $jefeventa,
													'titulo' 		  	=> $titulo									
												]);

		return $pdf->stream('download.pdf');
	}


	public function actionEstadoCuentaVendedorExcel($fechainicio,$fechafin,$jefeventa_id,$cliente_id,Request $request)
	{

		set_time_limit(0);
		$listadatos 				=	$this->est_resumen_estado_cuenta($fechainicio, $fechafin, $cliente_id,'');

		$cliente 					= 	DB::table('STD.EMPRESA')
									    ->where('COD_EMPR', $cliente_id)
										->first();
		$titulo 					=   'Estado Cuenta '.$cliente->NOM_EMPR;
		$funcion 					= 	$this->funciones;

	    Excel::create($titulo, function($excel) use ($listadatos,$titulo,$funcion) {
	        $excel->sheet('Estado cuenta', function($sheet) use ($listadatos,$titulo,$funcion) {
	            $sheet->loadView('estadocuenta/excel/eestadocuenta')->with('listadatos',$listadatos)
	                                         		 			   ->with('titulo',$titulo)
	                                         		 			   ->with('funcion',$funcion);                                        		 
	        });
	    })->export('xls');


	}



	public function actionReporteEstadoCuenta(Request $request)
	{

		$fecha_inicio 				= 	$request['fecha_inicio'];
		$fecha_fin 					= 	$request['fecha_fin'];
		$jefeventa_id 				= 	$request['jefeventa_id'];
		$cliente_id 				= 	$request['cliente_id'];
		$listadatos 				=	$this->est_resumen_estado_cuenta($fecha_inicio, $fecha_fin, $cliente_id,'');
		//DD($listadatos);
		return View::make('estadocuenta/reporte/ajax/aestadocuenta',
						 [
						 	'listadatos' 				=> $listadatos,
						 	'ajax'   		  			=> true,
						 ]);
	}


	public function actionComboClientexJefe(Request $request)
	{

		$operacion_id 				= 	$request['operacion_id'];
		$array_cliente = DB::table(DB::raw('(SELECT
		        MIN(LTRIM(E.NOM_EMPR)) AS NOM_EMPR_CLI, 
		        E.COD_EMPR AS COD_EMPR_CLI
		    FROM STD.EMPRESA E
		    INNER JOIN CMP.CONTRATO CO
		        ON CO.COD_EMPR_CLIENTE = E.COD_EMPR
		        AND CO.COD_ESTADO = 1
		        AND CO.COD_CATEGORIA_TIPO_CONTRATO = \'TCO0000000000068\'
		        AND CO.COD_CATEGORIA_JEFE_VENTA = ?
		    WHERE E.COD_ESTADO = 1
		        AND CO.COD_CATEGORIA_ESTADO_CONTRATO IN (\'ECO0000000000001\', \'ECO0000000000002\')
		    GROUP BY E.COD_EMPR) AS T'))
		    ->select('NOM_EMPR_CLI', 'COD_EMPR_CLI')
		    ->orderBy('NOM_EMPR_CLI', 'asc')
		    ->setBindings([$operacion_id])
			->pluck('NOM_EMPR_CLI','COD_EMPR_CLI')
			->toArray();





		$combocliente 				=   array('' => "Seleccione Cliente") + $array_cliente;
		$cliente_id 				=   "";

		return View::make('general/combo/combocliente',
						 [
						 	'combocliente' 				=> $combocliente,
						 	'cliente_id' 				=> $cliente_id,
						 	'ajax'   		  			=> true,
						 ]);
	}


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

		$array_jefe 	= 	DB::table('CMP.CATEGORIA')
						    ->select('COD_CATEGORIA', 'NOM_CATEGORIA')
						    ->where('TXT_GRUPO', 'LIKE', 'JEFE_VENTA')
						    ->where('COD_ESTADO', 1)
						    ->orderBy('NOM_CATEGORIA', 'asc')
							->pluck('NOM_CATEGORIA','COD_CATEGORIA')
							->toArray();

		$combojefeventa =   array('' => "Seleccione Jefe Venta") + $array_jefe;
		$jefeventa_id 	=   "";
		$combocliente 	=   array('' => "Seleccione Cliente");
		$cliente_id 	=   "";

		return View::make('estadocuenta/reporte/estadocuentavendedor',
						 [
						 	'idopcion' 					=> $idopcion,
						 	'combojefeventa' 			=> $combojefeventa,
						 	'jefeventa_id' 				=> $jefeventa_id,
						 	'combocliente' 				=> $combocliente,
						 	'cliente_id' 				=> $cliente_id,
							'inicio'					=> $this->inicio,
							'hoy'						=> $this->fin,
						 ]);

	}


}
