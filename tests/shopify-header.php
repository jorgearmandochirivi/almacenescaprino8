<?php
declare(strict_types=1);
require __DIR__ . '/../api/shopify/bootstrap.php';
$token = str_repeat('x', 64);
$count = 0;
foreach ([['HTTP_AUTHORIZATION' => 'Bearer ' . $token], ['REDIRECT_HTTP_AUTHORIZATION' => 'Bearer ' . $token], ['REDIRECT_REDIRECT_HTTP_AUTHORIZATION' => 'Bearer ' . $token]] as $server) {
    integrationAuthorize(['api_token' => $token], integrationAuthorizationHeader($server));
    $count++;
}
integrationAuthorize(['api_token' => $token], integrationAuthorizationHeader([], ['authorization' => 'bearer ' . $token]));
$count++;
foreach ([['', 'No se recibió el encabezado Authorization'], ['Bearer bad', 'No autorizado'], ['Bearer x, Bearer y', 'No autorizado']] as [$value, $message]) {
    try {
        integrationAuthorize(['api_token' => $token], $value);
        throw new RuntimeException('Se esperaba 401');
    } catch (UnexpectedValueException $e) {
        if ($e->getCode() !== 401 || $e->getMessage() !== $message) { throw $e; }
        $count++;
    }
}
// Una credencial primaria incorrecta nunca se sustituye por un fallback válido.
$value = integrationAuthorizationHeader(['HTTP_AUTHORIZATION' => 'Bearer incorrect', 'REDIRECT_HTTP_AUTHORIZATION' => 'Bearer ' . $token]);
if ($value !== 'Bearer incorrect') { throw new RuntimeException('Prioridad incorrecta'); }
$count++;
echo "$count pruebas de encabezado OK\n";
