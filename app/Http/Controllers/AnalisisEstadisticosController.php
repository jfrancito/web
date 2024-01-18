<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\viewVentaSalidas;
use App\viewVentasConsolidado;
use App\ALMProducto;
use App\CMPCategoria;
use App\CMPContrato;
use App\Traits\AnaliticaTraits;
use View;
use Session;
use PDO;



class AnalisisEstadisticosController extends Controller
{

	use AnaliticaTraits;

	public function actionVentas($idopcion,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $arrayautoserv  =   ['SUPERMERCADOS PERUANOS S.A.','HIPERMERCADOS TOTTUS S.A.','HIPERMERCADOS TOTTUS ORIENTE S.A.C.'];

		// $comboempresa 	=	viewVentasConsolidado::orderByRaw('viewVentasConsolidado2024.Cliente asc')	
		// 					->groupBy(DB::raw('Cliente'))
		// 					->where('TXT_CATEGORIA_CANAL_VENTA','=','AUTOSERVICIOS')
		// 					->pluck('Cliente','Cliente')
		// 					->toArray();

		$comboempresa   =	CMPContrato::where('COD_CATEGORIA_CANAL_VENTA','=','CVE0000000000001')
							->where('COD_CATEGORIA_ESTADO_CONTRATO','=','ECO0000000000001')
							->groupBy(DB::raw('TXT_EMPR_CLIENTE'))
							->pluck('TXT_EMPR_CLIENTE','TXT_EMPR_CLIENTE')
							->toArray();



		$empresa_nombre = 	'SUPERMERCADOS PERUANOS S.A.';

		$comboperiodo 	=	viewVentasConsolidado::groupBy(DB::raw('Cliente'))
							->whereIn('Cliente',$arrayautoserv)
							->select(DB::raw("(CAST(Year(Fecha) AS VARCHAR(4)) +'-'+RIGHT('00' + CAST(MONTH(Fecha) AS NVARCHAR(2)), 2)) as periodo"))
							->groupBy(DB::raw('MONTH (Fecha)'))
							->groupBy(DB::raw('YEAR (Fecha)'))
							->orderByRaw('YEAR(Fecha) desc,MONTH (Fecha) desc')	
							->pluck('periodo','periodo')
							->toArray();




		$anio 			=	date("Y");
		$mes 			=	date("m");

		$combotipomarca =	$this->funciones->combo_categoria_general('TIPO_MARCA');
		$tipomarca_sel 	=	'TPM0000000000001';
		$tipomarca_txt 	=	'TERCEROS';

		$periodo_sel 	=	$anio.'-'.$mes;

		$lventas 		=	viewVentasConsolidado::join('ALM.PRODUCTO', 'ALM.PRODUCTO.COD_PRODUCTO', '=', 'viewVentasConsolidado2024.COD_PRODUCTO')
							->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
							->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
							->where('Cliente','=',$empresa_nombre)
							->whereRaw("YEAR(Fecha) = ".$anio)
							->whereRaw("MONTH(Fecha) = ".$mes)
							->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(CAN_TOTAL_OV) venta,MARCA.NOM_CATEGORIA AS NombreProducto,TIPOMARCA.COD_CATEGORIA AS COD_TIPOMARCA'))
							->groupBy(DB::raw('Cliente'))
							->groupBy(DB::raw('YEAR(Fecha)'))
							->groupBy(DB::raw('MONTH (Fecha)'))
							->groupBy(DB::raw('MARCA.NOM_CATEGORIA'))
							->groupBy(DB::raw('TIPOMARCA.COD_CATEGORIA'))
							->orderByRaw('YEAR(Fecha),MONTH (Fecha) ASC')
							->get();

		//dd($lventas);					

		$meses  	= 		array();
		$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];


 		$colorArray = 		 array(
								    "#FF5733", "#3498db", "#2ecc71", "#e74c3c", "#8e44ad",
								    "#f39c12", "#1abc9c", "#c0392b", "#2980b9", "#27ae60",
								    "#e67e22", "#9b59b6", "#16a085", "#d35400", "#34495e",
								    "#FF7F50", "#00BFFF", "#00FA9A", "#DC143C", "#8A2BE2",
								    "#8B4513", "#483D8B", "#2F4F4F", "#3CB371", "#BA55D3",
								    "#F08080", "#00CED1", "#556B2F", "#B22222", "#800080"
								);


		$tventas  		= 		array();
		$tnprod  		= 		array();
		$tnc  			= 		array();
		$tcolores  		= 		array();
		$count      	= 		0;
		$totalimporte 	= 		0;

		$numerosGenerados 	= array();
		foreach($lventas as $index=>$item){
			if($item->COD_TIPOMARCA == $tipomarca_sel){
				$aleatorio 			= 		$this->obtenerNumeroAleatorioNoRepetido(0, 29, $numerosGenerados);
				$meses[$count] 		= 		$nmeses[$item->MES-1];
				$tnprod[$count]  	= 		$item->NombreProducto;
				$tventas[$count]  	= 		intval($item->venta);
				$tcolores[$count]  	= 		$colorArray[$aleatorio];
				$tnc[$count]  		= 		$item->Descuento;
				$count      		= 		$count+1;
				$totalimporte 		= 		$totalimporte + intval($item->venta);

			}
		}

		$jmeses 	=		json_encode($meses);
		$jventas 	=		json_encode($tventas);
		$jtnc 		=		json_encode($tnc);
		$jprod 		=		json_encode($tnprod);
		$jcol 		=		json_encode($tcolores);

		return View::make('analitica/ventasxproducto',
						 [
							'anio' 			=> $anio,
							'mes' 			=> $mes,

							'meses' 		=> $jmeses,
							'ventas' 		=> $jventas,
							'tnc' 			=> $jtnc,
							'jprod' 		=> $jprod,
							'jcol' 			=> $jcol,

							'comboempresa' 	=> $comboempresa,
							'comboperiodo' 	=> $comboperiodo,
							'periodo_sel' 	=> $periodo_sel,
							'combotipomarca'=> $combotipomarca,
							'tipomarca_sel' => $tipomarca_sel,
							'tipomarca_txt' => $tipomarca_txt,

							'empresa_nombre'=> $empresa_nombre,
							'totalimporte'=> $totalimporte,
						 ]);
	}

	public function actionAjaxListarDetalleVentasxProducto(Request $request)
	{

		$anio 						=  	$request['anio'];
		$empresa_nombre 			=  	$request['empresa_nombre'];
		$mes 						=  	$request['mes'];
		$marca 						=	$request['marca'];
		$periodo_sel 				=	$anio.'-'.$mes;
		$tipomarca 					=	$request['tipomarca'];


		$datatipomarca 				=	CMPCategoria::where('COD_CATEGORIA','=',$tipomarca)->first();
		$tipomarca_txt 				=	$datatipomarca->NOM_CATEGORIA;


		$lventas 					=	viewVentasConsolidado::join('ALM.PRODUCTO', 'ALM.PRODUCTO.COD_PRODUCTO', '=', 'viewVentasConsolidado2024.COD_PRODUCTO')
										->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
										->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
										->where('Cliente','=',$empresa_nombre)
										->whereRaw("YEAR(Fecha) = ".$anio)
										->whereRaw("MONTH(Fecha) = ".$mes)
										->where("MARCA.NOM_CATEGORIA",'=',$marca)
										->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(CAN_TOTAL_OV) venta,
											MARCA.NOM_CATEGORIA AS MARCA,
											ALM.PRODUCTO.NOM_PRODUCTO AS NombreProducto
											'))
										->groupBy(DB::raw('Cliente'))
										->groupBy(DB::raw('YEAR(Fecha)'))
										->groupBy(DB::raw('MONTH (Fecha)'))
										->groupBy(DB::raw('MARCA.NOM_CATEGORIA'))
										->groupBy(DB::raw('ALM.PRODUCTO.NOM_PRODUCTO'))
										->orderByRaw('YEAR(Fecha),MONTH (Fecha) ASC')
										->get();

		$meses  	= 		array();
		$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
 		
 		$colorArray = 		 array(
								    "#FF5733", "#3498db", "#2ecc71", "#e74c3c", "#8e44ad",
								    "#f39c12", "#1abc9c", "#c0392b", "#2980b9", "#27ae60",
								    "#e67e22", "#9b59b6", "#16a085", "#d35400", "#34495e",
								    "#FF7F50", "#00BFFF", "#00FA9A", "#DC143C", "#8A2BE2",
								    "#8B4513", "#483D8B", "#2F4F4F", "#3CB371", "#BA55D3",
								    "#F08080", "#00CED1", "#556B2F", "#B22222", "#800080"
								);

		$tventas  	= 		array();
		$tnprod  	= 		array();
		$tnc  		= 		array();
		$tcolores  	= 		array();
		$count      = 		0;
		$totalimporte 	= 		0;

		$numerosGenerados 	= array();


		foreach($lventas as $index=>$item){

				$aleatorio 			= 		$this->obtenerNumeroAleatorioNoRepetido(0, 29, $numerosGenerados);

				$meses[$count] 		= 		$nmeses[$item->MES-1];
				$tnprod[$count]  	= 		$item->NombreProducto;
				$tventas[$count]  	= 		intval($item->venta);
				$tcolores[$count]  	= 		$colorArray[$aleatorio];
				$tnc[$count]  		= 		$item->Descuento;
				$count      		= 		$count+1;
				$totalimporte 		= 		$totalimporte + intval($item->venta);
		}


		$jmeses 	=		json_encode($meses);
		$jventas 	=		json_encode($tventas);
		$jtnc 		=		json_encode($tnc);
		$jprod 		=		json_encode($tnprod);
		$jcol 		=		json_encode($tcolores);
		$funcion 	= 		$this;

		return View::make('analitica/ajax/aventasxproductodetalle',
						 [
							'anio' 			=> $anio,
							'mes' 			=> $mes,
							'meses' 		=> $jmeses,
							'ventas' 		=> $jventas,
							'tnc' 			=> $jtnc,
							'jprod' 		=> $jprod,
							'jcol' 			=> $jcol,
							'empresa_nombre'=> $empresa_nombre,
							'marca'			=> $marca,

							'periodo_sel'	=> $periodo_sel,
							'tipomarca_txt'	=> $tipomarca_txt,
							'totalimporte'	=> $totalimporte,

							'ajax' 			=> true
						 ]);
	}


	public function actionAjaxListarVentasxProducto(Request $request)
	{

		$empresa_nombre 			=  	$request['empresa_nombre'];
		$periodo 					=  	$request['periodo'];
		$tipomarca 					=  	$request['tipomarca'];

		$anio 						=	substr($periodo, 0, 4);
		$mes 						=	substr($periodo, 5, 2);

		$datatipomarca 				=	CMPCategoria::where('COD_CATEGORIA','=',$tipomarca)->first();
		$tipomarca_txt 				=	$datatipomarca->NOM_CATEGORIA;
		$tipomarca_sel 				=	$tipomarca;
		$periodo_sel 				=	$anio.'-'.$mes;

		// $lventas 					=	viewVentasConsolidado::join('ALM.PRODUCTO', 'ALM.PRODUCTO.NOM_PRODUCTO', '=', 'viewVentasConsolidado2024.Nombreproducto')
		// 								->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
		// 								->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
		// 								->where('Cliente','=',$empresa_nombre)
		// 								->whereRaw("YEAR(Fecha) = ".$anio)
		// 								->whereRaw("MONTH(Fecha) = ".$mes)
		// 								->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(TotalVenta) venta,sum(DescuentReglas) Descuento,MARCA.NOM_CATEGORIA AS NombreProducto,TIPOMARCA.COD_CATEGORIA AS COD_TIPOMARCA'))
		// 								->groupBy(DB::raw('Cliente'))
		// 								->groupBy(DB::raw('YEAR(Fecha)'))
		// 								->groupBy(DB::raw('MONTH (Fecha)'))
		// 								->groupBy(DB::raw('MARCA.NOM_CATEGORIA'))
		// 								->groupBy(DB::raw('TIPOMARCA.COD_CATEGORIA'))
		// 								->orderByRaw('YEAR(Fecha),MONTH (Fecha) ASC')
		// 								->get();

		$lventas 					=	viewVentasConsolidado::join('ALM.PRODUCTO', 'ALM.PRODUCTO.COD_PRODUCTO', '=', 'viewVentasConsolidado2024.COD_PRODUCTO')
										->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
										->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
										->where('Cliente','=',$empresa_nombre)
										->whereRaw("YEAR(Fecha) = ".$anio)
										->whereRaw("MONTH(Fecha) = ".$mes)
										->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(CAN_TOTAL_OV) venta,MARCA.NOM_CATEGORIA AS NombreProducto,TIPOMARCA.COD_CATEGORIA AS COD_TIPOMARCA'))
										->groupBy(DB::raw('Cliente'))
										->groupBy(DB::raw('YEAR(Fecha)'))
										->groupBy(DB::raw('MONTH (Fecha)'))
										->groupBy(DB::raw('MARCA.NOM_CATEGORIA'))
										->groupBy(DB::raw('TIPOMARCA.COD_CATEGORIA'))
										->orderByRaw('YEAR(Fecha),MONTH (Fecha) ASC')
										->get();


		$meses  	= 		array();
		$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
 		$colorArray = 		 array(
								    "#FF5733", "#3498db", "#2ecc71", "#e74c3c", "#8e44ad",
								    "#f39c12", "#1abc9c", "#c0392b", "#2980b9", "#27ae60",
								    "#e67e22", "#9b59b6", "#16a085", "#d35400", "#34495e",
								    "#FF7F50", "#00BFFF", "#00FA9A", "#DC143C", "#8A2BE2",
								    "#8B4513", "#483D8B", "#2F4F4F", "#3CB371", "#BA55D3",
								    "#F08080", "#00CED1", "#556B2F", "#B22222", "#800080"
								);

		$tventas  	= 		array();
		$tnprod  	= 		array();
		$tnc  		= 		array();
		$tcolores  	= 		array();

		$count      = 		0;
		$totalimporte 	= 		0;

		$numerosGenerados 	= array();

		foreach($lventas as $index=>$item){
			if($item->COD_TIPOMARCA == $tipomarca_sel){
				$aleatorio 			= 		$this->obtenerNumeroAleatorioNoRepetido(0, 29, $numerosGenerados);
				$meses[$count] 		= 		$nmeses[$item->MES-1];
				$tnprod[$count]  	= 		$item->NombreProducto;
				$tventas[$count]  	= 		intval($item->venta);
				$tcolores[$count]  	= 		$colorArray[$aleatorio];
				$tnc[$count]  		= 		$item->Descuento;
				$count      		= 		$count+1;
				$totalimporte 		= 		$totalimporte + intval($item->venta);

			}
		}


		$jmeses 	=		json_encode($meses);
		$jventas 	=		json_encode($tventas);
		$jtnc 		=		json_encode($tnc);
		$jprod 		=		json_encode($tnprod);
		$jcol 		=		json_encode($tcolores);
		$funcion 	= 		$this;

		return View::make('analitica/ajax/aventasxproducto',
						 [
							'anio' 			=> $anio,
							'mes' 			=> $mes,
							'meses' 		=> $jmeses,
							'ventas' 		=> $jventas,
							'tnc' 			=> $jtnc,
							'jprod' 		=> $jprod,
							'jcol' 			=> $jcol,
							'empresa_nombre'=> $empresa_nombre,
							'periodo_sel'	=> $periodo_sel,
							'tipomarca_txt'	=> $tipomarca_txt,
							'totalimporte'	=> $totalimporte,
							'ajax' 			=> true
						 ]);
	}



	// public function actionVentas($idopcion,Request $request)
	// {

	// 	/******************* validar url **********************/
	// 	$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	//     if($validarurl <> 'true'){return $validarurl;}
	//     /******************************************************/


	//     $arrayautoserv  =   ['SUPERMERCADOS PERUANOS S.A.','HIPERMERCADOS TOTTUS S.A.','HIPERMERCADOS TOTTUS ORIENTE S.A.C.'];

	// 	$comboempresa 	=	viewVentasConsolidado::groupBy(DB::raw('Cliente'))
	// 						->whereIn('Cliente',$arrayautoserv)
	// 						->pluck('Cliente','Cliente')
	// 						->toArray();


	// 	$empresa_nombre = 	'SUPERMERCADOS PERUANOS S.A.';
	// 	$anio 		=		'2023';


	// 	$lventas 	=		viewVentasConsolidado::where('Cliente','=',$empresa_nombre)
	// 						->whereRaw("YEAR(Fecha) = ".$anio)
	// 						->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(TotalVenta) venta,sum(DescuentReglas) Descuento'))
	// 						->groupBy(DB::raw('Cliente'))
	// 						->groupBy(DB::raw('YEAR(Fecha)'))
	// 						->groupBy(DB::raw('MONTH (Fecha)'))
	// 						->orderByRaw('YEAR(Fecha),MONTH (Fecha) ASC')
	// 						->get();

	// 	$meses  	= 		array();
	// 	$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
	// 	$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

	// 	$tventas  	= 		array();
	// 	$tnc  		= 		array();

	// 	foreach($lventas as $index=>$item){

	// 		$meses[$index] 		= 		$nmeses[$item->MES-1];
	// 		$tventas[$index]  	= 		$item->venta;
	// 		$tnc[$index]  		= 		$item->Descuento;

	// 	}

	// 	$jmeses 	=		json_encode($meses);
	// 	$jventas 	=		json_encode($tventas);
	// 	$jtnc 		=		json_encode($tnc);


	// 	return View::make('analitica/ventas',
	// 					 [
	// 						'anio' 			=> $anio,
	// 						'meses' 		=> $jmeses,
	// 						'ventas' 		=> $jventas,
	// 						'tnc' 			=> $jtnc,
	// 						'comboempresa' 	=> $comboempresa,
	// 						'empresa_nombre'=> $empresa_nombre,		 	
	// 					 ]);
	// }

	// public function actionAjaxListarVentas(Request $request)
	// {

	// 	$empresa_nombre 			=  	$request['empresa_nombre'];
	// 	$anio 		=		'2023';


	// 	$lventas 	=		viewVentasConsolidado::where('Cliente','=',$empresa_nombre)
	// 						->whereRaw("YEAR(Fecha) = ".$anio)
	// 						->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(TotalVenta) venta,sum(DescuentReglas) Descuento'))
	// 						->groupBy(DB::raw('Cliente'))
	// 						->groupBy(DB::raw('YEAR(Fecha)'))
	// 						->groupBy(DB::raw('MONTH (Fecha)'))
	// 						->orderByRaw('YEAR(Fecha),MONTH (Fecha) ASC')
	// 						->get();

	// 	$meses  	= 		array();
	// 	$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
	// 	$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

	// 	$tventas  	= 		array();
	// 	$tnc  		= 		array();

	// 	foreach($lventas as $index=>$item){

	// 		$meses[$index] 		= 		$nmeses[$item->MES-1];
	// 		$tventas[$index]  	= 		$item->venta;
	// 		$tnc[$index]  		= 		$item->Descuento;

	// 	}

	// 	$jmeses 	=		json_encode($meses);
	// 	$jventas 	=		json_encode($tventas);
	// 	$jtnc 		=		json_encode($tnc);
	// 	$funcion 	= 	$this;
	// 	return View::make('analitica/ajax/aventas',
	// 					 [
	// 						'anio' 			=> $anio,
	// 						'meses' 		=> $jmeses,
	// 						'ventas' 		=> $jventas,
	// 						'tnc' 			=> $jtnc,
	// 						'empresa_nombre'=> $empresa_nombre,
	// 						'ajax' 			=> true
	// 					 ]);
	// }




}
