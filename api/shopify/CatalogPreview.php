<?php
declare(strict_types=1);

/** Reporte de solo lectura; no constituye un plan ejecutable de escritura. */
final class CaprinoCatalogPreview
{
    public function __construct(private ShopifyClient $client, private $source) {}

    private function remote(string $field, callable $fetch): array
    {
        $all = []; $cursor = null; $seen = [];
        for ($page = 0; $page < 1000; $page++) {
            $data = $fetch($cursor)[$field] ?? null;
            if (!is_array($data) || !is_array($data['nodes'] ?? null)
                || !is_bool($data['pageInfo']['hasNextPage'] ?? null)) {
                throw new ShopifyIntegrationException('Catálogo Shopify incompleto; no se genera reporte');
            }
            foreach ($data['nodes'] as $node) {
                if (empty($node['id']) || isset($all[$node['id']])) {
                    throw new ShopifyIntegrationException('Catálogo Shopify cambió durante la lectura; repetir vista previa');
                }
                $all[$node['id']] = $node;
            }
            if (!$data['pageInfo']['hasNextPage']) return $all;
            $cursor = $data['pageInfo']['endCursor'] ?? null;
            if (!is_string($cursor) || $cursor === '' || isset($seen[$cursor])) break;
            $seen[$cursor] = true;
        }
        throw new ShopifyIntegrationException('Paginación Shopify incompleta; no se genera reporte');
    }

    public function preview(): array
    {
        $source = []; $errors = []; $warnings = []; $seenIds = [];
        for ($page = 1; ; $page++) {
            if ($page > 1000) throw new ShopifyIntegrationException('Catálogo Caprino excede el límite de vista previa');
            $batch = ($this->source)($page);
            if (!is_array($batch['products'] ?? null) || !is_bool($batch['has_more'] ?? null)
                || ($batch['has_more'] && !$batch['products'])) {
                throw new ShopifyIntegrationException('Catálogo Caprino incompleto; no se genera reporte');
            }
            foreach ($batch['products'] as $product) {
                $ref = trim((string) ($product['reference'] ?? ''));
                $id = $product['reference_id'] ?? null;
                if (!$id || isset($seenIds[$id]) || isset($source[$ref])) {
                    throw new ShopifyIntegrationException('Referencias duplicadas o lectura inconsistente en Caprino');
                }
                $seenIds[$id] = true;
                $source[$ref] = $product;
                if (!preg_match('/^[A-Z0-9]{2,30}$/D', $ref) || empty($product['variants'])) {
                    $errors[] = ['reference' => $ref, 'reason' => 'Referencia inválida o sin tallas vendibles'];
                }
                $skus = [];
                foreach ($product['variants'] as $variant) {
                    $sku = $variant['sku'] ?? '';
                    if (!preg_match('/^' . preg_quote($ref, '/') . '-[A-Za-z0-9.]{1,10}$/D', $sku)
                        || isset($skus[$sku]) || !is_numeric($variant['price'] ?? null)
                        || (float) $variant['price'] <= 0 || !is_numeric($variant['discount'] ?? null)
                        || $variant['discount'] < 0 || $variant['discount'] > 100
                        || !is_int($variant['available'] ?? null) || $variant['available'] < 0) {
                        $errors[] = ['reference' => $ref, 'sku' => $sku, 'reason' => 'SKU, precio, descuento o inventario inválido'];
                    }
                    $skus[$sku] = true;
                }
                if (empty($product['image_files'])) $warnings[] = ['reference' => $ref, 'reason' => 'Sin imágenes en FotoWeb1–FotoWeb4'];
            }
            if (!$batch['has_more']) break;
        }
        if (!$source) $errors[] = ['reason' => 'Catálogo Caprino vacío; archivado bloqueado'];
        $products = $this->remote('products', fn($cursor) => $this->client->query(
            'query($cursor: String) { products(first: 100, after: $cursor) { nodes { id title status } pageInfo { hasNextPage endCursor } } }',
            ['cursor' => $cursor]));
        $variants = $this->remote('productVariants', fn($cursor) => $this->client->variants($cursor));
        $bySku = []; $byProduct = [];
        foreach ($variants as $variant) {
            $pid = $variant['product']['id'] ?? '';
            if (!isset($products[$pid])) throw new ShopifyIntegrationException('Catálogo Shopify cambió durante la lectura; repetir vista previa');
            $bySku[$variant['sku']][] = $pid;
            $byProduct[$pid][] = $variant['sku'];
        }
        $create = []; $update = []; $used = [];
        foreach ($source as $ref => $product) {
            $matches = [];
            foreach ($product['variants'] as $variant) {
                $ids = $bySku[$variant['sku']] ?? [];
                if (count($ids) > 1) $errors[] = ['sku' => $variant['sku'], 'reason' => 'SKU duplicado en Shopify'];
                foreach ($ids as $id) $matches[$id] = true;
            }
            $entry = ['reference' => $ref, 'source' => $product];
            if (!$matches) { $create[] = $entry; continue; }
            if (count($matches) > 1) $errors[] = ['reference' => $ref, 'reason' => 'Referencia repartida entre varios productos Shopify'];
            foreach (array_keys($matches) as $id) {
                if (isset($used[$id])) $errors[] = ['product_id' => $id, 'reason' => 'Producto Shopify mezcla varias referencias'];
                $used[$id] = true;
                $entry['product_id'] = $id;
                $entry['current_title'] = $products[$id]['title'];
                $entry['extra_skus'] = array_values(array_diff($byProduct[$id] ?? [], array_column($product['variants'], 'sku')));
                if ($entry['extra_skus']) $errors[] = ['product_id' => $id, 'reason' => 'Producto contiene variantes ajenas al catálogo esperado'];
                $update[] = $entry;
            }
        }
        $archive = [];
        if (!$errors) foreach ($products as $id => $product) {
            if (!isset($used[$id]) && $product['status'] !== 'ARCHIVED') $archive[] = $product;
        }
        return ['dry_run' => true, 'complete' => true, 'archive_blocked' => (bool) $errors,
            'summary' => ['source_products' => count($source), 'shopify_products' => count($products),
                'create' => count($create), 'update_candidates' => count($update), 'archive' => count($archive)],
            'create' => $create, 'update_candidates' => $update, 'archive' => $archive,
            'errors' => $errors, 'warnings' => $warnings];
    }
}
