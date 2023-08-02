$(document).ready(function(){

    var carpeta = $("#carpeta").val();


   $('#buscarpedido').on('click', function(event){
        
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
            url     :   carpeta+"/ajax-listado-transferencia",
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


    $(".crearpedido").on('click','.filapedido', function(e) {

        var data_icl        =   $(this).attr('data_icl');
        var data_pcl        =   $(this).attr('data_pcl');
        var data_icu        =   $(this).attr('data_icu');
        var data_pcu        =   $(this).attr('data_pcu');
        var data_ncl        =   $(this).attr('data_ncl');
        var data_dcl        =   $(this).attr('data_dcl');
        var data_ccl        =   $(this).attr('data_ccl');
        var data_icontrato  =   $(this).attr('data_icontrato');

        var _token          =   $('#token').val();

        $.ajax({
              type    :   "POST",
              url     :   carpeta+"/ajax-transferencia-cliente",
              data    :   {
                            _token                : _token,
                            data_icl              : data_icl,
                            data_pcl              : data_pcl,
                            data_icu              : data_icu,
                            data_pcu              : data_pcu,
                            data_ncl              : data_ncl,
                            data_dcl              : data_dcl,
                            data_ccl              : data_ccl,
                            data_icontrato        : data_icontrato,
                          },
            beforeSend: function() {
                $('.ajaxdirecciones').html("<div class='row text-center'><div class='lds-roller'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>");
            },
            success: function (data) {
                $('.ajaxdirecciones').html(data);
            },
            error: function (data) {
                error500(data);
                setTimeout(function(){ $('.listaclientes').toggle("slow");  $('.direccioncliente').toggle("slow");}, 2000);
            }
        });

        $('.listaclientes').toggle("slow");
        $('.direccioncliente').toggle("slow");

    });

    $(".crearpedido").on('click','.filaproducto', function(e) {

        var data_ipr        =   $(this).attr('data_ipr');
        var data_ppr        =   $(this).attr('data_ppr');
        var data_npr        =   $(this).attr('data_npr');
        var data_upr        =   $(this).attr('data_upr');
        var data_mpr        =   $(this).attr('data_mpr');        
        var data_spr        =   $(this).attr('data_spr');    
        var data_epr        =   $(this).attr('data_epr');

        var _token          =   $('#token').val();
             
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-detalle-producto",
            data    :   {
                          _token                : _token,
                          data_ipr              : data_ipr,
                          data_ppr              : data_ppr,
                          data_npr              : data_npr,
                          data_upr              : data_upr,
                          data_mpr              : data_mpr,
                          data_spr              : data_spr
                            },

            success: function (data) {
                $('.ajaxdetalleproducto').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

        $('.listaproductos').toggle("slow");
        $('.precioproducto').toggle("slow");
        tituloprecioproducto(data_npr,data_upr,data_ipr,data_ppr,data_mpr,data_spr,data_epr);

    });

    $(".crearpedido").on('click','.mdi-check-cliente', function(e) {

        var almacen_select              =   $('#almacen_select').val();
        var centro_origen_select        =   $('#centroorigen_select').val();
        var nombre_empr                 =   $(this).attr('data_nomemp');
        var codigo_empr                 =   $(this).attr('data_codemp');
        var clienteop_select            =   $('#cliente_op_select').val();
        var fechapedido                 =   $('#fechapedido').val();
        var fechaentrega                =   $('#fechaentrega').val();
        var horaentrega                 =   $('#horaentrega').val();
        var nombre_almacen_select       =   $('#almacen_select :selected').text();
        var nombre_clienteop_select     =   $('#cliente_op_select :selected').text();
        var nombre_centro_origen_select =   $('#centroorigen_select :selected').text();

        var horaentrega1                 =   $('#horaentrega').val();
        
        // validacion dirección
        if(almacen_select ==''){ alertdangermobil("Seleccione un almacén destino."); return false;}
        if(centro_origen_select ==''){ alertdangermobil("Seleccione un almacén destino."); return false;}
        //if(validarfechadespacho(fechapedido)){ alertdangermobil("Fecha de pedido inválida."); return false;}
        //if(validarfechadespacho(fechaentrega)){ alertdangermobil("Fecha de entrega inválida."); return false;}
        if(validarfecha_ped_ent(fechapedido,fechaentrega)){ alertdangermobil("Fecha de entrega debe ser mayor a la fecha de pedido."); return false;}
       
        $('.listaclientes').toggle("slow");
        $('.direccioncliente').toggle("slow");
        activaTab('productotp');
        agregar_cliente(nombre_empr,codigo_empr,fechapedido,fechaentrega,horaentrega,nombre_almacen_select,nombre_clienteop_select,nombre_centro_origen_select);
        agregar_cliente_hidden(nombre_empr,codigo_empr,fechapedido,fechaentrega,horaentrega,almacen_select,clienteop_select,centro_origen_select);  
        
        alertmobil("Seleccionado correctamente.");

        return true;
    });

    $(".crearpedido").on('click','.mdi-close-precio', function(e) {
        limpiar_input_producto();
        $('.listaproductos').toggle("slow");
        $('.precioproducto').toggle("slow");
    });

    $(".crearpedido").on('click','.mdi-close-cliente', function(e) {
        history.go(-1);
    });

    $(".crearpedido #pedidotp").on('click','.col-atras', function(e) {
        activaTab('productotp');
    });

    // agregando producto
    $(".crearpedido").on('click','.mdi-check-precio', function(e) {

        var cantidad                =   $('#cantidad').val();
        var paquete                 =   $('#paquete').val();
        var pesototal               =   $('#pesototal').val();
        //cantidad                    =   cantidad.replace(",", "");
        //paquete                     =   paquete.replace(",", "");
        pesototal_string            =   pesototal;
        pesototal                   =   pesototal.replace(",", "");

        var data_ipr            =   $(this).attr('data_ipr');
        var data_ppr            =   $(this).attr('data_ppr');
        var data_npr            =   $(this).attr('data_npr');
        var data_upr            =   $(this).attr('data_upr');
        var data_mpr            =   $(this).attr('data_mpr');
     
        // validacion cantidad
        if(cantidad =='0.0000' || cantidad==''){ alertdangermobil("Ingrese cantidad"); return false;}
        if(paquete =='0.0000' || paquete==''){ alertdangermobil("Ingrese paquete"); return false;}
        if(pesototal =='0.0000' || pesototal==''){ alertdangermobil("Ingrese peso total"); return false;}
     
        if(existe_producto(data_ppr,data_ipr) == '0'){ alertdangermobil("El producto ya existe en la transferencia"); return false;}

        agregar_producto(data_npr,data_upr,cantidad,data_ipr,data_ppr,paquete,data_mpr,pesototal_string);
        
        //agregar_producto_hidden();data_prpr
        calcular_total();
        alertmobil("Producto "+data_npr+" agregado");
        limpiar_input_producto();
        $('.listaproductos').toggle("slow");
        $('.precioproducto').toggle("slow");
        return true;
    });

    // borrando un producto
    $(".crearpedido").on('click','.mdi-close-pedido', function(e) {
        relacion = $(this).parents('.productoseleccion').attr('data_ipr');        
        $(this).parents('.productoseleccion').remove();
        calcular_total(); 
    });


    $(".crearpedido").on('click','.mdi-close-cliente', function(e) {

            $('.listaclientes').toggle("slow");
            $('.direccioncliente').toggle("slow");

    });

     //guardar pedido
    $(".crearpedido").on('click','.btn-guardar', function(e) {

        var obs                       =   $('#iobs').val();
        var idtrans                   =   $('#iidtransferencia').val();
        var pesototal                 =   parseFloat($('.total-pedido').html());        
        
        agregar_obs_hidden(idtrans,obs,pesototal);

        // validacion productos        
        data = agregar_producto_hidden();
        if(data.length<=0){alertdangermobil("Seleccione por lo menos un producto"); return false;}
        var datastring = JSON.stringify(data);
        $('#productos').val(datastring);

        abrircargando();
        return true;
    });

    $(".listapedidoosiris").on('click','.btn-detalle-pedido-mobil', function(e) {

        var _token              = $('#token').val();
        var pedido_id           = $(this).attr('data-id');
        var id_opcion           = $(this).attr('id_opcion');
        var data_json_detalle   = $(this).attr('data-json-detalle');
        var m_accion            = $(this).attr('m_accion');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-transferencia",
            data    :   {
                            _token                  : _token,
                            pedido_id               : pedido_id,
                            id_opcion               : id_opcion,
                            m_accion                : m_accion
                        },    
            success: function (data) {
                $('.modal-detalle-pedido').html(data);
                $('#detalle-producto').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });

        
    });



    $(".listapedidoosiris").on('click','.btn_guardar_detalle', function(e) {

        var accion                      = $("#accion").val();
        var estado_id                   = $("#estado_id").val();
        var pedido_id                   = $("#id_transferencia_modal").val();        
        var _token                      = $('#token').val();
        var id_opcion                   = $('#id_opcion').val(); 
        var m_accion                    = $('#m_accion').val(); 
        var mensaje                     = validarestadopicking(accion,estado_id,m_accion);
        
        if(mensaje != ''){
            alerterrorajax(mensaje); 
            return false;
        }else{
            
            $.ajax({            
                type    :   "POST",
                url     :   carpeta+"/ajax-cambiar-estado-transferencia",
                data    :   {
                                _token  : _token,
                                pedido_id               : pedido_id,
                                estado_id               : estado_id,
                                accion                  : accion,
                                id_opcion               : id_opcion,
                                m_accion                : m_accion,
                            },    
                success: function (data) {                
                    JSONdata     = JSON.parse(data);
                    error        = JSONdata[0].error;
                    mensaje      = JSONdata[0].mensaje;

                     if(error==false){
                        alertajax("Correcto");            
                        $("#detalle-producto .close").click();                                          
                        $('#buscarpedido').click();
                    }else{
                        alerterrorajax(mensaje); 
                    }                 
                },
                error: function (data) {
                    error500(data);                    
                }
            });
        }
    });

    /*************************************** PICKING ********************************************/

    $('#buscarpicking').on('click', function(event){
        
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
            url     :   carpeta+"/ajax-listado-picking",
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

    $(".despacho").on('click','#buscarordenpedidodespacho', function() {

        var _token              = $('#token').val();
        var centroorigen_id     = $('#centroorigen_id').select2().val();
        var opcion_id           = $('#opcion').val();
        var idpicking           = $('#idpicking').val();
        
        if (centroorigen_id == ""){
            alerterrorajax("Seleccione Centro Origen");   
            return false;
        }else{          

            abrircargando();
            $.ajax({
                
                type    :   "POST",
                url     :   carpeta+"/ajax-modal-lista-transferencia-autorizada",
                data    :   {
                                _token          : _token,
                                centroorigen_id : centroorigen_id,
                                opcion_id       : opcion_id,
                                idpicking       : idpicking,
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
        }
    });

    $(".despacho").on('click','.input_check_pe_ln', function() {        
        codtrans  = $(this).attr('data_trans');
        data      = $(this).attr('data');
        
        var dataArr = JSON.parse(data);   

        if($(this).is(':checked')){                                   
             dataArr.forEach((obj, i) => {
                if (obj.cantidad_pendiente > 0){
                 agregar_modal_detalle_producto_picking(codtrans,obj);
                }
             });
        }else{
            dataArr.forEach((obj, i) => {
                eliminar_modal_detalle_producto_picking(codtrans,obj);
             });
            
        } 
    });

    $(".despacho").on('click','.input_check_pe_ov', function() {        
        codorden  = $(this).attr('data_trans');
        data      = $(this).attr('data');
                
        var dataArr = JSON.parse(data);   

        if($(this).is(':checked')){                                   
             dataArr.forEach((obj, i) => {
                agregar_modal_detalle_venta_picking(codorden,obj);
             });
        }else{
            dataArr.forEach((obj, i) => {
                eliminar_modal_detalle_venta_picking(codorden);
            });
            
        } 
    });


     $(".despacho").on('click','#agregarproductos', function() {

        event.preventDefault();

        var _token                  = $('#token').val();
        /*var grupo                 = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();*/
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var correlativo             = $('#correlativo').val();
        //var tabestado               = $('#tabestado').val();*/
        var opcion_id               = $('#opcion').val();
        var idpicking               = $('#idpicking').val();
       

        var mensaje1                = validarProductoRepetidoAgregarPicking(array_detalle_producto)
        if(mensaje1 != ''){alerterrorajax(mensaje1); return false;}
        var mensaje2                = validarCantidadesAgregarPicking();            
        if(mensaje2 != ''){alerterrorajax(mensaje2); return false;}
            
        data_producto               = dataenviarproducto();
        if(data_producto.length<=0){alerterrorajax('Ingrese cantidad al menos en un producto.'); return false;}
        
        $('#modal-detalledocumento').niftyModal('hide');
        
        abrircargando();
        $.ajax({

            type    :   "POST",
            url     :   carpeta+"/ajax-modal-agregar-producto-picking",
            data    :   {
                            _token                  : _token,
                            data_producto           : data_producto,
                            array_detalle_producto  : array_detalle_producto,
                            opcion_id               : opcion_id,
                            correlativo             : correlativo,
                            idpicking               : idpicking,
                        },    
            success: function (data) {
                cerrarcargando();
                $('.lista_pedidos_despacho').html(data);

            },
            error: function (data) {
                error500(data);
            }
        });
    });
   

    $(".despacho").on('click','#agregarproductoindividual', function() {

        var _token          = $('#token').val();

        $('#modal-detalledocumento').niftyModal('hide');
        $(".colored-header").addClass("colored-header-success"); 

        $.ajax({            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-producto-individual",
            data    :   {
                            _token          : _token
                        },
            success: function (data) {
                    $('.modal-producto-individual-container').html(data);
                    $('#modal-producto-individual').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });
    });

    
    $(".producto-individual").on('click','.btn_producto_individual', function(e) {

        var _token              =   $('#token').val();
   
        var producto_id         =   $('#producto_select').val();
        var cantidad_pr         =   $('#cantidad_pr').val();
        var array_detalle_producto  =   $('#array_detalle_producto').val();
        var correlativo             = $('#correlativo').val();
        var opcion_id               = $('#opcion').val();
        var idpicking               = $('#idpicking').val();
        var dataArr                 = JSON.parse(array_detalle_producto);   
        var txt_error               = "";

        if(dataArr.length==0){alerterrorajax("Primero debe agregar solicitud de trasferencia o venta.");return false;}
        if(producto_id==''){alerterrorajax("Seleccione un producto");return false;}
        if(cantidad_pr<='0.00'){alerterrorajax("La cantidad debe ser mayor a cero");return false;}
        
        dataArr.forEach((detalle, i) => {
            if(detalle.producto_id == producto_id){                        
                txt_error = "Este producto ya existe. Debe agregarlo como excedente.";
            }
        });

        if(txt_error!==''){alerterrorajax(txt_error);return false;}     

        $('#modal-producto-individual').niftyModal('hide');

        $.ajax({
              type    :   "POST",
              url     :   carpeta+"/ajax-agregar-producto-individual",
              data    :   {
                            _token                  : _token,
                            array_detalle_producto  : array_detalle_producto,
                            producto_id             : producto_id,
                            cantidad_pr             : cantidad_pr,
                            correlativo             : correlativo,
                            opcion_id               : opcion_id,
                            idpicking               : idpicking,
                          },
            success: function (data) {
                cerrarcargando();
                alertajax("Producto agregado correctamente.");  
                $('.lista_pedidos_despacho').html(data);                
            },
            error: function (data) {
                error500(data);
            }
        });
    });


    $(".despacho").on('keypress keyup keydown','.precio_modal, .cantidad_excedente', function(e) {
        var tot_cant    =   0;
        $(".lista_tabla_prod tbody tr").each(function(){
            cantidad_atender     = $(this).find('.precio_modal').val();    
            cantidad_excedente   = $(this).find('.cantidad_excedente').val();   
            peso                 = $(this).attr('producto_peso');     
            cantAtender   = (Number(cantidad_atender) + Number(cantidad_excedente)) * Number(peso);
            tot_cant = tot_cant + cantAtender;
        });
        $('#peso_atendido').val(tot_cant);  
    }); 

    $(".despacho").on('click','.btn-guardar-pedido', function() {
     
        var _token                  =   $('#token').val();
        var centro_origen_id        =   $('#centroorigen_id').val();        
        var array_detalle_producto  =   $('#array_detalle_producto').val();
        var palets                  =   $('#palets').val();
        
        
        $('#centro_origen_id').val(centro_origen_id);  
        $('#cantidad_palets').val(palets);  

        $.ajax({            
            type    :   "POST",
            url     :   carpeta+"/ajax-validar-cantidad-atender-picking",
            data    :   {
                            _token                  : _token,
                            array_detalle_producto  : array_detalle_producto,
                        },    
            success: function (data) {                
                JSONdata     = JSON.parse(data);
                error        = JSONdata[0].error;
                mensaje      = JSONdata[0].mensaje;

                 if(error){
                    alerterrorajax(mensaje); 
                    return false;
                }else{
                    $('#formguardarpicking').submit();
                }                 
            },
            error: function (data) {
                error500(data);                    
            }
        });

    });

    $(".despacho").on('click','.eliminar-producto-picking', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();
        /*var grupo                   = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();        */
        var correlativo             = $('#correlativo').val();
        var fila                    = $(this).parents('.fila_pedido').attr('data_correlativo');
        var idpicking               = $('#idpicking').val();
        var opcion_id               = $('#opcion').val();
        

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-picking-eliminar-fila",
            data    :   {
                            _token                      : _token,
                            array_detalle_producto      : array_detalle_producto,
                            /*grupo                       : grupo,
                            numero_mobil                : numero_mobil,*/
                            correlativo                 : correlativo,
                            fila                        : fila,
                            idpicking                   : idpicking,
                            opcion_id                   : opcion_id,
                        },
            success: function (data) {
                alertajax("Eliminación exitosa");
                $('.lista_pedidos_despacho').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });
    });

      $(".listapedidoosiris").on('click','.btn-detalle-picking', function(e) {

        var _token               = $('#token').val();
        var picking_id           = $(this).attr('data-id');
        var id_opcion            = $(this).attr('id_opcion');
        var m_accion             = $(this).attr('m_accion');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-picking",
            data    :   {
                            _token                  : _token,
                            picking_id              : picking_id,
                            id_opcion               : id_opcion,
                            m_accion                : m_accion
                        },    
            success: function (data) {
                $('.modal-detalle-pedido').html(data);
                $('#detalle-producto').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });       
    });

     $(".listapedidoosiris").on('click','.btn_guardar_detalle_picking', function(e) {

        var estado_id                   = $("#pk_estado_id").val();
        var picking_id                  = $("#id_picking_modal").val();        
        var _token                      = $('#token').val();
        var id_opcion                   = $('#id_opcion').val(); 
        var m_accion                    = $('#m_accion').val();         
        var mensaje                     = validarestadopicking(estado_id,m_accion);
        
        
        if(m_accion == 'VIEW'){
            $("#detalle-producto .close").click();  
            return true;
        }

        if(mensaje != ''){
            alerterrorajax(mensaje); 
            return false;
        }else{
            
            $.ajax({            
                type    :   "POST",
                url     :   carpeta+"/ajax-cambiar-estado-picking",
                data    :   {
                                _token                  : _token,
                                picking_id              : picking_id,
                                estado_id               : estado_id,
                                id_opcion               : id_opcion,
                                m_accion                : m_accion,
                            },    
                success: function (data) {                
                    JSONdata     = JSON.parse(data);
                    error        = JSONdata[0].error;
                    mensaje      = JSONdata[0].mensaje;

                     if(error==false){
                        alertajax("Correcto");            
                        $("#detalle-producto .close").click();                                          
                        $('#buscarpicking').click();
                    }else{
                        alerterrorajax(mensaje); 
                    }                 
                },
                error: function (data) {
                    error500(data);                    
                }
            });
        }
    });


}); 

function validarestadopicking(estado_id, m_accion){
    var txtmensaje = '';
    
    if (m_accion == 'DELETE') {
        if (estado_id != "EPP0000000000002"){
            txtmensaje = 'Solo se permite eliminar una transferencia generada.';
        }
    }

    if(m_accion == 'DECLINE') {
        if (estado_id != "EPP0000000000007"){
            txtmensaje = 'Solo se permite rechazar una transferencia cerrada.';
        }
    }

    return txtmensaje;
}

function validarProductoRepetidoAgregarPicking(array_detalle_producto){
    var txtmensaje = '';
    var dataArr = JSON.parse(array_detalle_producto);   
    
    if (dataArr.length > 0){
        $(".lista_tabla_prod tbody tr").each(function(){
            idtrans              = $(this).attr('idtrans');
            idtransdet           = $(this).attr('id');
            producto_id          = $(this).attr('data_producto_id');
            producto_nombre      = $(this).attr('producto_nombre');
            cantidad_atender     = $(this).find('.precio_modal').val();
           
            cantAtender   = Number(cantidad_atender);

            if(cantAtender>0){
                dataArr.forEach((detalle, indice) => {
                    if(detalle.transferenciadetalle_id == idtransdet && detalle.producto_id == producto_id){                        
                        txtmensaje = 'Producto repetido: ' + detalle.producto_nombre + ' - Transferencia: ' + detalle.transferencia_id; 
                    }
                });
            }            
        });
    }    
    return txtmensaje;
}

function validarCantidadesAgregarPicking(){
    var txtmensaje = '';
    
    $(".lista_tabla_prod tbody tr").each(function(){
        idtrans              = $(this).attr('idtrans');
        cantidad_atender     = $(this).find('.precio_modal').val();
        cantidad_pendiente   = $(this).attr('cantidad_pendiente');
        producto_nombre      = $(this).attr('producto_nombre');
        
        cantAtender   = Number(cantidad_atender);
        cantPendiente = Number(cantidad_pendiente);

        if(cantAtender>cantPendiente){
            txtmensaje = 'El producto: '+producto_nombre+' de la transferencia: '+idtrans+' la cantidad pendiente no puede ser mayor que la cantidad a atender.';
        }
    });

    return txtmensaje;
}
    

 function handleEvt(e,obj, pid){
    var cant = $(obj).val().replace(",", "");
    var peso = $('.mdi-check-precio').attr('data_mpr');
    var empa = $('.mdi-check-precio').attr('data_epr');
    var sfam = $('.mdi-check-precio').attr('data_spr');
    var total = cant*peso;

    document.getElementById('pesototal').value=total;

    if(sfam == "SFM0000000000104"){ // Embolsado
        document.getElementById('paquete').value=cant/empa;        
    }else{
        document.getElementById('paquete').value=cant;        
    }
}

function validarestadopedido(accion,estado_id, m_accion){
    var txtmensaje = '';
    
    if (m_accion == 'DELETE'){ accion = 'DELETE';}

    if (accion == 'DELETE') {
        if (estado_id != "EPP0000000000002"){
            txtmensaje = 'Solo se permite eliminar una transferencia generada.';
        }
    }
    if(accion == "Cerrar"){
        if (estado_id != "EPP0000000000002"){
            txtmensaje = 'Solo se permite cerrar una transferencia generada.';
        }
    }
    if(accion == "Autorizar"){
        if (estado_id != "EPP0000000000007"){
            txtmensaje = 'Solo se permite autorizar una transferencia cerrada.';
        }
    }
    return txtmensaje;
}


function agregar_producto_hidden(){

    var data = [];   
    $(".detalleproducto .productoseleccion").each(function(){
        
        var data_ipr_for  = $(this).attr('data_ipr');
        var data_ppr_for  = $(this).attr('data_ppr');
        var data_npr_for  = $(this).attr('data_npr');
        var data_pqpr_for = $(this).attr('data_pqpr').replace(",","");;
        var data_ctpr_for = $(this).attr('data_ctpr').replace(",","");;
        var data_pepr_for = $(this).attr('data_pepr').replace(",","");;
        var data_upr_for  = $(this).attr('data_upr');
        
        data.push({
            prefijo_producto    : data_ppr_for,
            id_producto         : data_ipr_for,
            nombre_producto     : data_npr_for,
            paquetes_producto   : data_pqpr_for,
            cantidad_producto   : data_ctpr_for,
            unidad_producto     : data_upr_for,
            peso_producto       : data_pepr_for,
        });

    });
    console.log(data);
    return data;
}

function agregar_obs_hidden(idtrans,obs,pesototal){
    $('#idtrans').val(idtrans);   
    $('#obs').val(obs);   
    $('#peso_total').val(pesototal);
}

function calcular_total(){
    var total = 0.00;
    $(".detalleproducto .productoseleccion").each(function(){
        var subtotal     = 0;    
        
        var data_mpr_for = $(this).attr('data_ctpr').replace(",","");
        var data_ipr_for = $(this).attr('data_pepr').replace(",","");;

        var subtotal     = parseFloat(data_mpr_for)*parseFloat(data_ipr_for);
    
        total = total + subtotal;
    });
  
    //var usFormat = total.toLocaleString('en-US');
    var tot = total.toFixed(4);
    var usFormat = tot.toLocaleString('en');
    
    $('.total').html(tot);
}

function agregar_cliente(nombre_emp,codigo_empr,fechapedido,fechaentrega,horaentrega,almacendestino,cliente_op,centro_origen){

    var cadena = '';            
    cadena += " <div class='col-sm-12 col-mobil-top'>";
    cadena += "     <div class='panel panel-full'>";
    cadena += "         <div class='panel-heading cell-detail'>";
    cadena +=               nombre_emp;
    cadena += "             <span class='panel-subtitle cell-detail-description-producto'>"+codigo_empr+"</span>";
    cadena += "             <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Fecha de Pedido:</strong> <small>"+fechapedido+"</small></span>";
    cadena += "             <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Fecha de Entrega:</strong> <small>"+fechaentrega+"</small></span>";
    cadena += "             <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Hora de Entrega :</strong> <small>"+horaentrega+"</small></span>";
    cadena += "             <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Centro Origen :</strong> <small>"+centro_origen+"</small></span>";
    cadena += "             <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Almacén Destino :</strong> <small>"+almacendestino+"</small></span>";
    cadena += "             <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Cliente Referencial :</strong> <small>"+cliente_op+"</small></span>";
    cadena += "         </div>";
    cadena += "     </div>";
    cadena += " </div>";

    $(".detallecliente").html(cadena);
}

function agregar_cliente_hidden(nombre_emp,codigo_empr,fechapedido,fechaentrega,horaentrega,almacen_select,cliente_op,centro_origen){
    $('#cod_empr').val(codigo_empr);   
    $('#nom_empr').val(nombre_emp);   
    $('#fecha_pedido').val(fechapedido);
    $('#fecha_entrega').val(fechaentrega);
    $('#hora_entrega').val(horaentrega);
    $('#almacen_destino').val(almacen_select);
    $('#cliente_op').val(cliente_op);
    $('#centro_origen').val(centro_origen);    
}

function activaTab(tab){
    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
}

function validarfechadespacho(fecha){

    var fecha1= moment().format("YYYY-MM-DD");
    var fecha2= moment(fecha).format("YYYY-MM-DD");

   
    if ( moment(fecha2).isBefore(fecha1) || fecha==''  ) {        
            return true;
    }
    else {
      return false;
    }
}

function validarfecha_ped_ent(fechapedido, fechaentrega){

    var fecha1= moment(fechapedido).format("YYYY-MM-DD");
    var fecha2= moment(fechaentrega).format("YYYY-MM-DD");
    
    if ( moment(fecha2).isBefore(fecha1) || fecha1=='' ) {
            return true;
    }
    else {
      return false;
    }
}

function tituloprecioproducto(data_npr,data_upr,data_ipr,data_ppr,data_mpr,data_spr,data_epr){

    // limpiar
    $('.p_nombre_producto').html('');
    $('.p_unidad_medida').html('');
    $('.p_peso_producto').html('');

    $(".mdi-check-precio").attr("data_ipr",'');
    $(".mdi-check-precio").attr("data_ppr",'');
    $(".mdi-check-precio").attr("data_npr",'');
    $(".mdi-check-precio").attr("data_upr",'');
    $(".mdi-check-precio").attr("data_spr",'');
    $(".mdi-check-precio").attr("data_epr",'');
    // AGREGAR 

    $('.p_nombre_producto').html(data_npr);
    $('.p_unidad_medida').html(data_upr);
    $('.p_peso_producto').html('PESO : ' + data_mpr);

    // agregar todos los valores del producto al check 
    $(".mdi-check-precio").attr("data_ipr",data_ipr);
    $(".mdi-check-precio").attr("data_ppr",data_ppr);
    $(".mdi-check-precio").attr("data_npr",data_npr);
    $(".mdi-check-precio").attr("data_upr",data_upr); 
    $(".mdi-check-precio").attr("data_mpr",data_mpr);  
    $(".mdi-check-precio").attr("data_spr",data_spr); 
    $(".mdi-check-precio").attr("data_epr",data_epr); 
}


function existe_producto(data_ppr,data_ipr){
    var sw = '1';

    $(".detalleproducto .productoseleccion").each(function(){
        var data_ppr_for = $(this).attr('data_ppr');
        var data_ipr_for = $(this).attr('data_ipr');
       
        if(data_ppr_for == data_ppr && data_ipr_for == data_ipr){
            sw = '0';
        }    
    });
    return sw;
}


function agregar_producto(nombreproducto,unidadmedida,cantidad,data_ipr,data_ppr,paquete,peso,pesototal){
    var update  = 1;
    var cadena = '';

    cadena += "<div class='col-sm-12 productoseleccion col-mobil-top'";
    cadena += "data_ipr='"+data_ipr+"' data_ppr= '"+data_ppr+"' data_npr= '"+nombreproducto+"' data_upr='"+unidadmedida+"' data_pqpr='"+paquete+"' data_ctpr='"+cantidad+"' data_pepr='"+peso+"' data_upd='"+update+"'>" 
    cadena += "     <div class='panel panel-default panel-contrast'>";
    cadena += "         <div class='panel-heading cell-detail detalle-trans'>";
    cadena +=               nombreproducto;
    cadena += "             <div class='tools'>";
    cadena += "                 <span class='icon mdi mdi-close mdi-close-pedido'></span>";
    cadena += "             </div>";
    cadena += "             <span class='panel-subtitle cell-detail-producto'>Cantidad : "+cantidad+" "+unidadmedida+"</span>";
    cadena += "             <span class='panel-subtitle cell-detail-producto'>Paquetes : "+paquete +"</span>";
    cadena += "             <span class='panel-subtitle cell-detail-producto'>Peso Total : "+  pesototal + " <strong> Peso Total "+pesototal+" </strong></span>";
    cadena += "         </div>";
    cadena += "     </div>";
    cadena += "</div>";
    $(".detalleproducto").append(cadena);
}


function limpiar_input_producto(){
    $('#cantidad').val('');
    $('#paquete').val('');
    $('#pesototal').val('');    
}


function agregar_modal_detalle_producto_picking(codtrans,detProd){    
    $('.lista_tabla_prod .odd').remove();
    var cadena = '';          
    cadena += "<tr class='filaprod' data_producto_id ='" + detProd.producto_id + "' id ='" + detProd.id + "' idtrans ='" + detProd.transferencia_id + "'";
    cadena += "  producto_peso = '"+ detProd.producto_peso + "' cantidad_pendiente ='" + detProd.cantidad_pendiente +"' producto_nombre ='"+ detProd.producto_nombre +"' tipo_operacion = 'TRANSFERENCIA'>";       
    cadena += "  <td>" + codtrans + "</td>";
    cadena += "  <td> TRANSFERENCIA </td>";
    cadena += "  <td>" + detProd.producto_nombre + "</td>";
    cadena += "  <td>" + detProd.producto_peso + "</td>";
    cadena += "  <td>" + detProd.cantidad + "</td>";
    cadena += "  <td>" +  detProd.cantidad_pendiente + "</td>";
    cadena += "  <td width='10%'><input type='text' id='" + detProd.id + "can' name='cantidad_atender_modal' autocomplete='off'"; 
    cadena +=             "value='0.00' class='form-control input-sm importe precio_modal'></td>"; 
    cadena += "  <td width='10%'><input type='text' id='" + detProd.COD_PRODUCTO + "canexc' name='cantidad_excedente_modal' autocomplete='off'"; 
    cadena +=             "value='0.00' class='form-control input-sm importe cantidad_excedente'></td>"; 
    cadena += "</tr>";
    $(".lista_tabla_prod").append(cadena);
}

function agregar_modal_detalle_venta_picking(codorden,detProd){    
    $('.lista_tabla_prod .odd').remove();

    var cadena = '';          
    cadena += "<tr class='filaprod' data_producto_id ='" + detProd.COD_PRODUCTO + "' id ='" + detProd.COD_TABLA + "' idtrans ='" + detProd.COD_TABLA + "'";
    cadena += " producto_peso = '"+ detProd.producto_peso + "' cantidad_pendiente ='" + detProd.CAN_PENDIENTE +"' producto_nombre ='"+ detProd.TXT_NOMBRE_PRODUCTO +"' tipo_operacion = 'ORDEN' >";       
    cadena += "  <td>" + codorden + "</td>";
    cadena += "  <td> ORDEN </td>";
    cadena += "  <td>" + detProd.TXT_NOMBRE_PRODUCTO + "</td>";
    cadena += "  <td>" + detProd.CAN_PESO_PRODUCTO + "</td>";
    cadena += "  <td>" + detProd.CAN_PRODUCTO + "</td>";
    cadena += "  <td>" +  detProd.CAN_PENDIENTE + "</td>";
    cadena += "  <td width='10%'><input type='text' id='" + detProd.COD_PRODUCTO + "can' name='cantidad_atender_modal' autocomplete='off'"; 
    cadena +=             "value='0.00' class='form-control input-sm importe precio_modal'></td>"; 
    cadena += "  <td width='10%'><input type='text' id='" + detProd.COD_PRODUCTO + "canexc' name='cantidad_excedente_modal' autocomplete='off'"; 
    cadena +=             "value='0.00' class='form-control input-sm importe cantidad_excedente'></td>"; 
    cadena += "</tr>";
    $(".lista_tabla_prod").append(cadena);
}

function eliminar_modal_detalle_producto_picking(codtrans,detProd){    
    $('#'+detProd.id).remove();    
}

function eliminar_modal_detalle_venta_picking(codtrans){    
    $('#'+codtrans).remove();    
}

function dataenviarproducto(){
    var data = [];
    $(".lista_tabla_prod tbody tr").each(function(){
        idtrans             = $(this).attr('idtrans');
        idtransdet          = $(this).attr('id');
        producto_id         = $(this).attr('data_producto_id');
        tipo_operacion      = $(this).attr('tipo_operacion');
        cantidad_atender    = $(this).find('.precio_modal').val();
        cantidad_excedente  = $(this).find('.cantidad_excedente').val();
        cantidad_pendiente  = $(this).attr('cantidad_pendiente');
        
        cant = Number(cantidad_atender);

        if (cant && cant>0){
            data.push({
                trans_id        : idtrans,
                transdet_id     : idtransdet,
                tipo_operacion  : tipo_operacion,
                producto_id     : producto_id,
                cantidad_atender    : cant,
                cantidad_pendiente  : cantidad_pendiente,
                cantidad_excedente  : cantidad_excedente,
            });
        }
    });
    return data;
}


