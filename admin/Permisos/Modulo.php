<body> <?php

$TitleMod ="Modulo";

$Table = "ModuloSite";
$TableJoin = "ModuloSite_Tabla";
$Key = "IDModuloSite";
$MOD = "Modulo";

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
				if( !empty( $HTTP_POST_VARS["Tabla"] ) )
				{
					$Tabla = explode(",",$HTTP_POST_VARS["Tabla"]);
					db_query( "DELETE FROM ModuloSite_Tabla WHERE IDModuloSite = '$HTTP_POST_VARS[$Key]'" );
					
					foreach( $Tabla as $key => $valor )
					{
						db_query("INSERT INTO ModuloSite_Tabla VALUES ('$valor','$HTTP_POST_VARS[$Key]','S')");
					}//end foreach( $Tabla as $key => $valor )
					
				}//if( !empty( $HTTP_POST_VARS["Tabla"] ) )
					
				$frm= vars_LOG($HTTP_POST_VARS);
				$id = update($frm);
				echo "<script>location.href='?mod=$MOD&action=edit&id=$frm[$Key]';</script>";
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

	GLOBAL $TitleMod,$Table,$MOD,$Key,$Label;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
function addSelect(newTxt, newVal, num) {
  newOption = new Option(newTxt, newVal, false, false);
  document.frm.Tabla.options[document.frm.Tabla.length] = newOption;
}

var Check = new Array('Descripcion');

function removeitem(PopName) {
	var boxLength = PopName.length;
	arrSelected = new Array();
	var count = 0;
	for (i = 0; i < boxLength; i++) {
		if (PopName.options[i].selected) {
		arrSelected[count] = PopName.options[i].value;
		}
		count++;
	}
	var x;
	for (i = 0; i < boxLength; i++) {
		for (x = 0; x < arrSelected.length; x++) {
			if (PopName.options[i].value == arrSelected[x]) {
				PopName.options[i] = null;
	  		 }
		}
		boxLength = PopName.length;
	}
}
function setSelectOptions(PopName)
{
   strValues = "";
   
   
    for (var i = 0; i < PopName.length; i++){
         PopName.options[i].selected = 'TRUE';
    	if (i == 0) 
			strValues = PopName.options[i].value;
        else
         strValues = strValues + "," + PopName.options[i].value;
	}
	
	if (i == 0) {
		newoption = new Option('', '', false, false);
		PopName.options[0] = newoption;
	}
	else
		PopName.options[i-1].value = strValues;
	
    return true;
} 		
function reload(){
	window.location.reload(true);
}
</script>
<table cellspacing='0' cellpadding='2' border='0' align=center width='600' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folders.gif border=0> 
			<a href="./?mod=<?php echo $MOD;?>">Administrar <?php echo $TitleMod;?></a> </td>
			<td><a href="./?mod=<?php echo $MOD;?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table><br>


<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="setSelectOptions(document.frm.Tabla);return EvaluaReg(this,Check)"<?php }?>>
	<table width=600 border=0 cellspacing=1 cellpadding=1 class=bordertable align=center>
			<tr><td class=maintitle colspan=2>
					<?=$TitleMod?>
				 </td></tr>
			<tr><td class=row1 align=right>Nombre del modulo</td><td class=row2><input type=text size=25 class=tbox   name=NombreModulo value="<?=$r->NombreModulo ?>"> </td></tr>
			<tr>
				<td class=row1 align=right>Directorio</td>
				<td class=row2><input type=text size=25 class=tbox   name=DirectorioModulo value="<?=$r->DirectorioModulo ?>"></td>
			</tr>
			<?php
			if($newmode != "insert")
			{
			?>
			
			<tr>
				<td class=row1 align=right><br>
				</td>
				<td class=row2></td>
			</tr>
			<tr>
				<td class=row1 align=right>Tablas</td>
				<td class=row2 align="left">
						<a href="javascript:;" onclick="window.open('<?php echo $url ?>Permisos/popTablas.php?modulo=<?php echo $r->IDModuloSite ?>','','width=500,height=480'); this.value=''">
							<img src="images/mas.gif" alt="Agregar Tabla" border="0">
						</a>
						<a href="javascript:;" onclick="removeitem(document.frm.Tabla);">
							<img src="images/trash.gif" alt="Eliminar Usuarios" border="0">
						</a><br>
						<?php
							$sql_Tabla = "SELECT S.Descripcion, MS.IDTabla FROM Tabla S, ModuloSite_Tabla MS, ModuloSite M WHERE MS.IDModuloSite IN ('$r->IDModuloSite') AND MS.IDTabla = S.IDTabla GROUP BY S.IDTabla ORDER BY S.Descripcion ";
							$qry_Tabla = db_query($sql_Tabla);
						?>
						<select name="Tabla" STYLE="width:160px" size="6" multiple>
						<?php 	
						//for($k=0;$k<=$numd;$k++){
						
						
					
						while ($rsubEspecialidad = db_fetch_object($qry_Tabla) ) {
						?>
							<option value="<?php pv($rsubEspecialidad->IDTabla) ?>" ><?php echo $rsubEspecialidad->Descripcion ?></option>
						<?php 
						} //} // End while directorio
						?>
						</select>
				</td>
			</tr>
			
			<?php
			}//end if($newmode != "insert")
			?>
			
			<tr>
			<td colspan=2 align=center class=row2><input type=hidden class=tbox   name=FechaTrEd value="<?=$r->FechaTrEd ?>"><input type=hidden class=tbox   name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>"><input type=hidden class=tbox   name=FechaTrCr value="<?=$r->FechaTrCr ?>"><input type=hidden  name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>"><input type=hidden name=IDModuloSite value="<?=$r->IDModuloSite ?>"><input type=hidden name=ID value="<?php echo $r->$Key ?>">
<input type=hidden name=action value=<?=$newmode?>>
<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
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

if($_GET['in_order']=="DESC"){
	$img="up.png";
	$order="ASC";
}else{
	$img="down.png";
	$order="DESC";
}
?>

<table cellspacing='0' align=center cellpadding='2' border='0'  width='600' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folders.gif border=0> 
			<a href="./?mod=<?php echo $MOD;?>">Administrar <?php echo $TitleMod;?></a> </td>
			<td><a href="./?mod=<?php echo $MOD;?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table><br>
<?php
if($rows > 0){
?>		
<br>

<table  cellpadding=0 align=center class="tablas" width=600 class=bordetable>

<tr>
<th class="maintitle">
<?php
	print $info;
?>
</th>
</tr>
	<tr><td>
			<table width=100% border=0 cellspacing=1 cellpadding=0>
			<tr>
				<th class=titlemedium nowrap>Editar</th>
				<th class=titlemedium> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NombreModulo&in_order=".$order."&listar=".$nav->limit; ?>">NombreModulo&nbsp;<?php if($_GET['order_by']=="NombreModulo")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </th>
						<th class=titlemedium> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit; ?>">Publicar&nbsp;<?php if($_GET['order_by']=="Publicar")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </th>
						<th class=titlemedium>Eliminar</th>
					</tr>
<?php while($r = db_fetch_object($result)){
$class = repetition()?"row1":"row2";
?>
<tr class=<?=$class?>>
						<td align=center valign=middle nowrap width=5>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap><?php echo $r->NombreModulo ?></td>
						<td nowrap><?php echo $r->DirectorioModulo ?></td>
						<td align=center valign=middle nowrap width=60>
&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<?php } // END for
?>
<tr>
<th bgcolor=#DBEAF5 colspan=5 nowrap>
	<?php
		print $pages;
	?>
</th>
</tr>
<tr>
</tr>		
</table></td>
</tr>
</table>	

<?php
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
}// Enf function list()				

?>
 