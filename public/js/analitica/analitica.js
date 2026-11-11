$(document).ready(function(){


    var carpeta = $("#carpeta").val();
    $('#buscarempresa').on('click', function(event){
        event.preventDefault();
        var empresa_nombre     = $('#empresa_nombre').val();
        var _token              = $('#token').val();
        $(".reporteajax").html("");
        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-listado-de-analitica",
            data    :   {
                            _token  : _token,
                            empresa_nombre : empresa_nombre
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
    }); 



      var anio = $('#anio').val();
      var meses = $('#meses').html();
      var ventas = $('#ventas').html();
      var tnc = $('#tnc').html();

      const ameses  = JSON.parse(meses);
      const aventas = JSON.parse(ventas);
      const atnc = JSON.parse(tnc);

      var options = {
              series: [ {
                            name: 'Venta',
                            data: aventas
                          }, 
                          {
                            name: 'Nota Credito',
                            data: atnc
                          }
                        ],
              chart:  {
                          type: 'bar',
                          height: 350
                        },
              plotOptions: {
                        horizontal: false,
                        columnWidth: '80%',
                        borderRadius: 10,
                        bar: {
                            dataLabels: {
                              position: 'top', // top, center, bottom
                            }
                        },
              },

              responsive: [{
                breakpoint: 480,
                options: {
                  legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                  }
                }
              }],


              dataLabels: {
                enabled: false
              },


              stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
              },
              xaxis: {
                categories: ameses,
                title: {
                  text: anio
                }
              },
              yaxis: {
                title: {
                  text: 'Total Importe'
                },
                labels: {
                  formatter: function (val) {
                    return "S/. " + val ;
                  }
                }
              },
              fill: {
                opacity: 1
              },

              tooltip: {
                x: {
                  formatter: function (val) {
                    return "Importe " + val + " "
                  }
                }
              }


              };


      var chart = new ApexCharts(document.querySelector("#chart"), options);
      chart.render();

});

