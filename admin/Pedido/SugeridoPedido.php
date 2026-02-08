<body> <?php 

$TitleMod ="SugeridoPedido";

$Table = "SugeridoPedido";
$TableJoin = "DetalleSugeridoPedido";
$Key = "IDSugeridoPedido";
$MOD = "Sugerido";
$m = "Pedido";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;
			
			case "insert" :
				$frm= vars_LOG($HTTP_POST_VARS);
				$id = insert($frm);
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);
				update($frm);
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$HTTP_GET_VARS[action]="";
				delete($ID);
			break;
			case "list" :	
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
		funcion print_form
*******************************************************************************************/
function print_form($id,$newmode,$TitleMod,$submit_option)
{
	
	Global $TitleMod,$MOD,$Table,$Key;
	
	$sql =  "SELECT * FROM $Table WHERE $Key = '$id' ORDER BY Fecha ASC";
	 	
	$query_sugerido = db_query($sql);
	$r_sugerido = db_fetch_object( $query_sugerido );
	
?>
<br><br>

<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;
				<img src=images/folderopen.gif border=0> 
					<a href="./?mod=Sugerido">
						Administrar Pedidos Sugeridos
					</a>
			</td>
			<td></td>
		</tr>
</table>

<br><br>
	<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
		<tr>
			<td class="maintitle" bgcolor="#9daac6"><b><?php echo $TitleMod ?></b></td>
		</tr>
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
						<tr>
							<td class="row1" nowrap>
								<table width=100% cellspacing="1" cellpadding="1" bgcolor=#ffffff>
									<tr>
										<td class=row1>
											Punto de Venta
										</td>
										<td class=row1>
											<input type="text" class="input" name="PuntoVenta" readonly size="24" value="<?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_sugerido->IDPuntoVenta)?>">
											<input type="hidden" name="IDPuntoVenta" value="<?php echo $r_sugerido->IDPuntoVenta?>">
										</td>
										<td class=row1>
											<div align="left">
												
												 Numero.</div>
										</td>
										<td class=row1>
											<input type="text" class="input" name="NumeroPedido" size="24" value="<?php echo $r_sugerido->NumeroSugerido?>">
										</td>
									</tr>
									<tr>
										<td class=row1>Fecha</td>
										<td class=row1>
											<input type="text" class="input" name="Fecha" size="15" value="<?php echo $r_sugerido->Fecha?>" readonly>
											<script language="JavaScript1.2">
												<!--
													if (!document.layers)
														document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
													//-->
											</script>
										</td>
										<td class=row1>Estado </td>
										<td class=row1>
											<input type="text" class="input" name="EstadoPedido" size="24" value="<?php echo get_field("EstadoPedido","Descripcion","IDEstadoPedido",$r_sugerido->IDEstadoPedido)?>">
											<input type="hidden" name="IDEstadoPedido" size="24" value="<?php echo $r_sugerido->IDEstadoPedido?>"></td>
									</tr>
									<tr>
										<td class=row1>Observaciones</td>
										<td colspan="3" class=row1><textarea name="Observaciones" rows="4" cols="64"></textarea></td>
									</tr>
									<tr>
										<td class=row1 colspan="4"></td>
									</tr>
									<tr>
										<td class=titlemedium colspan="4">
											Detalle del Pedido Sugerido
										</td>
									</tr>
									<tr>
										<td class=row2 colspan="4" width="500" >
											<?php verdetallesugerido($id);?>
										</td>
									</tr>
									<tr>
										<td class=row2 colspan="4" align="center">
											<input type="hidden" name="action" value="<?php echo $newmode?>">
											<input type="hidden" name="idsugerido" value="<?php echo $id?>">
											<input type="submit" class="submit" name="submit" value="<?php echo $submit_option?>">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</form>
				</table>
			</td>
		</tr>
	</table>	
<?php 
}// Enf function print_form()				

/*******************************************************************************************
	verdetallesugerido: Muestra el detalle de el pedido sugerido
	Parametros:
			$id : id del detalle sugerido a mostar
	Retorna:	
			Void
*******************************************************************************************/

function verdetallesugerido($id)
{
	
	Global $TitleMod,$MOD,$Table,$Key,$TableJoin;
	
	$sql_referencias =  "SELECT * FROM $TableJoin WHERE $Key = '$id' GROUP BY IDPuntoVentaReferencia";
	
	$query_referencias = db_query( $sql_referencias );
	
	$i=0;

	while( $r_referencias = db_fetch_object( $query_referencias ) )
	{
		
		echo "<br><table widh='100%' class='bordertable' align='center' >";
		
		$sql_detalle =  "SELECT * FROM $TableJoin WHERE $Key = '$id' AND IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia'";
		$query_detalle = db_query($sql_detalle);
		$rows_detalle = db_num_rows($query_detalle);
		
		while($r_detalle[$i] = db_fetch_array($query_detalle))
		{
			$i++;
		} //end while($r[$i] = db_fetch_array($query_detalle))
		
		$i = 0;
		//print_r($r);
		
		//REALIZAR EL QUERY PARA VER LA CODIFICACION ESPECIFICA DE LA REFERENCIA
		
		$sql_codificacion = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia' GROUP BY IDCodificacionEspecifica";
		$query_codificacion = db_query( $sql_codificacion );
		
		while($r_codificacion[$i] = db_fetch_array($query_codificacion))
		{
			$i++;
		} //end while($r_codificacion[$i] = db_fetch_array($query_codificacion))
		
		$i = 0;
		
	?>
		
			<tr>
				<td class=rowform align=center>
				<?php 
					echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$r_referencias->IDPuntoVentaReferencia));	
				?>
				</td>
				<?php 
					foreach($r_detalle as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=rowform align=center>".get_field("Talla","Descripcion","IDTalla",$talla[IDTalla])."</td>";
					}//end foreach($r_detalle as $talla)
				?>
				<td class=rowform align=center>
					<a href="./?mod=<?php echo $MOD?>&action=delref&idref=<?php echo $r_referencias->IDPuntoVentaReferencia?>&idsugerido=<?php echo $id?>" title="Quitar Item">
						<img src="images/trash.gif" border="0">
					</a>
				</td>	
			</tr>
			
			<tr>
				<td class=rowform align=center>
					Existencias
				</td>
				<?php 
					foreach($r_codificacion as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=row1 align=center>".$talla[Existencias]."</td>";
					}//end foreach($r_detalle as $talla)
				?>
				<td class=row1 align=center>
				</td>	
			</tr>
			
			<tr>
				<td class=rowform align=center>
					M&aacute;ximo
				</td>
				<?php 
					foreach($r_codificacion as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=row1 align=center>".$talla[Maximo]."</td>";
					}//end foreach($r_detalle as $talla)
				?>
				<td class=row1 align=center>
				</td>	
			</tr>
			
			<tr>
				<td class=rowform align=center>
					Minimo
				</td>
				<?php 
					foreach($r_codificacion as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=row1 align=center>".$talla[Minimo]."</td>";
					}//end foreach($r_detalle as $talla)
				?>
				<td class=row1 align=center>
				</td>	
			</tr>
			
			<tr>
				<td class=rowform align=center>
					Pedido
				</td>
				<?php 
					foreach($r_codificacion as $talla)
					{
						
						if(!empty($talla[IDTalla]))
						{
						
							echo "<td class=row1 align=center>".vercantidadpedida( $talla[IDPuntoVentaReferencia],$talla[IDTalla] )."</td>";
							
						}//end if(!empty($talla[IDTalla]))
					
					}//end foreach($r_detalle as $talla)
				?>
				<td class=row1 align=center>
				</td>	
			</tr>
			
			<tr>
				<td class="rowform" align=center>
					SUGERIDO
				</td>
				<?php 
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						echo "<td class=row1 align=center><input type=text size=5 value=".$talla[Cantidad]." name=".get_field("Referencia","Numero","IDReferencia",$r_referencias->IDReferencia)."[$talla[IDTalla]]>";
						//SE IMPRIME UN HIDDEN CON EL ID DE LAS TALLA
						//echo "<input type=hidden value=".$talla[IDTalla]." name=Talla".get_field("Referencia","Numero","IDReferencia",$r_referencias->IDReferencia)."[$talla[IDTalla]]></td>";
					}
				}
				?>
				<td class=row1 align=center>
					
				</td>	
			</tr>
	<?php 
	
	$r_detalle = array();
	$r_codificacion = array();
	
	echo "</table>";
	
	}//end while( $r_referencias = db_fetch_object( $query_referencias ) )
	?>

<?php 
}// end function verdetallesugerido($id)


/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY $Key";
	 	
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
							
		if($rows > 0){
?>		
<br>
<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
<?php filtrar();?>	
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
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Fecha&nbsp;<?php  if($_GET['order_by']=="Fecha"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDPuntoVenta&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Punto de Venta<?php  if($_GET['order_by']=="IDPuntoVenta"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NumeroSugerido&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">NumeroSugerido&nbsp;<?php  if($_GET['order_by']=="NumeroSugerido"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Estado&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Estado&nbsp;<?php  if($_GET['order_by']=="Estado"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Publicar&nbsp;<?php  if($_GET['order_by']=="Publicar"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=".$MOD."&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class=row1><?php echo $r->Fecha ?></td>
						<td nowrap class=row1><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?></td> <td nowrap class=row1><?php echo $r->NumeroSugerido ?></td>
						<td nowrap class=row1><?php echo get_field("EstadoPedido","Descripcion","IDEstadoPedido",$r->IDEstadoPedido )?></td>
						<td nowrap class=row1><?php echo $r->Publicar ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=7 nowrap>
	<?php 
		print $pages;
		?>
</td>
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
	<form name="frm" action="./" method="get" onsubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="FechaInicio">Fecha Publicacion</option>
					<option value="Nombre">Nombre</option>
					<option value="Contacto">Contacto</option>
					<option value="Lugar">Lugar</option>
					<option value="Hora">Hora</option>
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
					<option value="FechaInicio">Fecha Publicacion</option>
					<option value="Descripcion">Descripcion</option>
					<option value="Contacto">Contacto</option>
					<option value="Lugar">Lugar</option>
					<option value="Hora">Hora</option>
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
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Gerencia">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 		
	}//End function filtrar
?>
