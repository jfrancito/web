<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBTransferencia,App\WEBTransferenciaDetalle,App\WEBListaCliente,App\CMPContrato,App\WEBReglaCreditoCliente,App\WEBReglaProductoCliente,App\WEBPrecioProductoContrato,App\WEBPrecioProducto,App\WEBOpcion,App\ALMProducto,App\CMPCategoria,\App\WEBPicking,App\WEBPickingDetalle,App\User,App\CMPOrden;
use View;
use Session;
use App\Biblioteca\Osiris;
use App\Biblioteca\Funcion;
use PDO;
use Mail;
use PDF;
use Hashids,table;

class TransferenciaController extends Controller
{
	public function estadosTrasferencia(){
		return array('EPP0000000000002' => "GENERADO",
					'EPP0000000000007' => "CERRADO",
					'EPP0000000000003' => "AUTORIZADO",
					'EPP0000000000006' => "ATENDIDO PARCIALMENTE",
					'EPP0000000000004' => "EJECUTADO",
					'EPP0000000000005' => "RECHAZADO",
					'TODOS' => "TODOS");
	}

	public function estadosPicking(){
		return array('EPP0000000000002' => "GENERADO",
					'EPP0000000000006' => "ATENDIDO PARCIALMENTE",
					'EPP0000000000004' => "EJECUTADO",
					'TODOS' => "TODOS");
	}

	public function actionListarTransferencia($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	
	    $fechainicio  		= 	$this->fecha_menos_quince;
	    $fechafin  			= 	$this->fin;	    

	    $centro_id 				= Session::get('centros')->COD_CENTRO;
	    $codopcion	 			= $this->funciones->desencriptar_id('1CIX-'.$idopcion,8);
		$idestado_gen 			= "";
		$combo_estados   		= "";

		$opcion 			 = 	WEBOpcion::where('id','=',$codopcion)->first();

		$combo_estados  	=   $this->estadosTrasferencia();

		if($opcion->parametros == "Cerrar"){	// Solicitar Transferencia			
			$idestado_gen 		= "TODOS";			

		}elseif($opcion->parametros == "Autorizar"){ // Autorizar Transferencia
			$idestado_gen = "EPP0000000000007";
			//$combo_estados  	=  array('EPP0000000000007' => "CERRADO");

		}elseif($opcion->parametros == "Atender"){ // Atender Transferencia
			$idestado_gen = "EPP0000000000003";
			//$combo_estados  	=  array('EPP0000000000003' => "AUTORIZADO");
		}
		
		if($idestado_gen == 'TODOS'){

			$listapedidos	=  	WEBTransferencia::where('web.transferencia.activo','=',1)
		    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.transferencia.estado_id')
		    						->leftJoin('web.trans_destino', 'web.trans_destino.id', '=', 'web.transferencia.almacen_destino_id')
		    						->leftJoin('STD.EMPRESA', 'STD.EMPRESA.COD_EMPR', '=', 'web.transferencia.cliente_id')
			    					->where('fecha_pedido','>=', $fechainicio)
			    					->where('fecha_pedido','<=', $fechafin)
									->where('web.transferencia.empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('web.transferencia.centro_id','=',Session::get('centros')->COD_CENTRO)
									->select('web.transferencia.*','web.trans_destino.destino','CMP.CATEGORIA.NOM_CATEGORIA as estado_nom','STD.EMPRESA.NOM_EMPR as cliente_nom')
		    						->orderBy('fecha_pedido', 'desc')
		    						->get();
		}else{

						$listapedidos	=  	WEBTransferencia::where('web.transferencia.activo','=',1)
		    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.transferencia.estado_id')
		    						->leftJoin('web.trans_destino', 'web.trans_destino.id', '=', 'web.transferencia.almacen_destino_id')
			    					->where('fecha_pedido','>=', $fechainicio)
			    					->where('fecha_pedido','<=', $fechafin)
			    					->whereIn('estado_id', [$idestado_gen])
									->where('web.transferencia.empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('web.transferencia.centro_id','=',Session::get('centros')->COD_CENTRO)
									->select('web.transferencia.*','web.trans_destino.destino','CMP.CATEGORIA.NOM_CATEGORIA as estado_nom')
		    						->orderBy('fecha_pedido', 'desc')
		    						->get();
		}
		
		$funcion 				= 	$this;
	  
		return View::make('transferencia/listatransferencia',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'listapedidos' 	=> $listapedidos,
						 	'fechainicio' 	=> $fechainicio,
						 	'fechafin' 		=> $fechafin,
						 	'funcion' 		=> $funcion,
						 	'combo_estados' => $combo_estados,
						 	'idestado_gen'  => $idestado_gen,
						 	'opcion'		=> $opcion,
						 ]);
	}


	public function actionAjaxListarTransferencia(Request $request)
	{
		$fechainicio	=  date_format(date_create($request['finicio']), 'd-m-Y');
		$fechafin		=  date_format(date_create($request['ffin']), 'd-m-Y');
		$estado_id 		=  $request['estado_id'];
		$centro_id 	 	= 	Session::get('centros')->COD_CENTRO;
		$idopcion		= 	$request['id_opcion'];
	
		$codopcion	 			= $this->funciones->desencriptar_id('1CIX-'.$idopcion,8);
		$opcion 			 = 	WEBOpcion::where('id','=',$codopcion)->first();

		$combo_estados  	=  $this->estadosTrasferencia();
		
		if($estado_id == 'TODOS'){

			$listapedidos	=  	WEBTransferencia::where('web.transferencia.activo','=',1)
		    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.transferencia.estado_id')
		    						->leftJoin('web.trans_destino', 'web.trans_destino.id', '=', 'web.transferencia.almacen_destino_id')
		    						->leftJoin('STD.EMPRESA', 'STD.EMPRESA.COD_EMPR', '=', 'web.transferencia.cliente_id')
			    					->where('fecha_pedido','>=', $fechainicio)
			    					->where('fecha_pedido','<=', $fechafin)
									->where('web.transferencia.empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('web.transferencia.centro_id','=',Session::get('centros')->COD_CENTRO)
									->select('web.transferencia.*','web.trans_destino.destino','CMP.CATEGORIA.NOM_CATEGORIA as estado_nom','STD.EMPRESA.NOM_EMPR as cliente_nom')
		    						->orderBy('fecha_pedido', 'desc')
		    						->get();
		}else{

						$listapedidos	=  	WEBTransferencia::where('web.transferencia.activo','=',1)
		    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.transferencia.estado_id')
		    						->leftJoin('web.trans_destino', 'web.trans_destino.id', '=', 'web.transferencia.almacen_destino_id')
			    					->where('fecha_pedido','>=', $fechainicio)
			    					->where('fecha_pedido','<=', $fechafin)
			    					->whereIn('estado_id', [$estado_id])
									->where('web.transferencia.empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('web.transferencia.centro_id','=',Session::get('centros')->COD_CENTRO)
									->select('web.transferencia.*','web.trans_destino.destino','CMP.CATEGORIA.NOM_CATEGORIA as estado_nom')
		    						->orderBy('fecha_pedido', 'desc')
		    						->get();
		}
	    			
		$funcion 		= 	$this;
		
		return View::make('transferencia/ajax/listatransferencia',
						 [	
						 	 'idopcion'		  => $idopcion,
							 'listapedidos'   => $listapedidos,
							 'ajax'   		  => true,
							 'funcion'   	  => $funcion,
							 'opcion'		  => $opcion
						 ]);
	}

	public function LlenarDatosAgregar(Request $request,$idtransferencia){	
		$codigo 					= $this->funciones->generar_codigo_BD('WEB.transferencia',8);
		$productos 					= $request['productos'];
		$movil 						= 1;
		$estado_id 					= 'EPP0000000000002';

		$cabecera            	 	=	new WEBTransferencia;
		$cabecera->id 	     	 	=  	$idtransferencia;
		$cabecera->codigo 	    	=  	$codigo;
		$cabecera->movil 	    	=  	$movil;
		$cabecera->fecha_pedido	    =  	$request['fecha_pedido'];
		$cabecera->fecha_entrega   	=  	$request['fecha_entrega'];
		$cabecera->hora_entrega    	=  	$request['hora_entrega'];
		$cabecera->peso_total    	=  	$request['peso_total'];
		$cabecera->estado_id 	    =  	$estado_id; 
		$cabecera->cliente_id 	    =  	$request['cliente_op']; 		
		$cabecera->centro_origen_id	    =  	$request['centro_origen']; 				
		$cabecera->almacen_destino_id 	=  	$request['almacen_destino'];
		$cabecera->centro_id 		=   Session::get('centros')->COD_CENTRO;
		$cabecera->empresa_id   	=   Session::get('empresas')->COD_EMPR;
		$cabecera->activo 	 		=   1;
		$cabecera->observacion		= 	$request['obs'];
		$cabecera->fecha_crea 	 	=   $this->fechaactual;
		$cabecera->usuario_crea 	=   Session::get('usuario')->id;
		$cabecera->fecha_mod 	 	=   $this->fechaactual;
		$cabecera->usuario_mod	 	=   Session::get('usuario')->id;
		$cabecera->save();
	}

	public function LlenarDatosModificar(Request $request,$idtrans){

		$cabecera    				=	WEBTransferencia::where('id','=',$idtrans)->first();			
		$cabecera->fecha_pedido	    =  	$request['fecha_pedido'];
		$cabecera->fecha_entrega   	=  	$request['fecha_entrega'];
		$cabecera->hora_entrega    	=  	$request['hora_entrega'];
		//$cabecera->estado_id 	    =  	$estado_id; 
		$cabecera->cliente_id 	    =  	$request['cliente_op']; 				
		$cabecera->centro_origen_id 	=  	$request['centro_origen'];
		$cabecera->almacen_destino_id 	=  	$request['almacen_destino'];
		$cabecera->observacion		= 	$request['obs'];
		$cabecera->fecha_mod 	 	=   $this->fechaactual;
		$cabecera->usuario_mod	 	=   Session::get('usuario')->id;
		$cabecera->save();

		//Eliminar Detalle				
		$td  =	WEBTransferenciaDetalle::where('transferencia_id','=',$idtrans)->get();		
		foreach($td as $obj){			
			$obj->activo 			=   0;
			$obj->fecha_mod 	 	=   $this->fechaactual;
			$obj->usuario_mod	 	=   Session::get('usuario')->id;			
			$obj->save();
		} 
	}

	public function actionAgregarTransferencia($idopcion, $idtransferencia, Request $request)
	{
		/******************* validar url **********************/
		 $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	     if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/	
		
	    $idtrans = $this->funciones->decodificarmaestraBD('WEB.transferencia', $idtransferencia);
		
		if($_POST)
		{			
			try{			

				DB::beginTransaction();
				
				if($idtransferencia=='x'){
					$idtrans = $this->funciones->getCreateIdMaestraBD('WEB.transferencia');
					$this->LlenarDatosAgregar($request,$idtrans);
				}else{
					$this->LlenarDatosModificar($request,$idtrans);
				}

				//DETALLE PEDIDO

				$productos 					= 	$request['productos'];
				$productos 					= 	json_decode($productos, true);
				
				foreach($productos as $obj){

					$idtransferenciadetalle		= 	$this->funciones->getCreateIdMaestraBD('WEB.transferenciadetalle');
					$paquete_producto 			=  	(float)$obj['paquetes_producto'];
					$cantidad_producto 			=  	(float)$obj['cantidad_producto'];
					$peso_producto 				=  	(float)$obj['peso_producto'];
										
					$total_peso_producto		= 	$peso_producto*$cantidad_producto;
										
					$producto_id 				= 	$this->funciones->desencriptar_id($obj['prefijo_producto'].'-'.$obj['id_producto'],13);

					$cabecera            	 	=	new WEBTransferenciaDetalle;
					$cabecera->id 	     	 	=  	$idtransferenciadetalle;
					$cabecera->transferencia_id	=  	$idtrans;
					$cabecera->producto_id 	    =  	$producto_id;
					$cabecera->producto_nombre  =  	$obj['nombre_producto'];
					$cabecera->producto_peso 	=  	$peso_producto;
					$cabecera->cantidad 	    =  	$cantidad_producto;
					$cabecera->cantidad_pendiente   =  	$cantidad_producto;
					$cabecera->paquete  	    =  	$paquete_producto;
					$cabecera->empresa_id  	    =  	Session::get('empresas')->COD_EMPR;
					$cabecera->centro_id    	=  	Session::get('centros')->COD_CENTRO;
					$cabecera->peso_total    	=  	$total_peso_producto;
					$cabecera->activo  	    	=  	1;
					$cabecera->fecha_crea 	 	=   $this->fechaactual;
					$cabecera->usuario_crea 	=   Session::get('usuario')->id;
					$cabecera->fecha_mod 	 	=   $this->fechaactual;
					$cabecera->usuario_mod	 	=   Session::get('usuario')->id;
					$cabecera->save();
				}		

				DB::commit();
				//

 				return Redirect::to('/gestion-transferencia/'.$idopcion)->with('bienhecho', 'Operación realizada con éxito.');

			}catch(Exception $ex){
				DB::rollback();
				return Redirect::to('/gestion-transferencia/'.$idopcion)->with('errorbd', 'Ocurrió un error inesperado. Porfavor contacte con el administrador del sistema.');	
			}

		}else{

			//adicionar clientes
			$transferencia          =	new WEBTransferencia();
			$transferenciadetalle   =	new WEBTransferenciaDetalle();

			if($idtransferencia<>'x'){
				$response			= 	$this->validar_transferencia_estado($idtrans,'GENERADO','EPP0000000000002');
				
				if($response[0]['error']){
					return Redirect::to('/gestion-transferencia/'.$idopcion)->with('errorbd', 'La transferencia no se encuentra en estado GENERADO');	
				}

				$transferencia      =	WEBTransferencia::
										where('web.transferencia.id','=',$idtrans)
										->leftJoin('web.trans_destino', 'web.trans_destino.id', '=', 'web.transferencia.almacen_destino_id')
										->leftJoin('STD.EMPRESA', 'STD.EMPRESA.COD_EMPR', '=', 'web.transferencia.cliente_id')
										->select('web.transferencia.*','web.trans_destino.destino','STD.EMPRESA.NOM_EMPR as cliente_nom')
										->first();	

				$transferenciadetalle   =	WEBTransferenciaDetalle::
										where('transferencia_id','=',$idtrans)
										->where('activo','=','1')
										->get();
			}else{
				$transferencia->fecha_pedido	= 	date_format(date_create(date('d-m-Y')), 'Y-m-d');
				$transferencia->fecha_entrega	= 	date_format(date_create(date('d-m-Y')), 'Y-m-d');
			}		

		    $empresa_reg 		= 	DB::table('STD.EMPRESA')
		    						->Where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
									->first();
	
			$listaproductos 	= 	DB::table('ALM.PRODUCTO AS P')
									->leftJoin('CMP.CATEGORIA AS U', 'P.COD_CATEGORIA_UNIDAD_MEDIDA', '=', 'U.COD_CATEGORIA')										
									->where('P.COD_CATEGORIA_TIPO_PRODUCTO','=','TPR0000000000002')	
									->where('P.COD_ESTADO','=',1)		
									->where('P.IND_DISPONIBLE','=',1)
									->select('P.*','U.NOM_CATEGORIA as NOM_UNIDAD_MEDIDA')//, 'U.NOM_CATEGORIA as NOM_UNIDAD_MEDIDA')						
		    					 	->orderBy('NOM_PRODUCTO', 'asc')->get();

		    $listaalmacen 		= 	DB::table('WEB.trans_destino')
		   							->Where('centro_id','=',Session::get('centros')->COD_CENTRO)
                                    ->where('activo','=','1')
                                    ->pluck('destino','id')
                                    ->toArray();

            $comboalmacen		=	array('' => "Seleccione Destino") + $listaalmacen; 
	
		    $listaclientes 		= 	DB::table('WEB.LISTACLIENTE')
		    						->Where('COD_EMPR','=', Session::get('empresas')->COD_EMPR)
									->where('COD_CENTRO','=', Session::get('centros')->COD_CENTRO)
									->select('id','NOM_EMPR AS nombres')
									->get();

			$cod_centro 		=   Session::get('centros')->COD_CENTRO;
			//@DPZ0002
			$combo_centros 		= 	$this->funciones->combo_lista_quitar_centro_array_filtro($cod_centro);

			return View::make('transferencia/registrotransferencia',
						[				
						  	'idopcion'  			=> $idopcion,
							'idtransferencia' 		=> $idtransferencia,
						  	'transferencia' 		=> $transferencia,
						  	'transferenciadetalle' 	=> $transferenciadetalle,
						  	'empresa_reg'  			=> $empresa_reg,
						  	'listaproductos'  		=> $listaproductos,
						  	'comboalmacen'			=> $comboalmacen,
						  	'listaclientes'			=> $listaclientes,
						  	'combo_centros'			=> $combo_centros,
						]);
		}
	}

	public function validar_transferencia_estado($idtrans,$nom_estado,$estado) {

		$mensaje					=   '';
		$error						=   false;

		$transferencia				=   WEBTransferencia::where('id','=',$idtrans)->where('estado_id','=',$estado)->first();
		
		if(count($transferencia) <= 0){
			$mensaje = 'La transferencia '.$idtrans.' no se encuentra en estado '. $nom_estado .' no se puede actualizar.';
			$error   = true;
		}								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		return $response;

	}

	public function actionModificarTransferencia($idopcion,$idtransferencia,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idtrans = $this->funciones->decodificarmaestra($idtransferencia);
	    //dd( $idtrans, $idtransferencia);

		if($_POST)
		{

			$cabecera            	 =	WEBTransferencia::
										where('idtransferencia','=',$idtrans)->first();

			$productos 					= 	$request['productos'];	

			DB::beginTransaction();
				

				$total 						=   $this->funciones->trs_calcular_cabecera_total($productos);				
				$estado_id 					= 	'EPP0000000000002';				

				$cabecera->fecha_pedido	    =  	$request['fecha_pedido'];
				$cabecera->fecha_entrega   	=  	$request['fecha_entrega'];
				$cabecera->hora_entrega    	=  	$request['hora_entrega'];
				$cabecera->estado_id 	    =  	$estado_id; 
				$cabecera->cliente_id 	    =  	$request['cliente_op']; 				
				$cabecera->centro_origen_id 	=  	$request['centro_origen'];
				$cabecera->almacen_destino_id 	=  	$request['almacen_destino'];
				$cabecera->observacion		= 	$request['obs'];
				$cabecera->fecha_mod 	 	=   $this->fechaactual;
				$cabecera->usuario_mod	 	=   Session::get('usuario')->id;
				$cabecera->save();


			DB::commit();
			return Redirect::to('/gestion-transferencia/'.$idopcion)->with('bienhecho', 'Operación realizada con éxito.');

		}else{

			$transferencia          =	WEBTransferencia::
										where('id','=',$idtrans)->first();

			$transferenciadetalle   =	WEBTransferenciaDetalle::
										where('transferencia_id','=',$idtrans)
									  	->where('activo','=',1)
									    ->first();

		    $empresa_reg 		= 	DB::table('STD.EMPRESA')
		    						->Where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
									->first();
	
			// $listaproductos 	= 	DB::table('WEB.LISTAPRODUCTOSAVENDER')
			// 						->where('IND_MOVIL','=',1)
		    // 					 	->orderBy('NOM_PRODUCTO', 'asc')->get();
			
			$listaproductos 	= 	DB::table('ALM.PRODUCTO AS P')
									->leftJoin('CMP.CATEGORIA AS U', 'P.COD_CATEGORIA_UNIDAD_MEDIDA', '=', 'U.COD_CATEGORIA')										
									->where('P.COD_CATEGORIA_TIPO_PRODUCTO','=','TPR0000000000002')	
									->where('P.COD_ESTADO','=',1)		
									->where('P.IND_DISPONIBLE','=',1)
									->select('P.*','U.NOM_CATEGORIA as NOM_UNIDAD_MEDIDA')//, 'U.NOM_CATEGORIA as NOM_UNIDAD_MEDIDA')						
		    					 	->orderBy('NOM_PRODUCTO', 'asc')->get();
		    					
		   	$listaalmacen 		= 	DB::table('WEB.trans_destino')
		   							->Where('centro_id','=',Session::get('centros')->COD_CENTRO)
                                    ->where('activo','=','1')
                                    ->pluck('id','destino')
                                    ->toArray();

            $comboalmacen		=	array('' => "Seleccione Destino") + $listaalmacen; 
	

		    $listaclientes 		= 	DB::table('WEB.LISTACLIENTE')
		    						->Where('COD_EMPR','=',
		    										Session::get('empresas')->COD_EMPR)
									->where('COD_CENTRO','=',
													Session::get('centros')->COD_CENTRO)
									->select('id','NOM_EMPR AS nombres')
									->get();

				$funcion 	= 	$this;	

		        return View::make('transferencia/registrotransferencia', 
		        				[
								  	'idopcion'  			=> $idopcion,
								  	'idtransferencia' 		=> $idtransferencia,
								  	'transferencia' 		=> $transferencia,
								  	'transferenciadetalle' 	=> $transferenciadetalle,
								  	'empresa_reg'  			=> $empresa_reg,
								  	'listaproductos'  		=> $listaproductos,
								  	'comboalmacen'			=> $comboalmacen,
								  	'listaclientes'			=> $listaclientes
		        				]);
		}
	}


	public function actionAjaxDetalleProducto(Request $request)
	{	

		$data_ipr 					=  	$request['data_ipr']; 
		$data_ppr 					=  	$request['data_ppr']; 
		$data_npr 					=  	$request['data_npr']; 
		$data_upr 					=  	$request['data_upr']; 
		$data_mpr 					=  	$request['data_mpr']; 
		$data_spr 					=  	$request['data_spr']; 
		$data_epr 					=  	$request['data_epr']; 
		
		$producto_id 				= 	$this->funciones->desencriptar_id($data_ppr.'-'.$data_ipr,13);
		
		return View::make('transferencia/ajax/detalleproducto',
						 [
							 'producto_id' 	=> $producto_id,
							 'data_ipr'     => $data_ipr,
							 'data_ppr'     => $data_ppr,
							 'data_npr'     => $data_npr,
							 'data_upr'     => $data_upr,
							 'data_mpr'     => $data_mpr,							 
							 'data_spr'     => $data_spr,	
							 'data_epr'     => $data_epr,								 
						 ]);
	}

	public function actionAjaxDetalleTransferencia(Request $request)
	{			
		$idtransferencia_encriptado = 	$request['pedido_id'];
		$idtransferencia	 		= 	$request['pedido_id'];
		$data_json_detalle 			= 	$request['data_json_detalle'];
		$array_detalle_transferencia=   json_decode($data_json_detalle);
		$idopcion			 		= 	$request['id_opcion'];
		$m_accion					= 	$request['m_accion'];
		
		$opcion	 					=  WebOpcion::Where('id','=',$this->funciones->desencriptar_id('1CIX-'.$idopcion,8))->first();
		
	    $idtransferencia			= $this->funciones->decodificarmaestraBD('WEB.transferencia', $idtransferencia);

		$transferencia				=   WEBTransferencia::
										where('WEB.transferencia.id','=',$idtransferencia)
										->leftJoin('WEB.trans_destino', 'WEB.transferencia.almacen_destino_id', '=', 'WEB.trans_destino.id')
										->leftJoin('STD.EMPRESA', 'WEB.transferencia.cliente_id', '=', 'STD.EMPRESA.COD_EMPR')
										->leftJoin('ALM.CENTRO', 'WEB.transferencia.centro_origen_id', '=', 'ALM.CENTRO.COD_CENTRO')
										->select('web.transferencia.*','web.trans_destino.destino','STD.EMPRESA.NOM_EMPR as cliente_nom','ALM.CENTRO.NOM_CENTRO as centro_origen')
										->first();
		
		$transferenciadetalle		=   WEBTransferenciaDetalle::where('transferencia_id','=',$idtransferencia)
										->where('activo','=',1)
										->get();

		$funcion 					= 	$this;			
		
		$titulo_boton 				= '';

		if ($m_accion == 'DELETE'){ $titulo_boton = 'Eliminar';}
		elseif ($m_accion == 'DECLINE'){ $titulo_boton = 'Rechazar';}
		else{$titulo_boton =  $opcion->parametros;}

		return View::make('transferencia/ajax/modaldetalletransferencia',
						 [
							 'idtransferencia_encriptado'=> $idtransferencia_encriptado,
							 'transferencia'			=> $transferencia,
							 'transferencia_detalle'	=> $transferenciadetalle,
							 'funcion'   				=> $funcion,
							 'opcion'					=> $opcion,
							 'idopcion'					=> $idopcion,
							 'm_accion'					=> $m_accion,
							 'titulo_boton'				=> $titulo_boton,
						 ]);
	}

	public function actionAjaxCambiarEstadoTransferencia(Request $request)
	{	
		$idtransferencia	 		= 	$request['pedido_id'];
		$estado_id	 				= 	$request['estado_id'];
		$accion 					= 	$request['accion'];
		$idopcion 					= 	$request['id_opcion'];
		$m_accion					= 	$request['m_accion'];

		$transferencia				=   WEBTransferencia::where('id','=',$idtransferencia)->first();
		$id_estadoActualizar		= 	"";

		$nom_estado 				=   "";
		$cod_estado 				=   "";

		if ($m_accion == "DELETE") {
			$nom_estado 			=   "GENERADO";
			$cod_estado 			=   "EPP0000000000002";
		}elseif ($m_accion == "DECLINE") {
			$nom_estado 			=   "CERRADO";
			$cod_estado 			=   "EPP0000000000007";
		} else {
			if ($accion == "Cerrar") {
				$nom_estado 			=   "GENERADO";
				$cod_estado 			=   "EPP0000000000002";
			}elseif ($accion == "Autorizar") {
				$nom_estado 			=   "CERRADO";
				$cod_estado 			=   "EPP0000000000007";
			}
		}
		//Validar Estado Transferencia
		$response						= 	$this->validar_transferencia_estado($idtransferencia,$nom_estado,$cod_estado);
		if($response[0]['error']){echo json_encode($response); exit();}

		if ($m_accion == "DELETE") {
			$transferencia->activo			= 0;
			$transferencia->usuario_mod		= Session::get('usuario')->id;
			$transferencia->fecha_mod 		= $this->fechaactual;
			$transferencia->save();		
		}else {
			if ($m_accion == "DECLINE") {
				$id_estadoActualizar		= "EPP0000000000005";			
			}elseif ($accion == "Cerrar") {
				$id_estadoActualizar		= "EPP0000000000007";
			}elseif ($accion == "Autorizar") {
				$id_estadoActualizar		= "EPP0000000000003";
			}elseif ($accion == "DECLINE") {
				$id_estadoActualizar		= "EPP0000000000005";
			}

			$transferencia->estado_id		= $id_estadoActualizar;
			$transferencia->usuario_mod		= Session::get('usuario')->id;
			$transferencia->fecha_mod 		= $this->fechaactual;
			$transferencia->save();		
		}		

		//return Redirect::to('/gestion-transferencia/'.$idopcion)->with('bienhecho', 'Operación realizada con éxito.');
		echo json_encode($response);
	}

	//////////////////////////////////// PICKING ///////////////////////////////////////////////

	public function actionListarPicking($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	
	    $fechainicio  			= 	$this->fecha_menos_quince;
	    $fechafin  				= 	$this->fin;	    

	    $centro_id 				= Session::get('centros')->COD_CENTRO;
	    $codopcion	 			= $this->funciones->desencriptar_id('1CIX-'.$idopcion,8);
		$idestado_gen 			= "";
		$combo_estados   		= "";

		$opcion 			 	= 	WEBOpcion::where('id','=',$codopcion)->first();
			
		$idestado_gen 			= "TODOS";
		$combo_estados  		=  $this->estadosPicking();
										 
	    $listapicking			= 	WEBPicking::where('web.picking.activo','=',1)
		    						->leftJoin('CMP.CATEGORIA as E', 'E.COD_CATEGORIA', '=', 'web.picking.estado_id')
		    						->leftJoin('ALM.CENTRO as C', 'C.COD_CENTRO', '=', 'web.picking.centro_origen_id')
									->leftJoin('dbo.Users as U', 'U.id', '=', 'web.picking.usuario_crea')		    						
			    					->where('fecha_picking','>=', $fechainicio)
			    					->where('fecha_picking','<=', $fechafin)
									->where('web.picking.empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('web.picking.centro_id','=',Session::get('centros')->COD_CENTRO)
									->select('web.picking.*','E.NOM_CATEGORIA','E.COD_CATEGORIA', 'C.NOM_CENTRO'
											,'U.name as Usuario')
									->orderBy('fecha_picking', 'desc')
		    						->get();
				
		$funcion 				= 	$this;
		
		return View::make('transferencia/listapicking',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'listapicking' 	=> $listapicking,
						 	'fechainicio' 	=> $fechainicio,
						 	'fechafin' 		=> $fechafin,
						 	'funcion' 		=> $funcion,
						 	'combo_estados' => $combo_estados,
						 	'idestado_gen'  => $idestado_gen,
						 	'opcion'		=> $opcion,
						 ]);
	}
		
	public function actionAjaxListarPicking(Request $request)
	{
		$fechainicio	=  date_format(date_create($request['finicio']), 'd-m-Y');
		$fechafin		=  date_format(date_create($request['ffin']), 'd-m-Y');
		$estado_id 		=  $request['estado_id'];
		$idopcion		= 	$request['id_opcion'];
		
		$centro_id 	 	= 	Session::get('centros')->COD_CENTRO;
		$empresa_id     =	Session::get('empresas')->COD_EMPR;
	
		$codopcion	 			=  $this->funciones->desencriptar_id('1CIX-'.$idopcion,8);
		$opcion 			    =  WEBOpcion::where('id','=',$codopcion)->first();

		$idestado_gen 			= "EPP0000000000002";
		$combo_estados  		=  $this->estadosPicking();

		if($estado_id == 'TODOS'){
	
		   	$listapicking		= 	WEBPicking::where('web.picking.activo','=',1)
		    						->leftJoin('CMP.CATEGORIA as E', 'E.COD_CATEGORIA', '=', 'web.picking.estado_id')
		    						->leftJoin('ALM.CENTRO as C', 'C.COD_CENTRO', '=', 'web.picking.centro_origen_id')
									->leftJoin('dbo.Users as U', 'U.id', '=', 'web.picking.usuario_crea')
		    						->where('fecha_picking','>=', $fechainicio)
			    					->where('fecha_picking','<=', $fechafin)
									->where('web.picking.empresa_id','=',$empresa_id)
									->where('web.picking.centro_id','=',$centro_id)
									->select('web.picking.*','E.NOM_CATEGORIA','E.COD_CATEGORIA', 'C.NOM_CENTRO'
											,'U.name as Usuario')
									->orderBy('fecha_picking', 'desc')
		    						->get();
		}else{

		    $listapicking		= 	WEBPicking::where('web.picking.activo','=',1)
		    						->leftJoin('CMP.CATEGORIA as E', 'E.COD_CATEGORIA', '=', 'web.picking.estado_id')
		    						->leftJoin('ALM.CENTRO as C', 'C.COD_CENTRO', '=', 'web.picking.centro_origen_id')
									->leftJoin('dbo.Users as U', 'U.id', '=', 'web.picking.usuario_crea')
		    						->whereIn('web.picking.estado_id', [$estado_id])
			    					->where('fecha_picking','>=', $fechainicio)
			    					->where('fecha_picking','<=', $fechafin)
									->where('web.picking.empresa_id','=',$empresa_id)
									->where('web.picking.centro_id','=',$centro_id)
									->select('web.picking.*','E.NOM_CATEGORIA','E.COD_CATEGORIA', 'C.NOM_CENTRO'
											,'U.name as Usuario')
									->orderBy('fecha_picking', 'desc')
		    						->get();

		}
	    			
		$funcion 		= 	$this;
		

		return View::make('transferencia/ajax/alistapicking',
						 [	
						 	'idopcion' 		=> $idopcion,
						 	'listapicking' 	=> $listapicking,
						 	'fechainicio' 	=> $fechainicio,
						 	'fechafin' 		=> $fechafin,
						 	'funcion' 		=> $funcion,
						 	'combo_estados' => $combo_estados,
						 	'idestado_gen'  => $idestado_gen,
						 	'opcion'		=> $opcion,
						 	'ajax'   		  => true,						 	 
						 ]);
	}

	public function actionAgregarPicking($idopcion,$idpicking,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $codcentro 						= Session::get('centros')->COD_CENTRO;
		$nomcentro 						= Session::get('centros')->NOM_CENTRO;
		$palets							= 0;
		$palets_peso					= 0;

		if($_POST)
		{
			try{
				
				$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);				
				$palets 							=   $request['cantidad_palets'];
				//$response = $this->ValidarCantidadesAtenderPicking($array_detalle_producto_request);
								
				/*if($response[0]['error']){
					 return Redirect::back()->withInput()->with('errorbd', $response[0]['mensaje']);
				}*/
				DB::beginTransaction();

				$idpicking					= 	$this->funciones->getCreateIdMaestraBD('WEB.picking');
				
				$codigo 					= 	$this->funciones->generar_codigo_BD('WEB.picking',8);				
				//$ind_plantilla 	= 	$request['ind_plantilla'];

				//PICKING
				$cabecera            	 	=	new WEBPicking;
				$cabecera->id 	     	 	=  	$idpicking;
				$cabecera->codigo 	    	=  	$codigo;
				$cabecera->fecha_picking 	=   $this->fechaactual;
				$cabecera->estado_id 	    =  	'EPP0000000000002';
				$cabecera->centro_origen_id	=   $request['centro_origen_id'];
				$cabecera->empresa_id 		=   Session::get('empresas')->COD_EMPR;
				$cabecera->centro_id 		=   Session::get('centros')->COD_CENTRO;
				$cabecera->palets			=	$palets;
				$cabecera->activo 	 	 	=   1;
				$cabecera->fecha_crea 	 	=   $this->fechaactual;
				$cabecera->fecha_mod 	 	=   $this->fechaactual;
				$cabecera->usuario_crea 	=   Session::get('usuario')->id;
				$cabecera->usuario_mod	 	=   Session::get('usuario')->id;
				$cabecera->save();

				foreach($array_detalle_producto_request as $key => $row) {
					//$cantidad_atender 					= 	(float)$row['cantidad'] + (float)$row['muestra'];
					$detalle            	 			=	new WEBPickingDetalle;
					//$detalle->id 	     	 			=  	$iddetalleordendespacho;
					$detalle->picking_id 				=  	$idpicking;
					$detalle->transferencia_id 			=  	$row['transferencia_id'];
					$detalle->producto_id 				=  	$row['producto_id'];
					$detalle->tipo_operacion 			=  	$row['tipo_operacion'];
					$detalle->fecha_entrega 			=  	$row['fecha_entrega'];
					$detalle->cliente_id				=  	$row['cliente_id'];
					$detalle->producto_nombre 			=  	$row['producto_nombre'];
					$detalle->producto_peso 			=  	$row['producto_peso'];
					$detalle->cantidad 					=  	$row['cantidad_atender'];
					$detalle->cantidad_excedente		=  	$row['cantidad_excedente'];
					$detalle->paquete 					=  	$row['paquete'];
					$detalle->peso_total	 			=  	$row['peso_total'];					
					$detalle->departamento_id 			=  	$row['departamento_id'];
					$detalle->departamento_nom 			=  	$row['departamento_nom'];
					$detalle->provincia_id 				=  	$row['provincia_id'];
					$detalle->provincia_nom 			=  	$row['provincia_nom'];
					$detalle->distrito_id 				=  	$row['distrito_id'];
					$detalle->distrito_nom 				=  	$row['distrito_nom'];
					$detalle->direccion_cli_id 			=  	$row['direccion_cli_id'];
					$detalle->direccion_cli_nom 		=  	$row['direccion_cli_nom'];
					$detalle->activo 					=  	1;
					$detalle->fecha_crea 	 			=   $this->fechaactual;
					$detalle->usuario_crea 				=   Session::get('usuario')->id;
					$detalle->fecha_mod 	 			=   $this->fechaactual;
					$detalle->usuario_mod 				=   Session::get('usuario')->id;					
					$detalle->save();
					
					// actualizamos cantidades del detalle de la transferencia
					if ($row['tipo_operacion'] == 'TRANSFERENCIA' && $row['transferencia_id'] <> '00000000000000'){
						$det_id 							=  	$row['transferenciadetalle_id'];	
						$ObjDet 							=	WEBTransferenciaDetalle::where('id','=',$det_id)->first();
						$ObjDet->cantidad_pendiente        -=	$row['cantidad_atender'];
						$ObjDet->fecha_mod 	 				=   $this->fechaactual;
						$ObjDet->usuario_mod 				=   Session::get('usuario')->id;									
						$ObjDet->save();
					}

			    }

			    // actualizamos el estado de las transferencias
				$group_detalle_pedido 	=  $this->funciones->grouparray($array_detalle_producto_request,'transferencia_id');

				foreach($group_detalle_pedido as $key => $obj_trans){		
					if($obj_trans['groupeddata'][0]["tipo_operacion"] == "TRANSFERENCIA" && $obj_trans['transferencia_id'] <> '00000000000000'){

						$transferencia_id 	= $obj_trans['transferencia_id'];
						$trans				= WEBTransferencia::where('id','=',$transferencia_id)->first();
						$codestado			= 'EPP0000000000004';

						foreach($trans->transferenciadetalle as $obj){			
							if($obj->cantidad_pendiente>0){
								$codestado  = 'EPP0000000000006';
							}
						} 	
						$trans->estado_id 					=   $codestado;
						$trans->fecha_mod 	 				=   $this->fechaactual;
						$trans->usuario_mod 				=   Session::get('usuario')->id;									
						$trans->save();	
					}								
				}

				DB::commit();
	 			return Redirect::to('/gestion-picking/'.$idopcion)->with('bienhecho', 'Picking '.$codigo.' registrado con éxito');


			}catch(Exception $ex){
				DB::rollback();
				return Redirect::to('/gestion-picking/'.$idopcion)->with('errorbd', 'Ocurrió un error inesperado. Porfavor contacte con el administrador del sistema : '.$ex);	
			}

		}else{
             
             //adicionar clientes
			$picking          		=	new WEBPicking();
			$palets_peso 		  	=  $this->ObtenerPesoPalets();

			if($idpicking<>'x'){
				/*$response			= 	$this->validar_transferencia_estado($idtrans,'GENERADO','EPP0000000000002');
				
				if($response[0]['error']){
					return Redirect::to('/gestion-transferencia/'.$idopcion)->with('errorbd', 'La transferencia no se encuentra en estado GENERADO');	
				}*/
				$picking      			=	WEBPicking::where('web.picking.id','=',$idpicking)
										->leftJoin('ALM.ALMACEN', 'web.picking.centro_origen_id', '=', 'ALM.ALMACEN.COD_ALMACEN')
										->first();							
			}	

			$combo_lista_centros  	=  array($codcentro => $nomcentro);
			$correlativo 				=   0;

			return View::make('transferencia/registropicking',
							 [
							 	'idopcion' 				=> $idopcion,					
							 	'idpicking' 			=> $idpicking,	
							 	'picking'				=> $picking,
								'palets' 				=> $palets,
								'inicio'				=> $this->inicio,
								'hoy'					=> $this->fin,
								'correlativo'			=> $correlativo,
							 	'combo_lista_centros' 	=> $combo_lista_centros,
								'palets_peso'			=> $palets_peso,
							 ]);
		}
	}

	
	public function actionAjaxValidarCantidadesAtenderPicking(Request $request) {		  

		$mensaje				=   '';
		$error					=   false;
		
		$array_detalle_producto	= 	json_decode($request['array_detalle_producto'],true);	

		$group_detalle_pedido 	=  $this->funciones->grouparray($array_detalle_producto,'transferencia_id');

		foreach($group_detalle_pedido as $key => $obj_trans){
			
			if($obj_trans['groupeddata'][0]["tipo_operacion"] == "TRANSFERENCIA" && $obj_trans['transferencia_id'] <> '00000000000000'){

				$transferencia_id 	= $obj_trans['transferencia_id'];
				$transferencia		= WEBTransferencia::where('id','=',$transferencia_id)->first();
				
				if($transferencia->estado_id =='EPP0000000000003' || $transferencia->estado_id =='EPP0000000000006'){

					$trans_filter 			= 	array("transferencia_id" => $obj_trans['transferencia_id']);

					$lista_array_trans 		= 	array_filter($array_detalle_producto, function($array_detalle_producto) use ($trans_filter){
					    return in_array($array_detalle_producto['transferencia_id'], $trans_filter);
					});			

					foreach($lista_array_trans as $key => $obj_det){
						
						foreach($transferencia->transferenciadetalle as $obj){	

							if($obj_det['producto_id'] == $obj->producto_id && $obj->cantidad_pendiente < ($obj_det['cantidad_atender'])) {
								$mensaje	=  'Error en la cantidad de la Transferencia '.$transferencia->codigo.'. Cantidad Pendiente: '.$obj->cantidad_pendiente.'. Cantidad Atender: '.$obj_det['cantidad_atender'];	
									break;
							}
						}
					}
				}else{
					$mensaje	= 'La transferencia '.$transferencia->codigo.' debe encontrarse en estado APROBADO o ATENDIDO PARCIALMENTE';
				}

			}elseif($obj_trans['groupeddata'][0]["tipo_operacion"] == "ORDEN"){

				$cod_orden 			= $obj_trans['transferencia_id'];
				$orden 				= CMPOrden::where('COD_ORDEN','=',$cod_orden)->first();
				
				if($orden->COD_CATEGORIA_ESTADO_ORDEN =='EOR0000000000018' || $orden->COD_CATEGORIA_ESTADO_ORDEN =='EOR0000000000012'){

					$trans_filter 			= 	array("transferencia_id" => $obj_trans['transferencia_id']);

					$lista_array_trans 		= 	array_filter($array_detalle_producto, function($array_detalle_producto) use ($trans_filter){
					    return in_array($array_detalle_producto['transferencia_id'], $trans_filter);
					});			
					
					$lstProdPendiete		= 	$this->ObtenerProductosPendientesPicking($cod_orden);

					foreach($lista_array_trans as $key => $obj_det){						
						
						$canPendiente 			=   0;
						
						//foreach($lstProdPendiete as $objProd){

						foreach($lstProdPendiete as $codProd => $canProd){							
							if($obj_det['producto_id'] == $codProd){
								$canPendiente   +=  $canProd;
							}
						}

						foreach($orden->detalleproducto as $obj){	

							$cantProductoTotal   = $obj_det['cantidad_atender'] + $canPendiente;

							if($obj_det['producto_id'] == $obj->COD_PRODUCTO && $obj->CAN_PRODUCTO < $cantProductoTotal) {
								$mensaje	=  'Error en la cantidad de la Orden '.$orden->COD_ORDEN.'. Cantidad Total: '.$obj->CAN_PRODUCTO.'. Cantidad Atender: '.$obj_det['cantidad_atender'] . ' Cantidad Atendida: '. $canPendiente;	
									break;
							}
						}
					}
				}else{
					$mensaje	= 'La orden '.$cod_orden.' debe encontrarse en estado PROVISIONADO o ATENDIDO PARCIALMENTE';	
					break;
				}				
			}			
		}

		if($mensaje <> ''){ $error   = true; }								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		echo json_encode($response);
	}

	public function ObtenerProductosPendientesPicking($codOrden){
		$tipo  			= 1;
		$codpro			= '';
	 	$stmt			= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.PRODUCTOS_PENDIENTE_OV_PICKING ?,?');

        $stmt->bindParam(1, $tipo,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $codOrden ,PDO::PARAM_STR);                 
        $stmt->execute();       

		$array_ 						= array();
				
		while($row = $stmt->fetch()){
			$array_ 			  		=   $array_ + [$row['COD_PRODUCTO']=>$row['CAN_PRODUCTO']];
		}
		
		return $array_;
	}


	public function actionAjaxModalListaTransferenciaAutorizada(Request $request)
	{
		$centroorigen_id 				= 	$request['centroorigen_id'];
		$idpicking 						= 	$request['idpicking'];
		$funcion 						= 	$this;
	    		
		$listaproductos 				= 	DB::table('ALM.PRODUCTO')
											->where('COD_CATEGORIA_TIPO_PRODUCTO','=','TPR0000000000002')	
											->where('COD_ESTADO','=',1)								
											->orderBy('NOM_PRODUCTO', 'asc')->get();

		$empresa_id 					= 	Session::get('empresas')->COD_EMPR;
		$centro_id 						= 	Session::get('centros')->COD_CENTRO;

		$fecha_inicio 					= 	$this->fecha_menos_treinta_dias;
		$fecha_fin 						= 	$this->fin;
		/*$listaordencen					= 	
		$this->funciones->lista_orden_cen($empresa_id,$cliente_id,$centro_id,$fecha_inicio,$fecha_fin);*/

		$listapedidos					=  	WEBTransferencia::where('web.transferencia.activo','=',1)
				    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.transferencia.estado_id')
				    						->leftJoin('web.trans_destino', 'web.trans_destino.id', '=', 'web.transferencia.almacen_destino_id')
					    					->where('fecha_pedido','>=', $fecha_inicio)
					    					->where('fecha_pedido','<=', $fecha_fin)
					    					->whereIn('estado_id', ['EPP0000000000003','EPP0000000000006'])
											->where('web.transferencia.empresa_id','=',$empresa_id)
											//->where('web.transferencia.centro_id','=',$centro_id)
											//@DPZ0002
											->where('web.transferencia.centro_origen_id','=',$centroorigen_id)
											->select('web.transferencia.*','web.trans_destino.destino','CMP.CATEGORIA.NOM_CATEGORIA as estado_nom')
				    						->orderBy('fecha_pedido', 'desc')
				    						->get();

		$listaventas					= 	CMPOrden::where('cod_estado','=',1)			
											->where('FEC_ENTREGA','>=', $fecha_inicio)
					    					->where('FEC_ENTREGA','<=', $fecha_fin)
					    					->where('COD_CATEGORIA_TIPO_ORDEN','=', 'TOR0000000000006')					    
					    					->whereIn('COD_CATEGORIA_ESTADO_ORDEN',['EOR0000000000018','EOR0000000000012'])
											->where('COD_EMPR','=',$empresa_id)
											->where('COD_CENTRO','=',$centro_id)
											->get();

		//$listaventas 					= $this->OrdenesVentaPendienteAtenderPicking($fecha_inicio,$fecha_fin,$empresa_id,$centro_id);
		//dd($listaventas);

		$combotipogrupo					= 	array('oc_grupo' => "Grupo",'oc_individual' => "Individual"); 	

		return View::make('transferencia/modal/ajax/amtransferencia',
						 [
						 	'idpicking'					=> $idpicking,
						 	'centroorigen_id' 			=> $centroorigen_id,						 	
						 	'listaproductos' 			=> $listaproductos,
						 	'listapedidos' 				=> $listapedidos,
						 	'listaventas'				=> $listaventas,
						 	'funcion' 					=> $funcion,
						 	'combotipogrupo' 			=> $combotipogrupo,
						 	'ajax' 						=> true,
						 ]);
	}

	public function actionAjaxModalProductoIndividual(Request $request)
	{
		$listaproductos			 	= 	DB::table('ALM.PRODUCTO AS P')
										->leftJoin('CMP.CATEGORIA AS U', 'P.COD_CATEGORIA_UNIDAD_MEDIDA', '=', 'U.COD_CATEGORIA')										
										->where('P.COD_CATEGORIA_TIPO_PRODUCTO','=','TPR0000000000002')	
										->where('P.COD_ESTADO','=',1)		
										->where('P.IND_DISPONIBLE','=',1)
										->pluck('NOM_PRODUCTO','P.COD_PRODUCTO as producto_id')
										->toArray();		

		$combolistaproductos  		= 	array('' => "Seleccione producto") + $listaproductos;

		$funcion 					= 	$this;

		return View::make('transferencia/modal/ajax/productoindividual',
						 [
						 	'funcion' 				 		=> $funcion,			
						 	'combolistaproductos' 			=> $combolistaproductos,
						 ]);
	}

	public function actionAjaxAgregarProductoIndividual(Request $request)
	{
		$producto_id 					= 	$request['producto_id'];
		$cantidad_pr 					= 	$request['cantidad_pr'];	
		$array_detalle_producto 		= 	json_decode($request['array_detalle_producto'],true);	
		$idpicking 						= 	$request['idpicking'];	
		$palets 						= 	$request['palets'];	
		$correlativo 					= 	$request['correlativo'];	
		$opcion_id 						= 	$request['opcion_id'];	
		$palets 						= 	$request['palets'];	
		$palets_peso 					= 	$request['palets_peso'];	

		$transferencia_id 				= 	"00000000000000";
		$transferenciadetalle_id 		= 	"00000000000000";
		$tipo_operacion					= 	"TRANSFERENCIA";
		$fecha_entrega					= 	$this->fechaactual;
		$hora_entrega					= 	"";
		$cantidad_pendiente				= 	0;
		$cantidad_atender				= 	$cantidad_pr;
		$cantidad_excedente				= 	0;
		$vacio							= 	"";
		
		$producto 						= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
		$unidad_medida 					= 	CMPCategoria::where('COD_CATEGORIA','=',$producto->COD_CATEGORIA_UNIDAD_MEDIDA)->first();

		$paquete  						= 	0;
		if ($producto->COD_CATEGORIA_SUB_FAMILIA == 'SFM0000000000104') {
			$paquete 					=   $cantidad_atender/ $producto->CAN_PESO_MATERIAL;
		}else{
			$paquete 					= 	$cantidad_atender;
		} 
		$peso_total						=   $cantidad_atender*$producto->CAN_PESO_MATERIAL; 
		$correlativo					=   $correlativo+1;

		$array_nuevo_producto			= 	$this->funciones->llenar_array_productos_temp($correlativo,$transferencia_id,
							$transferenciadetalle_id,$tipo_operacion,$fecha_entrega,$hora_entrega,$vacio,$vacio,
							$producto->COD_PRODUCTO,$producto->NOM_PRODUCTO,$producto->CAN_PESO_MATERIAL,$producto->COD_CATEGORIA_UNIDAD_MEDIDA,
							$unidad_medida->NOM_CATEGORIA,$cantidad_pendiente,$cantidad_atender,$cantidad_excedente,$paquete,$peso_total,$vacio,
							$vacio,$vacio,$vacio,$vacio,$vacio, $vacio, $vacio);
				
		
		array_push($array_detalle_producto,$array_nuevo_producto);		
		
		$funcion 	= 	$this;

		return View::make('transferencia/ajax/alistapickingpedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'idpicking'						 		=> $idpicking,
							'palets'						 		=> $palets,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'ajax'   		  						=> true,
							'palets_peso' 							=> $palets_peso,
							'palets' 								=> $palets
						 ]);
	}

	public function ObtenerMensaje($msj, $data) {
		$mensaje					=  $msj;
		$error						=   false;		
		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje,
			'datos'      			=> $data
		);
		return $response;
	}

	public function actionAjaxModalAgregarProductosPicking(Request $request)
	{

		$data_producto 						= 	$request['data_producto'];
		$opcion_id 							= 	$request['opcion_id'];
		$idpicking							= 	$request['idpicking'];
		$correlativo 						= 	(int)$request['correlativo'];
		$palets 							=	(int)$request['palets'];

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();
		$rowspan 							= 	0;
		
		foreach($data_producto as $obj){

		    $producto_id 					= 	$obj['producto_id'];
		    $transferencia_id 				=	$obj['trans_id'];
		    $tipo_operacion 				=	$obj['tipo_operacion'];
		    $transferenciadetalle_id		=	$obj['transdet_id'];
		    $cantidad_atender 				= 	$obj['cantidad_atender'];
		    $cantidad_pendiente	 			= 	$obj['cantidad_pendiente'];
		    $cantidad_excedente	 			= 	$obj['cantidad_excedente'];

		    $nombre_cliente 				= 	"";
		    $cliente_id 	 				= 	"";
		    $cliente_dir_id 				= 	"";
		    $cliente_dir_nom 				= 	"";
		    $fecha_entrega 					= 	"";
		    $hora_entrega 					= 	"";
			$trans 							= 	"";
			$departamento_id				=  	""; 
			$departamento_nom 				= 	"";
			$provincia_id					= 	"";
			$provincia_nom 					= 	"";
			$distrito_id 					= 	"";
			$distrito 						= 	"";


		    if($tipo_operacion == 'TRANSFERENCIA'){
		    	
		    	$trans 		    				=	WEBTransferencia::where('id','=',$transferencia_id)->first();
		    	$fecha_entrega					=   $trans->fecha_entrega;
		    	$hora_entrega					=   $trans->hora_entrega;
				$cliente_id	 					=   $trans->cliente_id;

			   
			    $trans_destino					= 	DB::table('web.trans_destino as T')
			    									->leftJoin('CMP.CATEGORIA as DE', 'DE.COD_CATEGORIA', '=', 'T.departamento_id')
			    									->leftJoin('CMP.CATEGORIA as PR', 'PR.COD_CATEGORIA', '=', 'T.provincia_id')
			    									->leftJoin('CMP.CATEGORIA as DI', 'DI.COD_CATEGORIA', '=', 'T.distrito_id')
			    									->where('T.id','=',$trans->almacen_destino_id)	 
			    									->where('T.activo','=',1)
			    									->select('T.*','DE.NOM_CATEGORIA as departamento','PR.NOM_CATEGORIA as provincia','DI.NOM_CATEGORIA as distrito')			    	
			    									->first();

			    $departamento_id				=  	$trans_destino->departamento_id; 
				$departamento_nom 				= 	$trans_destino->departamento;
				$provincia_id					= 	$trans_destino->provincia_id;
				$provincia_nom 					= 	$trans_destino->provincia;
				$distrito_id 					= 	$trans_destino->distrito_id;
				$distrito_nom 					= 	$trans_destino->distrito;

		    }elseif($tipo_operacion == 'ORDEN'){

		    	$trans 		    				=	CMPOrden::where('COD_ORDEN','=',$transferencia_id)->first();
		    	$fecha_entrega					=   $trans->FEC_ENTREGA;
		    	$cliente_id	 					=   $trans->COD_EMPR_CLIENTE;

			    $trans_destino					= 	DB::table('web.trans_destino as T')
			    									->leftJoin('CMP.CATEGORIA as DE', 'DE.COD_CATEGORIA', '=', 'T.departamento_id')
			    									->leftJoin('CMP.CATEGORIA as PR', 'PR.COD_CATEGORIA', '=', 'T.provincia_id')
			    									->leftJoin('CMP.CATEGORIA as DI', 'DI.COD_CATEGORIA', '=', 'T.distrito_id')
			    									->where('T.id','=','1CIX00000001    ') //$trans->COD_CENTRO)	 
			    									->where('T.activo','=',1)
			    									->select('T.*','DE.NOM_CATEGORIA as departamento','PR.NOM_CATEGORIA as provincia','DI.NOM_CATEGORIA as distrito')
			    									->first();
		    }
	    	
	    	if ($cliente_id <> '') {			
		    	$cliente 					= 	DB::table('STD.EMPRESA')->where('COD_EMPR','=',$cliente_id)->first();	
				$nombre_cliente				= 	$cliente->NOM_EMPR;
				
				$direccion					=   DB::table('STD.EMPRESA_DIRECCION as ED')
												->leftJoin('CMP.CATEGORIA as DE', 'DE.COD_CATEGORIA', '=', 'ED.COD_DEPARTAMENTO')
		    									->leftJoin('CMP.CATEGORIA as PR', 'PR.COD_CATEGORIA', '=', 'ED.COD_PROVINCIA')
		    									->leftJoin('CMP.CATEGORIA as DI', 'DI.COD_CATEGORIA', '=', 'ED.COD_DISTRITO')
		    									->select('ED.*','DE.NOM_CATEGORIA as departamento','PR.NOM_CATEGORIA as provincia','DI.NOM_CATEGORIA as distrito')
												->where('ED.COD_EMPR','=',$cliente_id)
												->where('ED.IND_DIRECCION_FISCAL','=',1) 
												->where('ED.COD_ESTADO','=',1) 
												->first();

 				$cliente_dir_id 			= 	$direccion->COD_DIRECCION;
			    $cliente_dir_nom 			= 	$direccion->NOM_DIRECCION;
			    
			    if($tipo_operacion == 'ORDEN'){
			    	$departamento_id				=  	$direccion->COD_DEPARTAMENTO; 
					$departamento_nom 				= 	$direccion->departamento;
					$provincia_id					= 	$direccion->COD_PROVINCIA;
					$provincia_nom 					= 	$direccion->provincia;
					$distrito_id 					= 	$direccion->COD_DISTRITO;
					$distrito_nom 					= 	$direccion->distrito;
			    }			    
			 }	


	    	$producto 						= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
			$unidad_medida 					= 	CMPCategoria::where('COD_CATEGORIA','=',$producto->COD_CATEGORIA_UNIDAD_MEDIDA)
			    												->first();
			
			$paquete  						= 	0;
			if ($producto->COD_CATEGORIA_SUB_FAMILIA == 'SFM0000000000104') {
				$paquete 					=   $cantidad_atender/ $producto->CAN_PESO_MATERIAL;
			}else{
				$paquete 					= 	$cantidad_atender;
			} 
		    
			$array_nuevo_producto 			=	array();
			//$grupo 							= 	$grupo + 1;
			$correlativo 					= 	$correlativo + 1;
			//calculo de kilos,cantidad_sacos,palets
			$peso_total						=   ($cantidad_atender*$producto->CAN_PESO_MATERIAL) + ($cantidad_excedente*$producto->CAN_PESO_MATERIAL);

			$array_nuevo_producto		= 	$this->funciones->llenar_array_productos_temp($correlativo,$transferencia_id,
								$transferenciadetalle_id,$tipo_operacion,$fecha_entrega,$hora_entrega,$cliente_id,$nombre_cliente,
								$producto->COD_PRODUCTO,$producto->NOM_PRODUCTO,$producto->CAN_PESO_MATERIAL,$producto->COD_CATEGORIA_UNIDAD_MEDIDA,
								$unidad_medida->NOM_CATEGORIA,$cantidad_pendiente,$cantidad_atender,$cantidad_excedente,$paquete,$peso_total,$departamento_id,
								$departamento_nom,$provincia_id,$provincia_nom,$distrito_id,$distrito_nom, $cliente_dir_id, $cliente_dir_nom);

			$rowspan 						= 	$rowspan + 1;
			
			array_push($array_detalle_producto,$array_nuevo_producto);
		}

		if(count($array_detalle_producto_request)>0){
			foreach ($array_detalle_producto_request as $key => $item) {
				array_push($array_detalle_producto,$item);
			}
		}

		$funcion 	= 	$this;
		$palets_peso 		  =  $this->ObtenerPesoPalets();
		
		return View::make('transferencia/ajax/alistapickingpedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'idpicking'						 		=> $idpicking,
							'palets'						 		=> $palets,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'ajax'   		  						=> true,
							 'palets_peso' 							=> $palets_peso,
						 ]);

	}

	public function ObtenerPesoPalets() {
		return  ALMProducto::where('COD_PRODUCTO','=',"PRD0000000007926")->first()->CAN_PESO_MATERIAL;
	}
		

	public function actionAjaxPickingEliminarFila(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto 			=	array();
	    $codcentro 							= 	Session::get('centros')->COD_CENTRO;
		$correlativo 						= 	(int)$request['correlativo'];
		$fila 								= 	$request['fila'];
		$idpicking							= 	$request['idpicking'];
		$opcion_id 							= 	$request['opcion_id'];
		$palets 							= 	$request['palets'];	
		$palets_peso 						= 	$request['palets_peso'];	

		//eliminar la fila del array
		foreach ($array_detalle_producto_request as $key => $item) {
            if((int)$item['correlativo'] == $fila) {
                unset($array_detalle_producto_request[$key]);
            }
		}

	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_producto_request as $key => $item) {
			array_push($array_detalle_producto,$item);
		}

		$funcion 					= 	$this;
		
		$combo_lista_centros 		= 	$this->funciones->combo_lista_quitar_centro_array_filtro($codcentro);

		return View::make('transferencia/ajax/alistapickingpedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'idpicking'						 		=> $idpicking,
						 	'funcion' 								=> $funcion,
						 	'correlativo'							=> $correlativo,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
							'palets'   		  						=> $palets,
							'palets_peso'							=> $palets_peso,
						 	'ajax'   		  						=> true,
						 ]);
	}

	public function actionAjaxDetallePicking(Request $request)
	{			
		$idpickingencriptado 		= 	$request['picking_id'];
		$idpicking 			 		= 	$request['picking_id'];
		$idopcion			 		= 	$request['id_opcion'];
		$m_accion					= 	$request['m_accion'];
		$array_detalle_producto 	=	array();

		$idpicking 					= 	$this->funciones->decodificarmaestraBD('WEB.picking',$idpicking);
		
		$picking	 				=   WEBPicking::where('WEB.picking.id','=',$idpicking)
										->leftJoin('ALM.CENTRO as A', 'WEB.picking.centro_origen_id', '=', 'A.COD_CENTRO')
										->leftJoin('CMP.CATEGORIA as C', 'WEB.picking.estado_id', '=', 'C.COD_CATEGORIA')
										->select('web.picking.*', 'A.NOM_CENTRO as centro_origen', 'C.NOM_CATEGORIA as estado_nom')
										->first();
		
		$pickingdetalle				=   WEBPickingDetalle::where('picking_id','=',$idpicking)
										->leftJoin('STD.EMPRESA as E', 'WEB.pickingdetalle.cliente_id', '=', 'E.COD_EMPR')
										//->leftJoin('STD.EMPRESA_DIRECCION as D', 'E.COD_EMPR', '=', 'D.COD_EMPR')
										//->where('D.IND_DIRECCION_FISCAL','=',1) 	
										->leftJoin('STD.EMPRESA_DIRECCION as D', function($join){
											$join->on('E.COD_EMPR', '=', 'D.COD_EMPR')
												 ->where('D.IND_DIRECCION_FISCAL','=',1);
										})										
										->where('activo','=',1)
										->select('WEB.pickingdetalle.*','E.NOM_EMPR as cliente','D.NOM_DIRECCION as direccion')
										->get();
		//dd($pickingdetalle);
		$funcion 					= 	$this;			
		
		$titulo_boton 				= '';
		$palets_peso 		  		=  $this->ObtenerPesoPalets();
		 
		if ($m_accion == 'DELETE'){ $titulo_boton = 'Eliminar Picking';}
		else if($m_accion == 'VIEW'){ $titulo_boton = 'X';}

		return View::make('transferencia/ajax/modaldetallepicking',
						 [
						 	 'idpicking' 				=> $idpickingencriptado,
							 'picking'					=> $picking,
							 'pickingdetalle'			=> $pickingdetalle,
							 'funcion'   				=> $funcion,
							 'idopcion'					=> $idopcion,
							 'm_accion'					=> $m_accion,
							 'titulo_boton'				=> $titulo_boton,
							 'palets_peso' 				=> $palets_peso
						 ]);
	}


	public function actionAjaxCambiarEstadoPicking(Request $request)
	{	
		$idpicking	 				= 	$request['picking_id'];
		$estado_id	 				= 	$request['estado_id'];
		$idopcion 					= 	$request['id_opcion'];
		$m_accion					= 	$request['m_accion'];

		$usuario 					=	Session::get('usuario')->id;
		$picking					=   WEBPicking::where('id','=',$idpicking)->first();

		$nom_estado 				=   "";
		$cod_estado 				=   "";

		if ($m_accion == "DELETE") {
			$nom_estado 			=   "GENERADO";
			$cod_estado 			=   "EPP0000000000002";
		}

		//Validar Estado Transferencia
		$response						= 	$this->validar_picking_estado($idpicking,$nom_estado,$cod_estado);
		if($response[0]['error']){echo json_encode($response); exit();}	
			

		if ($m_accion == "DELETE") {

			$pd  =	WEBPickingDetalle::where('picking_id','=',$idpicking)->get();		

			DB::beginTransaction();
				// Eliminaos cabecera picking
				$picking->activo			= 0;
				$picking->usuario_mod		= $usuario;
				$picking->fecha_mod 		= $this->fechaactual;
				$picking->save();

				// Regresamos cantidad pendiente a transferencia
				foreach($picking->pickingdetalle as $obj){		

					if ($obj->tipo_operacion == "TRANSFERENCIA" && $obj->transferencia_id <> '00000000000000' ){

						$objdet   = WEBTransferenciaDetalle::where('activo','=',1)
								->where('transferencia_id','=',$obj->transferencia_id)
								->where('producto_id','=',$obj->producto_id)
								->first();

						$objdet->cantidad_pendiente	+=   $obj->cantidad;
						$objdet->fecha_mod 	 		=   $this->fechaactual;
						$objdet->usuario_mod		=   Session::get('usuario')->id;	
						$objdet->save();
					}
													
				} 

				// Regresamos el estado de transferencia
				foreach($picking->pickingdetalle as $obj){	
					if ($obj->tipo_operacion == "TRANSFERENCIA" && $obj->transferencia_id <> '00000000000000'){
						$ex = $this->cambiar_transferencia_estado_picking($obj->transferencia_id);
					}
				}

				// Eliminaos detalle de picking
				DB::table('WEB.pickingdetalle')->where('picking_id','=',$idpicking)
											->update(array('activo' => 0, 
															'usuario_mod' => $usuario, 
															'fecha_mod' => $this->fechaactual));
			DB::commit();
		}		

		//return Redirect::to('/gestion-transferencia/'.$idopcion)->with('bienhecho', 'Operación realizada con éxito.');
		echo json_encode($response);
	}

	public function cambiar_transferencia_estado_picking($idtrans) {
	
		$trans						=   WEBTransferencia::where('id','=',$idtrans)->first();
		$sum_cant					= 	0;
		$sum_pend					= 	0;
		$cod_estado 				=   "EPP0000000000003"; // autorizado
		
		foreach($trans->transferenciadetalle as $obj){		
			$sum_cant += $obj->cantidad;
			$sum_pend += $obj->cantidad_pendiente;
		} 

		if($sum_cant<>$sum_pend) { $cod_estado	= 'EPP0000000000006';} // atendido pacialmente

		$trans->estado_id	 		=   $cod_estado;
		$trans->fecha_mod 	 		=   $this->fechaactual;
		$trans->usuario_mod			=   Session::get('usuario')->id;	
		$trans->save();
		return true;
	}

	public function validar_picking_estado($idpicking,$nom_estado,$estado) {

		$mensaje					=   '';
		$error						=   false;

		$picking					=   WEBPicking::where('id','=',$idpicking)->where('estado_id','=',$estado)->first();
		
		if(count($picking) <= 0){
			$mensaje = 'El registro de Picking '.$idpicking.' no se encuentra en estado '. $nom_estado .' no se puede actualizar.';
			$error   = true;
		}								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		return $response;

	}

}
