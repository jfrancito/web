@if($totalimporte>0 && $totalimporte_s>0)

  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
      <h4 class="titulochar">Ventas Generales (S/. {{number_format($totalimporte, 2, '.', ',')}})</h4>
      <div id="chart01" >
      </div>
  </div>  
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <h4 class="titulochar">Ventas Atendidas (S/. {{number_format($totalimporte_s, 2, '.', ',')}})</h4>
      <div id="chart02" >
      </div>
  </div>  


  <input type="text" name="anio" id="anio" value='{{$anio}}' class='ocultar'>

  <div id="meses" class='ocultar'>{{$meses}}</div>
  <div id="anio" class='ocultar'>{{$anio}}</div>
  <div id="mes" class='ocultar'>{{$mes}}</div>
  <div id="empresa_nombre_text" class='ocultar'>{{$empresa_nombre}}</div>
  <div id="periodo_sel" class='ocultar'>{{$periodo_sel}}</div>
  <div id="tipomarca_txt" class='ocultar'>{{$tipomarca_txt}}</div>
  <div id="totalimporte" class='ocultar'>{{$totalimporte}}</div>


  <div id="ventas" class='ocultar'>{{$ventas}}</div>
  <div id="tnc" class='ocultar'>{{$tnc}}</div>
  <div id="prod" class='ocultar'>{{$jprod}}</div>
  <div id="color" class='ocultar'>{{$jcol}}</div>


  <div id="ventas_s" class='ocultar'>{{$ventas_s}}</div>
  <div id="tnc_s" class='ocultar'>{{$tnc_s}}</div>
  <div id="prod_s" class='ocultar'>{{$jprod_s}}</div>
  <div id="color_s" class='ocultar'>{{$jcol_s}}</div>


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
        var empresa_nombre_text = $('#empresa_nombre_text').html();
        var periodo_sel = $('#periodo_sel').html();
        var tipomarca_txt = $('#tipomarca_txt').html();
        var totalimporte = $('#totalimporte').html();
        var data_totalimporte = new oNumero(totalimporte);
        data_totalimporte  = data_totalimporte.formato(2, true);
        $('.total-pedido').html('S/. '+data_totalimporte);
        var options = {
            series: aventas,
            colors:acolor,
            chart: {
              width: 350,
              height: 800,
              type: 'pie',
              events: {
                dataPointSelection: (event, chartContext, config) => {

                  var periodo             = $('#periodo').val();
                  var tipomarca           = $('#tipomarca').val();

                  const marca = chartContext.w.globals.labels[config.dataPointIndex];
                  actualizar_ajax_det_producto(anio,empresa_nombre,mes,carpeta,marca,periodo,tipomarca);
                }
              },
            },
            title: {
                text: empresa_nombre_text,
                align: 'center',
                margin: 0,
                offsetX: 0,
                offsetY: 0,
                floating: false,
                style: {
                  fontSize:  '14px',
                  fontWeight:  'bold',
                  fontFamily:  undefined,
                  color:  '#263238'
                },
            },
            subtitle: {
                text: periodo_sel + ' / ' + tipomarca_txt,
                align: 'center',
                margin: 25,
                offsetX: 0,
                offsetY: 20,
                floating: false,
                style: {
                  fontSize:  '12px',
                  fontWeight:  'normal',
                  fontFamily:  undefined,
                  color:  '#9699a2'

                },
            },
            dataLabels: {
              formatter(val, opts) {
                const name = opts.w.globals.labels[opts.seriesIndex]
                return [name, val.toFixed(1) + '%']
              }
            },
            labels: aprod,
            yaxis: {
              show: false
            },
            legend: {
              position: 'bottom',
              horizontalAlign: 'left',
              fontSize: '10px',
              fontWeight: 600, 

              formatter: function(label, opts) {
                  const total = opts.w.globals.series[opts.seriesIndex];
                  var data_total = new oNumero(total);
                  data_total  = data_total.formato(2, true);
                  return label + "  S/." + data_total
              }
            },
        };
        var chart = new ApexCharts(document.querySelector("#chart01"), options);
        chart.render();


        var ventas_s = $('#ventas_s').html();
        var prod_s = $('#prod_s').html();
        var color_s = $('#color_s').html();
        const aventas_s = JSON.parse(ventas_s);
        const aprod_s = JSON.parse(prod_s);
        const acolor_s = JSON.parse(color_s);
        var options2 = {
            series: aventas_s,
            colors: acolor_s,
            title: {
                text: empresa_nombre_text,
                align: 'center',
                margin: 0,
                offsetX: 0,
                offsetY: 0,
                floating: false,
                style: {
                  fontSize:  '14px',
                  fontWeight:  'bold',
                  fontFamily:  undefined,
                  color:  '#263238'
                },
            },
            subtitle: {
                text: periodo_sel + ' / ' + tipomarca_txt,
                align: 'center',
                margin: 25,
                offsetX: 0,
                offsetY: 20,
                floating: false,
                style: {
                  fontSize:  '12px',
                  fontWeight:  'normal',
                  fontFamily:  undefined,
                  color:  '#9699a2'

                },
            },
            chart: {
              width: 350,
              height: 800,
              type: 'pie',
              events: {
                dataPointSelection: (event, chartContext, config) => {
                  var periodo             = $('#periodo').val();
                  var tipomarca           = $('#tipomarca').val();
                  const marca = chartContext.w.globals.labels[config.dataPointIndex];
                  actualizar_ajax_det_producto(anio,empresa_nombre,mes,carpeta,marca,periodo,tipomarca);
                }
              },
            },
            labels: aprod_s,
            dataLabels: {
              formatter(val, opts) {
                const name = opts.w.globals.labels[opts.seriesIndex]
                const importe = opts.w.globals.series[opts.seriesIndex]
                return [name, val.toFixed(1) + '%']
              }
            },
            yaxis: {
              show: false
            },
            legend: {
              position: 'bottom',
              horizontalAlign: 'left',
              fontSize: '10px',
              fontWeight: 600, 
              formatter: function(label, opts) {
                  const total = opts.w.globals.series[opts.seriesIndex];
                  var data_total = new oNumero(total);
                  data_total  = data_total.formato(2, true);
                  return label + "  S/." + data_total
              }
            },
        };
        var chart2 = new ApexCharts(document.querySelector("#chart02"), options2);
        chart2.render();


        function actualizar_ajax_det_producto(anio,empresa_nombre,mes,carpeta,marca,periodo,tipomarca){

            var _token              = $('#token').val();

            abrircargando();
            $.ajax({
                type    :   "POST",
                url     :   carpeta+"/ajax-listado-detalle-ventasxproducto",
                data    :   {
                                _token          : _token,
                                anio            : anio,
                                empresa_nombre  : empresa_nombre,
                                mes             : mes,
                                marca           : marca,
                                periodo         : periodo,
                                tipomarca       : tipomarca,

                            },
                success: function (data) {
                    cerrarcargando();
                    $(".reporteajax").html(data);

                },
                error: function (data) {
                    cerrarcargando();
                    error500(data);
                }
            });

        }


      //Objeto oNumero
      function oNumero(numero){
      //Propiedades 
      this.valor = numero || 0;
      this.dec = -1;
      //Métodos 
      this.formato = numFormat;
      this.ponValor = ponValor;
      //Definición de los métodos
          function ponValor(cad)
          {
            if (cad =='-' || cad=='+') return
            if (cad.length ==0) return
            if (cad.indexOf('.') >=0)
               this.valor = parseFloat(cad);
           else 
               this.valor = parseInt(cad);
          } 
          function numFormat(dec, miles)
          {
            var num = this.valor, signo=3, expr;
            var cad = ""+this.valor;
            var ceros = "", pos, pdec, i;
            for (i=0; i < dec; i++){
                 ceros += '0';
            }
           pos = cad.indexOf('.')
           if (pos < 0){
              cad = cad+"."+ceros;
            }
          else
              {
              pdec = cad.length - pos -1;
              if (pdec <= dec)
                  {
                  for (i=0; i< (dec-pdec); i++)
                      cad += '0';
                  }
              else
                  {
                  num = num*Math.pow(10, dec);
                  num = Math.round(num);
                  num = num/Math.pow(10, dec);
                  cad = new String(num);
                  }
              }
          pos = cad.indexOf('.')
          if (pos < 0) pos = cad.lentgh
          if (cad.substr(0,1)== '-' || cad.substr(0,1) == '+') 
                 signo = 4;
          if (miles && pos > signo){
              do{
                  expr = /([+-]?\d)(\d{3}[\.\,]\d*)/
                  cad.match(expr)
                  cad = cad.replace(expr, '$1,$2');
                  }
              while (cad.indexOf(',') > signo);
             }
              if (dec<0) cad = cad.replace(/\./,'') 
                  return cad;
          }
      }//Fin del objeto oNumero:


      });
    </script> 
  @endif
@else
  <div role="alert" class="alert alert-danger alert-simple alert-dismissible">
      <span class="icon mdi mdi-close-circle-o"></span><strong>Alerta!</strong> No hay ventas en estos filtros.
  </div>
@endif




