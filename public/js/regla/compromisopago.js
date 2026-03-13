$(document).ready(function(){
    var carpeta = $("#carpeta").val() || "";

    console.log("JS Compromisos de Pago Cargado - v689.0");

    // Delegation for dynamic content
    $(document).on('click', '.btn-detalle-pagos', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        var $btn = $(this);
        var data_div = $btn.attr('data_div') || $btn.attr('data-div') || $btn.data('div');
        
        console.log("Detectado click en detalle de pagos para Div:", data_div);

        if(!data_div){
            alert("Error: Atributo data_div no encontrado en el botón.");
            return false;
        }

        var _token = $('meta[name="csrf-token"]').attr('content') || $('#token').val() || $("input[name='_token']").val();
        var carpeta_actual = $("#carpeta").val() || "";
        
        if(typeof abrircargando === 'function'){
            abrircargando('Cargando detalles de pago...');
        } else {
            console.warn("abrircargando no es una función");
        }

        var fecha_regla = $btn.attr('data-fecha_regla') || $btn.data('fecha_regla') || "";
        var fecha_compromiso = $btn.attr('data-fecha_compromiso') || $btn.data('fecha_compromiso') || "";

        console.log("Enviando fechas al modal:", fecha_regla, fecha_compromiso);

        $.ajax({
            url: carpeta_actual + '/ajax-modal-detalle-pagos-periodo',
            type: "POST",
            data: {
                div: data_div,
                _token: _token,
                fecha_regla: fecha_regla,
                fecha_compromiso: fecha_compromiso
            },
            success: function (data) {
                console.log("Respuesta AJAX recibida con éxito");
                if(typeof cerrarcargando === 'function') { cerrarcargando(); }
                
                var $modal = $('#modal-detalle-pagos');
                if($modal.length == 0) {
                    alert("Error: No se encontró el elemento #modal-detalle-pagos en la página.");
                    return;
                }

                $modal.find('.modal-result').html(data);
                
                // Show modal
                if(typeof $modal.niftyModal === 'function') {
                    $modal.niftyModal();
                    console.log("NiftyModal disparado");
                } else {
                    console.error("niftyModal no es una función");
                    $modal.addClass('modal-show'); // Fallback manual
                }
            },
            error: function (xhr) {
                if(typeof cerrarcargando === 'function') { cerrarcargando(); }
                console.error("Error en AJAX:", xhr.status, xhr.statusText);
                alert("Error al cargar detalles (Código: " + xhr.status + "). Revise la consola del navegador.");
            }
        });
    });

    $(document).on('click', '#buscarreglacompromiso', function(e) {
        e.preventDefault();
        var fechainicio = $("#fechainicio").val();
        var fechafin = $("#fechafin").val();
        var idopcion = $("#idopcion").val();
        var sede = $("#sede").val();
        var _token = $('#token').val() || $("input[name='_token']").val();

        if(fechainicio == "" || fechafin == ""){
            alert("Seleccione las fechas");
            return false;
        }

        actualizar_reporte_compromiso(fechainicio, fechafin, idopcion, _token, sede);
    });

    $(document).on('click', '#descargarexcelcompromiso', function() {
        var fechainicio = $("#fechainicio").val();
        var fechafin = $("#fechafin").val();
        var sede = $("#sede").val() || "TODAS";
        if(fechainicio == "" || fechafin == ""){
            alert("Seleccione las fechas");
            return false;
        }
        window.location.href = carpeta + "/regla-compromiso-pago-excel/" + fechainicio + "/" + fechafin + "/" + sede;
    });

    if($("#tablereportecp").length > 0) {
        inicializar_datatable_cp("#tablereportecp", [[ 5, "desc" ]]);
    }

    if($("#tablereportecp_consolidado").length > 0) {
        inicializar_datatable_cp("#tablereportecp_consolidado", [[ 0, "asc" ]]);
    }
});

function inicializar_datatable_cp(selector, order_val) {
    if($(selector).length > 0) {
        // Usar un pequeño timeout para asegurar que el DOM esté listo, especialmente con Tabs
        setTimeout(function() {
            $(selector).dataTable({
                "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
                "ordering": true,
                "order": order_val,
                "responsive": true,
                "destroy": true, // Fundamental para reinicializar tras AJAX
                "oLanguage": {
                    "sSearch": "Buscar:",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sInfo": "Mostrando del _START_ al _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 al 0 de 0 registros",
                    "sInfoFiltered": "(filtrado de _MAX_ registros totales)",
                    "sZeroRecords": "No se encontraron resultados",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    }
                }
            });
            console.log("DataTable inicializado para:", selector);
        }, 150);
    }
}

function actualizar_reporte_compromiso(fechainicio, fechafin, idopcion, _token, sede) {
    
    $(".reporteajax").html('<div class="text-center" style="padding: 100px;"><i class="mdi mdi-refresh-sync mdi-spin" style="font-size: 4em; color: #4285f4;"></i><p style="margin-top: 20px; font-weight: 600; color: #555;">Procesando reporte...</p></div>');

    var carpeta = $("#carpeta").val() || "";

    $.ajax({
        url: carpeta + '/ajax-lista-reglas-compromiso-pago',
        type: "POST",
        data: {
            fechainicio: fechainicio,
            fechafin: fechafin,
            idopcion: idopcion,
            sede: sede,
            _token: _token
        },
        success: function (data) {
            $(".reporteajax").html(data);
            if(typeof $().tooltip === 'function') { $('[data-toggle="tooltip"]').tooltip(); }

            // Re-inicializar ambas tablas después del AJAX
            inicializar_datatable_cp("#tablereportecp", [[ 5, "desc" ]]);
            inicializar_datatable_cp("#tablereportecp_consolidado", [[ 0, "asc" ]]);
        },
        error: function (xhr) {
            $(".reporteajax").html('<div class="alert alert-danger">Error al cargar datos.</div>');
        }
    });
}
