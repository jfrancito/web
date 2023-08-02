<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use View;
use Session;
use App\Biblioteca\Osiris;
use App\Biblioteca\Funcion;
use PDO;
use Mail;
use PDF;
use App\ALMCarroIngresoSalida;
use App\CMPCategoria;
use App\STDEmpresa;
  
class DespachoCarroController extends Controller
{


	public function actionListarCarros($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $fechainicio  		= 	$this->fin;
	    $fechafin  			= 	$this->fin;
		$combo_estado_carros 		= 	$this->funciones->combo_estado_carros();
		$estado_carro_defecto_id          = 	'ETC0000000000001';
		$tipo_ingreso_id          = 	'IGC0000000000001';

		$listacarros 		= 	$this->funciones->lista_carro_ingreso_salida($fechainicio,$fechafin,$estado_carro_defecto_id,$tipo_ingreso_id);
		$funcion 			= 	$this;	


		//dd($listacarros);

		return View::make('despacho/listacarros',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'listacarros' 	=> $listacarros,
						 	'combo_estado_carros' 	=> $combo_estado_carros,
						 	'estado_carro_defecto_id' 	=> $estado_carro_defecto_id,
						 	'funcion' 		=> $funcion,
						 	'fechainicio' 	=> $fechainicio,
						 	'fechafin' 		=> $fechafin,
						 ]);

	}


	public function actionAjaxListarCarros(Request $request)
	{

		$finicio 					= 	$request['finicio'];
		$estadocarro_id 			= 	$request['estadocarro_id'];
		$ffin 						= 	$request['ffin'];
		$tipo_ingreso_id            = 	'';

		if($estadocarro_id == 'ETC0000000000003'){
			$tipo_ingreso_id            = 	'IGC0000000000001';
		}


		$listacarros 		= 	$this->funciones->lista_carro_ingreso_salida($finicio,$ffin,$estadocarro_id,$tipo_ingreso_id);
		$funcion 			= 	$this;

		return View::make('despacho/ajax/alistacarros',
						 [
						 	'listacarros' 	=> $listacarros,
						 	'funcion' 		=> $funcion,
						 	'ajax' 		=> true,
						 ]);
	}



	public function actionAjaxModalDetalleCarro(Request $request)
	{

		$carro_id 					= 	$request['carro_id'];
		$carro 						= 	ALMCarroIngresoSalida::where('COD_CARRO_INGRESO_SALIDA','=',$carro_id)->first();
		$estado 					= 	CMPCategoria::where('COD_CATEGORIA','=',$carro->COD_CATEGORIA_ESTADO_CARRO)->first();
		$chofer 					= 	STDEmpresa::where('COD_EMPR','=',$carro->COD_CHOFER)->first();


		if($estado->COD_CATEGORIA == 'ETC0000000000001'){

			if($carro->TIPO_INGRESO == 'IGC0000000000002'){
				$estado_envio 					= 	CMPCategoria::where('COD_CATEGORIA','=','ETC0000000000004')->first();
			}else{
				$estado_envio 					= 	CMPCategoria::where('COD_CATEGORIA','=','ETC0000000000002')->first();
			}
			
		}else{
			if($estado->COD_CATEGORIA == 'ETC0000000000002'){
				$estado_envio 					= 	CMPCategoria::where('COD_CATEGORIA','=','ETC0000000000003')->first();
			}else{

				if($estado->COD_CATEGORIA == 'ETC0000000000003'){
					$estado_envio 					= 	CMPCategoria::where('COD_CATEGORIA','=','ETC0000000000004')->first();
				}else{
					$estado_envio 					= 	CMPCategoria::where('COD_CATEGORIA','=','ETC0000000000005')->first();
				}
			
			}

		}

		return View::make('despacho/modal/ajax/adetallecarro',
						 [
						 	'carro' 	=> $carro,
						 	'estado' 	=> $estado,
						 	'estado_envio' 	=> $estado_envio,
						 	'chofer' 	=> $chofer,
						 ]);

	}


	public function actionEditarCarroDespacho(Request $request)
	{

			$carro_id 					= 	$request['carro_id'];
			$estado_id 					= 	$request['estado_id'];
			$estado_cambiar_id 			= 	$request['estado_cambiar_id'];





			//finalizar estado	        
			$accion 						=  	'I';
			$cod_carro_ingreso_detalle 		=  	'';
			$tipo_ini_fin 					=  	'F';
			$fecha_hora 					= 	$this->fecha_hora;
			$fecha 							= 	$this->fecha_pa;

			$centro_id 					= 	Session::get('centros')->COD_CENTRO;
			$empresa_id 				= 	Session::get('empresas')->COD_EMPR;
			$cod_estado 				=  	'1';
			$cod_usuario_registro                           =       Session::get('usuario')->name;





			// 	CMP.Isp_OrdenCompra_IAE
	        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC ALM.CARRO_INGRESO_SALIDA_DETALLE_IUD ?,?,?,?,?,?,?,?,?,?,?');

	        $stmt->bindParam(1, $accion ,PDO::PARAM_STR);                                   
	        $stmt->bindParam(2, $cod_carro_ingreso_detalle  ,PDO::PARAM_STR);               
	        $stmt->bindParam(3, $carro_id ,PDO::PARAM_STR);                      	
	        $stmt->bindParam(4, $estado_id ,PDO::PARAM_STR);
	        $stmt->bindParam(5, $tipo_ini_fin ,PDO::PARAM_STR);                   			
	        $stmt->bindParam(6, $fecha_hora ,PDO::PARAM_STR); 
	        $stmt->bindParam(7, $fecha  ,PDO::PARAM_STR);                  		
	        $stmt->bindParam(8, $empresa_id ,PDO::PARAM_STR);                               
	        $stmt->bindParam(9, $centro_id ,PDO::PARAM_STR);                         		
	        $stmt->bindParam(10,$cod_estado ,PDO::PARAM_STR);                         			
			$stmt->bindParam(11,$cod_usuario_registro ,PDO::PARAM_STR);   
	        $stmt->execute();


			//iniciar estado	        

			$tipo_ini_fin 					=  	'I';

			// 	CMP.Isp_OrdenCompra_IAE
	        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC ALM.CARRO_INGRESO_SALIDA_DETALLE_IUD ?,?,?,?,?,?,?,?,?,?,?');

	        $stmt->bindParam(1, $accion ,PDO::PARAM_STR);                                   
	        $stmt->bindParam(2, $cod_carro_ingreso_detalle  ,PDO::PARAM_STR);               
	        $stmt->bindParam(3, $carro_id ,PDO::PARAM_STR);                      	
	        $stmt->bindParam(4, $estado_cambiar_id ,PDO::PARAM_STR);
	        $stmt->bindParam(5, $tipo_ini_fin ,PDO::PARAM_STR);                   			
	        $stmt->bindParam(6, $fecha_hora ,PDO::PARAM_STR); 
	        $stmt->bindParam(7, $fecha  ,PDO::PARAM_STR);                  		
	        $stmt->bindParam(8, $empresa_id ,PDO::PARAM_STR);                               
	        $stmt->bindParam(9, $centro_id ,PDO::PARAM_STR);                         		
	        $stmt->bindParam(10,$cod_estado ,PDO::PARAM_STR);                         			
			$stmt->bindParam(11,$cod_usuario_registro ,PDO::PARAM_STR);   
	        $stmt->execute();


	        $carro_i_s = ALMCarroIngresoSalida::where('COD_CARRO_INGRESO_SALIDA','=',$carro_id)->first();
	        $carro_i_s->COD_CATEGORIA_ESTADO_CARRO = $estado_cambiar_id;
	        $carro_i_s->save();

	}







}
