# Preparación de la integración Caprino / Shopify

Estado: API de lectura y cliente GraphQL preparados. No está desplegada ni conectada a la tienda.
El 11 de septiembre de 2026 se verificó acceso en navegador a la tienda `kwtj0h-qz`: cuatro productos activos, variantes y stock cero. Esto no proporciona un token Admin API.

## Configurar

1. Copiar `config.example.php` fuera de la raíz servida por Nginx/Apache y del repositorio. Completar credenciales de base de datos (preferiblemente usuario de solo lectura), un `api_token` aleatorio de al menos 32 caracteres, puntos de venta y reserva de stock.
2. En Plesk, guardar la copia como `private-config/caprino-shopify.php` al mismo nivel que `httpdocs`. La API la busca automáticamente fuera de la raíz del proyecto; en este servidor corresponde a `/var/www/vhosts/almacenescaprino.com/private-config/caprino-shopify.php`. No requiere cambiar la configuración PHP del panel. Opcionalmente, definir `CAPRINO_INTEGRATION_CONFIG` en PHP/FPM con otra ruta absoluta: esa variable tiene prioridad y, si apunta a un archivo inexistente, se devuelve un error sin usar la ruta automática. En Docker, montar ese archivo fuera de `/var/www/html`, pasar la variable al servicio PHP y verificar que FPM la recibe. No se cargan archivos `.env` automáticamente.
3. PHP requiere PDO MySQL y cURL. Reconstruir la imagen del proyecto para incorporar `pdo_mysql`: `docker compose build php`.
4. Configurar dominio canónico `*.myshopify.com`, `client_id`, `client_secret` y versión `2026-07`. Con la app instalada en la misma organización, dejar `access_token` vacío: el cliente lo obtiene automáticamente. Como alternativa, se admite un token Admin API manual cuando no se configura el par de credenciales. El identificador de la URL del administrador debe confirmarse contra el dominio canónico.
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

`php tests/shopify-config.php`: carga automática en estructura Plesk, prioridad de la variable, configuración ausente e inválida.

## Autenticación automática

El cliente intercambia `client_id` y `client_secret` por un token usando `client_credentials` sobre HTTPS. Requiere que app y tienda pertenezcan a la misma organización y que la app esté instalada. No implementa OAuth para tiendas de otras organizaciones.

El token se conserva únicamente en memoria durante la vida del cliente PHP; se renueva 60 segundos antes de su vencimiento. Cada nueva petición HTTP a Caprino crea un cliente y solicita su propio token (no hay caché persistente ni cron de renovación). Una respuesta 401 en consultas de lectura provoca una renovación y un solo reintento. No se guardan tokens en el repositorio ni se incluyen respuestas de autenticación en mensajes de error.

Validar en el servidor con la acción `shopify.status` y el encabezado `Authorization: Bearer <api_token_de_caprino>`. Esa acción no requiere conexión MySQL. Un resultado exitoso devuelve la tienda y los permisos; guardar credenciales sin desplegar el cliente actualizado no activa esta funcionalidad.

`php tests/shopify-auth.php`: obtención y reutilización del token, renovación antes de vencimiento, reintento 401 limitado y respuestas de autenticación inválidas. Las pruebas usan credenciales ficticias y transporte simulado.

## Authorization en Apache/PHP-FPM

`api/.htaccess` habilita `CGIPassAuth On` solo para las entradas `caprino.php` y `actualizaexistencia.php`. Requiere Apache 2.4.13+ y permiso AuthConfig en AllowOverride; si Plesk devuelve HTTP 500 por esa directiva, el administrador debe configurarla en el virtual host y retirar la directiva del archivo. Nginx no usa .htaccess.

La API lee HTTP_AUTHORIZATION, sus variantes REDIRECT y getallheaders. Devuelve HTTP 401 con `No se recibió el encabezado Authorization` si no llegó, o `No autorizado` si llegó pero no es válido. Nunca expone tokens ni acepta credenciales en la URL. Pruebas: `php tests/shopify-header.php`.

## Piloto de escritura: Unicentro → Shopify

Implementado exclusivamente para `CG9NCRRO`, `point_of_sale_ids = [1]`, tienda `kwtj0h-qz.myshopify.com` y ubicación `gid://shopify/Location/84605075647`. Los límites están fijados en `InventorySync.php`; no acepta otras referencias ni destinos desde la petición. Requiere `write_inventory` y API `2026-07`. No modifica precios, descuentos, imágenes, productos ni otras referencias.

1. Con el Bearer token de Caprino, enviar GET:
   `/api/caprino.php?action=shopify.inventory.preview&Referencia=CG9NCRRO`
2. Revisar `response.variants`: `changeFromQuantity` es lo actual en Shopify y `quantity` lo disponible en Caprino. La vista previa no escribe. Copiar `response.plan` completo.
3. Dentro de diez minutos, enviar POST a `/api/caprino.php`, Body → raw → JSON:

```json
{"action":"shopify.inventory.apply","plan":"PEGAR_EL_PLAN_COMPLETO"}
```

Conservar Authorization. El plan está firmado; no editarlo. `response.applied = true` confirma aceptación de Shopify. `response.verified = true` confirma lectura posterior coincidente. Si `verified` es falso, la escritura fue aceptada pero la verificación falló o el inventario cambió después; revisar Shopify antes de iniciar otro plan.

Se validan siete SKU exactos y únicos, seguimiento de inventario y activación en el destino. Una talla ausente, un SKU duplicado o cambios de stock Caprino posteriores a la vista previa bloquean el envío. Shopify comprueba `changeFromQuantity` y rechaza datos obsoletos. La misma vista previa conserva la misma clave `@idempotent` al repetir el POST; no crea un ajuste nuevo por reintentar. Si hay timeout, repetir con el MISMO plan mientras siga vigente. Si vence con resultado incierto, comprobar primero inventario en Shopify.

Esta es una carga manual piloto, no una tarea periódica. Antes de automatizarla se deben integrar las ventas/pedidos Shopify con las existencias de Caprino: generar nuevos planes sin descontar ventas podría reponer unidades ya vendidas. No hay transacción distribuida entre MySQL y Shopify; el chequeo de Caprino ocurre inmediatamente antes del envío y Shopify protege su propia concurrencia.

Pruebas: `php tests/shopify-sync.php`, con transporte simulado (vista previa sin escrituras, envío y verificación, repetición idempotente, planes alterados/vencidos, concurrencia, SKU duplicado, inventario sin seguimiento y límites del piloto). La prueba real requiere desplegar y ejecutar los pasos anteriores.

Referencias oficiales: [cantidades y concurrencia](https://shopify.dev/docs/api/admin-graphql/latest/input-objects/inventoryquantityinput), [idempotencia](https://shopify.dev/docs/api/usage/implementing-idempotency).
