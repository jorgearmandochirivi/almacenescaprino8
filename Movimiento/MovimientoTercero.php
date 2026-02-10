<body> <?php

		$TitleMod = "Cambios";

		$Table = "PedidoTercero";
		$TableJoin = "DetallePedidoTercero";
		$Key = "IDPedidoTercero";
		$MOD = "MovimientoTercero";
		$m = "PedidoTercero";


		$permisos = get_permiso($ID_Usuario, $m, $Table);
		if ($permisos[0] >= 2) {
			switch (nvl($action)) {

				case "update":

					$frm = vars_LOG($_POST);
					//db_query("SET AUTOCOMMIT=0");
					//db_query("BEGIN");


					$error_devuelto = 0;
					$error_entrada = 0;

					$array_sql_devueltos = [];
					$array_sql_devueltos_id = [];

					for ($i = 0; $i <= $frm["ItemsDevolucion"]; $i++):
						//echo "<br>" . $frm["IDReferencia".$i] . " Talla " . $frm["IDTalla".$i] . " Devuelto " . $frm["Devuelto".$i] . " " . $frm["Observacion".$i];
						// Valido que vengas los datos minimos para hacer una devolucion
						if (!empty($frm["IDReferencia" . $i]) && !empty($frm["IDReferencia" . $i]) && !empty($frm["Devuelto" . $i])):
							//Valido cual es el registro del detalle del pedido que se va hacer la devolucion
							$sql_detalle_tercero = "Select * From DetallePedidoTerceroReferencia Where IDDetallePedidoTercero = '" . $frm["IDReferencia" . $i] . "' and IDPedidoTercero = '" . $frm["IDPedidoTercero"] . "' and IDTalla = '" . $frm["IDTalla" . $i] . "' and IDPuntoVenta = '" . $frm["IDPuntoVenta"] . "'";
							$result_detalle_pedido = db_query($sql_detalle_tercero);
							if (db_num_rows($result_detalle_pedido) > 0):
								$row_detale_pedido = db_fetch_array($result_detalle_pedido);
								// verifico que la cantidad de devuelto no sea mayor a la cantidad pedida
								if ((int)$frm["Devuelto" . $i] <= (int)$row_detale_pedido["Cantidad"]):
									//echo "<br>Inserto";
									$array_sql_devueltos[] = "Update DetallePedidoTerceroReferencia Set Estado = 'Devuelto', CantidadDevuelto = '" . $frm["Devuelto" . $i] . "', Observacion = '" . $frm["Observacion" . $i] . "', FechaDevuelto = NOW(), NumeroFactura = '" . $frm["NumeroFactura"] . "' Where IDDetallePedidoTerceroReferencia = '" . $row_detale_pedido["IDDetallePedidoTerceroReferencia"] . "'";
									$array_sql_devueltos_id[] = $row_detale_pedido["IDDetallePedidoTerceroReferencia"];
								else:
									echo Mensaje_Info("Error la cantidad devuelta es mayor a la cantidad pedida, por favor verifique ", "col2");
									$error_devuelto = 1;
									$error_entrada = 1;
								endif;
							else:
								echo Mensaje_Info("Uno de los items que selecciono para devolver no existe en este pedido, por favor verifique (" . $frm["Observacion" . $i] . ") ", "col2");
								$error_devuelto = 1;
								$error_entrada = 1;
							endif;
						endif;
					endfor;

					// Si no hubo errores en la validacion ejecuto los querys
					if ($error_devuelto == 0):
						if (count($array_sql_devueltos) > 0):
							foreach ($array_sql_devueltos as $query_devuelto):
								db_query($query_devuelto);
							endforeach;
						endif;

						if (count($array_sql_devueltos_id) > 0):
							// funcion para realizar la actualizacion del inventario	
							salidamercancia_tercero($array_sql_devueltos_id, $frm["IDPedidoTercero"]);
							foreach ($array_sql_devueltos_id as $IDDetalleTercero):
								//echo "<br>notificar_devuelto($IDDetalleTercero)";
								notificar_devuelto($IDDetalleTercero, "");
							endforeach;
						endif;

					else:
						echo Mensaje_Info("ATENCION:  Por favor revise los errores generados y vuelva a intentar", "col2");
					endif;


					$idpuntoref = "";
					$update_tercero = [];
					$sql_entrada = [];
					$sql_actualizacod = [];
					foreach ($frm["Recibido"] as $idpuntoref => $tallas) {
						foreach ($tallas as $idtalla => $detallepedidotercero) {
							foreach ($detallepedidotercero as $IDDetalleTercero => $valor) {
								//Actualizar Existencias
							$array_tallas_rel = [];
								//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
								$nombre_talla = get_field("Talla", "Descripcion", "IDTalla", $idtalla);
								$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '" . $nombre_talla . "'");
								while ($row_talla = db_fetch_array($sql_tallas_rel)):
									$array_tallas_rel[] = $row_talla["IDTalla"];
								endwhile;

								if (count($array_tallas_rel) > 0):
									$id_tallas_rel = implode(",", $array_tallas_rel);
								endif;

								$existencias = 0;

								$sql_existencia = "Select Maximo,Existencias From CodificacionEspecifica Where IDPuntoVentaReferencia='" . $idpuntoref . "' AND IDTalla in ($id_tallas_rel) ";
								$qry_existencia = db_query($sql_existencia);
								$row_existencia = db_fetch_array($qry_existencia);
								if (db_num_rows($qry_existencia) <= 0):
									//db_query( "ROLLBACK" );
									$frmIDReferencia = get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $idpuntoref);
									$frmReferencia = get_field("Referencia", "Numero", "IDReferencia", $frmIDReferencia);
									$frmTalla = get_field("Talla", "Descripcion", "IDTalla", $idtalla);

									echo Mensaje_Info("La Referencia $frmReferencia en la Talla $frmTalla no se encontraron existencias de minimo y maximo, por favor verifique ", "col2");
									$msg  = 1;
									$error_entrada = 1;
								else:
									$existencias = $row_existencia["Existencias"];
									$maximo = $row_existencia["Maximo"];
									$existencias = (int)$existencias + (int)$valor;
								endif;

								//echo "<br>EXIS " . $existencias;
								//echo "EXIS " . $existencias = get_field("CodificacionEspecifica","Existencias", "IDPuntoVentaReferencia='".$idpuntoref."' AND IDTalla in ($id_tallas_rel) " );
								//echo "<br>" . (int)$existencias ."+". (int)$valor;
								//$existencias = (int)$existencias + (int)$valor;							
								//$maximo = get_field("CodificacionEspecifica","Maximo", "IDPuntoVentaReferencia='".$idpuntoref."' AND IDTalla in ($id_tallas_rel)" );


								if (($valor > 0) && ($existencias <= $maximo) && $existencias > 0) {
									//Valido cual es el registro del detalle del pedido que se va hacer la entrada
									$sql_detalle_tercero = "Select * From DetallePedidoTerceroReferencia Where IDDetallePedidoTerceroReferencia = '" . $IDDetalleTercero . "'";
									$result_detalle_pedido = db_query($sql_detalle_tercero);
									if (db_num_rows($result_detalle_pedido) > 0):
										$row_detale_pedido = db_fetch_array($result_detalle_pedido);
										// verifico que la cantidad de entrada no sea mayor a la cantidad pedida
										if ((int)$valor > (int)$row_detale_pedido["Cantidad"]):
											//db_query( "ROLLBACK" );
											$frmIDReferencia = get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $idpuntoref);
											$frmReferencia = get_field("Referencia", "Numero", "IDReferencia", $frmIDReferencia);
											$frmTalla = get_field("Talla", "Descripcion", "IDTalla", $idtalla);
											echo Mensaje_Info("La Referencia $frmReferencia en la Talla $frmTalla la cantidad ingresada es mayor a la cantidad pedida, por favor verifique ", "col2");
											$msg  = 1;
											$error_entrada = 1;
										endif;
									endif;



									// Actualizo el registro del pedido de tercero con la entrada
									$update_tercero[] = "Update DetallePedidoTerceroReferencia Set Estado = 'Recibido', CantidadRecibido = CantidadRecibido + '" . $valor . "', Remision = '" . $frm["Remision"] . "', NumeroFactura = '" . $frm["NumeroFactura"] . "', FechaRecibido = NOW() Where IDDetallePedidoTerceroReferencia = '" . $IDDetalleTercero . "'";
									//db_query($update_tercero);
									//insertar entrada
									//$identrada = get_maxID("Entrada","IDEntrada");
									$consulta_sql_entrada = "INSERT INTO Entrada VALUES('consecutivoentrada','{$frm['Remision']}','{$frm['NumeroFactura']}','{$frm['Fecha']}','$idpuntoref','$idtalla','$valor',NOW(),'$IDPuntoVenta')";
									if (in_array($consulta_sql_entrada, $sql_entrada)) {
										echo Mensaje_Info("Se repite la referencia y talla mas de una vez, por favor verifique " . $consulta_sql_entrada, "col2");
										$msg  = 1;
										$error_entrada = 1;
									} else {
										$sql_entrada[] = $consulta_sql_entrada;
									}

									//db_query($sql_entrada);
									$sql_actualizacod[] = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDPuntoVentaReferencia = '$idpuntoref' AND IDTalla in ($id_tallas_rel)";
									//db_query( $sql_actualizacod );


								} //end if( $valor > 0 )
								elseif ($existencias > $maximo) {
									//No hacemos nada hasta que arreglen los maximos
									//db_query( "ROLLBACK" );
									$frmIDReferencia = get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $idpuntoref);
									$frmReferencia = get_field("Referencia", "Numero", "IDReferencia", $frmIDReferencia);
									$frmTalla = get_field("Talla", "Descripcion", "IDTalla", $idtalla);
									echo Mensaje_Info("La Referencia $frmReferencia en la Talla $frmTalla Supera el Maximo y no se realizara esta entrada para esta referencia ", "col2");
									$error_entrada = 1;
									$msg  = 1;
								} //end elseif

							}
						}
					} //end foreach($ingreso as $key => $valor)


					/*
				echo "Fuera del aire intente mas tarde por favor.<br><br>";
				echo "ERROR: <br>" . $error_entrada;
				print_r($sql_update_tercero);
				print_r($sql_entrada);
				print_r($sql_actualizacod);
				exit;
				*/

					// Si no hay errores realizo los ingresos
					if ($error_entrada == 0):
						if (count($update_tercero) > 0):
							foreach ($update_tercero as $sql_update_tercero):
								//echo "<br>" . $sql_update_tercero;
								db_query($sql_update_tercero);
							endforeach;
						endif;
						if (count($sql_entrada) > 0):
							foreach ($sql_entrada as $sql_entrada):
								$identrada = get_maxID("Entrada", "IDEntrada");
								$sql_entrada = str_replace("consecutivoentrada", $identrada, $sql_entrada);
								//echo "<br>" . $sql_entrada;
								db_query($sql_entrada);
							endforeach;
						endif;

						if (count($sql_actualizacod) > 0):
							foreach ($sql_actualizacod as $sql_actualizacod):
								//echo "<br>" . $sql_actualizacod;
								db_query($sql_actualizacod);
							endforeach;
						endif;
					else:
						echo Mensaje_Info("No se realizo ninguna entrada ", "col2");
						exit;
					endif;

					//exit;


					//db_query("COMMIT");

					if ($msg <> 1)
						echo "<script>location.href='?mod=verentrada';</script>";



					break;
				case "edit":
					print_form($id, "update", "Ver $TitleMod", "Realizar Cambios");
					break;
				case "list":



					if (!empty($_GET["NumeroFactura"])):
						$condiciones .= " and DPTR.Numerofactura LIKE '%" . $_GET["NumeroFactura"] . "%'";
					endif;

					if (!empty($_GET["NumeroOrdenCompra"]))
						$condiciones .= " and PT.NumeroOrdenCompra LIKE '%" . $_GET["NumeroOrdenCompra"] . "%'";

					if (!empty($_GET["IDProveedor"]))
						$condiciones .= " and PT.IDProveedor = '" . $_GET["IDProveedor"] . "'";

					if (!empty($_GET["IDEstadoPedidoTercero"]))
						$condiciones .= " and PT.IDEstadoPedidoTercero = '" . $_GET["IDEstadoPedidoTercero"] . "'";

					if (!empty($_GET["ReferenciaCaprino"]))
						$condiciones .= " and DPT.ReferenciaCaprino  like '%" . $_GET["ReferenciaCaprino"] . "%'";

					if (!empty($_GET["FechaDesde"])  && !empty($_GET["FechaHasta"]))
						$condiciones .= " and PT.FechaPedido >= '" . $_GET["FechaDesde"] . "' and PT.FechaPedido <= '" . $_GET["FechaHasta"] . "'";

					if (!empty($_GET["Tipologia"]))
						$condiciones .= " and DPT.Producto  like '%" . $_GET["Tipologia"] . "%'";


if (!empty($_GET["limit1"]) && !empty($_GET["limit2"]))
							$condiciones .= " and G.FechaTrCr between '" . $_GET["limit1"] . "' and '" . $_GET["limit2"] . "'";

					$sql = "Select PT.*
					  From PedidoTercero PT, DetallePedidoTercero DPT, DetallePedidoTerceroReferencia DPTR
					  Where PT.IDPedidoTercero = DPT.IDPedidoTercero AND
						DPTR.IDPuntoVenta = '" . $IDPuntoVenta . "' AND
						DPT.IDDetallePedidoTercero = DPTR.IDDetallePedidoTercero
						$condiciones
						Group by PT.IDPedidoTercero
						Order by IDPedidoTercero Desc";


					//$sql = make_qry_string($HTTP_GET_VARS);
					list_r($sql);





					$sql_ant = "Select * 
							From PedidoTercero PT, DetallePedidoTercero DPT, DetallePedidoTerceroReferencia DPTR
							Where PT.IDPedidoTercero = DPT.IDPedidoTercero AND
							AND DPT.IDDetallePedidoTercero =  DPTR.IDDetallePedidoTercero
							AND DPTR.IDPuntoVenta = '" . $IDPuntoVenta . "'";


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
			global $TitleMod, $Table, $MOD, $Key, $ID_Usuario, $IVA, $IDPuntoVenta;

			$qid = db_query(" SELECT * FROM PedidoTercero WHERE IDPedidoTercero = '$id'");

			$r = db_fetch_object($qid);

			$sql_detalle = "SELECT * FROM DetalleCambio WHERE IDCambio = '$r->IDCambio' AND IDPuntoVenta = '$IDPuntoVenta' LIMIT 1 ";
			$query_detalle = db_query($sql_detalle);
			$r_detalle = db_fetch_object($query_detalle);


			$sql_tallas = "Select * From Talla Where 1 Group By Descripcion Order By Nombre";
			$result_talla = db_query($sql_tallas);
			while ($row_talla = db_fetch_array($result_talla)) {
				$array_talla[$row_talla["IDTalla"]] = $row_talla;
			}

			$sql_punto_venta = "Select IDPuntoVenta,Nombre,IDCiudad From PuntoVenta Where IDPuntoVenta = '" . $IDPuntoVenta . "'  Order By IDCiudad, Nombre";
			$result_punto_venta = db_query($sql_punto_venta);
			while ($row_punto_venta = db_fetch_array($result_punto_venta)) {
				$array_punto_venta[$row_punto_venta["IDPuntoVenta"]] = $row_punto_venta;
			}



		?>

		<script>
			var Check = new Array('Remision', 'Fecha', 'NumeroFactura');
		</script>
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
		<FORM name="frm" id="frmEntradaTercero" method="post" enctype="multipart/form-data" action="<?= $PHP_SELF ?>" <?php if ($newmode != "delete") { ?>onsubmit="return EvaluaReg2(this,Check)" <?php } ?>>
			<table class="forumline" width="800" cellspacing="1" border="0" align="center">
				<tr>
					<td>
						<table width=100% border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff">

							<tr>
								<td colspan="2">

									<div align="center">
										<table width=100% border=0>
											<tr>
												<td width="250" colspan="4">
													<table class=rowtable width="100%">
														<tr>
															<td class=col1>Entrada Numero</td>
															<td class=col2>
																<?php
																$Remision = get_maxID("Entrada WHERE IDPuntoVenta = '$IDPuntoVenta'", "Remision");
																?>
																<input type="input" name="Remision" class="tbox" id="Remision" title="Remision" value="<?= $Remision ?>" readonly>
															</td>
															<td class=col2><span class="col1">Numero Factura </span></td>
															<td class=col2><input type="input" name="NumeroFactura" id="NumeroFactura" title="Numero Factura" class="tbox" required>
																<?php
																$qry_fac_pedido =  "Select  NumeroFactura, FechaRecibido From DetallePedidoTerceroReferencia Where IDPedidoTercero = '" . $id . "' and IDPuntoVenta = '" . $IDPuntoVenta . "' Group by NumeroFactura";
																$sql_fac_pedido = db_query($qry_fac_pedido);
																if (db_num_rows($sql_fac_pedido) > 0): ?>
																	<br>Facturas Recibidas:<br> <?php
																								$array_facturas = [];
																								while ($row_fac_pedido = db_fetch_array($sql_fac_pedido)):
																									if (!empty($row_fac_pedido["NumeroFactura"])) {
																										//Averiguo el total de ingresados en la factura
																										$sql_cantidad_fac = "Select sum(CantidadRecibido) as TotalRecibido, FechaRecibido From DetallePedidoTerceroReferencia Where NumeroFactura = '" . $row_fac_pedido["NumeroFactura"] . "' and IDPedidoTercero = '" . $id . "' and IDPuntoVenta = '" . $IDPuntoVenta . "'";
																										$result_cantidad_fac = db_query($sql_cantidad_fac);
																										$row_cantidad_fac = db_fetch_array($result_cantidad_fac);
																										$fecha_recibido = substr($row_cantidad_fac["FechaRecibido"], 0, 10);

																										$sql_cantidad_fac = "Select sum(Cantidad) as TotalRecibido, FechaRemision, Remision From Entrada Where NumeroFactura = '" . substr($row_fac_pedido["NumeroFactura"], 0, 10) . "' and IDPuntoVenta = '" . $IDPuntoVenta . "' and FechaRemision >= '" . $fecha_recibido . "'";
																										$result_cantidad_fac = db_query($sql_cantidad_fac);
																										$row_cantidad_fac = db_fetch_array($result_cantidad_fac);
																										$fecha_recibido = substr($row_cantidad_fac["FechaRecibido"], 0, 10);

																										$array_facturas[] = $row_fac_pedido["NumeroFactura"] . " - " . $row_fac_pedido["FechaRecibido"] . " Total recibido: " . $row_cantidad_fac["TotalRecibido"] . " Remision: " . $row_cantidad_fac["Remision"];
																									}
																								endwhile;
																								echo implode("<br>", $array_facturas);
																							endif;

																								?>


															</td>
														</tr>
														<tr>
															<td class=col1>Fecha </td>
															<td class=col2 colspan="3">
																<input type="text" class="tbox" name="Fecha" size="19" title="Fecha" value="<?php echo date('Y-m-d') ?>" readonly>

															</td>
														</tr>
														<tr>
															<td colspan="4" class=col1>
																<table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>
																	<tr class=row2>
																		<td width="23%"><span style="color:#FF7477; font-size:12px; font-weight:bold">ESTADO</span></td>
																		<td width="77%"><span style="color:#FF7477; font-size:12px; font-weight:bold">
																				<?php
																				echo estado_tercero_pto_vta($id, $IDPuntoVenta);
																				//echo get_field("EstadoPedidoTercero","Descripcion","IDEstadoPedidoTercero",$r->IDEstadoPedidoTercero); 
																				?></span>
																		</td>
																	</tr>
																	<tr class=row2>
																		<td colspan="2">
																			<table width="90%" border="0" style="border:1px solid #E8E2E2" align="center">
																				<tbody>
																					<tr>
																						<td colspan="4" align="center" style="font-weight:bold">Datos Proveedor
																							<?php
																							if (!empty($r->IDProveedor)) {
																								$sql_datos_proveedor = db_query("Select * From Proveedor Where IDProveedor = '" . $r->IDProveedor . "'");
																								$datos_proveedor = db_fetch_array($sql_datos_proveedor);
																								$datos_proveedor["Ciudad"] = get_field("Ciudad", "Descripcion", "IDCiudad", $datos_proveedor["IDCiudad"]);
																							}
																							?>
																						</td>
																					</tr>
																					<tr>
																						<td width="16%"><strong>Nombre</strong></td>
																						<td width="31%"><span id="NombreProveedor"><?php echo $datos_proveedor["Nombre"] ?></span></td>
																						<td width="19%"><strong>Direccion</strong></td>
																						<td width="34%"><span id="DireccionProveedor"><?php echo $datos_proveedor["Direccion"] ?></span></td>
																					</tr>
																					<tr>
																						<td><strong>Telefono</strong></td>
																						<td><span id="TelefonoProveedor"><?php echo $datos_proveedor["Telefono"] ?></span></td>
																						<td><strong>Ciudad</strong></td>
																						<td><span id="CiudadProveedor"><?php echo $datos_proveedor["Ciudad"] ?></span></td>
																					</tr>
																					<tr>
																						<td><strong>Email</strong></td>
																						<td colspan="3"><span id="EmailProveedor"><?php echo $datos_proveedor["Email"] ?></span></td>
																					</tr>
																				</tbody>
																			</table>
																		</td>
																	</tr>
																	<tr class=row2>
																		<td abbr="" colspan="2">
																			<b>Orden de Compra: </b>
																			<span style="color:#FF7477; font-size:12px; font-weight:bold">

																				<?php echo $r->NumeroOrdenCompra ?></span>
																			<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha M&aacute;xima Entrega:</b>
																			<span style="color:#FF7477; font-size:12px; font-weight:bold"><?php echo $r->FechaEntrega ?></span>
																			<br><br>El Punto De Venta, Por Ning&uacute;n Motivo Recibir&aacute; Despu&eacute;s De La Fecha Acordada y El Pedido Autom&aacute;ticamente Se Anula En El Sistema y La Mercanc&iacute;a Se Devolver&aacute;

																		</td>

																	</tr>
																	<tr>
																		<td colspan="2">

																			<table id="tabla_detalle_pedido" cellspacing="1" cellpadding="0" width="100%" border="0" align="center" style="border:1px solid #E5E5E5">
																				<tbody>
																					<tr bgcolor="#9399E4" class="maintitle" align="center">
																						<td style="color:#FFFFFF; !important;">Ref <br>
																							Provee</td>
																						<td style="color:#FFFFFF; !important;">Ref <br>
																							Caprino</td>
																						<td style="color:#FFFFFF; !important;">COL</td>
																						<td style="color:#FFFFFF; !important;">CUERO Y COLOR</td>
																						<td style="color:#FFFFFF; !important;">SUELA</td>
																						<td style="color:#FFFFFF; !important;">TACON</td>
																						<td style="color:#FFFFFF; !important;">ALTURA</td>
																						<td style="color:#FFFFFF; !important;">HORMA</td>
																						<td style="color:#FFFFFF; !important;">OBSERVACIONES</td>
																					</tr>
																					<?php

																					if (!empty($id)) {
																						$item_detalle = 1;
																						$q_detalle = db_query(" SELECT * FROM DetallePedidoTercero WHERE IDPedidoTercero = '$id'");
																						while ($r_detalle = db_fetch_array($q_detalle, $a)) {
																							$array_detalle_orden[$item_detalle] = $r_detalle;
																							$item_detalle++;
																						}
																					}

																					$detalle_inicial = (int)count($array_detalle_orden);

																					for ($i = 1; $i <= $detalle_inicial; $i++):  ?>
																						<tr>
																							<td align="center"><?php echo $array_detalle_orden[$i]["ReferenciaProveedor"]; ?></td>
																							<td align="center"><?php echo $array_detalle_orden[$i]["ReferenciaCaprino"];  ?></td>
																							<td align="center"><?php echo $array_detalle_orden[$i]["CodigoColor"];  ?></td>
																							<td align="center"><?php echo $array_detalle_orden[$i]["CueroColor"];  ?></td>
																							<td align="center"><?php echo $array_detalle_orden[$i]["Suela"];  ?></td>
																							<td align="center"><?php echo $array_detalle_orden[$i]["Tacon"];  ?></td>
																							<td align="center"><?php echo $array_detalle_orden[$i]["Altura"];  ?></td>
																							<td align="center"><?php echo $array_detalle_orden[$i]["Horma"];  ?></td>
																							<td align="center"><?php echo $array_detalle_orden[$i]["Observacion"];  ?></td>
																						</tr>
																					<?php endfor; ?>
																				</tbody>
																			</table>



																		</td>
																	</tr>
																	<tr class=row2>
																		<td colspan="2"></td>
																	</tr>
																	<?php
																	if ($r->Publicar == 'S') {
																	?>
																	<?php
																	}
																	?>
																</table>
															</td>
														</tr>
														<tr>
															<td class=col1>
															</td>
															<td class=col1></td>
															<td class=col1></td>
															<td class=col1></td>
														</tr>
														<tr>
															<td class=row1 colspan="4">





																<?php
																if (count($array_punto_venta) > 0):
																	$id_ciudad_ant = "";
																	foreach ($array_punto_venta as $id_punto_venta => $datos_punto_venta):
																?>
																		<table width="100%" border="0" cellspacing="1" cellpadding="0">
																			<tbody>
																				<tr>
																					<td class="titlemedium">Talla:</td>
																					<?php
																					if (count($array_talla) > 0):
																						$suma_item_pedir_talla = [];
																						$total_tienda = "0";
																						foreach ($array_talla as $id_talla => $datos_talla):
																					?>
																							<td class="titlemedium" nowrap align="center"><?php echo $datos_talla["Nombre"]; ?></td>

																					<?php endforeach;
																					endif;
																					?>
																					<td class="titlemedium" nowrap align="center">TOTAL</td>
																				</tr>


																				<?php for ($i = 1; $i <= $detalle_inicial; $i++):
																					$array_datos_curva = [];
																					$minimo_item = [];
																					$maximo_item = [];
																					$existencias_item = [];
																					$suma_item_pedir = 0;

																					if (!empty($array_detalle_orden[$i]["IDCurvaTercero"])) {
																						//Consulto el detalle de minimos y maximos
																						$sql_datos_curva = "Select* From DetalleCurvaTercero Where IDCurvaTercero = '" . $array_detalle_orden[$i]["IDCurvaTercero"] . "'";
																						$result_datos_curva = db_query($sql_datos_curva);
																						while ($row_datos_curva = db_fetch_array($result_datos_curva)) {
																							$array_datos_curva[$row_datos_curva["IDPuntoVenta"]][$row_datos_curva["IDTalla"]][$row_datos_curva["Tipo"]]  = $row_datos_curva["Valor"];
																						}
																					}


																				?>

																					<tr>
																						<td class="rowform">
																							<?php echo $array_detalle_orden[$i]["ReferenciaCaprino"] . $array_detalle_orden[$i]["CodigoColor"];  ?>
																						</td>
																						<?php
																						if (count($array_talla) > 0):
																							foreach ($array_talla as $id_talla => $datos_talla):
																								// Verifico si ya existe algo guardado para no reemplazar
																								$sql_detalle_pedido_ref = "Select Cantidad 
															  From DetallePedidoTerceroReferencia
															  Where IDPedidoTercero= '" . $r->IDPedidoTercero . "' and 
															  IDDetallePedidoTercero = '" . $array_detalle_orden[$i]["IDDetallePedidoTercero"] . "' and
																							  IDPuntoVenta = '" . $datos_punto_venta["IDPuntoVenta"] . "' and 
																							  IDTalla = '" . $datos_talla["IDTalla"] . "'";
																													$result_detalle_pedido_ref = db_query($sql_detalle_pedido_ref);
																													$row_detalle_pedido_ref = db_fetch_array($result_detalle_pedido_ref);

																													if (is_numeric($row_detalle_pedido_ref["Cantidad"]))
																														$valor_pedir_item = (int)$row_detalle_pedido_ref["Cantidad"];
																													else {
																														$valor_pedir_item = (int)$maximo_item[$id_talla] - (int)$existencias_item[$id_talla];
																														if ($valor_pedir_item < 0 && is_numeric($valor_pedir_item))
																															$valor_pedir_item = "";
																													}

																													$suma_item_pedir += $valor_pedir_item;
																													$suma_item_pedir_talla[$datos_talla["IDTalla"]] +=  $valor_pedir_item;

																													$super_total_talla[$datos_talla["IDTalla"]][$array_detalle_orden[$i]["IDDetallePedidoTercero"]] += $valor_pedir_item;

																									
																									if (is_numeric($valor_pedir_item) && $valor_pedir_item > 0):
																										echo (int)$valor_pedir_item;
																									endif;
																									?>



																								</td>

																						<?php endforeach;
																						endif;
																						?>
																						<td bgcolor="#F1CFCF" align=center style="font-weight:bold">
																							<?php
																							echo number_format($suma_item_pedir, 0, ",", ".");
																							?>
																						</td>
																					</tr>
																					<tr>
																						<td style="height:5px" bgcolor="#FFFFFF">

																						</td>
																						<?php
																						if (count($array_talla) > 0):
																							foreach ($array_talla as $id_talla => $datos_talla):
																						?>
																								<td bgcolor="#FFFFFF"></td>

																						<?php endforeach;
																						endif;
																						?>
																						<td bgcolor="#FFFFFF"></td>
																					</tr>
																				<?php endfor; ?>

																				<tr>
																					<td bgcolor="#F1CFCF" style="font-weight:bold">TOTALES</td>
																					<?php
																					if (count($array_talla) > 0):
																						foreach ($array_talla as $id_talla => $datos_talla):
																					?>
																							<td bgcolor="#F1CFCF" align="center" style=" font-weight:bold">
																								<?php
																								$total_tienda += $suma_item_pedir_talla[$id_talla];
																								if ($suma_item_pedir_talla[$id_talla] != "0") {
																									echo number_format($suma_item_pedir_talla[$id_talla], 0, ",", ".");
																								}

																								?>


																							</td>

																					<?php endforeach;
																					endif;
																					?>
																					<td bgcolor="#F1CFCF" align="center" style="font-weight:bold">
																						<?php
																									$total_ciudad[$datos_punto_venta["IDCiudad"]] += $total_tienda;

																					?>
																			</tbody>
																		</table>
																		<br />
																<?php
																	endforeach;
																endif;
																?>





															</td>
														</tr>
														<tr>
															<td class=row1 colspan="4">Realizar entrada</td>
														</tr>
														<tr>
															<td class=row1 colspan="4">
																<table width="100%" border="0" cellspacing="1" cellpadding="0">
																	<tbody>
																		<tr>
																			<td class="titlemedium">Talla:</td>
																			<?php
																			if (count($array_talla) > 0):
																				$suma_item_pedir_talla = [];
																				$total_tienda = "0";
																				foreach ($array_talla as $id_talla => $datos_talla):
																			?>
																					<td class="titlemedium" nowrap align="center"><?php echo $datos_talla["Nombre"]; ?></td>
																			<?php endforeach;
																			endif;
																			?>
																			<td class="titlemedium" nowrap align="center">TOTAL</td>
																		</tr>
																		<?php for ($i = 1; $i <= $detalle_inicial; $i++):
																			$array_datos_curva = [];
																			$minimo_item = [];
																			$maximo_item = [];
																			$existencias_item = [];
																			$suma_item_pedir = 0;

																			if (!empty($array_detalle_orden[$i]["IDCurvaTercero"])) {
																				//Consulto el detalle de minimos y maximos
																				$sql_datos_curva = "Select* From DetalleCurvaTercero Where IDCurvaTercero = '" . $array_detalle_orden[$i]["IDCurvaTercero"] . "'";
																				$result_datos_curva = db_query($sql_datos_curva);
																				while ($row_datos_curva = db_fetch_array($result_datos_curva)) {
																					$array_datos_curva[$row_datos_curva["IDPuntoVenta"]][$row_datos_curva["IDTalla"]][$row_datos_curva["Tipo"]]  = $row_datos_curva["Valor"];
																				}
																			}


																		?>
																			<tr>
																				<td class="rowform"><?php echo $referencia = $array_detalle_orden[$i]["ReferenciaCaprino"] . $array_detalle_orden[$i]["CodigoColor"];  ?></td>
																				<?php
																				if (count($array_talla) > 0):
																					foreach ($array_talla as $id_talla => $datos_talla):


																						// Verifico si ya existe algo guardado para no reemplazar
																						$sql_detalle_pedido_ref = "Select Cantidad, IDDetallePedidoTerceroReferencia, CantidadRecibido 
															  From DetallePedidoTerceroReferencia
															  Where IDPedidoTercero= '" . $r->IDPedidoTercero . "' and 
															  IDDetallePedidoTercero = '" . $array_detalle_orden[$i]["IDDetallePedidoTercero"] . "' and
															  IDPuntoVenta = '" . $datos_punto_venta["IDPuntoVenta"] . "' and 
															  IDTalla = '" . $datos_talla["IDTalla"] . "'";
																						$result_detalle_pedido_ref = db_query($sql_detalle_pedido_ref);
																						$row_detalle_pedido_ref = db_fetch_array($result_detalle_pedido_ref);

																						if (is_numeric($row_detalle_pedido_ref["Cantidad"]))
																							$valor_pedir_item = (int)$row_detalle_pedido_ref["Cantidad"];
																						else {
																							$valor_pedir_item = (int)$maximo_item[$id_talla] - (int)$existencias_item[$id_talla];
																							if ($valor_pedir_item < 0 && is_numeric($valor_pedir_item))
																								$valor_pedir_item = "";
																						}

																						//$suma_item_pedir+=$valor_pedir_item;
																						//$suma_item_pedir_talla[$datos_talla["IDTalla"]] +=  $valor_pedir_item;

																						$super_total_talla[$datos_talla["IDTalla"]][$array_detalle_orden[$i]["IDDetallePedidoTercero"]] += $valor_pedir_item;
																				?>
																						<td class=row1 align=center><?php
																													if (is_numeric($valor_pedir_item) && $valor_pedir_item > 0):
																														$contador_items++;


																														$id_referencia = "";
																														$IDPuntoVentaReferencia = "";


																														//verifico que exista la referencia
																														$id_referencia =  get_field("Referencia", "IDReferencia", "Nombre", $referencia);

																														if (empty($id_referencia))
																															$id_referencia = get_field("Referencia", "IDReferencia", "NombreAnterior", $referencia);

																														if (!empty($id_referencia)):




																															$sql =  db_query("SELECT * FROM  Referencia R, PuntoVentaReferencia PR 
																	WHERE  PR.IDPuntoVenta = '$IDPuntoVenta' 
																	AND PR.IDReferencia = R.IDReferencia 
																	AND PR.IDReferencia = '" . $id_referencia . "'
																	ORDER BY R.Numero ASC");

																															$row_punto_ref = db_fetch_array($sql);
																															$IDPuntoVentaReferencia = $row_punto_ref["IDPuntoVentaReferencia"];
																														endif;

																														if (empty($IDPuntoVentaReferencia)):
																															$ref_no_existe = 1;
																															echo "<font color='#FF5558'>Referencia No existe en almacen<br>.</font>";
																														endif;
																														//echo $referencia; 


																													?>

																								<?php
																														if (!empty($IDPuntoVentaReferencia) && ($row_detalle_pedido_ref["Cantidad"] != $row_detalle_pedido_ref["CantidadRecibido"]  &&  $row_detalle_pedido_ref["Cantidad"] >= $row_detalle_pedido_ref["CantidadRecibido"])): ?>
																									<?php if ((int)$row_detalle_pedido_ref["CantidadRecibido"] > 0):
																																echo "(" . $row_detalle_pedido_ref["CantidadRecibido"] . ")";
																															endif;
																									?>
																									<input type="hidden" name="maximo<?php echo $contador_items; ?>" id="maximo<?php echo $contador_items; ?>" value="<?php echo $valor_pedir_item; ?>">
																									<input type="text" name="Recibido[<?php echo $IDPuntoVentaReferencia ?>][<?php echo $datos_talla["IDTalla"]; ?>][<?php echo $row_detalle_pedido_ref["IDDetallePedidoTerceroReferencia"]; ?>]" id="Recibido<?php echo $contador_items; ?>" value="" size="2">
																								<?php else:
																															echo $row_detalle_pedido_ref["CantidadRecibido"];
																														endif;
																														$suma_item_pedir_talla[$id_talla] += (int)$row_detalle_pedido_ref["CantidadRecibido"];
																														$suma_item_pedir += (int)$row_detalle_pedido_ref["CantidadRecibido"];
																								?>

																							<?php
																													//echo (int)$valor_pedir_item;
																													endif;
																							?>
																						</td>
																				<?php endforeach;
																				endif;
																				?>
																				<td bgcolor="#F1CFCF" align=center style="font-weight:bold"><?php
																																			echo number_format($suma_item_pedir, 0, ",", ".");
																																			?></td>
																			</tr>
																			<tr>
																				<td style="height:5px" bgcolor="#FFFFFF"></td>
																				<?php
																				if (count($array_talla) > 0):
																					foreach ($array_talla as $id_talla => $datos_talla):
																				?>
																						<td bgcolor="#FFFFFF"></td>
																				<?php endforeach;
																				endif;
																				?>
																				<td bgcolor="#FFFFFF"></td>
																			</tr>
																		<?php endfor; ?>
																		<tr>
																			<td bgcolor="#F1CFCF" style="font-weight:bold">TOTALES</td>
																			<?php
																			if (count($array_talla) > 0):
																				foreach ($array_talla as $id_talla => $datos_talla):
																			?>
																					<td bgcolor="#F1CFCF" align="center" style=" font-weight:bold">
																						<?php
																						$total_tienda += $suma_item_pedir_talla[$id_talla];
																						if ($suma_item_pedir_talla[$id_talla] != "0" && $suma_item_pedir_talla[$id_talla] != 0) {
																							echo number_format($suma_item_pedir_talla[$id_talla], 0, ",", ".");
																						}

																						?></td>
																			<?php endforeach;
																			endif;
																			?>
																			<td bgcolor="#F1CFCF" align="center" style="font-weight:bold"><?php
																																			$total_ciudad[$datos_punto_venta["IDCiudad"]] += $total_tienda;
																																			echo number_format($total_tienda, 0, ",", "."); ?></td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
														<tr>
															<td class=col1><br></td>
															<td class=col1></td>
															<td class=col1></td>
															<td class=col1></td>
														</tr>
														<tr>
															<td colspan="4" align="center">

																<input type="hidden" name="action" value="<?= $newmode ?>">
																<input type="hidden" name="IDPuntoVenta" value="<?= $IDPuntoVenta ?>">
																<input type="hidden" name="IDPedidoTercero" value="<?= $id ?>">
																<input type="hidden" name="ITEMS" value="<?= $contador_items ?>">
																<input type="hidden" name="ItemsDevolucion" value="<?= $contador_filas ?>">
																<?php


																if ($newmode == "entrada") {
																	$caption = "Realizar Entrada";
																} else {
																	$caption = "Confirmar Entrada";
																}



																?>

																<?php
																if ($r->IDEstadoPedidoTercero == 4 || $r->IDEstadoPedidoTercero == 2) {
																	$FechaMaximaRecibir = date("Y-m-d", strtotime($r->FechaEntrega . "+7 days"));
																} else {
																	$FechaMaximaRecibir = $r->FechaEntrega;
																}


																if ($ref_no_existe != 1 && date("Y-m-d") <= $FechaMaximaRecibir): ?>
																	<input type="submit" class="button" name="enviar" value="<?= $caption ?>" id="btnConfirmarEntrada">
																<?php else: ?>
																	<br>
																	<font style="color:#EE080C;font-size:18px;"> Se super&oacute; la fecha m&aacute;xima para entrega!</font>
																	<br>
																<?php endif; ?>


															</td>
														</tr>
														<tr>
															<td colspan="4" align="center">
																<?php if ($ref_no_existe != 1): ?>
																	<a href="#" id="mostrar_devolucion"><br><br>Realizar Devoluci&oacute;n</a>
																	<font style="color:#EE080C"> Atenci&oacute;n: Primero debe hacer la entrada</font>
																<?php endif; ?>



																<table>
																	<tbody>
																		<tr>
																			<td class="row2">Foto 1</td>
																			<td class="row2">
																				<?php if (!empty($r->Foto1)): ?>
																					<img src="<?php echo "admin/imagenes/" . $r->Foto1; ?>" width="150" height="150">
																				<?php endif; ?>
																			</td>
																			<td>Foto 2</td>
																			<td><?php if (!empty($r->Foto2)): ?>
																					<img src="<?php echo "admin/imagenes/" . $r->Foto2; ?>" width="150" height="150">

																				<?php endif; ?>
																			</td>
																		</tr>
																		<tr>
																			<td class="row2">Foto 3</td>
																			<td class="row2"><?php if (!empty($r->Foto3)): ?>
																					<img src="<?php echo "admin/imagenes/" . $r->Foto3; ?>" width="150" height="150">

																				<?php endif; ?>
																			</td>
																			<td>Foto 4</td>
																			<td><?php if (!empty($r->Foto4)): ?>
																					<img src="<?php echo "admin/imagenes/" . $r->Foto4; ?>" width="150" height="150">

																				<?php endif; ?>
																			</td>
																		</tr>
																		<tr>
																			<td class="row2">Foto 5</td>
																			<td class="row2"><?php if (!empty($r->Foto5)): ?>
																					<img src="<?php echo "admin/imagenes/" . $r->Foto5; ?>" width="150" height="150">

																				<?php endif; ?>
																			</td>
																			<td>Foto 6</td>
																			<td><?php if (!empty($r->Foto6)): ?>
																					<img src="<?php echo "admin/imagenes/" . $r->Foto6; ?>" width="150" height="150">

																				<?php endif; ?>
																			</td>
																		</tr>
																		<tr>
																			<td class="row2">Foto 7</td>
																			<td class="row2"><?php if (!empty($r->Foto7)): ?>
																					<img src="<?php echo "admin/imagenes/" . $r->Foto7; ?>" width="150" height="150">

																				<?php endif; ?>
																			</td>
																			<td>Foto 8</td>
																			<td> <?php if (!empty($r->Foto8)): ?>
																					<img src="<?php echo "admin/imagenes/" . $r->Foto8; ?>" width="150" height="150">

																				<?php endif; ?>
																			</td>
																		</tr>
																	</tbody>
																</table>





																<div id="div_devolucion" style="display:none">
																	<table class="texto" width="100%" border="0" cellspacing="1" cellpadding="0" id=table1 align="center">
																		<tr bgcolor="#dfe3e7">
																			<td width="15%" align="center"><b>Referencia</b></td>
																			<td width="7%" align="center"><b>Talla</b></td>
																			<td align="center" width="14%"><b>Devueltos</b></td>
																			<td width="50%" align="center" nowrap><b>Observaciones</b></td>
																		</tr>


																		<?php for ($contador_filas = 0; $contador_filas <= 5; $contador_filas++): ?>

																			<tr>
																				<td align="left" class="col1list">
																					<select name="IDReferencia<?php echo $contador_filas ?>" id="IDReferencia<?php echo $contador_filas ?>">
																						<option value=""></option>
																						<?php
																						$q_detalle = db_query(" SELECT * FROM DetallePedidoTercero WHERE IDPedidoTercero = '$id'");
																						while ($r_detalle = db_fetch_array($q_detalle, $a)) {
																						?>
																							<option value="<?php echo $r_detalle["IDDetallePedidoTercero"] ?>"><?php echo $r_detalle["ReferenciaCaprino"] . $r_detalle["CodigoColor"]; ?></option>

																						<?php
																						}
																						?>
																					</select>
																				</td>
																				<td align="center" class="col1list">
																					<select name="IDTalla<?php echo $contador_filas ?>" id="IDTalla<?php echo $contador_filas ?>">
																						<option value=""></option>
																						<?php
																						foreach ($array_talla as $id_talla => $datos_talla): ?>
																							<option value="<?php echo $datos_talla["IDTalla"] ?>"><?php echo $datos_talla["Nombre"]; ?></option>
																						<?php
																						endforeach;
																						?>
																					</select>
																				</td>
																				<td class="col1list" align="center" width="14%">
																					<input type="text" name="Devuelto<?php echo $contador_filas ?>" id="Devuelto<?php echo $contador_filas ?>" value="" size="5">
																				</td>
																				<td class="col1list" align="center">
																					<textarea name="Observacion<?php echo $contador_filas ?>" id="Observacion<?php echo $contador_filas ?>" rows="3" cols="60"><?php echo $row_detalle["ObservacionDetalle"]; ?></textarea>
																				</td>
																			</tr>
																		<?php
																		endfor;
																		$contador_items++;
																		?>


																	</table>


																	<input type="submit" class="button" name="enviar_dev" value="Realizar Devolucion">
																</div>


															</td>
														</tr>
														<tr>
															<td colspan="4"><br>





															</td>
														</tr>

														<?php
														$sql_historia_devuelto = "Select * From DetallePedidoTerceroReferencia Where IDPedidoTercero = '" . $id . "' and IDPuntoVenta = '" . $IDPuntoVenta . "' and (Estado = 'Devuelto' or Observacion <> '') ";
														$qry_historia_devuelto = db_query($sql_historia_devuelto);
														if (db_num_rows($qry_historia_devuelto) > 0): ?>


															<tr>
																<td colspan="4">

																	<table class="texto" width="100%" border="0" cellspacing="1" cellpadding="0" id=table1 align="center">
																		<tr bgcolor="#dfe3e7">
																			<td colspan="6" align="center"><b>HISTORIAL DEVUELTOS</b></td>
																		</tr>
																		<tr bgcolor="#dfe3e7">
																			<td width="15%" align="center"><b>Referencia</b></td>
																			<td width="7%" align="center"><b>Talla</b></td>
																			<td align="center" width="14%"><b>Devueltos</b></td>
																			<td align="center" width="14%"><b>Fecha</b></td>
																			<td align="center" width="14%"><b>Factura</b></td>
																			<td width="50%" align="center" nowrap><b>Observaciones</b></td>
																		</tr>


																		<?php while ($row_historia_devuelto = db_fetch_array($qry_historia_devuelto)): ?>
																			<tr>
																				<td align="left" class="col1list">
																					<?php
																					echo $frmIDReferencia = get_field("DetallePedidoTercero", "ReferenciaCaprino", "IDDetallePedidoTercero", $row_historia_devuelto["IDDetallePedidoTercero"]);
																					echo $frmIDReferencia = get_field("DetallePedidoTercero", "CodigoColor", "IDDetallePedidoTercero", $row_historia_devuelto["IDDetallePedidoTercero"]);
																					?>
																				</td>
																				<td align="center" class="col1list">
																					<?php echo $frmTalla = get_field("Talla", "Descripcion", "IDTalla", $row_historia_devuelto["IDTalla"]); ?>
																				</td>
																				<td class="col1list" align="center" width="14%">
																					<?php echo $row_historia_devuelto["CantidadDevuelto"]; ?>
																				</td>
																				<td class="col1list" align="center" width="14%">
																					<?php echo $row_historia_devuelto["FechaDevuelto"]; ?>
																				</td>
																				<td class="col1list" align="center">
																					<?php echo $row_historia_devuelto["NumeroFactura"]; ?>
																				</td>
																				<td class="col1list" align="center">
																					<?php echo $row_historia_devuelto["Observacion"]; ?>
																				</td>
																			</tr>
																		<?php
																		endwhile;

																		?>


																	</table>


																</td>
															</tr>

														<?php endif; ?>



														<tr>
															<td class=col1 nowrap></td>
															<td class=col2></td>
															<td class=col1></td>
															<td class=col2></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr bgcolor=#e7ebef>
												<td colspan="4">&nbsp;</td>
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
		<?php
		} // END function print_form_fotos($id,$numfotos)
		/*******************************************************************************************
		funcion Listar
		 *******************************************************************************************/
		function list_r($sql = "")
		{
			global $TitleMod, $MOD, $Table, $Key, $listar, $IDPuntoVenta;
			if (empty($sql))
				$sql =  "Select * 
							From PedidoTercero PT, DetallePedidoTercero DPT, DetallePedidoTerceroReferencia DPTR
							Where PT.IDPedidoTercero = DPT.IDPedidoTercero 
							AND DPT.IDDetallePedidoTercero =  DPTR.IDDetallePedidoTercero
							AND DPTR.IDPuntoVenta = '" . $IDPuntoVenta . "'
							AND IDEstadoPedidoTercero > 1
							Group by PT.IDPedidoTercero
							Order by PT.IDPedidoTercero DESC
							";


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
						<table width=100% border=0 cellspacing=1 cellpadding=1 class="forumline texto">
							<tr>
								<td class="forumlink" colspan="2">
									<?php filtrar(); ?>
								</td>
							</tr>
							<tr>
								<td class="forumlink" colspan="2">
									<table width=100% border=0 cellspacing=1 cellpadding=0>
										<tr>
											<td align=center class=navpic valign=middle bgcolor=#DBEAF5 width=69>Ver</td>
											<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=IDCliente&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Proveedor&nbsp;
													<?php if ($_GET['order_by'] == "IDProveedor") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
											<td class=navpic nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroFacturaBono&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Numero</a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroFacturaBono&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'> Orden</a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroFacturaBono&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>
													<?php if ($_GET['order_by'] == "NumeroORdenCompra") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
											<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=FechaFacturaBono&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Fecha Pedido&nbsp;
													<?php if ($_GET['order_by'] == "FechaPedido") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
											<td class=navpic nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=ValorTotal&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Fecha Entrega </a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=ValorTotal&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>&nbsp;
													<?php if ($_GET['order_by'] == "FechaEntrega") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
											<td class=navpic nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=FechaFacturaBono&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Estado&nbsp;
													<?php if ($_GET['order_by'] == "IDEstadoPedidoTercero") { ?>
														<img src="images/<?php echo $img; ?>" border=0>
													<?php } ?>
												</a></td>
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
												<td nowrap class="<?= $class ?>"><?php echo get_field("Proveedor", "Nombre", "IDProveedor", $r->IDProveedor); ?></td>
												<td nowrap class="<?= $class ?>"><?php echo $r->NumeroOrdenCompra ?></td>
												<td nowrap class="<?= $class ?>"><?php echo formatofecha(substr($r->FechaPedido, 0, 10)) . " " . substr($r->FechaPedido, 10) ?></td>
												<td nowrap class="<?= $class ?>"><?php echo formatofecha(substr($r->FechaEntrega, 0, 10)) . " " . substr($r->FechaEntrega, 10) ?></td>
												<td nowrap class="<?= $class ?>"><?php
																					echo estado_tercero_pto_vta($r->IDPedidoTercero, $r->IDPuntoVenta);

																					//echo get_field("EstadoPedidoTercero","Descripcion","IDEstadoPedidoTercero",$r->IDEstadoPedidoTercero); 
																					?>
												</td>
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

					<table>
						<tr>
							<td>Numero Orden</td>
							<td><input type="text" name="NumeroOrdenCompra" id="NumeroOrdenCompra"></td>
							<td>Numero de Factura</td>
							<td><input type="text" name="NumeroFactura" id="NumeroFactura"></td>
							<td>Proveedor :</td>
							<td><select name="IDProveedor" id="IDProveedor">
									<option value="">[Seleccione]</option>
									<?php
									$sql_provee = db_query("Select * from Proveedor Where Publicar = 'S' Order by Nombre");
									while ($row_provee = db_fetch_array($sql_provee)) {
									?>
										<option value="<?php echo $row_provee["IDProveedor"]; ?>"><?php echo $row_provee["Nombre"]; ?></option>
									<?php
									}
									?>
								</select></td>
						</tr>
						<tr>
							<td>Estado:</td>
							<td><select name="IDEstadoPedidoTercero" id="IDEstadoPedidoTercero">
									<option value="">[Seleccione]</option>
									<?php
									$sql_estados = db_query("Select * from EstadoPedidoTercero Where 1 Order by Descripcion");
									while ($row_estado = db_fetch_array($sql_estados)) {
									?>
										<option value="<?php echo $row_estado["IDEstadoPedidoTercero"]; ?>"><?php echo $row_estado["Descripcion"]; ?></option>
									<?php
									}
									?>
								</select></td>
							<td>Referencia</td>
							<td><input type="text" name="ReferenciaCaprino" id="ReferenciaCaprino"></td>
							<td>Tipolog&iacute;a</td>
							<td><input type="text" name="Tipologia" id="Tipologia"></td>
						</tr>
						<tr>
							<td>Desde</td>
							<td><input type="text" name="FechaDesde" class="input" value="<?= $FechaDesde ?>" size="10">

								<script language="JavaScript1.2">
									<!--
									if (!document.layers)
										document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//
									-->
								</script>
							</td>
							<td>Hasta</td>
							<td><input type="text" name="FechaHasta" class="input" value="<?= $FechaHasta ?>" size="10">

								<script language="JavaScript1.2">
									<!--
									if (!document.layers)
										document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//
									-->
								</script>
							</td>
							<td></td>
							<td></td>
						</tr>
					</table>

					<br>
					<input type="hidden" name="mod" value="<?= $MOD ?>">
					<input type="hidden" name="action" value="list">
					<input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>

		</form>
	<?php
		} //End function filtrar
	?>