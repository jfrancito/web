<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMPReferecenciaAsoc extends Model
{
    protected $table = 'CMP.REFERENCIA_ASOC';
    public $timestamps=false;

    protected $primaryKey = 'COD_TABLA_ASOC';
	public $incrementing = false;
    public $keyType = 'string';
    
}
