<?php
	include("../config.inc.php");
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
		echo "Fecha recibido en fabrica" . $sep;
		echo "Fecha enviado a almacenes" . $sep;
		echo "Fecha Entrega al	cliente	" . $sep;
		echo "Total Dias Fabrica" . $sep;
		echo "Total Dias Entrega" . $sep;
		
		print("\n");	
	//start while loop to get data
		while($row = db_fetch_array($result))
		{	
			
			  $sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$row["IDFactura"]."' and IDPuntoVenta = '".$row["IDPuntoVentaFactura"]."'");
			  $r_factura=db_fetch_array($sql_datos_factura);				  										
			
			  echo $row["IDGarantia"] . $sep;
			  echo substr($row["FechaTrCr"],0,10) . $sep;
	
              // Recibido Fabrica
			  $sql_recibido_fabrica = "Select * From ComentarioGarantia Where IDGarantia = '".$row[IDGarantia]."' and IDEstadoGarantia in (5, 12)";
			  $result_recibido_fabrica = db_query($sql_recibido_fabrica);
			  $row_recibido_fabrica = db_fetch_array($result_recibido_fabrica);
			  echo substr($row_recibido_fabrica[FechaComentario],0,10) . $sep;
			 
			  // Enviada tienda 
			  $sql_enviada_tienda = "Select * From ComentarioGarantia Where IDGarantia = '".$row[IDGarantia]."' and IDEstadoGarantia in (7)";
			  $result_enviada_tienda = db_query($sql_enviada_tienda);
			  $row_enviada_tienda = db_fetch_array($result_enviada_tienda);
			  echo substr($row_enviada_tienda[FechaComentario],0,10) . $sep;;
			  
			  //Entrega Cliente
			  $sql_entrega_cliente = "Select * From ComentarioGarantia Where IDGarantia = '".$row[IDGarantia]."' and IDEstadoGarantia in (9)";
			  $result_entrega_cliente = db_query($sql_entrega_cliente);
			  $row_entrega_cliente = db_fetch_array($result_entrega_cliente);
			  echo substr($row_entrega_cliente[FechaComentario],0,10) . $sep;
			  
			  $fecha_inicio_fabrica = substr($row_recibido_fabrica[FechaComentario],0,10);
			  $fecha_fin_fabrica = substr($row_enviada_tienda[FechaComentario],0,10);
				
				if(!empty($fecha_inicio_fabrica) && !empty($fecha_fin_fabrica)):
					$datetime1 = new DateTime($fecha_inicio_fabrica);
					$datetime2 = new DateTime($fecha_fin_fabrica);
					
					$interval = $datetime1->diff($datetime2);
					echo $interval->format('%a') . $sep;
				endif;	
				
			    $fecha_inicio = substr($row[FechaTrCr],0,10);
				$fecha_fin = substr($row_entrega_cliente[FechaComentario],0,10);
				
				if(!empty($fecha_inicio) && !empty($fecha_fin)):
					$datetime1 = new DateTime($fecha_inicio);
					$datetime2 = new DateTime($fecha_fin);
					
					$interval = $datetime1->diff($datetime2);
					echo $interval->format('%a') . $sep;
				endif;		
			
			
			print "\n";
		}
		
		exit;
		
?>