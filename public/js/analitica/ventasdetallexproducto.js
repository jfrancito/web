$(document).ready(function(){

      var carpeta = $("#carpeta").val();
      var empresa_nombre = $('select[name="empresa_nombre"] option:selected').text();
      var mes = $('#mes').html();
      var marca = $('#marca').html();
      var empresa_nombre = $('#empresa_nombre_txt').html();
      var periodo_sel = $('#periodo_sel').html();
      var tipomarca_txt = $('#tipomarca_txt').html();
      var simmodena = $('#simmodena').html();
      var anio = $('#anio').val();
      var meses = $('#meses').html();
      var ventas = $('#ventas').html();
      var tnc = $('#tnc').html();
      var prod = $('#prod').html();
      var color = $('#color').html();

      var totalimporte = $('#totalimporte').html();
      var data_totalimporte = new oNumero(totalimporte);
      data_totalimporte  = data_totalimporte.formato(2, true);
      $('.total-pedido').html('S/. '+data_totalimporte);

      const ameses  = JSON.parse(meses);
      const aventas = JSON.parse(ventas);
      const atnc = JSON.parse(tnc);
      const aprod = JSON.parse(prod);
      const acolor = JSON.parse(color);
      // var options = {
      //     series: aventas,
      //     colors:acolor,
      //     chart: {
      //       height: 800,
      //       type: 'pie'
      //     },
      //     labels: aprod,
      //     yaxis: {
      //       show: false
      //     },
      //     legend: {
      //       position: 'bottom',
      //       horizontalAlign: 'left',
      //       fontSize: '12px',
      //       fontWeight: 600,          
      //       formatter: function(label, opts) {
      //           const total = opts.w.globals.series[opts.seriesIndex];
      //           var data_total = new oNumero(total);
      //           data_total  = data_total.formato(2, true);
      //           return label + " => " + simmodena + data_total
      //       }
      //     },
      // };
      // var chart3 = new ApexCharts(document.querySelector("#chart01"), options);
      // chart3.render();

      var ventas_s = $('#ventas_s').html();
      var prod_s = $('#prod_s').html();
      var color_s = $('#color_s').html();
      const aventas_s = JSON.parse(ventas_s);
      const aprod_s = JSON.parse(prod_s);
      const acolor_s = JSON.parse(color_s);
      var options2 = {
          series: aventas_s,
          colors: acolor_s,
          chart: {
            height: 600,
            type: 'pie'
          },
          labels: aprod_s,
          yaxis: {
            show: false
          },
          legend: {
            position: 'bottom',
            horizontalAlign: 'left',

            fontSize: '12px',
            fontWeight: 600, 
            formatter: function(label, opts) {
                const total = opts.w.globals.series[opts.seriesIndex];
                var data_total = new oNumero(total);
                data_total  = data_total.formato(2, true);
                return label + " => " + simmodena + data_total
            }

          },
      };
      var chart4 = new ApexCharts(document.querySelector("#chart02"), options2);
      chart4.render();

      var costos_s = $('#costos_s').html();
      const acostos_s = JSON.parse(costos_s);
      var utilidad_s = $('#utilidad_s').html();
      const autilidad_s = JSON.parse(utilidad_s);
      var totalimporte_s = $('#totalimporte_s').html();
      var jtotal_s = $('#jtotal_s').html();
      const total_s = JSON.parse(jtotal_s);
      debugger;
      if(simmodena == 'SOLES'){
        var options_b = {
          series: [
                    {
                      name: 'Costo',
                      group: 'u',
                      data: acostos_s
                    },
                    {
                      name: 'Utilidad',
                      group: 'u',
                      data: autilidad_s
                    },

                  ],
            dataLabels: {
              style: {
                  fontWeight: 'bold',
                  colors: ['#000', '#000']
              },
              formatter: (val) => {
                  return val;
              }
            },
            yaxis: {
              labels: {
                formatter: (val) => {
                  return val + ' %'
                }
              }
            },
          chart:  {
                    type: 'bar',
                    height: 450,
                    stacked: true
                  },
          stroke:  {
                    width: 1,
                    colors: ['#fff']
                  },

          plotOptions: {
                    bar: {
                      horizontal: false
                    }
                  },
          xaxis: {
                    categories: aprod_s
                  },
          fill: {
                  opacity: 1
                },
          colors: ['#FF5733', '#3498db', '#2ecc71'],
          legend: {
                    position: 'top',
                    horizontalAlign: 'left'
                  }
        };
        var chart_b1 = new ApexCharts(document.querySelector("#chart_b"), options_b);
        chart_b1.render();
      }


});