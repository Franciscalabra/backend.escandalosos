<?php

// app/Http/Controllers/Admin/OrderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with filters
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);
        
        // Filtro por estado
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filtro por fecha
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }
        
        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }
        
        // Ordenar por más recientes primero
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }
    
    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        // Cargar relaciones necesarias
        $order->load(['items.product', 'user']);
        
        return view('admin.orders.show', compact('order'));
    }
    
    /**
     * Update the status of an order
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivering,completed,cancelled'
        ]);
        
        $oldStatus = $order->status;
        $newStatus = $request->status;
        
        // Actualizar el estado
        $order->updateStatus($newStatus);
        
        // Registrar el cambio (opcional - para auditoría)
        \Log::info("Order {$order->order_number} status changed from {$oldStatus} to {$newStatus} by admin " . auth()->guard('admin')->user()->name);
        
        // Aquí puedes agregar lógica adicional según el cambio de estado
        switch ($newStatus) {
            case 'confirmed':
                // Enviar notificación al cliente de que su pedido fue confirmado
                // $this->sendOrderConfirmedNotification($order);
                break;
                
            case 'preparing':
                // Notificar a cocina
                // $this->notifyKitchen($order);
                break;
                
            case 'ready':
                // Notificar al cliente que su pedido está listo
                // $this->sendOrderReadyNotification($order);
                break;
                
            case 'delivering':
                // Asignar repartidor y notificar
                // $this->assignDeliveryPerson($order);
                break;
                
            case 'completed':
                // Marcar como entregado
                // $this->markAsDelivered($order);
                break;
                
            case 'cancelled':
                // Procesar cancelación y posible reembolso
                // $this->processCancellation($order);
                break;
        }
        
        return back()->with('success', 'Estado de la orden actualizado exitosamente');
    }
    
    /**
     * Export orders to CSV (opcional)
     */
    public function export(Request $request)
    {
        $query = Order::with(['items']);
        
        // Aplicar los mismos filtros que en index
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->get();
        
        // Crear CSV
        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Encabezados del CSV
            fputcsv($file, [
                'Orden #',
                'Fecha',
                'Cliente',
                'Email',
                'Teléfono',
                'Tipo',
                'Dirección',
                'Comuna',
                'Subtotal',
                'Delivery',
                'Descuento',
                'Total',
                'Método Pago',
                'Estado Pago',
                'Estado Orden'
            ]);
            
            // Datos
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer_name,
                    $order->customer_email,
                    $order->customer_phone,
                    $order->delivery_type,
                    $order->delivery_address,
                    $order->delivery_commune,
                    $order->subtotal,
                    $order->delivery_fee,
                    $order->discount,
                    $order->total,
                    $order->payment_method,
                    $order->payment_status,
                    $order->status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Print order (for kitchen or delivery)
     */
    public function print(Order $order)
    {
        $order->load(['items.product']);
        
        return view('admin.orders.print', compact('order'));
    }
    
    /**
     * Get order statistics for dashboard
     */
    public static function getTodayStats()
    {
        return [
            'total' => Order::whereDate('created_at', today())->count(),
            'pending' => Order::whereDate('created_at', today())->where('status', 'pending')->count(),
            'completed' => Order::whereDate('created_at', today())->where('status', 'completed')->count(),
            'revenue' => Order::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total')
        ];
    }
    
    /**
     * Cancel an order
     */
    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        // Solo se pueden cancelar órdenes que no estén completadas o ya canceladas
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'No se puede cancelar esta orden');
        }
        
        // Actualizar estado
        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_reason' => $request->reason,
            'cancelled_by' => auth()->guard('admin')->user()->id
        ]);
        
        // Si el pago ya fue procesado, marcar para reembolso
        if ($order->payment_status === 'paid' && $order->payment_method !== 'cash') {
            $order->update(['needs_refund' => true]);
            
            // Aquí puedes agregar lógica para procesar el reembolso automáticamente
            // $this->processRefund($order);
        }
        
        // Notificar al cliente
        // $this->sendCancellationNotification($order);
        
        return back()->with('success', 'Orden cancelada exitosamente');
    }
}