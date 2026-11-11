<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBContacto;
use View;
use Session;
use Hashids;


class GestionContactoController extends Controller
{


	//18-10-2019
	public function actionGestionContacto($idopcion)
	{

		$listacontactos 				= 	WEBContacto::get();
		return View::make('campania/listacontactos',
						 [
						 	'listacontactos' 					=> $listacontactos,			 	
						 	'idopcion' 							=> $idopcion,
						 ]);

	}






}
