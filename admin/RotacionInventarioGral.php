<body> <?php

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
					<?php echo $Title?>
				</span>
			</td>
		</tr>
	</table>
	<table cellspacing='0' cellpadding='2' border='0' align='center' class="bordertable" width="820">
		<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
			<tr>
			<td class=col1 width=30;?> 
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

				<input type=hidden name=action value=<?php echo $newmode?>>
				
			</td>
				<td align="left" valign="middle" class="nav" nowrap>Desde <input type="text" name="FechaDesde" class="input" value="<?php echo date( "Y-m-d" )?>" size="10"> 
					<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
				</td>
				<td align="left" valign="middle" class="nav" nowrap>Hasta <input type="text" name="FechaHasta" class="input" value="<?php echo date( "Y-m-d" )?>" size="10"> 
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
<?php
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
	<?php
	if( !empty( $campo ) && !empty( $referencia ) )
		$Titulo = " - ". echo $campo.":".$referencia;
	?>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="820">
		<tr>
			<td class="maintitle"><b></b><span class="gen"><?php echo $Title?> <?php echo $Titulo ?> </span></td>
		</tr>
	</table>
	<table width=820 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
		<td class="titlemedium" >
				<table cellspacing='0' cellpadding='2' border='0' align='center'  width="820">
					<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
						<tr>
							<td class=col1 width=30;?> 
				Buscar Referencia Por
				</td>
							<td class="col2" nowrap>
				&nbsp;&nbsp;&nbsp;	<select name="campo" class="input">
									<option value="Numero">Numero</option>
									<option value="Nombre">Nombre</option>
								</select>
				&nbsp;&nbsp;&nbsp;
		<input type=text class=tbox name=referencia value="<?php echo $referencia ?>">
		<input type="submit" class="button" name="enviar" value="Consultar">
		<input type=hidden name=action value='<?php echo "list"?>'></td>
							<td align="left" valign="middle" class="nav" nowrap>Desde <input type="text" name="FechaDesde" class="input" value="<?php echo $FechaDesde?>" size="10"> 
								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
							<td align="left" valign="middle" class="nav" nowrap>Hasta <input type="text" name="FechaHasta" class="input" value="<?php echo $FechaHasta?>" size="10"> 
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
<?php

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

	 	// Consulta optimizada: agregar ventas directamente en SQL
		$sql = " SELECT R.Numero, CE.IDTalla, SUM(DF.Cantidad) as TotalCantidad 
	 				FROM $Table CE 
	 				INNER JOIN PuntoVentaReferencia PR ON PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia
	 				INNER JOIN Referencia R ON R.IDReferencia = PR.IDReferencia
	 				INNER JOIN DetalleFactura DF ON CE.IDCodificacionEspecifica = DF.IDCodificacionEspecifica
	 				INNER JOIN Factura F ON DF.IDFactura = F.IDFactura AND DF.IDPuntoVenta = F.IDPuntoVenta
	 				WHERE $condicion F.FechaFactura >= '$FechaDesde' AND F.FechaFactura <= '$FechaHasta' 
	 				GROUP BY R.Numero, CE.IDTalla
	 				ORDER BY R.Numero, CE.IDTalla";
	 	
		$query_codificacion = db_query($sql);
		$rows = db_num_rows($query_codificacion);

		if($rows > 0){
		?>
			<tr>
				<td class="row1">
					<?php 
						// Procesar ventas agregadas
						while($r_codificacionesp = db_fetch_array($query_codificacion))
						{
							$array_ventas[ $r_codificacionesp['Numero'] ][ $r_codificacionesp['IDTalla'] ] = $r_codificacionesp['TotalCantidad'];
						}
						
						// INVENTARIO - Consulta optimizada: agregar existencias directamente en SQL
						$sql_inv = "SELECT R.Numero, CE.IDTalla, SUM(CE.Existencias) as TotalExistencias 
									FROM $Table CE 
									INNER JOIN PuntoVentaReferencia PR ON PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia
									INNER JOIN Referencia R ON R.IDReferencia = PR.IDReferencia
									WHERE $condicion 1=1
									GROUP BY R.Numero, CE.IDTalla
									ORDER BY R.Numero, CE.IDTalla";
					 
						$query_codificacion_inv = db_query($sql_inv);
						$rows_inv = db_num_rows($query_codificacion_inv);
						
						// Procesar inventario agregado
						while($r_codificacionesp = db_fetch_array($query_codificacion_inv))
						{
							$array_existencias[ $r_codificacionesp['Numero'] ][ $r_codificacionesp['IDTalla'] ] = $r_codificacionesp['TotalExistencias'];
							$array_tallas_mostrar[ $r_codificacionesp['IDTalla'] ] = $array_tallas[ $r_codificacionesp['IDTalla'] ];
						}
						ksort( $array_tallas_mostrar );
					?>
					
					<table width="100%" border="0" cellspacing="1" cellpadding="0">
						<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
							<tr>
								
								<td class="rowform" width="20%">Referencia</td>	
								<?php
								$width=60 / count( $array_tallas_mostrar ); 
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									//if( array_sum($array_existencias[ $numeroReferencia ]) > 0 )
										echo "<td class=rowform width='".$width."%' align=center>".$nombre."</td>";
								}//end for
								?>	
								<td class="rowform" width="20%" align="right" >TOTALES</td>
							</tr>
							
							<?php
							foreach( $array_existencias as $numeroReferencia => $datosreferencia )	
							{
								$class = repetition()?"row1":"row2";
							
							?>
							<tr>
								<td class=rowform align=center><?php echo $numeroReferencia; ?></td>
								<?php
							
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
								<td class="rowform"  align="right" ><?php echo $TotalVentasPunto[ $numeroReferencia ]."<br>".$TotalExistenciasPunto[ $numeroReferencia ]."<br>";echo number_format( $Rotacion = ( $TotalVentasPunto[ $numeroReferencia ] / ( $TotalVentasPunto[ $numeroReferencia ] + $TotalExistenciasPunto[ $numeroReferencia ] ) * 100 ), 2 ); ?></td>
							</tr>
							<?php
								
							}//end for
							
							?>
							<?php
							/*
							<tr>
								<td class="rowform">TOTALES</td>	
								<?php
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									//if( array_sum($array_existencias[ $numeroReferencia ]) > 0 )
										echo "<td class=rowform align=right>".$TotalVentasTalla[ $idtalla ]."<br>".$TotalExistenciasTalla[ $idtalla ]."<br>".number_format( $Rotacion = ( $TotalVentasTalla[ $idtalla ] / ( $TotalVentasTalla[ $idtalla ] + $TotalExistenciasTalla[ $idtalla ] ) * 100 ), 2 )."</td>";
								}//end for
								?>	
								<td class="rowform" align=right><?php echo array_sum($TotalVentasTalla)."<br>".array_sum($TotalExistenciasTalla)."<br>";echo number_format( ( $Rotacion = ( array_sum($TotalVentasTalla) / ( array_sum($TotalVentasTalla) + array_sum($TotalExistenciasTalla) ) ) ) * 100, 2 ); ?></td>
							</tr>
							*/
							?>
						</form>
					</table>
				</td>
			</tr>
		<?php
		}// End if$rows
		else
			echo "<tr><td><span class=col1list><b>No se encontraron registros con los par&aacute;metros proporcionados </b></span></td></tr>";
}
?>
</table>	

<?php
}// Enf function list()				
?>