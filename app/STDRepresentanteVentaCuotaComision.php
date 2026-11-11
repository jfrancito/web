<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class STDRepresentanteVentaCuotaComision extends Model
{
    protected $table = 'STD.REPRESENTANTE_VENTA_CUOTA_COMISION';
    public $timestamps=false;
    protected $primaryKey = 'COD_RV_CC';
	public $incrementing = false;
	public $keyType = 'string';
}



