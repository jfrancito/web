<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBListaCliente,App\STDTipoDocumento,App\WEBReglaProductoCliente,App\WEBPedido;
use App\WEBDetallePedido,App\CMPCategoria,App\WEBReglaCreditoCliente,App\STDEmpresa,App\WEBPrecioProducto,App\WEBMaestro,App\WEBPrecioProductoContrato,App\STDEmpresaDireccion;
use App\CMPDocumentoCtble;
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

		$combo_lista_centros 			= 	$this->funciones->combo_lista_centro_todos();


		return View::make('pedido/reporte/listatomapedidoxestado',
						 [
						 	'idopcion' 		=> $idopcion,
						 	'fechainicio' 	=> $fechainicio,
						 	'fechafin' 		=> $fechafin,
						 	'funcion' 		=> $funcion,
						 	'combo_estados' => $combo_estados,
						 	'combo_lista_centros' => $combo_lista_centros,
						 ]);



	}


	public function actionAjaxPedidoEstado(Request $request)
	{

		set_time_limit(0);
		$estado_id 									=  	$request['estado_id'];
		$finicio 									=  	$request['finicio'];	
		$ffin 										=  	$request['ffin'];	
		$centro_id 									=  	$request['centro_id'];

		if($estado_id == 'TODO'){

		    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
								->leftJoin('ALM.CENTRO', 'ALM.CENTRO.COD_CENTRO', '=', 'web.detallepedidos.centro_id')
								//->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
								->Centro($centro_id)
			    				->where('WEB.pedidos.fecha_venta','>=', $finicio)
			    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.pedidos.direccion_entrega_id,
			    								  ALM.CENTRO.NOM_CENTRO,WEB.detallepedidos.atendido'))
			    				->orderBy('WEB.detallepedidos.centro_id', 'asc')
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
								->leftJoin('ALM.CENTRO', 'ALM.CENTRO.COD_CENTRO', '=', 'web.detallepedidos.centro_id')
								//->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
								//->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
								->Centro($centro_id)
								->whereIn('WEB.detallepedidos.pedido_id',$arrayidpedidos)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.pedidos.direccion_entrega_id,
			    								  ALM.CENTRO.NOM_CENTRO,WEB.detallepedidos.atendido'))
			    				->orderBy('WEB.detallepedidos.centro_id', 'asc')
								->orderBy('WEB.pedidos.fecha_venta', 'desc')
								->get();

		}else{


		    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
								->leftJoin('ALM.CENTRO', 'ALM.CENTRO.COD_CENTRO', '=', 'web.detallepedidos.centro_id')
								->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
								//->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
								->Centro($centro_id)
			    				->where('WEB.pedidos.fecha_venta','>=', $finicio)
			    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.pedidos.direccion_entrega_id,
			    								  ALM.CENTRO.NOM_CENTRO,WEB.detallepedidos.atendido'))
			    				->orderBy('WEB.detallepedidos.centro_id', 'asc')
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





	public function actionContratoPendiente()
	{
		set_time_limit(0);
        $fechadia       =   date_format(date_create(date('d-m-Y')), 'd-m-Y');
        $fecha_actual   =   date("Y-m-d");
		$titulo 		=   'Contrato-Pendiente';

    	/*Lista para seleccionar solititud*/
		$stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC STD.LISTA_CONTRATOS_PENDIENTE');
        $stmt->execute();

		$funcion 									= 	$this;

	    Excel::create($titulo.'-('.$fecha_actual.')', function($excel) use ($stmt,$titulo,$funcion) {
	        $excel->sheet('Contrato pendiente', function($sheet) use ($stmt,$titulo,$funcion) {

	            $sheet->loadView('pedido/excel/listacontratoxpendiente')->with('lista',$stmt)
	                                         		 			   ->with('titulo',$titulo)
	                                         		 			   ->with('funcion',$funcion);                                        		 
	        });
	    })->store('xls');

	}

	public function actionDocumentoSinEnviarExcelAutomatico()
	{
		set_time_limit(0);
        $fechadia       =   date_format(date_create(date('d-m-Y')), 'd-m-Y');


        $fecha_actual   =   date("Y-m-d");
        //$fecha_actual   =   '2021-01-07';
        $titulo             =   'documentos-sin-enviar';
        $fecha_manana   =   date("Y-m-d",strtotime($fecha_actual."+ 1 days"));

        $lista_documento    =   CMPDocumentoCtble::leftJoin('SGD.USUARIO','SGD.USUARIO.COD_USUARIO','=','CMP.DOCUMENTO_CTBLE.COD_USUARIO_CREA_AUD')
                                ->whereRaw("LEFT(CMP.DOCUMENTO_CTBLE.NRO_SERIE ,1) in ('F','B')")
                                ->whereRaw("YEAR(CMP.DOCUMENTO_CTBLE.FEC_EMISION)>2021")
                                ->whereRaw("CMP.DOCUMENTO_CTBLE.COD_CATEGORIA_TIPO_DOC IN ('TDO0000000000001','TDO0000000000003','TDO0000000000007','TDO0000000000008')")
                                ->where('CMP.DOCUMENTO_CTBLE.ESTADO_ELEC','=','C')
                                ->where('CMP.DOCUMENTO_CTBLE.COD_ESTADO','=',1)
                                ->where('CMP.DOCUMENTO_CTBLE.IND_COMPRA_VENTA','=','V')
                                ->select(DB::raw('CMP.DOCUMENTO_CTBLE.TXT_EMPR_EMISOR as EMPR_EMISOR,CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE,CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_TIPO_DOC AS TIPO_DOC,CMP.DOCUMENTO_CTBLE.NRO_SERIE,CMP.DOCUMENTO_CTBLE.NRO_DOC,TXT_EMPR_RECEPTOR as CLIENTE,CMP.DOCUMENTO_CTBLE.FEC_EMISION,CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_ESTADO_DOC_CTBLE as ESTADO_DOC_CTBLE,SGD.USUARIO.NOM_TRABAJADOR'))

                                ->orderBy('CMP.DOCUMENTO_CTBLE.FEC_EMISION', 'ASC')
                                ->orderBy('CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', 'desc')
                                ->orderBy('CMP.DOCUMENTO_CTBLE.NRO_SERIE', 'desc')
                                ->orderBy('CMP.DOCUMENTO_CTBLE.NRO_DOC', 'desc')
                                ->get();



		$funcion 									= 	$this;

	    Excel::create($titulo.'-('.$fecha_actual.')', function($excel) use ($lista_documento,$titulo,$funcion) {
	        $excel->sheet('Pedidos', function($sheet) use ($lista_documento,$titulo,$funcion) {

	            $sheet->loadView('pedido/excel/listadocumentossinenviar')->with('lista_documento',$lista_documento)
	                                         		 			   ->with('titulo',$titulo)
	                                         		 			   ->with('funcion',$funcion);                                        		 
	        });
	    })->store('xls');

	}


	public function actionPedidoEstadoExcelAutomatico()
	{
		set_time_limit(0);
        $fechadia       =   date_format(date_create(date('d-m-Y')), 'd-m-Y');


        $fecha_actual   =   date("Y-m-d");
        //$fecha_actual   =   '2021-01-07';
		$titulo 		=   'Pedido-x-Estado';
        $fecha_manana   =   date("Y-m-d",strtotime($fecha_actual."+ 1 days"));

        $listapedidos   =   WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
                            ->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
                            ->leftJoin('ALM.CENTRO', 'ALM.CENTRO.COD_CENTRO', '=', 'web.detallepedidos.centro_id')
                            ->where('WEB.pedidos.fecha_venta','>=', $this->inicio)
                            ->where('WEB.pedidos.fecha_venta','<=', $fecha_actual)
                            ->where('WEB.detallepedidos.activo','=', 1)
                            ->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
                                              WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
                                              WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,WEB.detallepedidos.total,
                                              WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
                                              WEB.pedidos.direccion_entrega_id,
                                              ALM.CENTRO.NOM_CENTRO,WEB.detallepedidos.atendido'))
                            ->orderBy('WEB.detallepedidos.centro_id', 'asc')
                            ->orderBy('WEB.pedidos.fecha_venta', 'desc')
                            ->get();





		$funcion 									= 	$this;

	    Excel::create($titulo.'-('.$fecha_actual.')', function($excel) use ($listapedidos,$titulo,$funcion) {
	        $excel->sheet('Pedidos', function($sheet) use ($listapedidos,$titulo,$funcion) {

	            $sheet->loadView('pedido/excel/listapedidoxestado')->with('listapedidos',$listapedidos)
	                                         		 			   ->with('titulo',$titulo)
	                                         		 			   ->with('funcion',$funcion);                                        		 
	        });
	    })->store('xls');

	}


	public function actionPedidoEstadoExcel($finicio,$fechafin,$estado_id,$centro_id)
	{
		set_time_limit(0);

        $fechadia                                   =   date_format(date_create(date('d-m-Y')), 'd-m-Y');
		$titulo 									=   'Pedido x Estado';

		$centro_id 									=  	$centro_id;
		$estado_id 									=  	$estado_id;
		$finicio 									=  	$finicio;	
		$ffin 										=  	$fechafin;	




		if($estado_id == 'TODO'){

		    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
								->leftJoin('ALM.CENTRO', 'ALM.CENTRO.COD_CENTRO', '=', 'web.detallepedidos.centro_id')
								//->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
								->Centro($centro_id)
			    				->where('WEB.pedidos.fecha_venta','>=', $finicio)
			    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.detallepedidos.total,
			    								  WEB.pedidos.direccion_entrega_id,
			    								  ALM.CENTRO.NOM_CENTRO,WEB.detallepedidos.atendido'))
			    				->orderBy('WEB.detallepedidos.centro_id', 'asc')
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
								->leftJoin('ALM.CENTRO', 'ALM.CENTRO.COD_CENTRO', '=', 'web.detallepedidos.centro_id')
								//->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
								//->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
								->Centro($centro_id)
								->whereIn('WEB.detallepedidos.pedido_id',$arrayidpedidos)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.detallepedidos.total,
			    								  WEB.pedidos.direccion_entrega_id,
			    								  ALM.CENTRO.NOM_CENTRO,WEB.detallepedidos.atendido'))
			    				->orderBy('WEB.detallepedidos.centro_id', 'asc')
								->orderBy('WEB.pedidos.fecha_venta', 'desc')
								->get();

		}else{


		    $listapedidos	= 	WEBDetallePedido::join('WEB.pedidos', 'WEB.pedidos.id', '=', 'WEB.detallepedidos.pedido_id')
								->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.detallepedidos.estado_id')
								->leftJoin('ALM.CENTRO', 'ALM.CENTRO.COD_CENTRO', '=', 'web.detallepedidos.centro_id')
								->whereIn('WEB.detallepedidos.estado_id', [$estado_id])
								//->where('WEB.detallepedidos.centro_id','=',Session::get('centros')->COD_CENTRO)
								->Centro($centro_id)
			    				->where('WEB.pedidos.fecha_venta','>=', $finicio)
			    				->where('WEB.pedidos.fecha_venta','<=', $ffin)
			    				->where('WEB.detallepedidos.activo','=', 1)
			    				->select(DB::raw('WEB.pedidos.codigo,WEB.pedidos.fecha_venta,WEB.pedidos.usuario_crea,
			    								  WEB.pedidos.cliente_id,WEB.detallepedidos.producto_id,
			    								  WEB.detallepedidos.cantidad,WEB.detallepedidos.precio,
			    								  WEB.detallepedidos.empresa_receptora_id,CMP.CATEGORIA.NOM_CATEGORIA,
			    								  WEB.detallepedidos.total,
			    								  WEB.pedidos.direccion_entrega_id,
			    								  ALM.CENTRO.NOM_CENTRO,WEB.detallepedidos.atendido'))
			    				->orderBy('WEB.detallepedidos.centro_id', 'asc')
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



	public function actionImprimirPedidoTransportista($idpedido,$accion,$detalle)
	{



		$arraydetalle 								= 	base64_decode(trim($detalle));
		$array_detalle_producto_request 			= 	json_decode($arraydetalle,true);



		$titulo 									=   'Pedido';
		$idpedido 									= 	$this->funciones->desencriptar_id('1CIX-'.$idpedido,8);
		$pedido 									=   WEBPedido::where('id','=',$idpedido)->first();
		$funcion 									= 	$this;


		$pdf 										= 	PDF::loadView('pedido.pdf.imprimirpedidotransportista', 
														[
															'pedido' 	=> $pedido,
															'titulo' 	=> $titulo,
															'accion'    => $accion,
															'array_detalle_producto_request'    => $array_detalle_producto_request,
															'funcion' 	=> $funcion								
														]);

		return $pdf->stream('download.pdf');


	}







}
