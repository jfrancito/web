<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ALMCarroIngresoSalida extends Model
{
    protected $table = 'ALM.CARRO_INGRESO_SALIDA';
    public $timestamps=false;
    protected $primaryKey = 'COD_CARRO_INGRESO_SALIDA';
	public $incrementing = false;
	public $keyType = 'string';



}



