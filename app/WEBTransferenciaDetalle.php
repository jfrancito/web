<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBTransferenciaDetalle extends Model
{
    protected $table = 'WEB.transferenciadetalle';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function producto()
    {
        return $this->belongsTo('App\ALMProducto', 'producto_id', 'COD_PRODUCTO');
    }
    public function transferencia()
    {
        return $this->belongsTo('App\WEBTransferencia', 'transferencia_id', 'id');
    }

    public function scopeCentro($query,$tipo){

        if(trim($tipo) != 'TODOS'){
            $query->where('WEB.transferenciadetalle.centro_id', '=', $tipo);
        }

    }

}
