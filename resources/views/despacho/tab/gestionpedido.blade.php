<div class="panel panel-default panel-table">
  <div class="panel-heading">

    <div class='col-sm-3'>
      <b>Gestión del pedido</b>
    </div>

    <div class='col-sm-4'>
        <span class="label label-transferenciapt">Transferencia PT</span>
        <span class="label label-guia-utilizada">Guia ejecutada</span>
    </div>
    


    <div class="tools dropdown show">
      <div class="dropdown">
        <!-- <span class="icon toggle-loading mdi mdi-save guardarcambios" style='color:#34a853;' title="Guardar cambios"></span> -->
        <span class="icon mdi mdi-more-vert dropdown-toggle" id="menudespacho" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></span>
        <ul class="dropdown-menu" aria-labelledby="menudespacho" style="margin: 7px -169px 0px;">
          <li><a href="#" class='agregarproductogestion'>Agregar Producto</a></li>
          <li><a href="#" class='rechazarproductogestion'>Rechazar Producto</a></li>
<!--           <li><a href="#" class='cambiarfechaentrega'>Modificar fecha de carga</a></li>
          <li><a href="#" class='cambiarorigen'>Cambiar Origen</a></li> -->
        </ul>
      </div>
    </div>


  </div>
  <div class="panel-body">
    <div class='lista_orden_gestion'>
      @include('despacho.ajax.alistapedidogestion')
    </div>
  </div>


</div>