<?php
declare(strict_types=1);

// Solo mensajes controlados: nunca incluir cuerpos HTTP, tokens ni curl_error().
final class ShopifyIntegrationException extends RuntimeException {}

final class ShopifyClient
{
    private string $endpoint;
    private ?string $token = null;
    private int $tokenExpiresAt = 0;

    private function usesClientCredentials(): bool
    {
        return !empty($this->config['client_id']) && !empty($this->config['client_secret']);
    }

    private function request(string $url, string $payload, array $headers): array
    {
        if ($this->transport) {
            return ($this->transport)($url, $payload, $headers);
        }
        if (!function_exists('curl_init')) {
            throw new ShopifyIntegrationException('La extensión cURL no está habilitada en PHP');
        }
        $curl = curl_init($url);
        curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 45,
            CURLOPT_FOLLOWLOCATION => false, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2]);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $curlCode = curl_errno($curl);
        curl_close($curl);
        if ($body === false) {
            throw new ShopifyIntegrationException("No se pudo conectar con Shopify (cURL $curlCode)");
        }
        return [$status, $body];
    }

    private function accessToken(): string
    {
        if (!$this->usesClientCredentials()) {
            return $this->config['access_token'];
        }
        if ($this->token !== null && time() < $this->tokenExpiresAt - 60) {
            return $this->token;
        }
        $startedAt = time();
        [$status, $body] = $this->request(
            'https://' . $this->config['shop'] . '/admin/oauth/access_token',
            http_build_query(['grant_type' => 'client_credentials',
                'client_id' => $this->config['client_id'], 'client_secret' => $this->config['client_secret']]),
            ['Content-Type: application/x-www-form-urlencoded']
        );
        if ($status !== 200) {
            $error = json_decode($body, true);
            $code = is_array($error) ? ($error['error'] ?? '') : '';
            $detail = match ($code) {
                'invalid_client' => 'Client ID o Client Secret incorrectos',
                'shop_not_permitted' => 'La tienda no está permitida para esta app; revisar organización',
                'invalid_request' => 'Solicitud de autenticación rechazada',
                'invalid_grant' => 'Credenciales o autorización de la app no válidas',
                default => 'Revisar credenciales, instalación y organización',
            };
            throw new ShopifyIntegrationException("Autenticación Shopify HTTP $status: $detail");
        }
        try {
            $response = json_decode($body, true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ShopifyIntegrationException('Respuesta de autenticación Shopify inválida');
        }
        $token = $response['access_token'] ?? null;
        $expires = $response['expires_in'] ?? null;
        if (!is_string($token) || $token === '' || preg_match('/[\r\n]/', $token)
            || !is_int($expires) || $expires <= 0 || $expires > 86400 * 365) {
            throw new ShopifyIntegrationException('Respuesta de autenticación Shopify incompleta');
        }
        $this->token = $token;
        $this->tokenExpiresAt = $startedAt + $expires;
        return $this->token;
    }
    public function __construct(private array $config, private $transport = null)
    {
        $shop = $config['shop'] ?? '';
        $version = $config['api_version'] ?? '2026-07';
        if (!preg_match('/^[a-z0-9][a-z0-9-]*\.myshopify\.com$/D', $shop)) {
            throw new ShopifyIntegrationException('Configurar el dominio canónico myshopify.com');
        }
        if (!preg_match('/^20[0-9]{2}-(01|04|07|10)$/D', $version) || (!$this->usesClientCredentials() && empty($config['access_token']))) {
            throw new ShopifyIntegrationException('Configurar api_version y client_id/client_secret o access_token de Shopify');
        }
        $this->endpoint = "https://$shop/admin/api/$version/graphql.json";
    }

    public function query(string $query, array $variables = []): array
    {
        $payload = json_encode(['query' => $query, 'variables' => (object) $variables], JSON_THROW_ON_ERROR);
        [$status, $body] = $this->request($this->endpoint, $payload,
            ['Content-Type: application/json', 'X-Shopify-Access-Token: ' . $this->accessToken()]);
        // Solo reintentar consultas de lectura tras un token revocado anticipadamente.
        if ($status === 401 && $this->usesClientCredentials()
            && preg_match('/^\s*(?:\{|query\b)/', $query)) {
            $this->token = null;
            [$status, $body] = $this->request($this->endpoint, $payload,
                ['Content-Type: application/json', 'X-Shopify-Access-Token: ' . $this->accessToken()]);
        }
        if ($status !== 200) {
            throw new ShopifyIntegrationException("Shopify respondió HTTP $status");
        }
        try {
            $response = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ShopifyIntegrationException('Shopify devolvió una respuesta GraphQL inválida');
        }
        if (!empty($response['errors']) || !isset($response['data'])) {
            throw new ShopifyIntegrationException('Shopify rechazó la consulta GraphQL; revisar permisos y versión');
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
