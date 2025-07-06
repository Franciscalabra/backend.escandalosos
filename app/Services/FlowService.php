<?php

// app/Services/FlowService.php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlowService
{
    protected $apiKey;
    protected $secretKey;
    protected $apiUrl;
    protected $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.flow.api_key');
        $this->secretKey = config('services.flow.secret_key');
        $this->apiUrl = config('services.flow.api_url', 'https://www.flow.cl/api');
        $this->baseUrl = config('app.url');
    }
    
    public function createPayment(Order $order)
    {
        try {
            $params = [
                'apiKey' => $this->apiKey,
                'commerceOrder' => $order->order_number,
                'subject' => 'Pedido ' . $order->order_number,
                'currency' => 'CLP',
                'amount' => (int) $order->total,
                'email' => $order->customer_email,
                'paymentMethod' => 9, // Todos los medios de pago
                'urlConfirmation' => $this->baseUrl . '/api/checkout/flow/confirm',
                'urlReturn' => $this->baseUrl . '/order/success/' . $order->order_number,
                'optional' => json_encode([
                    'customer_phone' => $order->customer_phone,
                    'order_id' => $order->id
                ])
            ];
            
            // Ordenar parámetros alfabéticamente
            ksort($params);
            
            // Crear string para firma
            $toSign = '';
            foreach ($params as $key => $value) {
                $toSign .= $key . $value;
            }
            
            // Generar firma
            $signature = hash_hmac('sha256', $toSign, $this->secretKey);
            $params['s'] = $signature;
            
            // Realizar petición
            $response = Http::asForm()->post($this->apiUrl . '/payment/create', $params);
            
            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'url' => $data['url'] . '?token=' . $data['token'],
                    'token' => $data['token'],
                    'flowOrder' => $data['flowOrder']
                ];
            } else {
                Log::error('Flow payment creation failed', [
                    'response' => $response->body(),
                    'order' => $order->order_number
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Error al crear el pago'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Flow payment exception', [
                'error' => $e->getMessage(),
                'order' => $order->order_number
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ];
        }
    }
    
    public function getPaymentStatus($token)
    {
        try {
            $params = [
                'apiKey' => $this->apiKey,
                'token' => $token
            ];
            
            ksort($params);
            
            $toSign = '';
            foreach ($params as $key => $value) {
                $toSign .= $key . $value;
            }
            
            $signature = hash_hmac('sha256', $toSign, $this->secretKey);
            $params['s'] = $signature;
            
            $response = Http::get($this->apiUrl . '/payment/getStatus', $params);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Flow status check exception', [
                'error' => $e->getMessage(),
                'token' => $token
            ]);
            
            return null;
        }
    }
}