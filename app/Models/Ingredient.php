<?php
// app/Models/Ingredient.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name', 'price', 'category', 'active'];
    
    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean'
    ];
    
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}