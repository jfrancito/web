@extends('template')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <style>
        :root {
            --primary: #4f46e5;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
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
            height: 100%;
        }

        .kpi-card {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .kpi-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
            color: white;
        }

        .kpi-info h3 {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 700;
        }

        .kpi-info p {
            margin: 5px 0 0 0;
            font-size: 22px;
            font-weight: 800;
            color: #111827;
        }
        
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
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
    <div class="be-content">
        <div class="main-content container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h1 style="font-weight: 800; font-size: 24px; color: #111827; margin: 0;">
                            <i class="fa fa-line-chart" style="color: var(--primary);"></i> Dashboard Gerencial: Comisiones Mayorista
                        </h1>
                    </div>

                    <!-- Filtros -->
                    <div class="premium-card" style="height: auto;">
                        <div class="row" style="margin-left: -10px; margin-right: -10px;">
                            <div class="col-md-3" style="padding-left: 10px; padding-right: 10px;">
                                <label class="form-label">Jefe de Ventas</label>
                                <select id="cod_jefe" class="form-control select2">
                                    <option value="ALL">TODOS LOS JEFES</option>
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
                            <div class="col-md-3" style="padding-top: 34px; padding-left: 10px; padding-right: 10px; display: flex; align-items: flex-end;">
                                <button type="button" id="btn-generar" class="btn btn-primary" style="background-color: var(--primary); border: none; font-weight: 700; height: 40px; border-radius: 6px; width: 100%;">
                                    <i class="fa fa-refresh"></i> Generar Reporte
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="dashboard-content" style="display: none;">
                        <!-- KPIs -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="kpi-card">
                                    <div class="kpi-icon" style="background: var(--info);">
                                        <i class="fa fa-cubes"></i>
                                    </div>
                                    <div class="kpi-info">
                                        <h3>Volumen Total (50kg)</h3>
                                        <p id="kpi-volumen">0.00</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="kpi-card">
                                    <div class="kpi-icon" style="background: var(--success);">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div class="kpi-info">
                                        <h3>Ventas Brutas (S/)</h3>
                                        <p id="kpi-ventas">0.00</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="kpi-card">
                                    <div class="kpi-icon" style="background: var(--warning);">
                                        <i class="fa fa-credit-card"></i>
                                    </div>
                                    <div class="kpi-info">
                                        <h3>Comisiones Pagadas (S/)</h3>
                                        <p id="kpi-comisiones">0.00</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="kpi-card">
                                    <div class="kpi-icon" style="background: var(--danger);">
                                        <i class="fa fa-check-circle"></i>
                                    </div>
                                    <div class="kpi-info">
                                        <h3>Efectividad (%)</h3>
                                        <p id="kpi-efectividad">0%</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row 1 -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="premium-card">
                                    <h4 style="font-weight: 700; margin-top: 0; color: #1f2937;">Top 10 Jefes: Ventas vs Comisiones</h4>
                                    <div class="chart-container">
                                        <canvas id="chart-jefes"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="premium-card">
                                    <h4 style="font-weight: 700; margin-top: 0; color: #1f2937;">Motivos de Retención/Pago de Comisión</h4>
                                    <div class="chart-container">
                                        <canvas id="chart-motivos"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row 2 -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="premium-card">
                                    <h4 style="font-weight: 700; margin-top: 0; color: #1f2937;">Top 15 Productos (Subfamilia): Volumen vs Comisiones</h4>
                                    <div class="chart-container">
                                        <canvas id="chart-productos"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading overlay -->
                    <div id="loading" style="display: none; text-align: center; padding: 50px;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h4 style="margin-top: 20px; color: #6b7280;">Procesando datos...</h4>
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({ width: '100%' });
            App.init();

            let chartJefes = null;
            let chartMotivos = null;
            let chartProductos = null;

            $('#btn-generar').on('click', function() {
                var cod_jefe = $('#cod_jefe').val();
                var p_ini = $('#periodo_inicio').val();
                var p_fin = $('#periodo_fin').val();

                var options = $('#periodo_inicio option');
                var idx_ini = options.filter(function() { return $(this).val() == p_ini; }).index();
                var idx_fin = options.filter(function() { return $(this).val() == p_fin; }).index();

                if (idx_ini < idx_fin) {
                    alert('Rango de periodos inválido. Asegúrese de que el periodo inicio no sea mayor al fin.');
                    return;
                }

                $('#dashboard-content').hide();
                $('#loading').show();

                $.post('{{ url("/ajax-dashboard-comisiones-mayorista") }}', {
                    _token: '{{ csrf_token() }}',
                    cod_jefe: cod_jefe,
                    periodo_inicio: p_ini,
                    periodo_fin: p_fin
                }, function(res) {
                    $('#loading').hide();

                    if (res.status === 'success') {
                        $('#dashboard-content').show();
                        
                        // Set KPIs
                        $('#kpi-volumen').text(res.kpis.volumen_total);
                        $('#kpi-ventas').text('S/ ' + res.kpis.ventas_brutas);
                        $('#kpi-comisiones').text('S/ ' + res.kpis.comisiones_pagadas);
                        $('#kpi-efectividad').text(res.kpis.efectividad + '%');

                        // Destroy old charts if exist
                        if (chartJefes) chartJefes.destroy();
                        if (chartMotivos) chartMotivos.destroy();
                        if (chartProductos) chartProductos.destroy();

                        // 1. Chart Jefes (Bar/Line combination)
                        var ctxJefes = document.getElementById('chart-jefes').getContext('2d');
                        chartJefes = new Chart(ctxJefes, {
                            type: 'bar',
                            data: {
                                labels: res.charts.jefes.labels,
                                datasets: [
                                    {
                                        label: 'Comisiones (S/)',
                                        data: res.charts.jefes.comisiones,
                                        type: 'line',
                                        borderColor: '#f59e0b',
                                        backgroundColor: '#f59e0b',
                                        borderWidth: 3,
                                        yAxisID: 'y1',
                                    },
                                    {
                                        label: 'Ventas (S/)',
                                        data: res.charts.jefes.ventas,
                                        backgroundColor: '#3b82f6',
                                        borderRadius: 4,
                                        yAxisID: 'y',
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Ventas Brutas' } },
                                    y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Comisiones' } },
                                }
                            }
                        });

                        // 2. Chart Motivos (Doughnut)
                        var ctxMotivos = document.getElementById('chart-motivos').getContext('2d');
                        chartMotivos = new Chart(ctxMotivos, {
                            type: 'doughnut',
                            data: {
                                labels: res.charts.motivos.labels,
                                datasets: [{
                                    data: res.charts.motivos.data,
                                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#9ca3af'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'right' },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.label || '';
                                                let value = context.parsed || 0;
                                                return label + ': S/ ' + value.toLocaleString('es-PE', { minimumFractionDigits: 2 });
                                            }
                                        }
                                    }
                                },
                                cutout: '70%'
                            }
                        });

                        // 3. Chart Productos (Bar)
                        var ctxProductos = document.getElementById('chart-productos').getContext('2d');
                        chartProductos = new Chart(ctxProductos, {
                            type: 'bar',
                            data: {
                                labels: res.charts.subfamilias.labels,
                                datasets: [
                                    {
                                        label: 'Volumen (Sacos 50kg)',
                                        data: res.charts.subfamilias.volumen,
                                        backgroundColor: '#10b981',
                                        borderRadius: 4,
                                        yAxisID: 'y'
                                    },
                                    {
                                        label: 'Comisión Pagada (S/)',
                                        data: res.charts.subfamilias.comision,
                                        backgroundColor: '#4f46e5',
                                        borderRadius: 4,
                                        yAxisID: 'y1'
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Volumen' } },
                                    y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Comisión' } },
                                }
                            }
                        });

                    } else {
                        alert(res.message);
                    }
                }).fail(function() {
                    $('#loading').hide();
                    alert("Error de conexión con el servidor.");
                });
            });
            
            // Trigger first load
            setTimeout(function() {
                $('#btn-generar').trigger('click');
            }, 500);
        });
    </script>
@stop
