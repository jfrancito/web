<div class="tab-container reporte01">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#vg" data-toggle="tab">VENTAS ATENDIDAS</a></li>
  </ul>
  <div class="tab-content">
    <div id="vg" class="tab-pane active cont">
        <div class="titulobanner">
          <div><b class='titulo01'>01 REPORTE VENTAS AUTOSERVICIO EN {{$tituloban}}</b></div>
        </div>
        <div class="col-xs-12 contgrafico">
            <div class='titulografico'><b class='subtitulo0101'>({{$inicio}} / {{$hoy}})</b></div>
            <div class='titulografico'><b class='subtitulo0102'>{{$simmodena}} {{number_format($totalimporte_s, 2, '.', ',')}}</b></div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-12" style="margin-top: 15px;">
          <div class='scrollhochart'>
            <div class="contenedorauto">
              <div id="chartaut">
              </div> 
            </div>
          </div>
        </div>  
    </div>
  </div>
</div>