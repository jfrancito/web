<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\viewVentaSalidas;

use View;
use Session;
use PDO;

class AnalisisEstadisticosController extends Controller
{

	public function actionVentas($idopcion,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/


	    $arrayautoserv  =   ['SUPERMERCADOS PERUANOS S.A.','HIPERMERCADOS TOTTUS S.A.','HIPERMERCADOS TOTTUS ORIENTE S.A.C.'];

		$comboempresa 	=	viewVentaSalidas::groupBy(DB::raw('Cliente'))
							->whereIn('Cliente',$arrayautoserv)
							->pluck('Cliente','Cliente')
							->toArray();


		$empresa_nombre = 	'SUPERMERCADOS PERUANOS S.A.';
		$anio 		=		'2023';


		$lventas 	=		viewVentaSalidas::where('Cliente','=',$empresa_nombre)
							->whereRaw("YEAR(Fecha) = ".$anio)
							->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(TotalVenta) venta,sum(DescuentReglas) Descuento'))
							->groupBy(DB::raw('Cliente'))
							->groupBy(DB::raw('YEAR(Fecha)'))
							->groupBy(DB::raw('MONTH (Fecha)'))
							->orderByRaw('YEAR(Fecha),MONTH (Fecha) ASC')
							->get();

		$meses  	= 		array();
		$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

		$tventas  	= 		array();
		$tnc  		= 		array();

		foreach($lventas as $index=>$item){

			$meses[$index] 		= 		$nmeses[$item->MES-1];
			$tventas[$index]  	= 		$item->venta;
			$tnc[$index]  		= 		$item->Descuento;

		}

		$jmeses 	=		json_encode($meses);
		$jventas 	=		json_encode($tventas);
		$jtnc 		=		json_encode($tnc);


		return View::make('analitica/ventas',
						 [
							'anio' 			=> $anio,
							'meses' 		=> $jmeses,
							'ventas' 		=> $jventas,
							'tnc' 			=> $jtnc,
							'comboempresa' 	=> $comboempresa,
							'empresa_nombre'=> $empresa_nombre,		 	
						 ]);
	}




	public function actionAjaxListarVentas(Request $request)
	{

		$empresa_nombre 			=  	$request['empresa_nombre'];
		$anio 		=		'2023';


		$lventas 	=		viewVentaSalidas::where('Cliente','=',$empresa_nombre)
							->whereRaw("YEAR(Fecha) = ".$anio)
							->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(TotalVenta) venta,sum(DescuentReglas) Descuento'))
							->groupBy(DB::raw('Cliente'))
							->groupBy(DB::raw('YEAR(Fecha)'))
							->groupBy(DB::raw('MONTH (Fecha)'))
							->orderByRaw('YEAR(Fecha),MONTH (Fecha) ASC')
							->get();

		$meses  	= 		array();
		$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

		$tventas  	= 		array();
		$tnc  		= 		array();

		foreach($lventas as $index=>$item){

			$meses[$index] 		= 		$nmeses[$item->MES-1];
			$tventas[$index]  	= 		$item->venta;
			$tnc[$index]  		= 		$item->Descuento;

		}

		$jmeses 	=		json_encode($meses);
		$jventas 	=		json_encode($tventas);
		$jtnc 		=		json_encode($tnc);
		$funcion 	= 	$this;
		return View::make('analitica/ajax/aventas',
						 [
							'anio' 			=> $anio,
							'meses' 		=> $jmeses,
							'ventas' 		=> $jventas,
							'tnc' 			=> $jtnc,
							'empresa_nombre'=> $empresa_nombre,
							'ajax' 			=> true
						 ]);



	}




}
