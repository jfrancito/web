<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBListaCliente,App\STDTipoDocumento,App\WEBReglaProductoCliente,App\WEBPedido;
use App\WEBDetallePedido,App\CMPCategoria,App\WEBReglaCreditoCliente,App\STDEmpresa,App\WEBPrecioProducto,App\WEBMaestro,App\WEBPrecioProductoContrato,App\STDEmpresaDireccion;
use View;
use Session;
use App\Biblioteca\Osiris;
use App\Biblioteca\Funcion;
use PDO;
use Mail;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
  
class OrdenPedidoReporteController extends Controller
{


	public function actionPedidoXEstado($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $fechainicio  					= 	$this->fecha_menos_quince;
	    $fechafin  						= 	$this->fin;
        $combo_estados   				=   $this->funciones->combo_categoria_txt_grupo_parcialmente('ESTADO_PREORDEN');
		$funcion 						= 	$this;

		return View::make('pedido/reporte/listatomapedidoxestado',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'fechainicio' 	=> $fechainicio,
						 	'fechafin' 		=> $fechafin,
						 	'funcion' 		=> $funcion,
						 	'combo_estados' => $combo_estados,
						 ]);



	}


	public function actionAjaxPedidoEstado(Request $request)
	{

		set_time_limit(0);
		$estado_id 									=  	$request['estado_id'];
		$finicio 									=  	$request['finicio'];	
		$ffin 										=  	$request['ffin'];	

		if($estado_id == 'TODO'){

		    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
								//->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
								->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
			    				->where('WEB.pedidos.fecha_venta','>=', $finicio)
			    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.pedidos.direccion_entrega_id'))
								->orderBy('WEB.pedidos.fecha_venta', 'desc')
								->get();

		}else{


		if($estado_id == 'PARCIALMENTEATENDIDA'){



			$arrayidpedidos   	=	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
						    		->where('WEB.pedidos.fecha_venta','>=', $finicio)
				    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
									->groupBy('WEB.detallepedidos.pedido_id')
									->select('WEB.detallepedidos.pedido_id')
									->havingRaw('max(WEB.detallepedidos.estado_id) = ?', ['EPP0000000000004'])
									->havingRaw('min(WEB.detallepedidos.estado_id) = ?', ['EPP0000000000003'])
									->pluck('pedido_id')
									->toArray();


		    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
								//->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
								->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
								->whereIn('WEB.detallepedidos.pedido_id',$arrayidpedidos)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.pedidos.direccion_entrega_id'))
								->orderBy('WEB.pedidos.fecha_venta', 'desc')
								->get();

		}else{


		    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
								->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
								->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
			    				->where('WEB.pedidos.fecha_venta','>=', $finicio)
			    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.pedidos.direccion_entrega_id'))
								->orderBy('WEB.pedidos.fecha_venta', 'desc')
								->get();

		
			}


		}






		$funcion 									= 	$this;

		return View::make('pedido/reporte/ajax/listapedidoxestado',
						 [
							'listapedidos'   							=> $listapedidos,
						 	'funcion' 										=> $funcion,
				 					 					 
						 ]);

	}


	public function actionPedidoEstadoExcel($finicio,$fechafin,$estado_id)
	{
		set_time_limit(0);

        $fechadia                                   =   date_format(date_create(date('d-m-Y')), 'd-m-Y');
		$titulo 									=   'Pedido x Estado';


		$estado_id 									=  	$estado_id;
		$finicio 									=  	$finicio;	
		$ffin 										=  	$fechafin;	

		if($estado_id == 'TODO'){

		    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
								//->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
								->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
			    				->where('WEB.pedidos.fecha_venta','>=', $finicio)
			    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.pedidos.direccion_entrega_id'))
								->orderBy('WEB.pedidos.fecha_venta', 'desc')
								->get();

		}else{


			if($estado_id == 'PARCIALMENTEATENDIDA'){



				$arrayidpedidos   	=	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
							    		->where('WEB.pedidos.fecha_venta','>=', $finicio)
					    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
										->groupBy('WEB.detallepedidos.pedido_id')
										->select('WEB.detallepedidos.pedido_id')
										->havingRaw('max(WEB.detallepedidos.estado_id) = ?', ['EPP0000000000004'])
										->havingRaw('min(WEB.detallepedidos.estado_id) = ?', ['EPP0000000000003'])
										->pluck('pedido_id')
										->toArray();


			    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
									->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
									//->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
									->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
									->whereIn('WEB.detallepedidos.pedido_id',$arrayidpedidos)
				    				->where('WEB.detallepedidos.activo','=', 1)
				    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
				    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
				    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
				    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
				    								  WEB.pedidos.direccion_entrega_id'))
									->orderBy('WEB.pedidos.fecha_venta', 'desc')
									->get();

			}else{


			    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
									->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
									->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
									->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
				    				->where('WEB.pedidos.fecha_venta','>=', $finicio)
				    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
				    				->where('WEB.detallepedidos.activo','=', 1)
				    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
				    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
				    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
				    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
				    								  WEB.pedidos.direccion_entrega_id'))
									->orderBy('WEB.pedidos.fecha_venta', 'desc')
									->get();

			
				}


		}


		$funcion 									= 	$this;

	    Excel::create($titulo.' ('.$fechadia.')', function($excel) use ($listapedidos,$titulo,$funcion) {
	        $excel->sheet('Pedidos', function($sheet) use ($listapedidos,$titulo,$funcion) {

	            $sheet->loadView('pedido/excel/listapedidoxestado')->with('listapedidos',$listapedidos)
	                                         		 			   ->with('titulo',$titulo)
	                                         		 			   ->with('funcion',$funcion);                                        		 
	        });
	    })->export('xls');

	}




	public function actionImprimirPedido($idpedido)
	{


		$titulo 									=   'Pedido';
		$idpedido 									= 	$this->funciones->desencriptar_id('1CIX-'.$idpedido,8);
		$pedido 									=   WEBPedido::where('id','=',$idpedido)->first();
		$funcion 									= 	$this;


		$pdf 										= 	PDF::loadView('pedido.pdf.imprimirpedido', 
														[
															'pedido' 	=> $pedido,
															'titulo' 	=> $titulo,
															'funcion' 	=> $funcion								
														]);

		return $pdf->stream('download.pdf');


	}




}
