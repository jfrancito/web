<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBTransferencia extends Model
{
    protected $table = 'WEB.transferencia';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';


    public function empresa()
    {
        return $this->belongsTo('App\STDEmpresa', 'cliente_id', 'COD_EMPR');
    }
    public function estadocat()
    {
        return $this->belongsTo('App\CMPCategoria', 'estado_id', 'COD_CATEGORIA');
    }

    public function transferenciadetalle()
    {
        return $this->hasMany('App\WEBTransferenciaDetalle', 'transferencia_id', 'id')->where('activo','=', 1);
    }

    public function scopeCentro($query,$centro){

        if(trim($centro) == 'CEN0000000000002'){
            $query->where('centro_id','=',$centro);
        }else{
            $query->whereIn('centro_id', ['CEN0000000000001','CEN0000000000004','CEN0000000000006']);
        }
        
    }

    
}
