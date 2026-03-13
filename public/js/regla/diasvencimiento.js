
$(document).ready(function(){

	var carpeta = $("#carpeta").val();


    $(".reglaasignardv").on('click','#buscarordenventa', function() {

        var _token              = $('#token').val();
        var cuenta_id           = $('#cuenta_id').select2().val();
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
        var regla_id                = $(this).parent().parent('.filaorden').find('.seledtregla').find('.select_regla').val();
        var fecha_compromiso        = $(this).parent().parent('.filaorden').find('.sedfecha_compromiso').find('.fecha_compromiso').val();
        var autorizado_id           = $(this).parent().parent('.filaorden').find('.sedautorizado').find('.select_autorizado').val();
        var glosa                   = $(this).parent().parent('.filaorden').find('.sedglosa').find('.glosa').val();

        var cuenta_id               = $('#cuenta_id').val();
        var idopcion                = $('#opcion').val();


        if(regla_id.length<=0){
            alerterrorajax("Seleccione una regla");
            return false;
        }

        if(fecha_compromiso.length<=0){
            alerterrorajax("Seleccione una fecha compromiso");
            return false;
        }

        if(autorizado_id.length<=0){
            alerterrorajax("Seleccione quien autorizo");
            return false;
        }

        if(glosa.length<=0){
            alerterrorajax("Ingrese una glosa");
            return false;
        }



        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-asignar-orden-venta-regla-dias-vencimiento",
            data    :   {
                            _token                      : _token,
                            data_cod_orden_venta        :  data_cod_orden_venta,
                            regla_id                    :  regla_id,
                            fecha_compromiso            :  fecha_compromiso,
                            autorizado_id               :  autorizado_id,
                            glosa                       :  glosa,
                            cuenta_id                   :  cuenta_id,
                            idopcion                    :  idopcion
                        },
            success: function (data) {
                cerrarcargando();
                $('.ajax_lista_orden_venta').html(data.lista_modal);
                $('.reporteajax').html(data.lista_background);
                alertsuccessajax("Regla asignada con éxito");
            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });


    });

    // MASIVO: Seleccionar todos
    $(document).on('change', '#check_all_ov', function() {
        var checked = $(this).prop('checked');
        $('.check_ov').prop('checked', checked);
    });

    // MASIVO: Asignar
    $(document).on('click', '.btn-asignar-masivo', function(e) {
        e.preventDefault();

        var selected = [];
        $('.check_ov:checked').each(function() {
            selected.push($(this).val());
        });

        if(selected.length == 0) {
            alerterrorajax("Seleccione al menos una orden de venta");
            return false;
        }

        var regla_id         = $('#regla_id_masivo').val();
        var fecha_compromiso = $('#fecha_compromiso_masivo').val();
        var autorizado_id    = $('#autorizado_id_masivo').val();
        var glosa            = $('#glosa_masivo').val();
        var _token           = $('#token').val();
        var cuenta_id        = $('#cuenta_id').val();
        var idopcion         = $('#opcion').val();

        if(regla_id == "") {
            alerterrorajax("Seleccione una regla para la asignación masiva");
            return false;
        }

        abrircargando();
        $.ajax({
            type    : "POST",
            url     : carpeta + "/ajax-modal-asignar-masivo-orden-venta-regla-dias-vencimiento",
            data    : {
                _token: _token,
                selected_ids: selected,
                regla_id: regla_id,
                fecha_compromiso: fecha_compromiso,
                autorizado_id: autorizado_id,
                glosa: glosa,
                cuenta_id: cuenta_id,
                idopcion: idopcion
            },
            success: function (data) {
                cerrarcargando();
                $('.ajax_lista_orden_venta').html(data.lista_modal);
                $('.reporteajax').html(data.lista_background);
                alertsuccessajax("Reglas asignadas masivamente con éxito");
            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });
    });

});
