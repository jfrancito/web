$(document).ready(function () {
    var carpeta = $("#carpeta").val();

    $(".archivoautoservicio").on('click', '.buscararchivo', function () {

        event.preventDefault();
        var autoservicio = $('#autoservicio').val();
        var idopcion = $('#idopcion').val();
        var _token = $('#token').val();

        if (autoservicio === '') {
            alerterrorajax("Seleccione un autoservicio.");
            return false;
        }

        data = {
            _token: _token,
            autoservicio: autoservicio,
            idopcion: idopcion
        };
        
        //ajax_normal(data, "/ajax-listar-archivo-autoservicio");

        let link = "/ajax-listar-archivo-autoservicio";

        $(".listajax").html("");
        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+link,
            data    :   data,
            success: function (data) {
                cerrarcargando();
                $(".listajax").html(data);

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

    });

    $(".archivoautoservicio").on('change','#anio', function() {

        event.preventDefault();
        var anio        =   $('#anio').val();
        var _token      =   $('#token').val();
        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un anio."); return false;}
        data            =   {
            _token      : _token,
            anio        : anio
        };

        //ajax_normal_combo(data,"/ajax-combo-periodo-xanio-xempresa","ajax_anio")

        let link = "/ajax-combo-periodo-xanio-xempresa";
        let contenedor = "ajax_anio";

        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+link,
            data    :   data,
            success: function (data) {
                cerrarcargando();
                $("."+contenedor).html(data);

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

    });

});