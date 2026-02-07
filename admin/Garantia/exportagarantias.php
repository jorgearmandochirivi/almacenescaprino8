<?php
	include("../admin/config.inc.php");
	Encabezado();
	
    $sql_garantias = $_GET[sql];
	$now_date = date('m-d-Y H:i');
	$result = db_query($sql_garantias);
	$title = "Datos Reporte Garantias Fecha $now_date";
	$file_type = "vnd.ms-excel";
	$file_ending = "xls";
	
	
	header("Pragma: ");
	header("Cache-Control: ");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Content-Type: application/$file_type; charset=ISO-8859-1");
	header("Content-Disposition: attachment; filename=$title.$file_ending"); 
	
	
	echo("$title\n");
	//define separator (defines columns in excel & tabs in word)
	$sep = "\t"; //tabbed character
	$ponerdetalle = "";
	print("\n");
	//end of printing column names
	//Poner los nombres de las columnas
		
		echo "Numero" . $sep;
		echo "Fecha Ingreso garantia" . $sep;
		echo "Fecha estimada de entrega" . $sep;
		echo "Cliente" . $sep;
		echo "Cedula Cliente" . $sep;
		echo "Telefono Cliente" . $sep;
		echo "Celular Cliente" . $sep;
		echo "Direccion Cliente" . $sep;
		echo "Club Suavidad" . $sep;
		echo "Punto Venta" . $sep;
		echo "Almacen Recibe Garantia" . $sep;
		echo "Atendido por" . $sep;
		echo "Referencia Producto" . $sep;
		echo "Tipo" . $sep;
		echo "TipoServicio" . $sep;
		echo "Servicio" . $sep;
		echo "Reproceso" . $sep;
		echo "Remonta" . $sep;		
		echo "NumeroFacturaRemonta" . $sep;
		echo "ValorRemonta" . $sep;
		echo "PagoRemonta" . $sep;
		echo "Remonta" . $sep;
		echo "Cuero" . $sep;
		echo "Forro Tacon" . $sep;
		echo "Cremallera" . $sep;
		echo "Despegue" . $sep;
		echo "Cambrion" . $sep;
		echo "Tacon" . $sep;
		echo "Cerco" . $sep;
		echo "Cardado" . $sep;
		echo "Suela" . $sep;
		echo "Guarnicion" . $sep;
		echo "Puntera" . $sep;
		echo "Otro" . $sep;
		echo "FechaSalidaAlmacen" . $sep;
		echo "FechaEntradaAlmacen" . $sep;
		echo "FechaEntregaCliente" . $sep;
		echo "Descripcion" . $sep;
		
		print("\n");	
	//start while loop to get data
		while($row = db_fetch_array($result))
		{	
			echo $row["IDGarantia"] . $sep;
			echo substr($row["FechaTrCr"],0,10) . $sep;
			echo $row["FechaEstimadaEntrega"] . $sep;
			$id_cliente=get_field("Factura","IDCliente","IDFactura",$row["IDFactura"]);
			echo get_field("Cliente","Nombre","IDCliente",$id_cliente) . " " .get_field("Cliente","Apellido","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Cedula","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Telefono","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Celular","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Direccion","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","ClubSuavidad","IDCliente",$id_cliente) . $sep;
			$id_punto_venta_factura=get_field("Factura","IDPuntoVenta","IDFactura",$row["IDFactura"]);
			echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_punto_venta_factura) . $sep;
			echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$row["IDPuntoVenta"]) . $sep;
			echo get_field("Empleado","Nombre","IDEmpleado",$row["IDEmpleado"]) . " " . get_field("Empleado","Apellidos","IDEmpleado",$row["IDEmpleado"]) . $sep;
			//Producto
			$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$row["IDDetalleFactura"]."' and IDFactura = '".$row["IDFactura"]."' and IDPuntoVenta = '".$row["IDPuntoVenta"]."'";
			$qry_producto=db_query($sql_producto);
			$r_detalle=db_fetch_object($qry_producto);
			echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))  . $sep;
			//Fin Producto
			echo $row["TipoRegistro"] . $sep;
			echo $row["TipoServicio"] . $sep;
			echo $row["Servicio"] . $sep;
			echo $row["Reproceso"] . $sep;
			echo $row["Remonta"] . $sep;
			echo $row["NumeroFacturaRemonta"] . $sep;
			echo $row["ValorRemonta"] . $sep;
			echo $row["PagoRemonta"] . $sep;
			echo $row["TipoRemonta"] . $sep;
			echo $row["TipoCuero"] . $sep;
			echo $row["TipoForroTacon"] . $sep;
			echo $row["TipoCremallera"] . $sep;
			echo $row["TipoDespegue"] . $sep;
			echo $row["TipoCambrion"] . $sep;
			echo $row["TipoTacon"] . $sep;
			echo $row["TipoCerco"] . $sep;
			echo $row["TipoCardado"] . $sep;
			echo $row["TipoSuela"] . $sep;
			echo $row["TipoGuarnicion"] . $sep;
			echo $row["TipoPuntera"] . $sep;
			echo $row["TipoOtro"] . $sep;
			echo $row["FechaSalidaAlmacen"] . $sep;
			echo $row["FechaEntradaAlmacen"] . $sep;
			echo $row["FechaEntregaCliente"] . $sep;
			echo $row["Descripcion"] . $sep;			
			
			print "\n";
		}
		
		exit;
		
?>