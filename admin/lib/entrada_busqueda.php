<?php

// Shared by the paginated list and the spreadsheet export.
function entrada_busqueda_sql(array $params)
{
    $where = array();
    $joins = '';
    $field = $params['field'] ?? '';
    $order = $params['order_by'] ?? 'Fecha';
    $columns = array('Remision' => 'E.Remision', 'NumeroFactura' => 'E.NumeroFactura',
        'NumeroReferencia' => 'R.Numero', 'PuntoVenta.Nombre' => 'PV.Nombre');
    $orders = array('IDEntrada' => 'E.IDEntrada', 'IDPuntoVenta' => 'E.IDPuntoVenta',
        'Remision' => 'E.Remision', 'NumeroFactura' => 'E.NumeroFactura',
        'Referencia.Numero' => 'R.Numero', 'IDTalla' => 'E.IDTalla',
        'Fecha' => 'E.Fecha', 'Cantidad' => 'E.Cantidad');
    if ($field === 'NumeroReferencia' || (int)($params['IDProveedor'] ?? 0) > 0 || $order === 'Referencia.Numero') {
        $joins .= ' LEFT JOIN PuntoVentaReferencia PR ON PR.IDPuntoVentaReferencia = E.IDPuntoVentaReferencia'
            . ' LEFT JOIN Referencia R ON R.IDReferencia = PR.IDReferencia';
    }
    if ($field === 'PuntoVenta.Nombre') {
        $joins .= ' LEFT JOIN PuntoVenta PV ON PV.IDPuntoVenta = E.IDPuntoVenta';
    }
    if (isset($columns[$field]) && trim($params['QryString'] ?? '') !== '') {
        $text = mysqli_real_escape_string($GLOBALS['DB_LINK'], trim($params['QryString']));
        $where[] = $columns[$field] . " LIKE '%" . $text . "%'";
    }
    foreach (array('IDProveedor' => 'R.IDProveedor', 'IDPuntoVenta' => 'E.IDPuntoVenta') as $key => $column) {
        if ((int)($params[$key] ?? 0) > 0) {
            $where[] = $column . ' = ' . (int)$params[$key];
        }
    }
    $dates = array();
    foreach (array('limit1', 'limit2') as $key) {
        $value = trim($params[$key] ?? '');
        if ($value === '') continue;
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new InvalidArgumentException('Seleccione fechas validas con formato AAAA-MM-DD.');
        }
        $dates[$key] = $date;
    }
    if ($field === 'Fecha' && count($dates) !== 2) {
        throw new InvalidArgumentException('Seleccione la fecha inicial y final.');
    }
    if (isset($dates['limit1'], $dates['limit2']) && $dates['limit1'] > $dates['limit2']) {
        throw new InvalidArgumentException('La fecha inicial no puede ser posterior a la fecha final.');
    }
    if (isset($dates['limit1'])) $where[] = "E.Fecha >= '" . $dates['limit1']->format('Y-m-d') . " 00:00:00'";
    // Exclusive next-day boundary includes every time on the selected final day.
    if (isset($dates['limit2'])) $where[] = "E.Fecha < '" . $dates['limit2']->modify('+1 day')->format('Y-m-d') . " 00:00:00'";
    $direction = ($params['in_order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
    return 'SELECT E.* FROM Entrada E' . $joins
        . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
        . ' ORDER BY ' . ($orders[$order] ?? 'E.Fecha') . ' ' . $direction . ', E.IDEntrada ' . $direction;
}

function entrada_html($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function entrada_busqueda_url(array $params, array $changes = array(), $path = '')
{
    $params = array_intersect_key($params, array_flip(array('field', 'QryString', 'IDProveedor',
        'IDPuntoVenta', 'limit1', 'limit2', 'order_by', 'in_order', 'listar')));
    return $path . '?' . http_build_query(array_merge(array('mod' => 'Entrada', 'action' => 'list'), $params, $changes));
}
