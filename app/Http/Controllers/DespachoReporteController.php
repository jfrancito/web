<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBDetraccionGuia;
use App\WEBDocAsocDetraccion;
use View;
use Session;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class DespachoReporteController extends Controller
{


	public function actionPagoDetraciones($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

		$combo_lista_centros 	= 	$this->funciones->combo_lista_centro();

		return View::make('despacho/reporte/detraccionguia',
						 [
						 	'idopcion' 					=> $idopcion,
						 	'combo_lista_centros' 		=> $combo_lista_centros,
							'inicio'					=> $this->inicio,
							'hoy'						=> $this->fin,
						 ]);

	}


	public function actionAjaxReportePagoDetraccion(Request $request)
	{

		set_time_limit(0);
		$centro_id 				=  	$request['centro_id'];
		$finicio 				=  	$request['finicio'];	
		$ffin 					=  	$request['ffin'];	

	    $detraciongruia 		= 	WEBDetraccionGuia::where('WEB.DETRACCION_GUIA.FEC_EMISION','>=', $finicio)
			    					->where('WEB.DETRACCION_GUIA.FEC_EMISION','<=', $ffin)
			    					->where('WEB.DETRACCION_GUIA.COD_CENTRO','=', $centro_id)
			    					->where('WEB.DETRACCION_GUIA.COD_EMPR','=', Session::get('empresas')->COD_EMPR)
			    					->get();

		$funcion 				= 	$this;

		return View::make('despacho/reporte/ajax/detraccionguia',
						 [
							'detraciongruia'   	=> $detraciongruia,
						 	'funcion' 			=> $funcion,
						 	'ajax' 				=> true, 					 
						 ]);

	}


	public function actionPagoDetraccionExcel($cod_guia)
	{
		set_time_limit(0);

		$titulo 									=   'pagodetracciones'.$cod_guia;

		$listadetracciones = WEBDocAsocDetraccion::where('COD_DOC_ASOC','=',$cod_guia)->get();
		$funcion 									= 	$this;

	    Excel::create($titulo, function($excel) use ($listadetracciones,$titulo,$funcion) {
	        $excel->sheet('Pedidos', function($sheet) use ($listadetracciones,$titulo,$funcion) {

	            $sheet->loadView('despacho/excel/detraccionguia')->with('listadetracciones',$listadetracciones)
	                                         		 			   ->with('titulo',$titulo)
	                                         		 			   ->with('funcion',$funcion);                                        		 
	        });
	    })->export('xls');

	}




}
