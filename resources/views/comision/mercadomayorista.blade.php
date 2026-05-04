@extends('template')
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} " />
<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} " />
<style>
    :root {
        --primary: #4f46e5;
        --success: #10b981;
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
</style>
@stop

@section('section')
<div class="be-content">
    <div class="main-content container-fluid">
        <div class="row">
            <div class="col-sm-12">

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h1 style="font-weight: 800; font-size: 24px; color: #111827; margin: 0;">
                        <i class="fa fa-briefcase" style="color: var(--primary);"></i> Gestión de Comisiones - Mercado
                        Mayorista
                    </h1>
                </div>

                <div class="premium-card">
                    <h2 style="font-size: 15px; font-weight: 700; margin-bottom: 15px; color: #374151;">Filtros de
                        Búsqueda</h2>
                    <div class="row" style="margin-left: -10px; margin-right: -10px;">
                        <div class="col-md-3" style="padding-left: 10px; padding-right: 10px;">
                            <label class="form-label">Jefe de Ventas</label>
                            <select id="cod_jefe" class="form-control select2">
                                <option value="">Seleccione un jefe</option>
                                @foreach($jefes as $j)
                                    <option value="{{ $j->value }}">{{ $j->text }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" style="padding-left: 10px; padding-right: 10px;">
                            <label class="form-label">Periodo Inicio</label>
                            <select id="periodo_inicio" class="form-control select2">
                                @foreach($periodos as $p)
                                    <option value="{{ $p->value }}">{{ $p->text }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" style="padding-left: 10px; padding-right: 10px;">
                            <label class="form-label">Periodo Fin</label>
                            <select id="periodo_fin" class="form-control select2">
                                @foreach($periodos as $p)
                                    <option value="{{ $p->value }}">{{ $p->text }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3"
                            style="padding-top: 34px;padding-left: 10px; padding-right: 10px; display: flex; align-items: flex-end; gap: 10px;">
                            <button type="button" id="btn-buscar" class="btn btn-primary"
                                style="background-color: var(--primary); border: none; font-weight: 700; height: 40px; border-radius: 6px; flex: 1;">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                            <button type="button" id="btn-exportar" class="btn btn-success"
                                style="background-color: var(--success); border: none; font-weight: 700; height: 40px; border-radius: 6px; flex: 1;">
                                <i class="fa fa-file-excel-o"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="premium-card" id="resultado-busqueda" style="display: none;">
                    <!-- Aquí se cargará la tabla por AJAX -->
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
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });
        App.init();

        var periodosActuales = '';

        $('#btn-buscar').on('click', function () {
            var cod_jefe = $('#cod_jefe').val();
            var periodo_inicio = $('#periodo_inicio').val();
            var periodo_fin = $('#periodo_fin').val();

            if (!cod_jefe) {
                alert('Por favor seleccione un Jefe de Ventas.');
                return;
            }

            $('#resultado-busqueda').show().html('<div class="text-center" style="padding: 40px; color: #6b7280;"><div class="spinner-border text-primary" role="status"></div><p style="font-weight: 600; font-size: 16px; margin-top: 15px;">Buscando comisiones... por favor espere.</p></div>');

            $.post('{{ url("/ajax-buscar-comisiones-x-periodo") }}', {
                _token: '{{ csrf_token() }}',
                cod_jefe: cod_jefe,
                periodo_inicio: periodo_inicio,
                periodo_fin: periodo_fin
            }, function (response) {
                if (response.status === 'success') {
                    $('#resultado-busqueda').html(response.html);
                    periodosActuales = response.periodos_str;

                    $('.table-dynamic').DataTable({
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                        },
                        "pageLength": 25,
                        "order": []
                    });
                } else {
                    $('#resultado-busqueda').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }).fail(function () {
                $('#resultado-busqueda').html('<div class="alert alert-danger">Error de comunicación con el servidor.</div>');
            });
        });

        $('#btn-exportar').on('click', function () {
            var cod_jefe = $('#cod_jefe').val();
            if (!cod_jefe) {
                alert('Por favor seleccione un Jefe de Ventas para exportar.');
                return;
            }

            var p_ini = $('#periodo_inicio').val();
            var p_fin = $('#periodo_fin').val();

            var options = $('#periodo_inicio option');
            var idx_ini = options.filter(function() { return $(this).val() == p_ini; }).index();
            var idx_fin = options.filter(function() { return $(this).val() == p_fin; }).index();

            // Los periodos vienen en orden descendente (el más nuevo en índice 0).
            // Por ende, el Periodo Inicio (más antiguo) debe tener un índice mayor o igual al Periodo Fin (más reciente).
            if (idx_ini < idx_fin) {
                alert('Rango de periodos inválido. Asegúrese de que el periodo inicio no sea mayor al fin.');
                return;
            }

            var periodosArray = [];
            options.slice(idx_fin, idx_ini + 1).each(function () {
                periodosArray.push($(this).val());
            });

            if (periodosArray.length === 0) {
                alert('Rango de periodos inválido.');
                return;
            }

            var periodos_str = periodosArray.join(',');

            var url = '{{ url("/exportar-excel-comision-administrativo") }}' +
                '?cod_jefe=' + encodeURIComponent(cod_jefe) +
                '&cod_periodo=' + encodeURIComponent(periodos_str) +
                '&proviene=' + encodeURIComponent('MERCADO MAYORISTA');

            window.location.href = url;
        });
    });
</script>
@stop