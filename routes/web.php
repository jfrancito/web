<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/********************** USUARIOS *************************/
// header('Access-Control-Allow-Origin:  *');
// header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: *');

Route::group(['middleware' => ['guestaw']], function () {

	Route::any('/', 'UserController@actionLogin');
	Route::any('/login', 'UserController@actionLogin');
	Route::any('/acceso', 'UserController@actionAcceso');
	Route::any('/accesobienvenido/{idempresa}/{idcargo}', 'UserController@actionAccesoBienvenido');
	
}); 

Route::get('/cerrarsession', 'UserController@actionCerrarSesion');
Route::get('/cambiarperfil', 'UserController@actionCambiarPerfil');
Route::get('/despacho/print-pdf', [ 'as' => 'despacho.printpdf', 'uses' => 'OrdenPedidoController@RepSalida']);
Route::any('/excel-orden-despacho-email/{idopcion}/{idordendespacho}', 'AtenderPedidoDespachoController@actionExcelOrdenDespachoEmail');
Route::any('/pedido-estado-excel-automatico', 'OrdenPedidoReporteController@actionPedidoEstadoExcelAutomatico');
Route::any('/contrato-pendiente', 'OrdenPedidoReporteController@actionContratoPendiente');
Route::any('/documentos-sin-enviar-excel-automatico', 'OrdenPedidoReporteController@actionDocumentoSinEnviarExcelAutomatico');


Route::get('/leerxml', 'LeerxmlController@actionLeerXml');
Route::get('/generartoken', 'LeerxmlController@actionGenerarToken');
Route::get('/consultarcpe', 'LeerxmlController@actionConsultarCpe');



Route::group(['middleware' => ['authaw']], function () {


	Route::get('/bienvenido', 'UserController@actionBienvenido');
	Route::get('/gestion-de-analitica-ventas/{idopcion}', 'AnalisisEstadisticosController@actionVentas');
	Route::any('/ajax-listado-de-analitica', 'AnalisisEstadisticosController@actionAjaxListarVentas');



	Route::any('/gestion-de-usuarios/{idopcion}', 'UserController@actionListarUsuarios');
	Route::any('/agregar-usuario/{idopcion}', 'UserController@actionAgregarUsuario');
	Route::any('/modificar-usuario/{idopcion}/{idusuario}', 'UserController@actionModificarUsuario');
	Route::any('/ajax-activar-perfiles', 'UserController@actionAjaxActivarPerfiles');

	Route::any('/gestion-de-roles/{idopcion}', 'UserController@actionListarRoles');
	Route::any('/agregar-rol/{idopcion}', 'UserController@actionAgregarRol');
	Route::any('/modificar-rol/{idopcion}/{idrol}', 'UserController@actionModificarRol');

	Route::any('/gestion-de-permisos/{idopcion}', 'UserController@actionListarPermisos');
	Route::any('/ajax-listado-de-opciones', 'UserController@actionAjaxListarOpciones');
	Route::any('/ajax-activar-permisos', 'UserController@actionAjaxActivarPermisos');

	Route::any('/gestion-de-precio-producto/{idopcion}', 'ProductoController@actionPrecioProducto');
	Route::any('/ajax-guardar-precio-producto', 'ProductoController@actionAjaxGuardarPrecioProducto');

	Route::any('/gestion-de-configuracion-producto/{idopcion}', 'ProductoController@actionConfiguracionProducto');
	Route::any('/ajax-guardar-configuracion-producto', 'ProductoController@actionAjaxGuardarConfiguracionProducto');
	Route::any('/ajax-guardar-producto-indmobil', 'ProductoController@actionAjaxGuardarProductoIndmobil');


	Route::get('/gestion-de-regla-del-producto/{idopcion}', 'AsignarReglaController@actionListarClienteRegla');
	Route::any('/ajax-modal-detalle', 'AsignarReglaController@actionAjaxModalDetalle');
	Route::any('/ajax-modal-detalle-precio-regular', 'AsignarReglaController@actionAjaxModalDetallePrecioRegular');
	Route::any('/ajax-detalle-regla', 'AsignarReglaController@actionAjaxDetalleRegla');	
	Route::any('/ajax-agregar-regla', 'AsignarReglaController@actionAjaxAgregarRegla');
	Route::any('/ajax-agregar-regla-precio-regular', 'AsignarReglaController@actionAjaxAgregarReglaPrecioRegular');
	Route::any('/ajax-precio-regular-descuento', 'AsignarReglaController@actionAjaxPrecioRegularDescuento');


	Route::any('/ajax-actualizar-lista-regla', 'AsignarReglaController@actionAjaxActualizarListaRegla');
	Route::any('/ajax-actualizar-modal-regla', 'AsignarReglaController@actionAjaxActualizarModalRegla');
	Route::any('/ajax-actualizar-lista-regla-pr', 'AsignarReglaController@actionAjaxActualizarListaReglaPrecioRegular');
	Route::any('/ajax-actualizar-modal-regla-pr', 'AsignarReglaController@actionAjaxActualizarModalReglaPrecioRegular');
	Route::any('/ajax-eliminar-regla', 'AsignarReglaController@actionAjaxEliminarRegla');
	Route::any('/ajax-guardar-precio-producto-contrato', 'AsignarReglaController@actionAjaxGuardarPrecioProductoContrato');
	Route::any('/ajax-cambiar-estado-contrato', 'AsignarReglaController@actionAjaxCambiarEstadoContrato');

	Route::any('/gestion-de-regla-de-negociacion/{idopcion}', 'ProductoController@actionListarReglaNegociacion');
	Route::any('/agregar-regla-negociacion/{idopcion}', 'ProductoController@actionAgregarNegociacion');
	Route::any('/modificar-regla-negociacion/{idopcion}/{idregla}', 'ProductoController@actionModificarNegociacion');


	Route::any('/gestion-de-regla-de-precio-producto/{idopcion}', 'ProductoController@actionListarReglaPrecio');
	Route::any('/agregar-regla-precio/{idopcion}', 'ProductoController@actionAgregarPrecio');
	Route::any('/modificar-regla-precio/{idopcion}/{idregla}', 'ProductoController@actionModificarPrecio');

	Route::any('/ajax-lista-reglas-descuento', 'ProductoController@actionListaReglasDescuento');

	Route::any('/gestion-masiva-regla-precio/{idopcion}/{idregla}', 'GestionReglaController@actionGestionMasivaReglaPrecio');
	Route::any('/asignar_reglas_masiva', 'GestionReglaController@actionAsignarReglas');
	Route::any('/ajax-lista-contrato-producto_masivo', 'GestionReglaController@actionAjaxListaContratoProductoMasiva');
	Route::any('/ajax-actualizar-reglas-masivas', 'GestionReglaController@actionAjaxActualizarReglasMasivas');
	Route::any('/ajax-elimnar-reglas-masivas', 'GestionReglaController@actionAjaxEliminarReglasMasivas');

	Route::any('/gestion-de-precio-regular-producto/{idopcion}', 'GestionProductoController@actionGestionMasivaPrecioProducto');
	Route::any('/ajax-lista-precio-producto_masivo', 'GestionProductoController@actionAjaxListaPrecioProductoMasiva');
	Route::any('/ajax-actualizar-precio-producto-masivas', 'GestionProductoController@actionAjaxActualizarPrecioProductoMasivas');

	Route::any('/gestion-de-regla-de-cupon-producto/{idopcion}', 'ProductoController@actionListarReglaCupones');
	Route::any('/agregar-regla-cupon/{idopcion}', 'ProductoController@actionAgregarCupon');
	Route::any('/modificar-regla-cupon/{idopcion}/{idregla}', 'ProductoController@actionModificarCupon');
	Route::any('/ajax-tramo-deuda', 'OrdenPedidoController@actionAjaxDeudaSectorizada');
	
	Route::any('/gestion-de-regla-de-precio-regular/{idopcion}', 'ProductoController@actionListarReglaPrecioRegular');
	Route::any('/agregar-regla-precio-regular/{idopcion}', 'ProductoController@actionAgregarReglaPrecioRegular');
	Route::any('/modificar-regla-precio-regular/{idopcion}/{idregla}', 'ProductoController@actionModificarPrecioRegular');

	Route::any('/ajax-generarcupon', 'ProductoController@actionAjaxGenerarCupon');

	Route::any('/gestion-de-toma-de-pedido/{idopcion}', 'OrdenPedidoController@actionListarPedido');
	Route::any('/agregar-orden-pedido/{idopcion}', 'OrdenPedidoController@actionAgregarOrdenPedido');
	Route::any('/ajax-direcion-cliente', 'OrdenPedidoController@actionAjaxDireccioncliente');
	Route::any('/ajax-regla-producto', 'OrdenPedidoController@actionAjaxReglaProducto');
	Route::any('/ajax-modal-detalle-pedido-rechazar', 'OrdenPedidoController@actionAjaxDetallePedidoRechazar');
	Route::any('/imprimir-pedido/{idpedido}', 'OrdenPedidoReporteController@actionImprimirPedido');
	Route::any('/ajax-modal-detalle-deuda', 'OrdenPedidoController@actionImprimirDetalleDeuda');
	Route::any('/ajax-actualizar-deuda-cliente', 'OrdenPedidoController@actionActualizarDeudaCliente');

	Route::any('/imprimir-pedido-transportista/{idpedido}/{accion}/{detalle}', 'OrdenPedidoReporteController@actionImprimirPedidoTransportista');
	Route::any('/obsequio-orden-pedido/{idpedido}/{idopcion}', 'OrdenPedidoController@actionObsequioOrdenPedido');
	Route::any('/ajax-regla-producto-obsequio', 'OrdenPedidoController@actionAjaxReglaProductoObsequio');

	Route::any('/gestion-de-orden-de-pedido/{idopcion}', 'OrdenPedidoController@actionListarTomaPedido');
	Route::any('/ajax-listado-de-toma-pedidos', 'OrdenPedidoController@actionAjaxListarTomaPedido');
	Route::any('/ajax-modal-detalle-pedido', 'OrdenPedidoController@actionAjaxDetallePedido');
	Route::any('/ajax-modal-detalle-pedido-transportista', 'OrdenPedidoController@actionAjaxDetallePedidoTransportista');


	Route::any('/gestion-de-orden-de-pedido-anulacion/{idopcion}', 'OrdenPedidoController@actionListarTomaPedidoAnulacion');
	Route::any('/ajax-modal-detalle-pedido-anulacion', 'OrdenPedidoController@actionAjaxDetallePedidoAnulacion');
	Route::any('/ajax-modal-detalle-pedido-anulacion-observacion', 'OrdenPedidoController@actionAjaxModalPedidoAnulacionObservcion');
	Route::any('/enviar-a-rechazar-siete-dias/{idopcion}', 'OrdenPedidoController@actionEnviarRechazarSieteDias');



	Route::any('/enviar-a-osiris/{idopcion}', 'OrdenPedidoController@actionEnviarOsiris');
	Route::any('/enviar-a-rechazar/{idopcion}', 'OrdenPedidoController@actionEnviarRechazar');
	Route::any('/enviar-a-osiris-rechazar/{idopcion}', 'OrdenPedidoController@actionEnviarOsirisRechazar');
	Route::any('/ajax-modal-detalle-pedido-mobil', 'OrdenPedidoController@actionAjaxDetallePedidoMobil');
	Route::any('/ajax-listado-de-toma-pedidos-vendedor', 'OrdenPedidoController@actionAjaxListarTomaPedidoVendedor');
	Route::any('/gestion-de-orden-de-pedido-autorizacion/{idopcion}', 'OrdenPedidoController@actionListarTomaPedidoAutorizacion');
	Route::any('/ajax-listado-de-toma-pedidos-autorizacion', 'OrdenPedidoController@actionAjaxListarTomaPedidoAutorizacion');
	Route::any('/ajax-modal-detalle-pedido-autorizacion', 'OrdenPedidoController@actionAjaxDetallePedidoAutorizacion');
	Route::any('/autorizar-pedido/{idopcion}', 'OrdenPedidoController@actionAutorizarPedido');
	Route::any('/no-autorizar-pedido/{idopcion}', 'OrdenPedidoController@actionNoAutorizarPedido');
	Route::any('/ajax-guardar-cantidad-producto-pedido', 'OrdenPedidoController@actionAjaxGuardarCantidadProductoPedido');
	Route::any('/ajax-guardar-precio-producto-pedido', 'OrdenPedidoController@actionAjaxGuardarPrecioProductoPedido');
	Route::any('/ajax-modal-deuda-cliente', 'OrdenPedidoController@actionAjaxDeudaCliente');
	Route::any('/ajax-modal-limite-credito', 'OrdenPedidoController@actionAjaxLimiteCredito');
	Route::any('/ajax-relacion-producto-obsequio', 'OrdenPedidoController@actionAjaxObsequioRelacion');




	/************************************** GENERALES ********************************************/
	Route::any('/ajax-canal-responsable', 'GeneralAjaxController@actionCanalResponsable');
	Route::any('/ajax-cliente-responsable', 'GeneralAjaxController@actionClienteResponsable');
	Route::any('/ajax-subcanal_canal-responsable', 'GeneralAjaxController@actionSubCanalCanalResponsable');

	/************************************** Reportes ********************************************/

	Route::any('/reporte-reglas-cliente/{idopcion}', 'ReglaReporteController@actionReglasXcliente');
	Route::any('/ajax-reporte-lista-regla-clientes', 'ReglaReporteController@actionAjaxReglasxCliente');
	Route::any('/reglas-cliente-excel/{idcuenta}/{idtipoprecio}', 'ReglaReporteController@actionReglasClienteExcel');
	Route::any('/reglas-cliente-pdf/{idcuenta}/{idtipoprecio}', 'ReglaReporteController@actionReglaClientePDF');

	Route::any('/reporte-precio-producto-cliente/{idopcion}', 'ProductoReporteController@actionPrecioProductoXcliente');
	Route::any('/ajax-reporte-lista-precio-producto', 'ProductoReporteController@actionAjaxProductosxCliente');
	Route::any('/precio-producto-cliente-excel/{idcuenta}/{idtipoprecio}', 'ProductoReporteController@actionPrecioProductoClienteExcel');
	Route::any('/precio-producto-cliente-pdf/{idcuenta}/{idtipoprecio}', 'ProductoReporteController@actionPrecioProductoClientePDF');

	Route::any('/reporte-evolucion-precio-producto-cliente/{idopcion}', 'ProductoReporteController@actionEvolucionPrecioProductoXcliente');
	Route::any('/ajax-reporte-lista-evolucion-precio-producto', 'ProductoReporteController@actionAjaxEvolucionProductosxCliente');
	Route::any('/evolucion-precio-producto-cliente-excel/{idcuenta}/{fechadia}', 'ProductoReporteController@actionEvolucionPrecioProductoClienteExcel');
	Route::any('/evolucion-precio-producto-cliente-pdf/{idcuenta}/{fechadia}', 'ProductoReporteController@actionEvolucionPrecioProductoClientePDF');

	Route::any('/reporte-precio-canal-mayorista/{idopcion}', 'ProductoReporteController@actionPrecioCanalMayorista');
	Route::any('/ajax-reporte-precio-canal-mayorista', 'ProductoReporteController@actionAjaxPrecioCanalMayorista');
	Route::any('/precio-producto-canal-mayorista-excel/{fechadia}', 'ProductoReporteController@actionPrecioCanalMayoristaExcel');
	Route::any('/precio-producto-canal-mayorista-pdf/{fechadia}', 'ProductoReporteController@actionPrecioCanalMayoristaPDF');




	Route::any('/reporte-anticipo-prestamo-masivo/{idopcion}', 'ContablilidadReporteController@actionAnticipoPrestamo');
	//Route::any('/ajax-reporte-precio-canal-mayorista', 'ProductoReporteController@actionAjaxPrecioCanalMayorista');
	//Route::any('/precio-producto-canal-mayorista-excel/{fechadia}', 'ProductoReporteController@actionPrecioCanalMayoristaExcel');
	Route::any('/anticipo-prestamo-masivo-pdf/{centro_id}/{fechainicio}/{fechafin}', 'ContablilidadReporteController@actionAnticipoPrestamoPDF');

	Route::any('/reporte-estado-cuenta-vendedor/{idopcion}', 'EstadoCuentaReporteController@actionEstadoCuentaVendedor');



	Route::any('/reporte-pedidos-estados/{idopcion}', 'OrdenPedidoReporteController@actionPedidoXEstado');
	Route::any('/ajax-reporte-pedido-estado', 'OrdenPedidoReporteController@actionAjaxPedidoEstado');
	Route::any('/pedido-estado-excel/{finicio}/{fechafin}/{estado_id}/{centro_id}', 'OrdenPedidoReporteController@actionPedidoEstadoExcel');

	/*Route::any('/precio-producto-canal-mayorista-pdf/{fechadia}', 'ProductoReporteController@actionPrecioCanalMayoristaPDF');*/


	Route::any('/reporte-pago-detracciones/{idopcion}', 'DespachoReporteController@actionPagoDetraciones');
	Route::any('/ajax-reporte-pago-detracciones', 'DespachoReporteController@actionAjaxReportePagoDetraccion');
	Route::any('/descargar-detraccion-guias/{cod_guia}', 'DespachoReporteController@actionPagoDetraccionExcel');





	Route::any('/gestion-de-cuentas/{idopcion}', 'CarteraController@index');
	Route::any('/cartera', 'CarteraController@ListarCuentas');
	Route::any('/cartera/saldos', 'CarteraController@SaldoCuenta');
	Route::any('/categoria/listarCategoria','CategoriaController@ListarCategoria');
	Route::any('/reglacredito/actualizar','AsignarReglaController@ActualizarReglaCredito');
	Route::any('/reglacredito-actualizar-masivo','AsignarReglaController@ActualizarReglaCreditoMasivo');



	Route::any('/gestion-de-nota-credito-autoservicios/{idopcion}', 'NotaCreditoController@actionListarNotaCreditoAutoservicio');
	Route::any('/agregar-reglas-orden-cen/{idopcion}', 'NotaCreditoController@actionAgregarReglaOrdenCen');
	Route::any('/ajax-reglas-cliente-fechas', 'NotaCreditoController@actionAjaxReglasClienteFechas');
	Route::any('/ajax-lista-oredencen-nota-credito', 'NotaCreditoController@actionAjaxListaOrdenCenNotaCredito');
	Route::any('/ajax-modal-detalle-documento', 'NotaCreditoController@actionAjaxModalDetalleDocumento');
	Route::any('/asociar-nota-credito/{idopcion}/{iddocumentonotacredito}', 'NotaCreditoController@actionAsociarNotaCredito');
	Route::any('/ajax-nro-documento', 'NotaCreditoController@actionAjaxNroDocumento');
	Route::any('/agregar-nota-credito', 'NotaCreditoController@actionGuardarNotaCredito');
	Route::any('/ajax-glosa-documento', 'NotaCreditoController@actionAjaxGlosaDocumento');
	Route::any('/ver-asignacion-nota-credito/{idopcion}/{iddocumentonotacredito}', 'NotaCreditoController@actionVerAsignacionNotaCredito');
	Route::any('/agregar-orden-cen/{idopcion}/{iddocumentonotacredito}', 'NotaCreditoController@actionAgregarOrdenCen');
	Route::any('/eliminar-orden-cen/{idopcion}/{iddocumentonotacredito}', 'NotaCreditoController@actionEliminarOrdenCen');
	Route::any('/ajax-eliminar-orden-cen', 'NotaCreditoController@actionAjaxEliminarOrdenCen');
	Route::any('/ajax-lista-agregar-oredencen-nota-credito', 'NotaCreditoController@actionAjaxListaAgregarOrdenCenNotaCredito');
	Route::any('/ajax-agregar-regla-orden-cen', 'NotaCreditoController@actionAjaxAgregarReglaOrdenCen');
	Route::any('/ajax-lista-detalle-oredencen-nota-credito', 'NotaCreditoController@actionAjaxListaDetalleOrdenCenNotaCredito');

	Route::any('/gestion-de-generacion-nota-credito-masivo/{idopcion}', 'NotaCreditoMasivoController@actionListarNotaCreditoMasivo');

	Route::any('/crear-nota-credito-masiva/{idopcion}', 'NotaCreditoMasivoController@actionCrearNotaCreditoMasivo');
	Route::any('/ajax-modal-lista-orden-venta-nc', 'NotaCreditoMasivoController@actionAjaxModalListaOrdenVentaCuenta');


	Route::any('/ajax-modal-lista-orden-venta-fechas', 'NotaCreditoMasivoController@actionAjaxModalListaOrdenVentaFechas');
	Route::any('/ajax-orden-venta-boletas', 'NotaCreditoMasivoController@actionAjaxOrdenVentaBoletas');
	Route::any('/ajax-modal-detalle-producto', 'NotaCreditoMasivoController@actionAjaxModalDetalleProducto');
	Route::any('/ajax-modal-generar-nota-credito', 'NotaCreditoMasivoController@actionAjaxModalGenerarNotaCredito');
	Route::any('/ajax-detalle-producto-boleta-nc', 'NotaCreditoMasivoController@actionAjaxDetalleProductoBoletaNC');

	//detracciones
	Route::any('/gestion-de-detracciones/{idopcion}', 'DetraccionController@index');
	Route::any('/detraccion/orden', 'DetraccionController@getOrden');
	Route::any('/detraccion/procesar', 'DetraccionController@ProcesarPagoDetraccion');
	Route::any('/detraccion/getserie', 'DetraccionController@GetSerie');
	/*Route::any('/ajax-lista-precio-producto_masivo', 'GestionProductoController@actionAjaxListaPrecioProductoMasiva');
	Route::any('/ajax-actualizar-precio-producto-masivas', 'GestionProductoController@actionAjaxActualizarPrecioProductoMasivas');*/


    //DESPACHO (generar pedido)
	Route::any('/gestion-de-generar-pedido/{idopcion}', 'PedidoDespachoController@actionListarGeneracionPedido');

	Route::any('/ajax-lista-pedidos-despacho', 'PedidoDespachoController@actionAjaxListaPedidosDespacho');
	Route::any('/crear-orden-pedido-despacho/{idopcion}', 'PedidoDespachoController@actionCrearPedidoDepacho');
	Route::any('/ajax-modal-lista-orden-cen-producto', 'PedidoDespachoController@actionAjaxModalListaOrdenCenProducto');
	Route::any('/ajax-modal-agregar-productos-orden-cen', 'PedidoDespachoController@actionAjaxModalAgregarProductosOrdenCen');
	Route::any('/ajax-modal-agregar-orden-cen-pedido', 'PedidoDespachoController@actionAjaxModalAgregarOrdenCenPedido');
	Route::any('/ajax-modal-agregar-producto-pedido', 'PedidoDespachoController@actionAjaxModalAgregarProductosPedido');
	Route::any('/ajax-pedido-crear-movil', 'PedidoDespachoController@actionAjaxPedidoCrearMovil');
	Route::any('/ajax-pedido-crear-movil-individuales', 'PedidoDespachoController@actionAjaxPedidoCrearMovilIndividuales');
	Route::any('/ajax-pedido-eliminar-fila', 'PedidoDespachoController@actionAjaxPedidoEliminarFila');
	Route::any('/ajax-modificar-cantidad-producto-fila', 'PedidoDespachoController@actionAjaxModificarCantidadProductoFila');
	Route::any('/ajax-modificar-muestra-producto-fila', 'PedidoDespachoController@actionAjaxModificarMuestraProductoFila');
	Route::any('/ajax-modificar-muestra-producto-fila-separado', 'PedidoDespachoController@actionAjaxModificarMuestraProductoFilaSeparado');


	Route::any('/ajax-pedido-modificar-fecha-de-entrega', 'PedidoDespachoController@actionAjaxPedidoModificarFechaEntrega');
	Route::any('/ajax-modal-configuracion-producto-cantidad', 'PedidoDespachoController@actionAjaxModalConfiguracionProductoCantidad');
	Route::any('/ajax-modificar-configuracion-del-producto', 'PedidoDespachoController@actionAjaxModificarConfiguracionDelProducto');
	Route::any('/ajax-pedido-crear-update-pedido-despacho-centro', 'PedidoDespachoController@actionAjaxPedidoCrearUpdatePedidoDespachoCentro');
	Route::any('/ajax-pedido-crear-mobil-33-palets', 'PedidoDespachoController@actionAjaxCrearMobil33Palets');


	Route::any('/gestion-pedido-orden-despacho/{idopcion}/{idordendespacho}', 'PedidoDespachoController@actionGestionOrdenDespacho');
	Route::any('/ajax-modal-lista-orden-gestion-producto', 'PedidoDespachoController@actionAjaxModalListaOrdenGestionProducto');
	Route::any('/ajax-modal-agregar-producto-pedido-gestion', 'PedidoDespachoController@actionAjaxModalAgregarProductosPedidoGestion');
	Route::any('/ajax-rechazar-producto-gestion', 'PedidoDespachoController@actionAjaxRechazarProductoGestion');


	Route::any('/gestion-de-atender-pedido/{idopcion}', 'AtenderPedidoDespachoController@actionListarAtenderPedido');
	Route::any('/atender-orden-despacho/{idopcion}/{idordendespacho}', 'AtenderPedidoDespachoController@actionAtenderOrdenDespacho');
	Route::any('/ajax-lista-atender-pedidos-despacho', 'AtenderPedidoDespachoController@actionAjaxListaAtenderPedidosDespacho');
	Route::any('/ajax-modificar-cantidad-atender-producto-id', 'AtenderPedidoDespachoController@actionAjaxAjaxModificarCantidadAtenderProducto');
	Route::any('/ajax-modal-lista-orden-atender-producto', 'AtenderPedidoDespachoController@actionAjaxModalListaOrdenAtenderProducto');
	Route::any('/ajax-modal-agregar-producto-pedido-atender', 'AtenderPedidoDespachoController@actionAjaxModalAgregarProductosPedidoAtender');
	Route::any('/ajax-asignar-muestras-mobil', 'AtenderPedidoDespachoController@actionAjaxAsignarMuestrasMobil');
	Route::any('/ajax-imprimir-pedido-despacho', 'AtenderPedidoDespachoController@actionAjaxImprimirPedidoDespacho');
	Route::any('/ajax-modal-ver-impresion', 'AtenderPedidoDespachoController@actionModalImprimirPedidoDespacho');
	Route::any('/ajax-limpiar-impresion', 'AtenderPedidoDespachoController@actionLimpiarImpresion');
	Route::any('/ajax-modal-impresion', 'AtenderPedidoDespachoController@actionImpresion');
	Route::any('/ajax-modal-detalle-pedido-imprimir', 'AtenderPedidoDespachoController@actionModalDetalleImprimir');
	Route::any('/ajax-imprimir-pedido-despacho-xcantidad', 'AtenderPedidoDespachoController@actionAjaxImprimirPedidoDespachoxCantidad');
	Route::any('/ajax-imprimir-pedido-despacho-xpalets', 'AtenderPedidoDespachoController@actionAjaxImprimirPedidoDespachoxPalets');






	Route::any('/ajax-pedido-atender-modificar-fecha-de-entrega', 'AtenderPedidoDespachoController@actionAjaxPedidoAtenderModificarFechaEntrega');
	Route::any('/ajax-pedido-atender-modificar-origen', 'AtenderPedidoDespachoController@actionAjaxPedidoAtenderModificarOrigen');
	Route::any('/ajax-pedido-atender-modificar-cantidad-atender', 'AtenderPedidoDespachoController@actionAjaxPedidoAtenderModificarCantidadAtender');
	Route::any('/ajax-calcular-stock-almacen-lote', 'AtenderPedidoDespachoController@actionAjaxCalcularStockAlmacenLote');
	Route::any('/ajax-combo-lote-almacen', 'AtenderPedidoDespachoController@actionAjaxComboLoteAlmacen');
	Route::any('/ajax-combo-almacen-destino', 'AtenderPedidoDespachoController@actionAjaxComboAlmacenDestino');
	Route::any('/ajax-combo-almacen-origen', 'AtenderPedidoDespachoController@actionAjaxComboAlmacenOrigen');
	Route::any('/ajax-combo-cuenta-servicio', 'AtenderPedidoDespachoController@actionAjaxComboCuentaServicio');
	Route::any('/ajax-lista-productos-transferencia-pt', 'AtenderPedidoDespachoController@actionAjaxListaProductosTransferenciaPt');
	Route::any('/crear-transferencia-pt/{idopcion}/{idordendespacho}', 'AtenderPedidoDespachoController@actionCrearTransferenciaPt');
	Route::any('/ajax-agregar-servicio', 'AtenderPedidoDespachoController@actionAjaxAgregarServicio');
	Route::any('/ajax-rechazar-producto', 'AtenderPedidoDespachoController@actionAjaxRechazarProducto');
	Route::any('/ajax-orden-cen-mobil-modal', 'AtenderPedidoDespachoController@actionAjaxOrdenCenMobilModal');
	Route::any('/excel-orden-despacho/{idopcion}/{idordendespacho}', 'AtenderPedidoDespachoController@actionExcelOrdenDespacho');
	Route::any('/ajax-modificar-muestra-pedido-creado-fila-separado', 'AtenderPedidoDespachoController@actionAjaxModificarMuestraPedidoCreadoFilaSeparado');
	Route::any('/ajax-actualizar-lista-muestra-mobil', 'AtenderPedidoDespachoController@actionAjaxActualizarListaMuestraMobil');


	Route::any('/gestion-comision-planilla/{idopcion}', 'ComisionPlanillaController@actionListarComisionPlanilla');
	Route::any('/ver-detalle-comisiones/{idopcion}/{codperiodo}', 'ComisionPlanillaController@actionVerDetalleComision');
	Route::any('/cambiar-estado-comsion/{idopcion}', 'ComisionPlanillaController@actionCambiarEstadoComision');
	Route::any('/descargar-excel-comisiones/{idopcion}/{codperiodo}/{codcategoriajefe}/{proviene}', 'ComisionPlanillaController@actionDescargarExcelComisiones');


	Route::any('/gestion-carros-ingreso-salida/{idopcion}', 'DespachoCarroController@actionListarCarros');
	Route::any('/ajax-listar-carros-ingreso-salida', 'DespachoCarroController@actionAjaxListarCarros');
	Route::any('/ajax-modal-detalle-carro', 'DespachoCarroController@actionAjaxModalDetalleCarro');
	Route::any('/editar-carro-despacho', 'DespachoCarroController@actionEditarCarroDespacho');
	Route::get('/enviar-mensaje-whatsapp', 'PruebaController@actionEnviarMensajeWhatsapp');

	Route::any('/gestion-almacen-activos/{idopcion}', 'AlmacenActivoFijoController@actionListarActivosFijos');
	Route::any('/registrar-activo-fijo/{idproducto}/{iddocumento}', 'ActivoFijoController@registrarActivoFijo');
	Route::any('/gestion-almacen-activos-transferidos/{idopcion}', 'AlmacenActivoFijoTransferidoController@actionListarActivosFijosTransferidos');
	Route::any('/modificar-activo-fijo/{idactivofijo}', 'ActivoFijoController@modificarActivoFijo');
	Route::any('/depreciacion-activo-fijo/{idopcion}', 'DepreciacionActivoFijoController@index');
	Route::any('/catalogo-activos-fijos/{idopcion}', 'ActivoFijoController@catalogoActivosFijos');	
	Route::any('/registrar-obra-activo-fijo/', 'ActivoFijoController@registrarObraActivoFijo');	
	Route::any('/exportar-formato-iatr/{idopcion}', 'ExportarFormatoIatrController@exportarFormatoIATR');	 
	Route::any('/exportar-formato-iach/{idopcion}', 'ExportarFormatoIachController@exportarFormatoIACH');	 	

	Route::any('/gestion-cuadro-comisiones/{idopcion}', 'ComisionPlanillaController@actionCuadroComisiones');



	Route::any('/gestion-de-regla-de-ampliacion-linea-credito/{idopcion}', 'ReglaController@actionListarReglaLineaCredito');
	Route::any('/agregar-regla-linea-credito/{idopcion}', 'ReglaController@actionAgregarReglaLineaCredito');
	Route::any('/ajax-lista-reglas-linea-credito', 'ReglaController@actionListaAjaxReglasLimiteCredito');

	Route::any('/gestion-de-regla-dias-vencimiento/{idopcion}', 'ReglaController@actionListarReglaDiasVencimiento');
	Route::any('/agregar-regla-dias-vencimiento/{idopcion}', 'ReglaController@actionAgregarReglaDiasVencimiento');
	Route::any('/ajax-lista-reglas-dias-vencimiento', 'ReglaController@actionListaAjaxReglasDiasVencimiento');

	Route::any('/gestion-de-asignar-regla-dias-vencimiento/{idopcion}', 'ReglaController@actionAsignarReglaDiasVencimiento');
	Route::any('/ajax-modal-lista-orden-venta-regla', 'ReglaController@actionAjaxModalListaOrdenVentaCuenta');
	Route::any('/ajax-modal-asignar-orden-venta-regla-dias-vencimiento', 'ReglaController@actionAjaxAsignarReglaDiasVencimiento');
	Route::any('/eliminar-regla-dias-vencimiento/{asignarregla_id}/{idopcion}', 'ReglaController@actionAjaxEliminarReglaDiasVencimiento');


	Route::any('/gestion-de-asignar-regla-limite-credito/{idopcion}', 'ReglaController@actionAsignarReglaLimiteCredito');
	Route::any('/ajax-modal-lista-cliente-jefe-regla', 'ReglaController@actionAjaxModalListaClienteJefeRegla');
	Route::any('/ajax-modal-asignar-cliente-regla-limite-credito', 'ReglaController@actionAjaxAsignarReglaLimiteCredito');
	Route::any('/eliminar-regla-limite-credito/{asignarregla_id}/{idopcion}', 'ReglaController@actionAjaxEliminarReglaLimiteCredito');

	//Route::any('/ajax-modal-lista-orden-venta-nc', 'NotaCreditoMasivoController@actionAjaxModalListaOrdenVentaCuenta');

	Route::any('/gestion-de-comisiones-periodo/{idopcion}', 'ComisionReporteController@actionComisionPeriodo');
	Route::any('/comision-periodo-producto-excel/{periodoinicio}/{periodofin}/{vendedor_id}', 'ComisionReporteController@actionComisionPeriodoExcel');

	//bonos
	Route::any('/gestion-de-bonos/{idopcion}', 'BonosController@actionListarBonos');
	Route::any('/agregar-bono/{idopcion}', 'BonosController@actionAgregarBono');
	Route::any('/ingresar-cuotas/{idopcion}/{idcuota}', 'BonosController@actionIngresarCuota');
	Route::any('/ajax-modal-configuracion-cuota-detalle', 'BonosController@actionAjaxModalConfiguracionCuotaDetalle');
	Route::any('/ajax-combo-subcanal-xcanal', 'BonosController@actionAjaxComboSubCanalxCanal');
	Route::any('/ajax-modal-modificar-configuracion-cuota-detalle', 'BonosController@actionAjaxModalModificarConfiguracionCuota');
	Route::any('/ajax-modal-clonar', 'BonosController@actionAjaxClonar');
	Route::any('/ajax-guardar-clonar', 'BonosController@actionAjaxGuardarClonar');
	Route::any('/ajax-modal-emitir-cuota', 'BonosController@actionAjaxModalEmitirCuota');
	Route::any('/ajax-guardar-emitir', 'BonosController@actionAjaxGuardarEmitir');
	//calcalubono
	Route::any('/gestion-calcular-bonos/{idopcion}', 'CalcularBonosController@actionListarPeriodoBonos');
	Route::any('/agregar-calculo-bono/{idopcion}', 'CalcularBonosController@actionAgregarCalculoBono');
	Route::any('/ingresar-calculo-vendedor/{idopcion}/{idperiodobono}', 'CalcularBonosController@actionIngresarCalculoVendedor');
	Route::any('/ajax-modal-configuracion-bono-vendedor', 'CalcularBonosController@actionAjaxModalConfiguracionBonoVendedor');
	Route::any('/ajax-modal-detalle-calculobono-rd', 'CalcularBonosController@actionAjaxModalDetalleCalculoBono');
	Route::any('/cambiar-estado-bono/{idopcion}', 'CalcularBonosController@actionCambiarEstadoBono');



	//-- Picking
	Route::any('/gestion-transferencia/{idopcion}', 'TransferenciaController@actionListarTransferencia');
	Route::any('/ajax-listado-transferencia', 'TransferenciaController@actionAjaxListarTransferencia');
	Route::any('/agregar-transferencia/{idopcion}/{idtranferencia}', 'TransferenciaController@actionAgregarTransferencia');	
	Route::any('/modificar-transferencia/{idopcion}/{idtransferencia}', 'TransferenciaController@actionModificarTransferencia');
	Route::any('/ajax-transferencia-cliente', 'TransferenciaController@actionAjaxTransferenciaCliente');
	Route::any('/ajax-detalle-producto', 'TransferenciaController@actionAjaxDetalleProducto');
	Route::any('/ajax-modal-detalle-transferencia', 'TransferenciaController@actionAjaxDetalleTransferencia');
	Route::any('/ajax-cambiar-estado-transferencia', 'TransferenciaController@actionAjaxCambiarEstadoTransferencia');
	Route::any('/autorizar-transferencia/{idopcion}', 'TransferenciaController@actionListarTransferencia');
	Route::any('/atender-transferencia/{idopcion}', 'TransferenciaController@actionListarTransferencia');
	Route::any('/imprimir-solicitud-transferencia/{idtransferencia}', 'PickingReporteController@actionImprimirSolicitudTransferencia');

	Route::any('/gestion-picking/{idopcion}', 'TransferenciaController@actionListarPicking');
	Route::any('/ajax-listado-picking', 'TransferenciaController@actionAjaxListarPicking');
	Route::any('/agregar-picking/{idopcion}/{idpicking}', 'TransferenciaController@actionAgregarPicking');
	Route::any('/ajax-modal-lista-transferencia-autorizada', 'TransferenciaController@actionAjaxModalListaTransferenciaAutorizada');
	Route::any('/ajax-modal-agregar-producto-picking', 'TransferenciaController@actionAjaxModalAgregarProductosPicking');
	Route::any('/ajax-modal-producto-individual', 'TransferenciaController@actionAjaxModalProductoIndividual');
	Route::any('/ajax-agregar-producto-individual', 'TransferenciaController@actionAjaxAgregarProductoIndividual');
	
	Route::any('/ajax-validar-cantidad-atender-picking', 'TransferenciaController@actionAjaxValidarCantidadesAtenderPicking');
	Route::any('/ajax-picking-eliminar-fila/', 'TransferenciaController@actionAjaxPickingEliminarFila');
	Route::any('/ajax-modal-detalle-picking', 'TransferenciaController@actionAjaxDetallePicking');
	Route::any('/ajax-cambiar-estado-picking', 'TransferenciaController@actionAjaxCambiarEstadoPicking');

	Route::any('/atender-picking/{idopcion}', 'PickingController@actionListarAtenderPicking');
	Route::any('/ajax-listado-atender-picking', 'PickingController@actionAjaxListarAtenderPicking');
	Route::any('/atender-picking/{idopcion}/{idpicking}', 'PickingController@actionAtenderPicking');	
	Route::any('/ajax-lista-productos-transferencia-picking', 'PickingController@actionAjaxListaProductosTransferenciaPicking');
	Route::any('/ajax-lista-productos-ordensalida-picking', 'PickingController@actionAjaxListaProductosOrdenSalidaPicking');
	Route::any('/crear-transferencia-picking/{idopcion}/{idpicking}', 'PickingController@actionCrearTransferenciaPicking');
	Route::any('/crear-ordensalida-picking/{idopcion}/{idpicking}', 'PickingController@actionCrearOrdenSalidaPicking');
	Route::any('/generar-detraccion-picking/{idopcion}/{idpicking}', 'PickingController@actionGenerarDetraccionPicking');
	Route::any('/ajax-calcular-detraccion', 'PickingController@actionAjaxCalcularDetraccion');
	Route::any('/guardar-detraccion-picking/{idopcion}', 'PickingController@actionGuardarDetraccionPicking');
	
	Route::any('/imprimir-picking-detraccion/{idpicking}', 'PickingReporteController@actionImprimirPickingDetraccion');
	Route::any('/imprimir-picking/{idpicking}', 'PickingReporteController@actionImprimirPicking');
	
	Route::any('/reporte-detraccion-diario/{idopcion}', 'PickingReporteController@actionDetraccionDiario');
	Route::any('/ajax-reporte-detraccion-diario', 'PickingReporteController@actionAjaxDetraccionDiario');
	Route::any('/reporte-detraccion-diario-pdf/{fechadia}', 'PickingReporteController@actionDetraccionDiarioPDF');
	Route::any('/ajax-combo-almacen-destino-pk', 'PickingController@actionAjaxComboAlmacenDestinoPk');
	Route::any('/ajax-combo-almacen-origen-pk', 'PickingController@actionAjaxComboAlmacenOrigenPk');
	
 	//----------------------------------------







   	Route::get('buscarcliente', function (Illuminate\Http\Request  $request) {
        $term = $request->term ?: '';
        $tags = App\WEBListaCliente::where('NOM_EMPR', 'like', '%'.$term.'%')
				->where('COD_EMPR','=',Session::get('empresas')->COD_EMPR)
				->where('COD_CENTRO','=',Session::get('centros')->COD_CENTRO)
				->take(100)
        		->pluck('NOM_EMPR', 'NOM_EMPR');
        $valid_tags = [];
        foreach ($tags as $id => $tag) {
            $valid_tags[] = ['id' => $id, 'text' => $tag];
        }
        return \Response::json($valid_tags);
    });


   	Route::get('buscarproducto', function (Illuminate\Http\Request  $request) {
        $term = $request->term ?: '';
        $tags = App\WEBPrecioProducto::join('WEB.LISTAPRODUCTOSAVENDER', 'COD_PRODUCTO', '=', 'producto_id')
        								->where('NOM_PRODUCTO', 'like', '%'.$term.'%')
										->where('empresa_id','=',Session::get('empresas')->COD_EMPR)
										->where('centro_id','=',Session::get('centros')->COD_CENTRO)
										->take(100)
										->pluck('NOM_PRODUCTO','producto_id');

        $valid_tags = [];
        foreach ($tags as $id => $tag) {
            $valid_tags[] = ['id' => $id, 'text' => $tag];
        }
        return \Response::json($valid_tags);
    });



   	Route::get('buscarempresadespacho', function (Illuminate\Http\Request  $request) {

        $term = $request->term ?: '';
        $tags = App\STDEmpresa::where('NOM_EMPR', 'like', '%'.$term.'%')
				->take(100)
        		->pluck('NOM_EMPR', 'NOM_EMPR');
        $valid_tags = [];
        foreach ($tags as $id => $tag) {
            $valid_tags[] = ['id' => $id, 'text' => $tag];
        }
        return \Response::json($valid_tags);

    });


   	Route::get('buscarempresaserviciodespacho', function (Illuminate\Http\Request  $request) {

        $term = $request->term ?: '';
        $tags = App\STDEmpresa::where('NOM_EMPR', 'like', '%'.$term.'%')
				->take(100)
				->select('COD_EMPR', DB::raw("(NOM_EMPR + ' ' + NRO_DOCUMENTO) AS NOM_EMPR_N"))
        		->pluck( 'NOM_EMPR_N','COD_EMPR');

        $valid_tags = [];
        foreach ($tags as $id => $tag) {
            $valid_tags[] = ['id' => $id, 'text' => $tag];
        }
        return \Response::json($valid_tags);

    });







   	Route::any('/enviocorreos', 'CorreoController@enviocorreo');
   	Route::any('/pruebaquery', 'PruebaController@pruebas');
   	Route::any('/power-bi', 'PruebaController@indicadoresISL');

});

