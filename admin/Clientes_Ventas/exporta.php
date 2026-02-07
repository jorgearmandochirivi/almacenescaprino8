<?php
	include("../config.inc.php");
	Encabezado();
	
    //$sql_clientes = "SELECT * FROM BonoFidelizacion WHERE 1 ";
	$now_date = date('m-d-Y H:i');
	
	$result = db_query($_GET[sql]);
	$title = "Datos Reporte Ventas Fecha $now_date";
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
		
		echo "Cedula" . $sep;
		echo "Nombre" . $sep;
		echo "Apellido" . $sep;
		echo "Fidelizado" . $sep;
		echo "Facturas hechas en el segmento de tiempo seleccionado" . $sep;
		echo "Valor Total de las facturas hechas en el segmento de tiempo seleccionado" . $sep;
		echo "Facturas hechas desde la creacion del cliente" . $sep;
		echo "Valor total de las facturas desde la creacion del cliente" . $sep;
		echo "Fecha primer compra cliente" . $sep;
		echo "Fecha ultima compra" . $sep;
		print("\n");	
	//start while loop to get data
		while($row = db_fetch_array($result))
		{	
			echo $row["Cedula"] . $sep;
			echo $row["Nombre"] . $sep;
			echo $row["Apellido"] . $sep;
			echo $row["ClubSuavidad"] . $sep;
			
			//consulto el total de facturas en periodo
			$sql_total_fac_per=db_query("Select count(*) as Total From Factura Where IDCliente = '".$row["IDCliente"]."' and FechaFactura between '".$_GET[fecha1]."' and '".$_GET[fecha2]."'");
			$row_total_fac_per=db_fetch_array($sql_total_fac_per);			
			echo (int)$row_total_fac_per["Total"] . $sep;

			//consulto la suma de las  facturas
			$sql_sumtotal_fac_per=db_query("Select sum(ValorTotal) as SumaTotal From Factura Where IDCliente = '".$row["IDCliente"]."' and FechaFactura between '".$_GET[fecha1]."' and '".$_GET[fecha2]."'");
			$row_sumtotal_fac_per=db_fetch_array($sql_sumtotal_fac_per);
			echo (int)$row_sumtotal_fac_per["SumaTotal"] . $sep;
			
			//consulto el total de facturas
			$sql_total_fac=db_query("Select count(*) as Total From Factura Where IDCliente = '".$row["IDCliente"]."'");
			$row_total_fac=db_fetch_array($sql_total_fac);			
			echo (int)$row_total_fac["Total"] . $sep;
	
			//consulto la suma de las  facturas
			$sql_sumtotal_fac=db_query("Select sum(ValorTotal) as SumaTotal From Factura Where IDCliente = '".$row["IDCliente"]."'");
			$row_sumtotal_fac=db_fetch_array($sql_sumtotal_fac);
			echo (int)$row_sumtotal_fac["SumaTotal"] . $sep;
	
			//primera compra
			$sql_primera_fac=db_query("Select FechaFactura From Factura Where IDCliente = '".$row["IDCliente"]."' Order By IDFactura ASC limit 1");
			$row_primera_fac=db_fetch_array($sql_primera_fac);
			echo substr($row_primera_fac["FechaFactura"],0,10) . $sep;
	
			//ultima compra
			$sql_ultima_fac=db_query("Select FechaFactura From Factura Where IDCliente = '".$row["IDCliente"]."' Order By IDFactura DESC limit 1");
			$row_ultima_fac=db_fetch_array($sql_ultima_fac);
			echo substr($row_ultima_fac["FechaFactura"],0,10) . $sep;
			
			print "\n";
			
		}
		
		exit;
?>