<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\User,App\WEBGrupoopcion,App\WEBRol,App\WEBRolOpcion,App\WEBOpcion,App\WEBListaPersonal,App\WEBPedido,App\WEBDetallePedido;
use App\ALMCentro,App\STDEmpresa,App\WEBUserEmpresaCentro;
use View;
use Session;
use Hashids;


class UserController extends Controller
{

    public function actionLogin(Request $request){

		if($_POST)
		{
			/**** Validaciones laravel ****/
			$this->validate($request, [
	            'name' => 'required',
	            'password' => 'required',

			], [
            	'name.required' => 'El campo Usuario es obligatorio',
            	'password.required' => 'El campo Clave es obligatorio',
        	]);

			/**********************************************************/
			
			$usuario 	 				 = strtoupper($request['name']);
			$clave   	 				 = strtoupper($request['password']);
			$local_id  	 				 = $request['local_id'];

			$tusuario    				 = 	User::whereRaw('UPPER(name)=?',[$usuario])
											//->where('activo','=',1)
											->first();

			if(count($tusuario)>0)
			{
				$clavedesifrada 		 = 	strtoupper(Crypt::decrypt($tusuario->password));

				if($clavedesifrada == $clave)
				{

					$listamenu    		 = 	WEBGrupoopcion::join('web.opciones', 'web.opciones.grupoopcion_id', '=', 'web.grupoopciones.id')
											->join('web.rolopciones', 'web.rolopciones.opcion_id', '=', 'web.opciones.id')
											->where('web.grupoopciones.activo', '=', 1)
											->where('web.rolopciones.rol_id', '=', $tusuario->rol_id)
											->where('web.rolopciones.ver', '=', 1)
											->where('web.opciones.ind_oryza', '=', 1)
											->groupBy('web.grupoopciones.id')
											->groupBy('web.grupoopciones.nombre')
											->groupBy('web.grupoopciones.icono')
											->groupBy('web.grupoopciones.orden')
											->select('web.grupoopciones.id','web.grupoopciones.nombre','web.grupoopciones.icono','web.grupoopciones.orden')
											->orderBy('web.grupoopciones.orden', 'asc')
											->get();

					$listaopciones    	= 	WEBRolOpcion::join('web.opciones', 'web.rolopciones.opcion_id', '=', 'web.opciones.id')
											->where('web.opciones.ind_oryza', '=', 1)
											->where('rol_id', '=', $tusuario->rol_id)
											->where('ver', '=', 1)
											->orderBy('orden', 'asc')
											->pluck('opcion_id')
											->toArray();


					Session::put('usuario', $tusuario);
					Session::put('listamenu', $listamenu);
					Session::put('listaopciones', $listaopciones);

					return Redirect::to('acceso');
					
						
				}else{
					return Redirect::back()->withInput()->with('errorbd', 'Usuario o clave incorrecto');
				}	
			}else{
				return Redirect::back()->withInput()->with('errorbd', 'Usuario o clave incorrecto');
			}						    

		}else{

			return view('usuario.login');
		}
    }


	public function actionAcceso()
	{

		$accesos  	= 	WEBUserEmpresaCentro::where('activo','=',1)
						->where('usuario_id','=',Session::get('usuario')->id)->get();

		$funcion 	=   $this;

		return View::make('acceso',
						 [
						 	'accesos' => $accesos,
						 	'funcion' => $funcion,
						 ]);

	}

	public function actionAccesoBienvenido($idempresa,$idcentro)
	{
		
		$centros 	= 	ALMCentro::where('COD_CENTRO','=',$idcentro)
						->where('COD_ESTADO','=','1')->first(); 
		$empresas 	= 	STDEmpresa::where('COD_EMPR','=',$idempresa)
						->where('COD_ESTADO','=','1')->where('IND_SISTEMA','=','1')->first(); 


		$color 		=   $this->funciones->color_empresa($empresas->COD_EMPR);

		Session::put('color', $color);
		Session::put('empresas', $empresas);
		Session::put('centros', $centros);
		return Redirect::to('bienvenido');

	}

	public function actionBienvenido()
	{

	    $countpedidos		= 		WEBPedido::where('activo','=',1)
	    							->leftJoin('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'web.pedidos.estado_id')
	    							->where('usuario_crea','=',Session::get('usuario')->id)
									//->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
									->where('centro_id','=',Session::get('centros')->COD_CENTRO)
	    							->count();


		return View::make('bienvenido',
						 [
						 	'countpedidos' => $countpedidos,
						 ]);
	}

	public function actionCerrarSesion()
	{

		Session::forget('usuario');
		Session::forget('listamenu');
		Session::forget('empresas');
		Session::forget('centros');
		Session::forget('listaopciones');
		Session::forget('color');

		return Redirect::to('/login');
	}

	public function actionCambiarPerfil()
	{

		Session::forget('empresas');
		Session::forget('centros');
		return Redirect::to('/acceso');
	}


	public function actionListarUsuarios($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');

	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
        $array_rols    = WEBRol::where('ind_oryza','=',1)
                         ->pluck('id')
                         ->toArray();
                 
	    $listausuarios = User::where('id','<>',$this->prefijomaestro.'00000001')
	    				->whereIn('rol_id',$array_rols)->orderBy('id', 'asc')->get();

		return View::make('usuario/listausuarios',
						 [
						 	'listausuarios' => $listausuarios,
						 	'idopcion' => $idopcion,
						 ]);
	}


	public function actionAgregarUsuario($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

		if($_POST)
		{


			$personal_id 	 		 	= 	$request['personal'];
			$personal     				=   WEBListaPersonal::where('id', '=', $personal_id)->first();
			$idusers 				 	=   $this->funciones->getCreateIdMaestra('users');
			
			$cabecera            	 	=	new User;
			$cabecera->id 	     	 	=   $idusers;
			$cabecera->nombre 	     	=   $personal->nombres;
			$cabecera->name  		 	=	$request['name'];
			$cabecera->passwordmobil  	=	$request['password'];
			$cabecera->fecha_crea 	   	=  	$this->fechaactual;
			$cabecera->password 	 	= 	Crypt::encrypt($request['password']);
			$cabecera->rol_id 	 		= 	$request['rol_id'];
			$cabecera->usuarioosiris_id	= 	$personal->id;
			$cabecera->save();
 

 			return Redirect::to('/gestion-de-usuarios/'.$idopcion)->with('bienhecho', 'Usuario '.$personal->nombres.' registrado con exito');

		}else{

			$listapersonal 				= 	DB::table('WEB.LISTAPERSONAL')
	    									->leftJoin('users', 'WEB.LISTAPERSONAL.id', '=', 'users.usuarioosiris_id')
	    									->whereNull('users.usuarioosiris_id')
	    									->select('WEB.LISTAPERSONAL.id','WEB.LISTAPERSONAL.nombres')
	    									->get();

			$rol 						= 	DB::table('WEB.Rols')->where('id','<>',$this->prefijomaestro.'00000001')->pluck('nombre','id')->toArray();
			$comborol  					= 	array('' => "Seleccione Rol") + $rol;
		
			return View::make('usuario/agregarusuario',
						[
							'comborol'  		=> $comborol,
							'listapersonal'  	=> $listapersonal,					
						  	'idopcion'  		=> $idopcion
						]);
		}
	}


	public function actionModificarUsuario($idopcion,$idusuario,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idusuario = $this->funciones->decodificarmaestra($idusuario);

		if($_POST)
		{

			$cabecera            	 =	User::find($idusuario);			
			$cabecera->name  		 =	$request['name'];
			$cabecera->passwordmobil =	$request['password'];
			$cabecera->fecha_mod 	 =  $this->fechaactual;
			$cabecera->password 	 = 	Crypt::encrypt($request['password']);
			$cabecera->activo 	 	 =  $request['activo'];			
			$cabecera->rol_id 	 	 = 	$request['rol_id']; 
			$cabecera->save();


 			return Redirect::to('/gestion-de-usuarios/'.$idopcion)->with('bienhecho', 'Usuario '.$request['nombre'].' modificado con exito');


		}else{


				$usuario 	= 	User::where('id', $idusuario)->first();  
				$rol 		= 	DB::table('WEB.Rols')->where('id','<>',$this->prefijomaestro.'00000001')->pluck('nombre','id')->toArray();
				$comborol  	= 	array($usuario->rol_id => $usuario->rol->nombre) + $rol;
				$centros 	= 	ALMCentro::where('COD_ESTADO','=','1')->get(); 
				$empresas 	= 	STDEmpresa::where('COD_ESTADO','=','1')->where('IND_SISTEMA','=','1')->get(); 
				$funcion 	= 	$this;	

		        return View::make('usuario/modificarusuario', 
		        				[
		        					'usuario'  		=> $usuario,
									'comborol' 		=> $comborol,
						  			'idopcion' 		=> $idopcion,
									'centros' 		=> $centros,
									'empresas' 		=> $empresas,
									'funcion' 		=> $funcion,
		        				]);
		}
	}




	public function actionListarRoles($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $listaroles = WEBRol::where('id','<>',$this->prefijomaestro.'00000001')
	    				->where('ind_oryza','=',1)->orderBy('id', 'asc')->get();

		return View::make('usuario/listaroles',
						 [
						 	'listaroles' => $listaroles,
						 	'idopcion' => $idopcion,
						 ]);

	}


	public function actionAgregarRol($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

		if($_POST)
		{

			/**** Validaciones laravel ****/
			
			$this->validate($request, [
			    'nombre' => 'unico:WEB,rols',
			], [
            	'nombre.unico' => 'Rol ya registrado',
        	]);

			/******************************/
			$idrol 					 = $this->funciones->getCreateIdMaestra('WEB.rols');

			$cabecera            	 =	new WEBRol;
			$cabecera->id 	     	 =  $idrol;
			$cabecera->ind_oryza 	 =  1;
			$cabecera->fecha_crea 	 =  $this->fechaactual;
			$cabecera->nombre 	     =  $request['nombre'];
			$cabecera->save();

			$listaopcion 			 = 	WEBOpcion::where('ind_oryza','=',1)->orderBy('id', 'asc')->get();
			$count = 1;
			foreach($listaopcion as $item){


				$idrolopciones 		= $this->funciones->getCreateIdMaestra('WEB.rolopciones');


			    $detalle            =	new WEBRolOpcion;
			    $detalle->id 	    =  	$idrolopciones;
				$detalle->opcion_id = 	$item->id;
				$detalle->fecha_crea =  $this->fechaactual;
				$detalle->rol_id    =  	$idrol;
				$detalle->orden     =  	$count;
				$detalle->ver       =  	0;
				$detalle->anadir    =  	0;
				$detalle->modificar =  	0;
				$detalle->eliminar  =  	0;
				$detalle->todas     = 	0;
				$detalle->save();
				$count 				= 	$count +1;
			}

 			return Redirect::to('/gestion-de-roles/'.$idopcion)->with('bienhecho', 'Rol '.$request['nombre'].' registrado con exito');
		}else{

		
			return View::make('usuario/agregarrol',
						[
						  	'idopcion' => $idopcion
						]);

		}
	}


	public function actionModificarRol($idopcion,$idrol,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idrol = $this->funciones->decodificarmaestra($idrol);

		if($_POST)
		{

			/**** Validaciones laravel ****/
			$this->validate($request, [
				'nombre' => 'unico_menos:WEB,rols,id,'.$idrol,
			], [
            	'nombre.unico_menos' => 'Rol ya registrado',
        	]);
			/******************************/

			$cabecera            	 =	WEBRol::find($idrol);
			$cabecera->nombre 	     =  $request['nombre'];
			$cabecera->fecha_mod 	 =  $this->fechaactual;
			$cabecera->activo 	 	 =  $request['activo'];			
			$cabecera->save();
 
 			return Redirect::to('/gestion-de-roles/'.$idopcion)->with('bienhecho', 'Rol '.$request['nombre'].' modificado con éxito');

		}else{
				$rol = WEBRol::where('id', $idrol)->first();

		        return View::make('usuario/modificarrol', 
		        				[
		        					'rol'  		=> $rol,
						  			'idopcion' 	=> $idopcion
		        				]);
		}
	}



	public function actionListarPermisos($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $listaroles = WEBRol::where('id','<>',$this->prefijomaestro.'00000001')
	    				->where('ind_oryza','=',1)->orderBy('id', 'asc')->get();

		return View::make('usuario/listapermisos',
						 [
						 	'listaroles' => $listaroles,
						 	'idopcion' => $idopcion,
						 ]);
	}


	public function actionAjaxListarOpciones(Request $request)
	{
		$idrol =  $request['idrol'];
		$idrol = $this->funciones->decodificarmaestra($idrol);

		$listaopciones = WEBRolOpcion::where('rol_id','=',$idrol)->get();

		return View::make('usuario/ajax/listaopciones',
						 [
							 'listaopciones'   => $listaopciones
						 ]);
	}

	public function actionAjaxActivarPermisos(Request $request)
	{

		$idrolopcion =  $request['idrolopcion'];
		$idrolopcion = $this->funciones->decodificarmaestra($idrolopcion);

		$cabecera            	 =	WEBRolOpcion::find($idrolopcion);
		$cabecera->ver 	     	 =  $request['ver'];
		$cabecera->anadir 	 	 =  $request['anadir'];
		$cabecera->fecha_mod 	 =  $this->fechaactual;
		$cabecera->modificar 	 =  $request['modificar'];
		$cabecera->todas 	 	 =  $request['todas'];	
		$cabecera->save();

		echo("gmail");

	}
	
	public function actionAjaxActivarPerfiles(Request $request)
	{

		$idempresa =  $request['idempresa'];
		$idcentro =  $request['idcentro'];
		$idusuario =  $request['idusuario'];
		$check =  $request['check'];	

		$perfiles = WEBUserEmpresaCentro::where('empresa_id','=',$idempresa)
										  ->where('centro_id','=',$idcentro)
										  ->where('usuario_id','=',$idusuario)
										  ->first();

		if(count($perfiles)>0){

			$cabecera            	 =	WEBUserEmpresaCentro::find($perfiles->id);
			$cabecera->fecha_mod 	 = 	$this->fechaactual;
			$cabecera->activo 	     =  $check;	
			$cabecera->save();	
			
		}else{

			$id 					= 	$this->funciones->getCreateIdMaestra('WEB.userempresacentros');
		    $detalle            	=	new WEBUserEmpresaCentro;
		    $detalle->id 	    	=  	$id;
			$detalle->empresa_id 	= 	$idempresa;
			$detalle->centro_id    	=  	$idcentro;
			$detalle->fecha_crea 	 = 	$this->fechaactual;
			$detalle->usuario_id    =  	$idusuario;
			$detalle->save();

		}

		echo("gmail");

	}





}
