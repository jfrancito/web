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
            .fondogris{
                background: #cce6fd;
                text-align: center;
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
                    <div class="panelhead">Autorizaciones de NC/NCI Pendientes hasta {{$fecha_actual}}</div>
                    <div class='panelbody'>
                        <table  class="table demo" >
                            <tr>
                                <th>
                                    Empresa
                                </th>
                                <th>
                                    Fecha registro
                                </th>
                                <th>
                                    Usuario Solicitante
                                </th>
                                <th>
                                    Cliente
                                </th>
                                <th>
                                    Tipo Documento
                                </th>
                                <th>
                                    Moneda
                                </th>
                                <th>
                                    Material/Servicio
                                </th>
                                <th>
                                    Glosa
                                </th>
                            </tr>

                        @foreach($lista_autorizacion as $index => $item)
                            <tr>
                                <td>
                                    {{$item->NOM_EMPR}}
                                </td>
                                <td>
                                    {{$item->FEC_REGISTRO}}
                                </td>
                                <td>
                                    {{$item->TXT_USUARIO_SOLICITA}}
                                </td>
                                <td>
                                    {{$item->TXT_EMPR_CLIENTE}}
                                </td>
                                <td>
                                    {{$item->TXT_CATEGORIA_TIPO_DOC}}
                                </td>
                                <td>
                                    {{$item->TXT_CATEGORIA_MONEDA}}
                                </td>
                                <td>
                                    {{$item->MAT_SER}}
                                </td>
                                <td>
                                    {{$item->TXT_GLOSA_INTERNO}}
                                </td>

                            </tr>
                        @endforeach

                        </table>
                    </div>


                </div>
            </div>
        </section>
    </body>

</html>


