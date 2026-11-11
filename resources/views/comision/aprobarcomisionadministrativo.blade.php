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
        }

        .premium-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
            font-size: 13px;
        }

        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-generado { background: #e0f2fe; color: #0369a1; } /* Azul claro */
        .status-aprobado { background: #dbeafe; color: #1e40af; } /* Azul oscuro */
        .status-autorizado { background: #fef9c3; color: #854d0e; } /* Amarillo */
        .status-ejecutado { background: #dcfce7; color: #15803d; } /* Verde éxito */
        .status-default { background: #f3f4f6; color: #374151; }

        .table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 12px;
            border-bottom: 2px solid var(--border) !important;
            padding: 12px 15px !important;
        }

        .table tbody td {
            vertical-align: middle !important;
            padding: 12px 15px !important;
            font-size: 13px;
            color: #1e293b;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f5f9;
        }

        .select2-container--default .select2-selection--single {
            height: 40px;
            border: 1px solid var(--border);
            border-radius: 6px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
    </style>
@stop

@section('section')
    <div class="be-content">
        <div class="main-content container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h1 style="font-weight: 800; font-size: 24px; color: #111827; margin: 0;">
                            <i class="fa fa-check-circle" style="color: var(--primary);"></i> Aprobar Comisiones (Administrativo)
                        </h1>
                    </div>

                    <!-- FORMULARIO FILTROS -->
                    <div class="premium-card">
                        <h2 style="font-size: 15px; font-weight: 700; margin-bottom: 15px; color: #374151;">Filtros de Periodo</h2>
                        <form id="form-aprobar" method="GET" action="{{ url('/gestion-de-aprobar-comision-administrativo/'.$idopcion) }}">
                            <div class="row" style="margin-left: -10px; margin-right: -10px;">
                                <div class="col-md-4" style="padding-left: 10px; padding-right: 10px;">
                                    <label class="form-label">Periodo Inicio</label>
                                    {!! Form::select('id_periodo_ini', $combo_periodos, $id_periodo_ini, ['class' => 'form-control select2', 'id' => 'id_periodo_ini']) !!}
                                </div>
                                <div class="col-md-4" style="padding-left: 10px; padding-right: 10px;">
                                    <label class="form-label">Periodo Fin</label>
                                    {!! Form::select('id_periodo_fin', $combo_periodos, $id_periodo_fin, ['class' => 'form-control select2', 'id' => 'id_periodo_fin']) !!}
                                </div>
                                <div class="col-md-4" style="padding-left: 10px; padding-right: 10px; display: flex; align-items: flex-end;">
                                    <button type="submit" class="btn btn-primary" style="margin-top: 34px; background-color: var(--primary); border: none; font-weight: 700; width: 100%; height: 40px; border-radius: 6px;">
                                        <i class="fa fa-filter"></i> Filtrar Periodos
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="premium-card">
                        <!-- Buscador Personalizado -->
                        <div style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
                            <div style="flex: 1; position: relative;">
                                <i class="fa fa-search" style="position: absolute; left: 15px; top: 12px; color: #64748b; font-size: 18px;"></i>
                                <input type="text" id="search-input" class="form-control" placeholder="Buscar por periodo, jefe de venta, estado..." 
                                       style="padding-left: 45px; height: 45px; border-radius: 8px; border: 1px solid var(--border); font-size: 14px;">
                            </div>
                            <button type="button" id="btn-aprobar-masivo" class="btn btn-success" style="height: 45px; border-radius: 8px; font-weight: 700; background-color: #047857; border: none; padding: 0 20px; display: none;">
                                <i class="fa fa-check-double"></i> Aprobar Seleccionados
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table id="table-aprobar" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 40px; text-align: center;">
                                            <input type="checkbox" id="check-all" style="cursor: pointer;">
                                        </th>
                                        <th>JEFE DE VENTA</th>
                                        <th>TOTAL COMISIÓN</th>
                                        <th>INICIO</th>
                                        <th>FIN</th>
                                        <th>ESTADO</th>
                                        <th>PROVIENE</th>
                                        <th>AUTORIZA</th>
                                        <th>EJECUTA</th>
                                        <th style="text-align: center;">REPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($planillas_agrupadas as $periodo => $filas)
                                        @php $id_periodo = str_replace([' ', '.', '#'], '_', $periodo); @endphp
                                        <tr class="group-header" style="cursor: pointer;" data-periodo="{{ $id_periodo }}">
                                            <td colspan="10" style="background: #f8fafc; font-weight: 800; color: var(--primary); font-size: 14px; border-left: 4px solid var(--primary);">
                                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                                    <div class="periodo-text">
                                                        <i class="fa fa-chevron-right toggle-icon" style="font-size: 16px; transition: transform 0.2s; margin-right: 10px;"></i>
                                                        <i class="fa fa-calendar"></i> PERIODO: {{ $periodo }} 
                                                        <span style="font-weight: 400; color: #64748b; font-size: 12px; margin-left: 10px;">({{ count($filas) }} registros)</span>
                                                    </div>
                                                    <span class="badge badge-primary" style="background-color: var(--primary); font-size: 10px;">Click para expandir</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @foreach($filas as $item)
                                            <tr class="detail-row row-{{ $id_periodo }}" style="display: none;">
                                                <td style="text-align: center;">
                                                    @if($item->COD_ESTADO == 'EPP0000000000002')
                                                        <input type="checkbox" class="check-item" 
                                                            data-periodo="{{ $item->COD_PERIODO }}"
                                                            data-jefe="{{ $item->COD_CATEGORIA_JEFE_VENTA }}"
                                                            data-proviene="{{ trim($item->TXT_PROVIENE) }}"
                                                            style="cursor: pointer;">
                                                    @else
                                                        <i class="fa fa-lock" style="color: #cbd5e1;" title="Ya procesado"></i>
                                                    @endif
                                                </td>
                                                <td style="padding-left: 10px !important; font-weight: 600;">
                                                    {{ $item->TXT_CATEGORIA_JEFE_VENTA }}
                                                    @if(isset($item->ES_JEFE) && $item->ES_JEFE == 1)
                                                        <span class="badge" style="background-color: #6366f1; color: white; font-size: 9px; padding: 2px 5px; margin-left: 5px;">JEFE</span>
                                                    @endif
                                                </td>
                                                <td style="font-weight: 800; color: #1e293b;">S/. {{ number_format($item->TOTAL_COMISION, 2) }}</td>
                                                <td>{{ !empty($item->FEC_INICIO) ? date('d-m-Y', strtotime($item->FEC_INICIO)) : '-' }}</td>
                                                <td>{{ !empty($item->FEC_FIN) ? date('d-m-Y', strtotime($item->FEC_FIN)) : '-' }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = 'status-default';
                                                        if($item->TXT_ESTADO == 'GENERADO') $statusClass = 'status-generado';
                                                        else if($item->TXT_ESTADO == 'APROBADO') $statusClass = 'status-aprobado';
                                                        else if($item->TXT_ESTADO == 'AUTORIZADO') $statusClass = 'status-autorizado';
                                                        else if($item->TXT_ESTADO == 'EJECUTADO') $statusClass = 'status-ejecutado';
                                                    @endphp
                                                    <span class="badge-status {{ $statusClass }}">{{ $item->TXT_ESTADO }}</span>
                                                </td>
                                                <td>{{ $item->TXT_PROVIENE }}</td>
                                                <td>
                                                    @if($item->TXT_USUARIO_AUTORIZA)
                                                        <span style="font-size: 11px; color: #64748b;"><i class="fa fa-user-circle"></i> {{ $item->TXT_USUARIO_AUTORIZA }}</span>
                                                    @else
                                                        <span style="color: #cbd5e1; font-style: italic;">Pendiente</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->TXT_USUARIO_EJECUTA)
                                                        <span style="font-size: 11px; color: #64748b;"><i class="fa fa-user-plus"></i> {{ $item->TXT_USUARIO_EJECUTA }}</span>
                                                    @else
                                                        <span style="color: #cbd5e1; font-style: italic;">Pendiente</span>
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    @if(in_array(trim($item->TXT_PROVIENE), ['MERCADO MAYORISTA', 'AUTOSERVICIOS', 'COBRO AUTOSERVICIO', 'PACAS']))
                                                        <a href="{{ url('/exportar-excel-comision-administrativo?cod_jefe='.$item->COD_CATEGORIA_JEFE_VENTA.'&cod_periodo='.$item->COD_PERIODO.'&proviene='.trim($item->TXT_PROVIENE)) }}" 
                                                           class="btn btn-success btn-xs" title="Exportar Excel" style="background-color: #2d6a4f; border-color: #2d6a4f;">
                                                            <i class="fa fa-file-excel-o" style="color: white;"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();
            App.init();

            // Feedback al filtrar
            $('#form-aprobar').on('submit', function() {
                var btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Filtrando...');
            });

            // Toggle de Grupos
            $('.group-header').on('click', function() {
                var id = $(this).data('periodo');
                var icon = $(this).find('.toggle-icon');
                var badge = $(this).find('.badge');
                
                $('.row-' + id).fadeToggle(200);
                
                if (icon.hasClass('fa-chevron-right')) {
                    icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                    badge.text('Click para contraer');
                } else {
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                    badge.text('Click para expandir');
                }
            });

            // --- LÓGICA DE SELECCIÓN MASIVA ---
            function toggleMassButton() {
                var selectedCount = $('.check-item:checked').length;
                if (selectedCount > 0) {
                    $('#btn-aprobar-masivo').fadeIn().html('<i class="fa fa-check-double"></i> Aprobar Seleccionados (' + selectedCount + ')');
                } else {
                    $('#btn-aprobar-masivo').fadeOut();
                }
            }

            // Inicializar botón si hay precargados
            toggleMassButton();

            $('#check-all').on('click', function() {
                $('.check-item').prop('checked', this.checked);
                toggleMassButton();
            });

            $(document).on('change', '.check-item', function() {
                toggleMassButton();
                if (!this.checked) $('#check-all').prop('checked', false);
            });

            $('#btn-aprobar-masivo').on('click', function() {
                var selected = [];
                $('.check-item:checked').each(function() {
                    selected.push({
                        periodo: $(this).data('periodo'),
                        jefe: $(this).data('jefe'),
                        proviene: $(this).data('proviene')
                    });
                });

                if (selected.length === 0) return;

                $.confirm({
                    title: '¿Estás seguro?',
                    content: 'Se autorizarán masivamente ' + selected.length + ' comisiones.',
                    icon: 'fa fa-warning',
                    type: 'orange',
                    buttons: {
                        confirmar: {
                            text: 'Sí, Autorizar',
                            btnClass: 'btn-success',
                            action: function() {
                                $.ajax({
                                    url: "{{ url('/ajax-aprobar-comisiones-masivo') }}",
                                    type: 'POST',
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        comisiones: selected
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            $.alert({
                                                title: '¡Logrado!',
                                                content: response.message,
                                                type: 'green',
                                                buttons: {
                                                    ok: {
                                                        text: 'Aceptar',
                                                        action: function() { location.reload(); }
                                                    }
                                                }
                                            });
                                        } else {
                                            $.alert({ title: 'Error', content: response.message, type: 'red' });
                                        }
                                    },
                                    error: function() {
                                        $.alert({ title: 'Error', content: 'No se pudo procesar la solicitud.', type: 'red' });
                                    }
                                });
                            }
                        },
                        cancelar: {
                            text: 'Cancelar'
                        }
                    }
                });
            });

            // Buscador Personalizado
            $("#search-input").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                
                // Si el buscador está vacío, contraer todo de nuevo y mostrar todo
                if(value === "") {
                    $(".group-header").show();
                    $(".detail-row").hide();
                    $(".toggle-icon").removeClass('fa-chevron-down').addClass('fa-chevron-right');
                    $(".badge").text('Click para expandir');
                    return;
                }

                // Filtrar
                $(".group-header").each(function() {
                    var id = $(this).data('periodo');
                    var periodText = $(this).text().toLowerCase();
                    var $rows = $(".row-" + id);
                    var matchesInRows = false;

                    $rows.each(function() {
                        if ($(this).text().toLowerCase().indexOf(value) > -1) {
                            $(this).show();
                            matchesInRows = true;
                        } else {
                            $(this).hide();
                        }
                    });

                    if (periodText.indexOf(value) > -1 || matchesInRows) {
                        $(this).show();
                        if(matchesInRows) {
                            $(this).find('.toggle-icon').removeClass('fa-chevron-right').addClass('fa-chevron-down');
                            $(this).find('.badge').text('Click para contraer');
                        }
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
@stop
