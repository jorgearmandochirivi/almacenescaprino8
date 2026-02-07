<?php
	include("../config.inc.php");
	Encabezado();
	
    $sql_clientes = "SELECT * FROM Cliente WHERE 1";
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
		echo "Telefono" . $sep;
		echo "Celular" . $sep;
		echo "Direccion" . $sep;
		echo "Ciudad" . $sep;
		echo "EMail" . $sep;
		echo "Genero" . $sep;
		echo "Fecha Nacimiento" . $sep;
		echo "Mes Nacimiento" . $sep;
		echo "Dia Nacimiento" . $sep;
		echo "Ano Nacimiento" . $sep;
		echo "Fecha ultima Compra" . $sep;
		echo "Punto venta ultima Compra" . $sep;
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
				
			if (empty($row["Telefono"]) || $row["Telefono"]=="0"  ) 	
				echo "na". $sep;
			else				
				echo $row["Telefono"] . $sep;
				
			if (empty($row["Celular"]) || $row["Celular"]=="0"  ) 	
				echo "na". $sep;
			else				
				echo $row["Celular"] . $sep;
				
			if (empty($row["Direccion"]) || $row["Direccion"]=="0"  ) 	
				echo "na". $sep;
			else
				echo $row["Direccion"] . $sep;
				
				
			$sql_Ciudad = "SELECT * FROM `Ciudad` WHERE `IDCiudad` ='".$row["IDCiudad"]. "'";			
			$qry_Ciudad = db_query( $sql_Ciudad );
			
			if (db_num_rows($qry_Ciudad)>0){			
			   while( $r_Ciudad = db_fetch_array( $qry_Ciudad ) ){				
					echo $r_Ciudad["Descripcion"] . $sep;			
			   }
			}
			else{
				echo "na" . $sep;	
			}
			   
				 
			
			if (empty($row["EMail"]) || $row["EMail"]=="0"  ) 	
				echo "na". $sep;
			else
				echo $row["EMail"] . $sep;
				
			if (empty($row["Genero"]) || $row["Genero"]=="0"  ) 	
				echo "na". $sep;
			else
				echo $row["Genero"] . $sep;
				
				
			$dia_nacimiento=$row["Dia"];
			$mes_nacimiento=$row["Mes"];
			$ano_nacimiento=$row["Ano"];
			
			
			if (empty($dia_nacimiento) || (int)$dia_nacimiento==0)
				$dia_nacimiento="00";

			
			if (empty($mes_nacimiento) || (int)$mes_nacimiento==0)
				$mes_nacimiento="00";

				
			echo $dia_nacimiento."/".$mes_nacimiento."/".$ano_nacimiento . $sep;
			
			echo $mes_nacimiento . $sep;
			echo $dia_nacimiento . $sep;
			echo $ano_nacimiento . $sep;

			//Fecha Ultima Compra
			$sql_ultima_compra = "Select * From Factura Where IDCliente = '".$row["IDCliente"]."' Order By IDFactura DESC limit 1";  
			$qry_ultima_compra = db_query($sql_ultima_compra);
			$row_ultima_compra = db_fetch_array($qry_ultima_compra);
			echo $row_ultima_compra["FechaFactura"] . $sep;
			
			echo $id_pto_vta=get_field("PuntoVenta","Nombre","IDPuntoVenta",$row_ultima_compra["IDPuntoVenta"]) . $sep;
			
			print "\n";
			
		}
		
		exit;
		
?>