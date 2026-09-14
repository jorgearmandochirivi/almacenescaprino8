<?php
require_once __DIR__ . '/../admin/lib/entrada_busqueda.php';
// Temporary fixtures only: no application records are read or changed.
$db = new mysqli(getenv('TEST_DB_HOST') ?: 'mysql', getenv('TEST_DB_USER') ?: 'caprino', getenv('TEST_DB_PASSWORD') ?: '123456', 'caprino');
$GLOBALS['DB_LINK'] = $db;
$db->query('CREATE TEMPORARY TABLE Entrada (IDEntrada INT, IDPuntoVentaReferencia INT, IDPuntoVenta INT, Fecha DATETIME, Remision VARCHAR(30), NumeroFactura VARCHAR(30), IDTalla INT, Cantidad INT)');
$db->query('CREATE TEMPORARY TABLE PuntoVentaReferencia (IDPuntoVentaReferencia INT, IDReferencia INT)');
$db->query('CREATE TEMPORARY TABLE Referencia (IDReferencia INT, Numero VARCHAR(30), IDProveedor INT)');
$db->query('CREATE TEMPORARY TABLE PuntoVenta (IDPuntoVenta INT, Nombre VARCHAR(30))');
$db->query("INSERT INTO Referencia VALUES (1, 'VT1BCRNE', 7), (2, 'OTRA', 8)");
$db->query('INSERT INTO PuntoVentaReferencia VALUES (1,1), (2,2)');
$db->query("INSERT INTO PuntoVenta VALUES (1,'Junin')");
foreach (array('2026-08-31 23:59:59','2026-09-01 00:00:00','2026-09-30 23:59:59','2026-10-01 00:00:00') as $i => $date) {
    $db->query("INSERT INTO Entrada VALUES (" . ($i+1) . ",1,1,'$date','REM','FACT',1,1)");
}
$db->query("INSERT INTO Entrada VALUES (5,2,1,'2026-09-15 12:00:00','REM','FACT',1,1)");
function check($condition, $label) {
    if (!$condition) throw new RuntimeException($label);
    echo "OK: $label\n";
}
function ids($params) {
    global $db;
    return array_column($db->query(entrada_busqueda_sql($params))->fetch_all(MYSQLI_ASSOC), 'IDEntrada');
}
$range = array('limit1'=>'2026-09-01','limit2'=>'2026-09-30','order_by'=>'Fecha','in_order'=>'ASC');
check(ids($range) == array(2,5,3), 'Date-only range includes the full last day');
check(ids($range + array('field'=>'Fecha')) == array(2,5,3), 'Explicit Fecha without search text');
check(ids($range + array('field'=>'NumeroReferencia','QryString'=>'VT1BCRNE')) == array(2,3), 'Reference respects month');
check(ids($range + array('field'=>'NumeroReferencia','QryString'=>'VT1BCRNE','IDProveedor'=>8)) == array(), 'Reference and supplier combine');
check(ids($range + array('IDProveedor'=>7)) == array(2,3), 'Supplier respects month');
check(ids($range + array('field'=>'PuntoVenta.Nombre','QryString'=>'Junin')) == array(2,5,3), 'Point name respects month');
check(ids($range + array('field'=>'NumeroReferencia','QryString'=>"' OR 1=1 --")) == array(), 'Search text is escaped');
foreach (array(array('limit1'=>'2026-02-30'),array('limit1'=>'2026-10-01','limit2'=>'2026-09-01'),array('field'=>'Fecha')) as $bad) {
    try { entrada_busqueda_sql($bad); throw new RuntimeException('Invalid dates accepted'); }
    catch (InvalidArgumentException $e) { echo "OK: Invalid range rejected\n"; }
}
parse_str(parse_url(entrada_busqueda_url($range + array('offset'=>50,'field'=>'NumeroReferencia','QryString'=>'A&B'),array('order_by'=>'Cantidad')),PHP_URL_QUERY),$url);
check($url['limit1']===$range['limit1'] && $url['limit2']===$range['limit2'] && $url['QryString']==='A&B' && !isset($url['offset']), 'Sorting preserves filters and resets page');
echo "All entry search checks passed.\n";
