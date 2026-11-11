<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBDetallePlanillaComision extends Model
{
    protected $table = 'WEB.detalleplanillacomisiones';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function scopeVendedor($query,$vendedor){

        if(trim($vendedor) != 'TODO'){
            $query->where('COD_CATEGORIA_JEFE_VENTA', '=', $vendedor);
        }

    }


}
