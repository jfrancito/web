<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBCuota;
use App\WEBDetalleCuota;
use App\CMPCategoria;
use App\WEBPeriodo;
use App\WEBPeridoBono;
use App\WEBCalculoBono;
use App\VentaBono;
use App\WEBDetalleCalculoBono;
use App\WEBEstructuraBono;

use App\Traits\BonoTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

class CalcularBonosController extends Controller
{

	use BonoTraits;


	public function actionCambiarEstadoBono($idopcion,Request $request)
	{


		if($_POST)
		{

			$msjarray  			= array();
			$respuesta 			= json_decode($request['pedido'], true);

			foreach($respuesta as $obj){

				$periodobono_id 	= 	$obj['periodobono_id'];
	        	$cuotaactual               	=   WEBPeridoBono::where('id','=',$periodobono_id)->first();
				$cuotaactual->estado_id 	=   'EPP0000000000004';
				$cuotaactual->estado_nombre =   'EJECUTADO';
				$cuotaactual->fecha_mod 	=   $this->fechaactual;
				$cuotaactual->usuario_mod 	=   Session::get('usuario')->id;
	        	$cuotaactual->save();

				$stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.MIGRAR_CALCULO_BONOS ?');
                $stmt->bindParam(1, $periodobono_id ,PDO::PARAM_STR);                                                                
                $stmt->execute();

			}
			$mensaje	= 'generaron';
			return Redirect::to('/gestion-calcular-bonos/'.$idopcion)->with('bienhecho', 'Se '.$mensaje.' los bonos');
		}
	}


	public function actionAjaxModalDetalleCalculoBono(Request $request)
	{

		$calculobono_id 		=   $request['data_calculobono_id'];
		$idopcion 				=   $request['idopcion'];

		$listadcb 				= 	WEBDetalleCalculoBono::where('calculobono_id',$calculobono_id)->get();	
		$calculobono 			=	WEBCalculoBono::where('id','=',$calculobono_id)->first();
		$funcion 				= 	$this;
		
		return View::make('bono/modal/ajax/mdetallecalculobono',
						 [

						 	'listadcb'					=> $listadcb,
						 	'calculobono'				=> $calculobono,				 	
						 	'ajax' 						=> true,						 	
						 ]);
	}



	public function actionAjaxModalConfiguracionBonoVendedor(Request $request)
	{
		$periodobono_id 		=   $request['periodobono_id'];
		$idopcion 				=   $request['idopcion'];
		$anio  					=   $this->anio;
		//jefe venta
        $array_jv_pc     		= 	$this->bn_array_jefe_venta();
		$combo_jv_pc  			= 	$this->bn_generacion_combo_array('Seleccione jefe venta', '' , $array_jv_pc);
		$defecto_jv 			= 	'';
		$periodobono 			= 	WEBPeridoBono::where('id', $periodobono_id)->first();
		$cuota_detalle_id 		= 	'';
		$funcion 				= 	$this;

		return View::make('bono/modal/ajax/mabonovendedor',
						 [		 	
						 	'periodobono' 			=> $periodobono,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'combo_jv_pc' 			=> $combo_jv_pc,
						 	'defecto_jv' 			=> $defecto_jv,

						 	'cuota_detalle_id' 		=> $cuota_detalle_id,
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionIngresarCalculoVendedor($idopcion,$idperiodobono,Request $request)
	{

		$sdidperiodobono = $idperiodobono;
	    $idperiodobono = $this->funciones->decodificarmaestra($idperiodobono);
		$periodobono 	= 	WEBPeridoBono::where('id', $idperiodobono)->first();

		if($_POST)
		{

			$cuota_detalle_id 	 		 		= 	$request['cuota_detalle_id'];
			$jefeventa_id 	 	 				= 	$request['jefeventa_id'];

			$detallecuotavendedor 				= 	WEBDetalleCuota::join('WEB.cuotas', 'WEB.cuotas.id', '=', 'WEB.detallecuotas.cuota_id')
													->where('WEB.cuotas.anio','=',$periodobono->anio)
													->where('WEB.cuotas.mes','=',$periodobono->mes)
													->where('WEB.detallecuotas.jefeventa_id','=',$jefeventa_id)
													->get();

			if(count($detallecuotavendedor)<=0){
				return Redirect::back()->withInput()->with('errorbd', 'No esxite cuota para este vendedor en este periodo');
			}


			$jefeventa 							= 	CMPCategoria::where('COD_CATEGORIA','=', $jefeventa_id)->first();

			$calculobono 						=	WEBCalculoBono::where('periodo_id','=',$periodobono->periodo_id)
													->where('jefeventa_id','=',$jefeventa_id)->first();

			if(count($calculobono)<=0){

				$iddetallecuota 							=   $this->funciones->getCreateIdMaestra('web.calculobonos');
				$cabecera            	 					=	new WEBCalculoBono;
				$cabecera->id 	     	 					=   $iddetallecuota;
				$cabecera->periodo_id 	   					=   $periodobono->periodo_id;
				$cabecera->jefeventa_id 					=   $jefeventa->COD_CATEGORIA;
				$cabecera->jefeventa_nombre 				=   $jefeventa->NOM_CATEGORIA;
				$cabecera->periodobono_id 	   				=   $idperiodobono;
				$cabecera->fecha_registro 					=   $this->fin;
				$cabecera->anio 	   						=   $periodobono->anio;
				$cabecera->mes 	   							=   $periodobono->mes;
				$cabecera->fechas 	   						=   $periodobono->fechas;
				$cabecera->estado_id 	   					=   'EPP0000000000002';
				$cabecera->estado_nombre 					=   'GENERADO';
				$cabecera->alcance_inicial 					=   '';
				$cabecera->alcance_final 					=   '';
				$cabecera->cuota 							=   0;
				$cabecera->venta 							=   0;
				$cabecera->nc 								=   0;
				$cabecera->bono 							=   0;
				$cabecera->alcance 							=   0;

				$cabecera->fecha_crea 	 					=   $this->fechaactual;
				$cabecera->usuario_crea 					=   Session::get('usuario')->id;
				$cabecera->save();	

			}else{
				$iddetallecuota = $calculobono->id;
			}

			$detallecuota 									= 	WEBDetalleCuota::join('WEB.cuotas', 'WEB.cuotas.id', '=', 'WEB.detallecuotas.cuota_id')
																->where('WEB.cuotas.anio','=',$periodobono->anio)
																->where('WEB.cuotas.mes','=',$periodobono->mes)
																->where('WEB.detallecuotas.jefeventa_id','=',$jefeventa_id)
																->first();

			$periodo 										= 	WEBPeriodo::where('id','=', $periodobono->periodo_id)->first();

			$cuota_id 										= 	$detallecuota->cuota_id;

			$listadetalleinsrt  							= 	$this->bn_lista_detalle_calculado_insert($detallecuotavendedor,$periodobono,$jefeventa,$iddetallecuota,$idperiodobono);
			
			//VENTA
			$listadetalle  									= 	$this->bn_lista_detalle_calculado($cuota_id,$periodo->fecha_inicio,$periodo->fecha_fin,$jefeventa_id);

		    while ($row = $listadetalle->fetch()){

		    	$COD_CATEGORIA_CANAL_VENTA        			=   $row['COD_CATEGORIA_CANAL_VENTA'];
		    	$COD_CATEGORIA_SUB_CANAL        			=   $row['COD_CATEGORIA_SUB_CANAL'];
		    	$VENTA        								=   floatval($row['VENTA']);
		    	$cuota        								=   floatval($row['cuota']);
		    	WEBDetalleCalculoBono::where('calculobono_id','=',$iddetallecuota)
				->where('periodo_id','=',$periodobono->periodo_id)
				->where('jefeventa_id','=',$jefeventa_id)
				->where('canal_id','=',$COD_CATEGORIA_CANAL_VENTA)
				->where('subcanal_id','=',$COD_CATEGORIA_SUB_CANAL)
		    	->update(['cuota'=>$cuota,'venta'=>$VENTA]);						
	    	}

			//NC
			$listadetalle  									= 	$this->bn_lista_detalle_nc_calculado($cuota_id,$periodo->fecha_inicio,$periodo->fecha_fin,$jefeventa_id);

		    while ($row = $listadetalle->fetch()){

		    	$COD_CATEGORIA_CANAL_VENTA        			=   $row['COD_CATEGORIA_CANAL_VENTA'];
		    	$COD_CATEGORIA_SUB_CANAL        			=   $row['COD_CATEGORIA_SUB_CANAL'];
		    	$VENTA        								=   floatval($row['VENTA']);
		    	$cuota        								=   floatval($row['cuota']);
		    	WEBDetalleCalculoBono::where('calculobono_id','=',$iddetallecuota)
				->where('periodo_id','=',$periodobono->periodo_id)
				->where('jefeventa_id','=',$jefeventa_id)
				->where('canal_id','=',$COD_CATEGORIA_CANAL_VENTA)
				->where('subcanal_id','=',$COD_CATEGORIA_SUB_CANAL)
		    	->update(['cuota'=>$cuota,'nc'=>$VENTA]);						
	    	}



			$cuota 							=   0;
			$venta 							=   0;
			$nc 							=   0;
			$bono 							=   0;
			$alcance 						=   0;
			//sumar total
			$listadcb = WEBDetalleCalculoBono::where('calculobono_id',$iddetallecuota)->get();						
	        foreach ($listadcb as $index => $item) {
				$cuota 							=   $cuota + $item->cuota;
				$venta 							=   $venta + $item->venta;
				$nc 							=   $nc + $item->nc;
	        }

	        $alcance 							=	$venta - $nc;
	        $porcentaje 						=	($alcance*100)/$cuota;
	        $estructura 						=	WEBEstructuraBono::where('alcance_inicial','<=',$porcentaje)
	        										->where('alcance_final','>=',$porcentaje)->first();

			$calculobono 						=	WEBCalculoBono::where('id','=',$iddetallecuota)->first();


			$calculobono->alcance_inicial 				=   $estructura->alcance_inicial;
			$calculobono->alcance_final 				=   $estructura->alcance_final;
			$calculobono->cuota 				=   $cuota;
			$calculobono->venta 				=   $venta;
			$calculobono->nc 					=   $nc;
			$calculobono->alcance 				=   $alcance;
			$calculobono->bono 					=   $estructura->incentivo;
			$calculobono->save();

 			return Redirect::to('/ingresar-calculo-vendedor/'.$idopcion.'/'.$sdidperiodobono)->with('bienhecho', 'Calculo '.$jefeventa->NOM_CATEGORIA.' agregada con éxito');

		}else{

			$listadetallecalculobono 	= 	WEBCalculoBono::where('periodobono_id', $idperiodobono)
											->where('activo','=',1)
											->orderBy('jefeventa_nombre', 'asc')
											->get();

			$funcion 					= 	$this;


	        return View::make('bono/gestioncalculobono', 
	        				[
	        					'periodobono'  			 	 => $periodobono,
	        					'listadetallecalculobono'  	 => $listadetallecalculobono,
	        					'funcion'  					 => $funcion,
					  			'idopcion' 					 => $idopcion
	        				]);
		}
	}





	public function actionListarPeriodoBonos($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $empresa_id 			=	Session::get('empresas')->COD_EMPR;
	    $anio  					=   $this->anio;

        $array_anio_pc     		= 	$this->bn_array_anio_periodo();
		$combo_anio_pc  		= 	$this->bn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $listabonos 			= 	$this->bn_lista_calculo_bonos($anio);
		$funcion 				= 	$this;
		

		return View::make('bono/listacalculobonos',
						 [
						 	'listabonos' 			=> $listabonos,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'anio'					=> $anio,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}



	public function actionAgregarCalculoBono($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

		if($_POST)
		{

			$anio 	 		 			= 	$request['anio'];
			$mes 	 					= 	$request['mes'];

			$cuotaexiste 				= 	WEBPeridoBono::where('mes','=', $mes)
													->where('anio','=',$anio)
													->where('estado_id','=','EPP0000000000002')
													->first();
			if(count($cuotaexiste)>0){
				return Redirect::back()->withInput()->with('errorbd', 'Ya existe un bono con el mismo periodo sin ejecutarlo');
			}


			$perido 					= 	WEBPeriodo::where('anio','=',$anio)->where('mes','=',$mes)->first();
			$codigo 					= 	$this->funciones->generar_codigo('WEB.peridobonos',8);
			$idcuota 					= 	$this->funciones->getCreateIdMaestra('WEB.peridobonos');
		
			$cabecera            	 	=	new WEBPeridoBono;
			$cabecera->id 	     	 	=   $idcuota;
			$cabecera->codigo 	   		=   $codigo;
			$cabecera->anio 			=   $anio;
			$cabecera->mes 				=   $mes;
			$cabecera->periodo_id 		=   $perido->id;
			$cabecera->fechas 			=   $perido->fecha_inicio.' al '.$perido->fecha_fin;
			$cabecera->estado_id 	   	=   'EPP0000000000002';
			$cabecera->estado_nombre 	=   'GENERADO';
			$cabecera->fecha_crea 	 	=   $this->fechaactual;
			$cabecera->usuario_crea 	=   Session::get('usuario')->id;
			$cabecera->save();
 		 	return Redirect::to('/gestion-calcular-bonos/'.$idopcion)->with('bienhecho', 'Calculo Bono '.$codigo.' registrado con exito');

		}else{


		    $empresa_id 			=	Session::get('empresas')->COD_EMPR;
		    $anio  					=   $this->anio;
		    $mes  					=   $this->mes;

	        $array_anio_pc     		= 	$this->bn_array_anio_periodo();
			$combo_anio_pc  		= 	$this->bn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	        $array_mes_pc     		= 	$this->bn_array_mes_periodo();
			$combo_mes_pc  			= 	$this->bn_generacion_combo_array('Seleccione mes', '' , $array_mes_pc);

			return View::make('bono/agregarcalculobono',
						[
							'empresa_id'  			=> $empresa_id,
							'anio'  				=> $anio,
							'mes'  					=> $mes,
							'array_anio_pc'  		=> $array_anio_pc,
							'combo_anio_pc'  		=> $combo_anio_pc,
							'array_mes_pc'  		=> $array_mes_pc,
							'combo_mes_pc'  		=> $combo_mes_pc,
						  	'idopcion'  			=> $idopcion
						]);
		}
	}




}
