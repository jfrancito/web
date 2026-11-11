<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\CMPContrato;
use Session;

class viewVentaSalidas extends Model
{
    //
    protected $table = 'viewVentaSalidas2024';
    public $timestamps = false;
    public $incrementing = false;
    public $keyType = 'string';

    public function scopeTipoMarca($query,$name){
    	if(trim($name) != 'TODOS'){
    		$query->where('TIPOMARCA.COD_CATEGORIA', '=', $name);
    	}
    }

    public function scopeCliente($query,$name){
        if(trim($name) != 'TODOS'){

            $query->where('Cliente', '=', $name);

        }else{
            
            $arrayempresa   =   CMPContrato::where('COD_CATEGORIA_CANAL_VENTA','=','CVE0000000000001')
                                ->where('COD_CATEGORIA_ESTADO_CONTRATO','=','ECO0000000000001')
                                ->groupBy(DB::raw('TXT_EMPR_CLIENTE'))
                                ->pluck('TXT_EMPR_CLIENTE')
                                ->toArray();
            $query->whereIn('Cliente',$arrayempresa);

        }
    }


}
