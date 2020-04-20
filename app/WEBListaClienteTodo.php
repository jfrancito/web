<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;
class WEBListaClienteTodo extends Model
{
    protected $table = 'WEB.LISTACLIENTETODOS';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

}
