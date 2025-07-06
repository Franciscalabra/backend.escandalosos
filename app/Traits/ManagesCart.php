<?php

namespace App\Traits;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ManagesCart
{
    /**
     * Obtiene el carrito actual del usuario o de la sesión, o crea uno nuevo.
     */
    private function getCurrentCart(Request $request): Cart
    {
        $sessionId = $request->session()->getId();
        $userId = auth()->id();

        try {
            $cartQuery = Cart::query();

            if ($userId) {
                $cartQuery->where('user_id', $userId);
            } else {
                $cartQuery->where('session_id', $sessionId);
            }

            $cart = $cartQuery->with('items.product')->first();

            if (!$cart) {
                $cart = Cart::create([
                    'session_id' => $userId ? null : $sessionId,
                    'user_id' => $userId,
                ]);
            }
            
            return $cart;

        } catch (\Exception $e) {
            Log::error('Error getting current cart: ' . $e->getMessage());
            // Como último recurso, crear un carrito vacío para no romper la aplicación.
            return new Cart([
                'session_id' => $userId ? null : $sessionId,
                'user_id' => $userId,
            ]);
        }
    }
}