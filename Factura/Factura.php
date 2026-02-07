<body> <?php

		$TitleMod = "Factura";

		$Table = "Factura";
		$TableJoin = "Factura";
		$Key = "IDFactura";
		$MOD = "Factura";
		$m = "Factura";

		$permisos = get_permiso($ID_Usuario, $m, $Table);
		if ($permisos[0] >= 2) {
			switch (nvl($action)) {
				case "update":
					db_query("SET AUTOCOMMIT=0");
					db_query("BEGIN");

					//Actualizar Cuotas
					foreach ($IDCuota as $key => $value) {
						$fechapago = "FechaPago" . $key;
						if (!empty($_POST[$fechapago])) {
							echo "Atencion:: Por este modulo no es posible actualizar las cuotas, por favor dirijase a Facturas - > Pagos de creditos";
							exit;

							//inserto los punto spor la cuota
							//consulto los puntos
							$sql_puntos_anterior = "Select * from PuntosClienteFidelizacion Where IDCliente='" . $_POST[IDCliente] . "' and IDPuntoVenta = '" . $_POST[IDPuntoVenta] . "' and IDFactura = '" . $_POST[IDFactura] . "' limit 1";
							$qry_puntos_anterior = db_query($sql_puntos_anterior);
							while ($row_punto = db_fetch_array($qry_puntos_anterior)) {
								echo $sql_puntos = " INSERT INTO PuntosClienteFidelizacion (IDCliente, IDPuntoVenta, IDFactura,IDReglaPunto,NombreRegla, DescripcionRegla, Puntos, FechaVencimiento,ObservacionesRegla, FechaTrCr) VALUES ('" . $_POST[IDCliente] . "','" . $_POST[IDPuntoVenta] . "','" . $_POST[IDFactura] . "', '" . $row_punto[IDReglaPunto] . "',  '" . $row_punto[NombreRegla] . "','" . $row_punto[DescripcionRegla] . "','" . (int)$row_punto[Puntos] . "','" . $row_punto[FechaVencimiento] . "', '" . $row_punto[ObservacionesRegla] . " Cuota" . "',  NOW() ) ";
								$qry_puntos = db_query($sql_puntos);
							}

							//$sql_update = " UPDATE CreditoCuota SET FechaPago = '$_POST[$fechapago]' WHERE IDFactura = '$_POST[IDFactura]' AND IDPuntoVenta = '$_POST[IDPuntoVenta]' AND IDCuota = '$key' ";
							//$qry_update = db_query( $sql_update );

						} //end if
					} //end for

					//$sql_cuotas = " SELECT * FROM CreditoCuota WHERE IDFactura = '$_POST[IDFactura]' AND IDPuntoVenta = '$_POST[IDPuntoVenta]' AND FechaPago = '0000-00-00 00:00:00'  ";
					//$qry_cuotas = db_query( $sql_cuotas );
					//if( db_num_rows( $qry_cuotas ) == 0  )
					//{
					//	db_query( "UPDATE Credito SET Cancelado = 'S' " );
					//}//end if


					//db_query( "tales" );
					db_query("COMMIT");

					echo "<script>location.href='?mod=" . $MOD . "&action=edit&id=" . $_POST[IDFactura] . "'</script>";

					break;
				case "edit":
					print_form($id, "update", "Actualizar $TitleMod", "Realizar Cambios");
					break;
				case "list":

					if ($field == "NumeroReferencia") {

						$sql = " SELECT * FROM Referencia R, PuntoVentaReferencia PR, CodificacionEspecifica CE, DetalleFactura DF, Factura F
								WHERE R.Numero LIKE '%$QryString%'
								AND R.IDReferencia = PR.IDReferencia
								AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia
								AND CE.IDCodificacionEspecifica = DF.IDCodificacionEspecifica
								AND DF.IDFactura = F.IDFactura
								AND DF.IDPuntoVenta = F.IDPuntoVenta
								AND DF.IDPuntoVenta = '" . $IDPuntoVenta . "'
								GROUP BY F.IDFactura
								ORDER BY F.FechaFactura DESC ";
					} //end if
					elseif ($field == "Items" && (int)$QryString > 0) {
						$sql = " SELECT count(DF.IDFactura) as TotalProductos, F.* FROM  CodificacionEspecifica CE, DetalleFactura DF, Factura F
								WHERE
								DF.IDFactura = F.IDFactura GROUP BY F.IDFactura having count(DF.IDFactura) > $QryString ORDER BY F.FechaFactura DESC ";
					} else {
						$sql = make_qry_string($HTTP_GET_VARS);
					}


					list_r($sql);
					break;
				default:
					list_r();
					break;
			} // End switch

		} //end if(permisos[0] > 2)
		else
			echo Mensaje_Info("No tiene Permisos Suficientes", "col2");



		/*******************************************************************************************
		funtcion Print_form
		 *******************************************************************************************/
		function print_form($id, $newmode, $title, $submit_caption)
		{
			global $TitleMod, $Table, $MOD, $Key, $ID_Usuario, $IVA, $IDPuntoVenta, $crypt;

			if (!empty($_GET[IDPuntoVenta]))
				$IDPuntoVenta = $_GET[IDPuntoVenta];


			$qid = db_query(" SELECT * FROM Factura WHERE IDFactura = '$id' AND IDPuntoVenta = '$IDPuntoVenta' ");

			$r = db_fetch_object($qid);


			$club_suavidad = get_field("Cliente", "ClubSuavidad", "IDCliente", $r->IDCliente);

		?>


		<br>
		<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">

			<tr>
				<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
				</td>
				<td class="tbtbot"><b></b>
					<span class="gen">
						<?= $title ?>
					</span>
				</td>
				<td class="tbtr">
					<img src="images/spacer.gif" alt="" width="124" height="22" />
				</td>
			</tr>
		</table>
		<FORM name="frm" method="post" enctype="multipart/form-data" action="<?= $PHP_SELF ?>" <?php if ($newmode != "delete") { ?>onsubmit="return EvaluaReg(this,Check)" <?php } ?>>
			<table class="forumline" width="550" cellspacing="1" border="0" align="center">
				<tr>
					<td>
						<table width=100% border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff">

							<tr>
								<td colspan="2">

									<div align="center">
										<table width=100% border=0>
											<tr>
												<td colspan="4">
													<table class=rowtable>
														<tr>
															<td class=col1>Nro Comprobante</td>
															<td class=col2><input type="text" class="tbox" name="NumeroFactura" id="Numero Factura" size="24" value="<?= $r->NumeroFactura ?>">

															</td>

															<td class=col1>
																<?php if ($r->Estado == "ELECTRONICA") {
																	echo "Numero factura electr&oacute;nica: ";
																} ?>
															</td>
															<td class=col1><?php if ($r->Estado == "ELECTRONICA") {
																				echo $r->NumeroFacturaElectronica;
																			} ?></td>
														</tr>
														<tr>
															<td class=col1>Estado</td>
															<td class=col2 colspan="3">
																<?= $r->Estado ?>
															</td>
														</tr>
														<tr>
															<td class=col1>Punto de Venta</td>
															<td class=col2 colspan="3">
																<?php echo get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r->IDPuntoVenta); ?>
																<input type="hidden" value="<?= $IDPuntoVenta ?>" name="IDPuntoVenta">
																<input type="hidden" value="<?= $r->IDFactura ?>" name="IDFactura">
															</td>
														</tr>
														<tr>
															<td class=col1>Fecha Facturas</td>
															<td class=col2 colspan="3"><input type="text" class="tbox" name="FechaFactura" size="19" value="<?= $r->FechaFactura ?>" readonly>
																<script language="JavaScript1.2">

																</script> Fecha Creacion: <?= $r->FechaCreacion ?>
															</td>
														</tr>
														<tr>
															<td class=col1>Observaciones</td>
															<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?= $r->Observaciones ?></textarea></td>
														</tr>

														<tr>
															<td class=col1>Nro Pagare</td>
															<td class=col2 colspan="3">
																<input type="text" class="tbox" name="NumeroPagare" size="15" value='<?php echo $r->NumeroPagare; ?>' <?php if (!empty($r->NumeroPagare)) echo "readOnly"; ?>>
															</td>
														</tr>


														<tr>
															<td class=col1> </td>
															<td class=col1></td>
															<td class=col1></td>
															<td class=col1></td>
														</tr>
														<tr>
															<td class=row1 colspan="4"><b>CLIENTE</b></td>
														</tr>
														<tr>
															<td class=col1>C&eacute;dula</td>
															<td class=col2><input type="text" class="tbox" name="Cedula" readonly size="15" value='<?php echo get_field("Cliente", "Cedula", "IDCliente", $r->IDCliente); ?>'><input type="hidden" name="IDCliente" id="Cliente" value="<?= $r->IDCliente ?>"></td>
															<td class=col1>Nombre</td>
															<td class=col2><input type="text" class="tbox" name="NombreCliente" readonly size="20" value='<?php echo get_field("Cliente", "CONCAT(Nombre,' ',Apellido)", "IDCliente", $r->IDCliente); ?>'></td>
														</tr>
														<tr>
															<td class=col1 nowrap>Telefono Cliente</td>
															<td class=col2><input type="text" class="tbox" name="TeleCli" readonly size="15" value='<?php echo get_field("Cliente", "Telefono", "IDCliente", $r->IDCliente); ?>'></td>
															<td class="col1" nowrap="nowrap">Numero de Fidelizacion</td>
															<td class="col2"><input name="NumeroFidelizacion" type="text" class="tbox" id="NumeroFidelizacion" value='<?php echo $r->NumeroFidelizacion ?>' size="20" readonly /></td>
														</tr>
														<tr>
															<td class=col1 nowrap>Numero Payu<br></td>
															<td class=col2><?php echo $r->NumeroPayu; ?></td>
															<td class=col1>Numero Addi</td>
															<td class=col1><?php echo $r->NumeroAddi; ?></td>
														</tr>
														<tr>
															<td class=row1><b>VENDEDOR</b></td>
															<td class=row1 colspan="3"><input type="button" class="button" name="empleado" value="Buscar" onClick="window.open('Empleado/popEmpleados.php?IDPuntoVenta=<?= $IDPuntoVenta ?>','','width=400,height=400');"></td>
														</tr>
														<tr>
															<td class=col1>C&eacute;dula</td>
															<td class=col2><input type="text" class="tbox" name="CedulaEmpleado" readonly size="15" value='<?php echo get_field("Empleado", "Cedula", "IDEmpleado", $r->IDEmpleado); ?>'> <input type="hidden" id="Empleado" name="IDEmpleado" value=""></td>
															<td class=col1>Nombre</td>
															<td class=col2><input type="text" class="tbox" name="NombreEmpleado" readonly size="20" value='<?php echo get_field("Empleado", "Nombre", "IDEmpleado", $r->IDEmpleado) . " " . get_field("Empleado", "Apellidos", "IDEmpleado", $r->IDEmpleado); ?>'></td>
														</tr>
														<tr>
															<td class=col1><br></td>
															<td class=col1></td>
															<td class=col1></td>
															<td class=col1></td>
														</tr>
														<tr>
															<td class=row1 colspan="4"><b>CREDITO</b></td>
														</tr>
														<tr>
															<td class=col1>Financiaci&oacute;n</td>
															<td class=col2 colspan="3"><input type="text" class="tbox" name="Descuento" size="3" value="<?= $r->Descuento ?>" maxlength="3">%</td>
														</tr>
														<tr>
															<td class=col1>Comentario Descuento Especial</td>
															<td class=col2 colspan="3"><textarea class="tareabox" name="ObservacionDescuento" rows="4" cols="64"><?= $r->ObservacionDescuento ?></textarea></td>
														</tr>
														<tr>
															<td class=col1>Alianzas</td>
															<td class=col2 colspan="3">
																<?php if (!empty($r->IDAlianza)):
																	echo get_field("Alianza", "Nombre", "IDAlianza", $r->IDAlianza) . " - " . $r->DescuentoAlianza . "%";
																endif;
																?>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td class=navpic>Detalle Factura</td>
												<td class=navpic colspan="3">
													<div align="right">
													</div>
												</td>
											</tr>
											<tr bgcolor=#e7ebef>
												<td colspan="4">
													<table class="texto" border="0" cellspacing="1" cellpadding="0" id=table1 width="100%">
														<tr bgcolor="#dfe3e7">
															<td align="center"><b>Item</b></td>
															<td align="center"><b>Referencia</b></td>
															<td align="center"><b>Talla</b></td>
															<td align="center"><b>Nombre</b></td>
															<td align="center"><b>Cantidad</b></td>
															<td align="center"><b>Valor U.</b></td>
															<td align="center"><b>Desc. Par.</b></td>
															<td align="center"><b>Total</b></td>
														</tr>
														<?php
														$sql_detalle = "SELECT * FROM DetalleFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$IDPuntoVenta' ";
														$query_detalle = db_query($sql_detalle);
														$i = 0;
														while ($r_detalle = db_fetch_object($query_detalle)) {
															$class = repetition() ? "col1list" : "col2list";
															$i++;
														?>
															<tr bgcolor="#dfe3e7">
																<td align="left" class="<?= $class ?>"><b><?= $i ?></b></td>
																<td align="left" class="<?= $class ?>">
																	<?php
																	echo get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)));
																	if (!empty($r_detalle->CodigoTarjeta)) {
																		echo "-" . $r_detalle->CodigoTarjeta;
																	} //end if
																	?>
																</td>
																<td align="left" class="<?= $class ?>"><?php echo get_field("Talla", "Descripcion", "IDTalla", get_field("CodificacionEspecifica", "IDTalla", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica)) ?></td>
																<td align="left" class="<?= $class ?>"><?php echo $ref = get_field("Referencia", "Nombre", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $r_detalle->IDCodificacionEspecifica))) ?></td>
																<td align="left" class="<?= $class ?>"><?php echo $r_detalle->Cantidad ?></td>
																<td align="left" class="<?= $class ?>"><?php echo number_format($r_detalle->ValorU); ?></td>
																<td align="left" class="<?= $class ?>"><?php echo number_format($r_detalle->DescuentoPar); ?>%</td>
																<td align="left" class="<?= $class ?>"><?php echo number_format(($r_detalle->ValorU * $r_detalle->Cantidad) * (1 - ($r_detalle->DescuentoPar / 100))); ?></td>
															</tr>
														<?php
														}
														?>
													</table>
												</td>
											</tr>
											<tr>
												<td class=col1></td>
												<td class=col1 width="250"></td>
												<td class=navpic colspan="2">
													<div align="left">
														RESUMEN FACTURA</div>
												</td>
											</tr>

											<!--
										<tr>
										  <td class=col1></td>
										  <td class=col1></td>
										  <td class=col2>Valor IVA</td>
										  <td class=col2><input type=text readonly name=ValorIVA2 value="<?= number_format($r->ValorIVASinBono) ?>" class=tbox size=15></td>
									  </tr>
                                      -->

											<?php if ($r->ValorBono != "0"): ?>
												<tr>
													<td class=col1></td>
													<td class=col1></td>
													<td class=col2>Total Factura</td>
													<td class=col2><input type=text readonly name=ValorIVA3 value="<?= number_format($r->ValorTotalSinBono) ?>" class=tbox size=15></td>
												</tr>
											<?php endif; ?>


											<?php if ($r->ValorBono != "0"): ?>
												<tr>
													<td class=col1></td>
													<td class=col1></td>
													<td class=col2>Menos Bono fidelizaci&oacute;n</td>
													<td class=col2><input type=text readonly name=ValorIVA4 value="-<?= number_format($r->ValorBono) ?>" class=tbox size=15></td>
												</tr>
											<?php endif; ?>

											<?php if ($r->ValorBono != "0"): ?>
												<tr>
													<td class=col1></td>
													<td class=col1></td>
													<td class=col2>Sub Total</td>
													<td class=col2><input type=text readonly name=ValorIVA5 value="<?= number_format((int)$r->ValorTotalSinBono - (int)$r->ValorBono) ?>" class=tbox size=15></td>
												</tr>
											<?php endif; ?>

											<!--
										<tr>
										  <td class=col1></td>
										  <td class=col1></td>
										  <td class=col2>Valor sin IVA</td>
										  <td class=col2><input type=text readonly name=ValorIVA6 value="<?= number_format((int)$r->ValorTotal - (int)$r->ValorIVA) ?>" class=tbox size=15></td>
									  </tr>
                                      -->
											<tr>
												<td class=col1></td>
												<td class=col1 width="250"></td>
												<td class=col2>
													<div align="right">
														Valor IVA</div>
												</td>
												<td class=col2><input type=text readonly name=ValorIVA value="<?= number_format($r->ValorIVA) ?>" class=tbox size=15></td>
											</tr>
											<tr>
												<td class=col1></td>
												<td class=col1 width="250"></td>
												<td class=col2>
													<div align="right">
														Valor Neto</div>
												</td>
												<td class=col2><input type=text readonly name=ValorTotal value="<?= number_format($r->ValorTotal) ?>" class=tbox size=15></td>
											</tr>

											<tr>
												<td class=col1></td>
												<td class=col1 width="250"></td>
												<td class=col2>
													<div align="right">
														Valor Envio</div>
												</td>
												<td class=col2><input type=text readonly name=ValorEnvioFactura value="<?= number_format($r->ValorEnvioFactura) ?>" class=tbox size=15></td>
											</tr>

											<tr>
												<td class=col1></td>
												<td class=col1 width="250"></td>
												<td class=navpic colspan="2">
													<div align="left">
														FORMA DE PAGO</div>
												</td>
											</tr>
											<?php
											$sql_formapago = "SELECT * FROM FormaPagoFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta' ";
											$query_formapago = db_query($sql_formapago);

											while ($r_formapago = db_fetch_object($query_formapago)) {
												if ($r_formapago->Valor <> 0) {
											?>
													<tr>
														<td class=col1></td>
														<td class=col1 width="250"></td>
														<td class=col2>
															<div align="right">
																<?php echo get_field("FormaPago", "Descripcion", "IDFormaPago", $r_formapago->IDFormaPago) ?></div>
														</td>
														<td class=col2><input type=text readonly name="formapago[<?= $r_formapago->IDFormaPago ?>]" value="<?= number_format($r_formapago->Valor) ?>" class=tbox size=15></td>
													</tr>
												<?php
												} //end if($r_formapago->Valor <> 0)
											} //end while( $r_formapago = db_fetch_object( $query_formapago ) )

											$sql_credito = "SELECT * FROM Credito WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta'";
											$qry_credito = db_query($sql_credito);
											$r_credito = db_fetch_object($qry_credito);
											if (db_num_rows($qry_credito) > 0) {

												?>

												<tr>
													<td class="navpic" colspan="4" align="left">
														<b>Cuotas Factura - No Credito <?= $r_credito->NumeroDocumento ?></b>
													</td>
												</tr>
												<tr>
													<td colspan="4" align="center">
														<table width=100%>
															<tr bgcolor="#dfe3e7">
																<td align="center"><b>Cuota Numero</b></td>
																<td align="center"><b>Fecha Cuota</b></td>
																<td align="center"><b>Fecha Pago</b></td>
																<td align="center"><b>Medio Pago</b></td>
																<td align="center"><b>Valor Cuota</b></td>
															</tr>

															<?php
															$sql_cuotas = "SELECT * FROM CreditoCuota WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta' ";
															$qry_cuotas = db_query($sql_cuotas);
															while ($r_cuotas = db_fetch_object($qry_cuotas)) {
																$class = repetition() ? "col1list" : "col2list";
															?>
																<tr>
																	<td class="<?= $class ?>" align="center"><?= $r_cuotas->IDCuota ?></td>
																	<td class="<?= $class ?>" align="center"><?= $r_cuotas->FechaCuota ?></td>
																	<td class="<?= $class ?>" align="center">
																		<?php
																		if ($r_cuotas->FechaPago <> "0000-00-00 00:00:00")
																			echo $r_cuotas->FechaPago;
																		else {
																		?>
																			<input type="text" class="tbox" name="FechaPago<?= $r_cuotas->IDCuota ?>" size="19" value=''>
																			<script language="JavaScript1.2">
																				<!--
																				if (!document.layers)
																					document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaPago<?= $r_cuotas->IDCuota ?>,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
																				//
																				-->
																			</script>
																		<?php
																		} //end else
																		?>
																	</td>
																	<td class="<?= $class ?>" align="center"><?= $r_cuotas->MedioPago ?></td>
																	<td class="<?= $class ?>" align="center">
																		<?= number_format($r_cuotas->ValorTotal, 0) ?>
																		<?php
																		if ($r_cuotas->FechaPago == "0000-00-00 00:00:00")
																			$saldo_cuota += $r_cuotas->ValorTotal; ?>
																		<input type=hidden name=IDCuota[<?= $r_cuotas->IDCuota ?>] value="<?= $r_cuotas->IDCuota ?>">
																	</td>
																</tr>


															<?php
															} //end while
															?>

															<tr bgcolor="#dfe3e7">
																<td align="center">&nbsp;</td>
																<td align="center">&nbsp;</td>
																<td align="center">&nbsp;</td>
																<td align="center"><strong>Saldo</strong></td>
																<td align="center"><strong>$<?php echo number_format($saldo_cuota); ?></strong></td>
															</tr>

														</table>
													</td>
												</tr>
												<tr>
													<td colspan="4" align="center">
														<input type="submit" name="Submit" value="Actualizar Pagos" class="submit">
													</td>
												</tr>
											<?php
											} //end if cuotas
											?>

											<tr>
												<td class="navpic" colspan="4" align="center">
													<?php
													echo $r->Resolucion;
													echo "  Facturas desde " . $r->RDesde . " Hasta " . $r->RHasta;
													?>
												</td>
											</tr>
										</table>
										<input type="hidden" name="action" value="<?= $newmode ?>"><br>
									</div>

								</td>
							</tr>

							<!--
				<tr>
                    	<td style="color:#EE080C;">
                        	<?php if ($IDPuntoVenta == 19) { ?>
                            <a href="#" onClick="window.open( 'Factura/FImpresion.php?id=<?php echo $r->IDFactura ?>&idpunto=<?= $IDPuntoVenta ?>','','width=426, height=350' )">
                            Reimprimir
                            </a>
							<?php } ?>
                        </td>
                    </tr>
				-->

							<?php
							if (date("Y-m-d") >= "2023-10-01" && date("Y-m-d") <= "2023-11-30") {
								$sql_iva = "SELECT IDBonoIva FROM BonoIva WHERE IDFactura = '" . $r->IDFactura . "' and IDPuntoVenta = '" . $IDPuntoVenta . "'  LIMIT 1";
								$r_iva = db_query($sql_iva);
								$row_iva = db_fetch_array($r_iva);
								if ($row_iva["IDBonoIva"] > 0) {
									$rutabono = "BonoIva" . $row_iva["IDBonoIva"] . ".pdf";
								}




							?>
								<tr>
									<td style="color:#EE080C;">
										<a href="#" onClick="window.open( 'https://www.almacenescaprino.com/admin/files/facturas/<?php echo $rutabono; ?>','','width=426, height=350' )">
											Reimprimir Bono Iva
										</a>

									</td>
								</tr>

							<?php } ?>



						</table>
					</td>
				</tr>



			</table>
		</FORM>
		<?php
		} // END function print_form_fotos($id,$numfotos)
		/*******************************************************************************************
		funcion Listar
		 *******************************************************************************************/
		function list_r($sql = "")
		{
			global $TitleMod, $MOD, $Table, $Key, $listar, $IDPuntoVenta;
			if (empty($sql))
				$sql =  "SELECT * FROM $Table WHERE IDPuntoVenta = '$IDPuntoVenta' ORDER BY FechaFactura DESC";

			$nav = new buildNav;
			$nav->offset = 'offset';
			$nav->number_type = 'number';
			(!empty($listar)) ? $nav->limit = $listar : $nav->limit = 40;
			$nav->execute($sql, $dblink);
			$total_records =  $nav->total_result;
			$rows = $nav->rows;
			$result = $nav->sql_result;
			$row = $offset;
			$startrow = $offset + 1;
			$finalrow = ($row * $nav->limit) + $rows;

			$pages = $nav->show_num_pages('&laquo;', '&laquo; prev', '&raquo;', 'next &raquo;', '|', 'class=navvar');   // show pages

			$info = $nav->show_info();

			if ($_GET['in_order'] == "ASC" || $_GET['in_order'] == "") {
				$img = "down.png";
				$order = "DESC";
			} else if ($_GET['in_order'] == "DESC") {
				$img = "up.png";
				$order = "ASC";
			}

		?><?php
								if ($rows > 0) {
								?><br>
		<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="650">

			<tr>
				<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
				</td>
				<td class="tbtbot"><b></b>
					<span class="gen">
						Listar <?php echo $TitleMod ?>
					</span>
					<span class="gen">
						<?php echo $info ?>
					</span>
				</td>
				<td class="tbtr">
					<img src="images/spacer.gif" alt="" width="124" height="22" />
				</td>
			</tr>
		</table>
		<table class="forumline" width="650" cellspacing="1" border="0" align="center">
			<tr>
				<td>
					<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline">
						<tr>
							<td class="forumlink" colspan="2">
								<?php filtrar(); ?>
							</td>
						</tr>
						<tr>
							<td class="forumlink" colspan="2">
								<a href="Factura/exportafactura.php?sql=<?php echo $sql; ?>"><img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0">Exportar Registros </a>
								<table width=100% border=0 cellspacing=1 cellpadding=0>
									<tr>
										<td align=center class=navpic valign=middle bgcolor=#DBEAF5 width=69>Ver</td>
										<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=IDCliente&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Cliente&nbsp;<?php if ($_GET['order_by'] == "IDCliente") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
										<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroFactura&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Nro Comprobante&nbsp;<?php if ($_GET['order_by'] == "NumeroFactura") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
										<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=FechaFactura&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">FechaFactura&nbsp;<?php if ($_GET['order_by'] == "FechaFactura") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
										<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=ValorTotal&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">ValorTotal&nbsp;<?php if ($_GET['order_by'] == "ValorTotal") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
										<td class=navpic nowrap bgcolor=#DBEAF5>Estado</td>
										<td class=navpic nowrap bgcolor=#DBEAF5>Factura Electr&oacute;nica</td>
										<?php if ($IDPuntoVenta == 16) { ?>
											<td class=navpic nowrap bgcolor=#DBEAF5>Nro Ped Payu</td>
											<td class=navpic nowrap bgcolor=#DBEAF5>Payu</td>
										<?php } ?>
									</tr>

									<?php while ($r = db_fetch_object($result)) {
										$class = repetition() ? "col1list" : "col2list";
										$i++;
									?>

										<tr>
											<td align=center valign=middle nowrap width=50 class="<?= $class ?>">
												&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id=";
																echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
											</td>
											<td nowrap class="<?= $class ?>"><?php echo get_field("Cliente", "Nombre", "IDCliente", $r->IDCliente) . " " . get_field("Cliente", "Apellido", "IDCliente", $r->IDCliente) ?></td>
											<td nowrap class="<?= $class ?>"><?php echo $r->NumeroFactura ?></td>
											<td nowrap class="<?= $class ?>"><?php echo formatofecha(substr($r->FechaFactura, 0, 10)) . " " . substr($r->FechaFactura, 10) ?></td>
											<td align="right" nowrap class="<?= $class ?>"><?php echo number_format($r->ValorTotal) ?></td>
											<td align="center" nowrap class="<?= $class ?>"><?php echo $r->Estado ?></td>
											<td nowrap class=row1>

												<?php
												$FechaGFactura = date("Y-m-d", strtotime("2021-12-01"));
												$FechaFac = date("Y-m-d", strtotime(substr($r->FechaFactura, 0, 10)));

												if ($FechaFac >= $FechaGFactura) { ?>


													<input type="radio" class="btnelectronica" name="facturaelectronica<?php echo $r->$Key . "-" . $r->IDPuntoVenta; ?>" idfactura='<?php echo $r->$Key ?>' idpuntoventa='<?php echo $r->IDPuntoVenta ?>' value="S" <?php if ($r->FacturaElectronica == "S") echo "checked"; ?>>Si
													<input type="radio" class="btnelectronica" name="facturaelectronica<?php echo $r->$Key . "-" . $r->IDPuntoVenta; ?>" idfactura='<?php echo $r->$Key ?>' idpuntoventa='<?php echo $r->IDPuntoVenta ?>' value="N" <?php if ($r->FacturaElectronica == "N" || $r->FacturaElectronica == "") echo "checked"; ?>>No


													<?php
													if ($r->FacturaElectronica == "N" || $r->FacturaElectronica == "") {
														$sql_log = "SELECT Resultado FROM LogFacturaElectronica WHERE IDFactura = '" . $r->$Key . "' and IDPuntoventa = '" . $r->IDPuntoVenta . "' Order by IDLogFacturaElectronica DESC LIMIT 1";
														$r_log = db_query($sql_log);
														$row_log = db_fetch_array($r_log);
														echo "<br>" . utf8_decode($row_log["Resultado"]);
													?>
														<br><input type="button" id="btnreenviarfac" name="facturaelectronica<?php echo $r->$Key . "-" . $r->IDPuntoVenta; ?>" idfactura='<?php echo $r->$Key ?>' idpuntoventa='<?php echo $r->IDPuntoVenta ?>' class="submit btnreenviarfac" value="Reenviar">
													<?php

													}
													?>
													<div name='msgupdate<?php echo $r->$Key ?>' id='msgupdate<?php echo $r->$Key ?>'></div>
													<?php
													//echo $r->NumeroFideliazcion 
													?>
												<?php } ?>
											</td>

											<?php if ($IDPuntoVenta == 16) { ?>
												<td align=center valign=middle nowrap width=50 class="<?= $class ?>">
													<?php echo $r->NumeroPayu;  ?>
												</td>
												<td align=center valign=middle nowrap width=50 class="<?= $class ?>">
													<?php if (!empty($r->NumeroPayu) && $r->PagoPayu != "S") {  ?>
														<input type="radio" class="btnpayu" name="pagopayu<?php echo $r->$Key . "-" . $r->IDPuntoVenta; ?>" idfactura='<?php echo $r->$Key ?>' idpuntoventa='<?php echo $r->IDPuntoVenta ?>' value="S" <?php if ($r->PagoPayu == "S") echo "checked"; ?>>Si
														<input type="radio" class="btnpayu" name="pagopayu<?php echo $r->$Key . "-" . $r->IDPuntoVenta; ?>" idfactura='<?php echo $r->$Key ?>' idpuntoventa='<?php echo $r->IDPuntoVenta ?>' value="N" <?php if ($r->PagoPayu == "N" || $r->PagoPayu == "") echo "checked"; ?>>No
														<div name='msgupdatepayu<?php echo $r->$Key ?>' id='msgupdatepayu<?php echo $r->$Key ?>'></div>
													<?php } else {
														echo $r->PagoPayu;
													}
													?>
												</td>
											<?php } ?>

										</tr>
									<?php } // END for
									?>
									<tr>
										<td class="navpic" colspan=6 nowrap>
											<?php
											print $pages;
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	<?php
								} // End if$rows
								else
									echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
							}// Enf function list()

							/*******************************************************************************************
		funcion filtrar
							 *******************************************************************************************/
							function filtrar()
							{
								global $dblink, $total_records, $row, $numtoshow, $MOD, $IDPuntoVenta;
	?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="NumeroReferencia">Referencia</option>
					<option value="FechaFactura">Fecha</option>
					<option value="Cliente.Cedula">Cedula</option>
					<option value="NumeroFactura">Numero</option>
				</select>
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post">
				Entre <input type=text readonly size=10 class=input name=limit1>
				<script language='JavaScript1.2'>
					<!--
					if (!document.layers)
						document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//
					-->
				</script>
				y <input type=text size=10 readonly class=input name=limit2>
				<script language='JavaScript1.2'>
					<!--
					if (!document.layers)
						document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//
					-->
				</script>
				<br>
				ordenar por
				<select name="order_by" class="popup">
					<option value="FechaFactura">Fecha</option>
					<option value="Cliente.Cedula">Cedula</option>
					<option value="NumeroFactura">Numero</option>
				</select>
				de forma
				<select name="in_order" class="popup">
					<option value="DESC">Descendente</option>
					<option value="ASC">Ascendente</option>
				</select>
				Listar
				<select name="listar" class="popup">
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="30">30</option>
				</select>
				<br>
				<input type="hidden" name="mod" value="<?= $MOD ?>">
				<input type="hidden" name="rangofield" value="FechaFactura">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="IDPuntoVenta" value="<?= $IDPuntoVenta ?>">
				<input type="hidden" name="tjoin" value="Cliente">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
							} //End function filtrar
?>