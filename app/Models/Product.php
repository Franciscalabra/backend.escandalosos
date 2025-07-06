<?php

// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'image',
        'sizes', 'base_price', 'ingredients', 'customizable',
        'active', 'preparation_time'
    ];
    
    protected $casts = [
        'sizes' => 'array',
        'ingredients' => 'array',
        'customizable' => 'boolean',
        'active' => 'boolean',
        'base_price' => 'decimal:2',
        'preparation_time' => 'integer'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    public function getPriceForSize($size)
    {
        return $this->sizes[$size] ?? $this->base_price;
    }
}