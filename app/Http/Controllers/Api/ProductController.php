<?php

// app/Http/Controllers/Api/ProductController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->active();
        
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $products = $query->orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
    
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with('category')->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
    
    public function categories()
    {
        $categories = Category::active()->orderBy('order')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}