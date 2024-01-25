
var carpeta = $("#carpeta").val();

function modal_autoservicio(inicio,hoy,empresa){

      var _token                  =   $('#token').val();
      data                        =   {
                                          _token                  : _token,
                                          inicio                  : inicio,
                                          hoy                     : hoy,
                                          empresa                 : empresa
                                      };
      ajax_modal(data,"/ajax-modal-autoservicio-filtro",
                "modal-analitica","modal-analitica-container");

}


function actualizar_ajax(empresa_nombre,tipomarca,_token,carpeta,inicio,hoy,tiporeporte){
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+"/ajax-listado-de-ventasxproducto",
        data    :   {
                        _token          : _token,
                        empresa_nombre  : empresa_nombre,
                        inicio          : inicio,
                        hoy             : hoy,
                        tipomarca       : tipomarca,
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

function actualizar_ajax_anio(empresa_nombre,anio,_token,carpeta,inicio,hoy){
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+"/ajax-listado-de-clientexanio",
        data    :   {
                        _token          : _token,
                        empresa_nombre  : empresa_nombre,
                        inicio          : inicio,
                        hoy             : hoy,
                        anio            : anio
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




function actualizar_ajax_autoservicio(_token,carpeta,inicio,hoy){
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+"/ajax-listado-de-ventasxautoservicio",
        data    :   {
                        _token          : _token,
                        inicio          : inicio,
                        hoy             : hoy,
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
