<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />

        <style>

            body{

            }
            .banner{
                margin: 0 auto;
                text-align: center;
                width: 700px;               
            }
            p{
                margin-bottom: 0px;
                margin-top: 0px;
                text-align: left;
            }
            .titulo{
                margin-bottom: 3px;
                margin-top: 3px;
                font-size: 1.2em;
            }
            .titulo a{
                color: #000000;
                font-size: 0.8em;
            }
            .jefatura{
                margin-bottom: 6px;
                margin-top: 3px;               
            }
            .subtitulo{
                margin-top: 3px;
                font-size: 1em;
                padding-left: 10px;
                font-style: italic;
            }
            h1{
                text-decoration:underline;
                margin-bottom: 8px;
            }

        </style>


    </head>


    <body>
        <section>
            <div class='banner'>
                <h1>Papás Induamerica</h1>

                <table  bgcolor="#ffffff" >

                    <tr>
                        <td width='500'>
                            <p class='titulo'>
                            
                                Queremos agradecerles su comprometida dedicación por hacer de nuestra empresa la número uno 
                                y por seguir adelante con las mismas ganas de hacer de este mundo un lugar mejor para su familia. 
                            
                            </p>
                            <br>
                            <br>
                            <p class='titulo'><b>¡Feliz día del Padre, queridos colaboradores!</b></p>

                        </td>
                    </tr>

                    <tr>
                        <td width='500'>
                            <img src="{{ $message->embed('http://www.induamerica.com.pe/imgcorreo/diapadre.png')}}" alt="Banner" />
                        </td>
                    </tr>


                </table>
            </div>            
        </section>
    </body>

</html>


