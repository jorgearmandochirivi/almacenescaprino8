<?php
include("../admin/config.inc.php");
if (!class_exists('PdfModern')) {
	require_once(__DIR__ . "/../admin/lib/PdfModern.php");
}
//Encabezado();
$datos = Verifica_SesionCliente();
//print_r($datos);
$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
$ID_Usuario = $datos["IDUsuario"];
$Nivel =  $datos["Nivel"];
$IVA = $datos["IVA"];
$IDPuntoVenta = $datos["IDPuntoVenta"];
//include("admin/jscripts/tabs.php");

$TitleMod = "Impresion Reporte Diario";

$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura, F.ValorBono as ValorBono
						FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P
						WHERE F.IDPuntoVenta = '$IDPuntoVenta'
						AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = DATE_FORMAT('$Fecha','%Y-%c-%d' )
						AND F.IDFactura = DF.IDFactura
						AND F.IDPuntoVenta = DF.IDPuntoVenta
						AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica
						AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
						AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio
						AND R.Reportes <> 'N'
						Order by F.IDFactura;";


$qry_facturas = db_query($sql_facturas);

//punto de venta
$sql_punto = " SELECT * FROM PuntoVenta WHERE IDPuntoVenta = '" . $IDPuntoVenta . "'  ";
$qry_punto = db_query($sql_punto);
$r_puntoventa = db_fetch_object($qry_punto);

$i = 0;
$formapago = array();

while ($array_factura = db_fetch_array($qry_facturas)) {
	if ($i == 0):
		$primera_factura = $array_factura["NumeroFactura"];
	endif;
	$r_facturas[$i] = $array_factura;
	$i++;
	$ultima_factura = $array_factura["NumeroFactura"];
} //end while( $r_facturas = db_fetch_array( $qry_facturas ) )

$filedir = $dirroot . "/files/facturas/";

$name = "RDiario" . $r_puntoventa->Codigo . $Fecha . ".html";
$namePDF = "RDiario" . $r_puntoventa->Codigo . $Fecha . ".pdf";
$file = "$filedir$name";
$filepdf = "$filedir$namePDF";


//	ob_end_clean();

ob_start();

?>
<html>

<head>
	<meta charset="UTF-8">
</head>
<style>
	@page {
		size: 74mm 290mm;
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
		color: #000;
	}

	table {
		width: 100%;
		border-collapse: collapse;
		table-layout: fixed;
		margin: 0 0 4px 0;
	}

	td {
		overflow-wrap: break-word;
		word-wrap: break-word;
		vertical-align: top;
	}

	.texto,
	.navpic,
	.rowform,
	table {
		font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
		font-size: 7.8pt;
		line-height: 1.12;
		color: #000;
	}

	.navpic {
		font-weight: normal;
		padding: 1px 0.5mm;
	}

	.rowform {
		padding: 1px 0.5mm;
	}

	.report-header {
		font-size: 8.4pt;
		line-height: 1.15;
		text-align: center;
	}

	.report-table td {
		font-size: 7.3pt;
		line-height: 1.1;
		padding: 1px 0.4mm;
	}

	.report-table .head td {
		font-size: 7pt;
		font-weight: bold;
	}

	.num {
		white-space: nowrap;
	}

	.border-top {
		border-top: 1px dotted #000;
		margin-top: 3px;
		padding-top: 3px;
	}
</style>

<body><table id="#content"><tr><td class="report-header">IMACAL <?php
																								$fecha_gen = date("Y-m-d H:i:s");
																								echo $tipo_emp = ($fecha_gen >= "2019-07-19 00:00:00") ? "SAS" : "LTDA";
																								?><br>Nit 860033182-4<br>Almacen <?= $r_puntoventa->Nombre ?><br>No. De Serial <?= $r_puntoventa->EquipoComputo ?><br>Fecha Generacion: <?= date("Y-m-d"); ?><br>Fecha Reporte: <?php echo $_GET["Fecha"] ?></td></tr><tr><td class='mainbg'><table class="report-table border-top" border="0" cellspacing="0" cellpadding="0"><tr class="head"><td class="navpic" style="width: 13%;" nowrap>No.</td><td class="navpic" style="width: 20%;" align="center" nowrap>Ref</td><td class="navpic" style="width: 20%;" align="center" nowrap>Vr. Unit.</td><td class="navpic" style="width: 10%;" align="center" nowrap>Pares</td>
<td class="navpic" style="width: 20%;" align="center" nowrap>Pago</td>
<td class="navpic" style="width: 17%;" align="center" nowrap>IVA</td>
</tr>
<?php
foreach ($r_facturas as $key => $valor) {
	//print_r( $valor );
	$class = repetition() ? "row2" : "row1";
	//print_r($valor);
?>
	<tr>
		<td class="<?= $class ?>" align="center" nowrap><?= $valor['NumeroFactura'] ?></td>
		<td class="<?= $class ?>" align="center" nowrap><?= $valor['Numero'] ?> </td>
		<td class="<?= $class ?>" align="right" nowrap><?php
			$factor_descuento_ref = 1 - ($valor['DescuentoRef'] / 100);
			echo number_format($factor_descuento_ref != 0 ? $valor['PrecioU'] / $factor_descuento_ref : $valor['PrecioU'], 0);
		?></td>
		<td class="<?= $class ?>" align="center" nowrap><?php echo $valor['Cantidad'];
														$Pares += $valor['Cantidad']; ?></td>
		<!--<td class="<?= $class ?>" align="center" nowrap><?= $valor['DescuentoRef'] ?></td>-->
		<td class="<?= $class ?>" align="right" nowrap>
			<?php
			$descuento_bono = 0;
			if ((int)$valor['ValorBono'] > 0 && $numero_factura_ant !=  $valor['NumeroFactura']) {
				$descuento_bono = number_format($valor['ValorBono']);
			}


			if ($valor['DescuentoPar'] > 0)
				$valordescuentopar = ($valor['PrecioU'] * $valor['Cantidad']) *   ($valor['DescuentoPar'] / 100);
			else
				$valordescuentopar = 0;


			//consultar forma de pago pa saber si se le resta
			$sql_formasdepago = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '" . $valor["IDFactura"] . "' AND IDPuntoVenta = '$IDPuntoVenta' ";
			$qry_formasdepago = db_query($sql_formasdepago);
			$saldo = 0;
			while ($r_formasdepago = db_fetch_object($qry_formasdepago))
				if ($r_formasdepago->IDFormaPago == 13) //13 FormaPago Saldo
					$saldo = $r_formasdepago->Valor;

			if ($valor['DescuentoFactura'] == 0 && (int)$descuento_bono <= 0) {
				$valorparcial = (($valor['PrecioU'] * $valor['Cantidad']) *   (1 - ($valor['DescuentoFactura'] / 100))) - ($valordescuentopar);
				$pago = $valorparcial - $saldo;

				if ($pago < 0 || $valor["ValorTotal"] == 0):
					$pago = 0;
				endif;

				echo number_format($pago, 0);
				$Pago += $pago;
			} else {

				if ((int)$descuento_bono && $numero_factura_ant !=  $valor['NumeroFactura']) {

					$valorparcial = (($valor['PrecioU'] * $valor['Cantidad']) *   (1 - ($valor['DescuentoFactura'] / 100))) - ($valordescuentopar) - ($valor['ValorBono']);

					//echo $valorparcial."-".$TotalFactura."--";
					$pago = $valorparcial - $saldo;

					if ($pago < 0 || $valor["ValorTotal"] == 0):
						$pago = 0;
					endif;

					echo number_format($pago, 0);
					$Pago += $pago;
				} else {



					//$valorparcial =  ( ( $valor['PrecioU'] * $valor['Cantidad'] ) + ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) - ( $valordescuentopar );
					$Precio =  $valor['PrecioU'] - $valordescuentopar;
					$valorparcial =  (($Precio * $valor['Cantidad']) + (($Precio * $valor['Cantidad']) *   ($valor['DescuentoFactura'] / 100)));

					/* Se agrega pa las mayores */
					$TotalFactura = (float)$valor["ValorTotal"];
					$mayortotal = $TotalFactura - $valorparcial;
					if ($mayortotal <> 0 && $TotalFactura != 0) {
						$saldo = ($valorparcial / $TotalFactura) * $saldo; //Que porcentaje del item es para el total
						$pago = $valorparcial - $saldo;
					} //end if
					else //Hasta aqui se agrega pa las mayores
						$pago = $valorparcial - $saldo;
					echo number_format($pago, 0);
					$Pago += $pago;
				}
			} //end else

			$numero_factura_ant = $valor['NumeroFactura'];
			?>
		<td class="<?= $class ?>" align="right" nowrap>
			<?php

			$valoriva = ($valorparcial - ($valorparcial / (1 + $IVA)));

			if ($valoriva < 0 || $valor["ValorTotal"] == 0):
				$valoriva = 0;
			endif;

			echo number_format($valoriva, 0);
			$ValorIVA += $valoriva; ?>

		</td>
	</tr>

<?php
} //end foreach( $r_facturas as $key => $valor )
?>

<tr>
	<td class="navpic" colspan="3" align="right" nowrap><b>TOT</b></td>
	<td class="navpic" align="center" nowrap><?= $Pares ?></td>
	<!--<td class="navpic" align="right" nowrap></td>-->
	<td class="navpic" align="right" nowrap><?= number_format($Pago, 0) ?></td>
	<td class="navpic" align="right" nowrap><?= number_format($ValorIVA, 0) ?></td>
</tr>
</table>

<table class="border-top">
	<tr>
		<td align="center">Primera Fact d&iacute;a</td>
		<td align="center">&Uacute;ltima Fact d&iacute;a</td>
	</tr>
	<tr>
		<td align="center"><?php echo $primera_factura; ?></td>
		<td align="center"><?php echo $ultima_factura; ?></td>
	</tr>
</table>

</td>
</tr>
</td>


</table>


<?php
/* DESDE ACA PARA LAS FORMAS DE PAGO */
/*$sql_formapago = "SELECT PVB.IDFormaPago, FP.Descripcion, PVB.IDBanco
					FROM PuntoVentaBanco PVB, FormaPago FP
					WHERE PVB.IDPuntoVenta = '$IDPuntoVenta'
					AND FP.IDFormaPago = PVB.IDFormaPago";
	*/
$sql_formapago = "SELECT *
					FROM FormaPago FP
					WHERE FP.IDFormaPago !=  17";

$qry_formapago = db_query($sql_formapago);

$i = 0;
while ($array_formapago = db_fetch_array($qry_formapago)) {
	$r_formapago[$i] = $array_formapago;
	$i++;
} //end while( $array_formapago = db_fetch_array( $qry_formapago ) )

?>

<table class="border-top" id="#content">
	<tr>
		<td class="rowform" align="left" nowrap>Forma de Pago </td>
		<td class="rowform" align="right" nowrap>Registros</td>
		<td class="rowform" align="right" nowrap>Valor</td>
	</tr>

	<?php
	foreach ($r_formapago as $key => $valor) {

		//print_r($valor);
		//print_r($bancos);

		$sql_facturas = "SELECT  COUNT(*) as cantidad, SUM( FPF.Valor ) as valor FROM Factura F,  FormaPagoFactura FPF WHERE F.IDPuntoVenta = '$IDPuntoVenta' AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = DATE_FORMAT( '$Fecha','%Y-%c-%d' )  AND  F.IDFactura = FPF.IDFactura AND FPF.IDPuntoVenta = F.IDPuntoVenta AND FPF.IDFormaPago = " . $valor["IDFormaPago"] . " AND FPF.IDPuntoVenta = '$IDPuntoVenta' ";

		$qry_facturas = db_query($sql_facturas);
		$r_factura = db_fetch_object($qry_facturas);

		$i = 0;
		$formapago = array();

		if ($r_factura->valor > 0) {

	?>
			<tr>
				<td class="rowform" align="left" nowrap><?= $valor['Descripcion'] ?> </td>
				<td class="rowform" align="right" nowrap><?= $r_factura->cantidad ?> </td>
				<td class="rowform" align="right" nowrap><?= number_format($r_factura->valor, 0);
															$totValor += $r_factura->valor;  ?> </td>
			</tr>
	<?php
		} //end if
	}
	?>
	<tr>
		<td class="rowform" colspan="3" align="right" nowrap><?= number_format($totValor, 0);  ?> </td>
	</tr>
</table>


<?php

//$sql_credito = "SELECT * FROM CreditoCuota WHERE DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND IDPuntoVentaPago = '$IDPuntoVenta'";
$sql_credito = "SELECT * FROM CreditoCuota WHERE DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND (IDPuntoVentaPago = '$IDPuntoVenta' or ( IDPuntoVenta = '$IDPuntoVenta' and IDPuntoVentaPago = 0 )) ";


$qry_credito = db_query($sql_credito);

//punto de venta
$sql_punto = " SELECT * FROM PuntoVenta WHERE IDPuntoVenta = '" . $IDPuntoVenta . "'  ";
$qry_punto = db_query($sql_punto);
$r_puntoventa = db_fetch_object($qry_punto);

$i = 0;
$formapago = array();

while ($array_credito = db_fetch_array($qry_credito)) {
	$r_credito[$i] = $array_credito;
	$i++;
} //end while( $r_facturas = db_fetch_array( $qry_facturas ) )

?>
<table class="report-table border-top">
	<tr>
		<td class="navpic" nowrap>No.</td>
		<td class="navpic" align="center" nowrap>Almac</td>
		<td class="navpic" align="center" nowrap>Cuot<br> Abon</td>
		<td class="navpic" align="center" nowrap>Cuot<br>Pend</td>
		<td class="navpic" align="center" nowrap>Val Abon</td>
		<td class="navpic" align="center" nowrap>Val Sal</td>
	</tr>
	<?php

	foreach ($r_credito as $key => $valor) {
		//print_r( $valor );
		$class = repetition() ? "row2" : "row1";
		//print_r($valor);
	?>
		<tr>
			<td class="<?= $class ?>" align="center" nowrap><?= $valor['NumeroFactura'] ?></td>
			<td class="<?= $class ?>" align="center" nowrap><?= get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $valor["IDPuntoVenta"]) ?> </td>
			<td class="<?= $class ?>" align="center" nowrap><?= $numero_cuota = $valor["IDCuota"] ?></td>
			<td class="<?= $class ?>" align="center" nowrap>

				<?php
				//$sql_cuotas = " SELECT count(*) as numero FROM CreditoCuota WHERE IDFactura = '".$valor[IDFactura]."' AND IDPuntoVenta = '$valor[IDPuntoVenta]' AND FechaPago = '0000-00-00 00:00:00' ";
				//$qry_cuotas = db_query( $sql_cuotas );
				//$r_cuotas = db_fetch_object( $qry_cuotas );
				echo $pendiente_cuota = 5 - $numero_cuota;
				?>
			</td>
			<td class="<?= $class ?>" align="right" nowrap>
				<?= number_format($valor["ValorTotal"], 0);
				$PagoAbono += $valor["ValorTotal"];  ?>
			</td>
			<td class="<?= $class ?>" align="right" nowrap>
				<?php
				$saldo = (int)$pendiente_cuota * (int)$valor["ValorTotal"];
				echo number_format($saldo, 0);
				?>
			</td>
		</tr>

	<?php
	} //end foreach( $r_facturas as $key => $valor )
	?>

	<tr>
		<td class="navpic" colspan="2" align="right" nowrap><b>TOT</b></td>
		<td class="navpic" align="center" nowrap>&nbsp;</td>
		<td class="navpic" align="right" nowrap></td>
		<td class="navpic" align="right" nowrap><?= number_format($PagoAbono, 2) ?></td>
		<td class="navpic" align="right" nowrap></td>
	</tr>

	<tr>
		<td class="navpic" colspan="6" align="left" nowrap>
			<br><br>
			Revisado por: _________________________________
		</td>
	</tr>
	<tr>
		<td class="navpic" colspan="6" align="left" nowrap>
			<br><br>
			Observaciones:
			<br><br><br><br>
		</td>
	</tr>
	<tr>
		<td class="navpic" colspan="6" align="center" nowrap>
			<a href="/admin/files/facturas/RDiario<?= $r_puntoventa->Codigo . $Fecha ?>.pdf">pdf</a>
		</td>
	</tr>




</table>


</body>

</html>
<?php

$page = ob_get_contents();
file_put_contents($file, $page);

ob_end_clean();

echo $page;
PdfModern::generate($page, $filepdf, [74, 290]);

//passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf");
?>
