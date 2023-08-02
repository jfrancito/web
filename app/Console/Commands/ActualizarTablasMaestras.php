<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\WEBIlog,App\WEBMaestro;
use Mail;

class ActualizarTablasMaestras extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:tablasmaestras';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar tablas maestras';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /********************************* DESACTIVAR REGLAS ****************************/
        $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.SCS_MERGE_CENTRAL_GLOBALES');
        $stmt->execute();
        $resultado = $stmt->fetch();

    }

}
