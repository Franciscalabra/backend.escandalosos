<?php
// app/Http/Controllers/Api/OrderController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items.product');
        
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debe iniciar sesión para ver sus pedidos'
            ], 401);
        }
        
        $orders = $query->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
    
    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('items.product')
            ->firstOrFail();
            
        // Check if user can view this order
        if (auth()->check() && $order->user_id !== auth()->id()) {
            abort(403);
        }
        
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
    
    public function track($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'estimated_delivery_at' => $order->estimated_delivery_at,
                'delivered_at' => $order->delivered_at
            ]
        ]);
    }
}