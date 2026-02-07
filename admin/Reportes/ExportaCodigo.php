<body> <?php
$TitleMod ="Codificacion Especifica  ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta );

$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "ExportaCodigo";
$m="Referencia";

$filedir = $dirroot."files/";
$fileexp = $filedir."Inventario".$fecha."html";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
		

if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			default : 
				//seleccionareferencia("list");
				seleccionareferencia( "list");
				
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");

/*******************************************************************************************
	seleccionareferencia: formulario de busqueda para la referencia
	Parametros:
			$newmode : nieva accion a tomar con el submit
	Retorna:	
			Void
*******************************************************************************************/
function seleccionareferencia( $newmode)
{
	GLOBAL $Title, $MOD, $IDTipoReferencia;
?>	
	<br><br><br><br>
	<table cellspacing='0' cellpadding='2' border='0' align='center' class="forumline" width="700" class="bordertable">
		
			<tr>
				<td class=maintitle colspan="2">
					<form name="frm" action="Reportes/exportacodigos.php" method="post" onSubmit="return EvaluaReg(this,Check);">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="117">Puntos de Ventas	</td>
							<td colspan="2">
								<select name="IDPuntoVenta" >
									<option value="">Seleccione Un Punto de Venta</option>
									<?php 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre");
								while($punto = db_fetch_object($qry_punto)){
									?>
									<option value="<?php echo $punto->IDPuntoVenta;;?>">
										&nbsp;&nbsp;<?php echo $punto->Nombre ?></option>";
								<?php
								}
							?>
								</select>
								</td>
							
						</tr>
						<tr>
							
							<td colspan="3">
				<input type="submit" class="button" name="enviar" value="Consultar">

				<input type="hidden" name="Exportar" value="S" /><input type=hidden name=mod value="<?php echo $MOD?>"></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
		
	</table>
	<?php
}//end function seleccionapuntoventa($idreferencia)

?>