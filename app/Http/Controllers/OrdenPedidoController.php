<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBListaCliente,App\STDTipoDocumento,App\WEBReglaProductoCliente,App\WEBPedido;
use App\WEBDetallePedido,App\CMPCategoria,App\WEBReglaCreditoCliente,App\STDEmpresa,App\WEBPrecioProducto,App\WEBMaestro,App\WEBPrecioProductoContrato,App\STDEmpresaDireccion,App\CMPContrato,App\ALMProducto;
use App\WEBAsignarRegla;
use App\Traits\OrdenPedidoTraits;


use View;
use Session;
use App\Biblioteca\Osiris;
use App\Biblioteca\Funcion;
use PDO;
use Mail;
use PDF;
  
class OrdenPedidoController extends Controller
{

	use OrdenPedidoTraits;

	public function actionAjaxObsequioRelacion(Request $request)
	{


		$productos 					= 	$request['datastring'];
		//DETALLE PEDIDO
		$productos 					= 	json_decode($productos, true);
		$arraycombo 				=   array();
		foreach($productos as $obj){
			//$producto 		
			$precio_producto 			=  	(float)$obj['precio_producto'];
			$cantidad_producto 			=  	(float)$obj['cantidad_producto'];
			$obsequio 					=  	(int)$obj['obsequio'];
			$producto_id 				= 	$this->funciones->desencriptar_id($obj['prefijo_producto'].'-'.$obj['id_producto'],13);
			$producto                   = 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
			$cadena 					= 	$producto->NOM_PRODUCTO.' - Cantidad : '.$cantidad_producto.' - Precio : '.$precio_producto;
			
			if($obsequio=='0'){
				//ver si ya tiene relacion 
				$sw = 0;
				foreach($productos as $objr){
					if($obj['ind_producto_obsequio'] == $objr['ind_producto_obsequio']){
						$sw = $sw +1;
					}
				}

				if($sw<2){
					$arraycombo 			    =   $arraycombo + [$obj['ind_producto_obsequio']=>$cadena];
				}

			}
		}	

		return View::make('pedido/ajax/comboproductorelacionado',
						 [
							 'arraycombo'  => $arraycombo,
						 ]);


		
	}



	public function actionAjaxLimiteCredito(Request $request)
	{
		$pedido_id 					= 	$request['pedido_id'];
		$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$pedido_id,8);
		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();

		$deuda_osiris 				=   $this->funciones->deuda_total_osiris($pedido);
		$deuda_osyza 				=   $this->funciones->deuda_total_oryza($pedido);
		$deuda_actual_pedido        =   $pedido->total;
		$limite_credito				= 	$this->funciones->data_regla_limite_credito($pedido->cliente_id);	
		$l_c 						=   0;
	    if(count($limite_credito)>0){
	        $l_c = (float)$limite_credito->canlimitecredito;
	    }else{
	    	$l_c = 0;
	    }

		$suma_deudas = $deuda_osiris + $deuda_osyza; 
		$suma_posible = $suma_deudas + $deuda_actual_pedido;
		$suma_total = $suma_deudas + $deuda_osyza + $deuda_actual_pedido;

		$funcion 					= 	$this;			

		return View::make('pedido/ajax/modallimitecredito',
						 [
							 'pedido_id'   				=> $pedido_id,
							 'pedido'   				=> $pedido,
							 'deuda_osiris'   			=> $deuda_osiris,
							 'deuda_osyza'   			=> $deuda_osyza,
							 'l_c'   					=> $l_c,
							 'suma_deudas'   			=> $suma_deudas,
							 'suma_posible'   			=> $suma_posible,
							 'suma_total'   			=> $suma_total,
							 'funcion'   				=> $funcion,
							 'limite_credito'   				=> $limite_credito,
						 ]);
	}

	public function actionAjaxDeudaCliente(Request $request)
	{
		$pedido_id 					= 	$request['pedido_id'];
		$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$pedido_id,8);
		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();
		$lista_deuda_cliente		= 	$this->funciones->lista_saldo_cuenta_documento_todas_empresas($this->fechaactual,'TCO0000000000068',$pedido->cliente_id,'CON');
		$funcion 					= 	$this;			
		$limite_credito				= 	$this->funciones->data_regla_limite_credito($pedido->cliente_id);

        $deuda_antigua      =   DB::select('exec WEB.DEUDA_MAS_ANTIGUA_CLIENTE ?,?,?,?,?,?,?,?,?,?,?', array('','','','',date("Y-m-d"),$pedido->cliente_id,'TCO0000000000068','','','',''));



		return View::make('pedido/ajax/modaldeudacliente',
						 [
							 'pedido_id'   				=> $pedido_id,
							 'pedido'   				=> $pedido,
							 'lista_deuda_cliente'   	=> $lista_deuda_cliente,
							 'funcion'   				=> $funcion,
							 'limite_credito'   		=> $limite_credito,
							 'deuda_antigua'   			=> $deuda_antigua,
						 ]);
	}





	public function actionAjaxDetallePedidoRechazar(Request $request)
	{

		$detalle_pedido_id 					= 	$request['detalle_pedido_id'];
		$detallepedido 						=   WEBDetallePedido::where('id','=',$detalle_pedido_id)->first();
		$mensaje 							=  	'Estado del producto ('.$detallepedido->producto->NOM_PRODUCTO.') modificado con exito';
		$estado_pedido 						=   'EPP0000000000005';

		$response 							= 	$this->funciones->cambiar_estado_detalle_pedido($detalle_pedido_id,$mensaje,$estado_pedido);
		if($response[0]['error']){echo json_encode($response); exit();}

		echo json_encode($response);

	}


	public function actionAjaxGuardarPrecioProductoPedido(Request $request)
	{

		$precio 							= 	$request['precio'];
		$detallepedido_id 					= 	$request['detallepedido_id'];
		$pedido_id 							= 	$request['pedido_id'];
		$detallepedido 						=   WEBDetallePedido::where('id','=',$detallepedido_id)->first();

		$mensaje 							=  	'Precio del producto ('.$detallepedido->producto->NOM_PRODUCTO.') modificado con exito';

		$response 							= 	$this->funciones->el_pedido_estado_generado($pedido_id,$mensaje);
		if($response[0]['error']){echo json_encode($response); exit();}


		//reclaculo de importe,igv,subtotal detallepedido	

		$importe 							=   $detallepedido->cantidad * (float)$precio;

		if($detallepedido->ind_obsequio==1){
			$importe = 0;
		}

	    $detallepedido->precio 				= 	(float)$precio;
	    $detallepedido->igv 				= 	$this->funciones->calculo_igv($importe);
	    $detallepedido->subtotal 			= 	$this->funciones->calculo_subtotal($importe);
	    $detallepedido->total 				= 	$importe;
		$detallepedido->fecha_mod 	 		=   $this->fechaactual;
		$detallepedido->usuario_mod 		=   Session::get('usuario')->id;
		$detallepedido->save();
		//reclaculo de importe,igv,subtotal pedido
		$this->funciones->calculo_totales_pedido($pedido_id);


		echo json_encode($response);


	}

	public function actionAjaxGuardarCantidadProductoPedido(Request $request)
	{

		$cantidad 							= 	$request['cantidad'];
		$detallepedido_id 					= 	$request['detallepedido_id'];
		$pedido_id 							= 	$request['pedido_id'];
		$detallepedido 						=   WEBDetallePedido::where('id','=',$detallepedido_id)->first();

		$mensaje 							=  	'Cantidad producto ('.$detallepedido->producto->NOM_PRODUCTO.') modificado con exito';

		$response 							= 	$this->funciones->el_pedido_estado_generado($pedido_id,$mensaje);
		if($response[0]['error']){echo json_encode($response); exit();}


		//reclaculo de importe,igv,subtotal detallepedido	

		$importe 							=   $detallepedido->precio * (float)$cantidad;
	    $detallepedido->cantidad 			= 	$cantidad;
	    $detallepedido->igv 				= 	$this->funciones->calculo_igv($importe);
	    $detallepedido->subtotal 			= 	$this->funciones->calculo_subtotal($importe);
	    $detallepedido->total 				= 	$importe;
		$detallepedido->fecha_mod 	 		=   $this->fechaactual;
		$detallepedido->usuario_mod 		=   Session::get('usuario')->id;
		$detallepedido->save();
		//reclaculo de importe,igv,subtotal pedido
		$this->funciones->calculo_totales_pedido($pedido_id);


		echo json_encode($response);


	}



	public function actionAjaxDetallePedidoMobil(Request $request)
	{
		$pedido_id 					= 	$request['pedido_id'];
		$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$pedido_id,8);
		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();
		$funcion 					= 	$this;			


		return View::make('pedido/ajax/modaldetallepedidomobil',
						 [
							 'pedido_id'   	=> $pedido_id,
							 'pedido'   	=> $pedido,
							 'funcion'   	=> $funcion,
						 ]);

	}

	public function actionAjaxSeguimientoPedidoMobil(Request $request)
	{

		$pedido_id 					= 	$request['pedido_id'];
		$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$pedido_id,8);
		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();


		$ordenes 					 = 	DB::table('CMP.REFERENCIA_ASOC as asoc')
									    ->distinct()
									    ->join('WEB.detallepedidos as det', 'asoc.COD_TABLA', '=', 'det.id')
									    ->join('CMP.ORDEN as ord', 'ord.COD_ORDEN', '=', 'asoc.COD_TABLA_ASOC')
									    ->select('ord.*')
									    ->where('det.pedido_id', '=', $pedido_id)
									    ->where('asoc.COD_ESTADO', '=', 1)
									    ->where('ord.COD_ESTADO', '=', 1)
									    ->get();

		$funcion 					= 	$this;	

		return View::make('pedido/ajax/modalseguimientopedidomobil',
						 [
							 'pedido_id'   	=> $pedido_id,
							 'pedido'   	=> $pedido,
							 'ordenes'   	=> $ordenes,
							 'funcion'   	=> $funcion,
						 ]);

	}



	public function actionNoAutorizarPedido($idopcion,Request $request)
	{

		if($_POST)
		{

			$msjarray  			= array();
			$respuesta 			= json_decode($request['pedidorechazar'], true);
			$finicio 			= $request['fechainiciorechazar'];
			$fechafin 			= $request['fechafinrechazar'];
	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;
		

			foreach($respuesta as $obj){

				$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$obj['id'],8);
				$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();


			    if($pedido->estado_id == 'EPP0000000000002'){ 

			    	//existe algun estado ejecutado
			    	$detalle_pedido_estado = WEBDetallePedido::where('estado_id','=','EPP0000000000004')->first();

				    $pedido->estado_id 				 	= 	'EPP0000000000005';
					$pedido->fecha_autorizacion 	 	=   $this->fechaactual;
				    $pedido->ind_notificacion_rechazado = 	0;
					$pedido->usuario_autorizacion 		=   Session::get('usuario')->id;
   					$pedido->save();

   					//solo modificar los estados vacios o null
					WEBDetallePedido::where('pedido_id','=',$pedido_id)
					->where('activo','=',1)
					->where(function($query) {
		                $query->whereNull('estado_id')
		                      ->orWhere('estado_id', '=', 'EPP0000000000002');
		            })
					->update([	'estado_id' => 'EPP0000000000005',
								'fecha_mod' =>  $this->fechaactual,
								'usuario_mod' => Session::get('usuario')->id
							]);


			    	$msjarray[] 						= 	array(	"data_0" => $pedido->codigo, 
			    											"data_1" => 'pedido rechazado', 
			    											"tipo" => 'S');
			    	$conts 								= 	$conts + 1;

			    }else{


					/**** ERROR DE PROGRMACION O SINTAXIS ****/
					$msjarray[] = array("data_0" => $pedido->codigo, 
										"data_1" => 'este pedido esta autorizado', 
										"tipo" => 'D');
					$contd 		= 	$contd + 1;


			    }

			}


			/************** MENSAJES DEL DETALLE PEDIDO  ******************/
	    	$msjarray[] = array("data_0" => $conts, 
	    						"data_1" => 'pedidos rechazado', 
	    						"tipo" => 'TS');

	    	$msjarray[] = array("data_0" => $contw, 
	    						"data_1" => 'pedidos', 
	    						"tipo" => 'TW');	 

	    	$msjarray[] = array("data_0" => $contd, 
	    						"data_1" => 'pedidos errados', 
	    						"tipo" => 'TD');

			$msjarray[] = array("data_0" => $finicio, 
								"data_1" => $fechafin, 
								"tipo" => 'FE');

			$msjjson = json_encode($msjarray);


			return Redirect::to('/gestion-de-orden-de-pedido-autorizacion/'.$idopcion)->with('xmlmsj', $msjjson);

		
		}
	}
	public function actionAutorizarPedido($idopcion,Request $request)
	{

		if($_POST)
		{

			$msjarray  			= array();
			$respuesta 			= json_decode($request['pedido'], true);
			$finicio 			= $request['fechainicio'];
			$fechafin 			= $request['fechafin'];
	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;
		

			foreach($respuesta as $obj){

				$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$obj['id'],8);
				$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();


			    if($pedido->estado_id == 'EPP0000000000002'){ 

				    $pedido->estado_id 				 		= 	'EPP0000000000003';
					$pedido->fecha_autorizacion 	 		=   $this->fechaactual;
					$pedido->ind_notificacion_autorizacion 	=   0;
					$pedido->usuario_autorizacion 			=   Session::get('usuario')->id;
					$np=$pedido;
   					$pedido->save();

					WEBDetallePedido::where('pedido_id','=',$pedido_id)
					->where('activo','=',1)
					->where(function ($query){
					    $query->where('estado_id', '=', 'EPP0000000000002')
					          ->orwhereNull('estado_id');
					})
					->update(['estado_id' => 'EPP0000000000003', 'fecha_mod' =>  $this->fechaactual,'usuario_mod' => Session::get('usuario')->id]);
					
					
			    	$msjarray[] 			= 	array(	"data_0" => $pedido->codigo, 
			    									"data_1" => 'pedido autorizado', 
			    									"tipo" => 'S');
					$conts 					= 	$conts + 1;
					
					$codigo 				= 	$pedido->codigo;

			    }else{


					/**** ERROR DE PROGRMACION O SINTAXIS ****/
					$msjarray[] = array("data_0" => $pedido->codigo, 
										"data_1" => 'este pedido esta autorizado', 
										"tipo" => 'D');
					$contd 		= 	$contd + 1;


			    }

			}


			/************** MENSAJES DEL DETALLE PEDIDO  ******************/
	    	$msjarray[] = array("data_0" => $conts, 
	    						"data_1" => 'pedidos autorizados', 
	    						"tipo" => 'TS');

	    	$msjarray[] = array("data_0" => $contw, 
	    						"data_1" => 'pedidos', 
	    						"tipo" => 'TW');	 

	    	$msjarray[] = array("data_0" => $contd, 
	    						"data_1" => 'pedidos errados', 
	    						"tipo" => 'TD');

			$msjarray[] = array("data_0" => $finicio, 
								"data_1" => $fechafin, 
								"tipo" => 'FE');

			$msjjson = json_encode($msjarray);


			return Redirect::to('/gestion-de-orden-de-pedido-autorizacion/'.$idopcion)->with('xmlmsj', $msjjson);

		
		}
	}
	public function actionListarTomaPedidoAutorizacion($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/


	    if (Session::get('xmlmsj')){

	    	$obj 				= 	json_decode(Session::get('xmlmsj'));
	    	$pfi 				= 	array_search('FE', array_column($obj, 'tipo'));
	    	$pff 				= 	array_search('FE', array_column($obj, 'tipo'));
            $fechainicio 		= 	$obj[$pfi]->data_0;
            $fechafin 			= 	$obj[$pff]->data_1;

	    }else{

		    $fechainicio  		= 	$this->inicio;
		    $fechafin  			= 	$this->fin;

	    }

	    $centro_id 				= 	Session::get('centros')->COD_CENTRO;

	    $listapedidos			= 	WEBPedido::where('activo','=',1)
		    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
		    						->whereIn('estado_id', ['EPP0000000000002'])
			    					->where('fecha_venta','>=', $fechainicio)
			    					->where('fecha_venta','<=', $fechafin)
									//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
									->Centro($centro_id)
									//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
		    						->orderBy('fecha_venta', 'desc')
		    						->get();

		$funcion 					= 	$this;

		return View::make('pedido/listatomapedidoautorizacion',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'listapedidos' 	=> $listapedidos,
						 	'fechainicio' 	=> $fechainicio,
						 	'fechafin' 		=> $fechafin,
						 	'funcion' 		=> $funcion,
						 ]);
	}
	public function actionAjaxListarTomaPedidoAutorizacion(Request $request)
	{

		$finicio 		=   date_format(date_create($request['finicio']), 'd-m-Y');
		$ffin 			=   date_format(date_create($request['ffin']), 'd-m-Y');


		$centro_id 	    = 	Session::get('centros')->COD_CENTRO;
	    $listapedidos	= 	WEBPedido::where('activo','=',1)
	    					->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
		    				->whereIn('estado_id', ['EPP0000000000002'])
							//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
							//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
							->Centro($centro_id)
		    				->where('fecha_venta','>=', $finicio)
		    				->where('fecha_venta','<=', $ffin)
	    					->orderBy('fecha_venta', 'desc')
	    					->get();
	    			
		$funcion 		= 	$this;

		return View::make('pedido/ajax/listatomapedidoautorizacion',
						 [
							 'listapedidos'   => $listapedidos,
							 'ajax'   		  => true,
							 'funcion'   	  => $funcion,
						 ]);
	}
	public function actionAjaxDetallePedidoAutorizacion(Request $request)
	{
		$pedido_id 					= 	$request['pedido_id'];
		$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$pedido_id,8);
		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();
		$funcion 					= 	$this;			

		return View::make('pedido/ajax/modaldetallepedidoautorizacion',
						 [
							 'pedido_id'   	=> $pedido_id,
							 'pedido'   	=> $pedido,
							 'funcion'   	=> $funcion,
						 ]);
	}


	public function actionEnviarRechazarSieteDias($idopcion,Request $request)
	{

		if($_POST)
		{

			$msjarray  			= array();
			$respuesta 			= json_decode($request['pedido'], true);
			$motivo_id 			= $request['motivo_id_n'];
			$observacion 		= $request['observacion_n'];
	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;

			foreach($respuesta as $obj){


				$ind_rechazado_ejecutado        =   0;
				$pedido_id 						= 	$this->funciones->desencriptar_id('1CIX-'.$obj['id'],8);
				$pedido 						=   WEBPedido::where('id','=',$pedido_id)->first();
				$detalle 						=   WEBDetallePedido::where('pedido_id','=',$pedido->id)->get();


				foreach($detalle as $index => $item){

					if($item->atendido>0){

						$ind_rechazado_ejecutado        =   1;

						$item->estado_id = 'EPP0000000000004';
						$item->fecha_mod = $this->fechaactual;
						$item->usuario_mod = Session::get('usuario')->id;
						$item->save();

					}else{

						$item->estado_id = 'EPP0000000000005';
						$item->fecha_mod = $this->fechaactual;
						$item->usuario_mod = Session::get('usuario')->id;
						$item->save();
					
					}
				}



				if($ind_rechazado_ejecutado == 1){

					$pedido->estado_id = 'EPP0000000000004';
					$pedido->fecha_mod = $this->fechaactual;
					$pedido->motivo_rechazo_id = $motivo_id;
					$pedido->observacion = $observacion;
					$pedido->ind_notificacion_rechazado = 0;
					$pedido->usuario_mod = Session::get('usuario')->id;
					$pedido->save();


				}else{
					$pedido->estado_id = 'EPP0000000000005';
					$pedido->fecha_mod = $this->fechaactual;
					$pedido->motivo_rechazo_id = $motivo_id;
					$pedido->observacion = $observacion;
					$pedido->ind_notificacion_rechazado = 0;
					$pedido->usuario_mod = Session::get('usuario')->id;
					$pedido->save();				
				}


		    	$msjarray[] 			= 	array(	"data_0" => $pedido->codigo, 
		    									"data_1" => 'pedido rechazado', 
		    									"tipo" => 'S');
		    	$conts 					= 	$conts + 1;


			}


			/************** MENSAJES DEL DETALLE PEDIDO  ******************/
	    	$msjarray[] = array("data_0" => $conts, 
	    						"data_1" => 'pedidos rechazados', 
	    						"tipo" => 'TS');

	    	$msjarray[] = array("data_0" => $contw, 
	    						"data_1" => 'pedidos', 
	    						"tipo" => 'TW');	 

	    	$msjarray[] = array("data_0" => $contd, 
	    						"data_1" => 'pedidos errados', 
	    						"tipo" => 'TD');



			$msjjson = json_encode($msjarray);

			return Redirect::to('/gestion-de-orden-de-pedido-anulacion/'.$idopcion)->with('xmlmsj', $msjjson);

		}
	}


	public function actionEnviarRechazar($idopcion,Request $request)
	{

		if($_POST)
		{

			$msjarray  			= array();
			$respuesta 			= json_decode($request['pedidorechazar'], true);
			$finicio 			= $request['fechainiciorechazar'];
			$fechafin 			= $request['fechafinrechazar'];
	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;
		

			foreach($respuesta as $obj){

				$pedido_id 						= 	$this->funciones->desencriptar_id('1CIX-'.$obj['id'],8);
				$lista_array_detalle_pedido		= 	json_decode($obj['detalle'], true);
				$pedido 						=   WEBPedido::where('id','=',$pedido_id)->first();

				//filtrar solo check
				$eliminar 						= 	array("checked" => "checked");
				$lista_array_detalle_pedido 	= 	array_filter($lista_array_detalle_pedido, function($lista_array_detalle_pedido) use ($eliminar){
				    return in_array($lista_array_detalle_pedido['checked'], $eliminar);
				});
				$lista_array_detalle_pedido 	= array_values($lista_array_detalle_pedido);

				// guardar id de detalle pedido
				$array_detalle_pedido_id 		= 	array();
				foreach($lista_array_detalle_pedido as $key => $obj_det){
					$array_detalle_pedido_id[$key] =  $obj_det['detalle_pedido_id'];
				}


			    if($pedido->estado_id == 'EPP0000000000003'){ 

					WEBDetallePedido::whereIn('id',$array_detalle_pedido_id)
					->update([	'estado_id' => 'EPP0000000000005',
								'fecha_mod' =>  $this->fechaactual,
								'usuario_mod' => Session::get('usuario')->id
							]);

					$this->funciones->estado_pedido_ejecutado($pedido);
				



			    	$msjarray[] 			= 	array(	"data_0" => $pedido->codigo, 
			    									"data_1" => 'pedido rechazado', 
			    									"tipo" => 'S');
			    	$conts 					= 	$conts + 1;

			    }else{


					/**** ERROR DE PROGRMACION O SINTAXIS ****/
					$msjarray[] = array("data_0" => $pedido->codigo, 
										"data_1" => 'este pedido esta ejecutado', 
										"tipo" => 'D');
					$contd 		= 	$contd + 1;


			    }

			}


			/************** MENSAJES DEL DETALLE PEDIDO  ******************/
	    	$msjarray[] = array("data_0" => $conts, 
	    						"data_1" => 'pedidos rechazados', 
	    						"tipo" => 'TS');

	    	$msjarray[] = array("data_0" => $contw, 
	    						"data_1" => 'pedidos', 
	    						"tipo" => 'TW');	 

	    	$msjarray[] = array("data_0" => $contd, 
	    						"data_1" => 'pedidos errados', 
	    						"tipo" => 'TD');

			$msjarray[] = array("data_0" => $finicio, 
								"data_1" => $fechafin, 
								"tipo" => 'FE');

			$msjjson = json_encode($msjarray);

			return Redirect::to('/gestion-de-orden-de-pedido/'.$idopcion)->with('xmlmsj', $msjjson);

		
		}
	}

	public function actionEnviarOsirisRechazar($idopcion,Request $request)
	{

		if($_POST)
		{

			$msjarray  			= array();
			$respuesta 			= json_decode($request['pedidorechazar'], true);
			$finicio 			= $request['fechainiciorechazar'];
			$fechafin 			= $request['fechafinrechazar'];
	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;
		

			foreach($respuesta as $obj){

				$pedido_id 						= 	$this->funciones->desencriptar_id('1CIX-'.$obj['id'],8);
				$lista_array_detalle_pedido		= 	json_decode($obj['detalle'], true);
				$pedido 						=   WEBPedido::where('id','=',$pedido_id)->first();

				//filtrar solo check
				$eliminar 						= 	array("checked" => "checked");
				$lista_array_detalle_pedido 	= 	array_filter($lista_array_detalle_pedido, function($lista_array_detalle_pedido) use ($eliminar){
				    return in_array($lista_array_detalle_pedido['checked'], $eliminar);
				});
				$lista_array_detalle_pedido 	= array_values($lista_array_detalle_pedido);

				// guardar id de detalle pedido
				$array_detalle_pedido_id 		= 	array();
				foreach($lista_array_detalle_pedido as $key => $obj_det){
					$array_detalle_pedido_id[$key] =  $obj_det['detalle_pedido_id'];
				}


			    if($pedido->estado_id == 'EPP0000000000003'){ 

					WEBDetallePedido::whereIn('id',$array_detalle_pedido_id)
					->update([	'estado_id' => 'EPP0000000000005',
								'fecha_mod' =>  $this->fechaactual,
								'usuario_mod' => Session::get('usuario')->id
							]);

					$this->funciones->estado_pedido_ejecutado($pedido);
				



			    	$msjarray[] 			= 	array(	"data_0" => $pedido->codigo, 
			    									"data_1" => 'pedido rechazado', 
			    									"tipo" => 'S');
			    	$conts 					= 	$conts + 1;

			    }else{


					/**** ERROR DE PROGRMACION O SINTAXIS ****/
					$msjarray[] = array("data_0" => $pedido->codigo, 
										"data_1" => 'este pedido esta ejecutado', 
										"tipo" => 'D');
					$contd 		= 	$contd + 1;


			    }

			}


			/************** MENSAJES DEL DETALLE PEDIDO  ******************/
	    	$msjarray[] = array("data_0" => $conts, 
	    						"data_1" => 'pedidos rechazados', 
	    						"tipo" => 'TS');

	    	$msjarray[] = array("data_0" => $contw, 
	    						"data_1" => 'pedidos', 
	    						"tipo" => 'TW');	 

	    	$msjarray[] = array("data_0" => $contd, 
	    						"data_1" => 'pedidos errados', 
	    						"tipo" => 'TD');

			$msjarray[] = array("data_0" => $finicio, 
								"data_1" => $fechafin, 
								"tipo" => 'FE');

			$msjjson = json_encode($msjarray);

			return Redirect::to('/gestion-de-orden-de-pedido/'.$idopcion)->with('xmlmsj', $msjjson);

		
		}
	}
	public function actionEnviarOsiris($idopcion,Request $request)
	{

		if($_POST)
		{

			$msjarray  			= array();
			$respuesta 			= json_decode($request['pedido'], true);
			$finicio 			= $request['fechainicio'];
			$fechafin 			= $request['fechafin'];
	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;
			$respuesta_obq 		= json_decode($request['pedido'], true);



			foreach($respuesta as $obj){

				$pedido_id 						= 	$this->funciones->desencriptar_id('1CIX-'.$obj['id'],8);
				$lista_array_detalle_pedido		= 	json_decode($obj['detalle'], true);
				$pedido 						=   WEBPedido::where('id','=',$pedido_id)->first();
				$osiris 						= 	new Osiris();


				//dd($lista_array_detalle_pedido);


				//filtrar solo check
				$eliminar 						= 	array("checked" => "checked");
				$lista_array_detalle_pedido 	= 	array_filter($lista_array_detalle_pedido, function($lista_array_detalle_pedido) use ($eliminar){
				    return in_array($lista_array_detalle_pedido['checked'], $eliminar);
				});
				$lista_array_detalle_pedido 	= array_values($lista_array_detalle_pedido);

				//agrupar las empresas que van a guardarse
				$group_detalle_pedido 			=  $this->funciones->grouparray($lista_array_detalle_pedido,'empresa_id');


				//validarque la cuenta esten en las empresas seleccionadas
				$error = 0;
				foreach($group_detalle_pedido as $key => $obj_empresa_v){

					$empresa_id_v 					=   	$obj_empresa_v['empresa_id'];
					$empresa                        =       STDEmpresa::where('COD_EMPR','=',$empresa_id_v)->first();
					$cod_empr                       =       $empresa->COD_EMPR;
					$contrato                       =       CMPContrato::where('COD_CONTRATO','=',$pedido->cuenta_id)
                                                            ->first();
                    $contrato_diferente             =       CMPContrato::where('COD_EMPR','=',$cod_empr)
                                                            ->where('COD_CATEGORIA_TIPO_CONTRATO','=','TCO0000000000068')
                                                            ->where('TXT_EMPR_CLIENTE', 'like', '%'.$contrato->TXT_EMPR_CLIENTE.'%')
                                                            ->first();          
                    if(count($contrato_diferente)<=0){
                            return Redirect::back()->withInput()->with('errorbd', 'No existe cuenta en la empresa seleccionada');
                    }                                           

	        	}


				foreach($group_detalle_pedido as $key => $obj_empresa){

					// array de id_detalle_pedido por empresa
					$empresa_id 					=   $obj_empresa['empresa_id'];
					//$orden_detalle_pedido_id 		=   $obj_empresa['orden_detalle_pedido_id'];

					$empresa_filter 				= 	array("empresa_id" => $obj_empresa['empresa_id']);
					$lista_array_empresa 			= 	array_filter($lista_array_detalle_pedido, function($lista_array_detalle_pedido) use ($empresa_filter){
					    return in_array($lista_array_detalle_pedido['empresa_id'], $empresa_filter);
					});
					$lista_array_empresa 			= array_values($lista_array_empresa);

					// guardar id de detalle pedido
					$array_detalle_pedido_id 		= 	array();
					foreach($lista_array_empresa as $key => $obj_det){
						$array_detalle_pedido_id[$key] =  $obj_det['detalle_pedido_id'];
					}

					$pedidoagrupado 				=   WEBDetallePedido::where('pedido_id','=',$pedido_id)
														->whereIn('id',$array_detalle_pedido_id)
														->select(DB::raw("sum(total) as total"))
														->first();


					$respuesta 						=  	$osiris->guardar_orden_pedido_por_detalle($pedido,$pedidoagrupado,$array_detalle_pedido_id,$empresa_id,$respuesta_obq,$lista_array_detalle_pedido);


				}


			    if($respuesta){ 

			    	$this->funciones->estado_pedido_ejecutado($pedido);
			    	$msjarray[] 			= 	array(	"data_0" => $pedido->codigo, 
			    									"data_1" => 'aceptado a osiris', 
			    									"tipo" => 'S');
			    	$conts 					= 	$conts + 1;						
					$codigo 				= 	$pedido->codigo;

		
			    }else{


					/**** ERROR DE PROGRMACION O SINTAXIS ****/
					$msjarray[] = array("data_0" => $pedido->codigo, 
										"data_1" => $osiris->msjerror, 
										"tipo" => 'D');
					$contd 		= 	$contd + 1;


			    }

			}


			/************** MENSAJES DEL DETALLE PEDIDO  ******************/
	    	$msjarray[] = array("data_0" => $conts, 
	    						"data_1" => 'pedidos aceptados', 
	    						"tipo" => 'TS');

	    	$msjarray[] = array("data_0" => $contw, 
	    						"data_1" => 'pedidos rechazados', 
	    						"tipo" => 'TW');	 

	    	$msjarray[] = array("data_0" => $contd, 
	    						"data_1" => 'pedidos errados', 
	    						"tipo" => 'TD');

			$msjarray[] = array("data_0" => $finicio, 
								"data_1" => $fechafin, 
								"tipo" => 'FE');

			$msjjson = json_encode($msjarray);

			return Redirect::to('/gestion-de-orden-de-pedido/'.$idopcion)->with('xmlmsj', $msjjson);

		
		}
	}


	public function actionListarTomaPedidoAnulacion($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

		$fechaquince = date('Y-m-j');
		$nuevafechaq = strtotime ( '-8 day' , strtotime($fechaquince));
		$nuevafechaq = date ('Y-m-j' , $nuevafechaq);
		$fechafin 	 = date_format(date_create($nuevafechaq), 'd-m-Y');

		//fecha actual 15 dias
	    $centro_id 				= 	Session::get('centros')->COD_CENTRO;


	    $listapedidos			= 	WEBPedido::where('activo','=',1)
		    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
		    						->whereIn('estado_id', ['EPP0000000000003'])
			    					//->where('fecha_venta','>=', $fechainicio)
			    					//->where('fecha_venta','<=', $fechafin)
									//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
									//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
									->Centro($centro_id)
		    						->orderBy('fecha_venta', 'desc')
		    						->get();


		$funcion 					= 	$this;

		return View::make('pedido/listatomapedidoanulacion',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'listapedidos' 	=> $listapedidos,
						 	'fechafin' 		=> $fechafin,
						 	'funcion' 		=> $funcion,
						 ]);
	}



	public function actionListarTomaPedido($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/


	    if (Session::get('xmlmsj')){

	    	$obj 				= 	json_decode(Session::get('xmlmsj'));
	    	$pfi 				= 	array_search('FE', array_column($obj, 'tipo'));
	    	$pff 				= 	array_search('FE', array_column($obj, 'tipo'));
            $fechainicio 		= 	$obj[$pfi]->data_0;
            $fechafin 			= 	$obj[$pff]->data_1;

	    }else{

		    $fechainicio  		= 	$this->fecha_menos_quince;
		    $fechafin  			= 	$this->fin;

	    }

	    $centro_id 				= 	Session::get('centros')->COD_CENTRO;



	    $listapedidos			= 	WEBPedido::where('activo','=',1)
		    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
		    						->whereIn('estado_id', ['EPP0000000000003'])
			    					->where('fecha_venta','>=', $fechainicio)
			    					->where('fecha_venta','<=', $fechafin)
									//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
									//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
									->Centro($centro_id)
		    						->orderBy('fecha_venta', 'desc')
		    						->get();


		$combo_estados  		= 	array('EPP0000000000003' => "AUTORIZADO",
									'EPP0000000000004' => "EJECUTADO",
									'EPP0000000000005' => "RECHAZADO",
									'TODOS' => "TODOS");


		$funcion 					= 	$this;

		return View::make('pedido/listatomapedido',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'listapedidos' 	=> $listapedidos,
						 	'fechainicio' 	=> $fechainicio,
						 	'fechafin' 		=> $fechafin,
						 	'funcion' 		=> $funcion,
						 	'combo_estados' => $combo_estados,
						 ]);
	}


	public function actionAjaxListarTomaPedido(Request $request)
	{

		$finicio 		=  date_format(date_create($request['finicio']), 'd-m-Y');
		$ffin 			=  date_format(date_create($request['ffin']), 'd-m-Y');
		$estado_id 		=  $request['estado_id'];
		$centro_id 	 	= 	Session::get('centros')->COD_CENTRO;


		if($estado_id == 'TODOS'){

		    $listapedidos	= 	WEBPedido::where('activo','=',1)
					->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
					->whereIn('estado_id', ['EPP0000000000003','EPP0000000000004'])
					//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
					//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
					->Centro($centro_id)
    				->where('fecha_venta','>=', $finicio)
    				->where('fecha_venta','<=', $ffin)
					->orderBy('fecha_venta', 'desc')
					->get();
		}else{
		    $listapedidos	= 	WEBPedido::where('activo','=',1)
					->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
					->whereIn('estado_id', [$estado_id])
					//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
					//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
					->Centro($centro_id)
    				->where('fecha_venta','>=', $finicio)
    				->where('fecha_venta','<=', $ffin)
					->orderBy('fecha_venta', 'desc')
					->get();			
		}


	    			
		$funcion 		= 	$this;

		return View::make('pedido/ajax/listatomapedido',
						 [
							 'listapedidos'   => $listapedidos,
							 'ajax'   		  => true,
							 'funcion'   	  => $funcion,
						 ]);
	}




	public function actionAjaxDetallePedidoTransportista(Request $request)
	{
		
		$pedido_id_encriptado 		= 	$request['pedido_id'];
		$pedido_id 					= 	$request['pedido_id'];

		$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$pedido_id,8);

		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();

		$detalle_pedido 			=   WEBDetallePedido::where('pedido_id','=',$pedido_id)
	    								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
										->where('activo','=',1)
										->get();



		$funcion 					= 	$this;			

	    $empresas 					= 	STDEmpresa::where('COD_ESTADO','=','1')->where('IND_SISTEMA','=','1')
	    					 			->pluck('NOM_EMPR','COD_EMPR')
										->toArray();
		$comboempresas 				= 	$empresas;


	    $orden_detalle 				= 	WEBDetallePedido::where('activo','=','1')
	    								->where('estado_id','=','EPP0000000000004')
	    								->where('ind_obsequio','=','0')
	    								->where('pedido_id','=',$pedido_id)
	    					 			->pluck('orden_id','orden_id')
										->toArray();

		$comboorden_detalle 		= 	array('' => "Venta asociada") + $orden_detalle;



		return View::make('pedido/ajax/modaldetallepedidotransportista',
						 [
							 'pedido_id'   				=> $pedido_id_encriptado,
							 'pedido'   				=> $pedido,
							 'detalle_pedido'   		=> $detalle_pedido,
							 'comboempresas'   			=> $comboempresas,
							 'funcion'   				=> $funcion,
							 'comboorden_detalle'   	=> $comboorden_detalle,
						 ]);
	}




	public function actionAjaxDetallePedido(Request $request)
	{
		
		$pedido_id_encriptado 		= 	$request['pedido_id'];
		$pedido_id 					= 	$request['pedido_id'];
		$data_json_detalle 			= 	$request['data_json_detalle'];
		$array_detalle_pedido 		=   json_decode($data_json_detalle);


		$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$pedido_id,8);

		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();

		$detalle_pedido 			=   WEBDetallePedido::where('pedido_id','=',$pedido_id)
	    								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
										->where('activo','=',1)
										->get();

		$funcion 					= 	$this;			

	    $empresas 					= 	STDEmpresa::where('COD_ESTADO','=','1')->where('IND_SISTEMA','=','1')
	    					 			->pluck('NOM_EMPR','COD_EMPR')
										->toArray();
		$comboempresas 				= 	$empresas;


	    $orden_detalle 				= 	WEBDetallePedido::where('activo','=','1')
	    								->where('estado_id','=','EPP0000000000004')
	    								->where('ind_obsequio','=','0')
	    								->where('pedido_id','=',$pedido_id)
	    					 			->pluck('orden_id','orden_id')
										->toArray();

		$comboorden_detalle 		= 	array('' => "Venta asociada") + $orden_detalle;



		return View::make('pedido/ajax/modaldetallepedido',
						 [
							 'pedido_id'   				=> $pedido_id_encriptado,
							 'pedido'   				=> $pedido,
							 'detalle_pedido'   		=> $detalle_pedido,
							 'comboempresas'   			=> $comboempresas,
							 'funcion'   				=> $funcion,
							 'array_detalle_pedido'   	=> $array_detalle_pedido,
							 'comboorden_detalle'   	=> $comboorden_detalle,
						 ]);
	}

	public function actionAjaxModalPedidoAnulacionObservcion(Request $request)
	{
		
		$funcion 					= 	$this;			
		$idopcion 					= 	$request['idopcion'];	

	    $motivos 					= 	CMPCategoria::where('TXT_GRUPO','=','PEDIDO_MOTIVO')->where('COD_ESTADO','=','1')
	    					 			->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();
		$combomotivos 				= 	$motivos;


		return View::make('pedido/ajax/modaldetallepedidoanulacionobservacion',
						 [
							 'combomotivos'   			=> $combomotivos,
							 'idopcion'   				=> $idopcion,
						 ]);
	}

	public function actionAjaxDetallePedidoAnulacion(Request $request)
	{
		
		$pedido_id_encriptado 		= 	$request['pedido_id'];
		$pedido_id 					= 	$request['pedido_id'];
		$data_json_detalle 			= 	$request['data_json_detalle'];
		$array_detalle_pedido 		=   json_decode($data_json_detalle);


		$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$pedido_id,8);

		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();

		$detalle_pedido 			=   WEBDetallePedido::where('pedido_id','=',$pedido_id)
	    								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
										->where('activo','=',1)
										->get();

		$funcion 					= 	$this;			

	    $empresas 					= 	STDEmpresa::where('COD_ESTADO','=','1')->where('IND_SISTEMA','=','1')
	    					 			->pluck('NOM_EMPR','COD_EMPR')
										->toArray();
		$comboempresas 				= 	$empresas;


	    $orden_detalle 				= 	WEBDetallePedido::where('activo','=','1')
	    								->where('estado_id','=','EPP0000000000004')
	    								->where('ind_obsequio','=','0')
	    								->where('pedido_id','=',$pedido_id)
	    					 			->pluck('orden_id','orden_id')
										->toArray();

		$comboorden_detalle 		= 	array('' => "Venta asociada") + $orden_detalle;


		$orden_venta_obsequio 		= 	WEBDetallePedido::where('activo','=','1')
	    								->where('estado_id','=','EPP0000000000004')
	    								->where('ind_obsequio','=','1')
	    								->where('pedido_id','=',$pedido_id)
	    					 			->get();

	   	$mensaje 					= 	'';
	   	if(count($orden_venta_obsequio)){
	   		$mensaje 					= 	'Existen obsequios que se atendieron en su totalidad';
	   	}


		return View::make('pedido/ajax/modaldetallepedidoanulacion',
						 [
							 'pedido_id'   				=> $pedido_id_encriptado,
							 'pedido'   				=> $pedido,
							 'detalle_pedido'   		=> $detalle_pedido,
							 'comboempresas'   			=> $comboempresas,
							 'funcion'   				=> $funcion,
							 'array_detalle_pedido'   	=> $array_detalle_pedido,
							 'comboorden_detalle'   	=> $comboorden_detalle,
							 'mensaje'   				=> $mensaje,
						 ]);
	}


	public function actionListarPedido($idopcion)
	{


		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/


	    $fechainicio  		= 	$this->fin;
	    $fechafin  			= 	$this->fin;

		$centro_id 			= 	Session::get('centros')->COD_CENTRO;


	    $listapedidos		= 	WEBPedido::where('activo','=',1)
    							->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
    							->where('usuario_crea','=',Session::get('usuario')->id)
								//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
								//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
								->Centro($centro_id)
			    				->where('fecha_venta','>=', $fechainicio)
			    				->where('fecha_venta','<=', $fechafin)
    							->orderBy('fecha_venta', 'desc')
    							->get();

		$funcion 			= 	$this;	

		return View::make('pedido/listapedido',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'listapedidos' 	=> $listapedidos,
						 	'funcion' 		=> $funcion,
						 	'fechainicio' 	=> $fechainicio,
						 	'fechafin' 		=> $fechafin,
						 ]);

	}

	public function actionAjaxListarTomaPedidoVendedor(Request $request)
	{

		$finicio 		=  date_format(date_create($request['finicio']), 'd-m-Y');
		$ffin 			=  date_format(date_create($request['ffin']), 'd-m-Y');
		$idopcion 		=  $request['idopcion'];

		$centro_id 				= 	Session::get('centros')->COD_CENTRO;

	    $listapedidos	= 	WEBPedido::where('activo','=',1)
							->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
							->where('usuario_crea','=',Session::get('usuario')->id)
							//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
							//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
							->Centro($centro_id)
				    		->where('fecha_venta','>=', $finicio)
				    		->where('fecha_venta','<=', $ffin)
							->orderBy('fecha_venta', 'desc')
							->get();
		$funcion 		= 	$this;	

		return View::make('pedido/ajax/listatomapedidovendedor',
						 [
						 	'listapedidos' 	=> $listapedidos,
						 	'funcion' 		=> $funcion,
						 	'fechainicio' 	=> $finicio,
						 	'fechafin' 		=> $ffin,
						 	'idopcion' 		=> $idopcion,
						 	'ajax'   		=> true,
						 ]);

	}


	
	public function actionAgregarOrdenPedido($idopcion ,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
			
		if($_POST)
		{

			try{

				DB::beginTransaction();

				$productos 					= 	$request['productos'];
				$c_tipo_documento 			= 	$request['c_tipo_documento'];
				$c_tipo_venta 				= 	$request['c_tipo_venta'];

				$total 						=   $this->funciones->calcular_cabecera_total($productos);
				$codigo 					= 	$this->funciones->generar_codigo('WEB.pedidos',8);
				$idpedido 					= 	$this->funciones->getCreateIdMaestra('WEB.pedidos');
				$cuenta_id 					= 	$this->funciones->desencriptar_id($request['cuenta'],10);
				$cliente_id 				= 	$this->funciones->desencriptar_id($request['cliente'],10);
				$tipocambio 				= 	$this->funciones->tipo_cambio();
				$direcion_entrega_id 		= 	$request['direccion_entrega'];
				$moneda_id 					= 	'MON0000000000001';
				$moneda_nombre 				= 	'SOLES';


				//VALIDAR SI YA TIENE PEDIDO
				$pedido_sin_atender 		= 	WEBPedido::where('activo','=','1')
												->whereIn('estado_id', ['EPP0000000000002','EPP0000000000003','EPP0000000000006'])
												->where('cliente_id','=',$cliente_id)->first();

				// if(count($pedido_sin_atender)>0){
				// 	return Redirect::to('/agregar-orden-pedido/'.$idopcion)->with('errorbd', 'Este cliente tiene pedido por atender ('.$pedido_sin_atender->codigo.')');
				// }

				//VALIDAR SI YA TIENE PEDIDO
				$ind_relacionreglacanal     =   0;
				$contrato_id 				= 	$cuenta_id;
				$contrato					=	CMPContrato::where('COD_CONTRATO','=',$contrato_id)->first();
				$relacionreglacanal 		=   CMPCategoria::where('TXT_GRUPO','=','RELACION_COMER_REGLA')
												->where('TXT_TIPO_REFERENCIA','=',$contrato->COD_CATEGORIA_CANAL_VENTA)
												->where('TXT_REFERENCIA','=',$contrato->COD_CATEGORIA_SUB_CANAL)
												->where('COD_ESTADO','=',1)
												->first();
				if(count($relacionreglacanal)>0){
					$ind_relacionreglacanal     =   1;
				}

				$limite_credito 			= 	0.00;
				$regla_credito 				= 	WEBReglaCreditoCliente::where('cliente_id','=',$cliente_id)
												->where('activo',1)
												->first();
		        if(count($regla_credito)>0){
		        	$limite_credito 			= 	$regla_credito->canlimitecredito;
		        }
		        //deuda vencida 			
		        $tipo_contrato 				= 	'carteradv';
				$deuda_cliente_vencida 		= 	$this->funciones->total_deuda_cliente($cliente_id,$this->fechaactual,$tipo_contrato);
				//deuda general 
		        $tipo_contrato 				= 	'carteradg';
				$deuda_cliente_general		= 	$this->funciones->total_deuda_cliente($cliente_id,$this->fechaactual,$tipo_contrato);

				$adicional_limite_credito 	=	0;

				$adicionallimitecredito 	= 	WEBAsignarRegla::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.asignarreglas.regla_id')
												->where('WEB.asignarreglas.prefijo','=','RLC')
												->where('WEB.asignarreglas.activo','=',1)
												->where('WEB.asignarreglas.tabla_id','=',$cliente_id)
												->select('WEB.reglas.*')
												->first();

				if(count($adicionallimitecredito)>0){
					$adicional_limite_credito 	=	$adicionallimitecredito->descuento;
				}

				$pedido_sin_atender 		= 	WEBPedido::where('activo','=','1')
												->whereIn('estado_id', ['EPP0000000000002','EPP0000000000003','EPP0000000000006'])
												->where('cliente_id','=',$cliente_id)->first();

				$deuda_osyza 				=   $this->funciones->deuda_total_oryza_generado_autorizado($cliente_id);

        		$suma_total_cgeneral       =   $deuda_cliente_general + $total + $deuda_osyza;
		        if($ind_relacionreglacanal == '1'){
		           	$lcd     = ($limite_credito + $adicional_limite_credito);
		            //si tienes deudas por vencer y limite de credito 
		            if($deuda_cliente_vencida>0){
		            	return Redirect::to('/agregar-orden-pedido/'.$idopcion)->with('errorbd', 'Cliente tiene una deuda vencida');
		            }
		            if($request['condicion_pago'] != 'TIP0000000000001'){
			            if($lcd>0){
			                if($suma_total_cgeneral>$lcd){
			                	return Redirect::to('/agregar-orden-pedido/'.$idopcion)->with('errorbd', 'Cliente ya supero su línea de crédito (venta actual + deuda general + deuda oryza)');
			                }
			            }
		            }

		        }

		        //validar una sola categoria
		        $sw_ov 							=	0;
		        $sw_pa 							=	0;
				$productosv 					= 	json_decode($productos, true);
				foreach($productosv as $obj){
					$producto_id 				= 	$this->funciones->desencriptar_id($obj['prefijo_producto'].'-'.$obj['id_producto'],13);
					$producto 					=	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
					if($producto->COD_CATEGORIA_FAMILIA == 'FAM0000000000061' || $producto->COD_CATEGORIA_FAMILIA == 'FAM0000000000062'){
						$sw_pa 							=	1;
					}else{
						$sw_ov 							=	1;
					}
				}	
                if($sw_pa == 1 && $sw_ov == 1){
                	return Redirect::to('/agregar-orden-pedido/'.$idopcion)->with('errorbd', 'Hay productos que pertenecen a dos famlias diferentes no se puede realizar la venta');
                }




				//PEDIDO
				$cabecera            	 	=	new WEBPedido;
				$cabecera->id 	     	 	=  	$idpedido;
				$cabecera->codigo 	    	=  	$codigo;
				$cabecera->igv 	    		=  	$this->funciones->calculo_igv($total);
				$cabecera->subtotal 	    =  	$this->funciones->calculo_subtotal($total);
				$cabecera->total 	    	=  	$total;
				$cabecera->estado 	    	=  	'EM';
				$cabecera->cuenta_id 	    =  	$cuenta_id; 
				$cabecera->cliente_id 	    =  	$cliente_id;

				$cabecera->tipo_cambio 	    =  	$tipocambio->CAN_COMPRA; 
				$cabecera->moneda_id 	    =  	$moneda_id;
				$cabecera->moneda_nombre 	=  	$moneda_nombre; 



				$cabecera->direccion_entrega_id 	    =  	$direcion_entrega_id;
				$cabecera->empresa_id 		=   Session::get('empresas')->COD_EMPR;
				$cabecera->centro_id 		=   Session::get('centros')->COD_CENTRO;
				$cabecera->fecha_venta 	 	=   $this->fin;
				$cabecera->fecha_time_venta =   $this->fechaactual;
				$cabecera->fecha_crea 	 	=   $this->fechaactual;
				$cabecera->usuario_crea 	=   Session::get('usuario')->id;

				//adicional

				$cabecera->fecha_despacho   			= 	$request['fecha_entrega'];
				$cabecera->estado_id   					= 	'EPP0000000000002';
				$cabecera->tipopago_id   				= 	$request['condicion_pago'];
				$cabecera->tipo_documento   			= 	$c_tipo_documento;
				$cabecera->tipo_venta   				= 	$c_tipo_venta;
				$cabecera->contacto_gestion_transporte   			= 	$request['contatogestiontransporte'];



				$cabecera->recibo_conformidad  			= 	$request['recibo'];
				$cabecera->nro_orden_cen  				= 	$request['ordencen'];
				$cabecera->glosa  						= 	$request['obs'];


				$cabecera->ind_notificacion 	    	=  	0;
				$cabecera->ind_notificacion_autorizacion =   -1;
				$cabecera->ind_notificacion_rechazado 	=   -1;
				$cabecera->ind_notificacion_despacho 	=   -1;
				$np=$cabecera;
				$cabecera->save();


		        //GUARDAR LAS REGLAS 		
		        $lista_deuda 				= 	$this->funciones->lista_deuda_cliente($cliente_id,$this->fechaactual);
	    		foreach($lista_deuda as $index => $item){
	    			if($item['REGLA_ID']<>'' or $item['REGLA_ID']<> NULL){

						$idreglaproductocliente 	= 	$this->funciones->getCreateIdMaestra('WEB.asignarreglas');
						$cabecera            	 	=	new WEBAsignarRegla;
						$cabecera->id 	     	 	=  	$idreglaproductocliente;
						$cabecera->regla_id 	    =  	$item['asignarregla_id'];
						$cabecera->prefijo 	    	=  	'ARP'; // ASIGNAR REGLA PEDIDO
						$cabecera->tabla 	    	=  	'WEB.pedidos';
						$cabecera->tabla_id 	    =  	$idpedido;
						$cabecera->fecha_crea 	    =  	$this->fechaactual;
						$cabecera->empresa_id 		=  	Session::get('empresas')->COD_EMPR;
						$cabecera->centro_id 		=  	Session::get('centros')->COD_CENTRO;
						$cabecera->usuario_crea 	=  	Session::get('usuario')->id;
						$cabecera->save();
	    			}
	    		}



				if(count($adicionallimitecredito)>0){
						$idreglaproductocliente 	= 	$this->funciones->getCreateIdMaestra('WEB.asignarreglas');
						$cabecera            	 	=	new WEBAsignarRegla;
						$cabecera->id 	     	 	=  	$idreglaproductocliente;
						$cabecera->regla_id 	    =  	$adicionallimitecredito->id;
						$cabecera->prefijo 	    	=  	'ARP'; // ASIGNAR REGLA PEDIDO
						$cabecera->tabla 	    	=  	'WEB.pedidos';
						$cabecera->tabla_id 	    =  	$idpedido;
						$cabecera->fecha_crea 	    =  	$this->fechaactual;
						$cabecera->empresa_id 		=  	Session::get('empresas')->COD_EMPR;
						$cabecera->centro_id 		=  	Session::get('centros')->COD_CENTRO;
						$cabecera->usuario_crea 	=  	Session::get('usuario')->id;
						$cabecera->save();
				}

				//DETALLE PEDIDO

				$productos 					= 	json_decode($productos, true);

				foreach($productos as $obj){

					$iddetallepedido 			= 	$this->funciones->getCreateIdMaestra('WEB.detallepedidos');
					$precio_producto 			=  	(float)$obj['precio_producto'];
					$cantidad_producto 			=  	(float)$obj['cantidad_producto'];
					$obsequio 					=  	(int)$obj['obsequio'];
					$ind_producto_obsequio 		=  	(int)$obj['ind_producto_obsequio'];

					
					$total_producto 			= 	0;
					if($obsequio=='0'){
						$total_producto 			= 	$precio_producto*$cantidad_producto;
					}
					
					$producto_id 				= 	$this->funciones->desencriptar_id($obj['prefijo_producto'].'-'.$obj['id_producto'],13);

					$cabecera            	 	=	new WEBDetallePedido;
					$cabecera->id 	     	 	=  	$iddetallepedido;
					$cabecera->precio 	    	=  	$precio_producto;
					$cabecera->cantidad 	    =  	$cantidad_producto;
					$cabecera->igv 	    		=  	$this->funciones->calculo_igv($total_producto);
					$cabecera->subtotal 	    =  	$this->funciones->calculo_subtotal($total_producto);
					$cabecera->total 	    	=  	$total_producto;
					$cabecera->pedido_id 	    =  	$idpedido;
					$cabecera->producto_id 	    =  	$producto_id;
					$cabecera->estado_id 		= 	'EPP0000000000002';
					$cabecera->empresa_id 		=   Session::get('empresas')->COD_EMPR;
					$cabecera->centro_id 		=   Session::get('centros')->COD_CENTRO;
					$cabecera->fecha_crea 	 	=   $this->fechaactual;
					$cabecera->usuario_crea 	=   Session::get('usuario')->id;
					$cabecera->ind_obsequio 		=   $obsequio;
					$cabecera->ind_producto_obsequio =   $ind_producto_obsequio;
					$cabecera->save();
				}			

				DB::commit();
				//

 				return Redirect::to('/gestion-de-toma-de-pedido/'.$idopcion)->with('bienhecho', 'Pedido '.$codigo.' registrado con exito');

			}catch(Exception $ex){
				DB::rollback();
				return Redirect::to('/gestion-de-toma-de-pedido/'.$idopcion)->with('errorbd', 'Ocurrio un error inesperado. Porfavor contacte con el administrador del sistema');	
			}

		}else{


			//adicionar clientes
			$id_vendedor_adicionar = '';

			if(Session::get('usuario')->fuerzaventa_id=='JVE0000000000016'){
				$id_vendedor_adicionar = 'adicionar';
			}

			if(Session::get('usuario')->fuerzaventa_id=='JVE0000000000091'){
				$id_vendedor_adicionar = 'adicionarcix';
			}

			if(Session::get('usuario')->fuerzaventa_id=='JVE0000000000102'){
				$id_vendedor_adicionar = 'adicionarm';
			}


		    $listaclientes 		= 	WEBListaCliente::where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
									->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
									->Adicionarvendedor($id_vendedor_adicionar)
									->orderBy('NOM_EMPR', 'asc')
									->get();
	
			$tipo_comp 			=	'';
			$combotipocom      	=   array('' => "Seleccione Tipo de comprobante",'SIN_COMPROBANTE' => "SIN COMPROBANTE",
											'BOLETA' => "BOLETA",
											'FACTURA' => "FACTURA");

			$listaproductos 	= 	DB::table('WEB.LISTAPRODUCTOSAVENDER')
									->where('IND_MOVIL','=',1)
		    					 	->orderBy('NOM_PRODUCTO', 'asc')->get();

			$tipo_orden 		=	'ORDEN_VENTA';
			$combotipoorden     =   array('ORDEN_VENTA' => "ORDEN VENTA",
										  'ENTREGA_VENTA' => "ENTREGA VENTA (Solo Cuando es Factura de Anticipo)");

			return View::make('pedido/ordenpedido',
						[				
						  	'idopcion'  			=> $idopcion,
						  	'listaclientes'  		=> $listaclientes,
						  	'tipo_comp'  			=> $tipo_comp,
						  	'combotipocom'  		=> $combotipocom,

						  	'tipo_orden'  			=> $tipo_orden,
						  	'combotipoorden'  		=> $combotipoorden,

						  	'listaproductos'  		=> $listaproductos,
						]);
		}
	}



	public function actionObsequioOrdenPedido($idpedido,$idopcion,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		$pedido_id 					= 	$this->funciones->desencriptar_id('1CIX-'.$idpedido,8);
		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();

		if($pedido->estado_id != 'EPP0000000000002'){
			return Redirect::back()->withInput()->with('errorbd', 'El pedido tiene que estar en estado generado');
		}

		if($_POST)
		{

			try{

				DB::beginTransaction();

				$productos 					= 	$request['productos'];
				//DETALLE PEDIDO

				$productos 					= 	json_decode($productos, true);

				foreach($productos as $obj){

					$iddetallepedido 			= 	$this->funciones->getCreateIdMaestra('WEB.detallepedidos');
					$precio_producto 			=  	(float)$obj['precio_producto'];
					$cantidad_producto 			=  	(float)$obj['cantidad_producto'];
					$obsequio 					=  	(int)$obj['obsequio'];

					$total_producto 			= 	$precio_producto*$cantidad_producto;
					$producto_id 				= 	$this->funciones->desencriptar_id($obj['prefijo_producto'].'-'.$obj['id_producto'],13);

					$ind_producto_obsequio 		=  	(int)$obj['ind_producto_obsequio'];

					$cabecera            	 	=	new WEBDetallePedido;
					$cabecera->id 	     	 	=  	$iddetallepedido;
					$cabecera->precio 	    	=  	$precio_producto;
					$cabecera->cantidad 	    =  	$cantidad_producto;
					$cabecera->igv 	    		=   0.00;
					$cabecera->subtotal 	    =  	0.00;
					$cabecera->total 	    	=  	0.00;
					$cabecera->pedido_id 	    =  	$pedido->id;
					$cabecera->producto_id 	    =  	$producto_id;
					$cabecera->empresa_id 		=   Session::get('empresas')->COD_EMPR;
					$cabecera->centro_id 		=   Session::get('centros')->COD_CENTRO;
					$cabecera->fecha_crea 	 	=   $this->fechaactual;
					$cabecera->usuario_crea 	=   Session::get('usuario')->id;
					$cabecera->ind_obsequio 	=   $obsequio;
					$cabecera->estado_id 		= 	'EPP0000000000002';
					$cabecera->ind_producto_obsequio =   $ind_producto_obsequio;
					$cabecera->save();
				}			

				$pedido->estado_id = 'EPP0000000000002';
				$pedido->save();
				DB::commit();
				//
 				return Redirect::to('/gestion-de-toma-de-pedido/'.$idopcion)->with('bienhecho', 'Pedido '.$pedido->codigo.' modificado con exito');

			}catch(Exception $ex){
				DB::rollback();
				return Redirect::to('/gestion-de-toma-de-pedido/'.$idopcion)->with('errorbd', 'Ocurrio un error inesperado. Porfavor contacte con el administrador del sistema');	
			}

		}else{

			$funcion 					= 	$this;		
			$listaproductos 			= 	DB::table('WEB.LISTAPRODUCTOSAVENDER')
											->where('IND_MOVIL','=',1)
		    					 			->orderBy('NOM_PRODUCTO', 'asc')->get();

		    $contrato  					=   WEBListaCliente::where('id','=',$pedido->cliente_id)->first();
			$funcion 					= 	$this;



			$ids_detalle 				= 	WEBDetallePedido::where('pedido_id','=',$pedido->id)
											->select(DB::raw('max(id) as id_d'))
											->where('activo','=',1)
											->where('estado_id','<>','EPP0000000000005')
											->groupBy('ind_producto_obsequio')
											->having(DB::raw('count(ind_producto_obsequio)'), '<=', 1)
											->pluck('id_d')
											->toArray();

			$combo_relacion   			=	WEBDetallePedido::join('ALM.PRODUCTO', 'COD_PRODUCTO', '=', 'producto_id')
											->whereIn('id',$ids_detalle)
						    				->select(DB::raw('ALM.PRODUCTO.NOM_PRODUCTO,ind_producto_obsequio'))
											->pluck('ALM.PRODUCTO.NOM_PRODUCTO','ind_producto_obsequio')
											->toArray();

			return View::make('pedido/ordenpedidoobsequio',
						[				
						  	'listaproductos'  		=> $listaproductos,
						  	'idopcion'  			=> $idopcion,
						  	'pedido'  				=> $pedido,
						  	'contrato'  			=> $contrato,
						  	'funcion'  				=> $funcion,
						  	'combo_relacion'  		=> $combo_relacion,
						]);
		}
	}








	public function actionAjaxDireccioncliente(Request $request)
	{

		$data_icl 					=  	$request['data_icl']; //id_cliente
		$data_pcl 					=  	$request['data_pcl']; //prefijo_cliente
		$data_icu 					=  	$request['data_icu']; //id_contrato
		$data_pcu 					=  	$request['data_pcu']; //prefijo_contrato
		$data_ncl 					=  	$request['data_ncl']; //nombre_cliente
		$data_dcl 					=  	$request['data_dcl']; //documento_cliente
		$data_ccl 					=  	$request['data_ccl']; //cuenta_cliente
		$data_icontrato 			=  	$request['data_icontrato']; //cuenta_cliente

		$ind_relacionreglacanal     =   0;

		$cliente_id 				= 	$this->funciones->desencriptar_id($data_pcl.'-'.$data_icl,10);
		$contrato_id 				= 	$data_icontrato;

		$contrato					=	CMPContrato::where('COD_CONTRATO','=',$contrato_id)->first();

		$relacionreglacanal 		=   CMPCategoria::where('TXT_GRUPO','=','RELACION_COMER_REGLA')
										->where('TXT_TIPO_REFERENCIA','=',$contrato->COD_CATEGORIA_CANAL_VENTA)
										->where('TXT_REFERENCIA','=',$contrato->COD_CATEGORIA_SUB_CANAL)
										->where('COD_ESTADO','=',1)
										->first();

		if(count($relacionreglacanal)>0){
			$ind_relacionreglacanal     =   1;
		}

		$arraytpadicional 			=	array();

		// if($contrato->COD_CATEGORIA_CANAL_VENTA == 'CVE0000000000003'){
		// 	$arraytpadicional 		=	array('TIP0000000000002' => "CREDITO A 7 DÍAS" , 'TIP0000000000003' => "CREDITO A 15 DÍAS",'TIP0000000000004' => "CREDITO A 21 DÍAS");
		// }

	    $direcciones 				= 	DB::table('WEB.LISTADIRECCION')
	    					 			->orderBy('IND_DIRECCION_FISCAL', 'desc')
	    					 			->where('COD_EMPR','=',$cliente_id)
	    					 			->pluck('NOM_DIRECCION','COD_DIRECCION')
										 ->toArray();
										 
		$pagocontado                =   DB::table('CMP.CATEGORIA')
										 ->where('COD_CATEGORIA','=','TIP0000000000001')
										 ->pluck('NOM_CATEGORIA','COD_CATEGORIA');
										 
	
		$tipopago                   =   DB::table('CMP.CATEGORIA as CA')
		                                ->leftJoin('WEB.reglacreditoclientes as RC', 'CA.COD_CATEGORIA', '=', 'RC.condicionpago_id')
										->orderBy('NOM_CATEGORIA', 'desc')
										->where('COD_ESTADO','=',1)
										->where('COD_CATEGORIA','<>','TIP0000000000001')
										->where('RC.cliente_id','=',$cliente_id)
										->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->union($pagocontado)
										->toArray();

		$reglacredito 		= 	WEBReglaCreditoCliente::where('cliente_id','=',$cliente_id)
										->where('activo',1)
										->get();
			
		$saldocli=DB::select('exec RPS.SALDO_TRAMO_CUENTA ?,?,?,?,?,?,?,?,?,?,?', array('','','','',date("Y-m-d"),$cliente_id,'TCO0000000000068','','','',''));

		$combotipopago              =   array('' => "Seleccione el tipo de pago")  + $arraytpadicional + $tipopago;
		$combodirecciones  			= 	array('' => "Seleccione dirección") + $direcciones;

		$limite_credito 			= 	0.00;
		$regla_credito 				= 	WEBReglaCreditoCliente::where('cliente_id','=',$cliente_id)
										->where('activo',1)
										->first();

        if(count($regla_credito)>0){
        	$limite_credito 			= 	$regla_credito->canlimitecredito;
        }

        //deuda vencida 			
        $tipo_contrato 				= 	'carteradv';
		$deuda_cliente_vencida 		= 	$this->funciones->total_deuda_cliente($cliente_id,$this->fechaactual,$tipo_contrato);
		//deuda general 
        $tipo_contrato 				= 	'carteradg';
		$deuda_cliente_general		= 	$this->funciones->total_deuda_cliente($cliente_id,$this->fechaactual,$tipo_contrato);

		$deuda_osyza 				=   $this->funciones->deuda_total_oryza_generado_autorizado($cliente_id);


		$adicional_limite_credito 	=	0;
		$adicionallimitecredito 	= 	WEBAsignarRegla::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.asignarreglas.regla_id')
										->where('WEB.asignarreglas.prefijo','=','RLC')
										->where('WEB.asignarreglas.activo','=',1)
										->where('WEB.asignarreglas.tabla_id','=',$cliente_id)
										->first();

		if(count($adicionallimitecredito)>0){
			$adicional_limite_credito 	=	$adicionallimitecredito->descuento;
		}

		$pedido_sin_atender 		= 	WEBPedido::where('activo','=','1')
										->whereIn('estado_id', ['EPP0000000000002','EPP0000000000003','EPP0000000000006'])
										->where('cliente_id','=',$cliente_id)->first();


		return View::make('pedido/ajax/direccion',
						 [
							'combodirecciones' 	=> $combodirecciones,
							'combotipopago'     => $combotipopago,
						 	'data_icl' 			=> $data_icl,
						 	'data_pcl' 			=> $data_pcl,
						 	'data_icu' 			=> $data_icu,
						 	'data_pcu' 			=> $data_pcu,
						 	'data_ncl' 			=> $data_ncl,
						 	'data_dcl' 			=> $data_dcl,
							'data_ccl' 		    => $data_ccl,
							'reglacredito'      => $reglacredito,
							'limite_credito'    => $limite_credito,
							'deuda_cliente_vencida'    	=> $deuda_cliente_vencida,
							'deuda_cliente_general'    	=> $deuda_cliente_general,
							'deuda_osyza'    	=> $deuda_osyza,
							'adicional_limite_credito'    	=> $adicional_limite_credito,

							'contrato'      			=> $contrato,
							'ind_relacionreglacanal'    => $ind_relacionreglacanal,

							'saldocli'          => $saldocli,
							'pedido_sin_atender'          => $pedido_sin_atender,

						 ]);
	}



	public function actionImprimirDetalleDeuda(Request $request)
	{

		$cliente_id 				=  	$this->funciones->desencriptar_id($request['cliente_id'],10);
		$lista_deuda 				= 	$this->funciones->lista_deuda_cliente($cliente_id,$this->fechaactual);
		$cliente 					=	STDEmpresa::where('COD_EMPR','=',$cliente_id)->first();

		$limite_credito 			= 	0.00;
		$regla_credito 				= 	WEBReglaCreditoCliente::where('cliente_id','=',$cliente_id)
										->where('activo',1)
										->first();

        if(count($regla_credito)>0){
        	$limite_credito 			= 	$regla_credito->canlimitecredito;
        }

        //deuda vencida 			
        $tipo_contrato 				= 	'carteradv';
		$deuda_cliente_vencida 		= 	$this->funciones->total_deuda_cliente($cliente_id,$this->fechaactual,$tipo_contrato);
		//deuda general 
        $tipo_contrato 				= 	'carteradg';
		$deuda_cliente_general		= 	$this->funciones->total_deuda_cliente($cliente_id,$this->fechaactual,$tipo_contrato);


		return View::make('pedido/modal/ajax/amdetalledeuda',
						 [
							 'lista_deuda' 	=> $lista_deuda,
							 'cliente'     	=> $cliente,
							 'limite_credito' 	=> $limite_credito,
							 'deuda_cliente_vencida' 	=> $deuda_cliente_vencida,
							 'deuda_cliente_general' 	=> $deuda_cliente_general,

						 ]);

	}


	public function actionActualizarDeudaCliente(Request $request)
	{

		$cliente_id 				=  	$this->funciones->desencriptar_id($request['cliente_id'],10);
		// $cuenta 					=  	$this->funciones->desencriptar_id($request['cuenta'],10);

		// $contrato_id 				= 	$cuenta;

		// $ind_relacionreglacanal     =   0;

		// $contrato					=	CMPContrato::where('COD_CONTRATO','=',$contrato_id)->first();

		// $relacionreglacanal 		=   CMPCategoria::where('TXT_GRUPO','=','RELACION_COMER_REGLA')
		// 								->where('TXT_TIPO_REFERENCIA','=',$contrato->COD_CATEGORIA_CANAL_VENTA)
		// 								->where('TXT_REFERENCIA','=',$contrato->COD_CATEGORIA_SUB_CANAL)
		// 								->where('COD_ESTADO','=',1)
		// 								->first();

		// if(count($relacionreglacanal)>0){
		// 	$ind_relacionreglacanal     =   1;
		// }



		$lista_deuda 				= 	$this->funciones->lista_deuda_cliente($cliente_id,$this->fechaactual);
		$cliente 					=	STDEmpresa::where('COD_EMPR','=',$cliente_id)->first();


		$limite_credito 			= 	0.00;
		$regla_credito 				= 	WEBReglaCreditoCliente::where('cliente_id','=',$cliente_id)
										->where('activo',1)
										->first();

        if(count($regla_credito)>0){
        	$limite_credito 			= 	$regla_credito->canlimitecredito;
        }

		$deuda_osyza 				=   $this->funciones->deuda_total_oryza_generado_autorizado($cliente_id);

        //deuda vencida 			
        $tipo_contrato 				= 	'carteradv';
		$deuda_cliente_vencida 		= 	$this->funciones->total_deuda_cliente($cliente_id,$this->fechaactual,$tipo_contrato);
		//deuda general 
        $tipo_contrato 				= 	'carteradg';
		$deuda_cliente_general		= 	$this->funciones->total_deuda_cliente($cliente_id,$this->fechaactual,$tipo_contrato);

		$adicional_limite_credito 	=	0;
		$adicionallimitecredito 	= 	WEBAsignarRegla::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.asignarreglas.regla_id')
										->where('WEB.asignarreglas.prefijo','=','RLC')
										->where('WEB.asignarreglas.activo','=',1)
										->where('WEB.asignarreglas.tabla_id','=',$cliente_id)
										->first();

		if(count($adicionallimitecredito)>0){
			$adicional_limite_credito 	=	$adicionallimitecredito->descuento;
		}

    	$msjarray[] = array("lc" 		=> $limite_credito,
    						"alc" 		=> $adicional_limite_credito,
    						"dg" 		=> $deuda_cliente_general, 
    						"dv" 		=> $deuda_cliente_vencida,
    						"do" 		=> $deuda_osyza);

		$msjjson = json_encode($msjarray);

		return $msjjson;


	}



	public function actionAjaxReglaProducto(Request $request)
	{

		$data_ipr 					=  	$request['data_ipr']; 
		$data_ppr 					=  	$request['data_ppr']; 
		$data_npr 					=  	$request['data_npr']; 
		$data_upr 					=  	$request['data_upr']; 
	
		$cliente_id 				=  	$this->funciones->desencriptar_id($request['cli_id'],10);
		$cuenta_id                  = 	$this->funciones->desencriptar_id($request['cuenta_id'],10);

		$producto_id 				= 	$this->funciones->desencriptar_id($data_ppr.'-'.$data_ipr,13);
	

		$reglas		= 	WEBReglaProductoCliente::where('producto_id','=',$producto_id)
						->where('cliente_id','=',$cliente_id)
						->where('contrato_id','=',$cuenta_id)
						->where('activo',1)
						->get();

		$precioregular= WEBPrecioProductoContrato::where('producto_id','=',$producto_id)
		->where('contrato_id','=',$cuenta_id)
		->where('activo',1)
		->get();

		$precioestandar=WEBPrecioProducto::where('producto_id',"=",$producto_id)
		->where('activo',1)
		->get();

		return View::make('pedido/ajax/reglaproducto',
						 [
							 'producto_id' 	=> $producto_id,
							 'data_ipr'     => $data_ipr,
							 'data_ppr'     => $data_ppr,
							 'data_npr'     => $data_npr,
							 'data_upr'     => $data_upr,
							 'reglas'       =>$reglas,
							 'precioregular'=>$precioregular,
							 'precioestandar'=>$precioestandar
						 ]);
	}

	public function actionAjaxReglaProductoObsequio(Request $request)
	{

		$data_ipr 					=  	$request['data_ipr']; 
		$data_ppr 					=  	$request['data_ppr']; 
		$data_npr 					=  	$request['data_npr']; 
		$data_upr 					=  	$request['data_upr']; 
	
		$cliente_id 				=  	$request['cli_id'];
		$cuenta_id                  = 	$request['cuenta_id'];
		$producto_id 				= 	$this->funciones->desencriptar_id($data_ppr.'-'.$data_ipr,13);
	
		$pedido_id 					=  	$request['pedido_id']; 
		$relacionadas 				=  	$request['relacionadas']; 
		$arrayrelacionadas 			= 	explode(",", $relacionadas);

		$reglas		= 	WEBReglaProductoCliente::where('producto_id','=',$producto_id)
						->where('cliente_id','=',$cliente_id)
						->where('contrato_id','=',$cuenta_id)
						->where('activo',1)
						->get();

		$precioregular= WEBPrecioProductoContrato::where('producto_id','=',$producto_id)
		->where('contrato_id','=',$cuenta_id)
		->where('activo',1)
		->get();

		$precioestandar=WEBPrecioProducto::where('producto_id',"=",$producto_id)
		->where('activo',1)
		->get();


		$ids_detalle 				= 	WEBDetallePedido::where('pedido_id','=',$pedido_id)
										->select(DB::raw('max(id) as id_d'))
										->where('activo','=',1)
										->where('estado_id','<>','EPP0000000000005')
										->whereNotIn('ind_producto_obsequio', $arrayrelacionadas)
										->groupBy('ind_producto_obsequio')
										->having(DB::raw('count(ind_producto_obsequio)'), '<=', 1)
										->pluck('id_d')
										->toArray();

		$combo_relacion   			=	WEBDetallePedido::join('ALM.PRODUCTO', 'COD_PRODUCTO', '=', 'producto_id')
										->whereIn('id',$ids_detalle)
					    				->select(DB::raw('ALM.PRODUCTO.NOM_PRODUCTO,ind_producto_obsequio'))
										->pluck('ALM.PRODUCTO.NOM_PRODUCTO','ind_producto_obsequio')
										->toArray();

		//dd($combo_relacion);
		return View::make('pedido/ajax/reglaproductoobsequio',
						 [
							 'producto_id' 	=> $producto_id,
							 'data_ipr'     => $data_ipr,
							 'data_ppr'     => $data_ppr,
							 'data_npr'     => $data_npr,
							 'data_upr'     => $data_upr,
							 'reglas'       =>$reglas,
							 'precioregular'=>$precioregular,
							 'combo_relacion'=>$combo_relacion,
							 'precioestandar'=>$precioestandar
						 ]);
	}



	public function actionAjaxDeudaSectorizada(Request $request)
	{

		$data_icl					=  	$request['data_icl']; 
		$data_pcl 					=  	$request['data_pcl']; 
		
	

		$cliente_id 				= 	$this->funciones->desencriptar_id($data_pcl.'-'.$data_icl,10);

		// $reglas		= 	WEBReglaProductoCliente::where('producto_id','=',$producto_id)
		// 				->where('activo',1)
		// 				->get();

		

		$saldocuenta=DB::select('exec RPS.SALDO_TRAMO_CUENTA ?,?,?,?,?,?,?,?,?,?,?', array('','','','',date("Y-m-d"),$cliente_id ,'TCO0000000000068','','','',''));

		return [
		
			'saldocuenta' => $saldocuenta
		];
	}

	public function RepSalida()
    {
	   
		if($_POST)
		{

	   $pedido 			= json_decode($request['pedido'], true);
	
       $data = [
          'title' => 'First PDF for Medium',
          'heading' => 'Hello from 99Points.info',
          'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.' ];
        
        $pdf = PDF::loadView('emails/notificacionejecucion', $data);  
		return $pdf->download('medium.pdf');

	   }
    }
}
