<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CMPCategoria;
use App\WEBComisionConfiguracion;
use App\WEBSubcanalJefeVenta;
use View;
use Session;
use Maatwebsite\Excel\Facades\Excel;

class ComisionConfiguracionController extends Controller
{
    public function actionConfigurarComision($idopcion)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl <> 'true') {
            return $validarurl;
        }
        /******************************************************/

        // Fetch Categories for the selection form

        $marcas = CMPCategoria::where('TXT_GRUPO', 'MARCA_PRODUCTO')
            ->where('COD_ESTADO', 1)
            ->orderBy('NOM_CATEGORIA', 'asc')
            ->get();

        $tiempos = CMPCategoria::where('TXT_GRUPO', 'TIEMPO_COBRANZA')
            ->where('COD_ESTADO', 1)
            ->orderByRaw("CAST(NOM_CATEGORIA AS INT) ASC")
            ->get();



        $subcanales = CMPCategoria::where('TXT_GRUPO', 'SUB_CANAL_VENTA')
            ->where('COD_ESTADO', 1)
            ->orderBy('NOM_CATEGORIA', 'asc')
            ->get();

        $jefes = CMPCategoria::where('TXT_GRUPO', 'JEFE_VENTA')
            ->where('COD_ESTADO', 1)
            ->orderBy('NOM_CATEGORIA', 'asc')
            ->get();

        // Fetch existing configurations with names joined
        $configuraciones = WEBComisionConfiguracion::select(
            'WEB.comision_configuraciones.*',
            'M.NOM_CATEGORIA as NOM_MARCA',
            'T.NOM_CATEGORIA as NOM_TIEMPO',
            'S.NOM_CATEGORIA as NOM_SUBCANAL'
        )
            ->leftJoin('CMP.CATEGORIA as M', 'M.COD_CATEGORIA', '=', 'WEB.comision_configuraciones.cod_marca')
            ->leftJoin('CMP.CATEGORIA as T', 'T.COD_CATEGORIA', '=', 'WEB.comision_configuraciones.cod_tiempo_cobranza')
            ->leftJoin('CMP.CATEGORIA as S', 'S.COD_CATEGORIA', '=', 'WEB.comision_configuraciones.cod_sub_canal')
            ->orderBy('WEB.comision_configuraciones.id', 'asc')
            ->get();


        $subcanal_jefes = WEBSubcanalJefeVenta::where('cod_estado', 1)->get();


        // Map subcanal to jefes
        $subcanal_jefe_map = [];
        foreach ($subcanal_jefes as $sj) {
            $subcanal_jefe_map[$sj->cod_sub_canal][] = $sj->cod_jefe_venta;
        }

        // Map configuration for the Matrix View
        $config_map = [];
        $used_marcas_ids = [];
        $used_tiempos_ids = [];
        $used_subcanales_ids = [];

        foreach ($configuraciones as $config) {
            $key = $config->cod_marca . '|' . $config->cod_tiempo_cobranza . '|' . $config->cod_sub_canal;
            $config_map[$key] = [
                'porcentaje' => $config->porcentaje,
                'id' => $config->id
            ];

            if (!in_array($config->cod_marca, $used_marcas_ids))
                $used_marcas_ids[] = $config->cod_marca;
            if (!in_array($config->cod_tiempo_cobranza, $used_tiempos_ids))
                $used_tiempos_ids[] = $config->cod_tiempo_cobranza;
            if (!in_array($config->cod_sub_canal, $used_subcanales_ids))
                $used_subcanales_ids[] = $config->cod_sub_canal;
        }

        // Filter and keep the order of appearance
        $marcas_matrix = collect($used_marcas_ids)->map(function ($id) use ($marcas) {
            return $marcas->where('COD_CATEGORIA', $id)->first();
        })->filter();

        $tiempos_matrix = collect($used_tiempos_ids)->map(function ($id) use ($tiempos) {
            return $tiempos->where('COD_CATEGORIA', $id)->first();
        })->filter();

        $subcanales_matrix = collect($used_subcanales_ids)->map(function ($id) use ($subcanales) {
            return $subcanales->where('COD_CATEGORIA', $id)->first();
        })->filter();


        return View::make('comision/configuracioncomision', [
            'idopcion' => $idopcion,
            'marcas' => $marcas,
            'tiempos' => $tiempos,
            'subcanales' => $subcanales,
            'marcas_matrix' => $marcas_matrix,
            'tiempos_matrix' => $tiempos_matrix,
            'subcanales_matrix' => $subcanales_matrix,
            'jefes' => $jefes,
            'subcanal_jefe_map' => $subcanal_jefe_map,
            'configuraciones' => $configuraciones,
            'config_map' => $config_map,
            'funcion' => $this->funciones
        ]);
    }

    public function actionAjaxGuardarComision(Request $request)
    {
        $cod_marca = $request->input('cod_marca');
        $cod_tiempo = $request->input('cod_tiempo');
        $cod_sub_canal = $request->input('cod_sub_canal');
        $porcentaje = $request->input('porcentaje');

        // Check for duplicates
        $exists = WEBComisionConfiguracion::where('cod_marca', $cod_marca)
            ->where('cod_tiempo_cobranza', $cod_tiempo)
            ->where('cod_sub_canal', $cod_sub_canal)
            ->first();

        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'Esta combinación ya existe']);
        }

        $max_id = WEBComisionConfiguracion::max('id');
        if ($max_id) {
            // Extract numeric part after '1CIX'
            $number = (int) substr($max_id, 4) + 1;
        } else {
            $number = 1;
        }
        $new_id = '1CIX' . str_pad($number, 8, '0', STR_PAD_LEFT);

        // Fetch names for historical/denormalized storage
        $cat_marca = CMPCategoria::where('COD_CATEGORIA', $cod_marca)->first();
        $cat_tiempo = CMPCategoria::where('COD_CATEGORIA', $cod_tiempo)->first();
        $cat_sub_canal = CMPCategoria::where('COD_CATEGORIA', $cod_sub_canal)->first();

        $config = new WEBComisionConfiguracion();
        $config->id = $new_id;
        $config->cod_marca = $cod_marca;
        $config->nom_marca = $cat_marca->NOM_CATEGORIA ?? '';
        $config->cod_tiempo_cobranza = $cod_tiempo;
        $config->nom_tiempo_cobranza = $cat_tiempo->NOM_CATEGORIA ?? '';
        $config->cod_sub_canal = $cod_sub_canal;
        $config->nom_sub_canal = $cat_sub_canal->NOM_CATEGORIA ?? '';
        $config->porcentaje = $porcentaje;
        $config->usuario_creacion = Session::get('usuario')->id ?? 'ADMIN';
        $config->save();

        return response()->json(['status' => 'success', 'message' => 'Configuración añadida correctamente']);
    }

    public function actionAjaxModificarComision(Request $request)
    {
        try {
            $id = $request->input('id');
            $porcentaje = $request->input('porcentaje');

            $config = WEBComisionConfiguracion::find($id);
            if ($config) {
                $config->porcentaje = $porcentaje;
                $config->fecha_modificacion = DB::raw('GETDATE()');
                $config->usuario_modificacion = Session::get('usuario')->id ?? 'ADMIN';

                $config->save();
                return response()->json(['status' => 'success', 'message' => 'Comisión actualizada']);
            }

            return response()->json(['status' => 'error', 'message' => 'No se encontró el registro: ' . $id]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error en el servidor: ' . $e->getMessage()]);
        }
    }



    public function actionAjaxEliminarComision(Request $request)
    {
        $id = $request->input('id');
        WEBComisionConfiguracion::where('id', $id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Configuración eliminada']);
    }

    public function actionAjaxGuardarJefeSubcanal(Request $request)
    {
        $cod_sub_canal = $request->input('cod_sub_canal');
        $cod_jefes = $request->input('cod_jefes'); // Array of IDs

        // Fetch Subcanal name
        $cat_subcanal = CMPCategoria::where('COD_CATEGORIA', $cod_sub_canal)->first();
        $nom_subcanal = $cat_subcanal->NOM_CATEGORIA ?? '';

        // Deactivate all existing mappings for this subcanal (Soft Delete)
        WEBSubcanalJefeVenta::where('cod_sub_canal', $cod_sub_canal)
            ->update([
                'cod_estado' => 0,
                'usuario_modificacion' => Session::get('usuario')->id ?? 'ADMIN',
                'fecha_modificacion' => DB::raw('GETDATE()')
            ]);

        if (is_array($cod_jefes)) {
            foreach ($cod_jefes as $cod_jefe) {
                // Check if the record already exists (to reactivate or create)
                $sj = WEBSubcanalJefeVenta::where('cod_sub_canal', $cod_sub_canal)
                    ->where('cod_jefe_venta', $cod_jefe)
                    ->first();

                if ($sj) {
                    // Reactivate existing record
                    $sj->cod_estado = 1;
                    $sj->usuario_modificacion = Session::get('usuario')->id ?? 'ADMIN';
                    $sj->fecha_modificacion = DB::raw('GETDATE()');
                    $sj->save();
                } else {
                    // Create new record
                    $max_id = WEBSubcanalJefeVenta::max('id');
                    $number = 1;
                    if ($max_id && strpos($max_id, '1CIX') === 0) {
                        $number = (int) substr($max_id, 4) + 1;
                    }
                    $new_id = '1CIX' . str_pad($number, 8, '0', STR_PAD_LEFT);

                    $cat_jefe = CMPCategoria::where('COD_CATEGORIA', $cod_jefe)->first();

                    $sj = new WEBSubcanalJefeVenta();
                    $sj->id = $new_id;
                    $sj->cod_sub_canal = $cod_sub_canal;
                    $sj->nom_sub_canal = $nom_subcanal;
                    $sj->cod_jefe_venta = $cod_jefe;
                    $sj->nom_jefe_venta = $cat_jefe->NOM_CATEGORIA ?? '';
                    $sj->cod_estado = 1;
                    $sj->usuario_creacion = Session::get('usuario')->id ?? 'ADMIN';
                    $sj->fecha_creacion = DB::raw('GETDATE()');
                    $sj->save();
                }
            }
        }


        return response()->json(['status' => 'success', 'message' => 'Jefes asociados correctamente']);
    }

    public function actionExportarExcelComisiones()
    {
        try {
            set_time_limit(0);

            // Misma consulta con JOINs que usa la vista principal
            $configuraciones = WEBComisionConfiguracion::select(
                'WEB.comision_configuraciones.*',
                'M.NOM_CATEGORIA as NOM_MARCA',
                'T.NOM_CATEGORIA as NOM_TIEMPO',
                'S.NOM_CATEGORIA as NOM_SUBCANAL'
            )
                ->leftJoin('CMP.CATEGORIA as M', 'M.COD_CATEGORIA', '=', 'WEB.comision_configuraciones.cod_marca')
                ->leftJoin('CMP.CATEGORIA as T', 'T.COD_CATEGORIA', '=', 'WEB.comision_configuraciones.cod_tiempo_cobranza')
                ->leftJoin('CMP.CATEGORIA as S', 'S.COD_CATEGORIA', '=', 'WEB.comision_configuraciones.cod_sub_canal')
                ->orderBy('WEB.comision_configuraciones.id', 'asc')
                ->get();

            // Construir el mapa y recolectar valores únicos (en orden de aparición)
            $config_map = [];
            $marcas_ordered = [];
            $tiempos_ordered = [];
            $subcanales_ordered = [];

            foreach ($configuraciones as $c) {
                $marca = trim($c->NOM_MARCA ?? '');
                $tiempo = trim($c->NOM_TIEMPO ?? '');
                $subcanal = trim($c->NOM_SUBCANAL ?? '');

                if (!$marca || !$tiempo || !$subcanal)
                    continue;

                $config_map[$marca][$tiempo][$subcanal] = $c->porcentaje;

                if (!in_array($marca, $marcas_ordered))
                    $marcas_ordered[] = $marca;
                if (!in_array($tiempo, $tiempos_ordered))
                    $tiempos_ordered[] = $tiempo;
                if (!in_array($subcanal, $subcanales_ordered))
                    $subcanales_ordered[] = $subcanal;
            }

            // --- Datos de Jefes por Subcanal (para la segunda hoja) ---
            $subcanal_jefes_raw = WEBSubcanalJefeVenta::select(
                'WEB.subcanal_jefe_venta.*',
                'S.NOM_CATEGORIA as NOM_SUBCANAL',
                'J.NOM_CATEGORIA as NOM_JEFE'
            )
                ->leftJoin('CMP.CATEGORIA as S', 'S.COD_CATEGORIA', '=', 'WEB.subcanal_jefe_venta.cod_sub_canal')
                ->leftJoin('CMP.CATEGORIA as J', 'J.COD_CATEGORIA', '=', 'WEB.subcanal_jefe_venta.cod_jefe_venta')
                ->where('WEB.subcanal_jefe_venta.cod_estado', 1)
                ->orderBy('WEB.subcanal_jefe_venta.fecha_creacion', 'asc')
                ->get();

            // Agrupar jefes por subcanal (nombre -> lista de jefes)
            $jefes_por_subcanal = [];
            foreach ($subcanal_jefes_raw as $sj) {
                $nomSc = trim($sj->NOM_SUBCANAL ?? '');
                $nomJef = trim($sj->NOM_JEFE ?? '');
                if ($nomSc && $nomJef) {
                    $jefes_por_subcanal[$nomSc][] = $nomJef;
                }
            }

            \Excel::create('Cuadro_Comisiones_Matrix', function ($excel) use ($marcas_ordered, $tiempos_ordered, $subcanales_ordered, $config_map, $jefes_por_subcanal, $configuraciones) {
                $excel->sheet('Matrix', function ($sheet) use ($marcas_ordered, $tiempos_ordered, $subcanales_ordered, $config_map) {

                    // --- CABECERA ---
                    $header = ['MARCA', 'T/C'];
                    foreach ($subcanales_ordered as $sc) {
                        $header[] = $sc;
                    }
                    $sheet->row(1, $header);
                    $sheet->setHeight(1, 30); // Fila de cabecera más alta

                    // Estilo Cabecera
                    $lastColumnLetter = \PHPExcel_Cell::stringFromColumnIndex(count($header) - 1);
                    $sheet->cells("A1:{$lastColumnLetter}1", function ($cells) {
                        $cells->setBackground('#002060');
                        $cells->setFontColor('#ffffff');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                    // --- DATOS ---
                    $rowNum = 2;
                    foreach ($marcas_ordered as $marca) {
                        $marcaStartRow = $rowNum; // Fila donde empieza esta Marca
                        $marcaRowCount = 0;

                        foreach ($tiempos_ordered as $tiempo) {
                            // Solo imprimir si hay al menos 1 celda con dato para esta marca+tiempo
                            if (!isset($config_map[$marca][$tiempo]))
                                continue;

                            $rowData = ($marcaRowCount === 0) ? [$marca, $tiempo] : ['', $tiempo];

                            $zeroCols = [];
                            $colIdx = 2; // Empieza en Columna C
                            foreach ($subcanales_ordered as $sc) {
                                $val = $config_map[$marca][$tiempo][$sc] ?? null;
                                $rowData[] = $val !== null ? number_format($val, 2) : '';

                                // Si el valor es exactamente 0, guardamos la columna para pintar después
                                if ($val !== null && (float) $val == 0) {
                                    $zeroCols[] = \PHPExcel_Cell::stringFromColumnIndex($colIdx);
                                }
                                $colIdx++;
                            }
                            $sheet->row($rowNum, $rowData);

                            // 1. Color alterno para toda la fila
                            if ($rowNum % 2 == 0) {
                                $sheet->cells("A{$rowNum}:{$lastColumnLetter}{$rowNum}", function ($cells) {
                                    $cells->setBackground('#F9FAFB');
                                });
                            }

                            // 2. Sobrescribir celdas con 0 (Celeste) - DESPUÉS del color alterno para que no se pierda
                            foreach ($zeroCols as $colLetter) {
                                $sheet->cells($colLetter . $rowNum, function ($cell) {
                                    $cell->setBackground('#E1F5FE'); // Celeste claro
                                    $cell->setFontColor('#01579B'); // Azul oscuro para texto
                                    $cell->setFontWeight('bold');
                                });
                            }

                            $rowNum++;
                            $marcaRowCount++;
                        }

                        // Fusionar celdas de la columna A si la marca ocupa más de 1 fila
                        if ($marcaRowCount > 1) {
                            $marcaEndRow = $rowNum - 1;
                            $sheet->mergeCells("A{$marcaStartRow}:A{$marcaEndRow}");
                            $sheet->cells("A{$marcaStartRow}:A{$marcaEndRow}", function ($cells) {
                                $cells->setAlignment('center');
                                $cells->setValignment('center');
                                $cells->setFontWeight('bold');
                            });
                        }
                    }


                    // --- ESTILOS GENERALES ---
                    if ($rowNum > 2) {
                        $totalRows = $rowNum - 1;
                        $rangoCompleto = 'A1:' . $lastColumnLetter . $totalRows;

                        // Centrado
                        $sheet->cells($rangoCompleto, function ($cells) {
                            $cells->setAlignment('center');
                            $cells->setValignment('center');
                        });

                        // Bordes en todas las celdas (estilo tabla)
                        $sheet->getStyle($rangoCompleto)->applyFromArray([
                            'borders' => [
                                'allborders' => [
                                    'style' => 'thin',
                                    'color' => ['rgb' => 'AAAAAA'],
                                ],
                                'outline' => [
                                    'style' => 'medium',
                                    'color' => ['rgb' => '002060'],
                                ],
                            ],
                        ]);

                        // Auto-ajustar todas las columnas y luego fijar A y B
                        $sheet->setAutoSize(true);
                        $sheet->setWidth([
                            'A' => 16,
                            'B' => 8
                        ]);

                        // Estilo columna B (T/C) - Azul con letras blancas
                        $sheet->cells('B1:B' . $totalRows, function ($cells) {
                            $cells->setBackground('#002060');
                            $cells->setFontColor('#ffffff');
                            $cells->setFontWeight('bold');
                        });

                    }


                });

                // --- SEGUNDA HOJA: Jefes por Subcanal ---
                $excel->sheet('Jefes por Subcanal', function ($sheet) use ($jefes_por_subcanal) {

                    // Cabecera
                    $sheet->row(1, ['SUBCANAL', 'JEFE DE VENTA']);
                    $sheet->cells('A1:B1', function ($cells) {
                        $cells->setBackground('#002060');
                        $cells->setFontColor('#ffffff');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                    $rowNum = 2;
                    foreach ($jefes_por_subcanal as $subcanal => $jefes) {
                        $startRow = $rowNum;
                        $jefesCount = count($jefes);

                        foreach ($jefes as $idx => $jefe) {
                            $subcanName = ($idx === 0) ? $subcanal : '';
                            $sheet->row($rowNum, [$subcanName, $jefe]);

                            // Fondo celeste + negrita siempre en columna A
                            $sheet->cells("A{$rowNum}", function ($c) {
                                $c->setBackground('#E1F5FE');
                                $c->setFontWeight('bold');
                                $c->setAlignment('left'); // Alineado a la izquierda
                                $c->setValignment('center');
                            });

                            if ($rowNum % 2 == 0) {
                                $sheet->cells("B{$rowNum}", function ($c) {
                                    $c->setBackground('#EFF6FF');
                                });
                            }
                            $rowNum++;
                        }

                        // Fusionar columna A si el subcanal tiene más de 1 jefe
                        if ($jefesCount > 1) {
                            $endRow = $rowNum - 1;
                            $sheet->mergeCells("A{$startRow}:A{$endRow}");
                        }
                    }

                    // Estilos y bordes hoja 2
                    if ($rowNum > 2) {
                        $rango = 'A1:B' . ($rowNum - 1);

                        // Alineación general a la izquierda
                        $sheet->cells($rango, function ($cells) {
                            $cells->setAlignment('left');
                            $cells->setValignment('center');
                        });

                        $sheet->getStyle($rango)->applyFromArray([
                            'borders' => [
                                'allborders' => ['style' => 'thin', 'color' => ['rgb' => 'AAAAAA']],
                                'outline' => ['style' => 'medium', 'color' => ['rgb' => '002060']],
                            ],
                        ]);
                        $sheet->setWidth([
                            'A' => 35, // Un poco más ancho para que no se pegue al borde
                            'B' => 30
                        ]);
                    }


                });

                // --- TERCERA HOJA: Vista de Lista ---
                $excel->sheet('Vista de Lista', function ($sheet) use ($configuraciones) {

                    // Cabecera
                    $sheet->row(1, ['ID', 'MARCA', 'T/C', 'SUBCANAL', 'PORCENTAJE']);
                    $sheet->cells('A1:E1', function ($cells) {
                        $cells->setBackground('#002060');
                        $cells->setFontColor('#ffffff');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                    $rowNum = 2;
                    foreach ($configuraciones as $config) {
                        $sheet->row($rowNum, [
                            $config->id,
                            $config->NOM_MARCA,
                            $config->NOM_TIEMPO,
                            $config->NOM_SUBCANAL,
                            number_format($config->porcentaje, 2)
                        ]);

                        if ($rowNum % 2 == 0) {
                            $sheet->cells("A{$rowNum}:E{$rowNum}", function ($c) {
                                $c->setBackground('#F9FAFB');
                            });
                        }
                        $rowNum++;
                    }

                    // Estilos generales
                    if ($rowNum > 2) {
                        $rango = 'A1:E' . ($rowNum - 1);
                        $sheet->getStyle($rango)->applyFromArray([
                            'borders' => [
                                'allborders' => ['style' => 'thin', 'color' => ['rgb' => 'AAAAAA']],
                                'outline' => ['style' => 'medium', 'color' => ['rgb' => '002060']],
                            ],
                        ]);
                        $sheet->setAutoSize(true);
                        $sheet->cells($rango, function ($cells) {
                            $cells->setAlignment('center');
                            $cells->setValignment('center');
                        });
                    }
                });

                // --- FORZAR POSICIÓN EN LA PRIMERA HOJA ---
                $excel->setActiveSheetIndex(0);

            })->export('xls');

        } catch (\Exception $e) {
            return "Error al exportar Excel: " . $e->getMessage() . " en linea " . $e->getLine();
        }
    }

    public function actionAplicarComision($idopcion)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl <> 'true') {
            return $validarurl;
        }
        /******************************************************/
        $funcion = $this;

        // Filtro Jefe de Venta
        $jefes = DB::select("select cod_jefe_venta, nom_jefe_venta from WEB_CONFIGURACION_VENDEDOR");
        $combo_jefes = ['' => 'Seleccione Jefe'] + collect($jefes)->pluck('nom_jefe_venta', 'cod_jefe_venta')->toArray();

        // Filtro Periodo
        $periodos = DB::select("select COD_PERIODO, TXT_CODIGO, TXT_NOMBRE, FECHA_INCIO_COMISION, FECHA_FIN_COMISION from CON.PERIODO where COD_EMPR = 'IACHEM0000007086' ORDER BY COD_PERIODO DESC");
        $combo_periodos = ['' => 'Seleccione Periodo'] + collect($periodos)->pluck('TXT_CODIGO', 'COD_PERIODO')->toArray();


        return View::make('comision/aplicarcomision', [
            'idopcion' => $idopcion,
            'combo_jefes' => $combo_jefes,
            'combo_periodos' => $combo_periodos,
            'periodos_raw' => $periodos,
            'funcion' => $funcion,
        ]);
    }

    public function actionAprobarComisionAdministrativo($idopcion, Request $request)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl <> 'true') {
            return $validarurl;
        }
        /******************************************************/

        $id_periodo_ini = $request->input('id_periodo_ini', '');
        $id_periodo_fin = $request->input('id_periodo_fin', '');

        // Por defecto periodos del año actual
        if ($id_periodo_ini == '' && $id_periodo_fin == '') {
            $anio_actual = date('Y');
            $periodos_anio = DB::table('CON.PERIODO')
                ->where('COD_EMPR', 'IACHEM0000007086')
                ->where('TXT_CODIGO', 'like', $anio_actual . '%')
                ->orderBy('COD_PERIODO', 'asc')
                ->pluck('COD_PERIODO');
            
            if($periodos_anio->count() > 0){
                $id_periodo_ini = $periodos_anio->first();
                $id_periodo_fin = $periodos_anio->last();
            }
        }

        $periodos = DB::select("select COD_PERIODO, TXT_CODIGO, TXT_NOMBRE from CON.PERIODO where COD_EMPR = 'IACHEM0000007086' ORDER BY COD_PERIODO DESC");
        $combo_periodos = ['' => 'Seleccione Periodo'] + collect($periodos)->pluck('TXT_CODIGO', 'COD_PERIODO')->toArray();

        $query = DB::table('WEB.planillacomisiones as P')
            ->leftJoin(DB::raw('(SELECT COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE, SUM(TOTAL_COMISION) as SUM_COMISION 
                                 FROM WEB_COMISION_MERCADO_MAYORISTA 
                                 GROUP BY COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE) as C'), function($join) {
                $join->on('P.COD_PERIODO', '=', 'C.COD_PERIODO')
                     ->on('P.COD_CATEGORIA_JEFE_VENTA', '=', 'C.COD_CATEGORIA_JEFE_VENTA')
                     ->on('P.TXT_PROVIENE', '=', 'C.TXT_PROVIENE');
            })
            ->leftJoin(DB::raw('(SELECT COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE, SUM(TOTAL_COMISION) as SUM_COMISION_JEFE 
                                 FROM WEB_COMISION_MERCADO_MAYORISTA_JEFE 
                                 GROUP BY COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE) as CJ'), function($join) {
                $join->on('P.COD_PERIODO', '=', 'CJ.COD_PERIODO')
                     ->on('P.COD_CATEGORIA_JEFE_VENTA', '=', 'CJ.COD_CATEGORIA_JEFE_VENTA')
                     ->on('P.TXT_PROVIENE', '=', 'CJ.TXT_PROVIENE');
            })
            ->leftJoin(DB::raw('(SELECT COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE, SUM(CAN_SALDO) as SUM_COMISION_AUTO 
                                 FROM WEB_COMISION_AUTOSERVIRCIO_CABECERA 
                                 GROUP BY COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE) as CA'), function($join) {
                $join->on('P.COD_PERIODO', '=', 'CA.COD_PERIODO')
                     ->on('P.COD_CATEGORIA_JEFE_VENTA', '=', 'CA.COD_CATEGORIA_JEFE_VENTA')
                     ->on('P.TXT_PROVIENE', '=', 'CA.TXT_PROVIENE');
            })
            ->leftJoin(DB::raw("(SELECT COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE, SUM(TOTAL_COMISION) as SUM_COMISION_COBRO 
                                 FROM WEB_COMISION_COBRO_CABECERA 
                                 WHERE PRODUCTO = 'CANCELADO'
                                 GROUP BY COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE) as CC"), function($join) {
                $join->on('P.COD_PERIODO', '=', 'CC.COD_PERIODO')
                     ->on('P.COD_CATEGORIA_JEFE_VENTA', '=', 'CC.COD_CATEGORIA_JEFE_VENTA')
                     ->on('P.TXT_PROVIENE', '=', 'CC.TXT_PROVIENE');
            })
            ->leftJoin(DB::raw('(SELECT COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE, SUM(TOTAL_COMISION) as SUM_COMISION_PACAS 
                                 FROM WEB_COMISION_PACAS 
                                 GROUP BY COD_PERIODO, COD_CATEGORIA_JEFE_VENTA, TXT_PROVIENE) as CP'), function($join) {
                $join->on('P.COD_PERIODO', '=', 'CP.COD_PERIODO')
                     ->on('P.COD_CATEGORIA_JEFE_VENTA', '=', 'CP.COD_CATEGORIA_JEFE_VENTA')
                     ->on('P.TXT_PROVIENE', '=', 'CP.TXT_PROVIENE');
            })
            ->select('P.*', 
                     DB::raw('COALESCE(CJ.SUM_COMISION_JEFE, CA.SUM_COMISION_AUTO, CC.SUM_COMISION_COBRO, CP.SUM_COMISION_PACAS, C.SUM_COMISION) as TOTAL_COMISION'),
                     DB::raw('CASE WHEN CJ.SUM_COMISION_JEFE IS NOT NULL THEN 1 ELSE 0 END as ES_JEFE'));

        if ($id_periodo_ini != '' && $id_periodo_fin != '') {
            // Obtener todos los periodos que están entre el inicio y el fin (alfabéticamente/por ID)
            $rango_periodos = DB::table('CON.PERIODO')
                ->where('COD_EMPR', 'IACHEM0000007086')
                ->whereBetween('COD_PERIODO', [min($id_periodo_ini, $id_periodo_fin), max($id_periodo_ini, $id_periodo_fin)])
                ->pluck('COD_PERIODO');
                
            $query->whereIn('P.COD_PERIODO', $rango_periodos);
        } else {
            if ($id_periodo_ini != '') {
                $query->where('P.COD_PERIODO', $id_periodo_ini);
            }
            if ($id_periodo_fin != '') {
                $query->where('P.COD_PERIODO', $id_periodo_fin);
            }
        }
        // Nota: El orden de >= y <= depende de como esten construidos los IDs. 
        // Si el usuario quiere un rango, lo ideal seria buscar por fecha.
        // Pero usaremos el COD_PERIODO si es lo que solicitó.

        $lista_planillas = $query->orderBy('TXT_CODIGO', 'desc')
            ->orderBy('TXT_PROVIENE', 'asc')
            ->get();

        $planillas_agrupadas = $lista_planillas->groupBy('TXT_CODIGO');

        return View::make('comision/aprobarcomisionadministrativo', [
            'idopcion'            => $idopcion,
            'planillas_agrupadas' => $planillas_agrupadas,
            'combo_periodos'      => $combo_periodos,
            'id_periodo_ini'      => $id_periodo_ini,
            'id_periodo_fin'      => $id_periodo_fin,
        ]);
    }

    public function actionAjaxGuardarFechasPeriodo(Request $request)
    {
        $cod_periodo = $request->input('cod_periodo');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        try {
            DB::table('CON.PERIODO')
                ->where('COD_PERIODO', $cod_periodo)
                ->where('COD_EMPR', 'IACHEM0000007086')
                ->update([
                    'FECHA_INCIO_COMISION' => $fecha_inicio,
                    'FECHA_FIN_COMISION' => $fecha_fin
                ]);

            return response()->json(['error' => false, 'msj' => 'Fechas de comisión guardadas correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'msj' => $e->getMessage()]);
        }
    }

    public function actionAjaxListarComisionesVendedor(Request $request)
    {
        $cod_jefe = $request->input('cod_jefe');
        $cod_periodo = $request->input('cod_periodo');
        $fecha_ini = $request->input('fecha_ini');
        $fecha_fin = $request->input('fecha_fin');

        try {
            $lista_comisiones = DB::select("SET NOCOUNT ON; EXEC WEB_COMISION_VENDEDORES_GENERAL 
                @COD_EMPR = '', 
                @ind_formal_interno = 'I', 
                @RESPONSABLE = ?, 
                @FEC_INI = ?, 
                @FEC_FIN = ?, 
                @COD_PERIODO = ?", [
                $cod_jefe,
                $fecha_ini,
                $fecha_fin,
                $cod_periodo
            ]);

            Session::put('lista_comisiones_export', $lista_comisiones);

            // Info de planilla (si ya fue procesada/aplicada)
            $info_planilla = DB::select("
                SELECT TXT_CODIGO, TXT_CATEGORIA_JEFE_VENTA, TXT_ESTADO, TXT_PROVIENE,
                       TXT_USUARIO_AUTORIZA, TXT_USUARIO_EJECUTA, COD_ESTADO
                FROM WEB.planillacomisiones
                WHERE COD_CATEGORIA_JEFE_VENTA = ?
                  AND COD_PERIODO = ?
                  AND TXT_PROVIENE = 'MERCADO MAYORISTA'
            ", [$cod_jefe, $cod_periodo]);

            return View::make('comision/ajax/listacomisionesvendedor', [
                'lista_comisiones' => $lista_comisiones,
                'info_planilla' => $info_planilla
            ]);
        } catch (\Exception $e) {
            return "Error al ejecutar el procedimiento: " . $e->getMessage();
        }
    }

    public function actionAjaxAplicarComisionesVendedor(Request $request)
    {
        $cod_jefe = $request->input('cod_jefe');
        $cod_periodo = $request->input('cod_periodo');
        $txt_proviene = 'MERCADO MAYORISTA';

        if (!Session::has('lista_comisiones_export')) {
            return response()->json(['status' => 'error', 'message' => 'No hay datos en sesión para guardar. Realice la búsqueda nuevamente.']);
        }

        $lista_comisiones = Session::get('lista_comisiones_export');

        try {
            DB::beginTransaction();

            // Consultar datos del jefe y periodo para la cabecera
            $jefe_info = DB::select("SELECT TOP 1 nom_jefe_venta FROM WEB_CONFIGURACION_VENDEDOR WHERE cod_jefe_venta = ?", [$cod_jefe]);
            $periodo_info = DB::select("SELECT TOP 1 TXT_CODIGO, FECHA_INCIO_COMISION, FECHA_FIN_COMISION FROM CON.PERIODO WHERE COD_PERIODO = ?", [$cod_periodo]);

            if (empty($jefe_info) || empty($periodo_info)) {
                return response()->json(['status' => 'error', 'message' => 'No se encontró información del jefe o periodo.']);
            }

            // Eliminar registros previos si existen (Limpieza antes de aplicar)
            DB::table('WEB.planillacomisiones')
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('COD_PERIODO', $cod_periodo)
                ->where('TXT_PROVIENE', $txt_proviene)
                ->delete();

            DB::table('WEB.detalleplanillacomisionesnuevo')
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('COD_PERIODO', $cod_periodo)
                ->where('TXT_PROVIENE', $txt_proviene)
                ->delete();

            // Insertar Cabecera (Removidos campos de auditoría que no existen en esta tabla según el error)
            DB::table('WEB.planillacomisiones')->insert([
                'COD_PERIODO' => $cod_periodo,
                'TXT_CODIGO' => $periodo_info[0]->TXT_CODIGO,
                'FEC_INICIO' => !empty($periodo_info[0]->FECHA_INCIO_COMISION) ? date('Ymd', strtotime($periodo_info[0]->FECHA_INCIO_COMISION)) : null,
                'FEC_FIN' => !empty($periodo_info[0]->FECHA_FIN_COMISION) ? date('Ymd', strtotime($periodo_info[0]->FECHA_FIN_COMISION)) : null,
                'COD_CATEGORIA_JEFE_VENTA' => $cod_jefe,
                'TXT_CATEGORIA_JEFE_VENTA' => $jefe_info[0]->nom_jefe_venta,
                'COD_ESTADO' => 'EPP0000000000002',
                'TXT_ESTADO' => 'GENERADO',
                'TXT_PROVIENE' => $txt_proviene,
            ]);

            // Insertar Detalle
            foreach ($lista_comisiones as $item) {
                DB::table('WEB.detalleplanillacomisionesnuevo')->insert([
                    'COD_EMPR' => $item->COD_EMPR,
                    'NOM_EMPR' => $item->NOM_EMPR,
                    'COD_ORDEN' => $item->COD_ORDEN,
                    'COD_CLIENTE' => $item->COD_CLIENTE,
                    'CLIENTE' => $item->CLIENTE,
                    'COD_DOCUMENTO_CTBLE' => $item->COD_DOCUMENTO_CTBLE,
                    'COD_PRODUCTO' => $item->COD_PRODUCTO,
                    'PRODUCTO' => $item->PRODUCTO,
                    'MARCA_COD_CATEGORIA' => $item->MARCA_COD_CATEGORIA,
                    'MARCA_NOM_CATEGORIA' => $item->MARCA_NOM_CATEGORIA,
                    'CAT_SUP_COD_CATEGORIA' => $item->CAT_SUP_COD_CATEGORIA,
                    'CAT_SUP_NOM_CATEGORIA' => $item->CAT_SUP_NOM_CATEGORIA,
                    'CAT_INF_COD_CATEGORIA' => $item->CAT_INF_COD_CATEGORIA,
                    'CAT_INF_NOM_CATEGORIA' => $item->CAT_INF_NOM_CATEGORIA,
                    'CAT_UNI_COD_CATEGORIA' => $item->CAT_UNI_COD_CATEGORIA,
                    'CAT_UNI_NOM_CATEGORIA' => $item->CAT_UNI_NOM_CATEGORIA,
                    'COD_CATEGORIA_JEFE_VENTA' => $item->COD_CATEGORIA_JEFE_VENTA,
                    'TXT_CATEGORIA_JEFE_VENTA' => $item->TXT_CATEGORIA_JEFE_VENTA,
                    'COD_CATEGORIA_CANAL_VENTA' => $item->COD_CATEGORIA_CANAL_VENTA,
                    'TXT_CATEGORIA_CANAL_VENTA' => $item->TXT_CATEGORIA_CANAL_VENTA,
                    'COD_CATEGORIA_SUB_CANAL' => $item->COD_CATEGORIA_SUB_CANAL,
                    'TXT_CATEGORIA_SUB_CANAL' => $item->TXT_CATEGORIA_SUB_CANAL,
                    'CAN_PRODUCTO' => (int) $item->CAN_PRODUCTO,
                    'CAN_PRECIO_UNIT' => (float) $item->CAN_PRECIO_UNIT,
                    'SUB_TOTAL_P' => (float) $item->SUB_TOTAL_P,
                    'IMPUESTO_P' => (float) $item->IMPUESTO_P,
                    'TOTAL_P' => (float) $item->TOTAL_P,
                    'CAN_TOTAL_DET_SOL' => (float) $item->CAN_TOTAL_DET_SOL,
                    'FEC_ORDEN' => !empty($item->FEC_ORDEN) ? date('Ymd', strtotime($item->FEC_ORDEN)) : null,
                    'FEC_EMISION' => !empty($item->FEC_EMISION) ? date('Ymd', strtotime($item->FEC_EMISION)) : null,
                    'FEC_HABILITACION' => !empty($item->FEC_HABILITACION) ? date('Ymd', strtotime($item->FEC_HABILITACION)) : null,
                    'SALDO_AM' => (float) $item->SALDO_AM,
                    'CAPITAL' => (float) $item->CAPITAL,
                    'INTERES' => (float) $item->INTERES,
                    'CAN_CAPITAL_SALDO' => (float) $item->CAN_CAPITAL_SALDO,
                    'CAN_SALDO' => (float) $item->CAN_SALDO,
                    'DIFF' => (float) $item->DIFF,
                    'TASA_COMISION' => (float) $item->TASA_COMISION,
                    'PLAZO_PAGO' => (int) $item->PLAZO_PAGO,
                    'TOTAL_COBRO' => (float) $item->TOTAL_COBRO,
                    'MONTO_COMISION' => (float) $item->MONTO_COMISION,
                    'PESO_ORDEN_50' => (float) $item->PESO_ORDEN_50,
                    'VAL' => $item->VAL,
                    'COD_PERIODO' => $cod_periodo,
                    'TXT_PROVIENE' => $txt_proviene,
                    'TIPO_COMPROBANTE' => 'ORDEN_VENTA',
                    'FEC_CREACION' => date('Ymd H:i:s'),
                    'USUARIO_CREACION' => Session::get('usuario')->name,
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Comisiones aplicadas correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error al aplicar comisiones: ' . $e->getMessage()]);
        }
    }

    public function actionExportarExcelAplicarComision(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        // Usar datos de sesión si existen, si no re-ejecutar el SP
        if (Session::has('lista_comisiones_export')) {
            $lista_comisiones = Session::get('lista_comisiones_export');
        } else {
            $cod_jefe = $request->input('cod_jefe');
            $cod_periodo = $request->input('cod_periodo');
            $fecha_ini = $request->input('fecha_ini');
            $fecha_fin = $request->input('fecha_fin');

            $lista_comisiones = DB::select("SET NOCOUNT ON; EXEC WEB_COMISION_VENDEDORES_GENERAL 
                @COD_EMPR = '', 
                @ind_formal_interno = 'I', 
                @RESPONSABLE = ?, 
                @FEC_INI = ?, 
                @FEC_FIN = ?, 
                @COD_PERIODO = ?", [
                $cod_jefe,
                $fecha_ini,
                $fecha_fin,
                $cod_periodo
            ]);
        }

        // Obtener nombre del Jefe y del Periodo para la cabecera
        $cod_jefe_param = $request->input('cod_jefe', '');
        $cod_periodo_param = $request->input('cod_periodo', '');
        $inicio = $request->input('fecha_ini', '');
        $fin = $request->input('fecha_fin', '');

        $jefe_row = DB::select("SELECT TOP 1 nom_jefe_venta FROM WEB_CONFIGURACION_VENDEDOR WHERE cod_jefe_venta = ?", [$cod_jefe_param]);
        $periodo_row = DB::select("SELECT TOP 1 TXT_CODIGO FROM CON.PERIODO WHERE COD_PERIODO = ?", [$cod_periodo_param]);

        $jefe = $jefe_row ? (string) $jefe_row[0]->nom_jefe_venta : $cod_jefe_param;
        $periodo = $periodo_row ? (string) $periodo_row[0]->TXT_CODIGO : $cod_periodo_param;

        $titulo = "Comisiones_" . date('YmdHis');

        try {
            return Excel::create($titulo, function ($excel) use ($lista_comisiones, $jefe, $periodo, $inicio, $fin) {
                $excel->sheet('Reporte', function ($sheet) use ($lista_comisiones, $jefe, $periodo, $inicio, $fin) {

                    $sheet->setColumnFormat(['A' => '@', 'B' => '@', 'C' => '@']);

                    // --- CABECERA INFORMATIVA ---
                    $sheet->row(1, ['REPORTE DE APLICACIÓN DE COMISIONES']);
                    $sheet->row(2, ['Jefe de Ventas:', $jefe]);
                    $sheet->row(3, ['Periodo:', $periodo]);
                    $sheet->row(4, ['Rango de Fechas:', $inicio . ' al ' . $fin]);
                    $sheet->row(5, ['Fecha de Exportación:', date('d-m-Y H:i:s')]);
                    $sheet->row(6, []);

                    // Estilo cabecera
                    $sheet->cells('A1:B1', function ($cells) {
                        $cells->setFontWeight('bold')->setFontSize(14);
                    });
                    $sheet->cells('A2:A5', function ($cells) {
                        $cells->setFontWeight('bold');
                    });

                    // --- RESUMEN PIVOT ---
                    $subfamilias = [];
                    $resumen = [];
                    $totales_v = [];
                    $t_peso = 0;
                    $t_com = 0;

                    foreach ($lista_comisiones as $item) {
                        $subfam = (string) $item->CAT_INF_NOM_CATEGORIA;
                        if (!in_array($subfam, $subfamilias)) {
                            $subfamilias[] = $subfam;
                        }
                        $key = $item->NOM_EMPR . '|' . $item->TXT_CATEGORIA_CANAL_VENTA . '|' . $item->TXT_CATEGORIA_SUB_CANAL;
                        if (!isset($resumen[$key])) {
                            $resumen[$key] = ['empresa' => (string) $item->NOM_EMPR, 'canal' => (string) $item->TXT_CATEGORIA_CANAL_VENTA, 'subcanal' => (string) $item->TXT_CATEGORIA_SUB_CANAL, 'data' => [], 'tp' => 0, 'tc' => 0];
                        }
                        if (!isset($resumen[$key]['data'][$subfam])) {
                            $resumen[$key]['data'][$subfam] = ['peso' => 0, 'com' => 0];
                        }
                        $pv = is_numeric($item->PESO_ORDEN_50) ? (float) $item->PESO_ORDEN_50 : 0;
                        $cv = is_numeric($item->MONTO_COMISION) ? (float) $item->MONTO_COMISION : 0;
                        $resumen[$key]['data'][$subfam]['peso'] += $pv;
                        $resumen[$key]['data'][$subfam]['com'] += $cv;
                        $resumen[$key]['tp'] += $pv;
                        $resumen[$key]['tc'] += $cv;
                        if (!isset($totales_v[$subfam]))
                            $totales_v[$subfam] = ['peso' => 0, 'com' => 0];
                        $totales_v[$subfam]['peso'] += $pv;
                        $totales_v[$subfam]['com'] += $cv;
                        $t_peso += $pv;
                        $t_com += $cv;
                    }
                    sort($subfamilias);

                    // Cabecera resumen — FILA 1: nombre de subfamilia (mergeada sobre 2 cols)
                    $row = 7;
                    // Fila 1 (super-header): EMPRESA, CANAL, SUBCANAL + subfamilias (cada una ocupa 2 cols) + Total (2 cols)
                    $superRow = ['EMPRESA', 'CANAL', 'SUBCANAL'];
                    foreach ($subfamilias as $s) {
                        $superRow[] = $s;  // col par: nombre subfamilia
                        $superRow[] = ''; // col impar: vacía (se mergeará)
                    }
                    $superRow[] = 'Total';
                    $superRow[] = '';
                    $sheet->row($row, $superRow);
                    $sheet->cells('A' . $row . ':' . \PHPExcel_Cell::stringFromColumnIndex(count($superRow) - 1) . $row, function ($cells) {
                        $cells->setFontWeight('bold')->setBackground('#2d6a4f')->setFontColor('#FFFFFF')->setAlignment('center');
                    });
                    // Mergear celdas de subfamilia (de 2 en 2) en la fila super-header
                    $colIdx = 3; // columna D (índice 3)
                    foreach ($subfamilias as $s) {
                        $colStart = \PHPExcel_Cell::stringFromColumnIndex($colIdx);
                        $colEnd = \PHPExcel_Cell::stringFromColumnIndex($colIdx + 1);
                        $sheet->mergeCells($colStart . $row . ':' . $colEnd . $row);
                        $colIdx += 2;
                    }
                    // Mergear Total
                    $colStart = \PHPExcel_Cell::stringFromColumnIndex($colIdx);
                    $colEnd = \PHPExcel_Cell::stringFromColumnIndex($colIdx + 1);
                    $sheet->mergeCells($colStart . $row . ':' . $colEnd . $row);
                    $row++;

                    // Fila 2 (sub-header): Σ Saco 50kg y Σ Comisión por cada subfamilia
                    $subRow = ['', '', ''];
                    foreach ($subfamilias as $s) {
                        $subRow[] = 'Σ Saco 50kg';
                        $subRow[] = 'Σ Comisión';
                    }
                    $subRow[] = 'Σ Saco 50kg';
                    $subRow[] = 'Σ Comisión';
                    $sheet->row($row, $subRow);
                    $sheet->cells('A' . $row . ':' . \PHPExcel_Cell::stringFromColumnIndex(count($subRow) - 1) . $row, function ($cells) {
                        $cells->setFontWeight('bold')->setBackground('#52b788')->setFontColor('#FFFFFF')->setAlignment('center');
                    });
                    $row++;

                    foreach ($resumen as $r) {
                        $dataRow = [(string) $r['empresa'], (string) $r['canal'], (string) $r['subcanal']];
                        foreach ($subfamilias as $s) {
                            $dataRow[] = isset($r['data'][$s]) ? round($r['data'][$s]['peso'], 2) : 0;
                            $dataRow[] = isset($r['data'][$s]) ? round($r['data'][$s]['com'], 2) : 0;
                        }
                        $dataRow[] = round($r['tp'], 2);
                        $dataRow[] = round($r['tc'], 2);
                        $sheet->row($row, $dataRow);
                        $row++;
                    }

                    // Fila totales resumen
                    $totRow = ['', '', 'TOTALES'];
                    foreach ($subfamilias as $s) {
                        $totRow[] = round($totales_v[$s]['peso'], 2);
                        $totRow[] = round($totales_v[$s]['com'], 2);
                    }
                    $totRow[] = round($t_peso, 2);
                    $totRow[] = round($t_com, 2);
                    $sheet->row($row, $totRow);
                    $sheet->cells('A' . $row . ':' . \PHPExcel_Cell::stringFromColumnIndex(count($totRow) - 1) . $row, function ($cells) {
                        $cells->setFontWeight('bold')->setBackground('#cbd5e1');
                    });
                    $row += 2;

                    // --- DETALLE ---
                    $detailHeader = ['FECHA VENTA', 'ORDEN', 'DOCUMENTO', 'CLIENTE', 'PRODUCTO', 'FAMILIA', 'SUBFAMILIA', 'UNIDAD', 'MARCA', 'CANTIDAD', 'P.U.', 'TOTAL P', 'PESO 50kg', 'CANAL', 'SUBCANAL', 'FECHA PAGO', 'PRODUCTO COBRADO', 'DIFF', 'CONDICCION PAGO', 'EVALUACIÓN', 'JEFE VENTA', 'COMISION', 'TOTAL COMISION'];
                    $sheet->row($row, $detailHeader);
                    $lastCol = \PHPExcel_Cell::stringFromColumnIndex(count($detailHeader) - 1);
                    $sheet->cells('A' . $row . ':' . $lastCol . $row, function ($cells) {
                        $cells->setFontWeight('bold')->setBackground('#4f46e5')->setFontColor('#FFFFFF');
                    });
                    $row++;

                    foreach ($lista_comisiones as $item) {
                        $sheet->row($row, [
                            (string) $item->FEC_ORDEN,
                            (string) $item->COD_ORDEN,
                            (string) $item->COD_DOCUMENTO_CTBLE,
                            (string) $item->CLIENTE,
                            (string) $item->PRODUCTO,
                            (string) $item->CAT_SUP_NOM_CATEGORIA,
                            (string) $item->CAT_INF_NOM_CATEGORIA,
                            (string) $item->CAT_UNI_NOM_CATEGORIA,
                            (string) $item->MARCA_NOM_CATEGORIA,
                            is_numeric($item->CAN_PRODUCTO) ? round((float) $item->CAN_PRODUCTO, 2) : 0,
                            is_numeric($item->CAN_PRECIO_UNIT) ? round((float) $item->CAN_PRECIO_UNIT, 2) : 0,
                            is_numeric($item->TOTAL_P) ? round((float) $item->TOTAL_P, 2) : 0,
                            is_numeric($item->PESO_ORDEN_50) ? round((float) $item->PESO_ORDEN_50, 2) : 0,
                            (string) $item->TXT_CATEGORIA_CANAL_VENTA,
                            (string) $item->TXT_CATEGORIA_SUB_CANAL,
                            (string) $item->FEC_HABILITACION,
                            is_numeric($item->TOTAL_COBRO) ? round((float) $item->TOTAL_COBRO, 2) : 0,
                            (string) $item->DIFF,
                            (string) $item->PLAZO_PAGO,
                            (string) $item->VAL,
                            (string) $item->TXT_CATEGORIA_JEFE_VENTA,
                            is_numeric($item->TASA_COMISION) ? round((float) $item->TASA_COMISION, 2) : 0,
                            is_numeric($item->MONTO_COMISION) ? round((float) $item->MONTO_COMISION, 2) : 0,
                        ]);
                        $row++;
                    }

                    $sheet->setAutoSize(true);
                });
            })->export('xls');
        } catch (\Exception $e) {
            return "Error al exportar Excel: " . $e->getMessage() . " en línea " . $e->getLine();
        }
    }

    public function actionAjaxAprobarComisionesMasivo(Request $request)
    {
        $comisiones = $request->input('comisiones', []);
        $usuario_id = Session::get('usuario')->id;
        $usuario_nom = Session::get('usuario')->nombre;

        try {
            DB::beginTransaction();

            foreach ($comisiones as $com) {
                DB::table('WEB.planillacomisiones')
                    ->where('COD_PERIODO', $com['periodo'])
                    ->where('COD_CATEGORIA_JEFE_VENTA', $com['jefe'])
                    ->where('TXT_PROVIENE', $com['proviene'])
                    ->update([
                        'COD_ESTADO' => 'EPP0000000000003',
                        'TXT_ESTADO' => 'AUTORIZADO',
                        'COD_USUARIO_AUTORIZA' => $usuario_id,
                        'TXT_USUARIO_AUTORIZA' => $usuario_nom
                    ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => count($comisiones) . ' comisiones autorizadas correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function actionExportarExcelComisionAdministrativo(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $cod_jefe = $request->input('cod_jefe');
        $cod_periodo = $request->input('cod_periodo');
        $periodos_array = explode(',', $cod_periodo);
        $proviene = $request->input('proviene', 'MERCADO MAYORISTA');

        $es_jefe_flag = false;
        $lista_cabecera = collect();
        $lista_detalle = collect();

        if (trim($proviene) == 'AUTOSERVICIOS') {
            $lista_cabecera = DB::table('WEB_COMISION_AUTOSERVIRCIO_CABECERA')
                ->whereIn('COD_PERIODO', $periodos_array)
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('TXT_PROVIENE', $proviene)
                ->orderBy('CAN_CAPITAL_SALDO', 'asc')
                ->get();

            $lista_detalle = DB::table('WEB_COMISION_AUTOSERVIRCIO_DETALLE')
                ->whereIn('COD_PERIODO', $periodos_array)
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('TXT_PROVIENE', $proviene)
                ->get();
            
            $lista_comisiones = $lista_cabecera;
        } elseif (trim($proviene) == 'COBRO AUTOSERVICIO') {
            $lista_cabecera = DB::table('WEB_COMISION_COBRO_CABECERA')
                ->whereIn('COD_PERIODO', $periodos_array)
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('TXT_PROVIENE', $proviene)
                ->get();

            $lista_detalle = DB::table('WEB_COMISION_COBRO_DETALLE')
                ->whereIn('COD_PERIODO', $periodos_array)
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('TXT_PROVIENE', $proviene)
                ->get();
            
            $lista_comisiones = $lista_cabecera;
        } elseif (trim($proviene) == 'PACAS') {
            $lista_comisiones = DB::table('WEB_COMISION_PACAS')
                ->whereIn('COD_PERIODO', $periodos_array)
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('TXT_PROVIENE', $proviene)
                ->get();
        } else {
            $es_jefe_flag = DB::table('WEB_COMISION_MERCADO_MAYORISTA_JEFE')
                ->whereIn('COD_PERIODO', $periodos_array)
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('TXT_PROVIENE', $proviene)
                ->exists();

            if ($es_jefe_flag) {
                $lista_comisiones = DB::table('WEB_COMISION_MERCADO_MAYORISTA_JEFE')
                    ->select(
                        'TXT_CATEGORIA_JEFE_VENTA',
                        'TXT_CATEGORIA_JEFE_VENTA_ASIMILADO',
                        'CAT_INF_NOM_CATEGORIA',
                        DB::raw('SUM(PESO_ORDEN_50) as PESO_ORDEN_50'),
                        DB::raw('MAX(TASA_COMISION) as TASA_COMISION'),
                        DB::raw('SUM(TOTAL_COMISION) as TOTAL_COMISION')
                    )
                    ->whereIn('COD_PERIODO', $periodos_array)
                    ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                    ->where('TXT_PROVIENE', $proviene)
                    ->groupBy('TXT_CATEGORIA_JEFE_VENTA', 'TXT_CATEGORIA_JEFE_VENTA_ASIMILADO', 'CAT_INF_NOM_CATEGORIA')
                    ->orderBy('TXT_CATEGORIA_JEFE_VENTA_ASIMILADO', 'asc')
                    ->get();
            } else {
                $lista_comisiones = DB::table('WEB_COMISION_MERCADO_MAYORISTA')
                    ->whereIn('COD_PERIODO', $periodos_array)
                    ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                    ->where('TXT_PROVIENE', $proviene)
                    ->orderBy('TIPO_COMPROBANTE', 'asc')
                    ->get();
            }
        }

        $jefe_row = DB::select("SELECT TOP 1 nom_jefe_venta FROM WEB_CONFIGURACION_VENDEDOR WHERE cod_jefe_venta = ?", [$cod_jefe]);
        
        $periodos_nombres = DB::table('CON.PERIODO')
            ->whereIn('COD_PERIODO', $periodos_array)
            ->pluck('TXT_CODIGO')
            ->toArray();
        $periodo = implode(', ', $periodos_nombres);

        $jefe = $jefe_row ? (string) $jefe_row[0]->nom_jefe_venta : $cod_jefe;

        // Intentar obtener el nombre del jefe desde la data (cabecera o detalle)
        $registro_con_nombre = null;
        if ($lista_comisiones->count() > 0 && isset($lista_comisiones->first()->TXT_CATEGORIA_JEFE_VENTA)) {
            $registro_con_nombre = $lista_comisiones->first();
        } elseif ($lista_detalle->count() > 0 && isset($lista_detalle->first()->TXT_CATEGORIA_JEFE_VENTA)) {
            $registro_con_nombre = $lista_detalle->first();
        }

        if ($registro_con_nombre && !empty($registro_con_nombre->TXT_CATEGORIA_JEFE_VENTA)) {
            $jefe = (string) $registro_con_nombre->TXT_CATEGORIA_JEFE_VENTA;
        }

        if($es_jefe_flag) {
            $jefe .= ' (JEFE)';
        }

        $titulo = "Comisiones_Administrativo_" . date('YmdHis');

        try {
            return Excel::create($titulo, function ($excel) use ($lista_comisiones, $lista_cabecera, $lista_detalle, $jefe, $periodo, $proviene, $es_jefe_flag) {
                
                if (trim($proviene) == 'AUTOSERVICIOS') {
                    $excel->sheet('Reporte Autoservicios', function ($sheet) use ($lista_cabecera, $lista_detalle, $jefe, $periodo, $proviene) {
                        
                        $sheet->row(1, ['REPORTE DE COMISIONES - ' . $proviene]);
                        $sheet->row(2, ['Jefe de Ventas:', $jefe]);
                        $sheet->row(3, ['Periodo:', $periodo]);
                        $sheet->row(4, []);

                        $sheet->cells('A1:B1', function ($cells) {
                            $cells->setFontWeight('bold')->setFontSize(14);
                        });

                        $fila = 6;
                        $headerResumen = ['VENDEDOR', 'DESCRIPCION', 'COMISION', 'TOTAL'];
                        $sheet->row($fila, $headerResumen);
                        $sheet->cells('A' . $fila . ':D' . $fila, function ($cells) {
                            $cells->setFontWeight('bold')->setBackground('#27ae60')->setFontColor('#FFFFFF')->setAlignment('center');
                        });
                        $fila++;

                        $sum_can_saldo = 0;
                        $top_comision = 0;
                        $top_cobro = 0;
                        $top_porcentaje = 0;

                        if($lista_cabecera->count() > 0){
                            $primer = $lista_cabecera->first();
                            $top_comision = $primer->TOTAL_COMISION;
                            $top_cobro = $primer->TOTAL_COBRO;
                            $top_porcentaje = $primer->COMISION;
                        }

                        foreach ($lista_cabecera as $item) {
                            $sheet->row($fila, [
                                $item->TXT_CATEGORIA_JEFE_VENTA,
                                $item->TIPO_PAGO,
                                round($item->CAN_CAPITAL_SALDO, 2),
                                round($item->CAN_SALDO, 2)
                            ]);
                            $sum_can_saldo += (float) $item->CAN_SALDO;
                            $fila++;
                        }

                        $sheet->row($fila, ['CUOTA AL 100%', '', '', round($top_comision, 2)]); $fila++;
                        $sheet->row($fila, ['TOTAL VENTAS', '', '', round($top_cobro, 2)]); $fila++;
                        $sheet->row($fila, ['PORCENTAJE DE VENTAS REFERENTE A COMISON', '', '', round($top_porcentaje, 4)]); $fila++;
                        $sheet->row($fila, ['COMISION TOTAL', '', '', round($sum_can_saldo, 2)]);
                        $sheet->cells('A' . ($fila) . ':D' . $fila, function ($cells) {
                            $cells->setFontWeight('bold');
                        });
                        $sheet->getStyle('D' . ($fila))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $fila += 3;

                        $headerDetalle = [
                            'CENTRO', 'FECHA', 'NRO. ORDEN', 'TIPO', 'TIPO PAGO', 'ESTADO ORDEN', 'JEFE', 'CANAL', 'SUB CANAL', 
                            'REG COMERCIAL', 'CLIENTE', 'DIV', 'MONEDA', 'T.C.', 'SUB FAMILIA', 'UNIDAD DE MEDIDA', 'CANTIDAD', 
                            'PESO', 'CANTIDAD EN 50KG', 'PRECIO', 'TOTAL'
                        ];
                        $sheet->row($fila, $headerDetalle);
                        $lastColDet = \PHPExcel_Cell::stringFromColumnIndex(count($headerDetalle) - 1);
                        $sheet->cells('A' . $fila . ':' . $lastColDet . $fila, function ($cells) {
                            $cells->setFontWeight('bold')->setBackground('#4f46e5')->setFontColor('#FFFFFF')->setAlignment('center');
                        });
                        $fila++;

                        foreach ($lista_detalle as $det) {
                            $sheet->row($fila, [
                                $det->CENTRO,
                                !empty($det->FECHA) ? date('d-m-Y', strtotime($det->FECHA)) : '',
                                $det->ORDEN,
                                $det->TIPO_VENTA,
                                $det->TIPO_PAGO,
                                $det->TXT_CATEGORIA_ESTADO_ORDEN,
                                $det->TXT_CATEGORIA_JEFE_VENTA,
                                $det->TXT_CATEGORIA_CANAL_VENTA,
                                $det->TXT_CATEGORIA_SUB_CANAL,
                                $det->REG_COMERCIAL,
                                $det->CLIENTE,
                                $det->DIV,
                                $det->MONEDA,
                                $det->CAN_TIPO_CAMBIO,
                                $det->NOM_SUBFAMILIA_PRODUCTO,
                                $det->UM_OV,
                                $det->CAN_PRODUCTO,
                                $det->KG_OV,
                                $det->SACOS_50KG,
                                $det->CAN_PRECIO_UNIT_IGV,
                                $det->CAN_TOTAL_OV
                            ]);
                            $fila++;
                        }

                        $sheet->setAutoSize(true);
                        $sheet->setWidth([
                            'A' => 20, 'B' => 20, 'C' => 20, 'D' => 20
                        ]);
                    });

                } else if (trim($proviene) == 'COBRO AUTOSERVICIO') {
                    // --- EXPORTACIÓN PARA COBRO AUTOSERVICIO ---
                    $excel->sheet('Reporte Cobro Autoserv.', function ($sheet) use ($lista_cabecera, $lista_detalle, $jefe, $periodo, $proviene) {
                        
                        // Cabecera informativa
                        $sheet->row(1, ['REPORTE DE COMISIONES - ' . $proviene]);
                        $sheet->row(2, ['Jefe de Ventas:', $jefe]);
                        $sheet->row(3, ['Periodo:', $periodo]);
                        $sheet->row(4, []);

                        $sheet->cells('A1:B1', function ($cells) {
                            $cells->setFontWeight('bold')->setFontSize(14);
                        });

                        // --- TABLA RESUMEN (CABECERA) ---
                        $fila = 6;
                        $headerResumen = ['NOMBRE', 'TOTAL', 'PORCENTAJE', 'PAGAR'];
                        $sheet->row($fila, $headerResumen);
                        $sheet->cells('A' . $fila . ':D' . $fila, function ($cells) {
                            $cells->setFontWeight('bold')->setBackground('#27ae60')->setFontColor('#FFFFFF')->setAlignment('center');
                        });
                        $fila++;

                        foreach ($lista_cabecera as $item) {
                            $sheet->row($fila, [
                                $item->PRODUCTO,
                                round($item->TOTAL_P, 2),
                                round($item->TOTAL_COMISION, 4),
                                round($item->TOTAL_COMISION, 2)
                            ]);
                            $fila++;
                        }
                        $fila += 3;

                        // --- TABLA DETALLE ---
                        $headerDetalle = [
                            'FEC ORDEN', 'COD ORDEN', 'DOCUMENTO', 'CLIENTE', 'PRODUCTO', 'FAMILIA', 'SUN FAMILIA', 
                            'UNIDAD', 'CANTIDAD', 'P.U.', 'TOTAL P', 'PESO 50KG', 'CANAL', 'SUB CANAL', 'FECHA PAGO', 
                            'TOTAL COBRO', 'DIFF', 'VAL', 'JEFE DE VENTA'
                        ];
                        $sheet->row($fila, $headerDetalle);
                        $lastColDet = \PHPExcel_Cell::stringFromColumnIndex(count($headerDetalle) - 1);
                        $sheet->cells('A' . $fila . ':' . $lastColDet . $fila, function ($cells) {
                            $cells->setFontWeight('bold')->setBackground('#4f46e5')->setFontColor('#FFFFFF')->setAlignment('center');
                        });
                        $fila++;

                        foreach ($lista_detalle as $det) {
                            $sheet->row($fila, [
                                !empty($det->FEC_ORDEN) ? date('d-m-Y', strtotime($det->FEC_ORDEN)) : '',
                                $det->COD_ORDEN,
                                $det->COD_DOCUMENTO_CTBLE,
                                $det->CLIENTE,
                                $det->PRODUCTO,
                                $det->CAT_SUP_NOM_CATEGORIA,
                                $det->CAT_INF_NOM_CATEGORIA,
                                $det->CAT_UNI_NOM_CATEGORIA,
                                $det->CAN_PRODUCTO,
                                $det->CAN_PRECIO_UNIT,
                                $det->TOTAL_P,
                                $det->PESO_ORDEN_50,
                                $det->TXT_CATEGORIA_CANAL_VENTA,
                                $det->TXT_CATEGORIA_SUB_CANAL,
                                !empty($det->FEC_FIN) ? date('d-m-Y', strtotime($det->FEC_FIN)) : '',
                                $det->TOTAL_COBRO,
                                $det->DIFF,
                                $det->VAL,
                                $det->TXT_CATEGORIA_JEFE_VENTA
                            ]);
                            $fila++;
                        }

                        $sheet->setAutoSize(true);
                        $sheet->setWidth([
                            'A' => 20, 'B' => 20, 'C' => 20, 'D' => 20
                        ]);
                    });

                } else if ($es_jefe_flag) {
                    $excel->sheet('Resumen Jefe', function ($sheet) use ($lista_comisiones, $jefe, $periodo, $proviene) {
                        
                        $sheet->row(1, ['REPORTE DE COMISIONES - ' . $proviene]);
                        $sheet->row(2, ['Jefe de Ventas:', $jefe]);
                        $sheet->row(3, ['Periodo:', $periodo]);
                        $sheet->row(4, []);

                        $sheet->cells('A1:B1', function ($cells) {
                            $cells->setFontWeight('bold')->setFontSize(14);
                        });
                        $sheet->cells('A2:A3', function ($cells) {
                            $cells->setFontWeight('bold');
                        });

                        $fila = 6;
                        $header = ['VENDEDOR', 'SUBFAMILA', 'PESO 50KG', 'COMISION', 'TOTAL COMISION'];
                        $sheet->row($fila, $header);
                        
                        $sheet->cells('A' . $fila . ':E' . $fila, function ($cells) {
                            $cells->setFontWeight('bold')
                                  ->setBackground('#27ae60')
                                  ->setFontColor('#FFFFFF')
                                  ->setAlignment('center');
                        });
                        
                        $fila++;
                        $total_peso = 0;
                        $total_comision = 0;

                        foreach ($lista_comisiones as $item) {
                            $sheet->row($fila, [
                                $item->TXT_CATEGORIA_JEFE_VENTA_ASIMILADO,
                                $item->CAT_INF_NOM_CATEGORIA,
                                round($item->PESO_ORDEN_50, 2),
                                round($item->TASA_COMISION, 4),
                                round($item->TOTAL_COMISION, 2)
                            ]);
                            
                            $total_peso += (float) $item->PESO_ORDEN_50;
                            $total_comision += (float) $item->TOTAL_COMISION;
                            $fila++;
                        }

                        $sheet->setCellValue('D' . $fila, 'TOTAL:');
                        $sheet->setCellValue('C' . $fila, round($total_peso, 2));
                        $sheet->setCellValue('E' . $fila, round($total_comision, 2));
                        $sheet->getStyle('A'.$fila.':E'.$fila)->getFont()->setBold(true);
                        $sheet->getStyle('C'.$fila.':E'.$fila)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                        $sheet->setAutoSize(true);
                        $sheet->setWidth([
                            'A' => 20, 'B' => 20, 'C' => 20, 'D' => 20
                        ]);
                    });

                } else {
                    $excel->sheet('Reporte', function ($sheet) use ($lista_comisiones, $jefe, $periodo, $proviene) {

                        $sheet->setColumnFormat(['A' => '@', 'B' => '@', 'C' => '@']);

                        $sheet->row(1, ['REPORTE DE COMISIONES - ' . $proviene]);
                        $sheet->row(2, ['Jefe de Ventas:', $jefe]);
                        $sheet->row(3, ['Periodo:', $periodo]);
                        $sheet->row(4, ['Fecha de Exportación:', date('d-m-Y H:i:s')]);
                        $sheet->row(5, []);

                        $sheet->cells('A1:B1', function ($cells) {
                            $cells->setFontWeight('bold')->setFontSize(14);
                        });
                        $sheet->cells('A2:A4', function ($cells) {
                            $cells->setFontWeight('bold');
                        });

                        $subfamilias = [];
                        $resumen = [];
                        $totales_v = [];
                        $t_peso = 0;
                        $t_com = 0;

                        foreach ($lista_comisiones as $item) {
                            $subfam = (string) $item->CAT_INF_NOM_CATEGORIA;
                            if (!in_array($subfam, $subfamilias)) {
                                $subfamilias[] = $subfam;
                            }
                            $key = $item->NOM_EMPR . '|' . $item->TXT_CATEGORIA_CANAL_VENTA . '|' . $item->TXT_CATEGORIA_SUB_CANAL;
                            if (!isset($resumen[$key])) {
                                $resumen[$key] = [
                                    'empresa' => (string) $item->NOM_EMPR, 
                                    'canal' => (string) $item->TXT_CATEGORIA_CANAL_VENTA, 
                                    'subcanal' => (string) $item->TXT_CATEGORIA_SUB_CANAL, 
                                    'data' => [], 
                                    'tp' => 0, 
                                    'tc' => 0
                                ];
                            }
                            if (!isset($resumen[$key]['data'][$subfam])) {
                                $resumen[$key]['data'][$subfam] = ['peso' => 0, 'com' => 0];
                            }
                            $pv = is_numeric($item->PESO_ORDEN_50) ? (float) $item->PESO_ORDEN_50 : 0;
                            $cv = is_numeric($item->TOTAL_COMISION) ? (float) $item->TOTAL_COMISION : 0;
                            
                            $resumen[$key]['data'][$subfam]['peso'] += $pv;
                            $resumen[$key]['data'][$subfam]['com'] += $cv;
                            $resumen[$key]['tp'] += $pv;
                            $resumen[$key]['tc'] += $cv;

                            if (!isset($totales_v[$subfam])) {
                                $totales_v[$subfam] = ['peso' => 0, 'com' => 0];
                            }
                            $totales_v[$subfam]['peso'] += $pv;
                            $totales_v[$subfam]['com'] += $cv;
                            $t_peso += $pv;
                            $t_com += $cv;
                        }
                        sort($subfamilias);

                        $row = 7;
                        $superHeader = ['EMPRESA', 'CANAL', 'SUBCANAL'];
                        foreach ($subfamilias as $s) {
                            $superHeader[] = $s;
                            $superHeader[] = '';
                        }
                        $superHeader[] = 'Total';
                        $superHeader[] = '';
                        $sheet->row($row, $superHeader);
                        
                        $lastColumnIndex = count($superHeader) - 1;
                        $lastColumnLetter = \PHPExcel_Cell::stringFromColumnIndex($lastColumnIndex);
                        
                        $sheet->cells('A' . $row . ':' . $lastColumnLetter . $row, function ($cells) {
                            $cells->setFontWeight('bold')->setBackground('#2d6a4f')->setFontColor('#FFFFFF')->setAlignment('center');
                        });

                        $colIdx = 3; 
                        foreach ($subfamilias as $s) {
                            $colStart = \PHPExcel_Cell::stringFromColumnIndex($colIdx);
                            $colEnd = \PHPExcel_Cell::stringFromColumnIndex($colIdx + 1);
                            $sheet->mergeCells($colStart . $row . ':' . $colEnd . $row);
                            $colIdx += 2;
                        }
                        $colStart = \PHPExcel_Cell::stringFromColumnIndex($colIdx);
                        $colEnd = \PHPExcel_Cell::stringFromColumnIndex($colIdx + 1);
                        $sheet->mergeCells($colStart . $row . ':' . $colEnd . $row);
                        $row++;

                        $subHeader = ['', '', ''];
                        foreach ($subfamilias as $s) {
                            $subHeader[] = 'Σ Saco 50kg';
                            $subHeader[] = 'Σ Comisión';
                        }
                        $subHeader[] = 'Σ Saco 50kg';
                        $subHeader[] = 'Σ Comisión';
                        $sheet->row($row, $subHeader);
                        $sheet->cells('A' . $row . ':' . $lastColumnLetter . $row, function ($cells) {
                            $cells->setFontWeight('bold')->setBackground('#52b788')->setFontColor('#FFFFFF')->setAlignment('center');
                        });
                        $row++;

                        foreach ($resumen as $r) {
                            $dataRow = [(string) $r['empresa'], (string) $r['canal'], (string) $r['subcanal']];
                            foreach ($subfamilias as $s) {
                                $dataRow[] = isset($r['data'][$s]) ? round($r['data'][$s]['peso'], 2) : 0;
                                $dataRow[] = isset($r['data'][$s]) ? round($r['data'][$s]['com'], 2) : 0;
                            }
                            $dataRow[] = round($r['tp'], 2);
                            $dataRow[] = round($r['tc'], 2);
                            $sheet->row($row, $dataRow);
                            $row++;
                        }

                        $totRow = ['', '', 'TOTALES'];
                        foreach ($subfamilias as $s) {
                            $totRow[] = round($totales_v[$s]['peso'], 2);
                            $totRow[] = round($totales_v[$s]['com'], 2);
                        }
                        $totRow[] = round($t_peso, 2);
                        $totRow[] = round($t_com, 2);
                        $sheet->row($row, $totRow);
                        $sheet->cells('A' . $row . ':' . $lastColumnLetter . $row, function ($cells) {
                            $cells->setFontWeight('bold')->setBackground('#cbd5e1');
                        });
                        $row += 2;

                        $header = [
                            'FECHA VENTA', 'ORDEN', 'DOCUMENTO', 'CLIENTE', 'PRODUCTO', 'FAMILIA', 'SUBFAMILIA', 'UNIDAD', 'MARCA', 
                            'CANTIDAD', 'P.U.', 'TOTAL P', 'PESO 50kg', 'CANAL', 'SUBCANAL', 'FECHA PAGO', 'PRODUCTO COBRADO', 
                            'DIFF', 'CONDICCION PAGO', 'EVALUACIÓN', 'JEFE VENTA', 'COMISION', 'TOTAL COMISION'
                        ];

                        $sheet->row($row, $header);
                        $lastColDetail = \PHPExcel_Cell::stringFromColumnIndex(count($header) - 1);
                        $sheet->cells('A' . $row . ':' . $lastColDetail . $row, function ($cells) {
                            $cells->setFontWeight('bold')->setBackground('#4f46e5')->setFontColor('#FFFFFF');
                        });
                        $row++;

                        foreach ($lista_comisiones as $item) {
                            $sheet->row($row, [
                                !empty($item->FEC_ORDEN) ? date('d-m-Y', strtotime($item->FEC_ORDEN)) : '',
                                $item->COD_ORDEN,
                                $item->COD_DOCUMENTO_CTBLE,
                                $item->CLIENTE,
                                $item->PRODUCTO,
                                $item->CAT_SUP_NOM_CATEGORIA,
                                $item->CAT_INF_NOM_CATEGORIA,
                                $item->CAT_UNI_NOM_CATEGORIA,
                                $item->MARCA_NOM_CATEGORIA,
                                $item->CAN_PRODUCTO,
                                $item->CAN_PRECIO_UNIT,
                                $item->TOTAL_P,
                                $item->PESO_ORDEN_50,
                                $item->TXT_CATEGORIA_CANAL_VENTA,
                                $item->TXT_CATEGORIA_SUB_CANAL,
                                !empty($item->FEC_HABILITACION) ? date('d-m-Y', strtotime($item->FEC_HABILITACION)) : '',
                                $item->TOTAL_COBRO,
                                $item->DIFF,
                                $item->PLAZO_PAGO,
                                $item->VAL,
                                $item->TXT_CATEGORIA_JEFE_VENTA,
                                $item->TASA_COMISION,
                                $item->TOTAL_COMISION,
                            ]);

                            if (isset($item->TIPO_COMPROBANTE) && trim($item->TIPO_COMPROBANTE) == 'NOTA_CREDITO') {
                                $sheet->cells('A' . $row . ':' . $lastColDetail . $row, function ($cells) {
                                    $cells->setBackground('#FFC7CE');
                                    $cells->setFontColor('#9C0006');
                                });
                            }
                            $row++;
                        }

                        $sheet->setAutoSize(true);
                        $sheet->setWidth([
                            'A' => 20, 'B' => 20, 'C' => 20, 'D' => 20, 'E' => 20
                        ]);
                    });
                }
            })->export('xls');
        } catch (\Exception $e) {
            return "Error al exportar Excel: " . $e->getMessage() . " en línea " . $e->getLine();
        }
    }
    public function actionComisionMercadoMayorista($idopcion)
    {
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl <> 'true') {
            return $validarurl;
        }

        // Fetch Jefes de Venta for the selection
        $jefes = DB::table('WEB_COMISION_MERCADO_MAYORISTA')
            ->select('COD_CATEGORIA_JEFE_VENTA as value', 'TXT_CATEGORIA_JEFE_VENTA as text')
            ->whereNotNull('COD_CATEGORIA_JEFE_VENTA')
            ->distinct()
            ->orderBy('TXT_CATEGORIA_JEFE_VENTA', 'asc')
            ->get();

        // Fetch Periodos for selection
        $periodos = DB::table('CON.PERIODO')
            ->select('COD_PERIODO as value', 'TXT_CODIGO as text', 'FEC_INICIO', 'FEC_FIN')
            ->where('COD_EMPR', 'IACHEM0000007086')
            ->orderBy('FEC_INICIO', 'desc')
            ->get();

        return View::make('comision/mercadomayorista', [
            'idopcion' => $idopcion,
            'jefes' => $jefes,
            'periodos' => $periodos,
            'funcion' => $this->funciones
        ]);
    }

    public function actionAjaxBuscarComisionesxPeriodo(Request $request)
    {
        $cod_jefe = $request->input('cod_jefe');
        $periodo_inicio = $request->input('periodo_inicio');
        $periodo_fin = $request->input('periodo_fin');

        $p_inicio = DB::table('CON.PERIODO')->where('COD_PERIODO', $periodo_inicio)->first();
        $p_fin = DB::table('CON.PERIODO')->where('COD_PERIODO', $periodo_fin)->first();

        if (!$p_inicio || !$p_fin) {
            return response()->json(['status' => 'error', 'message' => 'Periodos no válidos']);
        }

        $fecha_inicio = date('Ymd', strtotime($p_inicio->FEC_INICIO));
        $fecha_fin = date('Ymd', strtotime($p_fin->FEC_FIN));

        // Get all periodos within range using CAST to avoid nvarchar to datetime out-of-range errors
        $periodos_rango = DB::table('CON.PERIODO')
            ->where('COD_EMPR', 'IACHEM0000007086')
            ->whereRaw("CAST(FEC_INICIO AS DATE) >= ?", [$fecha_inicio])
            ->whereRaw("CAST(FEC_FIN AS DATE) <= ?", [$fecha_fin])
            ->pluck('COD_PERIODO')
            ->toArray();

        if (empty($periodos_rango)) {
            return response()->json(['status' => 'error', 'message' => 'No hay periodos en el rango seleccionado']);
        }

        // Obtener la información de comisiones
        $es_jefe_flag = DB::table('WEB_COMISION_MERCADO_MAYORISTA_JEFE')
            ->whereIn('COD_PERIODO', $periodos_rango)
            ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
            ->where('TXT_PROVIENE', 'MERCADO MAYORISTA')
            ->exists();

        if ($es_jefe_flag) {
            $data = DB::table('WEB_COMISION_MERCADO_MAYORISTA_JEFE')
                ->select(
                    'TXT_CATEGORIA_JEFE_VENTA',
                    'TXT_CATEGORIA_JEFE_VENTA_ASIMILADO',
                    'CAT_INF_NOM_CATEGORIA',
                    DB::raw('SUM(PESO_ORDEN_50) as PESO_ORDEN_50'),
                    DB::raw('MAX(TASA_COMISION) as TASA_COMISION'),
                    DB::raw('SUM(TOTAL_COMISION) as TOTAL_COMISION')
                )
                ->whereIn('COD_PERIODO', $periodos_rango)
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('TXT_PROVIENE', 'MERCADO MAYORISTA')
                ->groupBy('TXT_CATEGORIA_JEFE_VENTA', 'TXT_CATEGORIA_JEFE_VENTA_ASIMILADO', 'CAT_INF_NOM_CATEGORIA')
                ->orderBy('TXT_CATEGORIA_JEFE_VENTA_ASIMILADO', 'asc')
                ->get();
        } else {
            $data = DB::table('WEB_COMISION_MERCADO_MAYORISTA')
                ->whereIn('COD_PERIODO', $periodos_rango)
                ->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe)
                ->where('TXT_PROVIENE', 'MERCADO MAYORISTA')
                ->orderBy('FEC_ORDEN', 'asc')
                ->get();
        }

        $subfamilias = [];
        $resumen = [];
        $totales_v = [];
        $t_peso = 0;
        $t_com = 0;

        if (!$es_jefe_flag) {
            foreach ($data as $item) {
                $subfam = (string) $item->CAT_INF_NOM_CATEGORIA;
                if (!in_array($subfam, $subfamilias)) {
                    $subfamilias[] = $subfam;
                }
                $key = $item->NOM_EMPR . '|' . $item->TXT_CATEGORIA_CANAL_VENTA . '|' . $item->TXT_CATEGORIA_SUB_CANAL;
                if (!isset($resumen[$key])) {
                    $resumen[$key] = [
                        'empresa' => (string) $item->NOM_EMPR, 
                        'canal' => (string) $item->TXT_CATEGORIA_CANAL_VENTA, 
                        'subcanal' => (string) $item->TXT_CATEGORIA_SUB_CANAL, 
                        'data' => [], 
                        'tp' => 0, 
                        'tc' => 0
                    ];
                }
                if (!isset($resumen[$key]['data'][$subfam])) {
                    $resumen[$key]['data'][$subfam] = ['peso' => 0, 'com' => 0];
                }
                $pv = is_numeric($item->PESO_ORDEN_50) ? (float) $item->PESO_ORDEN_50 : 0;
                $cv = is_numeric($item->TOTAL_COMISION) ? (float) $item->TOTAL_COMISION : 0;
                
                $resumen[$key]['data'][$subfam]['peso'] += $pv;
                $resumen[$key]['data'][$subfam]['com'] += $cv;
                $resumen[$key]['tp'] += $pv;
                $resumen[$key]['tc'] += $cv;

                if (!isset($totales_v[$subfam])) {
                    $totales_v[$subfam] = ['peso' => 0, 'com' => 0];
                }
                $totales_v[$subfam]['peso'] += $pv;
                $totales_v[$subfam]['com'] += $cv;
                $t_peso += $pv;
                $t_com += $cv;
            }
            sort($subfamilias);
        }

        $html = View::make('comision.ajax.mercadomayorista', [
            'data' => $data,
            'es_jefe_flag' => $es_jefe_flag,
            'subfamilias' => $subfamilias,
            'resumen' => $resumen,
            'totales_v' => $totales_v,
            't_peso' => $t_peso,
            't_com' => $t_com
        ])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'periodos_str' => implode(',', $periodos_rango)
        ]);
    }

    public function actionDashboardComisionesMayorista($idopcion)
    {
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl <> 'true') {
            return $validarurl;
        }

        $jefes = DB::table('WEB_COMISION_MERCADO_MAYORISTA')
            ->select('COD_CATEGORIA_JEFE_VENTA as value', 'TXT_CATEGORIA_JEFE_VENTA as text')
            ->whereNotNull('COD_CATEGORIA_JEFE_VENTA')
            ->distinct()
            ->orderBy('TXT_CATEGORIA_JEFE_VENTA', 'asc')
            ->get();

        $periodos = DB::table('CON.PERIODO')
            ->select('COD_PERIODO as value', 'TXT_CODIGO as text', 'FEC_INICIO', 'FEC_FIN')
            ->where('COD_EMPR', 'IACHEM0000007086')
            ->orderBy('FEC_INICIO', 'desc')
            ->get();

        return View::make('comision/dashboard_mayorista', [
            'idopcion' => $idopcion,
            'jefes' => $jefes,
            'periodos' => $periodos,
            'funcion' => $this->funciones
        ]);
    }

    public function actionAjaxDashboardComisionesMayorista(Request $request)
    {
        $cod_jefe = $request->input('cod_jefe');
        $periodo_inicio = $request->input('periodo_inicio');
        $periodo_fin = $request->input('periodo_fin');

        $p_inicio = DB::table('CON.PERIODO')->where('COD_PERIODO', $periodo_inicio)->first();
        $p_fin = DB::table('CON.PERIODO')->where('COD_PERIODO', $periodo_fin)->first();

        if (!$p_inicio || !$p_fin) {
            return response()->json(['status' => 'error', 'message' => 'Periodos no válidos']);
        }

        $fecha_inicio = date('Ymd', strtotime($p_inicio->FEC_INICIO));
        $fecha_fin = date('Ymd', strtotime($p_fin->FEC_FIN));

        $periodos_rango = DB::table('CON.PERIODO')
            ->where('COD_EMPR', 'IACHEM0000007086')
            ->whereRaw("CAST(FEC_INICIO AS DATE) >= ?", [$fecha_inicio])
            ->whereRaw("CAST(FEC_FIN AS DATE) <= ?", [$fecha_fin])
            ->pluck('COD_PERIODO')
            ->toArray();

        if (empty($periodos_rango)) {
            return response()->json(['status' => 'error', 'message' => 'No hay periodos en el rango seleccionado']);
        }

        $query = DB::table('WEB_COMISION_MERCADO_MAYORISTA')
            ->whereIn('COD_PERIODO', $periodos_rango)
            ->where('TXT_PROVIENE', 'MERCADO MAYORISTA');
            
        if (!empty($cod_jefe) && $cod_jefe != 'ALL') {
            $query->where('COD_CATEGORIA_JEFE_VENTA', $cod_jefe);
        }

        $data = $query->get();

        // 1. KPIs
        $volumen_total = 0;
        $ventas_brutas = 0;
        $comisiones_pagadas = 0;
        $ventas_exitosas = 0;
        $total_registros = count($data);

        // 2. Pérdida de comisión
        $motivos = ['Comisión Pagada' => 0, 'Pago Parcial' => 0, 'Pago Fuera Plazo' => 0, 'Reglas no cumplidas' => 0, 'Otro' => 0];

        // 3. Jefes de Venta
        $jefes_stats = [];

        // 4. Subfamilias
        $subfam_stats = [];

        foreach ($data as $item) {
            $peso = (float)$item->PESO_ORDEN_50;
            $total_p = (float)$item->TOTAL_P;
            $comision = (float)$item->TOTAL_COMISION;
            
            $volumen_total += $peso;
            $ventas_brutas += $total_p;
            $comisiones_pagadas += $comision;

            $val_estado = trim($item->VAL);

            if ($val_estado == 'CANCELADO') {
                $ventas_exitosas++;
                $motivos['Comisión Pagada'] += $total_p;
            } elseif ($val_estado == 'PARCIAL') {
                $motivos['Pago Parcial'] += $total_p;
            } elseif ($val_estado == 'NO VÁLIDO') {
                if (is_numeric($item->DIFF) && is_numeric($item->PLAZO_PAGO) && $item->DIFF > $item->PLAZO_PAGO) {
                    $motivos['Pago Fuera Plazo'] += $total_p;
                } else {
                    $motivos['Reglas no cumplidas'] += $total_p;
                }
            } else {
                $motivos['Otro'] += $total_p;
            }

            // Jefes
            $nom_jefe = trim($item->TXT_CATEGORIA_JEFE_VENTA) ?: 'Sin Jefe';
            if (!isset($jefes_stats[$nom_jefe])) {
                $jefes_stats[$nom_jefe] = ['ventas' => 0, 'comisiones' => 0];
            }
            $jefes_stats[$nom_jefe]['ventas'] += $total_p;
            $jefes_stats[$nom_jefe]['comisiones'] += $comision;

            // Subfamilias
            $subfam = trim($item->CAT_INF_NOM_CATEGORIA) ?: 'Sin Subfamilia';
            if (!isset($subfam_stats[$subfam])) {
                $subfam_stats[$subfam] = ['volumen' => 0, 'comision' => 0];
            }
            $subfam_stats[$subfam]['volumen'] += $peso;
            $subfam_stats[$subfam]['comision'] += $comision;
        }

        $efectividad = $total_registros > 0 ? round(($ventas_exitosas / $total_registros) * 100, 2) : 0;

        // Sort jefes and subfam by some metric
        uasort($jefes_stats, function($a, $b) { return $b['ventas'] <=> $a['ventas']; });
        uasort($subfam_stats, function($a, $b) { return $b['volumen'] <=> $a['volumen']; });

        return response()->json([
            'status' => 'success',
            'kpis' => [
                'volumen_total' => number_format($volumen_total, 2),
                'ventas_brutas' => number_format($ventas_brutas, 2),
                'comisiones_pagadas' => number_format($comisiones_pagadas, 2),
                'efectividad' => $efectividad
            ],
            'charts' => [
                'motivos' => [
                    'labels' => array_keys($motivos),
                    'data' => array_values($motivos)
                ],
                'jefes' => [
                    'labels' => array_keys(array_slice($jefes_stats, 0, 10)),
                    'ventas' => array_column(array_slice($jefes_stats, 0, 10), 'ventas'),
                    'comisiones' => array_column(array_slice($jefes_stats, 0, 10), 'comisiones')
                ],
                'subfamilias' => [
                    'labels' => array_keys(array_slice($subfam_stats, 0, 15)),
                    'volumen' => array_column(array_slice($subfam_stats, 0, 15), 'volumen'),
                    'comision' => array_column(array_slice($subfam_stats, 0, 15), 'comision')
                ]
            ]
        ]);
    }
}
