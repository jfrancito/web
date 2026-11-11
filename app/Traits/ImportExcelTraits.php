<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Modelos\WEBCuentaContable;
use App\Modelos\CONPeriodo;
use PDO;
use Session;

trait ImportExcelTraits
{
    public function guardar_stock_autoservicio($ind_generar, $autoservicio, $id_stock, $cod_producto, $nom_producto,
                                               $cod_sucursal, $nom_sucursal, $stock_producto, $fec_stock, $cod_estado)
    {
        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.STOCK_PRODUCTO_AUTOSERVICIO_IUD
                                                            @IND_GENERAR = ?,
                                                            @AUTOSERVICIO = ?,
                                                            @ID_STOCK = ?,
                                                            @COD_PRODUCTO = ?,
                                                            @NOM_PRODUCTO = ?,
                                                            @COD_SUCURSAL = ?,
                                                            @NOM_SUCURSAL = ?,
                                                            @STOCK_PRODUCTO = ?,
                                                            @FEC_STOCK = ?,
                                                            @USUARIO = ?,
                                                            @COD_ESTADO = ?,
                                                            @COD_EMPRESA = ?');

        $usuario = Session::get('usuario')->id;
        $cod_empresa = Session::get('empresas')->COD_EMPR;
        $stmt->bindParam(1, $ind_generar , PDO::PARAM_STR);
        $stmt->bindParam(2, $autoservicio ,PDO::PARAM_STR);
        $stmt->bindParam(3, $id_stock  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $cod_producto  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $nom_producto  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $cod_sucursal  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $nom_sucursal  ,PDO::PARAM_STR);
        $stmt->bindParam(8, $stock_producto  ,PDO::PARAM_STR);
        $stmt->bindParam(9, $fec_stock  ,PDO::PARAM_STR);
        $stmt->bindParam(10, $usuario  ,PDO::PARAM_STR);
        $stmt->bindParam(11, $cod_estado  ,PDO::PARAM_INT);
        $stmt->bindParam(12, $cod_empresa ,PDO::PARAM_STR);
        $stmt->execute();
    }

    public function guardar_archivo_stock_autoservicio($ind_generar, $autoservicio, $id_archivo, $fec_archivo,
                                                       $nom_archivo, $cod_estado)
    {
        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.ARCHIVO_PRODUCTO_AUTOSERVICIO_IUD
                                                            @IND_GENERAR = ?,
                                                            @AUTOSERVICIO = ?,
                                                            @ID_ARCHIVO = ?,
                                                            @FEC_ARCHIVO = ?,
                                                            @NOM_ARCHIVO = ?,
                                                            @USUARIO = ?,
                                                            @COD_ESTADO = ?,
                                                            @COD_EMPRESA = ?');

        $usuario = Session::get('usuario')->id;
        $cod_empresa = Session::get('empresas')->COD_EMPR;
        $stmt->bindParam(1, $ind_generar , PDO::PARAM_STR);
        $stmt->bindParam(2, $autoservicio ,PDO::PARAM_STR);
        $stmt->bindParam(3, $id_archivo  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $fec_archivo  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $nom_archivo  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $usuario  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $cod_estado  ,PDO::PARAM_INT);
        $stmt->bindParam(8, $cod_empresa ,PDO::PARAM_STR);
        $stmt->execute();
    }

    public function guardar_producto_autoservicio($ind_generar, $autoservicio, $id_producto, $sku_producto,
                                                       $nom_producto, $cod_estado)
    {
        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.PRODUCTO_AUTOSERVICIO_IUD
                                                            @IND_GENERAR = ?,
                                                            @AUTOSERVICIO = ?,
                                                            @ID_PRODUCTO = ?,
                                                            @SKU_PRODUCTO = ?,
                                                            @NOM_PRODUCTO = ?,
                                                            @USUARIO = ?,
                                                            @COD_ESTADO = ?,
                                                            @COD_EMPRESA = ?');

        $usuario = Session::get('usuario')->id;
        $cod_empresa = Session::get('empresas')->COD_EMPR;
        $stmt->bindParam(1, $ind_generar , PDO::PARAM_STR);
        $stmt->bindParam(2, $autoservicio ,PDO::PARAM_STR);
        $stmt->bindParam(3, $id_producto  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $sku_producto  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $nom_producto  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $usuario  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $cod_estado  ,PDO::PARAM_INT);
        $stmt->bindParam(8, $cod_empresa ,PDO::PARAM_STR);
        $stmt->execute();
    }

    public function guardar_sucursal_autoservicio($ind_generar, $autoservicio, $id_sucursal, $cod_sucursal,
                                                       $nom_sucursal, $cod_estado)
    {
        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.SUCURSAL_AUTOSERVICIO_IUD
                                                            @IND_GENERAR = ?,
                                                            @AUTOSERVICIO = ?,
                                                            @ID_SUCURSAL = ?,
                                                            @COD_SUCURSAL = ?,
                                                            @NOM_SUCURSAL = ?,
                                                            @USUARIO = ?,
                                                            @COD_ESTADO = ?,
                                                            @COD_EMPRESA = ?');

        $usuario = Session::get('usuario')->id;
        $cod_empresa = Session::get('empresas')->COD_EMPR;
        $stmt->bindParam(1, $ind_generar , PDO::PARAM_STR);
        $stmt->bindParam(2, $autoservicio ,PDO::PARAM_STR);
        $stmt->bindParam(3, $id_sucursal  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $cod_sucursal  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $nom_sucursal  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $usuario  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $cod_estado  ,PDO::PARAM_INT);
        $stmt->bindParam(8, $cod_empresa ,PDO::PARAM_STR);
        $stmt->execute();
    }

    public function obtener_tabla_cencosud($IND_TIPO_SUCURSAL, $IND_TIPO_PRODUCTO, $FEC_STOCK, $ORDEN_STOCK)
    {
        $array_lista_retail = array();

        $COD_EMPRESA = Session::get('empresas')->COD_EMPR;

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CALCULAR_CONTENIDO_STOCK_CENCOSUD
                                                            @IND_TIPO_SUCURSAL = ?,
                                                            @IND_TIPO_PRODUCTO = ?,
                                                            @FEC_STOCK = ?,
                                                            @ORDEN_STOCK = ?,
                                                            @COD_EMPRESA = ?');

        $stmt->bindParam(1, $IND_TIPO_SUCURSAL , PDO::PARAM_STR);
        $stmt->bindParam(2, $IND_TIPO_PRODUCTO ,PDO::PARAM_STR);
        $stmt->bindParam(3, $FEC_STOCK  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $ORDEN_STOCK  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $COD_EMPRESA  ,PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch()){
            array_push($array_lista_retail, $row);
        }

        return $array_lista_retail;
    }

    public function obtener_cabecera_cencosud($IND_TIPO_SUCURSAL, $IND_TIPO_PRODUCTO, $FEC_STOCK)
    {
        $array_lista_cabecera_retail = array();

        $COD_EMPRESA = Session::get('empresas')->COD_EMPR;

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CALCULAR_CABECERA_STOCK_CENCOSUD
                                                            @IND_TIPO_SUCURSAL = ?,
                                                            @IND_TIPO_PRODUCTO = ?,
                                                            @FEC_STOCK = ?,
                                                            @COD_EMPRESA = ?');

        $stmt->bindParam(1, $IND_TIPO_SUCURSAL , PDO::PARAM_STR);
        $stmt->bindParam(2, $IND_TIPO_PRODUCTO ,PDO::PARAM_STR);
        $stmt->bindParam(3, $FEC_STOCK  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $COD_EMPRESA  ,PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch()){
            array_push($array_lista_cabecera_retail, $row);
        }

        return $array_lista_cabecera_retail;
    }

    public function obtener_tabla_autoservicio($IND_TIPO_AUTOSERVICIO, $IND_TIPO_PRODUCTO, $IND_UNIDAD, $FEC_STOCK, $ORDEN_STOCK)
    {
        $array_lista_retail = array();

        $COD_EMPRESA = Session::get('empresas')->COD_EMPR;

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CALCULAR_CONTENIDO_STOCK_AUTOSERVICIO
                                                            @IND_TIPO_AUTOSERVICIO = ?,
                                                            @IND_TIPO_PRODUCTO = ?,
                                                            @IND_UNIDAD = ?,
                                                            @FEC_STOCK = ?,
                                                            @ORDEN_STOCK = ?,
                                                            @COD_EMPRESA = ?');

        $stmt->bindParam(1, $IND_TIPO_AUTOSERVICIO , PDO::PARAM_STR);
        $stmt->bindParam(2, $IND_TIPO_PRODUCTO ,PDO::PARAM_STR);
        $stmt->bindParam(3, $IND_UNIDAD  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $FEC_STOCK  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $ORDEN_STOCK  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $COD_EMPRESA  ,PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch()){
            array_push($array_lista_retail, $row);
        }

        return $array_lista_retail;
    }

    public function obtener_cabecera_autoservicio($IND_TIPO_AUTOSERVICIO, $IND_TIPO_PRODUCTO, $IND_UNIDAD, $FEC_STOCK)
    {
        $array_lista_cabecera_retail = array();

        $COD_EMPRESA = Session::get('empresas')->COD_EMPR;

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CALCULAR_CABECERA_STOCK_AUTOSERVICIO
                                                            @IND_TIPO_AUTOSERVICIO = ?,
                                                            @IND_TIPO_PRODUCTO = ?,
                                                            @IND_UNIDAD = ?,
                                                            @FEC_STOCK = ?,
                                                            @COD_EMPRESA = ?');

        $stmt->bindParam(1, $IND_TIPO_AUTOSERVICIO , PDO::PARAM_STR);
        $stmt->bindParam(2, $IND_TIPO_PRODUCTO ,PDO::PARAM_STR);
        $stmt->bindParam(3, $IND_UNIDAD  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $FEC_STOCK  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $COD_EMPRESA  ,PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch()){
            array_push($array_lista_cabecera_retail, $row);
        }

        return $array_lista_cabecera_retail;
    }


    public function obtener_tabla_spsa($IND_TIPO_AUTOSERVICIO, $FEC_STOCK, $ORDEN_STOCK, $IND_EMPRESA)
    {
        $array_lista_retail = array();

        $COD_EMPRESA = Session::get('empresas')->COD_EMPR;

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CALCULAR_CONTENIDO_STOCK_SPSA
                                                            @IND_TIPO_AUTOSERVICIO = ?,
                                                            @FEC_STOCK = ?,
                                                            @ORDEN_STOCK = ?,
                                                            @COD_EMPRESA = ?,
                                                            @IND_EMPRESA = ?');

        $stmt->bindParam(1, $IND_TIPO_AUTOSERVICIO , PDO::PARAM_STR);
        $stmt->bindParam(2, $FEC_STOCK ,PDO::PARAM_STR);
        $stmt->bindParam(3, $ORDEN_STOCK  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $COD_EMPRESA  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $IND_EMPRESA  ,PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch()){
            array_push($array_lista_retail, $row);
        }

        return $array_lista_retail;
    }

    public function obtener_cabecera_spsa($IND_TIPO_AUTOSERVICIO, $FEC_STOCK, $IND_EMPRESA)
    {
        $array_lista_cabecera_retail = array();

        $COD_EMPRESA = Session::get('empresas')->COD_EMPR;

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CALCULAR_CABECERA_STOCK_SPSA
                                                            @IND_TIPO_AUTOSERVICIO = ?,
                                                            @FEC_STOCK = ?,
                                                            @COD_EMPRESA = ?,
                                                            @IND_EMPRESA = ?');

        $stmt->bindParam(1, $IND_TIPO_AUTOSERVICIO , PDO::PARAM_STR);
        $stmt->bindParam(2, $FEC_STOCK ,PDO::PARAM_STR);
        $stmt->bindParam(3, $COD_EMPRESA  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $IND_EMPRESA  ,PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch()){
            array_push($array_lista_cabecera_retail, $row);
        }

        return $array_lista_cabecera_retail;
    }

    public function obtener_tabla_archivo_autoservicio($ind_generar, $autoservicio, $fecha_inicio, $fecha_fin, $estado)
    {
        $array_lista_archivo_autoservicio = array();

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.GENERAL_PRODUCTO_AUTOSERVICIO_LISTAR
                                                            @IND_GENERAR = ?,
                                                            @COD_EMPRESA = ?,
                                                            @AUTOSERVICIO = ?,
                                                            @FEC_INICIO = ?,
                                                            @FEC_FIN = ?,
                                                            @ESTADO = ?');

        $cod_empresa = Session::get('empresas')->COD_EMPR;

        $stmt->bindParam(1, $ind_generar , PDO::PARAM_STR);
        $stmt->bindParam(2, $cod_empresa ,PDO::PARAM_STR);
        $stmt->bindParam(3, $autoservicio  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $fecha_inicio ,PDO::PARAM_STR);
        $stmt->bindParam(5, $fecha_fin  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $estado  ,PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch()){
            array_push($array_lista_archivo_autoservicio, $row);
        }

        return $array_lista_archivo_autoservicio;
    }

    public function obtener_tabla_cabecera_refuerzo($autoservicio, $cod_anio, $ind_generar, $ind_mes)
    {
        $array_lista_cabecera_refuerzo = array();

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CABECERA_PRODUCTOS_REFUERZO
                                                            @IND_TIPO_AUTOSERVICIO = ?,
                                                            @COD_EMPRESA = ?,
                                                            @COD_ANIO = ?,
                                                            @IND_GENERAR = ?,
                                                            @IND_MES = ?');

        $cod_empresa = Session::get('empresas')->COD_EMPR;

        $stmt->bindParam(1, $autoservicio , PDO::PARAM_STR);
        $stmt->bindParam(2, $cod_empresa ,PDO::PARAM_STR);
        $stmt->bindParam(3, $cod_anio  ,PDO::PARAM_INT);
        $stmt->bindParam(4, $ind_generar ,PDO::PARAM_STR);
        $stmt->bindParam(5, $ind_mes  ,PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->rowCount()<>0) {
            while ($row = $stmt->fetch()) {
                array_push($array_lista_cabecera_refuerzo, $row);
            }
        }

        return $array_lista_cabecera_refuerzo;
    }

    public function obtener_tabla_contenido_refuerzo($autoservicio, $cod_anio, $cod_producto, $ind_mes)
    {
        $array_lista_contenido_refuerzo = array();

        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.CONTENIDO_REFUERZO
                                                            @IND_TIPO_AUTOSERVICIO = ?,
                                                            @COD_EMPRESA = ?,
                                                            @COD_ANIO = ?,
                                                            @COD_PRODUCTO = ?,
                                                            @IND_MES = ?');

        $cod_empresa = Session::get('empresas')->COD_EMPR;

        $stmt->bindParam(1, $autoservicio , PDO::PARAM_STR);
        $stmt->bindParam(2, $cod_empresa ,PDO::PARAM_STR);
        $stmt->bindParam(3, $cod_anio  ,PDO::PARAM_INT);
        $stmt->bindParam(4, $cod_producto ,PDO::PARAM_STR);
        $stmt->bindParam(5, $ind_mes  ,PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch()){
            array_push($array_lista_contenido_refuerzo, $row);
        }

        return $array_lista_contenido_refuerzo;
    }

    public function gn_combo_periodo_xanio_xempresa($anio, $cod_empresa, $todo, $titulo)
    {
        $array = CONPeriodo::where('COD_ESTADO', '=', 1)
            ->where('COD_ANIO', '=', $anio)
            ->where('COD_EMPR', '=', $cod_empresa)
            ->orderBy('COD_MES', 'DESC')
            ->pluck('TXT_NOMBRE', 'COD_PERIODO')
            ->toArray();

        if ($todo == 'TODO') {
            $combo = array('' => $titulo, $todo => $todo) + $array;
        } else {
            $combo = array('' => $titulo) + $array;
        }

        return $combo;


    }

    private function pc_array_anio_cuentas_contable($empresa_id)
    {
        /*
        $array_anio_pc = WEBCuentaContable::where('empresa_id', '=', $empresa_id)
            ->where('activo', '=', 1)
            ->groupBy('anio')
            ->pluck('anio', 'anio')
            ->toArray();
        */
        $array_anio_pc = CONPeriodo::where('COD_ESTADO', '=', 1)
            ->where('COD_EMPR', '=', $empresa_id)
            ->groupBy('COD_ANIO')
            ->pluck('COD_ANIO', 'COD_ANIO')
            ->toArray();

        return $array_anio_pc;

    }

    private function gn_generacion_combo_array($titulo, $todo, $array)
    {
        if ($todo == 'TODO') {
            $combo_anio_pc = array('' => $titulo, $todo => $todo) + $array;
        } else {
            $combo_anio_pc = array('' => $titulo) + $array;
        }
        return $combo_anio_pc;
    }

}