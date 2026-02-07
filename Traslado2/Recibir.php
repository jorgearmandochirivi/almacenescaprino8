<body> <?php

$TitleMod ="Recibir Traslado";

$Table = "Traslado";
$TableJoin = "DetalleTraslado";
$Key = "IDTraslado";
$MOD = "RecibirTraslado2";
$m = "Traslado";


		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($_POST);
				//print_r( $frm );
                                
				db_query("SET AUTOCOMMIT = 0");
				db_query("BEGIN");
				
				$sql_update = " SELECT IDEstadoTraslado FROM $Table WHERE IDTraslado = '$frm[IDTraslado]' AND IDPuntoVentaOrigen = '$frm[IDPuntoVentaOrigen]' ";
				$qry_update = db_query( $sql_update );
				$r_update = db_fetch_object( $qry_update );
				
				//echo $r_update->IDEstadoTraslado;
				
				if( $r_update->IDEstadoTraslado == "2" )
				{
					echo "<script>alert( 'Este traslado ya fue rebido' );location.href='?mod=GenerarFactura';</script>";
					exit;
				}//end if
				//print_r( $frm );
				//exit;
				
				update($frm);
				
				
				//echo "Estado ".$IDEstadoTraslado;
				//echo "<br>";
				//print_r( $IDCodificacionEspecifica  );
				
				//Actualizar Inventario si el estado viene en 2
				//print_r( $IDCodificacionEspecifica );
                               
				if( $IDEstadoTraslado == 2  )
				{
					foreach( $IDCodificacionEspecifica as $key => $valor )
					{
					
						//Actualizar Origen
						$existencias = get_field("CodificacionEspecifica","Existencias","IDCodificacionEspecifica",$valor);
						$existencias = $existencias -  $Cantidad[$key];
						
						 $sql_actualizaorigen =  "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '$valor' ";
						//echo "<br>";
						
						db_query($sql_actualizaorigen);
						
						//INSERTAR EL LOG
						insertlog($ID_Usuario,"CodificacionEspecifica",$valor,"Actualizar",$sql_actualizaorigen);
						
						//Actualizar Destino
						$idcodifcacion = get_field("CodificacionEspecifica","IDCodificacionEspecifica","IDPuntoventaReferencia",get_field("PuntoVentaReferencia","IDPuntoVentaReferencia","IDReferencia", $IDReferencia[$key]."' AND IDPuntoVenta = '$IDPuntoVenta")."' AND IDTalla = '$Talla[$key] ");
						
						if( empty( $idcodifcacion ) )
						{
							echo "<br><br><span class='rojo'>Hay referencias en el traslado que no est&aacute;n asignadas al punto de venta de destino, el traslado no se complet&oacute;. <br>Contacte al administrador para realizar este traslado.</span><br><br>";
							db_query("ROLLBACK");
							list_r();
							exit;
						}//end if
						
						/*
						echo $sql_codificacion = " SELECT C.IDCodificacionEspecifica FROM CodificacionEspecifica C, PuntoVentaReferencia P 
												WHERE P.IDReferencia = '".$IDReferencia[$key]."'
												AND P.IDPuntoVenta = '$IDPuntoVenta'
												AND P.IDPuntoVentaReferencia = C.IDPuntoVentaReferencia 
												AND C.IDTalla = '".$Talla[$key]."'";
						$qry_codificacion = db_query( $sql_codificacion );
						$r_codificacion = db_fetch_object( $qry_codificacion );
						$idcodifcacion = $r_codificacion->IDCodificacionEspecifica;
						*/
						$existencias = get_field("CodificacionEspecifica","Existencias","IDCodificacionEspecifica",$idcodifcacion);
						$existencias = $existencias + $Cantidad[$key];
						 $sql_actualizadestino =  "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '$idcodifcacion' ";
					//	echo "<br>";
						db_query($sql_actualizadestino);
						
						//INSERTAR EL LOG
						insertlog($ID_Usuario,"CodificacionEspecifica",$idcodifcacion,"Actualizar",$sql_actualizadestino);
												
					}//end foreach( $IDCodificacionEspecifica as $key => $valor )
				
				}//end if
				//db_query( "tales" );
				db_query("COMMIT");
				
				echo "<script>location.href='?mod=GenerarFactura';</script>";
				
			break;
			
			
			case "list" :
				if($field == "IDPuntoVentaOrigen1")
				{
					$sql = "SELECT T.* FROM Traslado T,PuntoVenta P WHERE T.IDPuntoVentaDestino = '$IDPuntoVenta' AND T.IDPuntoVentaOrigen = P.IDPuntoVenta AND P.Nombre LIKE '%$QryString%' AND T.IDEstadoTraslado = '1' ";
				}else	
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
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta,$TableJoin,$idpuntoorigen;
	
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' AND IDPuntoVentaOrigen = '$idpuntoorigen' ");
		
	$r = db_fetch_object($qid);
?>


<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="580">
	
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

<FORM name="frm" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" onSubmit="disable( this );" >
<table class="forumline" width="580" cellspacing="1" border="0" align="center">
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
														<td class=col1>Origen </td>
														<td class=col2 >
															<?php
																echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaOrigen);
															?><input type="hidden" value="<?=$r->IDPuntoVentaOrigen?>" name="IDPuntoVentaOrigen"></td>
														<td class=col1>Fecha </td>
														<td class=col2 ><input type="text" class="tbox" name="Fecha" size="19" value='<?=$r->Fecha?>' readonly>
															
															<input type="hidden" value="<?=$r->IDPuntoVentaDestino?>" name="IDPuntoVentaDestino"></td>
													</tr>
													<tr>
														<td class=col1>Estado</td>
														<td class=col2 colspan="3"><?php echo formpopup("EstadoTraslado","Descripcion","Descripcion","IDEstadoTraslado",$r->IDEstadoTraslado,"input\" id=\"Estado"); ?></td>
													</tr>
													<tr>
														<td class=col1 >Observaciones</td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?=$r->Observaciones?></textarea></td>
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
											<td class=navpic colspan="4"><b>Detalle Traslado</b></td>
										</tr>
										<tr bgcolor=#e7ebef>
											<td colspan="4">
												<table class="texto" border="0" cellspacing="1" cellpadding="0" width="100%" id=table1>
													<tr bgcolor="#dfe3e7">
														<td align="center"><b>Item</b><b></b></td>
														<td align="center"><b>Referencia</b></td>
														<td align="center"><b>Talla</b></td>
														<td align="center"><b>Nombre</b></td>
														<td align="center"><b>Cantidad</b></td>
													</tr>
													<?php
														$sql_detalle = " SELECT * FROM $TableJoin WHERE $Key = '$r->IDTraslado' AND IDPuntoVentaOrigen = '$r->IDPuntoVentaOrigen' ";
														$query_detalle = db_query($sql_detalle);
														$i = 0;
														while( $r_detalle = db_fetch_object( $query_detalle ) )
														{
															$class = repetition()?"col1list":"col2list";
															$i++;
															$PuntoVentaReferencia = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);
															$Talla = get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);;
													?>
													<tr >
														<td class="<?=$class?>">
															<b><?=$i?></b>
															<input type="hidden" name="IDCodificacionEspecifica[]" value="<?=$r_detalle->IDCodificacionEspecifica?>">
															<input type="hidden" name="Talla[]" value="<?=$Talla?>">
															<input type="hidden" name="Cantidad[]" value="<?php echo $r_detalle->Cantidad ?>">
															<input type="hidden" name="IDReferencia[]" value="<?=get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)?>">

														</td>
														<td class="<?=$class?>">
															<?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)); ?></td>
														<td class="<?=$class?>">
															<?php echo get_field("Talla","Descripcion","IDTalla",$Talla) ?>
														</td>
														<td class="<?=$class?>">
															<?php echo get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)); ?>
														</td>
														<td class="<?=$class?>">
															<?php echo $r_detalle->Cantidad ?>
														</td>
													</tr>
													<?php
														}//end while
													?>
												</table>
											</td>
										</tr>
									</table>
									<input type="hidden" name="action" value="<?=$newmode?>">
									<input type="hidden" name="ID" value="<?=$r->$Key?>">
									<input type="hidden" name="IDTraslado" value="<?=$r->$Key?>">
									<input type="submit" class="button" name="submit" value="<?=$submit_caption?>"></div>
							
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
	 	$sql =  "SELECT * FROM $Table WHERE IDPuntoVentaDestino = '$IDPuntoVenta' AND IDEstadoTraslado = '1' ORDER BY Fecha DESC";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=10;
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
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVentaDestino=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=IDPuntoVentaOrigen&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Almacen de Origen&nbsp;<?php if($_GET['order_by']=="IDPuntoVentaOrigen")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVentaDestino=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=IDPuntoVentaDestino&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Almacen de Destino&nbsp;<?php if($_GET['order_by']=="IDPuntoVentaDestino")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVentaDestino=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Fecha&nbsp;<?php if($_GET['order_by']=="Fecha")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVentaDestino=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=IDEstadoTraslado&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Estado&nbsp;<?php if($_GET['order_by']=="IDEstadoTraslado")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
								</tr>
							
							<?php
							$i = 0;
							while($r = db_fetch_object($result)){
								$class = repetition()?"col1list":"col2list";
								$i++;
							?>
							  	
							<tr>
								<td align=center valign=middle nowrap width=50 class="<?=$class?>">
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>&idpuntoorigen=<?php echo $r->IDPuntoVentaOrigen; ?>'><img src='images/edit.gif' border='0'></a>
								</td>
									<td nowrap class="<?=$class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaOrigen)?></td>
									<td nowrap class="<?=$class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaDestino) ?></td>
									<td nowrap class="<?=$class?>"><?php echo formatofecha(substr($r->Fecha,0,10))." ".substr($r->Fecha,10) ?></td>
									<td nowrap class="<?=$class?>"><?php echo get_field("EstadoTraslado","Descripcion","IDEstadoTraslado",$r->IDEstadoTraslado) ?></td>
								</tr>
							<?php } // END for
							?>
							<tr>
							<td class="navpic" bgcolor=#DBEAF5 colspan=5 nowrap>
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
	echo "<br><br><span class=subtitle><b>No hay traslados pendientes </b></span>";
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
					<option value="IDPuntoVentaOrigen1">Almacen de Origen</option>
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
					<option value="Fecha">Fecha</option>
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
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="IDPuntoVentaDestino" value="<?=$IDPuntoVenta?>">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
?>
