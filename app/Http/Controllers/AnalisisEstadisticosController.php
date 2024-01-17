<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\viewVentaSalidas;
use App\ALMProducto;
use App\CMPCategoria;

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

		$comboperiodo 	=	viewVentaSalidas::groupBy(DB::raw('Cliente'))
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

		$lventas 		=	viewVentaSalidas::join('ALM.PRODUCTO', 'ALM.PRODUCTO.NOM_PRODUCTO', '=', 'viewVentaSalidas2024.Nombreproducto')
							->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
							->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
							->where('Cliente','=',$empresa_nombre)
							->whereRaw("YEAR(Fecha) = ".$anio)
							->whereRaw("MONTH(Fecha) = ".$mes)
							->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(TotalVenta) venta,sum(DescuentReglas) Descuento,MARCA.NOM_CATEGORIA AS NombreProducto,TIPOMARCA.COD_CATEGORIA AS COD_TIPOMARCA'))
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


 		$colorArray = 		["#63b598", "#ce7d78", "#ea9e70", "#a48a9e", "#c6e1e8", "#648177" ,"#0d5ac1" , "#f205e6" ,"#1c0365" ,"#14a9ad" ,"#4ca2f9" ,"#a4e43f" ,"#d298e2" ,"#6119d0", "#d2737d" ,"#c0a43c" ,"#f2510e" ,"#651be6" ,"#79806e" ,"#61da5e" ,"#cd2f00" , "#9348af" ,"#01ac53" ,"#c5a4fb" ,"#996635","#b11573" ,"#4bb473" ,"#75d89e" , "#2f3f94" ,"#2f7b99" ,"#da967d" ,"#34891f" ,"#b0d87b" ,"#ca4751" ,"#7e50a8" , "#c4d647" ,"#e0eeb8" ,"#11dec1" ,"#289812" ,"#566ca0" ,"#ffdbe1" ,"#2f1179" , "#935b6d" ,"#916988" ,"#513d98" ,"#aead3a", "#9e6d71", "#4b5bdc", "#0cd36d", "#250662", "#cb5bea", "#228916", "#ac3e1b", "#df514a", "#539397", "#880977", "#f697c1", "#ba96ce", "#679c9d", "#c6c42c", "#5d2c52", "#48b41b", "#e1cf3b", "#5be4f0", "#57c4d8", "#a4d17a", "#225b8", "#be608b", "#96b00c", "#088baf", "#f158bf", "#e145ba", "#ee91e3", "#05d371", "#5426e0", "#4834d0", "#802234", "#6749e8", "#0971f0", "#8fb413", "#b2b4f0", "#c3c89d", "#c9a941", "#41d158", "#fb21a3", "#51aed9", "#5bb32d", "#807fb", "#21538e", "#89d534", "#d36647", "#7fb411", "#0023b8", "#3b8c2a", "#986b53", "#f50422", "#983f7a", "#ea24a3", "#79352c", "#521250", "#c79ed2", "#d6dd92", "#e33e52", "#b2be57", "#fa06ec", "#1bb699", "#6b2e5f", "#64820f", "#1c271", "#21538e", "#89d534", "#d36647", "#7fb411", "#0023b8", "#3b8c2a", "#986b53", "#f50422", "#983f7a", "#ea24a3", "#79352c", "#521250", "#c79ed2", "#d6dd92", "#e33e52", "#b2be57", "#fa06ec", "#1bb699", "#6b2e5f", "#64820f", "#1c271", "#9cb64a", "#996c48", "#9ab9b7", "#06e052", "#e3a481", "#0eb621", "#fc458e", "#b2db15", "#aa226d", "#792ed8", "#73872a", "#520d3a", "#cefcb8", "#a5b3d9", "#7d1d85", "#c4fd57", "#f1ae16", "#8fe22a", "#ef6e3c", "#243eeb", "#1dc18", "#dd93fd", "#3f8473", "#e7dbce", "#421f79", "#7a3d93", "#635f6d", "#93f2d7", "#9b5c2a", "#15b9ee", "#0f5997", "#409188", "#911e20", "#1350ce", "#10e5b1", "#fff4d7", "#cb2582", "#ce00be", "#32d5d6", "#17232", "#608572", "#c79bc2", "#00f87c", "#77772a", "#6995ba", "#fc6b57", "#f07815", "#8fd883", "#060e27", "#96e591", "#21d52e", "#d00043", "#b47162", "#1ec227", "#4f0f6f", "#1d1d58", "#947002", "#bde052", "#e08c56", "#28fcfd", "#bb09b", "#36486a", "#d02e29", "#1ae6db", "#3e464c", "#a84a8f", "#911e7e", "#3f16d9", "#0f525f", "#ac7c0a", "#b4c086", "#c9d730", "#30cc49", "#3d6751", "#fb4c03", "#640fc1", "#62c03e", "#d3493a", "#88aa0b", "#406df9", "#615af0", "#4be47", "#2a3434", "#4a543f", "#79bca0", "#a8b8d4", "#00efd4", "#7ad236", "#7260d8", "#1deaa7", "#06f43a", "#823c59", "#e3d94c", "#dc1c06", "#f53b2a", "#b46238", "#2dfff6", "#a82b89", "#1a8011", "#436a9f", "#1a806a", "#4cf09d", "#c188a2", "#67eb4b", "#b308d3", "#fc7e41", "#af3101", "#ff065", "#71b1f4", "#a2f8a5", "#e23dd0", "#d3486d", "#00f7f9", "#474893", "#3cec35", "#1c65cb", "#5d1d0c", "#2d7d2a", "#ff3420", "#5cdd87", "#a259a4", "#e4ac44", "#1bede6", "#8798a4", "#d7790f", "#b2c24f", "#de73c2", "#d70a9c", "#25b67", "#88e9b8", "#c2b0e2", "#86e98f", "#ae90e2", "#1a806b", "#436a9e", "#0ec0ff", "#f812b3", "#b17fc9", "#8d6c2f", "#d3277a", "#2ca1ae", "#9685eb", "#8a96c6", "#dba2e6", "#76fc1b", "#608fa4", "#20f6ba", "#07d7f6", "#dce77a", "#77ecca"];


		$tventas  	= 		array();
		$tnprod  	= 		array();
		$tnc  		= 		array();
		$tcolores  	= 		array();

		$count      = 		0;
		foreach($lventas as $index=>$item){
			if($item->COD_TIPOMARCA == $tipomarca_sel){
				$meses[$count] 		= 		$nmeses[$item->MES-1];

				$tnprod[$count]  	= 		$item->NombreProducto;

				$tventas[$count]  	= 		intval($item->venta);
				$tcolores[$count]  	= 		$colorArray[$count];
				$tnc[$count]  		= 		$item->Descuento;
				$count      		= 		$count+1;
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
						 ]);
	}

	public function actionAjaxListarDetalleVentasxProducto(Request $request)
	{

		$anio 						=  	$request['anio'];
		$empresa_nombre 			=  	$request['empresa_nombre'];
		$mes 						=  	$request['mes'];
		$marca 						=	$request['marca'];
		$periodo_sel 				=	$anio.'-'.$mes;


		$lventas 					=	viewVentaSalidas::join('ALM.PRODUCTO', 'ALM.PRODUCTO.NOM_PRODUCTO', '=', 'viewVentaSalidas2024.Nombreproducto')
										->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
										->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
										->where('Cliente','=',$empresa_nombre)
										->whereRaw("YEAR(Fecha) = ".$anio)
										->whereRaw("MONTH(Fecha) = ".$mes)
										->where("MARCA.NOM_CATEGORIA",'=',$marca)
										->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(TotalVenta) venta,
											sum(DescuentReglas) Descuento,
											MARCA.NOM_CATEGORIA AS MARCA,
											NombreProducto
											'))
										->groupBy(DB::raw('Cliente'))
										->groupBy(DB::raw('YEAR(Fecha)'))
										->groupBy(DB::raw('MONTH (Fecha)'))
										->groupBy(DB::raw('MARCA.NOM_CATEGORIA'))
										->groupBy(DB::raw('NombreProducto'))
										->orderByRaw('YEAR(Fecha),MONTH (Fecha) ASC')
										->get();

		$meses  	= 		array();
		$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
 		$colorArray = 		["#63b598", "#ce7d78", "#ea9e70", "#a48a9e", "#c6e1e8", "#648177" ,"#0d5ac1" , "#f205e6" ,"#1c0365" ,"#14a9ad" ,"#4ca2f9" ,"#a4e43f" ,"#d298e2" ,"#6119d0", "#d2737d" ,"#c0a43c" ,"#f2510e" ,"#651be6" ,"#79806e" ,"#61da5e" ,"#cd2f00" , "#9348af" ,"#01ac53" ,"#c5a4fb" ,"#996635","#b11573" ,"#4bb473" ,"#75d89e" , "#2f3f94" ,"#2f7b99" ,"#da967d" ,"#34891f" ,"#b0d87b" ,"#ca4751" ,"#7e50a8" , "#c4d647" ,"#e0eeb8" ,"#11dec1" ,"#289812" ,"#566ca0" ,"#ffdbe1" ,"#2f1179" , "#935b6d" ,"#916988" ,"#513d98" ,"#aead3a", "#9e6d71", "#4b5bdc", "#0cd36d", "#250662", "#cb5bea", "#228916", "#ac3e1b", "#df514a", "#539397", "#880977", "#f697c1", "#ba96ce", "#679c9d", "#c6c42c", "#5d2c52", "#48b41b", "#e1cf3b", "#5be4f0", "#57c4d8", "#a4d17a", "#225b8", "#be608b", "#96b00c", "#088baf", "#f158bf", "#e145ba", "#ee91e3", "#05d371", "#5426e0", "#4834d0", "#802234", "#6749e8", "#0971f0", "#8fb413", "#b2b4f0", "#c3c89d", "#c9a941", "#41d158", "#fb21a3", "#51aed9", "#5bb32d", "#807fb", "#21538e", "#89d534", "#d36647", "#7fb411", "#0023b8", "#3b8c2a", "#986b53", "#f50422", "#983f7a", "#ea24a3", "#79352c", "#521250", "#c79ed2", "#d6dd92", "#e33e52", "#b2be57", "#fa06ec", "#1bb699", "#6b2e5f", "#64820f", "#1c271", "#21538e", "#89d534", "#d36647", "#7fb411", "#0023b8", "#3b8c2a", "#986b53", "#f50422", "#983f7a", "#ea24a3", "#79352c", "#521250", "#c79ed2", "#d6dd92", "#e33e52", "#b2be57", "#fa06ec", "#1bb699", "#6b2e5f", "#64820f", "#1c271", "#9cb64a", "#996c48", "#9ab9b7", "#06e052", "#e3a481", "#0eb621", "#fc458e", "#b2db15", "#aa226d", "#792ed8", "#73872a", "#520d3a", "#cefcb8", "#a5b3d9", "#7d1d85", "#c4fd57", "#f1ae16", "#8fe22a", "#ef6e3c", "#243eeb", "#1dc18", "#dd93fd", "#3f8473", "#e7dbce", "#421f79", "#7a3d93", "#635f6d", "#93f2d7", "#9b5c2a", "#15b9ee", "#0f5997", "#409188", "#911e20", "#1350ce", "#10e5b1", "#fff4d7", "#cb2582", "#ce00be", "#32d5d6", "#17232", "#608572", "#c79bc2", "#00f87c", "#77772a", "#6995ba", "#fc6b57", "#f07815", "#8fd883", "#060e27", "#96e591", "#21d52e", "#d00043", "#b47162", "#1ec227", "#4f0f6f", "#1d1d58", "#947002", "#bde052", "#e08c56", "#28fcfd", "#bb09b", "#36486a", "#d02e29", "#1ae6db", "#3e464c", "#a84a8f", "#911e7e", "#3f16d9", "#0f525f", "#ac7c0a", "#b4c086", "#c9d730", "#30cc49", "#3d6751", "#fb4c03", "#640fc1", "#62c03e", "#d3493a", "#88aa0b", "#406df9", "#615af0", "#4be47", "#2a3434", "#4a543f", "#79bca0", "#a8b8d4", "#00efd4", "#7ad236", "#7260d8", "#1deaa7", "#06f43a", "#823c59", "#e3d94c", "#dc1c06", "#f53b2a", "#b46238", "#2dfff6", "#a82b89", "#1a8011", "#436a9f", "#1a806a", "#4cf09d", "#c188a2", "#67eb4b", "#b308d3", "#fc7e41", "#af3101", "#ff065", "#71b1f4", "#a2f8a5", "#e23dd0", "#d3486d", "#00f7f9", "#474893", "#3cec35", "#1c65cb", "#5d1d0c", "#2d7d2a", "#ff3420", "#5cdd87", "#a259a4", "#e4ac44", "#1bede6", "#8798a4", "#d7790f", "#b2c24f", "#de73c2", "#d70a9c", "#25b67", "#88e9b8", "#c2b0e2", "#86e98f", "#ae90e2", "#1a806b", "#436a9e", "#0ec0ff", "#f812b3", "#b17fc9", "#8d6c2f", "#d3277a", "#2ca1ae", "#9685eb", "#8a96c6", "#dba2e6", "#76fc1b", "#608fa4", "#20f6ba", "#07d7f6", "#dce77a", "#77ecca"];


		$tventas  	= 		array();
		$tnprod  	= 		array();
		$tnc  		= 		array();
		$tcolores  	= 		array();

		$count      = 		0;
		foreach($lventas as $index=>$item){

				$meses[$count] 		= 		$nmeses[$item->MES-1];
				$tnprod[$count]  	= 		$item->NombreProducto;
				$tventas[$count]  	= 		intval($item->venta);
				$tcolores[$count]  	= 		$colorArray[$count];
				$tnc[$count]  		= 		$item->Descuento;
				$count      		= 		$count+1;

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

		$lventas 					=	viewVentaSalidas::join('ALM.PRODUCTO', 'ALM.PRODUCTO.NOM_PRODUCTO', '=', 'viewVentaSalidas2024.Nombreproducto')
										->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
										->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
										->where('Cliente','=',$empresa_nombre)
										->whereRaw("YEAR(Fecha) = ".$anio)
										->whereRaw("MONTH(Fecha) = ".$mes)
										->select(DB::raw('Cliente,YEAR(Fecha) ANIO,MONTH (Fecha) MES,sum(TotalVenta) venta,sum(DescuentReglas) Descuento,MARCA.NOM_CATEGORIA AS NombreProducto,TIPOMARCA.COD_CATEGORIA AS COD_TIPOMARCA'))
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
 		$colorArray = 		["#63b598", "#ce7d78", "#ea9e70", "#a48a9e", "#c6e1e8", "#648177" ,"#0d5ac1" , "#f205e6" ,"#1c0365" ,"#14a9ad" ,"#4ca2f9" ,"#a4e43f" ,"#d298e2" ,"#6119d0", "#d2737d" ,"#c0a43c" ,"#f2510e" ,"#651be6" ,"#79806e" ,"#61da5e" ,"#cd2f00" , "#9348af" ,"#01ac53" ,"#c5a4fb" ,"#996635","#b11573" ,"#4bb473" ,"#75d89e" , "#2f3f94" ,"#2f7b99" ,"#da967d" ,"#34891f" ,"#b0d87b" ,"#ca4751" ,"#7e50a8" , "#c4d647" ,"#e0eeb8" ,"#11dec1" ,"#289812" ,"#566ca0" ,"#ffdbe1" ,"#2f1179" , "#935b6d" ,"#916988" ,"#513d98" ,"#aead3a", "#9e6d71", "#4b5bdc", "#0cd36d", "#250662", "#cb5bea", "#228916", "#ac3e1b", "#df514a", "#539397", "#880977", "#f697c1", "#ba96ce", "#679c9d", "#c6c42c", "#5d2c52", "#48b41b", "#e1cf3b", "#5be4f0", "#57c4d8", "#a4d17a", "#225b8", "#be608b", "#96b00c", "#088baf", "#f158bf", "#e145ba", "#ee91e3", "#05d371", "#5426e0", "#4834d0", "#802234", "#6749e8", "#0971f0", "#8fb413", "#b2b4f0", "#c3c89d", "#c9a941", "#41d158", "#fb21a3", "#51aed9", "#5bb32d", "#807fb", "#21538e", "#89d534", "#d36647", "#7fb411", "#0023b8", "#3b8c2a", "#986b53", "#f50422", "#983f7a", "#ea24a3", "#79352c", "#521250", "#c79ed2", "#d6dd92", "#e33e52", "#b2be57", "#fa06ec", "#1bb699", "#6b2e5f", "#64820f", "#1c271", "#21538e", "#89d534", "#d36647", "#7fb411", "#0023b8", "#3b8c2a", "#986b53", "#f50422", "#983f7a", "#ea24a3", "#79352c", "#521250", "#c79ed2", "#d6dd92", "#e33e52", "#b2be57", "#fa06ec", "#1bb699", "#6b2e5f", "#64820f", "#1c271", "#9cb64a", "#996c48", "#9ab9b7", "#06e052", "#e3a481", "#0eb621", "#fc458e", "#b2db15", "#aa226d", "#792ed8", "#73872a", "#520d3a", "#cefcb8", "#a5b3d9", "#7d1d85", "#c4fd57", "#f1ae16", "#8fe22a", "#ef6e3c", "#243eeb", "#1dc18", "#dd93fd", "#3f8473", "#e7dbce", "#421f79", "#7a3d93", "#635f6d", "#93f2d7", "#9b5c2a", "#15b9ee", "#0f5997", "#409188", "#911e20", "#1350ce", "#10e5b1", "#fff4d7", "#cb2582", "#ce00be", "#32d5d6", "#17232", "#608572", "#c79bc2", "#00f87c", "#77772a", "#6995ba", "#fc6b57", "#f07815", "#8fd883", "#060e27", "#96e591", "#21d52e", "#d00043", "#b47162", "#1ec227", "#4f0f6f", "#1d1d58", "#947002", "#bde052", "#e08c56", "#28fcfd", "#bb09b", "#36486a", "#d02e29", "#1ae6db", "#3e464c", "#a84a8f", "#911e7e", "#3f16d9", "#0f525f", "#ac7c0a", "#b4c086", "#c9d730", "#30cc49", "#3d6751", "#fb4c03", "#640fc1", "#62c03e", "#d3493a", "#88aa0b", "#406df9", "#615af0", "#4be47", "#2a3434", "#4a543f", "#79bca0", "#a8b8d4", "#00efd4", "#7ad236", "#7260d8", "#1deaa7", "#06f43a", "#823c59", "#e3d94c", "#dc1c06", "#f53b2a", "#b46238", "#2dfff6", "#a82b89", "#1a8011", "#436a9f", "#1a806a", "#4cf09d", "#c188a2", "#67eb4b", "#b308d3", "#fc7e41", "#af3101", "#ff065", "#71b1f4", "#a2f8a5", "#e23dd0", "#d3486d", "#00f7f9", "#474893", "#3cec35", "#1c65cb", "#5d1d0c", "#2d7d2a", "#ff3420", "#5cdd87", "#a259a4", "#e4ac44", "#1bede6", "#8798a4", "#d7790f", "#b2c24f", "#de73c2", "#d70a9c", "#25b67", "#88e9b8", "#c2b0e2", "#86e98f", "#ae90e2", "#1a806b", "#436a9e", "#0ec0ff", "#f812b3", "#b17fc9", "#8d6c2f", "#d3277a", "#2ca1ae", "#9685eb", "#8a96c6", "#dba2e6", "#76fc1b", "#608fa4", "#20f6ba", "#07d7f6", "#dce77a", "#77ecca"];


		$tventas  	= 		array();
		$tnprod  	= 		array();
		$tnc  		= 		array();
		$tcolores  	= 		array();

		$count      = 		0;
		foreach($lventas as $index=>$item){
			if($item->COD_TIPOMARCA == $tipomarca_sel){
				$meses[$count] 		= 		$nmeses[$item->MES-1];
				$tnprod[$count]  	= 		$item->NombreProducto;
				$tventas[$count]  	= 		intval($item->venta);
				$tcolores[$count]  	= 		$colorArray[$count];
				$tnc[$count]  		= 		$item->Descuento;
				$count      		= 		$count+1;
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

	// 	$comboempresa 	=	viewVentaSalidas::groupBy(DB::raw('Cliente'))
	// 						->whereIn('Cliente',$arrayautoserv)
	// 						->pluck('Cliente','Cliente')
	// 						->toArray();


	// 	$empresa_nombre = 	'SUPERMERCADOS PERUANOS S.A.';
	// 	$anio 		=		'2023';


	// 	$lventas 	=		viewVentaSalidas::where('Cliente','=',$empresa_nombre)
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


	// 	$lventas 	=		viewVentaSalidas::where('Cliente','=',$empresa_nombre)
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
