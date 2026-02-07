<body> <?php

$TitleMod ="SubEspecialidad";

$Table = "MD_SubEspecialidad";
$TableJoin = "Medico";
$Key = "IDMD_SubEspecialidad";
$MOD = "Medico&a=MD_SubEspecialidad";
$Modulo = "Medico";
include($languagedir."/".$Idioma."/".$Modulo."/".$Table.".php");

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
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$Label;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('MDID_Especialidad','Descripcion','Publicar');
</script>
<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>
	<table width=98% border=0 cellspacing=1 cellpadding=1 class=texto>
			<tr><td class=col1 align=right><?=$Label[MDID_Especialidad]?></td><td class=col2><?php echo formpopup("MD_Especialidad","Descripcion","Descripcion","MDID_Especialidad",$r->MDID_Especialidad,"inputSelect\" id=\"MDID_Especialidad"); ?></td></tr>
			<tr><td class=col1 align=right><?=$Label[Descripcion]?></td><td class=col2><input type=text size=25 class=tbox   name=Descripcion value="<?=$r->Descripcion ?>"> </td></tr>
<tr><td class=col1 align=right><?=$Label[Publicar]?></td><td class=col2><?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></td></tr>
<tr>
			<td colspan=2 align=center class=row2><input type=hidden name=IDMD_SubEspecialidad value="<?=$r->IDMD_SubEspecialidad ?>"><input type=hidden name=ID value="<?php echo $r->$Key ?>">
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

if($_GET['in_order']=="DESC"){
	$img="up.png";
	$order="ASC";
}else{
	$img="down.png";
	$order="DESC";
}

if($rows > 0){
?>		
<br>
<?php filtrar();?>
<table  cellpadding=0 align=center class="tablas" width=95;?>

<tr>
<th>
<?php
	print $info;
?>
</th>
</tr>
	<tr><td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
						<th class=titulodetablas nowrap>Editar</th>
						<th class=titulodetablas> <a href="<?php echo "?m=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=MDID_Especialidad&in_order=".$order."&listar=".$nav->limit; ?>">MDID_Especialidad&nbsp;<?php if($_GET['order_by']=="MDID_Especialidad")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </th>
						<th class=titulodetablas> <a href="<?php echo "?m=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Descripcion&in_order=".$order."&listar=".$nav->limit; ?>">Descripcion&nbsp;<?php if($_GET['order_by']=="Descripcion")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </th><th class=titulodetablas> <a href="<?php echo "?m=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit; ?>">Publicar&nbsp;<?php if($_GET['order_by']=="Publicar")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </th>
						<th class=titulodetablas>Eliminar</th>
					</tr>
<?php while($r = db_fetch_object($result)){
$class = repetition()?"col1list":"col2list";
?>
<tr class=<?=$class?>>
						<td align=center valign=middle nowrap width=5>
	&nbsp;<a href='<?php echo "?m=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap><?php echo get_field("MD_Especialidad","Descripcion","MDID_Especialidad","$r->MDID_Especialidad"); ?></td>
						<td nowrap><?php echo $r->Descripcion ?></td> <td nowrap><?php echo $r->Publicar ?></td>
						<td align=center valign=middle nowrap width=60>
&nbsp;<a href='<?php echo "?m=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=6 nowrap>
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
<table class="bordertable" width="100%">
<form name="frm" action="./" method="get">
<tr>
	<td class="content" align="center" colspan=8>
	<select name="field" id="Buscar por" class="popup">
		<option value="IDMD_SubEspecialidad">IDMD_SubEspecialidad</option>
<option value="MDID_Especialidad">MDID_Especialidad</option>
<option value="Descripcion">Descripcion</option>
<option value="Publicar">Publicar</option>

	</select> <input type="text" size="20" name="QryString" id="Buscar Por" class="post"> Entre <input type=text readonly size=10 class=input name=limit1>
	<script language='JavaScript1.2'>
	<!--
		if (!document.layers)
			document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick="popUpCalendar(this, document.frm.limit1,'yyyy-mm-dd')" width=16 height=16 border=0>")	
	-->
	</script> y <input type=text size=10 readonly class=input name=limit2> 
	<script language='JavaScript1.2'>
		<!--
			if (!document.layers)
				document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick="popUpCalendar(this, document.frm.limit2,'yyyy-mm-dd')" width=16 height=16 border=0>")
		//-->
	</script>
<br>ordenar por <select name="order_by" class="popup">
<option value="IDMD_SubEspecialidad">IDMD_SubEspecialidad</option>
<option value="MDID_Especialidad">MDID_Especialidad</option>
<option value="Descripcion">Descripcion</option>
<option value="Publicar">Publicar</option>

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
	<input type="hidden" name="mod" value="<?=$MOD?>">
	<input type="hidden" name="action" value="list">
	<input type="submit" name="submit" value="Buscar" class="submit">
	<input type="hidden" name="tjoin" value="">
	<input type="hidden" name="rangofield" value="">
	</td>
</tr>
	</form>
</table>	
<?php
	}//End function filtrar
?>
