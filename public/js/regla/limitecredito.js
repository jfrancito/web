
$(document).ready(function(){

	var carpeta = $("#carpeta").val();

    $(".reglaasignarlc").on('click','#buscarcliente', function() {

        var _token              = $('#token').val();
        var jefe_id             = $('#jefe_id').select2().val();
 
        /****** VALIDACIONES ********/
        if(jefe_id.length<=0){
            alerterrorajax("Seleccione un cliente");
            return false;
        }

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-lista-cliente-jefe-regla",
            data    :   {
                            _token          : _token,
                            jefe_id         : jefe_id,
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


   $(".reglaasignarlc").on('click','.asignar_regla', function() {

        var _token                  = $('#token').val();
        var data_cod_cliente        = $(this).parent().parent('.filacliente').attr('data_cod_cliente');


        var regla_id                = $(this).parent().parent('.filacliente').find('.seledtregla').find('.select_regla').select2().val();

        if(regla_id.length<=0){
            alerterrorajax("Seleccione una regla");
            return false;
        }

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-asignar-cliente-regla-limite-credito",
            data    :   {
                            _token                      : _token,
                            data_cod_cliente            :  data_cod_cliente,
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














