@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Órdenes Hoy</h6>
                            <h2 class="mb-0">{{ $todayStats['orders'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Ingresos Hoy</h6>
                            <h2 class="mb-0">${{ number_format($todayStats['revenue'] ?? 0, 0, ',', '.') }}</h2>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Órdenes Pendientes</h6>
                            <h2 class="mb-0">{{ $todayStats['pending_orders'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Productos Activos</h6>
                            <h2 class="mb-0">{{ $todayStats['active_products'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-pizza-slice fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Órdenes Recientes y Productos Más Vendidos -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Órdenes Recientes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Orden</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recentOrders) && count($recentOrders) > 0)
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>${{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No hay órdenes recientes
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top Productos</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @if(isset($topProducts) && count($topProducts) > 0)
                            @foreach($topProducts as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $product->product_name }}</span>
                                <span class="badge bg-primary rounded-pill">{{ $product->total_sold }}</span>
                            </li>
                            @endforeach
                        @else
                            <li class="list-group-item text-center text-muted">
                                No hay datos de productos
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Acceso Rápido -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Acceso Rápido</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary w-100">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-folder"></i> Ver Categorías
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-info w-100">
                        <i class="fas fa-shopping-cart"></i> Ver Órdenes
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-success w-100">
                        <i class="fas fa-chart-bar"></i> Ver Reportes
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection