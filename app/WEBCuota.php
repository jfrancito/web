<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBCuota extends Model
{
    protected $table = 'WEB.cuotas';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';


}
