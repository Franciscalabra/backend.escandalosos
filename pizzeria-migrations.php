<?php

// 1. create_categories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}

// 2. create_products_table.php
class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('sizes')->nullable(); // {"small": 8990, "medium": 12990, "large": 16990}
            $table->decimal('base_price', 10, 2)->nullable();
            $table->json('ingredients')->nullable(); // Para pizzas personalizables
            $table->boolean('customizable')->default(false);
            $table->boolean('active')->default(true);
            $table->integer('preparation_time')->default(20); // minutos
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}

// 3. create_ingredients_table.php
class CreateIngredientsTable extends Migration
{
    public function up()
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->string('category'); // 'cheese', 'meat', 'vegetable', 'sauce'
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ingredients');
    }
}

// 4. create_combos_table.php
class CreateCombosTable extends Migration
{
    public function up()
    {
        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->json('rules'); // {"pizzas": 2, "drinks": 2, "min_price": 25000}
            $table->string('image')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('combos');
    }
}

// 5. create_carts_table.php
class CreateCartsTable extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
            
            $table->index('session_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
}

// 6. create_cart_items_table.php
class CreateCartItemsTable extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('combo_id')->nullable()->constrained();
            $table->integer('quantity');
            $table->string('size')->nullable();
            $table->json('customizations')->nullable(); // {"extra_cheese": true, "ingredients": [1, 2, 3]}
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}

// 7. create_delivery_zones_table.php
class CreateDeliveryZonesTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('communes'); // ["Santiago Centro", "Providencia", "Las Condes"]
            $table->decimal('delivery_fee', 8, 2);
            $table->integer('estimated_time')->default(30); // minutos
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_zones');
    }
}

// 8. create_orders_table.php
class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->enum('delivery_type', ['delivery', 'pickup']);
            $table->text('delivery_address')->nullable();
            $table->string('delivery_commune')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'flow']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('flow_order_id')->nullable();
            $table->json('flow_response')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'delivering', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

// 9. create_order_items_table.php
class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('combo_id')->nullable()->constrained();
            $table->string('product_name');
            $table->integer('quantity');
            $table->string('size')->nullable();
            $table->json('customizations')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}

// 10. create_combo_rules_table.php
class CreateComboRulesTable extends Migration
{
    public function up()
    {
        Schema::create('combo_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('conditions'); // {"min_pizzas": 2, "min_total": 20000}
            $table->json('benefits'); // {"discount_percentage": 15, "free_drink": true}
            $table->integer('priority')->default(0);
            $table->boolean('active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('combo_rules');
    }
}