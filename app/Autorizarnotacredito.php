<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Autorizarnotacredito extends Model
{
    protected $table = 'Autorizarnotacreditos';
    public $timestamps=false;
    protected $primaryKey = 'COD_APROBAR_DOC';
	public $incrementing = false;
	public $keyType = 'string';

}



