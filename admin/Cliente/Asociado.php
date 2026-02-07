<body> <?php 

$TitleMod ="Asociados";

$Table = "Asociado";
$TableJoin = "Produccion";
$Key = "IDAsociado";
$MOD = "asociado";
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




/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('Numerodocumento','IDUsuario','Nombre','Apellido','Telefono','IDCiudad','Publicar','Dia','Mes','AutorizaMail');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>
<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>
	
<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
							<td width="40%">Cooperativa</td>
							<td><?php echo formpopup("Cliente","Nombre","Nombre","IDUsuario",$r->IDUsuario,"input\" id=\"IDUsuario"); ?></td>
						</tr>
						<tr class=row2>
			<td width="40%"> Cedula </td><td><input type=text size=25 class=input   name=NumeroDocumento id=Cedula value="<?php echo $r->NumeroDocumento ?>"> </td>
			</tr>
						<tr class=row2>
			<td width="40%"> Nombre </td><td><input type=text size=25 class=input   name=Nombre id=Nombre value="<?php echo $r->Nombre ?>"> </td>
			</tr>
			<tr class=row2>
			<td width="40%"> Apellidos </td><td><input type=text size=25 class=input   name=Apellido id=Apellidos value="<?php echo $r->Apellido ?>"> </td>
			</tr>
			<tr class=row2>
			<td width="40%"> Telefono </td><td><input type=text size=25 class=input   name=Telefono id=Telefono value="<?php echo $r->Telefono ?>"> </td>
			</tr>
			<tr class=row2>
			<td width="40%"> Celular </td><td><input type=text size=25 class=input   name=Celular id=Celular value="<?php echo $r->Celular ?>"> </td>
			</tr>
			<tr class=row2>
			<td width="40%"> Direccion </td><td><input type=text size=25 class=input   name=Direccion id=Direccion value="<?php echo $r->Direccion ?>"> </td>
			</tr>
						<tr class=row2>
			<td width="40%">Ciudad</td><td><?php echo formpopup("Ciudad","Descripcion","Descripcion","IDCiudad",$r->IDCiudad,"input\" id=\"IDCiudad"); ?></td>
			</tr>
						<tr class=row2>
							<td width="40%">
								Estado Civil
							</td>
							<td>
							
								<select name="EstadoCivil" id="Estado Civil" class="input">
			                      <option value="" Selected>Seleccione</option>
			                      <option value="Soltero">Soltero(a)</option>
			                      <option value="Casado">Casado(a)</option>
			                      <option value="Separado">Divorciado(a)</option>
			                      <option value="Viudo">Viudo(a)</option>
			                      <option value="UnionLibre">Union Libre</option>
			                      <option value="Otro">Otro</option>
			                    </select>
							
							</td>
						</tr>
						<tr class=row2>
			<td width="40%"> Publicar </td><td><?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></td>
			</tr>
						<tr>
			<td colspan=2 align=center class=row2><input type=hidden name=IDAsociado id=IDCliente value="<?php echo $r->IDAsociado ?>">   <input type=hidden name=ID value="<?php echo $r->$Key ?>">
				<input type=hidden name=action value=<?php echo $newmode?>>
				<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?php 
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
							
							?>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
<?php 
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
<td class=texto bgcolor=#DBEAF5 colspan=10 nowrap>
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
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Cedula&in_order=".$order."&listar=".$nav->limit; ?>&action=list'>Numero</a><a style="color: #3A4F6C;text-decoration: none" href='<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Cedula&in_order=".$order."&listar=".$nav->limit; ?>&action=list'> de Documento</a><a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Cedula&in_order=".$order."&listar=".$nav->limit; ?>&action=list">&nbsp;<?php  if($_GET['order_by']=="Cedula"){?><img src="images/<?php echo $img?>" border=0><?php }?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Nombre&nbsp;<?php  if($_GET['order_by']=="Nombre"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td><td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Apellido&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Apellidos&nbsp;<?php  if($_GET['order_by']=="Apellidos"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td><td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Telefono&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Telefono&nbsp;<?php  if($_GET['order_by']=="Telefono"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Celular&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Celular&nbsp;<?php  if($_GET['order_by']=="Celular"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDCiudad&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Ciudad&nbsp;<?php  if($_GET['order_by']=="IDCiudad"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Publicar&nbsp;<?php  if($_GET['order_by']=="Publicar"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class=row1><?php echo $r->Cedula ?></td>
						<td nowrap class=row1><?php echo $r->Nombre ?></td> <td nowrap class=row1><?php echo $r->Apellido?></td> <td nowrap class=row1><?php echo $r->Telefono ?></td>
						<td nowrap class=row1><?php echo $r->Celular ?></td>
						<td nowrap class=row1><?php echo get_field("Ciudad","Descripcion","IDCiudad",$r->IDCiudad) ?></td>
						<td nowrap class=row1><?php echo $r->Publicar ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=9 nowrap>
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
						<option value="Cedula">Cedula</option>
						<option value="Nombre">Nombre</option>
						<option value="Apellido">Apellido</option>
						<option value="Telefono">Telefono</option>
						<option value="AutoriazaMail">AutorizaMail</option>
						<option value="Ciudad.Descripcion">Ciudad</option>
					</select> <input type="text" size="20" name="QryString" id="Buscar Por" class="post"> Entre <input type=text readonly size=10 class=input name=limit1>
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
					ordenar por <select name="order_by" class="popup">
						<option value="Cedula">Cedula</option>
						<option value="Nombre">Nombre</option>
						<option value="Apellido">Apellido</option>
						<option value="Telefono">Telefono</option>
					</select> de forma <select name="in_order" class="popup">
						<option value="ASC">Ascendente</option>
						<option value="DESC">Descendente</option>
					</select>
					Listar <select name="listar" class="popup">
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
					<input type="hidden" name="tjoin" value="Ciudad">
					<input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>
	</form>
<?php 		
	}//End function filtrar
?>
