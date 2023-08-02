<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <style type="text/css">
            section{
                width: 100%;
                background: #E8E8E8;
                padding: 0px;
                margin: 0px;
            }

            .panelcontainer{
                width: 50%;
                background: #fff;
                margin: 0 auto;


            }
            .panelhead{
                background: #eb6357;
                padding-top: 10px;
                padding-bottom: 10px;
                color: #fff;
                text-align: center;
                font-size: 1.2em;
            }
            .panelbody,.panelbodycodigo{
                padding-left: 15px;
                padding-right: 15px;
            }
            .panelbodycodigo h3 small{
                color: #08257C;
            }

            table, td, th {    
                border: 1px solid #ddd;
                text-align: left;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            th, td {
                padding: 15px;
                font-size: 12px;
            }

        </style>

    </head>


    <body>
        <section>
            <div class='panelcontainer'>
                <div class="panel">

                    <div class="panelhead">Lista de documentos sin enviar</div>
<!--                     <div class='panelbody'>
                            <table  class="table demo" >
                                <tr>
                                    <th>
                                        EMPR_EMISOR
                                    </th>
                                    <th>
                                        COD_DOCUMENTO_CTBLE
                                    </th>
                                    <th>
                                        TIPO_DOCTIPO_DOC
                                    </th>
                                    <th>
                                        NRO_SERIE
                                    </th>
                                    <th>
                                        NRO_DOC
                                    </th>
                                    <th>
                                        CLIENTE
                                    </th>
                                    <th>
                                        FEC_EMISION
                                    </th>
                                    <th>
                                        ESTADO_DOC_CTBLE
                                    </th>
                                    <th>
                                        NOM_TRABAJADOR
                                    </th>  
                                </tr>
                                @foreach($lista_documento as $index=>$item)
                                <tr>

                                        <td>{{$item->EMPR_EMISOR}}</td>
                                        <td>{{$item->COD_DOCUMENTO_CTBLE}}</td>
                                        <td>{{$item->TIPO_DOC}}</td>
                                        <td>{{$item->NRO_SERIE}}</td>
                                        <td>{{$item->NRO_DOC}}</td>
                                        <td>{{$item->CLIENTE}}</td>
                                        <td>{{$item->FEC_EMISION}}</td>
                                        <td>{{$item->ESTADO_DOC_CTBLE}}</td>
                                        <td>{{$item->NOM_TRABAJADOR}}</td>

                                </tr>
                                @endforeach
                            </table>
                    </div> -->
                </div>
            </div>
        </section>
    </body>

</html>


