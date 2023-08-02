$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $('.enviarcalculobono').on('click', function(event){

        event.preventDefault();
        $('input[type=search]').val('').change();
        $("#table1").DataTable().search("").draw();
        data = dataenviarautorizacion();
        if(data.length<=0){alerterrorajax("Seleccione por lo menos un calculo de bono");return false;}
        var datastring = JSON.stringify(data);
        $('#pedido').val(datastring);
        $('#cod_estado_re').val($(this).attr('data_estado'));
        console.log(data);
        abrircargando();
        $( "#formpedido" ).submit();
        
    });

    $(".cuota").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
        var data_calculobono_id     =   $(this).attr('data_calculobono_id');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            data_calculobono_id              : data_calculobono_id,
                                            idopcion                : idopcion,
                                        };

                                        
        ajax_modal(data,"/ajax-modal-detalle-calculobono-rd",
                  "modal-detalle-calculobono","modal-detalle-calculobono-container");

    });



    $(".bono").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
        var cuota_id                =   $(this).attr('data_cuota_id');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            cuota_id                : cuota_id,
                                            idopcion                : idopcion,
                                        };

        ajax_modal(data,"/ajax-modal-emitir-cuota",
                  "modal-configuracion-cuota-detalle","modal-configuracion-cuota-detalle-container");

    });


    $(".bono").on('click','.btn-guardar-emitir', function() {

        var _token                  = $('#token').val();
        var cuota_id                =   $('#cuota_id').val();
        var idopcion                =   $('#idopcion').val();
        $('#modal-configuracion-cuota-detalle').niftyModal('hide');

        data            =   {
                                _token                      : _token,
                                cuota_id                    : cuota_id,
                                idopcion                    : idopcion,
                            };
        abrircargando();
        $.ajax({            
            type    :   "POST",
            url     :   carpeta+"/ajax-guardar-emitir",
            data    :   data,
            success: function (data) {


                JSONdata     = JSON.parse(data);
                error        = JSONdata[0].error;
                mensaje      = JSONdata[0].mensaje;

                if(error==false){ 
                    cerrarcargando();
                    alertajax(mensaje);
                    location.reload();

                }else{
                    cerrarcargando();
                    alerterror505ajax(mensaje); 
                    return false;                
                }

            },
            error: function (data) {
                cerrarcargando();
                if(data.status = 500){
                    /** error 505 **/
                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html()); 
                    console.log($(contenido).find('.trace-message').html());     
                }
            }
        });                

    });




    $(".cuota").on('click','.btn-guardar-clonar', function() {

        var _token      = $('#token').val();
        var cuota_id                =   $('#cuota_id').val();
        var idopcion                =   $('#idopcion').val();
        var cuotaclonar_id         =   $('#cuotaclonar_id').val();

        if(cuotaclonar_id.length<=0){
            alerterrorajax("Seleccione un bono para poder clonar");
            return false;
        }

        $('#modal-configuracion-cuota-detalle').niftyModal('hide');

        data            =   {
                                _token                      : _token,
                                cuota_id                    : cuota_id,
                                idopcion                    : idopcion,
                                cuotaclonar_id              : cuotaclonar_id,
                            };
        abrircargando();
        $.ajax({            
            type    :   "POST",
            url     :   carpeta+"/ajax-guardar-clonar",
            data    :   data,
            success: function (data) {


                JSONdata     = JSON.parse(data);
                error        = JSONdata[0].error;
                mensaje      = JSONdata[0].mensaje;

                if(error==false){ 
                    cerrarcargando();
                    alertajax(mensaje);
                    location.reload();

                }else{
                    cerrarcargando();
                    alerterror505ajax(mensaje); 
                    return false;                
                }

            },
            error: function (data) {
                cerrarcargando();
                if(data.status = 500){
                    /** error 505 **/
                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html()); 
                    console.log($(contenido).find('.trace-message').html());     
                }
            }
        });                

    });



    $(".cuota").on('click','.modificardetallecuota', function() {

        var _token                      =   $('#token').val();
        var cuota_id                    =   $(this).attr('data_cuota_id');
        var detalle_cuota_id            =   $(this).attr('data_detalle_cuota_id');
        var idopcion                    =   $('#idopcion').val();

        data                            =   {
                                                _token                      : _token,
                                                cuota_id                    : cuota_id,
                                                detalle_cuota_id            : detalle_cuota_id,
                                                idopcion                    : idopcion
                                            };

        ajax_modal(data,"/ajax-modal-modificar-configuracion-cuota-detalle",
                  "modal-configuracion-cuota-detalle","modal-configuracion-cuota-detalle-container");

    });


    $(".cuota").on('click','.agregacuota', function() {

        var _token                  =   $('#token').val();
        var cuota_id                =   $(this).attr('data_cuota_id');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            cuota_id                : cuota_id,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-configuracion-cuota-detalle",
                  "modal-configuracion-cuota-detalle","modal-configuracion-cuota-detalle-container");

    });

    $(".cuota").on('click','.agregabonovendedor', function() {

        var _token                  =   $('#token').val();
        var periodobono_id                =   $(this).attr('data_periodobono_id');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            periodobono_id          : periodobono_id,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-configuracion-bono-vendedor",
                  "modal-configuracion-cuota-detalle","modal-configuracion-cuota-detalle-container");

    });




    $(".cuota").on('click','.clonarfechaanteriores', function() {

        var _token                  =   $('#token').val();
        let cuota_id                =   $(this).attr('data_cuota');
        let idopcion                =   $(this).attr('data_opcion');

        debugger;

        data                        =   {
                                            _token                  : _token,
                                            cuota_id                : cuota_id,
                                            idopcion                : idopcion,
                                        };
                                        
        ajax_modal(data,"/ajax-modal-clonar",
                  "modal-configuracion-cuota-detalle","modal-configuracion-cuota-detalle-container");

    });



    $(".cuota").on('change','#canal_id', function() {

        event.preventDefault();
        var canal_id       =   $('#canal_id').val();
        var _token      =   $('#token').val();
        //validacioones
        if(canal_id ==''){ alerterrorajax("Seleccione un canal."); return false;}
        data            =   {
                                _token      : _token,
                                canal_id       : canal_id
                            };

        ajax_normal_combo(data,"/ajax-combo-subcanal-xcanal","ajax_subcanal")                    

    });


});
function dataenviarautorizacion(){
        var data = [];
        $(".listatabla tr").each(function(){

            
            check   = $(this).find('input');
            periodobono_id   = $(this).find('input').attr('data_calculobono_id');
            debugger;
            if($(check).is(':checked')){
                data.push({
                    periodobono_id      : periodobono_id
                });
            }               

        });
        return data;
}