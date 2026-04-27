<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CMPCategoria;
use App\WEBComisionConfiguracion;
use App\WEBSubcanalJefeVenta;
use View;
use Session;

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

        // Ensure tables exist
        $this->checkTablesExist();

        // Fetch Categories for the selection form
        $marcas = CMPCategoria::where('TXT_GRUPO', 'MARCA_PRODUCTO')
            ->where('COD_ESTADO', 1)
            ->orderBy('NOM_CATEGORIA', 'asc')
            ->get();

        $tiempos = CMPCategoria::where('TXT_GRUPO', 'TIEMPO_COBRANZA')
            ->where('COD_ESTADO', 1)
            ->orderBy('NOM_CATEGORIA', 'asc')
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
            ->orderBy('M.NOM_CATEGORIA', 'asc')
            ->get();

        $subcanal_jefes = WEBSubcanalJefeVenta::all();

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
            $config_map[$key] = $config->porcentaje;
            
            $used_marcas_ids[] = $config->cod_marca;
            $used_tiempos_ids[] = $config->cod_tiempo_cobranza;
            $used_subcanales_ids[] = $config->cod_sub_canal;
        }

        // Filter categories for the Matrix View (only those used)
        $marcas_matrix = $marcas->whereIn('COD_CATEGORIA', array_unique($used_marcas_ids));
        $tiempos_matrix = $tiempos->whereIn('COD_CATEGORIA', array_unique($used_tiempos_ids));
        $subcanales_matrix = $subcanales->whereIn('COD_CATEGORIA', array_unique($used_subcanales_ids));

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

        $config = new WEBComisionConfiguracion();
        $config->id = $new_id;
        $config->cod_marca = $cod_marca;
        $config->cod_tiempo_cobranza = $cod_tiempo;
        $config->cod_sub_canal = $cod_sub_canal;
        $config->porcentaje = $porcentaje;
        $config->usuario_creacion = Session::get('usuario')->id ?? 'ADMIN';
        $config->save();


        return response()->json(['status' => 'success', 'message' => 'Configuración añadida correctamente']);
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

        // Clear existing mappings for this subcanal
        WEBSubcanalJefeVenta::where('cod_sub_canal', $cod_sub_canal)->delete();

        if (is_array($cod_jefes)) {
            foreach ($cod_jefes as $cod_jefe) {
                $sj = new WEBSubcanalJefeVenta();
                $sj->cod_sub_canal = $cod_sub_canal;
                $sj->cod_jefe_venta = $cod_jefe;
                $sj->save();
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Jefes asociados correctamente']);
    }

    private function checkTablesExist()
    {
        // Simple check and creation if missing (specific for SQL Server/Laragon environment)
        try {
            // Check if WEB.comision_configuraciones exists
            DB::select("SELECT TOP 1 * FROM WEB.comision_configuraciones");
        } catch (\Exception $e) {
            DB::statement("
                CREATE TABLE WEB.comision_configuraciones (
                    id VARCHAR(20) PRIMARY KEY,
                    cod_marca VARCHAR(50) NOT NULL,
                    cod_tiempo_cobranza VARCHAR(50) NOT NULL,
                    cod_sub_canal VARCHAR(50) NOT NULL,
                    porcentaje DECIMAL(10, 4) DEFAULT 0.0000,
                    cod_estado INT DEFAULT 1,
                    fecha_creacion DATETIME DEFAULT GETDATE(),
                    usuario_creacion VARCHAR(50)
                )
            ");

        }

        try {
            // Check if WEB.subcanal_jefe_venta exists
            DB::select("SELECT TOP 1 * FROM WEB.subcanal_jefe_venta");
        } catch (\Exception $e) {
            DB::statement("
                CREATE TABLE WEB.subcanal_jefe_venta (
                    id INT IDENTITY(1,1) PRIMARY KEY,
                    cod_sub_canal VARCHAR(50) NOT NULL,
                    cod_jefe_venta VARCHAR(50) NOT NULL,
                    cod_estado INT DEFAULT 1,
                    fecha_creacion DATETIME DEFAULT GETDATE()
                )
            ");
        }
    }
}
