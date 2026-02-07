<?

	include("admin/config.inc.php");
	//include("admin/lib/fidelizacion_caprino.php");
	Encabezado();
	$datos = Verifica_SesionCliente();


	$sql_fact="SELECT F.*, C.Cedula,C.Nombre, C.Apellido, PV.Nombre as PuntoVenta
						 FROM Factura F, Cliente C, PuntoVenta PV
						 WHERE F.IDCliente=C.IDCliente
						 and PV.IDPuntoVenta=F.IDPuntoVenta
						 and (YEAR(FechaFactura) = 2004 or YEAR(FechaFactura) = 2003 or YEAR(FechaFactura) = 2002 )";
	$r_fact=db_query($sql_fact);
	while($row=db_fetch_array($r_fact)){
		$sql_detall="SELECT IDDetalleFactura,R.Numero
									FROM DetalleFactura DF, CodificacionEspecifica CE, PuntoVentaReferencia PVR, Referencia R
									WHERE	DF.IDCodificacionEspecifica = CE.IDCodificacionEspecifica
									and PVR.IDPuntoVentaReferencia=CE.IDPuntoVentaReferencia
									and PVR.IDReferencia = R.IDReferencia
									and DF.IDFactura=".$row["IDFactura"]."
									and DF.IDPuntoVenta = '".$row["IDPuntoVenta"]."'";
		$r_detall=db_query($sql_detall);
		$TotalItem=db_num_rows($r_detall);
		$Referencias="";
		while($row_item=db_fetch_array($r_detall)){
			$Referencias.=$row_item["Numero"] . "-";
		}

		echo "<br>".$row["NumeroFactura"]."|". $row["FechaFactura"]."|".$row["Cedula"]."|".$row["ValorTotal"]."|".$TotalItem."|".$Referencias."|".$row["PuntoVenta"];
	}
	echo "FIN";
	exit;


	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];
	$frm = array();

	$frm["ValorVenta"] = 290000;
	$frm["IDCliente"] = 55852;
	$frm["IDPuntoVenta"] = $IDPuntoVenta;

	echo "Prueba Actualizar Puntos<br>";
	//echo actualiza_puntos_fid($frm);

	echo "<br><br>Prueba GEt Puntos<br>";
	$array_puntos = getpuntos_fid($frm);
	foreach( $array_puntos as $idparam => $value )
	{
		echo "Descuento: " . $value["ValorDescuento"] . " - " . " Cantidad: " . $value["Cantidad"] . " - " ;
		echo "Redimido: " . $value["Redimido"] . " - " . " Puntos: " . $value["Puntos"] . " - <br><br>" ;
	}//end for

?>
