<head>
	<title>
		Ver detealle Log
	</title>
	<link rel="stylesheet" href="../styles.css" type="text/css">
</head>
<body> <?
include("../config.inc.php");
$TitleMod ="Log";
$Table = "Log";
$TableJoin = "Log";
$MOD = "Log";

$permisos = get_permiso($ID_Usuario,$m,$Table);
		switch (nvl($action)) {	
	default : 
		list_r();
	break;
	} // End switch

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

	function list_r(){
		Global $TitleMod,$MOD,$Table,$Key,$IDL;
			
	 	$qry_log =db_query("SELECT Fecha,Modulo,Operacion FROM Log Where IDLog='$IDL'");
	 	$rows=db_num_rows($qry_log);
	if($rows > 0)
	{
	$r=db_fetch_object($qry_log);
	?>		
 	<table cellspacing="0" cellpadding="2" border="0" align="center" width="440" bgcolor="#FFFFFF">
		<tr>
			<td id="large"><img src="../images/folders.gif" border="0" alt=""> Detalle Transaccion</td>
		</tr>
	</table>
	<table width="440"  cellpadding=0 cellspacing=0 class=bordertable>
		<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Operacion Realizada <? echo $TitleMod ?></b></td>
		</tr>
		<tr><td>
	<table width=440 border=0 cellspacing=1 cellpadding=0>
	<tr >
		<td class=rowform nowrap bgcolor=#DBEAF5>Modulo</td>
		<td class=row3 nowrap bgcolor=#DBEAF5><? echo $r->Modulo ?></td>
		<td class=rowform nowrap bgcolor=#DBEAF5>Fecha</td>
		<td class=row3 nowrap bgcolor=#DBEAF5><? echo $r->Fecha?></td>
	</tr>
	<tr>
		<td  class=row2>Operacion Realizada</td>
		<td colspan="3" align="justify"  class=row1><? echo urldecode($r->Operacion); ?></td>
	</tr>
	<tr>
		<td class=texto bgcolor=#DBEAF5 colspan=4 nowrap align=center><input type=button name=cerrar value=Cerrar onclick="window.close()"></td>
	</tr>		
</table></td>
</tr>
</table>	

<? 			
}// End if$rows
else
	echo "<br><br><p class=subtitle align=center><b>No existen registros en  $TitleMod </b></p>";

}// Enf function list()				

?>

