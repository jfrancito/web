<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View;
use App\WEBRegla;
use Session;

class PruebaController extends Controller
{


    public function actionDRS(Request $request)
    {

        $archivoCmd = "C:/laragon/www/web/storage/exports/py/ERS.cmd";
        // Ejecutar el archivo .cmd usando exec()
        exec($archivoCmd, $output, $returnValue);
        // Verificar si la ejecución fue exitosa
        if ($returnValue === 0) {
            echo "El comando se ejecutó correctamente.";
        } else {
            echo "Error al ejecutar el comando. Código de retorno: $returnValue";
        }
        // Puedes imprimir la salida del comando si es necesario
        echo "Salida del comando: " . implode("\n", $output);


    }



    public function actionEnviarMensajeWhatsapp(Request $request)
    {
		$data = [
			'token'=>'dea0d11bff646db6797ee04e96097223vff8b', //token waping
			'source' => '5076623233431',  // your phone
			'destination'=>'5076450634535', // Receivers phone
			'type'=>'text', //type message
			'body' => [ 
			'text'=>'Hello, Word!' // Message
			]  
			]; 
			$json = json_encode($data); // Encode data to JSON
			// URL for request POST /message
			$url = 'http://waping.es/api/send';
			// Make a POST request
			$options = stream_context_create(['http' => [
			    'method'  => 'POST',
			    'header'  => 'Content-type: application/json',
			    'content' => $json
			    ]
			]);

			// Send a request
			$result = file_get_contents($url, false, $options);
    }


    public function pruebas(Request $request)
    {

        $accion = 1;

        // lista reglas activas y que estan dentro de la fecha indicada
        if($accion = 1){



        	$cod_centro 		= 	'CEN0000000000002';
        	$cod_empresa 		= 	'IACHEM0000010394';
        	$tipo 				= 	'NEG';
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

			print_r($lista_activas);

        }


      
    }


    public function indicadoresISL(Request $request)
    {
		return View::make('prueba/powerbi');
    }




}
