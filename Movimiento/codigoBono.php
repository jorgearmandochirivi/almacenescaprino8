<?php
include("../admin/config.inc.php");
require_once $libdir . 'codigobarras.php';

$id_bono = (int) ($_GET['id'] ?? 0);
if ($id_bono <= 0 || !get_field('BonoFidelizacion', 'IDBonoFidelizacion', 'IDBonoFidelizacion', $id_bono)) {
	http_response_code(404);
	exit;
}

$codigo_barras = generar_codigo_barras_base64('BonoCaprino-' . ($id_bono * 21 + 133), $libdir, 30);
if ($codigo_barras === false) {
	http_response_code(500);
	exit;
}

header('Content-Type: image/png');
echo base64_decode(substr($codigo_barras, strpos($codigo_barras, ',') + 1));
?>
