<?php
include("../admin/config.inc.php");
if (!class_exists('PdfModern')) {
	require_once(__DIR__ . "/../admin/lib/PdfModern.php");
}

$datos = Verifica_SesionCliente();
$IDPuntoVentaSesion = $datos["IDPuntoVenta"];
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$idpunto = isset($_GET["idpunto"]) ? (int)$_GET["idpunto"] : (int)$IDPuntoVentaSesion;

$qid = db_query("SELECT * FROM Movimiento WHERE IDMovimiento = '$id' AND IDPuntoVenta = '$idpunto'");
$r = db_fetch_object($qid);

$filedir = $dirroot . "/filesotros/Salida/";
if (!is_dir($filedir)) {
	mkdir($filedir, 0775, true);
}

$name = "Salida" . $id . "_" . $idpunto . ".html";
$namePDF = "Salida" . $id . "_" . $idpunto . ".pdf";
$file = "$filedir$name";
$filepdf = "$filedir$namePDF";
$ruta_pdf = "/admin/filesotros/Salida/" . $namePDF;

ob_start();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Imprimir Salida</title>
	<style>
		@page {
			size: 74mm 190mm;
			margin: 0;
		}

		html {
			margin: 0;
			padding: 0;
		}

		body {
			font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
			font-size: 7pt;
			margin: 0 0 0 6mm;
			padding: 0 2mm 1mm 1mm;
			width: 62mm;
			box-sizing: border-box;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 3px;
			table-layout: fixed;
		}

		td {
			overflow-wrap: break-word;
			word-wrap: break-word;
			vertical-align: top;
			padding: 1px 0;
		}

		.center {
			text-align: center;
		}

		.right {
			text-align: right;
		}

		.bold {
			font-weight: bold;
		}

		.texto {
			font-size: 6.2pt;
			line-height: 1.1;
		}

		.border-top {
			border-top: 1px dotted #000;
			margin-top: 3px;
			padding-top: 3px;
		}

		.item-table td {
			font-size: 5.4pt;
			line-height: 1.05;
			padding: 1px 0.25mm;
			text-align: center;
		}

		.actions {
			margin-top: 8px;
			text-align: center;
		}

		.actions button,
		.actions a {
			font-size: 10px;
			margin: 0 3px;
		}

		@media print {
			.actions {
				display: none;
			}
		}
	</style>
</head>

<body>
	<div class="center bold">
		SALIDA No. <?php echo $r->IDMovimiento; ?>
	</div>

	<table class="texto border-top">
		<tr>
			<td class="bold">Fecha</td>
			<td><?php echo $r->Fecha; ?></td>
		</tr>
		<tr>
			<td class="bold">Movimiento</td>
			<td><?php echo get_field("TipoMovimiento", "NombreMovimiento", "IDTipoMovimiento", $r->IDTipoMovimiento); ?></td>
		</tr>
		<tr>
			<td class="bold">Empleado</td>
			<td><?php echo get_field("Empleado", "Nombre", "IDEmpleado", $r->IDEmpleado); ?></td>
		</tr>
		<tr>
			<td class="bold">Punto Venta</td>
			<td><?php echo get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r->IDPuntoVenta); ?></td>
		</tr>
	</table>

	<div class="texto border-top">
		<span class="bold">Observaciones:</span><br>
		<?php echo $r->Observaciones; ?>
	</div>

	<table class="item-table border-top">
		<tr class="bold">
			<td style="width: 45%;">Referencia</td>
			<td style="width: 25%;">Talla</td>
			<td style="width: 30%;">Cantidad</td>
		</tr>
		<?php
		$total_cantidad = 0;
		$sql_detalle = "SELECT DM.Cantidad, R.Numero, T.Descripcion AS Talla
			FROM DetalleMovimiento DM
			INNER JOIN PuntoVentaReferencia PVR ON DM.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
			INNER JOIN Referencia R ON PVR.IDReferencia = R.IDReferencia
			INNER JOIN Talla T ON DM.IDTalla = T.IDTalla
			WHERE DM.IDMovimiento = '$r->IDMovimiento'
			AND DM.IDPuntoVenta = '$r->IDPuntoVenta'
			ORDER BY R.Numero, T.Descripcion";
		$qry_detalle = db_query($sql_detalle);
		while ($detalle = db_fetch_object($qry_detalle)) {
			$total_cantidad += (int)$detalle->Cantidad;
		?>
			<tr>
				<td><?php echo $detalle->Numero; ?></td>
				<td><?php echo $detalle->Talla; ?></td>
				<td class="right"><?php echo (int)$detalle->Cantidad; ?></td>
			</tr>
		<?php } ?>
		<tr class="bold border-top">
			<td colspan="2">TOTAL</td>
			<td class="right"><?php echo $total_cantidad; ?></td>
		</tr>
	</table>

	<div class="actions">
		<button type="button" onclick="window.print();">Imprimir</button>
		<a href="<?php echo $ruta_pdf; ?>">PDF</a>
	</div>
</body>

</html>
<?php
$html = ob_get_clean();
$fw = fopen($file, "w");
fputs($fw, $html);
fclose($fw);

PdfModern::generate($html, $filepdf, [74, 190]);
echo $html;
?>
