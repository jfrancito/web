<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBPeriodo extends Model
{
    protected $table = 'WEB.periodos';
    public $timestamps=false;

    public $incrementing = false;
    public $keyType = 'string';


}
