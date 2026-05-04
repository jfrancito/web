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
    <div class="be-content aplicarcomision">
        <div class="main-content container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h1 style="font-weight: 800; font-size: 24px; color: #111827; margin: 0;">
                            <i class="mdi mdi-cash-multiple" style="color: var(--primary);"></i> Aplicar Comisiones
                        </h1>
                    </div>

                    <!-- FORMULARIO FILTROS -->
                    <div class="premium-card">
                        <h2 style="font-size: 15px; font-weight: 700; margin-bottom: 15px; color: #374151;">Filtros de Búsqueda</h2>
                        <form id="form-aplicar" method="GET" action="#">
                            <div class="row" style="margin-left: -10px; margin-right: -10px;">
                                <div class="col-md-4" style="padding-left: 10px; padding-right: 10px;">
                                    <label class="form-label">Jefe de Venta</label>
                                    {!! Form::select('cod_jefe', $combo_jefes, null, ['class' => 'form-control select2', 'id' => 'cod_jefe']) !!}
                                </div>
                                <div class="col-md-4" style="padding-left: 10px; padding-right: 10px;">
                                    <label class="form-label">Periodo</label>
                                    {!! Form::select('cod_periodo', $combo_periodos, null, ['class' => 'form-control select2', 'id' => 'cod_periodo']) !!}
                                </div>
                                <div class="col-md-4" style="padding-left: 10px; padding-right: 10px; display: flex; align-items: flex-end; gap: 10px;">
                                    <button type="button" id="btn-buscar" class="btn btn-primary" style="background-color: var(--primary); border: none; margin-top: 33px; font-weight: 700; flex: 1; height: 40px; border-radius: 6px;">
                                        <i class="mdi mdi-magnify"></i> Buscar
                                    </button>
                                    <button type="button" id="btn-exportar-excel" class="btn btn-success" style="background-color: #2d6a4f; border: none; margin-top: 33px; font-weight: 700; flex: 1; height: 40px; border-radius: 6px; display: none;">
                                        <i class="mdi mdi-file-excel"></i> Excel
                                    </button>
                                </div>
                            </div>
                            
                            <!-- SECCIÓN DE FECHAS DEL PERIODO -->
                            <div class="row" id="section-fechas-periodo" style="display: none; margin-left: -10px; margin-right: -10px; margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--border);">
                                <div class="col-md-12" style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 12px 20px; border-radius: 8px;">
                                    <div style="display: flex; align-items: center; gap: 20px;">
                                        <div id="display-fecha-inicio">
                                            <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block; text-transform: uppercase;">Inicio Comisión</span>
                                            <span class="fecha-val" style="font-size: 15px; font-weight: 700; color: #1e293b;">-</span>
                                        </div>
                                        <div style="color: #cbd5e1; font-size: 20px;">|</div>
                                        <div id="display-fecha-fin">
                                            <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block; text-transform: uppercase;">Fin Comisión</span>
                                            <span class="fecha-val" style="font-size: 15px; font-weight: 700; color: #1e293b;">-</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-default" id="btn-edit-fechas" style="border-radius: 6px; font-weight: 700;">
                                        <i class="mdi mdi-calendar-edit" style="color: var(--primary); font-size: 16px;"></i> Configurar Fechas
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- CONTENIDO RESULTADOS -->
                    <div class="premium-card" id="resultado-busqueda" style="display: none;">
                        <div class="text-center" style="padding: 40px; color: #6b7280;">
                            <i class="mdi mdi-inbox-arrow-down" style="font-size: 48px; color: #d1d5db; display: block; margin-bottom: 10px;"></i>
                            <p style="font-weight: 600; font-size: 16px;">Seleccione los filtros para visualizar la información.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR FECHAS -->
    <div id="modal-edit-fechas" tabindex="-1" role="dialog" class="modal fade colored-header colored-header-primary">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary);">
                    <button type="button" data-dismiss="modal" aria-hidden="true" class="close"><span class="mdi mdi-close"></span></button>
                    <h3 class="modal-title">Configurar Fechas de Comisión</h3>
                </div>
                <div class="modal-body">
                    <p id="modal-periodo-nombre" style="font-weight: 700; color: var(--primary); margin-bottom: 20px;"></p>
                    <div class="form-group">
                        <label class="form-label">Fecha Inicio Comisión</label>
                        <input type="date" id="m_fecha_inicio" class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label class="form-label">Fecha Fin Comisión</label>
                        <input type="date" id="m_fecha_fin" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Cancelar</button>
                    <button type="button" id="btn-save-modal-fechas" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script>
        const periodosData = {!! json_encode($periodos_raw) !!};

        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                width: '100%'
            });
            App.init();

            // Lógica cuando cambia el periodo
            $('#cod_periodo').on('change', function() {
                var selectedId = $(this).val();
                $('#section-fechas-periodo').hide();
                
                if (selectedId) {
                    var p = periodosData.find(x => x.COD_PERIODO === selectedId);
                    if (p) {
                        $('#section-fechas-periodo').show();
                        if (p.FECHA_INCIO_COMISION && p.FECHA_FIN_COMISION) {
                            $('#display-fecha-inicio .fecha-val').text(p.FECHA_INCIO_COMISION.substring(0, 10));
                            $('#display-fecha-fin .fecha-val').text(p.FECHA_FIN_COMISION.substring(0, 10));
                            $('#btn-edit-fechas').html('<i class="mdi mdi-pencil" style="color: var(--primary);"></i> Editar Fechas');
                        } else {
                            $('#display-fecha-inicio .fecha-val').text('No asignada');
                            $('#display-fecha-fin .fecha-val').text('No asignada');
                            $('#btn-edit-fechas').html('<i class="mdi mdi-plus" style="color: var(--success);"></i> Configurar Fechas');
                        }
                    }
                }
            });

            // Abrir Modal
            $('#btn-edit-fechas').on('click', function() {
                var selectedId = $('#cod_periodo').val();
                var p = periodosData.find(x => x.COD_PERIODO === selectedId);
                if (p) {
                    $('#modal-periodo-nombre').text('Periodo: ' + p.TXT_NOMBRE);
                    $('#m_fecha_inicio').val(p.FECHA_INCIO_COMISION ? p.FECHA_INCIO_COMISION.substring(0, 10) : '');
                    $('#m_fecha_fin').val(p.FECHA_FIN_COMISION ? p.FECHA_FIN_COMISION.substring(0, 10) : '');
                    $('#modal-edit-fechas').modal('show');
                }
            });

            // Guardar desde Modal
            $('#btn-save-modal-fechas').on('click', function() {
                var cod_periodo = $('#cod_periodo').val();
                var fecha_inicio = $('#m_fecha_inicio').val();
                var fecha_fin = $('#m_fecha_fin').val();

                if(!fecha_inicio || !fecha_fin) {
                    alerterrorajax('Debe ingresar ambas fechas.');
                    return false;
                }

                var _token = '{{ csrf_token() }}';
                abrircargando();
                $.post('{{ url("/ajax-guardar-fechas-periodo-comision") }}', {
                    _token: _token,
                    cod_periodo: cod_periodo,
                    fecha_inicio: fecha_inicio,
                    fecha_fin: fecha_fin
                }, function(response) {
                    cerrarcargando();
                    if(response.error == false){
                        alertajax(response.msj);
                        var p = periodosData.find(x => x.COD_PERIODO === cod_periodo);
                        if(p) {
                            p.FECHA_INCIO_COMISION = fecha_inicio + ' 00:00:00.000';
                            p.FECHA_FIN_COMISION = fecha_fin + ' 00:00:00.000';
                        }
                        $('#modal-edit-fechas').modal('hide');
                        $('#cod_periodo').trigger('change');
                    } else {
                        alerterrorajax(response.msj);
                    }
                });
            });

            $('#btn-buscar').on('click', function() {
                var cod_jefe = $('#cod_jefe').val();
                var cod_periodo = $('#cod_periodo').val();
                
                if(!cod_jefe || !cod_periodo) {
                    alerterrorajax('Por favor seleccione el Jefe de Venta y el Periodo');
                    return false;
                }

                // Validar que el periodo tenga fechas
                var p = periodosData.find(x => x.COD_PERIODO === cod_periodo);
                if(p && (!p.FECHA_INCIO_COMISION || !p.FECHA_FIN_COMISION)) {
                    alerterrorajax('Debe configurar las fechas del periodo antes de buscar.');
                    return false;
                }

                var _token = '{{ csrf_token() }}';
                var data = {
                    _token: _token,
                    cod_jefe: cod_jefe,
                    cod_periodo: cod_periodo,
                    fecha_ini: p.FECHA_INCIO_COMISION.substring(0, 10),
                    fecha_fin: p.FECHA_FIN_COMISION.substring(0, 10)
                };

                $('#btn-exportar-excel').hide();
                $('#resultado-busqueda').show().html('<div class="text-center" style="padding: 40px; color: #6b7280;"><div class="spinner-border text-primary" role="status"></div><p style="font-weight: 600; font-size: 16px; margin-top: 15px;">Consultando procedimiento... por favor espere.</p></div>');
                
                $.post('{{ url("/ajax-listar-comisiones-vendedor") }}', data, function(response) {
                    $('#btn-exportar-excel').show();
                    $('#resultado-busqueda').html(response);
                });
            });

            $('#btn-exportar-excel').on('click', function() {
                var cod_jefe = $('#cod_jefe').val();
                var cod_periodo = $('#cod_periodo').val();
                
                if(!cod_jefe || !cod_periodo) {
                    alerterrorajax('Por favor seleccione el Jefe de Venta y el Periodo');
                    return false;
                }

                var p = periodosData.find(x => x.COD_PERIODO === cod_periodo);
                if(p && (!p.FECHA_INCIO_COMISION || !p.FECHA_FIN_COMISION)) {
                    alerterrorajax('Debe configurar las fechas del periodo antes de exportar.');
                    return false;
                }

                var url = '{{ url("/exportar-excel-aplicar-comision") }}';
                url += '?cod_jefe=' + cod_jefe;
                url += '&cod_periodo=' + cod_periodo;
                url += '&fecha_ini=' + p.FECHA_INCIO_COMISION.substring(0, 10);
                url += '&fecha_fin=' + p.FECHA_FIN_COMISION.substring(0, 10);

                window.location.href = url;
            });
        });
    </script>
@stop
