<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBPickingDetalle extends Model
{
    protected $table = 'WEB.pickingdetalle';
    public $timestamps=false;
    //protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function producto()
    {
        return $this->belongsTo('App\ALMProducto', 'producto_id', 'COD_PRODUCTO');
    }
    public function picking()
    {
        return $this->belongsTo('App\WEBPicking', 'picking_id', 'id');
    }

    

}
