<body> <?php 

$TitleMod ="Precio";

$Table = "Precio";
$TableJoin = "CodificacionEspecifica";
$Key = "IDPrecio";
$MOD = "Precio";
$m="Referencia";
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

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('ValorVenta','Publicar');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>
<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>
	
<table width="500" cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
			<td width="25%">
								<div align="left">
									ValorVenta</div>
							</td><td width="25%"><input type=text size=8 class=input   name=ValorVenta id=ValorVenta value="<?php echo $r->ValorVenta ?>"> </td>
							<td width="25%">
									Descuento</td>
							<td width="25%"><input type=text size=3 class=input   name=Descuento id=Descuento value="<?php echo $r->Descuento ?>" maxlength="3">%</td>
						</tr>
						<tr class=row2>
			<td width="25%">
								<div align="left">
									
									Publicar</div>
							</td><td width="25%"><?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></td>
							<td width="25%"></td>
							<td width="25%"></td>
						</tr>
			<tr class=row2>
			<td width="25%">
								<div align="left"></div>
							</td><td width="25%"></td>
							<td width="25%"></td>
							<td width="25%"></td>
						</tr>
			<?php 
			if($newmode <> "insert")
			{
			?>	
			<tr>
							<td align=center class=row2 colspan=4>
								<div align="left">
									<?php mostrarreferencias($r->IDPrecio)?></div>
							</td>
						</tr>
			<?php 
			}//end if($newmode <> "insert")
			?>
			
			<tr>
							<td colspan=3 align=center class=row2><input type=hidden name=IDPrecio id=IDPrecio value="<?php echo $r->IDPrecio ?>"><input type=hidden name=UsuarioTrCr value="<?php echo $r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?php echo $r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?php echo $r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?php echo $r->FechaTrEd ?>">
				<input type=hidden name=ID value="<?php echo $r->$Key ?>">
				<input type=hidden name=action value=<?php echo $newmode?>>
				<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
			</td>
							<td align=center class=row2 width="25%"></td>
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
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=60;
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
<td class=texto bgcolor=#DBEAF5 colspan=17 nowrap>
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
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=ValorVenta&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">ValorVenta&nbsp;<?php  if($_GET['order_by']=="ValorVenta"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Descuento&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Descuento&nbsp;<?php  if($_GET['order_by']=="Descuento"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Publicar&nbsp;<?php  if($_GET['order_by']=="Publicar"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td align="right" nowrap class=row1><?php echo number_format( $r->ValorVenta ) ?></td>
						<td align="center" nowrap class=row1><?php echo $r->Descuento ?>%</td>
						<td nowrap class=row1><?php echo $r->Publicar ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=5 nowrap>
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
					<option value="ValorVenta">Valor Venta</option>
					<option value="Descuento">Descuento</option>
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
					<option value="ValorVenta">ValorVenta</option>
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
				<input type="hidden" name="tjoin" value="">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 		
	}//End function filtrar
	
/*******************************************************************************************
	mostrarreferencias: Muestra las referencias a las que se le ha asignado la lista de precios y 
		las que no tienen lista de precios asignada
	Parametros:
			$array_puntos: array con los puntos seleccionados para generar los pedidos
	Retorna:	
			Void
*******************************************************************************************/
function mostrarreferencias($idprecio)
{
	
	$sql_referencias = "SELECT * FROM Referencia WHERE IDPrecio = '$idprecio' ORDER BY IDLinea";
	$query_referencias = db_query($sql_referencias);
	
	if(db_num_rows($query_referencias) > 0)
	{
	
		echo "<tr>
				<td align=center class=titlemedium colspan=4>
					<div align='left'>Referencias</div>
				</td>
			</tr>";
		
		$numcols = 2;
		$cont = 0;
		$linea = "";
		$tipo = "";
		
		echo "<table width=100% cellspacing='0' cellpadding='2'>";
		echo "<tr>";
		echo "<ul>";
		while( $r_referencias = db_fetch_object( $query_referencias ) )
		{
			
			if( $linea <> $r_referencias->IDLinea )
			{
				
				$tipo_viene = get_field("Tipo","Descripcion","IDTipo", get_field("Linea","IDTipo","IDLinea",$r_referencias->IDLinea)); 
				if($tipo_viene <> $tipo)
				{
					
					echo "</td>";
					if($cont % $numcols == 0 )
					{
						echo "</tr><tr><td class=rowtablesoft>";
					}
					else
					{
						echo "<td class=rowtablesoft>";
						
					}
					$cont++;
					
					echo "</ul><ul><b>".$tipo_viene."</b>";
					$tipo = $tipo_viene;
				}
				
				echo "<li>".get_field("Linea","Nombre","IDLinea",$r_referencias->IDLinea)."</li>";
				$linea = $r_referencias->IDLinea;
				
				
			}
			
			echo "Ref No. ".$r_referencias->Numero." - ".$r_referencias->Nombre."<br>";
			
	
		}//end while( $r_referencias = d_fetch_object( $query_referencias ) )
		echo "</td>";
		echo "</tr>";
		echo "</table>";
	}//end if(db_num_rows($query_referencias) > 0)
	else
		echo "No se han asignado referencias a esta lista";
}//end function mostrarreferencias($r->IDPrecio)
?>
