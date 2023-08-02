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

class DespachoNotificacionNuevo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'despachonotificacion:nuevo';
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



        $fecha_actual                   =   date("Y-m-d");
        $fecha_manana                   =   date("Y-m-d",strtotime($fecha_actual."+ 1 days"));

        $lista_pedidos                  =   WEBOrdenDespacho::join('CMP.CATEGORIA','CMP.CATEGORIA.COD_CATEGORIA','=','WEB.ordendespachos.estado_id')
                                            ->where('ind_notificacion','=',0)
                                            //->where('fecha_orden','=', $fecha_actual)
                                            ->orderBy('fecha_crea', 'asc')
                                            ->get();

        $this->funciones                =   new Funcion();

        //dd(count($lista_pedidos));

        foreach($lista_pedidos as $item){

            $nombre_archivo     =   'Pedido-despacho-'.$item->codigo.'.xls';
            $file               =   storage_path(). "/exports/".$nombre_archivo;
            $array              =   Array();

            file_get_contents('http://10.1.50.2:8080/web/excel-orden-despacho-email/Op/'.Hashids::encode(substr($item->id, -8)));

            //file_get_contents('http://localhost:81/web/excel-orden-despacho-email/Op/'.Hashids::encode(substr($item->id, -8)));
            // correos from(de)
            $emailfrom          =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00001')->first();
            // correos principales y  copias
            $email              =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00009')->first();

            $codigo             =   $item->codigo;
                
            $array      =  Array(
                                    'pedido'            =>  $item,
                                    'codigo'            =>  $codigo,
                                    'funcion'           =>  $this->funciones
                                );


            Mail::send('emails.notificaciondespachonuevo', $array, function($message) use ($emailfrom,$email,$codigo,$file,$nombre_archivo)
            {

                $emailprincipal     = explode(",", $email->correoprincipal);
                $message->from($emailfrom->correoprincipal, 'Pedido despacho registrado'.' '.$codigo)->attach($file, [
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


            $pedido                         =   WEBOrdenDespacho::where('id','=',$item->id)->first();
            $pedido->ind_notificacion       =   1;
            $pedido->save();

        }
                     
    }
}
