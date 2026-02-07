<%
	include("../config.inc.php");
	Encabezado();
	$datos = Verifica_Sesion();
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
%>
<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
		<meta name="generator" content="Adobe GoLive 6">
		<title>Caprino :: Cliente</title>
	
	</head>

	<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
		<table width="100%" cellpadding="3" cellspacing="0" border="0" align=center>
			<tr>
				<th background="titlegrad.jpg" align="left"><strong><font color="white"><%pv($app_title);%> :: Referemcias</font></strong></th>
			</tr>
		</table>
		<link rel="stylesheet" href="../styles.css?1" type="text/css">
		<%

$TitleMod ="Empleados";
$MOD = "bempleados";
$Table = "Empleado";
$TableJoin = "PuntoVenta";
$Key = "IDEmpleado";
$KeyLength = 15;
$m="Empleado";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "list" :
					$sql = make_qry_string_empleados($_GET);
					list_r($sql);
			break;
			default : 
					list_r();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/


function make_qry_string_empleados($frm){
	GLOBAL $Table,$TableJoin;
	
	$select = "Select * From $Table ";
	
		if( !empty($frm['field']) && ( $frm['field'] <> "Nombre" ) )
			$where = " Where $frm[field] LIKE '$frm[QryString]%' ORDER BY Nombre ASC ";
		elseif( !empty($frm['field']) && ( $frm['field'] == "Nombre" ) )
			$where = " Where $frm[field] LIKE '$frm[QryString]%' OR Apellidos LIKE '$frm[QryString]%' ORDER BY Nombre ASC ";
	
	$qry_string = $select.$where;

return $qry_string;

}
	
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

	function list_r($sql=""){
		Global $dblink,$TitleMod,$MOD,$Table,$Key,$campo,$form,$IDPuntoVenta, $cont;
			
	if(!empty($sql)){
	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';// (or 'alpha' 'number')
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
	
	} // if(!empty($sql)){ 
%>

		<table width="350" border="0" cellspacing="1" cellpadding="0" align=center>
			<tr>
				<td>
					<table width="100%" border="0" cellspacing="1" cellpadding="1" class=bordertable align=center> 
						<?
						if(!empty($sql))
						{
						?>
						
						<tr>
							<td  class="titlemedium" nowrap align="center">Cedula</td>
							<td  class="titlemedium" colspan="2" nowrap align="center">Nombre</td>
						</tr>
						
						<%
						}
						
						for ($y = 1; $y <= $rows; $y++) {
						
							if($y % 2 == 0)
								$class="row1";
							else
								$class="row2"; 
							
						  	$r = db_fetch_object($result);
						  	
						%>
						
						<tr>
							<td class="<?=$class?>" nowrap>
								<a href="javascript:window.opener.selempleado('<?=$r->IDEmpleado?>','<?=$r->Cedula?>','<?echo $r->Nombre." ".$r->Apellidos?>');javascript:window.close();">
									<% echo $r->Cedula %>
								</a>
							</td>
							<td class="<?=$class?>" colspan="2">
								<% echo $r->Nombre." ".$r->Apellidos %>
							</td>
						</tr>
						<%
						} // END for
						%>
						<form action="<?=$PHP_SELF?>" method=get>
							<tr>
								<td class="rowtable" colspan="3" nowrap>Por
										<select name="field">
											<option selected value="Cedula">Cedula</option>
											<option value="Nombre">Nombre</option>
										</select>
									<input class="text" type="text" size="18" name="QryString">
									<input type="hidden" name="action" value="list">
									<input type="hidden" name="mod" value="bcliente">
									<input type="submit" name="submit" value="Buscar" class="submit">
								</td>
							</tr>
						</form>
					</table>
				</td>
		</tr>
		</table>
		<% 			
}// Enf function list()				
%></body>
</html>