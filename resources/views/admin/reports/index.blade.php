@extends('layouts.admin')

@section('title', 'Reportes')
@section('header', 'Centro de Reportes')

@section('content')
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-chart-line fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title text-center">Reporte de Ventas</h5>
                    <p class="card-text text-center text-muted">
                        Analiza las ventas por período, método de pago y tipo de entrega.
                    </p>
                    <div class="d-grid">
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-pizza-slice fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title text-center">Reporte de Productos</h5>
                    <p class="card-text text-center text-muted">
                        Descubre los productos más vendidos y el rendimiento por categoría.
                    </p>
                    <div class="d-grid">
                        <a href="{{ route('admin.reports.products') }}" class="btn btn-success">
                            <i class="fas fa-arrow-right"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resumen Rápido -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">📊 Resumen Rápido - Últimos 30 días</h5>
        </div>
        <div class="card-body">
            @php
                $startDate = now()->subDays(30);
                $endDate = now();
                
                $quickStats = \App\Models\Order::whereBetween('created_at', [$startDate, $endDate])
                    ->where('payment_status', 'paid')
                    ->selectRaw('
                        COUNT(*) as total_orders,
                        SUM(total) as total_revenue,
                        AVG(total) as average_order
                    ')
                    ->first();
                    
                $totalProducts = \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->where('orders.payment_status', 'paid')
                    ->sum('order_items.quantity');
            @endphp
            
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="p-3">
                        <h3 class="text-primary">${{ number_format($quickStats->total_revenue ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Ventas Totales</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <h3 class="text-success">{{ $quickStats->total_orders ?? 0 }}</h3>
                        <p class="text-muted mb-0">Órdenes Completadas</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <h3 class="text-info">${{ number_format($quickStats->average_order ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Ticket Promedio</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <h3 class="text-warning">{{ $totalProducts ?? 0 }}</h3>
                        <p class="text-muted mb-0">Productos Vendidos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-download text-primary"></i> Exportar Datos
                    </h6>
                    <p class="card-text small text-muted">
                        Descarga los datos de ventas en formato CSV
                    </p>
                    <button class="btn btn-sm btn-outline-primary" onclick="alert('Función de exportación en desarrollo')">
                        Exportar CSV
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-envelope text-success"></i> Reporte por Email
                    </h6>
                    <p class="card-text small text-muted">
                        Recibe reportes semanales automáticos
                    </p>
                    <button class="btn btn-sm btn-outline-success" onclick="alert('Función en desarrollo')">
                        Configurar
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-calendar text-warning"></i> Reportes Personalizados
                    </h6>
                    <p class="card-text small text-muted">
                        Crea reportes con fechas específicas
                    </p>
                    <button class="btn btn-sm btn-outline-warning" onclick="alert('Función en desarrollo')">
                        Crear Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection