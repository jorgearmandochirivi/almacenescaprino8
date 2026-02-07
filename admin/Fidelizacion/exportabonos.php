<?php
	include("../config.inc.php");
	Encabezado();
	
    $sql_clientes = "SELECT * FROM BonoFidelizacion WHERE 1 ";
	$now_date = date('m-d-Y H:i');
	$result = db_query($sql_clientes);
	$title = "Datos Reporte Bonos Fidelizacion Fecha $now_date";
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
		
		echo "Numero Bono" . $sep;
		echo "Fecha Generacion Bono" . $sep;
		echo "Cedula" . $sep;
		echo "Punto de Venta genero Bono" . $sep;
		echo "Factura que genero bono" . $sep;
		echo "Valor Bono" . $sep;
		echo "Cedula Que Redimio Bono" . $sep;
		echo "Punto de venta que Redimio Bono" . $sep;
		echo "Factura Redimido Bono" . $sep;
		echo "Fecha Redimido Bono" . $sep;
		echo "Valor Factura" . $sep;
		echo "Estado Actual Bono" . $sep;
		echo "Fecha Vencimiento Bono" . $sep;
		print("\n");	
	//start while loop to get data
		while($row = db_fetch_array($result))
		{	
			echo $row["IDBonoFidelizacion"] . $sep;
			echo substr($row["FechaTrCr"],0,10) . $sep;
			echo get_field("Cliente","Cedula","IDCliente",$row["IDCliente"]) .  $sep;
			echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$row["IDPuntoVenta"]) . $sep;
			echo get_field("Factura","NumeroFactura","IDFactura",$row["IDFacturaPadre"]) . $sep;
			echo $row["Valor"] . $sep;
			echo get_field("Cliente","Cedula","IDCliente",$row["IDClienteRedimioBono"]) .  $sep;
			echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$row["IDPuntoVentaRedimido"]) . $sep;
			echo get_field("Factura","NumeroFactura","IDFactura",$row["IDFactura"]) . $sep;
			echo substr($row["FechaRedimido"],0,10) . $sep;
			
			if($row[Estado]=="R"){				
				$sql_factura_redimido = db_query("Select * From Factura Where IDFactura = '".$row["IDFactura"]."' and IDPuntoVenta = '".$row["IDPuntoVentaRedimido"]."'");
				$row_factura_redimido = db_fetch_array($sql_factura_redimido);
				echo "$".number_format($row_factura_redimido["ValorTotal"],0,',','.') . $sep;
			}
			else{
				echo "" . $sep;	
			}
			
			
			if ($row["Estado"]=="D")
				echo "Disponible" . $sep;
			elseif($row["Estado"]=="R")	
				echo "Redimido" . $sep;
			elseif($row["Estado"]=="C")	
				echo "Cancelado" . $sep;
			else
				echo $row["Estado"] . $sep;	
				
			echo $row["FechaVencimiento"] . $sep;
			print "\n";
			
			
			
						
				
			
		}
		
		exit;
?>