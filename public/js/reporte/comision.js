$(document).ready(function(){
    var carpeta = $("#carpeta").val();



    $('#descargarcomisionperiodoproductoexcel').on('click', function(event){

        var _token                  = $('#token').val();
        var periodoinicio           = $('#periodo_inicio').select2().val();
        var periodofin              = $('#periodo_fin').select2().val();
        var vendedor_id             = $('#vendedor_id').select2().val();


        if(periodoinicio.length<=0){
            alerterrorajax("Seleccione un periodo inicio para el reporte");
            return false;
        }

        if(periodofin.length<=0){
            alerterrorajax("Seleccione un periodo fin para el reporte");
            return false;
        }

        if(vendedor_id.length<=0){
            alerterrorajax("Seleccione un vendedor para el reporte");
            return false;
        }

        href = $(this).attr('data-href')+'/'+periodoinicio+'/'+periodofin+'/'+vendedor_id;
        $(this).prop('href', href);
        return true;


    });


});