<?php
namespace App\Biblioteca;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Hashids,Session,Redirect,table;
use App\WEBRolOpcion,App\WEBListaCliente,App\STDTipoDocumento,App\WEBPrecioProducto,App\WEBReglaProductoCliente;
use App\WEBRegla,App\WEBUserEmpresaCentro,App\WEBPrecioProductoContrato,App\CMPCategoria,App\WEBPedido;
use App\WEBPrecioProductoContratoHistorial,App\WEBPrecioProductoHistorial,App\CMPOrden,App\CMPDetalleProducto,App\WEBDetallePedido;
use App\STDEmpresa,App\ALMCentro,App\STDEmpresaDireccion,App\CMPDocumentoCtble,App\WEBDocDoc;
use App\WEBDetalleDocumentoAsociados,App\WEBReglaCreditoCliente,App\WEBDetalleOrdenDespacho,App\WEBViewDetalleOrdenDespacho;
use App\WEBListaClienteTodo,App\STDTrabajador,App\WEBLISTASERIE;
use App\WEBOrdenDespacho;
use App\ALMProducto;
use App\CMPAprobarDoc;
use App\WEBCategoriaActivoFijo;
use App\WEBActivoFijo;
use App\WEBAsignarRegla;
use App\WEBEstado;

use App\WEBPlanillaComision;
use App\WEBDetallePlanillaComision;
use App\STDRepresentanteVentaCuotaComision;
use App\CONPeriodo;

use App\User;
use Keygen;
use PDO;

class Funcion{


	public function consolidadovendedorcomision_jefe($periodoinicio,$periodofin,$vendedor_id){


			$proviene  				=	'MERCADO MAYORISTA';

			$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodoinicio)->first();
			$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodofin)->first();

			$fi 					= 	date_format(date_create(date($periodoinicio->FEC_INICIO)), 'Ymd');
			$ff 					= 	date_format(date_create(date($periodofin->FEC_FIN)), 'Ymd');

			$periodo_array 			=   CONPeriodo::where('COD_EMPR','=','IACHEM0000007086')
		    							->where('CON.PERIODO.FEC_INICIO','>=',$fi)
		    							->where('CON.PERIODO.FEC_FIN','<=',$ff)
										->pluck('COD_PERIODO')
										->toArray();

			// subfamilia
			$subfamilia 			=   WEBDetallePlanillaComision::whereIn('COD_PERIODO',$periodo_array)
										->Vendedor($vendedor_id)
										->where('TXT_PROVIENE','=','MERCADO MAYORISTA')
										->where('TXT_DESCRIPCION','=','DETALLE')
										->where('CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%') //PARAMETRO FALTA
										->where('VAL','=','CANCELADO')
										->select('CAT_INF_NOM_CATEGORIA')
										->groupBy('CAT_INF_NOM_CATEGORIA')
										->get();



			///////////////////////////********************************/////////////////////////
			$array_tabla_comisiones 	=	array();

			//fila de uno
		    $array_nuevo_comision 	=	array();
			$array_nuevo_comision    =	array(
				"item0" 		=>  'COMISION COMO JEFE',
				"colspan0" 		=>  '5',
				"bacgraound0" 	=>  'tablaho',
				"negrita0" 		=>  'negrita',			
				"item1" 		=>  'COMISION COMO JEFE',
				"colspan1" 		=>  '-1',
				"bacgraound1" 	=>  'tablaho',
				"negrita1" 		=>  'negrita',
				"item2" 		=>  'COMISION COMO JEFE',
				"colspan2" 		=>  '-1',
				"negrita2" 		=>  'negrita',
				"bacgraound2" 	=>  'tablaho',
				"item3" 		=>  'COMISION COMO JEFE',
				"colspan3" 		=>  '-1',
				"negrita3" 		=>  'negrita',
				"bacgraound3" 	=>  'tablaho',
				"item3" 		=>  'COMISION COMO JEFE',
				"colspan4" 		=>  '-1',
				"negrita4" 		=>  'negrita',
				"bacgraound4" 	=>  'tablaho',

			);

			$contador = 5;
			$cantidad = 5;

	    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);
			array_push($array_tabla_comisiones,$array_nuevo_comision);

			//fila dos
		    $array_nuevo_comision 	=	array();
			$array_nuevo_comision    =	array(
				"item0" 		=>  'Suma de Σ Comisión',
				"colspan0" 		=>  '5',
				"bacgraound0" 	=>  'tablaho',
				"negrita0" 		=>  'negrita',			
				"item1" 		=>  'Suma de Σ Comisión',
				"colspan1" 		=>  '-1',
				"bacgraound1" 	=>  'tablaho',
				"negrita1" 		=>  'negrita',
				"item2" 		=>  'Suma de Σ Comisión',
				"colspan2" 		=>  '-1',
				"negrita2" 		=>  'negrita',
				"bacgraound2" 	=>  'tablaho',
				"item3" 		=>  'Suma de Σ Comisión',
				"colspan3" 		=>  '-1',
				"negrita3" 		=>  'negrita',
				"bacgraound3" 	=>  'tablaho',
				"item4" 		=>  'Suma de Σ Comisión',
				"colspan4" 		=>  '-1',
				"negrita4" 		=>  'negrita',
				"bacgraound4" 	=>  'tablaho',

			);

			$contador = 5;
			$cantidad = 5;

	    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

			array_push($array_tabla_comisiones,$array_nuevo_comision);

			//fila tres
		    $array_nuevo_comision 	=	array();
			$array_nuevo_comision    =	array(
				"item0" 		=>  'JEFE',
				"negrita0" 		=>  'negrita',
				"bacgraound0" 	=>  'tablaho',

				"item1" 		=>  'VENDEDOR',
				"bacgraound1" 	=>  'tablaho',
				"negrita1" 		=>  'negrita',			
				"item2" 		=>  'AÑO',
				"bacgraound2" 	=>  'tablaho',
				"negrita2" 		=>  'negrita',
				"item3" 		=>  'MES',
				"negrita3" 		=>  'negrita',
				"bacgraound3" 	=>  'tablaho',
				"item4" 		=>  'TOTAL',
				"negrita4" 		=>  'negrita',
				"bacgraound4" 	=>  'tablamar',

			);

			$contador = 5;
			$cantidad = 5;

	    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

			array_push($array_tabla_comisiones,$array_nuevo_comision);

			$detalle 			=   	WEBDetallePlanillaComision::from('WEB.detalleplanillacomisiones AS DP')

										->select(DB::raw('COD_CATEGORIA_JEFE_VENTA,TXT_CATEGORIA_JEFE_VENTA,PER.COD_ANIO,PER.TXT_NOMBRE,DP.TXT_CATEGORIA_JEFE_VENTA_ASIMILADO,
														 sum(DP.TOTAL_COMISION) SUMA_COMISION,sum(DP.PESO_ORDEN_50) SUMA_PESO'))
										->leftjoin('CON.PERIODO AS PER', 'PER.COD_PERIODO', '=', 'DP.COD_PERIODO')

										->whereIn('DP.COD_PERIODO',$periodo_array)
										->Vendedor($vendedor_id)
										->where('DP.TXT_PROVIENE','=','MERCADO MAYORISTA')
										->where('DP.TXT_DESCRIPCION','=','CABECERA')

										->groupBy('DP.COD_CATEGORIA_JEFE_VENTA')
										->groupBy('DP.TXT_CATEGORIA_JEFE_VENTA')
										->groupBy('DP.TXT_CATEGORIA_JEFE_VENTA_ASIMILADO')

										->groupBy('PER.COD_ANIO')
										->groupBy('PER.TXT_NOMBRE')
										->orderBy('DP.TXT_CATEGORIA_JEFE_VENTA', 'ASC')
										->orderBy('PER.COD_ANIO', 'ASC')
										->orderBy('PER.TXT_NOMBRE', 'ASC')
										->get();


		    foreach($detalle as $index => $item1){

			    $array_nuevo_comision 	=	array();
				$contador = 0;
				$cantidad = 0;

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_CATEGORIA_JEFE_VENTA,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_CATEGORIA_JEFE_VENTA_ASIMILADO,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->COD_ANIO,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_NOMBRE,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->SUMA_COMISION,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

		    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);
				array_push($array_tabla_comisiones,$array_nuevo_comision);
		    }


	    return $array_tabla_comisiones;

	}



	public function reportecomisionperidos_jefe($periodoinicio,$periodofin,$vendedor_id){


			$proviene  				=	'MERCADO MAYORISTA';

			$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodoinicio)->first();
			$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodofin)->first();

			$fi 					= 	date_format(date_create(date($periodoinicio->FEC_INICIO)), 'Ymd');
			$ff 					= 	date_format(date_create(date($periodofin->FEC_FIN)), 'Ymd');

			$periodo_array 			=   CONPeriodo::where('COD_EMPR','=','IACHEM0000007086')
		    							->where('CON.PERIODO.FEC_INICIO','>=',$fi)
		    							->where('CON.PERIODO.FEC_FIN','<=',$ff)
										->pluck('COD_PERIODO')
										->toArray();

			///////////////////////////********************************/////////////////////////
			$array_tabla_comisiones 	=	array();

			//fila de uno
		    $array_nuevo_comision 	=	array();
			$array_nuevo_comision    =	array(
				"item0" 		=>  'JEFE',
				"bacgraound0" 	=>  'tablaho',
				"negrita0" 		=>  'negrita',			
				"item1" 		=>  'VENDEDOR',
				"bacgraound1" 	=>  'tablaho',
				"negrita1" 		=>  'negrita',
				"item2" 		=>  'ANIO',
				"negrita2" 		=>  'negrita',
				"bacgraound2" 	=>  'tablaho',
				"item3" 		=>  'MES',
				"negrita3" 		=>  'negrita',
				"bacgraound3" 	=>  'tablaho',
				"item4" 		=>  'SUB FAMILIA',
				"negrita4" 		=>  'negrita',
				"bacgraound4" 	=>  'tablaho',
				"item5" 		=>  'PESO 50KG',
				"negrita5" 		=>  'negrita',
				"bacgraound5" 	=>  'tablaho',
				"item6" 		=>  'COMISION',
				"negrita6" 		=>  'negrita',
				"bacgraound6" 	=>  'tablaho',
				"item7" 		=>  'TOTAL COMISION',
				"negrita7" 		=>  'negrita',
				"bacgraound7" 	=>  'tablamar',

			);

			$contador = 8;
			$cantidad = 8;

	    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

			array_push($array_tabla_comisiones,$array_nuevo_comision);


			$detalle 			=   	WEBDetallePlanillaComision::from('WEB.detalleplanillacomisiones AS DP')
										->select(DB::raw('DP.TXT_CATEGORIA_JEFE_VENTA,DP.TXT_CATEGORIA_JEFE_VENTA_ASIMILADO,PER.COD_ANIO,PER.TXT_NOMBRE,DP.CAT_INF_NOM_CATEGORIA,DP.PESO_ORDEN_50,DP.COMISION,DP.TOTAL_COMISION'))
										->leftjoin('CON.PERIODO AS PER', 'PER.COD_PERIODO', '=', 'DP.COD_PERIODO')
										->whereIn('DP.COD_PERIODO',$periodo_array)
										->Vendedor($vendedor_id)
										//->where('DP.COD_CATEGORIA_JEFE_VENTA','=',$vendedor_id)
										->where('DP.TXT_PROVIENE','=','MERCADO MAYORISTA')
										->where('DP.TXT_DESCRIPCION','=','CABECERA')
										->orderBy('DP.TXT_CATEGORIA_JEFE_VENTA', 'ASC')
										->orderBy('DP.TXT_CATEGORIA_JEFE_VENTA_ASIMILADO', 'ASC')
										->orderBy('PER.FEC_INICIO', 'ASC')
										->get();


		    foreach($detalle as $index => $item1){

			    $array_nuevo_comision 	=	array();
				$contador = 0;
				$cantidad = 0;

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_CATEGORIA_JEFE_VENTA,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_CATEGORIA_JEFE_VENTA_ASIMILADO,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	


	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->COD_ANIO,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_NOMBRE,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->CAT_INF_NOM_CATEGORIA,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->PESO_ORDEN_50,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	


	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->COMISION,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TOTAL_COMISION,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;


		    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);
				array_push($array_tabla_comisiones,$array_nuevo_comision);
		    }


	    return $array_tabla_comisiones;

	}



	public function consolidadovendedorcomision($periodoinicio,$periodofin,$vendedor_id){


			$proviene  				=	'MERCADO MAYORISTA';

			$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodoinicio)->first();
			$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodofin)->first();

			$fi 					= 	date_format(date_create(date($periodoinicio->FEC_INICIO)), 'Ymd');
			$ff 					= 	date_format(date_create(date($periodofin->FEC_FIN)), 'Ymd');

			$periodo_array 			=   CONPeriodo::where('COD_EMPR','=','IACHEM0000007086')
		    							->where('CON.PERIODO.FEC_INICIO','>=',$fi)
		    							->where('CON.PERIODO.FEC_FIN','<=',$ff)
										->pluck('COD_PERIODO')
										->toArray();

			// subfamilia
			$subfamilia 			=   WEBDetallePlanillaComision::whereIn('COD_PERIODO',$periodo_array)
										->Vendedor($vendedor_id)
										->where('TXT_PROVIENE','=','MERCADO MAYORISTA')
										->where('TXT_DESCRIPCION','=','DETALLE')
										->where('CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%') //PARAMETRO FALTA
										->where('VAL','=','CANCELADO')
										->select('CAT_INF_NOM_CATEGORIA')
										->groupBy('CAT_INF_NOM_CATEGORIA')
										->get();



			///////////////////////////********************************/////////////////////////
			$array_tabla_comisiones 	=	array();

			//fila de uno
		    $array_nuevo_comision 	=	array();
			$array_nuevo_comision    =	array(
				"item0" 		=>  'COMISION COMO VENDEDOR',
				"colspan0" 		=>  '4',
				"bacgraound0" 	=>  'tablaho',
				"negrita0" 		=>  'negrita',			
				"item1" 		=>  'COMISION COMO VENDEDOR',
				"colspan1" 		=>  '-1',
				"bacgraound1" 	=>  'tablaho',
				"negrita1" 		=>  'negrita',
				"item2" 		=>  'COMISION COMO VENDEDOR',
				"colspan2" 		=>  '-1',
				"negrita2" 		=>  'negrita',
				"bacgraound2" 	=>  'tablaho',
				"item3" 		=>  'COMISION COMO VENDEDOR',
				"colspan3" 		=>  '-1',
				"negrita3" 		=>  'negrita',
				"bacgraound3" 	=>  'tablaho',
			);

			$contador = 4;
			$cantidad = 4;

	    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

			array_push($array_tabla_comisiones,$array_nuevo_comision);



			//fila dos
		    $array_nuevo_comision 	=	array();
			$array_nuevo_comision    =	array(
				"item0" 		=>  'Suma de Σ Comisión',
				"colspan0" 		=>  '4',
				"bacgraound0" 	=>  'tablaho',
				"negrita0" 		=>  'negrita',			
				"item1" 		=>  'Suma de Σ Comisión',
				"colspan1" 		=>  '-1',
				"bacgraound1" 	=>  'tablaho',
				"negrita1" 		=>  'negrita',
				"item2" 		=>  'Suma de Σ Comisión',
				"colspan2" 		=>  '-1',
				"negrita2" 		=>  'negrita',
				"bacgraound2" 	=>  'tablaho',
				"item3" 		=>  'Suma de Σ Comisión',
				"colspan3" 		=>  '-1',
				"negrita3" 		=>  'negrita',
				"bacgraound3" 	=>  'tablaho',
			);

			$contador = 4;
			$cantidad = 4;

	    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

			array_push($array_tabla_comisiones,$array_nuevo_comision);

			//fila tres
		    $array_nuevo_comision 	=	array();
			$array_nuevo_comision    =	array(
				"item0" 		=>  'VENDEDOR',
				"bacgraound0" 	=>  'tablaho',
				"negrita0" 		=>  'negrita',			
				"item1" 		=>  'AÑO',
				"bacgraound1" 	=>  'tablaho',
				"negrita1" 		=>  'negrita',
				"item2" 		=>  'MES',
				"negrita2" 		=>  'negrita',
				"bacgraound2" 	=>  'tablaho',
				"item3" 		=>  'TOTAL',
				"negrita3" 		=>  'negrita',
				"bacgraound3" 	=>  'tablamar',
			);

			$contador = 4;
			$cantidad = 4;

	    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

			array_push($array_tabla_comisiones,$array_nuevo_comision);

			$detalle 			=   	WEBDetallePlanillaComision::from('WEB.detalleplanillacomisiones AS DP')

										->select(DB::raw('COD_CATEGORIA_JEFE_VENTA,TXT_CATEGORIA_JEFE_VENTA,PER.COD_ANIO,PER.TXT_NOMBRE,
														 sum(DP.TOTAL_COMISION) SUMA_COMISION,sum(DP.PESO_ORDEN_50) SUMA_PESO'))
										->leftjoin('CON.PERIODO AS PER', 'PER.COD_PERIODO', '=', 'DP.COD_PERIODO')
										->whereIn('DP.COD_PERIODO',$periodo_array)
										->Vendedor($vendedor_id)
										//->where('DP.COD_CATEGORIA_JEFE_VENTA','=',$vendedor_id)
										->where('DP.TXT_PROVIENE','=','MERCADO MAYORISTA')
										->where('DP.TXT_DESCRIPCION','=','DETALLE')
										->where('DP.CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%') //PARAMETRO FALTA
										->where('DP.VAL','=','CANCELADO')
										->groupBy('DP.COD_CATEGORIA_JEFE_VENTA')
										->groupBy('DP.TXT_CATEGORIA_JEFE_VENTA')
										->groupBy('PER.COD_ANIO')
										->groupBy('PER.TXT_NOMBRE')
										->orderBy('DP.TXT_CATEGORIA_JEFE_VENTA', 'ASC')
										->orderBy('PER.COD_ANIO', 'ASC')
										->orderBy('PER.TXT_NOMBRE', 'ASC')
										->get();


		    foreach($detalle as $index => $item1){

			    $array_nuevo_comision 	=	array();
				$contador = 0;
				$cantidad = 0;

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_CATEGORIA_JEFE_VENTA,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;		
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->COD_ANIO,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_NOMBRE,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->SUMA_COMISION,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

		    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);
				array_push($array_tabla_comisiones,$array_nuevo_comision);
		    }


	    return $array_tabla_comisiones;

	}



	public function reportecomisionperidos($periodoinicio,$periodofin,$vendedor_id){


			$proviene  				=	'MERCADO MAYORISTA';

			$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodoinicio)->first();
			$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodofin)->first();

			$fi 					= 	date_format(date_create(date($periodoinicio->FEC_INICIO)), 'Ymd');
			$ff 					= 	date_format(date_create(date($periodofin->FEC_FIN)), 'Ymd');

			$periodo_array 			=   CONPeriodo::where('COD_EMPR','=','IACHEM0000007086')
		    							->where('CON.PERIODO.FEC_INICIO','>=',$fi)
		    							->where('CON.PERIODO.FEC_FIN','<=',$ff)
										->pluck('COD_PERIODO')
										->toArray();

			// subfamilia
			$subfamilia 			=   WEBDetallePlanillaComision::whereIn('COD_PERIODO',$periodo_array)
										->Vendedor($vendedor_id)
										->where('TXT_PROVIENE','=','MERCADO MAYORISTA')
										->where('TXT_DESCRIPCION','=','DETALLE')
										->where('CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%') //PARAMETRO FALTA
										->where('VAL','=','CANCELADO')
										->select('CAT_INF_NOM_CATEGORIA')
										->groupBy('CAT_INF_NOM_CATEGORIA')
										->get();



			///////////////////////////********************************/////////////////////////
			$array_tabla_comisiones 	=	array();

			//fila de ZONA
		    $array_nuevo_comision 	=	array();
			$array_nuevo_comision    =	array(
				"item0" 		=>  'INFORMACION',
				"colspan0" 		=>  '4',
				"bacgraound0" 	=>  'tablaho',
				"negrita0" 		=>  'negrita',			
				"item1" 		=>  'INFORMACION',
				"colspan1" 		=>  '-1',
				"bacgraound1" 	=>  'tablaho',
				"negrita1" 		=>  'negrita',
				"item2" 		=>  'INFORMACION',
				"colspan2" 		=>  '-1',
				"negrita2" 		=>  'negrita',
				"bacgraound2" 	=>  'tablaho',
				"item3" 		=>  'INFORMACION',
				"colspan3" 		=>  '-1',
				"negrita3" 		=>  'negrita',
				"bacgraound3" 	=>  'tablaho',
			);
			$contador = 4;
			$cantidad = 4;

		    foreach($subfamilia as $index => $item){

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item->CAT_INF_NOM_CATEGORIA,
							);
				$array_nuevo_comision = $array_nuevo_comision + array(
								"colspan".$contador 			=> 2,
							);
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"negrita".$contador 			=> 'negrita',
							);
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"bacgraound".$contador 			=> 'tablaho',
							);


				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item->CAT_INF_NOM_CATEGORIA,
							);
				$array_nuevo_comision = $array_nuevo_comision + array(
								"colspan".$contador 			=> -1,
							);

				$contador = $contador + 1;
				$cantidad = $cantidad + 1;		

		    }

    		$array_nuevo_comision = $array_nuevo_comision + array(
							"item".$contador 			=> 'TOTAL',
							"colspan".$contador 			=> 1,
							"negrita".$contador 			=> 'negrita',
							"bacgraound".$contador 			=> 'tablamar',
						);

			$contador = $contador + 1;
			$cantidad = $cantidad + 1;

    		$array_nuevo_comision = $array_nuevo_comision + array(
							"item".$contador 			=> 'TOTAL',
							"colspan".$contador 			=> 1,
							"negrita".$contador 			=> 'negrita',
							"bacgraound".$contador 			=> 'tablamar',
						);

			$contador = $contador + 1;
			$cantidad = $cantidad + 1;


	    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

			array_push($array_tabla_comisiones,$array_nuevo_comision);

			/*********************************************************** FILA 02 *************************************************/

			//fila de ZONA
		    $array_nuevo_comision 	=	array();
			$array_nuevo_comision    =	array(
				"item0" 		=>  'VENDEDOR',
				"negrita0" 		=>  'negrita',
				"bacgraound0" 	=>  'tablaho',
				"item1" 		=>  'PERIODO',
				"negrita1" 		=>  'negrita',
				"bacgraound1" 	=>  'tablaho',
				"item2" 		=>  'EMPRESA',
				"negrita2" 		=>  'negrita',
				"bacgraound2" 	=>  'tablaho',
				"item3" 		=>  'SUBCANAL',
				"negrita3" 		=>  'negrita',
				"bacgraound3" 	=>  'tablaho',
			);

			$contador = 4;
			$cantidad = 4;


			for ($i = 0; $i < (COUNT($subfamilia)+1); $i++) {

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> 'Σ Saco 50kg',
							);
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"negrita".$contador 			=> 'negrita',
							);

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"bacgraound".$contador 			=> 'tablaho',
							);

				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> 'Σ Comisión',
							);
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"negrita".$contador 			=> 'negrita',
							);
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"bacgraound".$contador 			=> 'tablaho',
							);

				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

			}

	    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);
			array_push($array_tabla_comisiones,$array_nuevo_comision);


			$detalle 			=   	WEBDetallePlanillaComision::from('WEB.detalleplanillacomisiones AS DP')
										->select('COD_CATEGORIA_JEFE_VENTA',
													'TXT_CATEGORIA_JEFE_VENTA',
													'DP.COD_PERIODO',
													'PER.TXT_CODIGO',
													'DP.NOM_EMPR',
													'DP.TXT_CATEGORIA_SUB_CANAL')
										->leftjoin('CON.PERIODO AS PER', 'PER.COD_PERIODO', '=', 'DP.COD_PERIODO')
										->whereIn('DP.COD_PERIODO',$periodo_array)
										->Vendedor($vendedor_id)
										//->where('DP.COD_CATEGORIA_JEFE_VENTA','=',$vendedor_id)
										->where('DP.TXT_PROVIENE','=','MERCADO MAYORISTA')
										->where('DP.TXT_DESCRIPCION','=','DETALLE')
										->where('DP.CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%') //PARAMETRO FALTA
										->where('DP.VAL','=','CANCELADO')
										->groupBy('DP.COD_CATEGORIA_JEFE_VENTA')
										->groupBy('DP.TXT_CATEGORIA_JEFE_VENTA')
										->groupBy('DP.COD_PERIODO')
										->groupBy('PER.TXT_CODIGO')
										->groupBy('DP.NOM_EMPR')
										->groupBy('DP.TXT_CATEGORIA_SUB_CANAL')
										->orderBy('DP.TXT_CATEGORIA_JEFE_VENTA', 'ASC')
										->orderBy('DP.COD_PERIODO', 'ASC')
										->orderBy('DP.NOM_EMPR', 'ASC')
										->get();

			$tododetalle 		=   	WEBDetallePlanillaComision::from('WEB.detalleplanillacomisiones AS DP')
										->whereIn('DP.COD_PERIODO',$periodo_array)
										->Vendedor($vendedor_id)
										//->where('DP.COD_CATEGORIA_JEFE_VENTA','=',$vendedor_id)
										->where('DP.TXT_PROVIENE','=','MERCADO MAYORISTA')
										->where('DP.TXT_DESCRIPCION','=','DETALLE')
										->where('DP.CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%')
										->where('DP.VAL','=','CANCELADO')->get();


			//dd($tododetalle);



			///////////////////////////********************************/////////////////////////

		    foreach($detalle as $index => $item1){

			    $array_nuevo_comision 	=	array();
				$contador = 0;
				$cantidad = 0;

	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_CATEGORIA_JEFE_VENTA,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;		
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_CODIGO,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->NOM_EMPR,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;	
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $item1->TXT_CATEGORIA_SUB_CANAL,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

			    foreach($subfamilia as $index => $item){


			    	$suma_sacos =  	$tododetalle->where('TXT_CATEGORIA_JEFE_VENTA','=',$item1->TXT_CATEGORIA_JEFE_VENTA)
			    					->where('COD_PERIODO','=',$item1->COD_PERIODO)
			    					->where('NOM_EMPR','=',$item1->NOM_EMPR)
			    					->where('TXT_CATEGORIA_SUB_CANAL','=',$item1->TXT_CATEGORIA_SUB_CANAL)
			    					->where('CAT_INF_NOM_CATEGORIA','=',$item->CAT_INF_NOM_CATEGORIA)
			    				  	->sum('PESO_ORDEN_50');

		    		$array_nuevo_comision = $array_nuevo_comision + array(
									"item".$contador 			=> $suma_sacos,
								);

					$contador = $contador + 1;
					$cantidad = $cantidad + 1;

			    	$suma_comision =  $tododetalle->where('TXT_CATEGORIA_JEFE_VENTA','=',$item1->TXT_CATEGORIA_JEFE_VENTA)
			    					->where('COD_PERIODO','=',$item1->COD_PERIODO)
			    					->where('NOM_EMPR','=',$item1->NOM_EMPR)
			    					->where('TXT_CATEGORIA_SUB_CANAL','=',$item1->TXT_CATEGORIA_SUB_CANAL)
			    					->where('CAT_INF_NOM_CATEGORIA','=',$item->CAT_INF_NOM_CATEGORIA)
			    				  	->sum('TOTAL_COMISION');


		    		$array_nuevo_comision = $array_nuevo_comision + array(
									"item".$contador 			=> $suma_comision,
								);
					$contador = $contador + 1;
					$cantidad = $cantidad + 1;		

			    }

		    	$suma_sacos =  	$tododetalle->where('TXT_CATEGORIA_JEFE_VENTA','=',$item1->TXT_CATEGORIA_JEFE_VENTA)
		    					->where('COD_PERIODO','=',$item1->COD_PERIODO)
		    					->where('NOM_EMPR','=',$item1->NOM_EMPR)
		    					->where('TXT_CATEGORIA_SUB_CANAL','=',$item1->TXT_CATEGORIA_SUB_CANAL)
		    				  	->sum('PESO_ORDEN_50');


	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $suma_sacos,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;

		    	$suma_comision =  $tododetalle->where('TXT_CATEGORIA_JEFE_VENTA','=',$item1->TXT_CATEGORIA_JEFE_VENTA)
		    					->where('COD_PERIODO','=',$item1->COD_PERIODO)
		    					->where('NOM_EMPR','=',$item1->NOM_EMPR)
		    					->where('TXT_CATEGORIA_SUB_CANAL','=',$item1->TXT_CATEGORIA_SUB_CANAL)
		    				  	->sum('TOTAL_COMISION');


	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"item".$contador 			=> $suma_comision,
							);
				$contador = $contador + 1;
				$cantidad = $cantidad + 1;
		    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);
				array_push($array_tabla_comisiones,$array_nuevo_comision);
		    }

	    return $array_tabla_comisiones;

	}




	public function cuadro_comision(){


		$listacomision = STDRepresentanteVentaCuotaComision::from('STD.REPRESENTANTE_VENTA_CUOTA_COMISION AS rc')
			->select('rc.COD_RV_CC',
					'C_RV.TXT_REFERENCIA AS CENTRO',
					'C_RV.NRO_ORDEN',
					'C_JV.NOM_CATEGORIA AS JEFE',
					'C_RV.CODIGO_SUNAT AS COMISION_JEFE',
					'C_RV.COD_CATEGORIA AS COD_VENDEDOR',		
					'C_RV.NOM_CATEGORIA AS VENDEDOR', 

					'C_SA.COD_CATEGORIA AS COD_SUBCANAL',
					'C_SA.NOM_CATEGORIA AS SUBCANAL',

					'C_SI.COD_CATEGORIA AS COD_SUBFAMILIA', 
					'C_SI.NOM_CATEGORIA AS SUBFAMILIA',

					'rc.PLAZO_PAGO', 
					'RC.COMISION')
			->leftjoin('CMP.CATEGORIA AS C_RV', 'C_RV.COD_CATEGORIA', '=', 'RC.COD_CATEGORIA_REPVEN')
			->leftjoin('CMP.CATEGORIA AS C_CA', 'C_CA.COD_CATEGORIA', '=', 'RC.COD_CATEGORIA_CANAL')
			->leftjoin('CMP.CATEGORIA AS C_SA', 'C_SA.COD_CATEGORIA', '=', 'RC.COD_CATEGORIA_SUBCANAL')
			->leftjoin('CMP.CATEGORIA AS C_SF', 'C_SF.COD_CATEGORIA', '=', 'RC.COD_CATEGORIA_SUP_FAMILIA')
			->leftjoin('CMP.CATEGORIA AS C_SI', 'C_SI.COD_CATEGORIA', '=', 'RC.COD_CATEGORIA_INF_FAMILIA')
			->leftjoin('CMP.CATEGORIA AS C_JV', 'C_JV.COD_CATEGORIA', '=', 'C_RV.TXT_TIPO_REFERENCIA')

			->where('RC.IND_COMISION', '=', 1)
			->where('RC.COD_ESTADO', '=', 1)
			->where('C_RV.COD_ESTADO', '=', 1)

			->orderBy('C_JV.NOM_CATEGORIA', 'asc')
			->orderBy('C_RV.NRO_ORDEN', 'ASC')

			->orderBy('C_RV.NOM_CATEGORIA', 'ASC')
			->orderBy('C_SA.NOM_CATEGORIA', 'ASC')
			->orderBy('C_SI.NOM_CATEGORIA', 'ASC')
			->get();


		//centro
		$array_centros 		=	array();
	    foreach($listacomision as $index => $item){
	    	$array_nuevo_centro 	=	array();
			$array_nuevo_centro    =	array(
				"CENTRO" 			=> $item->CENTRO,
				"vendedor" 				=> $item->VENDEDOR
			);
			array_push($array_centros,$array_nuevo_centro);
	    }
		$array_centros = 	array_unique($array_centros, SORT_REGULAR);

		//jefe
		$array_jefes 		=	array();
	    foreach($listacomision as $index => $item){
	    	$array_nuevo_jefe 	=	array();

	    	$jefetop = $item->JEFE;
	    	if(is_null($item->JEFE)){
	    		$jefetop = '-';
	    	}

			$array_nuevo_jefe    =	array(
				"JEFE" 					=> $jefetop,
				"VENDEDOR" 				=> $item->VENDEDOR
			);
			array_push($array_jefes,$array_nuevo_jefe);
	    }
		$array_jefes = 	array_unique($array_jefes, SORT_REGULAR);


		//comision jefe
		$array_comision_jefes 		=	array();
	    foreach($listacomision as $index => $item){
	    	$array_nuevo_comision_jefe 		=	array();
			$array_nuevo_comision_jefe    	=	array(
				"COMISION_JEFE" 		=> $item->COMISION_JEFE,
				"VENDEDOR" 				=> $item->VENDEDOR
			);
			array_push($array_comision_jefes,$array_nuevo_comision_jefe);
	    }
		$array_comision_jefes  		= 	array_unique($array_comision_jefes, SORT_REGULAR);



		//vendedores
		$array_vendedores 		=	array();
	    foreach($listacomision as $index => $item){

	    	$array_nuevo_vendedor 	=	array();
			$array_nuevo_vendedor    =	array(
				"cod_vendedor" 			=> $item->COD_VENDEDOR,
				"vendedor" 				=> $item->VENDEDOR
			);
			array_push($array_vendedores,$array_nuevo_vendedor);
	    }
		$array_vendedores = 	array_unique($array_vendedores, SORT_REGULAR);


		//canal
		$array_canal 		=	array();
	    foreach($listacomision as $index => $item){
	    	$array_nuevo_canal 	=	array();
			$array_nuevo_canal    =	array(
				"COD_SUBCANAL" 			=> $item->COD_SUBCANAL,
				"COD_SUBFAMILIA" 		=> $item->COD_SUBFAMILIA,

				"SUBCANAL" 			=> $item->SUBCANAL,
				"SUBFAMILIA" 		=> $item->SUBFAMILIA
			);
			array_push($array_canal,$array_nuevo_canal);
	    }
		$array_canal = 	array_unique($array_canal, SORT_REGULAR);



		///////////////////////////********************************/////////////////////////
		$array_tabla_comisiones 	=	array();

		//fila de ZONA
	    $array_nuevo_comision 	=	array();
		$array_nuevo_comision    =	array(
			"item0" 		=>  'ZONA',
			"colspan0" 		=>  '2',
			"negrita0" 		=>  'negrita',			
			"item1" 		=>  'ZONA',
			"colspan1" 		=>  '-1',
			"negrita1" 		=>  'negrita',			
		);
		$contador = 2;
		$cantidad = 2;

		$arraycantidad = array_count_values(array_column($array_centros, 'CENTRO'));



	    foreach($array_centros as $index => $item){

	    	$itembuscar = $item['CENTRO'];
	    	//verificar cantidad de colspan
	    	if($arraycantidad[$itembuscar]>1){

	    		$nrocolspan = $arraycantidad[$itembuscar];
	    		if($arraycantidad[$itembuscar]==100){
	    			$nrocolspan = -1;
	    		}
				$array_nuevo_comision = $array_nuevo_comision + array(
								"colspan".$contador 			=> $nrocolspan,
							);

				$arraycantidad[$itembuscar] = 100;
	    	}

    		$array_nuevo_comision = $array_nuevo_comision + array(
							"item".$contador 			=> $item['CENTRO'],
						);

    		//negrita
    		$array_nuevo_comision = $array_nuevo_comision + array(
							"negrita".$contador 			=> 'negrita',
						);

			$contador = $contador + 1;
			$cantidad = $cantidad + 1;

	    }
    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

		array_push($array_tabla_comisiones,$array_nuevo_comision);
		//dd($array_tabla_comisiones);

		//fila de JEFE
	    $array_nuevo_comision 	=	array();
		$array_nuevo_comision   =	array(
			"item0" 		=>  'JEFE',
			"item1" 		=>  'JEFE',
			"colspan0" 		=>  '2',
			"colspan1" 		=>  '-1',
			"negrita0" 		=>  'negrita',	
			"negrita1" 		=>  'negrita',
		);
		$contador = 2;

		$arraycantidad = array_count_values(array_column($array_jefes, 'JEFE'));

	    foreach($array_jefes as $index => $item){


	    	$itembuscar = $item['JEFE'];
	    	//verificar cantidad de colspan
	    	if($arraycantidad[$itembuscar]>1){

	    		$nrocolspan = $arraycantidad[$itembuscar];
	    		if($arraycantidad[$itembuscar]==100){
	    			$nrocolspan = -1;
	    		}
				$array_nuevo_comision = $array_nuevo_comision + array(
								"colspan".$contador 			=> $nrocolspan,
							);

				$arraycantidad[$itembuscar] = 100;
	    	}


    		$array_nuevo_comision = $array_nuevo_comision + array(
							"item".$contador 			=> $item['JEFE'],
						);

    		//negrita
    		$array_nuevo_comision = $array_nuevo_comision + array(
							"negrita".$contador 			=> 'negrita',
						);

			$contador = $contador + 1;	

	    }
    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

		array_push($array_tabla_comisiones,$array_nuevo_comision);


		//fila de COMISION JEFE
	    $array_nuevo_comision 	=	array();
		$array_nuevo_comision   =	array(
			"item0" 		=>  'COMISION JF',
			"item1" 		=>  'COMISION JF',
			"colspan0" 		=>  '2',
			"colspan1" 		=>  '-1',
			"negrita0" 		=>  'negrita',	
			"negrita1" 		=>  'negrita',
		);
		$contador = 2;
	    foreach($array_comision_jefes as $index => $item){

	    	$comision_jefe = $item['COMISION_JEFE'];

	    	if($item['COMISION_JEFE'] == 0){
	    		$comision_jefe = '';
	    	}

    		$array_nuevo_comision = $array_nuevo_comision + array(
							"item".$contador 			=> $comision_jefe,
						);
    		//negrita
    		$array_nuevo_comision = $array_nuevo_comision + array(
							"negrita".$contador 			=> 'negrita',
						);

    		$array_nuevo_comision = $array_nuevo_comision + array(
							"center".$contador 			=> 'center',
						);  

			$contador = $contador + 1;	

	    }

    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

		array_push($array_tabla_comisiones,$array_nuevo_comision);


		//fila de vendedores
	    $array_nuevo_comision 	=	array();

		$array_nuevo_comision    =	array(
			"item0" 		=>  'VENDEDOR',
			"item1" 		=>  'VENDEDOR',
			"colspan0" 		=>  '2',
			"colspan1" 		=>  '-1',
			"negrita0" 		=>  'negrita',	
			"negrita1" 		=>  'negrita',

		);
		$contador = 2;
	    foreach($array_vendedores as $index => $item){

    		$array_nuevo_comision = $array_nuevo_comision + array(
							"item".$contador 			=> $item['vendedor'],
						);
    		//negrita
    		$array_nuevo_comision = $array_nuevo_comision + array(
							"negrita".$contador 			=> 'negrita',
						);

    		$array_nuevo_comision = $array_nuevo_comision + array(
							"center".$contador 			=> 'center',
						); 

			$contador = $contador + 1;	

	    }
    	$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);

		array_push($array_tabla_comisiones,$array_nuevo_comision);
		$arraycantidad = array_count_values(array_column($array_canal, 'SUBCANAL'));
		//dd($arraycantidad);
		//fila de detalle
	    foreach($array_canal as $index => $itemc){

	    	$array_nuevo_comision 	=	array();

			$array_nuevo_comision    =	array(
				"item0" 			=> $itemc['SUBCANAL'],
				"item1" 			=> $itemc['SUBFAMILIA'],
				"negrita0" 		=>  'negrita',	
				"negrita1" 		=>  'negrita',
			);
			$contador = 2;

	    	$itembuscar = $itemc['SUBCANAL'];
	    	//verificar cantidad de rowspan
	    	if($arraycantidad[$itembuscar]>1){

	    		$nrocolspan = $arraycantidad[$itembuscar];
	    		if($arraycantidad[$itembuscar]==100){
	    			$nrocolspan = -1;
	    		}
				$array_nuevo_comision = $array_nuevo_comision + array(
								"rowspan0" 			=> $nrocolspan,
							);
	    		$array_nuevo_comision = $array_nuevo_comision + array(
								"center".$contador 			=> 'center',
							); 
				$arraycantidad[$itembuscar] = 100;
	    	}


			//dd($array_nuevo_comision);
	    	foreach($array_vendedores as $index => $item){

				$comision 				= 	STDRepresentanteVentaCuotaComision::from('STD.REPRESENTANTE_VENTA_CUOTA_COMISION AS rc')
											->where('RC.COD_CATEGORIA_SUBCANAL', '=', $itemc['COD_SUBCANAL'])
											->where('RC.COD_CATEGORIA_INF_FAMILIA', '=', $itemc['COD_SUBFAMILIA'])
											->where('RC.COD_CATEGORIA_REPVEN', '=', $item['cod_vendedor'])
											->first();
				if(count($comision)<=0){
		    		$array_nuevo_comision = $array_nuevo_comision + array(
									"item".$contador 			=> '-',
								);
		    		$array_nuevo_comision = $array_nuevo_comision + array(
									"center".$contador 			=> 'center',
								); 		    		

				}else{

					$comision_valor = number_format($comision->COMISION, 2, '.', ',');

		    		$array_nuevo_comision = $array_nuevo_comision + array(
									"item".$contador 			=> $comision_valor,
								);
		    		$array_nuevo_comision = $array_nuevo_comision + array(
									"center".$contador 			=> 'center',
								); 
		    		
				}

	    		$contador = $contador + 1;
			}
			$array_nuevo_comision = $array_nuevo_comision + array("cantidadarray" => $cantidad);
			array_push($array_tabla_comisiones,$array_nuevo_comision);
	    }
	    //dd($array_tabla_comisiones);
	    return $array_tabla_comisiones;

	}



	public function lista_deuda_cliente($cliente_id,$FEC_CORTE) {

		$clasecon = 'CON';
		$tipo_contrato = 'carteradg';
        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC RPS.CMP_SALDO_CUENTA_DOCUMENTO_COMERCIAL_NUEVO 
											@FEC_CORTE = ?,
											@CLIENTE = ?,
											@CLASECON = ?,
											@TIPO_CONTRATO = ?');

        $stmt->bindParam(1, $FEC_CORTE ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $cliente_id  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $clasecon  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $tipo_contrato  ,PDO::PARAM_STR);
        $stmt->execute();

		return $stmt;
	}

	public function total_deuda_cliente($cliente_id,$FEC_CORTE,$tipo_contrato) {

		$deuda = 0.00;

		$clasecon = 'CON';

        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC RPS.CMP_SALDO_CUENTA_DOCUMENTO_COMERCIAL_NUEVO 
											@FEC_CORTE = ?,
											@CLIENTE = ?,
											@CLASECON = ?,
											@TIPO_CONTRATO = ?');

        $stmt->bindParam(1, $FEC_CORTE ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $cliente_id  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $clasecon  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $tipo_contrato  ,PDO::PARAM_STR);
        $stmt->execute();
      	while ($row = $stmt->fetch()){
      		$deuda = $deuda + $row['CAN_SALDO'];
      		if($deuda<=0.1){
      			$deuda = 0;
      		}
      	}

		return $deuda;
	}


	public function etiqueta_obsequio($detallepedido) {

		$etiqueta = '';

		$detalle_pedido 		=   WEBDetallePedido::where('pedido_id','=',$detallepedido->pedido_id)
									->where('ind_producto_obsequio','=',$detallepedido->ind_producto_obsequio)
									->where('activo','=',1)
									->where('estado_id','<>','EPP0000000000005')
									->get();


		if(count($detalle_pedido)>=2){
			$etiqueta = "(".$detallepedido->ind_producto_obsequio.")";
		}


		return $etiqueta;
	}

	public function color_deuda_limite_credito($pedido) {

		$bacgraound = '';
		$deuda_osiris 				=   $this->deuda_total_osiris($pedido);
		$deuda_osyza 				=   $this->deuda_total_oryza($pedido);
		$deuda_actual_pedido        =   $pedido->total;
		$limite_credito				= 	$this->data_regla_limite_credito($pedido->cliente_id);
		$l_c 						=   0;
	    if(count($limite_credito)>0){
	        $l_c = (float)$limite_credito->canlimitecredito;
	    }else{
	    	$l_c = 0;
	    }


		$suma_deudas = $deuda_osiris + $deuda_osyza; 
		$suma_posible = $suma_deudas + $deuda_actual_pedido;

		if($suma_deudas >= $l_c){
			$bacgraound = '#f9d9d9';
		}else{
			if($suma_posible >= $l_c){
				$bacgraound = '#fbd99d';
			}
		}


		return $bacgraound;
	}


	public function deuda_total_oryza($pedido) {
		
		$total 						=	0;	
		$fechaactual 				= 	date('d-m-Y H:i:s');

	    $listapedidos				= 	WEBPedido::where('activo','=',1)
			    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
			    						->whereIn('estado_id', ['EPP0000000000003'])
				    					->where('cliente_id','=',$pedido->cliente_id)
			    						->orderBy('fecha_venta', 'desc')
			    						->get();


		foreach($listapedidos as $index => $item){

			$detalle_pedido 		=   WEBDetallePedido::where('pedido_id','=',$item->id)
	    								->whereIn('estado_id', ['EPP0000000000003','EPP0000000000006'])
										->where('activo','=',1)
										->get();

			foreach($detalle_pedido as $indexd => $itemd){
				$atendido = 0;

				if(is_null($itemd->atendido) || $itemd->atendido== ''){
					$atendido = 0;
				}else{
					$atendido = $itemd->atendido;
				}
				$total = $total + (($itemd->cantidad-$atendido)*$itemd->precio);
			}							


		}

		return $total;

	}


	public function deuda_total_oryza_generado_autorizado($cliente_id) {
		
		$total 						=	0;	
		$fechaactual 				= 	date('d-m-Y H:i:s');

	    $listapedidos				= 	WEBPedido::where('activo','=',1)
			    						->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
			    						->whereIn('estado_id', ['EPP0000000000003','EPP0000000000002'])
				    					->where('cliente_id','=',$cliente_id)
			    						->orderBy('fecha_venta', 'desc')
			    						->get();

		foreach($listapedidos as $index => $item){

			$detalle_pedido 		=   WEBDetallePedido::where('pedido_id','=',$item->id)
	    								->whereIn('estado_id', ['EPP0000000000003','EPP0000000000006','EPP0000000000002'])
										->where('activo','=',1)
										->where('ind_obsequio','=','0')
										->get();

			foreach($detalle_pedido as $indexd => $itemd){
				$atendido = 0;

				if(is_null($itemd->atendido) || $itemd->atendido== ''){
					$atendido = 0;
				}else{
					$atendido = $itemd->atendido;
				}
				$total = $total + (($itemd->cantidad-$atendido)*$itemd->precio);
			}							


		}

		return $total;

	}




	public function deuda_total_osiris($pedido) {
		
		$total 						=	0;	
		$fechaactual 				= 	date('d-m-Y H:i:s');

		$lista_deuda_cliente		= 	$this->lista_saldo_cuenta_documento_todas_empresas($fechaactual,'TCO0000000000068',$pedido->cliente_id,'CON');


	    while ($row = $lista_deuda_cliente->fetch()){
	    	//dd((float)number_format($row['CAN_SALDO'], 2, '.', ','));
	    	$total 						= $total +	(float)number_format($row['CAN_SALDO'], 2, '.', '');	
	    	
	    }

		return $total;

	}

	public function sacos_50_comision($atributa,$codperiodo,$codcategoriajefe,$proviene,$NOM_EMPR,$TXT_CATEGORIA_CANAL_VENTA,$TXT_CATEGORIA_SUB_CANAL,$CAT_INF_NOM_CATEGORIA) {
		
		$total = 0.00; 

		$comision = WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
					->where('TXT_PROVIENE','=',$proviene)
					->where('NOM_EMPR','=',$NOM_EMPR)
					->where('TXT_CATEGORIA_CANAL_VENTA','=',$TXT_CATEGORIA_CANAL_VENTA)
					->where('TXT_CATEGORIA_SUB_CANAL','=',$TXT_CATEGORIA_SUB_CANAL)
					->where('CAT_INF_NOM_CATEGORIA','=',$CAT_INF_NOM_CATEGORIA)
					->where('TXT_DESCRIPCION','=','DETALLE')
					->where('TOTAL_COMISION','>','0')
					->where('CLIENTE', 'NOT Like', '%(NOTA DE CREDITO)%') //PARAMETRO FALTA
					->select(DB::raw('sum('.$atributa.') as TOTAL_COMISION'))
					->first();

		if(count($comision)>0){
			$total = $comision->TOTAL_COMISION;
		}


		return $total;

	}


	public function sacos_50_comision_nc($atributa,$codperiodo,$codcategoriajefe,$proviene,$NOM_EMPR,$TXT_CATEGORIA_CANAL_VENTA,$TXT_CATEGORIA_SUB_CANAL,$CAT_INF_NOM_CATEGORIA) {
		
		$total = 0.00; 

		$comision = WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
					->where('TXT_PROVIENE','=',$proviene)
					->where('NOM_EMPR','=',$NOM_EMPR)
					->where('TXT_CATEGORIA_CANAL_VENTA','=',$TXT_CATEGORIA_CANAL_VENTA)
					->where('TXT_CATEGORIA_SUB_CANAL','=',$TXT_CATEGORIA_SUB_CANAL)
					->where('CAT_INF_NOM_CATEGORIA','=',$CAT_INF_NOM_CATEGORIA)
					->where('TXT_DESCRIPCION','=','DETALLE')
					->where('CLIENTE', 'Like', '%(NOTA DE CREDITO)%') //PARAMETRO FALTA
					//->where('TOTAL_COMISION','>','0')
					->select(DB::raw('sum('.$atributa.') as TOTAL_COMISION'))
					->first();

		if(count($comision)>0){
			$total = $comision->TOTAL_COMISION;
		}


		return $total;

	}

	public function sacos_50_comision_totales($atributa,$codperiodo,$codcategoriajefe,$proviene,$CAT_INF_NOM_CATEGORIA) {
		
		$total = 0.00; 

		$comision = WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
					->where('TXT_PROVIENE','=',$proviene)
					->where('CAT_INF_NOM_CATEGORIA','=',$CAT_INF_NOM_CATEGORIA)
					->where('TXT_DESCRIPCION','=','DETALLE')
					->where('TOTAL_COMISION','>','0')
					->select(DB::raw('sum('.$atributa.') as TOTAL_COMISION'))
					->first();

		if(count($comision)>0){
			$total = $comision->TOTAL_COMISION;
		}


		return $total;

	}

	public function sacos_50_comision_totales_nc($atributa,$codperiodo,$codcategoriajefe,$proviene,$CAT_INF_NOM_CATEGORIA) {
		
		$total = 0.00; 

		$comision = WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
					->where('TXT_PROVIENE','=',$proviene)
					->where('CAT_INF_NOM_CATEGORIA','=',$CAT_INF_NOM_CATEGORIA)
					->where('TXT_DESCRIPCION','=','DETALLE')
					->where('CLIENTE', 'Like', '%(NOTA DE CREDITO)%')
					//->where('TOTAL_COMISION','>','0')
					->select(DB::raw('sum('.$atributa.') as TOTAL_COMISION'))
					->first();

		if(count($comision)>0){
			$total = $comision->TOTAL_COMISION;
		}


		return $total;

	}	

	public function sacos_50_comision_t($atributa,$codperiodo,$codcategoriajefe,$proviene,$NOM_EMPR,$TXT_CATEGORIA_CANAL_VENTA,$TXT_CATEGORIA_SUB_CANAL) {
		
		$total = 0.00; 

		$comision = WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
					->where('TXT_PROVIENE','=',$proviene)
					->where('NOM_EMPR','=',$NOM_EMPR)
					->where('TXT_CATEGORIA_CANAL_VENTA','=',$TXT_CATEGORIA_CANAL_VENTA)
					->where('TXT_CATEGORIA_SUB_CANAL','=',$TXT_CATEGORIA_SUB_CANAL)
					->where('TXT_DESCRIPCION','=','DETALLE')
					->where('TOTAL_COMISION','>','0')
					->select(DB::raw('sum('.$atributa.') as TOTAL_COMISION'))
					->first();

		if(count($comision)>0){
			$total = $comision->TOTAL_COMISION;
		}


		return $total;

	}

	public function sacos_50_comision_t_nc($atributa,$codperiodo,$codcategoriajefe,$proviene,$NOM_EMPR,$TXT_CATEGORIA_CANAL_VENTA,$TXT_CATEGORIA_SUB_CANAL) {
		
		$total = 0.00; 

		$comision = WEBDetallePlanillaComision::where('COD_PERIODO','=',$codperiodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$codcategoriajefe)
					->where('TXT_PROVIENE','=',$proviene)
					->where('NOM_EMPR','=',$NOM_EMPR)
					->where('TXT_CATEGORIA_CANAL_VENTA','=',$TXT_CATEGORIA_CANAL_VENTA)
					->where('TXT_CATEGORIA_SUB_CANAL','=',$TXT_CATEGORIA_SUB_CANAL)
					->where('TXT_DESCRIPCION','=','DETALLE')
					->where('CLIENTE', 'Like', '%(NOTA DE CREDITO)%')
					//->where('TOTAL_COMISION','>','0')
					->select(DB::raw('sum('.$atributa.') as TOTAL_COMISION'))
					->first();

		if(count($comision)>0){
			$total = $comision->TOTAL_COMISION;
		}


		return $total;

	}

	public function estado_comision_general($periodo_id) {

		$nombre_estado = 'ATENDIDO PARCIALMENTE';

		$totales = WEBPlanillaComision::where('COD_PERIODO','=',$periodo_id)
					->get();

		$generado = WEBPlanillaComision::where('COD_PERIODO','=',$periodo_id)
					->where('COD_ESTADO','=','EPP0000000000002')
					->get();

		$autorizado = WEBPlanillaComision::where('COD_PERIODO','=',$periodo_id)
					->where('COD_ESTADO','=','EPP0000000000003')
					->get();

		$ejecutado = WEBPlanillaComision::where('COD_PERIODO','=',$periodo_id)
					->where('COD_ESTADO','=','EPP0000000000004')
					->get();

		if(count($totales) == count($generado)){
			$nombre_estado = 'GENERADO';
		}

		if(count($totales) == count($autorizado)){
			$nombre_estado = 'AUTORIZADO';
		}

		if(count($totales) == count($ejecutado)){
			$nombre_estado = 'EJECUTADO';
		}

		return $nombre_estado;				

	}

	public function nombre_usuario($usuario_id) {
		$nombre = '';
		$usuario 		= 		User::where('id','=',$usuario_id)->first();
		if(count($usuario)>0){
			$nombre = $usuario->nombre;
		}
		return $nombre;				

	}

	public function importe_pagar_comision_vendedor($vendedor_id,$cod_periodo,$proviene) {
		$total = 0.00; 
		if($proviene == 'AUTOSERVICIOSGC'){
			$total = $this->importe_autoservicio($vendedor_id,$cod_periodo,$proviene);
		}

		if($proviene == 'INCENTIVOS'){
			$total = $this->importe_autoservicio($vendedor_id,$cod_periodo,$proviene);
		}

		if($proviene == 'AUTOSERVICIOS'){
			$total = $this->importe_autoservicio($vendedor_id,$cod_periodo,$proviene);
		}
		if($proviene == 'COBRO AUTOSERVICIO'){
			$total = $this->importe_cobro_autoservicio($vendedor_id,$cod_periodo,$proviene);
		}

		if($proviene == 'MERCADO MAYORISTA'){
			$total = $this->importe_mercado_mayorista($vendedor_id,$cod_periodo,$proviene);
		}

		if($proviene == 'MERCADO MAYORISTA FESTIARROZ'){
			$total = $this->importe_mercado_mayorista($vendedor_id,$cod_periodo,$proviene);
		}

		if($proviene == 'PACAS'){
			$total = $this->importe_mercado_mayorista($vendedor_id,$cod_periodo,$proviene);
		}

		return $total;
	}

	public function importe_mercado_mayorista($vendedor_id,$cod_periodo,$proviene) {
		$total = 0.00; 

			$comision = WEBDetallePlanillaComision::where('COD_PERIODO','=',$cod_periodo)
						->where('COD_CATEGORIA_JEFE_VENTA','=',$vendedor_id)
						->where('TXT_PROVIENE','=',$proviene)
						->select(DB::raw('sum(TOTAL_COMISION) as TOTAL_COMISION'))
						->first();

			if(count($comision)>0){
				$total = $comision->TOTAL_COMISION;
			}


		return $total;
	}

	public function importe_cobro_autoservicio($vendedor_id,$cod_periodo,$proviene) {
		$total = 0.00; 

		$comision = WEBDetallePlanillaComision::where('COD_PERIODO','=',$cod_periodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$vendedor_id)
					->where('TXT_PROVIENE','=',$proviene)
					->where('TXT_DESCRIPCION','=','CABECERA')
					->where('PRODUCTO','=','CANCELADO')
					->select(DB::raw('sum(TOTAL_COMISION) as TOTAL_COMISION'))
					->first();

		if(count($comision)>0){
			$total = $comision->TOTAL_COMISION;
		}


		return $total;
	}

	public function importe_autoservicio($vendedor_id,$cod_periodo,$proviene) {
		$total = 0.00; 

		$comision = WEBDetallePlanillaComision::where('COD_PERIODO','=',$cod_periodo)
					->where('COD_CATEGORIA_JEFE_VENTA','=',$vendedor_id)
					->where('TXT_PROVIENE','=',$proviene)
					->where('TXT_DESCRIPCION','=','CABECERA')
					
					->select(DB::raw('sum(CAN_SALDO) as TOTAL_COMISION'))
					->first();

		if(count($comision)>0){
			$total = $comision->TOTAL_COMISION;
		}


		return $total;
	}



	public function producto_asignado_mobil($pedidodespacho_id) {
		$mobil = '';
		$detalleordendespacho = WEBDetalleOrdenDespacho::where('id','=',$pedidodespacho_id)->first();
		if(count($detalleordendespacho)>0){
			$mobil= $detalleordendespacho->grupo_movil;
		}
		return $mobil;
	}


	public function crear_array_producto_muestras($array_detalle_producto,$array_detalle_producto_muestra) {

		//recorre todos los productos y guardo sin repetirse
		$sw=0;
		foreach($array_detalle_producto as $key => $row) {
			$encontro = array_search($row['producto_id'], array_column($array_detalle_producto_muestra, 'producto_id'));
		    if (is_bool($encontro)){
		    	$array = array(				"correlativo"               => $sw,
								            "unidad_medida_id" 			=> $row['unidad_medida_id'],
								            "nombre_unidad_medida" 		=> $row['nombre_unidad_medida'],
								            "presentacion_producto"     => $row['presentacion_producto'],		    		
		    								"producto_id" 				=> $row['producto_id'],
								            "nombre_producto" 			=> $row['nombre_producto'],
								            "muestra"     				=> '0'
								        );
		    	array_push($array_detalle_producto_muestra,$array);
		    	$sw=$sw + 1;
		    }
	    }
        return $array_detalle_producto_muestra;

	}




	public function lista_carro_ingreso_salida($fechainicio,$fechafin,$categoria_estado_carro_id,$tipo_ingreso_id) {


	    $vacio 						=   "";
	    $nulo 						=   Null;
	    $tipooperacion 				=   'BUS';
		$centro_id 					= 	Session::get('centros')->COD_CENTRO;
		$empresa_id 				= 	Session::get('empresas')->COD_EMPR;



	    $activo 					=   1;
	    $idmoneda 					=   'CERO';
	    $cero 						=   0;
	    $uno 						=   1;
	    $idestadoorden 				=   '1CH000000001';
	    $categoria_estado_carro_id 			=   $categoria_estado_carro_id;
	    $opcionfecha 				=   'FO';

    	/*Lista para seleccionar solititud*/
		$stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC ALM.CARRO_INGRESO_SALIDA_LISTAR ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?');

        $stmt->bindParam(1, $tipooperacion ,PDO::PARAM_STR);
        $stmt->bindParam(2, $vacio ,PDO::PARAM_STR);
        $stmt->bindParam(3, $empresa_id ,PDO::PARAM_STR);
        $stmt->bindParam(4, $centro_id ,PDO::PARAM_STR);


        $stmt->bindParam(5, $vacio ,PDO::PARAM_STR);
        $stmt->bindParam(6, $vacio ,PDO::PARAM_STR);
        $stmt->bindParam(7, $vacio ,PDO::PARAM_STR);
        $stmt->bindParam(8, $vacio ,PDO::PARAM_STR);

        $stmt->bindParam(9, $uno ,PDO::PARAM_STR);
        $stmt->bindParam(10, $tipo_ingreso_id ,PDO::PARAM_STR);
        $stmt->bindParam(11, $categoria_estado_carro_id ,PDO::PARAM_STR);
        $stmt->bindParam(12, $vacio ,PDO::PARAM_STR);

        $stmt->bindParam(13, $fechainicio ,PDO::PARAM_STR);
        $stmt->bindParam(14, $fechafin ,PDO::PARAM_STR);
        $stmt->bindParam(15, $activo ,PDO::PARAM_STR);
        $stmt->execute();

        $listacarros = $stmt;
        return $listacarros;

	}



	public function recalcular_grupo_orden_mobil_33_palets($array_detalle_producto,$count_33_paltes){

		$mayor_mobil						= 	$this->mayor_grupo_mobil($array_detalle_producto);
		foreach ($array_detalle_producto as $key => $item) {
			if((float)$item['grupo_movil']==0){
				$array_detalle_producto[$key]['grupo_movil'] = $mayor_mobil + 1;
				$array_detalle_producto[$key]['grupo_orden_movil'] = $count_33_paltes;
			}

		}
		return $array_detalle_producto;
	}



	public function recalcular_las_guias_remision($orden_despacho_id,$mobil_mayor){

		$fechaactual 						= 	date('d-m-Y H:i:s');

		$detalle_orden_despacho 			=	WEBViewDetalleOrdenDespacho::where('ordendespacho_id','=',$orden_despacho_id)
												->where('grupo_movil','=',$mobil_mayor)
												->orderBy('id', 'asc')
												->get();

		if(count($detalle_orden_despacho)>0){

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
					$detalleordendespacho->fecha_mod 		=  	$fechaactual;
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

	}



	public function cambio_estado_parcialmente_terminado($orden_despacho_id){

		$fechaactual 				= 		date('d-m-Y H:i:s');
		$orden_despacho 			= 		WEBOrdenDespacho::where('id','=',$orden_despacho_id)->first();



		//parcialmente
		$count_parcialmente 		= 		WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$orden_despacho_id)
											->where('activo','=','1')
											->where(function ($query){
									            $query->where('nro_serie', '<>', '')
									            ->orWhere('nro_documento', '<>', '');
											})->get();

		if(count($count_parcialmente)>0){
		    $orden_despacho->estado_id 			= 	'EPP0000000000006';
			$orden_despacho->fecha_mod 	 		=   $fechaactual;
			$orden_despacho->usuario_mod 		=   Session::get('usuario')->id;
			$orden_despacho->save();
		}


		//terminado
		$count_terminado 			= 		WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$orden_despacho_id)
											->where('activo','=','1')
											->where('estado_id','=','EPP0000000000002')
											->where('estado_gruia_id','<>','EPP0000000000004')
											->get();

		if(count($count_terminado)<=0){
		    $orden_despacho->estado_id 						= 	'EPP0000000000004';
		    $orden_despacho->ind_notificacion_terminado 	= 	0;
			$orden_despacho->fecha_mod 	 					=   $fechaactual;
			$orden_despacho->usuario_mod 					=   Session::get('usuario')->id;
			$orden_despacho->save();
		}

	}



	public function lista_pedidos_por_empresa_por_centro(){

		$valor 		= 	'0';
		$centro_id 	= 	Session::get('centros')->COD_CENTRO;
		$empresa_id = 	Session::get('empresas')->COD_EMPR;

	    if($empresa_id == 'IACHEM0000007086'){
	    	if($centro_id =='CEN0000000000004' or $centro_id == 'CEN0000000000006'){
	    		$valor = '1';
	    	}
	    }else{
	    	if($empresa_id =='IACHEM0000010394'){
		    	if($centro_id =='CEN0000000000001'){
		    		$valor = '1';
		    	}
	    	}
	    }
	    return $valor;
	}



    public function combo_series($tipodocumento,$primera_letra) {


        $trabajador_sin_sede    =       STDTrabajador::where('COD_TRAB','=',Session::get('usuario')->usuarioosiris_id)
                                        ->where('COD_ESTADO','=',1)->first();


        if(count($trabajador_sin_sede)<= 0){
                $combo_series           =       array('' => "Seleccione Serie (0)");
                return $combo_series;     
        }


        $trabajador             =       STDTrabajador::where('NRO_DOCUMENTO','=',$trabajador_sin_sede->NRO_DOCUMENTO)
                                        ->where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
                                        ->where('COD_ESTADO','=',1)->first();

        if(count($trabajador)<= 0){
                $combo_series           =       array('' => "Seleccione Serie (1)");
                return $combo_series;
        }

        $lista_series           =       WEBLISTASERIE::where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
                                        ->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
                                        ->where('COD_TRAB','=',$trabajador->COD_TRAB)
                                        ->where('COD_CATEGORIA_TIPO_DOCUMENTO','=',$tipodocumento) //PARAMETRO FALTA
                                        ->where('NRO_SERIE', 'like', $primera_letra.'%') //PARAMETRO FALTA
                                        ->pluck('NRO_SERIE','NRO_SERIE')
                                        ->toArray();
                 
        if(count($lista_series)<= 0){
                $combo_series           =       array('' => "Seleccione Serie (2)");
                return $combo_series;
        }else{
                $combo_series           =       array('' => "Serie") + $lista_series;
                return $combo_series;         
        }


                 
    }

	public function ordernar_array_despacho($array_detalle_producto){
	    
		//order array
		$array_grupo_movil 		= array();
		$array_grupo 			= array();
		$array_correlativo 		= array();
		foreach ($array_detalle_producto as $clave=>$empleado){
			$array_grupo_movil[$clave] 		= $empleado["grupo_movil"];
			$array_grupo[$clave] 			= $empleado["grupo"];
			$array_correlativo[$clave] 		= $empleado["correlativo"];
		}
		array_multisort($array_grupo_movil, $array_grupo, $array_correlativo, SORT_ASC, $array_detalle_producto);


		return $array_detalle_producto;
	}


	public function ordernar_array_despacho_33($array_detalle_producto){
	    
		//order array
		$array_grupo_movil 		= array();
		$array_grupo 			= array();
		$array_correlativo 		= array();
		foreach ($array_detalle_producto as $clave=>$empleado){
			$array_grupo_movil[$clave] 		= $empleado["grupo"];
			$array_grupo[$clave] 			= $empleado["palets"];
		}
		array_multisort($array_grupo_movil, $array_grupo, SORT_DESC, $array_detalle_producto);


		return $array_detalle_producto;
	}

	public function ordernar_array_despacho_restante($array_detalle_producto){
	    
		//order array
		$array_grupo_movil 		= array();
		$array_grupo 			= array();
		$array_correlativo 		= array();
		foreach ($array_detalle_producto as $clave=>$empleado){
			$array_grupo_movil[$clave] 		= $empleado["grupo_movil"];
			$array_grupo[$clave] 			= $empleado["correlativo"];
		}
		array_multisort($array_grupo_movil, $array_grupo, SORT_DESC, $array_detalle_producto);

		return $array_detalle_producto;
	}



	public function rowspan_mobil_producto($ordendespacho_id,$grupo_movil){
	    
		
		$descuento 					= 	0;
		$detalle_orden_despacho 	=	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
										->where('activo','=','1')
										->where('grupo_movil','=',$grupo_movil)
										->select(DB::raw('(count(producto_id)-1) as count_descuento'))
										->groupBy('producto_id')
										->get();

		foreach($detalle_orden_despacho as $index => $item){
			$descuento 				=	$descuento + $item->count_descuento;
		}

		return $descuento;
	}


	public function select_data_almacen_lote($producto_id,$almacen_id,$lote_id,$atributo){
	    
	    $lote_id 					=	trim($lote_id);
	    if($lote_id==''){$lote_id = 'XXX';}

       	$lista_almance_lote 		=   $this->lista_almacen_lote($producto_id,$almacen_id,$lote_id);
       	$valor 						= 	0.00;

       	//dd($lista_almance_lote);
		while($row = $lista_almance_lote->fetch())
		{
			$valor 					=   $row[$atributo];
		}
		return $valor;
	}



	public function select_data_almacen_lote_group($producto_id,$almacen_id,$lote_array_id,$atributo){
	    
       	$valor 						= 	0.00;

		for ($i = 0; $i < count($lote_array_id); ++$i){

			$lote_id = $lote_array_id[$i];
	       	$lista_almance_lote 		=   $this->lista_almacen_lote($producto_id,$almacen_id,$lote_id);
			while($row = $lista_almance_lote->fetch())
			{
				$valor 					=   (float)$row[$atributo]+$valor;
			}

		}
		return $valor;
	}




	public function select_almacen_lote_primero($producto_id,$almacen_id){
	    
		$lote_id 					= 	'';
       	$lista_almance_lote 		=   $this->lista_almacen_lote($producto_id,$almacen_id,$lote_id);
       	$sw 						= 	0;

		while($row = $lista_almance_lote->fetch())
		{
			if($sw<1){
				$lote_id 				=   $row['COD_LOTE'];
			}
			$sw=$sw+1;
		}
		return $lote_id;
	}



	public function select_almacen_lote_group_array_lote($producto_id,$almacen_id,$cantidad_atender,$array_select_lote_id){
	    
		$lote_id 					= 	'';
       	$lista_almance_lote 		=   $this->lista_almacen_lote($producto_id,$almacen_id,$lote_id);
		$array_lotes     			=	array();
		$array_lote     			=	array();
		$lotes_id 					=   '';

		$array_lote_id     			=	array();
		$array_lotes_id     		=	array();

		while($row = $lista_almance_lote->fetch())
		{
			if (in_array($row['COD_LOTE'], $array_select_lote_id)) {
				$array_lote				=	array('COD_LOTE' => $row['COD_LOTE'] ,'STK_NETO' => $row['STK_NETO'],'CAN_COSTO' => $row['CAN_COSTO']);
				array_push($array_lotes,$array_lote);
			}
		}


		//flase es de menor a mayor
		$array_lotes 				= 	$this->ordermultidimensionalarray($array_lotes,'STK_NETO',false);
		$cantidad_atender 			=   (float)$cantidad_atender;
		$acumularstock 				= 	0.0;
		$sumastock 					= 	0.0;		
		$stock_neto 				= 	0.0;
		$atender 					= 	0.0;
		$sobrante_atender           =   $cantidad_atender;
		$total           			=   0.0;


		foreach($array_lotes as $key => $row){

			$stock_neto 			=   (float)$row['STK_NETO'];
			$acumularstock 			= 	$stock_neto+$acumularstock;
			$costo 					=   (float)$row['CAN_COSTO'];

			if($acumularstock<=$cantidad_atender or $sumastock<=$cantidad_atender){

				if($sobrante_atender >= $stock_neto){
					$atender 			= 	$stock_neto;
					$sobrante_atender   =   $sobrante_atender - $stock_neto;
				}else{
					$atender 			= 	$sobrante_atender;
					$sobrante_atender   =   0;
				}

                $total                  =   $atender * $costo;
				$array_lote_id			=	array(	'COD_LOTE' 			=> $row['COD_LOTE'] ,
													'CAN_COSTO' 		=> $costo ,
													'STK_NETO' 			=> $row['STK_NETO'] ,
													'TOTAL' 			=> $total ,
													'CANT_ATENDER_LOTE' => $atender
 											);

				array_push($array_lotes_id,$array_lote_id);

			}
			$sumastock 				= 	$stock_neto+$sumastock;
	    }


		return $array_lotes_id;

	}



	public function select_almacen_lote_group($producto_id,$almacen_id,$cantidad_atender){
	    
		$lote_id 					= 	'';
       	$lista_almance_lote 		=   $this->lista_almacen_lote($producto_id,$almacen_id,$lote_id);
		$array_lotes     			=	array();
		$array_lote     			=	array();
		$lotes_id 					=   '';
		$array_lotes_id     		=	array();

		while($row = $lista_almance_lote->fetch())
		{
			$array_lote				=	array('COD_LOTE' => $row['COD_LOTE'] ,'STK_NETO' => $row['STK_NETO']);
			array_push($array_lotes,$array_lote);
		}
		//true es de menor a mayor
		$array_lotes 				= 	$this->ordermultidimensionalarray($array_lotes,'STK_NETO',false);
		$cantidad_atender 			=   (float)$cantidad_atender;
		$acumularstock 				= 	0.0;
		$sumastock 					= 	0.0;		
		$stock_neto 				= 	0.0;

		foreach($array_lotes as $key => $row){
			$stock_neto 			=   (float)$row['STK_NETO'];
			$acumularstock 			= 	$stock_neto+$acumularstock;
			if($acumularstock<=$cantidad_atender or $sumastock<=$cantidad_atender){
				$lotes_id 				=	$row['COD_LOTE'].','.$lotes_id;	
			}
			$sumastock 				= 	$stock_neto+$sumastock;
	    }

	    if($lotes_id<>''){
	    	$lotes_id 					= 	substr($lotes_id, 0, -1);
			$array_lotes_id 			= 	explode(",", $lotes_id);
	    }

		return $array_lotes_id;

	}




	public function select_almacen_unidad_centro($unidad,$ultimo_almacen_id){

		$centro_id 			=  		Session::get('centros')->COD_CENTRO;
		$empresa_id 		= 		Session::get('empresas')->COD_EMPR;
		$almacen_id_sel 	=       $ultimo_almacen_id;


		if($centro_id == 'CEN0000000000001' and $empresa_id == 'IACHEM0000010394'){
			if($unidad == 'SACO'){
				$almacen_id_sel = 'IICHAL0000000034';
			}else{
				if($unidad == 'BOLSA'){
					$almacen_id_sel = 'IICHAL0000000035';
				}	
			}
		}else{
			if($centro_id == 'CEN0000000000006' and $empresa_id == 'IACHEM0000007086'){
				$almacen_id_sel = 'ISBEAL0000000049';
			}else{
				if($centro_id == 'CEN0000000000004' and $empresa_id == 'IACHEM0000007086'){
					$almacen_id_sel = 'ISRJAL0000000038';
				}	
			}

		}
		
		return $almacen_id_sel;

	}


	public function cantidad_mobil_cero($ordendespacho_id){
	    
		$listadetalleordendespacho    =	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)
										->where('activo','=','1')
										->where('grupo_movil','=','0')
										->get();

 	    return count($listadetalleordendespacho);
	}

	public function totales_kilos_palets_tabla($ordendespacho_id,$grupo_movil,$atributo){
	    
	    $total = 0;
		$listadetalleordendespacho    =	WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$ordendespacho_id)->where('activo','=','1')->get();

		foreach($listadetalleordendespacho as $index => $item){
			if($item->grupo_movil == $grupo_movil){
	    		 $total = $total + (float)$item->$atributo;
	    	}
		}

 	    return $total;
	}

	public function totales_kilos_palets($toOrderArray,$grupo_movil,$atributo){
	    
	    $total = 0;
	    foreach($toOrderArray as $key => $row) {

	    	if($row['grupo_movil'] == $grupo_movil){
	    		 $total = $total + (float)$toOrderArray[$key][$atributo];
	    	}
	    } 
 	    return $total;

	}





	public function llenar_array_productos($empresa_cliente_id,$empresa_cliente_nombre,$orden_id,$orden_cen,$fecha_pedido,
										   $fecha_entrega,$producto_id,$nombre_producto,$unidad_medida_id,$nombre_unidad_medida,
										   $cantidad,$kilos,$cantidad_sacos,$palets,$grupo,
										   $grupo_orden,$grupo_movil,$grupo_orden_movil,$correlativo,$tipo_grupo_oc,
										   $presentacion_producto,$centro_atender_id,$centro_atender_txt,
										   $empresa_atender_id,$empresa_atender_txt,$alias_id,$alias_nombre){


		return						array(
											"empresa_cliente_id" 		=> $empresa_cliente_id,
											"empresa_cliente_nombre" 	=> $empresa_cliente_nombre,
											"orden_id" 					=> $orden_id,
											"orden_cen" 				=> $orden_cen,
											"fecha_pedido" 				=> $fecha_pedido,
											"fecha_entrega" 			=> '',
								            "producto_id" 				=> $producto_id,
								            "nombre_producto" 			=> $nombre_producto,
								            "unidad_medida_id" 			=> $unidad_medida_id,
								            "nombre_unidad_medida" 		=> $nombre_unidad_medida,
								            "cantidad" 					=> $cantidad,
								            "kilos" 					=> $kilos,
								            "cantidad_sacos" 			=> $cantidad_sacos,
								            "palets" 					=> $palets,
								            "grupo" 					=> $grupo,
								            "grupo_orden" 				=> $grupo_orden,
								            "grupo_movil" 				=> $grupo_movil,
								            "grupo_orden_movil" 		=> $grupo_orden_movil,
								            "correlativo" 				=> $correlativo,
								            "tipo_grupo_oc" 			=> $tipo_grupo_oc,
								            "presentacion_producto"     => $presentacion_producto,
								            "muestra"     				=> '0',
								            "centro_atender_id"     	=> $centro_atender_id,
								            "centro_atender_txt"     	=> $centro_atender_txt,
								            "empresa_atender_id"     	=> $empresa_atender_id,
								            "empresa_atender_txt"     	=> $empresa_atender_txt,
								            "alias_id"     				=> $alias_id,
								            "alias_nombre"     			=> $alias_nombre
								        );



	}



	public function combo_todas_empresas_servicios(){

		$nombre_empre 				= 	'';
       	$lista_todas_empresas 		=   $this->lista_todas_empresas($nombre_empre);
		$combo_todas_empresas 		=	array();
		$array_todas_empresas 		=	array();
	  	$contador 					= 	1;
	   	$limite 					= 	10;
		$combo_todas_empresas		=	array("VACIO" => "") + $combo_todas_empresas;
		while($row = $lista_todas_empresas->fetch())
		{
			$combo_todas_empresas			=	array($row['COD_EMPR'] => $row['NOM_EMPR']." ".$row['NRO_DOCUMENTO']) + $combo_todas_empresas;
			$contador 						= 	$contador + 1;

			if($contador == $limite ){
				break;
			}
		}
		return $combo_todas_empresas;

	}


	public function combo_todas_empresas(){

		$nombre_empre 				= 	'';
       	$lista_todas_empresas 		=   $this->lista_todas_empresas($nombre_empre);
		$combo_todas_empresas 		=	array();
		$array_todas_empresas 		=	array();
	  	$contador 					= 	1;
	   	$limite 					= 	10;
	   	$data_empresa 				=   $this->data_empresa(Session::get('empresas')->COD_EMPR);
		$combo_todas_empresas		=	array($data_empresa->NOM_EMPR => $data_empresa->NOM_EMPR) + $combo_todas_empresas;
		while($row = $lista_todas_empresas->fetch())
		{
			$combo_todas_empresas			=	array($row['NOM_EMPR'] => $row['NOM_EMPR']) + $combo_todas_empresas;
			$contador 						= 	$contador + 1;

			if($contador == $limite ){
				break;
			}
		}
		return $combo_todas_empresas;

	}

	public function tipo_documento_servicio($categoria_id){

		$tipo_documento_id = '';

		if($categoria_id == ''){
			return $tipo_documento_id;
		}
		$categoria 			= 		CMPCategoria::where('COD_CATEGORIA','=',$categoria_id)->first();
		$tipo_documento_id 	= 		$categoria->COD_TIPO_DOCUMENTO;

		return $tipo_documento_id;				

	}



	public function combo_cuentas_empresa_cliente($empresa_cliente_id){

       	$lista_cuentas 				=   $this->lista_cuentas($empresa_cliente_id);
		$combo_cuentas 				=	array();
		$combo_cuentas				=	array("" => "Seleccione cuenta") + $combo_cuentas;

		

		while($row = $lista_cuentas->fetch())
		{
			$nombre_contrato 		= 	'';
			$contrato_resumido 		=   WEBListaClienteTodo::where('COD_CONTRATO','=',$row['COD_CONTRATO'])->first();
			if(count($contrato_resumido)>0){
				$nombre_contrato 		= 	$contrato_resumido->CONTRATO;
			}else{
				$nombre_contrato 		= 	$row['COD_CONTRATO'];
			}
			$combo_cuentas			=	array($row['COD_CONTRATO'] => $nombre_contrato) + $combo_cuentas;
		}
		return $combo_cuentas;

	}




	public function lista_cuentas($empresa_cliente_id){


        $accion                                         =       'PRO';
		$empresa_id 									= 		Session::get('empresas')->COD_EMPR;
		$centro_id 										= 		Session::get('centros')->COD_CENTRO;
        $estado                                     	=       '1';
        $stmt 											= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.CONTRATO_LISTAR 
        														@IND_TIPO_OPERACION = ?,
        														@COD_EMPR = ?,
        														@COD_CENTRO = ?,
        														@COD_EMPR_CLIENTE = ?,
        														@COD_ESTADO = ?');

        $stmt->bindParam(1, $accion ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa_id  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $centro_id  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $empresa_cliente_id  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $estado  ,PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;

    }


	public function lista_todas_empresas($nombre_empre){

        $vacio                                          =       '';
        $valor_cero                                     =       '0';
        $valor_uno                                     	=       '1';
        $fecha_ilimitada                                =       date_format(date_create('1901-01-01'), 'Y-m-d');
        $accion                                         =       'GEN';
		$empresa_id 									= 		Session::get('empresas')->COD_EMPR;
		$centro_id 										= 		Session::get('centros')->COD_CENTRO;
		$txt_clase 										= 		'CLA0000000000005';

        $stmt 	= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC STD.EMPRESA_LISTAR 
        			@IND_TIPO_OPERACION = ?,@COD_ESTADO = ?,@NOM_EMPR = ?');

        $stmt->bindParam(1, $accion ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $valor_uno  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $nombre_empre  ,PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;

    }



	public function lista_almacen_lote($producto_id,$almacen_id,$lote_id){

        $vacio                                          =       '';
        $valor_cero                                     =       '0';
        $valor_uno                                     	=       '1';
        $fecha_ilimitada                                =       date_format(date_create('1901-01-01'), 'Y-m-d');
        $accion                                         =       'TDO';
		$empresa_id 									= 		Session::get('empresas')->COD_EMPR;
		$centro_id 										= 		Session::get('centros')->COD_CENTRO;
		$txt_clase 										= 		'CLA0000000000005';

        $stmt 	= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC ALM.INVENTARIO_LOTE_LISTAR 
        			@IND_TIPO_OPERACION = ?,@COD_EMPR = ?,@COD_CENTRO = ?,@COD_CATEGORIA_TIPO_ORDEN = ?,@TXT_CATEGORIA_TIPO_ORDEN = ?,
        			@COD_CATEGORIA_MOVIMIENTO_INVENTARIO = ?,@TXT_CATEGORIA_MOVIMIENTO_INVENTARIO = ?,@FECINV_INI = ?,@FECINV_FIN = ?,@COD_ORDEN = ?,
        			@COD_ZONA_COMERCIAL = ?,@COD_ORDEN_EXTERNO = ?,@COD_EMPR_CLIENTE = ?,@TXT_EMPR_CLIENTE = ?,@COD_PRODUCTO = ?,
        			@TXT_PRODUCTO = ?,@COD_VARIEDAD = ?,@TXT_VARIEDAD = ?,@COD_UNIDAD_MEDIDA = ?,@TXT_UNIDAD_MEDIDA = ?,
        			@COD_LOTE = ?,@ESTADO_LOTE = ?,@COD_ALMACEN = ?,@TXT_ALMACEN = ?,@COD_SUBFAMILIA = ?,
        			@TXT_SUBFAMILIA = ?,@TIPO_REFERENCIA = ?,@TXT_REFERENCIA = ?,@IND_STOCK = ?,@COD_CLASE_ALMACEN = ?,
        			@COD_TIPO_ALMACEN = ?,@COD_EMPR_PROVEEDOR_SERV = ?,@COD_EMPR_PROPIETARIO = ?'
        			);


        $stmt->bindParam(1, $accion ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa_id  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $centro_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(4, $vacio  ,PDO::PARAM_STR);                 
        $stmt->bindParam(5, $vacio  ,PDO::PARAM_STR);

        $stmt->bindParam(6, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(7, $vacio  ,PDO::PARAM_STR);
        $stmt->bindParam(8, $fecha_ilimitada ,PDO::PARAM_STR);                   
        $stmt->bindParam(9, $fecha_ilimitada  ,PDO::PARAM_STR);                 
        $stmt->bindParam(10, $vacio  ,PDO::PARAM_STR);

        $stmt->bindParam(11, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(12, $vacio  ,PDO::PARAM_STR);
        $stmt->bindParam(13, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(14, $vacio  ,PDO::PARAM_STR);                 
        $stmt->bindParam(15, $producto_id  ,PDO::PARAM_STR);

        $stmt->bindParam(16, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(17, $vacio  ,PDO::PARAM_STR);
        $stmt->bindParam(18, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(19, $vacio  ,PDO::PARAM_STR);                 
        $stmt->bindParam(20, $vacio  ,PDO::PARAM_STR);

        $stmt->bindParam(21, $lote_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(22, $vacio  ,PDO::PARAM_STR);
        $stmt->bindParam(23, $almacen_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(24, $vacio  ,PDO::PARAM_STR);                 
        $stmt->bindParam(25, $vacio  ,PDO::PARAM_STR);

        $stmt->bindParam(26, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(27, $vacio  ,PDO::PARAM_STR);
        $stmt->bindParam(28, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(29, $valor_uno  ,PDO::PARAM_STR);                 
        $stmt->bindParam(30, $vacio  ,PDO::PARAM_STR);

        $stmt->bindParam(31, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(32, $empresa_id  ,PDO::PARAM_STR);                 
        $stmt->bindParam(33, $empresa_id  ,PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;

    }



	public function combo_almacen_lote($producto_id,$almacen_id){

		$lote_id 					= 	'';
       	$lista_almance_lote 		=   $this->lista_almacen_lote($producto_id,$almacen_id,$lote_id);
		$combo_almacen_lote 		=	array();
		$array_almacen_lote 		=	array();

		while($row = $lista_almance_lote->fetch())
		{
			if ((float)$row['STK_NETO']>0){
				$combo_almacen_lote			=	array($row['COD_LOTE'] => $row['COD_LOTE']) + $combo_almacen_lote ;			
			}
		}
		return $combo_almacen_lote;

	}

	public function lista_almacen($centro_id){

        $vacio                                          =       '';
        $valor_cero                                     =       '0';
        $valor_uno                                     	=       '1';
        $fecha_ilimitada                                =       date_format(date_create('1901-01-01'), 'Y-m-d');
        $accion                                         =       'GEN';
		$empresa_id 									= 		Session::get('empresas')->COD_EMPR;
		$centro_id 										= 		$centro_id;
		$txt_clase 										= 		'CLA0000000000005';

        $stmt 	= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC ALM.ALMACEN_LISTAR 
        			@IND_TIPO_OPERACION = ?,@COD_ALMACEN = ?,@COD_EMPR = ?,@COD_CENTRO = ?,@COD_EMPR_PROPIETARIO = ?,
        			@NOM_ALMACEN = ?,@TXT_ABREVIATURA = ?,@TXT_DIRECCION = ?,@IND_PRODUCCION = ?,@TXT_CLASE = ?,
        			@TXT_TIPO = ?,@CAN_CAPACIDAD = ?,@IND_PILADO = ?,@IND_LOTIZABLE = ?,@IND_RECEPCION = ?,
        			@IND_HU_SE = ?,@INDICE = ?,@COD_ZONA_COMERCIAL = ?,@TXT_ZONA_COMERCIAL = ?,@COD_MOD_SISTEMA = ?,
        			@TXT_MOD_SISTEMA = ?,@COD_ESTADO = ?,@COD_CATEGORIA_CLASE_ALMACEN = ?,@TXT_CATEGORIA_CLASE_ALMACEN = ?,@IND_PROPIO = ?,
        			@IND_TERCERO = ?,@IND_FISICO = ?,@IND_VIRTUAL = ?,@COD_ACTIVO = ?,@TXT_ANIO = ?'
        			);
        $stmt->bindParam(1, $accion ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $vacio  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $empresa_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(4, $centro_id  ,PDO::PARAM_STR);                 
        $stmt->bindParam(5, $vacio  ,PDO::PARAM_STR);

        $stmt->bindParam(6, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(7, $vacio  ,PDO::PARAM_STR);
        $stmt->bindParam(8, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(9, $valor_cero  ,PDO::PARAM_STR);                 
        $stmt->bindParam(10, $txt_clase  ,PDO::PARAM_STR);

        $stmt->bindParam(11, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(12, $valor_cero  ,PDO::PARAM_STR);
        $stmt->bindParam(13, $valor_cero ,PDO::PARAM_STR);                   
        $stmt->bindParam(14, $valor_cero  ,PDO::PARAM_STR);                 
        $stmt->bindParam(15, $valor_cero  ,PDO::PARAM_STR);

        $stmt->bindParam(16, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(17, $valor_cero  ,PDO::PARAM_STR);
        $stmt->bindParam(18, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(19, $vacio  ,PDO::PARAM_STR);                 
        $stmt->bindParam(20, $vacio  ,PDO::PARAM_STR);

        $stmt->bindParam(21, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(22, $valor_uno  ,PDO::PARAM_STR);
        $stmt->bindParam(23, $vacio ,PDO::PARAM_STR);                   
        $stmt->bindParam(24, $vacio  ,PDO::PARAM_STR);                 
        $stmt->bindParam(25, $valor_cero  ,PDO::PARAM_STR);

        $stmt->bindParam(26, $valor_cero ,PDO::PARAM_STR);                   
        $stmt->bindParam(27, $valor_cero  ,PDO::PARAM_STR);
        $stmt->bindParam(28, $valor_cero ,PDO::PARAM_STR);                   
        $stmt->bindParam(29, $valor_uno  ,PDO::PARAM_STR);                 
        $stmt->bindParam(30, $vacio  ,PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
	}

	public function combo_almacen($centro_id,$accion){

		$centro_id 					= 	$centro_id;
		$lista_almacen 				= 	$this->lista_almacen($centro_id);
		$combo_almacen 				=	array();
		$array_almacen 				=	array();
		while($row = $lista_almacen->fetch())
		{

			$cadena_de_texto = $row['NOM_ALMACEN'];

			if($accion == 'TODOS'){
	    		$combo_almacen			=	array($row['COD_ALMACEN'] => $row['NOM_ALMACEN']) + $combo_almacen ;
			}else{
				$cadena_buscada   = 'TRANSITO';
				$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
				//se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
				if($posicion_coincidencia === false) {
					$cadena_buscada   = 'TRANSITO';
	    		}else{
	    			$combo_almacen			=	array($row['COD_ALMACEN'] => $row['NOM_ALMACEN']) + $combo_almacen ;
	    		}
			}


		}
		return $combo_almacen;

	}


	public function ultimo_almacen_id(){

		$centro_id 					= 	Session::get('centros')->COD_CENTRO;
		$lista_almacen 				= 	$this->lista_almacen($centro_id );
		$ultimo_almacen_id 			=	array();

		while($row = $lista_almacen->fetch())
		{
			$ultimo_almacen_id		=	$row['COD_ALMACEN'];
		}
		return $ultimo_almacen_id;

	}



	public function lista_saldo_cuenta_documento($fecha_corte,$tipo_contrato,$cliente_id,$clase_con){

		$empresa_id = Session::get('empresas')->COD_EMPR;

        $stmt 	= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CMP_SALDO_CUENTA_DOCUMENTO 
        			@COD_EMPR = ?,
        			@FEC_CORTE = ?,
        			@TIPO = ?,
        			@CLIENTE = ?,
        			@CLASECON = ?'
        			);
        $stmt->bindParam(1, $empresa_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $fecha_corte  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $tipo_contrato ,PDO::PARAM_STR);                   
        $stmt->bindParam(4, $cliente_id  ,PDO::PARAM_STR);                 
        $stmt->bindParam(5, $clase_con  ,PDO::PARAM_STR); 
        $stmt->execute();
        return $stmt;

	}

	public function lista_saldo_cuenta_documento_todas_empresas($fecha_corte,$tipo_contrato,$cliente_id,$clase_con){

		$empresa_id = Session::get('empresas')->COD_EMPR;

        $stmt 	= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CMP_SALDO_CUENTA_DOCUMENTO 
        			@FEC_CORTE = ?,
        			@TIPO = ?,
        			@CLIENTE = ?,
        			@CLASECON = ?'
        			);                  
        $stmt->bindParam(1, $fecha_corte  ,PDO::PARAM_STR);
        $stmt->bindParam(2, $tipo_contrato ,PDO::PARAM_STR);                   
        $stmt->bindParam(3, $cliente_id  ,PDO::PARAM_STR);                 
        $stmt->bindParam(4, $clase_con  ,PDO::PARAM_STR); 
        $stmt->execute();
        return $stmt;

	}


	public function crearrolwpan($field_grupo, $index, $grupo){

		$sw_crear  =  0;
        //es el primer valor
        if($index == 0){
          	$grupo     =  $field_grupo;
          	$sw_crear  =  1;  
        }else{
        	//es el segundo hasta el fianl valor
            if($field_grupo == $grupo){
                $sw_crear  =  0;
            }else{
                $sw_crear  =  1;
                $grupo     =  $field_grupo;
            }
        }
        
		$array_respuesta 		=	array(
											"sw_crear" 		=> $sw_crear,
											"grupo" 		=> $grupo,
								        );

        return $array_respuesta;

	}


	public function menor_grupo_mobil($toOrderArray){
		$mayor 	=	0;
		foreach($toOrderArray as $key => $row)
		{
		    if(empty($nro))
		    {
		        $nro = (int)$row['grupo_movil'];
		        $mayor = $nro;
		    }
		    else
		    {
		        if((int)$row['grupo_movil'] < $mayor)
		        {
		            $mayor = (int)$row['grupo_movil'];
		        }
		    }
		}
 	    return $mayor; 
	}


	public function mayor_grupo_mobil($toOrderArray){
		$mayor 	=	0;
		foreach($toOrderArray as $key => $row)
		{
		    if(empty($nro))
		    {
		        $nro = (int)$row['grupo_movil'];
		        $mayor = $nro;
		    }
		    else
		    {
		        if((int)$row['grupo_movil'] > $mayor)
		        {
		            $mayor = (int)$row['grupo_movil'];
		        }
		    }
		}
 	    return $mayor; 
	}

	public function countgrupomovil($toOrderArray, $field, $grupo){
		$count 	=	0;
	    foreach($toOrderArray as $key => $row) {
	    	if($grupo == $row[$field]){
	    		$count 	= 	$count + 1;
	    	}
	    } 
 	    return $count; 
	}


	public function modificarmultidimensionalarray($toOrderArray, $field, $valor ,$orden_cen){

	    foreach($toOrderArray as $key => $row) {
	    	if($orden_cen == $row['orden_cen']){
	    		$toOrderArray[$key][$field] = $valor;
	    	}
	    } 
 	    return $toOrderArray; 
	}

	public function agregar_mobil_producto($toOrderArray, $grupo_orden_movil , $grupo_movil){

	    foreach($toOrderArray as $key => $row) {
	    	if($row['grupo_movil'] == 0){
	    		$toOrderArray[$key]['grupo_movil'] = $grupo_movil;
	    		$toOrderArray[$key]['grupo_orden_movil'] = $grupo_orden_movil;
	    	}
	    } 
 	    return $toOrderArray; 
	}
	
	public function agregar_cantidad_mobil_producto($toOrderArray, $grupo_orden_movil , $grupo_movil){

	    foreach($toOrderArray as $key => $row) {
	    	if($row['grupo_orden_movil'] == 0){
	    		$toOrderArray[$key]['grupo_orden_movil'] = $grupo_orden_movil;
	    	}
	    } 
 	    return $toOrderArray; 
	}



	public function modificar_individual_multidimensionalarray($toOrderArray, $field){

	    foreach($toOrderArray as $key => $row) {
	    	$toOrderArray[$key][$field] = "1";
	    } 
 	    return $toOrderArray; 
	}


	public function ordermultidimensionalarray($toOrderArray, $field, $inverse){  
	    $position = array();  
	    $newRow = array();  
	    foreach ($toOrderArray as $key => $row) {  
	            $position[$key]  = $row[$field];  
	            $newRow[$key] = $row;  
	    }  
	    if ($inverse) {  
	        arsort($position);  
	    }  
	    else {  
	        asort($position);  
	    }  
	    $returnArray = array();  
	    foreach ($position as $key => $pos) {       
	        $returnArray[] = $newRow[$key];  
	    }  
	    return $returnArray;  
	}


	public function lista_producto($producto_id){

		$tipo_operacion =  'GEN';
		$empresa_id 	=  Session::get('empresas')->COD_EMPR;


        $stmt 	= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC ALM.PRODUCTO_LISTAR 
        			@IND_TIPO_OPERACION = ?,
        			@COD_PRODUCTO = ?,
        			@COD_EMPR = ?');
        $stmt->bindParam(1, $tipo_operacion ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $producto_id  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $empresa_id  ,PDO::PARAM_STR);                      			
        $stmt->execute();
        return $stmt;

	}



	public function lista_orden($orden_cen,$empresa_id,$centro_id){

		$tipo_operacion = 'LIS';

        $stmt 	= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.ORDEN_LISTAR 
        			@IND_TIPO_OPERACION = ?,
        			@COD_ORDEN = ?,
        			@COD_EMPR = ?,
        			@COD_CENTRO = ?');
        $stmt->bindParam(1, $tipo_operacion ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $orden_cen  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $empresa_id  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $centro_id  ,PDO::PARAM_STR);                       			
        $stmt->execute();
        return $stmt;

	}


	public function lista_orden_cen_detalle($orden_cen_id){

		$tipo_operacion = 'SEL';

		$estado = 1;
        $stmt 	= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.DETALLE_PRODUCTO_LISTAR 
        			@IND_TIPO_OPERACION = ?,
        			@COD_TABLA = ?,
        			@COD_ESTADO = ?');

        $stmt->bindParam(1, $tipo_operacion ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $orden_cen_id  ,PDO::PARAM_STR); 
        $stmt->bindParam(3, $estado  ,PDO::PARAM_STR);                       			
        $stmt->execute();

        return $stmt;

	}


	public function lista_orden_cen($empresa_id,$cliente_id,$centro_id,$fecha_inicio,$fecha_fin){

		$tipo_operacion = 'LIS';
		$tipo_orden_id 	= 'TOR0000000000024';


        $stmt 	= 	DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.ORDEN_LISTAR 
        			@IND_TIPO_OPERACION = ?, 
        			@COD_EMPR = ?, 
        			@COD_CATEGORIA_TIPO_ORDEN = ?, 
        			@COD_EMPR_CLIENTE = ?,
        			@COD_CENTRO = ?, 
        			@FEC_ORDEN = ?, 
        			@FEC_ORDEN_FIN = ?');
        
        $stmt->bindParam(1, $tipo_operacion ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa_id  ,PDO::PARAM_STR);                        			
        $stmt->bindParam(3, $tipo_orden_id ,PDO::PARAM_STR);                           			
        $stmt->bindParam(4, $cliente_id  ,PDO::PARAM_STR);                        		
        $stmt->bindParam(5, $centro_id ,PDO::PARAM_STR);                           			
        $stmt->bindParam(6, $fecha_inicio  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $fecha_fin  ,PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;

	}



	public function data_detalle_producto_sum_cantidad($documento_id,$producto_id) {

		//devuelve las nota de credito asociada a la boletas
		$nota_credito 					=	$this->boleta_o_factura_asociada_nota_credito($documento_id,'TDO0000000000007');
		$array_documentos_id 			= 	$this->colocar_en_array_id_documentos_asociados_foreach($nota_credito);

		$producto    					=   CMPDetalleProducto::where('CMP.DETALLE_PRODUCTO.COD_ESTADO','=',1)
				                            ->whereIn('CMP.DETALLE_PRODUCTO.COD_TABLA',$array_documentos_id)
				                            ->where('CMP.DETALLE_PRODUCTO.COD_PRODUCTO','=',$producto_id)
				                            ->select(DB::raw('sum(CAN_PRODUCTO) as CAN_PRODUCTO, COD_PRODUCTO'))
				                            ->groupBy('CMP.DETALLE_PRODUCTO.COD_PRODUCTO')
				                            ->first();

		return $producto;

	}


	public function ind_faltante_en_boletas_nota_credito($documento_id) {

		//devuelve las nota de credito asociada a la boletas
		$ind_faltante 					= 	'terminada';
		$nota_credito 					=	$this->boleta_o_factura_asociada_nota_credito($documento_id,'TDO0000000000007');
		$array_documentos_id 			= 	$this->colocar_en_array_id_documentos_asociados_foreach($nota_credito);

		//detalle producto de boletas
		$detalle_producto_boleta    	=   CMPDetalleProducto::where('CMP.DETALLE_PRODUCTO.COD_ESTADO','=',1)
			                                ->where('CMP.DETALLE_PRODUCTO.COD_TABLA','=',$documento_id)
			                                ->get();

		foreach($detalle_producto_boleta as $index => $item){

			//detalle producto suma de cantidades
			$producto    				=   CMPDetalleProducto::where('CMP.DETALLE_PRODUCTO.COD_ESTADO','=',1)
				                            ->whereIn('CMP.DETALLE_PRODUCTO.COD_TABLA',$array_documentos_id)
				                            ->where('CMP.DETALLE_PRODUCTO.COD_PRODUCTO','=',$item->COD_PRODUCTO)
				                            ->select(DB::raw('sum(CAN_PRODUCTO) as CAN_PRODUCTO, COD_PRODUCTO'))
				                            ->groupBy('CMP.DETALLE_PRODUCTO.COD_PRODUCTO')
				                            ->first();
			if(count($producto)>0){
				if($item->CAN_PRODUCTO > $producto->CAN_PRODUCTO){
					$ind_faltante 		= 	'parcialmente';
				}
			}else{
				$ind_faltante 			= 	'parcialmente';
			}                        

		}

		return $ind_faltante;

	}



	public function array_boleta_o_factura_asociada_nota_credito($documento_id,$producto_id) {

		$detalle_documento_asociado  = 	WEBDetalleDocumentoAsociados::where('documento_id','=',$documento_id)
										->where('producto_id','=',$producto_id)->first();

		return 	$detalle_documento_asociado;				

	}


	public function data_detalle_documento_asociado($documento_id,$producto_id) {

		$detalle_documento_asociado  = 	WEBDetalleDocumentoAsociados::where('documento_id','=',$documento_id)
										->where('producto_id','=',$producto_id)->first();

		return 	$detalle_documento_asociado;				

	}

	public function nota_credit_referencia_div($nota_credito_id,$tipodocumento) {

		$tipo_operacion = 'GEN';
		$cod_tabla 		= $nota_credito_id;
		$vacio 			= '';
		$estado 		= 1;

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.REFERENCIA_ASOC_LISTAR ?,?,?,?,?,?,?,?,?,?,?');
        $stmt->bindParam(1, $tipo_operacion ,PDO::PARAM_STR);                           //@IND_TIPO_OPERACION='GEN',
        $stmt->bindParam(2, $vacio  ,PDO::PARAM_STR);                        			//@COD_TABLA='',
        $stmt->bindParam(3, $vacio ,PDO::PARAM_STR);                           			//@COD_TIPO_TABLA='',
        $stmt->bindParam(4, $cod_tabla  ,PDO::PARAM_STR);                        		//@COD_TABLA_ASOC='ISLMVR0000006713',
        $stmt->bindParam(5, $vacio ,PDO::PARAM_STR);                           			//@COD_TIPO_TABLA_ASOC='',
        $stmt->bindParam(6, $vacio  ,PDO::PARAM_STR);                        			//@TXT_TABLA='',
        $stmt->bindParam(7, $vacio ,PDO::PARAM_STR);                           			//@TXT_TABLA_ASOC='',
        $stmt->bindParam(8, $vacio  ,PDO::PARAM_STR);                        			//@TXT_GLOSA='',
        $stmt->bindParam(9, $vacio ,PDO::PARAM_STR);                           			//@TXT_TIPO_REFERENCIA='',
        $stmt->bindParam(10, $vacio  ,PDO::PARAM_STR);                       			//@TXT_REFERENCIA='',
        $stmt->bindParam(11, $estado ,PDO::PARAM_STR);                          		//@COD_ESTADO=1,
        $stmt->execute();

		$i 								= 0;
		$array_documentos_id 			= array();
		while($row = $stmt->fetch())
		{
			$array_documentos_id[$i] 	=   $row['COD_TABLA'];
			$i= $i +1;
		}

		$documento_div 		= 	CMPDocumentoCtble::whereIn('COD_DOCUMENTO_CTBLE',$array_documentos_id)
								->where('COD_CATEGORIA_TIPO_DOC','=',$tipodocumento)
								->first();
		return 	$documento_div;	

	}


	public function colocar_en_array_id_documentos_asociados_foreach($lista_documento_asociados){
		$array_documentos_id 			= array();
		foreach($lista_documento_asociados as $index => $item){
			$array_documentos_id[$index] 	=   $item->COD_DOCUMENTO_CTBLE;
		}
		return 	$array_documentos_id;
	}


	public function colocar_en_array_id_documentos_asociados($lista_documento_asociados) {
		$i 								= 0;
		$array_documentos_id 			= array();
		while($row = $lista_documento_asociados->fetch())
		{
			$array_documentos_id[$i] 	=   $row['COD_TABLA'];
			$i= $i +1;
		}
		return 	$array_documentos_id;
	}



	public function lista_documentos_contables_array($array_documentos_id,$tipodocumento) {

		$lista_documento_contable 	= 	CMPDocumentoCtble::whereIn('COD_DOCUMENTO_CTBLE',$array_documentos_id)
										->where('COD_CATEGORIA_TIPO_DOC','=',$tipodocumento)
										->where('COD_EMPR_RECEPTOR','=','IACHEM0000006957')
										->orderBy('CAN_TOTAL', 'desc')
										->get();

		return 	$lista_documento_contable;							

	}


	public function data_documento_ctbl($documento_id) {

		$documento 				= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$documento_id)->first();
        return $documento;

	}


	public function data_producto($producto_id) {

		$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$producto_id)->first();
        return $producto;
	}

	public function data_documento($documento_id) {

		$tipo_operacion = 'SEL';

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.DOCUMENTO_CTBLE_LISTAR ?,?');
        $stmt->bindParam(1, $tipo_operacion ,PDO::PARAM_STR);                       //@IND_TIPO_OPERACION='SEL',
        $stmt->bindParam(2, $documento_id  ,PDO::PARAM_STR);                        //@COD_DOCUMENTO_CTBLE='ISLMGRR000003384',
        $stmt->execute();
        $documento = $stmt->fetch(2);
        return $documento;

	}

	public function lista_referencia_orden_venta($orden_venta_id) {

		$tipo_operacion = 'GEN';
		$cod_tabla 		= $orden_venta_id;
		$vacio 			= '';
		$estado 		= 1;

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.REFERENCIA_ASOC_LISTAR ?,?,?,?,?,?,?,?,?,?,?');
        $stmt->bindParam(1, $tipo_operacion ,PDO::PARAM_STR);                           //@IND_TIPO_OPERACION='GEN',
        $stmt->bindParam(2, $vacio  ,PDO::PARAM_STR);                        			//@COD_TABLA='',
        $stmt->bindParam(3, $vacio ,PDO::PARAM_STR);                           			//@COD_TIPO_TABLA='',
        $stmt->bindParam(4, $cod_tabla  ,PDO::PARAM_STR);                        		//@COD_TABLA_ASOC='ISLMVR0000006713',
        $stmt->bindParam(5, $vacio ,PDO::PARAM_STR);                           			//@COD_TIPO_TABLA_ASOC='',
        $stmt->bindParam(6, $vacio  ,PDO::PARAM_STR);                        			//@TXT_TABLA='',
        $stmt->bindParam(7, $vacio ,PDO::PARAM_STR);                           			//@TXT_TABLA_ASOC='',
        $stmt->bindParam(8, $vacio  ,PDO::PARAM_STR);                        			//@TXT_GLOSA='',
        $stmt->bindParam(9, $vacio ,PDO::PARAM_STR);                           			//@TXT_TIPO_REFERENCIA='',
        $stmt->bindParam(10, $vacio  ,PDO::PARAM_STR);                       			//@TXT_REFERENCIA='',
        $stmt->bindParam(11, $estado ,PDO::PARAM_STR);                          		//@COD_ESTADO=1,
        $stmt->execute();
        return $stmt;

	}


	public function lista_detalle_producto_orden_venta($orden_venta_id,$producto_id) {

		$tipo_operacion = 'SEL';

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.DETALLE_PRODUCTO_LISTAR ?,?,?');
        $stmt->bindParam(1, $tipo_operacion ,PDO::PARAM_STR);                           //@IND_TIPO_OPERACION='SEL',
        $stmt->bindParam(2, $orden_venta_id  ,PDO::PARAM_STR);                        	//@COD_TABLA='ISLMGRR000003384',
        $stmt->bindParam(3, $producto_id  ,PDO::PARAM_STR);                        		//@COD_PRODUCTO='',
        $stmt->execute();
        return $stmt;

	}


	public function lista_detalle_producto_orden_venta_ncm($orden_venta_id,$producto_id) {

		$tipo_operacion = 'NCF';

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.APROBAR_DOC_DETALLE_LISTAR ?,?');
        $stmt->bindParam(1, $tipo_operacion ,PDO::PARAM_STR);                           //@IND_TIPO_OPERACION='SEL',
        $stmt->bindParam(2, $orden_venta_id  ,PDO::PARAM_STR);                        	//@COD_TABLA='ISLMGRR000003384',
        $stmt->execute();
        return $stmt;

	}



	public function lista_orden_venta_array_orden($array_orden) {

		$lista_orden 		= 		CMPOrden::whereIn('CMP.ORDEN.COD_ORDEN',$array_orden)->get();
		return $lista_orden;				

	}


	public function lista_orden_venta_array_orden_autorizadas($array_orden) {

		$lista_orden 		= 		CMPOrden::join('CMP.APROBAR_DOC', 'CMP.ORDEN.COD_ORDEN', '=', 'CMP.APROBAR_DOC.COD_ORDEN')
									->where('CMP.APROBAR_DOC.IND_MASIVO','=',1)
									->where('CMP.APROBAR_DOC.COD_CATEGORIA_ESTADO_DOC','=','EOR0000000000016')
									->where('CMP.APROBAR_DOC.COD_CATEGORIA_TIPO_DOC','=','TDO0000000000007')
									->where('CMP.APROBAR_DOC.COD_ESTADO','=','1')
									->select(DB::raw('CMP.ORDEN.*,CMP.APROBAR_DOC.COD_APROBAR_DOC'))
									->whereIn('CMP.ORDEN.COD_ORDEN',$array_orden)->get();
		return $lista_orden;				

	}

	public function lista_orden_venta_array_orden_reglas($array_orden) {

		$lista_orden 		= 		CMPOrden::whereIn('CMP.ORDEN.COD_ORDEN',$array_orden)
									->get();
		return $lista_orden;				

	}


	public function lista_clientes_jefe_regla($jefe_id) {

		$listaclientes   		=	WEBListaCliente::where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
									->where('COD_CATEGORIA_JEFE_VENTA','=',$jefe_id)
									->get();


		return $listaclientes;				

	}


	public function boleta_o_factura_asociada_nota_credito($documento_id,$tipo_documento_id) {

		//devuelve nota de credito
		$nota_credito 			=	WEBDocDoc::where('COD_CATEGORIA_TIPO_DOC','=',$tipo_documento_id)
									->where('COD_DOCUMENTO_CTBLE_BF','=',$documento_id)
									->get();

		return $nota_credito;
	}



	public function array_orden_venta_documento_fechas_cuenta($tipo_documento_id,$fecha_inicio,$fecha_fin,$cuenta_id) {


		$parametro_1 		= 		'CMP.ORDEN';
		$parametro_2 		= 		'CMP.DOCUMENTO_CTBLE';

		$array_orden 		= 		CMPOrden::join('CMP.REFERENCIA_ASOC', function ($join) use ($parametro_1,$parametro_2){
							            $join->on('CMP.REFERENCIA_ASOC.COD_TABLA', '=', 'CMP.ORDEN.COD_ORDEN')
							            //->where('CMP.REFERENCIA_ASOC.TXT_TABLA','=',$parametro_1)
							            ->whereIn('CMP.REFERENCIA_ASOC.TXT_TABLA',[$parametro_1])
							            ->whereIn('CMP.REFERENCIA_ASOC.TXT_TABLA_ASOC',[$parametro_2])
							            //->where('CMP.REFERENCIA_ASOC.TXT_TABLA_ASOC','=',$parametro_2)
							            ->where('CMP.REFERENCIA_ASOC.COD_ESTADO ','=',1);
							        })
									->join('CMP.DOCUMENTO_CTBLE', function ($join) use ($tipo_documento_id){
							            $join->on('CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'CMP.REFERENCIA_ASOC.COD_TABLA_ASOC')
							            ->where('CMP.DOCUMENTO_CTBLE.COD_ESTADO','=',1)
							            ->where('CMP.DOCUMENTO_CTBLE.COD_CATEGORIA_TIPO_DOC','=',$tipo_documento_id);
							        })
									->join('CMP.CATEGORIA', function ($join) {
							            $join->on('CMP.CATEGORIA.COD_CATEGORIA', '=', 'CMP.ORDEN.COD_CATEGORIA_TIPO_ORDEN')
							            ->where('CMP.CATEGORIA.TXT_GLOSA','=','VENTAS')
							            ->where('CMP.CATEGORIA.COD_ESTADO','=',1);
							        })
									->where('CMP.ORDEN.COD_ESTADO','=',1)        
									->where('CMP.ORDEN.COD_EMPR','=',Session::get('empresas')->COD_EMPR)
									//->where('CMP.ORDEN.FEC_ORDEN','>=',$fecha_inicio) 
									//->where('CMP.ORDEN.FEC_ORDEN','<=',$fecha_fin)
		                            ->where('CMP.ORDEN.COD_CONTRATO','=',$cuenta_id)
		                            //->where('CMP.ORDEN.COD_CATEGORIA_ESTADO_ORDEN','=','EOR0000000000003') // solo ordenes terminadas
		                            ->whereIn('CMP.ORDEN.COD_CATEGORIA_ESTADO_ORDEN',['EOR0000000000003' ,'EOR0000000000018','EOR0000000000012'])
		                            
							        /*->where(function ($query){
					                    $query->where('CMP.ORDEN.COD_CATEGORIA_ESTADO_ORDEN', '=', 'EOR0000000000005')
					                    ->orWhere('CMP.ORDEN.COD_ESTADO', '=', 1);
									})*/
									->groupBy('CMP.ORDEN.COD_ORDEN')
									->pluck('CMP.ORDEN.COD_ORDEN')
									->toArray();


		return $array_orden;
													

	}
	public function array_orden_venta_documento_fechas_cuenta_o($tipo_documento_id,$fecha_inicio,$fecha_fin,$cuenta_id) {


		$parametro_1 		= 		'CMP.ORDEN';
		$parametro_2 		= 		'CMP.DOCUMENTO_CTBLE';

		$orden_aprobados    =		CMPAprobarDoc::where('IND_MASIVO','=','1')
									->where('COD_CATEGORIA_ESTADO_DOC','=','EOR0000000000016')
									->where('COD_CATEGORIA_TIPO_DOC','=','TDO0000000000007')
									->where('COD_ESTADO','=','1')
									->pluck('COD_ORDEN')->toArray();


		$array_orden 		= 		CMPOrden::join('CMP.REFERENCIA_ASOC', function ($join) use ($parametro_1,$parametro_2){
							            $join->on('CMP.REFERENCIA_ASOC.COD_TABLA', '=', 'CMP.ORDEN.COD_ORDEN')
							            //->where('CMP.REFERENCIA_ASOC.TXT_TABLA','=',$parametro_1)
							            ->whereIn('CMP.REFERENCIA_ASOC.TXT_TABLA',[$parametro_1])
							            ->whereIn('CMP.REFERENCIA_ASOC.TXT_TABLA_ASOC',[$parametro_2])
							            //->where('CMP.REFERENCIA_ASOC.TXT_TABLA_ASOC','=',$parametro_2)
							            ->where('CMP.REFERENCIA_ASOC.COD_ESTADO ','=',1);
							        })
									//->whereIn('CMP.ORDEN.COD_ORDEN',$orden_aprobados)
									->join('CMP.DOCUMENTO_CTBLE', function ($join) use ($tipo_documento_id){
							            $join->on('CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'CMP.REFERENCIA_ASOC.COD_TABLA_ASOC')
							            ->where('CMP.DOCUMENTO_CTBLE.COD_ESTADO','=',1)
							            ->where('CMP.DOCUMENTO_CTBLE.COD_CATEGORIA_TIPO_DOC','=',$tipo_documento_id);
							        })
									->join('CMP.CATEGORIA', function ($join) {
							            $join->on('CMP.CATEGORIA.COD_CATEGORIA', '=', 'CMP.ORDEN.COD_CATEGORIA_TIPO_ORDEN')
							            ->where('CMP.CATEGORIA.TXT_GLOSA','=','VENTAS')
							            ->where('CMP.CATEGORIA.COD_ESTADO','=',1);
							        })
									->join('CMP.APROBAR_DOC', 'CMP.ORDEN.COD_ORDEN', '=', 'CMP.APROBAR_DOC.COD_ORDEN')

									->where('CMP.APROBAR_DOC.IND_MASIVO','=',1)
									->where('CMP.APROBAR_DOC.COD_CATEGORIA_ESTADO_DOC','=','EOR0000000000016')
									->where('CMP.APROBAR_DOC.COD_CATEGORIA_TIPO_DOC','=','TDO0000000000007')
									->where('CMP.APROBAR_DOC.COD_ESTADO','=','1')

									->where('CMP.ORDEN.COD_ESTADO','=',1)        
									->where('CMP.ORDEN.COD_EMPR','=',Session::get('empresas')->COD_EMPR)
		                            ->where('CMP.ORDEN.COD_CONTRATO','=',$cuenta_id)
		                            ->whereIn('CMP.ORDEN.COD_CATEGORIA_ESTADO_ORDEN',['EOR0000000000003' ,'EOR0000000000018','EOR0000000000012'])
									->groupBy('CMP.ORDEN.COD_ORDEN')
									->pluck('CMP.ORDEN.COD_ORDEN')
									->toArray();


		return $array_orden;
													
	}

	public function array_orden_venta_documento_fechas_cuenta_regla($tipo_documento_id,$fecha_inicio,$fecha_fin,$cuenta_id) {


		$parametro_1 		= 		'CMP.ORDEN';
		$parametro_2 		= 		'CMP.DOCUMENTO_CTBLE';


	    $array_reglas 		= 		WEBAsignarRegla::where('WEB.asignarreglas.prefijo','=','RDV')
		    						->where('WEB.asignarreglas.activo','=','1')
									->pluck('tabla_id')
									->toArray();


		$array_orden 		= 		CMPOrden::where('CMP.ORDEN.COD_ESTADO','=',1)        
									->where('CMP.ORDEN.COD_EMPR','=',Session::get('empresas')->COD_EMPR)
									->where('CMP.ORDEN.FEC_ORDEN','>=',$fecha_inicio)
									->where('CMP.ORDEN.FEC_ORDEN','<=',$fecha_fin)
		                            ->where('CMP.ORDEN.COD_CONTRATO','=',$cuenta_id)
		                            ->whereNotIn('CMP.ORDEN.COD_ORDEN',$array_reglas)
		                            ->where('TXT_CATEGORIA_TIPO_ORDEN','like','%VENTAS%')
		                            ->whereIn('CMP.ORDEN.COD_CATEGORIA_ESTADO_ORDEN',['EOR0000000000003' ,'EOR0000000000018','EOR0000000000012'])
									->groupBy('CMP.ORDEN.COD_ORDEN')
									->pluck('CMP.ORDEN.COD_ORDEN')
									->toArray();

		return $array_orden;
													
	}
	public function array_orden_venta_documento_fechas_cuenta_regla_nuevo($tipo_documento_id,$cuenta_id) {


		$parametro_1 		= 		'CMP.ORDEN';
		$parametro_2 		= 		'CMP.DOCUMENTO_CTBLE';


	    $array_reglas 		= 		WEBAsignarRegla::where('WEB.asignarreglas.prefijo','=','RDV')
		    						->where('WEB.asignarreglas.activo','=','1')
									->pluck('tabla_id')
									->toArray();



		return $array_reglas;
													
	}




	public function data_direccion_empresa($empresa_id) {

		$direccion 		= 		STDEmpresaDireccion::where('COD_EMPR','=',$empresa_id)->first();
		return $direccion;				

	}

	public function data_direccion($direccion_id) {

		$direccion 		= 		STDEmpresaDireccion::where('COD_DIRECCION','=',$direccion_id)->first();
		return $direccion;				

	}

	public function data_cliente($contrato_id) {

		$direccion 		= 		WEBListaCliente::where('COD_CONTRATO','=',$contrato_id)->first();
		return $direccion;				

	}


	public function nombre_cliente_despacho($contrato_id) {

		$nombre_cliente = 		'';
		$cliente 		= 		WEBListaCliente::where('COD_CONTRATO','=',$contrato_id)->first();

		if (count($cliente)>0){
			$nombre_cliente = $cliente->NOM_EMPR;
		}else{

			$array_extra 	= 	$this->cliente_extras_web();
			foreach ($array_extra as $k => $v){
				if( $k == $contrato_id){
					$nombre_cliente = $array_extra[$k];
				}
			}
		}
		return $nombre_cliente;				

	}

	public function nombre_cliente_despacho_cliente($cliente_id) {

		$nombre_cliente = 		'';
		$cliente 		= 		WEBListaCliente::where('id','=',$cliente_id)->first();

		if (count($cliente)>0){
			$nombre_cliente = $cliente->NOM_EMPR;
		}else{

			$array_extra 	= 	$this->cliente_extras_web();
			foreach ($array_extra as $k => $v){
				if( $k == $cliente_id){
					$nombre_cliente = $array_extra[$k];
				}
			}
		}
		return $nombre_cliente;				

	}



	public function data_cliente_cliente_id($cliente_id) {

		$cliente 		= 		WEBListaCliente::where('id','=',$cliente_id)->first();
		return $cliente;				

	}


	public function cambiar_estado_detalle_pedido($detalle_pedido_id,$mensaje,$estado_pedido){

		$fechaactual 				= 	date('d-m-Y H:i:s');
		$mensaje					=   $mensaje;
		$error						=   false;

		$detalle_pedido 			= 	WEBDetallePedido::where('id','=',$detalle_pedido_id)->first();

		if($detalle_pedido->estado_id == $estado_pedido){
			
			$mensaje = 'El producto ya esta con estado rechazado';
			$error   = true;

		}else{

		    $detalle_pedido->estado_id 			= 	$estado_pedido;
			$detalle_pedido->fecha_mod 	 		=   $fechaactual;
			$detalle_pedido->usuario_mod 		=   Session::get('usuario')->id;
			$detalle_pedido->save();

		}								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		return $response;


	}

	public function data_regla_limite_credito($cliente_id) {

        $limite_credito     =   WEBReglaCreditoCliente::where('cliente_id','=',$cliente_id)->first();
        return 	$limite_credito;		

	}


	public function data_regla_producto_cliente($regla_producto_cliente_id) {

		$regla_producto_cliente 		= 		WEBReglaProductoCliente::where('id','=',$regla_producto_cliente_id)->first();
		return $regla_producto_cliente;				

	}

	public function asignar_precio_estandar_producto_empresa($empresa_id,$producto_id,$precio) {

		$fechaactual 				= 	date('d-m-Y H:i:s');
		$precioproducto             =   WEBPrecioProducto::where('producto_id','=',$producto_id)
										->where('empresa_id','=',$empresa_id)
										->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->first();

		if(count($precioproducto)<=0){

			/****** AGREGAR PRECIO PRODUCTO **********/
			$idprecioproducto 			=  	$this->getCreateIdMaestra('WEB.precioproductos');
			$cabecera            	 	=	new WEBPrecioProducto;
			$cabecera->id 	     	 	=   $idprecioproducto;
			$cabecera->precio 	     	=   $precio;
			$cabecera->fecha_crea 	 	=   $fechaactual;
			$cabecera->usuario_crea 	=   Session::get('usuario')->id;
			$cabecera->producto_id 	 	= 	$producto_id;
			$cabecera->empresa_id 		=   $empresa_id;
			$cabecera->centro_id 		=   Session::get('centros')->COD_CENTRO;
			$cabecera->save();

		}


	}




	public function el_pedido_estado_generado($pedido_id,$mensaje) {


		$mensaje					=   $mensaje;
		$error						=   false;

		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->where('estado_id','=','EPP0000000000002')->first();

		if(count($pedido) <= 0){
			$mensaje = 'Esta pedido no esta en estado "GENERADO" no se puede actualizar';
			$error   = true;
		}								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		return $response;


	}


	public function calculo_totales_pedido($pedido_id) {


		$fechaactual 				= 	date('d-m-Y H:i:s');
		$detallepedido 				= 	WEBDetallePedido::where('pedido_id','=',$pedido_id)
										->where('activo','=','1')
										->select(DB::raw('sum(total) as total'))
										->first();

		$pedido 					=   WEBPedido::where('id','=',$pedido_id)->first();
	    $pedido->igv 				= 	$this->calculo_igv($detallepedido->total);
	    $pedido->subtotal 			= 	$this->calculo_subtotal($detallepedido->total);
	    $pedido->total 				= 	$detallepedido->total;
		$pedido->fecha_mod 	 		=   $fechaactual;
		$pedido->usuario_mod 		=   Session::get('usuario')->id;
		$pedido->save();
			

	}




	public function color_empresa($empresa_id) {

		$color 		= '';
		if($empresa_id == 'IACHEM0000010394'){
			$color 		= 'color-iin';
		}

		if($empresa_id == 'IACHEM0000007086'){
			$color 		= 'color-ico';
		}
		if($empresa_id == 'EMP0000000000007'){
			$color 		= 'color-itr';
		}

		if($empresa_id == 'IACHEM0000001339'){
			$color 		= 'color-ich';
		}

		if($empresa_id == 'EMP0000000000001'){
			$color 		= 'color-iaa';
		}
		return $color;
	}


	public function data_empresa_despacho_por_centro($centro_id) {

		if($centro_id=='CEN0000000000001'){
			$empresa 		= 	STDEmpresa::where('COD_EMPR','=','IACHEM0000010394')->first();
		}else{
			if($centro_id=='CEN0000000000004' or $centro_id=='CEN0000000000006'){
				$empresa 		= 	STDEmpresa::where('COD_EMPR','=','IACHEM0000007086')->first();
			}else{
				$empresa 		= 	STDEmpresa::where('COD_EMPR','=','')->first();
			}
		}
		return $empresa;
	}



	public function data_categoria_documento($documento_id) {

		$documento 		= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$documento_id)
							->first();

		$categoria 		= 	CMPCategoria::where('COD_CATEGORIA','=',$documento->COD_CATEGORIA_ESTADO_DOC_CTBLE)->first();
		return $categoria;


	}


	public function data_categoria($categoria_id) {

		$categoria 		= 		CMPCategoria::where('COD_CATEGORIA','=',$categoria_id)->first();
		return $categoria;				

	}


	public function data_centro($centro_id) {

		$centro 		= 		ALMCentro::where('COD_CENTRO','=',$centro_id)->first();
		return $centro;				

	}


	public function data_empresa($empresa_id) {

		$empresa 		= 		STDEmpresa::where('COD_EMPR','=',$empresa_id)->first();
		return $empresa;				

	}

	public function data_usuario($usuario_id) {

		$usuario 		= 		User::where('id','=',$usuario_id)->first();
		return $usuario;				

	}

	public function pedido_producto_rechazado($pedido) {

		$detalle_pedido 		= 		WEBDetallePedido::where('pedido_id','=',$pedido->id)
										->where('estado_id','=','EPP0000000000005')
										->where('activo','=',1)
										->get();

		return count($detalle_pedido);				

	}


	public function pedido_producto_obsequio($pedido) {

		$detalle_pedido 		= 		WEBDetallePedido::where('pedido_id','=',$pedido->id)
										->where('ind_obsequio','=',1)
										->where('activo','=',1)
										->get();

		return count($detalle_pedido);				

	}

	public function pedido_producto_registrado($pedido) {

		$detalle_pedido 		= 		WEBDetallePedido::where('pedido_id','=',$pedido->id)
										->where('estado_id','=','EPP0000000000004')
										->where('activo','=',1)
										->get();

		return count($detalle_pedido);				

	}

	public function pedido_producto_registrado_atendido($pedido) {

		$detalle_pedido 		= 		WEBDetallePedido::where('pedido_id','=',$pedido->id)
										//->where('estado_id','=','EPP0000000000004')
										->where('atendido','>',0)
										->where('activo','=',1)
										->get();

		return count($detalle_pedido);				

	}



	public function pedido_producto_registrado_atendido_detalle($detpedido) {

		$detalle_pedido 		= 		WEBDetallePedido::where('id','=',$detpedido->id)
										->where('atendido','>',0)
										->where('atendido','<',$detpedido->cantidad)
										->where('activo','=',1)
										->get();

		return count($detalle_pedido);				

	}

	public function pedido_producto_registrado_parcialmente($pedido_id) {

		$detalle_pedido 		= 		WEBDetallePedido::where('pedido_id','=',$pedido_id)
										->where('estado_id','=','EPP0000000000004')
										->where('activo','=',1)
										->get();

		return count($detalle_pedido);				

	}


	public function pedido_producto_registrado_despacho($pedido) {

		$detalle_pedido 		= 		WEBDetalleOrdenDespacho::where('ordendespacho_id','=',$pedido->id)
										->where('estado_id','=','EPP0000000000004')
										->where('activo','=',1)
										->get();

		return count($detalle_pedido);				

	}



	public function pedido_producto_total($pedido) {

		$detalle_pedido 		= 		WEBDetallePedido::where('pedido_id','=',$pedido->id)
										->where('activo','=',1)
										->get();

		return count($detalle_pedido);				

	}


	public function estado_pedido_ejecutado($pedido) {


		$fechaactual 			= 		date('d-m-Y H:i:s');
		$detalle_pedido 		= 		WEBDetallePedido::where('pedido_id','=',$pedido->id)
										->where('activo','=',1)
										->whereNotIn('estado_id',['EPP0000000000004','EPP0000000000005'])
										->get();

		if(count($detalle_pedido)<=0){

            $cabecera                               =   WEBPedido::find($pedido->id);
			$cabecera->fecha_mod 	 				=   $fechaactual;
			$cabecera->usuario_mod 					=   Session::get('usuario')->id;
            $cabecera->estado_id                    =   'EPP0000000000004';
            $cabecera->ind_notificacion_despacho    =   0;
            $cabecera->save();

		}								


	}


	public function json_detalle_pedido($pedido_id) {

		$json_detalle_pedido = 		WEBDetallePedido::where('pedido_id','=',$pedido_id)
									->where('activo','=',1)
									->select(DB::raw("	id as detalle_pedido_id,
													  	empresa_id,
													  	'' as orden_detalle_pedido_id,
													  	ind_obsequio,
													    (CASE   
														      WHEN estado_id != 'EPP0000000000004' and estado_id != 'EPP0000000000005' THEN 'checked'   
														      ELSE ''   
														END) as checked,
													  	estado_id"))
									->get()
									->toJson();

		return $json_detalle_pedido;

	}


	public function grouparray($array,$groupkey)
	{
	 if (count($array)>0)
	 {
	 	$keys = array_keys($array[0]);
	 	$removekey = array_search($groupkey, $keys);		if ($removekey===false)
	 		return array("Clave \"$groupkey\" no existe");
	 	else
	 		unset($keys[$removekey]);
	 	$groupcriteria = array();
	 	$return=array();
	 	foreach($array as $value)
	 	{
	 		$item=null;
	 		foreach ($keys as $key)
	 		{
	 			$item[$key] = $value[$key];
	 		}
	 	 	$busca = array_search($value[$groupkey], $groupcriteria);
	 		if ($busca === false)
	 		{
	 			$groupcriteria[]=$value[$groupkey];
	 			$return[]=array($groupkey=>$value[$groupkey],'groupeddata'=>array());
	 			$busca=count($return)-1;
	 		}
	 		$return[$busca]['groupeddata'][]=$item;
	 	}
	 	return $return;
	 }
	 else
	 	return array();
	}



	public function calculo_precio_venta($cliente,$producto,$fechadia) {


		$precio_regular 	=	0;
		$fechadia 			= 	date_format(date_create($fechadia), 'Y-m-d');


		$precio_producto 			= 	CMPOrden::join('CMP.DETALLE_PRODUCTO', 'CMP.ORDEN.COD_ORDEN', '=', 'CMP.DETALLE_PRODUCTO.COD_TABLA')
												->where('CMP.ORDEN.COD_CATEGORIA_TIPO_ORDEN','=','TOR0000000000024')
												->where('CMP.ORDEN.fec_orden','=',$fechadia)
												->where('CMP.ORDEN.COD_EMPR','=',Session::get('empresas')->COD_EMPR)
												->where('CMP.ORDEN.COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
												->where('CMP.ORDEN.COD_CONTRATO','=',$cliente->COD_CONTRATO)
												->where('CMP.DETALLE_PRODUCTO.COD_PRODUCTO','=',$producto->producto_id)
												->first();

		if(count($precio_producto)){
			$precio_regular 	=	$precio_producto->CAN_PRECIO_UNIT;
		}

		return $precio_regular;
	 			
	}




	//cambio 

	public function combo_jefe_ventas() {


        $lista_jefes_ventas = 		CMPCategoria::where('CMP.CATEGORIA.COD_ESTADO','=',1)
        							->where('CMP.CATEGORIA.IND_ACTIVO','=',1)
        							->where('CMP.CATEGORIA.TXT_GRUPO', '=' , 'JEFE_VENTA')
							        ->where('CMP.CATEGORIA.TXT_ABREVIATURA','=', Session::get('centros')->COD_CENTRO)
									->pluck('CMP.CATEGORIA.NOM_CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA')
									->toArray();

		$combo_jefes_ventas  	= 	array('' => "Seleccione Responsable",'1' => "TODOS") + $lista_jefes_ventas;
		return $combo_jefes_ventas;		 			
	}

	public function combo_jefe_ventas_regla() {


        $lista_jefes_ventas = 		CMPCategoria::where('CMP.CATEGORIA.COD_ESTADO','=',1)
        							->where('CMP.CATEGORIA.IND_ACTIVO','=',1)
        							->where('CMP.CATEGORIA.TXT_GRUPO', '=' , 'JEFE_VENTA')
							        ->where('CMP.CATEGORIA.TXT_ABREVIATURA','=', Session::get('centros')->COD_CENTRO)
									->pluck('CMP.CATEGORIA.NOM_CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA')
									->toArray();

		$combo_jefes_ventas  	= 	array('' => "Seleccione Responsable") + $lista_jefes_ventas;
		return $combo_jefes_ventas;		 			
	}


	public function combo_estado_carros() {


        $lista_estado_carros = 		CMPCategoria::where('CMP.CATEGORIA.COD_ESTADO','=',1)
        							->where('CMP.CATEGORIA.IND_ACTIVO','=',1)
        							->whereIn('COD_CATEGORIA',['ETC0000000000001' ,'ETC0000000000002','ETC0000000000003','ETC0000000000004'])
        							->pluck('CMP.CATEGORIA.NOM_CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA')
									->toArray();

		$combo_estado_carros  	= 	$lista_estado_carros;
		return $combo_estado_carros;		 			
	}



	public function lista_precios_departamento_cliente($contrato_id,$producto_id,$cliente_id) {


		$departamento_id 					= 	"";
		$lista_reglas_departamento 			= 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
												->join('CMP.CATEGORIA', 'WEB.reglas.departamento_id', '=', 'CMP.CATEGORIA.COD_CATEGORIA')
												->where('WEB.reglaproductoclientes.activo','=','1')
												->where('WEB.reglas.activo','=','1')
												->where('WEB.reglas.estado','=','PU')
												->where('WEB.reglas.tiporegla','=','PRD')
												->where('WEB.reglaproductoclientes.contrato_id','=',$contrato_id)
												->where('WEB.reglaproductoclientes.cliente_id','=',$cliente_id)
												->where('WEB.reglaproductoclientes.producto_id','=',$producto_id)
												->get();


		$lista_precio_departamento = array();
		$cadena = '';
		// RECORRER TODOS LOS DEPARTAMENTOS CON SU PRECIO
		foreach($lista_reglas_departamento as $item){

			$departamento_id 	= 	trim($item->departamento_id);

			$empresa_id			= 	Session::get('empresas')->COD_EMPR;
			$centro_id			=	Session::get('centros')->COD_CENTRO;
			$stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC web.precio_producto_contrato ?,?,?,?,?');
	        $stmt->bindParam(1, $contrato_id ,PDO::PARAM_STR);
	        $stmt->bindParam(2, $producto_id ,PDO::PARAM_STR);
	        $stmt->bindParam(3, $departamento_id ,PDO::PARAM_STR);
	        $stmt->bindParam(4, $empresa_id ,PDO::PARAM_STR);
	        $stmt->bindParam(5, $centro_id ,PDO::PARAM_STR);
	        $stmt->execute();
	        $resultado = $stmt->fetch();

	        $cadena	=	$item->NOM_CATEGORIA.' : S/. '.$resultado['precio'];
			array_push($lista_precio_departamento, $cadena);

		}

	 	return   $lista_precio_departamento;				 			
	}



	public function reglas_producto_fecha_sub_canales($producto_id,$fechadia) {



		$reglas 						=	'';
		$fechadia 						= 	date_format(date_create($fechadia), 'Y-m-d');
									
	    $array_cliente_contrato 		= 	WEBListaCliente::whereIn('COD_CATEGORIA_SUB_CANAL',['SCV0000000000004' ,'SCV0000000000020','SCV0000000000005'])
					    					->where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
					    					->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
											->pluck('COD_CONTRATO')
											->toArray();


		//historial de todas las reglas que tenia
		$lista_reglas_cliente 			= 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
												->where('WEB.reglas.tiporegla','=','POV')
												//->where('WEB.reglas.empresa_id','=',Session::get('empresas')->COD_EMPR)
												->where('WEB.reglas.centro_id','=',Session::get('centros')->COD_CENTRO)
												->whereIn('WEB.reglaproductoclientes.contrato_id',$array_cliente_contrato)
												//->where('WEB.reglaproductoclientes.contrato_id','=',$contrato_id)
												->where('WEB.reglaproductoclientes.producto_id','=',$producto_id)
												->whereNotNull('WEB.reglaproductoclientes.fecha_mod')
												->whereRaw('Convert(varchar(10), WEB.reglaproductoclientes.fecha_crea, 120) <= ?', [$fechadia])
												->whereRaw('Convert(varchar(10), WEB.reglaproductoclientes.fecha_mod, 120) >= ?', [$fechadia])
												->select('WEB.reglas.codigo','WEB.reglas.descuento')
												->groupBy('WEB.reglas.codigo')
												->groupBy('WEB.reglas.descuento')
												->get();									


		foreach($lista_reglas_cliente as $item){
			$reglas = $reglas . $item->codigo.' (S/.'.$item->descuento.' menos) ';
		}

		//ultima regla asignada
		$lista_reglas_cliente_ultima			= 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
													->where('WEB.reglas.tiporegla','=','POV')
													//->where('WEB.reglas.empresa_id','=',Session::get('empresas')->COD_EMPR)
													->where('WEB.reglas.centro_id','=',Session::get('centros')->COD_CENTRO)
													->whereIn('WEB.reglaproductoclientes.contrato_id',$array_cliente_contrato)
													//->where('WEB.reglaproductoclientes.contrato_id','=',$contrato_id)
													->where('WEB.reglaproductoclientes.producto_id','=',$producto_id)
													->select('WEB.reglaproductoclientes.*','WEB.reglas.descuento','WEB.reglas.codigo')
													->whereNull('WEB.reglaproductoclientes.fecha_mod')
													->whereRaw('Convert(varchar(10), WEB.reglaproductoclientes.fecha_crea, 120) <= ?', [$fechadia])
													->first();


		if(count($lista_reglas_cliente_ultima)>0){
			$reglas = $reglas . $lista_reglas_cliente_ultima->codigo.' (S/.'.$lista_reglas_cliente_ultima->descuento.' menos) ';
		}									


		return $reglas;
			 			
	}



	public function descuento_reglas_producto_fecha($contrato_id,$producto_id,$cliente_id,$departamento_id,$fechadia) {



		$descuento 						=	0.0000;
		$fechadia 						= 	date_format(date_create($fechadia), 'Y-m-d');
									

		//historial de todas las reglas que tenia
		$lista_reglas_cliente 			= 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
												->where('WEB.reglas.tiporegla','=','POV')
												//->where('WEB.reglas.empresa_id','=',Session::get('empresas')->COD_EMPR)
												->where('WEB.reglas.centro_id','=',Session::get('centros')->COD_CENTRO)
												->where('WEB.reglaproductoclientes.contrato_id','=',$contrato_id)
												->where('WEB.reglaproductoclientes.producto_id','=',$producto_id)
												->whereNotNull('WEB.reglaproductoclientes.fecha_mod')
												->select('WEB.reglaproductoclientes.*','WEB.reglas.descuento')
												->whereRaw('Convert(varchar(10), WEB.reglaproductoclientes.fecha_crea, 120) <= ?', [$fechadia])
												->whereRaw('Convert(varchar(10), WEB.reglaproductoclientes.fecha_mod, 120) >= ?', [$fechadia])
												->get();

		foreach($lista_reglas_cliente as $item){
			$descuento = $descuento + $item->descuento;
		}

		//ultima regla asignada
		$lista_reglas_cliente_ultima			= 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
												->where('WEB.reglas.tiporegla','=','POV')
												//->where('WEB.reglas.empresa_id','=',Session::get('empresas')->COD_EMPR)
												->where('WEB.reglas.centro_id','=',Session::get('centros')->COD_CENTRO)
												->where('WEB.reglaproductoclientes.contrato_id','=',$contrato_id)
												->where('WEB.reglaproductoclientes.producto_id','=',$producto_id)
												->select('WEB.reglaproductoclientes.*','WEB.reglas.descuento')
												->whereNull('WEB.reglaproductoclientes.fecha_mod')
												->whereRaw('Convert(varchar(10), WEB.reglaproductoclientes.fecha_crea, 120) <= ?', [$fechadia])
												->first();


		if(count($lista_reglas_cliente_ultima)>0){
				$descuento = $descuento + $lista_reglas_cliente_ultima->descuento;
		}									


		return $descuento;
			 			
	}




	public function descuento_reglas_producto($contrato_id,$producto_id,$cliente_id,$departamento_id) {

		$departamento_id = trim($departamento_id);

		$stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC web.descuento_regla_producto_contrato ?,?,?,?');
        $stmt->bindParam(1, $contrato_id ,PDO::PARAM_STR);
        $stmt->bindParam(2, $producto_id ,PDO::PARAM_STR);
        $stmt->bindParam(3, $cliente_id ,PDO::PARAM_STR);
        $stmt->bindParam(4, $departamento_id ,PDO::PARAM_STR); 
        $stmt->execute();
        $resultado = $stmt->fetch();
		return  $resultado['descuento'];
			 			
	}


	public function precio_descuento_reglas_producto($contrato_id,$producto_id,$cliente_id,$departamento_id) {

		$departamento_id = trim($departamento_id);

		$empresa_id			= 	Session::get('empresas')->COD_EMPR;
		$centro_id			=	Session::get('centros')->COD_CENTRO;
		$stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC web.precio_producto_contrato ?,?,?,?,?');
        $stmt->bindParam(1, $contrato_id ,PDO::PARAM_STR);
        $stmt->bindParam(2, $producto_id ,PDO::PARAM_STR);
        $stmt->bindParam(3, $departamento_id ,PDO::PARAM_STR);
        $stmt->bindParam(4, $empresa_id ,PDO::PARAM_STR);
        $stmt->bindParam(5, $centro_id ,PDO::PARAM_STR);
	         
        $stmt->execute();
        $resultado = $stmt->fetch();
		return  $resultado['precio'];
			 			
	}



	public function lista_reglas_cliente($contrato_id,$producto_id) {


		$lista_reglas_cliente 			= 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
												->where('WEB.reglaproductoclientes.activo','=','1')
												->where('WEB.reglas.activo','=','1')
												->where('WEB.reglas.estado','=','PU')
												->where('WEB.reglas.tiporegla','<>','PRD')
												//->where('WEB.reglas.empresa_id','=',Session::get('empresas')->COD_EMPR)
												->where('WEB.reglas.centro_id','=',Session::get('centros')->COD_CENTRO)
												->where('WEB.reglaproductoclientes.contrato_id','=',$contrato_id)
												->where('WEB.reglaproductoclientes.producto_id','=',$producto_id)
												->orderBy('WEB.reglas.departamento_id', 'asc')
												->get();

	 	return   $lista_reglas_cliente;				 			
	}


	public function lista_reglas_cliente_total($contrato_id,$producto_id,$regla_id) {
		$montotal 						=	0;
		$reglas 						= 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
												->where('WEB.reglaproductoclientes.activo','=','1')
												->where('WEB.reglas.activo','=','1')
												->where('WEB.reglas.estado','=','PU')
												->where('WEB.reglas.tiporegla','=','PNC')
												->Cuenta($contrato_id)
												->Producto($producto_id)
												->where('WEB.reglas.id','=',$regla_id)
												->first();
		if(count($reglas)>0){
			$montotal 					=	$reglas->descuento;
		}
	 	return   $montotal;				 			
	}

	public function lista_reglas_cliente_total_groupby($contrato_id,$producto_id) {

		$lista_reglas_cliente 			= 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
												->where('WEB.reglaproductoclientes.activo','=','1')
												->where('WEB.reglas.activo','=','1')
												->where('WEB.reglas.estado','=','PU')
												->where('WEB.reglas.tiporegla','=','PNC')
												->where('WEB.reglas.centro_id','=',Session::get('centros')->COD_CENTRO)
												->Cuenta($contrato_id)
												->Producto($producto_id)
												->select('WEB.reglas.id','WEB.reglas.nombre')
												->groupBy('WEB.reglas.id')
												->groupBy('WEB.reglas.nombre')
												->get();

	 	return   $lista_reglas_cliente;				 			
	}





	public function lista_precio_regular_departamento($contrato_id,$producto_id) {


		$lista_precio_regular_departamento 	= 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
												->where('WEB.reglaproductoclientes.activo','=','1')
												->where('WEB.reglas.activo','=','1')
												->where('WEB.reglas.estado','=','PU')
												->where('WEB.reglas.tiporegla','=','PRD')
												//->where('WEB.reglas.empresa_id','=',Session::get('empresas')->COD_EMPR)
												->where('WEB.reglas.centro_id','=',Session::get('centros')->COD_CENTRO)
												->where('WEB.reglaproductoclientes.contrato_id','=',$contrato_id)
												->where('WEB.reglaproductoclientes.producto_id','=',$producto_id)
												->orderBy('WEB.reglas.departamento_id', 'asc')
												->get();

	 	return   $lista_precio_regular_departamento;				 			
	}



	public function lista_productos_reglas($cuenta_id) {

		$array_productos_id 	= 	WEBReglaProductoCliente::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
									->join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.reglaproductoclientes.regla_id')
									->where('WEB.reglas.activo','=','1')
									->where('WEB.reglas.estado','=','PU')
									->where('WEB.reglas.tiporegla','<>','PRD')
									//->where('WEB.reglas.empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('WEB.reglas.centro_id','=',Session::get('centros')->COD_CENTRO)
									->Cuenta($cuenta_id)
									//->where('WEB.reglaproductoclientes.contrato_id','=',$cuenta_id)
									->where('WEB.reglaproductoclientes.activo','=','1')
									->pluck('WEB.reglaproductoclientes.producto_id')->toArray();

		//dd($array_productos_id);


		$lista_producto_regla 	= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
					    			->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
					    			->where('centro_id','=',Session::get('centros')->COD_CENTRO)
									->whereIn('producto_id',$array_productos_id)
									//->where('COD_PRODUCTO','=','PRD0000000000747')
	    					 		->orderBy('NOM_PRODUCTO', 'asc')->get();

		//dd($lista_producto_regla);

	 	return   $lista_producto_regla;				 			
	}



	public function lista_productos_precio_favotitos($cuenta_id) {




		if($cuenta_id=='TODO'){

			$array_contratos_id 	= 	WEBReglaProductoCliente::where('WEB.reglaproductoclientes.activo','=','1')
										->groupBy('WEB.reglaproductoclientes.contrato_id')
										->pluck('WEB.reglaproductoclientes.contrato_id')
										->toArray();

			$lista_producto_precio 	= 	WEBPrecioProductoContrato::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
								    	->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
						    			->where('centro_id','=',Session::get('centros')->COD_CENTRO)
						    			->whereIn('contrato_id',$array_contratos_id)
										//->where('contrato_id','=',$cuenta_id)
										//->Contrato($cuenta_id)
										->where('activo','=','1')
										->where('ind_contrato','=',1)
										->orderBy('NOM_PRODUCTO', 'asc')
										->get();
		}else{

			$lista_producto_precio 	= 	WEBPrecioProductoContrato::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
								    	->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
						    			->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->where('contrato_id','=',$cuenta_id)
										//->Contrato($cuenta_id)
										->where('activo','=','1')
										->where('ind_contrato','=',1)
										->orderBy('NOM_PRODUCTO', 'asc')
										->get();
		}







	 	return   $lista_producto_precio;				 			
	}


	public function combo_tipo_precio_productos_reglas() {

		$combotipoprecio_producto  	= 	array('2' => "Reglas" ,'1' => "Contratos" ,'0' => "Todos");
		return $combotipoprecio_producto;		 			
	}

	public function combo_tipo_precio_productos() {

		// $combotipoprecio_producto  	= 	array('1' => "Contratos" ,'0' => "Todos");
		$combotipoprecio_producto  	= 	array('1' => "Contratos");

		return $combotipoprecio_producto;		 			
	}


	public function combo_tipo_precio_productos_asignar() {
		$combotipoprecio_producto  	= 	array('0' => "Todos",'1' => "Contratos" ,);
		return $combotipoprecio_producto;		 			
	}


	public function tiene_contrato_activo($precioproducto_id,$contrato_id) {
		

		$precio_producto 		  	= 	WEBPrecioProducto::where('id','=',$precioproducto_id)->first();

		$precio_producto_contrato 	= 	WEBPrecioProductoContrato::where('producto_id','=',$precio_producto->producto_id)
										->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
										->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->where('contrato_id','=',$contrato_id)
										->first();

		if(count($precio_producto_contrato)>0){
			if($precio_producto_contrato->ind_contrato == 1){
				return 1;
			}else{
				return 0;
			}
		}else{
			return 0;
		}

					 			
	}

	public function favorito_precio_producto_contrato($precioproducto_id,$contrato_id) {
		

		$precio_producto 		  	= 	WEBPrecioProducto::where('id','=',$precioproducto_id)->first();

		$precio_producto_contrato 	= 	WEBPrecioProductoContrato::where('producto_id','=',$precio_producto->producto_id)
										->where('empresa_id','=',$precio_producto->empresa_id)
										->where('centro_id','=',$precio_producto->centro_id)
										->where('contrato_id','=',$contrato_id)
										->first();

		if(count($precio_producto_contrato)>0){
			return true;
		}else{
			return false;
		}

					 			
	}



	public function calculo_precio_regular_fecha_subcanal($sub_canal_id,$producto,$fechadia) {


		$precio_regular 				=	'0.000';
		$fechadia 						= 	date_format(date_create($fechadia), 'Y-m-d');



		// lista de clientes del subcanal
	    $array_cliente_contrato 		= 	WEBListaCliente::SubCanal($sub_canal_id)
					    					->where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
					    					->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
											->pluck('COD_CONTRATO')
											->toArray();
					    				
					    				
		//existe en esta tabla 
		$exiteprecio 					=	WEBPrecioProductoContrato::whereIn('contrato_id',$array_cliente_contrato)
											->where('producto_id','=',$producto->producto_id)
											->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
											->where('centro_id','=',Session::get('centros')->COD_CENTRO)
											->first();



		if(count($exiteprecio)>0){


			//existe ingreso de precio en la aplicacion 
			$primerregistro 	=	WEBPrecioProductoContrato::whereIn('contrato_id',$array_cliente_contrato)
									->where('producto_id','=',$producto->producto_id)
									->whereRaw('Convert(varchar(10), fecha_crea, 120) <= ?', [$fechadia])
									->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('centro_id','=',Session::get('centros')->COD_CENTRO)
									->first();


			if(count($primerregistro)>0){


				$precio_regular 	=	$primerregistro->precio;
				//ultimo precio ingresado
				$ultimoregistro 	=	WEBPrecioProductoContrato::whereIn('contrato_id',$array_cliente_contrato)
										->where('producto_id','=',$producto->producto_id)
							            ->where(function ($query) use($fechadia) {
							                $query->whereRaw('Convert(varchar(10), fecha_mod, 120) <= ?', [$fechadia])
							                      ->orwhereNull('fecha_mod');
							            })
										//->whereRaw('Convert(varchar(10), fecha_mod, 120) <= ?', [$fechadia])
										->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
										->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->first();



				if(count($ultimoregistro)>0){
					$precio_regular 	=	$ultimoregistro->precio;
				}else{



					//fecha anterior
					$preciohistorico 	=	WEBPrecioProductoContratoHistorial::whereIn('contrato_id',$array_cliente_contrato)
											->where('producto_id','=',$producto->producto_id)
											->whereRaw('Convert(varchar(10), fecha_crea, 120) < ?', [$fechadia])
											->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
											->where('centro_id','=',Session::get('centros')->COD_CENTRO)
											->orderBy('fecha_crea', 'desc')
											->first();

					//precio historico
					$preciohistoricoreal 	=	WEBPrecioProductoContratoHistorial::whereIn('contrato_id',$array_cliente_contrato)
											->where('producto_id','=',$producto->producto_id)
											->whereRaw('Convert(varchar(10), fecha_crea, 120) > ?', [$preciohistorico->fecha_crea])
											->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
											->where('centro_id','=',Session::get('centros')->COD_CENTRO)
											->orderBy('fecha_crea', 'asc')
											->first();

					$precio_regular 	=	$preciohistoricoreal->precio;

				}
			}
		}else{


			//existe ingreso de precio en la aplicacion 
			$primerregistro 	=	WEBPrecioProducto::where('producto_id','=',$producto->producto_id)
									->whereRaw('Convert(varchar(10), fecha_crea, 120) <= ?', [$fechadia])
									->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('centro_id','=',Session::get('centros')->COD_CENTRO)
									->first();

			if(count($primerregistro)>0){



				$precio_regular 	=	$primerregistro->precio;
				//ultimo precio ingresado
				$ultimoregistro 	=	WEBPrecioProducto::where('producto_id','=',$producto->producto_id)
							            ->where(function ($query) use($fechadia) {
							                $query->whereRaw('Convert(varchar(10), fecha_mod, 120) <= ?', [$fechadia])
							                      ->orwhereNull('fecha_mod');
							            })
										->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
										->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->first();

				if(count($ultimoregistro)>0){
					$precio_regular 	=	$ultimoregistro->precio;
				}else{

					//fecha anterior
					$preciohistorico 	=	WEBPrecioProductoHistorial::where('producto_id','=',$producto->producto_id)
											->whereRaw('Convert(varchar(10), fecha_crea, 120) < ?', [$fechadia])
											->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
											->where('centro_id','=',Session::get('centros')->COD_CENTRO)
											->orderBy('fecha_crea', 'desc')
											->first();


					//precio historico
					$preciohistoricoreal 	=	WEBPrecioProductoHistorial::where('producto_id','=',$producto->producto_id)
											->whereRaw('Convert(varchar(10), fecha_crea, 120) > ?', [$preciohistorico->fecha_crea])
											->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
											->where('centro_id','=',Session::get('centros')->COD_CENTRO)
											->orderBy('fecha_crea', 'asc')
											->first();

					$precio_regular 	=	$preciohistoricoreal->precio;

				}

			}



		}




	    							
		return $precio_regular;
	 			
	}



	public function calculo_precio_regular_fecha($cliente,$producto,$fechadia) {


		$precio_regular 	=	0;
		$fechadia 			= 	date_format(date_create($fechadia), 'Y-m-d');


		//existe en esta tabla 
		$exiteprecio 	=	WEBPrecioProductoContrato::where('contrato_id','=',$cliente->COD_CONTRATO)
							->where('producto_id','=',$producto->producto_id)
							->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
							->where('centro_id','=',Session::get('centros')->COD_CENTRO)
							->first();



		if(count($exiteprecio)>0){


			//existe ingreso de precio en la aplicacion 
			$primerregistro 	=	WEBPrecioProductoContrato::where('contrato_id','=',$cliente->COD_CONTRATO)
									->where('producto_id','=',$producto->producto_id)
									->whereRaw('Convert(varchar(10), fecha_crea, 120) <= ?', [$fechadia])
									->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('centro_id','=',Session::get('centros')->COD_CENTRO)
									->first();


			if(count($primerregistro)>0){



				$precio_regular 	=	$primerregistro->precio;
				//ultimo precio ingresado
				$ultimoregistro 	=	WEBPrecioProductoContrato::where('contrato_id','=',$cliente->COD_CONTRATO)
										->where('producto_id','=',$producto->producto_id)
							            ->where(function ($query) use($fechadia) {
							                $query->whereRaw('Convert(varchar(10), fecha_mod, 120) <= ?', [$fechadia])
							                      ->orwhereNull('fecha_mod');
							            })
										//->whereRaw('Convert(varchar(10), fecha_mod, 120) <= ?', [$fechadia])
										->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
										->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->first();



				if(count($ultimoregistro)>0){
					$precio_regular 	=	$ultimoregistro->precio;
				}else{



					//fecha anterior
					$preciohistorico 	=	WEBPrecioProductoContratoHistorial::where('contrato_id','=',$cliente->COD_CONTRATO)
											->where('producto_id','=',$producto->producto_id)
											->whereRaw('Convert(varchar(10), fecha_crea, 120) < ?', [$fechadia])
											->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
											->where('centro_id','=',Session::get('centros')->COD_CENTRO)
											->orderBy('fecha_crea', 'desc')
											->first();

					//return count($preciohistorico);
					//precio historico
					$preciohistoricoreal 	=	WEBPrecioProductoContratoHistorial::where('contrato_id','=',$cliente->COD_CONTRATO)
											->where('producto_id','=',$producto->producto_id)
											->whereRaw('Convert(varchar(10), fecha_crea, 120) > ?', [$preciohistorico->fecha_crea])
											->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
											->where('centro_id','=',Session::get('centros')->COD_CENTRO)
											->orderBy('fecha_crea', 'asc')
											->first();

					$precio_regular 	=	$preciohistoricoreal->precio;

				}
			}
		}else{


			//existe ingreso de precio en la aplicacion 
			$primerregistro 	=	WEBPrecioProducto::where('producto_id','=',$producto->producto_id)
									->whereRaw('Convert(varchar(10), fecha_crea, 120) <= ?', [$fechadia])
									->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('centro_id','=',Session::get('centros')->COD_CENTRO)
									->first();

			if(count($primerregistro)>0){



				$precio_regular 	=	$primerregistro->precio;
				//ultimo precio ingresado
				$ultimoregistro 	=	WEBPrecioProducto::where('producto_id','=',$producto->producto_id)
							            ->where(function ($query) use($fechadia) {
							                $query->whereRaw('Convert(varchar(10), fecha_mod, 120) <= ?', [$fechadia])
							                      ->orwhereNull('fecha_mod');
							            })
										->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
										->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->first();

				if(count($ultimoregistro)>0){
					$precio_regular 	=	$ultimoregistro->precio;
				}else{

					//fecha anterior
					$preciohistorico 	=	WEBPrecioProductoHistorial::where('producto_id','=',$producto->producto_id)
											->whereRaw('Convert(varchar(10), fecha_crea, 120) < ?', [$fechadia])
											->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
											->where('centro_id','=',Session::get('centros')->COD_CENTRO)
											->orderBy('fecha_crea', 'desc')
											->first();


					//precio historico
					$preciohistoricoreal 	=	WEBPrecioProductoHistorial::where('producto_id','=',$producto->producto_id)
											->whereRaw('Convert(varchar(10), fecha_crea, 120) > ?', [$preciohistorico->fecha_crea])
											->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
											->where('centro_id','=',Session::get('centros')->COD_CENTRO)
											->orderBy('fecha_crea', 'asc')
											->first();

					$precio_regular 	=	$preciohistoricoreal->precio;

				}

			}



		}




	    							
		return $precio_regular;
	 			
	}


	public function calculo_precio_regular($cliente,$producto) {


		$precioregular =      	WEBPrecioProductoContrato::where('contrato_id','=',$cliente->COD_CONTRATO)
								->where('producto_id','=',$producto->producto_id)
								->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
								->where('centro_id','=',Session::get('centros')->COD_CENTRO)
								->first();


		if(count($precioregular)){
			return $precioregular->precio;
		}

		return $producto->precio;
	 			
	}


	public function calculo_fecha_regular($cliente,$producto) {



		$precioregular =      	WEBPrecioProductoContrato::where('contrato_id','=',$cliente->COD_CONTRATO)
								->where('producto_id','=',$producto->producto_id)
								->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
								->where('centro_id','=',Session::get('centros')->COD_CENTRO)
								->first();


		if(count($precioregular)){
			return $precioregular->fecha_crea;
		}

		return $producto->fecha_crea;
	 			
	}



	public function combo_clientes_cuenta_seleccionada($cuenta_id) {

		$listaclientes   		=	WEBListaCliente::where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
					    			->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
					    			->where('COD_CONTRATO','=',$cuenta_id)
									->pluck('NOM_EMPR','COD_CONTRATO')
									->toArray();

		$combolistaclientes  	= 	$listaclientes;
		return $combolistaclientes;		 			
	}

	public function combo_clientes_cuenta() {

		$listaclientes   		=	WEBListaCliente::where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
					    			->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
									->pluck('NOM_EMPR','COD_CONTRATO')
									->toArray();

		$combolistaclientes  	= 	array('' => "Seleccione cliente",'TODO' => "TODO") + $listaclientes;
		return $combolistaclientes;		 			
	}

	public function combo_clientes_cuenta_regla() {


		$listaclientes   		=	WEBListaCliente::where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
									->pluck('NOM_EMPR','COD_CONTRATO')
									->toArray();

		$combolistaclientes  	= 	array('' => "Seleccione cliente") + $listaclientes;
		return $combolistaclientes;		 			
	}




	public function combo_regla_descuento() {


		$lista_activas 		= 	WEBRegla::where('activo','=',1)
				    			->whereIn('tiporegla', ['RDV'])
				    			->where('estado','=','PU')
								->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
								->select('id', DB::raw("(nombre + ' ' + CAST(descuento AS varchar(100)) ) AS nombre"))
								->pluck('nombre','id')
								->toArray();

		$combo_regla_descuento  	= 	array('' => "Seleccione regla") + $lista_activas;
		return $combo_regla_descuento;		 			
	}


	public function combo_regla_limite_credito() {


		$lista_activas 		= 	WEBRegla::where('activo','=',1)
				    			->whereIn('tiporegla', ['RLC'])
				    			->where('estado','=','PU')
								//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
								->select('id', DB::raw("(nombre + ' ' + CAST(descuento AS varchar(100)) ) AS nombre"))
								->pluck('nombre','id')
								->toArray();

		$combo_regla_descuento  	= 	array('' => "Seleccione regla") + $lista_activas;
		return $combo_regla_descuento;		 			
	}



	public function cliente_extras_web() {

		$array_cliente_extras  	= 	['1CIX000000000001' => 'MPSA',
									 '1CIX000000000002' => 'ALM.VILLA EL SALVADOR'
									];
		return $array_cliente_extras;	

	}



	public function combo_clientes_cuenta_lima() {

		$listaclientes   		=	WEBListaCliente::where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
					    			->where('COD_CENTRO','=','CEN0000000000002')
									->pluck('NOM_EMPR','COD_CONTRATO')
									->toArray();

		$combolistaclientes  	= 	array('' => "Seleccione cliente") + $listaclientes;
		return $combolistaclientes;		 			
	}

	public function combo_centro() {

		$listacentro   				=	ALMCentro::where('COD_ESTADO','=',1)
										->pluck('NOM_CENTRO','COD_CENTRO')
										->toArray();

		$combocentro  				= 	array('' => "Seleccione centro") + $listacentro;
		return $combocentro;		 			
	}


	public function combo_clientes() {

		$listaclientes   		=	WEBListaCliente::where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
					    			->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
									->pluck('NOM_EMPR','id')
									->toArray();

		$combolistaclientes  	= 	array('' => "Seleccione cliente") + $listaclientes;
		return $combolistaclientes;		 			
	}

	public function departamento($departamento_id) {


		$departamento_id = trim($departamento_id);
		$departamento   		=	CMPCategoria::where('TXT_PREFIJO','=','DEP')
									->where('COD_CATEGORIA','=',$departamento_id)
									->first();

		return 	$departamento;		 			
	}


	public function combo_categoria_general($grupo){

		$combo   		=	CMPCategoria::where('TXT_GRUPO','=',$grupo)
							->where('COD_ESTADO','=',1)
							->pluck('NOM_CATEGORIA','COD_CATEGORIA')
							->toArray();

		return 	$combo;		 			
	}


	public function combo_departamentos() {

		$listadepartamentos   		=	CMPCategoria::where('TXT_PREFIJO','=','DEP')
										->where('COD_ESTADO','=',1)
										->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		$combolistadepartamentos  	= 	array('' => "Seleccione departamento") + $listadepartamentos;
		return $combolistadepartamentos;					 			
	}
	public function combo_condicionpago() {

		$listacat 		=	CMPCategoria::where('TXT_GRUPO','=','TIPO_PAGO')
										->where('COD_ESTADO','=',1)
										->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		$combolistacondicionpago 	= 	array('' => "Seleccione condicion pago") + $listacat;
		return $combolistacondicionpago;					 			
	}


	public function combo_departamentos_modificar($documento_id) {

		$listadepartamentos   		=	CMPCategoria::where('TXT_PREFIJO','=','DEP')
										->where('COD_ESTADO','=',1)
										->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		$nombre_departamento 		=   $this->departamento($documento_id)->NOM_CATEGORIA;

		$combolistadepartamentos  	= 	array($documento_id => $nombre_departamento) + $listadepartamentos;
		return $combolistadepartamentos;					 			
	}


	//18-10-2019
	public function precio_producto_contrato_empresa($precioproducto_id,$contrato_id,$empresa_id) {
		

		$precio_producto 		  	= 	WEBPrecioProducto::where('id','=',$precioproducto_id)->first();

		$precio_producto_contrato 	= 	WEBPrecioProductoContrato::where('producto_id','=',$precio_producto->producto_id)
										->where('empresa_id','=',$empresa_id)
										->where('centro_id','=',$precio_producto->centro_id)
										->where('contrato_id','=',$contrato_id)
										->first();

		if(count($precio_producto_contrato)>0){
			return $precio_producto_contrato->precio;
		}else{
			return $precio_producto->precio;
		}

					 			
	}


	public function precio_producto_contrato($precioproducto_id,$contrato_id) {
		

		$precio_producto 		  	= 	WEBPrecioProducto::where('id','=',$precioproducto_id)->first();

		$precio_producto_contrato 	= 	WEBPrecioProductoContrato::where('producto_id','=',$precio_producto->producto_id)
										->where('empresa_id','=',$precio_producto->empresa_id)
										->where('centro_id','=',$precio_producto->centro_id)
										->where('contrato_id','=',$contrato_id)
										->first();

		if(count($precio_producto_contrato)>0){
			return $precio_producto_contrato->precio;
		}else{
			return $precio_producto->precio;
		}

					 			
	}


	public function cuenta_cliente($id_cliente) {
		
		$cuenta 		= 		DB::table('WEB.LISTACLIENTE')
        							->where('id','=',$id_cliente)
        							->first();

	 	return  $cuenta->CONTRATO;					 			
	}


	public function tipo_cambio() {
		
		$tipocambio 		= 		DB::table('WEB.TIPOCAMBIO')
        							->where('FEC_CAMBIO','<=',date('d/m/Y'))
        							->orderBy('FEC_CAMBIO', 'desc')
        							->first();

        return $tipocambio; 							
	}




	public function desencriptar_id($id,$count) {
		
		$idarray = explode('-', $id);
	  	//decodificar variable
	  	$decid 	= Hashids::decode($idarray[1]);
	  	//ver si viene con letras la cadena codificada
	  	if(count($decid)==0){ 
	  		return Redirect::back()->withInput()->with('errorurl', 'Indices de la url con errores'); 
	  	}
	  	//concatenar con ceros
	  	$idcompleta = str_pad($decid[0], $count, "0", STR_PAD_LEFT); 
	  	//concatenar prefijo
		$idcompleta = $idarray[0].$idcompleta;
		return $idcompleta;
	}


	public function calcular_cabecera_total($productos) {

		$total 						=   0.0000;
		$productos 					= 	json_decode($productos, true);

		foreach($productos as $obj){
			$obsequio 				=  	(int)$obj['obsequio'];
			if($obsequio=='0'){
				$total = $total + (float)$obj['precio_producto']*(float)$obj['cantidad_producto'];
			}
		}
		return $total;
	}

	public function calculo_igv($monto) {
	  	return $monto - ($monto/1.18);
	}
	public function calculo_subtotal($monto) {
	  	return $monto/1.18;
	}

	public function generar_codigo($basedatos,$cantidad) {

	  		// maximo valor de la tabla referente
			$tabla = DB::table($basedatos)
            ->select(DB::raw('max(codigo) as codigo'))
            ->get();

            //conversion a string y suma uno para el siguiente id
            $idsuma = (int)$tabla[0]->codigo + 1;

		  	//concatenar con ceros
		  	$correlativocompleta = str_pad($idsuma, $cantidad, "0", STR_PAD_LEFT); 

	  		return $correlativocompleta;

	}

	public function generar_lote($basedatos,$cantidad) {

	  		// maximo valor de la tabla referente
			$tabla = DB::table($basedatos)
            ->select(DB::raw('max(lote) as lote'))
            ->get();

            //conversion a string y suma uno para el siguiente id
            $idsuma = (int)$tabla[0]->lote + 1;

		  	//concatenar con ceros
		  	$lotecompleta = str_pad($idsuma, $cantidad, "0", STR_PAD_LEFT); 

	  		return $lotecompleta;

	}


	public function tiene_perfil($empresa_id,$centro_id,$usuario_id) {

		$perfiles 		=   WEBUserEmpresaCentro::where('empresa_id','=',$empresa_id)
							->where('centro_id','=',$centro_id)
							->where('usuario_id','=',$usuario_id)
							->where('activo','=','1')
							->first();

		if(count($perfiles)>0){
			return true;
		}else{
			return false;
		}	

	}

	public function precio_regla_calculo_menor_cero($producto_id,$cliente_id,$mensaje,$tiporegla,$regla_id) {

		$mensaje					=   $mensaje;
		$error						=   false;
		$precio 					=   WEBPrecioProducto::where('producto_id','=',$producto_id)
								    	->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
					    				->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->first();

		$regla 						=   WEBRegla::where('id','=',$regla_id)->first();

		$calculo 					= 	$this->calculo_precio_regla($regla->tipodescuento,$precio->precio,$regla->descuento,$regla->descuentoaumento);

		if($calculo < 0 && $regla->descuentoaumento <> 'AU'){
			$mensaje = 'La regla afecta al precio del producto en un valor negativo';
			$error   = true;
		}								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		return $response;
	}


	public function calculo_precio_regla($tipodescuento,$precio,$descuento,$aumentodescuento) {


		// precio regular 



		//calculo entre el producto y la regla
		$calculo = 0;
		if($tipodescuento == 'IMP'){
			if($aumentodescuento == 'AU'){
				$calculo = $precio + $descuento;
			}else{
				$calculo = $precio - $descuento;
			}
		}else{
			if($aumentodescuento == 'AU'){
				$calculo = $precio + $precio * ($descuento/100);
			}else{
				$calculo = $precio - $precio * ($descuento/100);
			}
		}
		return $calculo;

	}


	public function la_regla_esta_desactivada($regla_id,$mensaje) {

		$mensaje					=   $mensaje;
		$error						=   false;
		$cantidad 					=  	0;

		$regla 						=   WEBRegla::where('estado','=','CU')->where('id','=',$regla_id)->get();

		if(count($regla) > 0){
			$mensaje = 'Esta regla esta "CERRADA" no se puede actualizar';
			$error   = true;
		}								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		return $response;

	}



	public function tiene_regla_activa($producto_id,$cliente_id,$contrato_id,$mensaje,$tiporegla) {

		$mensaje					=   $mensaje;
		$error						=   false;
		$cantidad 					=  	0;

		$listareglas = 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglaproductoclientes.regla_id', '=', 'WEB.reglas.id')
						->where('producto_id','=',$producto_id)
						->where('WEB.reglas.tiporegla','=',$tiporegla)
						->where('cliente_id','=',$cliente_id)
						->where('contrato_id','=',$contrato_id)
						->where('WEB.reglaproductoclientes.activo','=','1')
						->get();

		if($tiporegla=='PNC' or $tiporegla=='POV' or $tiporegla=='PRD'){
			$cantidad = 6; //osea si tiene 7 reglas
		}

		if($tiporegla=='NEG'){
			$cantidad = 0; //osea si tiene 2 reglas
		}

		if($tiporegla=='CUP'){
			$cantidad = 0; //osea si tiene 2 reglas
		}


		if(count($listareglas) > $cantidad ){
			$mensaje = 'Tienes una regla activa por el momento';
			$error   = true;
		}								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		return $response;

	}

	public function tiene_regla_repetida_departamento($producto_id,$cliente_id,$contrato_id,$departamento_id_pr,$mensaje,$tipo){

		$mensaje					=   $mensaje;
		$error						=   false;
		$cantidad 					=  	0;
		$departamento_id_pr 		= 	trim($departamento_id_pr);


		$listareglas = 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglaproductoclientes.regla_id', '=', 'WEB.reglas.id')
						->where('WEB.reglaproductoclientes.producto_id','=',$producto_id)
						->where('WEB.reglaproductoclientes.cliente_id','=',$cliente_id)
						->where('WEB.reglaproductoclientes.contrato_id','=',$contrato_id)
						->where('WEB.reglas.departamento_id','=',$departamento_id_pr)						
						->where('WEB.reglaproductoclientes.activo','=','1')
						->get();


		if(count($listareglas) > 0){
			$mensaje = 'Este departamento ya tiene un precio regular';
			$error   = true;
		}								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		return $response;

	}


	public function tiene_regla_repetida($producto_id,$cliente_id,$contrato_id,$regla_id,$mensaje,$tiporegla){

		$mensaje					=   $mensaje;
		$error						=   false;
		$cantidad 					=  	0;

		$listareglas = 	WEBReglaProductoCliente::where('producto_id','=',$producto_id)
						->where('cliente_id','=',$cliente_id)
						->where('contrato_id','=',$contrato_id)
						->where('regla_id','=',$regla_id)						
						->where('activo','=','1')
						->get();

		if(count($listareglas) > 0){
			$mensaje = 'Esta que registra regla repetida';
			$error   = true;
		}								

		$response[] = array(
			'error'           		=> $error,
			'mensaje'      			=> $mensaje
		);

		return $response;

	}




	public function reglas_actualizar_modal($producto_id,$cliente_id,$contrato_id,$tiporegla) {

		$listareglas = 	WEBReglaProductoCliente::join('WEB.reglas', 'WEB.reglaproductoclientes.regla_id', '=', 'WEB.reglas.id')
						->select('WEB.reglaproductoclientes.*')
						->where('producto_id','=',$producto_id)
						->where('WEB.reglas.tiporegla','=',$tiporegla)
						->where('cliente_id','=',$cliente_id)
						->where('contrato_id','=',$contrato_id)
						->where('WEB.reglaproductoclientes.activo','=','1')
						->orderBy('WEB.reglaproductoclientes.activo', 'desc')
						->orderBy('WEB.reglaproductoclientes.fecha_crea', 'desc')
						//->take(5)
						->get();

	 	return  $listareglas;
	}

	public function combo_activas_regla_tipo($tipo,$nombreselect) {


		if($tipo == 'PRD'){

			$lista_activas 		= 	WEBRegla::join('CMP.CATEGORIA', 'COD_CATEGORIA', '=', 'departamento_id')
									->where('activo','=',1)
									->where('tiporegla','=',$tipo)
									->where('estado','=','PU')
									//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
	    							->where('centro_id','=',Session::get('centros')->COD_CENTRO)
									->select('id', DB::raw("(nombre + ' ' + NOM_CATEGORIA + ' ' + CASE WHEN tipodescuento = 'POR' THEN '%' WHEN tipodescuento = 'IMP' THEN 'S/.' END + CAST(descuento AS varchar(100)) ) AS nombre"))
									->pluck('nombre','id')
									->toArray();			
		}else{

		


        	$cod_centro 		= 	Session::get('centros')->COD_CENTRO;
        	$cod_empresa 		= 	Session::get('empresas')->COD_EMPR;
        	$fecha_actual 	    = 	date('Y-m-d H:i');

			$lista_activas 		= 	WEBRegla::where('activo','=',1)
									->where('tiporegla','=',$tipo)
									->where('estado','=','PU')
									//->where('empresa_id','=',$cod_empresa)
	    							->where('centro_id','=',$cod_centro)
	    							->whereRaw('Convert(varchar(16), fechainicio, 120) <= ?', [$fecha_actual])
	    							->where(function ($query) use ($fecha_actual) {
									    $query->whereRaw('Convert(varchar(16), fechafin, 120) >= ?', [$fecha_actual])
									          ->orWhere('fechafin', '=', '1900-01-01 00:00:00.000');
									})
									->select('id', DB::raw("(nombre + ' ' + CASE WHEN tipodescuento = 'POR' THEN '%' WHEN tipodescuento = 'IMP' THEN 'S/.' END  + CAST(descuento AS varchar(100)) ) AS nombre"))
									->pluck('nombre','id')
									->toArray();


		}




		$comboreglas 		= 	array('' => "Seleccione ".$nombreselect) + $lista_activas;

	 	return  $comboreglas;

	}

	
	public function nombre_producto_seleccionado($idproducto) {

		$nombre 						= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
											->where('producto_id','=',$idproducto)
					    					->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
					    					->where('centro_id','=',Session::get('centros')->COD_CENTRO)
	    					 				->first();
	 	return    $nombre->NOM_PRODUCTO;					 			
	}


	public function lista_productos_precio_buscar($idproducto,$tipoprecio_id,$contrato_id) {

		if($idproducto != ''){

			$lista_producto_precio 		= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
											->where('producto_id','=',$idproducto)
					    					->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
					    					->where('centro_id','=',Session::get('centros')->COD_CENTRO)
	    					 				->orderBy('NOM_PRODUCTO', 'asc')
	    					 				->get();
		}else{


			if($tipoprecio_id == '1'){

				$arrayproducto_id 				= 	WEBPrecioProductoContrato::where('WEB.precioproductocontratos.activo','=','1')
													->where('WEB.precioproductocontratos.ind_contrato','=','1')												
													->where('WEB.precioproductocontratos.empresa_id','=',Session::get('empresas')->COD_EMPR)
													->where('WEB.precioproductocontratos.centro_id','=',Session::get('centros')->COD_CENTRO)
													->where('WEB.precioproductocontratos.contrato_id','=',$contrato_id)
													->pluck('WEB.precioproductocontratos.producto_id')->toArray();


				$lista_producto_precio 		= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
						    					->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
						    					->where('centro_id','=',Session::get('centros')->COD_CENTRO)
						    					->whereIn('producto_id',$arrayproducto_id)
		    					 				->orderBy('NOM_PRODUCTO', 'asc')->get();

			}else{

				$lista_producto_precio 		= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
						    					->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
						    					->where('centro_id','=',Session::get('centros')->COD_CENTRO)
						    					//->whereIn('producto_id',$arrayproducto_id)
		    					 				->orderBy('NOM_PRODUCTO', 'asc')->get();

			}



		}

	 	return    $lista_producto_precio;					 			
	}


	public function producto_buscar($idproducto) {

		$producto 		= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
	    					->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
	    					->where('centro_id','=',Session::get('centros')->COD_CENTRO)
							->where('producto_id','=',$idproducto)
    					 	->first();

	 	return    $producto;					 			
	}

	public function regla_buscar($regla_id){

		$regla 		= 	WEBRegla::where('id','=',$regla_id)
    					->first();

	 	return    $regla;					 			
	}

	public function cliente_buscar($cliente_id) {

		$cliente 		= 	WEBListaCliente::where('id','=',$cliente_id)
    						->first();

	 	return    $cliente;					 			
	}



	public function lista_productos_precio() {

		$lista_producto_precio 		= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
					    				->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
					    				->where('centro_id','=',Session::get('centros')->COD_CENTRO)
	    					 			->orderBy('NOM_PRODUCTO', 'asc')->get();
	 	return  $lista_producto_precio;				 			
	}


	public function combo_nombres_lista_productos() {

		$lista_producto_precio 		= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
						    			->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
						    			->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->pluck('NOM_PRODUCTO','producto_id')
										->take(10)
										->toArray();

		$combolistaproductos  		= 	array('' => "Seleccione producto") + $lista_producto_precio;

	 	return  $combolistaproductos;					 			
	}

	//18-10-2019
	public function combo_lista_empresa() {

		$lista_empresas 			= 	STDEmpresa::where('COD_ESTADO','=','1')
										->where('IND_SISTEMA','=','1')
										->pluck('NOM_EMPR','COD_EMPR')
										->toArray();

		$comboempresas  			= $lista_empresas;

	 	return  $comboempresas;					 			
	}


	//18-10-2019
	public function combo_lista_centro() {

		$lista_centros 				= 	ALMCentro::where('COD_ESTADO','=','1')
										->pluck('NOM_CENTRO','COD_CENTRO')
										->toArray();

		$combocentros  				= array('' => "Seleccione centro") + $lista_centros;

	 	return  $combocentros;					 			
	}


	public function combo_lista_centro_todos() {

		$lista_centros 				= 	ALMCentro::where('COD_ESTADO','=','1')
										->pluck('NOM_CENTRO','COD_CENTRO')
										->toArray();
		$combocentros  				= 	array('' => "Seleccione centro",'TODOS' => 'TODOS') + $lista_centros;

	 	return  $combocentros;					 			
	}


	public function combo_lista_centro_array_filtro($array_centro_id) {

		$lista_centros 				= 	ALMCentro::where('COD_ESTADO','=','1')
										->whereIn('COD_CENTRO',$array_centro_id)
										->pluck('NOM_CENTRO','COD_CENTRO')
										->toArray();

		$combocentros  				=   array('' => "Seleccione centro") + $lista_centros;

	 	return  $combocentros;					 			
	}


	public function combo_con_sin_muestra() {

		$combo  				=   array('0' => "Con muestra",'1' => "Sin muestra");

	 	return  $combo;					 			
	}


	public function combo_estados_web($tipo) {

		$lista_centros 				= 	WEBEstado::where('activo','=','1')
										->where('tipoestado','=',$tipo)
										->pluck('nombre','id')
										->toArray();

		$combocentros  				=   array('' => "Seleccione Estado") + $lista_centros;

	 	return  $combocentros;				 			
	}



	//18-10-2019
	public function combo_lista_productos() {

		$lista_producto_precio 		= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
						    			->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
						    			->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->pluck('NOM_PRODUCTO','producto_id')
										->toArray();
		$combolistaproductos  		= 	array('' => "Seleccione producto") + $lista_producto_precio;

	 	return  $combolistaproductos;					 			
	}




	public function combo_lista_productos_todos() {

		$lista_producto_precio 		= 	WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
						    			->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
						    			->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->pluck('NOM_PRODUCTO','producto_id')
										->toArray();
		$combolistaproductos  		= 	array('' => "Seleccione producto",'1' => "TODOS") + $lista_producto_precio;

	 	return  $combolistaproductos;					 			
	}



	public function combo_nombres_lista_clientes() {

		$listaclientes   		=	WEBListaCliente::select('NOM_EMPR')
					    			->where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
					    			->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
									->pluck('NOM_EMPR','NOM_EMPR')
									->take(10)
									->toArray();

		$combolistaclientes  	= 	array('' => "Seleccione clientes") + $listaclientes;
		return $combolistaclientes;					 			
	}


	public function combo_categoria_txt_grupo($txt_grupo) {

		$listacategoria   		=	CMPCategoria::where('TXT_GRUPO','=',$txt_grupo)
									->pluck('NOM_CATEGORIA','COD_CATEGORIA')
									->toArray();

		$comboestados  			= 	array('' => "Seleccione estados",'TODO' => "TODO") + $listacategoria;
		return $comboestados;					 			
	}


	public function combo_categoria_txt_grupo_parcialmente($txt_grupo) {

		$listacategoria   		=	CMPCategoria::where('TXT_GRUPO','=',$txt_grupo)
									->pluck('NOM_CATEGORIA','COD_CATEGORIA')
									->toArray();

		$comboestados  			= 	array('' => "Seleccione estados",'TODO' => "TODO",'PARCIALMENTEATENDIDA' => "PARCIALMENTE ATENDIDA") + $listacategoria;
		return $comboestados;					 			
	}



	public function respuestavacio($cliente,$producto_select) {

		if(!is_null($cliente)){
			return false;
		}
		if(!is_null($producto_select)){
			return false;
		}

		return true;
	}

	public function array_id_clientes_top($cantidad){
		$arrayidclientes   			=	WEBListaCliente::where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
					    				->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
										->take($cantidad)->pluck('id')->toArray();
		return $arrayidclientes;
	}

	public function combotipodocumentoxclientes() {

		$arraytipodocumentocliente   	=	WEBListaCliente::select('COD_TIPO_DOCUMENTO','NOM_TIPO_DOCUMENTO')
											->groupBy('COD_TIPO_DOCUMENTO')
											->groupBy('NOM_TIPO_DOCUMENTO')
											->where('COD_TIPO_DOCUMENTO','!=','')
					    					->where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
					    					->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
											->pluck('NOM_TIPO_DOCUMENTO','COD_TIPO_DOCUMENTO')
											->toArray();

		$combotipodocumento  			= 	array('' => "Seleccione tipo documento") + $arraytipodocumentocliente;

		return $combotipodocumento;

	}

	public function getUrl($idopcion,$accion) {

	  	//decodificar variable
	  	$decidopcion = Hashids::decode($idopcion);
	  	//ver si viene con letras la cadena codificada
	  	if(count($decidopcion)==0){ 
	  		return Redirect::back()->withInput()->with('errorurl', 'Indices de la url con errores'); 
	  	}

	  	//concatenar con ceros
	  	$idopcioncompleta = str_pad($decidopcion[0], 8, "0", STR_PAD_LEFT); 
	  	//concatenar prefijo

	  	// hemos hecho eso porque ahora el prefijo va hacer fijo en todas las empresas que 1CIX
		//$prefijo = Local::where('activo', '=', 1)->first();
		//$idopcioncompleta = $prefijo->prefijoLocal.$idopcioncompleta;
		$idopcioncompleta = '1CIX'.$idopcioncompleta;

	  	// ver si la opcion existe
	  	$opcion =  WEBRolOpcion::where('opcion_id', '=',$idopcioncompleta)
	  			   ->where('rol_id', '=',Session::get('usuario')->rol_id)
	  			   ->where($accion, '=',1)
	  			   ->first();

	  	if(count($opcion)<=0){
	  		return Redirect::back()->withInput()->with('errorurl', 'No tiene autorización para '.$accion.' aquí');
	  	}
	  	return 'true';

	 }

	public function prefijomaestra() {

		$prefijo = '1CIX';
	  	return $prefijo;
	}

	public function getCreateIdMaestra($tabla) {

  		$id="";

  		// maximo valor de la tabla referente
		$id = DB::table($tabla)
        ->select(DB::raw('max(SUBSTRING(id,5,8)) as id'))
        ->get();

        //conversion a string y suma uno para el siguiente id
        $idsuma = (int)$id[0]->id + 1;

	  	//concatenar con ceros
	  	$idopcioncompleta = str_pad($idsuma, 8, "0", STR_PAD_LEFT);

	  	//concatenar prefijo
		$prefijo = $this->prefijomaestra();

		$idopcioncompleta = $prefijo.$idopcioncompleta;

  		return $idopcioncompleta;	

	}


	public function getCreateIdMaestraEstado($tabla,$prefijo,$tipo) {

  		$id="";

  		// maximo valor de la tabla referente
		$id = DB::table($tabla)
		->where('tipoestado','=',$tipo)
        ->select(DB::raw('max(SUBSTRING(id,5,8)) as id'))
        ->get();

        //conversion a string y suma uno para el siguiente id
        $idsuma = (int)$id[0]->id + 1;

	  	//concatenar con ceros
	  	$idopcioncompleta = str_pad($idsuma, 8, "0", STR_PAD_LEFT);

	  	//concatenar prefijo
		$prefijo = $prefijo;

		$idopcioncompleta = $prefijo.$idopcioncompleta;

  		return $idopcioncompleta;	

	}



	public function decodificarmaestra($id) {

	  	//decodificar variable
	  	$iddeco = Hashids::decode($id);
	  	//ver si viene con letras la cadena codificada
	  	if(count($iddeco)==0){ 
	  		return ''; 
	  	}
	  	//concatenar con ceros
	  	$idopcioncompleta = str_pad($iddeco[0], 8, "0", STR_PAD_LEFT); 
	  	//concatenar prefijo

		//$prefijo = Local::where('activo', '=', 1)->first();

		// apunta ahi en tu cuaderno porque esto solo va a permitir decodifcar  cuando sea el contrato del locl en donde estas del resto no 
		//¿cuando sea el contrato del local?
		$prefijo = $this->prefijomaestra();
		$idopcioncompleta = $prefijo.$idopcioncompleta;
	  	return $idopcioncompleta;

	}


	public function decodificarid($id,$prefijo) {

	  	//decodificar variable
	  	$iddeco = Hashids::decode($id);
	  	//ver si viene con letras la cadena codificada
	  	if(count($iddeco)==0){ 
	  		return ''; 
	  	}
	  	//concatenar con ceros
	  	$idopcioncompleta = str_pad($iddeco[0], 13, "0", STR_PAD_LEFT); 
	  	//concatenar prefijo
		$idopcioncompleta = $prefijo.$idopcioncompleta;
	  	return $idopcioncompleta;

	}

	public function codecupon(){
	  	return Hashids::encode(Keygen::numeric(10)->generate());
	}


	public function NotificarEstadoPedido($idpedido){
		$pedido 						=   WEBPedido::where('id','=',$idpedido)->first();
		$vendedor = $this->data_usuario($pedido->usuario_crea);

		switch ($pedido->estado_id) {
			case 'EPP0000000000002':
				$wm    = WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00005')->first();
				
				$sms="Un nuevo pedido ".$pedido->codigo." fue generado correctamente.";
				break;
			case 'EPP0000000000003':
				$wm    = WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00006')->first();
				$sms="El pedido ".$pedido->codigo." fue autorizado correctamente.";
				
				break;
			case 'EPP0000000000004':
				$wm    = WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00007')->first();
				$sms="El pedido ".$pedido->codigo." fue ejecutado correctamente.";
				break;
			case 'EPP0000000000005':
				$wm    = WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00006')->first();
				$sms="El pedido ".$pedido->codigo." ha sido rechazado.";
			    break;

		}

		return $this->SendSMS($wm->gsm.','.$vendedor->gsm,$sms);
    }

	public function SendSMS($gsm,$sms){

    $apikey = "55A2B819FF77";
    $apicard = "7050509039";
	$fields_string = "";
	$smstype = "0"; // 0: remitente largo, 1: remitente corto


    $smsnumber = $gsm;
    $smstext = $sms;
  

    //Preparamos las variables que queremos enviar
    $url = 'http://api2.gamacom.com.pe/smssend'; // Para HTTPS $url = 'https://api3.gamanet.pe/smssend'; 
    $fields = array(
                        'apicard'=>urlencode($apicard),
                        'apikey'=>urlencode($apikey),
                        'smsnumber'=>urlencode($smsnumber),
                        'smstext'=>urlencode($smstext),
                        'smstype'=>urlencode($smstype)
                );

    //Preparamos el string para hacer POST (formato querystring)
    foreach($fields as $key=>$value) { 
       $fields_string .= $key.'='.$value.'&'; 
    }
    $fields_string = rtrim($fields_string,'&');


    //abrimos la conexion
    $ch = curl_init();

    //configuramos la URL, numero de variables POST y los datos POST
    curl_setopt($ch,CURLOPT_URL,$url);
    //curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); //Descomentarlo si usa HTTPS
    curl_setopt($ch,CURLOPT_POST,count($fields));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

    //ejecutamos POST
    $result = curl_exec($ch);

    //cerramos la conexion
    curl_close($ch);

    //Resultado
    $array = json_decode($result,true);

	return "error:".$array["message"]."uniqueid:".$array["uniqueid"];
          
  }

  public function combo_categoria_activo_fijo($id_categoria='')
  {
	$combo_categorias_activos_fijos = WEBCategoriaActivoFijo::all()->pluck('nombre','id');
	if($id_categoria!=''){
		$categoria_sel = WEBCategoriaActivoFijo::find($id_categoria);
		$combo_categorias_activos_fijos = array($categoria_sel->id => $categoria_sel->nombre) + $combo_categorias_activos_fijos->toArray();
	}
	return $combo_categorias_activos_fijos;
  }
  
  public function combo_producto($id_producto='') 
  {
	$productos = ALMProducto::all()->pluck('NOM_PRODUCTO','COD_PRODUCTO');
	if($id_producto!=''){
		$producto_sel = ALMProducto::find($id_producto);
		$productos = array($producto_sel->COD_PRODUCTO => $producto_sel->NOM_PRODUCTO) + $productos->toArray();
	}
	return $productos;
  }

  public function combo_obra($id_obra='') 
  {
	$obras = WEBActivoFijo::where('modalidad_adquisicion','=','OBRA')->pluck('nombre','id');
	if($id_obra!=''){
		$obra_sel = WEBActivoFijo::find($id_obra);
		$obras = array($obra_sel->id => $obra_sel->nombre) + $obras->toArray();
	}
	return $obras;
  }

  public function combo_activo_fijo() 
  {
	$empresa_id = Session::get('empresas')->COD_EMPR;
	$activosfijos =  DB::table('WEB.activosfijos')
						  ->where('WEB.activosfijos.estado_depreciacion','<>','DEPRECIADO')
						  ->where('WEB.activosfijos.estado','<>','BAJA')
						  ->where('WEB.activosfijos.tipo_activo','<>','COMPUESTO')
						  ->where('WEB.activosfijos.cod_empresa','=',$empresa_id)
                          ->select('id', 'nombre')
                          ->get()
						  ->pluck('nombre','id')
						  ->toArray();
	return $activosfijos;
  }

  public function combo_mes() 
  {
	$meses = array(1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
					9=>'Setiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre');
	return $meses;
  }

  public function dias_mes($mes){
	return cal_days_in_month(CAL_GREGORIAN, $mes, date("Y"));
  }

  public function combo_tipo_activo_fijo($tipo='')
  {
	$estados = array('INDIVIDUAL' => 'INDIVIDUAL', 'PRINCIPAL' => 'PRINCIPAL', 'COMPUESTO' => 'COMPUESTO');
	if($tipo != ''){
		$tipo_sel = array($tipo => $tipo);
		$estados = $tipo_sel + $estados;
	}
	return $estados;
  }
  
  public function combo_estado_activo_fijo($estado = '')
  {
	$estados = array('OPERATIVO' => 'OPERATIVO', 'BAJA' => 'BAJA');
	if($estado != ''){
		$estado_sel = array($estado => $estado);
		$estados = $estado_sel + $estados;
	}
	return $estados;
  }

  public function combo_estado_conservacion_activo_fijo($estado_conservacion = '')
  {
	$estados = array('BUENO' => 'BUENO', 'REGULAR' => 'REGULAR', 'MALO' => 'MALO');
	if($estado_conservacion != ''){
		$estado_conservacion_sel = array($estado_conservacion => $estado_conservacion);
		$estados = $estado_conservacion_sel + $estados;
	}
	return $estados;
  }

  public function getCreateIdActivoFijo($tabla) {
	$id="";
	$id = DB::table($tabla)
			  ->select(DB::raw('max(SUBSTRING(id,5,8)) as id'))
			  ->get();
	$idsuma = (int)$id[0]->id + 1;
	$idopcioncompleta = str_pad($idsuma, 8, "0", STR_PAD_LEFT);
	$prefijo = 'ACTF';
	$idopcioncompleta = $prefijo.$idopcioncompleta;
	return $idopcioncompleta;	
  }

  public function getCreateIdDepreciacionActivoFijo($tabla)
  {
	$id="";
	$id = DB::table($tabla)
			  ->select(DB::raw('max(SUBSTRING(id,5,8)) as id'))
			  ->get();
	$idsuma = (int)$id[0]->id + 1;
	$idopcioncompleta = str_pad($idsuma, 8, "0", STR_PAD_LEFT);
	$prefijo = 'DEPR';
	$idopcioncompleta = $prefijo.$idopcioncompleta;
	return $idopcioncompleta;	
  }

  public function getCreateIdAsientoContable($tabla)
  {
	$id="";
	$id = DB::table($tabla)
			  ->select(DB::raw('max(SUBSTRING(COD_ASIENTO,7,10)) as id'))
			  ->get();
	$idsuma = (int)$id[0]->id + 1;
	$idopcioncompleta = str_pad($idsuma, 10, "0", STR_PAD_LEFT);
	$prefijo = 'ICCHAC';
	$idopcioncompleta = $prefijo.$idopcioncompleta;	
	return $idopcioncompleta;	
  }
 
  public function getCreateIdAsientoContableMovimiento($tabla)
  {
	$id="";
	$id = DB::table($tabla)
			  ->select(DB::raw('max(SUBSTRING(COD_ASIENTO_MOVIMIENTO,7,10)) as id'))
			  ->get();
	$idsuma = (int)$id[0]->id + 1;
	$idopcioncompleta = str_pad($idsuma, 10, "0", STR_PAD_LEFT);
	$prefijo = 'ICCHAM';
	$idopcioncompleta = $prefijo.$idopcioncompleta;
	return $idopcioncompleta;	
  }

  public function getCreateINumeroAsiento($tabla)
  {
	$id="";
	$id = DB::table($tabla)
			  ->select(DB::raw('max(SUBSTRING(NRO_ASIENTO,3,7)) as id'))
			  ->get();
	$idsuma = (int)$id[0]->id + 1;
	$idopcioncompleta = str_pad($idsuma, 7, "0", STR_PAD_LEFT);
	$prefijo = 'RJ';
	$idopcioncompleta = $prefijo.$idopcioncompleta;
	return $idopcioncompleta;	
  }
  
  public function compararFechas($fecha_inicio, $fecha_fin){
	$fechai=date_create($fecha_inicio);
	$fechaf=date_create($fecha_fin);
	$diff=date_diff($fechai,$fechaf);
	return $diff->format("%R%a");	
  }
  
  function diasAnio($year){
	if(date('L',mktime(1,1,1,1,1,$year))){
		$dias_anio = 366;
	} else {
		$dias_anio = 365;
	}
	return $dias_anio;
  }

  	public function combo_lista_quitar_centro_array_filtro($codcentro) {

		$array_centro_id 			=   ['CEN0000000000001','CEN0000000000002','CEN0000000000004','CEN0000000000006'];	
		$array_centro_id 			= 	array_diff($array_centro_id, array($codcentro));
		
		$lista_centros 				= 	ALMCentro::where('COD_ESTADO','=','1')
										->whereIn('COD_CENTRO',$array_centro_id)
										->pluck('NOM_CENTRO','COD_CENTRO')
										->toArray();

		$combocentros  				=   array('' => "Seleccione centro") + $lista_centros;

	 	return  $combocentros;					 			
	}

		public function trs_calcular_cabecera_total($productos) {

		$total 						=   0.0000;
		$productos 					= 	json_decode($productos, true);

		foreach($productos as $obj){
			$total = $total + (float)$obj['peso_producto']*(float)$obj['cantidad_producto'];
		}
		return $total;
	}
	//@DPZ1
	public function llenar_array_productos_temp($correlativo,$transferencia_id,$transdet_id,$tipo_operacion,$fecha_entrega,	
											$hora_entrega,$cliente_id,$cliente_nom,
										   $producto_id,$producto_nombre,$producto_peso,$unidad_medida_id,$nombre_unidad_medida,
										   $cantidad_pendiente,$cantidad_atender,$cantidad_excedente,$paquete,$peso_total,$departamento_id,$departamento_nom,
										   $provincia_id,$provincia_nom,$distrito_id,$distrito_nom,$direccion_cli_id,
										   $direccion_cli_nom){

		return						array(
											"correlativo"				=> $correlativo,
											"transferencia_id" 			=> $transferencia_id,
											"transferenciadetalle_id"	=> $transdet_id,
											"tipo_operacion"			=> $tipo_operacion,
											"fecha_entrega" 			=> $fecha_entrega,
											"hora_entrega" 				=> $hora_entrega,
											"cliente_id" 				=> $cliente_id,
											"cliente_nom" 				=> $cliente_nom,
								            "producto_id" 				=> $producto_id,
								            "producto_nombre" 			=> $producto_nombre,
								            "producto_peso" 			=> $producto_peso,
								            "unidad_medida_id" 			=> $unidad_medida_id,
								            "nombre_unidad_medida" 		=> $nombre_unidad_medida,
								            "cantidad_pendiente" 		=> $cantidad_pendiente,
								            "cantidad_atender" 			=> $cantidad_atender,	
											"cantidad_excedente"		=> $cantidad_excedente,						
								            "paquete" 					=> $paquete,								            
								            "peso_total" 				=> $peso_total,
								            "departamento_id" 			=> $departamento_id,
								            "departamento_nom" 			=> $departamento_nom,
								            "provincia_id" 				=> $provincia_id,
								            "provincia_nom" 			=> $provincia_nom,
								            "distrito_id" 				=> $distrito_id,
								            "distrito_nom" 				=> $distrito_nom,
								            "direccion_cli_id" 			=> $direccion_cli_id,
								            "direccion_cli_nom" 		=> $direccion_cli_nom,
								            "activo"     				=> 1,
								        );



	}

	//@DPZ1
	public function combo_almacen_pt($centro_id, $centro_destino_id){

		$centro_id 					= 	$centro_id;
		$lista_almacen 				= 	$this->lista_almacen($centro_id);
		$combo_almacen 				=	array();
		$array_almacen 				=	array();
		$centro    					=   ALMCentro::where('COD_CENTRO','=',$centro_destino_id)
										->select('NOM_CENTRO')
										->first();
		
		//dd($lista_almacen->fetch());

		while($row = $lista_almacen->fetch())
		{
			$cadena_de_texto = $row['NOM_ALMACEN'];
			
			$cadena_buscada   = 'TRANSITO';
			$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
			//se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
			if($posicion_coincidencia === false) {
				$cadena_buscada   = 'TRANSITO';
			}else{
				$posicion_coincidencia = strpos($cadena_de_texto, $centro->NOM_CENTRO);
				if($posicion_coincidencia !== false) {
					$combo_almacen	  =	array($row['COD_ALMACEN'] => $row['NOM_ALMACEN']) + $combo_almacen ;
				}
			}
		}
		return $combo_almacen;
	}
	

	public function configuracion_producto($producto_id){
		$config 			= 		 DB::table('ALM.CONFIG_PRODUCTO')->where('COD_PRODUCTO','=',$producto_id)
									->where('COD_EMPRESA','=',Session::get('empresas')->COD_EMPR)							
									->first();
		$ind_igv = 0;
		if($config){
			$ind_igv 			= 		$config->IND_IGV;
		}			
		return $ind_igv;	
	}

	// GENERAR CODIGOS CON EMPRESA Y CENTRO
	//@DPZ1
	public function getAbreviatura($tabla){		
		$abre = DB::table('WEB.tablaid')->where('tabla','=',$tabla)->first();
		return $abre[1];
	}

	public function getCreateIdMaestraBD($tabla) {

		$id="";

		// maximo valor de la tabla referente
		$id = DB::table($tabla)
		->select(DB::raw('max(SUBSTRING(id,7,8)) as id'))
		->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
		->where('centro_id','=',Session::get('centros')->COD_CENTRO)
		->get();

		//$abre = getAbreviatura($tabla);
		//dd(tabla);
		//conversion a string y suma uno para el siguiente id
		$idsuma = (int)$id[0]->id + 1;

		//concatenar con ceros
		$idopcioncompleta = str_pad($idsuma, 8, "0", STR_PAD_LEFT);

		//concatenar prefijo
		$prefijo = $this->prefijomaestraBD($tabla);

		$idopcioncompleta = $prefijo.$idopcioncompleta;

		return $idopcioncompleta;	

  	}

	public function prefijomaestraBD($tabla) {		
		$empresa = STDEmpresa::where('COD_EMPR','=', Session::get('empresas')->COD_EMPR)->first();
		$centro = ALMCentro::where('COD_CENTRO','=', Session::get('centros')->COD_CENTRO)->first();
		$abre = DB::table('WEB.tablaid')->where('tabla','=',$tabla)->first();
		
		$prefijo = $empresa->TXT_ABREVIATURA . $centro->TXT_ABREVIATURA . $abre->id;
		
		return $prefijo;	
	}

	public function generar_codigo_BD($basedatos,$cantidad) {

		  // maximo valor de la tabla referente
		$tabla = DB::table($basedatos)
		->select(DB::raw('max(codigo) as codigo'))
		->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
		->where('centro_id','=',Session::get('centros')->COD_CENTRO)
		->get();

		//conversion a string y suma uno para el siguiente id
		$idsuma = (int)$tabla[0]->codigo + 1;

		//concatenar con ceros
		$correlativocompleta = str_pad($idsuma, $cantidad, "0", STR_PAD_LEFT); 

		return $correlativocompleta;
	}

	public function decodificarmaestraBD($tabla, $id) {
		//decodificar variable
		$iddeco = Hashids::decode($id);
		//ver si viene con letras la cadena codificada
		if(count($iddeco)==0){ 
			return ''; 
		}
		//concatenar con ceros
		$idopcioncompleta = str_pad($iddeco[0], 8, "0", STR_PAD_LEFT); 		
		$prefijo = $this->prefijomaestraBD($tabla);
		$idopcioncompleta = $prefijo.$idopcioncompleta;
		
		return $idopcioncompleta;
 	}

	public function picking_detraccion_calculada($id_picking) {

        $stmt		= 		DB::connection('sqlsrv')->getPdo()
							->prepare('SET NOCOUNT ON;EXEC WEB.PICKING_DETRACCION_CALCULADA ?');

        $stmt->bindParam(1, $id_picking ,PDO::PARAM_STR);      
        $stmt->execute();

        $resultado = $stmt->fetch();
		
		if ($resultado){
			return 0;
		}			
		
		return 1;
 	 }

 	public function validate(Request $request) {
        $extensions = array("xls","xlsx","csv");

        $result = array($request->file('select_file')->getClientOriginalExtension());

        if(in_array($result[0],$extensions)){
            return true;
        }else{
            return false;
        }
    }
 	 	
}

