<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use View;
use Session;
use Hashids;
use DOMNode;
use DOMXPath;
use DOMElement;
use DOMDocument;
use DOMNodeList;
use Greenter\Parser\DocumentParserInterface;

use Greenter\Xml\Parser\InvoiceParser;
use Greenter\Xml\Parser\PerceptionParser;

class LeerxmlController extends Controller
{


    public function actionGenerarToken()
    {

      $apiInstance = new \Greenter\Sunat\ConsultaCpe\Api\AuthApi(
          new \GuzzleHttp\Client()
      );

      $grant_type = 'client_credentials'; // Constante
      $scope = 'https://api.sunat.gob.pe/v1/contribuyente/contribuyentes'; // Constante
      $client_id = '88ba4335-7528-4cdc-8e09-362d5feef8d3'; // client_id generado en menú sol
      $client_secret = 'BinvG3G0fZUMJHMzvommAw=='; // client_secret generado en menú sol
      try {
          $result = $apiInstance->getToken($grant_type, $scope, $client_id, $client_secret);
              
          echo 'Token: '.$result->getAccessToken().PHP_EOL;
          echo 'Expira: '.$result->getExpiresIn().' segundos'.PHP_EOL;
      } catch (Exception $e) {
          echo 'Excepcion cuando invocaba AuthApi->getToken: ', $e->getMessage(), PHP_EOL;
      }

    }

    public function actionConsultarCpe()
    {

        // Token generado en el ejemplo anterior
        $token = 'eyJraWQiOiJhcGkuc3VuYXQuZ29iLnBlLmtpZDEwMSIsInR5cCI6IkpXVCIsImFsZyI6IlJTMjU2In0.eyJzdWIiOiI4OGJhNDMzNS03NTI4LTRjZGMtOGUwOS0zNjJkNWZlZWY4ZDMiLCJhdWQiOiJbe1wiYXBpXCI6XCJodHRwczpcL1wvYXBpLnN1bmF0LmdvYi5wZVwiLFwicmVjdXJzb1wiOlt7XCJpZFwiOlwiXC92MVwvY29udHJpYnV5ZW50ZVwvY29udHJpYnV5ZW50ZXNcIixcImluZGljYWRvclwiOlwiMFwiLFwiZ3RcIjpcIjAxMDAwMFwifV19XSIsIm5iZiI6MTY2Nzg1MTk0MywiY2xpZW50SWQiOiI4OGJhNDMzNS03NTI4LTRjZGMtOGUwOS0zNjJkNWZlZWY4ZDMiLCJpc3MiOiJodHRwczpcL1wvYXBpLXNlZ3VyaWRhZC5zdW5hdC5nb2IucGVcL3YxXC9jbGllbnRlc2V4dHJhbmV0XC84OGJhNDMzNS03NTI4LTRjZGMtOGUwOS0zNjJkNWZlZWY4ZDNcL29hdXRoMlwvdG9rZW5cLyIsImV4cCI6MTY2Nzg1NTU0MywiZ3JhbnRUeXBlIjoiY2xpZW50X2NyZWRlbnRpYWxzIiwiaWF0IjoxNjY3ODUxOTQzfQ.r2QCObLqVBYJOWoLWg_VciNGM62wEVca3vTPeQrTuA8LGKMSELhumMLU6-i4OlutjXSyUA6uS9XSRgQnOPnqzBO7u6AX_J2zCgWYQtxSTB5ugn6O08qYcWMZY7zITca-NnNfeNX-1ItKXdey3uBNTGu-99g4mLd_ZEUKMkyN3CKNrwkGHZmKtBEBpqspONtF4282CiwZkrhq76MXl2OtYECzVCIKf0QpgDSD3a7lgmmDD9yxXWc_87QEyLnHAkirHsD3eDkz8LTRffzMxuQ5Jw5JhvXK-e5uudDbn-wmdqaslqLTvDvGWxhKC9w5F6lFyz6icoZnaooEmM1LwKBZhQ';

        $config = \Greenter\Sunat\ConsultaCpe\Configuration::getDefaultConfiguration()->setAccessToken($token);



        $apiInstance = new \Greenter\Sunat\ConsultaCpe\Api\ConsultaApi(
            new \GuzzleHttp\Client(),
            $config->setHost($config->getHostFromSettings(1))
        );



        $ruc = '20561347868'; // RUC de quién realiza la consulta

        $cpeFilter = (new \Greenter\Sunat\ConsultaCpe\Model\CpeFilter())
                    ->setNumRuc('20602379532') // RUC del emisor
                    ->setCodComp('01') // Tipo de comprobante
                    ->setNumeroSerie('F001')
                    ->setNumero('330')
                    ->setFechaEmision('26/08/2022')
                    ->setMonto('3423.84');

        try {
            $result = $apiInstance->consultarCpe($ruc, $cpeFilter);
            if (!$result->getSuccess()) {
                echo $result->getMessage();
                return;
            }

            $data = $result->getData();

            dd($data);

            switch ($data->getEstadoCp()) {
                case '0': echo 'NO EXISTE'; break;
                case '1': echo 'ACEPTADO'; break;
                case '2': echo 'ANULADO'; break;
                case '3': echo 'AUTORIZADO'; break;
                case '4': echo 'NO AUTORIZADO'; break;
            }

            echo PHP_EOL.'Estado RUC: '.$data->getEstadoRuc();
            echo PHP_EOL.'Condicion RUC: '.$data->getCondDomiRuc();

        } catch (Exception $e) {
            echo 'Excepcion cuando invocaba ConsultaApi->consultarCpe: ', $e->getMessage(), PHP_EOL;
        }

    }



    //
    public function actionLeerXml()
    {


      $path = storage_path() . "/exports/20100130204-01-FR01-01718724.xml";

      $parser = new InvoiceParser();
      $xml = file_get_contents($path);
      $factura = $parser->parse($xml);

      dd($factura);


        // $path = storage_path() . "/exports/20602379532-01-F001-330.xml";

        // $file = file_get_contents($path);
        // $xpt = $this->getXpath($file);

        // dd($xpt);

        // $invoice = self::constuctInvoice($xpt);

        // return $invoice;


    }

    public function loadXml($xml)
    {
        $doc = new \DOMDocument();
        @$doc->loadXML($xml);
        $this->loadDom($doc);
    }

}
