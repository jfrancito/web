<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ALMProducto extends Model
{
    protected $table = 'ALM.PRODUCTO';
    public $timestamps=false;

    protected $primaryKey = 'COD_PRODUCTO';
    public $incrementing = false;
    public $keyType = 'string';

    
    public function precioproducto()
    {
        return $this->hasMany('App\WEBPrecioProducto', 'producto_id', 'id');
    }

    public function precioproductohistorial()
    {
        return $this->hasMany('App\WEBPrecioProductoHistorial', 'producto_id', 'id');
    }

    public function detalleproducto()
    {
        return $this->hasMany('App\WEBDetallePedido', 'producto_id', 'id');
    }

    public function detalleordendespacho()
    {
        return $this->hasMany('App\WEBDetalleOrdenDespacho', 'producto_id', 'id');
    }

    public function unidadmedida()
    {
        return $this->belongsTo('App\CMPCategoria', 'COD_CATEGORIA_UNIDAD_MEDIDA', 'COD_CATEGORIA');
    }


}
