<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::orderBy('category')->orderBy('name')->get();
        return view('admin.ingredients.index', compact('ingredients'));
    }
    
    public function create()
    {
        $categories = ['cheese' => 'Quesos', 'meat' => 'Carnes', 'vegetable' => 'Vegetales', 'sauce' => 'Salsas'];
        return view('admin.ingredients.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:cheese,meat,vegetable,sauce'
        ]);
        
        Ingredient::create($request->all());
        
        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingrediente creado exitosamente');
    }
    
    public function edit(Ingredient $ingredient)
    {
        $categories = ['cheese' => 'Quesos', 'meat' => 'Carnes', 'vegetable' => 'Vegetales', 'sauce' => 'Salsas'];
        return view('admin.ingredients.edit', compact('ingredient', 'categories'));
    }
    
    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:cheese,meat,vegetable,sauce'
        ]);
        
        $ingredient->update($request->all());
        
        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingrediente actualizado exitosamente');
    }
    
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        
        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingrediente eliminado exitosamente');
    }
    
    public function toggleStatus(Ingredient $ingredient)
    {
        $ingredient->update(['active' => !$ingredient->active]);
        
        return back()->with('success', 'Estado del ingrediente actualizado');
    }
}