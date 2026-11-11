<?php
require __DIR__.'/../bootstrap/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fecha_inicio = '20230101';
$fecha_fin = '20231231';

$periodos_rango = DB::table('CON.PERIODO')
    ->where('COD_EMPR', 'IACHEM0000007086')
    ->whereRaw("CAST(FEC_INICIO AS DATE) >= ?", [$fecha_inicio])
    ->whereRaw("CAST(FEC_FIN AS DATE) <= ?", [$fecha_fin])
    ->pluck('COD_PERIODO')
    ->toArray();
    
print_r($periodos_rango);
