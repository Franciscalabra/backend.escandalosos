
@extends('layouts.admin')

@section('title', 'Detalle de Orden')
@section('header', 'Detalle de Orden #' . $order->order_number)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Estado de la Orden -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Estado de la Orden</h5>
                    
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                                    <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>En Preparación</option>
                                    <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Lista para Entrega</option>
                                    <option value="delivering" {{ $order->status == 'delivering' ? 'selected' : '' }}>En Camino</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completada</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Actualizar Estado
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Tipo:</strong> 
                                {{ $order->delivery_type == 'delivery' ? 'Delivery' : 'Retiro en Local' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Método de Pago:</strong> 
                                {{ $order->payment_method == 'cash' ? 'Efectivo' : 
                                   ($order->payment_method == 'transfer' ? 'Transferencia' : 'Flow') }}
                            </p>
                            <p><strong>Estado de Pago:</strong>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ $order->payment_status == 'paid' ? 'Pagado' : 'Pendiente' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Productos -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Productos</h5>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->product_name }}
                                        @if($item->size)
                                            <span class="badge bg-secondary">{{ $item->size }}</span>
                                        @endif
                                        @if($item->customizations)
                                            <br>
                                            <small class="text-muted">
                                                Personalizado
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td>${{ number_format($item->total_price, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <td>${{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @if($order->delivery_fee > 0)
                                <tr>
                                    <th colspan="3" class="text-end">Delivery:</th>
                                    <td>${{ number_format($order->delivery_fee, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if($order->discount > 0)
                                <tr>
                                    <th colspan="3" class="text-end">Descuento:</th>
                                    <td class="text-success">-${{ number_format($order->discount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <td><strong>${{ number_format($order->total, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Notas -->
            @if($order->notes)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Notas del Cliente</h5>
                    <p>{{ $order->notes }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <!-- Información del Cliente -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Información del Cliente</h5>
                    
                    <p><strong>Nombre:</strong><br>{{ $order->customer_name }}</p>
                    <p><strong>Email:</strong><br>{{ $order->customer_email }}</p>
                    <p><strong>Teléfono:</strong><br>{{ $order->customer_phone }}</p>
                    
                    @if($order->delivery_type == 'delivery')
                        <hr>
                        <p><strong>Dirección de Entrega:</strong><br>
                            {{ $order->delivery_address }}<br>
                            {{ $order->delivery_commune }}
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Acciones Rápidas -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Acciones</h5>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimir Orden
                        </button>
                        
                        @if($order->customer_phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" 
                           target="_blank" class="btn btn-success">
                            <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
                        </a>
                        @endif
                        
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Órdenes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @media print {
        .sidebar, .top-header, .btn, .form-select {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }
    }
</style>
@endpush