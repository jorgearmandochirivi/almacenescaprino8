<body> <?
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
$fileexp = $filedir."Inventario".$fecha."html";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
		
if( $_POST['Exportar'] == 'S')
	$action = "export";




		switch (nvl($action)) {
			case "export" :	
					unlink($fileexp);
					ob_start();	
					//seleccionareferencia("list");
					seleccionareferencia( "list");
					list_r($_POST['campo'],$_POST['referencia']);
					$page = ob_get_contents();
					$fecha = date( "Y-m-d" );
					$name = "Inventario".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$_POST[IDPuntoVenta] ).$fecha.".html";
					$file = $filedir.$name;
					$fw = fopen($file, "w");
					fputs($fw,$page,strlen($page));
					fclose($fw);
					ob_end_clean();
					header_export($file);
					echo $page;
			break;
			case "list" :	
					seleccionareferencia( "list");
					list_r();
			break;
			default : 
				unlink($fileexp);
				ob_start();	
				//seleccionareferencia("list");
				seleccionareferencia( "list");
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
		<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
			<tr>
				<td class=maintitle colspan="2">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="117">Puntos de Venta	</td>
							<td><select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><% 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							%>
								</select>
								</td>
							<td>
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td width="130">Tipo de Referencia</td>
										<td><select name="IDTipoReferencia"  >
												<option value="">Seleccione Un Tipo de Referencia</option><% 								
								$qry_tiporef = db_query("SELECT * FROM TipoReferencia ORDER BY Descripcion");
								while($tiporef = db_fetch_object($qry_tiporef)){
									 echo "<option value=$tiporef->IDTipoReferencia ";if($IDTipoReferencia == $tiporef->IDTipoReferencia ) echo "selected"; echo ">&nbsp;&nbsp;$tiporef->Descripcion</option>";
								}
							%>
											</select></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td width="117">Saldo</td>
							<td><? echo formradiogroup(array('S'=>'S','N'=>'N'),$Saldo, 'Saldo'); ?>&nbsp;&nbsp;&nbsp;Exportar&nbsp;<? echo formradiogroup(array('S'=>'S','N'=>'N'),$Exportar, 'Exportar'); ?></td>
							<td><select name="campo" class="input">
									<option value="Numero">Numero</option>
									<option value="Nombre">Nombre</option>
								</select><input type=text class=tbox name=referencia>
				<input type="submit" class="button" name="enviar" value="Consultar">

				<input type=hidden name=action value=<?=$newmode?>><input type=hidden name=mod value="<?=$MOD?>"></td>
						</tr>
					</table>
				</td>
			</tr>
		</form>
	</table>
	<?
}//end function seleccionapuntoventa($idreferencia)


/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($campo="", $referencia=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title,$Saldo, $referencia, $IDTipoReferencia;
	 	
	 	$puntoventa = $IDPuntoVenta;
	 	
	 	

?>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="700">
		<tr>
			<td class="titlemedium"><b></b><span class="gen"><?=$Title." ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta ) ?> - <? echo fecha(); ?></span></td>
		</tr>
	</table>
	<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
	
	
<?

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
					<td class="titlemedium">Referencia</td>
					<?
					foreach( $array_tallas as $idtalla => $datostallas )
					{
					?>
						<td class="titlemedium"><? echo  $datostallas[Descripcion] ?></td>
					<?
					}//end for
					?>
					<td class="titlemedium">Total</td>
				</tr>
				<?
				
				if( !empty( $Saldo ) )
					$condicion = " AND Saldo = '$Saldo' ";
				if( !empty( $referencia ) )
					$condicion .= " AND Numero LIKE '".$referencia."%' ";
				if( !empty( $IDTipoReferencia ) )
					$condicion .= " AND IDTipoReferencia = '".$IDTipoReferencia."%' ";

				$sql_referencia = "SELECT * FROM Referencia WHERE IDReferencia <> '160' $condicion AND Publicar <> 'N' ORDER BY  IDTipoTalla, Numero ";
				$qry_referencia = db_query( $sql_referencia );
				while( $r_referencia = db_fetch_object( $qry_referencia ) )
				{
					 	
				 	$ref = $r_referencia->IDReferencia;
				 	
					
					/*
				 	$sql =  "SELECT *, T.Descripcion as Talla FROM $Table CE, Referencia R, PuntoVentaReferencia PR, Talla T WHERE PR.IDPuntoVenta = '$puntoventa' AND R.IDReferencia = '$ref' ";
				 	$sql .= "AND R.IDReferencia = PR.IDReferencia ";
				 	$sql .= "AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia ";
				 	$sql .= "AND CE.IDTalla = T.IDTalla GROUP BY CE.IDTalla ";
					*/
					
					$sql =  "SELECT *, T.Descripcion as Talla FROM $Table CE, Referencia R, PuntoVentaReferencia PR, Talla T WHERE PR.IDPuntoVenta = '$puntoventa' AND R.IDReferencia = '$ref' ";
				 	$sql .= "AND R.IDReferencia = PR.IDReferencia ";
				 	$sql .= "AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia ";
				 	$sql .= "AND CE.IDTalla = T.IDTalla ";
					
					
					
				 
					$query_codificacion = db_query($sql);
					$rows = db_num_rows($query_codificacion);
					$array_codificacion = array( );
					while($r_codificacionesp = db_fetch_array($query_codificacion))
					{
						$array_codificacion[ $ref ][ $r_codificacionesp[Talla] ] = array( "Numero"=>$r_referencia->Numero,"Existencia"=>$r_codificacionesp[Existencias] );
					}//end while
					$totalreferencia = 0;
					foreach( $array_codificacion as $ref => $arraydatos )
					{
						
						//Totallizar la linea
						if( $linea <> substr( $r_referencia->Numero, 0, 2 )  )
						{
							if( array_sum( $array_linea[$linea] ) > 0 )
							{
				?>			<tr>
								<td class="rowform">Totales <?=$linea ?></td>
								<?
								foreach( $array_tallas as $idtalla => $datostallas )
								{
								?>
									<td class="rowform" align="right">
										<? 
											echo  $array_linea[$linea][$idtalla];
										?>
									</td>
								<?
									//print_r( $arraydatos );
								
								}//end for
								?>
								<td class="rowform" align="right"><b><?=array_sum( $array_linea[$linea] ) ?></b></td>
							</tr>
							<?
								$array_linea = array( );
							}//end if
							?>
				<?			
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
							<td class="row1"><? echo $r_referencia->Numero ?></td>
							<?
							foreach( $array_tallas as $idtalla => $datostallas )
							{
							?>
								<td class="row1">
									<? 
										echo  $arraydatos[$idtalla][Existencia];
										$array_linea[ $linea ][ $idtalla ] += 	$arraydatos[$idtalla][Existencia];
										$totales[ $idtalla ] += $arraydatos[$idtalla][Existencia];
										$totalreferencia +=  $arraydatos[$idtalla][Existencia];
									?>
								</td>
							<?
								//print_r( $arraydatos );
							
							}//end for
							?>
							<td class="row1"><b><?=$totalreferencia ?></b></td>
						</tr>
				<?
						}//end if mostrar
					}//end if
					
				}//end while referencia
				?>
				
				<tr>
					<td class="maintitle">Total</td>
					<?
					foreach( $array_tallas as $idtalla => $datostallas )
					{
					?>
						<td class="maintitle">
							<? 

								echo  $totales[$idtalla];
							?>
						</td>
					<?
						//print_r( $arraydatos );
					
					}//end for
					?>
					<td class="maintitle"><?=array_sum( $totales )?></td>
				</tr>
			
			
			
					

</table>	

<? 			
}// Enf function list()				
?>