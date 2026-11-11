<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBDetalleCuota extends Model
{
    protected $table = 'WEB.detallecuotas';
    public $timestamps=false;
    public $incrementing = false;
    public $keyType = 'string';


}
