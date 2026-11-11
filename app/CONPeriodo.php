<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CONPeriodo extends Model
{
    protected $table = 'CON.PERIODO';
    public $timestamps=false;
    protected $primaryKey = 'COD_PERIODO';
	public $incrementing = false;
	public $keyType = 'string';


}



