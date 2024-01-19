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


	public function colores_array() {
 		$colorArray = 		 array(
								    "#FF5733", "#3498db", "#2ecc71", "#e74c3c", "#8e44ad",
								    "#f39c12", "#1abc9c", "#c0392b", "#2980b9", "#27ae60",
								    "#e67e22", "#9b59b6", "#16a085", "#d35400", "#34495e",
								    "#FF7F50", "#00BFFF", "#00FA9A", "#DC143C", "#8A2BE2",
								    "#8B4513", "#483D8B", "#2F4F4F", "#3CB371", "#BA55D3",
								    "#F08080", "#00CED1", "#556B2F", "#B22222", "#800080"
								);

 		return $colorArray;
	}



}