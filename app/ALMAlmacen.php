<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ALMAlmacen extends Model
{
    protected $table = 'ALM.ALMACEN';
    public $timestamps=false;
    protected $primaryKey = 'COD_ALMACEN';
	public $incrementing = false;
	public $keyType = 'string';

}



