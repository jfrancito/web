<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\WEBRegla,App\WEBIlog,App\WEBMaestro,App\WEBOrdenDespacho,App\User,App\WEBReglaCreditoCliente;
use App\WEBDetalleOrdenDespacho;
use App\CMPDocumentoCtble;
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


            $lista_documento    =   CMPDocumentoCtble::join('ALM.CENTRO', 'ALM.CENTRO.COD_CENTRO', '=', 'CMP.DOCUMENTO_CTBLE.COD_CENTRO')
                                    ->join('STD.EMPRESA', 'STD.EMPRESA.COD_EMPR', '=', 'CMP.DOCUMENTO_CTBLE.COD_EMPR')
                                    ->where('CMP.DOCUMENTO_CTBLE.COD_ESTADO','=',1)
                                    ->where('CMP.DOCUMENTO_CTBLE.FEC_EMISION','>=',$fecha_actual)
                                    ->where('CMP.DOCUMENTO_CTBLE.FEC_EMISION','<=',$fecha_actual)
                                    ->where('CMP.DOCUMENTO_CTBLE.IND_ELECTRONICO','=',1)
                                    ->where('CMP.DOCUMENTO_CTBLE.COD_CATEGORIA_ESTADO_DOC_CTBLE','=','EDC0000000000001')
                                    ->whereIn('CMP.DOCUMENTO_CTBLE.COD_CATEGORIA_TIPO_DOC',['TDO0000000000001' ,'TDO0000000000003','TDO0000000000007','TDO0000000000008'])
                                    ->select('NRO_SERIE','NRO_DOC','STD.EMPRESA.NOM_EMPR','ALM.CENTRO.NOM_CENTRO','CMP.DOCUMENTO_CTBLE.COD_USUARIO_CREA_AUD',
                                        'TXT_CATEGORIA_TIPO_DOC')
                                    ->get();



            $array              =   Array(
                                        'fecha_actual'      =>  $fecha_actual,
                                        'lista_documento'   =>  $lista_documento,
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
