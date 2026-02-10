<body> <?php

function header_export($file){


	$filename = $file.date('m_d_Y_H_i').".xls";
	
	header("Pragma: ");
	header("Cache-control: ");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i ")." GMT");
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=$filename");

} // End funtion header_export

$TitleMod ="Codificacion Especifica  ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta );

$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "InventarioCon";
$m="Referencia";

$filedir = $dirroot."files/";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "list" :	
				list_r($HTTP_POST_VARS['campo'],$HTTP_POST_VARS['referencia']);
			break;
			default : 
				ob_start();	
				//seleccionareferencia("list");
				list_r();
				$page = ob_get_contents();
				$fecha = date( "Y-m-d" );
				$name = "Precios$fecha.xls";
				$file = $filedir.$name;
				
				$fw = fopen($file, "w");
				fputs($fw,$page,strlen($page));
				fclose($fw);
				ob_end_clean();
				
				header_export($file);
				echo $page;
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
	GLOBAL $Title;
?>	
	<br><br><br><br>
	<table cellspacing='0' cellpadding='2' border='0' align='center' class="forumline bordertable" width="700" >
		<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
			<tr>
				<td class=maintitle colspan="2">Puntos de Venta	<select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
						<option value="">Seleccione Un Punto de Venta</option><?php 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
					</select></td>
			</tr>
			<tr>
			<td class=maintitle width=30;?> 
				Buscar Referencia Por
			</td>
			<td class="maintitle">
				&nbsp;&nbsp;&nbsp;	
				<select name="campo" class="input">
					<option value="Numero">Numero</option>
					<option value="Nombre">Nombre</option>
				</select>
				&nbsp;&nbsp;&nbsp;
				<input type=text class=tbox name=referencia>
				<input type="submit" class="button" name="enviar" value="Consultar">

				<input type=hidden name=action value=<?php echo $newmode?>>
				
			</td>
		</tr>
		</form>
	</table>
<?php
}//end function seleccionapuntoventa($idreferencia)


/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($campo="", $referencia=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title;
	 	
	 	$puntoventa = $IDPuntoVenta;
	 	
	 	$sql_precios = " SELECT * FROM Precio ";
	 	$qry_precios = db_query( $sql_precios );
	 	while( $r_precios = db_fetch_array( $qry_precios ) )
	 		$arrayprecios[ $r_precios["IDPrecio"] ] = $r_precios;
	 		
	 	//print_r( $arrayprecios );
?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="700">
		<tr>
			<td class="titlemedium"><b></b><span class="gen"><?php echo $Title." ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta ) ?> - <?php echo fecha(); ?></span></td>
		</tr>
	</table>
	<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td class="titlemedium">Referencia</td>
					<td class="titlemedium">Ref Antigua</td>
					<td class="titlemedium"><?php echo  $datostallas["Descripcion"] ?></td>
					<td class="titlemedium">Descuento</td>
					<td class="titlemedium">Saldo</td>
				</tr>
				<?php

				$sql_referencia = "SELECT * FROM Referencia WHERE IDReferencia <> '160' ORDER BY  IDTipoTalla, Numero ";
				$qry_referencia = db_query( $sql_referencia );
				while( $r_referencia = db_fetch_object( $qry_referencia ) )
				{
				?>
						<tr>
							<td class="row1"><?php echo $r_referencia->Numero ?></td>
							<td class="row1"><?php echo $r_referencia->NumeroAnterior ?></td>
							<td class="row1">
								<?php 
										echo  number_format( $arrayprecios[$r_referencia->IDPrecio]["ValorVenta"],2);
									
								?>
							</td>
							<td class="row1">
								<?php 
										echo  number_format( $arrayprecios[$r_referencia->IDPrecio]["Descuento"],2);
									
								?>
							</td>
							<td class="row1">
								<?php 
									echo  $r_referencia->Saldo;
									
								?>
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
