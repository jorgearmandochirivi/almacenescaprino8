<body> <?php


$TitleMod ="Log de Accesos al Sistema  ";

$Table = "LogAcceso";
$TableJoin = "";
$Key = "IDLog";
$Title = " Log de Accesos al Sistema ";
$MOD = "LogAcceso";
$m="LogAcceso";


		//$permisos = get_permiso($ID_Usuario,$m,$Table);
$permisos[0] = 2;		
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			
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
			<td class="titlemedium"><b></b><span class="gen"><?=$Title ?></span></td>
		</tr>
	</table>
	<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
	
	<tr>
		<td>
        
        
        <table width='60%' align='center' border="0" cellspacing="0" cellpadding="2" class="bordertable">
					<form action="./" name="frmPuntoVenta" method="post">
						<tr>
						  <td  align='left' valign='middle' class="nav"> Desde
					      </td>
						  <td align="left" valign="middle" class="nav">
						  <input  type="text" name="FechaDesde" class="input" value="<?=$_POST["FechaDesde"]?>" size="10">
                          <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
                          
                          </td>
						  <td width="3%"  align='left' valign='middle' class="nav">Hasta</td>
							<td width="28%" align="left" valign="middle" class="nav"><input  type="text" name="FechaHasta" class="input" value="<?=$_POST["FechaHasta"]?>" size="10">
                            <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
                            </td>
						</tr>
						<tr>
						  <td  align='left' valign='middle' class="nav">Puntos de Venta</td>
						  <td align="left" valign="middle" class="nav"><select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
						    <option value="">Seleccione Un Punto de Venta</option>
						    <?php  								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
						    </select></td>
						  <td  align='left' valign='middle' class="nav">&nbsp;</td>
						  <td align="left" valign="middle" class="nav">&nbsp;</td>
					  </tr>
						<tr>
						  <td colspan="4"  align='center' valign='middle' class="nav"><input type="submit" value="Ver Reporte" name="submit" class="submit">
						    <input type="hidden" name="mod" value="LogAcceso">
                          <input type="hidden" name="action" value="view"></td>
					  </tr>
					</form>
				</table>
        
        
			<table width="100%">
				<tr>
					<td class="titlemedium">Usuario</td>
					<td class="titlemedium">Fecha </td>
					<td class="titlemedium">IP </td>
				</tr>
				<?php
				
					if($_POST["IDPuntoVenta"])	
						$condicion_filtro .= " and IDPuntoVenta = '".$_POST["IDPuntoVenta"]."' ";

				$sql = "SELECT * 
					   FROM LogAcceso 
					   Where DATE_FORMAT( Fecha,'%Y-%c-%d' ) >= DATE_FORMAT('".$_POST["FechaDesde"]."','%Y-%c-%d' ) 
					   AND DATE_FORMAT( Fecha,'%Y-%c-%d' ) <= DATE_FORMAT('".$_POST["FechaHasta"]."','%Y-%c-%d' ) 
					   ".$condicion_filtro."
					   Group by IDPuntoVenta, DATE_FORMAT( Fecha,'%Y-%c-%d' )
					   ORDER BY Fecha ASC Limit 100  ";
				$qry = db_query( $sql );
				while( $r = db_fetch_object( $qry ) )
				{
					 $class = repetition()?"row2":"row1";	
					 $sql_usuario = " SELECT * FROM Empleado WHERE IDEmpleado = '$r->IDUsuario' ";
					 $qry_usuario = db_query( $sql_usuario );
					 $r_usuario = db_fetch_object( $qry_usuario );
				?>			
					<tr>
						<td class="<?=$class?>"><?=$r_usuario->Nombre . " " . $r_usuario->Apellidos ?></td>
						<td class="<?=$class?>"><?=$r->Fecha ?></td>
						<td class="<?=$class?>"><?=$r->DireccionIP ?></td>
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