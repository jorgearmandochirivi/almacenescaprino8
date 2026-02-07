<body> <?

$TitleMod ="Entrada de Pedidos";

$Table = "OrdenCompra";
$TableJoin = "DetalleOrdenCompra";
$Key = "IDOrdenCompra";
$MOD = "Movimiento";
$m = "Movimientos";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form($id,"insert","Actualizar $TitleMod","Realizar Movimiento");
			break;
			
			case "insert" :
				$frm= vars_LOG($HTTP_POST_VARS);
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				$frm['ID'] = insert_width_table($frm,"Movimiento","IDMovimiento");
				entradapedido($frm);
				
				db_query("COMMIT");
				
				echo "<script>location.href='?mod=GenerarFactura';</script>";
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");
			break ;
			case "update" :
				entradapedido($HTTP_POST_VARS);
				echo "<script>location.href='?mod=Movimiento';</script>";
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
				if(empty($Orden))
					list_r();
				else
				{
					$sql = "SELECT * FROM OrdenCompra WHERE NumeroOrden = '$Orden' AND IDEstadoPedido <> '3' AND IDEstadoPedido <> '2' ";
					list_r($sql);
				}
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");

/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption)
{
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IDPuntoVenta;
	if($newmode == "insert")
	{
		$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' AND IDPuntoVenta = '$IDPuntoVenta' ");
	}
	else
	{
		$qid = db_query(" SELECT * FROM Movimiento WHERE IDMovimiento = '$id' AND IDPuntoVenta = 'IDPuntoVenta' ");
	}
		
	$r = db_fetch_object($qid);
?>
	<script>
var Check = new Array('IDMovimiento','IDTipoMovimiento','Remision','FechaRemision','IDOrdenCompra','Fecha','Estado','Publicar');
</script>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">
	
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
			</td>
			<td class="tbtbot"><b></b>
				<span class="gen">
					<? echo $TitleMod." - ".$info ?>
				</span>
			</td>
			<td class="tbtr">
				<img src="images/spacer.gif" alt="" width="124" height="22" />
			</td>
		</tr>
	</table>
	
	<table class="forumline" width="550" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
					<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
						<tr>
							<td class="row1" nowrap>
								<table width=100% cellspacing="1" cellpadding="1" bgcolor=#ffffff>
									<tr>
										<td class=col1>Numero.Orden</td>
										<td class=col2><input type="text" class="input" name="NumeroOrden" readonly size="24" value="<?=$r->NumeroOrden?>"></td>
										<td class=col1>Remisi&oacute;n</td>
										<td class=col2><input type="text" class="input" name="Remision" size="24" value="<?=$r->Remision?>"></td>
									</tr>
									<tr>
										<td class=col1>Fecha Remisi&oacute;n</td>
										<td class=col2>
											<input type="text" class="input" name="FechaRemision" size="15" value="<?=fecha()." ".hora()?>" readonly>
											<script language="JavaScript1.2">
												<!--
													if (!document.layers)
														document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaRemision,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
												//-->
											</script>
											
											
											
											<input type="hidden" name="Fecha" value="<?=fecha()?>"><input type="hidden" name="IDPuntoVenta" value="<?=$r->IDPuntoVenta;?>"></td>
										<td class=col1>Tipo de Movimiento</td>
										<td class=col2><input type="text" class="input" name="TMovimiento" readonly size="24" value="Ingreso Contra Pedido"><input type="hidden" name="IDTipoMovimiento" value="<?if($newmode == "insert") echo "1"; else echo  $r->IDTipoMovimiento;?>"><br>
										</td>
									</tr>
									<tr>
										<td class=col1>Empleado</td>
										<td class=col2><input type="text" class=input name="Empleado" value="<?if($newmode == "insert") echo $ID_Usuario; else echo  $r->IDEmpleado;?>" readonly></td>
										<td class=col1></td>
										<td class=col1></td>
									</tr>
									<tr>
										<td class=col1>Observaciones</td>
										<td colspan="3" class=col2><textarea name="Observaciones" rows="4" cols="64"><?=$r->Observaciones?></textarea></td>
									</tr>
									<tr>
										<td class=col1 colspan="4"></td>
									</tr>
									<tr>
										<td class=navpic colspan="3">Detalle Movimiento										</td>
										<td class=navpic align=right>	
											
										</td>	
									</tr>
									<tr>
										<td class=row1 colspan="4">
											<?verdetallemovimiento($r->IDOrdenCompra);?>
										</td>
									</tr>
									<tr>
										<td class=col1 colspan="4" align="center">
											<input type="hidden" name="action" value="<?=$newmode?>">
											<input type="hidden" name="ID" value="<?=$id?>">
											<input type="hidden" name="IDOrdenCompra" value="<?=$r->IDOrdenCompra?>"><input type="hidden" name="IDEmpleado" value="<?if($newmode == "insert") echo $ID_Usuario; else echo  $r->IDEmpleado;?>">
											<input type="submit" class="submit" name="submit" value="<?=$submit_caption?>">
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
	<?
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;
	if(empty($sql))
	 	$sql =  "SELECT * FROM OrdenCompra WHERE IDEstadoPedido <> '3' AND IDEstadoPedido <> '2' AND IDPuntoVenta = '$IDPuntoVenta' ORDER BY FechaOrden ASC";
	 	
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
							
							?><?
	if($rows > 0){
?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">
	
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
			</td>
			<td class="tbtbot"><b></b>
				<span class="gen">
					<? echo $TitleMod." - ".$info ?>
				</span>
			</td>
			<td class="tbtr">
				<img src="images/spacer.gif" alt="" width="124" height="22" />
			</td>
		</tr>
	</table>
	
	
	<table class="forumline" width="550" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<?
			filtrar();
		?>
	</td>
	</tr>
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
					<tr>
					<td align=center class=navpic valign=middle nowrap bgcolor=#DBEAF5>Crear</td>
						<td class=navpic nowrap bgcolor=#DBEAF5><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDPuntoVenta&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Punto de Venta</a><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDPuntoVenta&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>&nbsp;<% if($_GET['order_by']=="IDPuntoVenta"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NumeroOrden&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Numero de Orden</a><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NumeroOrden&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>&nbsp;<% if($_GET['order_by']=="NumeroOrden"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
					<td class=navpic nowrap bgcolor=#DBEAF5><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NumeroOrden&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Fecha de Orden</a><a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=FechaOrden&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">&nbsp;<% if($_GET['order_by']=="FechaOrden"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDEstadoPedido&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Estado Pedido</a><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDEstadoPedido&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'><% if($_GET['order_by']=="IDEstadoPedido"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
					</tr>
	
				<? 
				while($r = db_fetch_object($result)){
				
					$class = repetition()?"col1list":"col2list";
				?>
	  	
					<tr>
						<td align=center valign=middle nowrap width=50 class="<?=$class?>">
							&nbsp;<a href='<? echo "?mod=$MOD&action=add&id="; echo $r->$Key; ?>'  title="Crear Movimiento"><img src='images/edit.gif' border='0'></a>
						</td>
						<td nowrap class="<?=$class?>"><?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta)?></td>
						<td nowrap class="<?=$class?>"><? echo $r->NumeroOrden?></td> <td nowrap class="<?=$class?>"><? echo $r->FechaOrden?></td>
						<td nowrap class="<?=$class?>"><? echo get_field("EstadoPedido","Descripcion","IDEstadoPedido",$r->IDEstadoPedido); ?></td>
					</tr>
				<? } // END for
				?>
					<tr>
						<td class=col1 bgcolor=#DBEAF5 colspan=5 nowrap>
							<?
								print $pages;
							?>
						</td>
					</tr>		
				</table>
			</td>
		</tr>
	</table>
	<? 			
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
					<option value="NumeroOrden">NumeroOrden</option>
					<option value="FechaOrden">FechaOrden</option>
					<option value="IDEstadoPedido">IDEstadoPedido</option>
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
					<option value="NumeroOrden">NumeroOrden</option>
					<option value="FechaOrden">FechaOrden</option>
					<option value="IDEstadoPedido">IDEstadoPedido</option>
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
				<input type="hidden" name="rangofield" value="FechaOrden">
				<input type="hidden" name="action" value="list">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?		
	}//End function filtrar
	
/*******************************************************************************************
	verdetallemovimiento: Muestra el detalle de el Movimiento de Entrada Contra Pedido
	Parametros:
			$id : id del Pedido a Mostrar
	Retorna:	
			Void
*******************************************************************************************/

function verdetallemovimiento($id)
{
	
	Global $TitleMod,$MOD,$Table,$Key,$TableJoin;
	
	$sql_referencias =  "SELECT * FROM $TableJoin WHERE $Key = '$id' GROUP BY IDPuntoVentaReferencia";
	
	$query_referencias = db_query( $sql_referencias );
	
	$i=0;
?>
	<table width=97% cellpadding=1 cellspacing=1 align=center bgcolor=#DEE3E7>
<?
	while( $r_referencias = db_fetch_object( $query_referencias ) )
	{
		
		$sql_detalle =  "SELECT * FROM $TableJoin WHERE $Key = '$id' AND IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia'";
		$query_detalle = db_query($sql_detalle);
		$rows_detalle = db_num_rows($query_detalle);
		
		while($r_detalle[$i] = db_fetch_array($query_detalle))
		{
			$i++;
		} //end while($r[$i] = db_fetch_array($query_detalle))
		
		$i = 0;
		//print_r($r);
		
		//Query Para ver la codificacion especifica
		
		
		$sql_codificacion =  "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia'";
		$query_codificacion = db_query($sql_codificacion);
		$rows_codificacion = db_num_rows($query_codificacion);
		
		while($r_codificacion[$i] = db_fetch_array($query_codificacion))
		{
			$i++;
		} //end while($r[$i] = db_fetch_array($query_codificacion))
		
		$i = 0;
		//print_r($r);
		
?>
		
			
			
			<tr>
				<td  align=center>
					<b>
				<?
					echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$r_referencias->IDPuntoVentaReferencia));	
				?>
					</b>
				</td>
				<?
					foreach($r_detalle as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td  align=center>".get_field("Talla","Descripcion","IDTalla",$talla[IDTalla])."</td>";
					}//end foreach($r_detalle as $talla)
				?>
			</tr>
			
			<tr>
				<td class="col2list" align=center>
					CANTIDAD PEDIDA
				</td>
				<?
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						echo "<td class=col2list align=center><input type=text readonly size=5 value=".$talla[Cantidad]." name=ref".$r_referencias->IDPuntoVentaReferencia."[$talla[IDTalla]]>";
					}
				}
				?>
			</tr>
			
			<tr>
				<td class="col2list" align=center>
					CANTIDAD RECIBIDA
				</td>
				<?
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						
						$sql_cantidadrecibida  = "SELECT SUM(DM.Cantidad) as CantidadRecibida FROM DetalleMovimiento DM,Movimiento M, Ordencompra O ";
						$sql_cantidadrecibida .= "WHERE O.IDOrdenCompra = M.IDOrdenCompra AND O.IDOrdenCompra = '$talla[IDOrdenCompra]' ";
						$sql_cantidadrecibida .= "AND M.IDMovimiento = DM.IDMovimiento AND DM.IDPuntoVentaReferencia = '$talla[IDPuntoVentaReferencia]' AND DM.IDTalla = '$talla[IDTalla]'";
												
						$query_cantidadrecibida = db_query( $sql_cantidadrecibida );
						
						$r_cantidadrecibida = db_fetch_object( $query_cantidadrecibida );
						
						echo "<td class=col2list align=center><input type=text size=5 readonly value='".$r_cantidadrecibida->CantidadRecibida."' name='CantidadRecibida[$talla[IDTalla]]'>";
					}
				}
				?>
			</tr>
			
			<tr>
				<td class="col2list" align=center>
					INGRESO
				</td>
				<?
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						echo "<td class=col2list align=center><input type=text size=5 value=".$talla[Cantidad]." name=".$r_referencias->IDPuntoVentaReferencia."[$talla[IDTalla]]>";
						//SE IMPRIME UN HIDDEN CON EL ID DE LAS TALLA
						//echo "<input type=hidden value=".$talla[IDTalla]." name=Talla".get_field("Referencia","Numero","IDReferencia",$r_referencias->IDReferencia)."[$talla[IDTalla]]></td>";
					}
				}
				?>
			</tr>
		
	<?
	
	$r_detalle = array();
	$r_codificacion = array();
	
	}//end while( $r_referencias = db_fetch_object( $query_referencias ) )
	?>
	</table>
<?
}// end function verdetallepedido($id)
?>