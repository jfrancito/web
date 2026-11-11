<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Session;

class VentaBono extends Model
{
    //
    protected $table = 'VentaBono';
    public $timestamps = false;
    public $incrementing = false;
    public $keyType = 'string';
}
