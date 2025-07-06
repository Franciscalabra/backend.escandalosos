<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\ComboRuleController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Auth\AdminAuthController;

// ⬇️ SOLO AGREGA ESTAS 3 LÍNEAS ⬇️
// Ruta raíz - redirige al admin
Route::get('/', function () {
    return redirect('/admin');
});
// ⬆️ HASTA AQUÍ ⬆️

// Rutas de autenticación admin
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Rutas del panel administrativo (protegidas) - CAMBIO AQUÍ
Route::middleware([\App\Http\Middleware\AdminAuthenticate::class])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    
    // Categorías
    Route::resource('categories', CategoryController::class);
    
    // Productos
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    
    // Ingredientes
    Route::resource('ingredients', IngredientController::class);
    Route::post('ingredients/{ingredient}/toggle-status', [IngredientController::class, 'toggleStatus'])->name('ingredients.toggle-status');
    
    // Reglas de Combos
    Route::resource('combo-rules', ComboRuleController::class);
    Route::post('combo-rules/{rule}/toggle-status', [ComboRuleController::class, 'toggleStatus'])->name('combo-rules.toggle-status');
    
    // Zonas de Delivery
    Route::resource('delivery-zones', DeliveryZoneController::class);
    Route::post('delivery-zones/{zone}/toggle-status', [DeliveryZoneController::class, 'toggleStatus'])->name('delivery-zones.toggle-status');
    
    // Órdenes
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    
    // Reportes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
});