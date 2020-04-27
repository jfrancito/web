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
use App\WEBOrdenDespacho,App\WEBDetalleOrdenDespacho,App\CMPOrden,App\WEBListaCliente,App\ALMProducto,App\CMPCategoria;
use App\WEBViewDetalleOrdenDespacho;


class PedidoDespachoController extends Controller
{


	public function actionGestionOrdenDespacho($idopcion,$idordendespacho)
	{

		$idordendespacho_en 		= 	$idordendespacho;
		$idordendespacho 			= 	$this->funciones->decodificarmaestra($idordendespacho);

	    $ordendespacho 				=   WEBOrdenDespacho::where('id','=',$idordendespacho)->first();
		$funcion 					= 	$this;


		return View::make('despacho/gestionordendespacho',
						 [
						 	'ordendespacho' 						=> $ordendespacho,
						 	'funcion' 								=> $funcion,
						 	'idopcion' 								=> $idopcion,
						 	'idordendespacho' 						=> $idordendespacho,
						 ]);

	}






	public function actionAjaxPedidoCrearUpdatePedidoDespachoCentro(Request $request)
	{


		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();
		$grupo 								= 	$request['grupo'];
		$correlativo 						= 	$request['correlativo'];
		$numero_mobil 						= 	$request['numero_mobil'];		
		$opcion_id 							= 	$request['opcion_id'];
		$data_producto_pedido 				= 	$request['data_producto_pedido'];
		$array_data_producto_despacho 		= 	$request['data_producto_despacho'];


		//actualizar el array con nuevos valores(fecha de entrega) 
		foreach($array_detalle_producto_request as $item => $row) {

			$muestra 				= 	0.00;
	        $precio 				= 	0.00;
			$kilos 					= 	0.00;
	        $cantidad_sacos 		= 	0.00;
	        $palets 				= 	0.00;
	        $cantidad_ate 			= 	0.00;


	        $centro_atender_id 		= 	'';
	        $centro_atender_txt 	= 	'';
	        $empresa_atender_id 	= 	'';
	        $empresa_atender_txt 	= 	'';
	        $producto_id 			= 	'';


			foreach($array_data_producto_despacho as $key => $obj){

				if($row['correlativo'] == $obj['data_correlativo']){

					$muestra 			=  $obj['muestra'];
					$precio 			=  $obj['precio'];
					$centro_atender_id 	=  $obj['centro_atender_id'];
					$producto_id 		=  $obj['producto_id'];
					$cantidad_ate 		=  (float)$obj['precio'];

					//calculo de kilos,cantidad_sacos,palets
					$producto 							= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
					$kilos 								=   $cantidad_ate*$producto->CAN_PESO_MATERIAL;
					$cantidad_sacos						= 	$cantidad_ate/$producto->CAN_BOLSA_SACO;
					$palets 							= 	$cantidad_sacos/$producto->CAN_SACO_PALET;


					$data_centro 		=  $this->funciones->data_centro($centro_atender_id);
					if(count($data_centro)>0){$centro_atender_txt = $data_centro->NOM_CENTRO;}

					$data_empresa 		=  $this->funciones->data_empresa_despacho_por_centro($centro_atender_id);
					if(count($data_empresa)>0){
						$empresa_atender_id 	= $data_empresa->COD_EMPR;
						$empresa_atender_txt 	= $data_empresa->NOM_EMPR;
					}

				}
			}


			$array_detalle_producto_request[$item]['muestra'] 				= $muestra;
			$array_detalle_producto_request[$item]['cantidad'] 				= $precio;
			$array_detalle_producto_request[$item]['kilos'] 				= $kilos;
			$array_detalle_producto_request[$item]['cantidad_sacos'] 		= $cantidad_sacos;
			$array_detalle_producto_request[$item]['palets'] 				= $palets;
			$array_detalle_producto_request[$item]['centro_atender_id'] 	= $centro_atender_id;
			$array_detalle_producto_request[$item]['centro_atender_txt'] 	= $centro_atender_txt;
			$array_detalle_producto_request[$item]['empresa_atender_id'] 	= $empresa_atender_id;
			$array_detalle_producto_request[$item]['empresa_atender_txt'] 	= $empresa_atender_txt;			

	    } 


	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_producto_request as $key => $item) {
			array_push($array_detalle_producto,$item);
		}

		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);


		$funcion 					= 	$this;
		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);

	}



	public function actionCrearPedidoDepacho($idopcion,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

		if($_POST)
		{


			try{

				DB::beginTransaction();

				$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
				$idordendespacho			= 	$this->funciones->getCreateIdMaestra('WEB.ordendespachos');
				$codigo 					= 	$this->funciones->generar_codigo('WEB.ordendespachos',8);

				//PEDIDO
				$cabecera            	 	=	new WEBOrdenDespacho;
				$cabecera->id 	     	 	=  	$idordendespacho;
				$cabecera->estado_id 	    =  	'EPP0000000000002';
				$cabecera->codigo 	    	=  	$codigo;
				$cabecera->fecha_crea 	 	=   $this->fechaactual;
				$cabecera->fecha_orden 	 	=   $this->fecha_sin_hora;
				$cabecera->usuario_crea 	=   Session::get('usuario')->id;
				$cabecera->empresa_id 		=   Session::get('empresas')->COD_EMPR;
				$cabecera->centro_id 		=   Session::get('centros')->COD_CENTRO;
				$cabecera->save();


				foreach($array_detalle_producto_request as $key => $row) {

					$cantidad_atender 					= 	(float)$row['cantidad'] + (float)$row['muestra'];

					//calculo de kilos,cantidad_sacos,palets
					$producto 							= 	ALMProducto::where('COD_PRODUCTO','=',$row['producto_id'])->first();
					$kilos_atender 						=   $cantidad_atender*$producto->CAN_PESO_MATERIAL;
					$cantidad_sacos_atender				= 	$cantidad_atender/$producto->CAN_BOLSA_SACO;
					$palets_atende 						= 	$cantidad_sacos_atender/$producto->CAN_SACO_PALET;


					$iddetalleordendespacho				= 	$this->funciones->getCreateIdMaestra('WEB.detalleordendespachos');
					$detalle            	 			=	new WEBDetalleOrdenDespacho;

					$detalle->id 	     	 			=  	$iddetalleordendespacho;
					$detalle->ordendespacho_id 			=  	$idordendespacho;
					$detalle->nro_orden_cen 			=  	$row['orden_cen'];
					$detalle->fecha_pedido 				=  	$row['fecha_pedido'];
					$detalle->fecha_entrega 			=  	$row['fecha_entrega'];
					$detalle->muestra 					=  	$row['muestra'];
					$detalle->cantidad 					=  	$row['cantidad'];
					$detalle->cantidad_atender 			=  	$cantidad_atender;

					$detalle->kilos 					=  	$row['kilos'];
					$detalle->cantidad_sacos 			=  	$row['cantidad_sacos'];
					$detalle->palets 					=  	$row['palets'];

					$detalle->presentacion_producto 	=  	$row['presentacion_producto'];
					$detalle->grupo 					=  	$row['grupo'];
					$detalle->grupo_orden 				=  	$row['grupo_orden'];
					$detalle->grupo_movil 				=  	$row['grupo_movil'];
					$detalle->grupo_orden_movil 		=  	$row['grupo_orden_movil'];
					$detalle->correlativo 				=  	$row['correlativo'];
					$detalle->tipo_grupo_oc 			=  	$row['tipo_grupo_oc'];
					$detalle->fecha_crea 	 			=   $this->fechaactual;
					$detalle->usuario_crea 				=   Session::get('usuario')->id;
					$detalle->unidad_medida_id 			=  	$row['unidad_medida_id'];
					$detalle->modulo 					=  	'generar_pedido';
					$detalle->cliente_id 				=  	$row['empresa_cliente_id'];
					$detalle->orden_id 					=  	$row['orden_id'];
					$detalle->producto_id 				=  	$row['producto_id'];
					$detalle->empresa_id 				=   Session::get('empresas')->COD_EMPR;
					$detalle->centro_id 				=   Session::get('centros')->COD_CENTRO;
					$detalle->estado_id 	    		=  	'EPP0000000000002';
					$detalle->estado_gruia_id 	    	=  	'EPP0000000000002';
					$detalle->documento_guia_id 	    =  	'';
					$detalle->nro_serie 	    		=  	'';
					$detalle->nro_documento 	    	=  	'';
					$detalle->centro_atender_id 		=  	$row['centro_atender_id'];
					$detalle->centro_atender_txt 		=  	$row['centro_atender_txt'];
					$detalle->empresa_atender_id 		=  	$row['empresa_atender_id'];
					$detalle->empresa_atender_txt 		=  	$row['empresa_atender_txt'];
					$detalle->usuario_responsable_id 	=  	'';
					$detalle->usuario_responsable_txt 	=  	'';

					$detalle->kilos_atender 			=  	$kilos_atender;
					$detalle->cantidad_sacos_atender 	=  	$cantidad_sacos_atender;
					$detalle->palets_atender 			=  	$palets_atende;
					$detalle->fecha_carga 				=  	'';
					$detalle->fecha_recepcion 			=  	'';


					$detalle->save();

			    }



			    //recalcular numero de guias
			    //agrupar cuantos mobiles hay

				$group_mobiles 							=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$idordendespacho)
															->where('activo','=','1')
															->select(DB::raw('grupo_movil'))
															->groupBy('grupo_movil')
															->get();

				foreach($group_mobiles as $index => $item){

					$detalle_orden_despacho 			=	WEBViewDetalleOrdenDespacho::where('ordendespacho_id','=',$idordendespacho)
															->where('grupo_movil','=',$item->grupo_movil)
															->orderBy('id', 'asc')
															->get();

					$cantidad_productos 				=   count($detalle_orden_despacho);
					$parte_entera_division 				=   floor($cantidad_productos/8);
					$resto_division 					= 	$cantidad_productos%8;
					$conteo_productos 					=   1;
					$grupo_guia 						= 	0;
					$grupo_orden_guia 					=	0;
					$contador_por_producto 				=   1;


					if($resto_division>0){
						$parte_entera_division 			= 	$parte_entera_division + 1;
					}

					foreach($detalle_orden_despacho as $indexd => $itemd){

						if($conteo_productos < $parte_entera_division){
							$grupo_guia 				= 	$conteo_productos;
							$grupo_orden_guia 			= 	8;
						}else{

							if($resto_division==0){
								$grupo_guia 			= 	$conteo_productos;
								$grupo_orden_guia 		= 	8;
							}else{
								$grupo_guia 			= 	$conteo_productos;
								$grupo_orden_guia 		= 	$resto_division;
							}

						}

						$array_detalle_orden_despacho_id 		= 	explode(",", substr($itemd->id, 0, -1));
						foreach ($array_detalle_orden_despacho_id as $values)
						{
							$detalleordendespacho               	=   WEBDetalleOrdenDespacho::where('id','=',$values)->first();
							$detalleordendespacho->grupo_guia 	    =  	$grupo_guia;
							$detalleordendespacho->grupo_orden_guia =  	$grupo_orden_guia;
							$detalleordendespacho->fecha_mod 		=  	$this->fechaactual;
							$detalleordendespacho->usuario_mod 		=  	Session::get('usuario')->id;
							$detalleordendespacho->save();
						}


						$contador_por_producto 			= 	$contador_por_producto + 1;

						if($contador_por_producto>8){
							$conteo_productos 			=   $conteo_productos + 1;
							$contador_por_producto 		= 	1;
						}

					}


				}


				DB::commit();
	 			return Redirect::to('/gestion-de-generar-pedido/'.$idopcion)->with('bienhecho', 'Pedido para despacho '.$codigo.' registrado con exito');


			}catch(Exception $ex){
				DB::rollback();
				return Redirect::to('/gestion-de-generar-pedido/'.$idopcion)->with('errorbd', 'Ocurrio un error inesperado. Porfavor contacte con el administrador del sistema : '.$ex);	
			}

		}else{
                                   
			$comboclientes				= 	$this->funciones->combo_clientes_cuenta();
			$grupo						= 	0;
			$correlativo				= 	0;
			$numero_mobil				= 	0;

			$combo_lista_centros 		= 	$this->funciones->combo_lista_centro();


			return View::make('despacho/crearordenpedidodespacho',
							 [
							 	'idopcion' 				=> $idopcion,
								'comboclientes' 		=> $comboclientes,						
								'inicio'				=> $this->inicio,
								'hoy'					=> $this->fin,
							 	'grupo' 				=> $grupo,
							 	'correlativo' 			=> $correlativo,
							 	'combo_lista_centros' 	=> $combo_lista_centros,
							 	'numero_mobil' 			=> $numero_mobil,
							 ]);
		}
	}



	public function actionAjaxModificarConfiguracionDelProducto(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();
		$correlativo 						= 	$request['correlativo'];
		$grupo 								= 	$request['grupo'];
		$numero_mobil 						= 	$request['numero_mobil'];

		$cantidad_bolsa_saco 				= 	$request['cantidad_bolsa_saco'];
		$cantidad_saco_palet 				= 	$request['cantidad_saco_palet'];
		$producto_id 						= 	$request['producto_id'];
		$producto 							= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
		$opcion_id 							= 	$request['opcion_id'];

		$producto->CAN_BOLSA_SACO 			= 	$cantidad_bolsa_saco;
		$producto->CAN_SACO_PALET 			= 	$cantidad_saco_palet;
		$producto->save();


		//actualizar el array con nuevos valores(configuracion del producto) 
		foreach($array_detalle_producto_request as $key => $row) {
            if($row['producto_id'] == $producto_id) {
				//calculo de kilos,cantidad_sacos,palets
				$kilos 							=   $row['cantidad']*$producto->CAN_PESO_MATERIAL;
				$cantidad_sacos					= 	$row['cantidad']/$producto->CAN_BOLSA_SACO;
				$palets 						= 	$cantidad_sacos/$producto->CAN_SACO_PALET;
				//
				$array_detalle_producto_request[$key]['kilos'] 				= $kilos;
				$array_detalle_producto_request[$key]['cantidad_sacos'] 	= $cantidad_sacos;
				$array_detalle_producto_request[$key]['palets'] 			= $palets;
            }
	    } 

	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_producto_request as $key => $item) {
			array_push($array_detalle_producto,$item);
		}

		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);


		$funcion 					= 	$this;
		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);
	}



	public function actionAjaxModalConfiguracionProductoCantidad(Request $request)
	{

		$producto_id 		= 	$request['producto_id'];
		$funcion 			= 	$this;
		$producto 			= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
		$unidad_medida 		= 	CMPCategoria::where('COD_CATEGORIA','=',$producto->COD_CATEGORIA_UNIDAD_MEDIDA)->first();


		return View::make('despacho/modal/ajax/configuracionproductocantidad',
						 [
						 	'producto' 			=> $producto,
						 	'unidad_medida' 	=> $unidad_medida,
						 	'funcion' 			=> $funcion
						 ]);
	}


	public function actionAjaxPedidoModificarFechaEntrega(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();
		$data_producto_pedido 				= 	$request['data_producto_pedido'];
		$fechadeentrega 					=   date_format(date_create($request['fechadeentrega']), 'd-m-Y');
		$correlativo 						= 	$request['correlativo'];
		$grupo 								= 	$request['grupo'];
		$numero_mobil 						= 	$request['numero_mobil'];		
		$opcion_id 							= 	$request['opcion_id'];


		//actualizar el array con nuevos valores(fecha de entrega) 
		foreach($array_detalle_producto_request as $key => $row) {
			$encontro = array_search($row['correlativo'], array_column($data_producto_pedido, 'correlativo'));
		    if (!is_bool($encontro)){
		    	$array_detalle_producto_request[$key]['fecha_entrega'] = $fechadeentrega;
		    }
	    } 

	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_producto_request as $key => $item) {
			array_push($array_detalle_producto,$item);
		}

		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);


		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);
		$funcion 					= 	$this;

		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);
	}

	
	public function actionAjaxModificarMuestraProductoFila(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();
		$muestra 							= 	(float)$request['muestra'];
		$fila 								= 	$request['fila'];
		$producto_id 						= 	$request['producto_id'];
		$correlativo 						= 	$request['correlativo'];
		$grupo 								= 	$request['grupo'];
		$opcion_id 							= 	$request['opcion_id'];
		$numero_mobil 						= 	$request['numero_mobil'];


		//actualizar el array con nuevos valores
		foreach ($array_detalle_producto_request as $key => $item) {
            if((int)$item['correlativo'] == $fila) {
				$array_detalle_producto_request[$key]['muestra'] 		= $muestra;
            }
		}

	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_producto_request as $key => $item) {
			array_push($array_detalle_producto,$item);
		}

		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);

		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);
		$funcion 					= 	$this;

		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);
	}





	public function actionAjaxModificarCantidadProductoFila(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();
		$cantidad 							= 	(float)$request['cantidad'];
		$fila 								= 	$request['fila'];
		$producto_id 						= 	$request['producto_id'];
		$correlativo 						= 	$request['correlativo'];
		$grupo 								= 	$request['grupo'];
		$numero_mobil 						= 	(int)$request['numero_mobil'];
		$opcion_id 							= 	$request['opcion_id'];


		//calculo de kilos,cantidad_sacos,palets
		$producto 							= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
		$kilos 								=   $cantidad*$producto->CAN_PESO_MATERIAL;
		$cantidad_sacos						= 	$cantidad/$producto->CAN_BOLSA_SACO;
		$palets 							= 	$cantidad_sacos/$producto->CAN_SACO_PALET;
		//

		//actualizar el array con nuevos valores
		foreach ($array_detalle_producto_request as $key => $item) {
            if((int)$item['correlativo'] == $fila) {

				$array_detalle_producto_request[$key]['cantidad'] 		= $cantidad;
				$array_detalle_producto_request[$key]['kilos'] 			= $kilos;
				$array_detalle_producto_request[$key]['cantidad_sacos'] = $cantidad_sacos;
				$array_detalle_producto_request[$key]['palets'] 		= $palets;

            }
		}

	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_producto_request as $key => $item) {
			array_push($array_detalle_producto,$item);
		}

		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);

		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);
		$funcion 					= 	$this;

		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil, 	
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);
	}


	public function actionAjaxPedidoEliminarFila(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();
		$grupo 								= 	(int)$request['grupo'];
		$numero_mobil 						= 	(int)$request['numero_mobil'];
		$correlativo 						= 	(int)$request['correlativo'];
		$fila 								= 	$request['fila'];
		$opcion_id 							= 	$request['opcion_id'];


		$disminuir 							= 	0;
		$grupo_oc							= 	"";
		$orden_cen							= 	"";
		$disminuir_gm 						= 	0;
		$grupo_movil						= 	"";
		$grupo_orden_movil					= 	0;


		//eliminar la fila del array
		foreach ($array_detalle_producto_request as $key => $item) {
            if((int)$item['correlativo'] == $fila) {

                unset($array_detalle_producto_request[$key]);

                //guardamos para luego disminuir
				if($item['tipo_grupo_oc'] == 'oc_grupo'){	
					$disminuir 	= 	1;
					$grupo_oc 	= 	$item['grupo'];
					$orden_cen 	= 	$item['orden_cen'];			
				}
				if($item['grupo_movil'] > 0){	
					$disminuir_gm 		= 	1;
					$grupo_movil 		= 	$item['grupo_movil'];
					$grupo_orden_movil	= 	$item['grupo_orden_movil'];
				}
            }
		}

		if($disminuir>0){	
			// dismuir la cantidad de rowspan
			foreach ($array_detalle_producto_request as $key => $item) {
		        if($item['grupo'] == $grupo_oc && $item['orden_cen'] == $orden_cen) {
		        	$array_detalle_producto_request[$key]['grupo_orden'] = (int)$array_detalle_producto_request[$key]['grupo_orden'] -1;
		        }
			}
		}


		//disminuir mobil cantidad
		if($disminuir_gm>0){
			foreach ($array_detalle_producto_request as $key => $item) {
		        if($item['grupo_movil'] == $grupo_movil) {

		        	//print_r($array_detalle_producto_request[$key]['grupo_orden_novil']);
		        	$array_detalle_producto_request[$key]['grupo_orden_movil'] = (int)$grupo_orden_movil -1;

		        }
			}
		}

	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_producto_request as $key => $item) {
			array_push($array_detalle_producto,$item);
		}


		$numero_mobil  												= 	0;
		$numero_mobil 												= 	$this->funciones->mayor_grupo_mobil($array_detalle_producto);


		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);

		$funcion 					= 	$this;

		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);

		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,						 	
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);
	}


	public function actionAjaxPedidoCrearMovil(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$data_producto_pedido 				= 	$request['data_producto_pedido'];
		$array_detalle_producto 			=	array();
		$grupo 								= 	(int)$request['grupo'];
		$correlativo 						= 	(int)$request['correlativo'];
		$opcion_id 							= 	$request['opcion_id'];
		$numero_mobil 						= 	(int)$request['numero_mobil'];



		//limpiar centro y empresa
		foreach ($array_detalle_producto_request as $key => $item) {
				$array_detalle_producto_request[$key]['centro_atender_id'] 		= '';
				$array_detalle_producto_request[$key]['centro_atender_txt'] 	= '';
				$array_detalle_producto_request[$key]['empresa_atender_id'] 	= '';
				$array_detalle_producto_request[$key]['empresa_atender_txt'] 	= '';
				$array_detalle_producto_request[$key]['fecha_entrega'] 			= '';
		}


		//el mayor valor numero de movil
		$grupo_mobil_mayor 					=	0;
		foreach ($array_detalle_producto_request as $key => $item) {
            if((int)$item['grupo_movil'] > $grupo_mobil_mayor) {
                $grupo_mobil_mayor = (int)$item['grupo_movil'];
            }
		}


		//agregar el numero de movil y agrupar 	 
		foreach($array_detalle_producto_request as $key => $row) {
			$encontro = array_search($row['correlativo'], array_column($data_producto_pedido, 'correlativo'));
		    if (!is_bool($encontro)){
		    	$array_detalle_producto_request[$key]['grupo_movil'] = $grupo_mobil_mayor + 1;
		    }
	    } 


	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_producto_request as $key => $item) {
			array_push($array_detalle_producto,$item);
		}


		// ordenar el array por grupo movil
		$array_detalle_producto = 	$this->funciones->ordermultidimensionalarray($array_detalle_producto,'grupo_movil',false);
		$nuevo_grupo 		= 	0;
		$i 			 		=  	0;
		$sw 				= 	0;
		$grupo_diferente 	= 	0;
		//inicializar los grupos moviles 
		foreach($array_detalle_producto as $key => $row) {

            if((int)$row['grupo_movil'] > 0) {

            	$grupo_movil_nro    	= 	(int)$row['grupo_movil'];
		    	if($sw == 0){
		    		$grupo_diferente 	= 	$grupo_movil_nro;
		    		$sw 				= 	1;
		    	}

		    	if($grupo_movil_nro <> $grupo_diferente){
		    		$grupo_diferente 	=	$grupo_movil_nro;
		    		$i 					= 	$i + 1;
		    	}
             	$nuevo_grupo 			= 	$grupo_movil_nro - ($grupo_movil_nro-1) + $i;
		    	$array_detalle_producto[$key]['grupo_movil'] = $nuevo_grupo;
            }
	    }


	    //agregar la cantidad de grupo movil correcto
	    $count_grupo = 0;
		foreach($array_detalle_producto as $key => $row) {
			$grupo_movil_nro    	= 	(int)$row['grupo_movil'];
            if($grupo_movil_nro > 0) {
				$count_grupo 										= 	$this->funciones->countgrupomovil($array_detalle_producto,'grupo_movil',$grupo_movil_nro);
				$array_detalle_producto[$key]['grupo_orden_movil'] 	= 	$count_grupo;
            }
	    }


		$numero_mobil 												= 	$this->funciones->mayor_grupo_mobil($array_detalle_producto);

		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);
		
		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);
		$funcion 					= 	$this;

		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);

	}


	public function actionAjaxPedidoCrearMovilIndividuales(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$data_producto_pedido 				= 	$request['data_producto_pedido'];
		$array_detalle_producto 			=	array();
		$grupo 								= 	(int)$request['grupo'];
		$numero_mobil 						= 	(int)$request['numero_mobil'];

		$correlativo 						= 	(int)$request['correlativo'];
		$opcion_id 							= 	$request['opcion_id'];
		$grupo_mobil_mayor 					=	0;

		//limpiar centro y empresa
		foreach ($array_detalle_producto_request as $key => $item) {
				$array_detalle_producto_request[$key]['centro_atender_id'] 		= '';
				$array_detalle_producto_request[$key]['centro_atender_txt'] 	= '';
				$array_detalle_producto_request[$key]['empresa_atender_id'] 	= '';
				$array_detalle_producto_request[$key]['empresa_atender_txt'] 	= '';
				$array_detalle_producto_request[$key]['fecha_entrega'] 			= '';
		}

		foreach($data_producto_pedido as $index => $itemp) {
			//el mayor valor numero de movil

			foreach ($array_detalle_producto_request as $key => $item) {
	            if((int)$item['grupo_movil'] > $grupo_mobil_mayor) {
	                $grupo_mobil_mayor = (int)$item['grupo_movil'];
	            }
			}
			//agregar el numero de movil y agrupar 	 
			foreach($array_detalle_producto_request as $key => $row) {
			    if ($row['correlativo'] == $itemp['correlativo']){
			    	$array_detalle_producto_request[$key]['grupo_movil'] = $grupo_mobil_mayor + 1;
			    	//$grupo_mobil_mayor 									 = $grupo_mobil_mayor + 1;
			    }
		    } 
	    } 


	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_producto_request as $key => $item) {
			array_push($array_detalle_producto,$item);
		}


		// ordenar el array por grupo movil
		$array_detalle_producto = 	$this->funciones->ordermultidimensionalarray($array_detalle_producto,'grupo_movil',false);
		$nuevo_grupo 		= 	0;
		$i 			 		=  	0;
		$sw 				= 	0;
		$grupo_diferente 	= 	0;
		//inicializar los grupos moviles 
		foreach($array_detalle_producto as $key => $row) {

            if((int)$row['grupo_movil'] > 0) {

            	$grupo_movil_nro    	= 	(int)$row['grupo_movil'];
		    	if($sw == 0){
		    		$grupo_diferente 	= 	$grupo_movil_nro;
		    		$sw 				= 	1;
		    	}

		    	if($grupo_movil_nro <> $grupo_diferente){
		    		$grupo_diferente 	=	$grupo_movil_nro;
		    		$i 					= 	$i + 1;
		    	}
             	$nuevo_grupo 			= 	$grupo_movil_nro - ($grupo_movil_nro-1) + $i;
		    	$array_detalle_producto[$key]['grupo_movil'] = $nuevo_grupo;
            }
	    }


	    //agregar la cantidad de grupo movil correcto
	    $count_grupo = 0;
		foreach($array_detalle_producto as $key => $row) {
			$grupo_movil_nro    	= 	(int)$row['grupo_movil'];
            if($grupo_movil_nro > 0) {
				$count_grupo 										= 	$this->funciones->countgrupomovil($array_detalle_producto,'grupo_movil',$grupo_movil_nro);
				$array_detalle_producto[$key]['grupo_orden_movil'] 	= 	$count_grupo;
            }
	    }


		$numero_mobil 												= 	$this->funciones->mayor_grupo_mobil($array_detalle_producto);

		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);

		$funcion 					= 	$this;
		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);

	}



	public function actionAjaxModalAgregarProductosPedido(Request $request)
	{

		$data_producto 						= 	$request['data_producto'];
		$grupo 								= 	(int)$request['grupo'];
		$numero_mobil 						= 	(int)$request['numero_mobil'];
		$opcion_id 							= 	$request['opcion_id'];
		$correlativo 						= 	(int)$request['correlativo'];

		$cuenta_id_m 						= 	$request['cuenta_id_m'];
		$cliente 							= 	WEBListaCliente::where('COD_CONTRATO','=',$cuenta_id_m)->first();

		if(count($cliente)>0){
			$cliente_id 					= 	$cliente->id;
			$cliente_nombre 				= 	$cliente->NOM_EMPR;
		}else{
			$cliente_id 					= 	"";
			$cliente_nombre 				= 	"";
		}


		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();
		$rowspan 							= 	0;

		foreach($data_producto as $obj){

		    $producto_id 					= 	$obj['producto_id'];
		    $cantidad_atender 				= 	$obj['cantidad_atender'];

		    $producto 						= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
		    $unidad_medida 					= 	CMPCategoria::where('COD_CATEGORIA','=',$producto->COD_CATEGORIA_UNIDAD_MEDIDA)->first();


			$array_nuevo_producto 			=	array();
			$grupo 							= 	$grupo + 1;

			$correlativo 					= 	$correlativo + 1;


			//calculo de kilos,cantidad_sacos,palets
			$kilos 							=   $cantidad_atender*$producto->CAN_PESO_MATERIAL;
			$cantidad_sacos					= 	$cantidad_atender/$producto->CAN_BOLSA_SACO;
			$palets 						= 	$cantidad_sacos/$producto->CAN_SACO_PALET;
			//

			$numero_mobil 					= 	$numero_mobil + 1;

			$array_nuevo_producto		= 	
			$this->funciones->llenar_array_productos($cliente_id,$cliente_nombre,'','',$this->fin,
							$this->fin,$producto->COD_PRODUCTO,$producto->NOM_PRODUCTO,$producto->COD_CATEGORIA_UNIDAD_MEDIDA,$unidad_medida->NOM_CATEGORIA,
							$cantidad_atender,$kilos,$cantidad_sacos,$palets,$grupo,'1',$numero_mobil,'1',$correlativo,'oc_individual',$producto->CAN_PESO_MATERIAL,
							'','','','');


			$rowspan 						= 	$rowspan + 1;



			array_push($array_detalle_producto,$array_nuevo_producto);
		}

		if(count($array_detalle_producto_request)>0){
			foreach ($array_detalle_producto_request as $key => $item) {
				array_push($array_detalle_producto,$item);
			}
		}



		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);

		$funcion 	= 	$this;

		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);

		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);

	}

	public function actionAjaxModalAgregarOrdenCenPedido(Request $request)
	{

		$data_orden_cen 					= 	$request['data_orden_cen'];
		$grupo 								= 	(int)$request['grupo'];
		$correlativo 						= 	(int)$request['correlativo'];
		$tipo_grupo 						= 	$request['tipo_grupo'];
		$opcion_id 							= 	$request['opcion_id'];
		$numero_mobil 						= 	(int)$request['numero_mobil'];


		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();


		foreach($data_orden_cen as $obj){

		    $ordencen_id 					= 	$obj['ordencen_id'];
		    $orden 							= 	CMPOrden::where('COD_ORDEN','=',$ordencen_id)->first();
			$lista_detalle_ordencen			= 	$this->funciones->lista_orden_cen_detalle($ordencen_id);
			$array_nuevo_producto 			=	array();
			if($tipo_grupo == 'oc_grupo'){	$grupo 	= 	$grupo + 1;	$numero_mobil 	= 	$numero_mobil + 1;}	
			$rowspan 						= 	0;


			while($row = $lista_detalle_ordencen->fetch())
			{

				if($tipo_grupo == 'oc_individual'){	$grupo 	= 	$grupo + 1;	$numero_mobil 	= 	$numero_mobil + 1;}

				$unidad_medida 				= 	CMPCategoria::where('COD_CATEGORIA','=',$row['COD_CATEGORIA_UNIDAD_MEDIDA'])->first();
				$correlativo 				= 	$correlativo + 1;


				//calculo de kilos,cantidad_sacos,palets
				$producto 					= 	ALMProducto::where('COD_PRODUCTO','=',$row['COD_PRODUCTO'])->first();
				$kilos 						=   $row['CAN_PRODUCTO']*$producto->CAN_PESO_MATERIAL;
				$cantidad_sacos				= 	$row['CAN_PRODUCTO']/$producto->CAN_BOLSA_SACO;
				$palets 					= 	$cantidad_sacos/$producto->CAN_SACO_PALET;
				//

				$array_nuevo_producto		= 	

				$this->funciones->llenar_array_productos($orden->COD_EMPR_CLIENTE,$orden->TXT_EMPR_CLIENTE,$row['COD_TABLA'],$orden->NRO_ORDEN_CEN,$this->fin,
								$this->fin,$row['COD_PRODUCTO'],$row['TXT_NOMBRE_PRODUCTO'],$row['COD_CATEGORIA_UNIDAD_MEDIDA'],$unidad_medida->NOM_CATEGORIA,
								$row['CAN_PRODUCTO'],$kilos,$cantidad_sacos,$palets,$grupo,'0','0','0',$correlativo,$tipo_grupo,$producto->CAN_PESO_MATERIAL,
								'','','','');


				$rowspan 	= 	$rowspan + 1;
				array_push($array_detalle_producto,$array_nuevo_producto);

			}

			

			// modificar un valor en array
			if($tipo_grupo == 'oc_grupo'){


				$array_detalle_producto = $this->funciones->modificarmultidimensionalarray($array_detalle_producto,'grupo_orden',$rowspan,$orden->NRO_ORDEN_CEN);
				$array_detalle_producto = $this->funciones->agregar_mobil_producto($array_detalle_producto,$rowspan,$numero_mobil);
			}else{

				$array_detalle_producto = $this->funciones->modificar_individual_multidimensionalarray($array_detalle_producto,'grupo_orden');
				$array_detalle_producto = $this->funciones->agregar_mobil_producto($array_detalle_producto,$rowspan,$numero_mobil);
			}

		}

		if(count($array_detalle_producto_request)>0){
			foreach ($array_detalle_producto_request as $key => $item) {
				array_push($array_detalle_producto,$item);
			}
		}


		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);

		

		$funcion 					= 	$this;


		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);

		//dd($array_detalle_producto);

		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'funcion' 								=> $funcion,
						 	'ajax'   		  						=> true,
						 ]);

	}

	public function actionListarGeneracionPedido($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    /*
		$fechainicio 					=  	$this->fecha_menos_quince;
		$fechafin 						=  	$this->fin;
	    $listaordendespacho 			=   WEBDetalleOrdenDespacho::where('fecha_pedido','>=', $fechainicio)
	    									->where('fecha_pedido','<=', $fechafin)
	    									->orderBy('fecha_crea', 'desc')
	    									->get();
		*/

		$fechainicio 					=  	$this->fecha_menos_quince;
		$fechafin 						=  	$this->fin;

	    $listaordendespacho 			=   WEBOrdenDespacho::join('CMP.CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA','=','WEB.ordendespachos.estado_id')
	    									->where('fecha_orden','>=', $fechainicio)
	    									->where('fecha_orden','<=', $fechafin)
	    									->orderBy('fecha_crea', 'desc')
	    									->get();


		$funcion 						= 	$this;



		return View::make('despacho/listaordendespacho',
						 [
						 	'idopcion' 								=> $idopcion,
						 	'listaordendespacho' 					=> $listaordendespacho,
						 	'funcion' 								=> $funcion,
						 	'fechainicio' 							=> $fechainicio,
						 	'fechafin' 								=> $fechafin,
						 ]);

	}




	public function actionAjaxListaPedidosDespacho(Request $request)
	{



		$fechainicio 					=  	$request['fechainicio'];
		$fechafin 						=  	$request['fechafin'];
		
	    $listaordendespacho 			=   WEBOrdenDespacho::join('CMP.CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA','=','WEB.ordendespachos.estado_id')
	    									->where('fecha_orden','>=', $fechainicio)
	    									->where('fecha_orden','<=', $fechafin)
	    									->orderBy('fecha_crea', 'desc')
	    									->get();

		$funcion 						= 	$this;

		return View::make('despacho/ajax/alistapedidosdespachos',
						 [
						 	'listaordendespacho' 					=> $listaordendespacho,
						 	'funcion' 								=> $funcion,
						 	'ajax' 									=> true,
						 ]);

	}



	public function actionAjaxModalListaOrdenCenProducto(Request $request)
	{


		$cuenta_id 						= 	$request['cuenta_id'];
		$funcion 						= 	$this;

	    $listaproductos 				= 	DB::table('WEB.LISTAPRODUCTOSAVENDER')
	    									->whereIn('COD_CATEGORIA_UNIDAD_MEDIDA',['UME0000000000001','UME0000000000013'])
				    					 	->orderBy('NOM_PRODUCTO', 'asc')
				    					 	->get();


		/******* LISTA ORDEN CEN  **********/
		$empresa_id 					= 	Session::get('empresas')->COD_EMPR;
		$centro_id 						= 	Session::get('centros')->COD_CENTRO;
		$cliente 						= 	WEBListaCliente::where('COD_CONTRATO','=',$cuenta_id)->first();
		
		if(count($cliente)>0){
			$cliente_id = $cliente->id;
		}else{
			$cliente_id = "";
		}

		$fecha_inicio 					= 	$this->fecha_menos_treinta_dias;
		$fecha_fin 						= 	$this->fin;
		$listaordencen					= 	$this->funciones->lista_orden_cen($empresa_id,$cliente_id,$centro_id,$fecha_inicio,$fecha_fin);
		$combotipogrupo					= 	array('oc_grupo' => "Grupo",'oc_individual' => "Individual"); 	


		return View::make('despacho/modal/ajax/ordencenproducto',
						 [
						 	'cuenta_id' 				=> $cuenta_id,
						 	'listaproductos' 			=> $listaproductos,
						 	'listaordencen' 			=> $listaordencen,
						 	'funcion' 					=> $funcion,
						 	'combotipogrupo' 			=> $combotipogrupo,
						 	'ajax' 						=> true,
						 ]);


	}




//actionAjaxModalAgregarProductosOrdenCen




}
