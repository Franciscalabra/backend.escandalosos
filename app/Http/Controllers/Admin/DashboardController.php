<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas del día
        $todayStats = [
            'orders' => Order::whereDate('created_at', today())->count(),
            'revenue' => Order::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_products' => Product::where('active', true)->count()
        ];
        
        // Órdenes recientes
        $recentOrders = Order::with('items')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Productos más vendidos
        $topProducts = DB::table('order_items')
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();
        
        // Ventas de los últimos 7 días
        $salesChart = Order::where('created_at', '>=', now()->subDays(7))
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as total_orders')
            ]);
        
        return view('admin.dashboard', compact(
            'todayStats', 
            'recentOrders', 
            'topProducts',
            'salesChart'
        ));
    }
}
