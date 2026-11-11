<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMPCategoriaRelacion extends Model
{
    protected $table = 'CMP.CATEGORIA_RELACION';
    public $timestamps=false;

    protected $primaryKey = 'COD_CATEGORIA';
	public $incrementing = false;
    public $keyType = 'string';


}



