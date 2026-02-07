<?php
	include("../admin/config.inc.php");
	Encabezado();
	
    $sql_fac = $_GET[sql];
	$now_date = date('m-d-Y H:i');
	$result = db_query($sql_fac);
	$title = "Datos Reporte Cambios Fecha $now_date";
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
		
		echo "Cliente" . $sep;
		echo "Numero Registro" . $sep;
		echo "Fecha" . $sep;
		echo "Valor Total" . $sep;

		print("\n");	
	//start while loop to get data

		while($row = db_fetch_array($result))
		{	
			echo get_field("Cliente","Nombre","IDCliente",$row["IDCliente"]) . " " .get_field("Cliente","Apellido","IDCliente",$row["IDCliente"]) . $sep;
			echo $row["IDCambio"] . $sep;
			echo $row["FechaCambio"] . $sep;
			echo $row["Excedente"] . $sep;			
			print "\n";
		}
		
		exit;
		
?>