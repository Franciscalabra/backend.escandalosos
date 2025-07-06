<?php

// Guarda este archivo como fix_migrations.php en la raíz de tu proyecto Laravel
// y ejecuta: php fix_migrations.php

$migrationsPath = __DIR__ . '/database/migrations/';

// Array con el contenido correcto de cada migración
$migrations = [
    '[2025]_create_categories_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'categories\', function (Blueprint $table) {
            $table->id();
            $table->string(\'name\');
            $table->string(\'slug\')->unique();
            $table->string(\'image\')->nullable();
            $table->integer(\'order\')->default(0);
            $table->boolean(\'active\')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'categories\');
    }
};',

    '[2025]_create_products_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'products\', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\'category_id\')->constrained();
            $table->string(\'name\');
            $table->string(\'slug\')->unique();
            $table->text(\'description\')->nullable();
            $table->string(\'image\')->nullable();
            $table->json(\'sizes\')->nullable();
            $table->decimal(\'base_price\', 10, 2)->nullable();
            $table->json(\'ingredients\')->nullable();
            $table->boolean(\'customizable\')->default(false);
            $table->boolean(\'active\')->default(true);
            $table->integer(\'preparation_time\')->default(20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'products\');
    }
};',

    '[2025]_create_ingredients_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'ingredients\', function (Blueprint $table) {
            $table->id();
            $table->string(\'name\');
            $table->decimal(\'price\', 8, 2);
            $table->string(\'category\');
            $table->boolean(\'active\')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'ingredients\');
    }
};',

    '[2025]_create_carts_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'carts\', function (Blueprint $table) {
            $table->id();
            $table->string(\'session_id\');
            $table->foreignId(\'user_id\')->nullable()->constrained();
            $table->decimal(\'total\', 10, 2)->default(0);
            $table->timestamps();
            
            $table->index(\'session_id\');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'carts\');
    }
};',

    '[2025]_create_cart_items_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'cart_items\', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\'cart_id\')->constrained()->onDelete(\'cascade\');
            $table->foreignId(\'product_id\')->constrained()->onDelete(\'cascade\');
            $table->integer(\'quantity\');
            $table->decimal(\'price\', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'cart_items\');
    }
};',

    '[2025]_create_delivery_zones_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'delivery_zones\', function (Blueprint $table) {
            $table->id();
            $table->string(\'name\');
            $table->json(\'communes\');
            $table->decimal(\'delivery_fee\', 8, 2);
            $table->integer(\'estimated_time\')->default(30);
            $table->boolean(\'active\')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'delivery_zones\');
    }
};',

    '[2025]_create_orders_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'orders\', function (Blueprint $table) {
            $table->id();
            $table->string(\'order_number\')->unique();
            $table->foreignId(\'user_id\')->nullable()->constrained();
            $table->string(\'customer_name\');
            $table->string(\'customer_email\');
            $table->string(\'customer_phone\');
            $table->enum(\'delivery_type\', [\'delivery\', \'pickup\']);
            $table->text(\'delivery_address\')->nullable();
            $table->string(\'delivery_commune\')->nullable();
            $table->decimal(\'subtotal\', 10, 2);
            $table->decimal(\'delivery_fee\', 10, 2)->default(0);
            $table->decimal(\'discount\', 10, 2)->default(0);
            $table->decimal(\'total\', 10, 2);
            $table->enum(\'payment_method\', [\'cash\', \'transfer\', \'flow\']);
            $table->enum(\'payment_status\', [\'pending\', \'paid\', \'failed\', \'refunded\'])->default(\'pending\');
            $table->string(\'flow_order_id\')->nullable();
            $table->json(\'flow_response\')->nullable();
            $table->enum(\'status\', [\'pending\', \'confirmed\', \'preparing\', \'ready\', \'delivering\', \'completed\', \'cancelled\'])->default(\'pending\');
            $table->text(\'notes\')->nullable();
            $table->timestamp(\'estimated_delivery_at\')->nullable();
            $table->timestamp(\'delivered_at\')->nullable();
            $table->timestamps();
            
            $table->index(\'order_number\');
            $table->index(\'status\');
            $table->index(\'payment_status\');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'orders\');
    }
};',

    '[2025]_create_order_items_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'order_items\', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\'order_id\')->constrained()->onDelete(\'cascade\');
            $table->foreignId(\'product_id\')->constrained();
            $table->foreignId(\'combo_id\')->nullable()->constrained();
            $table->string(\'product_name\');
            $table->integer(\'quantity\');
            $table->string(\'size\')->nullable();
            $table->json(\'customizations\')->nullable();
            $table->decimal(\'unit_price\', 10, 2);
            $table->decimal(\'total_price\', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'order_items\');
    }
};',

    '[2025]_create_combos_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'combos\', function (Blueprint $table) {
            $table->id();
            $table->string(\'name\');
            $table->text(\'description\');
            $table->decimal(\'price\', 10, 2);
            $table->decimal(\'discount_percentage\', 5, 2)->default(0);
            $table->json(\'rules\');
            $table->string(\'image\')->nullable();
            $table->boolean(\'active\')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'combos\');
    }
};',

    '[2025]_create_combo_rules_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'combo_rules\', function (Blueprint $table) {
            $table->id();
            $table->string(\'name\');
            $table->json(\'conditions\');
            $table->json(\'benefits\');
            $table->integer(\'priority\')->default(0);
            $table->boolean(\'active\')->default(true);
            $table->date(\'valid_from\')->nullable();
            $table->date(\'valid_until\')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'combo_rules\');
    }
};'
];

// Corregir cada archivo
foreach ($migrations as $filename => $content) {
    $filepath = $migrationsPath . $filename;
    if (file_exists($filepath)) {
        file_put_contents($filepath, $content);
        echo "✅ Corregido: $filename\n";
    } else {
        echo "❌ No encontrado: $filename\n";
    }
}

echo "\n✨ ¡Proceso completado! Ahora ejecuta: php artisan migrate:fresh\n";