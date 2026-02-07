<body><?
		
	$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "InventarioCon";
$m="Referencia";

$permisos = get_permiso($ID_Usuario,$m,$Table);
	
	if($permisos[0] >= 2)
{	
		switch ($action) {
			
			case "view" :
				print_from($IDPuntoVenta,$FechaInicio, $FechaFin);
			break;
			
			default :
				print_from("");
			break;
		
		} // End switch

}
else
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");

 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($IDPuntoVenta="", $FechaInicio="", $FechaFin=""){
 Global $Nivel,$IVA,$Mes_array,$FechaInicio, $FechaFin, $IDPuntoVenta;
 
 
?>
	
	<table width="100%">
		
		<tr>
		<td>
        <form action="./" name="frmPuntoVenta" method="post" name="Moviles">
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">
							
								Desde	<input  type="text" name="FechaInicio" class="input" value="<?=$FechaInicio?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaInicio,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="nav">
								
								Hasta	<input  type="text" name="FechaFin" class="input" value="<?=$FechaFin?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaFin,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
							<td  align='left' valign='middle' class="nav"><img src='images/house.png' border='0'  alt=''></td>
							<td align="left" valign="middle" class="nav">Puntos de Venta	<select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><? 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select> <input type="hidden" name="mod" value="ReporteClientes"><input type="hidden" name="action" value="view"></td>
							<td align="left" valign="middle" class="nav">
								<input type="submit" value="Ver Reporte" name="submit" class="submit">
							</td>
						</tr>
				
			</table>
			</form>
		</td>
		</tr>
		
		<br>
		<br>
		<?
		if( !empty( $FechaInicio ) && !empty( $FechaFin ) ){
		?>
		<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp; <br>
				<br>
				<a href="exportar/archivoventas.php?IDPuntoVenta=<?=$IDPuntoVenta?>&FechaInicio=<?=$FechaInicio?>&FechaFin=<?=$FechaFin?>">Exportar Archivo</a>
				<br>
				<br></td>
		</tr>
	<? 
	 } // END if(!empty($IDEmpresa))
	?>
	</table>
	<?						
}// Enf function print()	

?>
</body>
