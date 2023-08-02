
$(document).ready(function(){

	var carpeta = $("#carpeta").val();


    $(".reglaasignardv").on('click','#buscarordenventa', function() {

        var _token              = $('#token').val();
        var cuenta_id           = $('#cuenta_id').select2().val();
        // var fechainicio         = $('#fechainicio').val();        
        // var fechafin            = $('#fechafin').val(); 

        // if(fechainicio == ''){
        //     alerterrorajax("Seleccione una fecha de inicio");
        //     return false;
        // }
        // if(fechafin == ''){
        //     alerterrorajax("Seleccione una fecha de fin");
        //     return false;
        // } 
        /****** VALIDACIONES ********/
        if(cuenta_id.length<=0){
            alerterrorajax("Seleccione un cliente");
            return false;
        }

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-lista-orden-venta-regla",
            data    :   {
                            _token          : _token,
                            cuenta_id       : cuenta_id
                        },
            success: function (data) {
                cerrarcargando();
                $('.modal-detalledocumento-container').html(data);
                $('#modal-detalledocumento').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });


    });


   $(".reglaasignardv").on('click','.asignar_regla', function() {

        var _token                  = $('#token').val();
        var data_cod_orden_venta    = $(this).parent().parent('.filaorden').attr('data_cod_orden_venta');
        var regla_id                = $(this).parent().parent('.filaorden').find('.seledtregla').find('.select_regla').select2().val();

        if(regla_id.length<=0){
            alerterrorajax("Seleccione una regla");
            return false;
        }

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-asignar-orden-venta-regla-dias-vencimiento",
            data    :   {
                            _token                      : _token,
                            data_cod_orden_venta        :  data_cod_orden_venta,
                            regla_id                    :  regla_id
                        },
            success: function (data) {
                cerrarcargando();
                $('#modal-detalledocumento').niftyModal('hide');

                location.reload();
            },
            error: function (data) {
                error500(data);
            }
        });


    });




});














