<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBDocAsocDetraccion extends Model
{
    protected $table = 'WEB.DOC_ASOC_DETRACCION';
    public $timestamps=false;

    protected $primaryKey = 'COD_DOC_ASOC';
	public $incrementing = false;
	public $keyType = 'string';

}
