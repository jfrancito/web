$(document).ready(function(){
    var carpeta = $("#carpeta").val();

    $(document).on('click', '#buscarreglacompromiso', function() {
        var fechainicio = $("#fechainicio").val();
        var fechafin = $("#fechafin").val();
        var idopcion = $("#idopcion").val();
        var _token = $("input[name='_token']").val();

        if(fechainicio == "" || fechafin == ""){
            alert("Seleccione las fechas");
            return false;
        }

        actualizar_reporte_compromiso(fechainicio, fechafin, idopcion, _token);
    });

    $(document).on('click', '#descargarexcelcompromiso', function() {
        var fechainicio = $("#fechainicio").val();
        var fechafin = $("#fechafin").val();
        if(fechainicio == "" || fechafin == ""){
            alert("Seleccione las fechas");
            return false;
        }
        window.location.href = carpeta + "/regla-compromiso-pago-excel/" + fechainicio + "/" + fechafin;
    });

    function actualizar_reporte_compromiso(fechainicio, fechafin, idopcion, _token) {
        
        // Mostrar cargando
        $(".reporteajax").html('<div class="text-center" style="padding: 100px;"><i class="mdi mdi-refresh-sync mdi-spin" style="font-size: 4em; color: #4285f4;"></i><p style="margin-top: 20px; font-weight: 600; color: #555;">Procesando reporte de vista Regla_Compromiso_Pago...</p></div>');

        $.ajax({
            url: carpeta + '/ajax-lista-reglas-compromiso-pago',
            type: "POST",
            data: {
                fechainicio: fechainicio,
                fechafin: fechafin,
                idopcion: idopcion,
                _token: _token
            },
            success: function (data) {
                $(".reporteajax").html(data);
                // Re-inicializar DataTable para el nuevo contenido
                $("#tablereportecp").dataTable({
                    "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
                    "ordering": true,
                    "order": [[ 2, "desc" ]],
                    responsive: true,
                    "oLanguage": {
                        "sSearch": "Buscar:"
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error(error);
                alert("Ocurrió un error al cargar los datos de la vista");
            }
        });
    }
});
