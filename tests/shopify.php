<?php
declare(strict_types=1);
require __DIR__ . '/../api/shopify/bootstrap.php';
require __DIR__ . '/../api/shopify/Catalog.php';
require __DIR__ . '/../api/shopify/Client.php';
$count = 0;
function check(bool $value): void {
    global $count;
    if (!$value) { throw new RuntimeException('Falló prueba ' . ($count + 1)); }
    $count++;
}
function fails(callable $call, string $class): void {
    try { $call(); } catch (Throwable $e) { check($e instanceof $class); return; }
    throw new RuntimeException('Se esperaba ' . $class);
}
$token = str_repeat('a', 64);
integrationAuthorize(['api_token' => $token], 'Bearer ' . $token);
check(true);
fails(fn() => integrationAuthorize(['api_token' => $token], ''), UnexpectedValueException::class);
fails(fn() => integrationAuthorize(['api_token' => $token], 'Bearer wrong'), UnexpectedValueException::class);
fails(fn() => integrationAuthorize([], ''), RuntimeException::class);
foreach (['1 OR 1=1', -1, [], 101] as $bad) {
    fails(fn() => integrationInteger($bad, 1, 100, 'limit'), InvalidArgumentException::class);
}
$db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$db->exec('CREATE TABLE Referencia (IDReferencia INTEGER, Numero TEXT, Nombre TEXT, Publicar TEXT, DescripcionCorta TEXT, DescripcionLarga TEXT, FotoWeb1 TEXT, FotoWeb2 TEXT, FotoWeb3 TEXT, FotoWeb4 TEXT, IDColor INTEGER, IDPrecio INTEGER);
CREATE TABLE PuntoVentaReferencia (IDPuntoVentaReferencia INTEGER, IDReferencia INTEGER, IDPuntoVenta INTEGER);
CREATE TABLE CodificacionEspecifica (IDPuntoVentaReferencia INTEGER, IDTalla INTEGER, Existencias INTEGER);
CREATE TABLE Talla (IDTalla INTEGER, Nombre TEXT, Publicar TEXT);
CREATE TABLE Color (IDColor INTEGER, DescripcionLarga TEXT);
CREATE TABLE Precio (IDPrecio INTEGER, ValorVenta NUMERIC, Descuento NUMERIC);
INSERT INTO Referencia (IDReferencia, Numero, Nombre, Publicar, IDColor, IDPrecio) VALUES (1,"ABCDNEG","Negro","S",1,1),(2,"ABCDBLA","Blanco","S",1,1),(3,"OTRA","Otro","S",1,1);
INSERT INTO PuntoVentaReferencia VALUES (1,1,1),(2,1,2),(3,1,99),(4,2,1);
INSERT INTO CodificacionEspecifica VALUES (1,1,3),(2,1,4),(3,1,100),(1,2,0),(4,1,-2);
INSERT INTO Talla VALUES (1,"35","S"),(2,"36","S");
INSERT INTO Color VALUES (1,"Negro");
INSERT INTO Precio VALUES (1,120000,10);');
$catalog = new CaprinoCatalog($db, ['point_of_sale_ids' => [1,2], 'stock_reserve' => 1]);
$first = $catalog->products('', 1, 1);
check($first['has_more'] === true);
check($first['products'][0]['variants'][0]['stock'] === 7);
check($first['products'][0]['variants'][0]['available'] === 6);
check($first['products'][0]['variants'][1]['available'] === 0);
check($first['products'][0]['variants'][0]['sku'] === 'ABCDNEG-35');
check($first['products'][0]['variants'][0]['price'] === '120000.00');
check($catalog->products('', 2, 1)['products'][0]['reference'] === 'ABCDBLA');
check($catalog->products('', 3, 1)['has_more'] === false);
check($catalog->products('', 4, 1)['products'] === []);
check($catalog->products("' OR 1=1 --", 1, 100)['products'] === []);
check($catalog->products('ABCDBLA', 1, 100)['products'][0]['variants'][0]['available'] === 0);
fails(fn() => (new CaprinoCatalog($db, []))->products('', 1, 1), RuntimeException::class);
$config = ['shop' => 'test.myshopify.com', 'access_token' => 'test'];
fails(fn() => new ShopifyClient(['shop' => 'attacker.example', 'access_token' => 'x']), RuntimeException::class);
$client = new ShopifyClient($config, function($url, $payload) {
    check($url === 'https://test.myshopify.com/admin/api/2026-07/graphql.json');
    check(json_decode($payload, true)['variables']['cursor'] === 'next-page');
    return [200, '{"data":{"productVariants":{"nodes":[],"pageInfo":{"hasNextPage":false}}}}'];
});
check($client->variants('next-page')['productVariants']['nodes'] === []);
foreach ([[401, '{}'], [429, '{}'], [200, '{"errors":[{"message":"denied"}]}']] as $failure) {
    fails(fn() => (new ShopifyClient($config, fn() => $failure))->status(), RuntimeException::class);
}
echo "$count pruebas OK\n";
