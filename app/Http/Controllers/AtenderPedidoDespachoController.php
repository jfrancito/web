<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

use View;
use Session;
use App\Biblioteca\OsirisDespacho;
use App\Biblioteca\Funcion;
use PDO;
use Mail;
use PDF;
use App\WEBOrdenDespacho,App\WEBDetalleOrdenDespacho,App\CMPOrden,App\WEBListaCliente,App\ALMProducto,App\CMPCategoria;
use App\STDEmpresa,App\ALMCentro,App\ALMAlmacen;
use App\WEBDespachoImprimir;
use App\CMPDetalleProducto;
use App\ALMProductiEquiv;
use App\WEBDespachoImprimirCantidad;


class AtenderPedidoDespachoController extends Controller
{

	public function actionAjaxQuitarAgregarPedidoProducto(Request $request)
	{

		$check 					=	$request['check'];
		$data_detalle_id 		=	$request['data_detalle_id'];
		$despacho_id 			=	$request['data_despacho_id'];
		$estado 				=	$request['estado'];

		$detalledespacho_sel 	=	WEBDetalleOrdenDespacho::where('id','=',$data_detalle_id)->first();
		$ordendespacho 			=	WEBOrdenDespacho::where('id','=',$despacho_id)->first();

		//dd($ordendespacho);

		if($data_detalle_id =='todo'){

			WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho->id)
			->where('tipo_grupo_oc','<>','muestras')
			->where('activo','=','1')
			->update(['ind_segmento' => $check]);

		}else{


			$detalledespacho 				=	WEBDetalleOrdenDespacho::where('id','=',$data_detalle_id)->first();
			$detalledespacho->ind_segmento 	= 	$check;
			$detalledespacho->save();
		}

		WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho->id)
		->update(['segmento_palets' => '']);


		$ind_todo 						=	1;
		$detalletodo 					=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho->id)
											->where('ind_segmento','=','0')
											->get();
		if(count($detalletodo)>0){
			$ind_todo 					=	0;
		}

		//agregar valores 
        $correlativo			=	0;
    	foreach($ordendespacho->viewdetalleordendespacho as $index => $item){

    		//$nroordecen = '';

    		if($item->ind_segmento==1){
    			$nroordecen		=	$item->nro_orden_cen;
	    		if($index == 0){
	    			$nroordecen		=	$item->nro_orden_cen;
	    		}else{
	    			if($nroordecen!=$item->nro_orden_cen){
						$correlativo =	0;  
						$nroordecen		=	$item->nro_orden_cen;			
	    			}
	    		}
	    		$segmento_palets = '';
	    		$primervalor = '';
	    		$ultimovalor = '';
	    		$countpalets =	$item->palets;	
				for ($i = 1; $i <= $countpalets; $i++) {
					$correlativo = $correlativo +1; 
					if($i == 1){
						$primervalor = 	str_pad($correlativo, 4, "0", STR_PAD_LEFT); 
					}
					if($i == $countpalets){
						$ultimovalor = 	str_pad($correlativo, 4, "0", STR_PAD_LEFT);
					}
				}
				
				if($primervalor == $ultimovalor){
					$segmento_palets = $primervalor;
				}else{
					$segmento_palets = $primervalor.' - '.$ultimovalor;
				}

				$detdespacho = WEBDetalleOrdenDespacho::where('id','=',$item->id_detalle)->first();
				$detdespacho->segmento_palets = $segmento_palets;
				$detdespacho->save();
	    		//dd($detdespacho);
    		}





    	}

        $listaxcantidad			=	WEBDespachoImprimir::orderby('item','asc')->get();
        $listaxpalest			=	WEBDespachoImprimir::orderby('item','asc')->get();
		$ordendespacho 			=	WEBOrdenDespacho::where('id','=',$despacho_id)->first();
        $array_detalle_palets 	= 	array();
        $array_detalle_cantidad = 	array();

		$funcion 				= 	$this;

		return View::make('despacho/ajax/alistadetallepedido',
						 [
						 	'ordendespacho' 			=> $ordendespacho,
						 	'funcion' 					=> $funcion,
						 	'listaxcantidad' 			=> $listaxcantidad,
						 	'listaxpalest' 				=> $listaxpalest,
						 	'array_detalle_palets' 		=> $array_detalle_palets,
						 	'array_detalle_cantidad' 	=> $array_detalle_cantidad,
						 	'ind_todo' 					=> $ind_todo,
						 	'ajax' 						=> true,
						 ]);
	}
	public function actionModalDetalleImprimir(Request $request)
	{

		$pedido_id 				=	$request['pedido_id'];
		$ordendespacho 			=	WEBOrdenDespacho::where('id','=',$pedido_id)->first();
		$detalledespacho 		=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$pedido_id)->first();
		$funcion 				= 	$this;
		$array_centro_id 		=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 	= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);

		WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$pedido_id)
		->update(['ind_segmento' => '1']);
		$ind_todo 				=	1;

		//agregar valores 
        $correlativo			=	0;
    	foreach($ordendespacho->viewdetalleordendespacho as $index => $item){



    		if($index == 0){
    			$nroordecen		=	$item->nro_orden_cen;
    		}else{
    			if($nroordecen!=$item->nro_orden_cen){
					$correlativo =	0;  
					$nroordecen		=	$item->nro_orden_cen;			
    			}
    		}
    		$segmento_palets = '';
    		$primervalor = '';
    		$ultimovalor = '';
    		$countpalets =	$item->palets;	
			for ($i = 1; $i <= $countpalets; $i++) {
				$correlativo = $correlativo +1; 
				if($i == 1){
					$primervalor = 	str_pad($correlativo, 4, "0", STR_PAD_LEFT); 
				}
				if($i == $countpalets){
					$ultimovalor = 	str_pad($correlativo, 4, "0", STR_PAD_LEFT);
				}
			}
			
			if($primervalor == $ultimovalor){
				$segmento_palets = $primervalor;
			}else{
				$segmento_palets = $primervalor.' - '.$ultimovalor;
			}

			$detdespacho = WEBDetalleOrdenDespacho::where('id','=',$item->id_detalle)->first();
			$detdespacho->segmento_palets = $segmento_palets;
			$detdespacho->save();
    		//dd($detdespacho);
    	}

        $listaxcantidad			=	WEBDespachoImprimir::orderby('item','asc')->get();
        $listaxpalest			=	WEBDespachoImprimir::orderby('item','asc')->get();
		$ordendespacho 			=	WEBOrdenDespacho::where('id','=',$pedido_id)->first();
        $array_detalle_palets 	= 	array();
        $array_detalle_cantidad = 	array();

		return View::make('despacho/modal/ajax/mdetallepedido',
						 [
						 	'ordendespacho' 			=> $ordendespacho,
						 	'detalledespacho' 			=> $detalledespacho,
						 	'funcion' 					=> $funcion,
						 	'array_centro_id' 			=> $array_centro_id,
						 	'combo_lista_centros' 		=> $combo_lista_centros,
						 	'listaxcantidad' 			=> $listaxcantidad,
						 	'listaxpalest' 				=> $listaxpalest,
						 	'array_detalle_palets' 		=> $array_detalle_palets,
						 	'array_detalle_cantidad' 	=> $array_detalle_cantidad,
						 	'ind_todo' 					=> $ind_todo,

						 	'ajax' 						=> true,
						 ]);
	}

	public function actionAjaxImprimirPedidoDespachoxPalets(Request $request)
	{

        $pedido_id 							= 	$request['pedido_id'];
        $correlativo						=	0;
        $nroordecen							=	'';
		$ordendespacho 						=	WEBOrdenDespacho::where('id','=',$pedido_id)->first();
    	$correlativo						=	0;
        $despachoc 							=	WEBOrdenDespacho::where('id','=',$pedido_id)->first();
        	//eliminar si existe
        WEBDespachoImprimir::where('activo',1)->delete();
        $array_detalle_palets = array();
		foreach($ordendespacho->viewdetalleordendespacho as $index => $detitem){


			if($detitem->ind_segmento == 1){

				$equivalente 			=	ALMProductiEquiv::where('COD_PRODUCTO','=',$detitem->producto_id)
	        								->where('COD_EMPR_CLIENTE','=',$detitem->cliente_id)->first();

	        	$ean13 					=	trim($equivalente->COD_EAN);
	          	$ean14 					=	trim($equivalente->EAN14);
	          	$sku 					=	trim($equivalente->COD_SKU);

	          	if(($ean13=='' or $ean14 =='' or $sku =='') and $equivalente->ESGRANEL != 1){
		            $array_nuevo_asiento = array();
		            $array_nuevo_asiento = array(
		                "nombre_producto" => $detitem->producto->NOM_PRODUCTO,
		                "ean13" => $ean13,
		                "ean14" => $ean14,
		                "sku" 	=> $sku
		            );
		            array_push($array_detalle_palets, $array_nuevo_asiento);
	          	}


	          	if(($ean13=='' or $sku =='') and $equivalente->ESGRANEL == 1){
		            $array_nuevo_asiento = array();
		            $array_nuevo_asiento = array(
		                "nombre_producto" => $detitem->producto->NOM_PRODUCTO,
		                "ean13" => $ean13,
		                "ean14" => $ean14,
		                "sku" 	=> $sku
		            );
		            array_push($array_detalle_palets, $array_nuevo_asiento);
	          	}

			}

		}

		if(count($array_detalle_palets)<=0){
	        $count = 0;
			foreach($ordendespacho->viewdetalleordendespacho as $index => $detitem){

				if($detitem->ind_segmento == 1){

					$equivalente 			=	ALMProductiEquiv::where('COD_PRODUCTO','=',$detitem->producto_id)
		        								->where('COD_EMPR_CLIENTE','=',$detitem->cliente_id)->first();

		        	$ean13 					=	trim($equivalente->COD_EAN);
		          	$ean14 					=	trim($equivalente->EAN14);
		          	$sku 					=	trim($equivalente->COD_SKU);

	        		if($index == 0){
	        			$nroordecen		=	$detitem->nro_orden_cen;
	        		}else{
	        			if($nroordecen!=$detitem->nro_orden_cen){
							$correlativo =	0;
							$nroordecen		=	$detitem->nro_orden_cen; 			
	        			}
	        		}

	        		$equivalente 							=	ALMProductiEquiv::where('COD_PRODUCTO','=',$detitem->producto_id)
	        													->where('COD_EMPR_CLIENTE','=',$detitem->cliente_id)->first();

	        		$producto 								=	ALMProducto::where('COD_PRODUCTO','=',$detitem->producto_id)->first();
	        		$detalleproducto 						=	CMPDetalleProducto::where('COD_TABLA','=',$detitem->orden_id)
	        													->where('COD_PRODUCTO','=',$detitem->producto_id)
	        													->first();
	        		$countpalets							=	$detitem->palets;
	        		
					for ($i = 1; $i <= $countpalets; $i++) {

						$correlativo							=	$correlativo +1; 
						$clpn 									= 	str_pad($correlativo, 4, "0", STR_PAD_LEFT); 

					    $lpn 									=	'500000'.str_replace( ",", '', $detitem->nro_orden_cen).$clpn;

			        	$despacho 								=	new WEBDespachoImprimir;
			        	$despacho->id		 					=	$detitem->id;
			        	$despacho->codigo		 				=	$despachoc->codigo;
			        	$despacho->item		 					=	$count;
			        	$despacho->nro_orden_cen		 		=	str_replace( ",", '', $detitem->nro_orden_cen);
			        	$despacho->fecha_pedido		 			=	$detitem->fecha_pedido;
			        	$despacho->fecha_entrega		 		=	$detitem->fecha_entrega;
			        	$despacho->muestra		 				=	$detitem->muestra;

			        	$despacho->cantidad		 				=	$detitem->cantidad;
			        	$despacho->cantidad_atender		 		=	$detitem->cantidad_atender;
			        	$despacho->centro_atender_id		 	=	$detitem->centro_atender_id;
			        	$despacho->centro_atender_txt		 	=	$detitem->centro_atender_txt;
			        	$despacho->empresa_atender_id		 	=	$detitem->empresa_atender_id;

			         	$despacho->empresa_atender_txt		 	=	$detitem->empresa_atender_txt;
			        	$despacho->usuario_responsable_id		=	$detitem->usuario_responsable_id;
			        	$despacho->usuario_responsable_txt		=	$detitem->usuario_responsable_txt;
			        	$despacho->estado_id		 			=	$detitem->estado_id;
			        	$despacho->estado_gruia_id		 		=	$detitem->estado_gruia_id;

			         	$despacho->documento_guia_id		 	=	$detitem->documento_guia_id;
			        	$despacho->kilos		 				=	$detitem->kilos;
			        	$despacho->cantidad_sacos		 		=	$detitem->cantidad_sacos;
			        	$despacho->palets		 				=	$detitem->palets;
			        	$despacho->kilos_atender		 		=	$detitem->kilos_atender;
			        	
			         	$despacho->cantidad_sacos_atender		=	$detitem->cantidad_sacos_atender;
			        	$despacho->palets_atender		 		=	$detitem->palets_atender;
			        	$despacho->fecha_carga		 			=	$detitem->fecha_carga;
			        	$despacho->fecha_recepcion		 		=	$detitem->fecha_recepcion;
			        	$despacho->presentacion_producto		=	$detitem->presentacion_producto;
			        	
			         	$despacho->grupo		 				=	$detitem->grupo;
			        	$despacho->grupo_orden		 			=	$detitem->grupo_orden;
			        	$despacho->grupo_movil		 			=	$detitem->grupo_movil;
			        	$despacho->grupo_orden_movil		 	=	$detitem->grupo_orden_movil;
			        	$despacho->nro_serie		 			=	$detitem->nro_serie;
			        	
			         	$despacho->nro_documento		 		=	$detitem->nro_documento;
			        	$despacho->grupo_guia		 			=	$detitem->grupo_guia;
			        	$despacho->grupo_orden_guia		 		=	$detitem->grupo_orden_guia;
			        	$despacho->correlativo		 			=	$detitem->correlativo;
			        	$despacho->tipo_grupo_oc		 		=	$detitem->tipo_grupo_oc;
			        	
			        	$despacho->usuario_crea		 			=	$detitem->usuario_crea;
			        	$despacho->unidad_medida_id		 		=	$detitem->unidad_medida_id;
			        	$despacho->modulo		 				=	$detitem->modulo;
			        	
			         	$despacho->usuario_mod		 			=	$detitem->usuario_mod;
			        	$despacho->activo		 				=	1;
			        	$despacho->ordendespacho_id		 		=	$detitem->ordendespacho_id;
			        	$despacho->cliente_id		 			=	$detitem->cliente_id;
			        	$despacho->orden_id		 				=	$detitem->orden_id;
			        	
			         	$despacho->orden_transferencia_id		=	$detitem->orden_transferencia_id;
			        	$despacho->producto_id		 			=	$detitem->producto_id;
			        	$despacho->producto_nombre		 		=	$detitem->producto->NOM_PRODUCTO;

			        	$despacho->empresa_id		 			=	$detitem->empresa_id;
			        	$despacho->centro_id		 			=	$detitem->centro_id;

			        	$despacho->empaquetexpallet		 		=	$producto->CAN_SACO_PALET;
			         	$despacho->skuxpallet		 			=	$producto->CAN_BOLSA_SACO;
			        	$despacho->blsscxpallet		 			=	$producto->CAN_SACO_PALET * $producto->CAN_BOLSA_SACO;
			        	$despacho->sku		 					=	trim($equivalente->COD_SKU);
			        	$despacho->costosku		 				=	$detalleproducto->CAN_PRECIO_UNIT;
			        	$despacho->ean13		 				=	trim($equivalente->COD_EAN);
			         	$despacho->ean14		 				=	trim($equivalente->EAN14);
			        	$despacho->lpn		 					=	$lpn;
		         		$despacho->ind_ean13		 			=	$equivalente->ESGRANEL;

			        	$despacho->save();


	        			$count = $count+1;

			        	$despacho 								=	new WEBDespachoImprimir;
			        	$despacho->id		 					=	$detitem->id;
			        	$despacho->codigo		 				=	$despachoc->codigo;
			        	$despacho->item		 					=	$count;
			        	$despacho->nro_orden_cen		 		=	str_replace( ",", '', $detitem->nro_orden_cen);
			        	$despacho->fecha_pedido		 			=	$detitem->fecha_pedido;
			        	$despacho->fecha_entrega		 		=	$detitem->fecha_entrega;
			        	$despacho->muestra		 				=	$detitem->muestra;

			        	$despacho->cantidad		 				=	$detitem->cantidad;
			        	$despacho->cantidad_atender		 		=	$detitem->cantidad_atender;
			        	$despacho->centro_atender_id		 	=	$detitem->centro_atender_id;
			        	$despacho->centro_atender_txt		 	=	$detitem->centro_atender_txt;
			        	$despacho->empresa_atender_id		 	=	$detitem->empresa_atender_id;

			         	$despacho->empresa_atender_txt		 	=	$detitem->empresa_atender_txt;
			        	$despacho->usuario_responsable_id		=	$detitem->usuario_responsable_id;
			        	$despacho->usuario_responsable_txt		=	$detitem->usuario_responsable_txt;
			        	$despacho->estado_id		 			=	$detitem->estado_id;
			        	$despacho->estado_gruia_id		 		=	$detitem->estado_gruia_id;

			         	$despacho->documento_guia_id		 	=	$detitem->documento_guia_id;
			        	$despacho->kilos		 				=	$detitem->kilos;
			        	$despacho->cantidad_sacos		 		=	$detitem->cantidad_sacos;
			        	$despacho->palets		 				=	$detitem->palets;
			        	$despacho->kilos_atender		 		=	$detitem->kilos_atender;
			        	
			         	$despacho->cantidad_sacos_atender		=	$detitem->cantidad_sacos_atender;
			        	$despacho->palets_atender		 		=	$detitem->palets_atender;
			        	$despacho->fecha_carga		 			=	$detitem->fecha_carga;
			        	$despacho->fecha_recepcion		 		=	$detitem->fecha_recepcion;
			        	$despacho->presentacion_producto		=	$detitem->presentacion_producto;
			        	
			         	$despacho->grupo		 				=	$detitem->grupo;
			        	$despacho->grupo_orden		 			=	$detitem->grupo_orden;
			        	$despacho->grupo_movil		 			=	$detitem->grupo_movil;
			        	$despacho->grupo_orden_movil		 	=	$detitem->grupo_orden_movil;
			        	$despacho->nro_serie		 			=	$detitem->nro_serie;
			        	
			         	$despacho->nro_documento		 		=	$detitem->nro_documento;
			        	$despacho->grupo_guia		 			=	$detitem->grupo_guia;
			        	$despacho->grupo_orden_guia		 		=	$detitem->grupo_orden_guia;
			        	$despacho->correlativo		 			=	$detitem->correlativo;
			        	$despacho->tipo_grupo_oc		 		=	$detitem->tipo_grupo_oc;
			        	
			        	$despacho->usuario_crea		 			=	$detitem->usuario_crea;
			        	$despacho->unidad_medida_id		 		=	$detitem->unidad_medida_id;
			        	$despacho->modulo		 				=	$detitem->modulo;
			        	
			         	$despacho->usuario_mod		 			=	$detitem->usuario_mod;
			        	$despacho->activo		 				=	1;
			        	$despacho->ordendespacho_id		 		=	$detitem->ordendespacho_id;
			        	$despacho->cliente_id		 			=	$detitem->cliente_id;
			        	$despacho->orden_id		 				=	$detitem->orden_id;
			        	
			         	$despacho->orden_transferencia_id		=	$detitem->orden_transferencia_id;
			        	$despacho->producto_id		 			=	$detitem->producto_id;
			        	$despacho->producto_nombre		 		=	$detitem->producto->NOM_PRODUCTO;

			        	$despacho->empresa_id		 			=	$detitem->empresa_id;
			        	$despacho->centro_id		 			=	$detitem->centro_id;

			        	$despacho->empaquetexpallet		 		=	$producto->CAN_SACO_PALET;
			         	$despacho->skuxpallet		 			=	$producto->CAN_BOLSA_SACO;
			        	$despacho->blsscxpallet		 			=	$producto->CAN_SACO_PALET * $producto->CAN_BOLSA_SACO;
			        	$despacho->sku		 					=	trim($equivalente->COD_SKU);
			        	$despacho->costosku		 				=	$detalleproducto->CAN_PRECIO_UNIT;
			        	$despacho->ean13		 				=	trim($equivalente->COD_EAN);
			         	$despacho->ean14		 				=	trim($equivalente->EAN14);
			        	$despacho->lpn		 					=	$lpn;
		         		$despacho->ind_ean13		 			=	$equivalente->ESGRANEL;

			        	$despacho->save();

	        			$count = $count+1;

					}

				}

	        }
		}

		$tipooperacion				=		'PALLETS';
        $stmt2 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.DESPACHO_BARCODE 
											@tipooperacion = ?');
        $stmt2->bindParam(1, $tipooperacion ,PDO::PARAM_STR);                   
        $stmt2->execute();	


        $listaxpalest		=	WEBDespachoImprimir::orderby('item','asc')->get();
		return View::make('despacho/ajax/listaxpalets',
						 [
						 	'listaxpalest' 			=> $listaxpalest,
						 	'array_detalle_palets' => $array_detalle_palets,
						 ]);


	}


	public function actionAjaxImprimirPedidoDespachoxCantidad(Request $request)
	{

        $pedido_id 							= 	$request['pedido_id'];
        $correlativo						=	0;
        $nroordecen							=	'';
		$ordendespacho 						=	WEBOrdenDespacho::where('id','=',$pedido_id)->first();

    	//eliminar si existe
    	WEBDespachoImprimirCantidad::where('activo','1')->delete();
        $array_detalle_cantidad = array();
		foreach($ordendespacho->viewdetalleordendespacho as $index => $detitem){

			if($detitem->ind_segmento == 1){

				$equivalente 			=	ALMProductiEquiv::where('COD_PRODUCTO','=',$detitem->producto_id)
	        								->where('COD_EMPR_CLIENTE','=',$detitem->cliente_id)->first();
	          	$ean14 					=	$equivalente->EAN14;
	          	$ean13 					=	$equivalente->COD_EAN;

	          	if($ean14 == '' and $equivalente->ESGRANEL != 1){
		            $array_nuevo_asiento = array();
		            $array_nuevo_asiento = array(
		                "nombre_producto" => $detitem->producto->NOM_PRODUCTO,
		                "ean14" => $ean14,
		                "ean13" => $ean13,
		            );
		            array_push($array_detalle_cantidad, $array_nuevo_asiento);
	          	}

	          	if($ean13 == '' and $equivalente->ESGRANEL == 1){
		            $array_nuevo_asiento = array();
		            $array_nuevo_asiento = array(
		                "nombre_producto" => $detitem->producto->NOM_PRODUCTO,
		                "ean14" => $ean14,
		                "ean13" => $ean13,
		            );
		            array_push($array_detalle_cantidad, $array_nuevo_asiento);
	          	}
			}
		}



    	$correlativo		= 0;
        $count 				= 0;

		if(count($array_detalle_cantidad)<=0){

			foreach($ordendespacho->viewdetalleordendespacho as $index => $detitem){

				if($detitem->ind_segmento == 1){

					$equivalente 			=	ALMProductiEquiv::where('COD_PRODUCTO','=',$detitem->producto_id)
		        								->where('COD_EMPR_CLIENTE','=',$detitem->cliente_id)->first();
		          	$ean14 					=	$equivalente->EAN14;
		    		$countpalets			=	$detitem->cantidad_sacos;

	          		if($equivalente->ESGRANEL == 1){
	          			$ean14 					=	$equivalente->COD_EAN;
	          		}
					for ($i = 1; $i <= $countpalets; $i++) {

			        	$despacho 								=	new WEBDespachoImprimirCantidad;
			        	$despacho->id		 					=	$detitem->id_detalle;
			        	$despacho->item		 					=	$count;
			        	$despacho->nro_orden_cen		 		=	str_replace ( ",", '', $detitem->nro_orden_cen);
			        	$despacho->producto_id		 			=	$detitem->producto_id;
			        	$despacho->producto_nombre		 		=	$detitem->producto->NOM_PRODUCTO;
			         	$despacho->ean14		 				=	$ean14;
			         	$despacho->ind_ean13		 			=	$equivalente->ESGRANEL;
			         	$despacho->activo		 				=	'1';
			        	$despacho->save();
		        		$count = $count+1;
					}

				}





	    	}
		}

		$tipooperacion				=		'SACOS';
        $stmt2 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.DESPACHO_BARCODE 
											@tipooperacion = ?');
        $stmt2->bindParam(1, $tipooperacion ,PDO::PARAM_STR);                   
        $stmt2->execute();	

        $listaxcantidad		=	WEBDespachoImprimirCantidad::orderby('item','asc')->get();
		return View::make('despacho/ajax/listaxcantidad',
						 [
						 	'listaxcantidad' 			=> $listaxcantidad,
						 	'array_detalle_cantidad' 	=> $array_detalle_cantidad,
						 ]);

      
	}










	public function actionLimpiarImpresion(Request $request)
	{

		 WEBDespachoImprimir::where('activo',1)->delete();

	}

	public function actionModalImprimirPedidoDespacho(Request $request)
	{

		$imprimir 			=	WEBDespachoImprimir::first();

		$codigo 			=	'';

		if(count($imprimir)>0){
			$codigo 			=	$imprimir->codigo;
		}

		$listaimprimir 		=	WEBDespachoImprimir::join('ALM.PRODUCTO','ALM.PRODUCTO.COD_PRODUCTO','=','WEB.despachoimprimir.producto_id')
								->join('STD.EMPRESA','STD.EMPRESA.COD_EMPR','=','WEB.despachoimprimir.cliente_id')
								->orderby('WEB.despachoimprimir.nro_orden_cen','asc')
								->get();


		return View::make('despacho/modal/ajax/mdetalleimprimir',
						 [
						 	'imprimir' 					=> $imprimir,
						 	'listaimprimir' 			=> $listaimprimir,
						 	'codigo' 					=> $codigo,
						 	'ajax' 						=> true,
						 ]);


	}



	public function actionAjaxImprimirPedidoDespacho(Request $request)
	{

        $detallepedido 							= 	json_decode($request['datastring'], false);
        $correlativo							=	0;
        $nroordecen								=	'';


        foreach ($detallepedido as $index => $item) {

      		$detelledespacho 						=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$item->pedido_id)
      													->where('modulo','<>','muestras')
      													->orderBy('nro_orden_cen','asc')
      													->get();
        	$despachoc 								=	WEBOrdenDespacho::where('id','=',$item->pedido_id)->first();
        	$correlativo							=	0;
        	//eliminar si existe
        	WEBDespachoImprimir::where('codigo',$despachoc->codigo)->delete();

        	foreach ($detelledespacho as $index => $detitem) {

        		if($index == 0){
        			$nroordecen		=	$detitem->nro_orden_cen;
        		}else{
        			if($nroordecen!=$detitem->nro_orden_cen){
						$correlativo =	0;  			
        			}
        		}

        		$equivalente 							=	ALMProductiEquiv::where('COD_PRODUCTO','=',$detitem->producto_id)
        													->where('COD_EMPR_CLIENTE','=',$detitem->cliente_id)->first();

        		$producto 								=	ALMProducto::where('COD_PRODUCTO','=',$detitem->producto_id)->first();
        		$detalleproducto 						=	CMPDetalleProducto::where('COD_TABLA','=',$detitem->orden_id)
        													->where('COD_PRODUCTO','=',$detitem->producto_id)
        													->first();
        		$countpalets							=	$detitem->palets;
        		
				for ($i = 1; $i <= $countpalets; $i++) {

					$correlativo							=	$correlativo +1; 
					$clpn 									= 	str_pad($correlativo, 4, "0", STR_PAD_LEFT); 

				    $lpn 									=	'500000'.$detitem->nro_orden_cen.$clpn;

		        	$despacho 								=	new WEBDespachoImprimir;
		        	$despacho->id		 					=	$detitem->id;
		        	$despacho->codigo		 				=	$despachoc->codigo;

		        	$despacho->nro_orden_cen		 		=	$detitem->nro_orden_cen;
		        	$despacho->fecha_pedido		 			=	$detitem->fecha_pedido;
		        	$despacho->fecha_entrega		 		=	$detitem->fecha_entrega;
		        	$despacho->muestra		 				=	$detitem->muestra;

		        	$despacho->cantidad		 				=	$detitem->cantidad;
		        	$despacho->cantidad_atender		 		=	$detitem->cantidad_atender;
		        	$despacho->centro_atender_id		 	=	$detitem->centro_atender_id;
		        	$despacho->centro_atender_txt		 	=	$detitem->centro_atender_txt;
		        	$despacho->empresa_atender_id		 	=	$detitem->empresa_atender_id;

		         	$despacho->empresa_atender_txt		 	=	$detitem->empresa_atender_txt;
		        	$despacho->usuario_responsable_id		=	$detitem->usuario_responsable_id;
		        	$despacho->usuario_responsable_txt		=	$detitem->usuario_responsable_txt;
		        	$despacho->estado_id		 			=	$detitem->estado_id;
		        	$despacho->estado_gruia_id		 		=	$detitem->estado_gruia_id;

		         	$despacho->documento_guia_id		 	=	$detitem->documento_guia_id;
		        	$despacho->kilos		 				=	$detitem->kilos;
		        	$despacho->cantidad_sacos		 		=	$detitem->cantidad_sacos;
		        	$despacho->palets		 				=	$detitem->palets;
		        	$despacho->kilos_atender		 		=	$detitem->kilos_atender;
		        	
		         	$despacho->cantidad_sacos_atender		=	$detitem->cantidad_sacos_atender;
		        	$despacho->palets_atender		 		=	$detitem->palets_atender;
		        	$despacho->fecha_carga		 			=	$detitem->fecha_carga;
		        	$despacho->fecha_recepcion		 		=	$detitem->fecha_recepcion;
		        	$despacho->presentacion_producto		=	$detitem->presentacion_producto;
		        	
		         	$despacho->grupo		 				=	$detitem->grupo;
		        	$despacho->grupo_orden		 			=	$detitem->grupo_orden;
		        	$despacho->grupo_movil		 			=	$detitem->grupo_movil;
		        	$despacho->grupo_orden_movil		 	=	$detitem->grupo_orden_movil;
		        	$despacho->nro_serie		 			=	$detitem->nro_serie;
		        	
		         	$despacho->nro_documento		 		=	$detitem->nro_documento;
		        	$despacho->grupo_guia		 			=	$detitem->grupo_guia;
		        	$despacho->grupo_orden_guia		 		=	$detitem->grupo_orden_guia;
		        	$despacho->correlativo		 			=	$detitem->correlativo;
		        	$despacho->tipo_grupo_oc		 		=	$detitem->tipo_grupo_oc;
		        	
		        	$despacho->usuario_crea		 			=	$detitem->usuario_crea;
		        	$despacho->unidad_medida_id		 		=	$detitem->unidad_medida_id;
		        	$despacho->modulo		 				=	$detitem->modulo;
		        	
		         	$despacho->usuario_mod		 			=	$detitem->usuario_mod;
		        	$despacho->activo		 				=	$detitem->activo;
		        	$despacho->ordendespacho_id		 		=	$detitem->ordendespacho_id;
		        	$despacho->cliente_id		 			=	$detitem->cliente_id;
		        	$despacho->orden_id		 				=	$detitem->orden_id;
		        	
		         	$despacho->orden_transferencia_id		=	$detitem->orden_transferencia_id;
		        	$despacho->producto_id		 			=	$detitem->producto_id;
		        	$despacho->empresa_id		 			=	$detitem->empresa_id;
		        	$despacho->centro_id		 			=	$detitem->centro_id;

		        	$despacho->empaquetexpallet		 		=	$producto->CAN_SACO_PALET;
		         	$despacho->skuxpallet		 			=	$producto->CAN_BOLSA_SACO;
		        	$despacho->blsscxpallet		 			=	$producto->CAN_SACO_PALET * $producto->CAN_BOLSA_SACO;
		        	$despacho->sku		 					=	'falta';
		        	$despacho->costosku		 				=	$detalleproducto->CAN_PRECIO_UNIT;
		        	$despacho->ean13		 				=	$equivalente->COD_EAN;
		         	$despacho->ean14		 				=	'falta';
		        	$despacho->lpn		 					=	$lpn;
		        	$despacho->save();







				}


        	}
      
        }


	}



	public function actionAjaxAsignarMuestrasMobil(Request $request)
	{

		$radiomobil 							= 	$request['radiomobil'];
		$ordendespacho_id 						= 	$request['ordendespacho_id'];
		$mobil_mayor 							= 	$radiomobil;

		//PRODUCTO MOBIL SELECCIONADO
		$listamuestras 							=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
													->where('activo','=','1')
													->where('tipo_grupo_oc','=','muestras')
													->where('muestra','>','0')
													->get();

		//actualizar la cantidad atender con muestras
		foreach($listamuestras as $index=>$item){

			$detalledespacho_e            	 	=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
													->where('activo','=','1')
													->where('tipo_grupo_oc','<>','muestras')
													->where('producto_id','=',$item->producto_id)
													->where('grupo_movil','=',$mobil_mayor)
													->first();

			if(count($detalledespacho_e)>0){

				$detalledespacho_e->muestra = $item->muestra;
				$detalledespacho_e->cantidad_atender = $detalledespacho_e->cantidad_atender + $item->muestra;
				$detalledespacho_e->fecha_mod 	=  	$this->fechaactual;
				$detalledespacho_e->usuario_mod 	=  	Session::get('usuario')->id;
				$detalledespacho_e->save();


				$item->orden_id = $detalledespacho_e->id;
				$item->fecha_mod 	=  	$this->fechaactual;
				$item->usuario_mod 	=  	Session::get('usuario')->id;
				$item->save();


			}else{

				$detalledespacho            	 	=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
														->where('activo','=','1')
														->where('tipo_grupo_oc','<>','muestras')
														->where('grupo_movil','=',$mobil_mayor)
														->first();


				$count_grupo 							=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
															->where('activo','=','1')
															->where('tipo_grupo_oc','<>','muestras')
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


				$cliente_despacho_id             		= 	'';
				$clientecombo 							=   '';


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

			    $producto_id 						= 	$item->producto_id;
			    $cantidad_atender 					= 	$item->muestra;
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
				$detalle->muestra 					=  	$item->muestra;
				$detalle->cantidad 					=  	0.0000;
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


				$item->orden_id = $detalledespacho->id;
				$item->fecha_mod 	=  	$this->fechaactual;
				$item->usuario_mod 	=  	Session::get('usuario')->id;
				$item->save();



			}									

		}

		//desactivar las muestras 
		WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
													->where('activo','=','1')
													->where('tipo_grupo_oc','=','muestras')
													->update([	'estado_id' => 'EPP0000000000004'
															 ]);

		//Actualizar grupo mobil 
		$count_grupo_movil 					=   WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
												->where('activo','=','1')
												->where('grupo_movil','=',$mobil_mayor)
												->where('tipo_grupo_oc','<>','muestras')
												->select(DB::raw('count(grupo_movil) as grupo_movil'))
												->groupBy('grupo_movil')
												->first();

		WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
								->where('activo','=','1')
								->where('grupo_movil','=',$mobil_mayor)
								->where('tipo_grupo_oc','<>','muestras')
								->update([	'grupo_orden_movil' => $count_grupo_movil->grupo_movil,
											'fecha_mod' 	=> $this->fechaactual,
											'usuario_mod' 	=> Session::get('usuario')->id
										 ]);

		//Recalculcular guia 
		$this->funciones->recalcular_las_guias_remision($ordendespacho_id,$mobil_mayor);

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


	public function actionAjaxActualizarListaMuestraMobil(Request $request)
	{
		
		$ordendespacho_id 				= 	$request['ordendespacho_id'];
        $muestras           			=   WEBDetalleOrdenDespacho::where('tipo_grupo_oc','=','muestras')
                                				->where('ordendespacho_id','=',$ordendespacho_id)
                                				->get();
		$funcion 						= 	$this;

		return View::make('despacho/ajax/alistamuestra',
						 [
						 	'muestras' 		=> $muestras,
						 	'funcion' 		=> $funcion,
						 	'ajax'   		=> true,
						 ]);

	}


	public function actionAjaxModificarMuestraPedidoCreadoFilaSeparado(Request $request)
	{
		
		$muestra 							= 	(float)$request['muestra'];
		$data_detalle_orden_id 				= 	$request['data_detalle_orden_id'];

		//dd($data_detalle_orden_id);

		$detalleordendespacho 				=   WEBDetalleOrdenDespacho::where('id','=',$data_detalle_orden_id)->first();



        $detalleordendespacho->muestra      =   $muestra;  
		$detalleordendespacho->fecha_mod 	=  	$this->fechaactual;
		$detalleordendespacho->usuario_mod 	=  	Session::get('usuario')->id;
		$detalleordendespacho->save();

        $muestras           				=   WEBDetalleOrdenDespacho::where('tipo_grupo_oc','=','muestras')
                                				->where('ordendespacho_id','=',$detalleordendespacho->ordendespacho_id)
                                				->get();

		$funcion 						= 	$this;
		return View::make('despacho/ajax/alistamuestra',
						 [
						 	'muestras' 		=> $muestras,
						 	'funcion' 		=> $funcion,
						 	'ajax'   		=> true,
						 ]);

	}

	public function actionExcelOrdenDespacho($idopcion,$idordendespacho,Request $request)
	{
		
		set_time_limit(0);
		$idordendespacho = 	$this->funciones->decodificarmaestra($idordendespacho);

        $pedido     	=   	WEBOrdenDespacho::join('CMP.CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA','=','WEB.ordendespachos.estado_id')
                                        ->where('id','=',$idordendespacho)
                                        ->first();

		$titulo 					=   'Pedido-despacho-'.$pedido->codigo;
		$funcion 					= 	$this->funciones;

        $muestras           =   WEBDetalleOrdenDespacho::where('tipo_grupo_oc','=','muestras')
                                ->where('ordendespacho_id','=',$pedido->id)
                                ->get();

        if($pedido->ind_plantilla == 0){

		    Excel::create($titulo, function($excel) use ($pedido,$titulo,$funcion,$muestras) {
		        $excel->sheet('Pedidos', function($sheet) use ($pedido,$titulo,$funcion,$muestras) {

		            $sheet->loadView('despacho/excel/pedido')->with('pedido',$pedido)
		                                         		 			   ->with('titulo',$titulo)
		                                         		 			   ->with('funcion',$funcion)
		                                         		 			   ->with('muestras',$muestras);                                        		 
		        });
		    })->export('xls');

        }else{


	        if($pedido->ind_plantilla == 1){

			    Excel::create($titulo, function($excel) use ($pedido,$titulo,$funcion,$muestras) {
			        $excel->sheet('Pedidos', function($sheet) use ($pedido,$titulo,$funcion,$muestras) {

			            $sheet->loadView('despacho/excel/pedidosinmuetsra')->with('pedido',$pedido)
			                                         		 			   ->with('titulo',$titulo)
			                                         		 			   ->with('funcion',$funcion)
			                                         		 			   ->with('muestras',$muestras);                                        		 
			        });
			    })->export('xls');

	        }




        }



	}


	public function actionExcelOrdenDespachoEmail($idopcion,$idordendespacho,Request $request)
	{
		
		set_time_limit(0);
		$idordendespacho 			= 	$this->funciones->decodificarmaestra($idordendespacho);

        $pedido     =   	WEBOrdenDespacho::join('CMP.CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA','=','WEB.ordendespachos.estado_id')
                                        ->where('id','=',$idordendespacho)
                                        ->first();

		$titulo 					=   'Pedido-despacho-'.$pedido->codigo;
		$funcion 					= 	$this->funciones;

        $muestras           =   WEBDetalleOrdenDespacho::where('tipo_grupo_oc','=','muestras')
                                ->where('ordendespacho_id','=',$pedido->id)
                                ->get();




        if($pedido->ind_plantilla == 0){

		    Excel::create($titulo, function($excel) use ($pedido,$titulo,$funcion,$muestras) {
		        $excel->sheet('Pedidos', function($sheet) use ($pedido,$titulo,$funcion,$muestras) {

		            $sheet->loadView('despacho/excel/pedido')->with('pedido',$pedido)
		                                         		 			   ->with('titulo',$titulo)
		                                         		 			   ->with('funcion',$funcion)
		                                         		 			   ->with('muestras',$muestras);                                        		 
		        });
		    })->store('xls');

        }else{


	        if($pedido->ind_plantilla == 1){

			    Excel::create($titulo, function($excel) use ($pedido,$titulo,$funcion,$muestras) {
			        $excel->sheet('Pedidos', function($sheet) use ($pedido,$titulo,$funcion,$muestras) {

			            $sheet->loadView('despacho/excel/pedidosinmuetsra')->with('pedido',$pedido)
			                                         		 			   ->with('titulo',$titulo)
			                                         		 			   ->with('funcion',$funcion)
			                                         		 			   ->with('muestras',$muestras);                                        		 
			        });
			    })->store('xls');

	        }




        }


	}



	public function actionAjaxAgregarServicio(Request $request)
	{	
	
		$count_servicio 			=  	(int)$request['count_servicio'] + 1;
		$calcula_cantidad_peso 		=  	$request['calcula_cantidad_peso'];	
		$ls_servicios 				=  	$request['ls_servicios'];
		$tipo 		 				=  	$request['tipo'];
		$lista_de_servicios			= 	array();
		$lista_de_servicios_temp	= 	array(
											array("servicio"=>'PRD0000000017065'),
											array("servicio"=>'PRD0000000003384')
											);

		if ($tipo == "PK") {	// Si viene desde picking
			$serv = explode("-", $ls_servicios);
			
			foreach($lista_de_servicios_temp as $index => $item){
				$band = 0;
				foreach($serv as $index => $item2){
					if($item["servicio"] == $item2){
						array_push($lista_de_servicios, array("servicio"=>$item["servicio"]));	
						$band = 1;
					}
				}
				if ($band == 0){
					array_push($lista_de_servicios, array("servicio"=>$item["servicio"]));	
				}
			};	
		}else {
			$lista_de_servicios			= 	array
										  	(
										  	array("servicio"=>'PRD0000000017065'),
										  	array("servicio"=>'PRD0000000003384')
										  	);
		
			for( $i= 1 ; $i < $count_servicio ; $i++ )
			{
				array_push($lista_de_servicios, array("servicio"=>'PRD0000000017065'));		
			}
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
	    								->where('activo','=','1')
	    								->select('orden_transferencia_id')
	    								->groupBy('orden_transferencia_id')
	    								->get();

        $muestras           		=   WEBDetalleOrdenDespacho::where('tipo_grupo_oc','=','muestras')
                                		->where('ordendespacho_id','=',$ordendespacho->id)
                                		->get();


	    $lista_de_servicios			= 	array
										  	(
										  	array("servicio"=>'PRD0000000017065'),
										  	array("servicio"=>'PRD0000000003384')
										  	);

		//dd($lista_de_servicios);						  	

		$array_centro_id 						=   ['CEN0000000000001','CEN0000000000004','CEN0000000000006'];
		$combo_lista_centros 					= 	$this->funciones->combo_lista_centro_array_filtro($array_centro_id);
		//$combo_lista_centros 					= 	$this->funciones->combo_lista_centro();
		$combo_almacen_origen 					=   array();
		$combo_almacen_destino 					=   array();
		$data_productos_tranferencia_pt 		=   array();
		$combo_serie_guia 						=   $this->funciones->combo_series('TDO0000000000009','0');
		$count_servicio 						= 	1;
		$calcula_cantidad_peso 					= 	0;


		return View::make('despacho/atenderordendespacho',
						 [
						 	'ordendespacho' 						=> $ordendespacho,
						 	'muestras' 								=> $muestras,
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
				$detalleordendespacho->fecha_carga 		=  	$fechadeentrega;//fecha entrega falta
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
				$detalleordendespacho               		=   WEBDetalleOrdenDespacho::where('id','=',$values)->first();
				$detalleordendespacho->centro_atender_id 	=  	$centro_origen_id;
				$detalleordendespacho->nro_serie 			=  	'';
				$detalleordendespacho->nro_documento 		=   '';
				$detalleordendespacho->fecha_mod 			=  	$this->fechaactual;
				$detalleordendespacho->usuario_mod 			=  	Session::get('usuario')->id;
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
			$producto_id								= 	$obj['producto_id'];
			$cantidad_atender 							= 	0.00;

			$array_detalle_orden_despacho_id 		= 	explode(",", $detalle_orden_despacho_id);
			foreach ($array_detalle_orden_despacho_id as $values)
			{


				$cantidad_atender							= 	$cantidad_atender_total/count($array_detalle_orden_despacho_id);
				$producto 									= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
				$kilos_atender 								=   $cantidad_atender*$producto->CAN_PESO_MATERIAL;
				$cantidad_sacos_atender						= 	$cantidad_atender/$producto->CAN_BOLSA_SACO;
				$palets_atende 								= 	$cantidad_sacos_atender/$producto->CAN_SACO_PALET;



				$detalleordendespacho               		=   WEBDetalleOrdenDespacho::where('id','=',$values)->first();
				$detalleordendespacho->cantidad_atender 	=  	$cantidad_atender;
				$detalleordendespacho->kilos_atender 			=  	$kilos_atender;
				$detalleordendespacho->cantidad_sacos_atender 	=  	$cantidad_sacos_atender;
				$detalleordendespacho->palets_atender 			=  	$palets_atende;

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





	public function actionAjaxRechazarProducto(Request $request)
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



	public function actionAjaxClientesMobilModal(Request $request)
	{


		$grupo_mobil_modal 				= 	$request['grupo_mobil_modal'];
		$ordendespacho_id 				= 	$request['ordendespacho_id'];


		$listaclientes 					=	WEBDetalleOrdenDespacho::join('STD.EMPRESA ', 'STD.EMPRESA.COD_EMPR', '=', 'WEB.detalleordendespachos.cliente_id')
											->where('ordendespacho_id','=',$ordendespacho_id)
											->where('activo','=','1')
											->where('grupo_movil','=',$grupo_mobil_modal)
											->select(DB::raw(" (STD.EMPRESA.COD_EMPR + '-' + WEB.detalleordendespachos.nro_orden_cen) as COD_EMPR , 
															  (max(STD.EMPRESA.NOM_EMPR) + ' - ' + WEB.detalleordendespachos.nro_orden_cen) as NOM_EMPR"))
											->groupBy('STD.EMPRESA.COD_EMPR')
											->groupBy('WEB.detalleordendespachos.nro_orden_cen')
											->pluck('NOM_EMPR','COD_EMPR')
											->toArray();

		$comboclientes  				= 	array('' => "Seleccione cliente") + $listaclientes;

		return View::make('despacho/modal/ajax/acombocliente',
						 [
						 	'comboclientes' 		=> $comboclientes,
						 	'ajax'   		  			=> true,
						 ]);
	}




	public function actionAjaxOrdenCenMobilModal(Request $request)
	{


		$grupo_mobil_modal 				= 	$request['grupo_mobil_modal'];
		$ordendespacho_id 				= 	$request['ordendespacho_id'];
		$cuenta_id_modal 				= 	$request['cuenta_id_modal'];

		$cliente 						=   '';
		$cliente_id 					=   '';
		$cuenta 						= 	WEBListaCliente::where('COD_CONTRATO','=',$cuenta_id_modal)->first();
		if(count($cuenta)>0){
			$cliente 					= 	WEBListaCliente::where('id','=',$cuenta->id)->first();		
		}

		if(isset($cliente->id)){
			$cliente_id 				=   $cliente->id;
		}

		$listaordencen 					=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
											->where('activo','=','1')
											->where('grupo_movil','=',$grupo_mobil_modal)
											->where('cliente_id','=',$cliente_id)
											->groupBy('nro_orden_cen')
											->pluck('nro_orden_cen','nro_orden_cen')
											->toArray();


		$comboordencen 					= 	array('' => "Seleccione orden cen") + $listaordencen;

		return View::make('despacho/modal/ajax/acomboordencen',
						 [
						 	'comboordencen' 		=> $comboordencen,
						 	'ajax'   		  			=> true,
						 ]);
	}



	public function actionAjaxModalAgregarProductosPedidoAtender(Request $request)
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

		$array_grupo_mobil 				= 	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
											->where('activo','=','1')
											->where('tipo_grupo_oc','<>','muestras')
											->select('grupo_movil')
											->select(DB::raw('grupo_movil 
															,max(orden_transferencia_id) orden_transferencia_id
															,max(nro_serie) nro_serie')
													)
											->groupBy('grupo_movil')
											->havingRaw("(max(orden_transferencia_id) is NULL or max(orden_transferencia_id) = '') 
														 and (max(nro_serie) = '' or max(nro_serie) is NULL) 
														 and (max(nro_documento) = '' or max(nro_documento) is NULL)")
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
	    									//->EmpresaCentro($sw_empresa_centro)
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
