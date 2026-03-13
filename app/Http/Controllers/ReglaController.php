<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\CMPCategoria,App\WEBPrecioProducto,App\WEBPrecioProductoHistorial,App\WEBRegla;
use App\ALMProducto;
use App\WEBAsignarRegla;
use App\CMPContrato;
use Maatwebsite\Excel\Facades\Excel;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


class ReglaController extends Controller
{


	public function actionAjaxEliminarReglaLimiteCredito($asignarregla_id,$idopcion,Request $request)
	{

		$cabecera 					= 	WEBAsignarRegla::where('id','=',$asignarregla_id)->first();

		$cabecera->activo 	    	=  	0;
		$cabecera->fecha_mod 	    =  	$this->fechaactual;
		$cabecera->usuario_mod 		=  	Session::get('usuario')->id;
		$cabecera->save();

		$regla 						=	WEBRegla::where('id','=',$cabecera->regla_id)->first();

	 	return Redirect::to('/gestion-de-asignar-regla-limite-credito/'.$idopcion)->with('bienhecho', 'Regla Limite credito '.$regla->codigo.' eliminado con exito');

	}


	public function actionAjaxAsignarReglaLimiteCredito(Request $request)
	{

	    $data_cod_cliente 			= 	$request['data_cod_cliente'];
	    $regla_id 					= 	$request['regla_id'];

		$idreglaproductocliente 	= 	$this->funciones->getCreateIdMaestra('WEB.asignarreglas');

		$cabecera            	 	=	new WEBAsignarRegla;
		$cabecera->id 	     	 	=  	$idreglaproductocliente;
		$cabecera->regla_id 	    =  	$regla_id;
		$cabecera->prefijo 	    	=  	'RLC'; // REGLAS LIMITE CREDITO
		$cabecera->tabla 	    	=  	'STD.EMPRESA';
		$cabecera->tabla_id 	    =  	$data_cod_cliente;
		$cabecera->fecha_crea 	    =  	$this->fechaactual;
		$cabecera->empresa_id 		=  	Session::get('empresas')->COD_EMPR;
		$cabecera->centro_id 		=  	Session::get('centros')->COD_CENTRO;
		$cabecera->usuario_crea 	=  	Session::get('usuario')->id;
		$cabecera->save();

	}


	public function actionAjaxModalListaClienteJefeRegla(Request $request)
	{

		$jefe_id 				= 	$request['jefe_id'];
		$jefe   				=	CMPCategoria::where('COD_CATEGORIA','=',$jefe_id)
									->first();

		$lista_clientes 		= 	$this->funciones->lista_clientes_jefe_regla($jefe_id);
		$comboregla	 			= 	$this->funciones->combo_regla_limite_credito();

		$funcion 				= 	$this;

		return View::make('regla/modal/ajax/limitecredito',
						 [
						 	'lista_clientes'=> $lista_clientes,
						 	'jefe_id' 		=> $jefe_id,
						 	'funcion' 		=> $funcion,
						 	'comboregla' 	=> $comboregla,
						 	'jefe' 			=> $jefe,
						 ]);

	}



	public function actionAjaxEliminarReglaDiasVencimiento($asignarregla_id,$idopcion,Request $request)
	{

		$cabecera 					= 	WEBAsignarRegla::where('id','=',$asignarregla_id)->first();

		$cabecera->activo 	    	=  	0;
		$cabecera->fecha_mod 	    =  	$this->fechaactual;
		$cabecera->usuario_mod 		=  	Session::get('usuario')->id;
		$cabecera->save();

		$regla 						=	WEBRegla::where('id','=',$cabecera->regla_id)->first();


	 	return Redirect::to('/gestion-de-asignar-regla-dias-vencimiento/'.$idopcion)->with('bienhecho', 'Regla Dias de Vencimiento '.$regla->codigo.' eliminado con exito');

	}


	public function actionAjaxAsignarMasivoReglaDiasVencimiento(Request $request)
	{
	    $selected_ids 		= 	$request['selected_ids']; // Array of COD_ORDEN
	    $regla_id 			= 	$request['regla_id'];
	    $fecha_compromiso 	= 	$request['fecha_compromiso'];
	    $autorizado_id 		= 	$request['autorizado_id'];
	    $glosa 				= 	$request['glosa'];
	    $cuenta_id 			= 	$request['cuenta_id'];
	    $idopcion 			= 	$request['idopcion'];

	    $user_autorizado 	= 	DB::table('users')->where('id','=',$autorizado_id)->first();

        foreach($selected_ids as $cod_orden) {
            $idreglaproductocliente 	= 	$this->funciones->getCreateIdMaestra('WEB.asignarreglas');

            $cabecera            	 	=	new WEBAsignarRegla;
            $cabecera->id 	     	 	=  	$idreglaproductocliente;
            $cabecera->regla_id 	    =  	$regla_id;
            $cabecera->prefijo 	    	=  	'RDV'; // REGLAS DIAS VENCIMIENTO
            $cabecera->tabla 	    	=  	'CMP.ORDEN';
            $cabecera->tabla_id 	    =  	$cod_orden;

            $cabecera->fecha_compromiso =  	date_format(date_create($fecha_compromiso), 'Y-m-d');
            $cabecera->autorizado_id 	=  	$user_autorizado->id;
            $cabecera->autorizado_nombre=  	$user_autorizado->nombre;
            $cabecera->glosa 		    =  	$glosa;

            $cabecera->fecha_crea 	    =  	$this->fechaactual;
            $cabecera->empresa_id 		=  	Session::get('empresas')->COD_EMPR;
            $cabecera->centro_id 		=  	Session::get('centros')->COD_CENTRO;
            $cabecera->usuario_crea 	=  	Session::get('usuario')->id;
            $cabecera->save();
        }

		// Actualizar la lista del modal
	    $tipo_documento_id 		= 	'TDO0000000000003'; //boletas
	    $contrato 				=	CMPContrato::where('COD_CONTRATO','=',$cuenta_id)->first();
		$array_orden 			= 	$this->funciones->array_orden_venta_documento_fechas_cuenta_regla_nuevo($tipo_documento_id,$cuenta_id);
		$lista_deuda 			= 	$this->funciones->lista_deuda_cliente($contrato->COD_EMPR_CLIENTE,$this->fechaactual);
		$comboregla	 			= 	$this->funciones->combo_regla_descuento();

		$comboautorizados 		= 	DB::table('users')
									->whereIn('id', ['1CIX00000032', '1CIX00000046', '1CIX00000218'])
									->pluck('nombre', 'id')
									->toArray();

		$funcion 				= 	$this;

		$lista_modal_html 		= 	View::make('regla/modal/ajax/listaordenventa',
									 [
									 	'lista_deuda' 	=> $lista_deuda,
									 	'array_orden' 	=> $array_orden,
									 	'cuenta_id' 	=> $cuenta_id,
									 	'funcion' 		=> $funcion,
									 	'comboregla' 	=> $comboregla,
									 	'comboautorizados' => $comboautorizados,
									 ])->render();

		// Actualizar la lista del dashboard (fondo)
	    $lista_reglas 		= 	WEBAsignarRegla::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.asignarreglas.regla_id')
	    						->join('CMP.ORDEN', 'CMP.ORDEN.COD_ORDEN', '=', 'WEB.asignarreglas.tabla_id')
	    						->leftjoin('CMP.REFERENCIA_ASOC', 'CMP.REFERENCIA_ASOC.COD_TABLA', '=', 'CMP.ORDEN.COD_ORDEN')
	    						->leftjoin('CMP.DOCUMENTO_CTBLE', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'CMP.REFERENCIA_ASOC.COD_TABLA_ASOC')
	    						->select('WEB.reglas.*','CMP.ORDEN.*','CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_TIPO_PAGO as CP','WEB.asignarreglas.id as asignarregla_id')
	    						->where('WEB.asignarreglas.prefijo','=','RDV')
	    						->where('WEB.asignarreglas.activo','=','1')
	    						->where('CMP.REFERENCIA_ASOC.TXT_GLOSA','like','%DOCUMENTO INTERNO VENTAS%')
	    						->get();

	    $lista_background_html 	= 	View::make('regla/ajax/listaasignardv',
						 			[
						 				'lista_reglas' 		=> $lista_reglas,
						 				'idopcion' 			=> $idopcion,
						 			])->render();

	    return response()->json([
	        'lista_modal' 		=> $lista_modal_html,
	        'lista_background' 	=> $lista_background_html
	    ]);

	}

	public function actionAjaxAsignarReglaDiasVencimiento(Request $request)
	{
	    $data_cod_orden_venta 		= 	$request['data_cod_orden_venta'];
	    $regla_id 					= 	$request['regla_id'];
	    $fecha_compromiso 			= 	$request['fecha_compromiso'];
	    $autorizado_id 				= 	$request['autorizado_id'];
	    $glosa 						= 	$request['glosa'];
	    $cuenta_id 					= 	$request['cuenta_id'];
	    $idopcion 					= 	$request['idopcion'];

	    $user_autorizado 			= 	DB::table('users')->where('id','=',$autorizado_id)->first();


		$idreglaproductocliente 	= 	$this->funciones->getCreateIdMaestra('WEB.asignarreglas');

		$cabecera            	 	=	new WEBAsignarRegla;
		$cabecera->id 	     	 	=  	$idreglaproductocliente;
		$cabecera->regla_id 	    =  	$regla_id;
		$cabecera->prefijo 	    	=  	'RDV'; // REGLAS DIAS VENCIMIENTO
		$cabecera->tabla 	    	=  	'CMP.ORDEN';
		$cabecera->tabla_id 	    =  	$data_cod_orden_venta;

		$cabecera->fecha_compromiso =  	date_format(date_create($fecha_compromiso), 'Y-m-d');
		$cabecera->autorizado_id 	=  	$user_autorizado->id;
		$cabecera->autorizado_nombre=  	$user_autorizado->nombre;
		$cabecera->glosa 		    =  	$glosa;

		$cabecera->fecha_crea 	    =  	$this->fechaactual;
		$cabecera->empresa_id 		=  	Session::get('empresas')->COD_EMPR;
		$cabecera->centro_id 		=  	Session::get('centros')->COD_CENTRO;
		$cabecera->usuario_crea 	=  	Session::get('usuario')->id;
		$cabecera->save();

		// Actualizar la lista del modal
	    $tipo_documento_id 		= 	'TDO0000000000003'; //boletas
	    $contrato 				=	CMPContrato::where('COD_CONTRATO','=',$cuenta_id)->first();
		$array_orden 			= 	$this->funciones->array_orden_venta_documento_fechas_cuenta_regla_nuevo($tipo_documento_id,$cuenta_id);
		$lista_deuda 			= 	$this->funciones->lista_deuda_cliente($contrato->COD_EMPR_CLIENTE,$this->fechaactual);
		$comboregla	 			= 	$this->funciones->combo_regla_descuento();

		$comboautorizados 		= 	DB::table('users')
									->whereIn('id', ['1CIX00000032', '1CIX00000046', '1CIX00000218'])
									->pluck('nombre', 'id')
									->toArray();

		$funcion 				= 	$this;

		$lista_modal_html 		= 	View::make('regla/modal/ajax/listaordenventa',
									 [
									 	'lista_deuda' 	=> $lista_deuda,
									 	'array_orden' 	=> $array_orden,
									 	'cuenta_id' 	=> $cuenta_id,
									 	'funcion' 		=> $funcion,
									 	'comboregla' 	=> $comboregla,
									 	'comboautorizados' => $comboautorizados,
									 ])->render();

		// Actualizar la lista del dashboard (fondo)
	    $lista_reglas 		= 	WEBAsignarRegla::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.asignarreglas.regla_id')
	    						->join('CMP.ORDEN', 'CMP.ORDEN.COD_ORDEN', '=', 'WEB.asignarreglas.tabla_id')
	    						->leftjoin('CMP.REFERENCIA_ASOC', 'CMP.REFERENCIA_ASOC.COD_TABLA', '=', 'CMP.ORDEN.COD_ORDEN')
	    						->leftjoin('CMP.DOCUMENTO_CTBLE', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'CMP.REFERENCIA_ASOC.COD_TABLA_ASOC')
	    						->select('WEB.reglas.*','CMP.ORDEN.*','CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_TIPO_PAGO as CP','WEB.asignarreglas.id as asignarregla_id')
	    						->where('WEB.asignarreglas.prefijo','=','RDV')
	    						->where('WEB.asignarreglas.activo','=','1')
	    						->where('CMP.REFERENCIA_ASOC.TXT_GLOSA','like','%DOCUMENTO INTERNO VENTAS%')
	    						->get();

	    $lista_background_html 	= 	View::make('regla/ajax/listaasignardv',
						 			[
						 				'lista_reglas' 		=> $lista_reglas,
						 				'idopcion' 			=> $idopcion,
						 			])->render();

	    return response()->json([
	        'lista_modal' 		=> $lista_modal_html,
	        'lista_background' 	=> $lista_background_html
	    ]);

	}






	public function actionAjaxModalListaOrdenVentaCuenta(Request $request)
	{

		$cuenta_id 				= 	$request['cuenta_id'];
	    $tipo_documento_id 		= 	'TDO0000000000003'; //boletas

	    $contrato 				=	CMPContrato::where('COD_CONTRATO','=',$cuenta_id)->first();

		$array_orden 			= 	$this->funciones->array_orden_venta_documento_fechas_cuenta_regla_nuevo($tipo_documento_id,$cuenta_id);

		$lista_deuda 			= 	$this->funciones->lista_deuda_cliente($contrato->COD_EMPR_CLIENTE,$this->fechaactual);

		//DD($contrato->COD_EMPR_CLIENTE);


		$comboregla	 			= 	$this->funciones->combo_regla_descuento();

		$comboautorizados 		= 	DB::table('users')
									->whereIn('id', ['1CIX00000032', '1CIX00000046', '1CIX00000218'])
									->pluck('nombre', 'id')
									->toArray();

		$funcion 				= 	$this;

		return View::make('regla/modal/ajax/ordenventa',
						 [
						 	'lista_deuda' 	=> $lista_deuda,
						 	'array_orden' 	=> $array_orden,
						 	'cuenta_id' 	=> $cuenta_id,
						 	'funcion' 		=> $funcion,
						 	'comboregla' 	=> $comboregla,
						 	'comboautorizados' => $comboautorizados,
						 ]);

	}



	public function actionAsignarReglaDiasVencimiento($idopcion,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
                                   
		$comboclientes	 	= 	$this->funciones->combo_clientes_cuenta_regla();
	    $fechainicio  		= 	$this->fecha_menos_treinta_dias;
	    $fechafin  			= 	$this->fin;

	    $lista_reglas 		= 	WEBAsignarRegla::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.asignarreglas.regla_id')
	    						->join('CMP.ORDEN', 'CMP.ORDEN.COD_ORDEN', '=', 'WEB.asignarreglas.tabla_id')
	    						->leftjoin('CMP.REFERENCIA_ASOC', 'CMP.REFERENCIA_ASOC.COD_TABLA', '=', 'CMP.ORDEN.COD_ORDEN')
	    						->leftjoin('CMP.DOCUMENTO_CTBLE', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'CMP.REFERENCIA_ASOC.COD_TABLA_ASOC')
	    						->select('WEB.reglas.*','CMP.ORDEN.*','CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_TIPO_PAGO as CP','WEB.asignarreglas.id as asignarregla_id')
	    						->where('WEB.asignarreglas.prefijo','=','RDV')
	    						->where('WEB.asignarreglas.activo','=','1')
	    						//->where('WEB.asignarreglas.empresa_id','=',Session::get('empresas')->COD_EMPR)
	    						->where('CMP.REFERENCIA_ASOC.TXT_GLOSA','like','%DOCUMENTO INTERNO VENTAS%')
	    						->get();

		return View::make('regla/asignardiasvencimiento',
						 [
						 	'idopcion' 			=> $idopcion,
							'comboclientes' 	=> $comboclientes,						
							'fechainicio'		=> $fechainicio,
							'fechafin'			=> $fechafin,
							'lista_reglas' 		=> $lista_reglas,
						 ]);

	}


	public function actionAsignarReglaLimiteCredito($idopcion,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
                                   
		$combojefeventa	 	= 	$this->funciones->combo_jefe_ventas_regla();
	    $fechainicio  		= 	$this->fecha_menos_treinta_dias;
	    $fechafin  			= 	$this->fin;

	    $lista_reglas 		= 	WEBAsignarRegla::join('WEB.reglas', 'WEB.reglas.id', '=', 'WEB.asignarreglas.regla_id')
	    						->join('STD.EMPRESA', 'STD.EMPRESA.COD_EMPR', '=', 'WEB.asignarreglas.tabla_id')
	    						->leftjoin('WEB.reglacreditoclientes', 'WEB.reglacreditoclientes.cliente_id', '=', 'STD.EMPRESA.COD_EMPR')
	    						->select('WEB.reglas.*','STD.EMPRESA.*','WEB.reglacreditoclientes.*','WEB.asignarreglas.id as asignarregla_id')
	    						->where('WEB.asignarreglas.prefijo','=','RLC')
	    						->where('WEB.asignarreglas.activo','=','1')
	    						//->where('WEB.asignarreglas.empresa_id','=',Session::get('empresas')->COD_EMPR)
	    						->get();




		return View::make('regla/asignarlimitecredito',
						 [
						 	'idopcion' 			=> $idopcion,
							'combojefeventa' 	=> $combojefeventa,						
							'fechainicio'		=> $fechainicio,
							'fechafin'			=> $fechafin,
							'lista_reglas' 		=> $lista_reglas,
						 ]);

	}



	public function actionListarReglaDiasVencimiento($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $fechainicio  		= 	$this->fecha_menos_treinta_dias;
	    $fechafin  			= 	$this->fecha_mas_uno;

	    $listadiasvencimiento 	= 	WEBRegla::orderBy('fechafin', 'asc')
			    				->whereIn('tiporegla', ['RDV'])
								//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
								->where('estado','=','PU')
		    					->where('fechainicio','>=', $fechainicio)
		    					->where('fechainicio','<=', $fechafin)
			    				->get();


		$comboestado        =   array('' => "Seleccione el tipo de pago",'CU' => "CERRADO",'PU' => "PUBLICADO");

		$fechavacia  		= 	$this->fechavacia;
		$funcion 			= 	$this;

		return View::make('regla/listadiasvencimiento',
						 [
						 	'listadiasvencimiento' 	=> $listadiasvencimiento,
						 	'fechavacia'	 		=> $fechavacia,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'fechainicio' 			=> $fechainicio,
						 	'fechafin' 				=> $fechafin,
						 	'comboestado' 			=> $comboestado,
						 	'ajax'   		  		=> false,	
						 ]);
	}


	public function actionAgregarReglaDiasVencimiento($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}

	    /******************************************************/


		if($_POST)
		{


			$codigo 					= 	$this->funciones->generar_codigo('WEB.reglas',6);
			$idregla 					= 	$this->funciones->getCreateIdMaestra('WEB.reglas');

			$cabecera            	 	=	new WEBRegla;
			$cabecera->id 	     	 	=  	$idregla;
			$cabecera->codigo 	    	=  	$codigo;
			$cabecera->tiporegla 	    =  	'RDV';
			$cabecera->nombre 	     	=  	trim($request['nombre']);
			$cabecera->fechainicio 	    =  	trim($request['fechainicio']);
			$cabecera->fechafin 	    =  	trim($request['fechafin']);
			$cabecera->cantidadminima 	=  	0;
			$cabecera->tipodescuento 	=  	'IMP';
			$cabecera->descuento 		=  	str_replace(',','',trim($request['descuento']));
			$cabecera->estado 			=  	'PU';
			$cabecera->fecha_crea 	    =  	$this->fechaactual;
			$cabecera->usuario_crea 	=  	Session::get('usuario')->id;
			$cabecera->empresa_id 		=   Session::get('empresas')->COD_EMPR;
			$cabecera->centro_id 		=   Session::get('centros')->COD_CENTRO;
			$cabecera->save();

 			return Redirect::to('/gestion-de-regla-dias-vencimiento/'.$idopcion)->with('bienhecho', 'Regla Dias de Vencimiento '.$request['nombre'].' registrado con exito');


		}else{


			$fechaactual = $this->fechaactualinput;
			$fechavacia  = $this->fechavacia;

			return View::make('regla/agregardiasvencimiento',
						[				
							'fechaactual'  			=> $fechaactual,
							'fechavacia'  			=> $fechavacia,
							'idopcion'  			=> $idopcion
						]);
		}
	}


	public function actionListaAjaxReglasDiasVencimiento(Request $request)
	{

		$estado_id 					=  	$request['estado_id'];
		$fechainicio 				=  	$request['fechainicio'];
		$fechafin 					=  	$request['fechafin'];
		$idopcion 					=  	$request['idopcion'];


	    $listadiasvencimiento 	= 	WEBRegla::orderBy('fechafin', 'asc')
			    				->whereIn('tiporegla', ['RDV'])
								//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
								->where('estado','=',$estado_id)
		    					->where('fechainicio','>=', $fechainicio)
		    					->where('fechainicio','<=', $fechafin)
			    				->get();

		$funcion 					= 	$this;
		$fechavacia  				= 	$this->fechavacia;



		return View::make('regla/ajax/listareglasdiasvencimiento',
						 [
						 	'listadiasvencimiento' 			=> $listadiasvencimiento,
						 	'funcion' 				=> $funcion,
						 	'fechavacia'	 		=> $fechavacia,
							'ajax'   		  		=> true,
						 	'idopcion'	 			=> $idopcion,					 	
						 ]);


	}




	public function actionListarReglaLineaCredito($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $fechainicio  		= 	$this->fecha_menos_treinta_dias;
	    $fechafin  			= 	$this->fecha_mas_uno;

	    $listalineacredito 	= 	WEBRegla::orderBy('fechafin', 'asc')
			    				->whereIn('tiporegla', ['RLC'])
								//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
								->where('estado','=','PU')
		    					->where('fechainicio','>=', $fechainicio)
		    					->where('fechainicio','<=', $fechafin)
			    				->get();


		$comboestado        =   array('' => "Seleccione el tipo de pago",'CU' => "CERRADO",'PU' => "PUBLICADO");

		$fechavacia  		= 	$this->fechavacia;
		$funcion 			= 	$this;

		return View::make('regla/listalineacredito',
						 [
						 	'listalineacredito' 	=> $listalineacredito,
						 	'fechavacia'	 		=> $fechavacia,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'fechainicio' 			=> $fechainicio,
						 	'fechafin' 				=> $fechafin,
						 	'comboestado' 			=> $comboestado,
						 	'ajax'   		  		=> false,	
						 ]);
	}


	public function actionListaAjaxReglasLimiteCredito(Request $request)
	{

		$estado_id 					=  	$request['estado_id'];
		$fechainicio 				=  	$request['fechainicio'];
		$fechafin 					=  	$request['fechafin'];
		$idopcion 					=  	$request['idopcion'];


	    $listalineacredito 	= 	WEBRegla::orderBy('fechafin', 'asc')
			    				->whereIn('tiporegla', ['RLC'])
								//->where('centro_id','=',Session::get('centros')->COD_CENTRO)
								->where('estado','=',$estado_id)
		    					->where('fechainicio','>=', $fechainicio)
		    					->where('fechainicio','<=', $fechafin)
			    				->get();

		$funcion 					= 	$this;
		$fechavacia  				= 	$this->fechavacia;



		return View::make('regla/ajax/listareglaslineacredito',
						 [
						 	'listalineacredito' 			=> $listalineacredito,
						 	'funcion' 				=> $funcion,
						 	'fechavacia'	 		=> $fechavacia,
							'ajax'   		  		=> true,
						 	'idopcion'	 			=> $idopcion,					 	
						 ]);


	}



	public function actionAgregarReglaLineaCredito($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}

	    /******************************************************/


		if($_POST)
		{


			$codigo 					= 	$this->funciones->generar_codigo('WEB.reglas',6);
			$idregla 					= 	$this->funciones->getCreateIdMaestra('WEB.reglas');

			$cabecera            	 	=	new WEBRegla;
			$cabecera->id 	     	 	=  	$idregla;
			$cabecera->codigo 	    	=  	$codigo;
			$cabecera->tiporegla 	    =  	'RLC';
			$cabecera->nombre 	     	=  	trim($request['nombre']);
			$cabecera->fechainicio 	    =  	trim($request['fechainicio']);
			$cabecera->fechafin 	    =  	trim($request['fechafin']);
			$cabecera->cantidadminima 	=  	0;
			$cabecera->tipodescuento 	=  	'IMP';
			$cabecera->descuento 		=  	str_replace(',','',trim($request['descuento']));
			$cabecera->estado 			=  	'PU';
			$cabecera->fecha_crea 	    =  	$this->fechaactual;
			$cabecera->usuario_crea 	=  	Session::get('usuario')->id;
			$cabecera->empresa_id 		=   Session::get('empresas')->COD_EMPR;
			$cabecera->centro_id 		=   Session::get('centros')->COD_CENTRO;
			$cabecera->save();



 			return Redirect::to('/gestion-de-regla-de-ampliacion-linea-credito/'.$idopcion)->with('bienhecho', 'Regla Linea Credito '.$request['nombre'].' registrado con exito');


		}else{



			$fechaactual = $this->fechaactualinput;
			$fechavacia  = $this->fechavacia;

			return View::make('regla/agregarlineacredito',
						[				
							'fechaactual'  			=> $fechaactual,
							'fechavacia'  			=> $fechavacia,
							'idopcion'  			=> $idopcion
						]);
		}
	}


	public function actionListarReglaCompromisoPago($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $fechainicio  		= 	$this->fin;
	    $fechafin  			= 	$this->fin;

	    $fechainicio_c 		= 	date_format(date_create($fechainicio), 'Y-m-d');
	    $fechafin_c 		= 	date_format(date_create($fechafin), 'Y-m-d');

	    $lista_sedes        =   DB::select("SELECT Sede FROM Regla_Compromiso_Pago GROUP BY Sede ORDER BY Sede ASC");
	    $combo_sedes        =   array('' => 'Todas las Sedes');
	    foreach($lista_sedes as $row){
	        if($row->Sede != ''){
	            $combo_sedes[$row->Sede] = $row->Sede;
	        }
	    }

	    $lista_reglas 		= 	DB::table('Regla_Compromiso_Pago')
	    						->whereRaw('CAST(Fecha_Regla AS DATE) >= ?', [$fechainicio_c])
	    						->whereRaw('CAST(Fecha_Regla AS DATE) <= ?', [$fechafin_c])
	    						->get();

	    $lista_consolidado 	= 	DB::table('Regla_Compromiso_Pago_Consolidado')
	    						->whereRaw('CAST(Fecha_Regla AS DATE) >= ?', [$fechainicio_c])
	    						->whereRaw('CAST(Fecha_Regla AS DATE) <= ?', [$fechafin_c])
	    						->get();

		return View::make('regla/listacompromisopago',
						 [
						 	'idopcion' 			=> $idopcion,					
							'fechainicio'		=> $fechainicio,
							'fechafin'			=> $fechafin,
							'lista_reglas' 		=> $lista_reglas,
							'lista_consolidado' => $lista_consolidado,
							'combo_sedes'       => $combo_sedes,
						 ]);

	}

	public function actionListaAjaxReglasCompromisoPago(Request $request)
	{

		$fechainicio 				=  	$request['fechainicio'];
		$fechafin 					=  	$request['fechafin'];
		$idopcion 					=  	$request['idopcion'];
		$sede                       =   $request['sede'];

	    $fechainicio_c 		= 	date_format(date_create($fechainicio), 'Y-m-d');
	    $fechafin_c 		= 	date_format(date_create($fechafin), 'Y-m-d');

	    $lista_reglas 		= 	DB::table('Regla_Compromiso_Pago')
	    						->whereRaw('CAST(Fecha_Regla AS DATE) >= ?', [$fechainicio_c])
	    						->whereRaw('CAST(Fecha_Regla AS DATE) <= ?', [$fechafin_c])
	    						->where(function($query) use ($sede){
	    							if($sede != ''){
	    								$query->where('Sede', '=', $sede);
	    							}
	    						})
	    						->get();

	    $lista_consolidado 	= 	DB::table('Regla_Compromiso_Pago_Consolidado')
	    						->whereRaw('CAST(Fecha_Regla AS DATE) >= ?', [$fechainicio_c])
	    						->whereRaw('CAST(Fecha_Regla AS DATE) <= ?', [$fechafin_c])
	    						->where(function($query) use ($sede){
	    							if($sede != ''){
	    								$query->where('Sede', '=', $sede);
	    							}
	    						})
	    						->get();

	    \Log::info("Busqueda CP - Sede: $sede, Detallado: ".count($lista_reglas).", Consolidado: ".count($lista_consolidado));

		return View::make('regla/ajax/listacompromisopago',
						 [
						 	'lista_reglas' 			=> $lista_reglas,
						 	'lista_consolidado' 	=> $lista_consolidado,
						 	'idopcion'	 			=> $idopcion,					 	
						 ]);
	}

	public function actionReglaCompromisoPagoExcel($fechainicio,$fechafin,$sede = '')
	{
		set_time_limit(0);

	    $fechainicio_c 		= 	date_format(date_create($fechainicio), 'Y-m-d');
	    $fechafin_c 		= 	date_format(date_create($fechafin), 'Y-m-d');

	    $lista_reglas 		= 	DB::table('Regla_Compromiso_Pago')
	    						->whereRaw('CAST(Fecha_Regla AS DATE) >= ?', [$fechainicio_c])
	    						->whereRaw('CAST(Fecha_Regla AS DATE) <= ?', [$fechafin_c])
	    						->where(function($query) use ($sede){
	    							if($sede != 'TODAS' && $sede != ''){
	    								$query->where('Sede', '=', $sede);
	    							}
	    						})
	    						->get();

	    $lista_consolidado 	= 	DB::table('Regla_Compromiso_Pago_Consolidado')
	    						->whereRaw('CAST(Fecha_Regla AS DATE) >= ?', [$fechainicio_c])
	    						->whereRaw('CAST(Fecha_Regla AS DATE) <= ?', [$fechafin_c])
	    						->where(function($query) use ($sede){
	    							if($sede != 'TODAS' && $sede != ''){
	    								$query->where('Sede', '=', $sede);
	    							}
	    						})
	    						->get();

		$titulo 		=   'Reporte Compromiso Pago';
		$empresa 		= 	Session::get('empresas')->NOM_EMPR;
		$centro 		= 	Session::get('centros')->NOM_CENTRO;
		$funcion 		= 	$this;

	    Excel::create($titulo, function($excel) use ($lista_reglas,$lista_consolidado,$titulo,$empresa,$centro,$funcion) {
	        $excel->sheet('Detallado', function($sheet) use ($lista_reglas,$titulo,$empresa,$centro,$funcion) {
	            $sheet->loadView('regla/excel/listacompromisopago')->with('lista_reglas',$lista_reglas)
	                                         		 ->with('titulo',$titulo)
	                                         		 ->with('empresa',$empresa)
	                                         		 ->with('centro',$centro)
	                                         		 ->with('funcion',$funcion);
	        });

	        $excel->sheet('Consolidado', function($sheet) use ($lista_consolidado,$titulo,$empresa,$centro,$funcion) {
	            $sheet->loadView('regla/excel/listacompromisopago')->with('lista_reglas',$lista_consolidado)
	                                         		 ->with('titulo',$titulo)
	                                         		 ->with('empresa',$empresa)
	                                         		 ->with('centro',$centro)
	                                         		 ->with('funcion',$funcion);
	        });
	    })->export('xls');

	}

	public function actionAjaxModalDetallePagosPeriodo(Request $request)
	{
		$div = $request->input('div');
		$fecha_regla = $request->input('fecha_regla');
		$fecha_compromiso = $request->input('fecha_compromiso');

		// Log for verification
		\Log::info("Modal Detalle Pagos - Div: $div, Regla: $fecha_regla, Compromiso: $fecha_compromiso");

		$detalle = DB::select("
			SELECT 
				HAB.COD_DOCUMENTO_CTBLE,
				HAB.COD_HABILITACION,
				HAB.FEC_HABILITACION,
				HAB.FEC_USUARIO_CREA_AUD,
				HAB.CAN_IMPORTE,
				ROW_NUMBER() OVER (
					PARTITION BY HAB.COD_DOCUMENTO_CTBLE, HAB.CAN_IMPORTE 
					ORDER BY HAB.COD_HABILITACION
				) AS RN_DETRACCION
			FROM CMP.HABILITACION HAB
			WHERE HAB.COD_PRODUCTO = 'PRD0000000009442' 
			AND HAB.COD_DOCUMENTO_CTBLE = ?
			ORDER BY HAB.FEC_HABILITACION ASC
		", [$div]);

		return View::make('regla/ajax/detallepagoperiodo', [
			'detalle' => $detalle,
			'div' => $div,
			'fecha_regla' => $fecha_regla,
			'fecha_compromiso' => $fecha_compromiso
		]);
	}

}
