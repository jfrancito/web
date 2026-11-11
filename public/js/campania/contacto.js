
$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    //18-10-2019
    $(".gestioncontacto").on('click','#asignarcelular', function() {

        event.preventDefault();

        $('input[type=search]').val('').change();
        $("#tablereglamasivo").DataTable().search("").draw();
        data = dataenviar();

        precio_total    =   $('#precio_total').val();

        if(data.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}
        /****** VALIDACIONES ********/
        if(precio_total==""){
            alerterrorajax("Ingrese un precio total");
            return false;
        }



        var datastring = JSON.stringify(data);
        actualizar_producto_masivas(datastring);
        actualizar_lista_porducto_masivas();


    });






});


