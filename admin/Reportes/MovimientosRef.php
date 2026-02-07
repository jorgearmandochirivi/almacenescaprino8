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
$Title = " Consultar Movimientos ";
$MOD = "MovimientoRef";
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
	<table cellspacing='0' cellpadding='2' border='0' align='center' class="forumline" width="700" class="bordertable">
		<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
			<tr>
				<td class=maintitle colspan="2">Puntos de Venta	<select name="IDPuntoVenta" onchange="document.frmPuntoVenta.submit();" >
						<option value="">Seleccione Un Punto de Venta</option><% 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							%>
					</select></td>
			</tr>
			<tr>
			<td class=maintitle width=30%> 
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

				<input type=hidden name=action value=<?=$newmode?>>
				
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
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title;
	 	
	 	$puntoventa = $IDPuntoVenta;
	 	
	 	//VENTAS
	 	if( !empty( $FechaDesde ) && !empty( $FechaHasta ) )
	 		$qry_fechas = " AND F.FechaFactura >= '$FechaDesde' AND F.FechaFactura <= '$FechaHasta'  ";
	 	$sql = " SELECT DF.IDFactura, DF.Cantidad, R.Numero, F.FechaFactura FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PR, Referencia R
	 					WHERE F.IDPuntoVenta = '$puntoventa'
	 					$qry_fechas
	 					AND F.IDFactura = DF.IDFactura
	 					AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica
	 					AND C.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia  
	 					AND PR.IDReferencia = R.IDReferencia 
	 					AND R.Numero LIKE '".$referencia."%'
	 					GROUP BY DF.IDDetalleFactura 
	 					ORDER BY F.FechaFactura DESC";
	 	$qry = db_query( $sql );
	 	while( $r = db_fetch_array( $qry ) )
	 		$array_kardex[ $r[Numero] ][ $r[FechaFactura] ][ Venta ] += $r[Cantidad] ;	
	 	
	 	//Entradas de pedido
	 	if( !empty( $FechaDesde ) && !empty( $FechaHasta ) )
	 		$qry_fechas = " AND Fecha >= '$FechaDesde' AND Fecha <= '$FechaHasta'  ";
	 	$sql = " SELECT E.Cantidad, R.Numero, E.Fecha FROM Entrada E, PuntoVentaReferencia PR, Referencia R
	 				WHERE E.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia
	 					$qry_fechas
	 					AND PR.IDReferencia = R.IDReferencia 
	 					AND R.Numero LIKE '".$referencia."%' 
	 					ORDER BY E.Fecha DESC";
	 	$qry = db_query( $sql );
	 	while( $r = db_fetch_array( $qry ) )
	 		$array_kardex[ $r[Numero] ][ $r[Fecha] ][ EntradaPedido ] += $r[Cantidad] ;	
	 	
	 	//Entrada por Cambios
	 	if( !empty( $FechaDesde ) && !empty( $FechaHasta ) )
	 		$qry_fechas = " AND Ca.FechaCambio >= '$FechaDesde' AND Ca.FechaCambio <= '$FechaHasta'  ";
	 	$sql = " SELECT DC.IDCambio, DC.Cantidad, R.Numero, Ca.FechaCambio FROM Cambio Ca, DetalleCambio DC, CodificacionEspecifica C, PuntoVentaReferencia PR, Referencia R
	 					WHERE Ca.IDPuntoVenta = '$puntoventa'
	 					$qry_fechas
	 					AND Ca.IDCambio = DC.IDCambio
	 					AND DC.IDCodificacionEspecificaCambio = C.IDCodificacionEspecifica
	 					AND C.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia  
	 					AND PR.IDReferencia = R.IDReferencia 
	 					AND R.Numero LIKE '".$referencia."%'
	 					GROUP BY DC.IDDetalleCambio 
	 					ORDER BY Ca.FechaCambio DESC";
	 	$qry = db_query( $sql );
	 	while( $r = db_fetch_array( $qry ) )
	 		$array_kardex[ $r[Numero] ][ $r[FechaCambio] ][ EntradaCambio ] += 1 ;	

	 	//Salida por Cambios
	 	if( !empty( $FechaDesde ) && !empty( $FechaHasta ) )
	 		$qry_fechas = " AND Ca.FechaCambio >= '$FechaDesde' AND Ca.FechaCambio <= '$FechaHasta'  ";
	 	$sql = " SELECT DC.IDCambio, DC.Cantidad, R.Numero, Ca.FechaCambio FROM Cambio Ca, DetalleCambio DC, CodificacionEspecifica C, PuntoVentaReferencia PR, Referencia R
	 					WHERE Ca.IDPuntoVenta = '$puntoventa'
	 					$qry_fechas
	 					AND Ca.IDCambio = DC.IDCambio
	 					AND DC.IDCodificacionEspecifica = C.IDCodificacionEspecifica
	 					AND C.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia  
	 					AND PR.IDReferencia = R.IDReferencia 
	 					AND R.Numero LIKE '".$referencia."%'
	 					GROUP BY DC.IDDetalleCambio 
	 					ORDER BY Ca.FechaCambio DESC";
	 	$qry = db_query( $sql );
	 	while( $r = db_fetch_array( $qry ) )
	 		$array_kardex[ $r[Numero] ][ $r[FechaCambio] ][ SalidaCambio ] += $r[Cantidad] ;	

		//VentaBono
	 	if( !empty( $FechaDesde ) && !empty( $FechaHasta ) )
	 		$qry_fechas = " AND F.FechaFacturaBono >= '$FechaDesde' AND F.FechaFacturaBono <= '$FechaHasta'  ";
	 	$sql = " SELECT DF.IDFacturaBono, DF.Cantidad, R.Numero, F.FechaFacturaBono FROM FacturaBono F, DetalleFacturaBono DF, CodificacionEspecifica C, PuntoVentaReferencia PR, Referencia R
	 					WHERE F.IDPuntoVenta = '$puntoventa'
	 					$qry_fechas
	 					AND F.IDFacturaBono = DF.IDFacturaBono
	 					AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica
	 					AND C.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia  
	 					AND PR.IDReferencia = R.IDReferencia 
	 					AND R.Numero LIKE '".$referencia."%'
	 					GROUP BY DF.IDDetalleFacturaBono 
	 					ORDER BY F.FechaFacturaBono DESC";
	 	$qry = db_query( $sql );
	 	while( $r = db_fetch_array( $qry ) )
	 		$array_kardex[ $r[Numero] ][ $r[FechaFacturaBono] ][ VentaBono ] += $r[Cantidad] ;	


		//Movimientos
	 	if( !empty( $FechaDesde ) && !empty( $FechaHasta ) )
	 		$qry_fechas = " AND M.Fecha >= '$FechaDesde' AND M.Fecha <= '$FechaHasta'  ";
	 	$sql = " SELECT DM.IDMovimiento, DM.Cantidad, R.Numero, M.IDTIpoMovimiento FROM Movimiento M, DetalleMovimiento DM, CodificacionEspecifica C, PuntoVentaReferencia PR, Referencia R
	 					WHERE M.IDPuntoVenta = '$puntoventa'
	 					$qry_fechas
	 					AND M.IDMovimiento = DM.IDMovimiento
	 					AND DM.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia  
	 					AND PR.IDReferencia = R.IDReferencia 
	 					AND R.Numero LIKE '".$referencia."%'
	 					GROUP BY DM.IDDetalleMovimiento 
	 					ORDER BY M.Fecha DESC";
	 	$qry = db_query( $sql );
	 	
	 	//Consulta tipos de movimiento
	 	$sql_tipo = " SELECT IDTipoMovimiento, NombreMovimiento FROM TipoMovimiento ";
	 	$qry_tipo = db_query( $sql_tipo );
	 	while( $r_tipo = db_fetch_array( $qry_tipo ) )
	 		$array_tipo[ $r_tipo[IDTipoMovimiento] ] = $r_tipo[NombreMovimiento];
	 	
	 	while( $r = db_fetch_array( $qry ) )
	 		$array_kardex[ $r[Numero] ][ $r[Fecha] ][ $array_tipo[ $r[IDTIpoMovimiento] ] ] += $r[Cantidad] ;	


		//Salida traslado origen
	 	if( !empty( $FechaDesde ) && !empty( $FechaHasta ) )
	 		$qry_fechas = " AND T.Fecha >= '$FechaDesde' AND T.Fecha <= '$FechaHasta'  ";
	 	$sql = " SELECT DT.IDTraslado, DT.Cantidad, R.Numero, T.Fecha FROM Traslado T, DetalleTraslado DT, CodificacionEspecifica C, PuntoVentaReferencia PR, Referencia R
	 					WHERE T.IDPuntoVentaOrigen = '$puntoventa'
	 					$qry_fechas
	 					AND T.IDTraslado = DT.IDTraslado
	 					AND DT.IDCodificacionEspecifica = C.IDCodificacionEspecifica
	 					AND C.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia  
	 					AND PR.IDReferencia = R.IDReferencia 
	 					AND R.Numero LIKE '".$referencia."%'
	 					GROUP BY DT.IDDetalleTraslado 
	 					ORDER BY T.Fecha DESC";
	 	$qry = db_query( $sql );
	 	while( $r = db_fetch_array( $qry ) )
	 		$array_kardex[ $r[Numero] ][ $r[Fecha] ][ SalidaTraslado ] += $r[Cantidad] ;	

		//Entrada traslado destino
	 	if( !empty( $FechaDesde ) && !empty( $FechaHasta ) )
	 		$qry_fechas = " AND T.Fecha >= '$FechaDesde' AND T.Fecha <= '$FechaHasta'  ";
	 	$sql = " SELECT DT.IDTraslado, DT.Cantidad, R.Numero, T.Fecha FROM Traslado T, DetalleTraslado DT, CodificacionEspecifica C, PuntoVentaReferencia PR, Referencia R
	 					WHERE T.IDPuntoVentaDestino = '$puntoventa'
	 					$qry_fechas
	 					AND T.IDTraslado = DT.IDTraslado
	 					AND DT.IDCodificacionEspecifica = C.IDCodificacionEspecifica
	 					AND C.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia  
	 					AND PR.IDReferencia = R.IDReferencia 
	 					AND R.Numero LIKE '".$referencia."%'
	 					GROUP BY DT.IDDetalleTraslado 
	 					ORDER BY T.Fecha DESC";
	 	$qry = db_query( $sql );
	 	while( $r = db_fetch_array( $qry ) )
	 	{
	 		$array_kardex[ $r[Numero] ][ $r[Fecha] ][ EntradaTraslado ] += $r[Cantidad] ;
	 		array_multisort( $array_kardex );
	 		//asort( $array_kardex[ $r[Numero]  ]);
	 	}//end while	
	 	//ordenar por referencia -> Fecha
?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="700">
		<tr>
			<td class="titlemedium"><b></b><span class="gen"><?=$Title." ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta ) ?> - <? echo fecha(); ?></span></td>
		</tr>
	</table>
	<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
	

	<tr>
		<td>
			<table width="100%">
				<tr>
					<td class="titlemedium">Referencia</td>
					<td class="titlemedium">Fecha</td>
					<td class="titlemedium">Movimiento</td>
					<td class="titlemedium">Cantidad</td>
				</tr>
				<?
				foreach( $array_kardex as $Referencia => $array_fechas )
				{
					foreach( $array_fechas as $Fecha => $TipoMovArray )
						foreach( $TipoMovArray as $TipoMovimiento => $Cantidad )
						{
				?>
				<tr>
					<td class="row1"><? echo $Referencia; ?></td>
					<td class="row1"><? echo $Fecha; ?></td>
					<td class="row1" align="right">
						<? 
							echo  $TipoMovimiento;
						?>
					</td>
					<td class="row1" align="right"><b><?=$Cantidad ?></b></td>
				</tr>
				<?
						}//end for
				?>
				<tr>
					<td class="titlemedium"><br></td>
					<td class="titlemedium"></td>
					<td class="titlemedium"></td>
					<td class="titlemedium"></td>
				</tr>

				<?
				}//end for
				?>
				
</table>	

<? 			
}// Enf function list()				
?>