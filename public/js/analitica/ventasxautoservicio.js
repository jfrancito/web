$(document).ready(function(){


    var carpeta = $("#carpeta").val();

    $(".crearpedido").on('click','.btn-autoservicio-marca', function(e) {
        event.preventDefault();
        var empresa_nombre      = $(this).attr('data_nombre_empresa');
        var inicio              = $('#fechainicio').val();
        var hoy                 = $('#fechafin').val();
        var tipomarca           = $('#tipomarca').val();  
        var tiporeporte         = $('#tiporeporte').val();
        var _token              = $('#token').val();
        $(".reporteajax").html("");
        actualizar_ajax(empresa_nombre,tipomarca,_token,carpeta,inicio,hoy,tiporeporte);
        $('#modal-analitica').niftyModal('hide');

    });

    $(".crearpedido").on('click','.btn-autoservicio-anio', function(e) {
        event.preventDefault();
        var empresa_nombre      = $(this).attr('data_nombre_empresa');
        var inicio              = $('#fechainicio').val();
        var hoy                 = $('#fechafin').val();
        var anio                = $('#anio').val();  
        var _token              = $('#token').val();
        $(".reporteajax").html("");
        actualizar_ajax_anio(empresa_nombre,anio,_token,carpeta,inicio,hoy);
        $('#modal-analitica').niftyModal('hide');

    });



    $(".crearpedido").on('click','.col-atras', function(e) {
        event.preventDefault();
        var inicio              = $('#fechainicio').val();
        var hoy                 = $('#fechafin').val();
        var _token              = $('#token').val();
        $(".reporteajax").html("");
        actualizar_ajax_autoservicio(_token,carpeta,inicio,hoy);
    });


    $(".crearpedido").on('click','#buscarautoservicio', function(e) {

        event.preventDefault();
        var inicio              = $('#fechainicio').val();
        var hoy                 = $('#fechafin').val();
        var _token              = $('#token').val();
        $(".reporteajax").html("");
        actualizar_ajax_autoservicio(_token,carpeta,inicio,hoy);

    }); 



    //ventas generales
    var   ventas_s  = $('#ventas_s').html();
    var   prod_s    = $('#prod_s').html();
    var   color_s   = $('#color_s').html();
    var   cliente_s = $('#cliente_s').html();

    const aventas_s   = JSON.parse(ventas_s);
    const aprod_s     = JSON.parse(prod_s);
    const acolor_s    = JSON.parse(color_s);
    const acliente_s  = JSON.parse(cliente_s);
    var simmodena = $('#simmodena').html();
    var optionsauto = {
                    series: [ 
                              {
                                data: aventas_s
                              }
                            ],
                    chart: {
                        type: 'bar',
                        height: 800,
                        events: {

                          legendClick: function(chartContext, seriesIndex, config) {
                            var inicio              = $('#fechainicio').val();
                            var hoy                 = $('#fechafin').val();
                            const empresa = chartContext.w.globals.labels[seriesIndex];
                            modal_autoservicio(inicio,hoy,empresa);
                          },
                          dataPointSelection: (event, chartContext, config) => {

                            var inicio              = $('#fechainicio').val();
                            var hoy                 = $('#fechafin').val();
                            const empresa = chartContext.w.globals.labels[config.dataPointIndex];
                            modal_autoservicio(inicio,hoy,empresa);

                          }

                        }
                      },


                    plotOptions: {
                      bar: {
                        barHeight: '100%',
                        distributed: true,
                        horizontal: true,
                        dataLabels: {
                          position: 'bottom'
                        },
                      }
                    },


                    colors: acolor_s,

                    dataLabels: {
                      enabled: true,
                      textAnchor: 'start',
                      style: {
                        colors: ['#000']
                      },
                      formatter: function (val, opt) {
                        var data_total = new oNumero(val);
                        data_total  = data_total.formato(2, true);

                        return opt.w.globals.labels[opt.dataPointIndex] + " :  " +simmodena+ data_total
                      },
                      offsetX: 0
                    },


                    stroke: {
                      width: 1,
                      colors: ['#fff']
                    },

                    xaxis: {
                      categories: acliente_s,

                      labels: {
                        formatter: (val) => {
                          var data_total = new oNumero(val);
                          data_total  = data_total.formato(2, true);
                          return simmodena + data_total
                        }
                      },


                    },
                    yaxis: {
                      labels: {
                        show: false
                      }
                    },




                    legend: {
                      position: 'bottom',
                      horizontalAlign: 'left',
                      fontSize: '12px',
                      fontWeight: 600, 
                      formatter: function(label, opts) {
                          const total = opts.w.globals.series[0][opts.seriesIndex];
                          var data_total = new oNumero(total);
                          data_total  = data_total.formato(2, true);
                          return label + " => " + simmodena + data_total
                      }
                    }

                  };
    var chartaut = new ApexCharts(document.querySelector("#chartaut"), optionsauto);
    chartaut.render();

});

