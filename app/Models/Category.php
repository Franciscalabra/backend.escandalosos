<?php
// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'order', 'active'];
    
    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}