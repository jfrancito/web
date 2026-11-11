$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".estadocuenta").on('change','#jefeventa_id', function() {

        var _token              =   $('#token').val();
        var operacion_id        =   $(this).val();

        $.ajax({
              type    :     "POST",
              url     :     carpeta+"/ajax-combo-cliente-xjefe",
              data    :     {
                                _token              : _token,
                                operacion_id        : operacion_id
                            },
                success: function (data) {
                    $('.ajax_cliente').html(data);
                },
                error: function (data) {
                    error500(data);
                }
        });



    });

    $('#descargarestadocuentasexcel').on('click', function(event){

        var _token              = $('#token').val();
        var jefeventa_id         =   $('#jefeventa_id').val();
        var fechainicio          =   $('#fechainicio').val();
        var fechafin             =   $('#fechafin').val();
        var cliente_id           =   $('#cliente_id').val();
        //validacioones
        if(fechainicio ==''){ alerterrorajax("Seleccione una fecha inicio."); return false;}
        if(fechafin ==''){ alerterrorajax("Seleccione una fecha fin."); return false;}
        if(jefeventa_id==''){ alertdangermobil("Seleccione un Jefe de venta"); return false;}
        if(cliente_id==''){ alertdangermobil("Seleccione un Cliente"); return false;}

        href = $(this).attr('data-href')+'/'+fechainicio+'/'+fechafin+'/'+jefeventa_id+'/'+cliente_id;
        $(this).prop('href', href);
        return true;


    });


    $('#descargarestadocuentaspdf').on('click', function(event){


        var _token              = $('#token').val();
        var jefeventa_id         =   $('#jefeventa_id').val();
        var fechainicio          =   $('#fechainicio').val();
        var fechafin             =   $('#fechafin').val();
        var cliente_id           =   $('#cliente_id').val();
        //validacioones
        if(fechainicio ==''){ alerterrorajax("Seleccione una fecha inicio."); return false;}
        if(fechafin ==''){ alerterrorajax("Seleccione una fecha fin."); return false;}
        if(jefeventa_id==''){ alertdangermobil("Seleccione un Jefe de venta"); return false;}
        if(cliente_id==''){ alertdangermobil("Seleccione un Cliente"); return false;}

        href = $(this).attr('data-href')+'/'+fechainicio+'/'+fechafin+'/'+jefeventa_id+'/'+cliente_id;
        $(this).prop('href', href);
        return true;


    });


    $(".estadocuenta").on('click','.buscardocumentofolio', function() {

        event.preventDefault();

        var idopcion             =   $('#idopcion').val();
        var _token               =   $('#token').val();
        var jefeventa_id         =   $('#jefeventa_id').val();
        var fechainicio          =   $('#fechainicio').val();
        var fechafin             =   $('#fechafin').val();
        var cliente_id           =   $('#cliente_id').val();
        //validacioones
        if(fechainicio ==''){ alerterrorajax("Seleccione una fecha inicio."); return false;}
        if(fechafin ==''){ alerterrorajax("Seleccione una fecha fin."); return false;}
        if(jefeventa_id==''){ alertdangermobil("Seleccione un Jefe de venta"); return false;}
        if(cliente_id==''){ alertdangermobil("Seleccione un Cliente"); return false;}
        abrircargando();


        debugger;
        data            =   {
                                _token                  : _token,
                                fecha_inicio            : fechainicio,
                                fecha_fin               : fechafin,
                                jefeventa_id            : jefeventa_id,
                                cliente_id              : cliente_id,
                                idopcion                : idopcion
                            };


        $.ajax({
              type    :     "POST",
              url     :     carpeta+"/ajax-buscar-documento-estado-cuenta",
              data    :     data,
                success: function (data) {
                                        cerrarcargando();
                    $('.listajax').html(data);

                },
                error: function (data) {
                    cerrarcargando();
                    error500(data);
                }
        });


    });


});
