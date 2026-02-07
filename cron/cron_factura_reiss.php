<?php
include("../admin/config.inc.php");

function subir_ftp($Archivo,$CarpetaRemota, $Tipo){
	//SUBIR AL FTP
	if($Tipo=="Ventas"){
		$ftp_server = "newoptimeyes.eyc.cl";
		$ftp_user = "Caprino_Ventas@192.168.12.37";
		$ftp_pass = "caprino.123";
	}
	else{
		$ftp_server = "newoptimeyes.eyc.cl";
		$ftp_user = "Caprino_Productos@192.168.12.37";
		$ftp_pass = "caprino.123";
	}
	// establecer una conexión o finalizarla
	$conn_id = ftp_connect($ftp_server) or die("No se pudo conectar a $ftp_server");
	// intentar iniciar sesión
	if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
			ftp_pasv($conn_id, true);
			# Cambio al directorio especificado
			if(@ftp_chdir($conn_id,$ruta))
			{
				$ftp_carpeta_local =  $_SERVER['DOCUMENT_ROOT'] . "/cron/plano/".$Archivo;
				$ftp_carpeta_remota= $Archivo;

				# Subimos el fichero
				if(@ftp_put($conn_id,$ftp_carpeta_remota,$ftp_carpeta_local,FTP_BINARY))
					echo "<br>Fichero subido correctamente";
				else
					echo "<br>No ha sido posible subir el fichero";
			}else
				echo "<br>No existe el directorio especificado";




	} else {
	    echo "No se pudo conectar como $ftp_user\n";
			exit;
	}

	// cerrar la conexión ftp
	ftp_close($conn_id);
}

//**************************************
//ARCHIVO DE VENTAS DIARIAS
//**************************************


$carpeta="plano/";
$NombreArchivo="VENTAS".date("ymd").".txt";
$file = fopen($carpeta.$NombreArchivo, "w+");
$sep = ";"; //tabbed character
//DEL DIA
$sql_factura="SELECT F.IDPuntoVenta, F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura,DATE_FORMAT( FechaFactura,'%d-%c-%Y %k:%i:%c' ) FechaFacturaFormato, E.Cedula CedulaVendedor,E.IDEmpleado IdentificadorEmpleado
							FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P, Empleado E
							WHERE DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = CURDATE()
							AND F.IDFactura = DF.IDFactura
							AND E.IDEmpleado=F.IDEmpleado
							AND F.IDPuntoVenta = DF.IDPuntoVenta
							AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica
							AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
							AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio AND R.Reportes <> 'N'
							AND F.IDPuntoVenta=6
							ORDER BY IDPuntoVenta,F.NumeroFactura";


		/*
	 $sql_factura="SELECT F.IDPuntoVenta, F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura,DATE_FORMAT( FechaFactura,'%d-%c-%Y %k:%i:%c' ) FechaFacturaFormato, E.Cedula CedulaVendedor,E.IDEmpleado IdentificadorEmpleado
								FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P, Empleado E
								WHERE F.FechaFactura>= '2020-02-04 00:00:00' and F.FechaFactura <= '2020-02-04 23:59:59'
								AND F.IDFactura = DF.IDFactura
								AND E.IDEmpleado=F.IDEmpleado
								AND F.IDPuntoVenta = DF.IDPuntoVenta
								AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica
								AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
								AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio AND R.Reportes <> 'N'
								AND F.IDPuntoVenta=6
								ORDER BY IDPuntoVenta,F.NumeroFactura";
		*/						


$r_factura=db_query($sql_factura);
while($row_factura=db_fetch_array($r_factura)){
		$linea="";
		$FechaFactura=
		$linea.=$row_factura["IDPuntoVenta"].$sep;
		$linea.=$row_factura["NumeroFactura"].$sep;
		$linea.=str_replace(".",",",$row_factura["ValorTotal"]).$sep;
		$linea.=$row_factura["FechaFacturaFormato"].$sep;
		$linea.="1".$sep;
		$linea.=$row_factura["IdentificadorEmpleado"].$sep;
		$linea.=$row_factura["Numero"].$sep;
		$linea.=$row_factura["Cantidad"].$sep;
		$linea.=str_replace(".",",",$row_factura["PrecioU"]).$sep;
		$linea.="FACTURA".$sep;
		fwrite($file, $linea . PHP_EOL);
}
fclose($file);
subir_ftp($NombreArchivo,$CarpetaRemota,"Ventas");
//**************************************
//FIN ARCHIVO DE VENTAS DIARIAS
//**************************************


//**************************************
//PEC
//**************************************
$NombreArchivo="PEC".date("ymd").".txt";
$file = fopen($carpeta.$NombreArchivo, "w+");
$sep = ";"; //tabbed character

//$fechaInicio=strtotime("2019-11-01");
//$fechaFin=strtotime("2019-12-04");

//for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
    //echo date("Y-m-d", $i)."<br>";
	//$fecha=date("Y-m-d", $i);
	//$fecha_mostrar=date("d-m-Y", $i);
$sql_empleado="SELECT * FROM Empleado WHERE IDPuntoVenta = 6 and Publicar='S' ";
$r_empleado = db_query($sql_empleado);
while($row_empleado=db_fetch_array($r_empleado)){
	$linea="";
	$linea.=$row_empleado["IDEmpleado"].$sep;
	$linea.="08:00:00".$sep;
	$linea.="12:00:00".$sep;
	$linea.="13:00:00".$sep;
	$linea.="19:00:00".$sep;
	$linea.=$row_empleado["IDPuntoVenta"].$sep;
	//$linea.=date("d-m-y").$sep;
	$linea.=$fecha_mostrar.$sep;
	$linea.=$row_empleado["Nombre"] . " " . $row_empleado["Apellidos"].$sep;
	fwrite($file, $linea . PHP_EOL);
}
//}
fclose($file);
subir_ftp($NombreArchivo,$CarpetaRemota,"PEC");
//**************************************
//PEC
//**************************************

//**************************************
//PRODUCTOS
//**************************************
$NombreArchivo="PRODUCTO".date("ymd").".txt";
$file = fopen($carpeta.$NombreArchivo, "w+");
$sep = ";"; //tabbed character
$sql_producto="SELECT R.*, T.Nombre NombreTipologia, P.Nombre NombreProveedor,PVR.IDPuntoVenta, TR.Descripcion NombreTipoReferencia
 							FROM CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Tipologia T, Proveedor P, TipoReferencia TR
 							WHERE C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
 							AND PVR.IDReferencia = R.IDReferencia
							AND R.IDTipologia=T.IDTipologia
							AND P.IDProveedor = R.IDProveedor
							AND TR.IDTiporeferencia=R.IDTiporeferencia
							AND R.Publicar = 'S'
							AND PVR.IDPuntoVenta = '6'
 							ORDER BY IDPuntoVenta";


$r_producto = db_query($sql_producto);
while($row_producto=db_fetch_array($r_producto)){
	$linea="";
	$linea.=$row_producto["Numero"].$sep;
	$linea.=$row_producto["Descripcion"].$sep;
	$linea.=$row_producto["NombreProveedor"].$sep;
	$linea.=$row_producto["NombreTipologia"].$sep;
	$linea.=$row_producto["NombreTipoReferencia"].$sep;
	$linea.=$row_producto["IDPuntoVenta"].$sep;
	fwrite($file, $linea . PHP_EOL);
}
fclose($file);
subir_ftp($NombreArchivo,$CarpetaRemota,"Productos");
//**************************************
//PRODUCTOS
//**************************************


echo "<br>FIN";
?>
