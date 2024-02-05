$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    //ventas generales
    var anio = $('#anio').val();
    var simmodena = $('#simmodena').html();
    var empresa_nombre = $('#empresa_nombre_text').html();
    var mes = $('#mes').html();
    var meses = $('#meses').html();
    var ventas = $('#ventas').html();
    var tnc = $('#tnc').html();
    var prod = $('#prod').html();
    var color = $('#color').html();
    var empresa_nombre_text = $('#empresa_nombre_text').html();
    var periodo_sel = $('#periodo_sel').html();
    var tipomarca_txt = $('#tipomarca_txt').html();
    const ameses  = JSON.parse(meses);
    const aventas = JSON.parse(ventas);
    const atnc = JSON.parse(tnc);
    const aprod = JSON.parse(prod);
    const acolor = JSON.parse(color);

    // var options = {
    //     series: aventas,
    //     colors: acolor,
    //     chart: {
    //       height: 500,
    //       type: 'pie',
    //       events: {
    //         dataPointSelection: (event, chartContext, config) => {

    //           var inicio              = $('#fechainicio').val();
    //           var hoy                 = $('#fechafin').val();
    //           var tipomarca           = $('#tipomarca').val();
    //           var tiporeporte           = $('#tiporeporte').val();

    //           debugger;
    //           const marca = chartContext.w.globals.labels[config.dataPointIndex];
    //           actualizar_ajax_det_producto(anio,empresa_nombre,mes,carpeta,marca,tipomarca,inicio,hoy,tiporeporte);
    //         }
    //       },


    //     },
    //     labels: aprod,
    //     dataLabels: {
    //       formatter(val, opts) {
    //         const name = opts.w.globals.labels[opts.seriesIndex]
    //         const importe = opts.w.globals.series[opts.seriesIndex]
    //         return [name, val.toFixed(1) + '%']
    //       }
    //     },

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
    // var chart = new ApexCharts(document.querySelector("#chart01"), options);
    // chart.render();

    //ventas atendidas
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
          height: 500,
          type: 'pie',
          events: {
            dataPointSelection: (event, chartContext, config) => {

              var inicio              = $('#fechainicio').val();
              var hoy                 = $('#fechafin').val();
              var tipomarca           = $('#tipomarca').val();
              var tiporeporte         = $('#tiporeporte').val();
              const marca = chartContext.w.globals.labels[config.dataPointIndex];
              $('.col-atras').attr('data_posicion','02');
              debugger;
              actualizar_ajax_det_producto(anio,empresa_nombre,mes,carpeta,marca,tipomarca,inicio,hoy,tiporeporte);
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
    var chart2 = new ApexCharts(document.querySelector("#chart02"), options2);
    chart2.render();


    var costos_s = $('#costos_s').html();
    const acostos_s = JSON.parse(costos_s);
    var utilidad_s = $('#utilidad_s').html();
    const autilidad_s = JSON.parse(utilidad_s);
    var totalimporte_s = $('#totalimporte_s').html();
    var jtotal_s = $('#jtotal_s').html();
    const total_s = JSON.parse(jtotal_s);

    if(simmodena == 'SOLES'){
      var options_b = {
        series: [
                  {
                    name: 'Costo',
                    group: 'u',
                    data: acostos_s
                  },
                  {
                    name: 'Utilidad Bruta',
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
                return val
            }
          },
          yaxis: {
            labels: {
              formatter: (val) => {
                return val + '%'
              }
            }
          },

        chart:  {
                  type: 'bar',
                  height: 350,
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
      var chart_b = new ApexCharts(document.querySelector("#chart_b"), options_b);
      chart_b.render();
    }


});


function actualizar_ajax_det_producto(anio,empresa_nombre,mes,carpeta,marca,tipomarca,inicio,hoy,tiporeporte){

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
                        tipomarca       : tipomarca,
                        inicio          : inicio,
                        hoy             : hoy,
                        tiporeporte     : tiporeporte,
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

