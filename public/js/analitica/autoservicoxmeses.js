$(document).ready(function(){
    var carpeta = $("#carpeta").val();
    //ventas generales
    var   ventas_s  = $('#ventas_s').html();
    var   prod_s    = $('#prod_s').html();
    var   color_s   = $('#color_s').html();
    var   cliente_s = $('#cliente_s').html();
    var   meses_s = $('#meses_s').html();
    
    const aventas_s   = JSON.parse(ventas_s);
    const aprod_s     = JSON.parse(prod_s);
    const acolor_s    = JSON.parse(color_s);
    const acliente_s  = JSON.parse(cliente_s);
    const ameses_s  = JSON.parse(meses_s);
    var simmodena = $('#simmodena').html();

    var optionsmeses = {
      series: [{
                name: 'Ventas',
                data: aventas_s
              }],
      chart: {
                height: 450,
                type: 'bar',
              },
      plotOptions: {
                bar: {
                  borderRadius: 10,
                  dataLabels: {
                    position: 'top', // top, center, bottom
                  },
                }
              },
      dataLabels: {
        
                enabled: true,
                formatter: function (val) {

                  var data_total = new oNumero(val);
                  data_total  = data_total.formato(2, true);
                  return simmodena + data_total

                },
                offsetY: -20,
                style: {
                  fontSize: '12px',
                  colors: ["#304758"]
                }
              },
    
      xaxis: {
                categories: ameses_s,
                position: 'top',
                axisBorder: {
                  show: false
                },
                axisTicks: {
                  show: false
                },
                crosshairs: {
                  fill: {
                    type: 'gradient',
                    gradient: {
                      colorFrom: '#D8E3F0',
                      colorTo: '#BED1E6',
                      stops: [0, 100],
                      opacityFrom: 0.4,
                      opacityTo: 0.5,
                    }
                  }
                },
                tooltip: {
                  enabled: true,
                }
              },
      yaxis: {
              axisBorder: {
                show: false
              },
              axisTicks: {
                show: false,
              },
              labels: {
                show: false,
                formatter: function (val) {
                  var data_total = new oNumero(val);
                  data_total  = data_total.formato(2, true);
                  return simmodena + data_total
                }
              }
            
            }
    };

    var chartmeses = new ApexCharts(document.querySelector("#chartmeses"), optionsmeses);
    chartmeses.render();

});

