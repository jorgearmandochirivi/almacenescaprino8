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

$TitleMod ="Codificacion Especifica";

$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "Inventario";
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
				$name = "Inventario$fecha.xls";
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
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="650">
		
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
			</td>
			<td class="tbtbot"><b></b>
				<span class="gen">
					<?=$Title?>
				</span>
			</td>
			<td class="tbtr">
				<img src="images/spacer.gif" alt="" width="124" height="22" />
			</td>
		</tr>
	</table>
	<table cellspacing='0' cellpadding='2' border='0' align='center' class="forumline" width="650">
		<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
		<tr>
			<td class=col1 width=30;?> 
				Buscar Referencia Por
			</td>
			<td class="col2">
				&nbsp;&nbsp;&nbsp;	
				<select name="campo" class="input">
					<option value="Numero">Numero</option>
					<option value="Nombre">Nombre</option>
				</select>
				&nbsp;&nbsp;&nbsp;
				<input type=text class=tbox name=referencia>
				<input type="submit" class="button" name="enviar" value="Consultar">

				<input type=hidden name=action value=<?=$newmode?>>
				
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

?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="650">
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" /></td>
			<td class="tbtbot"><b></b><span class="gen"><?=$Title?> - <?php echo $campo.":".$referencia;  ?></span></td>
			<td class="tbtr"><img src="images/spacer.gif" alt="" width="124" height="22" /></td>
		</tr>
	</table>
	<table width=650 cellpadding=0 cellspacing=0 align=center class=bordertable>
	
	
<?php

//seleccionar tallas
$sql_tallas = " SELECT * FROM Talla WHERE Publicar = 'S' ORDER BY Descripcion ";
$qry_tallas = db_query( $sql_tallas );
while( $r_tallas = db_fetch_array( $qry_tallas ) )
{
	//$array_tallas[$r_tallas[IDTalla]] = $r_tallas; 
	//con descripcion
	$array_tallas[$r_tallas[Descripcion]] = $r_tallas; 
}//end while
?>
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td class="navpic">Referencia</td>
					<?php
					foreach( $array_tallas as $idtalla => $datostallas )
					{
					?>
						<td class="navpic"><?php echo  $datostallas[Descripcion] ?></td>
					<?php
					}//end for
					?>
					<td class="navpic">Total</td>
				</tr>
				<?php

				$sql_referencia = "SELECT * FROM Referencia ORDER BY Numero, Sexo ";
				$qry_referencia = db_query( $sql_referencia );
				while( $r_referencia = db_fetch_object( $qry_referencia ) )
				{
					 	
				 	$ref = $r_referencia->IDReferencia;
				 	
				 	$sql =  "SELECT * FROM $Table CE, Referencia R, PuntoVentaReferencia PR WHERE PR.IDPuntoVenta = '$puntoventa' AND R.IDReferencia = '$ref' ";
				 	$sql .= "AND R.IDReferencia = PR.IDReferencia ";
				 	$sql .= "AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia ";
				 
					$query_codificacion = db_query($sql);
					$rows = db_num_rows($query_codificacion);
					$array_codificacion = array( );
					while($r_codificacionesp = db_fetch_array($query_codificacion))
					{
						$array_codificacion[ $ref ][ $r_codificacionesp[IDTalla] ] = array( "Numero"=>$r_referencia->Numero,"Existencia"=>$r_codificacionesp[Existencias] );
					}//end while
					$totalreferencia = 0;
					foreach( $array_codificacion as $ref => $arraydatos )
					{
						
						//Totallizar la linea
						if( $linea <> substr( $r_referencia->Numero, 0, 2 )  )
						{
				?>			<tr>
								<td class="row0">Totales <?=$linea ?></td>
								<?php
								foreach( $array_tallas as $idtalla => $datostallas )
								{
								?>
									<td class="row0">
										<?php 
											echo  $array_linea[$linea][$idtalla];
										?>
									</td>
								<?php
									//print_r( $arraydatos );
								
								}//end for
								?>
								<td class="row0"><b><?=array_sum( $array_linea[$linea] ) ?></b></td>
							</tr>
				<?php			
						}//end if
						
						$linea = substr( $r_referencia->Numero, 0, 2 );
						
						$mostrar = 0;
						foreach( $array_tallas as $idtalla => $datostallas )
						{
							if( $arraydatos[$idtalla][Existencia] > 0 )
								$mostrar = 1;
						}//end for
						if( $mostrar == 1 )
						{
						
				?>
						<tr>
							<td class="row1"><?=$r_referencia->Numero ?></td>
							<?php
							foreach( $array_tallas as $idtalla => $datostallas )
							{
							?>
								<td class="row1">
									<?php 
										echo  $arraydatos[$idtalla][Existencia];
										$array_linea[ $linea ][ $idtalla ] += 	$arraydatos[$idtalla][Existencia];
										$totales[ $idtalla ] += $arraydatos[$idtalla][Existencia];
										$totalreferencia +=  $arraydatos[$idtalla][Existencia];
									?>
								</td>
							<?php
								//print_r( $arraydatos );
							
							}//end for
							?>
							<td class="row1"><b><?=$totalreferencia ?></b></td>
						</tr>
				<?php
						}//end if mostrar
					}//end if
					
				}//end while referencia
				?>
				
				<tr>
					<td class="navpic">Total</td>
					<?php
					foreach( $array_tallas as $idtalla => $datostallas )
					{
					?>
						<td class="navpic">
							<?php 
								echo  $totales[$idtalla];
							?>
						</td>
					<?php
						//print_r( $arraydatos );
					
					}//end for
					?>
					<td class="navpic"><?=array_sum( $totales )?></td>
				</tr>
			
			
			
					

</table>	

<?php
}// Enf function list()				
?>