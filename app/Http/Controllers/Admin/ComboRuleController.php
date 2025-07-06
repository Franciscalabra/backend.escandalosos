<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComboRule;
use Illuminate\Http\Request;

class ComboRuleController extends Controller
{
    public function index()
    {
        $rules = ComboRule::orderBy('priority', 'desc')->get();
        return view('admin.combo-rules.index', compact('rules'));
    }
    
    public function create()
    {
        return view('admin.combo-rules.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_pizzas' => 'nullable|integer|min:1',
            'min_total' => 'nullable|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'priority' => 'required|integer|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from'
        ]);
        
        $conditions = [];
        if ($request->min_pizzas) {
            $conditions['min_pizzas'] = (int) $request->min_pizzas;
        }
        if ($request->min_total) {
            $conditions['min_total'] = (float) $request->min_total;
        }
        
        $benefits = [
            'discount_percentage' => (float) $request->discount_percentage
        ];
        
        ComboRule::create([
            'name' => $request->name,
            'conditions' => $conditions,
            'benefits' => $benefits,
            'priority' => $request->priority,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'active' => true
        ]);
        
        return redirect()->route('admin.combo-rules.index')
            ->with('success', 'Regla de combo creada exitosamente');
    }
    
    public function edit(ComboRule $comboRule)
    {
        return view('admin.combo-rules.edit', compact('comboRule'));
    }
    
    public function update(Request $request, ComboRule $comboRule)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_pizzas' => 'nullable|integer|min:1',
            'min_total' => 'nullable|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'priority' => 'required|integer|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from'
        ]);
        
        $conditions = [];
        if ($request->min_pizzas) {
            $conditions['min_pizzas'] = (int) $request->min_pizzas;
        }
        if ($request->min_total) {
            $conditions['min_total'] = (float) $request->min_total;
        }
        
        $benefits = [
            'discount_percentage' => (float) $request->discount_percentage
        ];
        
        $comboRule->update([
            'name' => $request->name,
            'conditions' => $conditions,
            'benefits' => $benefits,
            'priority' => $request->priority,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until
        ]);
        
        return redirect()->route('admin.combo-rules.index')
            ->with('success', 'Regla de combo actualizada exitosamente');
    }
    
    public function destroy(ComboRule $comboRule)
    {
        $comboRule->delete();
        
        return redirect()->route('admin.combo-rules.index')
            ->with('success', 'Regla de combo eliminada exitosamente');
    }
    
    public function toggleStatus(ComboRule $comboRule)
    {
        $comboRule->update(['active' => !$comboRule->active]);
        
        return back()->with('success', 'Estado de la regla actualizado');
    }
}