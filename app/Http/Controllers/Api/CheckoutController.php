<?php
// app/Http/Controllers/Api/CheckoutController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use App\Models\DeliveryZone;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // <-- IMPORTANTE: Añadido para el logging

class CheckoutController extends Controller
{
    protected $flowService;
    
    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address' => 'required_if:delivery_type,delivery',
            'delivery_commune' => 'required_if:delivery_type,delivery',
            'payment_method' => 'required|in:cash,transfer,flow',
            'notes' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            $cart = $this->getCurrentCart($request);
            
            // --- INICIO DE DEBUG ---
            Log::info('--- INICIANDO PROCESO DE CHECKOUT ---');
            Log::info('Carrito ID: ' . $cart->id);
            Log::info('Valores del carrito ANTES del recálculo: Subtotal=' . $cart->subtotal . ', Total=' . $cart->total);
            foreach ($cart->items as $item) {
                Log::info('  -> Item: ' . $item->product->name . ' | Cant: ' . $item->quantity . ' | Precio Unitario: ' . $item->price . ' | Subtotal Item: ' . $item->subtotal);
            }

            // SOLUCIÓN: Forzamos un recálculo de los totales del carrito ANTES de usar sus datos.
            $cart->calculateTotals();

            Log::info('Valores del carrito DESPUÉS del recálculo: Subtotal=' . $cart->subtotal . ', Total=' . $cart->total);
            Log::info('------------------------------------------');
            // --- FIN DE DEBUG ---

            if ($cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El carrito está vacío'
                ], 422);
            }
            
            // Calculate delivery fee
            $deliveryFee = 0;
            $estimatedTime = 30;
            
            if ($request->delivery_type === 'delivery') {
                $zone = DeliveryZone::findByCommune($request->delivery_commune);
                
                if (!$zone) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No realizamos entregas en esta comuna'
                    ], 422);
                }
                
                $deliveryFee = $zone->delivery_fee;
                $estimatedTime = $zone->estimated_time;
            }
            
            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'delivery_type' => $request->delivery_type,
                'delivery_address' => $request->delivery_address,
                'delivery_commune' => $request->delivery_commune,
                'subtotal' => $cart->subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => $cart->discount,
                'total' => $cart->total + $deliveryFee,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'estimated_delivery_at' => now()->addMinutes($estimatedTime)
            ]);
            
            // Copy cart items to order
            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'quantity' => $cartItem->quantity,
                    'size' => $cartItem->size,
                    'customizations' => $cartItem->customizations,
                    'unit_price' => $cartItem->price,
                    'total_price' => $cartItem->subtotal
                ]);
            }
            
            // Process payment
            if ($request->payment_method === 'flow') {
                $flowResponse = $this->flowService->createPayment($order);
                
                if (!$flowResponse['success']) {
                    throw new \Exception('Error al procesar el pago con Flow: ' . ($flowResponse['message'] ?? ''));
                }
                
                $order->update([
                    'flow_order_id' => $flowResponse['flowOrder'],
                    'flow_response' => $flowResponse
                ]);
                
                $cart->delete();
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Orden creada exitosamente',
                    'data' => [
                        'order' => $order,
                        'payment_url' => $flowResponse['url']
                    ]
                ]);
            } else {
                // For cash and transfer, order is confirmed immediately
                $order->update([
                    'status' => 'confirmed',
                    'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'paid'
                ]);
                
                $cart->delete();
                
                DB::commit();
                
                // Send notifications here
                
                return response()->json([
                    'success' => true,
                    'message' => 'Orden creada exitosamente',
                    'data' => [
                        'order' => $order->load('items')
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error al procesar la orden: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());

            return response()->json([
                'success' => false,
                'message' => 'Error interno al procesar la orden. Por favor, intente de nuevo.'
            ], 500);
        }
    }
    
    public function confirmFlow(Request $request)
    {
        $token = $request->input('token');
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 400);
        }
        
        $result = $this->flowService->getPaymentStatus($token);
        
        if (isset($result['status']) && $result['status'] === 2) { // Pago exitoso
            $order = Order::where('flow_order_id', $result['flowOrder'])->first();
            
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'flow_response' => $result
                ]);
                
                // Send notifications
                
                return response()->json([
                    'success' => true,
                    'message' => 'Pago confirmado exitosamente',
                    'data' => $order
                ]);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Error al confirmar el pago o el pago fue rechazado.'
        ], 400);
    }
    
    private function getCurrentCart(Request $request)
    {
        $sessionId = $request->session()->getId();
        $userId = auth()->id();

        $cartQuery = Cart::query();

        if ($userId) {
            $cartQuery->where('user_id', $userId);
        } else {
            $cartQuery->where('session_id', $sessionId);
        }

        $cart = $cartQuery->with('items.product')->first();

        if (!$cart) {
            $cart = Cart::create([
                'session_id' => $userId ? null : $sessionId,
                'user_id' => $userId,
            ]);
            $cart->load('items.product');
        }
        
        return $cart;
    }
}