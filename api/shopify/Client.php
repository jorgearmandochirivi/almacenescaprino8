<?php
declare(strict_types=1);

final class ShopifyClient
{
    private string $endpoint;
    public function __construct(private array $config, private $transport = null)
    {
        $shop = $config['shop'] ?? '';
        $version = $config['api_version'] ?? '2026-07';
        if (!preg_match('/^[a-z0-9][a-z0-9-]*\.myshopify\.com$/D', $shop)) {
            throw new RuntimeException('Configurar el dominio canónico myshopify.com');
        }
        if (!preg_match('/^20[0-9]{2}-(01|04|07|10)$/D', $version) || empty($config['access_token'])) {
            throw new RuntimeException('Configurar api_version y access_token de Shopify');
        }
        $this->endpoint = "https://$shop/admin/api/$version/graphql.json";
    }

    public function query(string $query, array $variables = []): array
    {
        $payload = json_encode(['query' => $query, 'variables' => (object) $variables], JSON_THROW_ON_ERROR);
        if ($this->transport) {
            [$status, $body] = ($this->transport)($this->endpoint, $payload);
        } else {
            $curl = curl_init($this->endpoint);
            curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'X-Shopify-Access-Token: ' . $this->config['access_token']],
                CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 45,
                CURLOPT_FOLLOWLOCATION => false, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2]);
            $body = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            curl_close($curl);
            if ($body === false) {
                throw new RuntimeException('No se pudo conectar con Shopify');
            }
        }
        if ($status !== 200) {
            throw new RuntimeException("Shopify respondió HTTP $status");
        }
        $response = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        if (!empty($response['errors']) || !isset($response['data'])) {
            throw new RuntimeException('Shopify rechazó la consulta GraphQL; revisar permisos y versión');
        }
        return $response['data'];
    }

    public function status(): array
    {
        return $this->query('{ shop { id name myshopifyDomain currencyCode } currentAppInstallation { accessScopes { handle } } }');
    }

    public function locations(): array
    {
        return $this->query('{ locations(first: 250) { nodes { id name isActive } pageInfo { hasNextPage endCursor } } }');
    }

    public function variants(?string $cursor): array
    {
        return $this->query('query($cursor: String) { productVariants(first: 100, after: $cursor) {
            nodes { id sku title price product { id title } inventoryItem { id tracked } }
            pageInfo { hasNextPage endCursor } } }', ['cursor' => $cursor]);
    }
}
