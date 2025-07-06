<?php
// app/Models/Combo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'discount_percentage',
        'rules', 'image', 'active'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'rules' => 'array',
        'active' => 'boolean'
    ];
    
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}