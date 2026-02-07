<body> <?

$TitleMod ="Codificacion Especifica";

$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "ConsolidadoVentas";
$m="Referencia";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "list" :	
				list_r($_POST['campo'],$_POST['referencia']);
			break;
			default : 
					
				list_r();
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
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="820">
		
		<tr>
			<td class="maintitle"><b></b>
				<span class="gen">
					<?=$Title?>
				</span>
			</td>
		</tr>
	</table>
	<table cellspacing='0' cellpadding='2' border='0' align='center' class="bordertable" width="820">
		<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
			<tr>
			<td class=col1 width=30%> 
				Buscar Referencia Por
			</td>
				<td class="col2" nowrap>
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
				<td align="left" valign="middle" class="nav" nowrap>Desde <input type="text" name="FechaDesde" class="input" value="<?=date( "Y-m-d" )?>" size="10"> 
					<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
				</td>
				<td align="left" valign="middle" class="nav" nowrap>Hasta <input type="text" name="FechaHasta" class="input" value="<?=date( "Y-m-d" )?>" size="10"> 
					<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
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
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$numeroReferenciaVenta,$Title,$numeroReferenciaVentaR, $FechaDesde, $FechaHasta;
	 	

?>
	<br>
	Los datos se muestran de la forma :<br>
	Ventas<br>
	Inventario<br>
	Ventas / ( Ventas + Inventario )<br>
	<br>
	<?
	if( !empty( $campo ) && !empty( $referencia ) )
		$Titulo = " - ". echo $campo.":".$referencia;
	?>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="820">
		<tr>
			<td class="maintitle"><b></b><span class="gen"><?=$Title?> <? echo $Titulo ?> </span></td>
		</tr>
	</table>
	<table width=820 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
		<td class="titlemedium" >
				<table cellspacing='0' cellpadding='2' border='0' align='center'  width="820">
					<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
						<tr>
							<td class=col1 width=30%> 
				Buscar Referencia Por
				</td>
							<td class="col2" nowrap>
				&nbsp;&nbsp;&nbsp;	<select name="campo" class="input">
									<option value="Numero">Numero</option>
									<option value="Nombre">Nombre</option>
								</select>
				&nbsp;&nbsp;&nbsp;
		<input type=text class=tbox name=referencia value="<?=$referencia ?>">
		<input type="submit" class="button" name="enviar" value="Consultar">
		<input type=hidden name=action value='<?="list"?>'></td>
							<td align="left" valign="middle" class="nav" nowrap>Desde <input type="text" name="FechaDesde" class="input" value="<?=$FechaDesde?>" size="10"> 
								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
							<td align="left" valign="middle" class="nav" nowrap>Hasta <input type="text" name="FechaHasta" class="input" value="<?=$FechaHasta?>" size="10"> 
								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
						</tr>
					</form>
				</table>
			</td>
	</tr>
<?

$sql_puntos = " SELECT IDPuntoVenta, Nombre FROM PuntoVenta ORDER BY IDCiudad, Nombre ";
$qry_puntos = db_query( $sql_puntos );
while( $r_puntos = db_fetch_array( $qry_puntos ) )
	$array_puntos[$r_puntos[IDPuntoVenta]] = $r_puntos[Nombre];

//Tallas
	$sql_tallas = " SELECT IDTalla, Descripcion FROM Talla ORDER BY Descripcion ";
	$qry_tallas = db_query( $sql_tallas );
	while( $r_tallas = db_fetch_array( $qry_tallas ) )
		$array_tallas[$r_tallas[IDTalla]] = $r_tallas[Descripcion];

if( !empty( $referencia ) )	
{
	$condicion = " R.Numero LIKE '%$referencia%' AND ";
}//end if( !empty( $referencia ) )	

	 	$sql = " SELECT DF.Cantidad, DF.IDPuntoVenta, R.Numero, CE.IDTalla FROM $Table CE, Referencia R, PuntoVentaReferencia PR, Factura F, DetalleFactura DF
	 				WHERE $condicion R.IDReferencia = PR.IDReferencia
	 				AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia
	 				AND CE.IDCodificacionEspecifica = DF.IDCodificacionEspecifica
	 				AND DF.IDFactura = F.IDFactura
	 				AND F.FechaFactura >= '$FechaDesde' AND F.FechaFactura <= '$FechaHasta' ORDER BY CE.IDTalla";
	 	
		$query_codificacion = db_query($sql);
		$rows = db_num_rows($query_codificacion);

		if($rows > 0){
		?>
			<tr>
				<td class="row1">
					<? 
						$i = 0;
						$r = array( );
						while($r_codificacionesp = db_fetch_array($query_codificacion))
						{
							$array_ventas[ $r_codificacionesp[Numero] ][ $r_codificacionesp[IDTalla] ] += $r_codificacionesp[Cantidad];
						} //end while($r[$i] = db_fetch_array($query_codificacion))
						//print_r($r);
						
						//INVENTARIO
						$sql_inv =  "SELECT CE.*, PR.IDPuntoVenta, R.Numero FROM $Table CE, Referencia R, PuntoVentaReferencia PR WHERE $condicion R.IDReferencia = PR.IDReferencia ";
					 	$sql_inv .= "AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia ";
					 
						$query_codificacion_inv = db_query($sql_inv);
						$rows_inv = db_num_rows($query_codificacion_inv);
						
						$i = 0;
						$r = array( );
						while($r_codificacionesp = db_fetch_array($query_codificacion_inv))
						{
							$array_existencias[ $r_codificacionesp[Numero] ][ $r_codificacionesp[IDTalla] ] += $r_codificacionesp[Existencias];
							$array_tallas_mostrar[ $r_codificacionesp[IDTalla] ] = $array_tallas[ $r_codificacionesp[IDTalla] ];
						}
						ksort( $array_tallas_mostrar );
					?>
					
					<table width="100%" border="0" cellspacing="1" cellpadding="0">
						<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
							<tr>
								
								<td class="rowform" width="20%">Referencia</td>	
								<?
								$width=60 / count( $array_tallas_mostrar ); 
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									//if( array_sum($array_existencias[ $numeroReferencia ]) > 0 )
										echo "<td class=rowform width='".$width."%' align=center>".$nombre."</td>";
								}//end for
								?>	
								<td class="rowform" width="20%" align="right" >TOTALES</td>
							</tr>
							
							<?
							foreach( $array_existencias as $numeroReferencia => $datosreferencia )	
							{
								$class = repetition()?"row1":"row2";
							
							?>
							<tr>
								<td class=rowform align=center><? echo $numeroReferencia; ?></td>
								<?
							
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									
									echo "<td class=".$class." align=right>";
									echo $array_ventas[ $numeroReferencia ][ $idtalla ]."</br>";
									echo $array_existencias[ $numeroReferencia ][ $idtalla ]."</br>";
									echo number_format( $Rotacion = ( $array_ventas[ $numeroReferencia ][ $idtalla ] / ( $array_ventas[ $numeroReferencia ][ $idtalla ] + $array_existencias[ $numeroReferencia ][ $idtalla ] ) * 100 ) , 2 )." % ";
									echo "</td>";
									
									$TotalVentasTalla[ $idtalla ] += $array_ventas[ $numeroReferencia ][ $idtalla ];
									$TotalExistenciasTalla[ $idtalla ] += $array_existencias[ $numeroReferencia ][ $idtalla ];
									
									$TotalVentasPunto[ $numeroReferencia ] += $array_ventas[ $numeroReferencia ][ $idtalla ];
									$TotalExistenciasPunto[ $numeroReferencia ] += $array_existencias[ $numeroReferencia ][ $idtalla ];
									
									$totaltalla[ $idtalla ] += $Rotacion;
									$totalpunto[ $numeroReferencia ] += $Rotacion;
									
								}//end for
								
								
								?>	
								<td class="rowform"  align="right" ><? echo $TotalVentasPunto[ $numeroReferencia ]."<br>".$TotalExistenciasPunto[ $numeroReferencia ]."<br>";echo number_format( $Rotacion = ( $TotalVentasPunto[ $numeroReferencia ] / ( $TotalVentasPunto[ $numeroReferencia ] + $TotalExistenciasPunto[ $numeroReferencia ] ) * 100 ), 2 ); ?></td>
							</tr>
							<?
								
							}//end for
							
							?>
							<?
							/*
							<tr>
								<td class="rowform">TOTALES</td>	
								<?
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									//if( array_sum($array_existencias[ $numeroReferencia ]) > 0 )
										echo "<td class=rowform align=right>".$TotalVentasTalla[ $idtalla ]."<br>".$TotalExistenciasTalla[ $idtalla ]."<br>".number_format( $Rotacion = ( $TotalVentasTalla[ $idtalla ] / ( $TotalVentasTalla[ $idtalla ] + $TotalExistenciasTalla[ $idtalla ] ) * 100 ), 2 )."</td>";
								}//end for
								?>	
								<td class="rowform" align=right><? echo array_sum($TotalVentasTalla)."<br>".array_sum($TotalExistenciasTalla)."<br>";echo number_format( ( $Rotacion = ( array_sum($TotalVentasTalla) / ( array_sum($TotalVentasTalla) + array_sum($TotalExistenciasTalla) ) ) ) * 100, 2 ); ?></td>
							</tr>
							*/
							?>
						</form>
					</table>
				</td>
			</tr>
		<?
		}// End if$rows
		else
			echo "<tr><td><span class=col1list><b>No se encontraron registros con los par&aacute;metros proporcionados </b></span></td></tr>";
}
?>
</table>	

<? 			
}// Enf function list()				
?>