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

	$TitleMod ="Factura";

	$Table = "Factura";
	$TableJoin = "Factura";
	$Key = "IDFactura";

	$qid = db_query(" SELECT * FROM Factura WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto' ");

	$r = db_fetch_object($qid);

	$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
	$qry_puntoventa = db_query( $sql_puntoVenta );
	$r_puntoventa = db_fetch_object( $qry_puntoventa );

	$filedir = $dirroot . "/files/facturas/";
	
	$name = "Factura" . $r_puntoventa->Codigo.$r->NumeroFactura . ".html";
	$namePDF = "Factura" . $r_puntoventa->Codigo.$r->NumeroFactura . ".pdf";
	$file = "$filedir$name";
	$filepdf = "$filedir$namePDF";


	$club_suavidad=get_field("Cliente","ClubSuavidad","IDCliente",$r->IDCliente);


	$array_fidelizacion = fid_get_puntos( $r->IDCliente, $id );




//	ob_end_clean();

	ob_start();
?>
<html>
<head>
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

     color:black;
	 }
table{
	font-size:6.5px;
	margin:0;
}


-->
}
</style>
<body>..



<?php ob_start(); ?>
<table  width="215" cellspacing="1" border="0" align="center" id="#content">
		<tr>
		  <td valign="top">.</td>
		  <td valign="top" align="left"><table width="98%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff">
				<tr>
						<td colspan="2">
							<div align="center">
								<table width=100% border=0>
									<tr>
										<td colspan="4">
											<table class=rowtable width="100%">



											<tr>
											<td class=texto colspan="4" nowrap align="center">
											IMACAL
											<?php
												echo $tipo_emp= ($r->FechaFactura>="2019-07-19 00:00:00") ? "SAS En Reorganizacion" : "LTDA En Reorganizacion";
											?><br>
											NIT <?php echo get_field( "NIT","NIT","IDNIT",1 );?>&nbsp;&nbsp;&nbsp;&nbsp;
											R&eacute;gimen com&uacute;n
											</td>
											</tr>
                                                <tr>
													<td class=texto colspan="4" nowrap><br>ESTE DOCUMENTO NO ES FACTURA DE VENTA,<br> NOTA CREDITO O DOCUMENTO EQUIVALENTE<br></td>
												</tr>
												<tr>
													<td class=texto colspan="4" nowrap align="center"><br>APRECIADO CLIENTE:<br>LA FACTURA O NOTA CREDITO ELECTRONICA<br>SERA ENVIADA POR CORREO<br><br></td>
												</tr>
												<tr>
													<td class=texto colspan="4" nowrap>COMPROBANTE DE ENTREGA No. <?php echo $r_puntoventa->Codigo.$r->NumeroFactura?></td>
												</tr>

												<tr>
													<td width="38%" class=texto>Almac&eacute;n</td>
													<td class=texto colspan="3" nowrap>

													<?php echo $r_puntoventa->Nombre ?> </td>
												</tr>
														<tr>
													<td class=texto>Direcci&oacute;n</td>
															<td class=texto colspan="3" nowrap><?php echo $r_puntoventa->Direccion?></td>
														</tr>
														<tr>
													<td class=texto nowrap>Tel&eacute;fono</td>
															<td class=texto colspan="3" nowrap><?php echo $r_puntoventa->Telefono?></td>
														</tr>
                                                        <tr>
													<td class=texto nowrap>Vendedor</td>
													<td class=texto colspan="3" nowrap><?php echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado)." ".get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado);?></td>
										</tr>
														<tr>
													<td class=texto nowrap>Fecha Factura</td>
													<td class=texto colspan="3" nowrap><?php echo $r->FechaFactura?></td>
										</tr>

                                        <tr>
													<td class=texto nowrap>Fecha Creacion</td>
													<td class=texto colspan="3" nowrap><?php echo $r->FechaCreacion?></td>
										</tr>






                                                <tr>
													<td class=texto>Cliente</td>
													<td class=texto nowrap><?php echo get_field("Cliente","CONCAT(Nombre,' ',Apellido)","IDCliente",$r->IDCliente);?></td>
													<td class=texto></td>
													<td class=texto></td>
												</tr>
												<tr>
													<td class=texto nowrap>No. Documento</td>
													<td class=texto colspan="3" ><?php echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente);?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="4"><table class="bordertable" border="0" cellspacing="1" cellpadding="0" id=table1 width="90%">
										  <tr >
										    <td align="left" class="texto"><b>Ref.</b></td>
										    <td align="center" class="texto"><b>Vr. U</b></td>
										    <td align="center" class="texto"><b>Can</b></td>
										    <td align="center" class="texto"><b>Dto1</b></td>
										    <td align="center" class="texto" nowrap><b>Vr Dto</b></td>
										    <td align="center" class="texto"><b>Dto2</b></td>
										    <td align="center" class="texto" nowrap><b>Vr s/ IVA</b></td>
									      </tr>
										  <?php
												$sql_detalle = "SELECT * FROM DetalleFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta' ";
												$query_detalle = db_query($sql_detalle);
												$i = 0;
												$segunda = 0;
												while( $r_detalle = db_fetch_object( $query_detalle ) )
												{
													$class = repetition()?"texto":"texto";
													$i++;
											?>
										  <tr >
										    <td align="center" class="<?php echo $class?>"><?php
															$ref=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
															echo $ref;
															//echo substr($ref,0,4) . "<br>" . substr($ref,4,10);


                                                        	if( !empty( $r_detalle->CodigoTarjeta ) )
															{
																echo "-" . $r_detalle->CodigoTarjeta;
															}//end if
														?>

										      <br>
										      <?php
															echo get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));
														?></td>
										    <td align="center" class="<?php echo $class?>"><?php


                                                                                                                $precio_consultado = get_field("Precio","ValorVenta","IDPrecio",get_field("Referencia","IDPrecio","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))));
                                                                                                                (!$precio_consultado)?$precio_consultado = $r_detalle->PrecioU :$precio_consultado=$precio_consultado;

                                                                                                                echo number_format($precio_consultado);

                                                                                                                ?></td>
										    <td align="center" class="<?php echo $class?>"><?php echo $r_detalle->Cantidad?></td>
										    <td align="center" class="<?php echo $class?>"><?php echo number_format($r_detalle->DescuentoRef);//number_format($r_detalle->DescuentoRef);?>%</td>
										    <td align="right" class="<?php echo $class?>"><?php echo number_format($r_detalle->PrecioU);?></td>
										    <td align="center" class="<?php echo $class?>"><?php echo number_format($r_detalle->DescuentoPar);//number_format($r_detalle->DescuentoRef);?>%</td>
										    <td align="left" class="<?php echo $class?>"><?php
															$valorsin = ( $r_detalle->ValorU * ( 1 - ( $r_detalle->DescuentoPar / 100 ) ) ) * $r_detalle->Cantidad;
															echo number_format( $valorsin );
														?></td>
									      </tr>
										  <?php
													$Movimiento = get_field("Referencia","IDMovimiento","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
													if( !empty( $Movimiento ) )
														$segunda = 1;

													$Saldo = get_field("Referencia","Saldo","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
													if( $Saldo == "S" )
														$segunda = 1;
												}
											?>
									    </table></td>
									</tr>
									<tr>
										<td width="1" class=texto></td>
										<td class=texto width="76"></td>
										<td class=texto colspan="2">
											<div align="left"></div>
										</td>
									</tr>
                                    <!--
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">IVA</div>
										</td>
										<td class=texto align="right"><?php echo number_format($r->ValorIVASinBono)?></td>
									</tr>
                                    -->



                                    <?php if($r->ValorBono!="0" ): ?>
									<tr>
										<td class=texto></td>
										<td class=texto width="76"></td>
										<td width="79" nowrap class=texto>
											<div align="right">Total Factura</div>
										</td>
										<td width="22" align="right" class=texto><?php echo number_format($r->ValorTotalSinBono)?></td>
									</tr>
                                    <?php endif; ?>


                                    <?php if($r->ValorBono!="0"): ?>
									<tr>
										<td class=texto></td>
										<td class=texto width="76"></td>
										<td class=texto nowrap>
											<div align="right">Menos  bono fidelizaci&oacute;n</div>
										</td>
										<td class=texto align="right">-<?php echo number_format($r->ValorBono)?></td>
									</tr>
                                    <?php endif; ?>


                                    <?php if($r->ValorBono!="0"): ?>
									<tr>
										<td class=texto></td>
										<td class=texto width="76"></td>
										<td class=texto nowrap>
											<div align="right">Sub Total</div>
										</td>
										<td class=texto align="right"><?php echo number_format((int)$r->ValorTotalSinBono-(int)$r->ValorBono)?></td>
									</tr>
                                    <?php endif; ?>


                                    <!--
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">Valor sin IVA</div>
										</td>
										<td class=texto align="right"><?php echo number_format((int)$r->ValorTotal-(int)$r->ValorIVA)?></td>
									</tr>
                                    -->


									<tr>
										<td class=texto></td>
										<td class=texto width="76"></td>
										<td class=texto nowrap>
											<div align="right">Iva </div>
										</td>
										<td class=texto align="right"><?php echo number_format($r->ValorIVA)?></td>
									</tr>


									<tr>
										<td class=texto></td>
										<td class=texto width="76"></td>
										<td class=texto nowrap>
											<div align="right">Valor Neto</div>
										</td>
										<td class=texto align="right"><?php echo number_format($r->ValorTotal)?></td>
									</tr>
									<tr>
										<td class=texto></td>
										<td class=texto width="76"></td>
												<td class=texto colspan="2" nowrap>
											<div align="left">
														<b>
												FORMA DE PAGO</b></div>
										</td>
											</tr>
									<?php
									$sql_formapago = "SELECT * FROM FormaPagoFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta'";
									$query_formapago = db_query( $sql_formapago );

									while( $r_formapago = db_fetch_object( $query_formapago ) )
									{
										if($r_formapago->Valor <> 0)
										{
								?>
									<tr>
										<td class=texto></td>
										<td class=texto width="76"></td>
										<td class=texto>
											<div align="right">
												<?php echo get_field("FormaPago","Descripcion","IDFormaPago",$r_formapago->IDFormaPago)?></div>
										</td>
										<td class=texto><?php echo number_format($r_formapago->Valor)?></td>
									</tr>
									<?php 									}//end if($r_formapago->Valor <> 0)
								}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
								?>
									<tr>
										<td class="texto mensajefooter" colspan="4" align="justify">
											<?php
												$sql_mensje = "SELECT Mensaje
																	FROM Mensaje
																	WHERE Publicar = 'S'
																	AND Segunda = 'N'
																	AND FechaInicio <= CURDATE()
																	AND FechaFin >= CURDATE()
																	ORDER BY RAND()";
												$qry_mensaje = db_query( $sql_mensje );
												$r_mensaje = db_fetch_object( $qry_mensaje );
												echo nl2br( $r_mensaje->Mensaje)."<br>";
												if( $segunda == 1 )
												{
													$sql_mensje = "SELECT Mensaje
																	FROM Mensaje
																	WHERE Publicar = 'S'
																	AND Segunda = 'S'";
													$qry_mensaje = db_query( $sql_mensje );
													$r_mensaje = db_fetch_object( $qry_mensaje );
													echo nl2br( $r_mensaje->Mensaje)."<br>";
												}//end


												//echo $r->Resolucion;
												//echo "  Facturas desde ".$r_puntoventa->RDesde." Hasta ".$r_puntoventa->RHasta;
											?>
										</td>
									</tr>

                                    <?php
                                    if( !empty( $array_fidelizacion ) && $club_suavidad=="S")
									{
									?>
                                    	<tr>
                                            <td class="texto mensajefooter" colspan="4" align="justify">
                                            	Puntos Disponibles antes de la Compra: <?php echo $array_fidelizacion["puntoantescompra"] ?>
                                                Puntos Ultima Compra: <?php echo $array_fidelizacion["puntosultimacompra"] ?>
                                                Puntos redimidos en la &uacute;ltima compra: <?php echo $array_fidelizacion["puntoredimidos"] ?>
                                                Puntos Totales Acumulados sin redimir: <?php echo $array_fidelizacion["puntostotal"] ?>
                                                <?php
                                                if( !empty( $array_fidelizacion["puntosproxvence"] ) )
												{
												?>
                                                	Puntos Pr&oacute;ximos a Vencer: <?php echo $array_fidelizacion["puntosproxvence"] ?>
                                            	<?php
												}//end if
												?>
                                                <?php
                                                if( !empty( $array_fidelizacion["bonosproxvence"] ) )
												{
												?>
                                                	Bonos Pr&oacute;ximos a Vencer: <?php echo $array_fidelizacion["bonosproxvence"] ?>
                                            	<?php
												}//end if
												?>
                                            </td>
                                        </tr>
                                    <?php
									}//end if
									?>


                                    <tr>
                                    	<td class="texto" colspan="4" align="center">
																					<?php $ruta_redireccion="/admin/files/facturas/Factura".$r_puntoventa->Codigo.$r->NumeroFactura.".pdf"; ?>
                                        	<a href="/admin/files/facturas/Factura<?php echo $r_puntoventa->Codigo.$r->NumeroFactura?>.pdf">PDF</a>
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
echo "<script>window.location.href='".$ruta_redireccion."';</script>";

?>
</body>
</html>
