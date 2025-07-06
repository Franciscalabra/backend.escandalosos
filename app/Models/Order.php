<?php
// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log; // <-- IMPORTANTE: Añadido para el logging

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'customer_name', 'customer_email',
        'customer_phone', 'delivery_type', 'delivery_address', 'delivery_commune',
        'subtotal', 'delivery_fee', 'discount', 'total',
        'payment_method', 'payment_status', 'flow_order_id', 'flow_response',
        'status', 'notes', 'estimated_delivery_at', 'delivered_at'
    ];
    
    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'flow_response' => 'array',
        'estimated_delivery_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        // Este evento se dispara JUSTO ANTES de que la orden se guarde en la BD
        static::creating(function ($order) {
            // --- INICIO DE DEBUG ---
            // Aquí registramos los atributos exactos que Eloquent va a insertar.
            // Si el 'total' aquí es correcto, el problema no está en el código PHP.
            Log::info('--- Evento "creating" del modelo Order ---');
            Log::info('Atributos del pedido que se van a guardar: ' . json_encode($order->getAttributes(), JSON_PRETTY_PRINT));
            
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
            }

            Log::info('Atributos FINALES antes de la inserción en BD: ' . json_encode($order->getAttributes(), JSON_PRETTY_PRINT));
            Log::info('------------------------------------------');
            // --- FIN DE DEBUG ---
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function updateStatus($status)
    {
        $this->status = $status;
        
        if ($status === 'delivered') {
            $this->delivered_at = now();
        }
        
        $this->save();
        
        // Aquí puedes agregar notificaciones
        
        return $this;
    }
}