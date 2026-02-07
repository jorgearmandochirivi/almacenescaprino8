
<body> <?php

$TitleMod ="Factura";

$Table = "Factura";
$TableJoin = "Factura";
$Key = "IDFactura";
$MOD = "Factura";
$m = "Factura";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "update":
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				//Actualizar Cuotas
				foreach( $IDCuota as $key => $value )
				{
					$fechapago = "FechaPago".$key;
					if( !empty( $_POST[$fechapago] ) )
					{
						$sql_update = " UPDATE CreditoCuota SET FechaPago = '$_POST[$fechapago]' WHERE IDFactura = '$_POST[IDFactura]' AND IDPuntoVenta = '$_POST[IDPuntoVenta]' AND IDCuota = '$key' ";
						$qry_update = db_query( $sql_update );
						echo "<script>window.open( 'Factura/FImpresionCredito.php?id=".$_POST[IDFactura]."&idpunto=".$_POST[IDPuntoVenta]."&idcuota=".$key."','','width=426, height=350' );</script>";
					}//end if
				}//end for
				
				$sql_cuotas = " SELECT * FROM CreditoCuota WHERE IDFactura = '$_POST[IDFactura]' AND IDPuntoVenta = '$_POST[IDPuntoVenta]' AND FechaPago = '0000-00-00 00:00:00'  ";
				$qry_cuotas = db_query( $sql_cuotas );
				if( db_num_rows( $qry_cuotas ) == 0  )
				{
					db_query( "UPDATE Credito SET Cancelado = 'S' " );
				}//end if
				
				
				//db_query( "tales" );
				db_query("COMMIT");
				
				echo "<script>location.href='?mod=".$MOD."&action=edit&id=".$_POST[IDFactura]."'</script>";
				
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "list" :	
			$sql = make_qry_string($_GET);
			list_r($sql);
			break;
			default : 
					list_r();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id,$newmode,$title,$submit_caption){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta,$crypt;

	$qid = db_query(" SELECT * FROM Factura WHERE IDFactura = '$id' AND IDPuntoVenta = '$IDPuntoVenta' ");
		
	$r = db_fetch_object($qid);
?>


<br>
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?php echo $title?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>
<FORM name="frm" method="post" enctype="multipart/form-data" action="<?php echo $PHP_SELF?>" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>
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
														<td class=col2 colspan="3" ><input type="text" class="tbox" name="NumeroFactura" id="Numero Factura" size="24" value="<?php echo $r->NumeroFactura?>"></td>
													</tr>
													<tr>
														<td class=col1>Punto de Venta</td>
														<td class=col2 colspan="3">
															<?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?>
															<input type="hidden" value="<?php echo $IDPuntoVenta?>" name="IDPuntoVenta">
															<input type="hidden" value="<?php echo $r->IDFactura?>" name="IDFactura"></td>
													</tr>
													<tr>
														<td class=col1>Fecha Factura</td>
														<td class=col2 colspan="3"><input type="text" class="tbox" name="FechaFactura" size="19" value="<?php echo $r->FechaFactura?>" readonly>
															<script language="JavaScript1.2">
															
														</script>
														</td>
													</tr>
													<tr>
														<td class=col1>Observaciones</td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?php echo $r->Observaciones?></textarea></td>
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
														<td class=col2><input type="text" class="tbox" name="Cedula" readonly size="15" value='<?php echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente);?>'><input type="hidden" name="IDCliente" id="Cliente" value="<?php echo $r->IDCliente?>"></td>
														<td class=col1>Nombre</td>
														<td class=col2><input type="text" class="tbox" name="NombreCliente" readonly size="20" value='<?php echo get_field("Cliente","CONCAT(Nombre,' ',Apellido)","IDCliente",$r->IDCliente);?>'></td>
													</tr>
													<tr>
														<td class=col1 nowrap>Telefono Cliente</td>
														<td class=col2><input type="text" class="tbox" name="TeleCli" readonly size="15" value='<?php echo get_field("Cliente","Telefono","IDCliente",$r->IDCliente);?>'></td>
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
														<td class=row1 ><b>VENDEDOR</b></td>
														<td class=row1 colspan="3"><input type="button" class="button" name="empleado" value="Buscar" onClick="window.open('Empleado/popEmpleados.php?IDPuntoVenta=<?php echo $IDPuntoVenta?>','','width=400,height=400');"></td>
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
														<td class=row1 colspan="4"><b>DESCUENTO ESPECIAL</b></td>
													</tr>
													<tr>
														<td class=col1>Valor Descuento</td>
														<td class=col2 colspan="3"><input type="text" class="tbox" name="Descuento" size="3" value="<?php echo $r->Descuento?>" maxlength="3">%</td>
													</tr>
													<tr>
														<td class=col1>Comentario Descuento Especial</td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="ObservacionDescuento" rows="4" cols="64"><?php echo $r->ObservacionDescuento?></textarea></td>
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
														while( $r_detalle = db_fetch_object( $query_detalle ) )
														{
															$class = repetition()?"col1list":"col2list";
															$i++;
													?>
													<tr bgcolor="#dfe3e7">
														<td align="left" class="<?php echo $class?>"><b><?php echo $i?></b></td>
														<td align="left" class="<?php echo $class?>"><?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))?></td>
														<td align="left" class="<?php echo $class?>"><?php echo get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))?></td>
														<td align="left" class="<?php echo $class?>"><?php echo $ref=get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))?></td>
														<td align="left" class="<?php echo $class?>"><?php echo $r_detalle->Cantidad?></td>
														<td align="left" class="<?php echo $class?>"><?php echo number_format($r_detalle->ValorU);?></td>
														<td align="left" class="<?php echo $class?>"><?php echo number_format($r_detalle->DescuentoPar);?>%</td>
														<td align="left" class="<?php echo $class?>"><?php echo number_format( ( $r_detalle->ValorU * $r_detalle->Cantidad ) * ( 1 - ( $r_detalle->DescuentoPar / 100 ) ) );?></td>
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
											<td class=col2><input type=text readonly name=ValorIVA value="<?php echo number_format($r->ValorIVA)?>" class=tbox size=15></td>
										</tr>
										<tr>
											<td class=col1></td>
											<td class=col1 width="250"></td>
											<td class=col2>
												<div align="right">
													Total Factura
												</div>
											</td>
											<td class=col2><input type=text readonly name=ValorTotal value="<?php echo number_format($r->ValorTotal)?>" class=tbox size=15></td>
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
											<td class=col2><input type=text readonly name="formapago[<?php echo $r_formapago->IDFormaPago?>]" value="<?php echo number_format($r_formapago->Valor)?>" class=tbox size=15></td>
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
												<b>Cuotas Factura - No Credito <?php echo $r_credito->NumeroDocumento ?></b>
											</td>
										</tr>
										<tr>
											<td  colspan="4" align="center">
												<table width=100% >
													<tr bgcolor="#dfe3e7">
														<td align="center"><b>Cuota Numero</b></td>
														<td align="center"><b>Fecha Cuota</b></td>
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
													<td class="<?php echo $class?>" align="center"><?php echo $r_cuotas->IDCuota?></td>
													<td class="<?php echo $class?>" align="center"><?php echo $r_cuotas->FechaCuota?></td>
													<td class="<?php echo $class?>" align="center">
														<?php	
															if( $r_cuotas->FechaPago <> "0000-00-00 00:00:00" )
																echo $r_cuotas->FechaPago;
															else 
															{
														?>
																<input type="text" class="tbox" name="FechaPago<?php echo $r_cuotas->IDCuota?>" size="19" value='' >
																<script language="JavaScript1.2">
																	<!--
																		if (!document.layers)
																			document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaPago<?php echo $r_cuotas->IDCuota?>,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
																	//-->
																</script>
														<?php	
															}//end else
														?>
													</td>
													<td class="<?php echo $class?>" align="center">
														<?php echo number_format( $r_cuotas->ValorTotal, 0 )?>
														<input type=hidden name=IDCuota[<?php echo $r_cuotas->IDCuota?>] value="<?php echo $r_cuotas->IDCuota?>">
													</td>
												</tr>
										
										<?php
											}//end while
										?>
												</table>
											</td>
										</tr>	
										<tr>
											<td  colspan="4" align="center">
												<input type="submit" name="Submit" value="Actualizar Pagos" class="submit" >
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
									<input type="hidden" name="action" value="<?php echo $newmode?>"><br><input value="Imprimir Factura" type="button" class="submit" onClick="window.open( 'Factura/FImpresion.php?id=<?php echo $r->IDFactura?>&idpunto=<?php echo $r->IDPuntoVenta?>','','width=426, height=350' )">
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
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table WHERE IDPuntoVenta = '$IDPuntoVenta' ORDER BY FechaFactura DESC";
	 	
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

 if($_GET['in_order']=="ASC" || $_GET['in_order']==""){
								$img="down.png";
								$order="DESC";
							}else if($_GET['in_order']=="DESC"){
								$img="up.png";
								$order="ASC";
							}
							
							?><?php
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
					<?php filtrar();?>
				</td>
			</tr>
			<tr>
				<td class="forumlink" colspan="2">
					<table width=100% border=0 cellspacing=1 cellpadding=0>
							<tr>
								<td align=center class=navpic valign=middle bgcolor=#DBEAF5 width=69>Ver</td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=IDCliente&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Cliente&nbsp;<?php if($_GET['order_by']=="IDCliente")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=NumeroFactura&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">NumeroFactura&nbsp;<?php if($_GET['order_by']=="NumeroFactura")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=FechaFactura&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">FechaFactura&nbsp;<?php if($_GET['order_by']=="FechaFactura")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=ValorTotal&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">ValorTotal&nbsp;<?php if($_GET['order_by']=="ValorTotal")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
								</tr>
							
							<?php while($r = db_fetch_object($result)){
								$class = repetition()?"col1list":"col2list";
								$i++;
							?>
							  	
							<tr>
								<td align=center valign=middle nowrap width=50 class="<?php echo $class?>">
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
								</td>
									<td nowrap class="<?php echo $class?>"><?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente)." ".get_field("Cliente","Apellido","IDCliente",$r->IDCliente)?></td>
									<td nowrap class="<?php echo $class?>"><?php echo $r->NumeroFactura ?></td>
									<td nowrap class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaFactura,0,10))." ".substr($r->FechaFactura,10) ?></td>
									<td align="right" nowrap class="<?php echo $class?>"><?php echo number_format($r->ValorTotal) ?></td>
								</tr>
							<?php } // END for
							?>
							<tr>
							<td  class="navpic" colspan=5 nowrap>
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
	echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
}// Enf function list()				

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD,$IDPuntoVenta;
?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
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
					//-->
				</script>
				 y <input type=text size=10 readonly class=input name=limit2> 
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
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
				<input type="hidden" name="mod" value="<?php echo $MOD?>">
				<input type="hidden" name="rangofield" value="FechaFactura">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="IDPuntoVenta" value="<?php echo $IDPuntoVenta?>">
				<input type="hidden" name="tjoin" value="Cliente">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
?>
