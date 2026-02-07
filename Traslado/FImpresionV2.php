<?php
	include("../admin/config.inc.php");



	//Encabezado();
	$datos = Verifica_SesionCliente();
	//print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];
	//include("admin/jscripts/tabs.php");

	$TitleMod ="Traslado";

	$Table = "Traslado";
	$TableJoin = "Traslado";
	$Key = "IDTraslado";

	$qid = db_query(" SELECT * FROM Traslado WHERE IDTraslado = '$id' AND IDPuntoVentaOrigen = '$idpunto' ");

	$r = db_fetch_object($qid);

	$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
	$qry_puntoventa = db_query( $sql_puntoVenta );
	$r_puntoventa = db_fetch_object( $qry_puntoventa );

	$filedir = $dirroot . "/files/Traslados/";

	$name = "Traslado" . $r_puntoventa->Codigo.$r->IDTraslado . ".html";
	$namePDF = "Traslado" . $r_puntoventa->Codigo.$r->IDTraslado . ".pdf";
	$file = "$filedir$name";
$filepdf = "$filedir$namePDF";


//	ob_end_clean();

	ob_start();

?>
<html>
<head>
</head>
<style>
<!--
body{
	font-size:11px;
	margin:0;
	font-family: "Arial Black";
}
table{
	font-size:12px;
	font-family: "Arial Black";
}
@page {

	size 6cm 12cm;
	margin-left: 0;
	font-family: "Arial Black";
	}
</style>


<body>


			<table  width="315" cellspacing="1" border="0" align="left" height="100" id="#content" style="table-layout:fixed">
		<tr>
			<td valign="top">
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff" style="table-layout:fixed">
					<tr>
						<td colspan="2">
							<div align="center">
								<table width=100% border=0>
									<tr>
									  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.</td>
										<td colspan="4">
											<table class=rowtable width="100%" style="table-layout:fixed">



                                                <tr>
													<td nowrap width="50">Traslado: No. </td>
													<td colspan="3" nowrap class=texto><font style="font-size:16px"><?php echo $r_puntoventa->Codigo.$r->IDTraslado?></font></td>
												</tr>


												<tr>
													<td class=texto>Almacen Destino</td>
													<td class=texto colspan="3" nowrap><?php
																echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaDestino);
															?> </td>
												</tr>
														<tr>
													<td class=texto>Almacen Origen</td>
															<td class=texto colspan="3" nowrap><?php
																echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaOrigen);
															?></td>
														</tr>
									<tr>
													<td class=texto nowrap>Fecha Traslado</td>
													<td class=texto colspan="3" nowrap><?=$r->Fecha ?></td>
										</tr>
                                        <tr>
													<td class=texto nowrap>Realizado por</td>
													<td class=texto colspan="3" nowrap>
                                                    <?php echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado) . " " . get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado); ?>
                                                    </td>
										</tr>






                                                <tr>
													<td class=texto>Estado</td>
													<td class=texto nowrap><?php echo get_field("EstadoTraslado","Descripcion","IDEstadoTraslado",$r->IDEstadoTraslado); ?></td>
													<td class=texto></td>
													<td class=texto></td>
												</tr>
												<tr>
													<td colspan="4" class=texto nowrap>Observaciones: <?php
													//echo $r->Observaciones;

													//Lo dejo en varias lineas
													echo $obs1=substr($r->Observaciones,0,30);
													$obs2=substr($r->Observaciones,30,40);
													$obs3=substr($r->Observaciones,70,40);
													$obs4=substr($r->Observaciones,100,40);
													if(!empty($obs2))
														echo "<br>".$obs2;
													if(!empty($obs3))
														echo "<br>".$obs3;
													if(!empty($obs4))
														echo "<br>".$obs4;

													?>
													</td>

												</tr>
											</table>
										</td>
									</tr>
									<tr>
									  <td>&nbsp;</td>
										<td colspan="4">
											<table class="bordertable" border="0" cellspacing="1" cellpadding="0" id=table1 width="100%" style="table-layout:fixed">


                                                <tr>
                                                    <td colspan="4">
                                                        <table align="left"border="0" cellspacing="1" cellpadding="0" width="60%" id=table1 style="table-layout:fixed">
                                                            <tr>
                                                                <td align="center"><b>Referencia</b></td>
                                                                <td align="center"><b>Talla</b></td>
                                                                <td align="center"><b>Cantidad</b></td>
																																<td align="center"><b>Nro Tarjetas</b></td>
                                                            </tr>
                                                            <?php
                                                                $sql_detalle = " SELECT * FROM DetalleTraslado WHERE $Key = '$r->IDTraslado' AND IDPuntoVentaOrigen = '$r->IDPuntoVentaOrigen' ";
                                                                $query_detalle = db_query($sql_detalle);
                                                                $i = 0;
                                                                while( $r_detalle = db_fetch_object( $query_detalle ) )
                                                                {
                                                                    $class = repetition()?"col1list":"col2list";
                                                                    $i++;
                                                                    $PuntoVentaReferencia = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);
                                                                    $Talla = get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);;
                                                            ?>
                                                            <tr >
                                                                <td  style="background-color:#FFFFFF;" align="center">
                                                                    <?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)); ?></td>
                                                                <td style="background-color:#FFFFFF;" align="center">
                                                                    <?php echo get_field("Talla","Descripcion","IDTalla",$Talla) ?>
                                                                </td>
                                                                <td style="background-color:#FFFFFF;" align="center">
                                                                    <?php  $cantidad_total += $r_detalle->Cantidad;
																		echo $r_detalle->Cantidad;

																	?>
                                                                </td>
																																<td style="background-color:#FFFFFF;" align="center">
																																		<?php
																																				echo $r_detalle->NumeroTarjeta;
																																			?>
																																</td>
                                                            </tr>
                                                            <?php

																if($i==58){
																	$i=0;
																?>
																	<div style="page-break-before: always;"></div>
																</table>
																<table class="bordertable" border="0" cellspacing="1" cellpadding="0" id=table1 width="100%" style="table-layout:fixed">
																<tr>
                                                                <td align="center"><b>Referencia</b></td>
                                                                <td align="center"><b>Talla</b></td>
                                                                <td align="center"><b>Cantidad</b></td>
																																<td align="center"><b>Nro Tarjetas</b></td>
                                                            </tr>
																<?php	
																}

                                                                }//end while
                                                            ?>
																														<tr>
                                                                <td align="center"><b></b></td>
                                                                <td align="center"><b></b></td>
                                                                <td align="center"><b>TOTAL</b></td>
																																<td align="center"><b><?php echo $cantidad_total ?></b></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>


											</table>
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
</body>
</html>


<?php

require_once("../jscripts/dompdf/dompdf_config.inc.php");
$dompdf = new DOMPDF();
//$dompdf->set_paper( array(0,0, 5 * 72, 5 * 72), "portrait" ); // 12" x 12"
$dompdf->set_paper( array(0,0, 5 * 72, 12 * 72), "portrait" ); // 12" x 12"
$dompdf->load_html(ob_get_clean());
$dompdf->render();
//$pdf = $dompdf->output();
$filename = "files/fileTraslado".$r_puntoventa->Codigo.$r->IDTraslado.".pdf";
file_put_contents($filename, $pdf);
//$dompdf->stream($filename);
$dompdf->stream($filename, array("Attachment" => false));

?>
