<?php
// Copiar a ../private-config/caprino-shopify.php, fuera de la raíz pública del proyecto.
// Opcional: CAPRINO_INTEGRATION_CONFIG permite indicar otra ruta absoluta.
return [
    'api_token' => '', // Generar con bin2hex(random_bytes(32)).
    'database' => [
        'dsn' => 'mysql:host=mysql;dbname=caprino;charset=utf8mb4',
        'user' => '',
        'password' => '',
    ],
    'point_of_sale_ids' => [], // IDs autorizados para el inventario web consolidado.
    'stock_reserve' => 0, // Unidades que se reservan por variante, después de sumar puntos.
    'shopify' => [
        'shop' => '', // Confirmar dominio canónico en Shopify; no usar admin.shopify.com.
        'api_version' => '2026-07',
        'client_id' => '', // App instalada en la misma organización de la tienda.
        'client_secret' => '', // Guardar únicamente en la copia privada del servidor.
        'access_token' => '', // Alternativa manual; dejar vacío usando client credentials.
    ],
];
