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

                    <div class="panelhead">Lista de documentos pendientes a emitir</div>
                    <div class='panelbody'>
                            <table  class="table demo" >
                                <tr>
                                    <th>
                                        NRO SERIE
                                    </th>
                                    <th>
                                        NRO DOC
                                    </th>
                                    <th>
                                        NOMBRE EMPRESA
                                    </th>
                                    <th>
                                        NOMBRE CENTRO
                                    </th>
                                    <th>
                                        USUARIO CREA
                                    </th>
                                    <th>
                                        TIPO DOCUMENTO
                                    </th>
                                       
                                </tr>
                                @foreach($lista_documento as $index=>$item)
                                <tr>

                                        <td>{{$item->NRO_SERIE}}</td>
                                        <td>{{$item->NRO_DOC}}</td>
                                        <td>{{$item->NOM_EMPR}}</td>
                                        <td>{{$item->NOM_CENTRO}}</td>
                                        <td>{{$item->COD_USUARIO_CREA_AUD}}</td>
                                        <td>{{$item->TXT_CATEGORIA_TIPO_DOC}}</td>

                                </tr>
                                @endforeach
                            </table>
                    </div>
                </div>
            </div>
        </section>
    </body>

</html>


