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

class PedidoxEstado extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedido:estado';
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
            $titulo             =   'Pedido-x-Estado';
            $nombre_archivo     =   $titulo.'-('.$fecha_actual.').xls';
            $file               =   storage_path(). "/exports/".$nombre_archivo;
            $array              =   Array();

            file_get_contents('http://10.1.50.2:8080/web/pedido-estado-excel-automatico');

            $emailfrom          =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00001')->first();
            // correos principales y  copias
            $email              =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00015')->first();

            $array      =  Array(
                                    'fecha_actual'      =>  $fecha_actual,
                                );


            Mail::send('emails.notificacionpedidoxestado', $array, function($message) use ($emailfrom,$email,$file,$nombre_archivo,$fecha_actual)
            {

                $emailprincipal     = explode(",", $email->correoprincipal);
                $message->from($emailfrom->correoprincipal, 'Pedido x estado'.' '.$fecha_actual)->attach($file, [
                        'as' => $nombre_archivo,
                        'mime' => 'application/xls',
                    ]);

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
