<?
	include("../admin/config.inc.php");
	Encabezado();
	$datos = Verifica_SesionCliente();
	//print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];
	include("admin/jscripts/tabs.php");
		
	$TitleMod ="Factura";
	
	$Table = "Credito";
	$TableJoin = "Credito";
	$Key = "IDFacturaBono";
	
	$qid = db_query(" SELECT * FROM Credito WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto'");
		
	$r = db_fetch_object($qid);
	
	$sql_cuota = " SELECT * FROM CreditoCuota WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto' AND IDCuota = '$idcuota' ";
	$qry_cuota = db_query( $sql_cuota );
	$r_cuota = db_fetch_object( $qry_cuota );
?>
<html>
<head>
<title>Imprimir Recibo</title>
</head>
<style>
<!--
.texto {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: x-small;
	color: #000000;
}
.bordertable {border: dotted 1px; color:#c3c3c3}
-->
</style>

<script>
<!--
function printWindow() {
  if (window.print)
    window.print();
  else
    alert("Lo siento, pero a tu navegador no se le puede ordenar imprimir" +
      " desde la web. Actualizate o hazlo desde los men?s");
}
-->
</script>

<body>
	
	<FORM name="frm" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" <?if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?}?>>
			<table class="forumline" width="215" height="100" cellspacing="1" border="0" align="center">
		<tr>
			<td valign="top">
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff">
					<tr>
						<td colspan="2">
							<div align="center">
										<table width=100% border=0>
											<tr>
												<td colspan="4">
													<table class=rowtable>
														<tr>
															<td class=texto></td>
															<td class=texto colspan="3" align="right" nowrap>Documento pago cuota credito. <?=$r->NumeroDocumento?></td>
														</tr>
														<?
													$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
													$qry_puntoventa = db_query( $sql_puntoVenta );
													$r_puntoventa = db_fetch_object( $qry_puntoventa );
												?>
														<tr>
															<td class=texto>Alamac&eacute;n</td>
															<td class=texto colspan="2">CALZADO CAPRINO <?=$r_puntoventa->Nombre?></td>
															<td class=texto nowrap>NIT <?=get_field( "NIT","NIT","IDNIT",1 );?><br>
																R&eacute;gimen com&uacute;n</td>
														</tr>
														<tr>
															<td class=texto>Direcci&oacute;n</td>
															<td class=texto colspan="2"><?=$r_puntoventa->Direccion?></td>
															<td class=texto nowrap>Tel&eacute;fono: <?=$r_puntoventa->Telefono?></td>
														</tr>
														<tr>
															<td class=texto nowrap>Cuota N&uacute;mero</td>
															<td class=texto colspan="3"><?=$r_cuota->IDCuota?></td>
														</tr>
														<tr>
															<td class=texto nowrap>Fecha Cuota</td>
															<td class=texto colspan="3"><?=$r_cuota->FechaCuota?></td>
														</tr>
														<tr>
															<td class=texto nowrap>Fecha Pago</td>
															<td class=texto colspan="3"><?=$r_cuota->FechaPago?></td>
														</tr>
														<tr>
															<td class=texto nowrap>Valor Total P&oacute;liza</td>
															<td class=texto colspan="3"><?=$r->ValorTotal?></td>
														</tr>
														<tr>
															<td class=texto nowrap>Valor Cuota Quincena</td>
															<td class=texto colspan="3"><?=$r_cuota->ValorTotal?></td>
														</tr>
														<tr>
															<td class=texto>CLIENTE</td>
															<td class=texto colspan="3" nowrap><? echo get_field("Cliente","CONCAT(Cedula,' ',Nombre,' ',Apellido)","IDCliente",$r->IDCliente);?></td>
														</tr>
														<tr>
															<td class=texto>Cuotas Pendientes</td>
															<td class=texto colspan="3" nowrap>
																
																<?
																$sql_cuotas = " SELECT count(*) as numero FROM CreditoCuota WHERE IDFactura = '$r_cuota->IDFactura' AND IDPuntoVenta = '$r_cuota->IDPuntoVenta' AND FechaPago = '0000-00-00 00:00:00' ";
																$qry_cuotas = db_query( $sql_cuotas );
																$r_cuotas = db_fetch_object( $qry_cuotas );
																echo $r_cuotas->numero;
																?>
																
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td class="texto" colspan="4" align="center">Este es un documento de pago de cuota de cr&eacute;dito.<br>
												</td>
											</tr>
										</table>
									</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
		</FORM>
<script>
printWindow();
</script>
</body>
</html>