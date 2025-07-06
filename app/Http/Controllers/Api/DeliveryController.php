<?php
// app/Http/Controllers/Api/DeliveryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function zones()
    {
        $zones = DeliveryZone::active()->get();
        
        return response()->json([
            'success' => true,
            'data' => $zones
        ]);
    }
    
    public function calculateFee(Request $request)
    {
        $request->validate([
            'commune' => 'required|string'
        ]);
        
        $zone = DeliveryZone::findByCommune($request->commune);
        
        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'No realizamos entregas en esta comuna'
            ], 422);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'zone' => $zone->name,
                'delivery_fee' => $zone->delivery_fee,
                'estimated_time' => $zone->estimated_time
            ]
        ]);
    }
}