<body> <?

$TitleMod ="CostoReferencia";

$Table = "CostoReferencia";
$TableJoin = "Referencia";
$Key = "IDCostoReferencia";
$MOD = "CostoReferencia";
$m = "CostoReferencia";

$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			
			case "insertcosto" :
				
				$frm= vars_LOG($_POST);

				$id = insert($frm);

				echo "<script>location.href='?mod=CostoReferencia&idReferencia=" . $frm["IDReferencia"] . "';</script>";
				
			break;
			
			case "edit":
				list_r($_GET["idReferencia"],"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);
				$sql_actualiza = db_query("Update CostoReferencia Set Costo = '".$frm[Costo]."', Fecha = '".$frm[Fecha]."', Observacion = '".$frm[Observacion]."', UsuarioTrEd = '".$ID_Usuario."', FechaTrEd = NOW() Where IDCostoReferencia = '".$frm[IDCostoReferencia]."'");
				?>
                <script>
					alert("Costo actualizado");
				</script>
                <?php
				list_r( $_GET["idReferencia"],"insertcosto" );
			break;
			case "del":
				list_r($_GET["idReferencia"],"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				//$HTTP_GET_VARS[action]="";
				$frm= vars_LOG($HTTP_POST_VARS);
				$sql_actualiza = db_query("Delete From  CostoReferencia  Where IDCostoReferencia = '".$frm[IDCostoReferencia]."'");
				?>
				<script>
					alert("Costo actualizado");
					window.location.href="?mod=CostoReferencia&idReferencia=<?php echo $_GET["idReferencia"]; ?>";
				</script>
                <?php
				list_r( $_GET["idReferencia"],"insertcosto" );
			break;
			
			default : 
					list_r( $_GET["idReferencia"],"insertcosto","Inserta $TitleMod" );
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");




/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r( $idReferencia, $accion, $Titulo ){
		Global $TitleMod,$MOD,$Table,$Key,$listar;


	 	$sql =  "SELECT * FROM $Table WHERE IDReferencia = '" . $idReferencia . "' ORDER BY Fecha DESC ";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=1000;
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
		<td></td>
	</tr>
</table>
	
<br>
<?
	$TABsel = 3;
	include("Referencia/menutabReferencia.php");
?>

<table cellpadding=1 cellspacing=0 class=bordertable align=left width="100%" >
<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" >

	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<? echo $TitleMod ?> <? echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
    <?php
    if (!empty($_GET[IDCostoReferencia])):
		$sql_costo = db_query("Select * From " .$Table." Where IDCostoReferencia = '".$_GET[IDCostoReferencia]."'");
		$row_costo = db_fetch_array($sql_costo);
	endif;
	?>
    
		<table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>
			<tr class=row2>
				<td width="200"> 
            		Costo 
             	</td>
                <td>
                	 <input type=text size=25 class=input   name=Costo id=costo value="<?php echo (int)$row_costo[Costo] ?>">
                </td>
            </tr>
            
            <tr class=row2>
				<td width="200"> 
            		fecha
             	</td>
                <td>
                	 <input type=text size=25 class=input   name=Fecha id=Fecha value="<?php echo $row_costo[Fecha] ?>">
                	 <script language="JavaScript1.2">
						<!--
							if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
						//-->
					</script>
                </td>
            </tr>  
            
            <tr class=row2>
				<td width="200"> 
            		Observaciones
             	</td>
                <td>
              		<textarea name="Observacion" id="Observacion" rows="5" cols="30" class="input" ><?php echo $row_costo[Observacion] ?></textarea>
                </td>
            </tr> 
            <tr>
				<td colspan=3 align=center class=row2>
                    <input type=hidden name=IDReferencia id=IDReferencia value="<?=$idReferencia ?>">
                    <input type=hidden name=IDCostoReferencia id=IDCostoReferencia value="<?=$_GET[IDCostoReferencia] ?>">
                    <input type=hidden name=action value="<?php echo $accion; ?>">
                    <input type=hidden name=mod value="CostoReferencia">
                    <input type=submit name=submit value="<?php echo $Titulo ?>" class="submit">
				</td>
			</tr>        
       	</table>
  	</td>
 </tr>
 </form>
 </table>
<br><br><br><br>
<table width="100%" cellpadding=0 cellspacing=0 align=left class=bordertable>
	
    
<tr>
			<td class=titlemedium  bgcolor=#9daac6><% echo $info;%></td>
		</tr>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=10 nowrap>
<?
	print $pages;
?>
</td>
</tr>

<?
		if($rows > 0){
?>	

	<tr>
			<td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
  <td class=rowform nowrap bgcolor=#DBEAF5>Editar</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Costo </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Fecha </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Observaci&oacute;n </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Eliminar </td>
					</tr>

<? while($r = db_fetch_object($result)){
?>
  	
<tr>
  <td nowrap class=row1><a href='<? echo "?mod=$MOD&action=edit&IDCostoReferencia="; echo $r->$Key; ?>&idReferencia=<?php echo $_GET[idReferencia]?>'><img src='images/edit.gif' border='0'></a></td>
						<td nowrap class=row1><? echo number_format( $r->Costo, 2 ) ?></td>
						<td nowrap class=row1><? echo $r->Fecha ?></td>
						<td nowrap class=row1><? echo $r->Observacion ?></td>
                        <td nowrap class=row1>
                        <a href='<? echo "?mod=CostoReferencia&action=del&IDCostoReferencia="; echo $r->$Key; ?>&idReferencia=<?php echo $_GET[idReferencia]?>'><img src='images/trash.gif' border='0'></a>	
                        </td>
					</tr>
<? } // END for
?>
<tr>
</tr>	

<?
}// End if$rows
else
	echo "<tr><td><br><br><span class=subtitle><b>No han sido cargados costos a esta referencia</b></span></td></tr>";
?>
	
</table></td>
		</tr>
</table>	

<? 			

}// Enf function list()				

?>