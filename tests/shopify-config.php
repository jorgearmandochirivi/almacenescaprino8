<?php
declare(strict_types=1);

// Emula la disposición del servidor sin leer credenciales reales.
$root = sys_get_temp_dir() . '/caprino-config-' . bin2hex(random_bytes(8));
$bootstrapDir = $root . '/httpdocs/api/shopify';
mkdir($bootstrapDir, 0700, true);
mkdir($root . '/private-config', 0700);
copy(__DIR__ . '/../api/shopify/bootstrap.php', $bootstrapDir . '/bootstrap.php');
require $bootstrapDir . '/bootstrap.php';
$previous = getenv('CAPRINO_INTEGRATION_CONFIG');
$count = 0;
function verifyConfig(bool $ok): void {
    global $count;
    if (!$ok) { throw new RuntimeException('Prueba de configuración fallida'); }
    $count++;
}
function expectConfigFailure(): void {
    try { integrationConfig(); } catch (RuntimeException $e) { verifyConfig(true); return; }
    throw new RuntimeException('Se esperaba error de configuración');
}
try {
    putenv('CAPRINO_INTEGRATION_CONFIG');
    expectConfigFailure();
    file_put_contents($root . '/private-config/caprino-shopify.php', '<?php return ["source" => "fallback"];');
    verifyConfig(integrationConfig()['source'] === 'fallback');
    putenv('CAPRINO_INTEGRATION_CONFIG=');
    verifyConfig(integrationConfig()['source'] === 'fallback');
    file_put_contents($root . '/override.php', '<?php return ["source" => "override"];');
    putenv('CAPRINO_INTEGRATION_CONFIG=' . $root . '/override.php');
    verifyConfig(integrationConfig()['source'] === 'override');
    putenv('CAPRINO_INTEGRATION_CONFIG=' . $root . '/missing.php');
    expectConfigFailure();
    putenv('CAPRINO_INTEGRATION_CONFIG');
    file_put_contents($root . '/private-config/caprino-shopify.php', '<?php return "invalid";');
    expectConfigFailure();
    echo "$count pruebas de configuración OK\n";
} finally {
    putenv($previous === false ? 'CAPRINO_INTEGRATION_CONFIG' : 'CAPRINO_INTEGRATION_CONFIG=' . $previous);
    unlink($root . '/override.php');
    unlink($root . '/private-config/caprino-shopify.php');
    unlink($bootstrapDir . '/bootstrap.php');
    rmdir($bootstrapDir);
    rmdir($root . '/httpdocs/api');
    rmdir($root . '/httpdocs');
    rmdir($root . '/private-config');
    rmdir($root);
}
