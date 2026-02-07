<body> 
<?

$TitleMod ="Promocion 50% Descuento en segundo par";

$Table = "TiendaPromocionSegundoPar";
$TableJoin = "";
$Key = "IDTiendaPromocionSegundoPar";
$MOD = "TiendaPromocionSegundoPar";
$m = "TiendaPromocionSegundoPar";

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

	GLOBAL $TitleMod,$Table,$MOD,$Key,$Dia_array;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('Fecha','Dia');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
			<td><a href="./?mod=<%=$MOD%>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>
<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?if($newmode!="delete"){?> onSubmit="return EvaluaReg(this,Check)" <?}?>>
	
<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<? echo $TitleMod ?> <? echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
			<td>Tienda</td><td><? echo formpopup("PuntoVenta","Nombre","Nombre","IDPuntoVenta",$r->IDPuntoVenta,"input\" id=\"PuntoVenta"); ?></td>
			</tr>
			<tr class=row2>
			<td>Fecha Inicio</td>
            <td>
            
            <input type="input" name="FechaInicio" id="FechaInicio" class="tbox" value="<?php if ($r->FechaInicio!="0000-00-00") { echo $r->FechaInicio; }?>">
            <script language="JavaScript1.2">
                            <!--
                                if (!document.layers)
                                    document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaInicio,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
                            //-->
                        </script>            
            
								
			</td>
			</tr>
			<tr class=row2>
			  <td>Fecha Fin</td>
			  <td>
              
			<input type="input" name="FechaFin" id="FechaFin" class="tbox" value="<?php if ($r->FechaFin!="0000-00-00") { echo $r->FechaFin; }?>" >
            <script language="JavaScript1.2">
                            <!--
                                if (!document.layers)
                                    document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaFin,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
                            //-->
                        </script>               
              
              </td>
		  </tr>
			<tr class=row2>
			  <td>Activo</td>
			  <td><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Activo, 'Activo'); ?></td>
		  </tr>		
			
			<tr>
			<td colspan=2 align=center class=row2><input type=hidden name=IDTiendaPromocionSegundoPar value="<?=$r->IDTiendaPromocionSegundoPar ?>">    <input type=hidden name=ID value="<? echo $r->$Key ?>">
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
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=100;
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
						<td class=rowform nowrap bgcolor=#DBEAF5>Tienda<a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Dia&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Fecha</a><a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Dia&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'> Inicio</a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Dia&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>FechaFin</a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Activo</td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<? while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<? echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class=row1><? echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta) ?></td>
						<td nowrap class=row1><? echo  $r->FechaInicio; ?></td>
						<td nowrap class=row1><? echo  $r->FechaFin; ?></td>
						<td nowrap class=row1><? echo  $r->Activo; ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<? echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<? } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=6 nowrap>
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

?>
