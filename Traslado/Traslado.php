<head>
	<title>..::Caprino</title>
	<link rel="stylesheet" href="../styles.css" type="text/css">
</head>

<body>

	<?php	

	
	$TitleMod = "Traslados2";
	$Table = "Traslado";
	$TableJoin = "DetalleTraslado";
	$Key = "IDTraslado";
	$MOD = "Traslado2";
	$m = "Traslado";
	$permisos = get_permiso($ID_Usuario, $m, $Table);
	if ($permisos[0] >= 2) {
		switch (nvl($action)) {
			case "insert":
				
				//print_r($HTTP_POST_VARS);
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");

				

				$frm = vars_LOG($HTTP_POST_VARS);



				/******Estado Traslado Enviado  1*********/
				$frm['IDEstadoTraslado'] = 1;

				//Valido que seleccione el empleado
				if ((int)$frm["IDEmpleado"] <= 0):
					echo "<script>alert('ATENCION: Debe seleccionar el empleado');</script>";
					echo "<script>location.href='?mod=Traslado';</script>";
					//Imprimir la factura
					exit;
				endif;

				//Valido que por lo menos se haya seleccionado un producto
				if (empty($frm["Cantidad1"]) || empty($frm["IDCodificacion1"])):
					echo "<script>alert('ATENCION: Debe seleccionar por lo menos un item');</script>";
					echo "<script>location.href='?mod=Traslado';</script>";
					//Imprimir la factura
					exit;
				endif;


				$frm['IDTraslado'] = insert($frm);





				for ($i = 1; $i <= $frm['ITEM']; $i++) {



					$cant = "Cantidad" . $i;
					$cod = "IDCodificacion" . $i;
					$num_tarj = "NumeroTarjeta" . $i;

					if (!empty($frm[$cant]) && (int)$frm[$cod] > 0) {


						$iddetalle = get_maxID("DetalleTraslado", "IDDetalleTraslado");
						$Codificacion = $frm[$cod];
						$Cantidad = $frm[$cant];
						$NumeroTarjeta = $frm[$num_tarj];

						$sql_insert = "INSERT INTO DetalleTraslado (IDDetalleTraslado, IDTraslado,IDPuntoVentaOrigen, IDCodificacionEspecifica, Cantidad, NumeroTarjeta, UsuarioTrCr, FechaTrCr ) ";
						$sql_insert .= "VALUES ('$iddetalle','{$frm['IDTraslado']}','{$frm['IDPuntoVentaOrigen']}','$Codificacion','$Cantidad','$NumeroTarjeta','{$frm['UsuarioTrCr']}','{$frm['FechaTrCr']}')";
						db_query($sql_insert);
					}
				}

				db_query("COMMIT");



				echo "<script>alert('Traslado Realizado. Esperando repuesta del punto de venta de destino...');</script>";

				//Imprimir la factura
				echo "<script>location.href='?mod=vertraslado&action=edit&id={$frm['IDTraslado']}&idpuntoorigen={$frm['IDPuntoVentaOrigen']}';</script>";

				//print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");
				break;

			case "edit":
				print_form($id, "update", "Actualizar $TitleMod", "Realizar Movimiento");
				break;
			case "list":
				$sql = make_qry_string($HTTP_GET_VARS);
				list_r($sql);
				break;
			default:
				print_form($id, "insert", "Realizar $TitleMod", "Realizar Traslado");
				break;
		} // End switch

	} //end if(permisos[0] > 2)
	else
		echo Mensaje_Info("No tiene Permisos Suficientes", "col1");





	/*******************************************************************************************
		funtcion Print_form
	 *******************************************************************************************/

	function print_form($id, $newmode, $title, $submit_caption)
	{
		global $TitleMod, $Table, $MOD, $Key, $ID_Usuario, $IVA, $IDPuntoVenta;

		$qid = db_query(" SELECT * FROM Cliente WHERE IDCliente = '$id' ");

		$r = db_fetch_object($qid);
	?>

		<script language="JavaScript">
			<!--
			function valida_primer() {


				var cod1 = document.getElementById('IDCodificacion1').value;
				var cant1 = document.getElementById('Cantidad1').value;
				if (cod1 == "" || cant1 == "") {
					alert("El primer item no puede estar vacio");
					return false;
				}

				var empleado = document.getElementById('Empleado').value;
				if (empleado == "") {
					alert("Debe Seleccionar el empleado");
					return false;
				}

				var quienpide = document.getElementById('QuienPide').value;
				if (quienpide.trim() == "") {
					alert("Debe agregar quien lo pide");
					return false;
				}

				var observaciones = document.getElementById('Observaciones').value;
				if (observaciones.trim() == "") {
					alert("Debe agregar observaciones");
					return false;
				}





				return true;
			}

			function habilitar_fila() {

				var habilitadas = document.getElementById('ContadorFilas').value;
				var inicio = habilitadas;
				var final = parseInt(habilitadas) + 19;
				for (var i = inicio; i <= final; i++) {
					var result_style = document.getElementById(i).style;
					result_style.display = 'table-row';
				}
				document.getElementById('ContadorFilas').value = parseInt(final) + 1;

				return false;
			}

			function calculagrantotal() {
				var total = 0;
				for (var i = 1; i <= 200; i++) {
					var campoCantidad = document.getElementById("Cantidad" + i);
					if (!campoCantidad) {
						continue;
					}
					var ctexto = campoCantidad.value;
					if (ctexto != "" && !isNaN(ctexto)) {
						total = parseInt(total) + parseInt(ctexto);
					}

				}
				document.getElementById('TotalCantidad').value = total;
			}

			function calculatotal(value, cont) {
				calculagrantotal();
			}


			function addCell(label) {
				var cell = document.createElement("TD");
				if (label)
					cell.innerHTML = label;
				return cell;
			}

			function formatCurrency(InpunObject) {

				num = InpunObject.value;
				num = num.toString().replace(/\$|\,/g, '');
				if (isNaN(num))
					num = "0";
				sign = (num == (num = Math.abs(num)));
				num = Math.floor(num * 100 + 0.50000000001);
				cents = num % 100;
				num = Math.floor(num / 100).toString();
				if (cents < 10)
					cents = "0" + cents;
				for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
					num = num.substring(0, num.length - (4 * i + 3)) + ',' +

					num.substring(num.length - (4 * i + 3));

				InpunObject.value = (((sign) ? '' : '-') + '$' + num + '.' + cents);

			}

			function addRow() {
				cont++;
				var tbody = document.getElementById("table1").getElementsByTagName("tbody")[0];
				var row = document.createElement("TR");

				var cell1 = addCell("<b>" + cont + "</b>");
				var cell2 = addCell("");
				var cell3 = addCell("");
				var cell4 = addCell("");
				var cell5 = addCell("");
				var cell6 = addCell("");
				var cell7 = addCell("");
				var cell8 = addCell("");
				var cell9 = addCell("");
				var cell10 = addCell("");
				var cell11 = addCell("");
				var cell12 = addCell("");

				var inp1 = addInput(5, "text", "Numero" + cont, "", 0, 0, cont);
				cell2.appendChild(inp1);

				var inp2 = addInput(5, "text", "Talla" + cont, "", 0, 0, cont);
				cell3.appendChild(inp2);

				var inp3 = addInput(15, "text", "Nombre" + cont, "", 0, 0, cont);
				cell4.appendChild(inp3);

				var inp4 = addInput(5, "hidden", "IDCodificacion" + cont, "", 0, 0, cont);
				cell5.appendChild(inp4);

				var inp5 = addInput(5, "text", "Cantidad" + cont, "", 0, 5, cont);
				cell6.appendChild(inp5);

				var inp6 = addInput(15, "text", "ValorU" + cont, "", 0, 0, cont);
				cell7.appendChild(inp6);

				var inp7 = addInput(15, "text", "Total" + cont, "", 0, 0, cont);
				cell8.appendChild(inp7);

				var inp8 = addInput(5, "button", "Agregar" + cont, "Referencia", 4, 0, cont);
				cell9.appendChild(inp8);

				var inp9 = addInput(5, "hidden", "Maximo" + cont, "", 0, 0, cont);
				cell10.appendChild(inp9);

				var inp10 = addInput(5, "hidden", "Precio" + cont, "", 0, 0, cont);
				cell11.appendChild(inp10);
				var inp11 = addInput(5, "hidden", "Descuento" + cont, "", 0, 0, cont);
				cell12.appendChild(inp11);

				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(cell5);
				row.appendChild(cell6);
				row.appendChild(cell7);
				row.appendChild(cell8);
				row.appendChild(cell9);
				row.appendChild(cell10);
				row.appendChild(cell11);
				row.appendChild(cell12);

				tbody.appendChild(row);
			}

			function delRow() {
				var tbl = document.getElementById('table1');
				var lastRow = tbl.rows.length;
				if (lastRow > 2) {
					tbl.deleteRow(lastRow - 1);
					cont--;
				}
			}

			function getNum(strNum)

			{

				num = strNum.toString().replace(/\$|\,/g, '');
				if (isNaN(num))
					num = "0";
				return num;

			}

			function validar(form) {
				var ret = true;
				var i = 0;
				while (ret == true) {
					if (form.elements[i].id == "req") {
						if (form.elements[i].value == "") {
							alert("Faltan fotos por escoger.");
							form.elements[i].focus();
							ret = false;
							i = form.elements.length;
						}
					}
					i++;
				}
				return ret;
			}




			function selreferencia(REFERENCIA, NOMBRE, TALLA, CODIFICACION, CONT, MAXIMO, VALORU) {
				//alert("Si");
				document.frm.elements["Numero" + CONT].value = REFERENCIA;
				document.frm.elements["Nombre" + CONT].value = NOMBRE;
				document.frm.elements["Talla" + CONT].value = TALLA;
				document.frm.elements["IDCodificacion" + CONT].value = CODIFICACION;

				/*******Si la factura tiene descuento especial se hace la operacion**************/
				var descuento = 0;
				var PRECIO = 0;
				var iva = <?= $IVA ?>;


				document.frm.elements["Maximo" + CONT].value = MAXIMO;
				document.frm.elements["ITEM"].value = CONT;
				calculagrantotal();
			}


			function habilita_tarjeta(value, cont) {
				var referencia;
				referencia = document.frm.elements["Numero" + cont].value;
				if (referencia == "TARJETA") {
					alert("Digite los numeros de tarjeta por favor");
					document.getElementById('div_tarjeta').style.display = 'block';
				}

			}


			function compruebamaximo(value, cont) {

				var maximo = document.frm.elements["Maximo" + cont].value;
				//alert(value);
				//alert(maximo);
				if (eval(value) > eval(maximo)) {
					alert("El maximo es " + maximo);
					return false;
				} else {

					return true;
				}
			}
			-->
		</script>
		<script>
			var Check = new Array('IDPuntoVentaDestino', 'Numero1', 'Talla1', 'Nombre1', 'Cantidad1', 'IDEmpleado');
		</script>
		<br>
		<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="580">

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
		<FORM name="frm" method="post" enctype="multipart/form-data" action="<?= $PHP_SELF ?>" onSubmit="disable( this );return valida_primer()">
			<table class="forumline" width="580" cellspacing="1" border="0" align="center">
				<tr>
					<td width="100%">
						<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff">

							<tr>
								<td colspan="2" width="100%">

									<div align="center">
										<table width="100%" border=0 align="center">
											<tr>
												<td colspan="4">
													<table class=rowtable width="100%">
														<tr>
															<td class=col1>Fecha </td>
															<td class=col2 colspan="3"><input type="text" class="tbox" name="Fecha" size="19" value='<?= fecha() . " " . hora() ?>'>
																<script language="JavaScript1.2">
																	<!--
																	if (!document.layers)
																		document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
																	//
																	-->
																</script>
																<input type="hidden" value="<?= $IDPuntoVenta ?>" name="IDPuntoVentaOrigen">


																Empleado: <?php echo formpopup("Empleado WHERE IDPuntoVenta = '$IDPuntoVenta' and Publicar = 'S' ", "Nombre", "Apellidos", "IDEmpleado", $frm['IDEmpleado'] ?? '', "input\" id=\"Empleado"); ?>

															</td>
														</tr>
														<tr>
															<td class=col1>Destino</td>
															<td class=col2 colspan="3">
																<select name="IDPuntoVentaDestino" class="InputSelect" required>
																	<option value=''>Seleccione</option>
																	<?php
																	$sql_puntoventa = "SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre";
																	$query_puntoventa = db_query($sql_puntoventa);
																	while ($r_puntoventa = db_fetch_object($query_puntoventa)) {
																		echo "<option value='" . $r_puntoventa->IDPuntoVenta . "'>" . $r_puntoventa->Nombre . "</option>";
																	} //end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
																	?>
																</select>
															</td>
														</tr>
														<tr>
															<td class=col1>Quien lo pide?</td>
															<td class=col2 colspan="3">
																<input type="text" name="QuienPide" id="QuienPide" required>
															</td>
														</tr>

														<tr>
															<td class=col1>Motivo:</td>
															<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" id="Observaciones" rows="4" cols="64" required><?= $r->Observaciones ?></textarea></td>
														</tr>
														<tr>
															<td class=col1><br></td>
															<td class=col1></td>
															<td class=col1></td>
															<td class=col1></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td class=navpic>Detalle Traslado</td>
												<td class=navpic colspan="3">
													<div align="right">
													</div>
												</td>
											</tr>
											<tr bgcolor=#e7ebef>
												<td colspan="4">
													<table class="texto" border="0" cellspacing="1" cellpadding="0" width="100%" id=table1>
														<tr bgcolor="#dfe3e7">
															<td align="center"><b></b></td>
															<td align="center"><b>Referencia</b></td>
															<td align="center"><b>Talla</b></td>
															<td align="center"><b>Nombre</b></td>
															<td align="center"><b></b></td>
															<td align="center"><b>Cantidad</b></td>
															<td align="center"><b>Agregar</b></td>
															<td align="center"><b></b></td>
															<td align="center"><b></b></td>
														</tr>
														<?php
														for ($i = 1; $i <= 100; $i++) {
															if ($i == 20 || $i == 40 || $i == 60 || $i == 80  || $i == 100 || $i == 120 || $i == 140 || $i == 160 || $i == 180):
																$mostrar = 1;
															endif;
															if ($i > 20):
																$nombrediv = "grupoitem" . $i;
																$mostrar_tr = 'style="display:none;"';
															endif;
														?>

															<tr id="<?php echo $i; ?>" <?php echo $mostrar_tr; ?>>
																<td align="center"><b><?= $i ?></b></td>

																<td align="center"><input type=text id=Numero<?= $i ?> name=Numero<?= $i ?> rel="<?= $i ?>" value="<?php echo $frm[$numero] ?>" class="tbox tboxReferencia" size=8></td>


																<td align="center"><input type=text readonly id=Talla<?= $i ?> name=Talla<?= $i ?> value="<?php echo $frm[$talla] ?>" class=tbox size=5></td>


																<td align="center"><input type=text readonly id=Nombre<?= $i ?> name=Nombre<?= $i ?> value="<?php echo $frm[$nombre] ?>" class=tbox size=15></td>


																<td align="center"><input type=hidden name=IDCodificacion<?= $i ?> id=IDCodificacion<?= $i ?> value="<?php echo $frm[$idcodificacion] ?>"></td>


																<td align="center">
																	<input type=number id="Cantidad<?= $i ?>" name=Cantidad<?= $i ?> value="<?php echo $frm[$cantidad] ?>" class="tbox" size=3 min="1" max="100000" onInput="calculagrantotal();" onBlur="calculagrantotal(); if(!compruebamaximo(this.value,<?= $i ?>)){ this.value = ''; calculagrantotal(); } else{ habilita_tarjeta(this.value,<?= $i ?>);calculatotal(this.value,<?= $i ?>);}">
																	<div id="div_tarjeta" style="display:none">
																		<textarea name="NumeroTarjeta<?= $i ?>" id="NumeroTarjeta<?= $i ?>"></textarea>
																	</div>
																</td>
																<td align="center"><input type=button name=Agregar<?= $i ?> class=submit value=Referencia onClick="window.open('Referencia/popReferencias.php?IDPuntoVenta=<?= $IDPuntoVenta ?>&cont=<?= $i ?>','','width=600,height=400');"></td>
																<td align="center"><input type=hidden name=Maximo<?= $i ?>></td>
																<td align="center"><input type=hidden name=Precio<?= $i ?>></td>
															</tr>

														<?php
														}
														?>
														<tr>
															<td colspan="4" align="right"></td>
															<td align="right">TOTAL</td>
															<td align="center">
																<input type="text" readonly id="TotalCantidad" value="0" size=5>

															</td>
															<td align="right">

																<br>
																<input type=button name="Habilita20" class=submit value="Habilitar 20 filas mas" onClick="habilitar_fila()">
																<input type="hidden" id="ContadorFilas" name="ContadorFilas" value="21">
															</td>

														</tr>
														<tbody bgcolor=#e7ebef></tbody>
													</table>
												</td>
											</tr>
										</table>
										<input type=hidden name=ITEM value="<?php echo ($i - 1); ?>">
										<input type="hidden" name="action" value="<?= $newmode ?>">
										<input type="submit" class="button" name="submit" value="<?= $submit_caption ?>">
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
	?>
</BODY>

</HTML>
