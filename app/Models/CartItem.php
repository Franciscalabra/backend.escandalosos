<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id', 
        'product_id', 
        'quantity',
        'size', 
        'customizations', 
        'price', // precio unitario
        'subtotal' // precio total del item (price * quantity)
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'customizations' => 'array',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function calculatePrice()
    {
        // Obtener precio base del producto
        $basePrice = $this->size && $this->product->sizes
            ? $this->product->getPriceForSize($this->size)
            : $this->product->base_price;
            
        // Agregar costo de ingredientes personalizados si existen
        if ($this->customizations && isset($this->customizations['ingredients'])) {
            $ingredientsCost = Ingredient::whereIn('id', $this->customizations['ingredients'])
                ->sum('price');
            $basePrice += $ingredientsCost;
        }
        
        // Establecer precio unitario y subtotal
        $this->price = $basePrice;
        $this->subtotal = $basePrice * $this->quantity;
        
        return $this;
    }
    
    // Método para actualizar precios después de guardar
    protected static function boot()
    {
        parent::boot();
        
        // Después de crear o actualizar un item, recalcular totales del carrito
        static::saved(function ($item) {
            if ($item->cart) {
                $item->cart->calculateTotals();
            }
        });
        
        // Después de eliminar un item, recalcular totales del carrito
        static::deleted(function ($item) {
            if ($item->cart) {
                $item->cart->calculateTotals();
            }
        });
    }
}