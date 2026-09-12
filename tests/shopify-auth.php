<?php
declare(strict_types=1);
require __DIR__ . '/../api/shopify/Client.php';
$count = 0;
function verifyAuth(bool $ok): void {
    global $count;
    if (!$ok) { throw new RuntimeException('Prueba autenticación fallida'); }
    $count++;
}
$config = ['shop' => 'test.myshopify.com', 'client_id' => 'test-id', 'client_secret' => 'test&secret'];
$authCalls = 0;
$apiCalls = 0;
$client = new ShopifyClient($config, function($url, $body, $headers) use (&$authCalls, &$apiCalls) {
    if (str_ends_with($url, '/oauth/access_token')) {
        $authCalls++;
        parse_str($body, $form);
        verifyAuth($form === ['grant_type' => 'client_credentials', 'client_id' => 'test-id', 'client_secret' => 'test&secret']);
        return [200, json_encode(['access_token' => 'token-' . $authCalls, 'expires_in' => 86399])];
    }
    $apiCalls++;
    verifyAuth(in_array('X-Shopify-Access-Token: token-' . $authCalls, $headers, true));
    return $apiCalls === 2 ? [401, '{}'] : [200, '{"data":{"shop":{"name":"Test"}}}'];
});
verifyAuth($client->status()['shop']['name'] === 'Test');
$client->status();
verifyAuth($authCalls === 2 && $apiCalls === 3);
$client->status();
verifyAuth($authCalls === 2 && $apiCalls === 4);
$authCalls = 0;
$client = new ShopifyClient($config, function($url) use (&$authCalls) {
    if (str_ends_with($url, '/oauth/access_token')) {
        $authCalls++;
        return [200, '{"access_token":"short","expires_in":1}'];
    }
    return [200, '{"data":{}}'];
});
$client->status();
$client->status();
verifyAuth($authCalls === 2);
foreach ([[400, '{"error":"secret details"}'], [200, '{}'], [200, 'not json'], [200, '{"access_token":"x","expires_in":0}']] as $failure) {
    $failed = false;
    try {
        (new ShopifyClient($config, fn() => $failure))->status();
    } catch (RuntimeException $e) {
        $failed = true;
        verifyAuth(!str_contains($e->getMessage(), 'secret details'));
    }
    verifyAuth($failed);
}
$attempts = 0;
$client = new ShopifyClient($config, function($url) use (&$attempts) {
    if (str_ends_with($url, '/oauth/access_token')) {
        return [200, '{"access_token":"x","expires_in":86399}'];
    }
    $attempts++;
    return [401, '{}'];
});
try { $client->status(); } catch (RuntimeException $e) {}
verifyAuth($attempts === 2);
echo "$count pruebas de autenticación OK\n";
foreach ([['invalid_client', 'Client ID o Client Secret incorrectos'], ['shop_not_permitted', 'La tienda no está permitida'], ['SECRET_MUST_NOT_LEAK', 'Revisar credenciales']] as [$code, $expected]) {
    try {
        (new ShopifyClient($config, fn() => [400, json_encode(['error' => $code, 'error_description' => 'SECRET_MUST_NOT_LEAK'])]))->status();
        throw new RuntimeException('Se esperaba error seguro');
    } catch (ShopifyIntegrationException $e) {
        verifyAuth(str_contains($e->getMessage(), $expected));
        verifyAuth(!str_contains($e->getMessage(), 'SECRET_MUST_NOT_LEAK'));
    }
}
echo "6 verificaciones de diagnóstico seguro OK\n";
