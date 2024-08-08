<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\View;
use App\Traits\ImportExcelTraits;
class ImportExcelController extends Controller
{

    use ImportExcelTraits;

    public function actionGestionImportExcel(){

        View::share('titulo','Importar Excel');


        return View::make('importacionesexcel/import_excel',
            [
                'estado' => false,
                'estado_validar' => true,
                'autoservicio' => 'CENCOSUD',
                'fecha' => date('Y-m-d'),
                'mensaje_retail' => '',
                'tipo_mensaje_retail' => 'alert-primary'
            ]);

        
    }

    function validate(Request $request) {
        $extensions = array("xls","xlsx","csv");

        $result = array($request->file('select_file')->getClientOriginalExtension());

        if(in_array($result[0],$extensions)){
            return true;
        }else{
            return false;
        }
    }

    function actionValidateExcel(Request $request)
    {
        $fecha = $request->get('startDate');
        $selected = $request->get('autoservicio');
        $mensaje_retail = '';
        $tipo_mensaje_retail = '';
        $mensaje_almacen = '';
        $tipo_mensaje_almacen = '';
        $mensaje_terceros = '';
        $tipo_mensaje_terceros = '';
        $estado = false;
        $array_cabecera_retail = array();
        $array_cabecera_almacen = array();
        $array_cabecera_terceros = array();
        $array_lista_retail = array();
        $array_lista_almacen = array();
        $array_lista_terceros = array();
        $array_cabecera_retail_tm = array();
        $array_cabecera_almacen_tm = array();
        $array_cabecera_terceros_tm = array();
        $array_lista_retail_tm = array();
        $array_lista_almacen_tm = array();
        $array_lista_terceros_tm = array();
        switch ($selected) {
            case 'CENCOSUD':
                $array_lista_retail = $this->obtener_tabla_cencosud('RETAIL', 'PROPIO', $fecha, '');
                $array_lista_almacen = $this->obtener_tabla_cencosud('ALMACEN', 'PROPIO', $fecha, '');
                $array_lista_terceros = $this->obtener_tabla_cencosud('', 'TERCEROS', $fecha, '');
                $array_cabecera_retail = $this->obtener_cabecera_cencosud('RETAIL', 'PROPIO', $fecha);
                $array_cabecera_almacen = $this->obtener_cabecera_cencosud('ALMACEN', 'PROPIO', $fecha);
                $array_cabecera_terceros = $this->obtener_cabecera_cencosud('', 'TERCEROS', $fecha);

                $contador_cabecera_retail = count($array_cabecera_retail);
                $contador_cabecera_almacen = count($array_cabecera_almacen);
                $contador_cabecera_terceros = count($array_cabecera_terceros);

                $nombre_excel = 'LISTADO_PRODUCTOS_CENCOSUD('.$fecha.')';

                $titulo = $nombre_excel;

                if((count($array_lista_retail) > 0 and $contador_cabecera_retail > 0)
                    or (count($array_lista_almacen) > 0 and $contador_cabecera_almacen > 0)
                    or (count($array_lista_terceros) > 0 and $contador_cabecera_terceros > 0)) {
                    Excel::create($titulo, function ($excel) use ($array_lista_retail, $array_lista_almacen,
                        $array_lista_terceros, $array_cabecera_retail, $array_cabecera_almacen,
                        $array_cabecera_terceros,$contador_cabecera_retail,$contador_cabecera_almacen,$contador_cabecera_terceros) {
                        if (count($array_lista_retail) > 0 and count($array_cabecera_retail) > 0) {
                            $excel->sheet('Productos Retail', function ($sheet) use ($array_lista_retail, $array_cabecera_retail,$contador_cabecera_retail) {
                                $sheet->setAutoFilter('A1:Z1');
                                $sheet->setColumnFormat(array(
                                    'B:Z' => '0.0000',
                                    'AA:AZ' => '0.0000'
                                ));
                                $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                    $array_lista_retail)->with('lista_cabecera', $array_cabecera_retail)->with('contador',$contador_cabecera_retail);
                            });
                        }
                        if (count($array_lista_almacen) > 0 and count($array_cabecera_almacen) > 0) {
                            $excel->sheet('Productos Almacen', function ($sheet) use ($array_lista_almacen, $array_cabecera_almacen,$contador_cabecera_almacen) {
                                $sheet->setAutoFilter('A1:Z1');
                                $sheet->setColumnFormat(array(
                                    'B:Z' => '0.0000',
                                    'AA:AZ' => '0.0000'
                                ));
                                $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                    $array_lista_almacen)->with('lista_cabecera', $array_cabecera_almacen)->with('contador',$contador_cabecera_almacen);
                            });
                        }
                        if (count($array_lista_terceros) > 0 and count($array_cabecera_terceros) > 0) {
                            $excel->sheet('Productos Terceros', function ($sheet) use ($array_lista_terceros, $array_cabecera_terceros,$contador_cabecera_terceros) {
                                $sheet->setAutoFilter('A1:Z1');
                                $sheet->setColumnFormat(array(
                                    'B:Z' => '0.0000',
                                    'AA:AZ' => '0.0000'
                                ));
                                $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                    $array_lista_terceros)->with('lista_cabecera', $array_cabecera_terceros)->with('contador',$contador_cabecera_terceros);
                            });
                        }
                    })->download('xlsx');
                    $mensaje_retail = 'SE ENCONTRARON DATOS EXPORTANDO';
                    $tipo_mensaje_retail = 'alert-success';
                } else {
                    $mensaje_retail = 'NO SE ENCONTRARON DATOS SUBA EL ARCHIVO EXCEL O CSV DEL DIA';
                    $tipo_mensaje_retail = 'alert-warning';
                    $estado = true;
                }
                break;
            default:
                $array_lista_retail_tm = $this->obtener_tabla_autoservicio($selected, 'PROPIO', 'BOLSA', $fecha, '');
                $array_lista_almacen_tm = $this->obtener_tabla_autoservicio($selected, 'PROPIO', 'SACO', $fecha, '');
                $array_lista_terceros_tm = $this->obtener_tabla_autoservicio($selected, 'TERCEROS', '', $fecha, '');
                $array_cabecera_retail_tm = $this->obtener_cabecera_autoservicio($selected, 'PROPIO', 'BOLSA', $fecha);
                $array_cabecera_almacen_tm = $this->obtener_cabecera_autoservicio($selected, 'PROPIO', 'SACO', $fecha);
                $array_cabecera_terceros_tm = $this->obtener_cabecera_autoservicio($selected, 'TERCEROS', '', $fecha);

                $contador_cabecera_retail_tm = count($array_cabecera_retail_tm);
                $contador_cabecera_almacen_tm = count($array_cabecera_almacen_tm);
                $contador_cabecera_terceros_tm = count($array_cabecera_terceros_tm);

                $nombre_excel_tm = 'LISTADO_PRODUCTOS_'.$selected.'('.$fecha.')';

                $titulo_tm = $nombre_excel_tm;

                if((count($array_lista_retail_tm) > 0 and $contador_cabecera_retail_tm > 0)
                    or (count($array_lista_almacen_tm) > 0 and $contador_cabecera_almacen_tm > 0)
                    or (count($array_lista_terceros_tm) > 0 and $contador_cabecera_terceros_tm > 0)) {
                    Excel::create($titulo_tm, function ($excel) use ($array_lista_retail_tm, $array_lista_almacen_tm,
                        $array_lista_terceros_tm, $array_cabecera_retail_tm, $array_cabecera_almacen_tm,
                        $array_cabecera_terceros_tm,$contador_cabecera_retail_tm,$contador_cabecera_almacen_tm,$contador_cabecera_terceros_tm) {
                        if (count($array_lista_retail_tm) > 0 and count($array_cabecera_retail_tm) > 0) {
                            $excel->sheet('Productos Propios Livianos', function ($sheet) use ($array_lista_retail_tm, $array_cabecera_retail_tm,$contador_cabecera_retail_tm) {
                                $sheet->setAutoFilter('A1:Z1');
                                $sheet->setColumnFormat(array(
                                    'B:Z' => '0.0000',
                                    'AA:AZ' => '0.0000'
                                ));
                                $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                    $array_lista_retail_tm)->with('lista_cabecera', $array_cabecera_retail_tm)->with('contador',$contador_cabecera_retail_tm);
                            });
                        }
                        if (count($array_lista_almacen_tm) > 0 and count($array_cabecera_almacen_tm) > 0) {
                            $excel->sheet('Productos Propios Sacos', function ($sheet) use ($array_lista_almacen_tm, $array_cabecera_almacen_tm,$contador_cabecera_almacen_tm) {
                                $sheet->setAutoFilter('A1:Z1');
                                $sheet->setColumnFormat(array(
                                    'B:Z' => '0.0000',
                                    'AA:AZ' => '0.0000'
                                ));
                                $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                    $array_lista_almacen_tm)->with('lista_cabecera', $array_cabecera_almacen_tm)->with('contador',$contador_cabecera_almacen_tm);
                            });
                        }
                        if (count($array_lista_terceros_tm) > 0 and count($array_cabecera_terceros_tm) > 0) {
                            $excel->sheet('Productos Terceros', function ($sheet) use ($array_lista_terceros_tm, $array_cabecera_terceros_tm,$contador_cabecera_terceros_tm) {
                                $sheet->setAutoFilter('A1:Z1');
                                $sheet->setColumnFormat(array(
                                    'B:Z' => '0.0000',
                                    'AA:AZ' => '0.0000'
                                ));
                                $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                    $array_lista_terceros_tm)->with('lista_cabecera', $array_cabecera_terceros_tm)->with('contador',$contador_cabecera_terceros_tm);
                            });
                        }
                    })->download('xlsx');
                    $mensaje_retail = 'SE ENCONTRARON DATOS EXPORTANDO';
                    $tipo_mensaje_retail = 'alert-success';
                } else {
                    $mensaje_retail = 'NO SE ENCONTRARON DATOS SUBA EL ARCHIVO EXCEL O CSV DEL DIA';
                    $tipo_mensaje_retail = 'alert-warning';
                    $estado = true;
                }
                break;
        }
        /*
        return redirect('/importacionesexcel/import_excel')->with('estado', $estado)->with('autoservicio', $selected)
            ->with('fecha', $fecha)->with('mensaje_retail', $mensaje_retail)->with('tipo_mensaje_retail', $tipo_mensaje_retail)->with('mensaje_almacen', $mensaje_almacen)
            ->with('tipo_mensaje_almacen', $tipo_mensaje_almacen)->with('mensaje_terceros', $mensaje_terceros)->with('tipo_mensaje_terceros', $tipo_mensaje_terceros);
        */
        //return response()->json(["data" => json_decode($mensaje_retail)],200);
        //echo json_encode(compact('estado'));

        /*
        return redirect()->back()->with('estado', $estado)->with('autoservicio', $selected)->with('fecha', $fecha)
            ->with('mensaje_retail', $mensaje_retail)->with('tipo_mensaje_retail', $tipo_mensaje_retail)->with('mensaje_almacen', $mensaje_almacen)
            ->with('tipo_mensaje_almacen', $tipo_mensaje_almacen)->with('mensaje_terceros', $mensaje_terceros)->with('tipo_mensaje_terceros', $tipo_mensaje_terceros);
        */
        View::share('titulo','Importar Excel');
        return View::make('importacionesexcel/import_excel',
            [
                'estado' => $estado,
                'estado_validar' => !$estado,
                'autoservicio' => $selected,
                'fecha' => $fecha,
                'mensaje_retail' => $mensaje_retail,
                'tipo_mensaje_retail' => $tipo_mensaje_retail
            ]);
        /*
        return Redirect::to('/import_excel')->with('estado',$estado)->with('autoservicio',$selected)->with('fecha',$fecha)
            ->with('mensaje_retail',$mensaje_retail)->with('tipo_mensaje_retail',$tipo_mensaje_retail);
        */
    }

    function actionImportExcel($idopcion,$autoservicio,$fecha,Request $request)
    {
        $validation = $this->validate($request);
        if ($validation) {

            $filePath = $request->file('select_file')->getRealPath();

            $charset = '';

            switch ($autoservicio) {
                case 'TOTTUS':
                    $charset = 'ISO-8859-1';
                    break;
                default:
                    $charset = 'UTF-8';
                    break;
            }

            Excel::load($filePath, function ($reader) use ($autoservicio, $fecha) {

                $auxiliar = $reader->toObject();
                //dd($auxiliar);

                foreach ($auxiliar as $key => $value) { //sheet
                    switch ($autoservicio) {
                        case 'CENCOSUD':
                            foreach ($value as $key1 => $value1) { //fila

                                $array = json_decode($value1, true);

                                $count = 0;
                                $cod_producto = '';
                                $nom_producto = '';
                                $cod_sucursal = '';
                                $nom_sucursal = '';
                                $stock_producto = 0.0;
                                foreach ($array as $key2 => $value2) {
                                    switch ($count) {
                                        case 0:
                                            break;
                                        case 1:
                                            $cod_producto = $value2;
                                            break;
                                        case 2:
                                            $nom_producto = $value2;
                                            break;
                                        default:
                                            if (!(is_null($key2) or empty($key2) or $key2 == '0')) {
                                                $sucursal_excel = explode("_", $key2);
                                                $cod_sucursal = strtoupper(end($sucursal_excel));
                                                $contador_limit = count($sucursal_excel) - 1;
                                                $nom_sucursal = '';
                                                for ($i = 0; $i < $contador_limit; $i++) {
                                                    $nom_sucursal = $nom_sucursal . ' ' . strtoupper($sucursal_excel[$i]);
                                                }
                                                $temporal = $value2;
                                                if (is_null($temporal) or empty($temporal)) {
                                                    $stock_producto = 0.0;
                                                } else {
                                                    $stock_producto = floatval($temporal);
                                                }
                                                $this->guardar_stock_autoservicio('I', $autoservicio,
                                                    '', trim($cod_producto), trim($nom_producto), trim($cod_sucursal), trim($nom_sucursal),
                                                    trim($stock_producto), trim($fecha), 1);
                                            }
                                            break;
                                    }
                                    $count++;
                                }
                            }
                            break;
                        case 'MAYORSA':

                            $array = json_decode($value, true);

                            $cod_producto = '';
                            $nom_producto = '';
                            $cod_sucursal = '';
                            $nom_sucursal = '';
                            $stock_producto = 0.0;
                            foreach ($array as $key2 => $value2) {
                                switch ($key2) {
                                    case 'p_codigo_de_producto':
                                        $cod_producto = $value2;
                                        break;
                                    case 'p_nombre_de_producto':
                                        $nom_producto = $value2;
                                        break;
                                    case 't_tienda':
                                        $sucursal_excel = explode(":", $value2);
                                        $cod_sucursal = strtoupper($sucursal_excel[0]);
                                        break;
                                    case 't_nombre_de_tienda':
                                        $nom_sucursal = $value2;
                                        break;
                                    case 'unidades_stock':
                                            $temporal = $value2;
                                            if (is_null($temporal) or empty($temporal)) {
                                                $stock_producto = 0.0;
                                            } else {
                                                $stock_producto = floatval($temporal);
                                            }
                                            $this->guardar_stock_autoservicio('I', $autoservicio,
                                                '', trim($cod_producto), trim($nom_producto), trim($cod_sucursal), trim($nom_sucursal),
                                                trim($stock_producto), trim($fecha), 1);
                                        break;
                                }
                            }
                            break;
                        case 'TOTTUS':
                            $array = json_decode($value, true);
                            $cod_producto = '';
                            $nom_producto = '';
                            $cod_sucursal = '';
                            $nom_sucursal = '';
                            $stock_producto = 0.0;
                            foreach ($array as $key2 => $value2) {
                                switch ($key2) {
                                    case 'sku':
                                        $cod_producto = $value2;
                                        break;
                                    case 'descripcion_del_producto':
                                        $nom_producto = $value2;
                                        break;
                                    case 'n0_local':
                                        $sucursal_excel = explode(":", $value2);
                                        $cod_sucursal = strtoupper($sucursal_excel[0]);
                                        break;
                                    case 'nombre_local':
                                        $nom_sucursal = $value2;
                                        break;
                                    case 'inventario_en_localesu':
                                        $temporal = $value2;
                                        if (is_null($temporal) or empty($temporal)) {
                                            $stock_producto = 0.0;
                                        } else {
                                            $stock_producto = floatval($temporal);
                                        }
                                        $this->guardar_stock_autoservicio('I', $autoservicio,
                                            '', trim($cod_producto), trim($nom_producto), trim($cod_sucursal), trim($nom_sucursal),
                                            trim($stock_producto), trim($fecha), 1);
                                        break;
                                }
                            }
                            break;
                    }
                }
            }, $charset);

            //$request->setJson(array('startDate'=>$fecha,'autoservicio'=>$autoservicio));

            //$this->validate($request);

            View::share('titulo','Importar Excel');
            return View::make('importacionesexcel/import_excel',
                [
                    'estado' => false,
                    'estado_validar' => true,
                    'autoservicio' => 'CENCOSUD',
                    'fecha' => $fecha,//date('Y-m-d'),
                    'mensaje_retail' => 'EL REGISTRO FUE EXITOSO VALIDE PERIODO PARA DESCARGAR EL FORMATO',
                    'tipo_mensaje_retail' => 'alert-success'
                ]);

        } else {
            View::share('titulo','Importar Excel');
            return View::make('importacionesexcel/import_excel',
                [
                    'estado' => true,
                    'estado_validar' => false,
                    'autoservicio' => 'CENCOSUD',
                    'fecha' => $fecha,//date('Y-m-d'),
                    'mensaje_retail' => 'EL TIPO DE FORMATO DEL ARCHIVO NO ES CORRECTO. FORMATOS PERMITIDOS (.xlsx,.xls,.csv)',
                    'tipo_mensaje_retail' => 'alert-danger'
                ]);
        }
    }

}