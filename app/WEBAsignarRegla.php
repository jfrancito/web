<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBAsignarRegla extends Model
{
    protected $table = 'WEB.asignarreglas';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';


}
