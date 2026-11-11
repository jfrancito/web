<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\WEBCuota;
use App\WEBDetalleCuota;
use App\CONPeriodo;
use App\CMPCategoria;
use App\CMPCategoriaRelacion;
use App\WEBPeridoBono;
use App\WEBDetalleCalculoBono;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait OrdenPedidoTraits
{

	public function op_lista_detalle_producto($cod_orden)
	{

		$listaproductos = 	DB::table('CMP.DETALLE_PRODUCTO')
						    ->where('COD_TABLA', '=', $cod_orden)
						    ->where('COD_ESTADO', '=', 1)
						    ->get();

		return $listaproductos;

	}


	public function op_lista_detalle_documentos($cod_orden)
	{

		$listadocumentos = 		DB::table('CMP.REFERENCIA_ASOC as asoc')
							    ->join('CMP.DOCUMENTO_CTBLE as doc', 'doc.COD_DOCUMENTO_CTBLE', '=', 'asoc.COD_TABLA_ASOC')
							    ->select(
							        'doc.COD_DOCUMENTO_CTBLE as ORDEN',
							        'doc.TXT_CATEGORIA_TIPO_DOC as TIPO_DOC',
							        DB::raw("CONCAT(doc.NRO_SERIE, '-', doc.NRO_DOC) as DOCUMENTO"),
							        'doc.FEC_EMISION as FECHA',
							        'doc.TXT_CATEGORIA_ESTADO_DOC_CTBLE as ESTADO_DOC'
							    )
							    ->where('asoc.COD_TABLA', '=', $cod_orden)
							    ->where('asoc.COD_ESTADO', '=', 1)
							    ->where('doc.COD_ESTADO', '=', 1)
							    ->union(
							        DB::table('CMP.REFERENCIA_ASOC as asoc')
							            ->join('CMP.ORDEN as ord', 'ord.COD_ORDEN', '=', 'asoc.COD_TABLA_ASOC')
							            ->select(
							                'ord.COD_ORDEN as ORDEN',
							                'ord.TXT_CATEGORIA_TIPO_ORDEN as TIPO_DOC',
							                'ord.COD_ORDEN as DOCUMENTO',
							                'ord.FEC_ORDEN as FECHA',
							                'ord.TXT_CATEGORIA_ESTADO_ORDEN as ESTADO_DOC'
							            )
							            ->where('asoc.COD_TABLA', '=', $cod_orden)
							            ->where('asoc.COD_ESTADO', '=', 1)
							            ->where('ord.COD_ESTADO', '=', 1)
							    )
							    ->get();

		return $listadocumentos;

	}



}