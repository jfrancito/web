<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBPicking,App\WEBPickingDetalle,App\CMPDetraccion,App\CMPDetraccionDetalle;
use App\WEBTransferencia;
use View;
use Session;
use App\Biblioteca\Osiris;
use App\Biblioteca\Funcion;
use PDO;
use Mail;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
  
class PickingReporteController extends Controller
{

	public function actionImprimirSolicitudTransferencia($idtransferencia)
	{        
		$titulo 										=   'Solicitud de Transferencia';
		$idtransferencia 								= 	$this->funciones->decodificarmaestraBD('WEB.transferencia', $idtransferencia);
		$transferencia 									=   WEBTransferencia::where('id','=',$idtransferencia)->first();

		$funcion 									= 	$this;
        
		$pdf 										= 	PDF::loadView('picking.reporte.imprimirtransferencia', 
														[
															'transferencia'  => $transferencia,
															'titulo' 		 => $titulo,
															'funcion' 		 => $funcion								
														]);
		return $pdf->stream('download.pdf');


	}

	public function actionImprimirPicking($idpicking)
	{        
		$titulo 									=   'Picking';
		$idpicking 									= 	$this->funciones->decodificarmaestraBD('WEB.picking', $idpicking);
		$picking 									=   WEBPicking::where('id','=',$idpicking)->first();

		$pickingdetalle								=   WEBPickingDetalle::where('picking_id','=',$idpicking)
														->leftJoin('CMP.DETALLE_PRODUCTO as D', 'orden_id', '=', 'D.COD_TABLA')
														->get();

		$funcion 									= 	$this;
        
		$pdf 										= 	PDF::loadView('picking.reporte.imprimirpicking', 
														[
															'picking' 		 => $picking,
															'pickingdetalle' => $pickingdetalle,
															'titulo' 		 => $titulo,
															'funcion' 		 => $funcion								
														]);
		return $pdf->stream('download.pdf');


	}

	public function actionImprimirPickingDetraccion($idpicking)
	{        
		$titulo 									=   'Picking';
		$idpicking 									= 	$this->funciones->decodificarmaestraBD('WEB.picking', $idpicking);

		$picking 									=   WEBPicking::where('id','=',$idpicking)->first();
				
		$stmt										= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.DETRACCION_IMPRIMIR_PICKING ?');
														$stmt->bindParam(1, $idpicking,PDO::PARAM_STR);                      
														$stmt->execute();    
		$detracciondetalle 							= 	array();

		while($row = $stmt->fetch()){
		  array_push($detracciondetalle,$row);
		}		

		$funcion 									= 	$this;
        
		$pdf 										= 	PDF::loadView('picking.reporte.imprimirpickingdetraccion', 
														[
															'detracciondetalle' => $detracciondetalle,
															'picking'			=> $picking,
															'titulo' 		 => $titulo,
															'funcion' 		 => $funcion								
														]);
		return $pdf->stream('download.pdf');
	}

	public function actionDetraccionDiario($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		return View::make('picking/reporte/detracciondiario',
						 [
						 	'idopcion' 					=> $idopcion,
							'inicio'					=> $this->inicio,
							'hoy'						=> $this->fin,
						 ]);

	}

	public function actionAjaxDetraccionDiario(Request $request)
	{
		set_time_limit(0);
		$fechafin 						=  	$request['fechafin'];	
		
		$listadetraccion				= 	DB::table('CMP.DETRACCION as T1')
											->leftJoin('CMP.DETRACCION_DETALLE as T2', 'T1.COD_DETRACCION', '=', 'T2.COD_DETRACCION')
											->where('T1.FEC_DETRACCION','=',$fechafin)	 
											->where('T1.COD_ESTADO','=',1)
											->where('T2.COD_ESTADO','=',1)
											->groupBy('T1.FEC_DETRACCION','T1.DOC_REFERENCIA','T2.IND_DOC','T1.CAN_DETRACCION')
											->select('T1.FEC_DETRACCION', 'DOC_REFERENCIA', 'T2.IND_DOC', 'T1.CAN_DETRACCION')			    												
											->get();

		$funcion 									= 	$this;

		return View::make('picking/reporte/ajax/listadetracciondiario',
						 [
							'listadetraccion'	  							=> $listadetraccion,
						 	'funcion' 										=> $funcion,
						 	'fechafin' 										=> $fechafin,				 						 					 
						 ]);

	}

	public function actionDetraccionDiarioPDF($fechadia)
	{
		$titulo 									=   'Detracción Diario';

		$listadetraccion							= 	DB::table('CMP.DETRACCION as T1')
														->leftJoin('CMP.DETRACCION_DETALLE as T2', 'T1.COD_DETRACCION', '=', 'T2.COD_DETRACCION')
														->where('T1.FEC_DETRACCION','=',$fechadia)	 
														->where('T1.COD_ESTADO','=',1)
														->where('T2.COD_ESTADO','=',1)
														->groupBy('T1.FEC_DETRACCION','T1.DOC_REFERENCIA','T2.IND_DOC','T1.CAN_DETRACCION')
														->select('T1.FEC_DETRACCION', 'DOC_REFERENCIA', 'T2.IND_DOC', 'T1.CAN_DETRACCION')			    												
														->get();


		$funcion 									= 	$this;
		$empresa 									= 	Session::get('empresas')->NOM_EMPR;
		$centro 									= 	Session::get('centros')->NOM_CENTRO;	
		$fechaactual 								=   $fechadia;

		$pdf 					= 	PDF::loadView('picking.reporte.pdf.listadetracciondiario', 
												[
													'listadetraccion'	  => $listadetraccion,
													'titulo' 		  	  => $titulo,
													'empresa' 		  	  => $empresa,											
													'centro' 		  	  => $centro,
													'funcion' 		  	  => $funcion,
													'fechafin' 		  	  => $fechaactual,									
												]);

		return $pdf->stream('download.pdf');
	}


}