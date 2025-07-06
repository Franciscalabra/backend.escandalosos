<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('order')->get();
        return view('admin.categories.index', compact('categories'));
    }
    
    public function create()
    {
        return view('admin.categories.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);
        
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        
        Category::create($data);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría creada exitosamente');
    }
    
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }
    
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);
        
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        
        $category->update($data);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría actualizada exitosamente');
    }
    
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una categoría con productos');
        }
        
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría eliminada exitosamente');
    }
}
