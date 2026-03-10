<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\View;
use Session;

class BarraMasivoController extends Controller
{
    public function actionGestionBarraMasivoGrande($idopcion)
    {
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl <> 'true') { return $validarurl; }

        View::share('titulo', 'Gestión Barra Masivo Grande');

        return View::make('barra/masivogrande', [
            'idopcion' => $idopcion,
        ]);
    }

    public function actionProcesarBarraMasivoGrande($idopcion, Request $request)
    {
        $validarurl = $this->funciones->getUrl($idopcion, 'Modificar');
        if ($validarurl <> 'true') { return $validarurl; }

        if($request->hasFile('select_file')) {
            $filePath = $request->file('select_file')->getRealPath();

            $data_export = [];

            try {
                Excel::load($filePath, function ($reader) use (&$data_export) {
                    $results = $reader->get();

                    if($results->count() > 0 && !isset($results[0]->lpn) && isset($results[0][0]) && isset($results[0][0]->lpn)) {
                        $results = $results[0]; // Take first sheet if multiple
                    }

                    // Modificación principal: Se van a mantener los agrupamientos con array de LPNs
                    // Sin embargo, en caso la librería se coma el 'LPN' como encabezado numérico largo lo asignaremos como string.
                    $groupedLPNs = [];

                    foreach ($results as $index => $row) {
                        $lpn_raw = isset($row->lpn) ? (string)$row->lpn : ''; 
                        
                        // Validar si la fila tiene un lpn o está vacía
                        if(empty($lpn_raw)) {
                            continue;
                        }

                        // Formatear numérico de notación científica (en caso de Excel) a string nativo
                        if (is_numeric($lpn_raw) && strpos(strtoupper($lpn_raw), 'E') !== false) {
                            $lpn = number_format((float)$lpn_raw, 0, '', '');
                        } else {
                            $lpn = (string) $lpn_raw;
                        }

                        if (!isset($groupedLPNs[$lpn])) {
                            $groupedLPNs[$lpn] = [];
                        }

                        $row_number = $index + 2;

                        $groupedLPNs[$lpn][] = [
                            'fila'      => (string)$row_number,
                            'nro_orden' => isset($row->nro_orden) ? (string)$row->nro_orden : '',
                            'cod_ean'   => isset($row->cod_ean) ? (string)$row->cod_ean : '',
                            'cod_tienda'=> isset($row->cod_tienda) ? (string)$row->cod_tienda : '',
                            'producto'  => isset($row->producto) ? (string)$row->producto : '',
                            'cantidad'  => isset($row->cantidad) ? (string)$row->cantidad : '',
                            'embolsado' => isset($row->embolsado) ? (string)$row->embolsado : '',
                        ];
                    }

                    // Armar la estructura resultante (1 lpn -> 10 columnas hacia la derecha)
                    $data_export = [];
                    foreach ($groupedLPNs as $lpn => $items) {
                        $flat_row = [];
                        // Set LPN forcefully as string
                        $flat_row['lpn'] = ' ' . $lpn; // Trick Excel to treating it strictly as text by prepending a space if needed, though formatting is better. Let's just use string.
                        $flat_row['lpn'] = (string)$lpn;
                        
                        $i = 1;
                        foreach ($items as $item) {
                            if($i > 10) break; 
                            
                            $flat_row['fila_'.$i]      = $item['fila'];
                            $flat_row['nro_orden_'.$i] = $item['nro_orden'];
                            $flat_row['cod_ean_'.$i]   = $item['cod_ean'];
                            $flat_row['cod_tienda_'.$i]= $item['cod_tienda'];
                            $flat_row['producto_'.$i]  = $item['producto'];
                            $flat_row['cantidad_'.$i]  = $item['cantidad'];
                            $flat_row['embolsado_'.$i] = $item['embolsado'];
                            $i++;
                        }
                        
                        // Fill remaining slots
                        for(; $i <= 10; $i++) {
                            $flat_row['fila_'.$i]      = '';
                            $flat_row['nro_orden_'.$i] = '';
                            $flat_row['cod_ean_'.$i]   = '';
                            $flat_row['cod_tienda_'.$i]= '';
                            $flat_row['producto_'.$i]  = '';
                            $flat_row['cantidad_'.$i]  = '';
                            $flat_row['embolsado_'.$i] = '';
                        }

                        $data_export[] = $flat_row;
                    }
                });

                if(count($data_export) > 0) {
                    $filename = 'barras3';
                    $tempPath = storage_path('exports');
                    
                    if(!file_exists($tempPath)) {
                        mkdir($tempPath, 0777, true);
                    }

                    Excel::create($filename, function($excel) use ($data_export) {
                        $excel->sheet('Sheet1', function($sheet) use ($data_export) {
                            $sheet->setColumnFormat(array(
                                'A' => '@',       // LPN
                                'D' => '0',       // cod_ean_1
                                'K' => '0',       // cod_ean_2
                                'R' => '0',       // cod_ean_3
                                'Y' => '0',       // cod_ean_4
                                'AF' => '0',      // cod_ean_5
                                'AM' => '0',      // cod_ean_6
                                'AT' => '0',      // cod_ean_7
                                'BA' => '0',      // cod_ean_8
                                'BH' => '0',      // cod_ean_9
                                'BO' => '0',      // cod_ean_10
                            ));
                            $sheet->fromArray($data_export, null, 'A1', true, true);
                        });
                    })->store('xlsx', $tempPath);

                    $fullTempPath = $tempPath . '/' . $filename . '.xlsx';
                    $networkPath = '\\\\10.1.1.117\\Barras\\barras3.xlsx';

                    try {
                        if (copy($fullTempPath, $networkPath)) {
                            $mensaje = 'Archivo procesado exitosamente y guardado en ' . $networkPath;
                            $tipo = 'bienhecho';
                        } else {
                            $mensaje = 'El archivo fue procesado, pero falló al copiar a la ruta de red. Se guardó localmente en '.$fullTempPath;
                            $tipo = 'error';
                        }
                    } catch (\Exception $e) {
                         $mensaje = 'Ocurrió un error al copiar el archivo a la ruta de red. Verifique permisos con 10.1.1.117';
                         $tipo = 'error';
                    }
                } else {
                    $mensaje = 'No se encontraron datos agrupables por lpn. Verifique los encabezados.';
                    $tipo = 'error';
                }
            } catch (\Exception $e) {
                $mensaje = 'Error al procesar el archivo Excel: ' . $e->getMessage();
                $tipo = 'error';
            }

        } else {
            $mensaje = 'Por favor seleccione un archivo Excel.';
            $tipo = 'error';
        }

        return Redirect::to('/gestion-de-barra-masivo-grande/'.$idopcion)->with($tipo, $mensaje);
    }
}
