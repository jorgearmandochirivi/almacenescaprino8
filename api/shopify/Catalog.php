<?php
declare(strict_types=1);

final class CaprinoCatalog
{
    public function __construct(private PDO $db, private array $config) {}

    public static function variant(array $row, int $reserve): array
    {
        return [
            'sku' => trim((string) $row['Numero']) . '-' . trim((string) $row['Talla']),
            'reference_id' => (int) $row['IDReferencia'],
            'reference' => $row['Numero'],
            'size_id' => (int) $row['IDTalla'],
            'size' => $row['Talla'],
            'color' => $row['Color'],
            'price' => $row['ValorVenta'] === null ? null : number_format((float) $row['ValorVenta'], 2, '.', ''),
            'discount' => $row['Descuento'],
            'stock' => (int) $row['Existencias'],
            'available' => max(0, (int) $row['Existencias'] - $reserve),
        ];
    }

    public function products(string $reference, int $page, int $limit): array
    {
        $stores = $this->config['point_of_sale_ids'] ?? [];
        if (!$stores || !is_array($stores)) {
            throw new RuntimeException('Configurar point_of_sale_ids antes de consultar inventario');
        }
        $stores = array_unique(array_map(fn($id) => integrationInteger($id, 1, PHP_INT_MAX, 'point_of_sale_ids'), $stores));
        $reserve = integrationInteger($this->config['stock_reserve'] ?? 0, 0, PHP_INT_MAX, 'stock_reserve');
        $filter = "Publicar = 'S' AND Numero NOT LIKE 'ZSE%' AND Numero NOT LIKE '%*%'";
        $params = [];
        if ($reference !== '') {
            $filter .= ' AND Numero = ?';
            $params[] = $reference;
        }
        $offset = ($page - 1) * $limit;
        $query = $this->db->prepare("SELECT IDReferencia, Numero, Nombre, DescripcionCorta, DescripcionLarga,
            FotoWeb1, FotoWeb2, FotoWeb3, FotoWeb4 FROM Referencia WHERE $filter
            ORDER BY IDReferencia LIMIT " . ($limit + 1) . " OFFSET $offset");
        $query->execute($params);
        $products = $query->fetchAll();
        $hasMore = count($products) > $limit;
        $products = array_slice($products, 0, $limit);
        $result = [];
        foreach ($products as $product) {
            $query = $this->db->prepare('SELECT r.IDReferencia, r.Numero, t.IDTalla, t.Nombre AS Talla,
                c.DescripcionLarga AS Color, p.ValorVenta, p.Descuento, SUM(ce.Existencias) AS Existencias
                FROM Referencia r
                JOIN PuntoVentaReferencia pv ON pv.IDReferencia = r.IDReferencia
                JOIN CodificacionEspecifica ce ON ce.IDPuntoVentaReferencia = pv.IDPuntoVentaReferencia
                JOIN Talla t ON t.IDTalla = ce.IDTalla
                LEFT JOIN Color c ON c.IDColor = r.IDColor
                LEFT JOIN Precio p ON p.IDPrecio = r.IDPrecio
                WHERE r.IDReferencia = ? AND t.Publicar = \'S\' AND pv.IDPuntoVenta IN (' . implode(',', $stores) . ')
                GROUP BY r.IDReferencia, r.Numero, t.IDTalla, t.Nombre, c.DescripcionLarga, p.ValorVenta, p.Descuento
                ORDER BY t.IDTalla');
            $query->execute([$product['IDReferencia']]);
            $variants = array_map(fn($row) => self::variant($row, $reserve), $query->fetchAll());
            $result[] = ['reference_id' => (int) $product['IDReferencia'], 'reference' => $product['Numero'],
                'name' => $product['Nombre'], 'description' => $product['DescripcionLarga'],
                'short_description' => $product['DescripcionCorta'],
                'image_files' => array_values(array_filter([$product['FotoWeb1'], $product['FotoWeb2'], $product['FotoWeb3'], $product['FotoWeb4']])),
                'variants' => $variants];
        }
        return ['products' => $result, 'page' => $page, 'per_page' => $limit, 'has_more' => $hasMore];
    }
}
