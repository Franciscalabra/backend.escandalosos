<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->paginate(10);
            
        return view('admin.products.index', compact('products'));
    }
    
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        // DEBUG: Log todos los datos recibidos
        Log::info('Datos recibidos en store:', $request->all());
        
        try {
            // Determinar si se están usando tamaños
            $usingSizes = $request->has('sizes') && is_array($request->sizes) && count(array_filter($request->sizes, function($size) {
                return !empty($size['name']) && !empty($size['price']);
            })) > 0;
            
            // Validación con lógica condicional
            $rules = [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'preparation_time' => 'required|integer|min:1',
                'image' => 'nullable|image|max:2048'
            ];
            
            if (!$usingSizes) {
                $rules['base_price'] = 'required|numeric|min:0';
            } else {
                $rules['sizes'] = 'required|array|min:1';
                $rules['sizes.*.name'] = 'required|string';
                $rules['sizes.*.price'] = 'required|numeric|min:0';
            }
            
            $messages = [
                'category_id.required' => 'Debe seleccionar una categoría',
                'category_id.exists' => 'La categoría seleccionada no existe',
                'name.required' => 'El nombre del producto es obligatorio',
                'preparation_time.required' => 'El tiempo de preparación es obligatorio',
                'base_price.required' => 'El precio es obligatorio cuando no se usan tamaños'
            ];
            
            $validated = $request->validate($rules, $messages);
            
            // Preparar datos
            $data = [
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'preparation_time' => $request->preparation_time,
                'customizable' => $request->has('customizable') ? true : false,
                'active' => true
            ];
            
            // DEBUG: Log datos procesados
            Log::info('Datos base procesados:', $data);
            
            // Procesar precio o tamaños
            if ($usingSizes) {
                $sizes = [];
                foreach ($request->sizes as $size) {
                    if (!empty($size['name']) && !empty($size['price'])) {
                        $sizes[$size['name']] = (float) $size['price'];
                    }
                }
                $data['sizes'] = $sizes;
                $data['base_price'] = null; // Importante: establecer como null cuando hay tamaños
                Log::info('Tamaños procesados:', $sizes);
            } else {
                $data['base_price'] = (float) $request->base_price;
                $data['sizes'] = null;
                Log::info('Precio base:', ['base_price' => $data['base_price']]);
            }
            
            // Procesar imagen
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
                Log::info('Imagen guardada en:', ['path' => $data['image']]);
            }
            
            // DEBUG: Log final antes de crear
            Log::info('Datos finales antes de crear:', $data);
            
            $product = Product::create($data);
            
            Log::info('Producto creado exitosamente:', ['id' => $product->id]);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Producto creado exitosamente');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', [
                'errors' => $e->errors(),
                'input' => $request->except(['image'])
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear producto:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()]);
        }
    }
    
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }
    
    public function update(Request $request, Product $product)
    {
        try {
            // Determinar si se están usando tamaños
            $usingSizes = $request->has('sizes') && is_array($request->sizes) && count(array_filter($request->sizes, function($size) {
                return !empty($size['name']) && !empty($size['price']);
            })) > 0;
            
            // Validación con lógica condicional
            $rules = [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'preparation_time' => 'required|integer|min:1',
                'image' => 'nullable|image|max:2048'
            ];
            
            if (!$usingSizes) {
                $rules['base_price'] = 'required|numeric|min:0';
            } else {
                $rules['sizes'] = 'required|array|min:1';
                $rules['sizes.*.name'] = 'required|string';
                $rules['sizes.*.price'] = 'required|numeric|min:0';
            }
            
            $request->validate($rules);
            
            $data = [
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'preparation_time' => $request->preparation_time,
                'customizable' => $request->has('customizable') ? true : false
            ];
            
            // Procesar precio o tamaños
            if ($usingSizes) {
                $sizes = [];
                foreach ($request->sizes as $size) {
                    if (!empty($size['name']) && !empty($size['price'])) {
                        $sizes[$size['name']] = (float) $size['price'];
                    }
                }
                $data['sizes'] = $sizes;
                $data['base_price'] = null;
            } else {
                $data['base_price'] = (float) $request->base_price;
                $data['sizes'] = null;
            }
            
            // Procesar imagen
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }
            
            $product->update($data);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Producto actualizado exitosamente');
                
        } catch (\Exception $e) {
            Log::error('Error al actualizar producto:', [
                'message' => $e->getMessage(),
                'product_id' => $product->id
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()]);
        }
    }
    
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
    
    public function toggleStatus(Product $product)
    {
        $product->update(['active' => !$product->active]);
        
        return back()->with('success', 'Estado del producto actualizado');
    }
}