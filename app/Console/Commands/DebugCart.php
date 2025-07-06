<?php

// app/Console/Commands/DebugCart.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Models\CartItem;

class DebugCart extends Command
{
    protected $signature = 'cart:debug {cart_id?}';
    protected $description = 'Debug cart issues and recalculate totals';

    public function handle()
    {
        $cartId = $this->argument('cart_id');
        
        if ($cartId) {
            $carts = Cart::where('id', $cartId)->get();
        } else {
            $carts = Cart::all();
        }
        
        $this->info("Debugging {$carts->count()} cart(s)...\n");
        
        foreach ($carts as $cart) {
            $this->info("Cart ID: {$cart->id}");
            $this->info("Session ID: " . ($cart->session_id ?: 'null'));
            $this->info("User ID: " . ($cart->user_id ?: 'null'));
            $this->info("Current Subtotal: {$cart->subtotal}");
            $this->info("Current Total: {$cart->total}");
            $this->info("Items: {$cart->items->count()}");
            
            if ($cart->items->count() > 0) {
                $this->table(
                    ['ID', 'Product ID', 'Quantity', 'Size', 'Price', 'Subtotal'],
                    $cart->items->map(function ($item) {
                        return [
                            $item->id,
                            $item->product_id,
                            $item->quantity,
                            $item->size ?: 'null',
                            $item->price,
                            $item->subtotal
                        ];
                    })
                );
                
                // Recalcular precios de items
                $this->info("\nRecalculando precios de items...");
                foreach ($cart->items as $item) {
                    $oldPrice = $item->price;
                    $oldSubtotal = $item->subtotal;
                    
                    $item->calculatePrice()->save();
                    
                    $this->info("Item {$item->id}: Price {$oldPrice} -> {$item->price}, Subtotal {$oldSubtotal} -> {$item->subtotal}");
                }
                
                // Recalcular totales del carrito
                $this->info("\nRecalculando totales del carrito...");
                $oldSubtotal = $cart->subtotal;
                $oldTotal = $cart->total;
                
                $cart->calculateTotals();
                
                $this->info("Subtotal: {$oldSubtotal} -> {$cart->subtotal}");
                $this->info("Total: {$oldTotal} -> {$cart->total}");
            }
            
            $this->info("\n" . str_repeat('-', 50) . "\n");
        }
        
        $this->info("Debug completado!");
    }
}

// Registrar el comando en app/Console/Kernel.php
// Agregar en el método commands():
// \App\Console\Commands\DebugCart::class,