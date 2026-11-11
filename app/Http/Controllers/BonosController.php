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

use App\Traits\BonoTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

class BonosController extends Controller
{

	use BonoTraits;

    public function actionAjaxGuardarEmitir(Request $request)
    {
        $cuota_id                   =   $request['cuota_id'];
        $idopcion                   =   $request['idopcion'];
        $user_id                    =   Session::get('usuario')->id;
        $sw                         =   0;
        $mensaje                    =   'Su proceso de clonacion se realizo Correctamente';  
        $error          			=   true;

        try{
            DB::beginTransaction();

        	$cuotaactual               	=   WEBCuota::where('id','=',$cuota_id)->first();
			$cuotaactual->estado_id 	=   'EPP0000000000004';
			$cuotaactual->estado_nombre =   'EJECUTADO';
			$cuotaactual->fecha_mod 	=   $this->fechaactual;
			$cuotaactual->usuario_mod 	=   Session::get('usuario')->id;
        	$cuotaactual->save();

            DB::commit();
        }catch(\Exception $ex){
            DB::rollback(); 
            $sw =   1;
            $mensaje  = $this->ge_getMensajeError($ex);
        }


        if($sw == 0) {
            $mensaje = $mensaje;
            $error   =  false;
        }
                                        
        $response[] = array(
            'error'      => $error,
            'mensaje'    => $mensaje,
        );

        if($response[0]['error']){echo json_encode($response); exit();}
        echo json_encode($response);

    }

    public function actionAjaxGuardarClonar(Request $request)
    {
        $cuota_id                   =   $request['cuota_id'];
        $idopcion                   =   $request['idopcion'];
        $cuotaclonar_id            	=   $request['cuotaclonar_id'];
        $user_id                    =   Session::get('usuario')->id;

        $cuotaclonar               	=   WEBCuota::where('id','=',$cuotaclonar_id)->first();
        $sw                         =   0;
        $mensaje                    =   'Su proceso de clonacion se realizo Correctamente';  
        $error          			=   true;

        try{
            DB::beginTransaction();
            $clonardatosgenerales       =   $this->bn_clonardatosgenerales($cuota_id,$cuotaclonar,$user_id);

        	$cuotaactual               	=   WEBCuota::where('id','=',$cuota_id)->first();
        	$cuotaactual->ind_clonado 	=	1;
        	$cuotaactual->save();
            DB::commit();
        }catch(\Exception $ex){
            DB::rollback(); 
            $sw =   1;
            $mensaje  = $this->ge_getMensajeError($ex);
        }


        if($sw == 0) {
            $mensaje = $mensaje;
            $error   =  false;
        }
                                        
        $response[] = array(
            'error'      => $error,
            'mensaje'    => $mensaje,
        );

        if($response[0]['error']){echo json_encode($response); exit();}
        echo json_encode($response);

    }



    public function actionAjaxClonar(Request $request)
    {

        $cuota_id               =   $request['cuota_id'];
        $idopcion               =   $request['idopcion'];
        $user_id                =   Session::get('usuario')->id;
        $cuotaactual            =   WEBCuota::where('id','=',$cuota_id)->first();
        if($cuotaactual->ind_clonado == 1){
        	$array_pb_pc = array();
        }else{
        	$array_pb_pc     		= 	$this->bn_array_periodo_bono($cuota_id);        	
        }

		$combo_pb_pc  			= 	$this->bn_generacion_combo_array('Seleccione bono a clonar', '' , $array_pb_pc);

        return View::make('bono/modal/ajax/amclonar',
                         [          
                            'combo_pb_pc' 		=> $combo_pb_pc,
                            'cuota_id'          => $cuota_id,
                            'idopcion'          => $idopcion, 
                            'ajax'          	=> true,                      
                         ]);

    }

    public function actionAjaxModalEmitirCuota(Request $request)
    {

        $cuota_id               =   $request['cuota_id'];
        $idopcion               =   $request['idopcion'];
        $user_id                =   Session::get('usuario')->id;
		$cuota 					= 	WEBCuota::where('id', $cuota_id)->first();


        return View::make('bono/modal/ajax/amemitir',
                         [          
                            'cuota_id'          => $cuota_id,
                            'cuota'          => $cuota,
                            'idopcion'          => $idopcion, 
                            'ajax'          	=> true,                      
                         ]);

    }




	public function actionAjaxModalModificarConfiguracionCuota(Request $request)
	{
		$cuota_id 					=   $request['cuota_id'];
		$detalle_cuota_id 			=   $request['detalle_cuota_id'];
		$idopcion 					=   $request['idopcion'];
		$anio  						=   $this->anio;

		$cuotadetalle				= 	WEBDetalleCuota::where('id', $detalle_cuota_id)->first();
		$cuota 						= 	WEBCuota::where('id', $cuota_id)->first();

		//jefe venta
        $array_jv_pc     			= 	$this->bn_array_jefe_venta();
		$combo_jv_pc  				= 	$this->bn_generacion_combo_array('Seleccione jefe venta', '' , $array_jv_pc);
		$defecto_jv 				= 	$cuotadetalle->jefeventa_id;

		//canal
        $array_canal_pc     		= 	$this->bn_array_canal();
		$combo_canal_pc  			= 	$this->bn_generacion_combo_array('Seleccione canal', '' , $array_canal_pc);
		$defecto_canal 				=	$cuotadetalle->canal_id;

		//subcanal
		$array_sub_canal 	    	= 	$this->bn_array_sub_canal($cuotadetalle->canal_id);
		$combo_sub_canal_pc  		= 	$this->bn_generacion_combo_array('Seleccione subcanal', '' , $array_sub_canal);
		$defecto_sub_canal			=	$cuotadetalle->subcanal_id;

		$cuota_detalle_id 			= 	$detalle_cuota_id;
		$funcion 					= 	$this;

		return View::make('bono/modal/ajax/macuota',
						 [		 	
						 	'cuota' 				=> $cuota,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,

						 	'combo_jv_pc' 			=> $combo_jv_pc,
						 	'combo_canal_pc' 		=> $combo_canal_pc,
						 	'combo_sub_canal_pc' 	=> $combo_sub_canal_pc,

						 	'defecto_jv' 			=> $defecto_jv,
						 	'defecto_canal' 		=> $defecto_canal,
						 	'defecto_sub_canal' 	=> $defecto_sub_canal,

						 	'cuota_detalle_id' 		=> $cuota_detalle_id,
						 	'cuotadetalle' 			=> $cuotadetalle,
						 	'ajax' 					=> true,							 	
						 ]);
	}


	public function actionIngresarCuota($idopcion,$idcuota,Request $request)
	{

		$sdidcuota = $idcuota;
	    $idcuota = $this->funciones->decodificarmaestra($idcuota);
		$cuota 	= 	WEBCuota::where('id', $idcuota)->first();

		if($_POST)
		{

			$cuota_detalle_id 	 		 		= 	$request['cuota_detalle_id'];
			$jefeventa_id 	 	 				= 	$request['jefeventa_id'];
			$canal_id 	 						= 	$request['canal_id'];
			$subcanal_id 	 					= 	$request['subcanal_id'];
			$cuota 	 							= 	floatval(str_replace(",", "", $request['cuota']));

			$detallecuotaexiste 				= 	WEBDetalleCuota::where('id','<>', $cuota_detalle_id)
													->where('cuota_id','=',$idcuota)
													->where('jefeventa_id','=',$jefeventa_id)
													->where('canal_id','=',$canal_id)
													->where('subcanal_id','=',$subcanal_id)
													->first();

			if(count($detallecuotaexiste)>0){
				return Redirect::back()->withInput()->with('errorbd', 'Ya existe una cuota con estos parametros');
			}


			$jefeventa 							= 	CMPCategoria::where('COD_CATEGORIA','=', $jefeventa_id)->first();
			$canal 								= 	CMPCategoria::where('COD_CATEGORIA','=', $canal_id)->first();
			$subcanal 							= 	CMPCategoria::where('COD_CATEGORIA','=', $subcanal_id)->first();

			//agregar cuenta contable
			if(trim($cuota_detalle_id)==''){
				
				$iddetallecuota 							=   $this->funciones->getCreateIdMaestra('web.detallecuotas');
				$cabecera            	 					=	new WEBDetalleCuota;
				$cabecera->id 	     	 					=   $iddetallecuota;
				$cabecera->cuota_id 	   					=   $idcuota;
				$cabecera->jefeventa_id 					=   $jefeventa->COD_CATEGORIA;
				$cabecera->jefeventa_nombre 				=   $jefeventa->NOM_CATEGORIA;
				$cabecera->canal_id 						=   $canal->COD_CATEGORIA;
				$cabecera->canal_nombre 					=   $canal->NOM_CATEGORIA;
				$cabecera->subcanal_id 						=   $subcanal->COD_CATEGORIA;
				$cabecera->subcanal_nombre 					=   $subcanal->NOM_CATEGORIA;
				$cabecera->cuota 							=   $cuota;
				$cabecera->empresa_id 	 					=   Session::get('empresas')->COD_EMPR;
				$cabecera->fecha_crea 	 					=   $this->fechaactual;
				$cabecera->usuario_crea 					=   Session::get('usuario')->id;
				$cabecera->save();

			}else{
				//modificar cuenta contable
				$detallecuota								= 	WEBDetalleCuota::where('id', $cuota_detalle_id)->first();
				$detallecuota->cuota 	   					=   $cuota;
				$detallecuota->jefeventa_id 				=   $jefeventa->COD_CATEGORIA;
				$detallecuota->jefeventa_nombre 			=   $jefeventa->NOM_CATEGORIA;
				$detallecuota->canal_id 					=   $canal->COD_CATEGORIA;
				$detallecuota->canal_nombre 				=   $canal->NOM_CATEGORIA;
				$detallecuota->subcanal_id 					=   $subcanal->COD_CATEGORIA;
				$detallecuota->subcanal_nombre 				=   $subcanal->NOM_CATEGORIA;
				$detallecuota->fecha_mod 	 				=   $this->fechaactual;
				$detallecuota->usuario_mod 					=   Session::get('usuario')->id;
				$detallecuota->save();
			}

 			return Redirect::to('/ingresar-cuotas/'.$idopcion.'/'.$sdidcuota)->with('bienhecho', 'Cuota '.$cuota.' agregada con éxito');

		}else{


			$listadetallecuota 	= 	WEBDetalleCuota::where('cuota_id', $idcuota)
											->where('activo','=',1)
											->orderBy('jefeventa_nombre', 'asc')
											->orderBy('canal_nombre','asc')
											->get();
			$funcion 					= 	$this;


	        return View::make('bono/gestioncuota', 
	        				[
	        					'cuota'  			 	     => $cuota,
	        					'listadetallecuota'  		 => $listadetallecuota,
	        					'funcion'  					 => $funcion,
					  			'idopcion' 					 => $idopcion
	        				]);
		}
	}



	public function actionAjaxComboSubCanalxCanal(Request $request)
	{

		$canal_id 				=   $request['canal_id'];
		$anio  					=   $this->anio;

		$array_sub_canal 	    = 	$this->bn_array_sub_canal($canal_id);
		$combo_sub_canal_pc  	= 	$this->bn_generacion_combo_array('Seleccione subcanal', '' , $array_sub_canal);
		$defecto_sub_canal		=	''; 

		return View::make('bono/combo/subcanal',
						 [		 	
						 	'combo_sub_canal_pc' 	=> $combo_sub_canal_pc,
						 	'defecto_sub_canal' 	=> $defecto_sub_canal,
						 	'ajax' 					=> true,						 	
						 ]);
	}	


	public function actionAjaxModalConfiguracionCuotaDetalle(Request $request)
	{
		$cuota_id 				=   $request['cuota_id'];
		$idopcion 				=   $request['idopcion'];
		$anio  					=   $this->anio;

		//jefe venta
        $array_jv_pc     		= 	$this->bn_array_jefe_venta();
		$combo_jv_pc  			= 	$this->bn_generacion_combo_array('Seleccione jefe venta', '' , $array_jv_pc);
		$defecto_jv 			= 	'';

		//canal
        $array_canal_pc     	= 	$this->bn_array_canal();
		$combo_canal_pc  		= 	$this->bn_generacion_combo_array('Seleccione canal', '' , $array_canal_pc);
		$defecto_canal 			=	'';

		//subcanal
		$combo_sub_canal_pc  	= 	$this->bn_generacion_combo_array('Seleccione subcanal', '' , array());
		$defecto_sub_canal		=	''; 

		$cuota 					= 	WEBCuota::where('id', $cuota_id)->first();
		$cuota_detalle_id 		= 	'';
		$funcion 				= 	$this;

		return View::make('bono/modal/ajax/macuota',
						 [		 	
						 	'cuota' 				=> $cuota,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,

						 	'combo_jv_pc' 			=> $combo_jv_pc,
						 	'combo_canal_pc' 		=> $combo_canal_pc,
						 	'combo_sub_canal_pc' 	=> $combo_sub_canal_pc,

						 	'defecto_jv' 			=> $defecto_jv,
						 	'defecto_canal' 		=> $defecto_canal,
						 	'defecto_sub_canal' 	=> $defecto_sub_canal,
						 	'cuota_detalle_id' 		=> $cuota_detalle_id,
						 	'ajax' 					=> true,						 	
						 ]);
	}






	public function actionListarBonos($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $empresa_id 			=	Session::get('empresas')->COD_EMPR;
	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->bn_array_anio_periodo();
		$combo_anio_pc  		= 	$this->bn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $listabonos 			= 	$this->bn_lista_bonos($anio);
		$funcion 				= 	$this;
		

		return View::make('bono/listabonos',
						 [
						 	'listabonos' 			=> $listabonos,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'anio'					=> $anio,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}



	public function actionAgregarBono($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

		if($_POST)
		{

			$anio 	 		 			= 	$request['anio'];
			$mes 	 					= 	$request['mes'];

			$cuotaexiste 				= 	WEBCuota::where('mes','=', $mes)
													->where('anio','=',$anio)
													->where('estado_id','=','EPP0000000000002')
													->first();
			if(count($cuotaexiste)>0){
				return Redirect::back()->withInput()->with('errorbd', 'Ya existe un bono con el mismo periodo sin ejecutarlo');
			}


			$codigo 					= 	$this->funciones->generar_codigo('WEB.cuotas',8);
			$idcuota 					= 	$this->funciones->getCreateIdMaestra('WEB.cuotas');
		
			$cabecera            	 	=	new WEBCuota;
			$cabecera->id 	     	 	=   $idcuota;
			$cabecera->codigo 	   		=   $codigo;
			$cabecera->fecha_registro 	=   $this->fin;
			$cabecera->anio 			=   $anio;
			$cabecera->mes 				=   $mes;
			$cabecera->estado_id 	   	=   'EPP0000000000002';
			$cabecera->estado_nombre 	=   'GENERADO';
			$cabecera->empresa_id 	 	=   Session::get('empresas')->COD_EMPR;
			$cabecera->fecha_crea 	 	=   $this->fechaactual;
			$cabecera->usuario_crea 	=   Session::get('usuario')->id;
			$cabecera->save();
 
 		 	return Redirect::to('/gestion-de-bonos/'.$idopcion)->with('bienhecho', 'Bono '.$codigo.' registrado con exito');

		}else{


		    $empresa_id 			=	Session::get('empresas')->COD_EMPR;
		    $anio  					=   $this->anio;
		    $mes  					=   $this->mes;

	        $array_anio_pc     		= 	$this->bn_array_anio_periodo();
			$combo_anio_pc  		= 	$this->bn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	        $array_mes_pc     		= 	$this->bn_array_mes_periodo();
			$combo_mes_pc  		= 	$this->bn_generacion_combo_array('Seleccione mes', '' , $array_mes_pc);

			return View::make('bono/agregarbono',
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
