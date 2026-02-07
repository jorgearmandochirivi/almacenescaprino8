<body> <?

$TitleMod ="Entrada de Pedidos";

$Table = "Pendientes";
$TableJoin = "";
$Key = "IDPendientes";
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
				
				//print_r($HTTP_POST_VARS);
				
				foreach($Ingreso as $key => $valor)
				{
					
					if( $valor > 0 )
					{
						$cantidadactualizar = $Cantidad[$key] - $valor;
						
						if( $cantidadactualizar > 0 )
						{
						
							$sql_actualizar = " UPDATE Pendientes SET CantidadPendiente = '$cantidadactualizar' WHERE IDPendientes = '$IDPendientes[$key]' ";	
							db_query($sql_actualizar);
						
							//insertar el log
							insertlog($ID_Usuario,"Pendientes",$IDPendientes[$key],"Actualizar",$sql_actualizar); 
							
							
						}//end if( $cantidadactualizar > 0 )
						else
						{
							
							//borrar los pendientes para no saturar la pantalla
							$sql_borrar = " DELETE FROM Pendientes WHERE IDPendientes = '$IDPendientes[$key]' ";	
							db_query($sql_borrar);
						
							//insertar el log
							insertlog($ID_Usuario,"Pendientes",$IDPendientes[$key],"Borrar",$sql_borrar); 
						
						}//end else
						
						//insertar entrada
						$identrada = get_maxID("Entrada","IDEntrada");
						$sql_entrada = "INSERT INTO Entrada VALUES('$identrada','$IDPuntoVentaReferencia[$key]','$IDTalla[$key]','$valor',NOW(),'$IDPuntoVenta')";
						db_query($sql_entrada);
						
						//Actualizar Existencias
				
						$existencias = get_field("CodificacionEspecifica","Existencias", "IDPuntoVentaReferencia",$IDPuntoVentaReferencia[$key]."' AND IDTalla = '$IDTalla[$key]" );
						$existencias = $existencias + $valor;
						
						$sql_actualizacod = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDPuntoVentaReferencia = '$IDPuntoVentaReferencia[$key]' AND IDTalla = '$IDTalla[$key]'";
						db_query( $sql_actualizacod );
						
						//insertar el log
						$idcodificacion = get_field("CodificacionEspecifica","IDCodificacionEspecifica", "IDPuntoVentaReferencia",$IDPuntoVentaReferencia[$key]."' AND IDTalla = '$IDTalla[$key]" );
						insertlog($ID_Usuario,"CodificacionEspecifica",$idcodificacion,"Actualizar",$sql_actualizacod);
						
						$existencias = 0;
					
					}//end if( $valor > 0 )
					
				}//end foreach($ingreso as $key => $valor)
				
				/*$frm['ID'] = insert_width_table($frm,"Movimiento","IDMovimiento");
				entradapedido($frm);*/
				
				db_query("COMMIT");
				
				echo "<script>location.href='?mod=Movimiento';</script>";
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
				
				if( $field == 'Talla.Descripcion')
				{
					$sql = "SELECT Pendientes.* FROM Pendientes, Talla WHERE Talla.Descripcion LIKE '%$QryString%' AND Talla.IDTalla = Pendientes.IDTalla AND IDPuntoVenta = '$IDPuntoVenta' GROUP BY IDPendientes ORDER BY IDPuntoVentaReferencia ";
				}else
				{
					$sql = make_qry_string($HTTP_GET_VARS);
				}
				
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
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;
	if(empty($sql))
	 	$sql =  "SELECT * FROM Pendientes WHERE  IDPuntoVenta = '$IDPuntoVenta' AND CantidadPendiente > 0 ORDER BY IDPuntoVentaReferencia ASC";
	 	
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
							
							?><?
	if($rows > 0){
?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="600">
	
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
	
	
	<table class="forumline" width="600" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<?
			filtrar();
		?>
	</td>
	</tr>
	<tr>
	<td>
		<form name="frm" action="<?=$PHP_SELF?>" method="post" >
		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
					<tr>
							<td class=navpic nowrap bgcolor=#DBEAF5><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Referencia.Numero&in_order=".$order."&listar=".$nav->limit."&tjoin=PuntoVentaReferencia&action=list"; %>'>Referencia</a><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Referencia.Numero&tjoin=PuntoVentaReferencia&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>&nbsp;<% if($_GET['order_by']=="Referencia.Numero"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=IDTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Talla</a><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'><% if($_GET['order_by']=="IDTalla"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=CantidadPendiente&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Cantidad</a><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=CantidadPendiente&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'><% if($_GET['order_by']=="CantidadPendiente"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5 align="center">Ingreso</td>
					</tr>
	
				<? 
				while($r = db_fetch_object($result)){
				
					$class = repetition()?"col1list":"col2list";
				?>
	  	
					<tr>
							<td nowrap class="<?=$class?>"><? echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$r->IDPuntoVentaReferencia))?></td>
							<td nowrap class="<?=$class?>"><? echo get_field("Talla","Descripcion","IDTalla",$r->IDTalla); ?></td>
						<td nowrap class="<?=$class?>">
							<? echo number_format($r->CantidadPendiente); ?>
							<input type="hidden" name="Cantidad[<?=$r->IDPendientes?>]" value="<?=$r->CantidadPendiente?>">
							<input type="hidden" name="IDPuntoVentaReferencia[<?=$r->IDPendientes?>]" value="<?=$r->IDPuntoVentaReferencia?>">
							<input type="hidden" name="IDTalla[<?=$r->IDPendientes?>]" value="<?=$r->IDTalla?>">
						</td>
						<td nowrap class="<?=$class?>">
							<input type="text" size="5" name="Ingreso[<?=$r->IDPendientes?>]">
							<input type="hidden" name="IDPendientes[<?=$r->IDPendientes?>]" value="<?=$r->IDPendientes?>">
						</td>
					</tr>
				<? } // END for
				?>
					<tr>
						<td  bgcolor=#DBEAF5 colspan=4 nowrap class="navpic" align="center">
							<?
								print $pages;
							?>
							<input type="hidden" name="action" value="insert">
							<input type="submit" class="button" name="enviar" value="Realizar Entradas">
						</td>
						
						
						</td>
					</tr>		
				</table>
		</form>
			</td>
		</tr>
	</table>
	<? 			
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No hay pedidos pendientes </b></span>";
}// Enf function list()				

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD,$IDPuntoVenta;
?>
	<form name="frm" action="./" method="get" onsubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="Referencia.Numero">Referencia</option>
					<option value="Talla.Descripcion">Talla</option>
				</select> 
				<input type="text" size="10" name="QryString" id="Buscar Por" class="post"> 
				
				
				ordenar por 
				<select name="order_by" class="popup">
					<option value="PuntoVentaReferencia.IDReferencia">Referencia</option>
					<option value="CantidadPendiente">CantidadPendiente</option>
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
				<input type="hidden" name="tjoin" value="PuntoVentaReferencia">
				<input type="hidden" name="IDPuntoVenta" value="<?=$IDPuntoVenta?>">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?		
	}//End function filtrar
?>