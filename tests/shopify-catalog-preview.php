<?php
declare(strict_types=1);
require __DIR__ . '/../api/shopify/Client.php';
require __DIR__ . '/../api/shopify/CatalogPreview.php';
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
function product($ref, $id) { return ['reference_id'=>$id,'reference'=>$ref,'image_files'=>[],
    'variants'=>[['sku'=>"$ref-35",'price'=>'100.00','discount'=>10,'available'=>1]]]; }
function runPreview($source, $remoteProducts, $remoteVariants, $broken = false) {
    $client = new ShopifyClient(['shop'=>'test.myshopify.com','access_token'=>'test'],
        function($url, $payload) use ($remoteProducts, $remoteVariants, $broken) {
            $request = json_decode($payload, true);
            check(!str_contains($request['query'], 'mutation'), 'La vista previa no escribe');
            $field = str_contains($request['query'], 'productVariants(') ? 'productVariants' : 'products';
            $items = $field === 'products' ? $remoteProducts : $remoteVariants;
            $index = (int) ($request['variables']['cursor'] ?? 0);
            $data = ['nodes'=>array_slice($items,$index,1),'pageInfo'=>[
                'hasNextPage'=>$index + 1 < count($items), 'endCursor'=>(string)($index+1)]];
            if ($broken) unset($data['pageInfo']);
            return [200,json_encode(['data'=>[$field=>$data]])];
        });
    return (new CaprinoCatalogPreview($client, fn($page)=>[
        'products'=>array_slice($source,$page-1,1),'has_more'=>$page<count($source)]))->preview();
}
$products = [['id'=>'p1','title'=>'Prueba','status'=>'ACTIVE'],['id'=>'p2','title'=>'Viejo','status'=>'ACTIVE']];
$variants = [['id'=>'v1','sku'=>'ABC-35','product'=>['id'=>'p1']],['id'=>'v2','sku'=>'OLD-35','product'=>['id'=>'p2']]];
$r=runPreview([product('ABC',1),product('DEF',2)],$products,$variants);
check($r['summary']===['source_products'=>2,'shopify_products'=>2,'create'=>1,'update_candidates'=>1,'archive'=>1], 'Todas las páginas se comparan');
check($r['archive'][0]['id']==='p2' && !$r['archive_blocked'], 'Archivo correcto');
check(count($r['warnings'])===2, 'Fotos faltantes visibles');
$r=runPreview([],$products,$variants);
check($r['archive_blocked'] && !$r['archive'], 'Vacío no archiva todo');
$r=runPreview([product('ABC',1)],$products,array_merge($variants,[['id'=>'v3','sku'=>'ABC-35','product'=>['id'=>'p2']]]));
check($r['archive_blocked'] && !$r['archive'], 'SKU duplicado bloquea archivo');
$r=runPreview([product('ABC',1)],$products,array_merge($variants,[['id'=>'v3','sku'=>'UNKNOWN','product'=>['id'=>'p1']]]));
check($r['archive_blocked'], 'Variantes ajenas requieren revisión');
$bad=product('ABC',1); $bad['variants'][0]['price']=null;
$r=runPreview([$bad],$products,$variants);
check($r['archive_blocked'], 'Precio inválido bloquea archivo');
foreach ([false,true] as $broken) {
    try {
        runPreview($broken ? [product('ABC',1)] : [product('ABC',1),product('DEF',1)],$products,$variants,$broken);
        throw new RuntimeException('Debió rechazar lectura');
    } catch (ShopifyIntegrationException $e) {}
}
echo "OK: catálogo paginado, coincidencias, archivo y bloqueos; solo consultas de lectura\n";

$r=runPreview([product('ABC',1),product('ABC',2),product('ABC',3),product('DEF',4)],$products,$variants);
check($r['archive_blocked'] && !$r['archive'], 'Duplicados bloquean archivo');
check(!$r['update_candidates'] && count($r['create'])===1 && $r['create'][0]['reference']==='DEF', 'No elegir arbitrariamente un duplicado');
$duplicateErrors=array_values(array_filter($r['errors'],fn($e)=>isset($e['reference_ids'])));
check($duplicateErrors[0]['reference_ids']===[1,2,3] && $duplicateErrors[0]['reference']==='ABC', 'Identifica todos los IDs duplicados entre páginas');
echo "OK: duplicados diagnosticados sin perder las referencias válidas\n";
