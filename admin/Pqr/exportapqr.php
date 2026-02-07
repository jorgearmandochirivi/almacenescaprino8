<?php
	include("../config.inc.php");
	Encabezado();

    $sql_garantias = $_GET[sql];
	$now_date = date('m-d-Y H:i');
	$result = db_query($sql_garantias);
	$title = "Datos Reporte Pqr Fecha $now_date";
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
		echo "Tipo" . $sep;
		echo "Estado" . $sep;
		echo "Motivo" . $sep;
		echo "Fuente" . $sep;
		echo "Creado Por" . $sep;
		echo "Punto Venta" . $sep;
		echo "Cliente" . $sep;
		echo "Cedula Cliente" . $sep;
		echo "Telefono Cliente" . $sep;
		echo "Celular Cliente" . $sep;
		echo "Direccion Cliente" . $sep;
		echo "Solucion" . $sep;
		echo "Asunto" . $sep;
		echo "Descripcion" . $sep;
		echo "Fecha" . $sep;
		print("\n");
	//start while loop to get data
		while($row = db_fetch_array($result))
		{
			echo $row["Numero"] . $sep;
			echo get_field("TipoPqr","Nombre","IDTipoPqr",$row["IDTipoPqr"]) . $sep;
			echo get_field("PqrEstado","Nombre","IDPqrEstado",$row["IDPqrEstado"]) . $sep;
			echo get_field("MotivoPqr","Nombre","IDMotivoPqr",$row["IDMotivoPqr"]) . $sep;
			echo get_field("FuentePqr","Nombre","IDFuentePqr",$row["IDFuentePqr"]) . $sep;
			echo get_field("Empleado","Nombre","IDEmpleado",$row["IDEmpleado"]). $sep;
			$id_pto_vta = get_field("Empleado","IDPuntoVenta","IDEmpleado",$r->IDEmpleado);
			echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_pto_vta) . $sep;
			$id_cliente = $row["IDCliente"];
			echo get_field("Cliente","Nombre","IDCliente",$id_cliente) . " " .get_field("Cliente","Apellido","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Cedula","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Telefono","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Celular","IDCliente",$id_cliente) . $sep;
			echo get_field("Cliente","Direccion","IDCliente",$id_cliente) . $sep;
			echo get_field("PqrSolucion","Nombre","IDPqrSolucion",$row["IDPqrSolucion"]). $sep;
			echo eregi_replace("[\n|\r|\n\r]", " ", $row["Asunto"])  . $sep;
			echo eregi_replace("[\n|\r|\n\r]", " ", $row["Descripcion"]) . $sep;
			echo $row["Fecha"] . $sep;
			print "\n";

		}

		exit;

?>
