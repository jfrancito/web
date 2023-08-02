<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBListaCliente,App\STDEmpresa,App\WEBPlanillaComision,App\WEBDetallePlanillaComision;
use App\CONPeriodo;
use View;
use Session;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class ComisionReporteController extends Controller
{


	public function actionComisionPeriodo($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/



	    $periodos 			= 	WEBPlanillaComision::where('TXT_PROVIENE','=','MERCADO MAYORISTA')
	    						->where('TXT_ESTADO','=','EJECUTADO')
	    						->groupBy('TXT_CODIGO')
	    						->groupBy('COD_PERIODO')
								->orderByRaw('MAX(FEC_INICIO) DESC')
								->pluck('TXT_CODIGO','COD_PERIODO')
								->toArray();

		$comboperiodoinicio =   array('' => "Seleccione periodo inicio") + $periodos;
		$comboperiodofin 	=   array('' => "Seleccione periodo fin") + $periodos;


	    $vendedores 		= 	WEBPlanillaComision::where('TXT_PROVIENE','=','MERCADO MAYORISTA')
	    						->where('TXT_ESTADO','=','EJECUTADO')
	    						->groupBy('COD_CATEGORIA_JEFE_VENTA')
	    						->groupBy('TXT_CATEGORIA_JEFE_VENTA')
								->orderByRaw('TXT_CATEGORIA_JEFE_VENTA asc')
								->pluck('TXT_CATEGORIA_JEFE_VENTA','COD_CATEGORIA_JEFE_VENTA')
								->toArray();


		$combovendedores 	=   array('' => "Seleccione vendedor",'TODO' => "TODO") + $vendedores;



		return View::make('comision/reporte/comisionperiodos',
						 [
						 	'idopcion' 					=> $idopcion,
						 	'comboperiodoinicio' 		=> $comboperiodoinicio,
						 	'comboperiodofin' 			=> $comboperiodofin,
						 	'combovendedores' 			=> $combovendedores,

							'inicio'					=> $this->inicio,
							'hoy'						=> $this->fin,
						 ]);

	}



	public function actionComisionPeriodoExcel($periodoinicio,$periodofin,$vendedor_id)
	{
		set_time_limit(0);


			$reportecomisionperidos = 	$this->funciones->reportecomisionperidos($periodoinicio,$periodofin,$vendedor_id);
			$consolidadovendedor = 	$this->funciones->consolidadovendedorcomision($periodoinicio,$periodofin,$vendedor_id);

			$reportecomisionperidos_jefe = 	$this->funciones->reportecomisionperidos_jefe($periodoinicio,$periodofin,$vendedor_id);
			$consolidadovendedor_jefe = 	$this->funciones->consolidadovendedorcomision_jefe($periodoinicio,$periodofin,$vendedor_id);
			$titulo = 'COMISIONES';

		    Excel::create($titulo, function($excel) use ($reportecomisionperidos,$consolidadovendedor,$reportecomisionperidos_jefe,$consolidadovendedor_jefe) {

		        $excel->sheet('Comision Vendedor', function($sheet) use ($reportecomisionperidos,$consolidadovendedor) {
		            $sheet->loadView('comision/excel/comisionperiodos')->with('reportecomisionperidos',$reportecomisionperidos)
		            													->with('consolidadovendedor',$consolidadovendedor);                                        		 
		        });

		        $excel->sheet('Comision Jefe', function($sheet) use ($reportecomisionperidos_jefe,$consolidadovendedor_jefe) {
		            $sheet->loadView('comision/excel/comisionperiodos_jefe')->with('reportecomisionperidos_jefe',$reportecomisionperidos_jefe)
		            														->with('consolidadovendedor_jefe',$consolidadovendedor_jefe);                                        		 
		        });


		    })->export('xls');



	}


}
