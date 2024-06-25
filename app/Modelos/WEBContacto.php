<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBContacto extends Model
{
    protected $table = 'WEB.contactos';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

}