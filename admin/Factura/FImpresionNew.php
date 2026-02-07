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
	
	$Table = "Factura";
	$TableJoin = "Factura";
	$Key = "IDFactura";
	
	$qid = db_query(" SELECT * FROM Factura WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto' ");
		
	$r = db_fetch_object($qid);
?>
<html>
<head>
<title>Imprimir Factura</title>
<style type="text/css">
<!--
.texto1 {	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
}
.texto1 {font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
}
-->
</style>
</head>
<style>
<!--
@page { size 12cm 12cm; }
@media print{
.texto {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
}
.bordertable {border: dotted 1px; color:#c3c3c3}
#content { margin-left:0; 
     float:none; 
     width:auto;
     height : 150px; 
     color:black; 
     font-size; 12pt }

-->
}
.texto {	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
}
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
	
	<FORM name="frm" method="post" enctype="multipart/form-data" action="<?php echo $PHP_SELF?>" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>
			<table class="forumline" width="314" cellspacing="1" border="0" align="center" height="100" id="#content">
		<tr>
			<td valign="top">
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff">
					<tr>
						<td colspan="2">
							<div align="center">
								<table width=100% border=0>
									<tr>
										<td colspan="4">
											<table width="443" class=rowtable>
												<tr>
												  <td width="108" class=texto></td>
												  <td class=texto colspan="3" align="right" nowrap><strong>CALZADO CAPRINO</strong><br>
											      <?php echo $r_puntoventa->Nombre?></td>
											  </tr>
												<tr>
												  <td colspan="4" nowrap class=texto>
                                                  
                                                  	<p><span class="texto1">INDUSTRIA MANUFACTURERA DE CALZADO LTDA IMACAL LTDA<br>
                                                  	  
                                                  	  NIT: <?php echo get_field( "NIT","NIT","IDNIT",1 );?>
                                               	    </span></p>                                               	  </td>
											  </tr>
												
														<tr>
													<td class=texto nowrap>Fecha Factura</td>
													<td class=texto colspan="3"><?php echo $r->FechaFactura?></td>
												</tr>
														<tr>
													<td class=texto>Cliente</td>
													<td width="172" nowrap class=texto><?php echo get_field("Cliente","CONCAT(Nombre,' ',Apellido)","IDCliente",$r->IDCliente);?></td>
													<td width="28" class=texto>&nbsp;</td>
													<td width="27" class=texto>&nbsp;</td>
												</tr>
											</table>										</td>
									</tr>
									<tr>
										<td colspan="4">
											<table class="bordertable" border="0" cellspacing="1" cellpadding="0" id=table1 width="100%">
												<tr >
													<td align="center" class="texto"><b>Referencia</b></td>
													<td align="center" class="texto"><b>Talla</b></td>
															<td align="center" class="texto"><b>Cant.</b></td>
															<td align="center" class="texto"><b>Desc Ref.</b></td>
															<td align="center" class="texto" nowrap><b>Vr. U.</b></td>
															<td align="center" class="texto"><b>Desc.</b></td>
															<td align="center" class="texto" nowrap><b>Vr. sin IVA</b></td>
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
													<td align="center" class="<?php echo $class?>"><?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))?></td>
													<td align="center" class="<?php echo $class?>"><?php echo get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))?></td>
															<td align="center" class="<?php echo $class?>"><?php echo $r_detalle->Cantidad?></td>
															<td align="center" class="<?php echo $class?>"><?php echo number_format($r_detalle->DescuentoRef);//number_format($r_detalle->DescuentoRef);?>%</td>
															<td align="right" class="<?php echo $class?>"><?php echo number_format($r_detalle->PrecioU);?></td>
															<td align="center" class="<?php echo $class?>"><?php echo number_format($r_detalle->DescuentoPar);//number_format($r_detalle->DescuentoRef);?>%</td>
															<td align="right" class="<?php echo $class?>">
														<?php
															$valorsin = ( $r_detalle->ValorU * ( 1 - ( $r_detalle->DescuentoPar / 100 ) ) ) * $r_detalle->Cantidad;
															echo number_format( $valorsin );
														?>													</td>
												</tr>
												<?php
													$Movimiento = get_field("Referencia","IDMovimiento","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))); 											
													if( !empty( $Movimiento ) )
														$segunda = 1;
												}
											?>
											</table>										</td>
									</tr>
									<tr>
										<td class=texto></td>
										<td class=texto width="250"></td>
										<td class=texto colspan="2">
											<div align="left"></div>										</td>
									</tr>
									<tr>
										<td class=texto></td>
										<td class=texto width="250"></td>
										<td class=texto nowrap>
											<div align="right">IVA</div>										</td>
										<td class=texto align="right"><?php echo number_format($r->ValorIVA)?></td>
									</tr>
									<tr>
										<td class=texto></td>
										<td class=texto width="250"></td>
										<td class=texto nowrap>
											<div align="right">Total</div>										</td>
										<td class=texto align="right"><?php echo number_format($r->ValorTotal)?></td>
									</tr>
									<tr>
										<td class=texto></td>
										<td class=texto width="250"></td>
												<td class=texto colspan="2" nowrap>
											<div align="left">
														<b>
												FORMA DE PAGO</b></div>										</td>
								  </tr>
									<?php 									$sql_formapago = "SELECT * FROM FormaPagoFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta'";
									$query_formapago = db_query( $sql_formapago );
									
									while( $r_formapago = db_fetch_object( $query_formapago ) )
									{
										if($r_formapago->Valor <> 0)
										{
								?>
									<tr>
										<td class=texto></td>
										<td class=texto width="250"></td>
										<td class=texto>
											<div align="right">
												<?php echo get_field("FormaPago","Descripcion","IDFormaPago",$r_formapago->IDFormaPago)?></div>										</td>
										<td class=texto><?php echo number_format($r_formapago->Valor)?></td>
									</tr>
									<?php 									}//end if($r_formapago->Valor <> 0)
								}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
								?>
									<tr>
									  <td class="texto" colspan="4" align="center"><table width="100%" class=rowtable>

                                        <tr>
                                          <td class=texto1><strong>Tiquete No.</strong></td>
                                          <td class=texto1 nowrap><?php echo $r_puntoventa->Codigo.$r->NumeroFactura?></td>
                                          <td class=texto1><strong>Vendedor</strong></td>
                                          <td nowrap class=texto1><?php echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado)." ".get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado);?></td>
                                        </tr>

                                      </table></td>
								  </tr>
									<tr>
										<td class="texto" colspan="4" align="center">
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
												
															
												echo $r->Resolucion;
												echo "  Facturas desde ".$r_puntoventa->RDesde." Hasta ".$r_puntoventa->RHasta;
											?>										<br>
											R&eacute;gimen com&uacute;n</td>
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
