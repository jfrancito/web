<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBTransferencia,App\WEBTransferenciaDetalle,App\CMPDetalleProducto,App\CMPContrato,App\WEBReglaCreditoCliente,App\WEBReglaProductoCliente,App\WEBPrecioProductoContrato,App\WEBPrecioProducto,App\WEBOpcion,App\ALMProducto,App\CMPCategoria,\App\WEBPicking,App\WEBPickingDetalle,App\User,App\CMPOrden,App\WEBOrdenDespacho,App\WEBDetalleOrdenDespacho;
use App\STDEmpresa,App\ALMCentro,App\ALMAlmacen,App\CMPReferecenciaAsoc,App\CMPDocumentoCtble,App\CMPDetraccion,App\CMPDetraccionDetalle;
use View;
use Session;
use App\Biblioteca\Osiris;
use App\Biblioteca\OsirisDespacho;
use App\Biblioteca\Funcion;
use PDO;
use Mail;
use PDF;
use Hashids,table;

class PickingController extends Controller
{	
	public function estadosPicking(){
		return array('EPP0000000000002' => "GENERADO",		
					'EPP0000000000006' => "ATENDIDO PARCIALMENTE",
					'EPP0000000000007' => "CERRADO",
					'TODOS' => "TODOS");
	}

	public function actionListarAtenderPicking($idopcion)
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
			
		$idestado_gen 			= "EPP0000000000002";
		$combo_estados  		=  $this->estadosPicking();
		
	    $listapicking			= 	WEBPicking::where('activo','=',1)
		    						->leftJoin('CMP.CATEGORIA as E', 'E.COD_CATEGORIA', '=', 'web.picking.estado_id')
		    						->leftJoin('ALM.CENTRO as C', 'C.COD_CENTRO', '=', 'web.picking.centro_origen_id')
		    						->where('web.picking.estado_id','=', $idestado_gen)
		    						->where('web.picking.centro_origen_id','=', $centro_id)
			    					->where('fecha_picking','>=', $fechainicio)
			    					->where('fecha_picking','<=', $fechafin)
									->where('web.picking.empresa_id','=',Session::get('empresas')->COD_EMPR)
									->orderBy('fecha_picking', 'desc')
		    						->get();
				
		$funcion 				= 	$this;
	  
		return View::make('picking/listaatenderpicking',
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

	public function actionAjaxListarAtenderPicking(Request $request)
	{
		$fechainicio	=  date_format(date_create($request['finicio']), 'd-m-Y');
		$fechafin		=  date_format(date_create($request['ffin']), 'd-m-Y');
		$estado_id 		=  $request['estado_id'];
		$centro_id 	 	= 	Session::get('centros')->COD_CENTRO;
		$idopcion		= 	$request['id_opcion'];
	
		$codopcion	 		 = $this->funciones->desencriptar_id('1CIX-'.$idopcion,8);
		$opcion 			 = 	WEBOpcion::where('id','=',$codopcion)->first();

		$idestado_gen 			= "EPP0000000000002";
		$combo_estados  		=  $this->estadosPicking();

		
		if($estado_id == 'TODOS'){
			
		   	$listapicking		= 	WEBPicking::where('activo','=',1)
		    						->leftJoin('CMP.CATEGORIA as E', 'E.COD_CATEGORIA', '=', 'web.picking.estado_id')
		    						->leftJoin('ALM.CENTRO as C', 'C.COD_CENTRO', '=', 'web.picking.centro_origen_id')
		    						->whereIn('web.picking.estado_id', ['EPP0000000000002','EPP0000000000004','EPP0000000000006','EPP0000000000007'])
			    					->where('fecha_picking','>=', $fechainicio)
			    					->where('fecha_picking','<=', $fechafin)
		    						->where('web.picking.centro_origen_id','=', $centro_id)
									->where('web.picking.empresa_id','=',Session::get('empresas')->COD_EMPR)
									->orderBy('fecha_picking', 'asc')
		    						->get();
		}else{

		    $listapicking		= 	WEBPicking::where('activo','=',1)
		    						->leftJoin('CMP.CATEGORIA as E', 'E.COD_CATEGORIA', '=', 'web.picking.estado_id')
		    						->leftJoin('ALM.CENTRO as C', 'C.COD_CENTRO', '=', 'web.picking.centro_origen_id')
		    						->whereIn('web.picking.estado_id', [$estado_id])
			    					->where('fecha_picking','>=', $fechainicio)
			    					->where('fecha_picking','<=', $fechafin)
		    						->where('web.picking.centro_origen_id','=', $centro_id)
									->where('web.picking.empresa_id','=',Session::get('empresas')->COD_EMPR)
									->orderBy('fecha_picking', 'asc')
		    						->get();
		}
	    			
		$funcion 		= 	$this;
		

		return View::make('picking/ajax/alistaatenderpicking',
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


	public function actionAtenderPicking($idopcion, $idpicking) //Request $request)
	{	
		$idpicking_en 				= 	$idpicking;
		$idpicking 		 			= 	$this->funciones->decodificarmaestraBD('WEB.picking',$idpicking);

		// Validamos no exista una parcialmente atendida
		$ExisteAtendida 		= 	WEBPicking::where('estado_id', '=', 'EPP0000000000006')
										->where('activo','=',1)
										->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
										->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->where('id','<>',$idpicking)
										->select('id')->first();

		if($ExisteAtendida){
			return Redirect::to('/atender-picking/'.$idopcion)->with('errorbd', 'Existe un Picking ATENDIDO PARCIALMENTE: '. $ExisteAtendida->id);	
		}	

		$almacen_combo_id 			= 	'';

	    $picking 	 				=   WEBPicking::where('id','=',$idpicking)->first();

		$funcion 					= 	$this;
		
		$combo_almacen				=   ALMAlmacen::where('COD_ESTADO','=',1)
										->where('TXT_TIPO','=','TPA0000000000007')
										->where('COD_CATEGORIA_CLASE_ALMACEN','=','TPI0000000000002')
										->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
										->where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
										->where('IND_FISICO','=',1)
										->where('COD_ACTIVO','=',1)
										->orderBy('NOM_ALMACEN', 'asc')
										->pluck('NOM_ALMACEN','COD_ALMACEN')
                                    	->toArray();
																						
		$combo_empresas 			= 	$this->funciones->combo_todas_empresas();
		$combo_empresas_servicios   = 	$this->funciones->combo_todas_empresas_servicios();
		$combo_cuentas_servicios 	= 	array();


		$combo_centro 				= 	$this->funciones->combo_centro();
		$cod_centro					= 	Session::get('centros')->COD_CENTRO;
		$data_empresa 				=   $this->funciones->data_empresa(Session::get('empresas')->COD_EMPR);
		$data_centro 				=   $this->funciones->data_centro($cod_centro);		
		$ultimo_almacen_id 			= 	$this->funciones->ultimo_almacen_id();
		$combo_sin_centro 			= 	$this->funciones->combo_lista_quitar_centro_array_filtro($cod_centro);


	    $listaordenesgeneradas 		=   WEBPickingDetalle::where('picking_id','=',$idpicking)
	    								->where('orden_id','<>','')
	    								->where('activo','=','1')
	    								->select('orden_id')
	    								->groupBy('orden_id')
	    								->get();

		$centro_registro_trans		= 	DB::table('Web.pickingdetalle AS PD')
										->leftJoin('web.transferencia AS T', 'PD.transferencia_id', '=', 'T.id')										
										->where('PD.tipo_operacion','=','TRANSFERENCIA')	
										->where('PD.picking_id','=',$idpicking)	
										->where('PD.activo','=',1)		
										->where('T.activo','=',1)
										->select('T.centro_id')
										->first();
		
	    $lista_de_servicios			= 	array
										  	(
										  	array("servicio"=>'PRD0000000017065'),
										  	array("servicio"=>'PRD0000000003384')
										  	);		  	

		$array_centro_id 						=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 					= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);
		$combo_almacen_origen 					=   array();
		$combo_almacen_destino       			= 	$this->funciones->combo_almacen_pt($cod_centro, $centro_registro_trans->centro_id);							
		$data_productos_tranferencia_pt  		=   array();
		$combo_serie_guia 						=   $this->funciones->combo_series('TDO0000000000009','0');
		$count_servicio 						= 	1;
		$calcula_cantidad_peso 					= 	0;


		return View::make('picking/atenderpicking',
						 [
						 	'picking' 					 			=> $picking,
						 	'funcion' 								=> $funcion,
						 	'idopcion' 								=> $idopcion,
						 	'combo_almacen' 						=> $combo_almacen,
						 	'ultimo_almacen_id' 					=> $ultimo_almacen_id,
						 	'combo_empresas' 						=> $combo_empresas,
						 	'data_empresa' 							=> $data_empresa,
						 	'combo_centro' 							=> $combo_centro,
						 	'combo_almacen_destino' 				=> $combo_almacen_destino,
						 	'combo_almacen_origen' 					=> $combo_almacen_origen,
						 	'data_centro' 							=> $data_centro,
						 	'idpicking' 			 				=> $idpicking_en,
						 	'data_productos_tranferencia_pt' 		=> $data_productos_tranferencia_pt,	
						 	'almacen_combo_id' 						=> $almacen_combo_id,
						 	'listaordenesgeneradas' 				=> $listaordenesgeneradas,
						 	'lista_de_servicios' 					=> $lista_de_servicios,
						 	'combo_empresas_servicios' 				=> $combo_empresas_servicios,
						 	'combo_cuentas_servicios' 				=> $combo_cuentas_servicios,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'combo_serie_guia' 						=> $combo_serie_guia,
						 	'count_servicio' 						=> $count_servicio,
						 	'calcula_cantidad_peso' 				=> $calcula_cantidad_peso,
							'id_detalle' 							=> '',
							'centro_registro_trans'					=> $centro_registro_trans,
						 ]);

	}

	public function actionAjaxListaProductosTransferenciaPicking(Request $request)
	{

		$data_productos_tranferencia_pt_r 	= 	$request['data'];
		$funcion 							= 	$this;

		$item_productos_tranferencia_pt 	= 	array();
		$data_productos_tranferencia_pt 	= 	array();		

		$calcula_cantidad_peso 				= 	0;


	    foreach($data_productos_tranferencia_pt_r as $index => $item){

	    	$almacen_lote_group_id    		=   $this->funciones->select_almacen_lote_group_array_lote(	$item['data_producto'],
	    																								$item['almacen_id'],
	    																								$item['cantidad_atender'],
	    																								$item['array_lote_id']);

		    foreach($almacen_lote_group_id as $indexa => $row){

		    	$producto 						= 	ALMProducto::where('COD_PRODUCTO','=',$item['data_producto'])->first();
		    	$calcula_cantidad_peso 			=   $calcula_cantidad_peso + (float)$row['CANT_ATENDER_LOTE']*$producto->CAN_PESO_MATERIAL;
				$item_productos_tranferencia_pt	=	array(	'data_detalle_orden_despacho' => $item['data_detalle_orden_despacho'] ,
															'data_producto' 			  => $item['data_producto'],
															'nombre_producto' 			  => $item['nombre_producto'],
															'unidad_medida' 			  => $item['unidad_medida'],
															'almacen_id' 			  	  => $item['almacen_id'],
															'almacen_nombre' 			  => $item['almacen_nombre'],
															'neto' 			  		  	  => $row['STK_NETO'],
															'lote_id' 			  		  => $row['COD_LOTE'],
															'cantidad_atender' 			  => $row['CANT_ATENDER_LOTE'],
															'costo' 			  		  => $row['CAN_COSTO'],
															'total' 			  		  => $row['TOTAL']
														 );

				array_push($data_productos_tranferencia_pt,$item_productos_tranferencia_pt);
		    }
		}

		$calcula_cantidad_peso 					= 	$calcula_cantidad_peso/1000;

		return View::make('picking/ajax/aproductostransferenciapicking',
						 [
						 	'data_productos_tranferencia_pt' 		=> $data_productos_tranferencia_pt,
						 	'calcula_cantidad_peso' 				=> $calcula_cantidad_peso,
						 	'funcion' 								=> $funcion,
						 	'ajax'   		  						=> true,
						 ]);
	}

	public function actionAjaxListaProductosOrdenSalidaPicking(Request $request)
	{

		$data_productos_ordensalida_r 	    = 	$request['data'];
		$funcion 							= 	$this;

		$item_productos_ordensalida 	= 	array();
		$data_productos_ordensalida 	= 	array();		

		$calcula_cantidad_peso 				= 	0;
		
	    foreach($data_productos_ordensalida_r as $index => $item){

	    	$almacen_lote_group_id    		=   $this->funciones->select_almacen_lote_group_array_lote(	$item['data_producto'],
	    																								$item['almacen_id'],
	    																								$item['cantidad_atender'],
	    																								$item['array_lote_id']);
			
		    foreach($almacen_lote_group_id as $indexa => $row){

		    	$producto 						= 	ALMProducto::where('COD_PRODUCTO','=',$item['data_producto'])->first();
		    	$calcula_cantidad_peso 			=   $calcula_cantidad_peso + (float)$row['CANT_ATENDER_LOTE']*$producto->CAN_PESO_MATERIAL;
				$item_productos_ordensalida 	=	array(	'data_detalle_orden_despacho' => $item['data_detalle_orden_despacho'] ,
															'data_producto' 			  => $item['data_producto'],
															'nombre_producto' 			  => $item['nombre_producto'],
															'unidad_medida' 			  => $item['unidad_medida'],
															'almacen_id' 			  	  => $item['almacen_id'],
															'almacen_nombre' 			  => $item['almacen_nombre'],
															'neto' 			  		  	  => $row['STK_NETO'],
															'lote_id' 			  		  => $row['COD_LOTE'],
															'cantidad_atender' 			  => $row['CANT_ATENDER_LOTE'],
															'costo' 			  		  => $row['CAN_COSTO'],
															'total' 			  		  => $row['TOTAL']
														 );

				array_push($data_productos_ordensalida,$item_productos_ordensalida);
		    }
		}

		$calcula_cantidad_peso 					= 	$calcula_cantidad_peso/1000;

		return View::make('picking/ajax/aproductosordensalidapicking',
						 [
						 	'data_productos_ordensalida' 			=> $data_productos_ordensalida,
						 	'calcula_cantidad_peso' 				=> $calcula_cantidad_peso,
						 	'funcion' 								=> $funcion,
						 	'ajax'   		  						=> true,
						 ]);
	}


	public function actionCrearTransferenciaPicking($idopcion,$idpicking,Request $request)
	{
			try{

				DB::beginTransaction();
				$h_glosa 								=  	$request['h_glosa'];
				$h_origen_propietario 					=  	$request['h_origen_propietario'];
				$h_origen_servicio 						=  	$request['h_origen_servicio'];
				$h_destino_propietario 					=  	$request['h_destino_propietario'];
				$h_destino_servicio 					=  	$request['h_destino_servicio'];
				$h_destino_centro 						=  	$request['h_destino_centro'];
				$h_destino_almacen 						=  	$request['h_destino_almacen'];
				$h_origen_almacen 						=  	$request['h_origen_almacen'];
				
				$data_origen_propietario 				=   STDEmpresa::where('NOM_EMPR','=',$h_origen_propietario)->first();
				$data_origen_servicio 					=   STDEmpresa::where('NOM_EMPR','=',$h_origen_servicio)->first();
				$data_destino_propietario 				=   STDEmpresa::where('NOM_EMPR','=',$h_destino_propietario)->first();
				$data_destino_servicio 					=   STDEmpresa::where('NOM_EMPR','=',$h_destino_servicio)->first();
				$data_destino_centro 					=   ALMCentro::where('COD_CENTRO','=',$h_destino_centro)->first();
				$data_destino_almacen 					=   ALMAlmacen::where('COD_ALMACEN','=',$h_destino_almacen)->first();
				$data_origen_almacen 					=   ALMAlmacen::where('COD_ALMACEN','=',$h_origen_almacen)->first();
				
				$data_tipo_cambio 						= 	$this->funciones->tipo_cambio();

				$h_array_productos_transferencia_pt 	=  	$request['array_productos_transferencia_pt_h'];
				$array_servicio_transferencia_pt_h 		=  	$request['array_servicio_transferencia_pt_h'];

				$despacho 								= 	new OsirisDespacho();
				
				$respuesta 								=  	$despacho->guardar_orden_pedido_transferencia($h_glosa,$data_origen_propietario,
															$data_origen_servicio,$data_destino_propietario,$data_destino_servicio,
															$data_destino_centro,$data_destino_almacen,$h_array_productos_transferencia_pt,$data_tipo_cambio,
															$data_origen_almacen,$array_servicio_transferencia_pt_h,"PICKING");
				
				
				//Cambiamos de estado al picking
				$this->CambiarEstadoPicking($idpicking);

				DB::commit();
	 			return Redirect::to('/atender-picking/'.$idopcion.'/'.$idpicking)->with('bienhecho', 'Transferencia PT '.$respuesta.' registrada con éxito');

			}catch(Exception $ex){
				DB::rollback();
				return Redirect::to('/atender-picking/'.$idopcion.'/'.$idpicking)->with('errorbd', 'Ocurrio un error inesperado. Porfavor contacte con el administrador del sistema : '.$ex);	
			}
	}

	public Function CambiarEstadoPicking($idpicking){
		$idpicking 								= $this->funciones->decodificarmaestraBD('WEB.picking',$idpicking);
		$picking 								=  WEBPicking::where('id','=',$idpicking)->first();
		$cantConOrden							=  0;
		
		foreach($picking->pickingdetalle as $obj){		
			if (trim($obj->orden_id) <> ""){
				$cantConOrden 	+= 1;						
			}													
		} 

        $picking->fecha_mod                	=       $this->fechaactual;
        $picking->usuario_mod              	=       Session::get('usuario')->id;
		
		if (count($picking->pickingdetalle) == $cantConOrden){ // Si es igual, todos tienen orden registrada
			$picking->estado_id  			=       "EPP0000000000004";      
		}else{
			$picking->estado_id  			=       "EPP0000000000006";         
		}		
		$picking->save();  
		           
	}

	public function actionCrearOrdenSalidaPicking($idopcion,$idpicking,Request $request)
	{		
			try{
				DB::beginTransaction();
				$h_glosa 								=  	$request['h_glosa'];
				$h_empresa_propietario 					=  	$request['h_empresa_propietario'];
				$h_empresa_servicio						=  	$request['h_empresa_servicio'];
				
				$data_empresa_propietario 				=   STDEmpresa::where('NOM_EMPR','=',$h_empresa_propietario)->first();
				$data_empresa_servicio 					=   STDEmpresa::where('NOM_EMPR','=',$h_empresa_servicio)->first();
				
				$data_tipo_cambio 						= 	$this->funciones->tipo_cambio();

				$h_array_productos_ordensalida 			=  	$request['array_productos_ordensalida_h'];
				$h_array_productos_ordensalida 		    =   json_decode($h_array_productos_ordensalida,true);
               
				$arrDetId                               =   explode("-", $h_array_productos_ordensalida[0]["data_detalle_orden_despacho"]); // Para sacar la orden de venta
				$codOrdenVenta 							= 	$arrDetId[1];

				$despacho 								= 	new OsirisDespacho();
				$respuesta_os							=  	$despacho->guardar_salida_venta($codOrdenVenta,$h_glosa,$data_empresa_propietario,
															$data_empresa_servicio,$h_array_productos_ordensalida,$data_tipo_cambio);
				
				$referencia_asoc 						= 	$despacho->guardar_referencia_asoc($codOrdenVenta,$respuesta_os,'CMP.ORDEN','CMP.ORDEN',$h_glosa);
				
				//Cambiamos el estado de la venta
				$ordenActualizada 						= 	$despacho->CambiarEstadoOrdenVenta($codOrdenVenta);
				
				//Cambiamos de estado al picking
				$this->CambiarEstadoPicking($idpicking);

				DB::commit();
	 			return Redirect::to('/atender-picking/'.$idopcion.'/'.$idpicking)->with('bienhecho', 'Orden de Salida '.$respuesta_os.' registrada con éxito');

			}catch(Exception $ex){
				DB::rollback();
				return Redirect::to('/atender-picking/'.$idopcion.'/'.$idpicking)->with('errorbd', 'Ocurrio un error inesperado. Porfavor contacte con el administrador del sistema : '.$ex);	
			}
	}


	public function actionGenerarDetraccionPicking($idopcion, $idpicking) //Request $request)
	{
		$idpicking_en 				= 	$idpicking;
		$idpicking 		 			= 	$this->funciones->decodificarmaestraBD('WEB.picking',$idpicking);

	    $picking 	 				=   WEBPicking::where('id','=',$idpicking)->first();

		$array_detalle_picking		=  json_decode($picking->pickingdetalle ,true);	
		
		$temp 						= array_unique(array_column($array_detalle_picking, 'transferencia_id'));
		$group_detalle_picking 		= [];
		
		foreach($temp as $index => $item){
			$tienedet				=	0;
			$tipo_operacion			=   "TRANSFERENCIA";
		
			$detraccion				=  	CMPDetraccionDetalle::where('COD_ESTADO','=',1)
										->where('COD_ORDEN','=',$item)
										->where('ID_PICKING','=',$idpicking)
										->first();
			
			if($detraccion){
				$tienedet			=	1;
			}
			if(strlen($item) == 16 ){
				$tipo_operacion		=	"ORDEN";
			}
			array_push($group_detalle_picking, array(
					'transferencia_id' 	=> $item,
					'tiene_detraccion' 	=> $tienedet,
					'tipo_operacion' 	=>  $tipo_operacion
			));	
		}
		
		$data_productos_detraccion 	= 	array();	

		$funcion 					= 	$this;
		
		return View::make('picking/generardetraccion',
						 [
						 	'picking' 					 			=> $picking,
						 	'funcion' 								=> $funcion,
						 	'idopcion' 								=> $idopcion,
						 	'idpicking' 			 				=> $idpicking_en,
							'group_detalle_picking'					=> $group_detalle_picking,
							'data_productos_detraccion'				=> $data_productos_detraccion,
							'total_detraccion'						=> 0,
						 ]);

	}

	public function actionAjaxCalcularDetraccion(Request $request)
	{
		$data_detraccion				 	= 	$request['data'];
		$picking_id 					 	= 	$request['idpicking'];
		$funcion 							= 	$this;
		
		$data_productos  					= 	array();	
		$item_productos_temp 				= 	array();	
		$arrPro								= 	array();	

		$cod_centro 						=	Session::get('centros')->COD_CENTRO;
	    $picking 	 						=   WEBPicking::where('id','=',$picking_id)->first();
		$total_detraccion 					=	0;
	
		if($data_detraccion){
			foreach($data_detraccion as $index => $item){
				$dataCode = explode("-",$item['id']);
				$coddet   = $dataCode[1];
	
				if(strlen($coddet) == 16){ // Es Venta

					if ($item['tipo'] == "GRR") {
						// Generamos segun GRR
						$arrPro = $this->CalculaDetraccionProductoGRR($data_productos,$picking,$coddet,$cod_centro);	

					}elseif($item['tipo'] == "FAC") {
						// Generamos segun Factura
						$arrPro = $this->CalculaDetraccionProductoFAC($data_productos,$picking,$coddet);

					}
				}else{ 	// Es Solicitud Transferencia
					if ($item['tipo'] == "GRR") {
						// Generamos segun GRR
						$arrPro = $this->CalculaDetraccionProductoGRR($data_productos,$picking,$coddet,$cod_centro);							
					}
				}
			}							
		}
		return View::make('picking/form/detracciondetalle',
						 [
						 	'data_productos_detraccion' 			=> $data_productos,
							'total_detraccion'						=> $total_detraccion,
						 	'funcion' 								=> $funcion,
						 	'ajax'   		  						=> true,
						 ]);
						 
	}

	public function CalculaDetraccionProductoFAC(&$data_productos,$picking,$coddet){

		$lsObjRef 				= CMPReferecenciaAsoc::where('COD_ESTADO','=',1)
									->where('COD_TABLA','=',$coddet)
									->get();	
		$codDocFac              = "";
		$arrTemp					= [];

		foreach($lsObjRef as $det){	
			if (str_contains($det->COD_TABLA_ASOC, 'FC')) {
				$codDocFac = $det->COD_TABLA_ASOC;
				break; 
			}
		}		

		foreach($picking->pickingdetalle as $obj){		
			
			if (trim($obj->transferencia_id) == $coddet){
				
				$PrecioProducto 	= 0;
				$PorcenMidagri 		= 3.85;

				if ($codDocFac <> ""){
					$lsDetFac		= CMPDetalleProducto::where('COD_ESTADO','=',1)
									->where('COD_TABLA','=',$codDocFac)									
									->get();	
									
					foreach($lsDetFac as $detfac){	
						if($detfac->COD_PRODUCTO == $obj->producto_id) {
							$PrecioProducto = $detfac->CAN_PRECIO_UNIT_IGV;
							break; 
						}
					}	
				}		

				$detraccion 	= $PrecioProducto * $obj->cantidad * ($PorcenMidagri / 100);

				array_push($data_productos, array(
					'tipo_operacion' 		=> $obj->tipo_operacion,
					'picking_id' 			=> $picking->id,
					'ind_doc'	 			=> 'FAC',
					'transferencia_id' 		=> $obj->transferencia_id,
					'producto_id' 			=> $obj->producto_id,
					'producto_nombre' 		=> $obj->producto_nombre,
					'cantidad' 				=> $obj->cantidad,	
					'precio_midragri'		=> $PrecioProducto,								
					'porcentaje_midragri'	=> $PorcenMidagri,
					'detraccion'			=> $detraccion
				 ));
			}													
		} 
	}

	public function CalculaDetraccionProductoGRR(&$data_productos,$picking,$coddet,$cod_centro){
		
		foreach($picking->pickingdetalle as $obj){		
		
			if (trim($obj->transferencia_id) == $coddet){
				
				$PrecioMidagri 	= 0;
				$PorcenMidagri 	= 3.85;

				$arrMidagri 	= $this->ObtenerPrecioMidragri($obj->producto_id, $cod_centro);
				
				if($arrMidagri){ $PrecioMidagri = $arrMidagri[0]; }
				$can_tot_item 	= $obj->cantidad + $obj->cantidad_excedente;
				
				$PesoProducto 	= $obj->producto_peso;
				if ($PesoProducto == 49) {
					$PesoProducto = 50;
				}

				$detraccion 	= $PrecioMidagri * ($can_tot_item * $PesoProducto / 50) * ($PorcenMidagri / 100);


				array_push($data_productos, array(
					'tipo_operacion' 		=> $obj->tipo_operacion,
					'picking_id' 			=> $picking->id,
					'ind_doc'	 			=> 'GRR',
					'transferencia_id' 		=> $obj->transferencia_id,
					'producto_id' 			=> $obj->producto_id,
					'producto_nombre' 		=> $obj->producto_nombre,
					'cantidad' 				=> $can_tot_item,	
					'precio_midragri'		=> $PrecioMidagri,								
					'porcentaje_midragri'	=> $PorcenMidagri,
					'detraccion'			=> $detraccion
				 ));
			}													
		} 
	}

	public function ObtenerPrecioMidragri($codproducto, $codcentro){
		$tipope 		=  "PRO";
		$codpro			=  $codproducto;
		$codcen			=  $codcentro;
		$vacio 			=  '';
		$activo 		=  1;
		$stmt			=  DB::connection('sqlsrv')->getPdo()
							->prepare('SET NOCOUNT ON;EXEC STD.PRECIO_CALIDAD_DETALLE_LISTAR ?,?,?,?,?');

		$stmt->bindParam(1, $tipope, PDO::PARAM_STR);                   
		$stmt->bindParam(2, $codpro, PDO::PARAM_STR);              
		$stmt->bindParam(3, $codcen ,PDO::PARAM_STR);              
		$stmt->bindParam(4, $vacio ,PDO::PARAM_STR);              
		$stmt->bindParam(5, $activo ,PDO::PARAM_STR);                 
		$stmt->execute();        

		$array_ 						= array();
				
		while($row = $stmt->fetch()){
			$array_ 			  		=   $array_ + [$row['CAN_PRECIO']];
		}		
		return $array_;
	}
	
	public function actionGuardarDetraccionPicking($idopcion,Request $request)
	{		
		$idpicking 							=  	$request['h_idpicking'];	
		
		try{
			DB::beginTransaction();

			$array_productos_detraccion 	=  	$request['h_data_productos_detraccion'];
			$array_group_detalle 			=  	$request['h_group_detalle_picking'];
			$array_productos_detraccion	   	=   json_decode($array_productos_detraccion,true);
			$array_group_detalle		  	=   json_decode($array_group_detalle,true);
			

			$doc_ref 						=  	(string)strtoupper($request['serie_grr']).'-'.$request['corr_grr'];	

			$tot 							=  	(string)$request['monto'];		
			$total							= 	str_replace(",","",$tot);			
			$cod_empr 						= 	Session::get('empresas')->COD_EMPR;
			$cod_centro 					= 	Session::get('centros')->COD_CENTRO;
			$usuario 						= 	Session::get('usuario')->id;		
			$activo							= 	1;
			$accion							= 	"I";
			$vacio							= 	"";
			
			$stmt			=  DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.DETRACCION_IUD ?,?,?,?,?,?,?,?');
				
			$stmt->bindParam(1, $accion, PDO::PARAM_STR);      
			$stmt->bindParam(2, $vacio, PDO::PARAM_STR);                   
			$stmt->bindParam(3, $doc_ref, PDO::PARAM_STR);              
			$stmt->bindParam(4, $total, PDO::PARAM_STR);              
			$stmt->bindParam(5, $cod_empr , PDO::PARAM_STR);              
			$stmt->bindParam(6, $cod_centro, PDO::PARAM_STR);            
			$stmt->bindParam(7, $usuario, PDO::PARAM_STR);              
			$stmt->bindParam(8, $activo, PDO::PARAM_STR);                 
			$stmt->execute();  
			$codigo = $stmt->fetch();
						
			foreach($array_productos_detraccion as $det){

					$detalle            	 			=	new CMPDetraccionDetalle;
					$detalle->COD_DETRACCION 			= 	$codigo[0];					
					$detalle->ID_PICKING 	   			=  	$det['picking_id'];
					$detalle->COD_ORDEN 	   			=  	$det['transferencia_id'];
					$detalle->IND_DOC 	    			=  	$det['ind_doc'];
					$detalle->COD_PRODUCTO 	   			=  	$det['producto_id'];
					$detalle->TXT_PRODUCTO 	   			=  	$det['producto_nombre'];
					$detalle->CAN_PRODUCTO 	   			=  	$det['cantidad'];
					$detalle->CAN_PRECIO_MIDAGRI 	   	=  	$det['precio_midragri'];
					$detalle->CAN_PORCENTAJE_MIDAGRI 	=  	$det['porcentaje_midragri'];
					$detalle->CAN_DETRACCION 			=  	$det['detraccion'];
					$detalle->COD_USUARIO_CREA_AUD 		=  	Session::get('usuario')->id;
					$detalle->FEC_USUARIO_CREA_AUD		=  	$this->fechaactual;
					$detalle->COD_USUARIO_MODIF_AUD		=  	Session::get('usuario')->id;
					$detalle->FEC_USUARIO_MODIF_AUD		=  	$this->fechaactual;
					$detalle->COD_ESTADO    			=  	1;
					$detalle->save();
			}		

			DB::commit();
			
			$coddec	= Hashids::encode(substr($idpicking, -8));

			return Redirect::to('/generar-detraccion-picking/'.$idopcion.'/'.$coddec)->with('bienhecho', 'Registrado correctamete.');								 

		}catch(Exception $ex){
			DB::rollback();
			return Redirect::to('/atender-picking/'.$idopcion)->with('errorbd', 'Ocurrio un error inesperado. Porfavor contacte con el administrador del sistema : '.$ex);	
		}  

	}

	public function actionAjaxComboAlmacenOrigenPk(Request $request)
	{

		$origen_centro_id 			= 	Session::get('centros')->COD_CENTRO; 
		$almacen_combo_id 			= 	$request['almacen_combo_id'];
        $combo_almacen_origen   	=   $this->funciones->combo_almacen($origen_centro_id,'TODOS');

		return View::make('despacho/ajax/acomboalmacenorigen',
						 [
						 	'combo_almacen_origen' 		=> $combo_almacen_origen,
						 	'almacen_combo_id' 			=> $almacen_combo_id,
						 	'ajax'   		  			=> true,
						 ]);
	}

	public function actionAjaxComboAlmacenDestinoPk(Request $request)
	{

		$destino_centro_id 			= 	Session::get('centros')->COD_CENTRO; 
        $combo_almacen_destino   	=   $this->funciones->combo_almacen($destino_centro_id,'TRANSITO');

		return View::make('despacho/ajax/acomboalmacendestino',
						 [
						 	'combo_almacen_destino' 	=> $combo_almacen_destino,
						 	'ajax'   		  			=> true,
						 ]);
	}



}