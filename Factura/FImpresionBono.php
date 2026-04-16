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
$Table = "FacturaBono";
$TableJoin = "FacturaBono";
$Key = "IDFacturaBono";

$qid = db_query(" SELECT * FROM FacturaBono WHERE IDFacturaBono = '$id' AND IDPuntoVenta = '$idpunto'");
$r = db_fetch_object($qid);

$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
$qry_puntoventa = db_query($sql_puntoVenta);
$r_puntoventa = db_fetch_object($qry_puntoventa);

$filedir = $dirroot . "/files/facturas/";
$name = "FBonos" . $r_puntoventa->Codigo . $r->IDFacturaBono . ".html";
$namePDF = "FBonos" . $r_puntoventa->Codigo . $r->IDFacturaBono . ".pdf";
$file = "$filedir$name";
$filepdf = "$filedir$namePDF";
$ruta_redireccion = "/admin/files/facturas/" . $namePDF;

ob_start();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Imprimir Recibo</title>
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
			margin-top: 3px;
			padding-top: 3px;
		}

		.item-table td {
			font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
			font-size: 5.4pt;
			font-weight: normal;
			line-height: 1.05;
			padding: 1px 0.25mm;
			text-align: center;
			vertical-align: top;
		}

		.item-table .head td {
			font-size: 4.8pt;
			font-weight: normal;
			line-height: 1.05;
			padding-bottom: 2px;
		}

		.ref-cell {
			line-height: 1.02;
			word-break: break-word;
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
		Documento transaccion bono. <?php echo $r->IDFacturaBono; ?><br>
		CALZADO CAPRINO <?php echo $r_puntoventa->Nombre; ?><br>
		NIT <?php echo get_field("NIT", "NIT", "IDNIT", 1); ?><br>
		Regimen comun
	</div>

	<div class="texto border-top">
		<strong>Direccion:</strong> <?php echo $r_puntoventa->Direccion; ?><br>
		<strong>Telefono:</strong> <?php echo $r_puntoventa->Telefono; ?><br>
		<strong>Fecha Factura:</strong> <?php echo $r->FechaFactura; ?>
	</div>

	<div class="texto border-top">
		<strong>Cliente:</strong> <?php echo get_field("Cliente", "CONCAT(Nombre,' ',Apellido)", "IDCliente", $r->IDCliente); ?><br>
		<strong>Vendedor:</strong> <?php echo get_field("Empleado", "Nombre", "IDEmpleado", $r->IDEmpleado) . " " . get_field("Empleado", "Apellidos", "IDEmpleado", $r->IDEmpleado); ?><br>
		<strong>No. Documento:</strong> <?php echo get_field("Cliente", "Cedula", "IDCliente", $r->IDCliente); ?>
	</div>

	<table class="item-table border-top" style="margin-top: 4px;">
		<tr class="head">
			<td style="width: 23%;">Ref</td>
			<td class="num" style="width: 12%;">Talla</td>
			<td class="num" style="width: 10%;">Cant</td>
			<td class="num" style="width: 18%;">Vr U</td>
			<td class="num" style="width: 13%;">Dto</td>
			<td class="num" style="width: 24%;">Total</td>
		</tr>
		<?php
		$sql_detalle = "SELECT * FROM DetalleFacturaBono WHERE IDFacturaBono = '$r->IDFacturaBono' AND IDPuntoVenta = '$r->IDPuntoVenta'  ";
		$query_detalle = db_query($sql_detalle);
		while ($r_detalle = db_fetch_object($query_detalle)) {
			$referencia = get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)));
			$talla = get_field("Talla", "Descripcion", "IDTalla", get_field("CodificacionEspecifica", "IDTalla", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica));
		?>
			<tr>
				<td class="ref-cell"><?php echo $referencia; ?></td>
				<td class="num"><?php echo $talla; ?></td>
				<td class="num"><?php echo $r_detalle->Cantidad; ?></td>
				<td class="num right"><?php echo number_format($r_detalle->PrecioU); ?></td>
				<td class="num"><?php echo number_format($r_detalle->DescuentoRef); ?></td>
				<td class="num right"><?php echo number_format($r_detalle->ValorU * $r_detalle->Cantidad); ?></td>
			</tr>
		<?php } ?>
	</table>

	<table class="texto border-top">
		<tr class="bold">
			<td>Total</td>
			<td class="right num"><?php echo number_format($r->ValorTotal); ?></td>
		</tr>
	</table>

	<div class="center texto border-top" style="margin-top: 8px;">
		Este es un documento de transaccion con bonos.
	</div>

	<div class="center" style="margin-top: 6px;">
		<a href="<?php echo $ruta_redireccion; ?>">DESCARGAR PDF</a>
	</div>
</body>

</html>
<?php
$html = ob_get_clean();
$fw = fopen($file, "w");
fputs($fw, $html);
fclose($fw);

echo $html;

PdfModern::generate($html, $filepdf, [74, 190]);
echo "<script>window.location.href='" . $ruta_redireccion . "';</script>";
?>
