<?php

// app/Console/Commands/FixCartTotals.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class FixCartTotals extends Command
{
    protected $signature = 'cart:fix-totals {--dry-run : Mostrar cambios sin aplicarlos}';
    protected $description = 'Recalcula los totales de todos los carritos basándose en sus items';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info("Ejecutando en modo DRY RUN - No se aplicarán cambios\n");
        }
        
        // Obtener todos los carritos con items
        $carts = Cart::has('items')->with('items.product')->get();
        
        $this->info("Encontrados {$carts->count()} carritos con items\n");
        
        $fixed = 0;
        $errors = 0;
        
        foreach ($carts as $cart) {
            try {
                $this->info("Procesando Cart ID: {$cart->id}");
                
                // Guardar valores actuales
                $oldSubtotal = $cart->subtotal;
                $oldTotal = $cart->total;
                
                // Recalcular cada item
                $newSubtotal = 0;
                
                foreach ($cart->items as $item) {
                    // Verificar que el producto existe
                    if (!$item->product) {
                        $this->error("  ⚠️  Item {$item->id} tiene product_id {$item->product_id} que no existe!");
                        continue;
                    }
                    
                    // Si el precio del item es 0 o incorrecto, recalcular
                    if ($item->price == 0 || $item->subtotal == 0) {
                        $this->info("  📝 Item {$item->id} necesita recálculo de precio");
                        
                        if (!$dryRun) {
                            $item->calculatePrice()->save();
                        }
                        
                        $this->info("    Precio: 0 -> {$item->price}");
                        $this->info("    Subtotal: 0 -> {$item->subtotal}");
                    }
                    
                    $newSubtotal += $item->subtotal;
                }
                
                // Mostrar cambios
                $this->info("  💰 Subtotal: {$oldSubtotal} -> {$newSubtotal}");
                
                // Calcular nuevo total
                $newTotal = $newSubtotal + $cart->delivery_fee - $cart->discount;
                $this->info("  💵 Total: {$oldTotal} -> {$newTotal}");
                
                // Aplicar cambios si no es dry run
                if (!$dryRun) {
                    $cart->subtotal = $newSubtotal;
                    $cart->total = $newTotal;
                    $cart->save();
                    
                    $this->info("  ✅ Carrito actualizado");
                } else {
                    $this->info("  🔍 [DRY RUN] No se aplicaron cambios");
                }
                
                $fixed++;
                $this->info("");
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("  ❌ Error procesando cart {$cart->id}: " . $e->getMessage());
            }
        }
        
        // Resumen
        $this->info(str_repeat('=', 50));
        $this->info("RESUMEN:");
        $this->info("  ✅ Carritos procesados: {$fixed}");
        $this->info("  ❌ Errores: {$errors}");
        
        if ($dryRun) {
            $this->warn("\n⚠️  Esto fue un DRY RUN. Para aplicar los cambios, ejecuta el comando sin --dry-run");
        }
    }
}

// INSTRUCCIONES DE USO:
// 1. Crear el archivo en app/Console/Commands/FixCartTotals.php
// 2. Ejecutar: php artisan cart:fix-totals --dry-run (para ver qué cambiaría)
// 3. Ejecutar: php artisan cart:fix-totals (para aplicar cambios)