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

class ContratosPendiente extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contrato:pendiente';
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
            //$fecha_actual       =   '2021-01-07';
            $fechadia           =   date_format(date_create(date('d-m-Y')), 'd-m-Y');
            $titulo             =   'Contrato-Pendiente';
            $nombre_archivo     =   $titulo.'-('.$fecha_actual.').xls';
            $file               =   storage_path(). "/exports/".$nombre_archivo;
            $array              =   Array();

            file_get_contents('http://10.1.50.2:8080/web/contrato-pendiente');
            //file_get_contents('http://localhost:81/web/contrato-pendiente');

            $emailfrom          =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00001')->first();
            // correos principales y  copias
            $email              =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00017')->first();

            
            $array      =  Array(
                                    'fecha_actual'      =>  $fecha_actual,
                                );


            Mail::send('emails.notificacioncontratopendiente', $array, function($message) use ($emailfrom,$email,$file,$nombre_archivo,$fecha_actual)
            {

                $emailprincipal     = explode(",", $email->correoprincipal);
                $message->from($emailfrom->correoprincipal, 'Correo Induamerica')->attach($file, [
                        'as' => $nombre_archivo,
                        'mime' => 'application/xls',
                    ]);

                if($email->correocopia<>''){
                    $emailcopias        = explode(",", $email->correocopia);
                    $message->to($emailprincipal)->cc($emailcopias);
                }else{
                    $message->to($emailprincipal);                
                }
                $message->subject($email->descripcion.' '.$fecha_actual);

            });

            dd("hola");
    }
}
