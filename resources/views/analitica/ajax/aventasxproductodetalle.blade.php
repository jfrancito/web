<div id="chart02">
</div>

<input type="text" name="anio" id="anio" value='{{$anio}}' class='ocultar'>
<div id="meses" class='ocultar'>{{$meses}}</div>
<div id="ventas" class='ocultar'>{{$ventas}}</div>
<div id="tnc" class='ocultar'>{{$tnc}}</div>
<div id="prod" class='ocultar'>{{$jprod}}</div>
<div id="color" class='ocultar'>{{$jcol}}</div>

<div id="anio" class='ocultar'>{{$anio}}</div>
<div id="mes" class='ocultar'>{{$mes}}</div>


@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){

      var carpeta = $("#carpeta").val();
      var empresa_nombre = $('select[name="empresa_nombre"] option:selected').text();
      var mes = $('#mes').html();
      var anio = $('#anio').val();
      var meses = $('#meses').html();
      var ventas = $('#ventas').html();
      var tnc = $('#tnc').html();
      var prod = $('#prod').html();
      var color = $('#color').html();
      const ameses  = JSON.parse(meses);
      const aventas = JSON.parse(ventas);
      const atnc = JSON.parse(tnc);
      const aprod = JSON.parse(prod);
      const acolor = JSON.parse(color);
      var options = {
          series: aventas,
          colors:acolor,
          chart: {
            width: 350,
            height: 800,
            type: 'pie'
          },
          labels: aprod,
          yaxis: {
            show: false
          },
          legend: {
            position: 'bottom'
          },
      };

      var chart = new ApexCharts(document.querySelector("#chart02"), options);
      chart.render();


    });
  </script> 
@endif