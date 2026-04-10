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

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<style>
		@page {
			size: 76mm 180mm;
			margin: 0;
		}

		body {
			font-family: Arial, sans-serif;
			font-size: 8pt;
			margin: 0;
			padding: 4mm;
			width: 72mm;
			box-sizing: border-box;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 4px;
		}

		.texto {
			font-size: 7pt;
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
			margin-top: 5px;
			padding-top: 5px;
		}

		.item-table th,
		.item-table td {
			font-size: 6.5pt;
			padding: 2px 0;
			vertical-align: top;
		}

		.item-table th {
			text-align: left;
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
			<th>Referencia</th>
			<th class="talla">Talla</th>
			<th class="cantidad">Cant</th>
			<th class="tarjeta">Tarjeta</th>
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
fputs($fw, $html);
fclose($fw);

PdfModern::generate($html, $filepdf, [76, 180]);
echo "<script>window.location.href='/admin/files/Traslados/" . $namePDF . "';</script>";
?>
