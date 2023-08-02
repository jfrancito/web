<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBCategoriaActivoFijo extends Model
{
    protected $table = 'WEB.categoriasactivosfijos';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
    public $keyType = 'string';
}
