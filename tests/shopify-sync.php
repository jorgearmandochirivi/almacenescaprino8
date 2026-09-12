<?php
declare(strict_types=1);
require __DIR__ . '/../api/shopify/Client.php';
require __DIR__ . '/../api/shopify/InventorySync.php';
$checks = 0;
function syncCheck(bool $ok): void { global $checks; if (!$ok) { throw new RuntimeException('Prueba sync falló'); } $checks++; }
function syncFails(callable $f): void { try { $f(); } catch (RuntimeException | InvalidArgumentException $e) { syncCheck(true); return; } throw new RuntimeException('Se esperaba rechazo'); }
$config = ['api_token' => str_repeat('x',64), 'point_of_sale_ids' => [1], 'shopify' => ['shop' => 'kwtj0h-qz.myshopify.com', 'access_token' => 'fake']];
$variants = [];
$stock = [];
foreach (range(34,40) as $size) {
    $variants[] = ['sku' => "CG9NCRRO-$size", 'available' => in_array($size,[34,36,40]) ? 1 : 0];
    $stock["CG9NCRRO-$size"] = 0;
}
$writes = 0; $seen = []; $duplicate = false; $untracked = false; $verifyFailure = false;
$client = new ShopifyClient($config['shopify'], function($url, $body) use (&$writes, &$seen, &$stock, &$duplicate, &$untracked, &$verifyFailure) {
    $request = json_decode($body,true); $vars = $request['variables']; $query = $request['query'];
    if (str_starts_with($query, 'mutation')) {
        $writes++;
        syncCheck(str_contains($query, '@idempotent'));
        if (!isset($seen[$vars['key']])) {
            foreach ($vars['input']['quantities'] as $q) {
                $sku = 'CG9NCRRO-' . basename($q['inventoryItemId']);
                syncCheck($q['locationId'] === 'gid://shopify/Location/84605075647');
                if ($stock[$sku] !== $q['changeFromQuantity']) {
                    return [200, '{"data":{"inventorySetQuantities":{"userErrors":[{"code":"CHANGE_FROM_QUANTITY_STALE"}]}}}'];
                }
            }
            foreach ($vars['input']['quantities'] as $q) { $stock['CG9NCRRO-' . basename($q['inventoryItemId'])] = $q['quantity']; }
            $seen[$vars['key']] = $vars;
        } else { syncCheck($seen[$vars['key']] === $vars); }
        return [200, '{"data":{"inventorySetQuantities":{"inventoryAdjustmentGroup":{"createdAt":"now"},"userErrors":[]}}}'];
    }
    if (isset($vars['search'])) {
        if ($verifyFailure) { return [500, '{}']; }
        $sku = substr($vars['search'],5,-1);
        $node = ['sku'=>$sku,'inventoryItem'=>['id'=>'gid://shopify/InventoryItem/'.substr($sku,-2),'tracked'=>!$untracked,
            'inventoryLevel'=>['quantities'=>[['name'=>'available','quantity'=>$stock[$sku]]]]]];
        return [200, json_encode(['data'=>['productVariants'=>['nodes'=>$duplicate ? [$node,$node]:[$node], 'pageInfo'=>['hasNextPage'=>false]]]])];
    }
    return [200, '{"data":{"location":{"id":"gid://shopify/Location/84605075647","name":"Destino","isActive":true}}}'];
});
$source = function($ref) use (&$variants) { return ['products'=>[['variants'=>$variants]],'has_more'=>false]; };
$sync = new CaprinoInventorySync($client,$config,$source);
$preview = $sync->preview('CG9NCRRO');
syncCheck($writes === 0 && count($preview['variants']) === 7);
$result = $sync->apply($preview['plan']);
syncCheck($result['applied'] && $result['verified'] && array_sum($stock) === 3);
$sync->apply($preview['plan']); // Mismo plan = misma clave y mismo cuerpo.
syncCheck(array_sum($stock) === 3);
syncFails(fn()=> $sync->apply($preview['plan'].'tampered'));
$variants[0]['available'] = 2;
syncFails(fn()=> $sync->apply($preview['plan']));
$variants[0]['available'] = 1;
$new = $sync->preview('CG9NCRRO');
$stock['CG9NCRRO-34'] = 0; // Venta posterior a la vista previa.
syncFails(fn()=> $sync->apply($new['plan']));
syncCheck($stock['CG9NCRRO-34'] === 0);
$duplicate = true; syncFails(fn()=> $sync->preview('CG9NCRRO')); $duplicate = false;
$untracked = true; syncFails(fn()=> $sync->preview('CG9NCRRO')); $untracked = false;
syncFails(fn()=> $sync->preview('OTHER'));
$other = $config; $other['point_of_sale_ids'] = [1,2];
syncFails(fn()=> (new CaprinoInventorySync($client,$other,$source))->preview('CG9NCRRO'));
$expired = json_decode(base64_decode(explode('.',$preview['plan'])[0]),true); $expired['expires'] = time()-1;
$payload = base64_encode(json_encode($expired));
syncFails(fn()=> $sync->apply($payload.'.'.hash_hmac('sha256',$payload,$config['api_token'])));
$new = $sync->preview('CG9NCRRO'); $verifyFailure = true;
$result = $sync->apply($new['plan']);
syncCheck($result['applied'] && !$result['verified'] && $result['verification_pending']);
echo "$checks verificaciones sincronización OK\n";

$verifyFailure = false;
foreach (['BY49MINE', 'ZO9BLIMI', 'ZY38CODO'] as $reference) {
    $variants = [['sku' => $reference . '-34', 'available' => 2], ['sku' => $reference . '-35', 'available' => 0]];
    $stock[$reference . '-34'] = 0; $stock[$reference . '-35'] = 0;
    $result = $sync->preview($reference);
    syncCheck(count($result['variants']) === 2);
    syncCheck($result['variants'][0]['sku'] === $reference . '-34');
}
$variants = [['sku' => 'ZY38CODO-34', 'available' => 0], ['sku' => 'ZY38CODO-34', 'available' => 0]];
syncFails(fn() => $sync->preview('ZY38CODO'));
$variants = [];
syncFails(fn() => $sync->preview('ZY38CODO'));
syncFails(fn() => $sync->preview('ZY38*'));
echo "9 verificaciones ampliación OK\n";
