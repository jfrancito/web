<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMPCategoria extends Model
{
    protected $table = 'CMP.CATEGORIA';
    public $timestamps=false;

    protected $primaryKey = 'COD_CATEGORIA';
	public $incrementing = false;
    public $keyType = 'string';

    public function detalleordendespacho()
    {
        return $this->hasMany('App\WEBDetalleOrdenDespacho', 'unidad_medida_id', 'id');
    }


    public function producto()
    {
        return $this->hasMany('App\ALMProducto', 'COD_CATEGORIA_UNIDAD_MEDIDA', 'COD_PRODUCTO');
    }

}



