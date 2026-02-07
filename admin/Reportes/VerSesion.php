<body> <?php


$TitleMod ="Codificacion Especifica  ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta );

$Table = "Sesion";
$TableJoin = "";
$Key = "IDSesion";
$Title = " Sesiones Activas en el Servidor ";
$MOD = "VerSesion";
$m="VerSesion";


		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2 || $ID_Usuario == 109)
{
		switch (nvl($action)) {
			case "del" :	
				$sql_borrar = "DELETE FROM Sesion WHERE IDSesion = '$id'";
				$qry_borrar = db_query( $sql_borrar );
				list_r();
			break;
			default : 
				list_r();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");



/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($campo="", $referencia=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title;
	 	

?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="700">
		<tr>
			<td class="titlemedium"><b></b><span class="gen"><?=$Title." ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta ) ?> - <?php echo fecha(); ?></span></td>
		</tr>
	</table>
	<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
	
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td class="titlemedium">Sesion</td>
					<td class="titlemedium">Usuario</td>
					<td class="titlemedium">Fecha Inicio</td>
					<td class="titlemedium">Eliminar Sesion</td>
				</tr>
				<?php

				$sql = "SELECT * FROM Sesion  ";
				$qry = db_query( $sql );
				while( $r = db_fetch_object( $qry ) )
				{
					 $class = repetition()?"row2":"row1";	
					 $sql_usuario = " SELECT * FROM Empleado WHERE IDEmpleado = '$r->IDUsuario' ";
					 $qry_usuario = db_query( $sql_usuario );
					 $r_usuario = db_fetch_object( $qry_usuario );
				?>			
					<tr>
						<td class="<?=$class?>"><?=$r->IDSesion?></td>
						<td class="<?=$class?>"><?=$r_usuario->Nombre." ".$r_usuario->Apellidos?></td>
						<td class="<?=$class?>"><?=$r->Inicio?></td>
						<td class="<?=$class?>" align="center">
							<a href="./?mod=<?=$MOD?>&action=del&id=<?=$r->IDSesion?>">
							<img src="images/trash.gif" border="0">
							</a>
						</td>
					</tr>
							<?php
					
				}//end while referencia
				?>
			</table>
		</td>
	</tr>
				
</table>	

<?php
}// Enf function list()				
?>