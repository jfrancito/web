<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBReglaProductoCliente extends Model
{
    protected $table = 'WEB.reglaproductoclientes';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';


    public function regla()
    {
        return $this->belongsTo('App\WEBRegla', 'regla_id', 'id');
    }


    public function scopeCuenta($query,$cuenta){

        if(trim($cuenta) != 'TODO'){
            $query->where('WEB.reglaproductoclientes.contrato_id', '=', $cuenta);
        }

    }


    public function scopeProducto($query,$producto){

        if(trim($producto) != 'TODO'){
            $query->where('WEB.reglaproductoclientes.producto_id', '=', $producto);
        }

    }


}
