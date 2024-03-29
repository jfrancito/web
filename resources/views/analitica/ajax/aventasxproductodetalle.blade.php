@if($totalimporte_s>0)

  <div class="tab-container">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#vg" data-toggle="tab">VENTAS ATENDIDAS</a></li>
<!--       <li><a href="#va" data-toggle="tab">VENTAS GENERALES</a></li> -->
    </ul>
    <div class="tab-content">
      <div id="vg" class="tab-pane active cont">
          <div class="titulobanner">
            <div><b>03 REPORTE BASADO EN {{$tituloban}}</b></div>
          </div>
          <div class="col-xs-12 contgrafico">
              <div class='titulografico'><b>{{$empresa_nombre}} ({{$inicio}} / {{$hoy}})</b></div>
              <div class='subtitulografico'><b>{{$simmodena}} {{number_format($totalimporte_s, 2, '.', ',')}} / {{$tipomarca_txt}}</b></div>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="margin-top: 15px;">
              <div id="chart02" >
              </div>
          </div>  
          @if($tituloban =='SOLES')
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="margin-top: 20px;">

            <div class='scrollhochart'>
              <div class="contenedorauto">

                <div id="chart_b">
                </div>
              </div>
            </div>             
          </div>
          @endif
          
      </div>

    </div>
  </div>
  <input type="text" name="anio" id="anio" value='{{$anio}}' class='ocultar'>
  
  <div id="meses" class='ocultar'>{{$meses}}</div>
  <div id="anio" class='ocultar'>{{$anio}}</div>
  <div id="mes" class='ocultar'>{{$mes}}</div>
  <div id="empresa_nombre_text" class='ocultar'>{{$empresa_nombre}}</div>
  <div id="periodo_sel" class='ocultar'>{{$periodo_sel}}</div>
  <div id="tipomarca_txt" class='ocultar'>{{$tipomarca_txt}}</div>
  <div id="ventas" class='ocultar'>{{$ventas}}</div>
  <div id="tnc" class='ocultar'>{{$tnc}}</div>
  <div id="prod" class='ocultar'>{{$jprod}}</div>
  <div id="color" class='ocultar'>{{$jcol}}</div>
  <div id="ventas_s" class='ocultar'>{{$ventas_s}}</div>
  <div id="tnc_s" class='ocultar'>{{$tnc_s}}</div>
  <div id="prod_s" class='ocultar'>{{$jprod_s}}</div>
  <div id="color_s" class='ocultar'>{{$jcol_s}}</div>
  <div id="costos_s" class='ocultar'>{{$jcostos_s}}</div>
  <div id="utilidad_s" class='ocultar'>{{$jutilidad_s}}</div>
  <div id="jtotal_s" class='ocultar'>{{$jtotal_s}}</div>
  <div id="simmodena" class='ocultar'>{{$simmodena}}</div>
  <div id="marca" class='ocultar'>{{$marca}}</div>
  <div id="tituloban" class='ocultar'>{{$tituloban}}</div>

  @if(isset($ajax))
      <script src="{{ asset('public/js/analitica/ventasdetallexproducto.js?v='.$version) }}" type="text/javascript"></script>  
  @endif
@else
  <div role="alert" class="alert alert-danger alert-simple alert-dismissible">
      <span class="icon mdi mdi-close-circle-o"></span><strong>Alerta!</strong> No hay ventas en estos filtros.
  </div>
@endif




