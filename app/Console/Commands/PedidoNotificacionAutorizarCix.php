<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\WEBRegla,App\WEBIlog,App\WEBMaestro,App\WEBPedido,App\User,App\STDEmpresaDireccion,App\WEBReglaCreditoCliente;
use Mail;
use App\Traits\WhatsappTraits;

class PedidoNotificacionAutorizarCix extends Command
{
    use WhatsappTraits;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedidonotificacion:autorizarcix';
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


        $lista_pedidos                  =   WEBPedido::where('WEB.pedidos.ind_notificacion_autorizacion','=',0)
                                            ->whereIn('WEB.pedidos.centro_id', ['CEN0000000000001','CEN0000000000006','CEN0000000000004'])
                                            ->get();


        foreach($lista_pedidos as $item){

                // correos from(de)
            $emailfrom          =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00001')->first();
            // correos principales y  copias
            $email              =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00012')->first();

            $direccion          =   STDEmpresaDireccion::where('COD_DIRECCION','=',$item->direccionentrega->COD_DIRECCION)->first();

            $vendedor           =   User::where('id','=',$item->usuario_crea)->first();
            $correorv           =   $vendedor->email;

            $saldocli           =   DB::select('exec WEB.SALDO_TRAMO_CUENTA ?,?,?,?,?,?,?,?,?,?,?', array('','','','',date("Y-m-d"),$item->cliente_id,'TCO0000000000068','','','',''));


            $limite_credito     =   WEBReglaCreditoCliente::where('cliente_id','=',$item->cliente_id)->first();
            $tipo_operacion     =   'SEL';
            $fecha_dia          =   date_format(date_create(date('Y-m-d')), 'Y-m-d');
            //$deuda_antigua      =   DB::select('exec WEB.DEUDA_MAS_ANTIGUA_CLIENTE ?,?', array($fecha_dia,$item->cliente_id));
            $deuda_antigua      =   DB::select('exec WEB.DEUDA_MAS_ANTIGUA_CLIENTE ?,?,?,?,?,?,?,?,?,?,?', array('','','','',date("Y-m-d"),$item->cliente_id,'TCO0000000000068','','','',''));


            $array              =   Array(
                'NP'                =>  $item,
                'saldo'             =>  $saldocli,
                'vendedor'          =>  $vendedor,
                'detalle'           =>  $item->detallepedido,
                'direccion'         =>  $direccion,
                'limite_credito'    =>  $limite_credito,
                'deuda_antigua'     =>  $deuda_antigua
            );

            $codigo             =   $item->codigo;

            $mensaje            =   'NOTA DE PEDIDO : '.$item->codigo.'%0D%0A'.'Solicitud : '.$vendedor->nombre.'%0D%0A'.'Cliente : '.$item->empresa->NOM_EMPR;
            $this->insertar_whatsaap('51979820173','JORGE FRANCELLI',$mensaje,'');
            $this->insertar_whatsaap('51977197023','PAUCAR',$mensaje,'');
            $this->insertar_whatsaap('51943516853','JOSE LUIS',$mensaje,'');
            $this->insertar_whatsaap('51914693880','JOSE CHERO',$mensaje,'');

            Mail::send('emails.notificacionautorizacion', $array, function($message) use ($emailfrom,$email,$correorv,$codigo)
            {

                $emailprincipal     = explode(",", $email->correoprincipal.','.$correorv);
                //$emailprincipal     = explode(",", $email->correoprincipal);
        
                $message->from($emailfrom->correoprincipal, 'El pedido '.' '.$codigo.' fue autorizado.');

                if($email->correocopia<>''){
                    $emailcopias        = explode(",", ltrim(rtrim($email->correocopia)));
                    $message->to($emailprincipal)->cc($emailcopias);
                }else{
                    $message->to($emailprincipal);                
                }
                $message->subject($email->descripcion);

            });

            $pedido                                     =   WEBPedido::where('WEB.pedidos.id','=',$item->id)->first();
            $pedido->ind_notificacion_autorizacion      =   1;
            $pedido->save();
            

        }
                     
    }
}
