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


	public function actionAjaxAsignarReglaDiasVencimiento(Request $request)
	{

	    $data_cod_orden_venta 		= 	$request['data_cod_orden_venta'];
	    $regla_id 					= 	$request['regla_id'];

		$idreglaproductocliente 	= 	$this->funciones->getCreateIdMaestra('WEB.asignarreglas');

		$cabecera            	 	=	new WEBAsignarRegla;
		$cabecera->id 	     	 	=  	$idreglaproductocliente;
		$cabecera->regla_id 	    =  	$regla_id;
		$cabecera->prefijo 	    	=  	'RDV'; // REGLAS DIAS VENCIMIENTO
		$cabecera->tabla 	    	=  	'CMP.ORDEN';
		$cabecera->tabla_id 	    =  	$data_cod_orden_venta;
		$cabecera->fecha_crea 	    =  	$this->fechaactual;
		$cabecera->empresa_id 		=  	Session::get('empresas')->COD_EMPR;
		$cabecera->centro_id 		=  	Session::get('centros')->COD_CENTRO;
		$cabecera->usuario_crea 	=  	Session::get('usuario')->id;
		$cabecera->save();

	}






	public function actionAjaxModalListaOrdenVentaCuenta(Request $request)
	{

		$cuenta_id 				= 	$request['cuenta_id'];
	    $tipo_documento_id 		= 	'TDO0000000000003'; //boletas

	    $contrato 				=	CMPContrato::where('COD_CONTRATO','=',$cuenta_id)->first();

		$array_orden 			= 	$this->funciones->array_orden_venta_documento_fechas_cuenta_regla_nuevo($tipo_documento_id,$cuenta_id);

		$lista_deuda 			= 	$this->funciones->lista_deuda_cliente($contrato->COD_EMPR_CLIENTE,$this->fechaactual);
		$comboregla	 			= 	$this->funciones->combo_regla_descuento();

		$funcion 				= 	$this;

		return View::make('regla/modal/ajax/ordenventa',
						 [
						 	'lista_deuda' 	=> $lista_deuda,
						 	'array_orden' 	=> $array_orden,
						 	'cuenta_id' 	=> $cuenta_id,
						 	'funcion' 		=> $funcion,
						 	'comboregla' 	=> $comboregla,
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


}
