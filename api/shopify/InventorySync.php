<?php
declare(strict_types=1);

final class CaprinoInventorySync
{
    private const LOCATION = 'gid://shopify/Location/84605075647';
    public function __construct(private ShopifyClient $client, private array $config, private $catalog) {}

    private function source(string $reference): array
    {
        // Sincronización manual por referencia, conservando tienda y punto acordados.
        if (!preg_match('/^[A-Z0-9]{2,30}$/D', $reference) || ($this->config['shopify']['shop'] ?? '') !== 'kwtj0h-qz.myshopify.com'
            || array_map('intval', $this->config['point_of_sale_ids'] ?? []) !== [1]) {
            throw new ShopifyIntegrationException('Se requiere una referencia válida, Unicentro (1) y la tienda kwtj0h-qz');
        }
        $data = ($this->catalog)($reference);
        if (count($data['products'] ?? []) !== 1 || !empty($data['has_more'])) {
            throw new ShopifyIntegrationException('La referencia no existe o es ambigua en Caprino');
        }
        $variants = $data['products'][0]['variants'];
        $source = [];
        foreach ($variants as $variant) {
            $sku = $variant['sku'];
            if (isset($source[$sku]) || !preg_match('/^' . preg_quote($reference, '/') . '-[A-Za-z0-9.]{1,10}$/D', $sku)
                || !is_int($variant['available']) || $variant['available'] < 0) {
                throw new ShopifyIntegrationException('SKU o cantidad inválida en Caprino');
            }
            $source[$sku] = $variant['available'];
        }
        if (count($source) < 1 || count($source) > 30) {
            throw new ShopifyIntegrationException('Se admiten entre 1 y 30 variantes por referencia');
        }
        ksort($source);
        return $source;
    }

    private function remote(string $sku): array
    {
        $data = $this->client->query('query($search: String!, $location: ID!) {
            productVariants(first: 10, query: $search) {
                nodes { id sku inventoryItem { id tracked inventoryLevel(locationId: $location) {
                    quantities(names: ["available"]) { name quantity }
                } } } pageInfo { hasNextPage }
            }
        }', ['search' => 'sku:"' . $sku . '"', 'location' => self::LOCATION]);
        $connection = $data['productVariants'] ?? [];
        $matches = array_values(array_filter($connection['nodes'] ?? [], fn($v) => ($v['sku'] ?? null) === $sku));
        if (!empty($connection['pageInfo']['hasNextPage']) || count($matches) !== 1) {
            throw new ShopifyIntegrationException("SKU ausente o duplicado en Shopify: $sku");
        }
        $item = $matches[0]['inventoryItem'];
        $quantities = $item['inventoryLevel']['quantities'] ?? [];
        if (($item['tracked'] ?? false) !== true || count($quantities) !== 1
            || $quantities[0]['name'] !== 'available' || !is_int($quantities[0]['quantity'])) {
            throw new ShopifyIntegrationException("Inventario no activo en la ubicación para $sku");
        }
        return ['sku' => $sku, 'inventoryItemId' => $item['id'], 'locationId' => self::LOCATION,
            'changeFromQuantity' => $quantities[0]['quantity']];
    }

    public function preview(string $reference): array
    {
        $source = $this->source($reference);
        $location = $this->client->query('query($id: ID!) { location(id: $id) { id name isActive } }', ['id' => self::LOCATION]);
        if (($location['location']['isActive'] ?? false) !== true) {
            throw new ShopifyIntegrationException('La ubicación destino no está activa');
        }
        $rows = [];
        foreach ($source as $sku => $quantity) {
            $rows[] = $this->remote($sku) + ['quantity' => $quantity];
        }
        $plan = ['reference' => $reference, 'shop' => $this->config['shopify']['shop'], 'source' => $source,
            'rows' => $rows, 'expires' => time() + 600, 'key' => bin2hex(random_bytes(16))];
        $payload = base64_encode(json_encode($plan, JSON_THROW_ON_ERROR));
        return ['dry_run' => true, 'location' => $location['location'], 'point_of_sale_ids' => [1],
            'variants' => $rows, 'plan' => $payload . '.' . hash_hmac('sha256', $payload, $this->config['api_token']),
            'expires_in' => 600];
    }

    public function apply(string $signed): array
    {
        $parts = explode('.', $signed);
        if (count($parts) !== 2 || !hash_equals(hash_hmac('sha256', $parts[0], $this->config['api_token']), $parts[1])) {
            throw new InvalidArgumentException('Plan inválido');
        }
        $plan = json_decode(base64_decode($parts[0], true) ?: '', true, 32, JSON_THROW_ON_ERROR);
        if (($plan['expires'] ?? 0) < time() || ($plan['shop'] ?? '') !== ($this->config['shopify']['shop'] ?? '')) {
            throw new ShopifyIntegrationException('El plan venció o corresponde a otra tienda; generar vista previa');
        }
        if ($this->source($plan['reference']) !== $plan['source']) {
            throw new ShopifyIntegrationException('El inventario de Caprino cambió; generar otra vista previa');
        }
        $quantities = array_map(function($row) { unset($row['sku']); return $row; }, $plan['rows']);
        $result = $this->client->query('mutation($input: InventorySetQuantitiesInput!, $key: String!) {
            inventorySetQuantities(input: $input) @idempotent(key: $key) {
                inventoryAdjustmentGroup { createdAt }
                userErrors { code field }
            }
        }', ['input' => ['name' => 'available', 'reason' => 'correction', 'quantities' => $quantities], 'key' => $plan['key']]);
        $outcome = $result['inventorySetQuantities'] ?? null;
        if (!is_array($outcome) || !array_key_exists('userErrors', $outcome)) {
            throw new ShopifyIntegrationException('Respuesta de escritura incompleta; reintentar con el mismo plan');
        }
        if ($outcome['userErrors']) {
            // Códigos estructurados, nunca cuerpos HTTP ni mensajes arbitrarios externos.
            throw new ShopifyIntegrationException('Shopify rechazó el inventario: ' . implode(', ', array_map(
                fn($e) => preg_replace('/[^A-Z_]/', '', (string) ($e['code'] ?? 'UNKNOWN')), $outcome['userErrors'])));
        }
        if (empty($outcome['inventoryAdjustmentGroup'])) {
            throw new ShopifyIntegrationException('Shopify no confirmó el ajuste; reintentar con el mismo plan');
        }
        $verified = [];
        try {
            foreach ($plan['rows'] as $row) {
                $current = $this->remote($row['sku']);
                $verified[] = ['sku' => $row['sku'], 'expected' => $row['quantity'],
                    'actual' => $current['changeFromQuantity'], 'matches' => $current['changeFromQuantity'] === $row['quantity']];
            }
        } catch (ShopifyIntegrationException $e) {
            return ['applied' => true, 'verified' => false, 'verification_pending' => true, 'variants' => $verified];
        }
        return ['applied' => true, 'verified' => !in_array(false, array_column($verified, 'matches'), true), 'variants' => $verified];
    }
}
