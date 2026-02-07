<body> <?

$TitleMod ="Referencia";

$Table = "Referencia";
$TableJoin = "CodificacionEspecifica";
$Key = "IDReferencia";
$MOD = "Referencia";
$m="Referencia";
 $permisos = get_permiso($ID_Usuario,$m,$Table);



if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;
			
			case "insert" :
				//if($permiso[0]!=2){	
				
					$frm= vars_LOG($HTTP_POST_VARS);
					
					$sql_verifica = " SELECT * FROM Referencia WHERE Numero = '$frm[Numero]' ";
					$qry_verifica = db_query( $sql_verifica );
					if( db_num_rows( $qry_verifica ) > 0 )
					{
						echo "esta referencia ya existe en el sistema, verifique por favor";
						exit;
					}//end if
					
					$id = insert($frm);
					
					if(isset($PuntoVenta))
						foreach ($PuntoVenta as $IDPuntoVenta)
						{
							$idpuntoventareferencia = get_maxID("PuntoVentaReferencia","IDPuntoVentaReferencia");
							$qry_PuntoVentaReferencia = db_query("INSERT INTO PuntoVentaReferencia values('$idpuntoventareferencia', '$id','$IDPuntoVenta')");
						}
						
					
					insert_codEspecifica($id);
					
					print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
				/*
				}
				else{
					window_alert($error_acceso);
					return false;
				}	
				*/
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				//if($permiso[0]<>"2"){	
					$frm= vars_LOG($HTTP_POST_VARS);
	
					//Variable temporal para saber si el tipo de talla es actualizado
					
					$temp = 0;
					
					if(isset($HTTP_POST_VARS['IDTipoTalla']))
					{
					
						$sql_tipotalla = "SELECT IDTipoTalla FROM $Table WHERE $Key = '$ID' ";
						$query_tipotalla = db_query( $sql_tipotalla );
						
						$r_tipotalla = db_fetch_object( $query_tipotalla );
						
						if( $r_tipotalla->IDTipoTalla <> $HTTP_POST_VARS['IDTipoTalla'] )
						{
							$temp = 1;
						}//end if( $r_tipotalla->tipotalla <> $IDTalla )
					
					}//end if(isset($IDTalla))
									
					update($frm);
					
					if( $temp == 1 )
					{
						insert_codEspecifica($ID);
					}//end if( $temp == 1 )
					
					//$qry = db_query("DELETE FROM PuntoVentaReferencia WHERE IDReferencia = '$ID' ");
			
					//actualizacion de los puntos de venta en donde esta la referencia
					
					if(isset($PuntoVenta))
					{
						actualizapunto($ID,$PuntoVenta,$HTTP_POST_VARS['IDTipoTalla']);
					}//end if(isset($PuntoVenta))
					
					
					//verificacion del tipo de talla en la base de datos contra el que viene
					//en el POST. Si es diferente se actualiza la codificacion especifica de la referencia
				/*
				}
				else{
					window_alert($error_acceso);
					return false;
				}
				*/
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
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('IDProveedor','IDTipoTalla','IDColor','IDLinea','Tipo','Cuero','Numero','Nombre','Descripcion','Publicar');

function CheckAll()
{	 
	for (var i=0;i< document.frm.elements.length;i++)
	{
		var e = document.frm.elements[i];
		if (e.name != 'allbox')
		e.checked = !e.checked;
	}
}

function selmovimiento( IDMOVIMIENTO, FECHA )
{
	document.frm.IDMovimiento.value= IDMOVIMIENTO;
	document.frm.FechaMovimiento.value= FECHA;
}//end function

</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
			<td><a href="./?mod=<%=$MOD%>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>

<?	
	if($newmode <> "insert")
	{
		$TABsel = 1;
		$idReferencia = $r->IDReferencia;
	 	include("Referencia/menutabReferencia.php");
	 	
	 	$qry_movimiento = db_query( $sql_movimiento = "SELECT * from Movimiento WHERE IDMovimiento = '$r->IDMovimiento' " );
	 	$r_movimiento = db_fetch_object( $qry_movimiento );
	 	
	}
?>	
<table cellpadding=1 cellspacing=0 class=bordertable align=left >
	<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?}?>>
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<? echo $TitleMod ?> <? echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
							<td colspan="2">Si la referencia es producto de una operaci&oacute;n de 'segunda' indique el movimiento aqu&iacute;.</td>
						</tr>
						<tr class=row2>
							<td>Movimiento Segundas</td>
							<td><input type=text size=25 class=input   name=FechaMovimiento id=Numero value="<?=$r_movimiento->Fecha ?>"><input type=hidden name=IDMovimiento id=IDReferencia value="<?=$r->IDIDMovimiento ?>"><input type="button" name="Segunda" value="Segunda" onClick="window.open( 'Movimiento/popMovimiento.php','','width=600, height=500' );" class=submit></td>
						</tr>
						<tr class=row2>
							<td width="50%">Numero<br>
								<input type=text size=25 class=input   name=Numero id=Numero value="<?=$r->Numero ?>"></td>
							<td valign="top">
								<table width="100%" border="0" cellspacing="2" cellpadding="0">
									<tr>
										<td class=row2>Sexo</td>
										<td class=row2>Saldo</td>
									</tr>
									<tr>
										<td class=row1><? echo formradiogroup(array('M'=>'M','F'=>'F','Otro'=>'Otro'),$r->Sexo, 'Sexo'); ?></td>
										<td class=row1><? echo formradiogroup(array('S'=>'S','N'=>'N'),$r->Saldo, 'Saldo'); ?></td>
									</tr>
									<tr>
										<td></td>
										<td></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr class=row2>
							<td width="50%">Nombre<br>
								<input type=text size=25 class=input   name=Nombre id=Nombre value="<?=$r->Nombre ?>"></td>
							<td>Lista de Precios<br>
								<select name="IDPrecio" class="input">
								<option value="">Seleccione...</option>
								<? 
								
								$sql_precio = " SELECT * FROM Precio ORDER BY ValorVenta ";
								$qry_precio = db_query( $sql_precio );
								while( $r_precio = db_fetch_object( $qry_precio ) )
								{
									echo "<option value='".$r_precio->IDPrecio."'";
									if( $r_precio->IDPrecio == $r->IDPrecio   )
									{
										echo " selected ";
									}//end if
									echo " >".$r_precio->ValorVenta." - ".$r_precio->Descuento."%</option>"; 
								}//end while
								?>
								</select>
						</tr>
						<tr class=row2>
			<td width="50%">Proveedor<br>
								<? echo formpopup("Proveedor","Nombre","Nombre","IDProveedor",$r->IDProveedor,"input\" id=\"Proveedor"); ?></td><td>Talla <br>
								<select name=IDTipoTalla>
									<option value="">[ Seleccione ]</option><? 
								$sql_tipotalla = "SELECT * FROM TipoTalla ORDER BY Descripcion";
								$query_tipotalla = db_query($sql_tipotalla);
								while($r_tipotalla = db_fetch_object($query_tipotalla))
								{
									$query_tallas = db_query("SELECT * FROM Talla WHERE IDTipoTalla = '$r_tipotalla->IDTipoTalla'");
									
									if( db_num_rows( $query_tallas ) > 0 )

									{
										echo "<option value=$r_tipotalla->IDTipoTalla";
										if($r->IDTipoTalla == $r_tipotalla->IDTipoTalla) echo " selected ";
										echo ">".$r_tipotalla->Descripcion."</option>";
									}//end if( db_num_rows( $query_tallas ) > 0 )
								}//end while($r_tipotalla = db_fetch_object($query_tipotalla))
							?>
								</select></td>
			</tr>
						<tr class=row2>
			<td width="50%">Color<br>
								<? echo formpopup("Color","Nombre","Nombre","IDColor",$r->IDColor,"input\" id=\"Color"); ?></td><td>Linea<br>
								<select name=IDLinea>
								<option value="">[ Seleccione ]</option>
								<? 
									$sql_tipo = "SELECT * FROM Tipo ORDER BY Descripcion";
									$query_tipo = db_query($sql_tipo);
									while($r_tipo = db_fetch_object($query_tipo))
									{
										echo "<option value=''>----".$r_tipo->Descripcion."</option>";
										$sql_linea = "SELECT * FROM Linea WHERE IDTipo = '$r_tipo->IDTipo'";
										$query_linea = db_query($sql_linea);
										while ( $r_linea = db_fetch_object($query_linea) )
										{
											echo "<option value=$r_linea->IDLinea";
											if($r->IDLinea == $r_linea->IDLinea) echo " selected ";
											echo ">".$r_linea->Nombre."</option>";
										}
									}
								?>
								</select></td>
			</tr>
						<tr class=row2>
			<td width="50%">Cuero<br>
								<? echo formpopup("Cuero","Descripcion","Descripcion","IDCuero",$r->IDCuero,"input\" id=\"Cuero"); ?></td><td> Publicar<br>
								 <? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></td>
			</tr>
						<tr class=row3>
							<td colspan="2">
								<b>PUNTOS DE VENTA</b>
							</td>
						</tr>
						<tr class=row2>
							<td colspan="2">
								<br>
								<%
									table_check_list($Table,$Key,$r->$Key,"PuntoVenta","IDPuntoVenta","PuntoVentaReferencia","PuntoVenta[]",$newmode);
								%>							
							</td>
						</tr>
						<tr class=row3>
							<td colspan="2">
								<input type="button" name="check" value="Seleccionar Todos" onClick="CheckAll();" class=submit>
							</td>
						</tr>
						<tr class=row2>
							<td colspan="2"> Descripci&oacute;n <br>
								<textarea rows=5 cols=55 wrap=virtual name=Descripcion id=Descripcion><?=$r->Descripcion?></textarea></td>
						</tr>
						<tr class=row2>
			<td width="50%"></td><td></td>
			</tr>
			<tr>
			<td colspan=2 align=center class=row2><input type=hidden name=IDReferencia id=IDReferencia value="<?=$r->IDReferencia ?>"><input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=ID value="<? echo $r->$Key ?>">
				<input type=hidden name=action value=<?=$newmode?>>
				<input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?
}// End function print_form()

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
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=30;
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
<br>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
		<td><a href="./?mod=<%=$MOD%>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
<?
		if($rows > 0){
?>		
<br>
<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <? echo $TitleMod ?></b></td>
		</tr>
<?filtrar();?>	
<tr>
			<td class=titlemedium  bgcolor=#9daac6><% echo $info;%></td>
		</tr>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=16 nowrap>
<?
	print $pages;
?>
</td>
</tr>
	<tr>
			<td>
				<table width=100% border=0 cellspacing=1 cellpadding=0>
				<tr>
				<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Numero&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Numero<% if($_GET['order_by']=="Numero"){%><img src="images/<%=$img%>" border=0><%}%></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDProveedor&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Proveedor&nbsp;<% if($_GET['order_by']=="IDProveedor"){%><img src="images/<%=$img%>" border=0><%}%></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5><a href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Tipo de Talla</a><a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; %>"><% if($_GET['order_by']=="IDTipoTalla"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDLinea&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Tipo Ref.&nbsp;<% if($_GET['order_by']=="IDLinea"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDLinea&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Linea&nbsp;<% if($_GET['order_by']=="IDLinea"){%><img src="images/<%=$img%>" border=0><%}%></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Nombre<% if($_GET['order_by']=="Nombre"){%><img src="images/<%=$img%>" border=0><%}%></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Publicar&nbsp;<% if($_GET['order_by']=="Publicar"){%><img src="images/<%=$img%>" border=0><%}%></a> </td>
				<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
				</tr>

<? while($r = db_fetch_object($result)){
?>
  	
<tr>
<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<? echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
<td nowrap class=row1><? echo $r->Numero ?></td> 
<td nowrap class=row1><? echo $r->IDProveedor ?></td>
						<td nowrap class=row1><? echo get_field("TipoTalla","Descripcion","IDTipoTalla",$r->IDTipoTalla) ?></td>
						<td nowrap class=row1><?echo get_field("Tipo","Descripcion","IDTipo",get_field("Linea","IDTipo","IDLinea",$r->IDLinea))?></td>
						<td nowrap class=row1><? echo get_field("Linea","Nombre","IDLinea",$r->IDLinea) ?></td>
						<td nowrap class=row1><? echo $r->Nombre ?></td>
						<td nowrap class=row1><? echo $r->Publicar ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<? echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<? } // END for
?>
<tr>
						<td class=texto bgcolor=#DBEAF5 colspan=8 nowrap>
	<?
		print $pages;
		?>
</td>
					</tr>		
</table></td>
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
					<option value="Numero">Numero</option>
					<option value="Tipo.descripcion">Tipo</option>
					<option value="Linea.Nombre">Linea</option>
					<option value="TipoTalla.Descripcion">tipo de Talla</option>
					<option value="Nombre">Nombre</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				ordenar por 
				<select name="order_by" class="popup">
					<option value="Numero">Numero</option>
					<option value="Tipo.descripcion">Tipo</option>
					<option value="Linea.Nombre">Linea</option>
					<option value="Nombre">Nombre</option>
				</select> 
				<br>
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
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Linea">
				<input type="hidden" name="tlevel" value="TipoTalla">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?		
	}//End function filtrar
?>
