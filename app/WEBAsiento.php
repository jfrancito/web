<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Session;

class WEBAsiento extends Model
{
    //
    protected $table = 'WEB.asientos';
    public $timestamps = false;
    protected $primaryKey = 'COD_ASIENTO';
    public $incrementing = false;
    public $keyType = 'string';
}
