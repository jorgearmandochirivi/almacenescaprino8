<?php
include("../admin/config.inc.php");
if (!class_exists('PdfModern')) {
	require_once(__DIR__ . "/../admin/lib/PdfModern.php");
}

$datos = Verifica_SesionCliente();
$IDPuntoVenta = $datos["IDPuntoVenta"];

$qid = db_query(" SELECT * FROM Credito WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto'");
$r = db_fetch_object($qid);

$sql_cuota = " SELECT * FROM CreditoCuota WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto' AND IDCuota = '$idcuota' ";
$qry_cuota = db_query($sql_cuota);
$r_cuota = db_fetch_object($qry_cuota);

$sql_factura = " SELECT * FROM Factura WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto'";
$qry_factura = db_query($sql_factura);
$r_factura = db_fetch_object($qry_factura);

$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
$qry_puntoventa = db_query($sql_puntoVenta);
$r_puntoventa = db_fetch_object($qry_puntoventa);

$sql_cuotas = " SELECT count(*) as numero FROM CreditoCuota WHERE IDFactura = '$r_cuota->IDFactura' AND IDPuntoVenta = '$r_cuota->IDPuntoVenta' AND FechaPago = '0000-00-00 00:00:00' ";
$qry_cuotas = db_query($sql_cuotas);
$r_cuotas = db_fetch_object($qry_cuotas);

$cliente = get_field("Cliente", "CONCAT(Nombre,' ',Apellido)", "IDCliente", $r->IDCliente);
$documento = get_field("Cliente", "Cedula", "IDCliente", $r->IDCliente);
$almacen_pago = get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r_cuota->IDPuntoVentaPago);
if (empty($almacen_pago)) {
	$almacen_pago = $r_puntoventa->Nombre;
}

$namePDF = "FCreditos" . $r_puntoventa->Codigo . $r->NumeroFactura . $idcuota . ".pdf";

ob_start();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Imprimir Recibo</title>
	<style>
		@page {
			size: 74mm 165mm;
			margin: 0;
		}

		html {
			margin: 0;
			padding: 0;
		}

		body {
			font-family: DejaVu Sans Condensed, DejaVu Sans, Arial, sans-serif;
			font-size: 9pt;
			margin: 0 0 0 6mm;
			padding: 0 2mm 1mm 1mm;
			width: 62mm;
			box-sizing: border-box;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			table-layout: fixed;
		}

		td {
			padding: 1px 0;
			overflow-wrap: break-word;
			word-wrap: break-word;
			vertical-align: top;
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
			margin-top: 4px;
			padding-top: 4px;
		}

		.label {
			width: 45%;
			font-weight: bold;
		}

		.value {
			width: 55%;
			text-align: right;
		}

		.num {
			font-family: DejaVu Sans Condensed, DejaVu Sans, Arial, sans-serif;
			white-space: nowrap;
		}
	</style>
</head>

<body>
	<div class="center bold">
		IMACAL SAS<br>
		<?php echo ($r_cuota->FechaCuota >= "2019-07-19 00:00:00") ? "En Reorganizacion" : "LTDA En Reorganizacion"; ?><br>
		NIT <?php echo get_field("NIT", "NIT", "IDNIT", 1); ?><br>
		Regimen comun
	</div>

	<div class="center bold border-top">
		RECIBO PAGO CUOTA CREDITO<br>
		No. <?php echo $r_cuota->Consecutivo; ?>
	</div>

	<table class="border-top">
		<tr>
			<td class="label">Almacen</td>
			<td class="value"><?php echo $r_puntoventa->Nombre; ?></td>
		</tr>
		<tr>
			<td class="label">Direccion</td>
			<td class="value"><?php echo $r_puntoventa->Direccion; ?></td>
		</tr>
		<tr>
			<td class="label">Telefono</td>
			<td class="value"><?php echo $r_puntoventa->Telefono; ?></td>
		</tr>
	</table>

	<table class="border-top">
		<tr>
			<td class="label">Cuota No.</td>
			<td class="value num"><?php echo $r_cuota->IDCuota; ?></td>
		</tr>
		<tr>
			<td class="label">Fecha cuota</td>
			<td class="value num"><?php echo substr($r_cuota->FechaCuota, 0, 10); ?></td>
		</tr>
		<tr>
			<td class="label">Fecha pago</td>
			<td class="value num"><?php echo $r_cuota->FechaPago; ?></td>
		</tr>
		<tr>
			<td class="label">Valor total poliza</td>
			<td class="value num"><?php echo number_format($r->ValorTotal); ?></td>
		</tr>
		<tr>
			<td class="label">Valor cuota</td>
			<td class="value num"><?php echo number_format($r_cuota->ValorTotal); ?></td>
		</tr>
		<tr>
			<td class="label">Cuotas pendientes</td>
			<td class="value num"><?php echo $r_cuotas->numero; ?></td>
		</tr>
	</table>

	<table class="border-top">
		<tr>
			<td class="label">Cliente</td>
			<td class="value"><?php echo $cliente; ?></td>
		</tr>
		<tr>
			<td class="label">Identificacion</td>
			<td class="value num"><?php echo $documento; ?></td>
		</tr>
		<tr>
			<td class="label">Factura abonada</td>
			<td class="value num"><?php echo $r_factura->NumeroFactura; ?></td>
		</tr>
		<tr>
			<td class="label">Almacen pago</td>
			<td class="value"><?php echo $almacen_pago; ?></td>
		</tr>
	</table>

</body>

</html>
<?php
$html = ob_get_clean();
$pdf = PdfModern::render($html, [74, 165]);

if ($pdf === false) {
	http_response_code(500);
	exit('No fue posible generar el PDF del recibo de credito.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $namePDF . '"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;
?>
