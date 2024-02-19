  <div id="ventas_s" class='ocultar'>{{$ventas_s}}</div>
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


        $('.titulo01').html('01 REPORTE VENTAS AUTOSERVICIO EN '+tituloban);
        $('.subtitulo0101').html(inicio + ' / '+ hoy);
        $('.subtitulo0102').html(simmodena + ' '+ totalimporte_s);


        chartaut.updateSeries([{
          data: aventas_s
        }]);
        chartaut.updateOptions({
           xaxis: {
              categories: acliente_s
           }
        });
    </script>
  @endif





