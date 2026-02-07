<?php
	include("../config.inc.php");
	Encabezado();

	if (empty($_GET["condicionfecha"]) ){
		$_GET[limit1]=date("Y-m-01");
		$_GET[limit2]=date("Y-m-30");
		$_GET["condicionfecha"].=" and F.FechaFactura between '".$_GET[limit1]."' and '".$_GET[limit2]."'";
	}

  $sql_clientes = $_GET["sql"];
	$now_date = date('m-d-Y H:i');
	$result = db_query($sql_clientes);
	$title = "Datos Reporte Cliente Fecha $now_date";
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
		echo "Ciudad" . $sep;
		echo "Valor" . $sep;
		echo "Item" . $sep;
		print("\n");
	//start while loop to get data
		while($row = db_fetch_array($result))
		{
			if (empty($row["Cedula"]) || $row["Cedula"]=="0"  )
				echo "na". $sep;
			else
				echo $row["Cedula"] . $sep;

			if (empty($row["Nombre"]) || $row["Nombre"]=="0"  )
				echo "na". $sep;
			else
				echo $row["Nombre"] . $sep;

			if (empty($row["Apellido"]) || $row["Apellido"]=="0"  )
				echo "na". $sep;
			else
				echo $row["Apellido"] . $sep;

			if (empty($row["IDCiudad"]) || $row["IDCiudad"]=="0"  )
				echo "na". $sep;
			else
				echo $row["IDCiudad"] . $sep;

			if (empty($row["ValorCompras"]) || $row["ValorCompras"]=="0"  )
				echo "na". $sep;
			else
				echo $row["ValorCompras"] . $sep;

				$sql_item="SELECT SUM(DF.Cantidad) as CantidadTotal
						 FROM Factura F, DetalleFactura DF
						 WHERE F.IDFactura=DF.IDfactura AND
									 F.IDCliente='".$row["IDCliente"]."'
									 ".$_GET["condicionfecha"]."
						 GROUP BY F.IDCliente";
				
			 $r_item=db_query($sql_item);
			 $row_item=db_fetch_array($r_item);
			 echo $row_item["CantidadTotal"]. $sep;

			print "\n";
		}

		exit;

?>
