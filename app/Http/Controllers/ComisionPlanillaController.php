<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBPlanillaComision,App\WEBDetallePlanillaComision,App\CMPCategoria;



use View;
use Session;
use App\Biblioteca\Osiris;
use App\Biblioteca\Funcion;
use PDO;
use Mail;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
  
class ComisionPlanillaController extends Controller
{
	public function actionCuadroComisiones($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

		$funcion 					= 	$this;

		$cuadrocomisiones 			= 	$this->funciones->cuadro_comision();

		return View::make('comision/cuadrocomisiones',
						 [
						 	'idopcion' 			=> $idopcion,
						 	'cuadrocomisiones' 	=> $cuadrocomisiones,
						 	'funcion' 			=> $funcion,
						 ]);
	}




	public function actionDescargarExcelComisiones($idopcion,$codperiodo,$codcategoriajefe,$proviene)
	{
		set_time_limit(0);

		$cabecera 		=   	WEBPlanillaComision::where('COD_PERIODO','=',$codperiodo)
								->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
								->where('TXT_PROVIENE','=',$proviene)
								->first();

		$detalleuno 		=   WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
								->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
								->where('TXT_PROVIENE','=',$proviene)
								->where('TXT_DESCRIPCION','=','CABECERA')
								->orderBy('TXT_CATEGORIA_JEFE_VENTA_ASIMILADO', 'asc')
								->get();

		$detalledos 		=   WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
								->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
								->where('TXT_PROVIENE','=',$proviene)
								->where('TXT_DESCRIPCION','=','DETALLE')
								->get();

		$funcion 									= 	$this;

		$titulo             = 	$cabecera->TXT_CATEGORIA_JEFE_VENTA.' ('.$cabecera->FEC_INICIO.' al '.$cabecera->FEC_FIN.')'.' -'.$cabecera->TXT_PROVIENE;

		if($proviene == 'MERCADO MAYORISTA' || $proviene == 'PACAS' || $proviene == 'MERCADO MAYORISTA FESTIARROZ'){

			$grupouno 		=   WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
									->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
									->where('TXT_PROVIENE','=',$proviene)
									->where('TXT_DESCRIPCION','=','DETALLE')
									->where('CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%') //PARAMETRO FALTA
									->select('NOM_EMPR','TXT_CATEGORIA_CANAL_VENTA','TXT_CATEGORIA_SUB_CANAL')
									->groupBy('NOM_EMPR')
									->groupBy('TXT_CATEGORIA_CANAL_VENTA')
									->groupBy('TXT_CATEGORIA_SUB_CANAL')
									->get();


			$grupodos 		=   WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
									->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
									->where('TXT_PROVIENE','=',$proviene)
									->where('TXT_DESCRIPCION','=','DETALLE')
									->where('CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%')
									->select('CAT_INF_NOM_CATEGORIA')
									->groupBy('CAT_INF_NOM_CATEGORIA')
									->get();


			
			$grupouno_nc 	=   WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
									->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
									->where('TXT_PROVIENE','=',$proviene)
									->where('TXT_DESCRIPCION','=','DETALLE')
									->where('CLIENTE', 'Like', '%(NOTA DE CREDITO)%')
									->select('NOM_EMPR','TXT_CATEGORIA_CANAL_VENTA','TXT_CATEGORIA_SUB_CANAL')
									->groupBy('NOM_EMPR')
									->groupBy('TXT_CATEGORIA_CANAL_VENTA')
									->groupBy('TXT_CATEGORIA_SUB_CANAL')
									->get();


			$grupodos_nc 	=   WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
									->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
									->where('TXT_PROVIENE','=',$proviene)
									->where('TXT_DESCRIPCION','=','DETALLE')
									->where('CLIENTE', 'Like', '%(NOTA DE CREDITO)%')
									->select('CAT_INF_NOM_CATEGORIA')
									->groupBy('CAT_INF_NOM_CATEGORIA')
									->get();
			
			$detalledos 		=   WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
									->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
									->where('TXT_PROVIENE','=',$proviene)
									->where('TXT_DESCRIPCION','=','DETALLE')
									->where('CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%')
									->get();


			$detalledos_nc 		=   WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
									->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
									->where('TXT_PROVIENE','=',$proviene)
									->where('TXT_DESCRIPCION','=','DETALLE')
									->where('CLIENTE', 'Like', '%(NOTA DE CREDITO)%')
									->get();


		    Excel::create($titulo, function($excel) use ($cabecera,$detalleuno,$detalledos,$funcion,$grupouno,$grupodos,$codperiodo,$codcategoriajefe,$proviene,$grupouno_nc,$grupodos_nc,$detalledos_nc) {
		        $excel->sheet('Pedidos', function($sheet) use ($cabecera,$detalleuno,$detalledos,$funcion,$grupouno,$grupodos,$codperiodo,$codcategoriajefe,$proviene,$grupouno_nc,$grupodos_nc,$detalledos_nc) {

		            $sheet->loadView('comision/excel/mercadomayorista')->with('cabecera',$cabecera)
		                                         		 			   ->with('detalleuno',$detalleuno)
		                                         		 			   ->with('detalledos',$detalledos)
		                                         		 			   ->with('grupouno',$grupouno)
		                                         		 			   ->with('grupodos',$grupodos)
		                                         		 			   ->with('codperiodo',$codperiodo)
		                                         		 			   ->with('codcategoriajefe',$codcategoriajefe)
		                                         		 			   ->with('proviene',$proviene)
		                                         		 			   ->with('grupouno_nc',$grupouno_nc)
		                                         		 			   ->with('grupodos_nc',$grupodos_nc)
		                                         		 			   ->with('detalledos_nc',$detalledos_nc)
		                                         		 			   ->with('funcion',$funcion);                                        		 
		        });
		    })->export('xls');

		}


		if($proviene == 'AUTOSERVICIOS' || $proviene == 'INCENTIVOS' || $proviene == 'AUTOSERVICIOSGC'){

			$cabecerauno 		=   WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
									->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
									->where('TXT_PROVIENE','=',$proviene)
									->where('TXT_DESCRIPCION','=','CABECERA')
									->orderBy('CAN_SALDO', 'desc')
									->first();


		    Excel::create($titulo, function($excel) use ($cabecera,$detalleuno,$detalledos,$funcion,$codperiodo,$codcategoriajefe,$proviene,$cabecerauno) {
		        $excel->sheet('Pedidos', function($sheet) use ($cabecera,$detalleuno,$detalledos,$funcion,$codperiodo,$codcategoriajefe,$proviene,$cabecerauno) {

		            $sheet->loadView('comision/excel/autoservicio')->with('cabecera',$cabecera)
		                                         		 			   ->with('detalleuno',$detalleuno)
		                                         		 			   ->with('detalledos',$detalledos)
		                                         		 			   ->with('codperiodo',$codperiodo)
		                                         		 			   ->with('codcategoriajefe',$codcategoriajefe)
		                                         		 			   ->with('proviene',$proviene)
		                                         		 			   ->with('cabecerauno',$cabecerauno)
		                                         		 			   ->with('funcion',$funcion);                                        		 
		        });
		    })->export('xls');

		}


		if($proviene == 'COBRO AUTOSERVICIO'){



		    Excel::create($titulo, function($excel) use ($cabecera,$detalleuno,$detalledos,$funcion,$codperiodo,$codcategoriajefe,$proviene) {
		        $excel->sheet('Pedidos', function($sheet) use ($cabecera,$detalleuno,$detalledos,$funcion,$codperiodo,$codcategoriajefe,$proviene) {

		            $sheet->loadView('comision/excel/cobroautoservicio')->with('cabecera',$cabecera)
		                                         		 			   ->with('detalleuno',$detalleuno)
		                                         		 			   ->with('detalledos',$detalledos)
		                                         		 			   ->with('codperiodo',$codperiodo)
		                                         		 			   ->with('codcategoriajefe',$codcategoriajefe)
		                                         		 			   ->with('proviene',$proviene)
		                                         		 			   ->with('funcion',$funcion);                                        		 
		        });
		    })->export('xls');

		}




	}



	public function actionListarComisionPlanilla($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $listaplanilla			= 	WEBPlanillaComision::select('WEB.planillacomisiones.TXT_CODIGO','WEB.planillacomisiones.COD_PERIODO')
									->groupBy('WEB.planillacomisiones.TXT_CODIGO','WEB.planillacomisiones.COD_PERIODO')
									->orderBy('WEB.planillacomisiones.COD_PERIODO', 'desc')
		    						->get();

		$funcion 					= 	$this;

		return View::make('comision/listacomisiones',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'listaplanilla' 	=> $listaplanilla,
						 	'funcion' 		=> $funcion,
						 ]);
	}


	public function actionVerDetalleComision($idopcion,$codperiodo)
	{


	    $listaplanilladetalle		= 	WEBPlanillaComision::where('WEB.planillacomisiones.COD_PERIODO','=',$codperiodo)
	    								->orderBy('TXT_CATEGORIA_JEFE_VENTA', 'asc')		
		    							->get();

		$funcion 					= 	$this;



		return View::make('comision/detallecomision',
						 [
						 	'idopcion' 				=> $idopcion,
						 	'listaplanilladetalle' 	=> $listaplanilladetalle,
						 	'funcion' 				=> $funcion,
						 	'codperiodo' 				=> $codperiodo,
						 ]);
	}


	public function actionCambiarEstadoComision($idopcion,Request $request)
	{

		if($_POST)
		{

			$msjarray  			= array();
			$respuesta 			= json_decode($request['pedido'], true);
			$cod_periodo 		= $request['cod_periodo'];
			$cod_estado_re 		= $request['cod_estado_re'];

	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;
			$usuario_autoriza_id	= '';
			$usuario_autoriza_nombre	= '';
			$usuario_ejecuta_id	= '';
			$usuario_ejecuta_nombre	= '';
			$mensaje	= '';
			$ind_elmino = 0;

			foreach($respuesta as $obj){


				$codperiodo 				= 	$obj['codperiodo'];
				$codvendedor 				= 	$obj['codvendedor'];
				$proviene 					= 	$obj['proviene'];

				$detalle  = 				WEBPlanillaComision::where('COD_PERIODO','=',$codperiodo)
											->where('COD_CATEGORIA_JEFE_VENTA','=',$codvendedor)
											->where('TXT_PROVIENE','=',$proviene)
											->first();

				$autoriza 					=   CMPCategoria::where('COD_CATEGORIA','=',$cod_estado_re)->first();


				if($cod_estado_re == 'EPP0000000000001'){

					if($detalle->COD_ESTADO != 'EPP0000000000004'){

						WEBPlanillaComision::where('COD_PERIODO','=',$codperiodo)
						->where('COD_CATEGORIA_JEFE_VENTA','=',$codvendedor)
						->where('TXT_PROVIENE','=',$proviene)
						->delete();

						WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
						->where('COD_CATEGORIA_JEFE_VENTA','=',$codvendedor)
						->where('TXT_PROVIENE','=',$proviene)
						->delete();
						$ind_elmino = 1;
						$mensaje	= 'eliminaron';

					}else{
						return Redirect::back()->withInput()->with('errorbd', 'Uno de los seleccionados ya se encuentra ejecutado no se puede eliminar');
					}

				}

				if($cod_estado_re == 'EPP0000000000003'){
					$usuario_autoriza_id	= Session::get('usuario')->id;
					$usuario_autoriza_nombre	= Session::get('usuario')->nombre;
					$mensaje	= 'autorizaron';



				}

				if($cod_estado_re == 'EPP0000000000003'){
					$mensaje	= 'generaron';
				}


				if($cod_estado_re == 'EPP0000000000004'){
					if($detalle->COD_ESTADO != 'EPP0000000000003'){
						return Redirect::back()->withInput()->with('errorbd', 'Unos de los seleccionados no esta autorizados');

					}
					$usuario_ejecuta_id	= Session::get('usuario')->id;
					$usuario_ejecuta_nombre	= Session::get('usuario')->nombre;
					$mensaje	= 'ejecutaron';


					WEBPlanillaComision::where('COD_PERIODO','=',$codperiodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$codvendedor)
					->where('TXT_PROVIENE','=',$proviene)
					->update([
								'COD_ESTADO' => $cod_estado_re
								,'TXT_ESTADO' => $autoriza->NOM_CATEGORIA 
								, 'COD_USUARIO_EJECUTA' => $usuario_ejecuta_id
								, 'TXT_USUARIO_EJECUTA' => $usuario_ejecuta_nombre
							]);

					$stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.MIGRAR_COMISIONES_A_PLANILLA ?,?,?');
	                $stmt->bindParam(1, $codperiodo ,PDO::PARAM_STR);                                   
	                $stmt->bindParam(2, $codvendedor  ,PDO::PARAM_STR);                                   
	                $stmt->bindParam(3, $proviene ,PDO::PARAM_STR);                                 
	                $stmt->execute();


				}


				if($ind_elmino==0 && $cod_estado_re != 'EPP0000000000004'){

					WEBPlanillaComision::where('COD_PERIODO','=',$codperiodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$codvendedor)
					->where('TXT_PROVIENE','=',$proviene)
					->update([
								'COD_ESTADO' => $cod_estado_re
								,'TXT_ESTADO' => $autoriza->NOM_CATEGORIA 
								, 'COD_USUARIO_AUTORIZA' => $usuario_autoriza_id
								, 'TXT_USUARIO_AUTORIZA' => $usuario_autoriza_nombre
								, 'COD_USUARIO_EJECUTA' => $usuario_ejecuta_id
								, 'TXT_USUARIO_EJECUTA' => $usuario_ejecuta_nombre
							]);

				}




			}

			return Redirect::to('/ver-detalle-comisiones/'.$idopcion.'/'.$cod_periodo)->with('bienhecho', 'Se '.$mensaje.' las comisiones');

		
		}
	}

}
