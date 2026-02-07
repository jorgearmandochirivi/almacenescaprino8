<?php
	include("../config.inc.php");
	Encabezado();
	
    $sql = base64_decode($_GET[sql]);
	$now_date = date('m-d-Y H:i');
	$result = db_query($sql);
	$title = "Datos Reporte Facturas Alianza Fecha $now_date";
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
		echo "Nombre Cliente" . $sep;
		echo "Documento Cliente" . $sep;
		echo "Telefono Cliente" . $sep;
		echo "Celular Cliente" . $sep;
		echo "Direccion Cliente" . $sep;
		echo "Club Suavidad Cliente" . $sep;
		echo "Punto de Venta" . $sep;
		echo "Numero Factura" . $sep;
		echo "Fecha Factura" . $sep;
		echo "Valor Total" . $sep;
		echo "Alianza" . $sep;
		echo "DescuentoAlianza" . $sep;
		print("\n");	
	//start while loop to get data
		while($row = db_fetch_array($result))
		{	
			
			$id_cliente=$row[IDCliente];
			echo get_field("Cliente","Nombre","IDCliente",$id_cliente) . " " .get_field("Cliente","Apellido","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Cedula","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Telefono","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Celular","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Direccion","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","ClubSuavidad","IDCliente",$id_cliente) . $sep;			
			echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$row[IDPuntoVenta]) . $sep;
			echo $row["NumeroFactura"] . $sep;
			echo $row["FechaFactura"] . $sep;
			echo $row["ValorTotal"] . $sep;
			echo get_field("Alianza","Nombre","IDAlianza",$row[IDAlianza]) . $sep;
			echo $row["DescuentoAlianza"] . $sep;
			print "\n";
		}
		
		exit;
		
?>