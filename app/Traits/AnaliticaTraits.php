<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait AnaliticaTraits
{

	public function obtenerNumeroAleatorioNoRepetido($min, $max, &$numerosGenerados) {
	    $numeroAleatorio = rand($min, $max);

	    // Verificar si el número ya ha sido generado
	    while (in_array($numeroAleatorio, $numerosGenerados)) {
	        $numeroAleatorio = rand($min, $max);
	    }

	    // Agregar el número a la lista de generados
	    $numerosGenerados[] = $numeroAleatorio;

	    return $numeroAleatorio;
	}


}