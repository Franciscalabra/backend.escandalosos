#!/bin/bash
set -e # Detiene el script si un comando falla

echo "🚀 Iniciando corrección automática de Laravel..."

# --- Problema 1: Controlador inexistente ---
echo "1️⃣  Corrigiendo referencia al controlador AdminAuthController..."
# Comprueba si el archivo de rutas API existe antes de modificarlo
if [ -f "routes/api.php" ]; then
    # Comenta la línea 'use' y la definición de la ruta para evitar el error de clase no encontrada.
    # Usamos -i.bak para compatibilidad con macOS
    sed -i.bak "s|use App\\\Http\\\Controllers\\\Auth\\\AdminAuthController;|// &|g" routes/api.php
    sed -i.bak "s|Route::post('/admin/login', \[AdminAuthController::class, 'login'\]);|// &|g" routes/api.php
    rm routes/api.php.bak # Elimina el backup creado por sed
    echo "✓ Ruta y 'use' statement para AdminAuthController comentados en routes/api.php."
else
    echo "⚠️  Archivo routes/api.php no encontrado. Omitiendo."
fi

# --- Problema 2: Rate Limiter no definido ---
echo "2️⃣  Definiendo el Rate Limiter para 'api'..."
# Comprueba si el Service Provider existe
if [ -f "app/Providers/RouteServiceProvider.php" ]; then
    # Verifica si el rate limiter 'api' ya existe para no duplicarlo
    if ! grep -q "RateLimiter::for('api'" app/Providers/RouteServiceProvider.php; then
        # Inserta el código del rate limiter después de la apertura del método configureRateLimiting
        # awk es más robusto para este tipo de inserciones
        awk '
        /protected function configureRateLimiting\(\): void/ {
            print; 
            getline; # Lee la siguiente línea, que debería ser '{'
            print;   # Imprime '{'
            print "        RateLimiter::for(\"api\", function (Request \$request) {";
            print "            return Limit::perMinute(60)->by(\$request->user()?->id ?: \$request->ip());";
            print "        });";
            next;
        }
        {print}
        ' app/Providers/RouteServiceProvider.php > tmp_provider.php && mv tmp_provider.php app/Providers/RouteServiceProvider.php
        echo "✓ Rate limiter 'api' añadido a app/Providers/RouteServiceProvider.php."
    else
        echo "✓ El Rate Limiter 'api' ya parece estar definido."
    fi
else
    echo "⚠️  Archivo app/Providers/RouteServiceProvider.php no encontrado. Omitiendo."
fi

# --- Limpieza Final ---
echo "3️⃣  Limpiando las cachés de Laravel..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear

echo "✅ ¡Corrección completada! Por favor, reinicia tu servidor y prueba la aplicación de nuevo."