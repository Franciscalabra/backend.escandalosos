<?php

// app/Models/Cart.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'session_id', 
        'user_id', 
        'subtotal', 
        'delivery_fee',
        'discount', 
        'total'
    ];
    
    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    
    public function calculateTotals()
    {
        // Calcular subtotal sumando todos los items
        $this->subtotal = $this->items->sum('subtotal');
        
        // Calcular total
        $this->total = $this->subtotal + $this->delivery_fee - $this->discount;
        
        // Guardar cambios
        $this->save();
        
        return $this;
    }
    
    public function applyComboRules()
    {
        $rules = ComboRule::active()->orderBy('priority', 'desc')->get();
        
        foreach ($rules as $rule) {
            if ($this->meetsConditions($rule->conditions)) {
                $this->applyBenefits($rule->benefits);
                break; // Apply only the highest priority rule
            }
        }
        
        return $this;
    }
    
    private function meetsConditions($conditions)
    {
        // Verificar mínimo de pizzas
        if (isset($conditions['min_pizzas'])) {
            $pizzaCount = $this->items()
                ->whereHas('product', function($q) {
                    $q->whereHas('category', function($q2) {
                        $q2->where('slug', 'pizzas');
                    });
                })
                ->sum('quantity');
                
            if ($pizzaCount < $conditions['min_pizzas']) {
                return false;
            }
        }
        
        // Verificar monto mínimo
        if (isset($conditions['min_total']) && $this->subtotal < $conditions['min_total']) {
            return false;
        }
        
        return true;
    }
    
    private function applyBenefits($benefits)
    {
        if (isset($benefits['discount_percentage'])) {
            $this->discount = $this->subtotal * ($benefits['discount_percentage'] / 100);
        }
    }
}
