<body> <?php 

$TitleMod ="Ver Cambios";

$Table = "Cambio";
$TableJoin = "DetalleCambio";
$Key = "IDCambio";
$MOD = "Cambios";
$m = "Cambio";




$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;

			case "autorizacambio":
				$sql_inserta_aut = "Insert Into AutorizacionCambioReferencia (IDCambio, IDPuntoVenta, FechaAutorizacion, AutorizadoPor)
									Values ('".$_GET["idcambio"]."','".$_GET["id_punto_venta"]."',NOW(),'".$datos["Nombre"]."')";
				db_query( $sql_inserta_aut );
				echo "
				<script>
					alert('Autorizacion registrada con exito');
					location.href='?mod=".$MOD."&action=edit&id=".$_GET["idcambio"]."&idpunto=".$_GET["id_punto_venta"]."'
				</script>

			";

			break;

			case "list" :

			if(!empty($_GET["limit1"]) && !empty($_GET["limit2"])) {
					$condicion= "and  FechaCambio >= '".$_GET["limit1"]."' and FechaCambio <= '".$_GET["limit2"]."' ";
			}

				if($field == "IDPuntoVenta")
				{
					$sql = "SELECT C.* FROM Cambio C,PuntoVenta P WHERE C.IDPuntoVenta = P.IDPuntoVenta AND P.Nombre LIKE '%$QryString%' ".$condicion." order by IDCambio desc";
				}
				elseif($field == "Cliente"){
					$sql = "SELECT C.* FROM Cambio C,Cliente CL WHERE C.IDCliente = CL.IDCliente AND CL.Cedula LIKE '%$QryString%' ".$condicion." order by IDCambio desc";
				}
				elseif($field == "Referencia")
				{
					$sql_ref = "Select IDCodificacionEspecifica
								From Referencia R, PuntoVentaReferencia PVR, CodificacionEspecifica CE
								Where R.IDReferencia = PVR.IDReferencia
								and CE.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
								and R.Numero like '%".$_GET["QryString"]."%' order by IDCodificacionEspecifica desc";
					$result_ref = db_query($sql_ref);
					while($row_ref = db_fetch_array($result_ref)):
						$array_codif[]= $row_ref["IDCodificacionEspecifica"];
					endwhile;
					if(count($array_codif)>0):
						$id_ref = implode(",",$array_codif);
					else:
						$id_ref = 0;
					endif;

					$sql = "SELECT C.* FROM Cambio C,DetalleCambio DC WHERE C.IDCambio = DC.IDCambio AND (DC.IDCodificacionEspecifica in (".$id_ref.") or DC.IDCodificacionEspecificaCambio in (".$id_ref.")) ".$condicion." order by IDCambio desc";
				}
				elseif(!empty($_GET["limit1"]) && !empty($_GET["limit2"])) {
						$condicion= "and  FechaCambio >= '".$_GET["limit1"]."' and FechaCambio <= '".$_GET["limit2"]."' ";
						$sql = "SELECT C.* FROM Cambio C,DetalleCambio DC WHERE C.IDCambio = DC.IDCambio AND (DC.IDCodificacionEspecifica in (".$id_ref.") or DC.IDCodificacionEspecificaCambio in (".$id_ref.")) ".$condicion." order by IDCambio desc";
					}
				else
					$sql = make_qry_string($HTTP_GET_VARS);
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
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta,$TableJoin,$idpunto;

	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' AND IDPuntoVenta = '$idpunto' ");

	$r = db_fetch_object($qid);
?>

<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td></td>
		</tr>
</table><br>



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
												<table class=rowtable width="100%">
													<tr>
														<td class=col1 >No. Regisro</td>
														<td class=col2 colspan="3" >
														<input type="text" class="tbox" name="NumeroFacturaBono" id="Numero FacturaBono" readonly size="24" value="<?php echo $r->IDCambio?>"></td>
													</tr>
													<tr>
														<td class=col1>Factura del Cambio</td>
														<td class=col2><input type="text" class="tbox" name="FacturaCambio" id="Numero FacturaBono" readonly size="24" value="<?php echo $r->IDFacturaCambio?>"></td>
														<td class=col2><a href="?mod=Factura&action=edit&id=<?php echo $r->IDFacturaCambio?>" title="VerFactura"><img src="../images/magnifier.png" border="0"></a></td>
														<td class=col2></td>
													</tr>
													<tr>
														<td class=col1>Punto de Venta</td>
														<td class=col2 colspan="3">
															<?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?>
															<input type="hidden" value="<?php echo $IDPuntoVenta?>" name="IDPuntoVenta">
														</td>
													</tr>
													<tr>
														<td class=col1>Fecha </td>
														<td class=col2 colspan="3">
															<input type="text" class="tbox" name="FechaFacturaBono" size="19" value='<?php echo $r->FechaCambio?>' readonly>

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
														<td class=row1 ><b>EMPLEADO</b></td>
														<td class=row1 colspan="3"></td>
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
													<td class=navpic colspan="4">Referencia ENTRA Cambio </td>
												</tr>
												<tr>
													<td colspan="4">
														<table class="texto" width="70%" border="0" cellspacing="1" cellpadding="0" id=table1 align="center">
															<tr bgcolor="#dfe3e7">
																<td align="center"><b>Referencia</b></td>
																<td align="center"><b>Talla</b></td>
																<td align="center"><b>Nombre</b></td>
																<td align="center" width="100%"><b>Cantidad</b></td>
																<td align="center" width="100%"><b>Valor U</b></td>
																	<td align="center" nowrap><b>Ver Factura</b></td>
																</tr>
															<?php 
															$query_detalle = db_query("Select * From DetalleProductoCambio Where IDCambio = '$id' AND IDPuntoVenta = '$idpunto' ");
															if(db_num_rows($query_detalle)<=0):
																$sql_detalle = "SELECT * FROM DetalleCambio WHERE IDCambio = '$r->IDCambio' AND IDPuntoVenta = '$idpunto' LIMIT 1 ";
																$query_detalle = db_query($sql_detalle);
															endif;



															while( $r_detalle = db_fetch_object( $query_detalle ) ){
															$sql_referenciacambio = " SELECT R.*, T.Descripcion as Talla FROM CodificacionEspecifica C, PuntoVentaReferencia P, Referencia R, Talla T
																						WHERE C.IDCodificacionEspecifica = '$r_detalle->IDCodificacionEspecificaCambio'
																						AND C.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia
																						AND P.IDReferencia = R.IDReferencia
																						AND C.IDTalla = T.IDTalla";
															$qry_referenciacambio = db_query( $sql_referenciacambio );

															$r_referenciacambio = db_fetch_object( $qry_referenciacambio );

															?>
															<tr>
																<td align="left" class="col1list">
																	<?php echo $r_referenciacambio->Numero?>
																</td>
																<td align="left" class="col1list">
																	<?php echo $r_referenciacambio->Talla?>
																</td>
																<td class="col1list" align="left"><?php echo $r_referenciacambio->Nombre?></td>
																<td class="col1list" align="center" width="100%"><?php echo $r_detalle->Cantidad?></td>
																<td class="col1list" align="center" width="100%">
                                                                <?php 
                                                                if($r_detalle->ValorU==0)
																	echo number_format(get_field("Precio","ValorVenta","IDPrecio",$r_referenciacambio->IDPrecio),2);
																else
																	echo $r_detalle->ValorU

                                                                ?></td>
																<td class="col1list" align="center"><a href="?mod=Factura&action=edit&id=<?php echo $r->IDFacturaCambio?>" title="VerFactura"><img src="../images/magnifier.png" border="0"></a></td>
															</tr>
															<?php 
															}
	$sql_detalle = "SELECT * FROM DetalleCambio WHERE IDCambio = '$r->IDCambio' AND IDPuntoVenta = '$idpunto'";
	$query_detalle = db_query($sql_detalle);
	$r_detalle = db_fetch_object( $query_detalle );


		?>



														</table>
													</td>
												</tr>
													<tr>
														<td class=col1 nowrap></td>
														<td class=col1>Excedente</td>
														<td class=col2 colspan="2"><input type="text" class="tbox" name="Excedente" readonly size="20" value="<?php echo number_format( $r->Excedente,2 )?>"></td>
													</tr>
													<tr>
														<td class=col1 nowrap></td>
														<td class=col1>Factura Excedente</td>
														<td class=col2 colspan="2"><input type="text" class="tbox" name="Factura" readonly size="20" value="<?php echo $r->IDFactura?>">
															<a href="?mod=Factura&action=edit&id=<?php echo $r->IDFactura?>" title="VerFactura"><img src="../images/magnifier.png" border="0">
															</a></td>
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
											<td class=navpic colspan="4"><b>Detalle SALIDA Cambio</b></td>
										</tr>
										<tr bgcolor=#e7ebef>
											<td colspan="4">
												<table class="texto" border="0" cellspacing="1" cellpadding="0" id=table1 width="100%">
													<tr bgcolor="#dfe3e7">
														<td align="center"><b>Referencia</b></td>
														<td align="center"><b>Talla</b></td>
														<td align="center"><b>Nombre</b></td>
														<td align="center"><b>Cantidad</b></td>

														<td align="center"><b>Total</b></td>
													</tr>
													<?php 
													do{
															$sql_referencia = " SELECT R.*, T.Descripcion as Talla FROM CodificacionEspecifica C, PuntoVentaReferencia P, Referencia R, Talla T
																						WHERE C.IDCodificacionEspecifica = '$r_detalle->IDCodificacionEspecifica'
																						AND C.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia
																						AND P.IDReferencia = R.IDReferencia
																						AND C.IDTalla = T.IDTalla";
															$qry_referencia = db_query( $sql_referencia );

															$r_referencia = db_fetch_object( $qry_referencia );
													$class = repetition()?"col1list":"col2list";

													?>
													<tr bgcolor="#dfe3e7">
														<td align="left" class="<?php echo $class?>"><?php echo $r_referencia->Numero?></td>
														<td align="left" class="<?php echo $class?>"><?php echo $r_referencia->Talla?></td>
														<td align="left" class="<?php echo $class?>"><?php echo $r_referencia->Nombre?></td>
														<td align="left" class="<?php echo $class?>"><?php echo $r_detalle->Cantidad?></td>

														<td align="left" class="<?php echo $class?>"><?php echo number_format($r_detalle->ValorU * $r_detalle->Cantidad);?></td>
													</tr>
													<?php 
													}while( $r_detalle = db_fetch_object( $query_detalle ) );
													?>
												</table>
											</td>
										</tr>
										<tr>
											<td class=col1></td>
											<td class=col1 width="250"></td>
											<td class=navpic colspan="2">
												<div align="left"><b>RESUMEN REGISTRO</b></div>
											</td>
										</tr>
										<tr>
											<td class=col1></td>
											<td class=col1 width="250"></td>
											<td class=col2>
												<div align="right">
													Total</div>
											</td>
											<td class=col2><input type=text readonly name=ValorTotal value="<?php echo number_format($r->Excedente)?>" class=tbox size=15></td>
										</tr>

									</table>

									<table class="bordertable" border="0" cellspacing="1" cellpadding="1" id=table1 width="100%" bgcolor="#ffffff">
			            	<tr>
			            	  <th colspan="2"><a href="?mod=Cambios&action=autorizacambio&idcambio=<?php echo $id ?>&id_punto_venta=<?php echo $idpunto?>">Autorizar cambio</a></th>
			           	  </tr>
			            	<tr>
			                	<th>
			                    	Fecha Autorizacion
			                    </th>
			                    <th>
			                    	Autorizado por
			                    </th>
			                </tr>
			                <?php
							$sql_autorizacion = "Select * From AutorizacionCambioReferencia Where IDCambio = '".$id."' and IDPuntoVenta = '".$idpunto."'";
							$result_autorizacion = db_query($sql_autorizacion);
							while($row_autorizacion = db_fetch_array($result_autorizacion)):?>
			                <tr>
			                	<td>
			                    	<?php echo $row_autorizacion["FechaAutorizacion"] ?>
			                    </td>
			                    <td align="center">
			                    	<?php echo $row_autorizacion["AutorizadoPor"] ?>
			                    </td>
			                </tr>
			                <?php endwhile; ?>

			            </table>
									<input type="hidden" name="action" value="<?php echo $newmode?>">
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

		/****** EstadoTraslado = Enviado = 1 *********/

	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY IDCambio DESC";

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
?><table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td></td>
		</tr>
</table><br>
	<table border="0" cellpadding="0" cellspacing="0" class="bordertable" align="center" width="650">

	<tr>
			<td class="maintitle" width="100%">
			Listar <?php echo $TitleMod ?>
			<?php echo $info ?>
		</td>
		</tr>
	<tr>
			<td  width="100%">
			<?php filtrar();?>
		</td>
		</tr>
		<tr>
			<td width="100%">
				<table width=100% border=0 cellspacing=1 cellpadding=0>
					<tr>
						<td align=center class="titlemedium" valign=middle  width=69>Ver</td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><span class="navpic"><a style="color: #3A4F6C;text-decoration: none" href='<?php  echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVentaDestino=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>No. de Cambio</a></span></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDPuntoVenta&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Cliente&nbsp;<?php  if($_GET['order_by']=="IDPuntoVenta"){?><img src="images/<?php echo $img?>" border=0><?php }?></a></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDPuntoVentaDestino&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Punto Venta&nbsp;<?php  if($_GET['order_by']=="IDPuntoVentaDestino"){?><img src="images/<?php echo $img?>" border=0><?php }?></a></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Fecha&nbsp;<?php  if($_GET['order_by']=="Fecha"){?><img src="images/<?php echo $img?>" border=0><?php }?></a></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDEstadoTraslado&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Valor Total&nbsp;<?php  if($_GET['order_by']=="IDEstadoTraslado"){?><img src="images/<?php echo $img?>" border=0><?php }?></a></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><span class="navpic">Excedente</span></td>
					</tr>
					<?php 
							$i = 0;
							while($r = db_fetch_object($result)){
								$class = repetition()?"row1":"row2";
								$i++;
							?>
					<tr>
						<td align=center valign=middle nowrap width=50 class="<?php echo $class?>">
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>&idpunto=<?php echo $r->IDPuntoVenta?>'><img src='images/edit.gif' border='0'></a></td>
						<td nowrap class="<?php echo $class?>"><?php echo $r->IDCambio?></td>
						<td nowrap class="<?php echo $class?>"><?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$r->IDCliente)?></td>
						<td nowrap class="<?php echo $class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta) ?></td>
						<td nowrap class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaCambio,0,10))." ".substr($r->FechaCambio,10) ?></td>
						<td nowrap class="<?php echo $class?>" align="right"><?php echo number_format($r->ValorTotal,0,'','.') ?></td>
						<td nowrap class="<?php echo $class?>" align="right"><?php echo number_format($r->Excedente,0,'','.') ?></td>
					</tr>
					<?php } // END for
							?>
					<tr>
						<td class="navpic" bgcolor=#DBEAF5 colspan=7 nowrap><?php 
										print $pages;
									?></td>
					</tr>
				</table>
			</td>
			<td class="titlemedium"></td>
		</tr>
	</table>
	<?php 
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No hay cambios </b></span>";
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
					<option value="Cliente">Cedula Cliente</option>
					<option value="IDPuntoVenta">Almacen</option>
					<option value="IDCambio">Numero</option>
                    <option value="Referencia">Referencia</option>
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
					<option value="FechaCambio">Fecha</option>
				</select>
				de forma
				<select name="in_order" class="popup">
					<option value="ASC">Ascendente</option>
					<option value="DESC" >Descendente</option>
				</select>
				Listar
				<select name="listar" class="popup">
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="30">30</option>
                    <option value="50" selected>50</option>
				</select>
				<br>
				<input type="hidden" name="mod" value="<?php echo $MOD?>">
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="action" value="list">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 
	}//End function filtrar
?>
