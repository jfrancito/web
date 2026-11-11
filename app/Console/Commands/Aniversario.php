<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\WEBIlog,App\WEBMaestro;
use Mail;

class Aniversario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aniversario:induamerica';
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


        // correos from(de)
        $emailfrom = WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00001')->first();
        // correos principales y  copias
        $email     = WEBMaestro::where('codigoatributo','=','0001')->where('codigoestado','=','00003')->first();

        $array = array(
            'aviso' => '¡Feliz Día del Ingeniero!'
        );

        Mail::send('emails.campanautiles', $array, function($message) use ($emailfrom,$email)
        {

            $emailprincipal     = explode(",", $email->correoprincipal);
            
            $message->from($emailfrom->correoprincipal, '¡Feliz Día del Ingeniero!');

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
