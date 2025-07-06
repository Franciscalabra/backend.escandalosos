@extends('layouts.admin')

@section('title', 'Reporte de Productos')
@section('header', 'Reporte de Productos')

@section('content')
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.products') }}" class="row g-3">
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
    
    <!-- Top Productos -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">🏆 Top 20 Productos Más Vendidos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="35%">Producto</th>
                            <th width="15%" class="text-center">Cantidad Vendida</th>
                            <th width="15%" class="text-end">Ingresos</th>
                            <th width="30%">% del Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalRevenue = $topProducts->sum('total_revenue');
                        @endphp
                        @forelse($topProducts as $index => $product)
                        <tr>
                            <td>
                                <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $product->product_name }}</strong>
                                @if($index < 3)
                                    <span class="badge bg-warning ms-1">
                                        <i class="fas fa-star"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary px-3 py-2">{{ $product->total_quantity }}</span>
                            </td>
                            <td class="text-end">
                                <strong>${{ number_format($product->total_revenue, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @php
                                    $percentage = $totalRevenue > 0 ? 
                                        ($product->total_revenue / $totalRevenue) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" 
                                         role="progressbar"
                                         style="width: {{ $percentage }}%"
                                         aria-valuenow="{{ $percentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay datos para el período seleccionado</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($topProducts->count() > 0)
                    <tfoot>
                        <tr class="fw-bold bg-light">
                            <td colspan="2">Total</td>
                            <td class="text-center">{{ $topProducts->sum('total_quantity') }}</td>
                            <td class="text-end">${{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            <td>100%</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <!-- Ventas por Categoría -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">📊 Ventas por Categoría</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
                <div class="col-md-6">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Ingresos</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalCategoryRevenue = $salesByCategory->sum('total_revenue');
                            @endphp
                            @foreach($salesByCategory as $category)
                            <tr>
                                <td>
                                    <i class="fas fa-circle me-2" style="color: {{ ['#dc3545', '#28a745', '#ffc107', '#17a2b8', '#6610f2'][$loop->index % 5] }}"></i>
                                    {{ $category->category_name }}
                                </td>
                                <td class="text-center">{{ $category->total_quantity }}</td>
                                <td class="text-end">${{ number_format($category->total_revenue, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    {{ $totalCategoryRevenue > 0 ? number_format(($category->total_revenue / $totalCategoryRevenue) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td class="text-center">{{ $salesByCategory->sum('total_quantity') }}</td>
                                <td class="text-end">${{ number_format($totalCategoryRevenue, 0, ',', '.') }}</td>
                                <td class="text-end">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis de Rendimiento -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Producto Estrella</h6>
                    <h4 class="text-primary">{{ $topProducts->first()->product_name ?? 'N/A' }}</h4>
                    <p class="mb-0">{{ $topProducts->first()->total_quantity ?? 0 }} unidades vendidas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Categoría Líder</h6>
                    <h4 class="text-success">{{ $salesByCategory->first()->category_name ?? 'N/A' }}</h4>
                    <p class="mb-0">${{ number_format($salesByCategory->first()->total_revenue ?? 0, 0, ',', '.') }} en ventas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Productos Únicos Vendidos</h6>
                    <h4 class="text-info">{{ $topProducts->count() }}</h4>
                    <p class="mb-0">En el período seleccionado</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botones de Acción -->
    <div class="text-center">
        <button class="btn btn-success" onclick="exportProductsToCSV()">
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
    // Datos para el gráfico de categorías
    const categoryData = @json($salesByCategory);
    
    // Gráfico de dona para categorías
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.category_name),
            datasets: [{
                data: categoryData.map(item => item.total_revenue),
                backgroundColor: [
                    '#dc3545',
                    '#28a745',
                    '#ffc107',
                    '#17a2b8',
                    '#6610f2'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return context.label + ': $' + value.toLocaleString('es-CL') + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    
    // Función para exportar productos a CSV
    function exportProductsToCSV() {
        const productsData = @json($topProducts);
        
        // Crear contenido CSV
        let csv = 'Posicion,Producto,Cantidad Vendida,Ingresos\n';
        productsData.forEach((item, index) => {
            csv += `${index + 1},"${item.product_name}",${item.total_quantity},${item.total_revenue}\n`;
        });
        
        // Crear blob y descargar
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte_productos_${new Date().toISOString().split('T')[0]}.csv`;
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
        .btn, .form-control {
            display: none !important;
        }
        .card {
            border: 1px solid #dee2e6 !important;
        }
        .progress {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
    
    .progress {
        position: relative;
    }
    
    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
</style>
@endpush