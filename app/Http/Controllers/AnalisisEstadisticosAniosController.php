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


class AnalisisEstadisticosAniosController extends Controller
{
	use AnaliticaTraits;

	public function actionVentaAutoservicioEntreAnios($idopcion,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		$inicio			= 	$this->inicio;
		$hoy			= 	$this->fin;

		$comboanioinicio=	viewVentaSalidas::select(DB::raw("YEAR (Fecha) as anio"))
							->groupBy(DB::raw('YEAR (Fecha)'))
							->orderByRaw('YEAR(Fecha) desc')
							->pluck('anio','anio')
							->toArray();
		$selec_anioini  =   $this->anio-1;
		$comboaniofin	=	$comboanioinicio;
		$selec_aniofin  =   $this->anio;
		$tiporeporte 	=	'SOLES';

		$arrayempresa   =	CMPContrato::where('COD_CATEGORIA_CANAL_VENTA','=','CVE0000000000001')
							->where('COD_CATEGORIA_ESTADO_CONTRATO','=','ECO0000000000001')
							->groupBy(DB::raw('TXT_EMPR_CLIENTE'))
							->pluck('TXT_EMPR_CLIENTE')
							->toArray();

		$combocliente 	=	viewVentaSalidas::join('ALM.PRODUCTO', 'ALM.PRODUCTO.NOM_PRODUCTO', '=', 'viewVentaSalidas2024.NombreProducto')
							->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
							->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
							->where(function ($query) use ($selec_anioini,$selec_aniofin) {
							    $query->where(DB::raw('YEAR(Fecha)'), '=', $selec_anioini)
							          ->orWhere(DB::raw('YEAR(Fecha)'), '=', $selec_aniofin);
							})
							->whereIn('Cliente',$arrayempresa)
							->select(DB::raw("Cliente"))
							->groupBy(DB::raw('Cliente'))
							->pluck('Cliente','Cliente')
							->toArray();

		$combocliente   = 	array('TODOS' => 'TODOS') + $combocliente;
		$selec_cliente  =   'TODOS';



		$lventassalida 	=	viewVentaSalidas::join('ALM.PRODUCTO', 'ALM.PRODUCTO.NOM_PRODUCTO', '=', 'viewVentaSalidas2024.NombreProducto')
							->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
							->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
							->where(function ($query) use ($selec_anioini,$selec_aniofin) {
							    $query->where(DB::raw('YEAR(Fecha)'), '=', $selec_anioini)
							          ->orWhere(DB::raw('YEAR(Fecha)'), '=', $selec_aniofin);
							})
							->whereIn('Cliente',$arrayempresa)
							->select(DB::raw("MAX(Cliente) Cliente,
								YEAR(Fecha) Anio,
								MONTH(Fecha) Mes,
								(CASE
								    WHEN '".$tiporeporte."' = 'SACOS' THEN sum(Cant50kg)
								    ELSE sum(CantidadProducto2*PrecioVentaIGV)
								 END) as venta,
								sum(CostoExtendido) as CostoExtendido"))
							->groupBy(DB::raw('YEAR(Fecha)'))
							->groupBy(DB::raw('MONTH(Fecha)'))
							->orderByRaw('sum(TotalVenta) desc')
							->get();

 		$colorArray 	= 	$this->colores_array();
		$ttotal_s  		= 	array();
		$tcosto_s  		= 	array();
		$tutilidad_s  	= 	array();
		$meses_s  		= 	array();
		$tventas_s  	= 	array();
		$tventas2_s  	= 	array();

		$tcliente_s  	= 	array();

		$tnprod_s  		= 	array();
		$tnc_s  		= 	array();
		$tcolores_s  	= 	array();
		$count_s      	= 	0;
		$totalimporte_s = 	0;
		$numerosGenerados 	= array();


		$meses  	= 		array();
		$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];


		for($i=0; $i<count($nmeses); $i++)
      	{
      		$importe01 = 0.00;
      		$importe02 = 0.00;
			foreach($lventassalida as $index=>$item){
				//anio01
				if($item->Anio == $selec_anioini){
					if($item->Mes-1 == $i){
						$importe01  = 	intval($item->venta);			
					}
				}
				//anio02
				if($item->Anio == $selec_aniofin){
					if($item->Mes-1 == $i){
						$importe02  = 	intval($item->venta);			
					}
				}
			}

			$tventas_s[$count_s]  = 	intval($importe01);
			$tventas2_s[$count_s]  = 	intval($importe02);
			$count_s 			=		$count_s +1;
      	}
		$total01 	= 	0;
		$total02 	= 	0;

		foreach($lventassalida as $index=>$item){


			if($item->Anio == $selec_anioini){
				$total01  = 	$total01 + intval($item->venta);
			}
			if($item->Anio == $selec_aniofin){
				$total02  = 	$total02 + intval($item->venta);
			}

			$totalimporte_s 	= 		$totalimporte_s + intval($item->venta);
		}

		$jmeses_s 	=		json_encode($nmeses);
		$jventas_s 	=		json_encode($tventas_s);
		$jventas2_s =		json_encode($tventas2_s);
		$jtnc_s 	=		json_encode($tnc_s);
		$jprod_s 	=		json_encode($tnprod_s);
		$jcol_s 	=		json_encode($tcolores_s);
		$jcostos_s 	=		json_encode($tcosto_s);
		$jutilidad_s=		json_encode($tutilidad_s);
		$jcliente_s =		json_encode($tcliente_s);
		$jtotal_s 	=		json_encode($ttotal_s);
		$tituloban 	=		'SOLES';
		$simmodena 	=		'S/.';

		return View::make('analitica/ventasxautoservicioxanio',
						 [
							'meses_s' 		=> $jmeses_s,
							'ventas_s' 		=> $jventas_s,
							'ventas2_s' 	=> $jventas2_s,
							'tnc_s' 		=> $jtnc_s,
							'jprod_s' 		=> $jprod_s,
							'jcol_s' 		=> $jcol_s,
							'totalimporte_s'=> $totalimporte_s,
							'jcostos_s'		=> $jcostos_s,
							'jutilidad_s'	=> $jutilidad_s,
							'jtotal_s'		=> $jtotal_s,
							'inicio'		=> $this->inicio,
							'hoy'			=> $this->fin,
							'tituloban'		=> $tituloban,
							'simmodena'		=> $simmodena,
							'jcliente_s'	=> $jcliente_s,
							'tiporeporte'	=> $tiporeporte,
							'comboanioinicio'=> $comboanioinicio,
							'selec_anioini'	=> $selec_anioini,
							'comboaniofin'	=> $comboaniofin,
							'selec_aniofin'	=> $selec_aniofin,
							'combocliente'	=> $combocliente,
							'selec_cliente'	=> $selec_cliente,
							'total01'		=> $total01,
							'total02'		=> $total02,

						 ]);
	}



	public function actionAjaxListarVentasxAutoservicioAnio(Request $request)
	{

		$selec_anioini	= 	$request['selec_anioini'];
		$selec_aniofin	= 	$request['selec_aniofin'];
		$selec_cliente	= 	$request['selec_cliente'];

		$tiporeporte 	=	'SOLES';
		$inicio			= 	$this->inicio;
		$hoy			= 	$this->fin;
		//dd($tiporeporte);

		$arrayempresa   =	CMPContrato::where('COD_CATEGORIA_CANAL_VENTA','=','CVE0000000000001')
							->where('COD_CATEGORIA_ESTADO_CONTRATO','=','ECO0000000000001')
							->groupBy(DB::raw('TXT_EMPR_CLIENTE'))
							->pluck('TXT_EMPR_CLIENTE')
							->toArray();

		$lventassalida 	=	viewVentaSalidas::join('ALM.PRODUCTO', 'ALM.PRODUCTO.NOM_PRODUCTO', '=', 'viewVentaSalidas2024.NombreProducto')
							->join('CMP.CATEGORIA AS MARCA', 'MARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_MARCA')
							->join('CMP.CATEGORIA AS TIPOMARCA', 'TIPOMARCA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_PRODUCTO_SUPERMERCADOS')
							->where(function ($query) use ($selec_anioini,$selec_aniofin) {
							    $query->where(DB::raw('YEAR(Fecha)'), '=', $selec_anioini)
							          ->orWhere(DB::raw('YEAR(Fecha)'), '=', $selec_aniofin);
							})
							->Cliente($selec_cliente)
							->select(DB::raw("MAX(Cliente) Cliente,
								YEAR(Fecha) Anio,
								MONTH(Fecha) Mes,
								(CASE
								    WHEN '".$tiporeporte."' = 'SACOS' THEN sum(Cant50kg)
								    ELSE sum(CantidadProducto2*PrecioVentaIGV)
								 END) as venta,
								sum(CostoExtendido) as CostoExtendido"))
							->groupBy(DB::raw('YEAR(Fecha)'))
							->groupBy(DB::raw('MONTH(Fecha)'))
							->orderByRaw('sum(TotalVenta) desc')
							->get();


 		$colorArray 	= 	$this->colores_array();
		$ttotal_s  		= 	array();
		$tcosto_s  		= 	array();
		$tutilidad_s  	= 	array();
		$meses_s  		= 	array();
		$tventas_s  	= 	array();
		$tventas2_s  	= 	array();

		$tcliente_s  	= 	array();

		$tnprod_s  		= 	array();
		$tnc_s  		= 	array();
		$tcolores_s  	= 	array();
		$count_s      	= 	0;
		$totalimporte_s = 	0;
		$numerosGenerados 	= array();

		$total01 	= 	0;
		$total02 	= 	0;

		$meses  	= 		array();
		$nmeses 	= 		["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$nmeses 	= 		["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];


		for($i=0; $i<count($nmeses); $i++)
      	{
      		$importe01 = 0.00;
      		$importe02 = 0.00;
			foreach($lventassalida as $index=>$item){
				//anio01
				if($item->Anio == $selec_anioini){
					if($item->Mes-1 == $i){
						$importe01  = 	intval($item->venta);			
					}
				}
				//anio02
				if($item->Anio == $selec_aniofin){
					if($item->Mes-1 == $i){
						$importe02  = 	intval($item->venta);			
					}
				}
			}


			$tventas_s[$count_s]  = 	intval($importe01);
			$tventas2_s[$count_s]  = 	intval($importe02);
			$count_s 			=		$count_s +1;
      	}

		foreach($lventassalida as $index=>$item){
			if($item->Anio == $selec_anioini){
				$total01  = 	$total01 + intval($item->venta);
			}
			if($item->Anio == $selec_aniofin){
				$total02  = 	$total02 + intval($item->venta);
			}
			$totalimporte_s 	= 		$totalimporte_s + intval($item->venta);
		}


		$jmeses_s 	=		json_encode($nmeses);
		$jventas_s 	=		json_encode($tventas_s);
		$jventas2_s =		json_encode($tventas2_s);
		$jtnc_s 	=		json_encode($tnc_s);
		$jprod_s 	=		json_encode($tnprod_s);
		$jcol_s 	=		json_encode($tcolores_s);
		$jcostos_s 	=		json_encode($tcosto_s);
		$jutilidad_s=		json_encode($tutilidad_s);
		$jcliente_s =		json_encode($tcliente_s);
		$jtotal_s 	=		json_encode($ttotal_s);
		$tituloban 	=		'SOLES';
		$simmodena 	=		'S/.';


		return View::make('analitica/ajax/aventasxautoservicoanio',
						 [
							'meses_s' 		=> $jmeses_s,
							'ventas_s' 		=> $jventas_s,
							'ventas2_s' 	=> $jventas2_s,
							'tnc_s' 		=> $jtnc_s,
							'jprod_s' 		=> $jprod_s,
							'jcol_s' 		=> $jcol_s,
							'totalimporte_s'=> $totalimporte_s,
							'jcostos_s'		=> $jcostos_s,
							'jutilidad_s'	=> $jutilidad_s,
							'jtotal_s'		=> $jtotal_s,
							'inicio'		=> $this->inicio,
							'hoy'			=> $this->fin,
							'tituloban'		=> $tituloban,
							'simmodena'		=> $simmodena,
							'jcliente_s'	=> $jcliente_s,
							'tiporeporte'	=> $tiporeporte,
							'selec_anioini'	=> $selec_anioini,
							'selec_aniofin'	=> $selec_aniofin,
							'selec_cliente'	=> $selec_cliente,
							'total01'		=> $total01,
							'total02'		=> $total02,


							'ajax' 			=> true
						 ]);
	}



}
