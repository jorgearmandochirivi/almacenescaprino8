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

$filedir = $dirroot . "/files/facturas/";
$name = "Factura" . $r_puntoventa->Codigo . $r->NumeroFactura . ".html";
$namePDF = "Factura" . $r_puntoventa->Codigo . $r->NumeroFactura . ".pdf";
$file = "$filedir$name";
$filepdf = "$filedir$namePDF";

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
			size: 60mm 200mm;
			margin: 0;
		}

		body {
			font-family: DejaVu Sans Condensed, DejaVu Sans, Arial, sans-serif;
			font-size: 7pt;
			margin: 0 0 0 4mm;
			padding: 2mm 0.5mm 2mm 1mm;
			width: 55mm;
			box-sizing: border-box;
		}

		.texto {
			font-size: 6.2pt;
			line-height: 1.1;
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

		.item-table td {
			font-size: 4.9pt;
			padding: 1px 0;
			vertical-align: top;
		}

		.footer-text {
			font-size: 6pt;
			line-height: 1.12;
			text-align: left;
		}

		.item-table .head td {
			font-size: 4.5pt;
			line-height: 1.05;
			padding-bottom: 2px;
		}

		.num {
			font-family: DejaVu Sans Condensed, DejaVu Sans, Arial, sans-serif;
			font-weight: bold;
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

	<div class="center texto" style="margin-top: 10px;">
		ESTE DOCUMENTO NO ES FACTURA DE VENTA,<br>
		NOTA CREDITO O DOCUMENTO EQUIVALENTE
	</div>

	<div class="center bold" style="margin-top: 10px;">
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

	<table class="item-table border-top" style="margin-top: 4px;">
		<tr class="bold head">
			<td style="width: 23%;">Ref</td>
			<td class="right num" style="width: 14%;">Vr U</td>
			<td class="right num" style="width: 8%;">Can</td>
			<td class="right num" style="width: 9%;">Dto</td>
			<td class="right num" style="width: 15%;">Vr D</td>
			<td class="right num" style="width: 9%;">D2</td>
			<td class="right num" style="width: 22%;">Total</td>
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
				<td><?= $ref ?><br><small><?= get_field("Talla", "Descripcion", "IDTalla", get_field("CodificacionEspecifica", "IDTalla", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)); ?></small></td>
				<td class="right num"><?= number_format($precio_consultado) ?></td>
				<td class="right num"><?= $r_detalle->Cantidad ?></td>
				<td class="right num"><?= number_format($r_detalle->DescuentoRef) ?>%</td>
				<td class="right num"><?= number_format($r_detalle->PrecioU) ?></td>
				<td class="right num"><?= number_format($r_detalle->DescuentoPar) ?>%</td>
				<td class="right num"><?= number_format($valorsin) ?></td>
			</tr>
		<?php
			$Movimiento = get_field("Referencia", "IDMovimiento", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)));
			if (!empty($Movimiento)) $segunda = 1;
			$Saldo = get_field("Referencia", "Saldo", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)));
			if ($Saldo == "S") $segunda = 1;
		}
		?>
	</table>

	<table class="texto border-top">
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
		<tr class="bold">
			<td>Valor Neto</td>
			<td class="right num"><?= number_format($r->ValorTotal) ?></td>
		</tr>
	</table>

	<div class="texto bold" style="margin-top: 5px;">FORMA DE PAGO</div>
	<table class="texto">
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

	<div class="footer-text border-top" style="margin-top: 10px;">
		<?php
		$sql_mensje = "SELECT Mensaje FROM Mensaje WHERE Publicar = 'S' AND Segunda = 'N' AND FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ORDER BY RAND() LIMIT 1";
		$qry_mensaje = db_query($sql_mensje);
		if ($r_mensaje = db_fetch_object($qry_mensaje)) echo nl2br($r_mensaje->Mensaje) . "<br>";

		if ($segunda == 1) {
			$sql_mensje = "SELECT Mensaje FROM Mensaje WHERE Publicar = 'S' AND Segunda = 'S' LIMIT 1";
			$qry_mensaje = db_query($sql_mensje);
			if ($r_mensaje = db_fetch_object($qry_mensaje)) echo nl2br($r_mensaje->Mensaje) . "<br>";
		}
		?>
	</div>

	<?php if (!empty($array_fidelizacion) && $club_suavidad == "S"): ?>
		<div class="footer-text border-top">
			Puntos Disponibles: <?= $array_fidelizacion["puntostotal"] ?><br>
			Puntos Compra: <?= $array_fidelizacion["puntosultimacompra"] ?>
		</div>
	<?php endif; ?>

	<div class="center" style="margin-top: 20px;">
		<?php $ruta_redireccion = "/admin/files/facturas/Factura" . $r_puntoventa->Codigo . $r->NumeroFactura . ".pdf"; ?>
		<a href="<?= $ruta_redireccion ?>">DESCARGAR PDF</a>
	</div>

</body>

</html>
<?php
$html = ob_get_clean();
$fw = fopen($file, "w");
fputs($fw, $html);
fclose($fw);

echo $html;

PdfModern::generate($html, $filepdf, [60, 200]);
echo "<script>window.location.href='" . $ruta_redireccion . "';</script>";
?>
