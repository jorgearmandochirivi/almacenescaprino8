<body> <?php


		$TitleMod = "Entrada de Pedidos";
		$Table = "Pendientes";
		$TableJoin = "";
		$Key = "IDPendientes";
		$MOD = "Movimiento";
		$m = "Movimientos";
		$permisos = get_permiso($ID_Usuario, $m, $Table);
		if ($permisos[0] >= 2) {
			switch (nvl($action)) {
				case "add":
					print_form($id, "insert", "Actualizar $TitleMod", "Realizar Movimiento");
					break;

				case "insert":
					$frm = vars_LOG($_POST);
					db_query("SET AUTOCOMMIT=0");
					db_query("BEGIN");




					foreach ($frm["Ingreso"] as $idpuntoref => $tallas)
						foreach ($tallas as $idtalla => $valor) {

							//Actualizar Existencias

							$existencias = get_field("CodificacionEspecifica", "Existencias", "IDPuntoVentaReferencia", $idpuntoref . "' AND IDTalla = '$idtalla");
							$existencias = $existencias + $valor;

							$maximo = get_field("CodificacionEspecifica", "Maximo", "IDPuntoVentaReferencia", $idpuntoref . "' AND IDTalla = '$idtalla");

							if (($valor > 0) && ($existencias <= $maximo)) {

								if ((int)$IDPuntoVenta <= 0):
									echo "Error Punto venta, vuelva a intentarlo";
									exit;
								else:

									//insertar entrada
									$identrada = get_maxID("Entrada", "IDEntrada");
										$sql_entrada = "INSERT INTO Entrada VALUES('$identrada','" . $frm['Remision'] . "','" . $frm['NumeroFactura'] . "','" . $frm['Fecha'] . "','$idpuntoref','$idtalla','$valor',NOW(),'$IDPuntoVenta')";
									db_query($sql_entrada);

									$sql_actualizacod = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDPuntoVentaReferencia = '$idpuntoref' AND IDTalla = '$idtalla'";
									db_query($sql_actualizacod);

									//insertar el log
									$idcodificacion = get_field("CodificacionEspecifica", "IDCodificacionEspecifica", "IDPuntoVentaReferencia", $idpuntoref . "' AND IDTalla = '$idtalla");
									//insertlog($ID_Usuario,"CodificacionEspecifica",$idcodificacion,"Actualizar",$sql_actualizacod);

									$existencias = 0;
								endif;
							} //end if( $valor > 0 )
							elseif ($existencias > $maximo) {
								//No hacemos nada hasta que arreglen los maximos
								//db_query( "ROLLBACK" );
								$frmIDReferencia = get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $IDPuntoVentaReferencia[$key]);
								$frmReferencia = get_field("Referencia", "Numero", "IDReferencia", $frmIDReferencia);
								$frmTalla = get_field("Talla", "Descripcion", "IDTalla", $IDTalla[$key]);
								echo Mensaje_Info("La Referencia $frmReferencia en la Talla $frmTalla Supera el Maximo y no se realizara esta entrada para esta referencia ", "col2");
								$msg  = 1;
							} //end elseif





						} //end foreach($ingreso as $key => $valor)

					/*$frm['ID'] = insert_width_table($frm,"Movimiento","IDMovimiento");
				entradapedido($frm);*/
					db_query("COMMIT");

						if ($msg <> 1):
							echo "<script>window.open( 'Movimiento/popEntradas.php?Remision=" . $frm['Remision'] . "','','width=500, height=500, scrollbars=1, resize=yes' );</script>";
						endif;
					break;
				case "edit":
					print_form($id, "update", "Actualizar $TitleMod", "Realizar Movimiento");
					break;
				case "update":
					entradapedido($_POST);
					echo "<script>location.href='?mod=Movimiento';</script>";
					break;
				case "del":
					print_form($id, "delete", "Eliminar $TitleMod", "Remover Registro");
					break;
					case "delete":
						$_GET['action'] = "";
					delete($ID);
					break;
				case "list":

					if ($field == 'Talla.Descripcion') {
						$sql = "SELECT Pendientes.* FROM Pendientes, Talla WHERE Talla.Descripcion LIKE '%$QryString%' AND Talla.IDTalla = Pendientes.IDTalla AND IDPuntoVenta = '$IDPuntoVenta' GROUP BY IDPendientes ORDER BY IDPuntoVentaReferencia ";
					} else {
						$sql = make_qry_string($_GET);
					}

					list_r($sql);
					break;
				case "listar":
					//print_r( $_POST );
					list_r($_POST, "entrada");
					break;
				case "entrada":
					//print_r( $_POST );
					list_r($_POST, "insert");
					break;
				default:
					previoentrada();
					break;
			} // End switch

		} //end if(permisos[0] > 2)
		else
			echo Mensaje_Info("No tiene Permisos Suficientes", "col2");



		/*******************************************************************************************
		funcion Listar
		 *******************************************************************************************/
			function list_r($frm, $newmode)
			{
				global $TitleMod, $MOD, $Table, $Key, $listar, $IDPuntoVenta, $_POST;
				$referencias_csv = "";
				if (is_array($frm) && isset($frm['Referencias'])) {
					$referencias_csv = (string)$frm['Referencias'];
				}
				$referencias_ids = array();
				foreach (explode(",", $referencias_csv) as $ref_id) {
					$ref_id = (int)trim($ref_id);
					if ($ref_id > 0) {
						$referencias_ids[] = $ref_id;
					}
				}
				$referencias_sql = !empty($referencias_ids) ? implode(",", $referencias_ids) : "0";

			/*
	 	$sql =  "SELECT * FROM Pendientes P, Referencia R, PuntoVentaReferencia PR 
	 				WHERE  P.IDPuntoVenta = '$IDPuntoVenta' 
	 				AND P.IDPuntoVentaReferencia IN ($frm[Referencias])
	 				AND P.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia 
	 				AND PR.IDReferencia = R.IDReferencia 
	 				GROUP BY P.IDPuntoVentaReferencia 
	 				ORDER BY R.Numero ASC";
	 			*/
			$sql =  "SELECT * FROM  Referencia R, PuntoVentaReferencia PR 
	 				WHERE  PR.IDPuntoVenta = '$IDPuntoVenta' 
	 				AND PR.IDPuntoVentaReferencia IN ($referencias_sql)
	 				AND PR.IDReferencia = R.IDReferencia 
	 				ORDER BY R.Numero ASC";
			$nav = new buildNav;
			$nav->offset = 'offset';
			$nav->number_type = 'number';
			(!empty($listar)) ? $nav->limit = $listar : $nav->limit = 1000;
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

				$i = 0;
				while ($r = db_fetch_array($result)) {
					$array_referencias[$i] = $r;
					//$sql_tallas = " SELECT * FROM Pendientes WHERE IDPuntoVenta = '$IDPuntoVenta' AND CantidadPendiente > 0 AND IDPuntoVentaReferencia = '$r[IDPuntoVentaReferencia]' ";
						$sql_tallas = " SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '" . $r['IDPuntoVentaReferencia'] . "' ";
						$qry_tallas = db_query($sql_tallas);
						$j = 0;
						while ($r_tallas = db_fetch_array($qry_tallas)) {
							$array_tallas[$r['IDPuntoVentaReferencia']][$j] = $r_tallas;
							$j++;
						} //end while

					if ($j > $colspan)
						$colspan = $j;



					$i++;
				} //end while


					$sql_entradas = " SELECT E.*, R.Numero FROM Entrada E, PuntoVentaReferencia PR, Referencia R
							WHERE E.Remision = '" . $frm['Remision'] . "' 
							AND E.IDPuntoVenta = '$IDPuntoVenta'
							AND E.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia
							AND PR.IDReferencia = R.IDReferencia
							ORDER BY R.Numero";
				$qry_entradas = db_query($sql_entradas);
				while ($r_entradas = db_fetch_array($qry_entradas)) {

						if (empty($array_tallas_ya[$r_entradas['Numero']][$r_entradas['IDTalla']]))
							$tallas++;

						$array_entradas_ya[$r_entradas['Numero']][$r_entradas['IDTalla']] = $r_entradas['Cantidad'];
						$array_tallas_ya[$r_entradas['Numero']][$r_entradas['IDTalla']]++;

						if (empty($array_tallas_ya[$r_entradas['Numero']][$r_entradas['IDTalla']]))
							$tallas++;
				} //ed while


				//print_r( $array_entradas_ya );

		?>
			<br>
			<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="600">

				<tr>
					<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
					</td>
					<td class="tbtbot"><b></b>
						<span class="gen">
							<?php echo $TitleMod . " - " . $info ?>
						</span>
					</td>
					<td class="tbtr">
						<img src="images/spacer.gif" alt="" width="124" height="22" />
					</td>
				</tr>
			</table>


			<table class="forumline" width="600" cellspacing="1" border="0" align="center">
				<tr>
					<td>
						<?php
						filtrar();
						?>
					</td>
				</tr>
				<tr>
					<td>
						<form name="frm" action="<?= $PHP_SELF ?>" method="post" onSubmit="disable(this);">
							<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline">
								<tr>
									<?php
										if (empty($frm['Remision']))
											$frm['Remision'] = get_maxID("Entrada WHERE IDPuntoVenta = '$IDPuntoVenta'", "Remision");
										?>
										<td class="col1" nowrap>Entrada No.</td>
										<td class="col2"><input type="input" name="Remision" readonly value="<?= $frm['Remision'] ?>" class="tbox" id="Remision"></td>
										<td class="col1" nowrap>Numero de Factura</td>
										<td class="col2"><input type="input" name="NumeroFactura" readonly value="<?= $frm['NumeroFactura'] ?>" class="tbox" required></td>
								</tr>
								<tr>
									<td class="col1" nowrap>Fecha</td>
									<td class="col2" nowrap>
											<input type="input" name="Fecha" readonly value="<?= $frm['Fecha'] ?>" id="Fecha" class="tbox">
									</td>
									<td class="col1" colspan="2">

									</td>
								</tr>

							</table>

							<!--LO QUE YA ESTA-->
							<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline">
								<tr>
									<td class=navpic align="center" nowrap bgcolor="#DBEAF5">REFERENCIA</td>
									<td class=navpic nowrap bgcolor="#DBEAF5" align="center" colspan="<?= $tallas ?>">TALLAS</td>
								</tr>

								<?php
								foreach ($array_entradas_ya as $key => $valor) {

									$class = repetition() ? "col1list" : "col2list";
										$tamanoarray = isset($array_tallas_ya[$key]) ? count($array_tallas_ya[$key]) : 0;
									$columnasmas = $colspan - $tamanoarray;
								?>

									<tr>
										<td nowrap class="navpic"></td>
										<?php
											foreach (($array_tallas_ya[$key] ?? array()) as $idtalla => $numerodetallas) {
										?>
											<td nowrap class="navpic"><b><?php echo get_field("Talla", "Descripcion", "IDTalla", $idtalla); ?></b></td>
										<?php
										}

										?>
									</tr>


									<tr>
										<td nowrap width="74" class="<?= $class ?>"><?= $key ?></td>
										<?php
										$j = 0;
										foreach ($valor as $idtalla => $cantidad) {
										?>
											<td nowrap class="<?= $class ?>"><b><?php echo $cantidad;
																				$TotalYaCargadas++; ?></b></td>
										<?php
											$j++;
										} //end for



										?>
									</tr>

								<?php
								} //end for

								//LISTAR REFERENCIAS

								?>
							</table>
							<!--LO QUE YA ESTA-->
							<br>
							<table width="100%">
								<tr>
									<td class="navpic" width="240"><strong>TOTAL REFERENCIAS YA CARGADAS</strong></td>
									<td align="left" class="navpic"><?= $TotalYaCargadas ?></td>
								</tr>
							</table>
							<br>

							<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline">
								<tr>
									<td class=navpic align="center" nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=Referencia.Numero&in_order=" . $order . "&listar=" . $nav->limit . "&tjoin=PuntoVentaReferencia&action=list"; ?>' style="text-decoration: none;">REFERENCIA</a><a style="text-decoration: none;" href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=Referencia.Numero&tjoin=PuntoVentaReferencia&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>&nbsp;<?php if ($_GET['order_by'] == "Referencia.Numero") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
									<td class=navpic nowrap bgcolor=#DBEAF5 align="center" colspan="<?= $colspan + 1 ?>">TALLAS</td>
								</tr>

								<?php
								foreach ($array_referencias as $key => $valor) {

									$class = repetition() ? "col1list" : "col2list";
											$tamanoarray = isset($array_tallas[$valor['IDPuntoVentaReferencia']]) ? count($array_tallas[$valor['IDPuntoVentaReferencia']]) : 0;
									$columnasmas = $colspan - $tamanoarray;
								?>

									<tr>
										<td nowrap class="<?= $class ?>"></td>
										<?php
												foreach (($array_tallas[$valor['IDPuntoVentaReferencia']] ?? array()) as $preferencia => $datos) {
											?>
												<td nowrap class="<?= $class ?>"><b><?php echo get_field("Talla", "Descripcion", "IDTalla", $datos['IDTalla']); ?></b></td>
										<?php
										}
										//anadir las columnas que falten (DISENO)
										for ($i = 0; $i < $columnasmas; $i++) {
										?>
											<td nowrap class="<?= $class ?>">
											</td>
										<?php
										} //end for
										?>
										<td class="<?= $class ?>">
										</td>
									</tr>

									<tr>
											<td nowrap class="<?= $class ?>"><?php echo get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $valor['IDPuntoVentaReferencia'])) ?></td>
											<?php
											$TIngreso = 0;
												foreach (($array_tallas[$valor['IDPuntoVentaReferencia']] ?? array()) as $preferencia => $datos) {
											?>
												<td nowrap class="<?= $class ?>">
												<?php //echo number_format($datos[CantidadPendiente]); 
												?><br>
													<input type="hidden" name="IDPuntoVentaReferencia[<?= $datos['IDPuntoVentaReferencia'] ?>]" value="<?= $datos['IDPuntoVentaReferencia'] ?>">
													<input type="hidden" name="IDTalla[<?= $datos['IDPuntoVentaReferencia'] ?>][<?= $datos['IDTalla'] ?>]" value="<?= $datos['IDTalla'] ?>">
													<?php
													$pend = $datos['IDPendientes'];
														$Ingr = (isset($_POST['Ingreso']) && is_array($_POST['Ingreso'])) ? $_POST['Ingreso'] : array();
														$TIngreso += (int)($frm["Ingreso"][$datos['IDPuntoVentaReferencia']][$datos['IDTalla']] ?? 0);
														$ingresotalla = (int)($frm["Ingreso"][$datos['IDPuntoVentaReferencia']][$datos['IDTalla']] ?? 0);
														$TPares += (int)($Ingr[$pend] ?? 0);
													?>
													<input type="text" size="5" name="Ingreso[<?= $datos['IDPuntoVentaReferencia'] ?>][<?= $datos['IDTalla'] ?>]" value="<?php echo $ingresotalla ?>">
													<input type="hidden" name="IDPendientes[<?= $datos['IDPendientes'] ?>]" value="<?= $datos['IDPendientes'] ?>">
												</td>

										<?php
										}
										//anadir las columnas que falten (DISENO)
										for ($i = 0; $i < $columnasmas; $i++) {
										?>
											<td nowrap class="<?= $class ?>">
											</td>
										<?php
										} //end for
										?>
										<td class="<?= $class ?>">
											<input type="text" readonly size="5" name="TIngreso" value="<?= $TIngreso ?>" value="">
										</td>
									</tr>
								<?php } // END for
								if ($TPares > 0) {
								?>
									<tr>
										<td bgcolor=#DBEAF5 colspan="<?= $colspan + 2 ?>" nowrap class="navpic" align="center">
											Total Pares a ingresar = <?= $TPares ?>
										</td>
									</tr>
								<?php
								}
								?>
								<tr>
									<td bgcolor=#DBEAF5 colspan="<?= $colspan + 2 ?>" nowrap class="navpic" align="center">
										<?php
										print $pages;
										?>
										<input type="hidden" name="action" value="<?= $newmode ?>">
										<input type="hidden" name="Referencias" value="<?= $frm['Referencias'] ?>">
										<?php
										if ($newmode == "entrada") {
											$caption = "Realizar Entrada";
										} else {
											$caption = "Confirmar Entrada";
										}
										?>
										<div id="bloquea_confirmar"><br>
											<input type="submit" class="button" name="enviar" id="<?php if ($caption == "Confirmar Entrada") echo "envia_entrada" ?>" value="<?php echo $caption; ?>">
										</div>
									</td>
									<td></td>



















































































					</td>
				</tr>
			</table>
			</form>
			</td>
			</tr>
			</table>
		<?php
			} // End if$rows
			else
				echo "<br><br><span class=subtitle><b>No hay pedidos pendientes </b></span>";
		}// Enf function list()				


		/*******************************************************************************************
		funcion previo entrada
		 *******************************************************************************************/
		function previoentrada($sql = "")
		{
			global $TitleMod, $MOD, $Table, $Key, $listar, $IDPuntoVenta;

			$sql_referencias = "SELECT R.IDReferencia, R.Numero, PVR.IDPuntoVentaReferencia
							FROM Referencia R, PuntoVentaReferencia PVR, Pendientes P 
							WHERE PVR.IDPuntoVenta = '$IDPuntoVenta' 
							AND PVR.IDReferencia = R.IDReferencia
							AND PVR.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia";

			$qry_referencias = db_query($sql_referencias);
			$i = 0;
			while ($r_referencias = db_fetch_array($qry_referencias)) {
				$array_referencias[$i] = $r_referencias;
				$i++;
			} //end while

		?>
		<script>
			<!--
			function addSelect(newTxt, newVal, num) {
				newOption = new Option(newTxt, newVal, false, false);
				document.frm.Referencias.options[document.frm.Referencias.length] = newOption;
			}

			function removeitem(PopName) {
				var boxLength = PopName.length;
				arrSelected = new Array();
				var count = 0;
				for (i = 0; i < boxLength; i++) {
					if (PopName.options[i].selected) {
						arrSelected[count] = PopName.options[i].value;
					}
					count++;
				}
				var x;
				for (i = 0; i < boxLength; i++) {
					for (x = 0; x < arrSelected.length; x++) {
						if (PopName.options[i].value == arrSelected[x]) {
							PopName.options[i] = null;
						}
					}
					boxLength = PopName.length;
				}
			}


			function setSelectOptions(PopName) {
				strValues = "";

				for (var i = 0; i < PopName.length; i++) {

					PopName.options[i].selected = 'TRUE';
					if (i == 0) {
						strValues = PopName.options[i].value;

					} else {
						strValues = strValues + "," + PopName.options[i].value;
					}
				}

				if (i == 0) {
					newoption = new Option('', '', false, false);
					PopName.options[0] = newoption;
				} else
					PopName.options[i - 1].value = strValues;



				return true;
			}
			var Check = new Array('Remision', 'Fecha');

			//
			-->
		</script>
		<br>
		<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="600">

			<tr>
				<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
				</td>
				<td class="tbtbot"><b></b>
					<span class="gen">
						<?php echo $TitleMod . " - " . $info ?>
					</span>
				</td>
				<td class="tbtr">
					<img src="images/spacer.gif" alt="" width="124" height="22" />
				</td>
			</tr>
		</table>


		<table class="forumline" width="600" cellspacing="1" border="0" align="center">
			<tr>
				<td>
					<form name="frm" action="<?= $PHP_SELF ?>" method="post" onsubmit="setSelectOptions(document.frm.Referencias);return EvaluaReg(this,Check)">
						<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline">
							<tr>
								<?php
								if (empty($frm['Remision']))
									$frm['Remision'] = get_maxID("Entrada WHERE IDPuntoVenta = '$IDPuntoVenta'", "Remision");
								?>
								<td class="col1" nowrap>Entrada No.</td>
								<td class="col2"><input type="input" name="Remision" class="tbox" id="Remision" value="<?= $frm['Remision'] ?>"></td>
								<td class="col1" nowrap>Numero de Factura</td>
								<td class="col2"><input type="input" name="NumeroFactura" class="tbox" required></td>
							</tr>
							<tr>
								<td class="col1" nowrap>Fecha</td>
								<td class="col2" nowrap><input type="input" name="Fecha" id="Fecha" class="tbox" value="<?php echo date("Y-m-d"); ?>">
									<script language="JavaScript1.2">
										<!--
										if (!document.layers)
											document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
										//
										-->
									</script>
								</td>
								<td class="col1" colspan="2">

								</td>
							</tr>
							<tr>
								<td class="col2" colspan="4">
									Agregue las Referencias a ingresar el pedido y haga clic en 'Continuar'.
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<table cellpadding="1" align=center cellspacing="1" width="100%">
										<td class="col1list">Para agregar una referebcia haga <a href="javascript:;" onClick="window.open('Referencia/poppendientes.php','','width=600,height=500'); this.value=''">click aqu&iacute; </a><br>
											<select name="Referencias" style="width:180px; " size="20" multiple class="inputSelect" id="Referencias"></select><br>
											</a>Para eliminar una referencia haga <a href="JavaScript:removeitem(document.frm.Referencias);" class="tex-menu-sup">click aqu&iacute; </a><br>
										</td>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" class="col1list">
									<input type="hidden" name="action" value="listar">
									<input type="submit" value="Continuar" name="enviar">
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
	<?php
		}// Enf function previoentrada()				


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
						<option value="Referencia.Numero">Referencia</option>
						<option value="Talla.Descripcion">Talla</option>
					</select>
					<input type="text" size="10" name="QryString" id="Buscar Por" class="post">


					ordenar por
					<select name="order_by" class="popup">
						<option value="PuntoVentaReferencia.IDReferencia">Referencia</option>
						<option value="CantidadPendiente">CantidadPendiente</option>
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
					<input type="hidden" name="rangofield" value="FechaOrden">
					<input type="hidden" name="action" value="list">
					<input type="hidden" name="tjoin" value="PuntoVentaReferencia">
					<input type="hidden" name="IDPuntoVenta" value="<?= $IDPuntoVenta ?>">
					<input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>
		</form>
	<?php
		} //End function filtrar
	?>
