<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::orderBy('name')->get();
        return view('admin.delivery-zones.index', compact('zones'));
    }
    
    public function create()
    {
        return view('admin.delivery-zones.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'communes' => 'required|string',
            'delivery_fee' => 'required|numeric|min:0',
            'estimated_time' => 'required|integer|min:1'
        ]);
        
        $data = $request->all();
        // Convertir string de comunas a array
        $data['communes'] = array_map('trim', explode(',', $request->communes));
        
        DeliveryZone::create($data);
        
        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Zona de delivery creada exitosamente');
    }
    
    public function edit(DeliveryZone $deliveryZone)
    {
        // Convertir array de comunas a string para el formulario
        $deliveryZone->communes_string = implode(', ', $deliveryZone->communes);
        return view('admin.delivery-zones.edit', compact('deliveryZone'));
    }
    
    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'communes' => 'required|string',
            'delivery_fee' => 'required|numeric|min:0',
            'estimated_time' => 'required|integer|min:1'
        ]);
        
        $data = $request->all();
        $data['communes'] = array_map('trim', explode(',', $request->communes));
        
        $deliveryZone->update($data);
        
        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Zona de delivery actualizada exitosamente');
    }
    
    public function destroy(DeliveryZone $deliveryZone)
    {
        $deliveryZone->delete();
        
        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Zona de delivery eliminada exitosamente');
    }
    
    public function toggleStatus(DeliveryZone $deliveryZone)
    {
        $deliveryZone->update(['active' => !$deliveryZone->active]);
        
        return back()->with('success', 'Estado de la zona actualizado');
    }
}