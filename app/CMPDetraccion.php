<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMPDetraccion extends Model
{
    protected $table = 'CMP.DETRACCION';
    public $timestamps=false;
    protected $primaryKey = 'ID_PICKING';
    public $incrementing = false;
    public $keyType = 'string';

    public function detracciondetalle()
    {
        return $this->hasMany('App\CMPDetraccionDetalle', 'ID_PICKING', 'ID_PICKING')->where('COD_ESTADO','=', 1);
    }
    
}
