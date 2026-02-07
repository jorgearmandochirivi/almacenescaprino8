<body> <?php

$TitleMod ="FacturaBono";

$Table = "FacturaBono";
$TableJoin = "DetalleFacturaBono";
$Key = "IDFacturaBono";
$MOD = "FacturaBono";
$m = "Factura";


		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "list":

				if($field == "Numero"){
					$sql = "SELECT T.* FROM $Table T, $TableJoin DT, CodificacionEspecifica C, PuntoVentaReferencia PR, Referencia R
							WHERE ( T.IDPuntoVenta = '$IDPuntoVenta'  )
							AND T.$Key = DT.$Key
							AND DT.IDCodificacionEspecifica = C.IDCodificacionEspecifica
							AND C.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia
							AND PR.IDReferencia = R.IDReferencia
							AND R.Numero LIKE '%$QryString%'

							ORDER BY FechaFacturaBono DESC ";
				}
				else
					$sql = make_qry_string($HTTP_GET_VARS);

				list_r($sql);

			break;
			default :

				list_r($sql);
			break;

		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id,$newmode,$title,$submit_caption){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta;

	$qid = db_query(" SELECT * FROM FacturaBono WHERE IDFacturaBono = '$id' AND IDPuntoVenta = '$IDPuntoVenta' ");

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
												<table class=rowtable width="100%">
													<tr>
														<td class=col1 >No. Regisro</td>
														<td class=col2 colspan="3" ><input type="text" class="tbox" name="NumeroFacturaBono" id="Numero FacturaBono" readonly size="24" value="<?=$r->NumeroFacturaBono?>"></td>
													</tr>
													<tr>
														<td class=col1>Punto de Venta</td>
														<td class=col2 colspan="3">
															<?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?>
															<input type="hidden" value="<?=$IDPuntoVenta?>" name="IDPuntoVenta">
														</td>
													</tr>
													<tr>
														<td class=col1>Fecha </td>
														<td class=col2 colspan="3"><input type="text" class="tbox" name="FechaFacturaBono" size="19" value='<?=fecha()." ".hora()?>' readonly>
															<script language="JavaScript1.2">

														</script>
														</td>
													</tr>
													<tr>
														<td class=col1>Observaciones</td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?=$r->Observaciones?></textarea></td>
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
														<td class=col2><input type="text" class="tbox" name="Cedula" readonly size="15" value='<?php echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente);?>'><input type="hidden" name="IDCliente" id="Cliente" value="<?=$r->IDCliente?>"></td>
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
														<td class=row1 ><b>EMPLEADO</b></td>
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
														<td class=row1 colspan="4"><b>INFORMACI&Oacute;N BONO</b></td>
													</tr>
													<tr>
														<td class=col1>Tipo de Bono</td>
														<td class=col2>
															<?php
																//echo $r->IDFormaPagoBono."Bono";


																echo formpopup("FormaPagoBono","Descripcion","IDFormaPagoBono","IDFormaPagoBono",$r->IDFormaPagoBono,"input\" id=\"Bono");
															?>
                                                        </td>
														<td class=col1>Numero Bono</td>
														<td class=col2><input type="text" class="tbox" name="NuemeroBono" readonly size="20" value="<?php echo $r->NumeroBono?>"></td>
													</tr>
													<tr>
														<td class=col1 nowrap>Valor ( $ )</td>
														<td class=col2><input type="text" class="tbox" name="ValorBono" size="20" value="<?php echo number_format( $r->ValorBono,2) ?>" onBlur="recalcularbono()"></td>
														<td class=col1>Excedente( $ )</td>
														<td class=col2><input type="text" class="tbox" name="Excedente" readonly size="20" value="<?php echo number_format($r->Excedente,2)?>"></td>
													</tr>
													<tr>
														<td class=col1 nowrap>Tarjeta</td>
														<td class=col2><?php echo $r->CodigoTarjeta; ?></td>
														<td class=col1>Factura Excedente</td>
														<td class=col2>
															<input type="text" class="tbox" name="Excedente" readonly size="20" value="<?php echo $r->IDFactura?>">
															<a href="?mod=Factura&action=edit&id=<?=$r->IDFactura?>&idpunto=<?=$r->IDPuntoVenta?>" title="VerFactura">
																<img src="images/magnifier.png" border="0">
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
											<td class=navpic>Detalle Venta Bono</td>
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
														<td align="center"><b>Total</b></td>
													</tr>
													<?php
														$sql_detalle = "SELECT * FROM DetalleFacturaBono WHERE IDFacturaBono = '$r->IDFacturaBono' AND IDPuntoVenta = '$r->IDPuntoVenta' ";
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
														<td align="left" class="<?=$class?>"><?php echo get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))?></td>
														<td align="left" class="<?=$class?>"><?php echo $r_detalle->Cantidad?></td>
														<td align="left" class="<?=$class?>"><?php echo number_format($r_detalle->ValorU);?></td>
														<td align="left" class="<?=$class?>"><?php echo number_format($r_detalle->ValorU * $r_detalle->Cantidad);?></td>
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
												<div align="left">RESUMEN REGISTRO</div>
											</td>
										</tr>
										<tr>
											<td class=col1></td>
											<td class=col1 width="250"></td>
											<td class=col2>
												<div align="right">
													Total</div>
											</td>
											<td class=col2><input type=text readonly name=ValorTotal value="<?=number_format($r->ValorTotal)?>" class=tbox size=15></td>
										</tr>

									</table>
									<input type="hidden" name="action" value="<?=$newmode?>">
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
	 	$sql =  "SELECT * FROM $Table WHERE IDPuntoVenta = '$IDPuntoVenta' ORDER BY FechaFacturaBono DESC";

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
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=NumeroFacturaBono&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">NumeroFacturaBono&nbsp;<?php if($_GET['order_by']=="NumeroFacturaBono")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=FechaFacturaBono&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">FechaFacturaBono&nbsp;<?php if($_GET['order_by']=="FechaFacturaBono")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=ValorTotal&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">ValorTotal&nbsp;<?php if($_GET['order_by']=="ValorTotal")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
								</tr>

							<?php while($r = db_fetch_object($result)){
								$class = repetition()?"col1list":"col2list";
								$i++;
							?>

							<tr>
								<td align=center valign=middle nowrap width=50 class="<?=$class?>">
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
								</td>
									<td nowrap class="<?=$class?>"><?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente)." ".get_field("Cliente","Apellido","IDCliente",$r->IDCliente)?></td>
									<td nowrap class="<?=$class?>"><?php echo $r->NumeroFacturaBono ?></td>
									<td nowrap class="<?=$class?>"><?php echo formatofecha(substr($r->FechaFacturaBono,0,10))." ".substr($r->FechaFacturaBono,10) ?></td>
									<td nowrap class="<?=$class?>"><?php echo number_format($r->ValorTotal) ?></td>
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
					<option value="FechaFacturaBono">Fecha</option>
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
				<input type="hidden" name="mod" value="<?=$MOD?>">
				<input type="hidden" name="rangofield" value="FechaFacturaBono">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="IDPuntoVenta" value="<?=$IDPuntoVenta?>">
				<input type="hidden" name="tjoin" value="Cliente">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
?>
