<?php
declare(strict_types=1);
require_once __DIR__ . '/shopify/bootstrap.php';
require_once __DIR__ . '/shopify/Catalog.php';
require_once __DIR__ . '/shopify/Client.php';
require_once __DIR__ . '/shopify/InventorySync.php';

ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
$status = 200;
try {
    $config = integrationConfig();
    integrationAuthorize($config, integrationAuthorizationHeader(
        $_SERVER, function_exists('getallheaders') ? getallheaders() : []
    ));
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (!in_array($method, ['GET', 'POST'], true)) {
        header('Allow: GET, POST');
        throw new UnexpectedValueException('Método no permitido', 405);
    }
    $input = $method === 'POST' ? $_POST : $_GET;
    if ($method === 'POST' && str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
        $raw = file_get_contents('php://input', false, null, 0, 65537);
        if (strlen($raw) > 65536) {
            throw new InvalidArgumentException('Solicitud demasiado grande');
        }
        $input = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
        if (!is_array($input)) {
            throw new InvalidArgumentException('Se requiere un objeto JSON');
        }
    }
    $action = $input['action'] ?? '';
    switch ($action) {
        case 'getreferencias':
            $prefix = $input['Prefijo'] ?? '';
            if (!is_string($prefix)) {
                throw new InvalidArgumentException('Prefijo inválido');
            }
            $response = (new CaprinoCatalog(integrationDb($config), $config))->references($prefix);
            break;
        case 'getproducto':
        case 'getinventario':
            $reference = $input['Referencia'] ?? '';
            if (!is_string($reference) || strlen($reference) > 100) {
                throw new InvalidArgumentException('Referencia inválida');
            }
            $page = integrationInteger($input['Pagina'] ?? 1, 1, 1000000, 'Pagina');
            $limit = integrationInteger($input['CantidadPorPagina'] ?? 50, 1, 100, 'CantidadPorPagina');
            $response = (new CaprinoCatalog(integrationDb($config), $config))->products($reference, $page, $limit);
            break;
        case 'shopify.inventory.preview':
        case 'shopify.inventory.apply':
            if ($action === 'shopify.inventory.apply' && $method !== 'POST') {
                header('Allow: POST');
                throw new UnexpectedValueException('La escritura requiere POST', 405);
            }
            $sync = new CaprinoInventorySync(new ShopifyClient($config['shopify'] ?? []), $config,
                fn($ref) => (new CaprinoCatalog(integrationDb($config), $config))->products($ref, 1, 2));
            $value = $input[$action === 'shopify.inventory.apply' ? 'plan' : 'Referencia'] ?? '';
            if (!is_string($value) || $value === '' || strlen($value) > 20000) {
                throw new InvalidArgumentException('Referencia o plan inválido');
            }
            $response = $action === 'shopify.inventory.apply' ? $sync->apply($value) : $sync->preview($value);
            break;
        case 'shopify.status':
            $response = (new ShopifyClient($config['shopify'] ?? []))->status();
            break;
        case 'shopify.locations':
            $response = (new ShopifyClient($config['shopify'] ?? []))->locations();
            break;
        case 'shopify.variants':
            $cursor = $input['cursor'] ?? null;
            if ($cursor !== null && (!is_string($cursor) || strlen($cursor) > 2048)) {
                throw new InvalidArgumentException('Cursor inválido');
            }
            $response = (new ShopifyClient($config['shopify'] ?? []))->variants($cursor);
            break;
        default:
            throw new UnexpectedValueException('Acción no disponible', 404);
    }
    $result = ['success' => true, 'message' => 'OK', 'response' => $response];
} catch (InvalidArgumentException | JsonException $e) {
    $status = 400;
    $result = ['success' => false, 'message' => 'Parámetros inválidos', 'response' => null];
} catch (UnexpectedValueException $e) {
    $status = in_array($e->getCode(), [401, 404, 405], true) ? $e->getCode() : 400;
    $result = ['success' => false, 'message' => $e->getMessage(), 'response' => null];
} catch (ShopifyIntegrationException $e) {
    $status = 503;
    $result = ['success' => false, 'message' => $e->getMessage(), 'response' => null];
} catch (Throwable $e) {
    $status = 503;
    error_log('Caprino integration failure: ' . get_class($e));
    $result = ['success' => false, 'message' => 'Integración no disponible; revisar configuración y conexión', 'response' => null];
}
http_response_code($status);
$result['date'] = gmdate('c');
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
