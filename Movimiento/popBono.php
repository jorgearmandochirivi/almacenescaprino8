<?php
include("../admin/config.inc.php");
if (!class_exists('PdfModern')) {
	require_once(__DIR__ . "/../admin/lib/PdfModern.php");
}
require_once $libdir . 'codigobarras.php';

$id_bonos = array_filter(array_map('intval', explode('|', $_GET['id'] ?? '')));
$ruta_logo = __DIR__ . '/../images/cabezote actual-01.png';
$logo = is_readable($ruta_logo) ? 'data:image/png;base64,' . base64_encode(file_get_contents($ruta_logo)) : '';

if (empty($id_bonos)) {
	http_response_code(400);
	exit('Debe indicar al menos un bono.');
}

// El correo usa una URL dinámica del código de barras y ya no depende de archivos en disco.
if (!empty($_GET['correo']) && !empty($_GET['id_cliente'])) {
	envia_bono_cliente((int) $_GET['id_cliente'], $id_bonos);
}

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<style>
		body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #000; }
		.bono { width: 500px; margin: 0 auto; }
		.bono + .bono { page-break-before: always; }
		.texto { font-size: 10px; }
	</style>
</head>
<body>
<?php foreach ($id_bonos as $id_bono): ?>
	<?php
	$qid = db_query("SELECT * FROM BonoFidelizacion WHERE IDBonoFidelizacion = '$id_bono'");
	$r = db_fetch_object($qid);
	if (!$r) {
		continue;
	}

	$valor_numerico_bono = $r->IDBonoFidelizacion * 21 + 133;
	$codigo_barras = generar_codigo_barras_base64('BonoCaprino-' . $valor_numerico_bono, $libdir, 30);
	?>
	<table class="bono">
		<tr>
			<td colspan="2"><?php if ($logo !== ''): ?><img src="<?php echo $logo; ?>" alt="Caprino" width="215" height="107"><?php endif; ?></td>
		</tr>
		<tr><td class="texto">Número</td><td class="texto"><?php echo $r->IDBonoFidelizacion; ?></td></tr>
		<tr>
			<td class="texto">Fecha de vencimiento</td>
			<td class="texto"><?php echo $r->FechaVencimiento; ?><br>(<?php echo get_field('ParametroFidelizacion', 'Valor', 'IDParametroFidelizacion', 3); ?> meses a partir de hoy)</td>
		</tr>
		<tr><td class="texto">Valor bono</td><td class="texto"><?php echo '$' . number_format($r->Valor, 2); ?></td></tr>
		<tr><td colspan="2" class="texto">&nbsp;</td></tr>
		<tr>
			<td colspan="2" class="texto">
				Nombre cliente: <?php echo get_field('Cliente', 'Nombre', 'IDCliente', $r->IDCliente) . ' ' . get_field('Cliente', 'Apellido', 'IDCliente', $r->IDCliente); ?><br>
				Documento cliente: <?php echo get_field('Cliente', 'Cedula', 'IDCliente', $r->IDCliente); ?><br><br><br>
				Firma cliente: ____________________<br><br>
				Vendedor redime bono: ____________________<br><br>
				Firma vendedor: ____________________<br><br>
				<?php if ($codigo_barras !== false): ?><img src="<?php echo $codigo_barras; ?>" width="500"><?php endif; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="texto">
				<ul>
					<li>Cada bono tiene un código único asociado a tu documento de identidad y solo podrá redimirse una vez.</li>
					<li>Puedes utilizarlo como medio de pago en tiendas Caprino o en la tienda virtual.</li>
					<li>Debes diligenciar el bono con tus datos y firma para hacerlo válido.</li>
					<li>Si no lo usas durante su vigencia, se vencerá y no podrá utilizarse.</li>
					<li>Redimible hasta por el 50% del valor de compras superiores a $100.000.</li>
					<li>No acumulable con otras promociones.</li>
				</ul>
			</td>
		</tr>
	</table>
<?php endforeach; ?>
</body>
</html>
<?php
$html = ob_get_clean();
$pdf = PdfModern::render($html, 'A4');

if ($pdf === false) {
	http_response_code(500);
	exit('No fue posible generar el PDF de los bonos.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="Bonos-' . implode('-', $id_bonos) . '.pdf"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;
?>
