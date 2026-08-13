<?php
include("../admin/config.inc.php");
if (!class_exists('PdfModern')) {
	require_once(__DIR__ . "/../admin/lib/PdfModern.php");
}
$datos = Verifica_SesionCliente();
$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
$ID_Usuario = $datos["IDUsuario"];
$Nivel =  $datos["Nivel"];
$IVA = $datos["IVA"];
$IDPuntoVenta = $datos["IDPuntoVenta"];

$TitleMod = "Factura";
$Table = "Factura";
$Key = "IDFactura";

$qid = db_query(" SELECT * FROM Factura WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto' ");
$r = db_fetch_object($qid);

$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
$qry_puntoventa = db_query($sql_puntoVenta);
$r_puntoventa = db_fetch_object($qry_puntoventa);

$namePDF = "Factura" . $r_puntoventa->Codigo . $r->NumeroFactura . ".pdf";

$club_suavidad = get_field("Cliente", "ClubSuavidad", "IDCliente", $r->IDCliente);
$array_fidelizacion = fid_get_puntos($r->IDCliente, $id);

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
			font-size: 8pt;
			margin: 0 0 0 6mm;
			padding: 0 2mm 1mm 1mm;
			width: 62mm;
			box-sizing: border-box;
		}

		.texto {
			font-size: 7pt;
			line-height: 1.08;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 2px;
			table-layout: fixed;
		}

		td {
			overflow-wrap: break-word;
			word-wrap: break-word;
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
			margin-top: 2px;
			padding-top: 2px;
		}

		.item-table td {
			font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
			font-size: 6.8pt;
			font-weight: normal;
			line-height: 1.05;
			padding: 0.5px 0.35mm;
			text-align: center;
			vertical-align: top;
		}

		.footer-text {
			font-size: 6.8pt;
			line-height: 1.05;
			text-align: justify;
			text-align-last: left;
			page-break-inside: auto;
		}

		.item-table .head td {
			font-size: 6.4pt;
			font-weight: bold;
			line-height: 1.05;
			padding-bottom: 1px;
		}

		.ref-cell {
			line-height: 1.05;
			white-space: normal;
			overflow-wrap: anywhere;
			word-break: break-word;
			text-align: left;
		}

		.ref-main {
			display: block;
			white-space: normal;
			overflow-wrap: anywhere;
			word-break: break-word;
		}

		.ref-size {
			display: block;
			font-size: 6.3pt;
			margin-top: 0.2mm;
		}

		.num {
			font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
			font-weight: normal;
			white-space: nowrap;
		}
	</style>
</head>

<body>
	<div class="center bold">
		IMACAL SAS<br>
		<?php echo ($r->FechaFactura >= "2019-07-19 00:00:00") ? "En Reorganizacion" : "LTDA En Reorganizacion"; ?><br>
		NIT <?= get_field("NIT", "NIT", "IDNIT", 1); ?><br>
		Régimen común
	</div>

	<div class="center texto" style="margin-top: 6px;">
		ESTE DOCUMENTO NO ES FACTURA DE VENTA, NOTA CREDITO O DOCUMENTO EQUIVALENTE
	</div>

	<div class="center bold" style="margin-top: 6px;">
		APRECIADO CLIENTE:<br>
		LA FACTURA O NOTA CREDITO ELECTRONICA<br>
		SERA ENVIADA POR CORREO
	</div>

	<div class="texto border-top">
		COMPROBANTE DE ENTREGA No. <?php echo $r_puntoventa->Codigo . $r->NumeroFactura ?><br>
		<strong>Almacén:</strong> <?php echo $r_puntoventa->Nombre ?><br>
		<strong>Dirección:</strong> <?= $r_puntoventa->Direccion ?><br>
		<strong>Teléfono:</strong> <?= $r_puntoventa->Telefono ?><br>
		<strong>Vendedor:</strong> <?php echo get_field("Empleado", "Nombre", "IDEmpleado", $r->IDEmpleado) . " " . get_field("Empleado", "Apellidos", "IDEmpleado", $r->IDEmpleado); ?><br>
		<strong>Fecha:</strong> <?= $r->FechaFactura ?>
	</div>

	<div class="texto border-top">
		<strong>Cliente:</strong> <?php echo get_field("Cliente", "CONCAT(Nombre,' ',Apellido)", "IDCliente", $r->IDCliente); ?><br>
		<strong>Documento:</strong> <?php echo get_field("Cliente", "Cedula", "IDCliente", $r->IDCliente); ?>
	</div>

	<table class="item-table border-top" style="margin-top: 2px;">
		<tr class="head">
			<td style="width: 26%;">Ref</td>
			<td class="num" style="width: 14%;">Vr U</td>
			<td class="num" style="width: 7%;">Can</td>
			<td class="num" style="width: 8%;">Dto</td>
			<td class="num" style="width: 15%;">Vr D</td>
			<td class="num" style="width: 7%;">D2</td>
			<td class="num" style="width: 23%;">Total</td>
		</tr>
		<?php
		$sql_detalle = "SELECT * FROM DetalleFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta' ";
		$query_detalle = db_query($sql_detalle);
		$segunda = 0;
		while ($r_detalle = db_fetch_object($query_detalle)) {
			$ref = get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)));
			$precio_consultado = get_field("Precio", "ValorVenta", "IDPrecio", get_field("Referencia", "IDPrecio", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica))));
			if (!$precio_consultado) {
				$precio_consultado = $r_detalle->PrecioU;
			}
			$valorsin = ($r_detalle->ValorU * (1 - ($r_detalle->DescuentoPar / 100))) * $r_detalle->Cantidad;
		?>
			<tr>
				<td class="ref-cell">
					<span class="ref-main"><?= $ref ?><?= !empty($r_detalle->CodigoTarjeta) ? "-" . $r_detalle->CodigoTarjeta : "" ?></span>
					<span class="ref-size"><?= get_field("Talla", "Descripcion", "IDTalla", get_field("CodificacionEspecifica", "IDTalla", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)); ?></span>
				</td>
				<td class="num"><?= number_format($precio_consultado) ?></td>
				<td class="num"><?= $r_detalle->Cantidad ?></td>
				<td class="num"><?= number_format($r_detalle->DescuentoRef) ?>%</td>
				<td class="num"><?= number_format($r_detalle->PrecioU) ?></td>
				<td class="num"><?= number_format($r_detalle->DescuentoPar) ?>%</td>
				<td class="num"><?= number_format($valorsin) ?></td>
			</tr>
		<?php
			$Movimiento = get_field("Referencia", "IDMovimiento", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)));
			if (!empty($Movimiento)) $segunda = 1;
			$Saldo = get_field("Referencia", "Saldo", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)));
			if ($Saldo == "S") $segunda = 1;
		}
		?>
	</table>

	<table class="border-top" style="font-size: 7.4pt; line-height: 1.08;">
		<?php if ($r->ValorBono != "0"): ?>
			<tr>
				<td>Total Factura</td>
				<td class="right num"><?= number_format($r->ValorTotalSinBono) ?></td>
			</tr>
			<tr>
				<td>Bono</td>
				<td class="right num">-<?= number_format($r->ValorBono) ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td>IVA</td>
			<td class="right num"><?= number_format($r->ValorIVA) ?></td>
		</tr>
		<tr class="bold" style="font-size: 8pt;">
			<td>Valor Neto</td>
			<td class="right num"><?= number_format($r->ValorTotal) ?></td>
		</tr>
	</table>

	<div class="bold" style="font-size: 8pt; margin-top: 3px;">FORMA DE PAGO</div>
	<table style="font-size: 7.4pt; line-height: 1.08;">
		<?php
		$sql_formapago = "SELECT * FROM FormaPagoFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta'";
		$query_formapago = db_query($sql_formapago);
		while ($r_formapago = db_fetch_object($query_formapago)) {
			if ($r_formapago->Valor <> 0) {
		?>
				<tr>
					<td><?php echo get_field("FormaPago", "Descripcion", "IDFormaPago", $r_formapago->IDFormaPago) ?></td>
					<td class="right num"><?= number_format($r_formapago->Valor) ?></td>
				</tr>
		<?php }
		} ?>
	</table>

	<div class="footer-text border-top" style="margin-top: 5px;">
		<?php
		$sql_mensje = "SELECT Mensaje FROM Mensaje WHERE Publicar = 'S' AND Segunda = 'N' AND FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ORDER BY RAND() LIMIT 1";
		$qry_mensaje = db_query($sql_mensje);
		if ($r_mensaje = db_fetch_object($qry_mensaje)) echo preg_replace('/\s+/', ' ', trim($r_mensaje->Mensaje)) . "<br>";

		if ($segunda == 1) {
			$sql_mensje = "SELECT Mensaje FROM Mensaje WHERE Publicar = 'S' AND Segunda = 'S' LIMIT 1";
			$qry_mensaje = db_query($sql_mensje);
			if ($r_mensaje = db_fetch_object($qry_mensaje)) echo preg_replace('/\s+/', ' ', trim($r_mensaje->Mensaje)) . "<br>";
		}
		?>
	</div>

	<?php if (!empty($array_fidelizacion) && $club_suavidad == "S"): ?>
		<div class="footer-text border-top">
			Puntos Disponibles: <?= $array_fidelizacion["puntostotal"] ?><br>
			Puntos Compra: <?= $array_fidelizacion["puntosultimacompra"] ?>
		</div>
	<?php endif; ?>

</body>

</html>
<?php
$html = ob_get_clean();
$pdf = PdfModern::render($html, [74, 190]);

if ($pdf === false) {
	http_response_code(500);
	exit('No fue posible generar el PDF de la factura.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $namePDF . '"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;
?>
