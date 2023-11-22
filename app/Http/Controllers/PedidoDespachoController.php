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
use App\WEBViewDetalleOrdenDespacho,App\ALMCentro;


class PedidoDespachoController extends Controller
{


	public function actionAjaxRechazarProductoGestion(Request $request)
	{


		$array_data_producto_despacho 				= 	$request['data_productos_rechazar'];
		$ordendespacho_id 							= 	$request['ordendespacho_id'];


		foreach($array_data_producto_despacho as $key => $obj){

			$detalle_orden_despacho_id				= 	$obj['data_detalle_orden_despacho'];
			$mobil_grupo							= 	$obj['mobil_grupo'];


			//actualizar fechas en detalle de pedido despacho
			$array_detalle_orden_despacho_id 		= 	explode(",", $detalle_orden_despacho_id);
			foreach ($array_detalle_orden_despacho_id as $values)
			{

				$detalle_orden_despacho 		    =   WEBDetalleOrdenDespacho::where('id','=',$values)->first();

				//cambiar el estado al detalle del pedido
				WEBDetalleOrdenDespacho::where('id','=',$values)
										->update([	'activo' 		=> '0',
													'estado_id' 	=> 'EPP0000000000005',
													'fecha_mod' 	=> $this->fechaactual,
													'usuario_mod' 	=> Session::get('usuario')->id
												 ]);

 				//disminuir grupo_orden_movil en 1
				$count_grupo_movil 					=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
														->where('activo','=','1')
														->where('grupo_movil','=',$mobil_grupo)
														->select(DB::raw('max(grupo_orden_movil) as grupo_orden_movil'))
														->groupBy('grupo_orden_movil')
														->first();

				if(count($count_grupo_movil)>0){

					WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
											->where('grupo_movil','=',$mobil_grupo)
											->where('activo','=','1')
											->update([	'grupo_orden_movil' 		=> $count_grupo_movil->grupo_orden_movil -1,
														'fecha_mod' 				=> $this->fechaactual,
														'usuario_mod' 				=> Session::get('usuario')->id
													 ]);

					//disminuir en uno grupo_orden_cen	en 1
					$count_grupo_cen 					=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
															->where('activo','=','1')
															->where('grupo_movil','=',$mobil_grupo)
															->where('grupo','=',$detalle_orden_despacho->grupo)
															->select(DB::raw('max(grupo_orden) as grupo_orden'))
															->groupBy('grupo_orden')
															->first();

					if(count($count_grupo_cen)>0){

						WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
												->where('grupo_movil','=',$mobil_grupo)
												->where('grupo','=',$detalle_orden_despacho->grupo)
												->where('activo','=','1')
												->update([	'grupo_orden' 				=> $count_grupo_cen->grupo_orden-1,
															'fecha_mod' 				=> $this->fechaactual,
															'usuario_mod' 				=> Session::get('usuario')->id
														 ]);
					}

					//Recalculcular guia 
					$this->funciones->recalcular_las_guias_remision($ordendespacho_id,$mobil_grupo);

				}
			}
		}	


	    $ordendespacho 								=   WEBOrdenDespacho::where('id','=',$ordendespacho_id)->first();
		$funcion 									= 	$this;



		return View::make('despacho/ajax/alistapedidogestion',
						 [
						 	'ordendespacho' 			=> $ordendespacho,
						 	'funcion' 					=> $funcion,
						 	'ajax'   		  			=> true,
						 ]);
	}

	public function actionAjaxModalAgregarProductosPedidoGestion(Request $request)
	{

		$data_producto 							= 	$request['data_producto'];
		$ordendespacho_id 						= 	$request['ordendespacho_id'];
		//$detalleordendespacho               	=   WEBDetalleOrdenDespacho::where('id','=',$ordendespacho_id)->first();
		$grupo_mobil_modal 						= 	$request['grupo_mobil_modal'];


		$detalleodmobil 						=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
													//->where('activo','=','1')
													->select(DB::raw('max(grupo_movil) as grupo_movil'))
													->groupBy('grupo_movil')
													->orderByRaw('max(grupo_movil) desc')
													->first();

		$centro 								=   ALMCentro::where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)->first();
		$empresa 								=   $this->funciones->data_empresa_despacho_por_centro(Session::get('centros')->COD_CENTRO);
		$cuenta_id_modal 						= 	$request['cuenta_id_modal'];
		$orden_cen_modal 						= 	$request['orden_cen_modal'];


		// PRODUCTO MOBIL NUEVO
		if($grupo_mobil_modal == "0" ){


			$mobil_mayor 							= 	$detalleodmobil->grupo_movil + 1;
			$fecha_pedido 							= 	$this->fecha_sin_hora;
			$fecha_entrega 							= 	$this->fecha_sin_hora;
			$centro_atender_id 						=  	$centro->COD_CENTRO;
			$centro_atender_txt 					=  	$centro->NOM_CENTRO;
			$empresa_atender_id 					=  	$empresa->COD_EMPR;
			$empresa_atender_txt 					=  	$empresa->NOM_EMPR;


			$grupo 									= 	0;
			$grupo_orden 							= 	1;
			$sw_oc_grupo 							= 	0;
			$sw_existe_cliente 						= 	0;
			$cliente_id 							= 	''; 

			//cliente
			$cliente 								=   '';
			$cuenta 								= 	WEBListaCliente::where('COD_CONTRATO','=',$cuenta_id_modal)->first();

			if(count($cuenta)>0){
				$cliente 							= 	WEBListaCliente::where('id','=',$cuenta->id)->first();
				$cliente_id 						=   $cliente->id;
				$sw_existe_cliente 					= 	1;	
			}else{
				if($cuenta_id_modal==''){
					$cliente_id  					=   '';
					$sw_existe_cliente 				= 	0;
				}else{
					$cliente_id  					=   $cuenta_id_modal;
					$sw_existe_cliente 				= 	1;
				}
			}


			if($sw_existe_cliente == 1){

				$cliente_id 						= 	$cliente_id;
				$orden_id 							= 	'';
				$tipo_grupo_oc 						= 	'oc_individual';
				$nro_orden_cen 						= 	'';

			}else{
				$cliente_id 						= 	$cliente_id;
				$orden_id 							= 	'';
				$tipo_grupo_oc 						= 	'oc_individual';
				$nro_orden_cen 						= 	'';
			}


		}else{


			$mobil_mayor 							= 	$grupo_mobil_modal;
			//PRODUCTO MOBIL SELECCIONADO

			$detalledespacho            	 		=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
														->where('activo','=','1')
														->where('grupo_movil','=',$mobil_mayor)->first();

			$count_grupo 							=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
														->where('activo','=','1')
														->where('grupo_movil','=',$mobil_mayor)
														->select(DB::raw('max(grupo) as grupo'))
														->groupBy('grupo')
														->orderByRaw('max(grupo) desc')
														->first();

			$fecha_pedido 							= 	date_format(date_create($detalledespacho->fecha_pedido), 'd-m-Y');
			$fecha_entrega 							= 	date_format(date_create($detalledespacho->fecha_entrega), 'd-m-Y');

			$centro_atender_id 						=  	$detalledespacho->centro_atender_id;
			$centro_atender_txt 					=  	$detalledespacho->centro_atender_txt;
			$empresa_atender_id 					=  	$detalledespacho->empresa_atender_id;
			$empresa_atender_txt 					=  	$detalledespacho->empresa_atender_txt;





			//cliente
			$cliente_despacho_id             		= 	'';
			$clientecombo 							=   '';
			$cuenta 								= 	WEBListaCliente::where('COD_CONTRATO','=',$cuenta_id_modal)->first();

			if(count($cuenta)>0){
				$clientecombo 						= 	WEBListaCliente::where('id','=',$cuenta->id)->first();		
			}else{

				if($cuenta_id_modal==''){
					$cliente_despacho_id  			=   '';
				}else{
					$cliente_despacho_id  			=   $cuenta_id_modal;
				}

			}

			if(isset($clientecombo->id)){
				$cliente_despacho_id 			   = 	$clientecombo->id;
			}

			//cliente
			$cliente            	 				=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
														->where('activo','=','1')
														->where('grupo_movil','=',$mobil_mayor)
														->where('cliente_id','=',$cliente_despacho_id)
														->where('nro_orden_cen','=',$orden_cen_modal)
														->first();

			if(count($cliente)>0 and $orden_cen_modal <> ''){

				$cliente_id 						= 	$cliente->cliente_id;
				$orden_id 							= 	$cliente->orden_id;
				$tipo_grupo_oc 						= 	$cliente->tipo_grupo_oc;
				$nro_orden_cen 						=   $cliente->nro_orden_cen;

				if($tipo_grupo_oc == 'oc_grupo'){

					$grupo 								= 	$cliente->grupo;
					$grupo_orden 						= 	$cliente->grupo_orden + count($data_producto);
					$sw_oc_grupo 						= 	1;

					WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
											->where('activo','=','1')
											->where('grupo_movil','=',$mobil_mayor)
											->where('cliente_id','=',$cliente_despacho_id)
											->where('nro_orden_cen','=',$orden_cen_modal)
											//->where('tipo_grupo_oc','=','oc_grupo')
											->update([	'grupo_orden' => $grupo_orden,
														'fecha_mod' 	=> $this->fechaactual,
														'usuario_mod' 	=> Session::get('usuario')->id
													 ]);
			
				}else{

					$grupo 								= 	$count_grupo->grupo;
					$grupo_orden 						= 	1;
					$sw_oc_grupo 						= 	0;
				}

			}else{

				if(isset($clientecombo->id)){
					$cliente_id 						= 	$clientecombo->id;
				}else{
					$cliente_id 						= 	'';
				}

				$orden_id 							= 	'';
				$tipo_grupo_oc 						= 	'oc_individual';
				$nro_orden_cen 						= 	'';
				$grupo 								= 	$count_grupo->grupo;
				$grupo_orden 						= 	1;
				$sw_oc_grupo 						= 	0;


			}


		}




		foreach($data_producto as $obj){

		    $producto_id 						= 	$obj['producto_id'];
		    $cantidad_atender 					= 	(float)$obj['cantidad_atender'];
		    $producto 							= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
			$kilos_atender 						=   $cantidad_atender*$producto->CAN_PESO_MATERIAL;
			$cantidad_sacos_atender				= 	$cantidad_atender/$producto->CAN_BOLSA_SACO;
			$palets_atende 						= 	$cantidad_sacos_atender/$producto->CAN_SACO_PALET;


			if($sw_oc_grupo == 0){
				$grupo                          =   $grupo+1;
			}
			
			$iddetalleordendespacho				= 	$this->funciones->getCreateIdMaestra('WEB.detalleordendespachos');
			$detalle            	 			=	new WEBDetalleOrdenDespacho;
			$detalle->id 	     	 			=  	$iddetalleordendespacho;
			$detalle->ordendespacho_id 			=  	$ordendespacho_id;
			$detalle->nro_orden_cen 			=  	$nro_orden_cen;
			$detalle->fecha_pedido 				=  	$fecha_pedido; 
			$detalle->fecha_entrega 			=  	$fecha_entrega;//fecha entrega falta
			$detalle->muestra 					=  	0.0000;
			$detalle->cantidad 					=  	$cantidad_atender;
			$detalle->cantidad_atender 			=  	$cantidad_atender;
			$detalle->modulo 					=  	'atender_pedido';
			$detalle->kilos 					=  	$kilos_atender;
			$detalle->cantidad_sacos 			=  	$cantidad_sacos_atender;
			$detalle->palets 					=  	$palets_atende;
			$detalle->presentacion_producto 	=  	$producto->CAN_PESO_MATERIAL;

			$detalle->grupo 					=  	$grupo;
			$detalle->grupo_orden 				=  	$grupo_orden;

			$detalle->grupo_movil 				=  	$mobil_mayor; 
			$detalle->grupo_orden_movil 		=  	1; 			  //recalcular cuendo es mobil existente
			$detalle->grupo_guia 				=  	1;            //recalcular cuendo es mobil existente
			$detalle->grupo_orden_guia 			=  	1;            //recalcular cuendo es mobil existente
			$detalle->correlativo 				=  	$detalle->correlativo + 1;
			$detalle->tipo_grupo_oc 			=  	$tipo_grupo_oc;
			$detalle->fecha_crea 	 			=   $this->fechaactual;
			$detalle->usuario_crea 				=   Session::get('usuario')->id;
			$detalle->unidad_medida_id 			=  	$producto->COD_CATEGORIA_UNIDAD_MEDIDA;
			$detalle->cliente_id 				=  	$cliente_id;
			$detalle->orden_id 					=  	$orden_id;
			$detalle->producto_id 				=  	$producto->COD_PRODUCTO;
			$detalle->empresa_id 				=   Session::get('empresas')->COD_EMPR;
			$detalle->centro_id 				=   Session::get('centros')->COD_CENTRO;
			$detalle->estado_id 	    		=  	'EPP0000000000002';
			$detalle->estado_gruia_id 	    	=  	'EPP0000000000002';
			$detalle->documento_guia_id 	    =  	'';
			$detalle->nro_serie 				=  	'';
			$detalle->nro_documento 			=  	'';

			$detalle->centro_atender_id 		=  	$centro_atender_id;
			$detalle->centro_atender_txt 		=  	$centro_atender_txt;
			$detalle->empresa_atender_id 		=  	$empresa_atender_id;
			$detalle->empresa_atender_txt 		=  	$empresa_atender_txt;


			$detalle->usuario_responsable_id 	=  	'';
			$detalle->usuario_responsable_txt 	=  	'';
			$detalle->kilos_atender 			=  	$kilos_atender;
			$detalle->cantidad_sacos_atender 	=  	$cantidad_sacos_atender;
			$detalle->palets_atender 			=  	$palets_atende;
			$detalle->fecha_carga 				=  	'';
			$detalle->fecha_recepcion 			=  	'';
			$detalle->save();

		}


			//Actualizar grupo mobil 
			$count_grupo_movil 					=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
													->where('activo','=','1')
													->where('grupo_movil','=',$mobil_mayor)
													->select(DB::raw('count(grupo_movil) as grupo_movil'))
													->groupBy('grupo_movil')
													->first();

			WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
									->where('activo','=','1')
									->where('grupo_movil','=',$mobil_mayor)
									->update([	'grupo_orden_movil' => $count_grupo_movil->grupo_movil,
												'fecha_mod' 	=> $this->fechaactual,
												'usuario_mod' 	=> Session::get('usuario')->id
											 ]);



		//Recalculcular guia 
		$this->funciones->recalcular_las_guias_remision($ordendespacho_id,$mobil_mayor);

	    $ordendespacho 							=   WEBOrdenDespacho::where('id','=',$ordendespacho_id)->first();
		$funcion 								= 	$this;


		return View::make('despacho/ajax/alistapedidogestion',
						 [
						 	'ordendespacho' 			=> $ordendespacho,
						 	'funcion' 					=> $funcion,
						 	'ajax'   		  			=> true,
						 ]);

	}


	public function actionAjaxModalListaOrdenGestionProducto(Request $request)
	{


		$ordendespacho_id 				= 	$request['ordendespacho_id'];
	    $ordendespacho 					=   WEBOrdenDespacho::where('id','=',$ordendespacho_id)->first();
	    $listaproductos 				= 	DB::table('WEB.LISTAPRODUCTOSAVENDER')
	    									->whereIn('COD_CATEGORIA_UNIDAD_MEDIDA',['UME0000000000001','UME0000000000013'])
				    					 	->orderBy('NOM_PRODUCTO', 'asc')
				    					 	->get();

		$array_grupo_mobil 				= 	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
											->where('activo','=','1')
											->select('grupo_movil')
											->select(DB::raw('grupo_movil 
															,max(orden_transferencia_id) orden_transferencia_id
															,max(nro_serie) nro_serie')
													)
											->groupBy('grupo_movil')
											->havingRaw("(max(orden_transferencia_id) is NULL or max(orden_transferencia_id) = '') 
														 and max(nro_serie) = '' and max(nro_documento) = ''")
											->pluck('grupo_movil','grupo_movil')
                                        	->toArray();
		    					 	
		$combo_grupo_mobil           	=   array('0' => "Nuevo Mobil") + $array_grupo_mobil;

		$funcion 						= 	$this;
		$comboclientes					= 	$this->funciones->combo_clientes_cuenta_lima();
		$comboclientes					= 	$comboclientes + $this->funciones->cliente_extras_web();

		$comboordencen 					= 	array('' => "Seleccione orden cen");


		return View::make('despacho/modal/ajax/lproducto',
						 [
						 	'ordendespacho_id' 			=> $ordendespacho_id,
						 	'ordendespacho' 			=> $ordendespacho,
						 	'listaproductos' 			=> $listaproductos,
						 	'combo_grupo_mobil' 		=> $combo_grupo_mobil,
						 	'comboclientes' 			=> $comboclientes,
						 	'comboordencen' 			=> $comboordencen,
						 	'funcion' 					=> $funcion,
						 	'ajax' 						=> true,
						 ]);


	}


	public function actionAjaxCrearMobil33Palets(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto'],true);
		$correlativo 						= 	$request['correlativo'];
		$grupo 								= 	$request['grupo'];
		$numero_mobil 						= 	$request['numero_mobil'];		
		$opcion_id 							= 	$request['opcion_id'];



		//dd($array_detalle_producto_request);
		//solo mobil seleccionada 33 PALETS
		$array_detalle_producto_33_palets 			=	array();
		$array_detalle_producto_resto_palets 		=	array();


		//MOBIL SELECCINADA
		$suma_palest 								=   0.0;
		foreach ($array_detalle_producto_request as $key => $item) {
			if((int)$item['grupo_movil'] == (int)$numero_mobil){
				array_push($array_detalle_producto_33_palets,$item);
				$suma_palest 			    =   $suma_palest + (float)$item["palets"];
			}else{
				array_push($array_detalle_producto_resto_palets,$item);
			}
		}



		$array_detalle_producto_33_palets   = 	$this->funciones->ordernar_array_despacho_33($array_detalle_producto_33_palets);

		//print_r($array_detalle_producto_33_palets);

		$array_detalle_producto 			=	array();
		$array_nuevo_producto 				=	array();
		$conteo_productos 					=   1;
		$grupo_mobil 						=   0;
		$cantidad_productos 				=   $suma_palest;  				   //cantidad de palets



		$parte_entera_division 				=   floor($cantidad_productos/33);
		$resto_division 					= 	$cantidad_productos%33;

		if($resto_division>0){
			$parte_entera_division 			= 	$parte_entera_division + 1;
		}


		$contador_de_palest 				=   0;
		$faltante_palets 					=   0;
		$paletsprimer_corte 				=   0;
		$paletssegundo_corte 				=   0;
		$correlativo 						=   1;
		$sumar_33_paltes  					=   0;
		$count_33_paltes  					=   0;

		//print_r($array_detalle_producto_33_palets);

		foreach ($array_detalle_producto_33_palets as $key => $item) {


			$cant_partes_cortar_producto 			=   0;
			$parte_entera_division_producto 		=  	0;
			$resto_division_producto 				= 	0;

			//$contador_de_palest 					=   $contador_de_palest%33;
			$palest 								= 	(float)$item['palets'];
			$faltante_palets 						=   33 - $contador_de_palest;
			$diferencia_palets_faltante 			= 	$palest - $faltante_palets;


			
			if($palest > $faltante_palets){
				$parte_entera_division_producto 	=   floor($palest/33);

				if($palest<33){
					$cant_partes_cortar_producto 	= 	$cant_partes_cortar_producto + 1;
				}

				$cant_partes_cortar_producto 		= 	$cant_partes_cortar_producto + $parte_entera_division_producto;
				$resto_division_producto 			= 	(float)($palest)%33;

				if($resto_division_producto>0){
					$cant_partes_cortar_producto 	= 	$cant_partes_cortar_producto + 1;
				}
				if($diferencia_palets_faltante>33){
					$cant_partes_cortar_producto 	= 	$cant_partes_cortar_producto + 1;
				}
				
				if($cant_partes_cortar_producto==4){
					$cant_partes_cortar_producto 	= 	3;
				}
			}else{
				$cant_partes_cortar_producto 		=   1;
			}


			/*print_r($palest.',,');
			print_r($faltante_palets.',');
			print($cant_partes_cortar_producto.',');
			print('-');*/

			$diferencia_palets_faltante 			= 	0;
			$contador_hacia_atras 					= 	$cant_partes_cortar_producto;
			$suma_palets_ultimo    					=	0;

			for ($i = 0; $i < $cant_partes_cortar_producto; $i++) {


				if($cant_partes_cortar_producto == 1){

					$faltante_palets 						=   $palest;
					$diferencia_palets_faltante 			= 	$palest - $faltante_palets;


				    $producto 								= 	ALMProducto::where('COD_PRODUCTO','=',$item['producto_id'])->first();
					$cantidad_sacos							= 	$faltante_palets*$producto->CAN_SACO_PALET;
					$cantidad_atender						= 	$cantidad_sacos*$producto->CAN_BOLSA_SACO;
					$kilos 									=   $cantidad_atender*$producto->CAN_PESO_MATERIAL;

					//primer corte
					$array_nuevo_producto 		=	array();

					$array_nuevo_producto		= 	
					$this->funciones->llenar_array_productos($item['empresa_cliente_id'],$item['empresa_cliente_nombre'],$item['orden_id'],$item['orden_cen'],
									$item['fecha_pedido'],
									$item['fecha_entrega'],$item['producto_id'],$item['nombre_producto'],$item['unidad_medida_id'],$item['nombre_unidad_medida'],
									$cantidad_atender,$kilos,$cantidad_sacos,$faltante_palets,$correlativo,
									'1',$grupo_mobil,$item['grupo_orden_movil'],$correlativo,'oc_individual',
									$item['presentacion_producto'],$item['centro_atender_id'],$item['centro_atender_txt'],$item['empresa_atender_id'],$item['empresa_atender_txt']);
					array_push($array_detalle_producto,$array_nuevo_producto);

					$sumar_33_paltes 			=   $sumar_33_paltes + $faltante_palets;
					$count_33_paltes 			=   $count_33_paltes + 1;

					if($sumar_33_paltes == 33){
						$array_detalle_producto		= 	$this->funciones->recalcular_grupo_orden_mobil_33_palets($array_detalle_producto,$count_33_paltes);
						$sumar_33_paltes 			=   0;
						$count_33_paltes 			=   0;

					}

					$contador_de_palest 		=   $contador_de_palest + $faltante_palets;
					$correlativo 				=   $correlativo + 1;


				}else{

					if($cant_partes_cortar_producto == 2){

						$faltante_palets 						=   33 - $contador_de_palest + $diferencia_palets_faltante;
						$diferencia_palets_faltante 			= 	$palest - $faltante_palets;

					    $producto 								= 	ALMProducto::where('COD_PRODUCTO','=',$item['producto_id'])->first();
						$cantidad_sacos							= 	$faltante_palets*$producto->CAN_SACO_PALET;
						$cantidad_atender						= 	$cantidad_sacos*$producto->CAN_BOLSA_SACO;
						$kilos 									=   $cantidad_atender*$producto->CAN_PESO_MATERIAL;

						//primer corte
						$array_nuevo_producto 		=	array();

						$array_nuevo_producto		= 	
						$this->funciones->llenar_array_productos($item['empresa_cliente_id'],$item['empresa_cliente_nombre'],$item['orden_id'],$item['orden_cen'],
										$item['fecha_pedido'],
										$item['fecha_entrega'],$item['producto_id'],$item['nombre_producto'],$item['unidad_medida_id'],$item['nombre_unidad_medida'],
										$cantidad_atender,$kilos,$cantidad_sacos,$faltante_palets,$correlativo,
										'1',$grupo_mobil,$item['grupo_orden_movil'],$correlativo,'oc_individual',
										$item['presentacion_producto'],$item['centro_atender_id'],$item['centro_atender_txt'],$item['empresa_atender_id'],$item['empresa_atender_txt']);
						array_push($array_detalle_producto,$array_nuevo_producto);

						$sumar_33_paltes 			=   $sumar_33_paltes + $faltante_palets;
						$count_33_paltes 			=   $count_33_paltes + 1;
						if($sumar_33_paltes == 33){
							$array_detalle_producto		= 	$this->funciones->recalcular_grupo_orden_mobil_33_palets($array_detalle_producto,$count_33_paltes);
							$sumar_33_paltes 			=   0;
							$count_33_paltes 			=   0;

						}


						$contador_de_palest 		=   $contador_de_palest + $faltante_palets;
						$correlativo 				=   $correlativo + 1;
						if($i == 1){
							$contador_de_palest     = 	$faltante_palets;
						}

					}else{


						


						if($i==0){
							$faltante_palets 					=   33 - $contador_de_palest + $diferencia_palets_faltante;
							$suma_palets_ultimo 					=   $suma_palets_ultimo+ $faltante_palets;
						}else{
							if($i == $cant_partes_cortar_producto - 1){

								$faltante_palets 					=   $palest - $suma_palets_ultimo;
								//print("entro".$suma_palets_ultimo.',');
							}else{
								$faltante_palets 					=   33;
								$suma_palets_ultimo 					=   $suma_palets_ultimo+ $faltante_palets;	
							}
						}

						
						$diferencia_palets_faltante 			= 	$palest - $faltante_palets;

						/*print_r($suma_palets_ultimo.',');
						print_r($palest.',');
						print_r($faltante_palets.',');
						print_r($diferencia_palets_faltante.',');
						print($cant_partes_cortar_producto.','.$i.'*');*/

					    $producto 								= 	ALMProducto::where('COD_PRODUCTO','=',$item['producto_id'])->first();
						$cantidad_sacos							= 	$faltante_palets*$producto->CAN_SACO_PALET;
						$cantidad_atender						= 	$cantidad_sacos*$producto->CAN_BOLSA_SACO;
						$kilos 									=   $cantidad_atender*$producto->CAN_PESO_MATERIAL;

						//primer corte
						$array_nuevo_producto 		=	array();

						$array_nuevo_producto		= 	
						$this->funciones->llenar_array_productos($item['empresa_cliente_id'],$item['empresa_cliente_nombre'],$item['orden_id'],$item['orden_cen'],
										$item['fecha_pedido'],
										$item['fecha_entrega'],$item['producto_id'],$item['nombre_producto'],$item['unidad_medida_id'],$item['nombre_unidad_medida'],
										$cantidad_atender,$kilos,$cantidad_sacos,$faltante_palets,$correlativo,
										'1',$grupo_mobil,$item['grupo_orden_movil'],$correlativo,'oc_individual',
										$item['presentacion_producto'],$item['centro_atender_id'],$item['centro_atender_txt'],$item['empresa_atender_id'],$item['empresa_atender_txt']);
						array_push($array_detalle_producto,$array_nuevo_producto);

						$sumar_33_paltes 			=   $sumar_33_paltes + $faltante_palets;
						$count_33_paltes 			=   $count_33_paltes + 1;

						if($sumar_33_paltes == 33){
							$array_detalle_producto		= 	$this->funciones->recalcular_grupo_orden_mobil_33_palets($array_detalle_producto,$count_33_paltes);
							$sumar_33_paltes 			=   0;
							$count_33_paltes 			=   0;

						}

						$contador_de_palest 		=   $contador_de_palest + $faltante_palets;
						$correlativo 				=   $correlativo + 1;
						$contador_hacia_atras 		= 	$contador_hacia_atras - 1;

						if($i == $cant_partes_cortar_producto - 1){
							$contador_de_palest     = 	$faltante_palets;
						}
					}
				}
			}
		}

		$array_detalle_producto										= 	$this->funciones->recalcular_grupo_orden_mobil_33_palets($array_detalle_producto,$count_33_paltes);


		$array_detalle_producto_resto_palets 						= 	$this->funciones->ordernar_array_despacho_restante($array_detalle_producto_resto_palets);
		$menor_mobil 												= 	$this->funciones->menor_grupo_mobil($array_detalle_producto_resto_palets);
		$numero_mobil 												= 	$this->funciones->mayor_grupo_mobil($array_detalle_producto);




		foreach ($array_detalle_producto_resto_palets as $key => $item) {

			if($menor_mobil == $item['grupo_movil']){
				$array_detalle_producto_resto_palets[$key]['grupo_movil'] = $numero_mobil + 1;
				$array_detalle_producto_resto_palets[$key]['correlativo'] = $correlativo;
			}else{
				$numero_mobil  = $numero_mobil + 1;
				$array_detalle_producto_resto_palets[$key]['grupo_movil'] = $numero_mobil + 1;
				$array_detalle_producto_resto_palets[$key]['correlativo'] = $correlativo;
				$menor_mobil   = $item['grupo_movil'];
			}
			$correlativo 				=   $correlativo + 1;
		}

		foreach ($array_detalle_producto_resto_palets as $key => $item) {
			array_push($array_detalle_producto,$item);
		}



		// ordenar el array por grupo
		$array_detalle_producto 									= 	$this->funciones->ordernar_array_despacho($array_detalle_producto);
		$numero_mobil 												= 	$this->funciones->mayor_grupo_mobil($array_detalle_producto);


		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);
		$funcion 					= 	$this;

		//crear tabla muestras
		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);



		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);

		
	}

	public function actionGestionOrdenDespacho($idopcion,$idordendespacho)
	{

		$idordendespacho_en 		= 	$idordendespacho;
		$idordendespacho 			= 	$this->funciones->decodificarmaestra($idordendespacho);
	    $ordendespacho 				=   WEBOrdenDespacho::where('id','=',$idordendespacho)->first();
		$funcion 					= 	$this;

		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);

		return View::make('despacho/gestionordendespacho',
						 [
						 	'ordendespacho' 						=> $ordendespacho,
						 	'funcion' 								=> $funcion,
						 	'idopcion' 								=> $idopcion,
						 	'idordendespacho' 						=> $idordendespacho,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
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


		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);



		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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
				$array_detalle_producto_muestra_request 	= 	json_decode($request['array_detalle_producto_muestra'],true);
				$ind_plantilla 	= 	$request['ind_plantilla'];

				//PEDIDO
				$cabecera            	 	=	new WEBOrdenDespacho;
				$cabecera->id 	     	 	=  	$idordendespacho;
				$cabecera->estado_id 	    =  	'EPP0000000000002';
				$cabecera->codigo 	    	=  	$codigo;
				$cabecera->fecha_crea 	 	=   $this->fechaactual;
				$cabecera->fecha_orden 	 	=   $this->fecha_sin_hora;
				$cabecera->ind_plantilla 	=   $ind_plantilla;
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

			    
			    //guardar lista de muestras
				foreach($array_detalle_producto_muestra_request as $key => $row) {

					//calculo de kilos,cantidad_sacos,palets
					$iddetalleordendespacho				= 	$this->funciones->getCreateIdMaestra('WEB.detalleordendespachos');
					$detalle            	 			=	new WEBDetalleOrdenDespacho;

					$detalle->id 	     	 			=  	$iddetalleordendespacho;
					$detalle->ordendespacho_id 			=  	$idordendespacho;
					$detalle->nro_orden_cen 			=  	'';
					$detalle->fecha_pedido 				=  	$this->fechaactual;
					$detalle->fecha_entrega 			=  	$this->fechaactual;
					$detalle->muestra 					=  	$row['muestra'];
					$detalle->cantidad 					=  	0;
					$detalle->cantidad_atender 			=  	0;
					$detalle->kilos 					=  	0;
					$detalle->cantidad_sacos 			=  	0;
					$detalle->palets 					=  	0;
					$detalle->presentacion_producto 	=  	$row['presentacion_producto'];
					$detalle->grupo 					=  	0;
					$detalle->grupo_orden 				=  	0;
					$detalle->grupo_movil 				=  	0;
					$detalle->grupo_orden_movil 		=  	0;
					$detalle->correlativo 				=  	$row['correlativo'];
					$detalle->tipo_grupo_oc 			=  	'muestras';
					$detalle->fecha_crea 	 			=   $this->fechaactual;
					$detalle->usuario_crea 				=   Session::get('usuario')->id;
					$detalle->unidad_medida_id 			=  	'';
					$detalle->modulo 					=  	'muestras';
					$detalle->cliente_id 				=  	'';
					$detalle->orden_id 					=  	'';
					$detalle->producto_id 				=  	$row['producto_id'];
					$detalle->empresa_id 				=   Session::get('empresas')->COD_EMPR;
					$detalle->centro_id 				=   Session::get('centros')->COD_CENTRO;
					$detalle->estado_id 	    		=  	'EPP0000000000002';
					$detalle->estado_gruia_id 	    	=  	'';
					$detalle->documento_guia_id 	    =  	'';
					$detalle->nro_serie 	    		=  	'';
					$detalle->nro_documento 	    	=  	'';
					$detalle->centro_atender_id 		=  	'';
					$detalle->centro_atender_txt 		=  	'';
					$detalle->empresa_atender_id 		=  	'';
					$detalle->empresa_atender_txt 		=  	'';
					$detalle->usuario_responsable_id 	=  	'';
					$detalle->usuario_responsable_txt 	=  	'';

					$detalle->kilos_atender 			=  	0;
					$detalle->cantidad_sacos_atender 	=  	0;
					$detalle->palets_atender 			=  	0;
					$detalle->fecha_carga 				=  	'';
					$detalle->fecha_recepcion 			=  	'';


					$detalle->save();
			    }

			    //agrupar cuantos mobiles hay
				$group_mobiles 							=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$idordendespacho)
															->where('activo','=','1')
															->where('tipo_grupo_oc','<>','muestras')
															->select(DB::raw('grupo_movil'))
															->groupBy('grupo_movil')
															->get();

				foreach($group_mobiles as $index => $item){

					$detalle_orden_despacho 			=	WEBViewDetalleOrdenDespacho::where('ordendespacho_id','=',$idordendespacho)
															->where('grupo_movil','=',$item->grupo_movil)
															->where('tipo_grupo_oc','<>','muestras')
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
			$comboclientes				= 	$comboclientes + $this->funciones->cliente_extras_web();


			$grupo						= 	0;
			$correlativo				= 	0;
			$numero_mobil				= 	0;

			$combo_lista_centros 		= 	$this->funciones->combo_lista_centro();
			$combo_con_sin_muestra 		= 	$this->funciones->combo_con_sin_muestra();

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
							 	'combo_con_sin_muestra' => $combo_con_sin_muestra,
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
		$producto->FEC_USUARIO_MODIF_AUD 	= 	$this->fechaactual;
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


		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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
		$fecha_i_t 							= 	$request['fecha_i_t'];


		if($fecha_i_t=='t'){

			//ACTUALIZR FECHA DE ENTREGA TOTAL
			//actualizar el array con nuevos valores(fecha de entrega) 
			foreach($array_detalle_producto_request as $key => $row) {
			    	$array_detalle_producto_request[$key]['fecha_entrega'] = $fechadeentrega;
		    } 

		}else{
			//ACTUALIZR FECHA DE ENTREGA POR MOBIL
			//actualizar el array con nuevos valores(fecha de entrega) 
			foreach($array_detalle_producto_request as $key => $row) {
				$encontro = array_search($row['correlativo'], array_column($data_producto_pedido, 'correlativo'));
			    if (!is_bool($encontro)){
			    	$array_detalle_producto_request[$key]['fecha_entrega'] = $fechadeentrega;
			    }
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

		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);

		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
						 	'grupo' 								=> $grupo,
						 	'numero_mobil' 							=> $numero_mobil,
						 	'correlativo' 							=> $correlativo,
						 	'funcion' 								=> $funcion,
						 	'opcion_id' 							=> $opcion_id,
						 	'combo_lista_centros' 					=> $combo_lista_centros,
						 	'ajax'   		  						=> true,
						 ]);
	}

	
	public function actionAjaxModificarMuestraProductoFilaSeparado(Request $request)
	{

		$array_detalle_producto_request 	= 	json_decode($request['array_detalle_producto_muestra'],true);
		$array_detalle_producto 			=	json_decode($request['array_detalle_producto'],true);
		$array_detalle_producto_muestra 	=	array();
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
			array_push($array_detalle_producto_muestra,$item);
		}

		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 		= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);
		$funcion 					= 	$this;


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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

		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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

		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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

		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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

		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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


		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);



		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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
		$nombre_cliente						= 	'';


		if(count($cliente)>0){
			$cliente_id 					= 	$cliente->id;
			$cliente_nombre 				= 	$cliente->NOM_EMPR;
		}else{

			$nombre_cliente 				=  	$this->funciones->nombre_cliente_despacho($cuenta_id_m);
			if($nombre_cliente <> ''){
				$cliente_id 					= 	$cuenta_id_m;
				$cliente_nombre 				= 	$nombre_cliente;
			}else{
				$cliente_id 					= 	"";
				$cliente_nombre 				= 	"";
			}
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


		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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
			$grupo_orden 					= 	'0';
			$grupo_movil 					= 	'0';
			$grupo_orden_movil 				= 	'0';

			if($tipo_grupo == 'oc_grupo'){	$grupo 	= 	$grupo + 1;}	
			$rowspan 						= 	0;
			$numero_mobil 					= 	$numero_mobil + 1;




			while($row = $lista_detalle_ordencen->fetch())
			{

				if($tipo_grupo == 'oc_individual'){	
					$grupo = $grupo + 1;
					$grupo_orden = '1'; 
					$grupo_movil = $numero_mobil; 
					$grupo_orden_movil = '0';
				}

				$unidad_medida 				= 	CMPCategoria::where('COD_CATEGORIA','=',$row['COD_CATEGORIA_UNIDAD_MEDIDA'])->first();
				$correlativo 				= 	$correlativo + 1;

				//calculo de kilos,cantidad_sacos,palets
				$producto 					= 	ALMProducto::where('COD_PRODUCTO','=',$row['COD_PRODUCTO'])->first();
				$kilos 						=   $row['CAN_PRODUCTO']*$producto->CAN_PESO_MATERIAL;
				$cantidad_sacos				= 	$row['CAN_PRODUCTO']/$producto->CAN_BOLSA_SACO;
				$palets 					= 	$cantidad_sacos/$producto->CAN_SACO_PALET;
				//
				//dd("hola3");


				$array_nuevo_producto		= 	

				$this->funciones->llenar_array_productos($orden->COD_EMPR_CLIENTE,$orden->TXT_EMPR_CLIENTE,$row['COD_TABLA'],$orden->NRO_ORDEN_CEN,$this->fin,
					$this->fin,$row['COD_PRODUCTO'],$row['TXT_NOMBRE_PRODUCTO'],$row['COD_CATEGORIA_UNIDAD_MEDIDA'],$unidad_medida->NOM_CATEGORIA,
					$row['CAN_PRODUCTO'],$kilos,$cantidad_sacos,$palets,$grupo,$grupo_orden,$grupo_movil,$grupo_orden_movil,$correlativo,$tipo_grupo,$producto->CAN_PESO_MATERIAL,
					'','','','');

				$rowspan 	= 	$rowspan + 1;
				array_push($array_detalle_producto,$array_nuevo_producto);

			}
			
			// modificar un valor en array
			if($tipo_grupo == 'oc_grupo'){
				$array_detalle_producto = $this->funciones->modificarmultidimensionalarray($array_detalle_producto,'grupo_orden',$rowspan,$orden->NRO_ORDEN_CEN);
				$array_detalle_producto = $this->funciones->agregar_mobil_producto($array_detalle_producto,$rowspan,$numero_mobil);
			}else{
				$array_detalle_producto = $this->funciones->agregar_cantidad_mobil_producto($array_detalle_producto,$rowspan,$grupo_movil);
			}

		}

		//dd($array_detalle_producto);


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



		//crear tabla muestras
		$array_detalle_producto_muestra = array();
		$array_detalle_producto_muestra  = 	$this->funciones->crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra);


		return View::make('despacho/ajax/alistapedido',
						 [
						 	'array_detalle_producto' 				=> $array_detalle_producto,
						 	'array_detalle_producto_muestra' 		=> $array_detalle_producto_muestra,
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

	    //dd($listaordendespacho);


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
		$idopcion 						=  	$request['idopcion'];


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
						 	'idopcion' 								=> $idopcion,
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
						 	'cliente_id' 				=> $cliente_id,

						 	'listaproductos' 			=> $listaproductos,
						 	'listaordencen' 			=> $listaordencen,
						 	'funcion' 					=> $funcion,
						 	'combotipogrupo' 			=> $combotipogrupo,
						 	'ajax' 						=> true,
						 ]);


	}




//actionAjaxModalAgregarProductosOrdenCen




}
