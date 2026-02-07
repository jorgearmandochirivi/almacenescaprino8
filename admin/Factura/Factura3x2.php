<body> <?php 

$TitleMod ="Factura 3X2";

$Table = "Factura";
$TableJoin = "Factura";
$Key = "IDFactura";
$MOD = "Factura_3_X_2";
$m = "Factura";


$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;
			
			case "insert" :
				$frm= vars_LOG($_POST);
				$id = insert($frm);
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($_POST);
				update($frm);
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$_GET[action]="";
				delete($ID);
			break;
			case "list" :	
			
				if( $field == "NumeroReferencia" )
				{
				
					$sql = " SELECT * FROM Referencia R, PuntoVentaReferencia PR, CodificacionEspecifica CE, DetalleFactura DF, Factura F 
								WHERE R.Numero LIKE '%$QryString%'
								AND R.IDReferencia = PR.IDReferencia 
								AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia 
								AND CE.IDCodificacionEspecifica = DF.IDCodificacionEspecifica
								AND DF.IDFactura = F.IDFactura GROUP BY F.IDFactura ORDER BY F.FechaFactura DESC " ;
					
				}//end if
				else
				{	
							$sql = make_qry_string($HTTP_GET_VARS);
				}
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
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $idpunto;

	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' AND IDPuntoVenta = '$idpunto' ");
		
	$r = db_fetch_object($qid);
?>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td></td>
	</tr>
</table>

<br>
<br>
<script>
function eliminafactura( IDFactura, IDPuntoVenta )
{
	if( confirm( "Seguro que desea eliminar esta factura?" ) )
		window.open( 'Factura/eliminafactura.php?IDFactura='+IDFactura+'&IDPuntoVenta='+IDPuntoVenta,'','width=100, height=100' );
}
</script>
<table border=1 cellpadding=1 cellspacing=0 bordercolor=#9DAAC6 align=center style="border-collapse: collapse">
	
	<tr>
		<td class=maintitle bgcolor=#9daac6>Factura</td>
	</tr>
	<tr>
		<td>
			<table width=450 border=0 cellspacing=1 cellpadding=1 class=texto bgcolor=ffffff>
				<tr class=row2>
					<td colspan="2">
						<FORM name="frm" method="post" enctype="multipart/form-data" action="<?php echo $PHP_SELF?>">
								<div align="center">
									<table width=100% border=0>
										<tr>
											<td colspan="4">
												<table class=rowtable>
													<tr>
														<td class=row2 colspan="2"></td>
														<td class=row2>
															<div align="left">
																Numero Factura</div>														</td>
														<td class=row2><input type="text" class="input" name="NumeroFactura" readonly size="24" value="<?php echo $r->NumeroFactura?>"></td>
													</tr>
													<tr>
														<td class=row2>Punto de Venta</td>
														<td class=row2><input type="text" class="input" name="PuntoVenta" readonly size="24" value="<?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta)?>"></td>
														<td class=row2>No. Documento</td>
														<td class=row2><input type="text" class="input" name="NumeroDocumento" readonly size="24" value="<?php echo $r->NumeroDocumento?>"></td>
													</tr>
													<tr>
														<td class=row2></td>
														<td class=row2></td>
														<td class=row2></td>
														<td class=row2></td>
													</tr>
													<tr>
														<td class=rowtable><b>CLIENTE</b></td>
														<td class=rowtable></td>
														<td class=rowtable></td>
														<td class=rowtable></td>
													</tr>
													<tr>
														<td class=row2>Cedula</td>
														<td class=row2><input type="text" class="input" name="Cedula" readonly size="15" value='<?php echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente);?>'><input type="hidden" name="IDCliente" value="<?php echo $r->IDCliente?>"></td>
														<td class=row2>Nombre</td>
														<td class=row2><input type="text" class="input" name="Cliente" readonly size="20" value='<?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente)." ".get_field("Cliente","Apellido","IDCliente",$r->IDCliente);?>'></td>
													</tr>
													<tr>
														<td class=row2 nowrap>Telefono Cliente</td>
														<td class=row2><input type="text" class="input" name="TeleCli" readonly size="15" value='<?php echo get_field("Cliente","Telefono","IDCliente",$r->IDCliente);?>'></td>
														<td class="col1" nowrap="nowrap">Numero de Fidelizacion</td>
														<td class="col2"><input name="NumeroFidelizacion" type="text" class="tbox" id="NumeroFidelizacion" value='<?php echo $r->NumeroFidelizacion?>' size="20" readonly /></td>
													</tr>
													<tr>
														<td class=row1></td>
														<td class=row1 colspan="3"></td>
													</tr>
													<tr>
														<td class=row1>Fecha Factura</td>
														<td class=row1 colspan="3"><input type="text" class="input" name="FechaFactura" size="24" value='<?php echo $r->FechaFactura?>' readonly> </td>
													</tr>
													<tr>
														<td class=row1><br>														</td>
														<td class=row1></td>
														<td class=row1></td>
														<td class=row1></td>
													</tr>
													<tr>
														<td class=row1>Observaciones</td>
														<td colspan="3" class=row1><textarea name="Observaciones" rows="4" cols="64"><?php echo $r->Observaciones?></textarea></td>
													</tr>
													
													<tr>
														<td class=rowtable><b>EMPLEADO</b></td>
														<td class=rowtable><?php if($newmode == "insert"){?><input type="button" class="submit" name="empleado" value="Buscar" onClick="window.open('Empleado/popEmpleados.php','','width=400,height=400');"><?php }?></td>
														<td class=rowtable></td>
														<td class=rowtable></td>
													</tr>
													<tr>
														<td class=row2>C&eacute;dula</td>
														<td class=row2><input type="text" class="input" name="CedulaEmpleado" readonly size="15" value='<?php echo get_field("Empleado","Cedula","IDEmpleado",$r->IDEmpleado);?>'> <input type="hidden" name="IDEmpleado" value="<?php echo $r->IDEmpleado?>"></td>
														<td class=row2>Nombre</td>
														<td class=row2><input type="text" class="input" name="NombreEmpleado" readonly size="20" value='<?php echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado)." ".get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado);?>'></td>
													</tr>
													<tr>
														<td class=row2><br></td>
														<td class=row2></td>
														<td class=row2></td>
														<td class=row2></td>
													</tr>
													<tr>
														<td class=rowtable colspan="2"><b>DESCUENTO ESPECIAL</b></td>
														<td class=rowtable></td>
														<td class=rowtable></td>
													</tr>
													<tr>
														<td class=row2>Valor Descuento</td>
														<td class=row2><input type="text" class="input" name="Descuento" size="3" value="<?php echo $r->Descuento?>" maxlength="3">%</td>
														<td class=row2></td>
														<td class=row2></td>
													</tr>
													<tr>
														<td class=row2>Comentario Descuento Especial</td>
														<td class=row2 colspan="3"><textarea name="ObservacionDescuento" rows="4" cols="64"><?php echo $r->ObservacionDescuento?></textarea></td>
													</tr>
													<?php 
													echo $newmode; 	
													if( $newmode == "delete" )
													{
													?>
													<tr>
														<td class="row2" colspan="4" align="center"><input type="button" value="Eliminar Factura" onClick="eliminafactura( <?php echo $r->IDFactura?>,<?php echo $r->IDFactura?> );" class="input"></td>
													</tr>
													<?php 
													}//end fi
													?>
												</table>
										  </td>
										</tr>
										<?php 
									if($newmode <> "insert")
									{
									?>
										<tr>
											<td class=titlemedium colspan="4">Detalle Factura</td>
										</tr>
										<tr>
											<td id="field" colspan=4 bgcolor=#e7ebef></td>
										</tr>
										<tr bgcolor=#e7ebef>
											<td colspan="4" width="816">
												<table class="bordertable" border="0" cellspacing="1" cellpadding="1" id=table1 width="100%" bgcolor="#ffffff">
													<tr bgcolor="#dfe3e7">
														<td align="center" class=rowform><b>Item</b></td>
														<td align="center" class=rowform><b>Referencia</b></td>
														<td align="center" class=rowform><b>Talla</b></td>
														<td align="center" class=rowform><b>Nombre</b></td>
														<td align="center" class=rowform><b>Cantidad</b></td>
														<td align="center" class=rowform><b>Valor U. (sin descuento)</b></td>
														<td align="center" class=rowform><b>% Primer Descuento.</b></td>
														<td align="center" class=rowform><b>Valor U.</b></td>
														<td align="center" class=rowform><b>Descuento Par.</b></td>
														<td align="center" class=rowform><b>Total</b></td>
													</tr>
													<?php 												//Query para el detalle de la factura
												
												$sql_detalle = "SELECT * FROM DetalleFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta'";
												$query_detalle = db_query($sql_detalle);
												$i = 1;
												
												while( $r_detalle = db_fetch_object( $query_detalle ) )
												{
													if( $i % 2 == 0 )
														$class = "row2";
													else
														$class = "rowtable";
														
														
													
														
													if($r_detalle->DescuentoPar=="100"){
														$class="";	
														$style_regalo=" style=bgcolor='#FF0000' ";
													}
													
												?>
													<tr bgcolor="#CCFF66">
														<td align="left" class="<?php echo $class?>"><b><?php echo $i?></b></td>
														<td align="left" class="<?php echo $class?>"><?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))?></td>
														<td align="left" class="<?php echo $class?>"><?php echo get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))?></td>
														<td align="left" class="<?php echo $class?>"><?php echo get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))?></td>
														<td align="left" class="<?php echo $class?>"><?php echo $r_detalle->Cantidad?></td>
														<td align="left" class="<?php echo $class?>">
															<?php
																
																$IDPuntoRef=get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);
																$IDRefe=get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$IDPuntoRef);
																$IDPrecio=get_field("Referencia","IDPrecio","IDReferencia",$IDRefe);
																$precio=get_field("Precio","Descripcion","IDPrecio",$IDPrecio);
																$descuento=get_field("Precio","Descuento","IDPrecio",$IDPrecio);
																
																
																echo number_format($precio); 
															?></td>
														<td align="left" class="<?php echo $class?>"><?php echo $descuento?></td>
														<td align="left" class="<?php echo $class?>"><?php echo number_format($r_detalle->ValorU);?></td>
														<td <?php $style_regalo; ?> align="left" class="<?php echo $class?>"><?php echo number_format($r_detalle->DescuentoPar);?></td>
														<td align="left" class="<?php echo $class?>"><?php echo number_format( ( $r_detalle->ValorU * $r_detalle->Cantidad ) * ( 1 - ( $r_detalle->DescuentoPar / 100 ) ) );?></td>
													</tr>
												<?php 
													$i++;
												}//while( $r_detalle = db_fetch_object( $query_detalle ) )
												?>
												<tr bgcolor="#dfe3e7">
														<td align="left" class="<?php echo $class?>"><b><br></b></td>
														<td align="left" class="<?php echo $class?>"></td>
														<td align="left" class="<?php echo $class?>"></td>
														<td align="left" class="<?php echo $class?>"></td>
														<td align="left" class="<?php echo $class?>"></td>
														<td align="left" class="<?php echo $class?>"></td>
														<td align="left" class="<?php echo $class?>"></td>
														<td align="left" class="<?php echo $class?>"></td>
														<td align="left" class="<?php echo $class?>"></td>
														<td align="left" class="<?php echo $class?>"></td>
													</tr>
											<tr bgcolor="#dfe3e7">
														<td align="left" class="row2"><b><br></b></td>
														<td align="left" class="row2"></td>
														<td align="left" class="row2"></td>
														<td align="left" class="row2"></td>
														<td align="left" class="rowform" colspan="6">RESUMEN FACTURA</td>
													</tr>
											<tr bgcolor="#dfe3e7">
														<td align="left" class="row2"><b><br></b></td>
														<td align="left" class="row2"></td>
														<td align="left" class="row2">
															</td>
														<td align="left" class="row2">
															</td>
														<td align="left" class="row2">
															<div align="right">
																Valor IVA</div></td>
														<td align="left" class="rowtable" colspan="5"><?php echo number_format($r->ValorIVA)?></td>
													</tr>
											<tr bgcolor="#dfe3e7">
														<td align="left" class="row2"><b><br></b></td>
														<td align="left" class="row2"></td>
														<td align="left" class="row2">
															</td>
														<td align="left" class="row2">
															</td>
														<td align="left" class="row2">
															<div align="right">
																Total Factura</div></td>
														<td align="left" class="rowtable" colspan="5"><?php echo number_format($r->ValorTotal)?></td>
													</tr>
													
											<tr bgcolor="#dfe3e7">
														<td align="left" class="row2"><b><br></b></td>
														<td align="left" class="row2"></td>
														<td align="left" class="row2"></td>
														<td align="left" class="row2"></td>
														<td align="left" class="rowform">FORMA DE PAGO</td>
														<td align="left" class="rowform">&nbsp;</td>
														<td align="left" class="rowform">&nbsp;</td>
														<td align="left" class="rowform">VALOR</td>
														<td align="left" class="rowform"></td>
														<td align="left" class="rowform">No. DOCUMENTO</td>
													</tr>
													
											<?php 
												$sql_formapago = "SELECT * FROM FormaPagoFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta'";
												$query_formapago = db_query( $sql_formapago );
												
												while( $r_formapago = db_fetch_object( $query_formapago ) )
												{
													if($r_formapago->Valor <> 0)
													{
											?>
													<tr bgcolor="#dfe3e7">
														<td align="left" class="row2"><b><br></b></td>
														<td align="left" class="row2"></td>
														<td align="left" class="row2">
															</td>
														<td align="left" class="row2">
															</td>
														<td align="left" class="row2">
															<div align="right">
																<?php echo get_field("FormaPago","Descripcion","IDFormaPago",$r_formapago->IDFormaPago)?>
															</div>
														</td>
														<td align="left" class="rowtable" >&nbsp;</td>
														<td align="left" class="rowtable" >&nbsp;</td>
														<td align="left" class="rowtable" ><?php echo number_format($r_formapago->Valor)?></td>
														<td align="left" class="rowtable"></td>
														<td align="left" class="rowtable" ><?php echo $r_formapago->NumeroDocumento?></td>
													</tr>
												<?php 
													}//end if($r_formapago->Valor <> 0)
												}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
												
												
												
												
												
												?>	
															
												</table>
											</td>
										</tr>
										<?php 	
										
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
																<input type="text" class="tbox" name="FechaPago<?php echo $r_cuotas->IDCuota?>" size="19" value='' readonly>
																<script language="JavaScript1.2">
																	<!--
																		if (!document.layers)
																			document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaPago<?php echo $r_cuotas->IDCuota?>,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
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
										
										
																		}
									?>
									</table>
									<input type="hidden" name="action" value="<?php echo $newmode?>">
									<input type="hidden" name="ID" value="<?php echo $id?>">
									<input type="hidden" name="IDFactura" value="<?php echo $r->IDFactura?>"><input type="hidden" name="IDEmpleado" value='<?php if($newmode == "insert") echo $ID_Usuario; else echo  $r->IDEmpleado;?>'> 
									<input type="hidden" name="idpunto" value="<?php echo $idpunto?>">
									</div>
							</FORM>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
</table>
<?php 
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar;
		
	// selecciono las facturas que contienen la promocion	
		$sql_facturas_promocion=db_query("Select F.IDFactura from Factura F where ObservacionDescuento = 'pague 2 lleva 3'");

	while($result_facturas=db_fetch_array($sql_facturas_promocion)){
		$array_facturas[]=$result_facturas[IDFactura];	
	}
	
	$id_facturas=implode(",",$array_facturas);
	if(empty($id_facturas))
		$id_facturas=0;
		
	if(empty($sql))
	 	$sql =  "Select * from Factura F where ObservacionDescuento = 'pague 2 lleva 3'";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=50;
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
							
							?>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td></td>
	</tr>
</table>
<?php 
		if($rows > 0){
?>		
<br>
<table width=750 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
<?php filtrar();?>	
<tr>
	<td class=titlemedium  bgcolor=#9daac6><a href="Factura/exportar_facturas.php"> <img src="images/excel_icon.gif" border="0" width="20" height="20"></a> Exportar resultado</td>
</tr>

<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
		</tr>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=16 nowrap>
<?php 
	print $pages;
?>
</td>
</tr>
	<tr>
			<td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDCliente&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Cliente&nbsp;<?php  if($_GET['order_by']=="IDCliente"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NumeroDocumento&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Punto de Venta&nbsp;<?php  if($_GET['order_by']=="NumeroDocumento"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NumeroFactura&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">NumeroFactura&nbsp;<?php  if($_GET['order_by']=="NumeroFactura"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Numero de Fidelizacion</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=FechaFactura&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">FechaFactura&nbsp;<?php  if($_GET['order_by']=="FechaFactura"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=ValorTotal&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">ValorTotal&nbsp;<?php  if($_GET['order_by']=="ValorTotal"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>&idpunto=<?php echo $r->IDPuntoVenta?>'><img src='images/edit.gif' border='0'></a></td>
						<td nowrap class=row1><?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente)." ".get_field("Cliente","Apellido","IDCliente",$r->IDCliente)?></td>
						<td nowrap class=row1><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta) ?></td>
						<td nowrap class=row1><?php echo $r->NumeroFactura ?></td>
						<td nowrap class=row1><?php echo $r->NumeroFideliazcion ?></td>
						<td nowrap class=row1><?php echo $r->FechaFactura ?></td>
						<td nowrap class=row1><?php echo $r->ValorTotal ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>&idpunto=<?php echo $r->IDPuntoVenta;?>'><img src='images/trash.gif' border='0'></a></td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=8 nowrap>
	<?php 
		print $pages;
		?></td>
</tr>		
</table></td>
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
	Global $dblink,$total_records,$row,$numtoshow,$MOD;
?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="Cliente.Nombre">Nombre Cliente</option>
					<option value="Cliente.Apellido">Apellido Cliente</option>
					<option value="Cliente.Cedula">cedula Cliente</option>
					<option value="PuntoVenta.Nombre">Punto de Venta</option>
					<option selected value="NumeroFactura">Numero de Factura</option>
					<option selected value="NumeroReferencia">Numero de Referencia</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				Entre <input type=text readonly size=10 class=input name=limit1>
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")	
					//-->
				</script>
				 y <input type=text size=10 readonly class=input name=limit2> 
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				</script>
				<br>
				ordenar por 
				<select name="order_by" class="popup">
					<option value="Cliente.Nombre">Nombre Cliente</option>
					<option value="Cliente.Apellido">Apellido Cliente</option>
					<option value="Cliente.Cedula">cedula Cliente</option>
					<option value="PuntoVenta.Nombre">Punto de Venta</option>
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
				<input type="hidden" name="mod" value="<?php echo $MOD?>">
				<input type="hidden" name="rangofield" value="FechaFactura">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Cliente">
				<input type="hidden" name="tlevel" value="PuntoVenta">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 		
	}//End function filtrar
?>
