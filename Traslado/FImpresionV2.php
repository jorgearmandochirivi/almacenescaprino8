<?php
include("../admin/config.inc.php");
if (!class_exists('PdfModern')) {
	require_once(__DIR__ . "/../admin/lib/PdfModern.php");
}

$datos = Verifica_SesionCliente();
$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
$ID_Usuario = $datos["IDUsuario"];
$Nivel = $datos["Nivel"];
$IVA = $datos["IVA"];
$IDPuntoVenta = $datos["IDPuntoVenta"];

$TitleMod = "Traslado";
$Table = "Traslado";
$Key = "IDTraslado";

$qid = db_query(" SELECT * FROM Traslado WHERE IDTraslado = '$id' AND IDPuntoVentaOrigen = '$idpunto' ");
$r = db_fetch_object($qid);

$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
$qry_puntoventa = db_query($sql_puntoVenta);
$r_puntoventa = db_fetch_object($qry_puntoventa);

$filedir = $dirroot . "/files/Traslados/";
$name = "Traslado" . $r_puntoventa->Codigo . $r->IDTraslado . ".html";
$namePDF = "Traslado" . $r_puntoventa->Codigo . $r->IDTraslado . ".pdf";
$file = "$filedir$name";
$filepdf = "$filedir$namePDF";

if (!is_dir($filedir)) {
	mkdir($filedir, 0775, true);
}

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<style>
		@page {
			size: 74mm 220mm;
			margin: 0;
		}

		html {
			margin: 0;
			padding: 0;
		}

		body {
			font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
			font-size: 9pt;
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

		td,
		th {
			overflow-wrap: break-word;
			word-wrap: break-word;
		}

		.texto {
			font-size: 8pt;
			line-height: 1.15;
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

		.border-top {
			border-top: 1px dotted #000;
			margin-top: 3px;
			padding-top: 3px;
		}

		.item-table th,
		.item-table td {
			font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
			font-size: 7.8pt;
			font-weight: normal;
			line-height: 1.1;
			padding: 1px 0.5mm;
			vertical-align: top;
		}

		.item-table th {
			font-size: 7pt;
			font-weight: bold;
			line-height: 1.1;
			border-bottom: 1px solid #000;
		}

		.item-table .talla,
		.item-table .cantidad,
		.item-table .tarjeta {
			text-align: center;
		}
	</style>
</head>
<body>
	<div class="center bold texto">
		TRASLADO No. <?php echo $r_puntoventa->Codigo . $r->IDTraslado; ?>
	</div>

	<div class="texto border-top">
		<strong>Almacen Destino:</strong> <?= get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r->IDPuntoVentaDestino); ?><br>
		<strong>Almacen Origen:</strong> <?= get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r->IDPuntoVentaOrigen); ?><br>
		<strong>Fecha Traslado:</strong> <?= $r->Fecha ?><br>
		<strong>Realizado por:</strong> <?= get_field("Empleado", "Nombre", "IDEmpleado", $r->IDEmpleado) . " " . get_field("Empleado", "Apellidos", "IDEmpleado", $r->IDEmpleado); ?><br>
		<strong>Estado:</strong> <?= get_field("EstadoTraslado", "Descripcion", "IDEstadoTraslado", $r->IDEstadoTraslado); ?>
	</div>

	<?php if (!empty($r->Observaciones)): ?>
		<div class="texto border-top">
			<strong>Observaciones:</strong><br>
			<?= nl2br(htmlentities($r->Observaciones, ENT_QUOTES, 'UTF-8')); ?>
		</div>
	<?php endif; ?>

	<table class="item-table border-top">
		<tr>
			<th style="width: 33%;">Referencia</th>
			<th class="talla" style="width: 15%;">Talla</th>
			<th class="cantidad" style="width: 12%;">Cant</th>
			<th class="tarjeta" style="width: 40%;">Tarjeta</th>
		</tr>
		<?php
		$sql_detalle = " SELECT * FROM DetalleTraslado WHERE $Key = '$r->IDTraslado' AND IDPuntoVentaOrigen = '$r->IDPuntoVentaOrigen' ";
		$query_detalle = db_query($sql_detalle);
		$cantidad_total = 0;
		while ($r_detalle = db_fetch_object($query_detalle)) {
			$PuntoVentaReferencia = get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica);
			$Talla = get_field("CodificacionEspecifica", "IDTalla", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica);
			$cantidad_total += (int)$r_detalle->Cantidad;
		?>
			<tr>
				<td><?= get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $PuntoVentaReferencia)); ?></td>
				<td class="talla"><?= get_field("Talla", "Descripcion", "IDTalla", $Talla); ?></td>
				<td class="cantidad"><?= (int)$r_detalle->Cantidad; ?></td>
				<td class="tarjeta"><?= $r_detalle->NumeroTarjeta; ?></td>
			</tr>
		<?php } ?>
		<tr class="bold">
			<td colspan="2">TOTAL</td>
			<td class="cantidad"><?= $cantidad_total; ?></td>
			<td></td>
		</tr>
	</table>
</body>
</html>
<?php
$html = ob_get_clean();
$fw = fopen($file, "w");
if ($fw !== false) {
	fputs($fw, $html);
	fclose($fw);
}

PdfModern::generate($html, $filepdf, [74, 220]);
echo "<script>window.location.href='/admin/files/Traslados/" . $namePDF . "';</script>";
?>
