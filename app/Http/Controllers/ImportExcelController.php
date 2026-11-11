<?php

namespace App\Http\Controllers;

use App\Modelos\CONPeriodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\View;
use App\Traits\ImportExcelTraits;
use Session;

class ImportExcelController extends Controller
{

    use ImportExcelTraits;

    public function actionGestionImportExcel($idopcion)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        $mensaje = '';
        $tipo_mensaje = 'alert-primary';
        $estado_validar = true;
        $listaarchivo = $this->obtener_tabla_archivo_autoservicio('A', 'CENCOSUD', '1901-01-01', '1901-01-01', 'A');
        $sel_periodo = '';
        $anio = $this->anio;
        $array_anio_pc = $this->pc_array_anio_cuentas_contable(Session::get('empresas')->COD_EMPR);
        $combo_anio_pc = $this->gn_generacion_combo_array('Seleccione año', '', $array_anio_pc);
        $combo_periodo = $this->gn_combo_periodo_xanio_xempresa($anio, Session::get('empresas')->COD_EMPR, '', 'Seleccione periodo');
        if ($validarurl <> 'true') {
            $mensaje = 'NO TIENES ACCESO A ESTA OPCION';
            $estado_validar = false;
            $tipo_mensaje = 'alert-danger';
        }
        /******************************************************/
        View::share('titulo', 'Importar Excel');
        return View::make('importacionesexcel/import_excel',
            [
                'estado' => false,
                'estado_validar' => $estado_validar,
                'estado_eliminar' => false,
                'autoservicio' => 'CENCOSUD',
                'fecha' => date('Y-m-d'),
                'mensaje_retail' => $mensaje,
                'tipo_mensaje_retail' => $tipo_mensaje,
                'idopcion' => $idopcion,
                'listaarchivo' => $listaarchivo,
                'ajax' => true,
                'anio' => $anio,
                'combo_anio_pc' => $combo_anio_pc,
                'sel_periodo' => $sel_periodo,
                'combo_periodo' => $combo_periodo
            ]);
    }

    function actionValidateExcel($idopcion, Request $request)
    {
        /******************* VARIABLES REQUEST **********************/
        $fecha = $request->get('startDate');
        $selected = $request->get('autoservicio');
        /******************************************************/
        /******************* VALIDAR URL **********************/
        $sel_periodo = '';
        $anio = $this->anio;
        $array_anio_pc = $this->pc_array_anio_cuentas_contable(Session::get('empresas')->COD_EMPR);
        $combo_anio_pc = $this->gn_generacion_combo_array('Seleccione año', '', $array_anio_pc);
        $combo_periodo = $this->gn_combo_periodo_xanio_xempresa($anio, Session::get('empresas')->COD_EMPR, '', 'Seleccione periodo');
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl <> 'true') {
            View::share('titulo', 'Importar Excel');
            return View::make('importacionesexcel/import_excel',
                [
                    'estado' => false,
                    'estado_validar' => false,
                    'estado_eliminar' => true,
                    'autoservicio' => $selected,
                    'fecha' => $fecha,
                    'mensaje_retail' => 'NO TIENES ACCESO A ESTA OPCION',
                    'tipo_mensaje_retail' => 'alert-danger',
                    'idopcion' => $idopcion,
                    'listaarchivo' => array(),
                    'ajax' => true,
                    'anio' => $anio,
                    'combo_anio_pc' => $combo_anio_pc,
                    'sel_periodo' => $sel_periodo,
                    'combo_periodo' => $combo_periodo
                ]);
        }
        /******************************************************/
        /******************* DECLARACIÓN DE VARIABLES GLOBALES **********************/
        $mensaje_retail = '';
        $tipo_mensaje_retail = '';
        $mensaje_almacen = '';
        $tipo_mensaje_almacen = '';
        $mensaje_terceros = '';
        $tipo_mensaje_terceros = '';
        $estado = false;
        $existe_data = true;
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
        $array_cabecera_spsa = array();
        $array_cabecera_vivanda = array();
        $array_cabecera_makro = array();
        $array_cabecera_mass = array();
        $array_lista_spsa = array();
        $array_lista_vivanda = array();
        $array_lista_makro = array();
        $array_lista_mass = array();
        /******************************************************/
        /******************* BÚSQUEDA DE RESULTADOS **********************/
        $listaarchivo = $this->obtener_tabla_archivo_autoservicio('A', $selected, '1901-01-01', '1901-01-01', 'A');
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
                break;
            case 'SPSA':
                $array_lista_spsa = $this->obtener_tabla_spsa('SPSA', $fecha, '', 'PVEA');
                $array_lista_vivanda = $this->obtener_tabla_spsa('SPSA', $fecha, '', 'VIVANDA');
                $array_lista_makro = $this->obtener_tabla_spsa('SPSA', $fecha, '', 'MK');
                $array_lista_mass = $this->obtener_tabla_spsa('SPSA', $fecha, '', 'MASS');
                $array_cabecera_spsa = $this->obtener_cabecera_spsa('SPSA', $fecha, 'PVEA');
                $array_cabecera_vivanda = $this->obtener_cabecera_spsa('SPSA', $fecha, 'VIVANDA');
                $array_cabecera_makro = $this->obtener_cabecera_spsa('SPSA', $fecha, 'MK');
                $array_cabecera_mass = $this->obtener_cabecera_spsa('SPSA', $fecha, 'MASS');

                $contador_cabecera_spsa = count($array_cabecera_spsa);
                $contador_cabecera_vivanda = count($array_cabecera_vivanda);
                $contador_cabecera_makro = count($array_cabecera_makro);
                $contador_cabecera_mass = count($array_cabecera_mass);
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
                break;
        }
        /******************************************************/
        if ($request->get('validar') !== null) {
            switch ($selected) {
                case 'CENCOSUD':

                    $nombre_excel = 'LISTADO_PRODUCTOS_CENCOSUD(' . $fecha . ')';

                    $titulo = $nombre_excel;

                    if ((count($array_lista_retail) > 0 and $contador_cabecera_retail > 0)
                        or (count($array_lista_almacen) > 0 and $contador_cabecera_almacen > 0)
                        or (count($array_lista_terceros) > 0 and $contador_cabecera_terceros > 0)) {
                        $existe_data = false;
                        Excel::create($titulo, function ($excel) use (
                            $array_lista_retail, $array_lista_almacen,
                            $array_lista_terceros, $array_cabecera_retail, $array_cabecera_almacen,
                            $array_cabecera_terceros, $contador_cabecera_retail, $contador_cabecera_almacen, $contador_cabecera_terceros
                        ) {
                            if (count($array_lista_retail) > 0 and count($array_cabecera_retail) > 0) {
                                $excel->sheet('Productos Retail', function ($sheet) use ($array_lista_retail, $array_cabecera_retail, $contador_cabecera_retail) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_retail)->with('lista_cabecera', $array_cabecera_retail)->with('contador', $contador_cabecera_retail);
                                });
                            }
                            if (count($array_lista_almacen) > 0 and count($array_cabecera_almacen) > 0) {
                                $excel->sheet('Productos Almacen', function ($sheet) use ($array_lista_almacen, $array_cabecera_almacen, $contador_cabecera_almacen) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_almacen)->with('lista_cabecera', $array_cabecera_almacen)->with('contador', $contador_cabecera_almacen);
                                });
                            }
                            if (count($array_lista_terceros) > 0 and count($array_cabecera_terceros) > 0) {
                                $excel->sheet('Productos Terceros', function ($sheet) use ($array_lista_terceros, $array_cabecera_terceros, $contador_cabecera_terceros) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_terceros)->with('lista_cabecera', $array_cabecera_terceros)->with('contador', $contador_cabecera_terceros);
                                });
                            }
                        })->download('xlsx');
                        $mensaje_retail = 'EXPORTANDO DATOS DEL DIA POR FAVOR ESPERE';
                        $tipo_mensaje_retail = 'alert-success';
                    } else {
                        $mensaje_retail = 'NO SE ENCONTRARON DATOS SUBA EL ARCHIVO EXCEL O CSV DEL DIA PARA EL AUTOSERVICIO';
                        $tipo_mensaje_retail = 'alert-warning';
                        $estado = true;
                    }
                    break;
                case 'SPSA':

                    $nombre_excel = 'LISTADO_PRODUCTOS_SPSA(' . $fecha . ')';

                    $titulo = $nombre_excel;

                    if ((count($array_lista_spsa) > 0 and $contador_cabecera_spsa > 0)
                        or (count($array_lista_vivanda) > 0 and $contador_cabecera_vivanda > 0)
                        or (count($array_lista_makro) > 0 and $contador_cabecera_makro > 0)
                        or (count($array_lista_mass) > 0 and $contador_cabecera_mass > 0)) {
                        $existe_data = false;
                        Excel::create($titulo, function ($excel) use (
                            $array_lista_spsa, $array_lista_vivanda,
                            $array_lista_makro, $array_lista_mass, $array_cabecera_spsa,
                            $array_cabecera_vivanda, $array_cabecera_makro, $array_cabecera_mass, $contador_cabecera_spsa,
                            $contador_cabecera_vivanda, $contador_cabecera_makro, $contador_cabecera_mass
                        ) {
                            if (count($array_lista_spsa) > 0 and count($array_cabecera_spsa) > 0) {
                                $excel->sheet('SPSA', function ($sheet) use ($array_lista_spsa, $array_cabecera_spsa, $contador_cabecera_spsa) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_spsa)->with('lista_cabecera', $array_cabecera_spsa)->with('contador', $contador_cabecera_spsa);
                                });
                            }
                            if (count($array_lista_vivanda) > 0 and count($array_cabecera_vivanda) > 0) {
                                $excel->sheet('VIVANDA', function ($sheet) use ($array_lista_vivanda, $array_cabecera_vivanda, $contador_cabecera_vivanda) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_vivanda)->with('lista_cabecera', $array_cabecera_vivanda)->with('contador', $contador_cabecera_vivanda);
                                });
                            }
                            if (count($array_lista_makro) > 0 and count($array_cabecera_makro) > 0) {
                                $excel->sheet('MAKRO', function ($sheet) use ($array_lista_makro, $array_cabecera_makro, $contador_cabecera_makro) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_makro)->with('lista_cabecera', $array_cabecera_makro)->with('contador', $contador_cabecera_makro);
                                });
                            }
                            if (count($array_lista_mass) > 0 and count($array_cabecera_mass) > 0) {
                                $excel->sheet('MASS', function ($sheet) use ($array_lista_mass, $array_cabecera_mass, $contador_cabecera_mass) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_mass)->with('lista_cabecera', $array_cabecera_mass)->with('contador', $contador_cabecera_mass);
                                });
                            }
                        })->download('xlsx');
                        $mensaje_retail = 'EXPORTANDO DATOS DEL DIA POR FAVOR ESPERE';
                        $tipo_mensaje_retail = 'alert-success';
                    } else {
                        $mensaje_retail = 'NO SE ENCONTRARON DATOS SUBA EL ARCHIVO EXCEL O CSV DEL DIA PARA EL AUTOSERVICIO';
                        $tipo_mensaje_retail = 'alert-warning';
                        $estado = true;
                    }
                    break;
                default:

                    $nombre_excel_tm = 'LISTADO_PRODUCTOS_' . $selected . '(' . $fecha . ')';

                    $titulo_tm = $nombre_excel_tm;

                    if ((count($array_lista_retail_tm) > 0 and $contador_cabecera_retail_tm > 0)
                        or (count($array_lista_almacen_tm) > 0 and $contador_cabecera_almacen_tm > 0)
                        or (count($array_lista_terceros_tm) > 0 and $contador_cabecera_terceros_tm > 0)) {
                        $existe_data = false;
                        Excel::create($titulo_tm, function ($excel) use (
                            $array_lista_retail_tm, $array_lista_almacen_tm,
                            $array_lista_terceros_tm, $array_cabecera_retail_tm, $array_cabecera_almacen_tm,
                            $array_cabecera_terceros_tm, $contador_cabecera_retail_tm, $contador_cabecera_almacen_tm, $contador_cabecera_terceros_tm
                        ) {
                            if (count($array_lista_retail_tm) > 0 and count($array_cabecera_retail_tm) > 0) {
                                $excel->sheet('Productos Propios Livianos', function ($sheet) use ($array_lista_retail_tm, $array_cabecera_retail_tm, $contador_cabecera_retail_tm) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_retail_tm)->with('lista_cabecera', $array_cabecera_retail_tm)->with('contador', $contador_cabecera_retail_tm);
                                });
                            }
                            if (count($array_lista_almacen_tm) > 0 and count($array_cabecera_almacen_tm) > 0) {
                                $excel->sheet('Productos Propios Sacos', function ($sheet) use ($array_lista_almacen_tm, $array_cabecera_almacen_tm, $contador_cabecera_almacen_tm) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_almacen_tm)->with('lista_cabecera', $array_cabecera_almacen_tm)->with('contador', $contador_cabecera_almacen_tm);
                                });
                            }
                            if (count($array_lista_terceros_tm) > 0 and count($array_cabecera_terceros_tm) > 0) {
                                $excel->sheet('Productos Terceros', function ($sheet) use ($array_lista_terceros_tm, $array_cabecera_terceros_tm, $contador_cabecera_terceros_tm) {
                                    $sheet->setAutoFilter('A1:Z1');
                                    $sheet->setColumnFormat(array(
                                        'B:Z' => '0.0000',
                                        'AA:AZ' => '0.0000'
                                    ));
                                    $sheet->loadView('importacionesexcel/contenedor_migracion')->with('lista_migracion',
                                        $array_lista_terceros_tm)->with('lista_cabecera', $array_cabecera_terceros_tm)->with('contador', $contador_cabecera_terceros_tm);
                                });
                            }
                        })->download('xlsx');
                        $mensaje_retail = 'EXPORTANDO DATOS DEL DIA POR FAVOR ESPERE';
                        $tipo_mensaje_retail = 'alert-success';
                    } else {
                        $mensaje_retail = 'NO SE ENCONTRARON DATOS SUBA EL ARCHIVO EXCEL O CSV DEL DIA PARA EL AUTOSERVICIO';
                        $tipo_mensaje_retail = 'alert-warning';
                        $estado = true;
                    }
                    break;
            }
            View::share('titulo', 'Importar Excel');
            return View::make('importacionesexcel/import_excel',
                [
                    'estado' => $estado,
                    'estado_validar' => !$estado,
                    'estado_eliminar' => $existe_data,
                    'autoservicio' => $selected,
                    'fecha' => $fecha,
                    'mensaje_retail' => $mensaje_retail,
                    'tipo_mensaje_retail' => $tipo_mensaje_retail,
                    'idopcion' => $idopcion,
                    'listaarchivo' => $listaarchivo,
                    'ajax' => true,
                    'anio' => $anio,
                    'combo_anio_pc' => $combo_anio_pc,
                    'sel_periodo' => $sel_periodo,
                    'combo_periodo' => $combo_periodo
                ]);
        } else {
            switch ($selected) {
                case 'CENCOSUD':
                    if ((count($array_lista_retail) > 0 and $contador_cabecera_retail > 0)
                        or (count($array_lista_almacen) > 0 and $contador_cabecera_almacen > 0)
                        or (count($array_lista_terceros) > 0 and $contador_cabecera_terceros > 0)) {
                        $this->guardar_stock_autoservicio('D', $selected,
                            '', '', '', '', '',
                            '0.0', trim($fecha), 0);
                        $this->guardar_archivo_stock_autoservicio('D', $selected,
                            '', trim($fecha), '', 0);
                        $mensaje_retail = 'ARCHIVO DEL DIA ' . $fecha . ' PARA EL AUTOSERVICIO ' . $selected . ' ELIMINADO CORRECTAMENTE';
                        $tipo_mensaje_retail = 'alert-success';
                    } else {
                        $mensaje_retail = 'ARCHIVO DEL DIA ' . $fecha . ' PARA EL AUTOSERVICIO ' . $selected . ' NO EXISTE';
                        $tipo_mensaje_retail = 'alert-warning';
                        $estado = true;
                    }
                    break;
                default:
                    if ((count($array_lista_retail_tm) > 0 and $contador_cabecera_retail_tm > 0)
                        or (count($array_lista_almacen_tm) > 0 and $contador_cabecera_almacen_tm > 0)
                        or (count($array_lista_terceros_tm) > 0 and $contador_cabecera_terceros_tm > 0)) {
                        $this->guardar_stock_autoservicio('D', $selected,
                            '', '', '', '', '',
                            '0.0', trim($fecha), 0);
                        $this->guardar_archivo_stock_autoservicio('D', $selected,
                            '', trim($fecha), '', 0);
                        $mensaje_retail = 'ARCHIVO DEL DIA ' . $fecha . ' PARA EL AUTOSERVICIO ' . $selected . ' ELIMINADO CORRECTAMENTE';
                        $tipo_mensaje_retail = 'alert-success';
                    } else {
                        $mensaje_retail = 'ARCHIVO DEL DIA ' . $fecha . ' PARA EL AUTOSERVICIO ' . $selected . ' NO EXISTE';
                        $tipo_mensaje_retail = 'alert-warning';
                        $estado = true;
                    }
                    break;
            }
            View::share('titulo', 'Importar Excel');
            return View::make('importacionesexcel/import_excel',
                [
                    'estado' => false,
                    'estado_validar' => true,
                    'estado_eliminar' => false,
                    'autoservicio' => $selected,
                    'fecha' => $fecha,
                    'mensaje_retail' => $mensaje_retail,
                    'tipo_mensaje_retail' => $tipo_mensaje_retail,
                    'idopcion' => $idopcion,
                    'listaarchivo' => $listaarchivo,
                    'ajax' => true,
                    'anio' => $anio,
                    'combo_anio_pc' => $combo_anio_pc,
                    'sel_periodo' => $sel_periodo,
                    'combo_periodo' => $combo_periodo
                ]);
        }
    }

    function actionImportExcel($idopcion, Request $request)
    {
        $autoservicio = $request->get('autoservice');
        $fecha = $request->get('date');
        $sel_periodo = '';
        $anio = $this->anio;
        $array_anio_pc = $this->pc_array_anio_cuentas_contable(Session::get('empresas')->COD_EMPR);
        $combo_anio_pc = $this->gn_generacion_combo_array('Seleccione año', '', $array_anio_pc);
        $combo_periodo = $this->gn_combo_periodo_xanio_xempresa($anio, Session::get('empresas')->COD_EMPR, '', 'Seleccione periodo');
        if ($request->get('upload') !== null) {
            /******************* validar url **********************/
            $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
            if ($validarurl <> 'true') {
                View::share('titulo', 'Importar Excel');
                return View::make('importacionesexcel/import_excel',
                    [
                        'estado' => false,
                        'estado_validar' => false,
                        'autoservicio' => $autoservicio,
                        'fecha' => $fecha,
                        'mensaje_retail' => 'NO TIENES ACCESO A ESTA OPCION',
                        'tipo_mensaje_retail' => 'alert-danger',
                        'idopcion' => $idopcion,
                        'listaarchivo' => array(),
                        'ajax' => true,
                        'anio' => $anio,
                        'combo_anio_pc' => $combo_anio_pc,
                        'sel_periodo' => $sel_periodo,
                        'combo_periodo' => $combo_periodo
                    ]);
            }
            /******************************************************/

            $validation = $this->funciones->validate($request);
            if ($validation) {

                $filePath = $request->file('select_file')->getRealPath();
                $original_name_file = $request->file('select_file')->getClientOriginalName();

                $charset = '';

                switch ($autoservicio) {
                    case 'TOTTUS' or 'SPSA':
                        $charset = 'ISO-8859-1';
                        break;
                    default:
                        $charset = 'UTF-8';
                        break;
                }

                Excel::load($filePath, function ($reader) use ($autoservicio, $fecha, $original_name_file) {

                    $auxiliar = $reader->toObject();

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
                                                    $this->guardar_producto_autoservicio('I', $autoservicio,
                                                        '', trim($cod_producto), trim($nom_producto), 1);
                                                    $this->guardar_sucursal_autoservicio('I', $autoservicio,
                                                        '', trim($cod_sucursal), trim($nom_sucursal), 1);
                                                    $this->guardar_archivo_stock_autoservicio('I', $autoservicio,
                                                        '', trim($fecha), trim($original_name_file), 1);
                                                }
                                                break;
                                        }
                                        $count++;
                                    }
                                }
                                break;
                            case 'MAYORSA':

                                $array = json_decode($value, true);

                                $cod_producto = $array['p_codigo_de_producto'];;
                                $nom_producto = $array['p_nombre_de_producto'];;
                                $sucursal_excel = explode(":", $array['t_tienda']);
                                $cod_sucursal = strtoupper($sucursal_excel[0]);
                                $nom_sucursal = $array['t_nombre_de_tienda'];
                                if (is_null($array['unidades_stock']) or empty($array['unidades_stock'])) {
                                    $stock_producto = 0.0;
                                } else {
                                    $stock_producto = floatval($array['unidades_stock']);
                                }

                                $this->guardar_stock_autoservicio('I', $autoservicio,
                                    '', trim($cod_producto), trim($nom_producto), trim($cod_sucursal), trim($nom_sucursal),
                                    trim($stock_producto), trim($fecha), 1);
                                $this->guardar_producto_autoservicio('I', $autoservicio,
                                    '', trim($cod_producto), trim($nom_producto), 1);
                                $this->guardar_sucursal_autoservicio('I', $autoservicio,
                                    '', trim($cod_sucursal), trim($nom_sucursal), 1);
                                $this->guardar_archivo_stock_autoservicio('I', $autoservicio,
                                    '', trim($fecha), trim($original_name_file), 1);
                                break;
                            case 'TOTTUS':

                                $array = json_decode($value, true);

                                $cod_producto = $array['sku'];;
                                $nom_producto = $array['descripcion_del_producto'];;
                                $sucursal_excel = explode(":", $array['n0_local']);
                                $cod_sucursal = strtoupper($sucursal_excel[0]);
                                $nom_sucursal = $array['nombre_local'];
                                if (is_null($array['inventario_en_localesu']) or empty($array['inventario_en_localesu'])) {
                                    $stock_producto = 0.0;
                                } else {
                                    $stock_producto = floatval($array['inventario_en_localesu']);
                                }

                                $this->guardar_stock_autoservicio('I', $autoservicio,
                                    '', trim($cod_producto), trim($nom_producto), trim($cod_sucursal), trim($nom_sucursal),
                                    trim($stock_producto), trim($fecha), 1);
                                $this->guardar_producto_autoservicio('I', $autoservicio,
                                    '', trim($cod_producto), trim($nom_producto), 1);
                                $this->guardar_sucursal_autoservicio('I', $autoservicio,
                                    '', trim($cod_sucursal), trim($nom_sucursal), 1);
                                $this->guardar_archivo_stock_autoservicio('I', $autoservicio,
                                    '', trim($fecha), trim($original_name_file), 1);
                                break;
                            case 'SPSA':

                                $array = json_decode($value, true);

                                $cod_producto = $array['cod_spsa'];
                                $nom_producto = $array['descripcion'];
                                $cod_sucursal = $array['cod_local'];
                                $nom_sucursal = $array['descripcion_local'];
                                if (is_null($array['inventariou']) or empty($array['inventariou'])) {
                                    $stock_producto = 0.0;
                                } else {
                                    $stock_producto = floatval($array['inventariou']);
                                }

                                $this->guardar_stock_autoservicio('I', $autoservicio,
                                    '', trim($cod_producto), trim($nom_producto), trim($cod_sucursal), trim($nom_sucursal),
                                    trim($stock_producto), trim($fecha), 1);
                                $this->guardar_producto_autoservicio('I', $autoservicio,
                                    '', trim($cod_producto), trim($nom_producto), 1);
                                $this->guardar_sucursal_autoservicio('I', $autoservicio,
                                    '', trim($cod_sucursal), trim($nom_sucursal), 1);
                                $this->guardar_archivo_stock_autoservicio('I', $autoservicio,
                                    '', trim($fecha), trim($original_name_file), 1);
                                break;
                        }
                    }
                }, $charset);

                $listaarchivo = $this->obtener_tabla_archivo_autoservicio('A', $autoservicio, '1901-01-01', '1901-01-01', 'A');

                View::share('titulo', 'Importar Excel');
                return View::make('importacionesexcel/import_excel',
                    [
                        'estado' => false,
                        'estado_validar' => true,
                        'estado_eliminar' => false,
                        'autoservicio' => $autoservicio,
                        'fecha' => $fecha,//date('Y-m-d'),
                        'mensaje_retail' => 'EL REGISTRO FUE EXITOSO VALIDE EL ARCHIVO DEL DIA PARA DESCARGAR EL FORMATO',
                        'tipo_mensaje_retail' => 'alert-success',
                        'idopcion' => $idopcion,
                        'listaarchivo' => $listaarchivo,
                        'ajax' => true,
                        'anio' => $anio,
                        'combo_anio_pc' => $combo_anio_pc,
                        'sel_periodo' => $sel_periodo,
                        'combo_periodo' => $combo_periodo
                    ]);

            } else {

                $listaarchivo = $this->obtener_tabla_archivo_autoservicio('A', $autoservicio, '1901-01-01', '1901-01-01', 'A');

                View::share('titulo', 'Importar Excel');
                return View::make('importacionesexcel/import_excel',
                    [
                        'estado' => true,
                        'estado_validar' => false,
                        'estado_eliminar' => true,
                        'autoservicio' => $autoservicio,
                        'fecha' => $fecha,//date('Y-m-d'),
                        'mensaje_retail' => 'EL TIPO DE FORMATO DEL ARCHIVO NO ES CORRECTO. FORMATOS PERMITIDOS (.xlsx,.xls,.csv)',
                        'tipo_mensaje_retail' => 'alert-danger',
                        'idopcion' => $idopcion,
                        'listaarchivo' => $listaarchivo,
                        'ajax' => true,
                        'anio' => $anio,
                        'combo_anio_pc' => $combo_anio_pc,
                        'sel_periodo' => $sel_periodo,
                        'combo_periodo' => $combo_periodo
                    ]);
            }
        } else {

            $listaarchivo = $this->obtener_tabla_archivo_autoservicio('A', $autoservicio, '1901-01-01', '1901-01-01', 'A');

            View::share('titulo', 'Importar Excel');
            return View::make('importacionesexcel/import_excel',
                [
                    'estado' => false,
                    'estado_validar' => true,
                    'estado_eliminar' => false,
                    'autoservicio' => $autoservicio,
                    'fecha' => $fecha,//date('Y-m-d'),
                    'mensaje_retail' => 'VALIDE ARCHIVO DEL DIA PARA EL AUTOSERVICIO',
                    'tipo_mensaje_retail' => 'alert-success',
                    'idopcion' => $idopcion,
                    'listaarchivo' => $listaarchivo,
                    'ajax' => true,
                    'anio' => $anio,
                    'combo_anio_pc' => $combo_anio_pc,
                    'sel_periodo' => $sel_periodo,
                    'combo_periodo' => $combo_periodo
                ]);
        }
    }

    public function actionAjaxListarArchivoAutoservicio(Request $request)
    {
        $autoservicio = $request['autoservicio'];
        $idopcion = $request['idopcion'];
        $listaarchivo = $this->obtener_tabla_archivo_autoservicio('A', $autoservicio, '1901-01-01', '1901-01-01', 'A');

        //dd($listacuentacategoria);

        $funcion = $this;

        return View::make('importacionesexcel/alistaarchivo',
            [
                'listaarchivo' => $listaarchivo,
                'idopcion' => $idopcion,
                'funcion' => $funcion,
                'ajax' => true,
            ]);
    }

    function actionDescargarReporteRefuerzo($idopcion, Request $request)
    {
        /******************* VARIABLES REQUEST **********************/
        $cod_anio = $request->get('anio');
        $cod_periodo = $request->get('periodo_id');
        $autoservicio = $request->get('autoservicio_reporte');
        if (is_null($cod_periodo) or empty($cod_periodo)) {
            $cod_mes = 0;
        } else {
            $periodo = CONPeriodo::where('COD_PERIODO', '=', $cod_periodo)
                ->get();
            $cod_mes = $periodo[0]->COD_MES;
        }
        /******************************************************/
        /******************* VALIDAR URL **********************/
        $sel_periodo = '';
        $anio = $this->anio;
        $array_anio_pc = $this->pc_array_anio_cuentas_contable(Session::get('empresas')->COD_EMPR);
        $combo_anio_pc = $this->gn_generacion_combo_array('Seleccione año', '', $array_anio_pc);
        $combo_periodo = $this->gn_combo_periodo_xanio_xempresa($anio, Session::get('empresas')->COD_EMPR, '', 'Seleccione periodo');
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl <> 'true') {
            return "<script languaje='javascript' type='text/javascript'>window.close();</script>";
            /*
            View::share('titulo', 'Importar Excel');
            return View::make('importacionesexcel/import_excel',
                [
                    'estado' => false,
                    'estado_validar' => false,
                    'estado_eliminar' => true,
                    'autoservicio' => $autoservicio,
                    'fecha' => date("Y-m-d"),
                    'mensaje_retail' => 'NO TIENES ACCESO A ESTA OPCION',
                    'tipo_mensaje_retail' => 'alert-danger',
                    'idopcion' => $idopcion,
                    'listaarchivo' => array(),

                    'ajax' => true,
                    'anio' => $anio,
                    'combo_anio_pc' => $combo_anio_pc,
                    'sel_periodo' => $sel_periodo,
                    'combo_periodo' => $combo_periodo
                ]);
                */
        }
        /******************************************************/
        /******************* DECLARACIÓN DE VARIABLES GLOBALES **********************/
        //$mensaje_retail = 'DESCARGANDO DATOS';
        //$tipo_mensaje_retail = 'alert-success';
        //$estado = false;
        //$existe_data = true;

        $array_lista_producto = array();
        $array_lista_dias = array();
        $array_lista_meses = array();
        /******************************************************/
        /******************* BÚSQUEDA DE RESULTADOS **********************/
        $listaarchivo = $this->obtener_tabla_archivo_autoservicio('A', $autoservicio, '1901-01-01', '1901-01-01', 'A');

        $array_lista_producto = $this->obtener_tabla_cabecera_refuerzo($autoservicio, $cod_anio, 'C', $cod_mes);
        $array_lista_dias = $this->obtener_tabla_cabecera_refuerzo($autoservicio, $cod_anio, 'B', $cod_mes);
        $array_lista_meses = $this->obtener_tabla_cabecera_refuerzo($autoservicio, $cod_anio, 'A', $cod_mes);

        $contador_aux = 0;
        $lista_dias_separador = array();
        //dd($array_lista_dias);
        foreach ($array_lista_dias as $dia) {
            $fecha_string = $dia['FECHA_STRING'];
            $dia_actual = $dia['DIA'];
            if (count($array_lista_dias) - 1 > $contador_aux) {
                $dia_despues = $array_lista_dias[$contador_aux + 1]['DIA'];
                $suma_dias_semana = $dia_actual + 3;
                $suma_dias = $dia_actual + 2;
                $ind_semana = 0;
                $ind_mes = 0;
                if ($suma_dias_semana == $dia_despues) {
                    $ind_semana = 1;
                } else {
                    if ($suma_dias == $dia_despues) {
                        $ind_semana = 0;
                        $ind_mes = 0;
                    } else {
                        $ind_mes = 1;
                    }
                }
                $array_dia = array($fecha_string, $dia_actual, 1, $ind_semana, $ind_mes, 0.0000, 0.0000, 0.0000);
                $lista_dias_separador[] = $array_dia;
            } else {
                $ind_semana = 0;
                $ind_mes = 0;
                $array_dia = array($fecha_string, $dia_actual, 1, $ind_semana, $ind_mes, 0.0000, 0.0000, 0.0000);
                $lista_dias_separador[] = $array_dia;
            }
            $contador_aux++;
        }

        $nombre_excel = 'REPORTE_REFUERZO_' . $autoservicio;

        $titulo = $nombre_excel;

        if(count($array_lista_producto)>0){
            Excel::create($titulo, function ($excel) use (
                $array_lista_producto, $array_lista_dias,
                $array_lista_meses, $autoservicio, $cod_anio, $cod_mes, $lista_dias_separador
            ) {
                $contador_general = count($array_lista_dias) + 7;
                $contador_listadias = count($array_lista_dias);
                foreach ($array_lista_producto as $producto) {
                    $array_lista_refuerzo = $this->obtener_tabla_contenido_refuerzo($autoservicio, $cod_anio, trim($producto[2]), $cod_mes);
                    $nombre_producto = trim($producto[2]) . ' / ' . $producto[3];
                    foreach ($array_lista_refuerzo as $lista_refuerzo) {
                        $contador_aux_dias = 0;
                        $contador_vende_diario = 0;
                        $contador_vende_semanal = 0;
                        $contador_vende_mes = 0;
                        $contador_unid_dias = 0;
                        $contador_unid_semanas = 0;
                        $contador_unid_meses = 0;
                        $sum_unid_dias = 0;
                        $sum_unid_semanas = 0;
                        $sum_unid_meses = 0;
                        foreach ($lista_dias_separador as $separador) {
                            $ind_fecha_actual = $separador[0];
                            if (count($lista_dias_separador) - 1 > $contador_aux_dias) {
                                $ind_fecha_despues = $lista_dias_separador[$contador_aux_dias + 1][0];
                                $ind_dia = $separador[2];
                                $ind_semana = $separador[3];
                                $ind_mes = $separador[4];
                                $venta_diaria_actual = $lista_refuerzo[$ind_fecha_actual];
                                $venta_diaria_despues = $lista_refuerzo[$ind_fecha_despues];
                                $resta_venta = $venta_diaria_actual - $venta_diaria_despues;
                                if ($resta_venta > 0) {
                                    if ($ind_dia == 1) {
                                        $contador_vende_diario = $contador_vende_diario + $resta_venta;
                                        $separador[5] = $resta_venta;
                                    }
                                    if ($ind_semana == 1) {
                                        $contador_vende_semanal = $contador_vende_semanal + $contador_vende_diario;
                                        $separador[6] = $contador_vende_semanal;
                                        $contador_vende_diario = 0.0000;
                                    }
                                    if ($ind_mes == 1) {
                                        $contador_vende_mes = $contador_vende_mes + $contador_vende_semanal;
                                        $separador[7] = $contador_vende_mes;
                                        $contador_vende_semanal = 0.0000;
                                    }
                                }
                            } else {
                                $ind_dia = $separador[2];
                                $ind_semana = $separador[3];
                                $ind_mes = $separador[4];
                                $resta_venta = 0.0000;
                                if ($ind_dia == 1) {
                                    $contador_vende_diario = $contador_vende_diario + $resta_venta;
                                    $separador[5] = $resta_venta;
                                }
                                if ($ind_semana == 1) {
                                    $contador_vende_semanal = $contador_vende_semanal + $contador_vende_diario;
                                    $separador[6] = $contador_vende_semanal;
                                    $contador_vende_diario = 0.0000;
                                }
                                if ($ind_mes == 1) {
                                    $contador_vende_mes = $contador_vende_mes + $contador_vende_semanal;
                                    $separador[7] = $contador_vende_mes;
                                    $contador_vende_semanal = 0.0000;
                                }
                            }
                            $contador_aux_dias++;
                        }
                        foreach ($lista_dias_separador as $separador_contador) {
                            if ($separador_contador[2] == 1 and $separador_contador[5] > 0.0000) {
                                $contador_unid_dias++;
                                $sum_unid_dias = $sum_unid_dias + $separador_contador[5];
                            }
                            if ($separador_contador[3] == 1 and $separador_contador[6] > 0.0000) {
                                $contador_unid_semanas++;
                                $sum_unid_semanas = $sum_unid_semanas + $separador_contador[6];
                            }
                            if ($separador_contador[4] == 1 and $separador_contador[7] > 0.0000) {
                                $contador_unid_meses++;
                                $sum_unid_meses = $sum_unid_meses + $separador_contador[7];
                            }
                        }
                        if ($sum_unid_dias > 0.0000 and $contador_unid_dias > 0) {
                            $lista_refuerzo['VTA_DIA'] = $sum_unid_dias / $contador_unid_dias;
                        }
                        if ($sum_unid_semanas > 0.0000 and $contador_unid_semanas > 0) {
                            $lista_refuerzo['VTA_SEM'] = $sum_unid_semanas / $contador_unid_semanas;
                        }
                        if ($sum_unid_meses > 0.0000 and $contador_unid_meses > 0) {
                            $lista_refuerzo['VTA_MES'] = $sum_unid_meses / $contador_unid_meses;
                        }
                        foreach ($lista_dias_separador as $separador_contador) {
                            $separador_contador[5] = 0.0000;
                            $separador_contador[6] = 0.0000;
                            $separador_contador[7] = 0.0000;
                        }
                    }
                    $lista_totales = array();
                    $aux = 1;
                    $suma_total_dias_mes = 0;
                    $suma_total_dias_semana = 0;
                    $suma_total_dias_dia = 0;
                    foreach ($array_lista_refuerzo as $lista_refuerzo) {
                        $suma_total_dias_mes = $suma_total_dias_mes + $lista_refuerzo[3];
                        $suma_total_dias_semana = $suma_total_dias_semana + $lista_refuerzo[4];
                        $suma_total_dias_dia = $suma_total_dias_dia + $lista_refuerzo[5];
                    }
                    $lista_totales[] = $suma_total_dias_mes;
                    $lista_totales[] = $suma_total_dias_semana;
                    $lista_totales[] = $suma_total_dias_dia;
                    foreach ($array_lista_dias as $lista_dia) {
                        $aux_index = $aux + 5;
                        $suma_total_dias = 0;
                        foreach ($array_lista_refuerzo as $lista_refuerzo) {
                            $suma_total_dias = $suma_total_dias + $lista_refuerzo[$aux_index];
                        }
                        $lista_totales[] = $suma_total_dias;
                        $aux++;
                    }
                    $lista_totales[] = 0.0000;
                    if (count($array_lista_refuerzo) > 0) {
                        $excel->sheet(substr(trim($producto[3]), 0, 30), function ($sheet) use (
                            $array_lista_refuerzo, $array_lista_meses, $array_lista_dias,
                            $contador_general, $contador_listadias, $nombre_producto, $lista_totales
                        ) {
                            //$sheet->setAutoFilter('A1:FZ1');
                            $sheet->setColumnFormat(array(
                                'D:FH' => '0.0000'
                            ))->setStyle(array(
                                'font' => array(
                                    'name' => 'Arial',
                                    'size' => 8
                                )
                            ));
                            $sheet->loadView('importacionesexcel/contenedor_reporte_refuerzo')
                                ->with('producto', $nombre_producto)
                                ->with('contador_general', $contador_general)
                                ->with('contador_listadias', $contador_listadias)
                                ->with('lista_meses', $array_lista_meses)
                                ->with('lista_dias', $array_lista_dias)
                                ->with('lista_migracion', $array_lista_refuerzo)
                                ->with('lista_totales', $lista_totales)
                                ->setColumnFormat(array(
                                    'D:FH' => '0.0000'
                                ))->setStyle(array(
                                    'font' => array(
                                        'name' => 'Arial',
                                        'size' => 8
                                    )
                                ));
                        });
                    }
                }
            })->download('xlsx');
        } else {
            return "<script languaje='javascript' type='text/javascript'>window.close();</script>";
            /*
            $mensaje_retail = 'NO HAY DATOS QUE MOSTRAR';
            $tipo_mensaje_retail = 'alert-warning';
            View::share('titulo', 'Importar Excel');
            return View::make('importacionesexcel/import_excel',
                [
                    'estado' => $estado,
                    'estado_validar' => !$estado,
                    'estado_eliminar' => $existe_data,
                    'autoservicio' => $autoservicio,
                    'fecha' => date("Y-m-d"),
                    'mensaje_retail' => $mensaje_retail,
                    'tipo_mensaje_retail' => $tipo_mensaje_retail,
                    'idopcion' => $idopcion,
                    'listaarchivo' => $listaarchivo,
                    'ajax' => true,
                    'anio' => $anio,
                    'combo_anio_pc' => $combo_anio_pc,
                    'sel_periodo' => $sel_periodo,
                    'combo_periodo' => $combo_periodo
                ]);
                */
        }
        /*
        View::share('titulo', 'Importar Excel');
        return View::make('importacionesexcel/import_excel',
            [
                'estado' => $estado,
                'estado_validar' => !$estado,
                'estado_eliminar' => $existe_data,
                'autoservicio' => $autoservicio,
                'fecha' => date("Y-m-d"),
                'mensaje_retail' => $mensaje_retail,
                'tipo_mensaje_retail' => $tipo_mensaje_retail,
                'idopcion' => $idopcion,
                'listaarchivo' => $listaarchivo,
                'ajax' => true,
                'anio' => $anio,
                'combo_anio_pc' => $combo_anio_pc,
                'sel_periodo' => $sel_periodo,
                'combo_periodo' => $combo_periodo
            ]);
            */
    }

    public function actionAjaxComboPeriodoAnioEmpresa(Request $request)
    {

        $anio                   =   $request['anio'];
        $combo_periodo          =   $this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas')->COD_EMPR,'','Seleccione periodo');
        $sel_periodo            =   '';
        $funcion                =   $this;
        
        return View::make('importacionesexcel/combo/cperiodo',
                         [

                            'combo_periodo'         => $combo_periodo,
                            'sel_periodo'           => $sel_periodo,                        
                            'ajax'                  => true,                            
                         ]);
    }

}