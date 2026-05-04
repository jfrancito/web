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

        .input-edit {
            width: 80px;
            border: 1px solid transparent;
            background: transparent;
            text-align: center;
            font-weight: 700;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .input-edit:hover {
            border-color: #d1d5db;
            background: #fff;
        }

        .input-edit:focus {
            border-color: var(--primary);
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
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
            width: 60px;
        }

        .matrix-cell-value {
            background: var(--matrix-cell);
        }

        .matrix-cell-zero {
            background: #f1f5f9; /* Light gray-blue for 0% values */
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
            height: 49px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 6px !important;
            padding-top: 10px !important;
        }

        .select2-container {
            width: 100% !important;
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
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ url('/exportar-excel-comisiones-configuracion') }}" class="btn btn-success" style="background-color: #1b5e20; border: none; font-weight: 700;">
                                <i class="mdi mdi-file-excel"></i> Exportar a Excel
                            </a>
                            <button class="btn btn-default" onclick="$('#modal-config-canales').modal('show')">
                                <i class="mdi mdi-account-group"></i> Configurar Jefes por Canal
                            </button>
                        </div>
                    </div>


                    <!-- FORMULARIO GENERAL -->
                    <div class="premium-card">
                        <h2 style="font-size: 15px; font-weight: 700; margin-bottom: 15px; color: #374151;">Añadir Nueva Configuración</h2>
                        <form id="form-comision" onsubmit="return false;">
                            <div class="row" style="margin-left: -10px; margin-right: -10px;">
                                <div class="col-md-3" style="padding-left: 10px; padding-right: 10px;">
                                    <label class="form-label">Marca</label>
                                    <select id="cod_marca" class="form-control select2">
                                        <option value="">Seleccione Marca</option>
                                        @foreach($marcas as $m)
                                            <option value="{{ $m->COD_CATEGORIA }}">{{ $m->NOM_CATEGORIA }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding-left: 10px; padding-right: 10px;">
                                    <label class="form-label">Tiempo Cobranza (T/C)</label>
                                    <select id="cod_tiempo" class="form-control select2">
                                        <option value="">Seleccione T/C</option>
                                        @foreach($tiempos as $t)
                                            <option value="{{ $t->COD_CATEGORIA }}">{{ $t->NOM_CATEGORIA }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding-left: 10px; padding-right: 10px;">
                                    <label class="form-label">Sub Canal</label>
                                    <select id="cod_sub_canal" class="form-control select2">
                                        <option value="">Seleccione Sub Canal</option>
                                        @foreach($subcanales as $s)
                                            <option value="{{ $s->COD_CATEGORIA }}">{{ $s->NOM_CATEGORIA }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2" style="padding-left: 10px; padding-right: 10px;">
                                    <label class="form-label">Comisión (%)</label>
                                    <input type="number" step="0.0001" id="porcentaje" class="form-control" placeholder="0.0000" style="height: 49px; border-radius: 6px; border: 1px solid #d1d5db;">
                                </div>
                                <div class="col-md-1" style="padding-left: 10px; padding-right: 10px; display: flex; align-items: flex-end;">
                                    <button type="button" class="btn-premium btn-primary-premium" onclick="crearConfiguracion()" style="height: 49px; width: 100%; justify-content: center; margin-top: 26px; background-color: #2e7d32;">
                                        <i class="mdi mdi-plus"></i> Crear
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>

                    <!-- TABS -->
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
                        <!-- VISTA MATRIZ -->
                        <div class="tab-pane active" id="v-matriz" role="tabpanel">
                            <div class="premium-card">
                                <div class="matrix-container">
                                    <table class="matrix-table">
                                        <thead>
                                            <tr>
                                                <th style="background: #fff; font-weight: bold; width: 150px;">MARCA</th>
                                                <th class="matrix-th-tc" style="width: 60px;">T/C</th>
                                                @foreach($subcanales_matrix as $sc)
                                                    <th class="matrix-th-subcanal" style="padding: 12px 8px; position: relative; min-width: 100px; height: 60px;">
                                                        <span style="display: block; text-align: center; margin-bottom: 5px;">{{ $sc->NOM_CATEGORIA }}</span>
                                                        <button class="btn btn-xs btn-default" onclick="abrirModalJefes('{{ $sc->COD_CATEGORIA }}')" 
                                                                title="Configurar Jefes" 
                                                                style="position: absolute; bottom: 4px; right: 4px; width: 22px; height: 22px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #eef2ff; border: 1px solid #c7d2fe; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                            <i class="mdi mdi-edit" style="color: var(--primary); font-size: 12px;"></i>
                                                        </button>
                                                        <div class="jefe-popover" id="popover-{{ $sc->COD_CATEGORIA }}">


                                                            <strong>JEFES:</strong><br>
                                                            <div class="lista-jefes-popover">
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
                                                                $data = $config_map[$key] ?? null;
                                                                $cellClass = '';
                                                                if($data !== null) {
                                                                    $cellClass = ($data['porcentaje'] > 0) ? 'matrix-cell-value' : 'matrix-cell-zero';
                                                                }
                                                            @endphp
                                                            <td class="{{ $cellClass }}">
                                                                @if($data !== null)
                                                                    <input type="number" step="0.01" class="input-edit input-comision-{{ $data['id'] }}" 
                                                                           value="{{ number_format($data['porcentaje'], 2, '.', '') }}"
                                                                           onchange="modificarComision('{{ $data['id'] }}', this.value)">
                                                                @endif
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($configuraciones as $conf)
                                                <tr id="row-{{ $conf->id }}">
                                                    <td style="font-weight: 600;">{{ $conf->NOM_MARCA }}</td>
                                                    <td style="text-align: center;">{{ $conf->NOM_TIEMPO }}</td>
                                                    <td>{{ $conf->NOM_SUBCANAL }}</td>
                                                    <td style="text-align: center;">
                                                        <input type="number" step="0.0001" class="input-edit input-comision-{{ $conf->id }}" 
                                                               value="{{ number_format($conf->porcentaje, 4, '.', '') }}"
                                                               onchange="modificarComision('{{ $conf->id }}', this.value)"
                                                               style="width: 100px;">
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

                            <div style="margin-top: 30px;">
                                <label class="form-label" style="color: var(--primary); font-weight: 700;">Jefes Seleccionados:</label>
                                <div id="resumen-jefes-asignados" style="margin-top: 10px; padding: 15px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; min-height: 100px;">
                                    <ul id="lista-resumen-jefes" style="padding-left: 15px; margin: 0; font-size: 13px; color: #334155;">
                                        <li style="color: #94a3b8; font-style: italic; list-style: none;">Ningún jefe seleccionado</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label">Asignar Jefes de Venta</label>
                            <input type="text" id="busqueda_jefe" class="form-control" placeholder="Buscar jefe..." style="margin-bottom: 10px; border-radius: 6px;">
                            <div id="contenedor-jefes" style="max-height: 400px; overflow-y: auto; border: 1px solid var(--border); border-radius: 8px; padding: 15px;">
                                @foreach($jefes as $j)
                                    <div class="checkbox-item" style="margin-bottom: 10px;">
                                        <label style="font-weight: 500; cursor: pointer;">
                                            <input type="checkbox" class="chk-jefe" value="{{ $j->COD_CATEGORIA }}"> 
                                            <span class="nombre-jefe">{{ $j->NOM_CATEGORIA }}</span>
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
            $('.select2').select2({
                width: '100%'
            });
            App.init();

            // Lógica de búsqueda en el modal de jefes
            $('#busqueda_jefe').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#contenedor-jefes .checkbox-item').filter(function() {
                    $(this).toggle($(this).find('.nombre-jefe').text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Actualizar resumen al marcar/desmarcar
            $(document).on('change', '.chk-jefe', function() {
                actualizarResumenJefes();
            });
        });

        function actualizarResumenJefes() {
            var html = '';
            $('.chk-jefe:checked').each(function() {
                var nombre = $(this).closest('label').find('.nombre-jefe').text();
                html += '<li>' + nombre + '</li>';
            });

            if(html == '') {
                html = '<li style="color: #94a3b8; font-style: italic; list-style: none;">Ningún jefe seleccionado</li>';
            }
            $('#lista-resumen-jefes').html(html);
        }




        function crearConfiguracion() {
            var data = {
                _token: '{{ csrf_token() }}',
                cod_marca: $('#cod_marca').val(),
                cod_tiempo: $('#cod_tiempo').val(),
                cod_sub_canal: $('#cod_sub_canal').val(),
                porcentaje: $('#porcentaje').val()
            };

            if(!data.cod_marca || !data.cod_tiempo || !data.cod_sub_canal || !data.porcentaje) {
                alerterrorajax('Por favor complete todos los campos');
                return;
            }

            $.post('{{ url("/ajax-guardar-comision-configuracion") }}', data, function(response) {
                if(response.status == 'success') {
                    alertajax(response.message);
                    location.reload(); 
                } else {
                    alerterrorajax(response.message);
                }
            });
        }

        function modificarComision(id, porcentaje) {
            $.post('{{ url("/ajax-modificar-comision-configuracion") }}', {
                _token: '{{ csrf_token() }}',
                id: id,
                porcentaje: porcentaje
            }, function(response) {
                if(response.status == 'success') {
                    // Sincronizar todos los inputs que tengan el mismo ID
                    $('.input-comision-' + id).val(porcentaje);
                    mostrarIndicador();
                } else {
                    alerterrorajax(response.message);
                }
            });
        }


        function abrirModalJefes(codSubcanal) {
            $('#modal_sub_canal').val(codSubcanal);
            cargarJefesCanal(codSubcanal);
            $('#modal-config-canales').modal('show');
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
            actualizarResumenJefes();
        }


        function guardarJefesCanal() {
            var codSubcanal = $('#modal_sub_canal').val();
            if(!codSubcanal) {
                alerterrorajax('Seleccione un subcanal');
                return;
            }

            var jefes = [];
            var nombresJefes = []; // Para actualizar la matriz sin refrescar
            $('.chk-jefe:checked').each(function() {
                jefes.push($(this).val());
                nombresJefes.push($(this).closest('label').find('.nombre-jefe').text());
            });

            $.post('{{ url("/ajax-guardar-jefe-subcanal") }}', {
                _token: '{{ csrf_token() }}',
                cod_sub_canal: codSubcanal,
                cod_jefes: jefes
            }, function(response) {
                if(response.status == 'success') {
                    subcanalJefeMap[codSubcanal] = jefes;
                    
                    // Actualizar el contenido del popover en la matriz
                    var htmlJefes = '';
                    if(nombresJefes.length > 0) {
                        nombresJefes.forEach(function(nombre) {
                            htmlJefes += '- ' + nombre + '<br>';
                        });
                    } else {
                        htmlJefes = '(Sin asignar)';
                    }
                    $('#popover-' + codSubcanal + ' .lista-jefes-popover').html(htmlJefes);

                    $('#modal-config-canales').modal('hide');
                    alertajax(response.message);
                    mostrarIndicador();
                } else {
                    alerterrorajax(response.message);
                }
            });
        }



        function mostrarIndicador() {
            $('#indicator').fadeIn().delay(2000).fadeOut();
        }
    </script>
@stop
