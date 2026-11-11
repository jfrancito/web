<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBCuota;
use App\WEBDetalleCuota;
use App\CONPeriodo;
use App\CMPCategoria;
use App\CMPCategoriaRelacion;
use App\WEBPeridoBono;
use App\WEBDetalleCalculoBono;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait BonoTraits
{

	public function bn_lista_detalle_calculado_insert($detallecuotavendedor,$periodobono,$jefeventa,$iddetallecuota,$idperiodobono){

		 WEBDetalleCalculoBono::where('calculobono_id',$iddetallecuota)->delete();


        foreach ($detallecuotavendedor as $index=>$item) {

				$detalle            	 					=	new WEBDetalleCalculoBono;
				$detalle->periodo_id 	   					=   $periodobono->periodo_id;
				$detalle->jefeventa_id 						=   $jefeventa->COD_CATEGORIA;
				$detalle->jefeventa_nombre 					=   $jefeventa->NOM_CATEGORIA;
				$detalle->anio 	   							=   $periodobono->anio;
				$detalle->mes 	   							=   $periodobono->mes;
				$detalle->calculobono_id 	   				=   $iddetallecuota;
				$detalle->periodobono_id 	   				=   $idperiodobono;
				$detalle->canal_id 							=   $item->canal_id;
				$detalle->canal_nombre 	   					=   $item->canal_nombre;
				$detalle->subcanal_id 	   					=   $item->subcanal_id;
				$detalle->subcanal_nombre 					=   $item->subcanal_nombre;

				$detalle->cuota 							=   0;
				$detalle->venta 							=   0;
				$detalle->nc 								=   0;

				$detalle->alcance 							=   0;
				$detalle->bono 								=   0;

				$detalle->fecha_crea 	 					=   date('Ymd h:i:s');
				$detalle->usuario_crea 						=   Session::get('usuario')->id;
				$detalle->save();

        }

		return 	1;
	}


	private function bn_lista_detalle_nc_calculado($cuota_id,$fecha_inicio,$fecha_fin,$jefeventa_id){

        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.listaBonoNC 
							@cuota_id = ?,
							@fecha_inicio = ?,
							@fecha_fin = ?,
							@jefeventa_id = ?');

        $stmt->bindParam(1, $cuota_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $fecha_inicio  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $fecha_fin  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $jefeventa_id  ,PDO::PARAM_STR);
        $stmt->execute();

		return $stmt;
	}


	private function bn_lista_detalle_calculado($cuota_id,$fecha_inicio,$fecha_fin,$jefeventa_id){

        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.listaBonoVenta 
							@cuota_id = ?,
							@fecha_inicio = ?,
							@fecha_fin = ?,
							@jefeventa_id = ?');

        $stmt->bindParam(1, $cuota_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $fecha_inicio  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $fecha_fin  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $jefeventa_id  ,PDO::PARAM_STR);
        $stmt->execute();

		return $stmt;
	}

	private function bn_lista_calculo_bonos($anio)
	{

		$listabonos 	= 	WEBPeridoBono::where('anio','=',$anio)->get();

		return $listabonos;

	}


	public function bn_clonardatosgenerales($cuota_id,$cuotaclonar,$user_id){

		$bonoclonacion 		= 	WEBDetalleCuota::where('cuota_id','=',$cuotaclonar->id)->get();
		$bonoactual 		= 	WEBDetalleCuota::where('cuota_id','=',$cuota_id)->get();

        foreach ($bonoclonacion as $index=>$item) {

			$jefeventa 							= 	CMPCategoria::where('COD_CATEGORIA','=', $item->jefeventa_id)->first();
			$canal 								= 	CMPCategoria::where('COD_CATEGORIA','=', $item->canal_id)->first();
			$subcanal 							= 	CMPCategoria::where('COD_CATEGORIA','=', $item->subcanal_id)->first();

			$iddetallecuota 							=   $this->funciones->getCreateIdMaestra('web.detallecuotas');
			$cabecera            	 					=	new WEBDetalleCuota;
			$cabecera->id 	     	 					=   $iddetallecuota;
			$cabecera->cuota_id 	   					=   $cuota_id;
			$cabecera->jefeventa_id 					=   $jefeventa->COD_CATEGORIA;
			$cabecera->jefeventa_nombre 				=   $jefeventa->NOM_CATEGORIA;
			$cabecera->canal_id 						=   $canal->COD_CATEGORIA;
			$cabecera->canal_nombre 					=   $canal->NOM_CATEGORIA;
			$cabecera->subcanal_id 						=   $subcanal->COD_CATEGORIA;
			$cabecera->subcanal_nombre 					=   $subcanal->NOM_CATEGORIA;
			$cabecera->cuota 							=   0;
			$cabecera->empresa_id 	 					=   Session::get('empresas')->COD_EMPR;
			$cabecera->fecha_crea 	 					=   date('Y-m-d h:i:s');
			$cabecera->usuario_crea 					=   Session::get('usuario')->id;
			$cabecera->save();

        }

		return 	1;
	}






	private function bn_array_periodo_bono($cuota_id)
	{
	    $array 				= 		WEBCuota::orderBy('WEB.cuotas.codigo','desc')
	    							->where('id','<>',$cuota_id)
	    							->selectRaw("WEB.cuotas.codigo + ' ' + WEB.cuotas.anio +'-'+WEB.cuotas.mes as nombre,id")
	    							->pluck('nombre','id')
	    							->toArray();
		return $array;
	}


	private function bn_array_mes_periodo()
	{
	    $array_anio 		= 		CONPeriodo::orderBy('CON.PERIODO.COD_MES','desc')
	    							->pluck('COD_MES','COD_MES')
	    							->toArray();
		return $array_anio;
	}


	private function bn_array_anio_periodo()
	{

	    $array_anio 		= 		CONPeriodo::orderBy('CON.PERIODO.COD_ANIO','desc')
	    							->pluck('COD_ANIO','COD_ANIO')
	    							->toArray();

		return $array_anio;
	}

	private function bn_array_jefe_venta()
	{

	    $array 				= 		CMPCategoria::where('TXT_GRUPO','=','JEFE_VENTA')
	    							->where('IND_OPERACION_AUTO','=','1')
	    							->orderBy('CMP.CATEGORIA.NOM_CATEGORIA','asc')
	    							->pluck('NOM_CATEGORIA','COD_CATEGORIA')
	    							->toArray();

		return $array;
	}

	private function bn_array_canal()
	{

	    $array 				= 		CMPCategoria::where('TXT_GRUPO','=','CANAL_VENTA')
	    							->where('COD_ESTADO','=','1')
	    							->orderBy('CMP.CATEGORIA.NOM_CATEGORIA','asc')
	    							->pluck('NOM_CATEGORIA','COD_CATEGORIA')
	    							->toArray();

		return $array;
	}


	private function bn_array_sub_canal($canal_id)
	{

		$relacion 			= 		CMPCategoriaRelacion::where('COD_CATEGORIA_SUP','=',$canal_id)
									->where('TXT_GRUPO','=','CANAL_VENTA')	    							
									->pluck('COD_CATEGORIA')
	    							->toArray();




	    $array 				= 		CMPCategoria::where('TXT_GRUPO','=','SUB_CANAL_VENTA')
	    							->where('COD_ESTADO','=','1')
	    							->whereIn('COD_CATEGORIA',$relacion)
	    							->orderBy('CMP.CATEGORIA.NOM_CATEGORIA','asc')
	    							->pluck('NOM_CATEGORIA','COD_CATEGORIA')
	    							->toArray();

		return $array;
	}




	private function bn_generacion_combo_array($titulo, $todo , $array)
	{
		if($todo=='TODO'){
			$combo_anio_pc  		= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo_anio_pc  		= 	array('' => $titulo) + $array;
		}
	    return $combo_anio_pc;
	}

	private function bn_lista_bonos($anio)
	{

		$listabonos 	= 	WEBCuota::where('anio','=',$anio)->get();

		return $listabonos;

	}




}