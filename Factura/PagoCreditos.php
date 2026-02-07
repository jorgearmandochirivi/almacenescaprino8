
<body> <?php


$TitleMod ="Factura";

$Table = "Factura";
$TableJoin = "Factura";
$Key = "IDFactura";
$MOD = "PagoCreditos";
$m = "PagoCreditos";

$permisos = get_permiso($ID_Usuario,$m,$Table);
//if($permisos[0] >= 2)
//{
		switch (nvl($action)) {
			case "update":
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");

				//Actualizar Cuotas
				foreach( $IDCuota as $key => $value )
				{
					$fechapago = "FechaPago".$key;
					$mediopago="MedioPago".$key;
					if( !empty( $_POST[$fechapago] ) )
					{

						//inserto los punto spor la cuota
						$sql_puntos_cuota = " SELECT * FROM CreditoCuota WHERE IDFactura = '$_POST[IDFactura]' AND IDPuntoVenta = '$_POST[IDPuntoVenta]' limit 1 ";
						$qry_puntos_cuota = db_query( $sql_puntos_cuota );
						$row_puntos_cuota = db_fetch_array($qry_puntos_cuota);


						$sql_regla=db_query("Select * from ReglaPunto Where Activo = 'S' and FechaInicio <= CURDATE() and FechaFin >= CURDATE() limit 1");
						while($r_regla = db_fetch_array( $sql_regla )){
								$nombre_regla_utilizada=$r_regla[Nombre];
								$descrip_regla_utilizada=$r_regla[Descripcion];

								//cada X Valor pesos vale X puntos
								$cantidas_puntos = $r_regla[Puntos];
								$por_cada_valor=$r_regla[Valor];

								$puntos_esta_factura = (int)$row_puntos_cuota[ValorTotal] * (int)$cantidas_puntos / $por_cada_valor;

								//los puntos se vencen en X año a partir del ultimo dia del mes
								$vigencia_puntos=get_field("ParametroFidelizacion","Valor","IDParametroFidelizacion",1);
								if ((int)$vigencia_puntos==0)
									$vigencia_puntos="4";

								$array_fecha_factura = explode("-", substr( $row_puntos_cuota["FechaTrCr"], 0, 10 ) );

								$mes = $array_fecha_factura[1];
								$year = date("Y") + $vigencia_puntos;

								$m = mktime( 0, 0, 0, $mes, 1, $year );
								$dia = date("t",$m);

								$fechavencimiento = $year . "-" . $mes . "-" . $dia;

								$sql_puntos = " INSERT INTO PuntosClienteFidelizacion (IDCliente, IDPuntoVenta, IDFactura,IDReglaPunto,NombreRegla, DescripcionRegla, Puntos, FechaVencimiento,ObservacionesRegla, FechaTrCr)
													VALUES ('" . $_POST[IDCliente] . "','" . $_POST[IDPuntoVenta] . "','" . $_POST[IDFactura] . "', '".$r_regla[IDReglaPunto]."',  '".$nombre_regla_utilizada."','".$descrip_regla_utilizada."','" . (int)$puntos_esta_factura . "','" . $fechavencimiento . "', 'Cuota credito',  NOW() ) ";

								$qry_puntos = db_query( $sql_puntos );

						}



						/*
						$sql_puntos_anterior="Select * from PuntosClienteFidelizacion Where IDCliente='".$_POST[IDCliente]."' and IDPuntoVenta = '".$_POST[IDPuntoVenta]."' and IDFactura = '".$_POST[IDFactura]."' limit 1";
						$qry_puntos_anterior = db_query( $sql_puntos_anterior );
						while($row_punto=db_fetch_array($qry_puntos_anterior)){
							$sql_puntos = " INSERT INTO PuntosClienteFidelizacion (IDCliente, IDPuntoVenta, IDFactura,IDReglaPunto,NombreRegla, DescripcionRegla, Puntos, FechaVencimiento,ObservacionesRegla, FechaTrCr) VALUES ('" . $_POST[IDCliente] . "','" . $_POST[IDPuntoVenta] . "','" . $_POST[IDFactura] . "', '".$row_punto[IDReglaPunto]."',  '".$row_punto[NombreRegla]."','".$row_punto[DescripcionRegla]."','" . (int)$row_punto[Puntos] . "','" . $row_punto[FechaVencimiento] . "', '".$row_punto[ObservacionesRegla]." Cuota"."',  NOW() ) ";
							$qry_puntos = db_query( $sql_puntos );
						}
						*/

						$frm["FechaFactura"]=$_POST[FechaFactura];
						$frm["IDCliente"]=$_POST[IDCliente];
						$frm["id"]=$_POST[IDFactura]; // id factura
						$frm[idpunto]=$_POST[IDPuntoVenta];
						$frm[UsuarioTrCr]=$ID_Usuario;

						genera_bonos($_POST[IDCliente],$frm);



						$sql_max=db_query("Select max(Consecutivo) SiguienteConsecutivo From CreditoCuota Where 1");
						$row_maximo  = db_fetch_array( $sql_max );

						if ((int)$row_maximo["SiguienteConsecutivo"]==0){
							$conseuctivo = 1;
						}
						else{
							$conseuctivo = (int)$row_maximo["SiguienteConsecutivo"] + 1;
						}

						$sql_update = " UPDATE CreditoCuota SET FechaPago = '$_POST[$fechapago]', Consecutivo = '".$conseuctivo."', IDPuntoVentaPago = '".$_POST[IDPuntoVentaPago]."', MedioPago = '".$_POST[$mediopago]."', UsuarioTrEd = '" . $ID_Usuario . ",Punto: " . $IDPuntoVenta . "' , FechaTrEd = CURDATE() WHERE IDFactura = '$_POST[IDFactura]' AND IDPuntoVenta = '$_POST[IDPuntoVenta]' AND IDCuota = '$key' ";
						$qry_update = db_query( $sql_update );
						echo "<script>window.open( 'Factura/FImpresionCredito.php?id=".$_POST[IDFactura]."&idpunto=".$_POST[IDPuntoVenta]."&idcuota=".$key."','','width=426, height=350' );</script>";
					}//end if
				}//end for


				if($_POST[ComentarioCredito]!=$_POST[ComentarioCreditoAnt]){
					$actualiza_fecha_comen=", FechaUtimoComentario= NOW() ";
				}


				$update_factura="UPDATE Factura Set ComentarioCredito = '".$_POST[ComentarioCredito]."',FechaUltimaGestion = '".$_POST[FechaUltimaGestion]."',FechaCartaNotificacion = '".$_POST[FechaCartaNotificacion]."',NumeroPagare = '".$_POST[NumeroPagare]."',FechaReporteCredito = '".$_POST[FechaReporteCredito]."' ".$actualiza_fecha_comen." Where IDFactura = '".$_POST[IDFactura]."' ";
				db_query( $update_factura );

				$sql_cuotas = " SELECT * FROM CreditoCuota WHERE IDFactura = '$_POST[IDFactura]' AND IDPuntoVenta = '$_POST[IDPuntoVenta]' AND FechaPago = '0000-00-00 00:00:00'  ";
				$qry_cuotas = db_query( $sql_cuotas );
				if( db_num_rows( $qry_cuotas ) == 0  )
				{
					db_query( "UPDATE Credito SET Cancelado = 'S' " );
				}//end if

				//db_query( "tales" );
				db_query("COMMIT");



				echo "<script>location.href='?mod=".$MOD."&action=edit&id=".$_POST[IDFactura]."&idp=".$_POST[IDPuntoVenta]."'</script>";

			break;
			case "edit":
				print_form($id,$idp,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "list" :
				if( !empty($_GET["NumeroFactura"]) )
				{
					$sql = "SELECT F.* FROM Factura F, Credito C WHERE F.NumeroFactura = '" . $_GET["NumeroFactura"] . "' AND F.IDFactura = C.IDFactura AND F.IDPuntoVenta = C.IDPuntoVenta Order by IDFactura Asc";
				}

				if( !empty($_GET["Cedula"]) )
				{
					$sql = "SELECT F.* FROM Factura F, Credito C, Cliente Cli
							WHERE F.IDCliente = Cli.IDCliente
							AND  Cli.Cedula = '" . $_GET["Cedula"] . "'

							AND F.IDFactura = C.IDFactura AND F.IDPuntoVenta = C.IDPuntoVenta ORDER BY F.FechaFactura ASC ";
				}

				list_r( $sql );
			break;
			default :
					list_r();
			break;

		} // End switch

//}//end if(permisos[0] > 2)
//else
//	echo Mensaje_Info("No tiene Permisos Suficientes","col2");



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id,$idp,$newmode,$title,$submit_caption){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta,$crypt;

	$qid = db_query(" SELECT * FROM Factura WHERE IDFactura = '$id' AND IDPuntoVenta = '$idp' ");

	$r = db_fetch_object($qid);
?>


<br>
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?=$title?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>
<FORM name="frm" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>
<table class="forumline" width="550" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff" >

				<tr >
					<td colspan="2">

								<div align="center">
									<table width=100% border=0>
										<tr>
											<td colspan="4">
												<table class=rowtable>
													<tr>
														<td class=col1 >No. Factura</td>
														<td class=col2 colspan="3" ><input type="text" class="tbox" name="NumeroFactura" id="Numero Factura" size="24" value="<?=$r->NumeroFactura?>"></td>
													</tr>
													<tr>
														<td class=col1>Punto de Venta</td>
														<td class=col2 colspan="3">
															<?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?>
															<input type="hidden" value="<?=$r->IDPuntoVenta ?>" name="IDPuntoVenta">
															<input type="hidden" value="<?=$r->IDFactura ?>" name="IDFactura"></td>
													</tr>
													<tr>
														<td class=col1>Fecha Factura</td>
														<td class=col2 colspan="3"><input type="text" class="tbox" name="FechaFactura" size="19" value="<?=$r->FechaFactura?>" readonly>
															<script language="JavaScript1.2">

														</script>														</td>
													</tr>
													<tr>
														<td class=col1>Observaciones</td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?=$r->Observaciones?></textarea></td>
													</tr>
													<tr>
														<td class=col1>														</td>
														<td class=col1></td>
														<td class=col1></td>
														<td class=col1></td>
													</tr>

													<!-- DATOS CREDITOS -->
													<tr>
														<td class=row1 colspan="4"><b>INFORMACION CREDITO</b></td>
													</tr>
													<tr>
														<td class=col1>Comentario</td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="ComentarioCredito" rows="4" cols="64"><?=$r->ComentarioCredito?></textarea></td>
													</tr>
													<tr>
														<td class=col1>Fecha Ultimo Comentario</td>
														<td class=col2 colspan="3"><?=$r->FechaUtimoComentario?></td>
													</tr>
													<tr>
														<td class=col1>Fecha Ultima Gesti&oacute;n</td>
														<td class=col2>
															<input type="text" class="tbox" name="FechaUltimaGestion" size="12" value='<?php if($r->FechaUltimaGestion!="0000-00-00") echo $r->FechaUltimaGestion;  ?>' readonly>
															<script language="JavaScript1.2">
															<!--
																if (!document.layers)
																	document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaUltimaGestion,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
															//-->
															</script>
														</td>
														<td class=col1>Fecha Carta Notif</td>
														<td class=col2>
															<input type="text" class="tbox" name="FechaCartaNotificacion" size="12" value='<?php if($r->FechaCartaNotificacion!="0000-00-00") echo $r->FechaCartaNotificacion;  ?>' readonly>
															<script language="JavaScript1.2">
															<!--
																if (!document.layers)
																	document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaCartaNotificacion,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
															//-->
															</script>
														</td>
													</tr>
													<tr>
														<td class=col1 nowrap>Nro Pagare</td>
														<td class=col2><input type="text" class="tbox" name="NumeroPagare" size="15" value='<?php echo $r->NumeroPagare;?>' <?php if(!empty($r->NumeroPagare)) echo "readOnly"; ?> ></td>
														<td class="col1" nowrap="nowrap">fecha Reportado a Procredito</td>
														<td class="col2">
															<input type="text" class="tbox" name="FechaReporteCredito" size="12" value='<?php if($r->FechaReporteCredito!="0000-00-00") echo $r->FechaReporteCredito;  ?>' readonly>
															<script language="JavaScript1.2">
															<!--
																if (!document.layers)
																	document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaReporteCredito,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
															//-->
															</script>
														</td>
													</tr>



													<!-- FIN DATOS CREDITOS -->

													<tr>
														<td class=row1 colspan="4"><b>CLIENTE</b></td>
													</tr>
													<tr>
														<td class=col1>C&eacute;dula</td>
														<td class=col2><input type="text" class="tbox" name="Cedula" readonly size="15" value='<?php echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente);?>'><input type="hidden" name="IDCliente" id="Cliente" value="<?=$r->IDCliente?>"></td>
														<td class=col1>Nombre</td>
														<td class=col2><input type="text" class="tbox" name="NombreCliente" readonly size="20" value='<?php echo get_field("Cliente","CONCAT(Nombre,' ',Apellido)","IDCliente",$r->IDCliente);?>'></td>
													</tr>
													<tr>
														<td class=col1 nowrap>Telefono Cliente</td>
														<td class=col2><input type="text" class="tbox" name="TeleCli" readonly size="15" value='<?php echo get_field("Cliente","Telefono","IDCliente",$r->IDCliente);?>'></td>
														<td class="col1" nowrap="nowrap">Numero de Fidelizacion</td>
														<td class="col2"><input name="NumeroFidelizacion" type="text" class="tbox" id="NumeroFidelizacion" value='<?php echo $r->NumeroFidelizacion?>' size="20" readonly /></td>
													</tr>
													<tr>
														<td class=col1 nowrap><br>														</td>
														<td class=col1></td>
														<td class=col1></td>
														<td class=col1></td>
													</tr>
													<tr>
														<td class=row1 ><b>VENDEDOR</b></td>
														<td class=row1 colspan="3"><input type="button" class="button" name="empleado" value="Buscar" onClick="window.open('Empleado/popEmpleados.php?IDPuntoVenta=<?=$IDPuntoVenta?>','','width=400,height=400');"></td>
													</tr>
													<tr>
														<td class=col1>C&eacute;dula</td>
														<td class=col2><input type="text" class="tbox" name="CedulaEmpleado" readonly size="15" value='<?php echo get_field("Empleado","Cedula","IDEmpleado",$r->IDEmpleado);?>'> <input type="hidden" id="Empleado" name="IDEmpleado" value=""></td>
														<td class=col1>Nombre</td>
														<td class=col2><input type="text" class="tbox" name="NombreEmpleado" readonly size="20" value='<?php echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado)." ".get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado);?>'></td>
													</tr>
													<tr>
														<td class=col1><br></td>
														<td class=col1></td>
														<td class=col1></td>
														<td class=col1></td>
													</tr>
													<tr>
														<td class=row1 colspan="4"><b>VENTA CREDITO</b></td>
													</tr>
													<tr>
														<td class=col1>Incremento</td>
														<td class=col2 colspan="3"><input type="text" class="tbox" name="Descuento" size="3" value="<?=$r->Descuento?>" maxlength="3">%</td>
													</tr>
													<tr>
														<td class=col1>Comentario</td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="ObservacionDescuento" rows="4" cols="64"><?=$r->ObservacionDescuento?></textarea></td>
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
														$sql_detalle = "SELECT * FROM DetalleFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta' ";
														$query_detalle = db_query($sql_detalle);
														$i = 0;
														while( $r_detalle = db_fetch_object( $query_detalle ) )
														{
															$class = repetition()?"col1list":"col2list";
															$i++;
													?>
													<tr bgcolor="#dfe3e7">
														<td align="left" class="<?=$class?>"><b><?=$i?></b></td>
														<td align="left" class="<?=$class?>"><?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))?></td>
														<td align="left" class="<?=$class?>"><?php echo get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))?></td>
														<td align="left" class="<?=$class?>"><?php echo $ref=get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))?></td>
														<td align="left" class="<?=$class?>"><?php echo $r_detalle->Cantidad?></td>
														<td align="left" class="<?=$class?>"><?php echo number_format($r_detalle->ValorU);?></td>
														<td align="left" class="<?=$class?>"><?php echo number_format($r_detalle->DescuentoPar);?>%</td>
														<td align="left" class="<?=$class?>"><?php echo number_format( ( $r_detalle->ValorU * $r_detalle->Cantidad ) * ( 1 - ( $r_detalle->DescuentoPar / 100 ) ) );?></td>
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
										<tr>
											<td class=col1></td>
											<td class=col1 width="250"></td>
											<td class=col2>
												<div align="right">
													Valor IVA</div>
											</td>
											<td class=col2><input type=text readonly name=ValorIVA value="<?=number_format($r->ValorIVA)?>" class=tbox size=15></td>
										</tr>
										<tr>
											<td class=col1></td>
											<td class=col1 width="250"></td>
											<td class=col2>
												<div align="right">
													Total Factura
												</div>
											</td>
											<td class=col2><input type=text readonly name=ValorTotal value="<?=number_format($r->ValorTotal)?>" class=tbox size=15></td>
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
											$query_formapago = db_query( $sql_formapago );

											while( $r_formapago = db_fetch_object( $query_formapago ) )
											{
												if($r_formapago->Valor <> 0)
												{
										?>
										<tr>
											<td class=col1></td>
											<td class=col1 width="250"></td>
											<td class=col2>
												<div align="right">
													<?php echo get_field("FormaPago","Descripcion","IDFormaPago",$r_formapago->IDFormaPago)?></div>
											</td>
											<td class=col2><input type=text readonly name="formapago[<?=$r_formapago->IDFormaPago?>]" value="<?=number_format($r_formapago->Valor)?>" class=tbox size=15></td>
										</tr>
										<?php
											}//end if($r_formapago->Valor <> 0)
										}//end while( $r_formapago = db_fetch_object( $query_formapago ) )

										$sql_credito = "SELECT * FROM Credito WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta'";
										$qry_credito = db_query( $sql_credito );
										$r_credito = db_fetch_object( $qry_credito );
										if( db_num_rows( $qry_credito ) > 0 )
										{

										?>

										<tr>
											<td class="navpic" colspan="4" align="left">
												<b>Cuotas Factura - No Credito <?=$r_credito->NumeroDocumento ?></b>
											</td>
										</tr>
										<tr>
											<td  colspan="4" align="center">



                                            <?php if($r->Estado!="ANULADA"): ?>

												<table width=100% >
													<tr bgcolor="#dfe3e7">
														<td align="center"><b>Cuota Numero</b></td>
														<td align="center"><b>Fecha Cuota</b></td>
														<td align="center"><b>Pago</b></td>
														<td align="center"><b>Medio Pago</b></td>
														<td align="center"><b>Fecha Pago</b></td>
														<td align="center"><b>Valor Cuota</b></td>
													</tr>

										<?php
											$sql_cuotas = "SELECT * FROM CreditoCuota WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta' ";
											$qry_cuotas = db_query( $sql_cuotas );
											while( $r_cuotas = db_fetch_object( $qry_cuotas ) )
											{
												$class = repetition()?"col1list":"col2list";
										?>
												<tr>
													<td class="<?=$class?>" align="center"><?=$r_cuotas->IDCuota?></td>
													<td class="<?=$class?>" align="center"><?=$r_cuotas->FechaCuota?></td>
													<td class="<?=$class?>" align="center">

														<?php
                                                        if( $r_cuotas->FechaPago == "0000-00-00 00:00:00" && $r_cuotas->Estado !="Cartera Castigada")
														{
														?>
																<input type="checkbox" class="seleccion_cuota_pago" name="CheckFechaPago<?=$r_cuotas->IDCuota?>" id="CheckFechaPago<?=$r_cuotas->IDCuota?>" alt="<?=$r_cuotas->IDCuota?>">


														<?php
															}//end else
														?>

                                                    </td>
													<td class="<?=$class?>" align="center">

														<?php
                                                        if( $r_cuotas->FechaPago == "0000-00-00 00:00:00" && $r_cuotas->Estado !="Cartera Castigada")
														{
														?>
																<select name="MedioPago<?=$r_cuotas->IDCuota?>" id="MedioPago<?=$r_cuotas->IDCuota?>" alt="<?=$r_cuotas->IDCuota?>" >
																	<option value="">Seleccione</option>
																	<option value="Efectivo">Efectivo</option>
																	<option value="Transferencia">Transferencia</option>
																</select>	
														<?php
															}
															else{
																echo $r_cuotas->MedioPago;
															}
															//end else
														?>

                                                    </td>
													<td class="<?=$class?>" align="center">
														<?php
															if( $r_cuotas->FechaPago <> "0000-00-00 00:00:00" )
																echo $r_cuotas->FechaPago;
															else
															{
																if($r_cuotas->Estado=="Cartera Castigada"){
																	echo "Cartera Castigada";
																}
																else{
														?>
																<?php //Usuario especial con id 57 que puede modificar las fechas de creditos  ?>
                                                                <input type="text" class="tbox" name="FechaPago<?=$r_cuotas->IDCuota?>"  id="FechaPago<?=$r_cuotas->IDCuota?>" size="19" value='' <?php if($ID_Usuario!=57){ ?>readonly <?php } ?>  >

														<?php
																}
															}
															//end else
														?>
													</td>
													<td class="<?=$class?>" align="center">
														<?=number_format( $r_cuotas->ValorTotal, 0 )?>
                                                        <?php
														if( $r_cuotas->FechaPago == "0000-00-00 00:00:00" )
															$saldo_cuota+=$r_cuotas->ValorTotal; ?>

														<input type=hidden name=IDCuota[<?=$r_cuotas->IDCuota?>] value="<?=$r_cuotas->IDCuota?>">
													</td>
												</tr>

										<?php
											}//end while
										?>

                                        <tr bgcolor="#dfe3e7">
												  <td  align="center">&nbsp;</td>
                                                  <td  align="center">&nbsp;</td>
												  <td  align="center">&nbsp;</td>
												  <td  align="center"><strong>Saldo</strong></td>
												  <td  align="center"><strong>$<?php echo number_format($saldo_cuota); ?></strong></td>
												  </tr>
												</table>
                                                 <?php endif; ?>

											</td>
										</tr>
										<tr>
											<td  colspan="4" align="center">
                                            	<?php if($r->Estado=="ANULADA"): ?>
                                                	FACTURA ANULADA
                                                 <?php else: ?>
												<input type="submit" name="Submit" value="Actualizar Pagos" class="submit" >
                                                <?php endif; ?>
											</td>
										</tr>
										<?php
										}//end if cuotas
										?>

										<tr>
											<td class="navpic" colspan="4" align="center">
												<?php
													echo $r->Resolucion;
													echo "  Facturas desde ".$r->RDesde." Hasta ".$r->RHasta;
												?>
											</td>
										</tr>
									</table>
									<input type="hidden" name="action" value="<?=$newmode?>">

									<br>
                                    <?php if($r->Estado=="ANULADA"): ?>
                                        FACTURA ANULADA
                                     <?php else: ?>
                                    <input value="Imprimir Factura" type="button" class="submit" onClick="window.open( 'Factura/FImpresion.php?id=<?=$r->IDFactura?>&idpunto=<?=$r->IDPuntoVenta?>','','width=426, height=350' )">
                                    <?php endif; ?>

                                    <input type="hidden" name="IDPuntoVentaPago" id="IDPuntoVentaPago" value="<?php echo $IDPuntoVenta; ?>">
																		  <input type="hidden" name="ComentarioCreditoAnt" id="ComentarioCreditoAnt" value="<?php echo $r->ComentarioCredito; ?>">
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
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;

	if( !empty($sql) )
	{

		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=20;
   		$nav->execute($sql,$dblink);
		$total_records =  $nav->total_result;
		$rows = $nav->rows;
		$result = $nav->sql_result;
		$row = $offset;
		$startrow = $offset + 1;
		$finalrow = ($row * $nav->limit) + $rows;

	 	$pages = $nav->show_num_pages('&laquo;','&laquo; prev','&raquo;','next &raquo;','|','class=navvar');   // show pages

		$info = $nav->show_info();

 	}//end if

?>
		<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="650">

			<tr>
				<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
				</td>
				<td class="tbtbot"><b></b>
					<span class="gen">
						Buscar factura para pago de creditos
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
					<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >

						<tr>
							<td>

								<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
									<tr>
										<td class="forumlink" colspan="2">
											<?php filtrar();?>
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
		if($rows > 0){
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


		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >

			<tr>
				<td class="forumlink" colspan="2">
					<table width=100% border=0 cellspacing=1 cellpadding=0>
							<tr>
								<td align=center class=navpic valign=middle bgcolor=#DBEAF5 width=69>Ver</td>
								<td class=navpic nowrap bgcolor=#DBEAF5>Almacen</td>
									<td class=navpic nowrap bgcolor=#DBEAF5> Cliente&nbsp; </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> NumeroFactura&nbsp;</td>
									<td class=navpic nowrap bgcolor=#DBEAF5> FechaFactura&nbsp; </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> ValorTotal&nbsp; </td>
								</tr>

							<?php while($r = db_fetch_object($result)){
								$class = repetition()?"col1list":"col2list";
								$i++;
								//$contador=0;
								// verifico si tiene cuotas pendientes
								$sql_cuota="Select * from CreditoCuota Where IDPuntoVenta = '".$r->IDPuntoVenta."' and IDFactura = '".$r->IDFactura."' and FechaPago = '0000-00-00 00:00:00' and Estado <> 'Cartera Castigada'";
								$result_cuota=db_query($sql_cuota);
								if(db_num_rows($result_cuota)>0):
							?>

							<tr style="color:#EE080C">
								<td align=center valign=middle nowrap width=50 class="<?=$class?>" >
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id=" . $r->$Key . "&idp=" . $r->IDPuntoVenta ?>'><img src='images/edit.gif' border='0'></a>
								</td>
								<td nowrap class="<?=$class?>" <?php if($contador==0): echo 'style="color:#EE080C"'; endif; ?>><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta);?></td>
									<td nowrap class="<?=$class?>" <?php if($contador==0): echo 'style="color:#EE080C"'; endif; ?>><?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente)." ".get_field("Cliente","Apellido","IDCliente",$r->IDCliente)?></td>
									<td nowrap class="<?=$class?>" <?php if($contador==0): echo 'style="color:#EE080C"'; endif; ?>><?php echo $r->NumeroFactura ?></td>
									<td nowrap class="<?=$class?>" <?php if($contador==0): echo 'style="color:#EE080C"'; endif; ?>><?php echo formatofecha(substr($r->FechaFactura,0,10))." ".substr($r->FechaFactura,10) ?></td>
									<td align="right" nowrap class="<?=$class?>"><?php echo number_format($r->ValorTotal) ?></td>
								</tr>
                               <?php
							   	$contador++;
								else:
									$array_id_factura_ant[]=$r->IDFactura;
							   endif; ?>
							<?php } // END for
							?>
							<tr>


							<td  class="navpic" colspan=6 nowrap>
									<?php
										print $pages;
									?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

        <table width="100%">
        <tr>
         <td bgcolor="#FFFFFF" align="center"><span><strong>CREDITOS ANTERIORES</strong></span><br></td>
        <tr>
        </table>
        <table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >

			<tr>
				<td class="forumlink" colspan="2">
					<table width=100% border=0 cellspacing=1 cellpadding=0>
							<tr>
								<td align=center class=navpic valign=middle bgcolor=#DBEAF5 width=69>Ver</td>
								<td class=navpic nowrap bgcolor=#DBEAF5>Almacen</td>
									<td class=navpic nowrap bgcolor=#DBEAF5> Cliente&nbsp; </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> NumeroFactura&nbsp;</td>
									<td class=navpic nowrap bgcolor=#DBEAF5> FechaFactura&nbsp; </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> ValorTotal&nbsp; </td>
								</tr>

							<?php
							$sql=str_replace("ORDER BY F.FechaFactura ASC","ORDER BY F.FechaFactura DESC",$sql);
							$result=db_query($sql);
							$contador=10;
							while($r = db_fetch_object($result)){
								$class = repetition()?"col1list":"col2list";
								$i++;
								//$contador=0;
								// verifico si tiene cuotas pendientes
								if(in_array($r->IDFactura,$array_id_factura_ant)){
							?>

							<tr style="color:#EE080C">
								<td align=center valign=middle nowrap width=50 class="<?=$class?>" >
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id=" . $r->$Key . "&idp=" . $r->IDPuntoVenta ?>'><img src='images/edit.gif' border='0'></a>
								</td>
								<td nowrap class="<?=$class?>" <?php if($contador==0): echo 'style="color:#EE080C"'; endif; ?>><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta);?></td>
									<td nowrap class="<?=$class?>" <?php if($contador==0): echo 'style="color:#EE080C"'; endif; ?>><?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente)." ".get_field("Cliente","Apellido","IDCliente",$r->IDCliente)?></td>
									<td nowrap class="<?=$class?>" <?php if($contador==0): echo 'style="color:#EE080C"'; endif; ?>><?php echo $r->NumeroFactura ?></td>
									<td nowrap class="<?=$class?>" <?php if($contador==0): echo 'style="color:#EE080C"'; endif; ?>><?php echo formatofecha(substr($r->FechaFactura,0,10))." ".substr($r->FechaFactura,10) ?></td>
									<td align="right" nowrap class="<?=$class?>"><?php echo number_format($r->ValorTotal) ?></td>
								</tr>
                               <?php
							   	$contador++;
								}
							   ?>
							<?php } // END for
							?>
							<tr>


							<td  class="navpic" colspan=6 nowrap>
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
}// End if$rows
else
{
	if( empty($sql) )
		echo "<br><br><span class=subtitle><b>Debe realizar la busqueda de factura y punto de venta </b></span>";
	else
		echo "<br><br><span class=subtitle><b>No se han encontrado registros </b></span>";
}//end else
}// Enf function list()

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD,$IDPuntoVenta;
?>
	<form name="frm" action="./" method="get" >
		<tr>
			<td class="rowform" align="center" colspan=8>
				N&uacute;mero de Factura
				<input type="text" size="20" name="NumeroFactura" id="NumeroFactura" class="post">
				o
				C&eacute;dula del Cliente
				<input type="text" size="20" name="Cedula" id="Cedula" class="post">

				<br>
                <!--
				Punto de Venta
				<select name="IDPuntoVenta" onChange="document.frm.submit();" >
						<option value="">Seleccione Un Punto de Venta</option>
						<?php
						$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre ");
						while($punto = db_fetch_object($qry_punto)){
							 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
						}
						?>
				</select>
                -->
				<input type="hidden" name="mod" value="<?=$MOD?>">
				<input type="hidden" name="action" value="list">
				<input type="submit" name="submit" value="Buscar Factura" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
?>
