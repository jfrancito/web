<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
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
}