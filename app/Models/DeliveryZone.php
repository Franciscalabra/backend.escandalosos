<?php
// app/Models/DeliveryZone.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = [
        'name', 'communes', 'delivery_fee', 'estimated_time', 'active'
    ];
    
    protected $casts = [
        'communes' => 'array',
        'delivery_fee' => 'decimal:2',
        'estimated_time' => 'integer',
        'active' => 'boolean'
    ];
    
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    public static function findByCommune($commune)
    {
        return static::active()
            ->whereJsonContains('communes', $commune)
            ->first();
    }
}