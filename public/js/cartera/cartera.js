$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    // Cargar la lista inicial al entrar
    cargarListaCuentas(1);

    // Buscar al presionar clic en botón Buscar
    $("#btn-buscar-cuentas").on("click", function(e){
        e.preventDefault();
        cargarListaCuentas(1);
    });

    // Buscar al presionar Enter en el input de búsqueda
    $("#filtro_buscar").on("keypress", function(e){
        if(e.which === 13){
            e.preventDefault();
            cargarListaCuentas(1);
        }
    });

    // Paginación vía AJAX (intercepta clics en los links de paginación)
    $(".listacuentas-container").on("click", ".pagination a", function(e){
        e.preventDefault();
        var url = $(this).attr("href");
        var page = 1;
        if(url){
            var match = url.match(/page=(\d+)/);
            if(match){
                page = match[1];
            }
        }
        cargarListaCuentas(page);
    });

    // Función principal para cargar la tabla vía AJAX
    function cargarListaCuentas(page){
        var _token = $("#token").val();
        var buscar = $("#filtro_buscar").val();
        var criterio = $("#filtro_criterio").val();
        var filtrojv = $("#filtro_jv").val();

        if (typeof abrircargando === "function") {
            abrircargando();
        }

        $.ajax({
            type: "POST",
            url: carpeta + "/cartera?page=" + page,
            data: {
                _token: _token,
                buscar: buscar,
                criterio: criterio,
                filtrojv: filtrojv
            },
            success: function(response){
                if (typeof cerrarcargando === "function") {
                    cerrarcargando();
                }
                $(".listacuentas-container").html(response);
            },
            error: function(xhr){
                if (typeof cerrarcargando === "function") {
                    cerrarcargando();
                }
                if (typeof error500 === "function") {
                    error500(xhr);
                } else {
                    alert("Error al cargar la cartera de cuentas.");
                }
            }
        });
    }

    // Abrir modal para editar regla de crédito
    $(".listacuentas-container").on("click", ".btn-editar-regla", function(e){
        e.preventDefault();
        var _token = $("#token").val();
        var id = $(this).data("id");
        var codcliente = $(this).data("codcliente");
        var cliente = $(this).data("cliente");
        var limite = $(this).data("limite");
        var condicion = $(this).data("condicion");
        var clasificacion = $(this).data("clasificacion");

        if (typeof abrircargando === "function") {
            abrircargando();
        }

        $.ajax({
            type: "POST",
            url: carpeta + "/cartera/modal-detalle-cuenta",
            data: {
                _token: _token,
                id: id,
                codcliente: codcliente,
                cliente: cliente,
                limite: limite,
                condicion: condicion,
                clasificacion: clasificacion
            },
            success: function(html){
                if (typeof cerrarcargando === "function") {
                    cerrarcargando();
                }
                $("#modal-regla-body").html(html);
                $("#modal-regla-credito").modal("show");
            },
            error: function(xhr){
                if (typeof cerrarcargando === "function") {
                    cerrarcargando();
                }
                if (typeof error500 === "function") {
                    error500(xhr);
                } else {
                    alert("Ocurrió un error al obtener la información de la cuenta.");
                }
            }
        });
    });

    // Guardar cambios en la regla de crédito
    $("#btn-guardar-regla").on("click", function(e){
        e.preventDefault();
        var _token = $("#token").val();
        var cliente = $("#modal_cliente_cod").val();
        var clasificacion = $("#modal_clasificacion").val();
        var condicionpago = $("#modal_condicionpago").val();
        var limitecredito = $("#modal_limitecredito").val();
        var reglacredito = $("#modal_reglacredito_id").val();

        if (typeof abrircargando === "function") {
            abrircargando();
        }

        $.ajax({
            type: "POST",
            url: carpeta + "/reglacredito/actualizar",
            data: {
                _token: _token,
                cliente: cliente,
                clasificacion: clasificacion,
                condicionpago: condicionpago,
                limitecredito: limitecredito,
                reglacredito: reglacredito
            },
            success: function(response){
                if (typeof cerrarcargando === "function") {
                    cerrarcargando();
                }
                $("#modal-regla-credito").modal("hide");
                if (typeof alertajax === "function") {
                    alertajax("Límite y condición de crédito actualizados correctamente.");
                } else {
                    alert("Actualizado correctamente.");
                }
                // Refrescar página actual
                var page = $(".pagination .active span").text() || 1;
                cargarListaCuentas(page);
            },
            error: function(xhr){
                if (typeof cerrarcargando === "function") {
                    cerrarcargando();
                }
                if (typeof alerterrorajax === "function") {
                    alerterrorajax("Error al actualizar la regla de crédito.");
                } else {
                    alert("Error al actualizar la regla de crédito.");
                }
            }
        });
    });

});

