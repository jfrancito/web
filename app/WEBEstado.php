<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBEstado extends Model
{
    protected $table = 'WEB.estados';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';


}
