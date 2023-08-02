<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBViewDetalleOrdenDespachoSinMuestra extends Model
{
    protected $table = 'WEB.viewdetalleordendespachossinmuestra';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function ordendespacho()
    {
        return $this->belongsTo('App\WEBOrdenDespacho', 'ordendespacho_id', 'id')->where('activo','=', 1);
    }

    public function producto()
    {
        return $this->belongsTo('App\ALMProducto', 'producto_id', 'COD_PRODUCTO');
    }


}
