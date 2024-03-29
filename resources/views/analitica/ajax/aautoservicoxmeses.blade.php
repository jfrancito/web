
<div class="tab-container reporte0101">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#vg" data-toggle="tab">VENTAS ATENDIDAS</a></li>
  </ul>
  <div class="tab-content">
    <div id="vg" class="tab-pane active cont">
        <div class="titulobanner">
          <div><b>01.01 REPORTE VENTAS AUTOSERVICIO EN {{$tituloban}}</b></div>
        </div>
        <div class="col-xs-12 contgrafico">
            <div class='titulografico'><b>({{$empresa_nombre}})</b></div>
            <div class='subtitulografico'><b>{{$anio}}</b></div>
            <div class='subtitulografico'><b>{{$simmodena}} {{number_format($totalimporte_s, 2, '.', ',')}}</b></div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-12" style="margin-top: 15px;">
          <div class='scrollhochart'>
            <div class="contenedorauto">
              <div id="chartmeses" >
              </div>
            </div>
          </div>
        </div>  
    </div>
  </div>
</div>
<div id="ventas_s" class='ocultar'>{{$ventas_s}}</div>
<div id="tnc_s" class='ocultar'>{{$tnc_s}}</div>
<div id="prod_s" class='ocultar'>{{$jprod_s}}</div>
<div id="color_s" class='ocultar'>{{$jcol_s}}</div>
<div id="costos_s" class='ocultar'>{{$jcostos_s}}</div>
<div id="utilidad_s" class='ocultar'>{{$jutilidad_s}}</div>
<div id="jtotal_s" class='ocultar'>{{$jtotal_s}}</div>
<div id="simmodena" class='ocultar'>{{$simmodena}}</div>
<div id="cliente_s" class='ocultar'>{{$jcliente_s}}</div>
<div id="meses_s" class='ocultar'>{{$meses_s}}</div>

@if(isset($ajax))
    <script src="{{ asset('public/js/analitica/autoservicoxmeses.js?v='.$version) }}" type="text/javascript"></script>  
@endif





