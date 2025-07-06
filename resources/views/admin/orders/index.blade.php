
@extends('layouts.admin')

@section('title', 'Órdenes')
@section('header', 'Gestión de Órdenes')

@section('content')
    <div class="mb-4">
        <!-- Filtros -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>En Preparación</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Lista</option>
                    <option value="delivering" {{ request('status') == 'delivering' ? 'selected' : '' }}>En Camino</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="date" class="form-control" 
                       value="{{ request('date') }}" onchange="this.form.submit()">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Número de orden, cliente, teléfono..."
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Tipo</th>
                            <th>Pago</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong>{{ $order->order_number }}</strong>
                            </td>
                            <td>
                                {{ $order->customer_name }}<br>
                                <small class="text-muted">{{ $order->customer_phone }}</small>
                            </td>
                            <td>${{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                @if($order->delivery_type == 'delivery')
                                    <i class="fas fa-truck text-primary"></i> Delivery
                                @else
                                    <i class="fas fa-store text-success"></i> Retiro
                                @endif
                            </td>
                            <td>
                                @if($order->payment_method == 'cash')
                                    <i class="fas fa-money-bill text-success"></i> Efectivo
                                @elseif($order->payment_method == 'transfer')
                                    <i class="fas fa-university text-info"></i> Transfer
                                @else
                                    <i class="fas fa-credit-card text-primary"></i> Flow
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'preparing' => 'info',
                                        'ready' => 'primary',
                                        'delivering' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'confirmed' => 'Confirmada',
                                        'preparing' => 'Preparando',
                                        'ready' => 'Lista',
                                        'delivering' => 'En Camino',
                                        'completed' => 'Completada',
                                        'cancelled' => 'Cancelada'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $order->created_at->format('d/m H:i') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection