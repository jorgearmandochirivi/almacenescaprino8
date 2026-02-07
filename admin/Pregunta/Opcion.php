<body> <?

$TitleMod ="Opciones";

$Table = "FidOpcion";
$TableJoin = "FidClienteRespuesta";
$Key = "IDFidOpcion";
$MOD = "Opciones";
$m="Opciones";
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
				
				echo "<SCRIPT>location.href='?mod=Opciones&action=edit&idPregunta=" . $frm["idnot"] . "&id=" . $frm[$Key] . "';</SCRIPT>";
			break;
			
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$HTTP_GET_VARS[action]="";
				delete($ID);
			break;
			case "list" :	
				list_r($HTTP_POST_VARS['puntoventa']);
			break;
			default : 
				list_r();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Opcion_Info("No tiene Permisos Suficientes","row2");


/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
function list_r($puntoventa){
	Global $TitleMod,$MOD,$Table,$Key,$listar,$idPregunta;
	$sql =  "SELECT * FROM $Table  WHERE  IDFidPregunta = '" . $idPregunta . "' ";
	 	
	$qry = db_query($sql);
	$rows = db_num_rows($qry);
?>

<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width="76%">&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/folderopen.gif" border="0" > 
			<a href="./?mod=Fidelizacion">Administrar Preguntas</a> </td>
		<td>
		<td><a href="./?mod=<%=$MOD%>&action=add&idPregunta=<?=$idPregunta ?>"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>


	
<br>
<?
	$TABsel = 2;
	$IDFidPregunta = $idPregunta;
 	include("Pregunta/menutabFidPregunta.php");
?>	
<table width=500 cellpadding=0 cellspacing=0 align=left class=bordertable>
	<tr>
        <td class=maintitle  bgcolor=#9daac6>Se encontraron <% echo $rows;%> opciones</td>
    </tr>
    <tr>
		<td class="maintitle" bgcolor="#9daac6">

<?
if($rows > 0){
?>	

            <table width=100% border=0 cellspacing=1 cellpadding=0>

				

                <tr>
                    <td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
                    <td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Opcion</a><a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>&nbsp;<% if($_GET['order_by']=="Opcion"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
                    <td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
                </tr>
            
            <? 
                while($r = db_fetch_object($qry))
                {
            ?>
                
                <tr>
                    <td align=center valign=middle nowrap width=50 class=row2>
                        &nbsp;<a href='<? echo "?mod=$MOD&action=edit&idPregunta=" . $idPregunta . "&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
                    </td>
                    <td nowrap class=row1><? echo substr( $r->Opcion, 0, 100 ) ?></td>
                    <td align=center valign=middle nowrap width=60 class=row2>
                        &nbsp;&nbsp;<a href='<? echo "?mod=$MOD&action=del&idPregunta=" . $idPregunta . "&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
                    </td>
                </tr>
            <?
                } // END for
            ?>
            </table>

<?

}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";

?>


		</td>
	</tr>
</table>	

<?
}// Enf function list()		



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key, $idPregunta, $idnot;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);
	
	if( empty( $idPregunta ) )
		$idPregunta = $idnot;

?>
<script>
var Check = new Array('Opcion');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
			<td><a href="./?mod=<%=$MOD%>&action=add&idPregunta=<?=$idPregunta?>"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>
<br>
<?
	$TABsel = 2;
	$IDFidPregunta = $idPregunta;
 	include("Pregunta/menutabFidPregunta.php");
?>	

	
<table cellpadding=1 cellspacing=0 class=bordertable align="left" >
<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?if($newmode!="delete"){?> onSubmit="return EvaluaReg(this,Check)" <?}?>>
    <tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<? echo $TitleMod ?> <? echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
			<td>Opcion</td><td><textarea name="Opcion" rows="4" cols="40"><?=$r->Opcion?></textarea></td>
			</tr>
						<tr>
			<td colspan=2 align=center class=row2>
            	<input type=hidden name=IDFidOpcion value="<?=$r->IDFidOpcion ?>">
                <input type=hidden name=ID value="<? echo $r->$Key ?>">
				<input type=hidden name=action value=<?=$newmode?>>
				<input type=hidden name=idPregunta value=<?=$idPregunta?>>
				<input type=hidden name=idnot value=<?=$idPregunta ?>>
				<input type=hidden name=IDFidPregunta value=<?=$idPregunta?>>
				<input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			</td>
				</tr>
			</table>
		</td>
	</tr>
</form>    
</table>

<?
}// End function print_form()


		
?>
