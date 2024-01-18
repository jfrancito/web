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
   
    $(".crearpedido").on('click','.col-atras', function(e) {
      
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


    //ventas generales
    var anio = $('#anio').val();
    var empresa_nombre = $('select[name="empresa_nombre"] option:selected').text();
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

    var options = {
        series: aventas,
        colors:acolor,

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
        labels: aprod,
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
    var chart = new ApexCharts(document.querySelector("#chart01"), options);
    chart.render();


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
});

