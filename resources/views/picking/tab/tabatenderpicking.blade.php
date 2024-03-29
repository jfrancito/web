<div class="panel panel-default panel-table">
  <div class="panel-heading">

    <div class='col-sm-3'>
      <b>Atender picking</b>
    </div>
    <div class='col-sm-4'>
        <span class="label label-transferenciapt">Atendida</span>
        <span class="label label-origen">Origen</span>
        <span class="label label-guia-utilizada">Guia ejecutada</span>
    </div>
    
    <div class="tools dropdown show">
      <div class="dropdown">

        <span class="icon toggle-loading mdi mdi-save guardarcambios" style='color:#34a853;' title="Guardar cambios"></span>

        <span class="icon mdi mdi-more-vert dropdown-toggle" id="menudespacho" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></span>
        <ul class="dropdown-menu" aria-labelledby="menudespacho" style="margin: 7px -169px 0px;">
          <!--<li><a href="#" class='agregarproductoatender'>Agregar Producto <span></span></a></li> -->
          <!-- <li><a href="#" class='rechazarproductoatender'>Rechazar Producto <span class="mdi mdi-check-square"></span></a></li> -->
          <!-- <li><a href="#" class='cambiarfechaentrega'>Modificar fecha de carga <span class="mdi mdi-check-circle"></span></a></li> -->
          <!-- <li><a href="#" class='cambiarorigen'>Cambiar Origen <span class="mdi mdi-check-circle"></span></a></li> -->
          <!-- <li><a href="#" class='agregarmuestras'>Agregar muestras <span class="mdi mdi-check-circle"></span></a></li> -->
          <li><a href="#" class='ptransferenciapt'>Transferencia PT <span class="mdi mdi-check-circle"></span></a></li>
          <li><a href="#" class='pordensalida'>Orden Salida <span class="mdi mdi-check-circle"></span></a></li>
        </ul>
      </div>
    </div>
  </div>


  <div class="panel-body">
    <div class='lista_orden_atender'>
        @include('picking.ajax.alistapedidoatendertransferencia')
    </div>
  </div>
   
  <!-- LISTADO DE TRANSFERENCIA -->
  <div>
    <div class='col-sm-12' style="margin-top: 20px;">
      <b style="font-size: 18px;">Lista de Tranferencias / Ordenes Salida</b>
    </div>
    <div class='lista_transferencias_pt'>
        @include('picking.tab.tablas.listatranferenciaspt')  
    </div>
  </div>
</div>


<div class='row-menu'>
    <div class='row'>
      <div class="col-sm-12 col-mobil-top">
        <div class="col-fr-2 col-atras-picking">          
          <a class = "col-ata-atras" href="{{ url('/atender-picking/'.$idopcion) }}">
            <span class="mdi mdi-arrow-left"></span>
          </a>               
        </div> 
      </div>
    </div>
  </div>