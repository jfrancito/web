<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\WEBRegla,App\WEBIlog,App\WEBMaestro,App\WEBOrdenDespacho,App\User,App\WEBReglaCreditoCliente;
use App\WEBDetalleOrdenDespacho;
use Mail;
use PDO;
use App\Biblioteca\Funcion;
use Hashids;

class SunatAviso extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sunat:aviso';
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


            $fecha_actual       =   date("Y-m-d");
            $array              =   Array();            
            $array              =   Array(
                                        'fecha_actual'      =>  $fecha_actual,
                                    );

            $emailfrom          =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00001')->first();
            // correos principales y  copias
            $email              =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00016')->first();


            Mail::send('emails.sunataviso', $array, function($message) use ($emailfrom,$email)
            {

                $emailprincipal     = explode(",", $email->correoprincipal);
                $message->from($emailfrom->correoprincipal, 'Sunat, envío de comprobantes electrónicos');

                if($email->correocopia<>''){
                    $emailcopias        = explode(",", $email->correocopia);
                    $message->to($emailprincipal)->cc($emailcopias);
                }else{
                    $message->to($emailprincipal);                
                }
                $message->subject($email->descripcion);

            });


                     
    }
}
