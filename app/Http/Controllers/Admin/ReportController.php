<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Muestra el dashboard principal de reportes.
     */
    public function index()
    {
        // Simplemente muestra la vista del hub de reportes.
        return view('admin.reports.index');
    }

    /**
     * Muestra el reporte de ventas detallado.
     */
    public function sales(Request $request)
    {
        // Lógica para el reporte de ventas (puedes moverla aquí desde donde la tengas)
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Aquí iría la lógica compleja para obtener todos los datos de sales.blade.php
        // Por ahora, lo mantenemos simple para que la vista cargue.
        $totals = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(total) as total_revenue, COUNT(*) as total_orders, AVG(total) as average_order, SUM(discount) as total_discounts')
            ->first();

        return view('admin.reports.sales', compact('startDate', 'endDate', 'totals'));
    }

    /**
     * Muestra el reporte de productos detallado.
     */
    public function products(Request $request)
    {
        // Lógica para el reporte de productos
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('product_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_price) as total_revenue'))
            ->groupBy('product_name')
            ->orderBy('total_revenue', 'desc')
            ->limit(20)
            ->get();
            
        return view('admin.reports.products', compact('startDate', 'endDate', 'topProducts'));
    }
}