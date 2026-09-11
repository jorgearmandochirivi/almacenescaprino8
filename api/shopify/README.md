# Preparación de la integración Caprino / Shopify

Estado: API de lectura y cliente GraphQL preparados. No está desplegada ni conectada a la tienda.
El 11 de septiembre de 2026 se verificó acceso en navegador a la tienda `kwtj0h-qz`: cuatro productos activos, variantes y stock cero. Esto no proporciona un token Admin API.

## Configurar

1. Copiar `config.example.php` fuera de la raíz servida por Nginx/Apache y del repositorio. Completar credenciales de base de datos (preferiblemente usuario de solo lectura), un `api_token` aleatorio de al menos 32 caracteres, puntos de venta y reserva de stock.
2. Definir `CAPRINO_INTEGRATION_CONFIG` en el proceso PHP/FPM con la ruta absoluta del archivo. En Docker, montar ese archivo fuera de `/var/www/html`, pasar la variable al servicio PHP y verificar que FPM la recibe. No se cargan archivos `.env` automáticamente.
3. PHP requiere PDO MySQL y cURL. Reconstruir la imagen del proyecto para incorporar `pdo_mysql`: `docker compose build php`.
4. Configurar dominio canónico `*.myshopify.com`, token Admin API válido y versión `2026-07`. El identificador de la URL del administrador debe confirmarse contra el dominio canónico.
5. Servir la API por HTTPS y enviar `Authorization: Bearer <api_token>`. El token de Caprino es distinto del de Shopify y nunca se entrega al storefront.

## Operaciones

Entrada: `/api/caprino.php`. GET, formulario POST o JSON POST. Todas requieren autenticación.

| action | Parámetros | Resultado |
| --- | --- | --- |
| `getproducto` | `Referencia` exacta opcional, `Pagina` (1+), `CantidadPorPagina` (1–100) | Catálogo paginado con variantes y stock |
| `getinventario` | Los mismos | Misma estructura con stock consolidado |
| `shopify.status` | Ninguno | Tienda, moneda y permisos concedidos |
| `shopify.locations` | Ninguno | Primeras 250 ubicaciones, con indicador de continuación |
| `shopify.variants` | `cursor` opcional | 100 variantes y cursor de continuación |

Ejemplo de cuerpo POST:

```json
{"action":"getproducto","Pagina":1,"CantidadPorPagina":50}
```

Respuestas: `success`, `message`, `response`, `date` UTC. HTTP 400 parámetros/JSON inválidos, 401 token inválido, 404 acción desconocida, 405 método inválido, 503 configuración, base de datos o Shopify no disponibles. HTTP 429 de Shopify se comunica como indisponibilidad; no hay reintentos automáticos.

## Reglas y diferencias con la API antigua

- Se reemplazó la entrada inactiva; no se usa la clave JWT incrustada ni `UsuarioWS`. Los consumidores deben migrar al nuevo contrato.
- No se expone `gettoken`, bonos ni `setpedido`; las clases antiguas quedan sin conectar a la nueva entrada.
- Cada referencia completa se mantiene separada: no se agrupa por los primeros cuatro caracteres, para no perder colores.
- SKU propuesto: `Numero-Talla.Nombre`. Validar contra los SKU reales de Shopify antes de sincronizar; todavía no se crean ni asignan SKU en la tienda.
- Se suman existencias exclusivamente de `point_of_sale_ids`. `available = max(0, stock - stock_reserve)`. Se conservan ceros; no se depende de `ExistenciaWeb`, que el proceso antiguo dejaba desactualizado.
- Se consultan referencias y tallas publicadas, excluyendo referencias `ZSE%` y las que contienen `*` como la API anterior. No aparecen tallas sin registros de codificación en los puntos elegidos.
- Precio y descuento se exportan sin aplicar una fórmula comercial no confirmada. Las imágenes se devuelven como nombres de archivo; no se construyen URLs a partir del encabezado Host.
- `/api/actualizaexistencia.php` pasa por la API protegida; ya no actualiza públicamente `Referencia`.

## Pendientes para activar sincronización

Confirmar puntos de venta, reserva, agrupación de productos, SKU, descuento y URL pública de imágenes. Instalar/configurar una app con permisos de lectura `read_products`, `read_inventory`, `read_locations` para diagnóstico. Obtener token por OAuth si la app pertenece a una organización diferente; el flujo client credentials aplica únicamente cuando app y tienda pertenecen a la misma organización. No se han creado apps ni concedido permisos.

Después: implementar y probar las escrituras de productos e inventario con mapeo persistente de IDs, concurrencia y reintentos; recepción de pedidos con firma HMAC y deduplicación transaccional; despliegue y cron. El archivo `cron/actualizashopify.php` sigue siendo un marcador antiguo, no un sincronizador operativo. Ninguna operación nueva modifica Shopify ni pedidos locales.

## Pruebas

`php tests/shopify.php`: autenticación, límites, paginación sin saltos, separación de referencias, consolidación de puntos autorizados, ceros/negativos, inyección SQL y errores GraphQL/HTTP. Usa SQLite en memoria y transporte Shopify simulado. Falta validar esquema/datos en MySQL real y acceso Shopify autenticado.

Documentación oficial: [autenticación](https://shopify.dev/docs/apps/build/authentication-authorization), [apps independientes](https://shopify.dev/docs/apps/build/authentication-authorization/authenticate-standalone-apps), [client credentials](https://shopify.dev/docs/apps/build/authentication-authorization/client-credentials-grant).
