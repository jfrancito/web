<!DOCTYPE html>
<html lang="es">

<head>
  <title>{{$titulo}}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="icon" type="image/x-icon" href="{{ asset('public/favicon.ico') }}"> 
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/pdf.css') }} "/>

</head>
<body>
    <header>
      <div class='reporte'>
        <h3 class="center titulo">{{$empresa}} - {{$centro}}</h3>
        <p class="subtitulo">
          <strong class='fecha'>Día : {{date_format(date_create($fechafin), 'd-m-Y')}}</strong>
        </p>
      </div>
    </header>
    <section>
        <article>
        <table id="tablereporte" class="table table-striped table-hover table-fw-widget">
            <thead>
            <tr>
                <th class= 'tabladp'>Fecha</th>             
                <th class= 'center tabladp'>Doc. Referencia</th> 
                <th class= 'center tabladp'>Tipo</th>
                <th class= 'center tabladp'>Detracción</th>
            </tr>
            </thead>
            <tbody>

            @php
                $total          =   0.0000;
            @endphp

            @foreach($listadetraccion as $index => $item) 
                        <tr>

                        <td>{{$item->FEC_DETRACCION}}</td>

                        <td class='left negrita'>
                            {{$item->DOC_REFERENCIA}}
                        </td>

                        <td class='left negrita'>
                            @if($item->IND_DOC == 'GRR') 
                                GUÍA
                            @else
                                FACTURA 
                            @endif                     
                        </td>

                        <td class='left negrita'>
                            S/. {{number_format($item->CAN_DETRACCION,2,'.',',')}}
                        </td>

                        @php
                        $total          =   $total + $item->CAN_DETRACCION
                    @endphp

                        </tr>
            @endforeach       

            <tr>
                <th></th>             
                <th></th> 
                <th>TOTAL</th>
                <th>S/. {{ number_format($total,2,'.',',') }}</th>
            </tr>

            </tbody>
        </table>
        </article>
    </section>
</body>
</html>