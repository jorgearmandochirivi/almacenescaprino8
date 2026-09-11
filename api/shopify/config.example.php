<?php
// Copiar FUERA de la raíz pública; configurar CAPRINO_INTEGRATION_CONFIG con su ruta absoluta.
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
        'access_token' => '', // Token Admin API de la app instalada, no una cookie del navegador.
    ],
];
