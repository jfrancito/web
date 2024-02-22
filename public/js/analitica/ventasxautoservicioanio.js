var carpeta = $("#carpeta").val();

$(".crearpedido").on('click','#buscarautoservicioanio', function(e) {
    event.preventDefault();
    var selec_anioini       = $('#selec_anioini').val();
    var selec_aniofin       = $('#selec_aniofin').val();
    var selec_cliente       = $('#selec_cliente').val();


    var _token              = $('#token').val();
    $(".reporteajax").html("");
    actualizar_ajax_autoservicioanio(_token,carpeta,selec_anioini,selec_aniofin,selec_cliente);
}); 
//ventas generales





var   ventas_s          = $('#ventas_s').html();
var   tituloban         = $('#tituloban').html();
const aventas_s         = JSON.parse(ventas_s);
var   cliente_s         = $('#cliente_s').html();
const acliente_s        = JSON.parse(cliente_s);
var   inicio            = $('#inicio').html();
var   hoy               = $('#hoy').html();
var   simmodena         = $('#simmodena').html();
var   totalimporte_s    = $('#totalimporte_s').html();
var   ventas2_s         = $('#ventas2_s').html();
const aventas2_s        = JSON.parse(ventas2_s);
var   anio01            = $('#anio01').html();
var   anio02            = $('#anio02').html();
var   meses_s         = $('#meses_s').html();
const ameses_s        = JSON.parse(meses_s);


var   total01           = $('#total01').html();
var   total02           = $('#total02').html();

var data_total01        = new oNumero(total01);
data_total01            = data_total01.formato(2, true);
var data_total02        = new oNumero(total02);
data_total02            = data_total02.formato(2, true);



var options = {
  series: [{
            name: anio01 +' : ' + simmodena + data_total01,
            data: aventas_s
          }, {
            name: anio02 +' : ' + simmodena + data_total02,
            data: aventas2_s
          }],
  chart: {
  type: 'bar',
  height: 450
},
plotOptions: {
  bar: {
    borderRadius: 10,
    dataLabels: {
      position: 'center', // top, center, bottom,
      orientation: 'vertical'
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

  style: {
    fontSize: '12px',
    colors: ["#304758"]
  }


},
stroke: {
  show: true,
  width: 2,
  colors: ['transparent']
},
xaxis: {
  categories: ameses_s,
},
fill: {
  opacity: 1
},
tooltip: {
  y: {
    formatter: function (val) {
      var data_total = new oNumero(val);
      data_total  = data_total.formato(2, true);
      return simmodena + data_total
    }
  }
}
};

var chartanio = new ApexCharts(document.querySelector("#chartanio"), options);
chartanio.render();


