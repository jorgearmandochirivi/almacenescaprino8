<?php
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
	
	$Table = "FacturaBono";
	$TableJoin = "FacturaBono";
	$Key = "IDFacturaBono";
	
	$qid = db_query(" SELECT * FROM FacturaBono WHERE IDFacturaBono = '$id' AND IDPuntoVenta = '$idpunto'");
		
	$r = db_fetch_object($qid);

	 $sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
	$qry_puntoventa = db_query( $sql_puntoVenta );
	$r_puntoventa = db_fetch_object( $qry_puntoventa );

	$filedir = $dirroot . "/files/facturas/";
	
	$name = "FBonos" . $r_puntoventa->Codigo.$r->IDFacturaBono . ".html";
	$namePDF = "FBonos" . $r_puntoventa->Codigo.$r->IDFacturaBono . ".pdf";
	$file = "$filedir$name";
	$filepdf = "$filedir$namePDF";

	ob_start();
?>
<html>
<head>
<title>Imprimir Recibo</title>
</head>

<style>
<!--
body{
	font-size:6.5px;
	margin:0;
}
table{
	font-size:6.5px;
}
@page { size 6cm 12cm; 
	margin-left: 0;
	}

@media print{
*{
	margin:0;
	padding:0;
}
body{
	font-size:7px;
	margin:0;
	padding:0;
}

.texto {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 6.5px;
	color: #000000;
}
.mensajefooter{
	font-size:6px;
}


.bordertable {border: dotted 1px; color:#c3c3c3}
#content { margin-left:0; 
     float:none; 
     width:auto;
     height : 300px; 
     color:black;
	 }
table{
	font-size:6.5px;
	margin:0;
}


-->
}
</style>

<script>
<!--
function printWindow() {
  if (window.print)
    window.print();
  else
    alert("Lo siento, pero a tu navegador no se le puede ordenar imprimir" +
      " desde la web. Actualizate o hazlo desde los menœs");
}
-->
</script>

<body>
	
	<table  width="215" cellspacing="1" border="0" align="left" height="100" id="#content">
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
															<td class=texto colspan="4" align="right" nowrap>Documento transaccion bono. <?=$r->IDFacturaBono?></td>
														</tr>
												
												
												<tr>
													<td class=texto>Alamac&eacute;n</td>
													<td class="texto" colspan="3">CALZADO CAPRINO <?=$r_puntoventa->Nombre?> </td>
													
												</tr>
												<tr>
													<td class=texto nowrap>NIT </td>
													<td class="texto" colspan="3">
														<?=get_field( "NIT","NIT","IDNIT",1 );?> R&eacute;gimen com&uacute;n</td>
												</tr>
														<tr>
													<td class=texto>Direcci&oacute;n</td>
															<td class=texto colspan="3"><?=$r_puntoventa->Direccion?></td>
															
														</tr>
													<tr>
														<td class=texto nowrap>Tel&eacute;fono: </td>
														<td class="texto" colspan="3"><?=$r_puntoventa->Telefono?></td>
													</tr>
														<tr>
													<td class=texto nowrap>Fecha Factura</td>
													<td class=texto colspan="3"><?=$r->FechaFactura?></td>
												</tr>
														<tr>
													<td class=texto>Cliente</td>
													<td class=texto colspan="3"><?php echo get_field("Cliente","CONCAT(Nombre,' ',Apellido)","IDCliente",$r->IDCliente);?></td>
													
												</tr>
												<tr>
													<td class=texto>Vendedor</td>
													<td class="texto" colspan="3"><?php echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado)." ".get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado);?></td>
												</tr>
												<tr>
													<td class=texto nowrap>No. Documento</td>
													<td class=texto colspan="3"><?php echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente);?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="4">
											<table class="bordertable" border="0" cellspacing="1" cellpadding="0" id=table1 width="100%">
												<tr >
													<td align="center" class="texto"><b>Referencia</b></td>
													<td align="center" class="texto"><b>Talla</b></td>
													<td align="center" class="texto"><b>Cantidad</b></td>
													<td align="center" class="texto" nowrap><b>Vr. U.</b></td>
													<td align="center" class="texto"><b>Descuento U.</b></td>
													<td align="center" class="texto" nowrap><b>Vr.</b></td>
												</tr>
												<?php
												$sql_detalle = "SELECT * FROM DetalleFacturaBono WHERE IDFacturaBono = '$r->IDFacturaBono' AND IDPuntoVenta = '$r->IDPuntoVenta'  ";
												$query_detalle = db_query($sql_detalle);
												$i = 0;
												while( $r_detalle = db_fetch_object( $query_detalle ) )
												{
													$class = repetition()?"texto":"texto";
													$i++;
											?>
												<tr >
													<td align="center" class="<?=$class?>"><?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))?></td>
													<td align="center" class="<?=$class?>"><?php echo get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))?></td>
													<td align="center" class="<?=$class?>"><?php echo $r_detalle->Cantidad?></td>
													<td align="right" class="<?=$class?>"><?php echo number_format($r_detalle->PrecioU);?></td>
													<td align="center" class="<?=$class?>"><?php echo number_format($r_detalle->DescuentoRef);?></td>
													<td align="right" class="<?=$class?>"><?php echo number_format($r_detalle->ValorU * $r_detalle->Cantidad);?></td>
												</tr>
												<?php 											}
											?>
											</table>
										</td>
									</tr>
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto colspan="2">
											<div align="left"></div>
										</td>
									</tr>
									
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">Total</div>
										</td>
										<td class=texto align="right"><?=number_format($r->ValorTotal);?></td>
									</tr>
									
									
									<tr>
										<td class="texto" colspan="4" align="center">Este es un documento de transacc&oacute;n con bonos.<br>
										</td>
									</tr>
									<tr>
                                    	<td class="texto" colspan="4" align="center">
                                        	<a href="/admin/files/facturas/FBonos<?=$r_puntoventa->Codigo.$r->IDFacturaBono ?>.pdf">pdf</a>
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
<script>
//printWindow();
</script>
</body>
</html>
<?php

$page = ob_get_contents();
$fw = fopen($file, "w");
fputs($fw,$page,strlen($page));
fclose($fw);

ob_end_clean();

echo $page;
//passthru("htmldoc --format pdf --size 'Universal' --textfont Arial --title 'Acta' --charset 8859-15 --left 0cm --right 0cm --top 0cm --bottom 0cm --fontsize 7 --webpage $file -f $filedir/$namePDF");
//echo "/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf";
passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf");
?>