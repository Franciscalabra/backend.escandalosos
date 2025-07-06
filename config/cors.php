<?php

// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
        env('FRONTEND_URL', 'http://localhost:3000')
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // IMPORTANTE: Debe ser true
];

// config/session.php - Verificar estas configuraciones:
return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION', null),
    'table' => 'sessions',
    'store' => env('SESSION_STORE', null),
    'lottery' => [2, 100],
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null), // Dejar null para desarrollo local
    'secure' => env('SESSION_SECURE_COOKIE', false), // false para desarrollo local
    'httponly' => true,
    'same_site' => 'lax', // Importante para CORS
];

