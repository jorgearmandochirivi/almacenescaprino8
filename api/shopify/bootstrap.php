<?php
declare(strict_types=1);

function integrationConfig(): array
{
    $path = getenv('CAPRINO_INTEGRATION_CONFIG');
    if ($path === false || $path === '') {
        $path = dirname(__DIR__, 3) . '/private-config/caprino-shopify.php';
    }
    if (!is_file($path) || !is_readable($path)) {
        throw new RuntimeException('Archivo de configuración de integración no disponible');
    }
    $config = require $path;
    if (!is_array($config)) {
        throw new RuntimeException('Configuración de integración inválida');
    }
    return $config;
}

function integrationDb(array $config): PDO
{
    if (empty($config['database']['dsn'])) {
        throw new RuntimeException('Falta configurar database.dsn');
    }
    return new PDO($config['database']['dsn'], $config['database']['user'] ?? '', $config['database']['password'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function integrationAuthorizationHeader(array $server, array $headers = []): string
{
    foreach (['HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION', 'REDIRECT_REDIRECT_HTTP_AUTHORIZATION'] as $key) {
        if (isset($server[$key]) && is_string($server[$key]) && $server[$key] !== '') {
            return $server[$key];
        }
    }
    foreach ($headers as $name => $value) {
        if (strcasecmp((string) $name, 'Authorization') === 0 && is_string($value)) {
            return $value;
        }
    }
    return '';
}

function integrationAuthorize(array $config, string $authorization): void
{
    $secret = $config['api_token'] ?? '';
    if (strlen($secret) < 32) {
        throw new RuntimeException('Configurar api_token de al menos 32 caracteres');
    }
    if ($authorization === '') {
        throw new UnexpectedValueException('No se recibió el encabezado Authorization', 401);
    }
    if (!preg_match('/^Bearer[ \t]+(\S+)$/iD', $authorization, $matches) || !hash_equals($secret, $matches[1])) {
        throw new UnexpectedValueException('No autorizado', 401);
    }
}

function integrationInteger($value, int $min, int $max, string $name): int
{
    $parsed = filter_var($value, FILTER_VALIDATE_INT);
    if ($parsed === false || $parsed < $min || $parsed > $max) {
        throw new InvalidArgumentException("Parámetro inválido: $name");
    }
    return $parsed;
}
