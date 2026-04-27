<body> <?php
			if (!class_exists('PdfModern')) {
				require_once(__DIR__ . "/../admin/lib/PdfModern.php");
			}

		$TitleMod = "Cambios";

		$Table = "Cambio";
		$TableJoin = "DetalleCambio";
		$Key = "IDCambio";
		$MOD = "vercambios";
		$m = "VerMovimientos";


		$permisos = get_permiso($ID_Usuario, $m, $Table);
		if ($permisos[0] >= 2) {
			switch (nvl($action)) {
				case "edit":
					print_form($id, "update", "Ver $TitleMod", "Realizar Cambios");
					break;
				case "list":
					if ($field == "Numero") {
						$sql = "SELECT T.* FROM $Table T, $TableJoin DT, CodificacionEspecifica C, PuntoVentaReferencia PR, Referencia R 
							WHERE ( T.IDPuntoVenta = '$IDPuntoVenta'  ) 
							AND T.$Key = DT.$Key 
							AND
							(
							
							( DT.IDCodificacionEspecifica = C.IDCodificacionEspecifica
							AND C.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia 
							AND PR.IDReferencia = R.IDReferencia
							AND R.Numero LIKE '%$QryString%' )
							
							OR
							
							(DT.IDCodificacionEspecificaCambio = C.IDCodificacionEspecifica
							AND C.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia 
							AND PR.IDReferencia = R.IDReferencia
							AND R.Numero LIKE '%$QryString%' )
							
							)
							GROUP BY IDCambio ORDER BY FechaCambio DESC ";
					} else
						$sql = make_qry_string($HTTP_GET_VARS);
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
			global $TitleMod, $Table, $MOD, $Key, $ID_Usuario, $IVA, $IDPuntoVenta, $dirroot;

			$qid = db_query(" SELECT * FROM Cambio WHERE IDCambio = '$id' AND IDPuntoVenta = '$IDPuntoVenta'  ");

			$r = db_fetch_object($qid);


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
													<table class=rowtable width="100%">
														<tr>
															<td class=col1>No. Regisro</td>
															<td class=col2 colspan="3">
																<input type="text" class="tbox" name="NumeroFacturaBono" id="Numero FacturaBono" readonly size="24" value="<?= $r->IDCambio ?>">
															</td>
														</tr>
														<tr>
															<td class=col1>Factura del Cambio</td>
															<td class=col2><input type="text" class="tbox" name="FacturaCambio" id="Numero FacturaBono" readonly size="24" value="<?= $r->IDFacturaCambio ?>"></td>
															<td class=col2><a href="?mod=Factura&action=edit&id=<?= $r->IDFacturaCambio ?>" title="VerFactura"><img src="images/magnifier.png" border="0"></a></td>
															<td class=col2></td>
														</tr>
														<tr>
															<td class=col1>Punto de Venta</td>
															<td class=col2 colspan="3">
																<?php echo get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r->IDPuntoVenta); ?>
																<input type="hidden" value="<?= $IDPuntoVenta ?>" name="IDPuntoVenta">
															</td>
														</tr>
														<tr>
															<td class=col1>Fecha </td>
															<td class=col2 colspan="3">
																<input type="text" class="tbox" name="FechaCambio" size="19" value='<?= $r->FechaCambio ?>' readonly>

																<script language="JavaScript1.2">

																</script>
															</td>
														</tr>
														<tr>
															<td class=col1>Observaciones</td>
															<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?= $r->Observaciones ?></textarea></td>
														</tr>
														<tr>
															<td class=col1>
															</td>
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
															<td class=col1></td>
															<td class=col1></td>
														</tr>
														<tr>
															<td class=col1 nowrap><br>
															</td>
															<td class=col1></td>
															<td class=col1></td>
															<td class=col1></td>
														</tr>
														<tr>
															<td class=row1><b>EMPLEADO</b></td>
															<td class=row1 colspan="3"></td>
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
															<td class=navpic colspan="4">Referencia ENTRA Cambio </td>
														</tr>
														<tr>
															<td colspan="4">
																<table class="texto" width="70%" border="0" cellspacing="1" cellpadding="0" id=table1 align="center">
																	<tr bgcolor="#dfe3e7">
																		<td align="center"><b>Referencia</b></td>
																		<td align="center"><b>Talla</b></td>
																		<td align="center"><b>Nombre</b></td>
																		<td align="center" width="100%"><b>Cantidad</b></td>
																		<td align="center" width="100%"><b>Valor U</b></td>
																		<td align="center" nowrap><b>Ver Factura</b></td>
																	</tr>
																	<?php
																	$query_detalle = db_query("Select * From DetalleProductoCambio Where IDCambio = '$id' AND IDPuntoVenta = '$IDPuntoVenta' ");
																	if (db_num_rows($query_detalle) <= 0):
																		$sql_detalle = "SELECT * FROM DetalleCambio WHERE IDCambio = '$r->IDCambio' AND IDPuntoVenta = '$IDPuntoVenta' LIMIT 1 ";
																		$query_detalle = db_query($sql_detalle);
																	endif;



																	while ($r_detalle = db_fetch_object($query_detalle)) {
																		$sql_referenciacambio = " SELECT R.*, T.Descripcion as Talla FROM CodificacionEspecifica C, PuntoVentaReferencia P, Referencia R, Talla T 
																						WHERE C.IDCodificacionEspecifica = '$r_detalle->IDCodificacionEspecificaCambio' 
																						AND C.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia 
																						AND P.IDReferencia = R.IDReferencia 
																						AND C.IDTalla = T.IDTalla";
																		$qry_referenciacambio = db_query($sql_referenciacambio);

																		$r_referenciacambio = db_fetch_object($qry_referenciacambio);

																	?>
																		<tr>
																			<td align="left" class="col1list">
																				<?php echo $r_referenciacambio->Numero; ?>
																			</td>
																			<td align="left" class="col1list">
																				<?php echo $r_referenciacambio->Talla; ?>
																			</td>
																			<td class="col1list" align="left"><?php echo $r_referenciacambio->Nombre; ?></td>
																			<td class="col1list" align="center" width="100%"><?php echo $r_detalle->Cantidad ?></td>
																			<td class="col1list" align="center" width="100%">
																				<?php
																				if ($r_detalle->ValorU == 0)
																					echo number_format(get_field("Precio", "ValorVenta", "IDPrecio", $r_referenciacambio->IDPrecio), 2);
																				else
																					echo $r_detalle->ValorU

																				?></td>
																			<td class="col1list" align="center"><a href="?mod=Factura&action=edit&id=<?= $r->IDFacturaCambio ?>" title="VerFactura"><img src="images/magnifier.png" border="0"></a></td>
																		</tr>
																	<?php
																	}
																	$sql_detalle = "SELECT * FROM DetalleCambio WHERE IDCambio = '$r->IDCambio' AND IDPuntoVenta = '$IDPuntoVenta'";
																	$query_detalle = db_query($sql_detalle);
																	$r_detalle = db_fetch_object($query_detalle);


																	?>



																</table>
															</td>
														</tr>
														<tr>
															<td class=col1 nowrap></td>
															<td class=col1>Excedente</td>
															<td class=col2 colspan="2"><input type="text" class="tbox" name="Excedente" readonly size="20" value="<?php echo number_format($r->Excedente, 2) ?>"></td>
														</tr>
														<tr>
															<td class=col1 nowrap></td>
															<td class=col1>Factura Excedente</td>
															<td class=col2 colspan="2"><input type="text" class="tbox" name="Factura" readonly size="20" value="<?php echo $r->IDFactura ?>">
																<a href="?mod=Factura&action=edit&id=<?= $r->IDFactura ?>" title="VerFactura"><img src="images/magnifier.png" border="0">
																</a>
															</td>
														</tr>
														<tr>
															<td class=col1 nowrap></td>
															<td class=col2></td>
															<td class=col1></td>
															<td class=col2></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td class=navpic colspan="4"><b>Detalle SALIDA Cambio</b></td>
											</tr>
											<tr bgcolor=#e7ebef>
												<td colspan="4">
													<table class="texto" border="0" cellspacing="1" cellpadding="0" id=table1 width="100%">
														<tr bgcolor="#dfe3e7">
															<td align="center"><b>Referencia</b></td>
															<td align="center"><b>Talla</b></td>
															<td align="center"><b>Nombre</b></td>
															<td align="center"><b>Cantidad</b></td>

															<td align="center"><b>Total</b></td>
														</tr>
														<?php
														do {
															$sql_referencia = " SELECT R.*, T.Descripcion as Talla FROM CodificacionEspecifica C, PuntoVentaReferencia P, Referencia R, Talla T 
																						WHERE C.IDCodificacionEspecifica = '$r_detalle->IDCodificacionEspecifica' 
																						AND C.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia 
																						AND P.IDReferencia = R.IDReferencia 
																						AND C.IDTalla = T.IDTalla";
															$qry_referencia = db_query($sql_referencia);

															$r_referencia = db_fetch_object($qry_referencia);
															$class = repetition() ? "col1list" : "col2list";

														?>
															<tr bgcolor="#dfe3e7">
																<td align="left" class="<?= $class ?>"><?php echo $r_referencia->Numero ?></td>
																<td align="left" class="<?= $class ?>"><?php echo $r_referencia->Talla ?></td>
																<td align="left" class="<?= $class ?>"><?php echo $r_referencia->Nombre ?></td>
																<td align="left" class="<?= $class ?>"><?php echo $r_detalle->Cantidad ?></td>

																<td align="left" class="<?= $class ?>"><?php echo number_format($r_detalle->ValorU * $r_detalle->Cantidad); ?></td>
															</tr>
														<?php
														} while ($r_detalle = db_fetch_object($query_detalle));
														?>
													</table>
												</td>
											</tr>
											<tr>
												<td class=col1></td>
												<td class=col1 width="250"></td>
												<td class=navpic colspan="2">
													<div align="left"><b>RESUMEN REGISTRO</b></div>
												</td>
											</tr>
											<tr>
												<td class=col1></td>
												<td class=col1 width="250"></td>
												<td class=col2>
													<div align="right">
														Total</div>
												</td>
												<td class=col2><input type=text readonly name=ValorTotal value="<?= number_format($r->Excedente) ?>" class=tbox size=15></td>
											</tr>

										</table>
										<input type="hidden" name="action" value="<?= $newmode ?>">
									</div>

								</td>
							</tr>
						</table>
					</td>
				</tr>

			</table>
		</FORM>





			<div id="areaimprimir">
				<?php
				$filedir = $dirroot . "/filesotros/Cambio/";
			$name = "Cambio" . $id . "_" . $IDPuntoVenta . ".html";
			$namePDF = "Cambio" .  $id . "_" . $IDPuntoVenta . ".pdf";
			$file = "$filedir$name";
			$filepdf = "$filedir$namePDF";
				ob_start();
					?>
					<!DOCTYPE html>
					<html>
					<head>
						<meta charset="UTF-8">
						<style>
							@page {
								size: 74mm 220mm;
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

							.section {
								border-top: 1px dotted #000;
								margin-top: 5px;
								padding-top: 4px;
							}

							.kv {
								width: 100%;
								border-collapse: collapse;
								table-layout: fixed;
							}

							.kv td {
								padding: 1px 0;
								vertical-align: top;
								overflow-wrap: break-word;
							}

							.kv .label {
								width: 34%;
								font-weight: bold;
							}

							.kv .value {
								width: 66%;
							}

							.item {
								margin-top: 4px;
								padding-bottom: 4px;
								border-bottom: 1px dotted #000;
							}

							.item-title {
								font-size: 8pt;
								font-weight: bold;
								margin-bottom: 2px;
							}

							.item-line {
								font-size: 8pt;
								line-height: 1.15;
								overflow-wrap: break-word;
							}

							.summary {
								margin-top: 5px;
								font-size: 9pt;
							}
						</style>
					</head>
					<body>
						<div class="center bold">CAMBIO No. <?= $r->IDCambio ?></div>
						<table class="kv">
							<tr>
								<td class="label">Factura cambio</td>
								<td class="value"><?= $r->IDFacturaCambio ?></td>
							</tr>
							<tr>
								<td class="label">Punto venta</td>
								<td class="value"><?php echo get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r->IDPuntoVenta); ?></td>
							</tr>
							<tr>
								<td class="label">Fecha</td>
								<td class="value"><?= $r->FechaCambio ?></td>
							</tr>
							<tr>
								<td class="label">Observaciones</td>
								<td class="value"><?= $r->Observaciones ?></td>
							</tr>
						</table>

						<div class="section bold">CLIENTE</div>
						<table class="kv">
							<tr>
								<td class="label">Cedula</td>
								<td class="value"><?php echo get_field("Cliente", "Cedula", "IDCliente", $r->IDCliente); ?></td>
							</tr>
							<tr>
								<td class="label">Nombre</td>
								<td class="value"><?php echo get_field("Cliente", "CONCAT(Nombre,' ',Apellido)", "IDCliente", $r->IDCliente); ?></td>
							</tr>
							<tr>
								<td class="label">Telefono</td>
								<td class="value"><?php echo get_field("Cliente", "Telefono", "IDCliente", $r->IDCliente); ?></td>
							</tr>
						</table>

						<div class="section bold">EMPLEADO</div>
						<table class="kv">
							<tr>
								<td class="label">Cedula</td>
								<td class="value"><?php echo get_field("Empleado", "Cedula", "IDEmpleado", $r->IDEmpleado); ?></td>
							</tr>
							<tr>
								<td class="label">Nombre</td>
								<td class="value"><?php echo get_field("Empleado", "Nombre", "IDEmpleado", $r->IDEmpleado) . " " . get_field("Empleado", "Apellidos", "IDEmpleado", $r->IDEmpleado); ?></td>
							</tr>
						</table>

						<div class="section bold">ITEMS QUE ENTRAN</div>
						<?php
						$query_detalle_entrada = db_query("Select * From DetalleProductoCambio Where IDCambio = '$id' AND IDPuntoVenta = '$IDPuntoVenta' ");
						if (db_num_rows($query_detalle_entrada) <= 0):
							$query_detalle_entrada = db_query("SELECT * FROM DetalleCambio WHERE IDCambio = '$r->IDCambio' AND IDPuntoVenta = '$IDPuntoVenta' LIMIT 1 ");
						endif;

						while ($r_detalle_entrada = db_fetch_object($query_detalle_entrada)) {
							$sql_referenciacambio = " SELECT R.*, T.Descripcion as Talla FROM CodificacionEspecifica C, PuntoVentaReferencia P, Referencia R, Talla T
														WHERE C.IDCodificacionEspecifica = '$r_detalle_entrada->IDCodificacionEspecificaCambio'
														AND C.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia
														AND P.IDReferencia = R.IDReferencia
														AND C.IDTalla = T.IDTalla";
							$qry_referenciacambio = db_query($sql_referenciacambio);
							$r_referenciacambio = db_fetch_object($qry_referenciacambio);
							$valor_entrada = ($r_detalle_entrada->ValorU == 0) ? get_field("Precio", "ValorVenta", "IDPrecio", $r_referenciacambio->IDPrecio) : $r_detalle_entrada->ValorU;
						?>
							<div class="item">
								<div class="item-title"><?php echo $r_referenciacambio->Numero; ?> - <?php echo $r_referenciacambio->Nombre; ?></div>
								<div class="item-line">Talla: <?php echo $r_referenciacambio->Talla; ?> | Cantidad: <?php echo $r_detalle_entrada->Cantidad; ?></div>
								<div class="item-line">Valor U.: <?php echo number_format($valor_entrada, 2); ?></div>
							</div>
						<?php
						}
						?>

						<div class="section">
							<table class="kv">
								<tr>
									<td class="label">Excedente</td>
									<td class="value right"><?php echo number_format($r->Excedente, 2); ?></td>
								</tr>
								<tr>
									<td class="label">Factura excedente</td>
									<td class="value right"><?php echo $r->IDFactura; ?></td>
								</tr>
							</table>
						</div>

						<div class="section bold">ITEMS QUE SALEN</div>
						<?php
						$query_detalle_salida = db_query("SELECT * FROM DetalleCambio WHERE IDCambio = '$r->IDCambio' AND IDPuntoVenta = '$IDPuntoVenta'");
						while ($r_detalle_salida = db_fetch_object($query_detalle_salida)) {
							$sql_referencia = " SELECT R.*, T.Descripcion as Talla FROM CodificacionEspecifica C, PuntoVentaReferencia P, Referencia R, Talla T
												WHERE C.IDCodificacionEspecifica = '$r_detalle_salida->IDCodificacionEspecifica'
												AND C.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia
												AND P.IDReferencia = R.IDReferencia
												AND C.IDTalla = T.IDTalla";
							$qry_referencia = db_query($sql_referencia);
							$r_referencia = db_fetch_object($qry_referencia);
							$total_salida = $r_detalle_salida->ValorU * $r_detalle_salida->Cantidad;
						?>
							<div class="item">
								<div class="item-title"><?php echo $r_referencia->Numero; ?> - <?php echo $r_referencia->Nombre; ?></div>
								<div class="item-line">Talla: <?php echo $r_referencia->Talla; ?> | Cantidad: <?php echo $r_detalle_salida->Cantidad; ?></div>
								<div class="item-line">Valor U.: <?php echo number_format($r_detalle_salida->ValorU, 2); ?></div>
								<div class="item-line">Total: <?php echo number_format($total_salida, 2); ?></div>
							</div>
						<?php
						}
						?>

						<div class="summary right bold">TOTAL: <?= number_format($r->Excedente, 2) ?></div>
					</body>
					</html>

				<?php
				$page = ob_get_contents();
				$fw = fopen($file, "w");
				fputs($fw, $page, strlen($page));
				fclose($fw);

					ob_end_clean();
					PdfModern::generate($page, $filepdf, [74, 220]);
					$ruta_impresion = file_exists($filepdf) ? "/admin/filesotros/Cambio/" . $namePDF : "/admin/filesotros/Cambio/" . $name;
					?>
			</div>

			<div align="center">
				<a href="<?= $ruta_impresion ?>" target="_blank">Imprimir Cambio</a>
			</div>









		<?php
		} // END function print_form_fotos($id,$numfotos)
		/*******************************************************************************************
		funcion Listar
		 *******************************************************************************************/
		function list_r($sql = "")
		{
			global $TitleMod, $MOD, $Table, $Key, $listar, $IDPuntoVenta;
			if (empty($sql))
				$sql =  "SELECT * FROM $Table WHERE IDPuntoVenta = '$IDPuntoVenta' ORDER BY FechaCambio DESC";


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
									<a href="Factura/exportacambio.php?sql=<?php echo $sql; ?>"><img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0">Exportar Registros </a>
									<table width=100% border=0 cellspacing=1 cellpadding=0>
										<tr>
											<td align=center class=navpic valign=middle bgcolor=#DBEAF5 width=69>Ver</td>
											<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=IDCliente&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Cliente&nbsp;<?php if ($_GET['order_by'] == "IDCliente") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
											<td class=navpic nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroFacturaBono&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Numero</a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroFacturaBono&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'> Registro</a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroFacturaBono&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>&nbsp;<?php if ($_GET['order_by'] == "IDCambio") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
											<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=FechaCambio&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Fecha&nbsp;<?php if ($_GET['order_by'] == "FechaCambio") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
											<td class=navpic nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=ValorTotal&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Valor </a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=ValorTotal&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>&nbsp;<?php if ($_GET['order_by'] == "ValorTotal") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
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
												<td nowrap class="<?= $class ?>"><?php echo $r->IDCambio ?></td>
												<td nowrap class="<?= $class ?>"><?php echo formatofecha(substr($r->FechaCambio, 0, 10)) . " " . substr($r->FechaCambio, 10) ?></td>
												<td nowrap class="<?= $class ?>"><?php echo number_format($r->Excedente) ?></td>
											</tr>
										<?php } // END for
										?>
										<tr>
											<td class="navpic" colspan=5 nowrap>
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
						<option value="FechaInicio">Fecha</option>
						<option value="Cliente.Cedula">Cedula</option>
						<option value="NumeroFacturaBono">Numero</option>
						<option value="Numero">Referencia</option>
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
						<option value="FechaCambio">Fecha</option>
						<option value="Cliente.Cedula">Cedula</option>
						<option value="NumeroFacturaBono">Numero</option>
					</select>
					de forma
					<select name="in_order" class="popup">
						<option value="ASC">Ascendente</option>
						<option value="DESC">Descendente</option>
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
					<input type="hidden" name="rangofield" value="FechaCambio">
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
