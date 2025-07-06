<?php

// app/Http/Controllers/Api/ComboController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\ComboRule;
use App\Models\Cart;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function index()
    {
        $combos = Combo::active()->get();
        
        return response()->json([
            'success' => true,
            'data' => $combos
        ]);
    }
    
    public function rules()
    {
        $rules = ComboRule::active()->orderBy('priority', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $rules
        ]);
    }
    
    public function checkEligibility(Request $request)
    {
        $cart = $this->getCurrentCart($request);
        $eligibleCombos = [];
        
        $rules = ComboRule::active()->orderBy('priority', 'desc')->get();
        
        foreach ($rules as $rule) {
            if ($this->meetsConditions($cart, $rule->conditions)) {
                $eligibleCombos[] = [
                    'rule' => $rule,
                    'savings' => $this->calculateSavings($cart, $rule->benefits)
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $eligibleCombos
        ]);
    }
    
    private function meetsConditions($cart, $conditions)
    {
        // Implementar lógica similar a la del modelo Cart
        return true;
    }
    
    private function calculateSavings($cart, $benefits)
    {
        if (isset($benefits['discount_percentage'])) {
            return $cart->subtotal * ($benefits['discount_percentage'] / 100);
        }
        
        return 0;
    }
    
    private function getCurrentCart(Request $request)
    {
        // Similar al CartController
        return Cart::where('session_id', $request->session()->getId())
            ->orWhere('user_id', auth()->id())
            ->firstOrFail();
    }
}
