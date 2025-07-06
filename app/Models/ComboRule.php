<?php
// app/Models/ComboRule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComboRule extends Model
{
    protected $fillable = [
        'name', 'conditions', 'benefits', 'priority',
        'active', 'valid_from', 'valid_until'
    ];
    
    protected $casts = [
        'conditions' => 'array',
        'benefits' => 'array',
        'priority' => 'integer',
        'active' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date'
    ];
    
    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where(function($q) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }
}