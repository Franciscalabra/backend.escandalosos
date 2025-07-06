<?php

// app/Http/Controllers/Api/CartController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\ManagesCart; // <-- 1. Importar el Trait


class CartController extends Controller
{
    use ManagesCart; // <-- 2. Usar el Trait
    public function getCart(Request $request)
    {
        try {
            $cart = $this->getCurrentCart($request);
            $cart->load(['items.product']);
            
            // Si el total es 0 pero hay items, recalcular
            if ($cart->total == 0 && $cart->items->count() > 0) {
                $cart->calculateTotals();
            }
            
            // Aplicar reglas de combo
            $cart->applyComboRules();
            
            return response()->json([
                'success' => true,
                'data' => $cart
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el carrito',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'customizations' => 'nullable|array'
        ]);
        
        DB::beginTransaction();
        
        try {
            $cart = $this->getCurrentCart($request);
            $product = Product::findOrFail($request->product_id);
            
            // Verificar si el item ya existe
            $existingItem = $cart->items()
                ->where('product_id', $product->id)
                ->where('size', $request->size)
                ->where(function($query) use ($request) {
                    if ($request->customizations) {
                        $query->whereJsonContains('customizations', $request->customizations);
                    } else {
                        $query->whereNull('customizations');
                    }
                })
                ->first();
                
            if ($existingItem) {
                // Si existe, actualizar cantidad
                $existingItem->quantity += $request->quantity;
                $existingItem->calculatePrice()->save();
            } else {
                // Si no existe, crear nuevo item
                $item = new CartItem([
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'size' => $request->size,
                    'customizations' => $request->customizations
                ]);
                
                $item->calculatePrice();
                $cart->items()->save($item);
            }
            
            // Aplicar reglas de combo y recalcular totales
            $cart->applyComboRules()->calculateTotals();
            
            DB::commit();
            
            // Recargar relaciones
            $cart->load(['items.product']);
            
            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'data' => $cart
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error adding item to cart: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar producto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);
        
        DB::beginTransaction();
        
        try {
            $cart = $this->getCurrentCart($request);
            $item = $cart->items()->findOrFail($itemId);
            
            if ($request->quantity === 0) {
                // Si la cantidad es 0, eliminar el item
                $item->delete();
            } else {
                // Actualizar cantidad y recalcular precio
                $item->quantity = $request->quantity;
                $item->calculatePrice()->save();
            }
            
            // Aplicar reglas de combo y recalcular totales
            $cart->applyComboRules()->calculateTotals();
            
            DB::commit();
            
            // Recargar relaciones
            $cart->load(['items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Carrito actualizado',
                'data' => $cart
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating cart item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar carrito: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function removeItem(Request $request, $itemId)
    {
        DB::beginTransaction();
        
        try {
            $cart = $this->getCurrentCart($request);
            $item = $cart->items()->findOrFail($itemId);
            
            // Eliminar el item
            $item->delete();
            
            // Aplicar reglas de combo y recalcular totales
            $cart->applyComboRules()->calculateTotals();
            
            DB::commit();
            
            // Recargar relaciones
            $cart->load(['items.product']);
            
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'data' => $cart
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error removing cart item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function clear(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $cart = $this->getCurrentCart($request);
            
            // Eliminar todos los items
            $cart->items()->delete();
            
            // Resetear totales
            $cart->update([
                'subtotal' => 0,
                'discount' => 0,
                'total' => 0,
                'delivery_fee' => 0
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Carrito vaciado',
                'data' => $cart
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error clearing cart: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al vaciar carrito: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getCurrentCart(Request $request)
    {
        $sessionId = $request->session()->getId();
        $userId = auth()->id();
        
        try {
            // Buscar carrito existente
            $cart = Cart::where(function($query) use ($sessionId, $userId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })->first();
            
            // Si no existe, crear uno nuevo
            if (!$cart) {
                $cart = Cart::create([
                    'session_id' => $userId ? null : $sessionId,
                    'user_id' => $userId,
                    'subtotal' => 0,
                    'delivery_fee' => 0,
                    'discount' => 0,
                    'total' => 0
                ]);
            }
            
            // Si el usuario acaba de iniciar sesión, fusionar carrito de sesión
            if ($userId && $cart->session_id) {
                $this->mergeSessionCart($cart, $sessionId);
            }
            
            return $cart;
            
        } catch (\Exception $e) {
            Log::error('Error getting current cart: ' . $e->getMessage());
            
            // Si hay cualquier error, crear un carrito nuevo
            return Cart::create([
                'session_id' => $userId ? null : $sessionId,
                'user_id' => $userId,
                'subtotal' => 0,
                'delivery_fee' => 0,
                'discount' => 0,
                'total' => 0
            ]);
        }
    }
    
    private function mergeSessionCart($userCart, $sessionId)
    {
        try {
            $sessionCart = Cart::where('session_id', $sessionId)
                ->where('user_id', null)
                ->first();
                
            if ($sessionCart && $sessionCart->id !== $userCart->id) {
                // Mover items del carrito de sesión al carrito del usuario
                foreach ($sessionCart->items as $item) {
                    // Verificar si ya existe un item similar
                    $existingItem = $userCart->items()
                        ->where('product_id', $item->product_id)
                        ->where('size', $item->size)
                        ->where(function($query) use ($item) {
                            if ($item->customizations) {
                                $query->whereJsonContains('customizations', $item->customizations);
                            } else {
                                $query->whereNull('customizations');
                            }
                        })
                        ->first();
                    
                    if ($existingItem) {
                        // Si existe, sumar cantidades
                        $existingItem->quantity += $item->quantity;
                        $existingItem->calculatePrice()->save();
                    } else {
                        // Si no existe, duplicar el item
                        $newItem = $item->replicate();
                        $newItem->cart_id = $userCart->id;
                        $newItem->save();
                    }
                }
                
                // Eliminar el carrito de sesión
                $sessionCart->delete();
            }
            
            // Actualizar el carrito para quitar session_id
            $userCart->update(['session_id' => null]);
            
            // Recalcular totales
            $userCart->calculateTotals();
            
        } catch (\Exception $e) {
            // Si hay error en la fusión, continuar sin fusionar
            Log::error('Error merging session cart: ' . $e->getMessage());
        }
    }
}