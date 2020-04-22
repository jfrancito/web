<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;

use View;
use Session;
use App\Biblioteca\OsirisDespacho;
use App\Biblioteca\Funcion;
use PDO;
use Mail;
use PDF;
use App\WEBOrdenDespacho,App\WEBDetalleOrdenDespacho,App\CMPOrden,App\WEBListaCliente,App\ALMProducto,App\CMPCategoria;
use App\STDEmpresa,App\ALMCentro,App\ALMAlmacen;
    
class AtenderPedidoDespachoController extends Controller
{


	public function actionAjaxAgregarServicio(Request $request)
	{
		
		$count_servicio 			=  	(int)$request['count_servicio'] + 1;
		$calcula_cantidad_peso 		=  	$request['calcula_cantidad_peso'];


	    $lista_de_servicios			= 	array
										  	(
										  	array("servicio"=>'PRD0000000017065'),
										  	array("servicio"=>'PRD0000000003384')
										  	);

	    for( $i= 1 ; $i < $count_servicio ; $i++ )
		{
			array_push($lista_de_servicios, array("servicio"=>'PRD0000000017065'));		
		}


						  	

		$combo_empresas_servicios   = 	$this->funciones->combo_todas_empresas_servicios();
		$combo_cuentas_servicios 	= 	array();
		$funcion 					= 	$this;


		return View::make('despacho/tab/tablas/listaservicios',
						 [
						 	'lista_de_servicios' 		=> $lista_de_servicios,
						 	'combo_empresas_servicios' 	=> $combo_empresas_servicios,
						 	'combo_cuentas_servicios' 	=> $combo_cuentas_servicios,
						 	'funcion' 					=> $funcion,
						 	'count_servicio' 			=> $count_servicio,
						 	'calcula_cantidad_peso' 	=> $calcula_cantidad_peso,
						 	'ajax'   		  			=> true,
						 ]);
	}



	public function actionAjaxComboCuentaServicio(Request $request)
	{

		$empresa_servicio_id 			= 	$request['empresa_servicio_id'];
        $combo_cuentas_servicios   		=   $this->funciones->combo_cuentas_empresa_cliente($empresa_servicio_id);

		return View::make('despacho/ajax/acombocuentaservicio',
						 [
						 	'combo_cuentas_servicios' 	=> $combo_cuentas_servicios,
						 	'ajax'   		  			=> true,
						 ]);
	}


	public function actionCrearTransferenciaPt($idopcion,$idordendespacho,Request $request)
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
															$data_origen_almacen,$array_servicio_transferencia_pt_h);

				DB::commit();
	 			return Redirect::to('/atender-orden-despacho/'.$idopcion.'/'.$idordendespacho)->with('bienhecho', 'Transferencia PT '.$respuesta.' registrado con exito');

			}catch(Exception $ex){
				DB::rollback();
				return Redirect::to('/atender-orden-despacho/'.$idopcion.'/'.$idordendespacho)->with('errorbd', 'Ocurrio un error inesperado. Porfavor contacte con el administrador del sistema : '.$ex);	
			}
	}



	public function actionAtenderOrdenDespacho($idopcion,$idordendespacho)
	{

		$idordendespacho_en 		= 	$idordendespacho;
		$idordendespacho 			= 	$this->funciones->decodificarmaestra($idordendespacho);
		$almacen_combo_id 			= 	'';

	    $ordendespacho 				=   WEBOrdenDespacho::where('id','=',$idordendespacho)->first();


		$funcion 					= 	$this;
		$combo_almacen 				= 	$this->funciones->combo_almacen(Session::get('centros')->COD_CENTRO,'TODOS');
		$combo_empresas 			= 	$this->funciones->combo_todas_empresas();
		$combo_empresas_servicios   = 	$this->funciones->combo_todas_empresas_servicios();
		$combo_cuentas_servicios 	= 	array();


		$combo_centro 				= 	$this->funciones->combo_centro();
		$data_empresa 				=   $this->funciones->data_empresa(Session::get('empresas')->COD_EMPR);
		$data_centro 				=   $this->funciones->data_centro(Session::get('centros')->COD_CENTRO);		
		$ultimo_almacen_id 			= 	$this->funciones->ultimo_almacen_id();


	    $listatranferenciaspt 		=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$idordendespacho)
	    								->where('orden_transferencia_id','<>','')
	    								->select('orden_transferencia_id')
	    								->groupBy('orden_transferencia_id')
	    								->get();

	    $lista_de_servicios			= 	array
										  	(
										  	array("servicio"=>'PRD0000000017065'),
										  	array("servicio"=>'PRD0000000003384')
										  	);

		//dd($lista_de_servicios);						  	
		$combo_lista_centros 					= 	$this->funciones->combo_lista_centro();
		$combo_almacen_origen 					=   array();
		$combo_almacen_destino 					=   array();
		$data_productos_tranferencia_pt 		=   array();
		$combo_serie_guia 						=   $this->funciones->combo_series('TDO0000000000009','0');
		$count_servicio 						= 	1;
		$calcula_cantidad_peso 					= 	0;


		return View::make('despacho/atenderordendespacho',
						 [
						 	'ordendespacho' 						=> $ordendespacho,
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
						 	'idordendespacho' 						=> $idordendespacho_en,
						 	'data_productos_tranferencia_pt' 		=> $data_productos_tranferencia_pt,	
						 	'almacen_combo_id' 						=> $almacen_combo_id,
						 	'listatranferenciaspt' 					=> $listatranferenciaspt,
						 	'lista_de_servicios' 					=> $lista_de_servicios,
						 	'combo_empresas_servicios' 				=> $combo_empresas_servicios,
						 	'combo_cuentas_servicios' 				=> $combo_cuentas_servicios,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'combo_serie_guia' 						=> $combo_serie_guia,
						 	'count_servicio' 						=> $count_servicio,
						 	'calcula_cantidad_peso' 				=> $calcula_cantidad_peso,

						 ]);

	}








	public function actionAjaxListaProductosTransferenciaPt(Request $request)
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

		return View::make('despacho/ajax/aproductostransferenciapt',
						 [
						 	'data_productos_tranferencia_pt' 		=> $data_productos_tranferencia_pt,
						 	'calcula_cantidad_peso' 				=> $calcula_cantidad_peso,
						 	'funcion' 								=> $funcion,
						 	'ajax'   		  						=> true,
						 ]);
	}


	public function actionAjaxComboAlmacenOrigen(Request $request)
	{

		$origen_centro_id 			= 	'CEN0000000000001';
		$almacen_combo_id 			= 	$request['almacen_combo_id'];
        $combo_almacen_origen   	=   $this->funciones->combo_almacen($origen_centro_id,'TODOS');

		return View::make('despacho/ajax/acomboalmacenorigen',
						 [
						 	'combo_almacen_origen' 		=> $combo_almacen_origen,
						 	'almacen_combo_id' 			=> $almacen_combo_id,
						 	'ajax'   		  			=> true,
						 ]);
	}



	public function actionAjaxComboAlmacenDestino(Request $request)
	{

		$destino_centro_id 			= 	'CEN0000000000001';
        $combo_almacen_destino   	=   $this->funciones->combo_almacen($destino_centro_id,'TRANSITO');

		return View::make('despacho/ajax/acomboalmacendestino',
						 [
						 	'combo_almacen_destino' 	=> $combo_almacen_destino,
						 	'ajax'   		  			=> true,
						 ]);
	}



	public function actionAjaxComboLoteAlmacen(Request $request)
	{

		$almacen_id 				= 	$request['almacen_id'];
		$producto_id 				= 	$request['producto_id'];
		$cantidad_atender 			= 	$request['cantidad_atender'];

        $combo_almacen_lote   		=   $this->funciones->combo_almacen_lote($producto_id,$almacen_id);
        $almacen_lote_group_id   	=   $this->funciones->select_almacen_lote_group($producto_id,$almacen_id,$cantidad_atender);


		return View::make('despacho/ajax/acombolote',
						 [
						 	'combo_almacen_lote' 		=> $combo_almacen_lote,
						 	'almacen_lote_group_id' 	=> $almacen_lote_group_id,
						 	'ajax'   		  			=> true,
						 ]);
	}



	public function actionAjaxCalcularStockAlmacenLote(Request $request)
	{

		$array_lote_id 				= 	$request['array_lote_id'];
		$almacen_id 				= 	$request['almacen_id'];
		$producto_id 				= 	$request['producto_id'];

        $stock_neto           		=   $this->funciones->select_data_almacen_lote_group($producto_id,$almacen_id,$array_lote_id,'STK_NETO');
        $stock_fisico         		=   $this->funciones->select_data_almacen_lote_group($producto_id,$almacen_id,$array_lote_id,'CAN_FIN_MAT');
        $costo                		=   $this->funciones->select_data_almacen_lote_group($producto_id,$almacen_id,$array_lote_id,'CAN_COSTO');

		return View::make('despacho/ajax/astockalmacenlote',
						 [
						 	'stock_neto' 				=> $stock_neto,
						 	'stock_fisico' 				=> $stock_fisico,
						 	'costo' 					=> $costo,
						 	'ajax'   		  			=> true,
						 ]);
	}


	public function actionAjaxPedidoAtenderModificarFechaEntrega(Request $request)
	{


		$array_data_producto_despacho 				= 	$request['data_producto_despacho'];
		$fechadeentrega 							=   date_format(date_create($request['fechadeentrega']), 'd-m-Y');
		$ordendespacho_id 							= 	$request['ordendespacho_id'];

		foreach($array_data_producto_despacho as $key => $obj){

			$detalle_orden_despacho_id				= 	$obj['data_detalle_orden_despacho'];
			//actualizar fechas en detalle de pedido despacho
			$array_detalle_orden_despacho_id 		= 	explode(",", $detalle_orden_despacho_id);
			foreach ($array_detalle_orden_despacho_id as $values)
			{
				$detalleordendespacho               	=   WEBDetalleOrdenDespacho::where('id','=',$values)->first();
				$detalleordendespacho->fecha_entrega 	=  	$fechadeentrega;//fecha entrega falta
				$detalleordendespacho->fecha_mod 		=  	$this->fechaactual;
				$detalleordendespacho->usuario_mod 		=  	Session::get('usuario')->id;
				$detalleordendespacho->save();
			}

		}	

	    $ordendespacho 								=   WEBOrdenDespacho::where('id','=',$ordendespacho_id)->first();
		$funcion 									= 	$this;

		$combo_almacen 								= 	$this->funciones->combo_almacen(Session::get('centros')->COD_CENTRO,'TODOS');	
		$ultimo_almacen_id 							= 	$this->funciones->ultimo_almacen_id();
		$combo_serie_guia 							=   $this->funciones->combo_series('TDO0000000000009','0');



		return View::make('despacho/ajax/alistapedidoatendertransferencia',
						 [
						 	'ordendespacho' 			=> $ordendespacho,
						 	'funcion' 					=> $funcion,
						 	'combo_almacen' 			=> $combo_almacen,
						 	'ultimo_almacen_id' 		=> $ultimo_almacen_id,
						 	'ajax'   		  			=> true,
						 	'combo_serie_guia'   		=> $combo_serie_guia,	
						 ]);
	}


	public function actionAjaxPedidoAtenderModificarOrigen(Request $request)
	{


		$array_data_producto_despacho 				= 	$request['data_producto_despacho'];
		$centro_origen_id 							=   $request['centro_origen_id'];
		$ordendespacho_id 							= 	$request['ordendespacho_id'];

		foreach($array_data_producto_despacho as $key => $obj){

			$detalle_orden_despacho_id				= 	$obj['data_detalle_orden_despacho'];
			//actualizar fechas en detalle de pedido despacho
			$array_detalle_orden_despacho_id 		= 	explode(",", $detalle_orden_despacho_id);
			foreach ($array_detalle_orden_despacho_id as $values)
			{
				$detalleordendespacho               	=   WEBDetalleOrdenDespacho::where('id','=',$values)->first();
				$detalleordendespacho->centro_atender_id 	=  	$centro_origen_id;
				$detalleordendespacho->fecha_mod 		=  	$this->fechaactual;
				$detalleordendespacho->usuario_mod 		=  	Session::get('usuario')->id;
				$detalleordendespacho->save();
			}

		}	

	    $ordendespacho 								=   WEBOrdenDespacho::where('id','=',$ordendespacho_id)->first();
		$funcion 									= 	$this;

		$combo_almacen 								= 	$this->funciones->combo_almacen(Session::get('centros')->COD_CENTRO,'TODOS');	
		$ultimo_almacen_id 							= 	$this->funciones->ultimo_almacen_id();
		$combo_serie_guia 							=   $this->funciones->combo_series('TDO0000000000009','0');



		return View::make('despacho/ajax/alistapedidoatendertransferencia',
						 [
						 	'ordendespacho' 			=> $ordendespacho,
						 	'funcion' 					=> $funcion,
						 	'combo_almacen' 			=> $combo_almacen,
						 	'ultimo_almacen_id' 		=> $ultimo_almacen_id,
						 	'ajax'   		  			=> true,
						 	'combo_serie_guia'   		=> $combo_serie_guia,
						 ]);
	}



	public function actionAjaxPedidoAtenderModificarCantidadAtender(Request $request)
	{


		$array_data_producto_despacho 				= 	$request['data_producto_despacho'];
		$ordendespacho_id 							= 	$request['ordendespacho_id'];

		foreach($array_data_producto_despacho as $key => $obj){

			$detalle_orden_despacho_id					= 	$obj['data_detalle_orden_despacho'];
			$cantidad_atender_total						= 	$obj['cantidad_atender'];
			$serie										= 	$obj['serie'];
			$nro_documento								= 	$obj['nro_documento'];


			$cantidad_atender 							= 	0.00;

			$array_detalle_orden_despacho_id 		= 	explode(",", $detalle_orden_despacho_id);
			foreach ($array_detalle_orden_despacho_id as $values)
			{

				$cantidad_atender							= 	$cantidad_atender_total/count($array_detalle_orden_despacho_id);
				$detalleordendespacho               		=   WEBDetalleOrdenDespacho::where('id','=',$values)->first();
				$detalleordendespacho->cantidad_atender 	=  	$cantidad_atender;
				$detalleordendespacho->nro_serie 			=  	$serie;
				$detalleordendespacho->nro_documento 		=  	$nro_documento;
				$detalleordendespacho->fecha_mod 			=  	$this->fechaactual;
				$detalleordendespacho->usuario_mod 			=  	Session::get('usuario')->id;
				$detalleordendespacho->save();

			}


		}	


		$this->funciones->cambio_estado_parcialmente_terminado($ordendespacho_id);


	    $ordendespacho 								=   WEBOrdenDespacho::where('id','=',$ordendespacho_id)->first();
		$funcion 									= 	$this;

		$combo_almacen 								= 	$this->funciones->combo_almacen(Session::get('centros')->COD_CENTRO,'TODOS');	
		$ultimo_almacen_id 							= 	$this->funciones->ultimo_almacen_id();
		$combo_serie_guia 							=   $this->funciones->combo_series('TDO0000000000009','0');

		return View::make('despacho/ajax/alistapedidoatendertransferencia',
						 [
						 	'ordendespacho' 			=> $ordendespacho,
						 	'funcion' 					=> $funcion,
						 	'combo_almacen' 			=> $combo_almacen,
						 	'ultimo_almacen_id' 		=> $ultimo_almacen_id,
						 	'ajax'   		  			=> true,
						 	'combo_serie_guia'   		=> $combo_serie_guia,
						 ]);
	}





	public function actionAjaxModalAgregarProductosPedidoAtender(Request $request)
	{

		$data_producto 							= 	$request['data_producto'];
		$ordendespacho_id 						= 	$request['ordendespacho_id'];
		//$detalleordendespacho               	=   WEBDetalleOrdenDespacho::where('id','=',$ordendespacho_id)->first();

		$detalleodmobil 						=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
													->select(DB::raw('max(grupo_movil) as grupo_movil'))
													->groupBy('grupo_movil')
													->orderByRaw('max(grupo_movil) desc')
													->first();

		$mobil_mayor 							= 	$detalleodmobil->grupo_movil;

		foreach($data_producto as $obj){

		    $producto_id 						= 	$obj['producto_id'];
		    $cantidad_atender 					= 	$obj['cantidad_atender'];
		    $mobil_mayor 						= 	$mobil_mayor + 1;


		    $producto 							= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();

			$iddetalleordendespacho				= 	$this->funciones->getCreateIdMaestra('WEB.detalleordendespachos');
			$detalle            	 			=	new WEBDetalleOrdenDespacho;
			$detalle->id 	     	 			=  	$iddetalleordendespacho;
			$detalle->ordendespacho_id 			=  	$ordendespacho_id;
			$detalle->nro_orden_cen 			=  	'';
			$detalle->fecha_pedido 				=  	$this->fecha_sin_hora; 
			$detalle->fecha_entrega 			=  	$this->fecha_sin_hora;//fecha entrega falta
			$detalle->muestra 					=  	0.0000;
			$detalle->cantidad 					=  	0.0000;
			$detalle->cantidad_atender 			=  	$cantidad_atender;
			$detalle->modulo 					=  	'atender_pedido';
			$detalle->kilos 					=  	0.0000;
			$detalle->cantidad_sacos 			=  	0.0000;
			$detalle->palets 					=  	0.0000;
			$detalle->presentacion_producto 	=  	$producto->CAN_PESO_MATERIAL;
			$detalle->grupo 					=  	0;
			$detalle->grupo_orden 				=  	0;

			$detalle->grupo_movil 				=  	$mobil_mayor;
			$detalle->grupo_orden_movil 		=  	1;

			$detalle->nro_serie 				=  	'';
			$detalle->nro_documento 			=  	'';

			$detalle->grupo_guia 				=  	1;
			$detalle->grupo_orden_guia 			=  	1;


			$detalle->correlativo 				=  	$detalle->correlativo + 1;
			$detalle->tipo_grupo_oc 			=  	'';
			$detalle->fecha_crea 	 			=   $this->fechaactual;
			$detalle->usuario_crea 				=   Session::get('usuario')->id;
			$detalle->unidad_medida_id 			=  	$producto->COD_CATEGORIA_UNIDAD_MEDIDA;
			$detalle->cliente_id 				=  	'';
			$detalle->orden_id 					=  	'';
			$detalle->producto_id 				=  	$producto->COD_PRODUCTO;
			$detalle->empresa_id 				=   Session::get('empresas')->COD_EMPR;
			$detalle->centro_id 				=   Session::get('centros')->COD_CENTRO;
			$detalle->estado_id 	    		=  	'EPP0000000000002';	
			$detalle->centro_atender_id 		=  	'CEN0000000000001';
			$detalle->save();

		}

	    $ordendespacho 							=   WEBOrdenDespacho::where('id','=',$ordendespacho_id)->first();
		$funcion 								= 	$this;

		$combo_almacen 							= 	$this->funciones->combo_almacen(Session::get('centros')->COD_CENTRO,'TODOS');	
		$ultimo_almacen_id 						= 	$this->funciones->ultimo_almacen_id();
		$combo_serie_guia 						=   $this->funciones->combo_series('TDO0000000000009','0');

		return View::make('despacho/ajax/alistapedidoatendertransferencia',
						 [
						 	'ordendespacho' 			=> $ordendespacho,
						 	'funcion' 					=> $funcion,
						 	'combo_almacen' 			=> $combo_almacen,
						 	'ultimo_almacen_id' 		=> $ultimo_almacen_id,
						 	'ajax'   		  			=> true,
						 	'combo_serie_guia'   		=> $combo_serie_guia,
						 ]);

	}




	public function actionAjaxModalListaOrdenAtenderProducto(Request $request)
	{


		$ordendespacho_id 				= 	$request['ordendespacho_id'];
	    $ordendespacho 					=   WEBOrdenDespacho::where('id','=',$ordendespacho_id)->first();
	    $listaproductos 				= 	DB::table('WEB.LISTAPRODUCTOSAVENDER')
	    									->whereIn('COD_CATEGORIA_UNIDAD_MEDIDA',['UME0000000000001','UME0000000000013'])
				    					 	->orderBy('NOM_PRODUCTO', 'asc')
				    					 	->get();
		$funcion 						= 	$this;




		return View::make('despacho/modal/ajax/lproducto',
						 [
						 	'ordendespacho_id' 			=> $ordendespacho_id,
						 	'ordendespacho' 			=> $ordendespacho,
						 	'listaproductos' 			=> $listaproductos,
						 	'funcion' 					=> $funcion,

						 	'ajax' 						=> true,
						 ]);


	}


	public function actionAjaxAjaxModificarCantidadAtenderProducto(Request $request)
	{


		$detalle_orden_despacho_id 						= 	$request['detalle_orden_despacho_id'];
		$cantidad_atender_total							= 	(float)$request['catidad_atender'];
		$cantidad_atender 								= 	0.00;
		$ordendespacho_id 								= 	'';


		$array_detalle_orden_despacho_id 				= 	explode(",", $detalle_orden_despacho_id);
		foreach ($array_detalle_orden_despacho_id as $values)
		{

			$cantidad_atender							= 	$cantidad_atender_total/count($array_detalle_orden_despacho_id);

			$detalleordendespacho               		=   WEBDetalleOrdenDespacho::where('id','=',$values)->first();
			$detalleordendespacho->cantidad_atender 	=  	$cantidad_atender;
			$detalleordendespacho->fecha_mod 			=  	$this->fechaactual;
			$detalleordendespacho->usuario_mod 			=  	Session::get('usuario')->id;
			$detalleordendespacho->save();

			$ordendespacho_id 							= 	$detalleordendespacho->ordendespacho_id;

		}


	    $ordendespacho 									=   WEBOrdenDespacho::where('id','=',$ordendespacho_id)->first();
		$funcion 										= 	$this;
		$combo_almacen 									= 	$this->funciones->combo_almacen(Session::get('centros')->COD_CENTRO,'TODOS');	
		$ultimo_almacen_id 								= 	$this->funciones->ultimo_almacen_id();

		return View::make('despacho/ajax/acantidadatender',
						 [
						 	'ordendespacho' 			=> $ordendespacho,
						 	'funcion' 					=> $funcion,
						 	'combo_almacen' 			=> $combo_almacen,
						 	'ultimo_almacen_id' 		=> $ultimo_almacen_id,
						 	'catidad_atender'  		    => $cantidad_atender_total,
						 	'ajax'   		  			=> true,
						 ]);
	}







	public function actionAjaxListaAtenderPedidosDespacho(Request $request)
	{



		$fechainicio 					=  	$request['fechainicio'];
		$fechafin 						=  	$request['fechafin'];
		$idopcion 						=  	$request['opcion_id'];


	    $listaordenatender 				=   WEBOrdenDespacho::join('CMP.CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA','=','WEB.ordendespachos.estado_id')
	    									->where('fecha_orden','>=', $fechainicio)
	    									->where('fecha_orden','<=', $fechafin)
	    									->orderBy('fecha_crea', 'desc')
	    									->get();
		$funcion 						= 	$this;

		return View::make('despacho/ajax/alistarpedidoatender',
						 [
						 	'listaordenatender' 					=> $listaordenatender,
						 	'funcion' 								=> $funcion,
						 	'idopcion' 								=> $idopcion,

						 	'ajax' 									=> true,
						 ]);

	}





	public function actionListarAtenderPedido($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

		$fechainicio 					=  	$this->fecha_menos_quince;
		$fechafin 						=  	$this->fin;

		$sw_empresa_centro 				= 	$this->funciones->lista_pedidos_por_empresa_por_centro();

	    $listaordenatender 				=   WEBOrdenDespacho::join('CMP.CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA','=','WEB.ordendespachos.estado_id')
	    									->EmpresaCentro($sw_empresa_centro)
	    									->where('fecha_orden','>=', $fechainicio)
	    									->where('fecha_orden','<=', $fechafin)
	    									->orderBy('fecha_crea', 'desc')
	    									->get();

		$funcion 						= 	$this;


		return View::make('despacho/listarordenatender',
						 [
						 	'idopcion' 								=> $idopcion,
						 	'listaordenatender' 					=> $listaordenatender,
						 	'funcion' 								=> $funcion,
						 	'fechainicio' 							=> $fechainicio,
						 	'fechafin' 								=> $fechafin,
						 ]);
	}

}
