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
					
				seleccionareferencia("list");
				//list_r();
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
		<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
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
	function list_r($campo, $referencia){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title,$IDPuntoVentaR, $FechaDesde, $FechaHasta;
	 	

 if(strtotime($FechaDesde)<=strtotime("2017-01-31")):
 	$IVA = 0.16;
 endif;
 

?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="820">
		<tr>
			<td class="maintitle"><b></b><span class="gen"><?=$Title?> - <? echo $campo.":".$referencia;  ?> Los Almacenes que no apareces, no tienen venta para esta referencia</span></td>
		</tr>
	</table>
	<table width=820 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
		<td class="titlemedium" >
				<table cellspacing='0' cellpadding='2' border='0' align='center'  width="820">
					<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
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
if( !empty( $referencia ) )	
{
	
	$sql_puntos = " SELECT IDPuntoVenta, Nombre FROM PuntoVenta WHERE Publicar = 'S' ";
	$qry_puntos = db_query( $sql_puntos );
	while( $r_puntos = db_fetch_array( $qry_puntos ) )
		$array_puntos[$r_puntos[IDPuntoVenta]] = $r_puntos[Nombre];
 	
 	//Tallas
	$sql_tallas = " SELECT IDTalla, Descripcion FROM Talla ";
	$qry_tallas = db_query( $sql_tallas );
	while( $r_tallas = db_fetch_array( $qry_tallas ) )
		$array_tallas[$r_tallas[IDTalla]] = $r_tallas[Descripcion];
	 	
	 	 $sql = " SELECT F.IDPuntoVenta as IDPuntoFactura,DF.Cantidad, DF.IDPuntoVenta, R.Numero, CE.IDTalla FROM $Table CE, Referencia R, PuntoVentaReferencia PR, Factura F, DetalleFactura DF
	 				WHERE R.Numero LIKE '%$referencia%' 
	 				AND R.IDReferencia = PR.IDReferencia
	 				AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia
	 				AND CE.IDCodificacionEspecifica = DF.IDCodificacionEspecifica
	 				AND DF.IDFactura = F.IDFactura
	 				AND F.FechaFactura >= '$FechaDesde 00:00:00' AND F.FechaFactura <= '$FechaHasta 23:59:59'					
					";
	 	
	 
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
							if($r_codificacionesp[IDPuntoFactura]==$r_codificacionesp[IDPuntoVenta]):
								$array_ventas[ $r_codificacionesp[IDPuntoVenta] ][ $r_codificacionesp[IDTalla] ] += $r_codificacionesp[Cantidad];
								$array_tallas_mostrar[ $r_codificacionesp[IDTalla] ] = $array_tallas[ $r_codificacionesp[IDTalla] ];
							endif;	
						} //end while($r[$i] = db_fetch_array($query_codificacion))
						//print_r($r);
					?>
					
					<table width="100%" border="0" cellspacing="1" cellpadding="0">
						<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
							<tr>
								
								<td class="rowform">PUNTOS DE VENTA</td>	
								<?
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									//if( array_sum($array_existencias[ $idpunto ]) > 0 )
										echo "<td class=rowform align=center>".$nombre."</td>";
								}//end for
								?>	
								<td class="rowform">TOTALES</td>
							</tr>
							
							<?
							foreach($array_puntos as $idpunto => $nombre)
							{
							?>
							<tr>
								<td class=rowform align=center><? echo $nombre; ?></td>
								<?
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									echo "<td class=row1 align=center>".$array_ventas[ $idpunto ][ $idtalla ]."</td>";
									$totaltalla[ $idtalla ] += $array_ventas[ $idpunto ][ $idtalla ];
									$totalpunto[ $idpunto ] += $array_ventas[ $idpunto ][ $idtalla ];
									
								}//end for
								?>	
								<td class="rowform"><? echo $totalpunto[ $idpunto ]; ?></td>
							</tr>
							<?
							}//end for
							?>
							
							<tr>
								<td class="rowform">TOTALES</td>	
								<?
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									//if( array_sum($array_existencias[ $idpunto ]) > 0 )
										echo "<td class=rowform align=center>".$totaltalla[ $idtalla ]."</td>";
								}//end for
								?>	
								<td class="rowform"><? echo array_sum( $totalpunto ); ?></td>
							</tr>
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