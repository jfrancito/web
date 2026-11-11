<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBComisionConfiguracion extends Model
{
    protected $table = 'WEB.comision_configuraciones';
    public $timestamps = false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
}

