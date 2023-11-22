<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WEBOrdenDespacho extends Model
{
    protected $table = 'WEB.ordendespachos';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function detalleordendespacho()
    {
        return $this->hasMany('App\WEBDetalleOrdenDespacho', 'ordendespacho_id', 'id')->where('activo','=', 1)
                                                                                      ->orderBy('grupo_movil', 'asc')
                                                                                      ->orderBy('grupo', 'asc')
                                                                                      ->orderBy('id', 'asc');
    }

    public function viewdetalleordendespacho()
    {
        return $this->hasMany('App\WEBViewDetalleOrdenDespacho', 'ordendespacho_id', 'id')->where('activo','=', 1)
        ->orderBy('grupo_movil', 'asc')
        //->orderBy('grupo', 'asc')
        //->orderBy('id', 'asc')
        ->orderBy('grupo_guia', 'asc')
        ->orderBy('id', 'asc');
    }


    public function viewdetalleordendespachosinmuestra()
    {
        return $this->hasMany('App\WEBViewDetalleOrdenDespachoSinMuestra', 'ordendespacho_id', 'id')->where('activo','=', 1)
        ->orderBy('grupo_movil', 'asc');
        //->orderBy('id', 'asc');
    }

    public function empresa()
    {
        return $this->belongsTo('App\STDEmpresa', 'empresa_id', 'COD_EMPR');
    }


    public function centro()
    {
        return $this->belongsTo('App\ALMCentro', 'centro_id', 'COD_CENTRO');
    }


    public function scopeEmpresaCentro($query,$name){
        $query->whereRaw("1 = ".$name);
    }

    
}
