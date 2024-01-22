<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Session;

class viewVentaSalidas extends Model
{
    //
    protected $table = 'viewVentaSalidas2024';
    public $timestamps = false;
    public $incrementing = false;
    public $keyType = 'string';

    public function scopeTipoMarca($query,$name){
    	if(trim($name) != 'TODOS'){
    		$query->where('TIPOMARCA.COD_CATEGORIA', '=', $name);
    	}
    }

}
