<body> <?

$TitleMod ="Log";

$Table = "Log";
$TableJoin = "Log";
$Key = "IDLog";
$MOD = "Log";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
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
		funcion Listar
*******************************************************************************************/

	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key;
			
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY Fecha Desc";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		$nav->limit = 20;
   		$nav->execute($sql,$dblink);
		$total_records =  $nav->total_result;
		$rows = $nav->rows;
		$result = $nav->sql_result;
		$row = $offset;
		$startrow = $offset + 1;
		$finalrow = ($row * $nav->limit) + $rows;
	
	 	$pages = $nav->show_num_pages('&laquo;','&laquo; prev','&raquo;','next &raquo;','|','class=navvar');   // show pages
		
		$info = $nav->show_info(); 
	if($rows > 0){
?>		
 	
<br>
<table width="600"  cellpadding=0 cellspacing=0 class=bordertable>
	<tr>
		<td class=titlemedium bgcolor=#9daac6><b>Listar <? echo $TitleMod ?></b></td>
	</tr>
	<tr><td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr >
						<td class=rowform nowrap bgcolor=#DBEAF5>Modulo</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>IDModulo</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fecha</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Transaccion</td>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Direccion IP</td>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Usuario</td>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Ver</td>
					</tr>

<? while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td nowrap class=row1><? echo $r->Modulo ?></td>
						<td align="center" nowrap class=row1><? echo $r->IDModulo ?></td>
						<td nowrap class=row1><? echo $r->Fecha?></td>
						<td nowrap class=row1><? echo $r->Transaccion?></td>
						<td align=center valign=middle nowrap width=60 class=row2><? echo $r->DireccionIP?></td>
						<td align=center valign=middle nowrap width=60 class=row2><? echo get_field("Usuario","Nombre","IDUsuario",$r->IDUsuario)?></td>
						<td align=center valign=middle nowrap width=60 class=row2><a href="#" onclick="window.open('log/verdetalle.php?IDL=<?=$r->IDLog; ?>','','scrollbars=yes,width=500,height=280,top=160, left=160 ')"><img src='images/edit.gif' border='0'></a></td>
					</tr>
<? } // END for
?>
<tr>
						<td class=texto bgcolor=#DBEAF5 colspan=5 nowrap>

	<?
		print $pages;
		?>
		</td>
						<td class=texto bgcolor=#DBEAF5 nowrap></td>
						<td class=texto bgcolor=#DBEAF5 nowrap></td>
					</tr>		
</table></td>
</tr>
</table>	

<? 			
}// End if$rows
else
	echo "<br><br><p class=subtitle align=center><b>No existen registros en  $TitleMod </b></p>";
filtrar();	
}// Enf function list()				

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD;
?>
	<table width=600>
	<form name="frm" action="" method="get" onsubmit="return valbuscar(document.frm)">
			<tr>
				<td class="rowform" align="center" colspan=8>
					<select name="field" id="Buscar Por" class="popup">
						<option value="">Buscar Por</option>
						<option value="Modulo">Modulo</option>
						<option value="IDModulo">IDModulo</option>
						<option value="Transaccion">Transaccion</option>
						<option value="DireccionIP">Direccion IP</option>
					</select> <input type="text" size="20" name="QryString" id="Buscar Por" class="post">
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
					ordenar por <select name="order_by" class="popup">
						<option value="Modulo">Modulo</option>
						<option value="IDModulo">IDModulo</option>
						<option value="Ubicacion">Transaccion</option>
						<option value="DireccionIP">Direccion IP</option>
					</select>
					de forma <select name="in_order" class="popup">
						<option value="ASC">Ascendente</option>
						<option value="DESC">Descendente</option>
					</select><br>
					<input type="hidden" name="mod" value="<?=$MOD?>">
					<input type="hidden" name="action" value="list">
					<input type="hidden" name="rangofield" value="Fecha">
					<input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>
	</form>
	</table>
<?		
	}//End function filtrar
?>

