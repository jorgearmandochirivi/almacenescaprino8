<body> <?

$TitleMod ="Ver Traslados";

$Table = "Traslado";
$TableJoin = "DetalleTraslado";
$Key = "IDTraslado";
$MOD = "RecibirTraslado";
$m = "Traslado";




$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;

			case "list" :

			if(!empty($_GET["limit1"]) && !empty($_GET["limit2"])) {
					$condicion= "and  Fecha >= '".$_GET["limit1"]."' and Fecha <= '".$_GET["limit2"]."' ";
			}


				if($field == "IDPuntoVentaOrigen1")
				{
					$sql = "SELECT T.* FROM Traslado T,PuntoVenta P WHERE T.IDPuntoVentaOrigen = P.IDPuntoVenta AND P.Nombre LIKE '%$QryString%' ".$condicion." Order by IDTraslado DESC";
				}
				elseif($field == "IDPuntoVentaDestino1")
				{
					$sql = "SELECT T.* FROM Traslado T,PuntoVenta P WHERE T.IDPuntoVentaDestino = P.IDPuntoVenta AND P.Nombre LIKE '%$QryString%' ".$condicion." Order by IDTraslado DESC";
				}
				elseif($field == "Referencia")
				{
					$sql_ref = "Select IDCodificacionEspecifica
								From Referencia R, PuntoVentaReferencia PVR, CodificacionEspecifica CE
								Where R.IDReferencia = PVR.IDReferencia
								and CE.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
								and R.Numero like '%".$_GET["QryString"]."%' Order by IDCodificacionEspecifica DESC";
					$result_ref = db_query($sql_ref);
					while($row_ref = db_fetch_array($result_ref)):
						$array_codif[]= $row_ref["IDCodificacionEspecifica"];
					endwhile;
					if(count($array_codif)>0):
						$id_ref = implode(",",$array_codif);
					else:
						$id_ref = 0;
					endif;
					$sql = "SELECT T.*
									FROM Traslado T,DetalleTraslado DT
									WHERE T.IDTraslado = DT.IDTraslado ".$condicion." and DT.IDCodificacionEspecifica in (".$id_ref.")
									ORDER BY IDTraslado DESC";

				}
				elseif(!empty($_GET["limit1"]) && !empty($_GET["limit2"])) {
						$condicion= "and  Fecha >= '".$_GET["limit1"]."' and Fecha <= '".$_GET["limit2"]."' ";
						$sql = "SELECT T.* FROM Traslado T,DetalleTraslado DT WHERE T.IDTraslado = DT.IDTraslado ".$condicion." Order by IDTraslado DESC";
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

	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' AND IDPuntoVentaOrigen = '$idpunto' ");

	$r = db_fetch_object($qid);
?>

<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
			<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
			<td></td>
		</tr>
</table><br>



<FORM name="frm" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" >
<table class="bordertable" width="580" cellspacing="1" border="0" align="center">
	<tr>

		<td class="maintitle"><b></b>
				<?=$title?>
		</td>
	</tr>
	<tr>
	<td width="100%">
		<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff" >

				<tr >
					<td colspan="2" width="100%">

								<div align="center">
									<table width="100%" border=0 align="center">
										<tr>
											<td colspan="4">
												<table class=rowtable width="100%">
													<tr>
														<td class=row1>Origen </td>
														<td class=row2 >
															<?
																echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaOrigen);
															?>
														</td>
														<td class=row1>Destino </td>
														<td class=row2 >
															<?
																echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaDestino);
															?>
														</td>
													</tr>
													<tr>
														<td class=row1>Estado</td>
														<td class=row2 ><? echo get_field("EstadoTraslado","Descripcion","IDEstadoTraslado",$r->IDEstadoTraslado); ?></td>
														<td class=row1>Fecha </td>
														<td class=row2 ><input type="text" class="tbox" name="Fecha" size="19" value='<?=$r->Fecha?>' readonly>

															</td>
													</tr>
													<tr>
														<td class=col1>Quien lo pide?</td>
														<td class=col2 colspan="3"><input type="text" name="QuienPide" id="QuienPide" value="<?php echo$r->QuienPide; ?>" ></td>
													</tr>
													<tr>
														<td class=row1 >Observaciones</td>
														<td class=row2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?=$r->Observaciones?></textarea></td>
													</tr>
													<tr>
													  <td class=row1 >Realizado por</td>
													  <td class=row2 colspan="3"><?php echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado) . " " . get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado); ?></td>
												  </tr>
												</table>
											</td>
										</tr>
										<tr>
											<td colspan="4" class=titlemedium>Detalle Traslado</td>

										</tr>
										<tr bgcolor=#e7ebef>
											<td colspan="4">
												<table class="texto" border="0" cellspacing="1" cellpadding="0" width="100%" id=table1>
													<tr bgcolor="#dfe3e7">
														<td class="rowform" align="center"><b>Item</b><b></b></td>
														<td class="rowform" align="center"><b>Referencia</b></td>
														<td class="rowform" align="center"><b>Talla</b></td>
														<td class="rowform" align="center"><b>Nombre</b></td>
														<td class="rowform" align="center"><b>Cantidad</b></td>
														<td class="rowform" align="center"><b>Numero</b></td>
													</tr>
													<?
														$sql_detalle = " SELECT * FROM $TableJoin WHERE $Key = '$r->IDTraslado' AND IDPuntoVentaOrigen = '$r->IDPuntoVentaOrigen' ";
														$query_detalle = db_query($sql_detalle);
														$i = 0;
														while( $r_detalle = db_fetch_object( $query_detalle ) )
														{
															$class = repetition()?"row1":"row2";
															$i++;
															$PuntoVentaReferencia = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);
															$Talla = get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);;
													?>
													<tr >
														<td class="<?=$class?>">
															<b><?=$i?></b>
															<input type="hidden" name="IDCodificacionEspecifica[]" value="<?=$r_detalle->IDCodificacionEspecifica?>">
															<input type="hidden" name="Talla[]" value="<?=$Talla?>">
															<input type="hidden" name="IDReferencia[]" value="<?=get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)?>">

														</td>
														<td class="<?=$class?>">
															<? echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)); ?></td>
														<td class="<?=$class?>">
															<? echo get_field("Talla","Descripcion","IDTalla",$Talla) ?>
														</td>
														<td class="<?=$class?>">
															<? echo get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)); ?>
														</td>
														<td class="<?=$class?>">
															<? echo $r_detalle->Cantidad ?>
														</td>
														<td class="<?=$class?>">
															<? echo $r_detalle->NumeroTarjeta ?>
														</td>
													</tr>
													<?
														}//end while
													?>
												</table>
											</td>
										</tr>
									</table>


					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>
</FORM>
<?
} // END function print_form_fotos($id,$numfotos)
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;

		/****** EstadoTraslado = Enviado = 1 *********/

	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY IDEstadoTraslado ASC, Fecha DESC";

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

							?><?
		if($rows > 0){
?><table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
			<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
			<td></td>
		</tr>
</table><br>
	<table border="0" cellpadding="0" cellspacing="0" class="bordertable" align="center" width="650">

	<tr>
			<td class="maintitle" width="100%">
			Listar <? echo $TitleMod ?>
			<? echo $info ?>
		</td>
		</tr>
	<tr>
			<td  width="100%">
			<?filtrar();?>
		</td>
		</tr>
		<tr>
			<td width="100%">
				<table width=100% border=0 cellspacing=1 cellpadding=0>
					<tr>
						<td align=center class="titlemedium" valign=middle  width=69>Ver</td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><span class="navpic"><a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVentaDestino=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>No. de Traslado</a></span></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDPuntoVentaOrigen&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Almacen de Origen&nbsp;<% if($_GET['order_by']=="IDPuntoVentaOrigen"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDPuntoVentaDestino&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Almacen de Destino&nbsp;<% if($_GET['order_by']=="IDPuntoVentaDestino"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Fecha&nbsp;<% if($_GET['order_by']=="Fecha"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDEstadoTraslado&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Estado&nbsp;<% if($_GET['order_by']=="IDEstadoTraslado"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class="titlemedium" nowrap bgcolor=#DBEAF5><span class="navpic">Fecha Recibido</span></td>
					</tr>
					<?
							$i = 0;
							while($r = db_fetch_object($result)){
								$class = repetition()?"row1":"row2";
								$i++;
							?>
					<tr>
						<td align=center valign=middle nowrap width=50 class="<?=$class?>">
								&nbsp;<a href='<? echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>&idpunto=<%=$r->IDPuntoVentaOrigen%>'><img src='images/edit.gif' border='0'></a></td>
						<td nowrap class="<?=$class?>"><? echo $r->IDTraslado?></td>
						<td nowrap class="<?=$class?>"><? echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaOrigen)?></td>
						<td nowrap class="<?=$class?>"><? echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaDestino) ?></td>
						<td nowrap class="<?=$class?>"><? echo formatofecha(substr($r->Fecha,0,10))." ".substr($r->Fecha,10) ?></td>
						<td nowrap class="<?=$class?>"><? echo get_field("EstadoTraslado","Descripcion","IDEstadoTraslado",$r->IDEstadoTraslado) ?></td>
						<td nowrap class="<?=$class?>"><? if ($r->FechaTrEd!="0000-00-00 00:00:00") echo formatofecha(substr($r->FechaTrEd,0,10))." ".substr($r->FechaTrEd,10) ?></td>
					</tr>
					<? } // END for
							?>
					<tr>
						<td class="navpic" bgcolor=#DBEAF5 colspan=7 nowrap><?
										print $pages;
									?></td>
					</tr>
				</table>
			</td>
			<td class="titlemedium"></td>
		</tr>
	</table>
	<?
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No hay traslados pendientes </b></span>";
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
					<option value="IDPuntoVentaOrigen1">Almacen de Origen</option>
					<option value="IDPuntoVentaDestino1">Almacen de Destino</option>
					<option value="EstadoTraslado.Descripcion">Estado</option>
                    <option value="IDTraslado">Numero</option>
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
					<option value="Fecha">Fecha</option>
				</select>
				de forma
				<select name="in_order" class="popup">
					<option value="ASC">Ascendente</option>
					<option value="DESC" selected>Descendente</option>
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
				<input type="hidden" name="mod" value="<?=$MOD?>">
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="EstadoTraslado">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?
	}//End function filtrar
?>
