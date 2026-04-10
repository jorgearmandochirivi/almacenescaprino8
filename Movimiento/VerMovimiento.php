<body> <?php

		$TitleMod = "Movimientos";

		$Table = "Movimiento";
		$TableJoin = "DetalleMovimiento";
		$Key = "IDMovimiento";
		$MOD = "VerMovimiento";
		$m = "VerMovimientos";
		$permisos = get_permiso($ID_Usuario, $m, $Table);
		if ($permisos[0] >= 2) {
			switch (nvl($action)) {
				case "add":
					print_form($id, "insert", "Actualizar $TitleMod", "Realizar Movimiento");
					break;


				case "edit":
					print_form($id, "update", "Actualizar $TitleMod", "Realizar Movimiento");
					break;


				case "list":
					if ($field == "Numero") {
						$sql = "SELECT T.* FROM $Table T, $TableJoin DT,  PuntoVentaReferencia PR, Referencia R
							WHERE  T.IDPuntoVenta = '$IDPuntoVenta'
							AND T.$Key = DT.$Key
							AND DT.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia
							AND PR.IDReferencia = R.IDReferencia
							AND R.Numero LIKE '%$QryString%'
							GROUP BY IDMovimiento ORDER BY Fecha DESC ";
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
		function print_form($id = "", $newmode, $title, $submit_caption)
		{
			global $TitleMod, $Table, $MOD, $Key, $ID_Usuario, $IDPuntoVenta, $dirroot, $cantidad_total;

			$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' AND IDPuntoVenta = '$IDPuntoVenta' ");

			$r = db_fetch_object($qid);
		?>
		<script>
			var Check = new Array('IDMovimiento', 'IDTipoMovimiento', 'Remision', 'FechaRemision', 'IDOrdenCompra', 'Fecha', 'IDempleado', 'Estado', 'Observaciones', 'Publicar', 'UsuarioTrCr', 'FechaTrCr', 'UsuarioTrEd', 'FechaTrEd');
		</script>
		<br>
		<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">

			<tr>
				<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
				</td>
				<td class="tbtbot"><b></b>
					<span class="gen">
						Movimientos
					</span>
				</td>
				<td class="tbtr">
					<img src="images/spacer.gif" alt="" width="124" height="22" />
				</td>
			</tr>
		</table>


		<table class="forumline" width="550" cellspacing="1" border="0" align="center">
			<tr>
				<td>
					<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline">
						<form name="frm" action="<?= $PHP_SELF ?>" method="post" onSubmit="return EvaluaReg(this,Check);">
							<tr>
								<td class="row1" nowrap>
									<table width=100% cellspacing="1" cellpadding="1" bgcolor=#ffffff>
										<tr>
											<td class=col1>Consecutivo</td>
											<td class=col2><?= $r->IDMovimiento ?></td>
											<td class=col1></td>
											<td class=col2></td>
										</tr>
										<tr>
											<td class=col1>Numero.Orden</td>
											<td class=col2><input type="text" class="input" name="NumeroOrden" readonly size="24" value="<?= $r->NumeroOrden ?>"></td>
											<td class=col1>Documento</td>
											<td class=col2><input type="text" class="input" name="Remision" size="24" value="<?= $r->Remision ?>"></td>
										</tr>
										<tr>
											<td class=col1>Fecha Remisi&oacute;n</td>
											<td class=col2>
												<input type="text" class="input" name="FechaRemision" size="15" value="<?= fecha() . " " . hora() ?>" readonly>
												<script language="JavaScript1.2">
													<!--
													if (!document.layers)
														document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
													//
													-->
												</script>
											</td>
											<td class=col1>Tipo de Movimiento</td>
											<td class=col2><input type="text" class=input name="Empleado" value='<?php echo get_field("TipoMovimiento", "NombreMovimiento", "IDTipoMovimiento", $r->IDTipoMovimiento) ?>' readonly>
											</td>
										</tr>
										<tr>
											<td class=col1>Empleado</td>
											<td class=col2><input type="text" class=input name="Empleado" value="<?php if ($newmode == "insert") echo $ID_Usuario;
																													else echo  $r->IDEmpleado; ?>"></td>
											<td class=col1>Punto de Venta</td>
											<td class=col2>
												<?php
												echo get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $IDPuntoVenta);
												?>
											</td>
										</tr>
										<tr>
											<td class=col1>Observaciones</td>
											<td colspan="3" class=col2><textarea name="Observaciones" rows="4" cols="64"><?= $r->Observaciones ?></textarea></td>
										</tr>
										<tr>
											<td class=col1 colspan="4"></td>
										</tr>
										<tr>
											<td class=navpic colspan="4">Detalle Movimiento</td>
										</tr>
										<tr>
											<td class=row2 colspan="4">
												<?php
												verdetallemovimiento($r->IDMovimiento); ?>
											</td>
										</tr>

										<tr>
											<td class=row1 colspan="4" align="center">
												<input type="hidden" name="action" value="<?= $newmode ?>">
												<input type="hidden" name="ID" value="<?= $id ?>">
												<input type="hidden" name="IDEmpleado" value="<?php if ($newmode == "insert") echo $ID_Usuario;
																								else echo  $r->IDEmpleado; ?>">
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</form>
					</table>
				</td>
			</tr>
		</table>





		<div id="areaimprimir">

			<?php
			$filedir = $dirroot . "/filesotros/Salida/";
			$name = "Salida" . $r->IDMovimiento . "_" . $IDPuntoVenta . ".html";
			$namePDF = "Salida" .  $r->IDMovimiento . "_" . $IDPuntoVenta . ".pdf";
			$file = "$filedir$name";
			$filepdf = "$filedir$namePDF";
			ob_start();
			?>

			<table width=80% border=0 cellspacing=1 cellpadding=1 class="texto forumline" align="center">
				<tr>
					<td class="row1" nowrap>
						<table width="500px" cellspacing="1" cellpadding="1" bgcolor=#ffffff align="center">
							<tr>
								<td class=col1><span style="font-size:30px; !important">Consecutivo:</span></td>
								<td class=col2><?= $r->IDMovimiento ?></td>
							</tr>
							<!--
                                                <tr>
                                                  <td class=col1>Documento</td>
                                                  <td class=col2><?= $r->Remision ?></td>
                                                </tr>

																							<tr>
                                                  <td class=col1>Numero.Orden</td>
                                                  <td class=col2><?= $r->NumeroOrden ?></td>
                                                </tr>
																							-->
							<tr>
								<td class=col1>Fecha Remisi&oacute;n</td>
								<td class=col2><?= fecha() . " " . hora() ?></td>
							</tr>
							<tr>
								<td class=col1>Tipo de Movimiento</td>
								<td class=col2><?php echo get_field("TipoMovimiento", "NombreMovimiento", "IDTipoMovimiento", $r->IDTipoMovimiento) ?></td>
							</tr>
							<tr>
								<td class=col1>Empleado</td>
								<td class=col2>
									<?php echo get_field("Empleado", "Nombre", "IDEmpleado", $r->IDEmpleado); ?>


								</td>
							</tr>
							<tr>
								<td class=col1>Punto de Venta</td>
								<td class=col2><?php
												echo get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $IDPuntoVenta);
												?></td>
							</tr>
						</table>
						<br><br>
						<table width="500px" cellspacing="1" cellpadding="1" bgcolor=#ffffff align="center">
							<tr>
								<td colspan="2" class=col1>Observaciones:</td>
							</tr>
							<tr>
								<td colspan="2" class=col1 nowrap width="200"><?= $r->Observaciones ?>
									<br><br>

								</td>
							</tr>
							<tr>
								<td colspan="2" class=col1 nowrap width="200">Detalle Movimiento</td>
							</tr>
							<tr>
								<td colspan="2" class=col1 nowrap width="200"><?php verdetallemovimiento_texto($r->IDMovimiento); ?></td>
							</tr>
						</table>
						<br><br>


					</td>
				</tr>

			</table>

			<?php


			$page = ob_get_contents();
			$fw = fopen($file, "w");
			fputs($fw, $page, strlen($page));
			fclose($fw);

			ob_end_clean();
			//echo $page;
			//passthru("htmldoc --format pdf --size 'Universal' --textfont Arial --title 'Acta' --charset 8859-15 --left 0cm --right 0cm --top 0cm --bottom 0cm --fontsize 7 --webpage $file -f $filedir/$namePDF");
				passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf");
			?>
		</div>

		<div align="center">
			<a style="font-size:16px" target="_blank" href="/admin/filesotros/Salida/Salida<?= $r->IDMovimiento . "_" . $IDPuntoVenta ?>.pdf">Imprimir Salida</a>
		</div>






	<?php
		}// End function print_form()

		/*******************************************************************************************
		funcion Listar
		 *******************************************************************************************/
		function list_r($sql = "")
		{
			global $TitleMod, $MOD, $Table, $Key, $listar, $IDPuntoVenta;
			if (empty($sql))
				$sql =  "SELECT * FROM Movimiento WHERE IDPuntoVenta = '$IDPuntoVenta' ORDER BY Fecha DESC";

			$nav = new buildNav;
			$nav->offset = 'offset';
			$nav->number_type = 'number';
			(!empty($listar)) ? $nav->limit = $listar : $nav->limit = 50;
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

	?>

		<?php
			if ($rows > 0) {
		?>
			<br>
			<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">

				<tr>
					<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
					</td>
					<td class="tbtbot"><b></b>
						<span class="gen">
							Movimientos - <?= $info ?>
						</span>
					</td>
					<td class="tbtr">
						<img src="images/spacer.gif" alt="" width="124" height="22" />
					</td>
				</tr>
			</table>

			<table class="forumline" width="550" cellspacing="1" border="0" align="center">
				<tr>
					<td>
						<?php
						filtrar();
						?>
					</td>
				</tr>
				<tr>
					<td>
						<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline">
							<tr>
								<td align=center class=navpic valign=middle nowrap bgcolor=#DBEAF5>Crear</td>
								<td class=navpic nowrap bgcolor=#DBEAF5>Consecutivo</td>
								<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=IDPuntoVenta&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Punto de Venta</a><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=IDPuntoVenta&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>&nbsp;<?php if ($_GET['order_by'] == "IDPuntoVenta	") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
								<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=IDTipoMovimiento&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Tipo Movimiento</a><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=IDTipoMovimiento&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>&nbsp;<?php if ($_GET['order_by'] == "IDTipoMovimiento") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
								<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=Fecha&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Fecha</a><a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=Fecha&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">&nbsp;<?php if ($_GET['order_by'] == "Fecha") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
								<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=Remision&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Remision</a><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=Remision&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'><?php if ($_GET['order_by'] == "Remision") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
							</tr>

							<?php
							while ($r = db_fetch_object($result)) {

								$class = repetition() ? "col1list" : "col2list";
							?>

								<tr>
									<td align=center valign=middle nowrap width=50 class="<?= $class ?>">
										&nbsp;<a href='<?php echo "?mod=$MOD&action=add&id=";
														echo $r->$Key; ?>' title="Crear Movimiento"><img src='images/edit.gif' border='0'></a>
									</td>
									<td class="<?= $class ?>"><?= $r->IDMovimiento ?></td>
									<td nowrap class="<?= $class ?>"><?= get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r->IDPuntoVenta) ?></td>
									<td nowrap class="<?= $class ?>"><?php echo get_field("TipoMovimiento", "NombreMovimiento", "IDTipoMovimiento", $r->IDTipoMovimiento) ?></td>
									<td nowrap class="<?= $class ?>"><?php echo $r->Fecha ?></td>
									<td nowrap class="<?= $class ?>"><?php echo $r->Remision; ?></td>
								</tr>
							<?php } // END for
							?>
							<tr>
								<td class=texto bgcolor=#DBEAF5 colspan=6 nowrap>
									<?php
									print $pages;
									?>
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
						<option value="NumeroOrden">NumeroOrden</option>
						<option value="Numero">Referencia</option>
						<option value="Fecha">Fecha</option>
						<option value="TipoMovimiento.NombreMovimiento">Tipo Movimiento</option>
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
						<option value="NumeroOrden">NumeroOrden</option>
						<option value="Fecha">Fecha</option>
						<option value="Remision">Remision</option>
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
					<input type="hidden" name="rangofield" value="Fecha">
					<input type="hidden" name="action" value="list">
					<input type="hidden" name="IDPuntoVenta" value="<?= $IDPuntoVenta ?>">
					<input type="hidden" name="tjoin" value="TipoMovimiento">
					<input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>
		</form>
	<?php
		}//End function filtrar

		/*******************************************************************************************
	verdetallemovimiento: Muestra el detalle de el Movimiento
	Parametros:
			$id : id del Pedido a Mostrar
	Retorna:
			Void
		 *******************************************************************************************/

		function verdetallemovimiento($id)
		{

			global $TitleMod, $MOD, $Table, $Key, $TableJoin, $cantidad_total;

			$sql_referencias =  "SELECT * FROM $TableJoin WHERE $Key = '$id' GROUP BY IDPuntoVentaReferencia";

			$query_referencias = db_query($sql_referencias);

			$i = 0;
	?>
		<table width=100% cellpadding=1 cellspacing=1 class=text align=center bgcolor=#ffffff>
			<?php
			while ($r_referencias = db_fetch_object($query_referencias)) {

				$sql_detalle =  "SELECT * FROM $TableJoin WHERE $Key = '$id' AND IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia'";
				$query_detalle = db_query($sql_detalle);
				$rows_detalle = db_num_rows($query_detalle);

				while ($r_detalle[$i] = db_fetch_array($query_detalle)) {
					$i++;
				} //end while($r[$i] = db_fetch_array($query_detalle))

				$i = 0;
				//print_r($r);

			?>

				<tr>
					<td class=col2list align=center>
						<b>
							<?php
							echo get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $r_referencias->IDPuntoVentaReferencia));
							?>
						</b>
					</td>
					<?php
					foreach ($r_detalle as $talla) {
						if (!empty($talla["IDTalla"]))
							echo "<td class=col2list align=center><b>" . get_field("Talla", "Descripcion", "IDTalla", $talla["IDTalla"]) . "</b></td>";
					} //end foreach($r_detalle as $talla)
					?>
				</tr>



				<tr>
					<td class="col1list" align=center>
						CANTIDAD
					</td>
					<?php
					foreach ($r_detalle as $talla) {
						if (!empty($talla["IDTalla"])) {
							$cantidad_total = $talla["Cantidad"];
							$cantidad_suma += $talla["Cantidad"];
							echo "<td class=col1list align=center><input type=text size=5 value=" . $talla["Cantidad"] . " name=" . $r_referencias->IDPuntoVentaReferencia . "[" . $talla["IDTalla"] . "]>";
						}
					}
					?>
				</tr>
			<?php

				$r_detalle = array();
				$r_codificacion = array();
			} //end while( $r_referencias = db_fetch_object( $query_referencias ) )
			?>
		</table>
		<table>
			<tr>
				<td>TOTAL</td>
				<td><?php echo $cantidad_suma; ?></td>

			</tr>
		</table>
	<?php
		} // end function verdetallepedido($id)
	?>

	<?php
	function verdetallemovimiento_texto($id)
	{

		global $TitleMod, $MOD, $Table, $Key, $TableJoin;

		$sql_referencias =  "SELECT * FROM $TableJoin WHERE $Key = '$id' GROUP BY IDPuntoVentaReferencia";

		$query_referencias = db_query($sql_referencias);

		$i = 0;
	?>
		<table width=50% cellpadding=1 cellspacing=1 class=text align=left bgcolor=#ffffff>
			<?php
			while ($r_referencias = db_fetch_object($query_referencias)) {

				$sql_detalle =  "SELECT * FROM $TableJoin WHERE $Key = '$id' AND IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia'";
				$query_detalle = db_query($sql_detalle);
				$rows_detalle = db_num_rows($query_detalle);

				while ($r_detalle[$i] = db_fetch_array($query_detalle)) {
					$i++;
				} //end while($r[$i] = db_fetch_array($query_detalle))

				$i = 0;
				//print_r($r);

			?>

				<tr>
					<td class="col2list" align=center>

						<?php
						echo get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $r_referencias->IDPuntoVentaReferencia));
						?>

					</td>
					<?php
					foreach ($r_detalle as $talla) {
						if (!empty($talla["IDTalla"]))
							echo "<td align=left>" . get_field("Talla", "Descripcion", "IDTalla", $talla["IDTalla"]) . "</td>";
					} //end foreach($r_detalle as $talla)
					?>
				</tr>



				<tr>
					<td class="col1list" align=center>
						CANTIDAD
					</td>
					<?php
					foreach ($r_detalle as $talla) {
						if (!empty($talla["IDTalla"])) {
							$total_cantidad += $talla["Cantidad"];
							echo "<td align=left>" . $talla["Cantidad"] . "</td>";
						}
					}
					?>
				</tr>
			<?php

				$r_detalle = array();
				$r_codificacion = array();
			} //end while( $r_referencias = db_fetch_object( $query_referencias ) )
			?>
		</table>
		<br>
		<table align="center">
			<tr>
				<td align="center">TOTAL:</td>
				<td><?php echo $total_cantidad; ?></td>

			</tr>
		</table>

	<?php
	} // end function verdetallepedido($id)
	?>
