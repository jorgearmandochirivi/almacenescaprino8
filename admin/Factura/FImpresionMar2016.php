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
	font-size:5px;
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
											<table class=rowtable width="100%">
												


                                                <tr>
													<td class=texto colspan="4" nowrap>FACTURA DE VENTA: No. <? echo $r_puntoventa->Codigo.$r->NumeroFactura?></td>
												</tr>
                                                <tr>
													<td class=texto colspan="4">
                                                    	NIT <?=get_field( "NIT","NIT","IDNIT",1 );?>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    	R&eacute;gimen com&uacute;n
                                                    </td>
												</tr>


												<tr>
													<td width="38%" class=texto>Almac&eacute;n</td>
													<td class=texto colspan="3" nowrap>IMACAL LTDA. <?=$r_puntoventa->Nombre?> </td>
												</tr>
														<tr>
													<td class=texto>Direcci&oacute;n</td>
															<td class=texto colspan="3" nowrap><?=$r_puntoventa->Direccion?></td>
														</tr>
														<tr>
													<td class=texto>Tel&eacute;fono</td>
															<td width="14%"  nowrap class=texto><?=$r_puntoventa->Telefono?></td>
                                                                                                                <td width="12%" class=texto>Ven.</td>
													<td width="36%" nowrap class=texto><? echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado)." ".get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado);?></td>

														</tr>
														<tr>
													<td class=texto nowrap>Fecha Factura</td>
													<td class=texto colspan="3" nowrap><?=$r->FechaFactura?></td>
										</tr>
                                        
                                        <tr>
													<td class=texto nowrap>Fecha Creacion</td>
													<td class=texto colspan="3" nowrap><?=$r->FechaCreacion?></td>
										</tr>
												
                                                
                                                
                                                
                                                
                                                
                                                <tr>
													<td class=texto>Cliente</td>
													<td class=texto nowrap><? echo get_field("Cliente","CONCAT(Nombre,' ',Apellido)","IDCliente",$r->IDCliente);?></td>
													<td class=texto></td>
													<td class=texto></td>
												</tr>
												<tr>
													<td class=texto nowrap>No. Documento</td>
													<td class=texto colspan="3" ><? echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente);?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="4">
											<table class="bordertable" border="0" cellspacing="1" cellpadding="0" id=table1 width="100%">
												<tr >
													<td align="center" class="texto"><b>Referencia</b></td>
													
															
                                                                                                                        <td align="center" class="texto"><b>Vr. U</b></td>
                                                                                                                        <td align="center" class="texto"><b>Cant.</b></td>
															<td align="center" class="texto"><b>1er Desc.</b></td>
															<td align="center" class="texto" nowrap><b>Vr. Desc</b></td>
															<td align="center" class="texto"><b>2do Desc.</b></td>
															<td align="center" class="texto" nowrap><b>Vr. sin IVA</b></td>
												</tr>
												<?
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
													<td align="center" class="<?=$class?>">
														<?php 
															echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
															
															

                                                        	if( !empty( $r_detalle->CodigoTarjeta ) )
															{
																echo "-" . $r_detalle->CodigoTarjeta;
															}//end if
														?>
                                                            <br>T 
														<?php 
															echo get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));
														?>
                                                    </td>
													<td align="center" class="<?=$class?>"><?php 
                                                                                                        
                                                                                                        
                                                                                                                $precio_consultado = get_field("Precio","ValorVenta","IDPrecio",get_field("Referencia","IDPrecio","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))));
                                                                                                                (!$precio_consultado)?$precio_consultado = $r_detalle->PrecioU :$precio_consultado=$precio_consultado;
                                                                                                                
                                                                                                                echo number_format($precio_consultado);
                                                                                                                
                                                                                                                ?></td>
															<td align="center" class="<?=$class?>"><?php echo $r_detalle->Cantidad?></td>
															<td align="center" class="<?=$class?>"><?echo number_format($r_detalle->DescuentoRef);//number_format($r_detalle->DescuentoRef);?>%</td>
															<td align="right" class="<?=$class?>"><?echo number_format($r_detalle->PrecioU);?></td>
															<td align="center" class="<?=$class?>"><?echo number_format($r_detalle->DescuentoPar);//number_format($r_detalle->DescuentoRef);?>%</td>
															<td align="right" class="<?=$class?>">
														<?
															$valorsin = ( $r_detalle->ValorU * ( 1 - ( $r_detalle->DescuentoPar / 100 ) ) ) * $r_detalle->Cantidad;
															echo number_format( $valorsin );
														?>
													</td>
												</tr>
												<?
													$Movimiento = get_field("Referencia","IDMovimiento","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))); 											
													if( !empty( $Movimiento ) )
														$segunda = 1;
														
													$Saldo = get_field("Referencia","Saldo","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))); 											
													if( $Saldo == "S" )
														$segunda = 1;
												}
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
                                    <!--
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">IVA</div>
										</td>
										<td class=texto align="right"><?=number_format($r->ValorIVASinBono)?></td>
									</tr>
                                    -->
                                    
                                    
                                    
                                    <?php if($r->ValorBono!="0" ): ?>
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">Total Factura</div>
										</td>
										<td class=texto align="right"><?=number_format($r->ValorTotalSinBono)?></td>
									</tr>
                                    <?php endif; ?>
									
                                    
                                    <?php if($r->ValorBono!="0"): ?>
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">Menos  bono fidelizaci&oacute;n</div>
										</td>
										<td class=texto align="right">-<?=number_format($r->ValorBono)?></td>
									</tr>
                                    <?php endif; ?>
                                    
                                    
                                    <?php if($r->ValorBono!="0"): ?>
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">Sub Total</div>
										</td>
										<td class=texto align="right"><?=number_format((int)$r->ValorTotalSinBono-(int)$r->ValorBono)?></td>
									</tr>
                                    <?php endif; ?>
									
                                    
                                    <!--
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">Valor sin IVA</div>
										</td>
										<td class=texto align="right"><?=number_format((int)$r->ValorTotal-(int)$r->ValorIVA)?></td>
									</tr>
                                    -->
                                    

									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">Iva </div>
										</td>
										<td class=texto align="right"><?=number_format($r->ValorIVA)?></td>
									</tr>
                                    
                                    
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">Valor Neto</div>
										</td>
										<td class=texto align="right"><?=number_format($r->ValorTotal)?></td>
									</tr>
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
												<td class=texto colspan="2" nowrap>
											<div align="left">
														<b>
												FORMA DE PAGO</b></div>
										</td>
											</tr>
									<? 
									$sql_formapago = "SELECT * FROM FormaPagoFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta'";
									$query_formapago = db_query( $sql_formapago );
									
									while( $r_formapago = db_fetch_object( $query_formapago ) )
									{
										if($r_formapago->Valor <> 0)
										{
								?>
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto>
											<div align="right">
												<?php echo get_field("FormaPago","Descripcion","IDFormaPago",$r_formapago->IDFormaPago)?></div>
										</td>
										<td class=texto><?=number_format($r_formapago->Valor)?></td>
									</tr>
									<? 									}//end if($r_formapago->Valor <> 0)
								}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
								?>
									<tr>
										<td class="texto mensajefooter" colspan="4" align="center">
											<?
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
												
															
												echo $r->Resolucion;
												echo "  Facturas desde ".$r_puntoventa->RDesde." Hasta ".$r_puntoventa->RHasta;
											?>
										</td>
									</tr>
                                    
                                    <?
                                    if( !empty( $array_fidelizacion ) && $club_suavidad=="S")
									{
									?>
                                    	<tr>
                                            <td class="texto mensajefooter" colspan="4" align="center">
                                            	Puntos Disponibles antes de la Compra: <?=$array_fidelizacion["puntoantescompra"] ?> 
                                                Puntos Ultima Compra: <?=$array_fidelizacion["puntosultimacompra"] ?> 
                                                Puntos redimidos en la &uacute;ltima compra: <?=$array_fidelizacion["puntoredimidos"] ?> 
                                                Puntos Totales Acumulados sin redimir: <?=$array_fidelizacion["puntostotal"] ?> 
                                                <?
                                                if( !empty( $array_fidelizacion["puntosproxvence"] ) )
												{
												?>
                                                	Puntos Pr&oacute;ximos a Vencer: <?=$array_fidelizacion["puntosproxvence"] ?> 
                                            	<?
												}//end if
												?>
                                                <?
                                                if( !empty( $array_fidelizacion["bonosproxvence"] ) )
												{
												?>
                                                	Bonos Pr&oacute;ximos a Vencer: <?=$array_fidelizacion["bonosproxvence"] ?> 
                                            	<?
												}//end if
												?>
                                            </td>
                                        </tr>
                                    <?
									}//end if
									?>
                                    
                                    
                                    <tr>
                                    	<td class="texto" colspan="4" align="center">
                                        	<a href="/admin/files/facturas/Factura<?=$r_puntoventa->Codigo.$r->NumeroFactura?>.pdf">Ver pdf</a>
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
