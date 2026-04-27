@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --success: #10b981;
            --danger: #ef4444;
            --bg-gray: #f9fafb;
            --border: #e5e7eb;
            --matrix-header: #1a237e;
            --matrix-cell: #fffde7;
        }

        .premium-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        .nav-tabs-premium {
            border-bottom: 2px solid var(--border);
            margin-bottom: 20px;
            gap: 5px;
        }

        .nav-tabs-premium li a {
            border: none;
            color: #6b7280;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 8px 8px 0 0;
            transition: all 0.2s;
            cursor: pointer;
            display: block;
            text-decoration: none !important;
        }

        .nav-tabs-premium li.active a,
        .nav-tabs-premium li.active a:hover,
        .nav-tabs-premium li.active a:focus {
            color: var(--primary) !important;
            background: #eef2ff !important;
            border: none !important;
            border-bottom: 3px solid var(--primary) !important;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
            font-size: 13px;
        }

        .btn-premium {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-premium {
            background: var(--primary);
            color: white;
        }

        .config-table th {
            background: var(--bg-gray);
            padding: 10px 15px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            color: #4b5563;
            border-bottom: 2px solid var(--border);
        }

        .matrix-container {
            overflow-x: auto;
            border: 1px solid #000;
        }

        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Arial', sans-serif;
            background: white;
        }

        .matrix-table th, .matrix-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 11px;
        }

        .matrix-th-tc {
            background: var(--matrix-header);
            color: white;
        }

        .matrix-cell-value {
            background: var(--matrix-cell);
            font-weight: 700;
        }

        .jefe-popover {
            position: absolute;
            background: #fff9c4;
            border: 1px solid #fbc02d;
            padding: 8px;
            border-radius: 4px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            z-index: 100;
            display: none;
            text-align: left;
            min-width: 130px;
            color: #333;
            font-weight: normal;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
        }

        .matrix-th-subcanal {
            position: relative;
            background: #fff;
            font-weight: bold;
        }

        .matrix-th-subcanal:hover .jefe-popover {
            display: block;
        }

        .select2-container--default .select2-selection--single {
            height: 49px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding-top: 10px;
        }

        .save-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #111827;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            display: none;
            z-index: 1000;
        }
    </style>
@stop

@section('section')
    <div class="be-content configuracioncomision">
        <div class="main-content container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h1 style="font-weight: 800; font-size: 24px; color: #111827; margin: 0;">
                            <i class="mdi mdi-settings-outline" style="color: var(--primary);"></i> Gestión de Comisiones
                        </h1>
                        <button class="btn btn-default" onclick="$('#modal-config-canales').modal('show')">
                            <i class="mdi mdi-account-group"></i> Configurar Jefes por Canal
                        </button>
                    </div>

                    <!-- FORMULARIO GENERAL -->
                    <div class="premium-card">
                        <h2 style="font-size: 15px; font-weight: 700; margin-bottom: 15px; color: #374151;">Añadir Nueva Configuración</h2>
                        <form id="form-comision" onsubmit="return false;">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Marca</label>
                                    <select id="cod_marca" class="form-control select2">
                                        <option value="">Seleccione Marca</option>
                                        @foreach($marcas as $m)
                                            <option value="{{ $m->COD_CATEGORIA }}">{{ $m->NOM_CATEGORIA }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tiempo Cobranza (T/C)</label>
                                    <select id="cod_tiempo" class="form-control select2">
                                        <option value="">Seleccione T/C</option>
                                        @foreach($tiempos as $t)
                                            <option value="{{ $t->COD_CATEGORIA }}">{{ $t->NOM_CATEGORIA }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sub Canal</label>
                                    <select id="cod_sub_canal" class="form-control select2">
                                        <option value="">Seleccione Sub Canal</option>
                                        @foreach($subcanales as $s)
                                            <option value="{{ $s->COD_CATEGORIA }}">{{ $s->NOM_CATEGORIA }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Comisión (%)</label>
                                    <input type="number" step="0.0001" id="porcentaje" class="form-control" placeholder="0.0000" style="height: 49px; border-radius: 6px; border: 1px solid #d1d5db;">
                                </div>
                                <div class="col-md-1" style="display: flex; align-items: flex-end;">
                                    <button type="button" class="btn-premium btn-primary-premium" onclick="crearConfiguracion()" style="height: 49px; width: 100%; justify-content: center; margin-top: 26px;">
                                        <i class="mdi mdi-plus"></i> Crear
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- TABS (Cuadro primero) -->
                    <ul class="nav nav-tabs nav-tabs-premium" role="tablist">
                        <li class="nav-item active">
                            <a class="nav-link" data-toggle="tab" href="#v-matriz" role="tab">
                                <i class="mdi mdi-grid"></i> Cuadro Informativo (Matriz)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#v-lista" role="tab">
                                <i class="mdi mdi-format-list-bulleted"></i> Vista de Lista
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- VISTA MATRIZ (Activa por defecto) -->
                        <div class="tab-pane active" id="v-matriz" role="tabpanel">
                            <div class="premium-card">
                                <div class="matrix-container">
                                    <table class="matrix-table">
                                        <thead>
                                            <tr>
                                                <th style="background: #fff; font-weight: bold; width: 150px;">MARCA</th>
                                                <th class="matrix-th-tc" style="width: 60px;">T/C</th>
                                                @foreach($subcanales_matrix as $sc)
                                                    <th class="matrix-th-subcanal">
                                                        {{ $sc->NOM_CATEGORIA }}
                                                        <div class="jefe-popover">
                                                            <strong>JEFES:</strong><br>
                                                            @if(isset($subcanal_jefe_map[$sc->COD_CATEGORIA]))
                                                                @foreach($jefes as $j)
                                                                    @if(in_array($j->COD_CATEGORIA, $subcanal_jefe_map[$sc->COD_CATEGORIA]))
                                                                        - {{ $j->NOM_CATEGORIA }}<br>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                (Sin asignar)
                                                            @endif
                                                        </div>
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($marcas_matrix as $marca)
                                                @php 
                                                    $tiempos_de_esta_marca = $tiempos_matrix->filter(function($t) use ($marca, $config_map, $subcanales_matrix) {
                                                        foreach($subcanales_matrix as $sc) {
                                                            $key = $marca->COD_CATEGORIA . '|' . $t->COD_CATEGORIA . '|' . $sc->COD_CATEGORIA;
                                                            if(isset($config_map[$key])) return true;
                                                        }
                                                        return false;
                                                    });
                                                    $first_tc = true; 
                                                @endphp
                                                @foreach($tiempos_de_esta_marca as $tiempo)
                                                    <tr>
                                                        @if($first_tc)
                                                            <td rowspan="{{ $tiempos_de_esta_marca->count() }}" style="font-weight: bold; text-align: left;">{{ $marca->NOM_CATEGORIA }}</td>
                                                            @php $first_tc = false; @endphp
                                                        @endif
                                                        <td class="matrix-th-tc">{{ $tiempo->NOM_CATEGORIA }}</td>
                                                        @foreach($subcanales_matrix as $sc)
                                                            @php
                                                                $key = $marca->COD_CATEGORIA . '|' . $tiempo->COD_CATEGORIA . '|' . $sc->COD_CATEGORIA;
                                                                $valor = $config_map[$key] ?? null;
                                                            @endphp
                                                            <td class="{{ $valor !== null ? 'matrix-cell-value' : '' }}">
                                                                {{ $valor !== null ? number_format($valor, 2) : '' }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- VISTA LISTA -->
                        <div class="tab-pane" id="v-lista" role="tabpanel">
                            <div class="premium-card">
                                <div class="table-responsive">
                                    <table class="config-table" id="table-list" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Marca</th>
                                                <th style="text-align: center;">T/C</th>
                                                <th>Sub Canal</th>
                                                <th style="text-align: center;">% Comisión</th>
                                                <th style="text-align: center;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($configuraciones as $conf)
                                                <tr id="row-{{ $conf->id }}">
                                                    <td style="font-weight: 600;">{{ $conf->NOM_MARCA }}</td>
                                                    <td style="text-align: center;">{{ $conf->NOM_TIEMPO }}</td>
                                                    <td>{{ $conf->NOM_SUBCANAL }}</td>
                                                    <td style="text-align: center; font-weight: 700;">{{ number_format($conf->porcentaje, 4) }}%</td>
                                                    <td style="text-align: center;">
                                                        <button class="btn btn-xs btn-danger" onclick="eliminarConfiguracion({{ $conf->id }})" title="Eliminar">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Configuración Canales-Jefes -->
    <div id="modal-config-canales" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header" style="background: var(--bg-gray); border-bottom: 1px solid var(--border);">
                    <button type="button" data-dismiss="modal" class="close"><span>&times;</span></button>
                    <h4 class="modal-title" style="font-weight: 800;">Configurar Jefes por Sub Canal</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label">Seleccionar Sub Canal</label>
                            <select id="modal_sub_canal" class="form-control" onchange="cargarJefesCanal(this.value)">
                                <option value="">Seleccione...</option>
                                @foreach($subcanales as $s)
                                    <option value="{{ $s->COD_CATEGORIA }}">{{ $s->NOM_CATEGORIA }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Asignar Jefes de Venta</label>
                            <div style="max-height: 400px; overflow-y: auto; border: 1px solid var(--border); border-radius: 8px; padding: 15px;">
                                @foreach($jefes as $j)
                                    <div class="checkbox" style="margin-bottom: 10px;">
                                        <label style="font-weight: 500; cursor: pointer;">
                                            <input type="checkbox" class="chk-jefe" value="{{ $j->COD_CATEGORIA }}"> {{ $j->NOM_CATEGORIA }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: var(--bg-gray); border-top: 1px solid var(--border);">
                    <button type="button" data-dismiss="modal" class="btn btn-default" style="font-weight: 700;">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarJefesCanal()" style="font-weight: 800;">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="save-indicator" id="indicator">
        <i class="mdi mdi-check-circle" style="color: var(--success);"></i> Operación Exitosa
    </div>

@stop

@section('script')
    <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2').select2();
            App.init();
        });

        function crearConfiguracion() {
            var data = {
                _token: '{{ csrf_token() }}',
                cod_marca: $('#cod_marca').val(),
                cod_tiempo: $('#cod_tiempo').val(),
                cod_sub_canal: $('#cod_sub_canal').val(),
                porcentaje: $('#porcentaje').val()
            };

            if(!data.cod_marca || !data.cod_tiempo || !data.cod_sub_canal || !data.porcentaje) {
                alert('Por favor complete todos los campos');
                return;
            }

            $.post('{{ url("/ajax-guardar-comision-configuracion") }}', data, function(response) {
                if(response.status == 'success') {
                    location.reload(); 
                } else {
                    alert(response.message);
                }
            });
        }

        function eliminarConfiguracion(id) {
            if(!confirm('¿Está seguro de eliminar esta configuración?')) return;

            $.post('{{ url("/ajax-eliminar-comision-configuracion") }}', {
                _token: '{{ csrf_token() }}',
                id: id
            }, function(response) {
                if(response.status == 'success') {
                    $('#row-' + id).fadeOut(300, function() { $(this).remove(); });
                    mostrarIndicador();
                }
            });
        }

        var subcanalJefeMap = {!! json_encode($subcanal_jefe_map) !!};

        function cargarJefesCanal(codSubcanal) {
            $('.chk-jefe').prop('checked', false);
            if(subcanalJefeMap[codSubcanal]) {
                var jefes = subcanalJefeMap[codSubcanal];
                $('.chk-jefe').each(function() {
                    if(jefes.indexOf($(this).val()) !== -1) {
                        $(this).prop('checked', true);
                    }
                });
            }
        }

        function guardarJefesCanal() {
            var codSubcanal = $('#modal_sub_canal').val();
            if(!codSubcanal) {
                alert('Seleccione un subcanal');
                return;
            }

            var jefes = [];
            $('.chk-jefe:checked').each(function() {
                jefes.push($(this).val());
            });

            $.post('{{ url("/ajax-guardar-jefe-subcanal") }}', {
                _token: '{{ csrf_token() }}',
                cod_sub_canal: codSubcanal,
                cod_jefes: jefes
            }, function(response) {
                if(response.status == 'success') {
                    subcanalJefeMap[codSubcanal] = jefes;
                    $('#modal-config-canales').modal('hide');
                    mostrarIndicador();
                }
            });
        }

        function mostrarIndicador() {
            $('#indicator').fadeIn().delay(2000).fadeOut();
        }
    </script>
@stop
