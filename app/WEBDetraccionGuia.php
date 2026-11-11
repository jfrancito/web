<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBDetraccionGuia extends Model
{
    protected $table = 'WEB.DETRACCION_GUIA';
    public $timestamps=false;


    protected $primaryKey = 'COD_DOCUMENTO_CTBLE';
	public $incrementing = false;
	public $keyType = 'string';


}
