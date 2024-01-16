$(document).ready(function(){


    var carpeta = $("#carpeta").val();

   $('#buscarempresa').on('click', function(event){
        event.preventDefault();
        var empresa_nombre      = $('#empresa_nombre').val();
        var periodo             = $('#periodo').val();
        var tipomarca           = $('#tipomarca').val();  


        var _token              = $('#token').val();
        $(".reporteajax").html("");
        actualizar_ajax(empresa_nombre,periodo,tipomarca,_token,carpeta);

    }); 

    $(".contenido").on('change','#empresa_nombre,#periodo,#tipomarca', function() {


        event.preventDefault();
        var empresa_nombre      = $('#empresa_nombre').val();
        var periodo             = $('#periodo').val();
        var tipomarca           = $('#tipomarca').val();  


        var _token              = $('#token').val();
        $(".reporteajax").html("");
        actualizar_ajax(empresa_nombre,periodo,tipomarca,_token,carpeta);


    });
   

    function actualizar_ajax(empresa_nombre,periodo,tipomarca,_token,carpeta){

        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-listado-de-ventasxproducto",
            data    :   {
                            _token  : _token,
                            empresa_nombre : empresa_nombre,
                            periodo : periodo,
                            tipomarca : tipomarca,
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
            type: 'pie',
          },
          labels: aprod,
          yaxis: {
            show: false
          },
          legend: {
            position: 'bottom'
          },
      };

      var chart = new ApexCharts(document.querySelector("#chart01"), options);
      chart.render();

});

