<?php
	ini_set('max_execution_time', 0);
	include("../config.inc.php");
	Encabezado();
    $sql_clientes = "SELECT * FROM Cliente WHERE ClubSuavidad = 'S' Order By IDCLiente LIMIT 4000 OFFSET 40001";
	//$sql_clientes = "SELECT * FROM Cliente WHERE ClubSuavidad = 'S' Order By IDCLiente LIMIT 20 OFFSET 0";
	//$sql_clientes = "SELECT * FROM Cliente WHERE ClubSuavidad = 'S' Order By IDCLiente";
	$now_date = date('m-d-Y H:i');
	$result = db_query($sql_clientes);
	$title = "Datos Reporte Fidelizacion Fecha $now_date";
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
		echo "Edad" . $sep;
		echo "Acepta SMS" . $sep;
		echo "Acepta Terminos" . $sep;
		echo "Acepta Habeas Data" . $sep;
		echo "Acepta recibir e-mail" . $sep;
		echo "NumeroTarjeta" . $sep;
		echo "Punto Venta Fidelizo" . $sep;
		echo "Empleado Fidelizo" . $sep;
		echo "Fecha Fidelizacion" . $sep;
		echo "Mes Fidelizacion" . $sep;
		echo "Dia Fidelizacion" . $sep;
		echo "Ano Fidelizacion" . $sep;			
		echo "Total Puntos" . $sep;
		echo "Total Puntos Redimidos" . $sep;
		echo "Total Puntos Disponibles" . $sep;
		echo "Bonos Disponibles" . $sep;
		echo "Bonos Redimidos" . $sep;
		echo "Bonos Asignados" . $sep;
		echo "Total Compras" . $sep;
		echo "Fecha ultima Compra" . $sep;
		echo "Mes Ultima Compra" . $sep;
		echo "Dia Ultima Compra" . $sep;
		echo "Ano Ultima Compra" . $sep;				
		echo "Total compras con bonos" . $sep;		
		echo "Total Compras Desde Fidelizacion" . $sep;
		echo "Fecha Factura 1" . $sep;
		echo "Valor Factura 1" . $sep;
		echo "Items Factura 1" . $sep;
		echo "Fecha Factura 2" . $sep;
		echo "Valor Factura 2" . $sep;
		echo "Items Factura 2" . $sep;
		echo "Fecha Factura 3" . $sep;
		echo "Valor Factura 3" . $sep;
		echo "Items Factura 3" . $sep;
		echo "Año 2014" . $sep;
		echo "Valor 2014" . $sep;
		echo "Items 2014" . $sep;
		echo "Año 2015" . $sep;
		echo "Valor 2015" . $sep;
		echo "Items 2015" . $sep;
		echo "Año 2016" . $sep;
		echo "Valor 2016" . $sep;
		echo "Items 2016" . $sep;
		
		
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

			$edad="na";
			if (empty($ano_nacimiento) || (int)$ano_nacimiento==0){
				$ano_nacimiento="0000";
				$edad="na";
			}else{
				$edad=(int)date("Y") - (int)$ano_nacimiento	;
			}
				
				
			echo $dia_nacimiento."/".$mes_nacimiento."/".$ano_nacimiento . $sep;
			
			echo $mes_nacimiento . $sep;
			echo $dia_nacimiento . $sep;
			echo $ano_nacimiento . $sep;
			echo $edad . $sep;
			
			
				
			if (empty($row["AceptaSMS"]) || $row["AceptaSMS"]=="0"  ) 	
				echo "na". $sep;
			else
				echo $row["AceptaSMS"] . $sep;
				
			if (empty($row["AceptaTerminos"]) || $row["AceptaTerminos"]=="0"  ) 	
				echo "na". $sep;
			else
				echo $row["AceptaTerminos"] . $sep;
				
			if (empty($row["AceptaHabeas"]) || $row["AceptaHabeas"]=="0"  ) 	
				echo "na". $sep;
			else
				echo $row["AceptaHabeas"] . $sep;
				
			if (empty($row["AutorizaMail"]) || $row["AutorizaMail"]=="0"  ) 	
				echo "na". $sep;
			else
				echo $row["AutorizaMail"] . $sep;	
				
			if (empty($row["NumeroTarjeta"]) || $row["NumeroTarjeta"]=="0"  ) 	
				echo "na". $sep;
			else				
				echo $row["NumeroTarjeta"] . $sep;
				
				
			$id_pto_vta=get_field("TarjetaFidelizacion","IDPuntoVenta","IDCliente",$row["IDCliente"]);
			$nombre_punto=get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_pto_vta);

			if (empty($nombre_punto) || $nombre_punto=="0"  ) 	
				echo "na". $sep;
			else				
				echo $nombre_punto . $sep;
			
			
			$nombre_empleado=get_field("Empleado","Nombre","IDEmpleado",$row["IDUsuarioFideliza"]);
			if (empty($nombre_empleado) || $nombre_empleado=="0"  ){ 	
				//consulto algun empleado de la tienda si no esta el dato
				//$id_empleado=get_field("Empleado","IDEmpleado","IDPuntoVenta",$id_pto_vta);
				//$nombre_empleado=get_field("Empleado","Nombre","IDEmpleado",$id_empleado) . " " . get_field("Empleado","Apellidos","IDEmpleado",$id_empleado);
				if (empty($nombre_empleado))
					echo "na". $sep;
				else	
					echo $nombre_empleado . $sep;
			}
			else{				
				echo $nombre_empleado . $sep;
			}
			
			
			
			
			$fecha_fidelizacion=get_field("TarjetaFidelizacion","FechaTrCr","IDCliente",$row["IDCliente"],0,10);
			if ($fecha_fidelizacion=="0000-00-00 00:00:00"){
				$fecha_fidelizacion=get_field("TarjetaFidelizacion","FechaTrEd","IDCliente",$row["IDCliente"],0,10);
			}
			
			
			
			$array_fecha_fidelizacion=explode("-",substr($fecha_fidelizacion,0,10));
			
			
			
			$dia_fidelizacion=$array_fecha_fidelizacion[2];
			$mes_fidelizacion=$array_fecha_fidelizacion[1];
			$ano_fidelizacion=$array_fecha_fidelizacion[0];
			
			if (empty($dia_fidelizacion) || (int)$dia_fidelizacion==0)
				$dia_fidelizacion="00";

			
			if (empty($mes_fidelizacion) || (int)$mes_fidelizacion==0)
				$mes_fidelizacion="00";

			if (empty($ano_fidelizacion) || (int)$ano_fidelizacion==0)
				$ano_fidelizacion="0000";	
				
			if (empty($fecha_fidelizacion) || $fecha_fidelizacion=="0000-00-00 00:00:00"  ) 	
				echo "na". $sep;
			else				
				echo $dia_fidelizacion."/".$mes_fidelizacion."/".$ano_fidelizacion . $sep;
			
			
			echo $mes_fidelizacion . $sep;
			echo $dia_fidelizacion . $sep;
			echo $ano_fidelizacion . $sep;
			
			
			//Puntos
			$sql_total_puntos = "Select SUM(Puntos) as Total_Puntos From PuntosClienteFidelizacion Where IDCliente = '".$row["IDCliente"]."'";  
			$qry_total_puntos = db_query($sql_total_puntos);
			$row_total_puntos = db_fetch_array($qry_total_puntos);
			
			if (empty($row_total_puntos["Total_Puntos"]) || $row_total_puntos["Total_Puntos"]=="0"  ) 	
				echo "na". $sep;
			else				
				echo $row_total_puntos["Total_Puntos"] . $sep;
			
			//Puntos Redimidos
			$sql_total_puntos_redimidos = "Select SUM(PuntosRedimidos) as Total_Puntos_Redimidos From PuntosClienteFidelizacion Where IDCliente = '".$row["IDCliente"]."'";  
			$qry_total_puntos_redimidos = db_query($sql_total_puntos_redimidos);
			$row_total_puntos_redimidos = db_fetch_array($qry_total_puntos_redimidos);
			
			if (empty($row_total_puntos_redimidos["Total_Puntos_Redimidos"]) || $row_total_puntos_redimidos["Total_Puntos_Redimidos"]=="0"  ) 	
				echo "na". $sep;
			else				
				echo $row_total_puntos_redimidos["Total_Puntos_Redimidos"] . $sep;
			
			
			//Puntos disponibles
			$sql_total_puntos_sin_redimir = "Select SUM(Puntos) as Total_Puntos_Redimidos From PuntosClienteFidelizacion Where IDCliente = '".$row["IDCliente"]."' and Redimido = 'N'";  
			$qry_total_puntos_sin_redimir = db_query($sql_total_puntos_sin_redimir);
			$row_total_puntos_sin_redimir = db_fetch_array($qry_total_puntos_sin_redimir);
			//echo $row_total_puntos_sin_redimir["Total_Puntos_Redimidos"] . $sep;
			
			if (empty($row_total_puntos_sin_redimir["Total_Puntos_Redimidos"]) || $row_total_puntos_sin_redimir["Total_Puntos_Redimidos"]=="0"  ) 	
				echo "na". $sep;
			else				
				echo $row_total_puntos_sin_redimir["Total_Puntos_Redimidos"] . $sep;
			
			
			//Bonos disponibles
			$sql_bonos_disponibles = "Select COUNT(IDBonoFidelizacion) as Total_Bonos_Disponibles From BonoFidelizacion Where IDCliente = '".$row["IDCliente"]."' and Estado = 'D'";  
			$qry_bonos_disponibles = db_query($sql_bonos_disponibles);
			$row_bonos_disponibles = db_fetch_array($qry_bonos_disponibles);
			//echo $row_bonos_disponibles["Total_Bonos_Disponibles"] . $sep;
			
			if (empty($row_bonos_disponibles["Total_Bonos_Disponibles"]) || $row_bonos_disponibles["Total_Bonos_Disponibles"]=="0"  ) 	
				echo "na". $sep;
			else				
				echo $row_bonos_disponibles["Total_Bonos_Disponibles"] . $sep;
			
			
			//Bonos Redimidos
			$sql_bonos_redimidos = "Select COUNT(IDBonoFidelizacion) as Total_Bonos_Disponibles From BonoFidelizacion Where IDCliente = '".$row["IDCliente"]."' and Estado = 'R'";  
			$qry_bonos_redimidos = db_query($sql_bonos_redimidos);
			$row_bonos_redimidos = db_fetch_array($qry_bonos_redimidos);
			//echo $row_bonos_redimidos["Total_Bonos_Disponibles"] . $sep;
			
			if (empty($row_bonos_redimidos["Total_Bonos_Disponibles"]) || $row_bonos_redimidos["Total_Bonos_Disponibles"]=="0"  ) 	
				echo "na". $sep;
			else				
				echo $row_bonos_redimidos["Total_Bonos_Disponibles"] . $sep;
			

			//Bonos Asignados
			$bonos_asignados=(int)$row_bonos_disponibles["Total_Bonos_Disponibles"] + (int)$row_bonos_redimidos["Total_Bonos_Disponibles"];
			if (empty($bonos_asignados) || $bonos_asignados=="0"  ) 	
				echo "na". $sep;
			else				
				echo $bonos_asignados . $sep;
			
			
			//Total Compras
			 $total_compras=0;
			 $fecha_ultima_compra="";
			 $total_compras_fidelizacion=0;
			
			unset($array_factura);
			$sql_compra_cliente = "Select ValorTotal, FechaFactura, IDFactura From Factura Where IDCliente = '".$row["IDCliente"]."' Order by FechaFactura DESC";  
			$qry_compra_cliente = db_query($sql_compra_cliente);
			$item_compra=0;
			while ($row_compra_cliente = db_fetch_array($qry_compra_cliente)):
				if($item_compra==0):
					$fecha_ultima_compra = $row_compra_cliente["FechaFactura"];
				endif;
				
				
				$fecha_iniciofid = substr($fecha_fidelizacion,0,10);
				$fecha_comprafac = substr($row_compra_cliente["FechaFactura"],0,10);
				$fecha1 = strtotime($fecha_iniciofid);
				$fecha2 = strtotime($fecha_comprafac);

				if($fecha_comprafac >= $fecha_iniciofid):
					$total_compras_fidelizacion+=$row_compra_cliente["ValorTotal"];	
					$array_factura [$item_compra]["FechaFactura"]= $row_compra_cliente["FechaFactura"];
					$array_factura [$item_compra]["ValorFactura"]= $row_compra_cliente["ValorTotal"];					
					//items
					$sql_detalle_fac = "Select count(IDDetalleFactura) as Items From DetalleFactura Where IDFactura = '".$row_compra_cliente["IDFactura"]."'";
					$qry_detalle_fac = db_query($sql_detalle_fac);
					$row_detalle_factura = db_fetch_array($qry_detalle_fac);
					$array_factura [$item_compra]["ItemFactura"]= $row_detalle_factura["Items"];
					
					$array_year[substr($row_compra_cliente["FechaFactura"],0,4)]++;
					$array_valor[substr($row_compra_cliente["FechaFactura"],0,4)]+=$row_compra_cliente["ValorTotal"];
					$array_item[substr($row_compra_cliente["FechaFactura"],0,4)]+=$row_compra_cliente["ValorTotal"];
					
				endif;
					
				$total_compras+=$row_compra_cliente["ValorTotal"];
				$item_compra++;
				
				
			endwhile;
			
			
			if (empty($total_compras) || $total_compras=="0"  ) 	
				echo "na". $sep;
			else	
				echo "$".str_replace(".","",(int)$total_compras) . $sep;
			
		
			$fecha_ultima_compra=substr($fecha_ultima_compra,0,10);
			
			$array_fecha_ultima=explode("-",$fecha_ultima_compra);
			$dia_ultima_compra=$array_fecha_ultima[2];
			$mes_ultima_compra=$array_fecha_ultima[1];
			$ano_ultima_compra=$array_fecha_ultima[0];
			
			if (empty($dia_ultima_compra) || (int)$dia_ultima_compra==0)
				$dia_ultima_compra="00";
			
			if (empty($mes_ultima_compra) || (int)$mes_ultima_compra==0)
				$mes_ultima_compra="00";

			if (empty($ano_ultima_compra) || (int)$ano_ultima_compra==0)
				$ano_ultima_compra="0000";	
				
			if (empty($fecha_ultima_compra) || $fecha_ultima_compra=="0000-00-00"  ) 	
				echo "na". $sep;
			else				
				echo $dia_ultima_compra."/".$mes_ultima_compra."/".$ano_ultima_compra . $sep;
			
			
			echo $mes_ultima_compra . $sep;
			echo $dia_ultima_compra . $sep;
			echo $ano_ultima_compra . $sep;
			
			$total_compras_bonos = 0;
			// Total compras con bonos de fidelizacion
			$sql_bonos_redimidos = "Select IDFactura From BonoFidelizacion Where IDCliente = '".$row["IDCliente"]."' and Estado = 'R'"; 
			//$sql_bonos_redimidos = "Select sum(ValorTotal) as Total_FacturaBono From Factura Where IDFactura in ()"; 
			$qry_bonos_redimidos = db_query($sql_bonos_redimidos);
			while($row_bonos_redimidos = db_fetch_array($qry_bonos_redimidos)):
				$sql_facturas_bono = "Select sum(ValorTotal) as Total_FacturaBono From Factura Where IDFactura = '".$row_bonos_redimidos["IDFactura"]."'";
				$qry_facturas_bono = db_query($sql_facturas_bono);
				$row_factura_bono = db_fetch_array($qry_facturas_bono);
				$total_compras_bonos += $row_factura_bono["Total_FacturaBono"];
			endwhile;
			
			
			if (empty($total_compras_bonos) || $total_compras_bonos=="0"  ) 	
				echo "na". $sep;
			else	
				echo "$".str_replace(".","",(int)$total_compras_bonos) . $sep;
		
			//Total Compras desde que se fidelizo
			if (empty($total_compras_fidelizacion) || $total_compras_fidelizacion=="0"  ) 	
				echo "na". $sep;
			else	
				echo "$".str_replace(".","",(int)$total_compras_fidelizacion) . $sep;
				
			//Fac 1	
			echo $array_factura[2]["FechaFactura"] . $sep;
			echo $array_factura[2]["ValorFactura"] . $sep;
			echo $array_factura[2]["ItemFactura"] . $sep;
			//Fac 2
			echo $array_factura[1]["FechaFactura"] . $sep;
			echo $array_factura[1]["ValorFactura"] . $sep;
			echo $array_factura[1]["ItemFactura"] . $sep;
			//Fac 3
			echo $array_factura[0]["FechaFactura"] . $sep;
			echo $array_factura[0]["ValorFactura"] . $sep;
			echo $array_factura[0]["ItemFactura"] . $sep;
			
			
			//2014	
			echo $array_year[2]["FechaFactura"] . $sep;
			echo $array_valor[2]["ValorFactura"] . $sep;
			echo $array_item[2]["ItemFactura"] . $sep;
			//2015
			echo $array_year[2]["FechaFactura"] . $sep;
			echo $array_valor[2]["ValorFactura"] . $sep;
			echo $array_item[2]["ItemFactura"] . $sep;
			//2016
			echo $array_year[2]["FechaFactura"] . $sep;
			echo $array_valor[2]["ValorFactura"] . $sep;
			echo $array_item[2]["ItemFactura"] . $sep;
			
			
			$array_year[substr($row_compra_cliente["FechaFactura"],0,4)]++;
					$array_valor[substr($row_compra_cliente["FechaFactura"],0,4)]+=$row_compra_cliente["ValorTotal"];
					$array_item[substr($row_compra_cliente["FechaFactura"],0,4)]+=$row_compra_cliente["ValorTotal"];

			
			print "\n";
			//exit;
		}
		
		exit;
		
?>
