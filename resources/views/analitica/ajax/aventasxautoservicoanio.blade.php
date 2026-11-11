  <div id="ventas_s" class='ocultar'>{{$ventas_s}}</div>
  <div id="ventas2_s" class='ocultar'>{{$ventas2_s}}</div>
  <div id="tituloban" class='ocultar'>{{$tituloban}}</div>
  <div id="tnc_s" class='ocultar'>{{$tnc_s}}</div>
  <div id="prod_s" class='ocultar'>{{$jprod_s}}</div>
  <div id="color_s" class='ocultar'>{{$jcol_s}}</div>
  <div id="costos_s" class='ocultar'>{{$jcostos_s}}</div>
  <div id="utilidad_s" class='ocultar'>{{$jutilidad_s}}</div>
  <div id="jtotal_s" class='ocultar'>{{$jtotal_s}}</div>
  <div id="simmodena" class='ocultar'>{{$simmodena}}</div>
  <div id="cliente_s" class='ocultar'>{{$jcliente_s}}</div>
  <div id="count" class='ocultar'>1</div>
  <div id="inicio" class='ocultar'>{{$inicio}}</div>
  <div id="hoy" class='ocultar'>{{$hoy}}</div>
  <div id="totalimporte_s" class='ocultar'>{{$totalimporte_s}}</div>
  <div id="anio01" class='ocultar'>{{$selec_anioini}}</div>
  <div id="anio02" class='ocultar'>{{$selec_aniofin}}</div>
  <div id="meses_s" class='ocultar'>{{$meses_s}}</div>
  <div id="selec_clientetitulo" class='ocultar'>{{$selec_cliente}}</div>
  <div id="total01" class='ocultar'>{{$total01}}</div>
  <div id="total02" class='ocultar'>{{$total02}}</div>

  @if(isset($ajax))
    <script type="text/javascript">


        var   ventas_s          = $('#ventas_s').html();
        var   tituloban         = $('#tituloban').html();
        const aventas_s         = JSON.parse(ventas_s);
        var   cliente_s         = $('#cliente_s').html();
        const acliente_s        = JSON.parse(cliente_s);
        var   inicio            = $('#inicio').html();
        var   hoy               = $('#hoy').html();
        var   simmodena         = $('#simmodena').html();
        var   totalimporte_s    = $('#totalimporte_s').html();
        var   ventas2_s         = $('#ventas2_s').html();
        const aventas2_s        = JSON.parse(ventas2_s);
        var   anio01            = $('#anio01').html();
        var   anio02            = $('#anio02').html();
        var   meses_s           = $('#meses_s').html();
        const ameses_s          = JSON.parse(meses_s);
        var   selec_cliente     = $('#selec_clientetitulo').html();


        var   total01           = $('#total01').html();
        var   total02           = $('#total02').html();

        var data_total01        = new oNumero(total01);
        data_total01            = data_total01.formato(2, true);
        var data_total02        = new oNumero(total02);
        data_total02            = data_total02.formato(2, true);


        var data_total          = new oNumero(totalimporte_s);
        data_total              = data_total.formato(2, true);

        $('.titulo01').html('01 REPORTE VENTAS AUTOSERVICIO EN '+tituloban);
        $('.subtitulo0101').html(anio01 + ' / '+ anio02);
        $('.subtitulo0102').html(simmodena + ' '+ data_total);
        $('.subtitulo0103').html(selec_cliente);


        chartanio.updateSeries(
            [{
                name: anio01 +' : ' + simmodena + data_total01,
                data: aventas_s
              }, {
                name: anio02 +' : ' + simmodena + data_total02,
                data: aventas2_s
            }]
        );

        chartanio.updateOptions({
           xaxis: {
              categories: ameses_s
           }
        });







    </script>
  @endif





