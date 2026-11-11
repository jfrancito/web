<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMPDetraccionDetalle extends Model
{
    protected $table = 'CMP.DETRACCION_DETALLE';
    public $timestamps=false;
    public $incrementing = false;
    public $keyType = 'string';

    public function picking()
    {
        return $this->belongsTo('App\CMPDetraccion', 'ID_PICKING', 'ID_PICKING');
    }

    

}
