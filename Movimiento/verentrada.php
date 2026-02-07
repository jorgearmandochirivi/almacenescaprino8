<body> <?php

		$TitleMod = "Entrada de Pedidos";

		$Table = "Entrada";
		$TableJoin = "";
		$Key = "IDEntrada";
		$MOD = "verentrada";
		$m = "VerMovimientos";
		$permisos = get_permiso($ID_Usuario, $m, $Table);
		if ($permisos[0] >= 2) {
			switch (nvl($action)) {
				case "list":
					if ($field == 'Talla.Descripcion') {
						$sql = "SELECT Entrada.* FROM Entrada, Talla WHERE Talla.Descripcion LIKE '%$QryString%' AND Talla.IDTalla = Entrada.IDTalla AND IDPuntoVenta = '$IDPuntoVenta' GROUP BY IDEntrada ORDER BY Fecha DESC	 ";
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
		funcion Listar
		 *******************************************************************************************/
		function list_r($sql = "")
		{
			global $TitleMod, $MOD, $Table, $Key, $listar, $IDPuntoVenta, $dirroot;
			if (empty($sql))
				$sql =  "SELECT * FROM $Table WHERE  IDPuntoVenta = '$IDPuntoVenta' ORDER BY Fecha DESC";




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

			$pages = $nav->show_num_pages('&laquo;', '&laquo; prev', '&raquo;', 'next &raquo;', '|', 'class=nav');   // show pages

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
				<form name="frm" action="<?= $PHP_SELF ?>" method="post">
					<?php
									$filedir = $dirroot . "files/";
									ob_start();
					?>
					<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline">
						<tr>
							<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=Remision&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Remisi&oacute;n</a><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=Remision&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>&nbsp;<?php if ($_GET['order_by'] == "Remision") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroFactura&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Numero Factura</a><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=NumeroFactura&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>&nbsp;<?php if ($_GET['order_by'] == "NumeroFactura") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=Referencia.Numero&tjoin=PuntoVentaReferencia&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Referencia</a><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=Referencia.Numero&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>&nbsp;<?php if ($_GET['order_by'] == "Referencia.Numero") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=IDTalla&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Talla</a><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=IDTalla&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'><?php if ($_GET['order_by'] == "IDTalla") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=Cantidad&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Cantidad</a><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=Cantidad&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'><?php if ($_GET['order_by'] == "Cantidad") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5>&nbsp;&nbsp;&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&IDPuntoVenta=" . $IDPuntoVenta . "&QryString=" . $_GET['QryString'] . "&order_by=Fecha&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'>Fecha</a><a href='<?php echo "?mod=$MOD&field=" . $_GET['field'] . "&QryString=" . $_GET['QryString'] . "&order_by=Fecha&in_order=" . $order . "&listar=" . $nav->limit . "&action=list"; ?>'><?php if ($_GET['order_by'] == "Fecha") { ?><img src="images/<?php echo $img; ?>" border=0><?php } ?></a></td>
						</tr>

						<?php
									$CantidadEntrada = 0;
									$i = 0;
									while ($r = db_fetch_object($result)) {

										$class = repetition() ? "col1list" : "col2list";



										if ($remisionanterior <> $r->Remision) {
											$remisionanterior = $r->Remision;
						?>
								<tr>
									<td align="right" style="text-align:right;" nowrap class="navpic">

									</td>
									<td align="right" style="text-align:right;" nowrap class="navpic"></td>
									<td align="right" style="text-align:right;" nowrap class="navpic"></td>
									<td nowrap class="navpic">TOTAL</td>
									<td align="right" style="text-align:right;" nowrap class="navpic">
										<?php
											echo number_format($CantidadEntrada);
											$CantidadEntrada = 0;
										?> </td>
									<td align="left" style="text-align:left;" nowrap class="navpic">

									</td>
								</tr>
							<?php

										} //end if
							?>

							<tr>
								<td align="right" style="text-align:right;" nowrap class="<?= $class ?>">
									<!-- <a href='javascript:;' onclick="window.open( 'Movimiento/popEntradas.php?Remision=<?= $r->Remision ?>','','width=500, height=500, scrollbars=1, resize=yes' )"><?php echo  $r->Remision; ?></a><br>-->
									<a href='javascript:;' onclick="window.open( 'Movimiento/popEntradasV2.php?Remision=<?= $r->Remision ?>','','width=500, height=500, scrollbars=1, resize=yes' )"><?php echo  $r->Remision; ?></a>
								</td>
								<td align="right" style="text-align:left;" nowrap class="<?= $class ?>"><?php echo  $r->NumeroFactura; ?></td>
								<td align="right" style="text-align:right;" nowrap class="<?= $class ?>"><?php echo get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", $r->IDPuntoVentaReferencia)) ?></td>
								<td nowrap class="<?= $class ?>"><?php echo get_field("Talla", "Descripcion", "IDTalla", $r->IDTalla); ?></td>
								<td align="right" style="text-align:right;" nowrap class="<?= $class ?>">
									<?php
										echo number_format($r->Cantidad);
										$CantidadEntrada += $r->Cantidad;
									?> </td>
								<td align="left" style="text-align:left;" nowrap class="<?= $class ?>">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo formatofecha(substr($r->Fecha, 0, 10)) . " a las " . substr($r->Fecha, 10) ?>
								</td>
							</tr>




						<?php } // END for

									if ($CantidadEntrada > 0) {
						?>
							<tr>
								<td align="right" style="text-align:right;" nowrap class="navpic">

								</td>
								<td align="right" style="text-align:right;" nowrap class="navpic"></td>
								<td align="right" style="text-align:right;" nowrap class="navpic"></td>
								<td nowrap class="navpic">TOTAL</td>
								<td align="right" style="text-align:right;" nowrap class="navpic">
									<?php
										echo number_format($CantidadEntrada);
										$CantidadEntrada = 0;
									?> </td>
								<td align="left" style="text-align:left;" nowrap class="navpic">

								</td>
							</tr>
						<?php

									} //end if

						?>

						<tr>
							<td class=col1 bgcolor=#DBEAF5 colspan=6 nowrap><?php
																			print $pages;
																			?><input type="hidden" name="action" value="insert"></td>





			</td>
		</tr>
	</table>
	<?php
									$page = ob_get_contents();
									$fecha = date("Y-m-d H:i:s");
									$name = "Entradas$fecha.xls";
									$file = $filedir . $name;

									$fw = fopen($file, "w");
									fputs($fw, $page, strlen($page));
									fclose($fw);
									ob_end_clean();

									//header_export($file);
									echo $page;
	?>
	</form>
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
<form name="frm" action="./" method="get" onsubmit="return valbuscar(document.frm)">
	<tr>
		<td class="rowform" align="center" colspan=8>
			<select name="field" id="Buscar por" class="popup">
				<option value="">Buscar Por</option>
				<option value="Referencia.Numero">Referencia</option>
				<option value="Talla.Descripcion">Talla</option>
				<option value="Remision">Remisionnnn</option>
				<option value="NumeroFactura">Numero de Factura</option>
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

				<option value="IDPuntoVentaReferencia">Referencia</option>
			</select>
			de forma
			<select name="in_order" class="popup">
				<option value="ASC">Ascendente</option>
				<option value="DESC">Descendente</option>
			</select>
			Listar
			<select name="listar" class="popup">
				<option value="30">30</option>
				<option value="40">40</option>
			</select>
			<br>
			<input type="hidden" name="mod" value="<?= $MOD ?>">
			<input type="hidden" name="rangofield" value="Fecha">
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