@extends('layouts.admin')

@section('title', 'Reporte de Ventas')
@section('header', 'Reporte de Ventas')

@section('content')
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.sales') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="start_date" class="form-control" 
                           value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="end_date" class="form-control" 
                           value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Resumen General -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-dollar-sign"></i> Ingresos Totales
                    </h6>
                    <h3>${{ number_format($totals->total_revenue ?? 0, 0, ',', '.') }}</h3>
                    <small>Período seleccionado</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-shopping-cart"></i> Órdenes Totales
                    </h6>
                    <h3>{{ $totals->total_orders ?? 0 }}</h3>
                    <small>Completadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-chart-line"></i> Ticket Promedio
                    </h6>
                    <h3>${{ number_format($totals->average_order ?? 0, 0, ',', '.') }}</h3>
                    <small>Por orden</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-percentage"></i> Total Descuentos
                    </h6>
                    <h3>${{ number_format($totals->total_discounts ?? 0, 0, ',', '.') }}</h3>
                    <small>Aplicados</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráfico de Ventas por Día -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">📈 Ventas por Día</h5>
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>
    
    <!-- Tablas de Resumen -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">💳 Por Método de Pago</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Método</th>
                                <th class="text-center">Órdenes</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalRevenue = $salesByPayment->sum('total_sales');
                            @endphp
                            @foreach($salesByPayment as $payment)
                            <tr>
                                <td>
                                    @if($payment->payment_method == 'cash')
                                        <i class="fas fa-money-bill text-success"></i> Efectivo
                                    @elseif($payment->payment_method == 'transfer')
                                        <i class="fas fa-university text-info"></i> Transferencia
                                    @else
                                        <i class="fas fa-credit-card text-primary"></i> Flow
                                    @endif
                                </td>
                                <td class="text-center">{{ $payment->total_orders }}</td>
                                <td class="text-end">${{ number_format($payment->total_sales, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    {{ $totalRevenue > 0 ? number_format(($payment->total_sales / $totalRevenue) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td class="text-center">{{ $salesByPayment->sum('total_orders') }}</td>
                                <td class="text-end">${{ number_format($totalRevenue, 0, ',', '.') }}</td>
                                <td class="text-end">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">🚚 Por Tipo de Entrega</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th class="text-center">Órdenes</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalDeliveryRevenue = $salesByDelivery->sum('total_sales');
                            @endphp
                            @foreach($salesByDelivery as $delivery)
                            <tr>
                                <td>
                                    @if($delivery->delivery_type == 'delivery')
                                        <i class="fas fa-truck text-primary"></i> Delivery
                                    @else
                                        <i class="fas fa-store text-success"></i> Retiro
                                    @endif
                                </td>
                                <td class="text-center">{{ $delivery->total_orders }}</td>
                                <td class="text-end">${{ number_format($delivery->total_sales, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    {{ $totalDeliveryRevenue > 0 ? number_format(($delivery->total_sales / $totalDeliveryRevenue) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td class="text-center">{{ $salesByDelivery->sum('total_orders') }}</td>
                                <td class="text-end">${{ number_format($totalDeliveryRevenue, 0, ',', '.') }}</td>
                                <td class="text-end">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabla Detallada -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">📊 Detalle por Día</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th class="text-center">Órdenes</th>
                            <th class="text-end">Ventas</th>
                            <th class="text-end">Ticket Promedio</th>
                            <th class="text-end">Variación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $previousTotal = 0;
                        @endphp
                        @foreach($salesByDay as $day)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $day->total_orders }}</td>
                            <td class="text-end">${{ number_format($day->total_sales, 0, ',', '.') }}</td>
                            <td class="text-end">${{ number_format($day->average_order, 0, ',', '.') }}</td>
                            <td class="text-end">
                                @if($previousTotal > 0)
                                    @php
                                        $variation = (($day->total_sales - $previousTotal) / $previousTotal) * 100;
                                    @endphp
                                    <span class="badge bg-{{ $variation >= 0 ? 'success' : 'danger' }}">
                                        {{ $variation >= 0 ? '+' : '' }}{{ number_format($variation, 1) }}%
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @php
                            $previousTotal = $day->total_sales;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Botones de Acción -->
    <div class="text-center">
        <button class="btn btn-success" onclick="exportToCSV()">
            <i class="fas fa-download"></i> Exportar a CSV
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir Reporte
        </button>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos para el gráfico
    const salesData = @json($salesByDay);
    
    // Configurar gráfico
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('es-CL', { day: '2-digit', month: 'short' });
            }),
            datasets: [{
                label: 'Ventas',
                data: salesData.map(item => item.total_sales),
                borderColor: 'rgb(220, 53, 69)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Órdenes',
                data: salesData.map(item => item.total_orders * 1000), // Multiplicado para visualización
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.1,
                fill: false,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Órdenes') {
                                return context.dataset.label + ': ' + (context.parsed.y / 1000);
                            }
                            return context.dataset.label + ': $' + context.parsed.y.toLocaleString('es-CL');
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString('es-CL');
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value / 1000;
                        }
                    }
                }
            }
        }
    });
    
    // Función para exportar a CSV
    function exportToCSV() {
        // Crear contenido CSV
        let csv = 'Fecha,Ordenes,Ventas,Ticket Promedio\n';
        salesData.forEach(item => {
            csv += `${item.date},${item.total_orders},${item.total_sales},${item.average_order}\n`;
        });
        
        // Crear blob y descargar
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte_ventas_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }
</script>
@endpush

@push('styles')
<style>
    @media print {
        .btn, .card-header, .form-control, .form-label {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush