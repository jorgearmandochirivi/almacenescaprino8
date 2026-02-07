<body>
<?

$TitleMod ="Link Cambios";

$Table = "LinkCambio";
$TableJoin = "";
$Key = "IDLinkCambio";
$MOD = "LinkCambios";
$m = "DEspeciales";

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
				if(isset($PuntoVenta)){
					foreach ($PuntoVenta as $IDPuntoVenta)
					{
						$idpuntoventaalink = get_maxID("PuntoVentaLink","IDPuntoVentaLink");
						$qry_PuntoVentaLink = db_query("INSERT INTO PuntoVentaLink values('$idpuntoventaalink', '$id','$IDPuntoVenta')");
					}
				}
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :

				db_query("Delete From  PuntoVentaLink Where IDLinkCambio = '".$id."'");
				if(isset($PuntoVenta)){
					foreach ($PuntoVenta as $IDPuntoVenta)
					{
						$idpuntoventaalink = get_maxID("PuntoVentaLink","IDPuntoVentaLink");
						$qry_PuntoVentaLink = db_query("INSERT INTO PuntoVentaLink values('$idpuntoventaalink', '$id','$IDPuntoVenta')");
					}
				}
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

	GLOBAL $TitleMod,$Table,$MOD,$Key,$Dia_array;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('Fecha');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
			<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
			<td><a href="./?mod=<%=$MOD%>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>
<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?if($newmode!="delete"){?> onsubmit="return EvaluaReg(this,Check)" <?}?>>

<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<? echo $TitleMod ?> <? echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
			<tr class=row2>
				<td>Nombre</td>
				<td><input type="text" name="Nombre" id="Nombre" value="<?php echo $r->Nombre; ?>" ></td>
</tr>
			<?php if($r->$Key==1){ ?>
			<tr class=row2>
			<td>Habilitar Link</td><td>
            <? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Habilitar, 'Habilitar'); ?>

							</td>
			</tr>
						<tr class=row2>
						  <td>Link</td>
						  <td>
                          http://www.almacenescaprino.com/?mod=cambioreferenciaanterior
                          </td>
		  </tr>
			<? } ?>

			<?php if($r->$Key==2){ ?>
						<tr class=row2>
						  <td>Habilitar Descuentos para Relaciones publicas</td>
						  <td><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->RelacionesPublicas, 'RelacionesPublicas'); ?></td>
		  </tr>
				<? } ?>
						<tr class=row2>
						  <td colspan="2">Puntos de venta que aplica</td>
		  </tr>
						<tr class=row2>
						  <td colspan="2">
                           <?php
                    table_check_list($Table,$Key,$r->$Key,"PuntoVenta","IDPuntoVenta","PuntoVentaLink","PuntoVenta[]",$newmode," and Publicar = 'S' ");
                ?>
                          </td>
		  </tr>
						<tr>
			<td colspan=2 align=center class=row2>
            	<input type=hidden name=IDLinkCambio value="<?=$r->IDLinkCambio ?>">    <input type=hidden name=ID value="<? echo $r->$Key ?>">
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
		Global $TitleMod,$MOD,$Table,$Key,$listar,$Dia_array;
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

	<tr>
		<td class=titlemedium  bgcolor=#9daac6><% echo $info;%></td>
	</tr>
	<tr>
		<td class=texto bgcolor=#DBEAF5 colspan= nowrap>
		<?
			print $pages;
		?>
		</td>
	</tr>
	<tr><td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Nombre</td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Habilitar</a><a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>&nbsp;
						    <% if($_GET['order_by']=="Fecha"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<? while($r = db_fetch_object($result)){
?>

<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<? echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class=row1><? echo $r->Nombre ?></td>
						<td nowrap class=row1><?
						switch($r->$Key){
							case "1":
								echo $r->Habilitar;
							break;
							case "2":
								echo $r->RelacionesPublicas;
							break;
						}
						 ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<? echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>
</td>
					</tr>
<? } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=3 nowrap>
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

						<option value="Nombre">Nombre</option>
					<option value="Publicar">Publicar</option>
				</select>
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post">

					ordenar por <select name="order_by" class="popup">
					<option value="Nombre">Nombre</option>
					<option value="Publicar">Publicar</option>
				</select><BR> de forma <select name="in_order" class="popup">
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
				</td>
			</tr>
	</form>
<?
	}//End function filtrar
?>
