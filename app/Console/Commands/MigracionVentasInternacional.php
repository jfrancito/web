<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Mail;
use PDO;
use App\Biblioteca\Funcion;
use Hashids;

class MigracionVentasInternacional extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migracion:ventasinternacional';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            file_get_contents('http://10.1.50.2:8080/meta/migrar-ventas-internacional');
            //file_get_contents('http://localhost:81/meta/migrar-ventas');
    }
}
