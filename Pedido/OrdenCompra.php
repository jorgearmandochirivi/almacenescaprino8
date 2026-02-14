<body> <?php

		$TitleMod = "Orden de Compra";

		$Table = "OrdenCompra";
		$TableJoin = "DetalleOrdenCompra";
		$Key = "IDOrdenCompra";
		$MOD = "Pedido";
		$m = "Pedido";
		$permisos = get_permiso($ID_Usuario, $m, $Table);
		if ($permisos[0] >= 2) {
			switch (nvl($action)) {
				case "add":
					print_form("", "insert", "Nuevo Registro $TitleMod", "Agregar Registro");
					break;

				case "insert":
					print_form($id, "update", "Actualizar $TitleMod", "Realizar Cambios");
					break;
				case "edit":
					print_form($id, "update", "Actualizar $TitleMod", "Realizar Cambios");
					break;
				case "update":
					print_form($id, "update", "Actualizar $TitleMod", "Realizar Cambios");
					break;
				case "del":
					print_form($id, "delete", "Eliminar $TitleMod", "Remover Registro");
					break;
				case "delete":
					print_form($id, "update", "Actualizar $TitleMod", "Realizar Cambios");
					break;
				case "list":
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

			global $TitleMod, $MOD, $Table, $Key;

			$sql =  "SELECT * FROM $Table WHERE $Key = '$id'";

			$query_pedido = db_query($sql);
			$r_pedido = db_fetch_object($query_pedido);

		?><br>
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
		<form name="frm" action="<?= $PHP_SELF ?>" method="post" onsubmit="return EvaluaReg(this,Check);">
			<table class="forumline" width="550" cellspacing="1" border="0" align="center">
				<tr>
					<td>
						<table width=100% border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff">

							<tr>
								<td class="row1" nowrap>
									<table width=100% cellspacing="1" cellpadding="1" bgcolor=#ffffff>
										<tr>
											<td class=row1>
												Punto de Venta
											</td>
											<td class=row1>
												<input type="text" class="input" name="PuntoVenta" readonly size="24" value="<?= get_field("PuntoVenta", "Nombre", "IDPuntoVenta", $r_pedido->IDPuntoVenta) ?>">
												<input type="hidden" name="IDPuntoVenta" value="<?= $r_pedido->IDPuntoVenta ?>">
											</td>
											<td class=row1>
												<div align="left">

													Numero.</div>
											</td>
											<td class=row1>
												<input type="text" class="input" name="NumeroOrden" readonly size="24" value="<?= $r_pedido->NumeroOrden ?>">
											</td>
										</tr>
										<tr>
											<td class=row1>Fecha</td>
											<td class=row1>
												<input type="text" class="input" name="FechaOrden" size="24" value="<?= $r_pedido->FechaOrden ?>" readonly>
											</td>
											<td class=row1>Estado </td>
											<td class=row1>
												<?php echo formpopup("EstadoPedido", "Descripcion", "IDEstadoPedido", "IDEstadoPedido", $r_pedido->IDEstadoPedido, "input\" id=\"IDEstadoPedido"); ?>
											</td>
										</tr>
										<tr>
											<td class=row1>Observaciones</td>
											<td colspan="3" class=row1><textarea name="Observaciones" rows="4" cols="64"><?= $r_pedido->Observaciones ?></textarea></td>
										</tr>
										<tr>
											<td class=row1 colspan="4"></td>
										</tr>
										<tr>
											<td class=navpic colspan="4"><b>DETALLE DE LA ORDEN DE COMPRA</b></td>
										</tr>
										<tr>
											<td class=row2 colspan="4">
												<?php verdetallepedido($id); ?>
											</td>
										</tr>
										<tr>
											<td class=row2 colspan="4" align="center">
												<input type="hidden" name="action" value="<?= $newmode ?>">
												<input type="hidden" name="ID" value="<?= $id ?>">
												<input type="hidden" name="IDOrdenCompra" value="<?= $id ?>"> <input type="hidden" name="IDSugerido" value="<?= $r_pedido->IDSugerido ?>">
											</td>
										</tr>
									</table>
								</td>
							</tr>

						</table>
					</td>
				</tr>
			</table>
		</form>
		<?php
		}// End function print_form()

		/*******************************************************************************************
		funcion Listar
		 *******************************************************************************************/
		function list_r($sql = "")
		{
			global $TitleMod, $MOD, $Table, $Key, $listar, $IDPuntoVenta;
			if (empty($sql))
				$sql =  "SELECT * FROM $Table WHERE IDPuntoVenta = '$IDPuntoVenta' ORDER BY FechaOrden DESC";

			$nav = new buildNav;
			$nav->offset = 'offset';
			$nav->number_type = 'number';
			(!empty($listar)) ? $nav->limit = $listar : $nav->limit = 10;
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
		?>

			<br>
			<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">

				<tr>
					<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
					</td>
					<td class="tbtbot"><b></b>
						<span class="gen">
							<?= $title . " - " . $info ?>
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
						<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline">
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
											<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroOrden&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">NumeroOrden&nbsp;<?php if ($_GET['order_by'] == "NumeroOrden") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
											<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=FechaOrden&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">FechaOrden&nbsp;<?php if ($_GET['order_by'] == "FechaOrden") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
											<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=FechaOrden&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>">Items<?php if ($_GET['order_by'] == "FechaOrden") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a> </td>
										</tr>

										<?php while ($r = db_fetch_object($result)) {
											$class = repetition() ? "col1list" : "col2list";
										?>

											<tr>
												<td align=center valign=middle nowrap width=50 class="<?= $class ?>">
													&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id=";
																	echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
												</td>
												<td nowrap class="<?= $class ?>"><?php echo $r->NumeroOrden ?></td>
												<td nowrap class="<?= $class ?>"><?php echo $r->FechaOrden ?></td>
												<td nowrap class="<?= $class ?>"><?php echo get_field("DetalleOrdenCompra", "count(IDDetalleOrdenCompra)", "IDOrdenCompra", $r->IDOrdenCompra); ?></td>
											</tr>
										<?php } // END for
										?>
										<tr>
											<td class=texto bgcolor=#DBEAF5 colspan=4 nowrap>
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
			global $dblink, $total_records, $row, $numtoshow, $MOD;
		?>
		<form name="frm" action="./" method="get">
			<tr>
				<td class="navpic" align="center">
					<select name="field" id="Buscar por" class="popup">
						<option value="">Buscar Por</option>
						<option value="NumeroOrden">NumeroOrden</option>
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
					<input type="hidden" name="tjoin" value="">
					<input type="hidden" name="IDPuntoVenta" value="<?= $IDPuntoVenta ?>">
					<input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>
		</form>
	<?php
		}//End function filtrar


		/*******************************************************************************************
	verdetallepedido: Muestra el detalle de el pedido
	Parametros:
			$id : id del detalle del pedido a mostrar
	Retorna:	
			Void
		 *******************************************************************************************/
		function verdetallepedido($id)
		{

			global $TitleMod, $MOD, $Table, $Key, $TableJoin;

			$sql_referencias =  "SELECT * FROM $TableJoin WHERE $Key = '$id' GROUP BY IDPuntoVentaReferencia";

			$query_referencias = db_query($sql_referencias);

			$i = 0;
	?>
		<table width=80% cellpadding=1 cellspacing=0 class=text align=center>
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

				//REALIZAR EL QUERY PARA VER LA CODIFICACION ESPECIFICA DE LA REFERENCIA

				$sql_codificacion = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia' GROUP BY IDCodificacionEspecifica";
				$query_codificacion = db_query($sql_codificacion);

				while ($r_codificacion[$i] = db_fetch_array($query_codificacion)) {
					$i++;
				} //end while($r_codificacion[$i] = db_fetch_array($query_codificacion))

				$i = 0;

			?>

				<tr>
					<td class=navpic align=center>
						<?php
						echo get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $r_referencias->IDPuntoVentaReferencia));
						?>
					</td>
					<?php
						foreach ($r_detalle as $talla) {
							if (!empty($talla["IDTalla"]))
								echo "<td class=navpic align=center>" . get_field("Talla", "Descripcion", "IDTalla", $talla["IDTalla"]) . "</td>";
					} //end foreach($r_detalle as $talla)
					?>
				</tr>

				<tr>
					<td class=col1 align=center>
						Existencias
					</td>
					<?php
					foreach ($r_detalle as $talladetalle) {
							foreach ($r_codificacion as $talla) {
								if (!empty($talla["IDTalla"]) && ($talla["IDTalla"] == $talladetalle["IDTalla"]))
									echo "<td class=row1 align=center>" . $talla["Existencias"] . "</td>";
						} //end foreach($r_detalle as $talla)
					} //end foreach($r_detalle as $talla)
					?>
				</tr>

				<tr>
					<td class=col1 align=center>
						M&aacute;ximo
					</td>
					<?php
					foreach ($r_detalle as $talladetalle) {
							foreach ($r_codificacion as $talla) {
								if (!empty($talla["IDTalla"]) && ($talla["IDTalla"] == $talladetalle["IDTalla"]))
									echo "<td class=row1 align=center>" . $talla["Maximo"] . "</td>";
						} //end foreach($r_detalle as $talla)
					} //end foreach($r_detalle as $talla)
					?>
				</tr>

				<tr>
					<td class=col1 align=center>
						Minimo
					</td>
					<?php
					foreach ($r_detalle as $talladetalle) {
							foreach ($r_codificacion as $talla) {
								if (!empty($talla["IDTalla"]) && ($talla["IDTalla"] == $talladetalle["IDTalla"]))
									echo "<td class=row1 align=center>" . $talla["Minimo"] . "</td>";
						} //end foreach($r_detalle as $talla)
					} //end foreach($r_detalle as $talla)
					?>
				</tr>



				<tr>
					<td class="col1" align=center>
						PEDIDO
					</td>
					<?php
						foreach ($r_detalle as $talla) {
							if (!empty($talla["IDTalla"])) {
								echo "<td class=row1 align=center><input readonly type=text size=5 value=" . $talla["Cantidad"] . " name=" . get_field("Referencia", "Numero", "IDReferencia", $r_referencias->IDReferencia) . "[".$talla["IDTalla"]."]>";
							//SE IMPRIME UN HIDDEN CON EL ID DE LAS TALLA
							//echo "<input readonly type=hidden value=".$talla[IDTalla]." name=Talla".get_field("Referencia","Numero","IDReferencia",$r_referencias->IDReferencia)."[$talla[IDTalla]]></td>";
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
	<?php
		} // end function verdetallesugerido($id)
	?>
