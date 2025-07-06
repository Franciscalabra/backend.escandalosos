<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\DeliveryZone;
use App\Models\ComboRule;
use App\Models\Ingredient;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Pizzas', 'slug' => 'pizzas', 'order' => 1],
            ['name' => 'Bebidas', 'slug' => 'bebidas', 'order' => 2],
            ['name' => 'Postres', 'slug' => 'postres', 'order' => 3],
            ['name' => 'Complementos', 'slug' => 'complementos', 'order' => 4],
        ];
        
        foreach ($categories as $cat) {
            Category::create($cat);
        }
        
        // Sample Products
        $pizzaCategory = Category::where('slug', 'pizzas')->first();
        
        if ($pizzaCategory) {
            $pizzas = [
                [
                    'name' => 'Pizza Margherita',
                    'slug' => 'pizza-margherita',
                    'description' => 'Salsa de tomate, mozzarella y albahaca fresca',
                    'sizes' => ['personal' => 7990, 'mediana' => 12990, 'familiar' => 16990],
                    'customizable' => true,
                    'preparation_time' => 20,
                    'active' => true
                ],
                [
                    'name' => 'Pizza Pepperoni',
                    'slug' => 'pizza-pepperoni',
                    'description' => 'Salsa de tomate, mozzarella y pepperoni',
                    'sizes' => ['personal' => 8990, 'mediana' => 13990, 'familiar' => 17990],
                    'customizable' => true,
                    'preparation_time' => 20,
                    'active' => true
                ],
                [
                    'name' => 'Pizza Hawaiana',
                    'slug' => 'pizza-hawaiana',
                    'description' => 'Salsa de tomate, mozzarella, jamón y piña',
                    'sizes' => ['personal' => 8990, 'mediana' => 13990, 'familiar' => 17990],
                    'customizable' => true,
                    'preparation_time' => 20,
                    'active' => true
                ],
            ];
            
            foreach ($pizzas as $pizza) {
                $pizzaCategory->products()->create($pizza);
            }
        }
        
        // Ingredients
        $ingredients = [
            ['name' => 'Extra Queso', 'price' => 1500, 'category' => 'cheese'],
            ['name' => 'Pepperoni', 'price' => 2000, 'category' => 'meat'],
            ['name' => 'Jamón', 'price' => 1800, 'category' => 'meat'],
            ['name' => 'Champiñones', 'price' => 1500, 'category' => 'vegetable'],
            ['name' => 'Pimentón', 'price' => 1200, 'category' => 'vegetable'],
            ['name' => 'Cebolla', 'price' => 1000, 'category' => 'vegetable'],
        ];
        
        foreach ($ingredients as $ing) {
            Ingredient::create($ing);
        }
        
        // Delivery Zones
        $zones = [
            [
                'name' => 'Zona Centro',
                'communes' => ['Santiago Centro', 'Providencia', 'Ñuñoa'],
                'delivery_fee' => 2000,
                'estimated_time' => 30,
                'active' => true
            ],
            [
                'name' => 'Zona Oriente',
                'communes' => ['Las Condes', 'Vitacura', 'La Reina', 'Lo Barnechea'],
                'delivery_fee' => 2500,
                'estimated_time' => 40,
                'active' => true
            ],
            [
                'name' => 'Zona Sur',
                'communes' => ['San Miguel', 'La Cisterna', 'San Joaquín'],
                'delivery_fee' => 3000,
                'estimated_time' => 45,
                'active' => true
            ],
        ];
        
        foreach ($zones as $zone) {
            DeliveryZone::create($zone);
        }
        
        // Combo Rules
        ComboRule::create([
            'name' => '2x1 en Pizzas Medianas',
            'conditions' => ['min_pizzas' => 2, 'pizza_size' => 'mediana'],
            'benefits' => ['discount_percentage' => 50],
            'priority' => 10,
            'active' => true
        ]);
        
        ComboRule::create([
            'name' => '15% descuento en pedidos sobre $25.000',
            'conditions' => ['min_total' => 25000],
            'benefits' => ['discount_percentage' => 15],
            'priority' => 5,
            'active' => true
        ]);
    }
}