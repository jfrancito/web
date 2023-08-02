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
            //$fecha_actual       =   '2021-01-07';
            $fechadia           =   date_format(date_create(date('d-m-Y')), 'd-m-Y');
            $titulo             =   'documentos-sin-enviar';
            $nombre_archivo     =   $titulo.'-('.$fecha_actual.').xls';
            $file               =   storage_path(). "/exports/".$nombre_archivo;
            $array              =   Array();


            $lista_documento    =   CMPDocumentoCtble::leftJoin('SGD.USUARIO','SGD.USUARIO.COD_USUARIO','=','CMP.DOCUMENTO_CTBLE.COD_USUARIO_CREA_AUD')
                                    ->whereRaw("LEFT(CMP.DOCUMENTO_CTBLE.NRO_SERIE ,1) in ('F','B')")
                                    ->whereRaw("YEAR(CMP.DOCUMENTO_CTBLE.FEC_EMISION)>2021")
                                    ->whereRaw("CMP.DOCUMENTO_CTBLE.COD_CATEGORIA_TIPO_DOC IN ('TDO0000000000001','TDO0000000000003','TDO0000000000007','TDO0000000000008')")
                                    ->where('CMP.DOCUMENTO_CTBLE.ESTADO_ELEC','=','C')
                                    ->where('CMP.DOCUMENTO_CTBLE.COD_ESTADO','=',1)
                                    ->where('CMP.DOCUMENTO_CTBLE.IND_COMPRA_VENTA','=','V')
                                    ->select(DB::raw('CMP.DOCUMENTO_CTBLE.TXT_EMPR_EMISOR as EMPR_EMISOR,CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE,CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_TIPO_DOC AS TIPO_DOC,CMP.DOCUMENTO_CTBLE.NRO_SERIE,CMP.DOCUMENTO_CTBLE.NRO_DOC,TXT_EMPR_RECEPTOR as CLIENTE,CMP.DOCUMENTO_CTBLE.FEC_EMISION,CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_ESTADO_DOC_CTBLE as ESTADO_DOC_CTBLE,SGD.USUARIO.NOM_TRABAJADOR'))

                                    ->orderBy('CMP.DOCUMENTO_CTBLE.FEC_EMISION', 'ASC')
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', 'desc')
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.NRO_SERIE', 'desc')
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.NRO_DOC', 'desc')
                                    ->get();

            file_get_contents('http://10.1.50.2:8080/web/documentos-sin-enviar-excel-automatico');
            //file_get_contents('http://localhost:81/web/documentos-sin-enviar-excel-automatico');



            $array              =   Array(
                                        'fecha_actual'      =>  $fecha_actual,
                                        'lista_documento'   =>  $lista_documento,
                                    );

            $emailfrom          =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00001')->first();
            // correos principales y  copias
            $email              =   WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00016')->first();

            Mail::send('emails.sunataviso', $array, function($message) use ($emailfrom,$email,$file,$nombre_archivo)
            {

                $emailprincipal     = explode(",", $email->correoprincipal);
                $message->from($emailfrom->correoprincipal, 'Sunat, documentos sin enviar')->attach($file, [
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
