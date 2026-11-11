<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Session;

class WEBAsientoMovimiento extends Model
{
    //
    protected $table = 'WEB.asientomovimientos';
    public $timestamps = false;
    protected $primaryKey = 'COD_ASIENTO_MOVIMIENTO';
    public $incrementing = false;
    public $keyType = 'string';
}
