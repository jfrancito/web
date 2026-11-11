<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\WEBRegla,App\WEBIlog,App\WEBMaestro,App\WEBOrdenDespacho,App\User,App\WEBReglaCreditoCliente;
use App\Autorizarnotacredito;
use Mail;
use PDO;
use App\Biblioteca\Funcion;
use Hashids;

class AutorizarNotaCreditoLima extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autorizar:notacredito';
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
            $array              =   Array();

            $emailfrom          =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00001')->first();
            $email              =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00018')->first();

        
            /****************************************   LIMA    *********************************************************/
            $lista_autorizacion =   Autorizarnotacredito::where('COD_CENTRO','=','CEN0000000000002')
                                    ->get();


            if(count($lista_autorizacion)>0){
                $array      =  Array(
                                        'fecha_actual'            =>  $fecha_actual,
                                        'lista_autorizacion'      =>  $lista_autorizacion,
                                    );

                Mail::send('emails.autorizarnotacredito', $array, function($message) use ($emailfrom,$email,$fecha_actual)
                {

                    $emailprincipal     = explode(",", $email->correoprincipal);
                    $message->from($emailfrom->correoprincipal, 'Autorizaciones de NC/NCI Pendientes hasta'.' '.$fecha_actual);
                    if($email->correocopia<>''){
                        $emailcopias        = explode(",", $email->correocopia);
                        $message->to($emailprincipal)->cc($emailcopias);
                    }else{
                        $message->to($emailprincipal);                
                    }
                    $message->subject($email->descripcion);

                });



            }

            $email              =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00019')->first();

            /****************************************   CHICLAYO    *********************************************************/
            $lista_autorizacion =   Autorizarnotacredito::where('COD_CENTRO','=','CEN0000000000001')
                                    ->get();

            if(count($lista_autorizacion)>0){

                $array      =  Array(
                                        'fecha_actual'            =>  $fecha_actual,
                                        'lista_autorizacion'      =>  $lista_autorizacion,
                                    );

                Mail::send('emails.autorizarnotacredito', $array, function($message) use ($emailfrom,$email,$fecha_actual)
                {

                    $emailprincipal     = explode(",", $email->correoprincipal);
                    $message->from($emailfrom->correoprincipal, 'Autorizaciones de NC/NCI Pendientes hasta'.' '.$fecha_actual);
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
}
