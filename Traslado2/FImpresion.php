<?php
include("../admin/config.inc.php");
$datos = Verifica_SesionCliente();
$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
$ID_Usuario = $datos["IDUsuario"];
$Nivel =  $datos["Nivel"];
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
			size: 60mm 150mm;
			margin: 0;
		}

		body {
			font-family: Arial, sans-serif;
			font-size: 8pt;
			margin: 0;
			padding: 5mm;
			width: 50mm;
		}

		.texto {
			font-size: 7.5pt;
			line-height: 1.2;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 5px;
		}

		.center {
			text-align: center;
		}

		.bold {
			font-weight: bold;
		}

		.border-top {
			border-top: 1px dotted #000;
			margin-top: 5px;
			padding-top: 5px;
		}

		.item-table td {
			font-size: 7pt;
			padding: 2px 0;
		}
	</style>
</head>

<body>
	<div class="center bold texto">
		TRASLADO No. <?php echo $r_puntoventa->Codigo . $r->IDTraslado; ?>
	</div>

	<div class="texto border-top">
		<strong>Origen:</strong> <?= get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r->IDPuntoVentaOrigen); ?><br>
		<strong>Destino:</strong> <?= get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r->IDPuntoVentaDestino); ?><br>
		<strong>Fecha:</strong> <?= $r->Fecha ?><br>
		<strong>Estado:</strong> <?= get_field("EstadoTraslado", "Descripcion", "IDEstadoTraslado", $r->IDEstadoTraslado); ?>
	</div>

	<?php if ($r->Observaciones): ?>
		<div class="texto border-top">
			<strong>Obs:</strong> <?= $r->Observaciones ?>
		</div>
	<?php endif; ?>

	<table class="item-table border-top" style="margin-top: 10px;">
		<tr class="bold">
			<td>Referencia</td>
			<td style="text-align: center;">Talla</td>
			<td style="text-align: right;">Cant</td>
		</tr>
		<?php
		$sql_detalle = " SELECT * FROM DetalleTraslado WHERE $Key = '$r->IDTraslado' AND IDPuntoVentaOrigen = '$r->IDPuntoVentaOrigen' ";
		$query_detalle = db_query($sql_detalle);
		$total_cantidad = 0;
		while ($r_detalle = db_fetch_object($query_detalle)) {
			$PuntoVentaReferencia = get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica);
			$Talla = get_field("CodificacionEspecifica", "IDTalla", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica);
			$total_cantidad += $r_detalle->Cantidad;
		?>
			<tr>
				<td><?= get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $PuntoVentaReferencia)); ?></td>
				<td style="text-align: center;"><?= get_field("Talla", "Descripcion", "IDTalla", $Talla) ?></td>
				<td style="text-align: right;"><?= $r_detalle->Cantidad ?></td>
			</tr>
		<?php } ?>
		<tr class="bold border-top">
			<td colspan="2">TOTAL</td>
			<td style="text-align: right;"><?= $total_cantidad ?></td>
		</tr>
	</table>

	<div class="center" style="margin-top: 20px;">
		<a href="/admin/files/Traslados/Traslado<?php echo $r_puntoventa->Codigo . $r->IDTraslado; ?>.pdf">DESCARGAR PDF</a>
	</div>

</body>

</html>
<?php
$html = ob_get_clean();
$fw = fopen($file, "w");
fputs($fw, $html);
fclose($fw);

echo $html;
PdfModern::generate($html, $filepdf, [60, 150]);
?>