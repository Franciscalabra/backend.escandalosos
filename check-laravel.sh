#!/bin/bash

# Script de diagnóstico para Laravel
# Guárdalo como check-laravel.sh y ejecuta: bash check-laravel.sh

echo "🔍 DIAGNÓSTICO COMPLETO DE LARAVEL"
echo "=================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Verificar archivo .env
echo "1️⃣  Verificando archivo .env..."
if [ -f .env ]; then
    echo -e "${GREEN}✓ Archivo .env existe${NC}"
    
    # Verificar APP_KEY
    if grep -q "APP_KEY=base64:" .env; then
        echo -e "${GREEN}✓ APP_KEY está configurada${NC}"
    else
        echo -e "${RED}✗ APP_KEY no está configurada${NC}"
        echo "  Ejecuta: php artisan key:generate"
    fi
    
    # Verificar configuración de DB
    echo ""
    echo "Configuración de base de datos:"
    grep "DB_" .env | grep -v "DB_PASSWORD"
else
    echo -e "${RED}✗ Archivo .env no existe${NC}"
    echo "  Ejecuta: cp .env.example .env"
fi

echo ""
echo "=================================="

# 2. Verificar logs de errores
echo "2️⃣  Últimos errores en logs..."
if [ -f storage/logs/laravel.log ]; then
    echo "Últimos 5 errores:"
    grep -i "ERROR" storage/logs/laravel.log | tail -5
else
    echo -e "${YELLOW}⚠ No hay archivo de log${NC}"
fi

echo ""
echo "=================================="

# 3. Verificar permisos
echo "3️⃣  Verificando permisos..."
STORAGE_PERMS=$(ls -ld storage | awk '{print $1}')
if [[ $STORAGE_PERMS == *"w"* ]]; then
    echo -e "${GREEN}✓ Directorio storage tiene permisos de escritura${NC}"
else
    echo -e "${RED}✗ Problema con permisos en storage${NC}"
    echo "  Ejecuta: chmod -R 775 storage"
fi

# Verificar directorios importantes
DIRS=("storage/framework/sessions" "storage/framework/cache" "storage/logs" "bootstrap/cache")
for dir in "${DIRS[@]}"; do
    if [ -d "$dir" ]; then
        echo -e "${GREEN}✓ $dir existe${NC}"
    else
        echo -e "${RED}✗ $dir no existe${NC}"
        echo "  Ejecuta: mkdir -p $dir"
    fi
done

echo ""
echo "=================================="

# 4. Verificar configuración de PHP
echo "4️⃣  Verificando PHP..."
php -v
echo ""
echo "Extensiones necesarias:"
for ext in "pdo_mysql" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json"; do
    if php -m | grep -q "^$ext$"; then
        echo -e "${GREEN}✓ $ext instalada${NC}"
    else
        echo -e "${RED}✗ $ext NO instalada${NC}"
    fi
done

echo ""
echo "=================================="

# 5. Verificar archivos problemáticos
echo "5️⃣  Verificando archivos críticos..."

# VerifyCsrfToken.php
echo "Verificando VerifyCsrfToken.php..."
if [ -f app/Http/Middleware/VerifyCsrfToken.php ]; then
    if php -l app/Http/Middleware/VerifyCsrfToken.php > /dev/null 2>&1; then
        echo -e "${GREEN}✓ VerifyCsrfToken.php sintaxis correcta${NC}"
    else
        echo -e "${RED}✗ Error de sintaxis en VerifyCsrfToken.php${NC}"
        php -l app/Http/Middleware/VerifyCsrfToken.php
    fi
else
    echo -e "${RED}✗ VerifyCsrfToken.php no existe${NC}"
fi

# RouteServiceProvider.php
echo "Verificando RouteServiceProvider.php..."
if grep -q "configureRateLimiting" app/Providers/RouteServiceProvider.php 2>/dev/null; then
    echo -e "${GREEN}✓ Rate limiting configurado${NC}"
else
    echo -e "${RED}✗ Rate limiting NO configurado${NC}"
fi

echo ""
echo "=================================="

# 6. Verificar base de datos
echo "6️⃣  Verificando conexión a base de datos..."
php artisan db:show 2>&1 | head -5

echo ""
echo "Estado de migraciones:"
php artisan migrate:status 2>&1 | head -10

echo ""
echo "=================================="

# 7. Verificar rutas
echo "7️⃣  Verificando rutas API..."
echo "Rutas de carrito:"
php artisan route:list --path=api/v1/cart 2>&1

echo ""
echo "=================================="

# 8. Limpiar cachés
echo "8️⃣  Limpiando cachés..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "=================================="

# 9. Test de API
echo "9️⃣  Probando endpoint del carrito..."
echo "Iniciando servidor temporalmente..."

# Iniciar servidor en background
php artisan serve --port=8001 > /dev/null 2>&1 &
SERVER_PID=$!
sleep 3

# Hacer petición de prueba
echo "Haciendo petición a http://localhost:8001/api/v1/cart"
RESPONSE=$(curl -s -w "\nHTTP_CODE:%{http_code}" -H "Accept: application/json" http://localhost:8001/api/v1/cart)
HTTP_CODE=$(echo "$RESPONSE" | grep "HTTP_CODE" | cut -d: -f2)
BODY=$(echo "$RESPONSE" | sed '/HTTP_CODE/d')

if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✓ API respondió correctamente (HTTP $HTTP_CODE)${NC}"
    echo "Respuesta: $BODY" | jq . 2>/dev/null || echo "$BODY"
else
    echo -e "${RED}✗ API respondió con error (HTTP $HTTP_CODE)${NC}"
    echo "Respuesta: $BODY"
fi

# Detener servidor
kill $SERVER_PID 2>/dev/null

echo ""
echo "=================================="

# 10. Resumen de acciones recomendadas
echo "📋 RESUMEN DE ACCIONES RECOMENDADAS:"
echo ""

# Verificar si hay problemas y dar recomendaciones
if ! [ -f .env ]; then
    echo "1. cp .env.example .env"
    echo "2. php artisan key:generate"
fi

if ! grep -q "configureRateLimiting" app/Providers/RouteServiceProvider.php 2>/dev/null; then
    echo "3. Configura el rate limiting en RouteServiceProvider.php"
fi

echo "4. Si hay errores de permisos:"
echo "   chmod -R 775 storage bootstrap/cache"
echo "   chown -R \$USER:www-data storage bootstrap/cache"

echo ""
echo "5. Si la base de datos no está configurada:"
echo "   - Verifica las credenciales en .env"
echo "   - Ejecuta: php artisan migrate:fresh --seed"

echo ""
echo "=================================="
echo "✅ Diagnóstico completado!"
