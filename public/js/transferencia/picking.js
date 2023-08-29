$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    /*************************************** PICKING ********************************************/

    $('#buscaratenderpicking').on('click', function(event){
        
        event.preventDefault();
        var finicio     = $('#finicio').val();
        var ffin        = $('#ffin').val();
        var estado_id   = $('#estado_id').val();
        var id_opcion   = $(this).attr('id_opcion');
        
        var _token      = $('#token').val();
        
        $(".listatablapedido").html("");
        abrircargando();
        
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-listado-atender-picking",
            data    :   {
                            _token  : _token,
                            finicio : finicio,
                            estado_id : estado_id,
                            ffin    : ffin,
                            id_opcion: id_opcion
                        },
            success: function (data) {
                cerrarcargando();
                $(".listatablapedido").html(data);

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });
    }); 



    $(".despacho").on('click','.checkbox_asignar_lp', function() {

        var input   = $(this).siblings('.input_asignar_lp');
        var accion  = $(this).attr('data-atr');

        var name    = $(this).attr('name');
        var check   = -1;
        var estado  = -1;

        if($(input).is(':checked')){
            check   = 0;
            estado  = false;
        }else{
            check   = 1;
            estado  = true;
        }
        validarrelleno(accion,name,estado,check);
    });



    $(".despacho").on('click','.ptransferenciapt', function() {

        event.preventDefault();
        var _token                      =   $('#token').val();

        var data                        =   [];
        var sw                          =   0;
        var msj                         =   '';
        var codtrans                    =   '';
        var indError                    =   0;
      
        $(".table-pedidos-despachos tbody .fila_pedido").each(function(){

            nombre                  = $(this).find('.input_asignar_lp').attr('id');
            almacen_combo_id        = '';
            
            if(nombre != 'todo_asignar'){

                var cabecera_tabla_tr               =   $(this);
                var data_detalle_orden_despacho     =   $(cabecera_tabla_tr).attr('data_detalle_orden_despacho');
                              
                var data_producto                   =   $(cabecera_tabla_tr).attr('data_producto');
                var nombre_producto                 =   $(cabecera_tabla_tr).attr('nombre_producto');
                var unidad_medida                   =   $(cabecera_tabla_tr).attr('unidad_medida');
                var stock_neto                      =   $(cabecera_tabla_tr).find('.stock_neto').html();
                var almacen_id                      =   $(cabecera_tabla_tr).find('#almacen_id').val();
                var almacen_nombre                  =   $(cabecera_tabla_tr).find('#almacen_id option:selected').text();
                var array_lote_id                   =   $(cabecera_tabla_tr).find('#lote_id').val();
                var costo                           =   $(cabecera_tabla_tr).find('.costo').html();            
                var cantidad_atender                =   $(cabecera_tabla_tr).find('.updatepriceatender').val();
                debugger;
                var stock_neto                      =   parseFloat(stock_neto.replace(",", ""));
                var costo                           =   parseFloat(costo.replace(",", ""));
                var cantidad_atender                =   parseFloat(cantidad_atender.replace(",", ""));
                var check                           =   $(this).find('.input_asignar_lp');
                var total                           =   cantidad_atender * costo;
                almacen_combo_id                    =   almacen_id;

                if($(check).is(':checked')){

                    const dataCode = data_detalle_orden_despacho.split("-");

                    if(dataCode[1].length==16){ // Verificamos que no sea orden de venta
                        msj = 'No se puede generar Transferencias a la orden de venta : ' + dataCode[1]; 
                        sw=1;
                    }
                    if(codtrans != '' && codtrans !='00000000000000'){
                        if(codtrans != dataCode[1]){ // Verificamos que sea igual
                            msj = 'No se puede generar para diferentes transferencias : ' + dataCode[1]; 
                            sw=1;
                        }
                    }
                    codtrans = dataCode[1];                    

                    if(cantidad_atender<=0){
                        msj = 'Cantidad Atender del '+nombre_producto+' debe ser mayor a cero'; 
                        sw=1;
                    }
                    if(stock_neto<=0){
                        msj = 'Stock Neto del '+nombre_producto+' debe ser mayor a cero'; 
                        sw=1;
                    }
                    if(stock_neto < cantidad_atender){
                        msj = nombre_producto+' Cantidad Atender debe ser menor al Stock Neto'; 
                        sw=1;
                    }

                    data.push({
                        data_detalle_orden_despacho     : data_detalle_orden_despacho,
                        data_producto                   : data_producto,
                        nombre_producto                 : nombre_producto,
                        unidad_medida                   : unidad_medida,
                        almacen_id                      : almacen_id,
                        almacen_nombre                  : almacen_nombre,
                        array_lote_id                   : array_lote_id,
                        cantidad_atender                : cantidad_atender,
                        costo                           : costo,
                        total                           : total
                    });
                }
            }
            if(sw==1){
                alerterrorajax(msj); 
                indError = 1;
                return false;
            }
        });
       
        if(data.length == 0){
            alerterrorajax("Seleccione productos para atender."); 
            indError = 1;
            return false;
        }

        if (indError==0){
            abrircargando();

            $.ajax({
                type    :   "POST",
                url     :   carpeta+"/ajax-lista-productos-transferencia-picking",
                data    :   {
                                _token                  : _token,
                                data                    : data,
                            },
                success: function (data) {
                    activaTab('tranferenciapt');
                    $('.ajax_lista_producto_tp').html(data);
                    actualizar_combo_almacen_origen(_token,carpeta,almacen_combo_id);
                    cerrarcargando();
                },
                error: function (data) {
                    error500(data);
                }
            });
        }       
        
    }); 

    
    $(".despacho").on('change','#destino_centro', function() {

        var _token                      =   $('#token').val();
        var destino_centro_id           =   $(this).val();

        abrircargando(); 
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-combo-almacen-destino-pk",
            data    :   {
                            _token                  : _token,
                            destino_centro_id       : destino_centro_id,
                        },
            success: function (data) {
                $('.ajax_almacen_destino').html(data);
                cerrarcargando();
            },
            error: function (data) {
                error500(data);
            }
        });
    });


    $(".despacho").on('click','.btn_guardar_transferencia_pt', function() {

        var glosa                                           =   $('#glosa').val();
        var origen_propietario                              =   $('#origen_propietario').val();
        var origen_servicio                                 =   $('#origen_servicio').val();
        var destino_propietario                             =   $('#destino_propietario').val();
        var destino_servicio                                =   $('#destino_servicio').val();
        var destino_centro                                  =   $('#destino_centro').val();
        var destino_almacen                                 =   $('#destino_almacen').val();        
        var origen_almacen                                  =   $('#origen_almacen').val();
        var h_array_productos_transferencia_pt              =   $('#h_array_productos_transferencia_pt').val();

        $('#h_glosa').val(glosa);
        $('#h_origen_propietario').val(origen_propietario);
        $('#h_origen_servicio').val(origen_servicio);
        $('#h_destino_propietario').val(destino_propietario);
        $('#h_destino_servicio').val(destino_servicio);
        $('#h_destino_centro').val(destino_centro);
        $('#h_destino_almacen').val(destino_almacen);
        $('#h_origen_almacen').val(origen_almacen); 
        $('#array_productos_transferencia_pt_h').val(h_array_productos_transferencia_pt); 

        var h_glosa                     =   $('#h_glosa').val();
        var h_origen_propietario        =   $('#h_origen_propietario').val();
        var h_origen_servicio           =   $('#h_origen_servicio').val();
        var h_destino_propietario       =   $('#h_destino_propietario').val();
        var h_destino_servicio          =   $('#h_destino_servicio').val();
        var h_destino_centro            =   $('#h_destino_centro').val();
        var h_destino_almacen           =   $('#h_destino_almacen').val();


        if(h_origen_propietario.length<=0){alerterrorajax("Seleccione un propietario origen");return false;}
        if(h_origen_servicio.length<=0){alerterrorajax("Seleccione un servicio origen");return false;}
        if(h_destino_propietario.length<=0){alerterrorajax("Seleccione un propietario destino");return false;}
        if(h_destino_servicio.length<=0){alerterrorajax("Seleccione un servicio destino");return false;}
        if(h_destino_centro.length<=0){alerterrorajax("Seleccione un centro destino");return false;}
        if(h_destino_almacen.length<=0){alerterrorajax("Seleccione un almacen destino");return false;}

        var h_array_productos_transferencia_pt  = $('#h_array_productos_transferencia_pt').val();
        if(h_array_productos_transferencia_pt.length<=2){alerterrorajax('No existe ningun material en la lista'); return false;}

        
        //servicio 
        var data_servicio_despacho = [];
        var sw   = 0;
        var msj  = '';

        $(".listaservicios tbody tr").each(function(){

            var cabecera_tabla_tr               =   $(this);
            var nombre_estado                   =   $(cabecera_tabla_tr).find('.nombre_estado').html();
            var producto_id                     =   $(cabecera_tabla_tr).find('.producto_id').html();
            var nombre_producto                 =   $(cabecera_tabla_tr).find('.nombre_porducto_id').html();
            var costo                           =   $(cabecera_tabla_tr).find('.costo').html();

            var catidad_servicio                =   $(cabecera_tabla_tr).find('.update_price_cantidad_servicio').val();
            var precio_servicio                 =   $(cabecera_tabla_tr).find('.update_price_precio_servicio').val();
            catidad_servicio                    =   parseFloat(catidad_servicio.replace(",", ""));
            precio_servicio                     =   parseFloat(precio_servicio.replace(",", ""));
            var input                           =   $(cabecera_tabla_tr).find('.input_asignar_ser');

            if($(input).is(':checked')){
                ind_igv   = 1;
            }else{
                ind_igv   = 0;
            }

            var subtotal                        =   $(cabecera_tabla_tr).find('.subtotal').html();
            var igv                             =   $(cabecera_tabla_tr).find('.igv').html();

            var total_servicio                  =   $(cabecera_tabla_tr).find('.update_price_total_servicio').val();
            total_servicio                      =   parseFloat(total_servicio.replace(",", ""));

            var empresa_servicio                =   $(cabecera_tabla_tr).find('.empresa_servicio_select').val();
            var cuenta_servicio                 =   $(cabecera_tabla_tr).find('#cuenta_servicio').val();
            var contrato_cuenta                 =   $(cabecera_tabla_tr).find('.contrato_cuenta').html();
            var tipo_documento_id               =   $(cabecera_tabla_tr).find('.tipo_documento_id').html();


            if(catidad_servicio<=0){
                msj = 'Servicio ('+nombre_producto+') ingrese cantidad';
                sw=1;
            }
            if(precio_servicio<=0){
                msj = 'Servicio ('+nombre_producto+') ingrese precio';
                sw=1;
            }
            if(total_servicio <=0){
                msj = 'Servicio ('+nombre_producto+') ingrese total';
                sw=1;
            }

            if(empresa_servicio == 'VACIO'){
                msj = 'Servicio ('+nombre_producto+') seleccione empresa servicio';
                sw=1;
            }

            if(cuenta_servicio == ''  || cuenta_servicio === null){
                msj = 'Servicio ('+nombre_producto+') seleccione cuenta';
                sw=1;
            }

            data_servicio_despacho.push({
                nombre_estado                   : nombre_estado,
                producto_id                     : producto_id,
                nombre_producto                 : nombre_producto,
                costo                           : costo,
                catidad_servicio                : catidad_servicio,
                precio_servicio                 : precio_servicio,
                ind_igv                         : ind_igv,
                subtotal                        : subtotal,
                igv                             : igv,
                total_servicio                  : total_servicio,
                empresa_servicio                : empresa_servicio,
                cuenta_servicio                 : cuenta_servicio,
                contrato_cuenta                 : contrato_cuenta,
                tipo_documento_id               : tipo_documento_id
            });
           
        });
        if(sw==1){alerterrorajax(msj); return false;}

        $('#array_servicio_transferencia_pt_h').val(JSON.stringify(data_servicio_despacho)); 
        /*
        console.log(data_servicio_despacho);
        console.log($('#array_servicio_transferencia_pt_h').val());
        */
        abrircargando();
        return true;

    });

    $(".despacho").on('click','.btn_guardar_ordensalida', function() {

        var glosa                                           =   $('#txt_glosa').val();
        var empresa_propietario                             =   $('#empresa_propietario').val();
        var empresa_servicio                                =   $('#empresa_servicio').val();
        var h_array_productos_ordensalida                   =   $('#h_array_productos_ordensalida').val();

        $('#h_glosa').val(glosa);
        $('#h_empresa_propietario').val(empresa_propietario);
        $('#h_empresa_servicio').val(empresa_servicio);
        $('#array_productos_ordensalida_h').val(h_array_productos_ordensalida); 

        var h_glosa                     =   $('#h_glosa').val();
        var h_empresa_propietario        =   $('#h_empresa_propietario').val();
        var h_empresa_servicio           =   $('#h_empresa_servicio').val();
        
        if(h_empresa_propietario.length<=0){alerterrorajax("Seleccione un propietario");return false;}
        if(h_empresa_servicio.length<=0){alerterrorajax("Seleccione un servicio");return false;}
       
        var h_array_productos_ordensalida  = $('#h_array_productos_ordensalida').val();
        if(h_array_productos_ordensalida.length<=2){alerterrorajax('No existe ningun material en la lista'); return false;}
        
        if(sw==1){alerterrorajax(msj); return false;}

        abrircargando();
        return true;

    });

    $(".despacho").on('click','.eliminar-servicio-despacho', function() {        
        event.preventDefault();
        $(this).parents('.fila_servicio').remove();
    });

    
    $(".despacho").on('keypress keyup keydown','.update_price_total_servicio', function(e) {

        var cabecera_tabla_tr           =   $(this).parents('.fila_servicio');
        var _token                      =   $('#token').val();
        var evtType                     =   e.type;

        var cantidad_servicio           =   $(cabecera_tabla_tr).find('.update_price_cantidad_servicio').val();
        var precio_servicio             =   $(cabecera_tabla_tr).find('.update_price_precio_servicio').val();
        var total_servicio              =   $(cabecera_tabla_tr).find('.update_price_total_servicio').val();
        var cantidad_servicio           =   parseFloat(cantidad_servicio.replace(",", ""));
        var precio_servicio             =   parseFloat(precio_servicio.replace(",", ""));
        var total_servicio              =   parseFloat(total_servicio.replace(",", ""));
        var check                       =   $(cabecera_tabla_tr).find('.input_asignar_ser');
        var click_check                 =   0;

        if(cantidad_servicio>0){
            switch (evtType) {
                case 'keypress':
                    calcular_totales_servicio(cabecera_tabla_tr,'precio',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                    break;
                case 'keyup':
                    calcular_totales_servicio(cabecera_tabla_tr,'precio',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                    break;
                case 'keydown':
                    calcular_totales_servicio(cabecera_tabla_tr,'precio',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                    break;
                default:
                    break;
            }
        }else{
            $(cabecera_tabla_tr).find('.update_price_total_servicio').val('0.0000')
            alerterrorajax("Ingrese Cantidad");
            return false;
        }
    });
    
    $(".despacho").on('keypress keyup keydown','.update_price_cantidad_servicio,.update_price_precio_servicio', function(e) {

        var cabecera_tabla_tr           =   $(this).parents('.fila_servicio');
        var _token                      =   $('#token').val();
        var evtType                     =   e.type;
        var cantidad_servicio           =   $(cabecera_tabla_tr).find('.update_price_cantidad_servicio').val();
        var precio_servicio             =   $(cabecera_tabla_tr).find('.update_price_precio_servicio').val();
        var total_servicio              =   $(cabecera_tabla_tr).find('.update_price_total_servicio').val();
        var cantidad_servicio           =   parseFloat(cantidad_servicio.replace(",", ""));
        var precio_servicio             =   parseFloat(precio_servicio.replace(",", ""));
        var total_servicio              =   parseFloat(total_servicio.replace(",", ""));
        var check                       =   $(cabecera_tabla_tr).find('.input_asignar_ser');
        var click_check                 =   0;


        switch (evtType) {
            case 'keypress':
                calcular_totales_servicio(cabecera_tabla_tr,'total',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                break;
            case 'keyup':
                calcular_totales_servicio(cabecera_tabla_tr,'total',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                break;
            case 'keydown':
                calcular_totales_servicio(cabecera_tabla_tr,'total',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                break;
            default:
                break;
        }
    });


    function calcular_totales_servicio(cabecera_tabla_tr,atributo,cantidad_servicio,precio_servicio,total_servicio,check,ind_ckeck,click_check){

        if(atributo == 'total'){
            var calculo = cantidad_servicio*precio_servicio;
                $(cabecera_tabla_tr).find('.update_price_total_servicio').val(calculo);   
            total_servicio =  calculo;          
        }

        if(atributo == 'precio'){
            var calculo = total_servicio/cantidad_servicio;
            $(cabecera_tabla_tr).find('.update_price_precio_servicio').val(calculo);   
        }


        if(click_check == 0){
            if($(check).is(':checked')){
                costo           =   precio_servicio/1.18;
                subtotal        =   total_servicio/1.18;
                igv             =   total_servicio-subtotal;
            }else{

                costo           =   precio_servicio;
                subtotal        =   total_servicio;
                igv             =   0.00;
            }
        }else{
            if(ind_ckeck==1){
                costo           =   precio_servicio/1.18;
                subtotal        =   total_servicio/1.18;
                igv             =   total_servicio-subtotal;
            }else{

                costo           =   precio_servicio;
                subtotal        =   total_servicio;
                igv             =   0.00;
            }
        }



        $(cabecera_tabla_tr).find('.costo').html(costo.toFixed(4));
        $(cabecera_tabla_tr).find('.subtotal').html(subtotal.toFixed(4));
        $(cabecera_tabla_tr).find('.igv').html(igv.toFixed(4)); 
    }

    $(".despacho").on('change','.empresa_servicio_select', function() {
      
        var cabecera_tabla_tr           =   $(this).parents('.fila_servicio');
        var _token                      =   $('#token').val();
        var empresa_servicio_id         =   $(this).val();

        abrircargando(); 
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-combo-cuenta-servicio",
            data    :   {
                            _token                  : _token,
                            empresa_servicio_id     : empresa_servicio_id,
                        },
            success: function (data) {
                $(cabecera_tabla_tr).find('.ajax_cuenta_servicio').html(data);
                cerrarcargando();
            },
            error: function (data) {
                error500(data);
            }
        });
    });

    
    $(".despacho").on('click','.checkbox_asignar_ser', function() {

        var cabecera_tabla_tr           =   $(this).parents('.fila_servicio');
        var input                       =   $(this).siblings('.input_asignar_ser');
        var cantidad_servicio           =   $(cabecera_tabla_tr).find('.update_price_cantidad_servicio').val();
        var precio_servicio             =   $(cabecera_tabla_tr).find('.update_price_precio_servicio').val();
        var total_servicio              =   $(cabecera_tabla_tr).find('.update_price_total_servicio').val();
        var cantidad_servicio           =   parseFloat(cantidad_servicio.replace(",", ""));
        var precio_servicio             =   parseFloat(precio_servicio.replace(",", ""));
        var total_servicio              =   parseFloat(total_servicio.replace(",", ""));
        var click_check                 =   1;

        var check                       =   -1;
        var estado                      =   -1;
        if($(input).is(':checked')){
            check   = 0;
            estado  = false;
        }else{
            check   = 1;
            estado  = true;
        }

        calcular_totales_servicio(cabecera_tabla_tr,'cantidad',cantidad_servicio,precio_servicio,total_servicio,input,check,click_check);
    });

    
    $(".despacho").on('click','.agregar_servicio', function() {

        var _token                      =   $('#token').val();
        var count_servicio              =   $('#count_servicio').val();
        var calcula_cantidad_peso       =   $('#calcula_cantidad_peso').val();
        var ls_servicios   = ""
        $(".listaservicios tbody tr").each(function(){
            var cabecera_tabla_tr               =   $(this);
            var producto_id                     =   $(cabecera_tabla_tr).find('.producto_id').html();  
            ls_servicios = ls_servicios + "-" + producto_id;          
        })

        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-agregar-servicio",
            data    :   {
                            _token                  : _token,
                            count_servicio          : count_servicio,
                            calcula_cantidad_peso   : calcula_cantidad_peso, 
                            ls_servicios            : ls_servicios,
                            tipo                    : "PK",   
                        },
            success: function (data) {
                $(".ajax_lista_servicio").html(data);
                cerrarcargando();
            },
            error: function (data) {
                error500(data);
            }
        });
    });

    
    $(".despacho").on('click','.pordensalida', function() {

        event.preventDefault();
        var _token                      =   $('#token').val();

        var data                        =   [];
        var sw                          =   0;
        var msj                         =   '';
        
        var cod_orden                  =   '';
        var fec_orden                  =   '';
        var cliente                    =   '';
        var codOrden                   =   '';
        var indError                   =   0;
        
        $(".table-pedidos-despachos tbody .fila_pedido").each(function(){

            nombre                  = $(this).find('.input_asignar_lp').attr('id');
            almacen_combo_id        = '';
            

            if(nombre != 'todo_asignar'){

                var cabecera_tabla_tr               =   $(this);
                var data_detalle_orden_despacho     =   $(cabecera_tabla_tr).attr('data_detalle_orden_despacho');
                //var mobil_grupo                     =   $(cabecera_tabla_tr).attr('mobil_grupo');
                              
                var data_producto                   =   $(cabecera_tabla_tr).attr('data_producto');
                var nombre_producto                 =   $(cabecera_tabla_tr).attr('nombre_producto');
                var unidad_medida                   =   $(cabecera_tabla_tr).attr('unidad_medida');
                var stock_neto                      =   $(cabecera_tabla_tr).find('.stock_neto').html();
                var almacen_id                      =   $(cabecera_tabla_tr).find('#almacen_id').val();
                var almacen_nombre                  =   $(cabecera_tabla_tr).find('#almacen_id option:selected').text();
                var array_lote_id                   =   $(cabecera_tabla_tr).find('#lote_id').val();
                var costo                           =   $(cabecera_tabla_tr).find('.costo').html();            
                var cantidad_atender                =   $(cabecera_tabla_tr).find('.updatepriceatender').val();
                debugger;
                var stock_neto                      =   parseFloat(stock_neto.replace(",", ""));
                var costo                           =   parseFloat(costo.replace(",", ""));
                var cantidad_atender                =   parseFloat(cantidad_atender.replace(",", ""));
                var check                           =   $(this).find('.input_asignar_lp');
                var total                           =   cantidad_atender * costo;
                almacen_combo_id                    =   almacen_id;

                //console.log($(check).is(':checked'));
                //if($(check).is(':checked')){1
                if($(check).is(':checked')){

                    const dataCode = data_detalle_orden_despacho.split("-");
                    
                    if(dataCode[1].length==14){ // Verificamos que no sea transferencia
                        msj = 'Solo se pueden generar Salidas a las ordenes de venta : ' + dataCode[1]; 
                        sw=1;
                    }
                    if(codOrden != ''){
                        if(codOrden != dataCode[1]){ // Verificamos que sea igual
                            msj = 'No se puede generar salidas para diferentes Ordenes de Venta : ' + dataCode[1]; 
                            sw=1;
                        }
                    }
                    codOrden = dataCode[1];   

                    if(cantidad_atender<=0){
                        msj = 'Cantidad Atender del '+nombre_producto+' debe ser mayor a cero'; 
                        sw=1;
                    }
                    if(stock_neto<=0){
                        msj = 'Stock Neto del '+nombre_producto+' debe ser mayor a cero'; 
                        sw=1;
                    }
                    if(stock_neto < cantidad_atender){
                        msj = nombre_producto+' Cantidad Atender debe ser menor al Stock Neto'; 
                        sw=1;
                    }

                    cod_orden                       =   $(cabecera_tabla_tr).attr('cod_orden');
                    fec_orden                       =   $(cabecera_tabla_tr).attr('fec_orden');
                    cliente                         =   $(cabecera_tabla_tr).attr('cliente'); 
                    
                    data.push({
                        data_detalle_orden_despacho     : data_detalle_orden_despacho,
                        data_producto                   : data_producto,
                        nombre_producto                 : nombre_producto,
                        unidad_medida                   : unidad_medida,
                        almacen_id                      : almacen_id,
                        almacen_nombre                  : almacen_nombre,
                        array_lote_id                   : array_lote_id,
                        cantidad_atender                : cantidad_atender,
                        costo                           : costo,
                        total                           : total
                    });
                }
            }

            if(sw==1){
                alerterrorajax(msj);                 
                indError = 1;
                return false;
            }
      
        });
        
        if(data.length == 0){
            alerterrorajax("Seleccione productos para atender."); 
            indError = 1;
            return false;
        }

        if(indError==0){
            abrircargando();   

            $.ajax({
                type    :   "POST",
                url     :   carpeta+"/ajax-lista-productos-ordensalida-picking",
                data    :   {
                                _token                  : _token,
                                data                    : data,
                            },
                success: function (data) {
                    activaTab('ordensalida');
                    $('.ajax_lista_producto_ordensalida').html(data);
                    actualizar_combo_almacen_origen(_token,carpeta,almacen_combo_id);
                    actualizarDatosVentaSalida(cod_orden,cliente,fec_orden);
                    cerrarcargando();
                },
                error: function (data) {
                    error500(data);
                }
            });
        }       
    }); 
    
    $(".listapedidoosiris").on('click','.btn-atender-picking', function(e) {
        abrircargando();   
        return true;
    });

    $(".despacho").on('change','.select_tabla_almacen_id', function() {

        var cabecera_tabla_tr           =  $(this).parents('.fila_pedido');
        var almacen_id                  =  $(this).val();
        var producto_id                 =  $(this).parents('.fila_pedido').attr('data_producto');
        var cantidad_atender            =  $(this).parents('.fila_pedido').find('.updatepriceatender').val();
        cantidad_atender                =  cantidad_atender.replace(",", "");        
        var _token                      = $('#token').val();

        abrircargando(); 
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-combo-lote-almacen",
            data    :   {
                            _token                  : _token,
                            almacen_id              : almacen_id,
                            producto_id             : producto_id,
                            cantidad_atender        : cantidad_atender,
                        },
            success: function (data) {
                $(cabecera_tabla_tr).find('.ajax_combo_lote').html(data);
                var lote_id     =       $(cabecera_tabla_tr).find('.ajax_combo_lote').find('#lote_id').val();
                actualizar_stock(lote_id,almacen_id,producto_id,_token,carpeta,cabecera_tabla_tr);
                
            },
            error: function (data) {
                error500(data);
            }
        });
    });

    $(".despacho").on('change','.select_tabla_lote_id', function() {

        var cabecera_tabla_tr           =  $(this).parents('.fila_pedido');
        var array_lote_id               =  $(this).val();

        var almacen_id                  =  $(cabecera_tabla_tr).find('#almacen_id').val();
        var producto_id                 =  $(this).parents('.fila_pedido').attr('data_producto');
        var _token                      =   $('#token').val();

        abrircargando();

        actualizar_stock(array_lote_id,almacen_id,producto_id,_token,carpeta,cabecera_tabla_tr);
    });

    $(".detraccion").on('click','.checkbox_asignar_lp', function() {
        
    });      

    $(".detraccion").on('click','.input_asignar_lp', function() {
        var _token                      = $('#token').val();
        var idpicking                   = $('#idpicking').val();
        var data                        = [];
        
        var name    = $(this).attr('name');
        var id      = $(this).attr('id');

       let dataCode = name.split("-");

        $(".table-detraccion tbody tr").each(function(){
            if($(this).find('.input_asignar_lp').length > 0){
                if($(this).find('.input_asignar_lp').is(':checked')){
                    nombre = $(this).find('.input_asignar_lp').attr('name');
                    grr = $(this).find('.input_check_grr').is(':checked');
                    fac = $(this).find('.input_check_fac').is(':checked');
                    opc = "";
                    if (grr){ opc = "GRR" }
                    if (fac){ opc = "FAC" }
                    let idSolTra = nombre.split("-");
                    data.push({id  : nombre, tipo : opc, idSolTra: idSolTra[1] });
                }
            }
        });

        const dataAux = data.filter((x) => {
            return x.idSolTra !== "00000000000000";
       });

        if (dataAux.length >1) {
            let tipo = dataAux[0].tipo;
            let tamCodigo = dataCode[1].length;
            let nameDiferent = "" ;
            let msj = "" ;

            dataAux.forEach(element => {
                // Validamos por tamaño 
                if(element.tipo != tipo){                   
                    document.getElementById(id).checked = false; 
                    nameDiferent = name;
                    msj = "Solo puede agregar el mismo tipo de documento."; 
                    return ;                   
                }

                let arr = element.id.split("-");   
                if (arr[1].length !== tamCodigo){
                    nameDiferent = name;
                    document.getElementById(id).checked = false; 
                    msj = "Solo puede agregar el mismo tipo de orden."; 
                    return ;    
                }

                // Validamos que solo las solictudes sea uno solo
                if (tamCodigo === 14 && arr[1].length === 14 ){
                    nameDiferent = name;
                    document.getElementById(id).checked = false; 
                    msj = "Cuando es solicitud de transferencia, solo debe seleccionar uno."; 
                    return ;    
                }
            });
                        
            if (msj !== ""){
                alerterrorajax(msj); 
                data = data.filter(x => x.id !== nameDiferent);
            }  
        }

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-calcular-detraccion",
            data    :   {
                            _token                  : _token,
                            data                    :  data,
                            idpicking               :  idpicking
                        },
            success: function (data) {       
                $('.ajax_lista_producto_detraccion').html(data);                
                detraccion = 0;
                tdetraccion = 0;
                $(".listatabledetalle tbody tr").each(function(){
                    detraccion = parseFloat($(this).attr('data_detraccion'));
                    if (!isNaN(detraccion)) {
                        tdetraccion = tdetraccion + detraccion;
                    }
                });
                $('#monto').val(tdetraccion);
            },
            error: function (data) {
                error500(data);
            }
        });

    });

    // REPORTES 
    $('#buscardetracciondiaria').on('click', function(event){

        var _token              = $('#token').val();
        var fechafin            = $('#fechafin').val(); 

        if(fechafin == ''){
            alerterrorajax("Seleccione un día");
            return false;
        } 

        abrircargando();

        var textoajax   = $('.listapickingdetraccion').html(); 
        $(".listapickingdetraccion").html("");

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-reporte-detraccion-diario",
            data    :   {
                            _token          : _token,
                            fechafin        : fechafin,                            
                        },
            success: function (data) {
                cerrarcargando();
                $(".listapickingdetraccion").html(data);                
            },
            error: function (data) {

                cerrarcargando();
                
                if(data.status = 500){

                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html()); 
                    $(".listapickingdetraccion").html(textoajax);  
                    console.log($(contenido).find('.trace-message').html());     
                }
            }
        });
    });
 
    $('#descargardetracciondiariopdf').on('click', function(event){

        var _token              = $('#token').val();
        var fechafin            = $('#fechafin').val(); 

        if(fechafin == ''){
            alerterrorajax("Seleccione un día");
            return false;
        } 

        href = $(this).attr('data-href')+'/'+fechafin;
        $(this).prop('href', href);
        return true;

    });



}); 


function actualizar_stock(array_lote_id,almacen_id,producto_id,_token,carpeta,cabecera_tabla_tr){

    $.ajax({
        type    :   "POST",
        url     :   carpeta+"/ajax-calcular-stock-almacen-lote",
        data    :   {
                        _token                  : _token,
                        array_lote_id           : array_lote_id,
                        almacen_id              : almacen_id,
                        producto_id             : producto_id
                    },
        success: function (data) {
            $(cabecera_tabla_tr).find('.ajax_stock_almacen_lote').html(data);
            pintar_input_cantidad_atender(cabecera_tabla_tr);
            cerrarcargando();
        },
        error: function (data) {
            error500(data);
        }
    });

}

function pintar_input_cantidad_atender(cabecera_tabla_tr){

    var stock_neto        =    $(cabecera_tabla_tr).find('.stock_neto').html();
    var cantidad_atender  =    $(cabecera_tabla_tr).find('.updatepriceatender').val();
    var stock_neto        =    stock_neto.replace(",", "");
    var cantidad_atender  =    cantidad_atender.replace(",", "");

    //$(cabecera_tabla_tr).find('.td_cantidad_atender').removeClass("background_rojo");
    (cabecera_tabla_tr).find('.updatepriceatender').removeClass("color_rojo");
    if(parseFloat(stock_neto) < parseFloat(cantidad_atender)){
        $(cabecera_tabla_tr).find('.updatepriceatender').addClass("color_rojo");
        //$(cabecera_tabla_tr).find('.td_cantidad_atender').addClass("background_rojo");
    }
}

function actualizarDatosVentaSalida(orden_venta,cliente,fecha_orden){
    $('#cod_orden').val(orden_venta);  
    $('#txt_cliente').val(cliente);      
    $('#fec_orden').val(fecha_orden);  
}

function actualizar_combo_almacen_origen(_token,carpeta,almacen_combo_id){


    $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-combo-almacen-origen-pk",
            data    :   {
                            _token                  : _token,
                            almacen_combo_id        : almacen_combo_id
                        },
            success: function (data) {
                $('.ajax_almacen_origen').html(data);
        },
        error: function (data) {
            error500(data);
        }
    });

}

function activaTab(tab){
    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
}

function validarrelleno(accion,name,estado,check,token){

    if (accion=='todas_asignar') {
        $(".table-pedidos-despachos tr").each(function(){
            nombre              =    $(this).find('.input_asignar_lp').attr('id');
            check_disableb      =    $(this).find('.check_disableb').length;
            if(nombre != 'todo_asignar' && check_disableb==0){
                $(this).find('.input_asignar_lp').prop("checked", estado);
                $(this).find('.input_asignar_gop').prop("checked", estado);
            }
        });
    }else{

        sw = 0;
        if(estado){
            $(".table-pedidos-despachos tr").each(function(){
                nombre = $(this).find('.input_asignar_lp').attr('id');
                check_disableb      =    $(this).find('.check_disableb').length;

                if(nombre != 'todo_asignar' && $(this).find('.input_asignar_lp').length > 0 && check_disableb==0){
                    if(!($(this).find('.input_asignar_lp').is(':checked'))){
                        sw = sw + 1;
                    }
                }
            });
            if(sw==1){
                $("#todo_asignar").prop("checked", estado);
            }
        }else{
            $("#todo_asignar").prop("checked", estado);
        }           
    }
}




