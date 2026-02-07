<?php
	ini_set('max_execution_time', 0);
	include("../config.inc.php");
	Encabezado();


	/*
	$year = 2016;
	$sql_bono_year = "SELECT IDFactura, IDPuntoVenta
				FROM  BonoFidelizacion
				WHERE Estado =  'R'
				AND YEAR(  `Fecha` ) ='".$year."'";
	$qry_bono_year = db_query($sql_bono_year);
	while ($row_bono_year = db_fetch_array($qry_bono_year )){
		//$row_bono_year["IDFactura"];
		$sql_venta = "Select ValorTotal, FechaFactura, IDFactura From Factura Where IDfactura = '".$row_bono_year["IDFactura"]."' and IDPuntoVenta = '".$row_bono_year["IDPuntoVenta"]."'";
		$qry_venta = db_query($sql_venta);
		$row_venta = db_fetch_array($qry_venta);
		$ventas_total_year += $row_venta["ValorTotal"];

		//items
		$sql_detalle_fac = "Select count(IDDetalleFactura) as Items From DetalleFactura Where IDFactura = '".$row_bono_year["IDFactura"]."' and IDPuntoVenta = '".$row_bono_year["IDPuntoVenta"]."'";
		$qry_detalle_fac = db_query($sql_detalle_fac);
		$row_detalle_factura = db_fetch_array($qry_detalle_fac);
		$items_total_year += $row_detalle_factura["Items"];

	}

	$linea.= "VALOR TOTAL " .$year . ": $" . number_format($ventas_total_year,0,',','.') . " ITEMS: " . $items_total_year;
	exit;
	*/


	$sql_ciudad="SELECT IDCiudad, Descripcion FROM Ciudad WHERE 1";
	$r_ciudad=db_query($sql_ciudad);
	while($row_ciudad=db_fetch_array($r_ciudad)){
		$array_ciudad[$row_ciudad["IDCiudad"]]=$row_ciudad["Descripcion"];
	}


	$sql_emp="SELECT IDEmpleado, Nombre FROM Empleado WHERE 1";
	$r_emp=db_query($sql_emp);
	while($row_emp=db_fetch_array($r_emp)){
		$array_emp[$row_emp["IDEmpleado"]]=$row_emp["Nombre"] . " " . $row_emp["Apellidos"];
	}


	$sql_pto="SELECT IDPuntoVenta, Nombre FROM PuntoVenta WHERE 1";
	$r_pto=db_query($sql_pto);
	while($row_pto=db_fetch_array($r_pto)){
		$array_pto[$row_pto["IDPuntoVenta"]]=$row_pto["Nombre"];
	}

	$sql_tarj="SELECT * FROM TarjetaFidelizacion WHERE 1";
	$r_tarj=db_query($sql_tarj);
	while($row_tarj=db_fetch_array($r_tarj)){
		$array_tarj[$row_tarj["IDCliente"]]=$row_tarj;
	}





	 $file = fopen("Reporte.txt", "a");
  	//$sql_clientes = "SELECT * FROM Cliente WHERE ClubSuavidad = 'S'  Order By IDCLiente Limit 50000, 100000";
		$sql_clientes = "SELECT * FROM Cliente WHERE ClubSuavidad = 'S'  Order By IDCLiente";
	//$sql_clientes = "SELECT * FROM Cliente WHERE ClubSuavidad = 'S' Order By IDCLiente LIMIT 20 OFFSET 0";
	//$sql_clientes = "SELECT * FROM Cliente WHERE ClubSuavidad = 'S' Order By IDCLiente";
	$now_date = date('m-d-Y H:i');
	$result = db_query($sql_clientes);
	$title = "Datos Reporte Fidelizacion Fecha $now_date";
	$linea.=$title;
	fwrite($file, $linea . PHP_EOL);
	$linea="";
	//define separator (defines columns in excel & tabs in word)
	$sep = "\t"; //tabbed character
	$ponerdetalle = "";
	//$linea =("\n");
	//end of $linea =ing column names
	//Poner los nombres de las columnas

		$linea.= "IDCliente" . $sep;
		$linea.= "Cedula" . $sep;
		$linea.= "Nombre" . $sep;
		$linea.= "Apellido" . $sep;
		$linea.= "Telefono" . $sep;
		$linea.= "Celular" . $sep;
		$linea.= "Direccion" . $sep;
		$linea.= "Ciudad" . $sep;
		$linea.= "EMail" . $sep;
		$linea.= "Genero" . $sep;
		$linea.= "Fecha Nacimiento" . $sep;
		$linea.= "Mes Nacimiento" . $sep;
		$linea.= "Dia Nacimiento" . $sep;
		$linea.= "Ano Nacimiento" . $sep;
		$linea.= "Edad" . $sep;
		$linea.= "Acepta SMS" . $sep;
		$linea.= "Acepta Terminos" . $sep;
		$linea.= "Acepta Habeas Data" . $sep;
		$linea.= "Acepta recibir e-mail" . $sep;
		$linea.= "NumeroTarjeta" . $sep;
		$linea.= "Punto Venta Fidelizo" . $sep;
		$linea.= "Empleado Fidelizo" . $sep;
		$linea.= "Fecha Fidelizacion" . $sep;
		$linea.= "Mes Fidelizacion" . $sep;
		$linea.= "Dia Fidelizacion" . $sep;
		$linea.= "Ano Fidelizacion" . $sep;
		$linea.= "Total Puntos" . $sep;
		$linea.= "Total Puntos Redimidos" . $sep;
		$linea.= "Total Puntos Disponibles" . $sep;
		$linea.= "Bonos Disponibles" . $sep;
		$linea.= "Bonos Redimidos" . $sep;
		$linea.= "Bonos Asignados" . $sep;
		$linea.= "Total Compras" . $sep;
		$linea.= "Fecha ultima Compra" . $sep;
		$linea.= "Mes Ultima Compra" . $sep;
		$linea.= "Dia Ultima Compra" . $sep;
		$linea.= "Ano Ultima Compra" . $sep;
		$linea.= "Total compras con bonos" . $sep;
		$linea.= "Total Compras Desde Fidelizacion" . $sep;
		$linea.= "Fecha Factura 1" . $sep;
		$linea.= "Valor Factura 1" . $sep;
		$linea.= "Items Factura 1" . $sep;
		$linea.= "Fecha Factura 2" . $sep;
		$linea.= "Valor Factura 2" . $sep;
		$linea.= "Items Factura 2" . $sep;
		$linea.= "Fecha Factura 3" . $sep;
		$linea.= "Valor Factura 3" . $sep;
		$linea.= "Items Factura 3" . $sep;
		$linea.= "Año 2018" . $sep;
		$linea.= "Valor 2018" . $sep;
		$linea.= "Items 2018" . $sep;
		$linea.= "Año 2019" . $sep;
		$linea.= "Valor 2019" . $sep;
		$linea.= "Items 2019" . $sep;
		$linea.= "Año 2021" . $sep;
		$linea.= "Valor 2021" . $sep;
		$linea.= "Items 2021" . $sep;

		fwrite($file, $linea . PHP_EOL);


		//$linea =("\n");
	//start while loop to get data
		while($row = db_fetch_array($result))
		{
		$linea="";

			if (empty($row["IDCliente"]) || $row["IDCliente"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row["IDCliente"] . $sep;

			if (empty($row["Cedula"]) || $row["Cedula"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= trim($row["Cedula"]) . $sep;

			if (empty($row["Nombre"]) || $row["Nombre"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= trim($row["Nombre"]) . $sep;

			if (empty($row["Apellido"]) || $row["Apellido"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= trim($row["Apellido"]) . $sep;

			if (empty($row["Telefono"]) || $row["Telefono"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= trim($row["Telefono"]) . $sep;

			if (empty($row["Celular"]) || $row["Celular"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= trim($row["Celular"]) . $sep;

			if (empty($row["Direccion"]) || $row["Direccion"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= trim($row["Direccion"]) . $sep;


			if(!empty($array_ciudad[$row["IDCiudad"]]))
				$linea.= trim($array_ciudad[$row["IDCiudad"]]) . $sep;
			else
				$linea.= "na" . $sep;



			if (empty($row["EMail"]) || $row["EMail"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= trim($row["EMail"]) . $sep;

			if (empty($row["Genero"]) || $row["Genero"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= trim($row["Genero"]) . $sep;


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


			$linea.= $dia_nacimiento."/".$mes_nacimiento."/".$ano_nacimiento . $sep;

			$linea.= $mes_nacimiento . $sep;
			$linea.= $dia_nacimiento . $sep;
			$linea.= $ano_nacimiento . $sep;
			$linea.= $edad . $sep;



			if (empty($row["AceptaSMS"]) || $row["AceptaSMS"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row["AceptaSMS"] . $sep;

			if (empty($row["AceptaTerminos"]) || $row["AceptaTerminos"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row["AceptaTerminos"] . $sep;

			if (empty($row["AceptaHabeas"]) || $row["AceptaHabeas"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row["AceptaHabeas"] . $sep;

			if (empty($row["AutorizaMail"]) || $row["AutorizaMail"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row["AutorizaMail"] . $sep;

			if (empty($row["NumeroTarjeta"]) || $row["NumeroTarjeta"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= trim($row["NumeroTarjeta"]) . $sep;


			//$id_pto_vta=get_field("TarjetaFidelizacion","IDPuntoVenta","IDCliente",$row["IDCliente"]);

			$id_pto_vta=$array_tarj[$row["IDCliente"]]["IDPuntoVenta"];

			if(empty($id_pto_vta)):
				$id_pto_vta=$row["IDPuntoVentaFideliza"];
			endif;

			//$nombre_punto=get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_pto_vta);
			$nombre_punto=$array_pto[$id_pto_vta];

			if (empty($nombre_punto) || $nombre_punto=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $nombre_punto . $sep;

			$nombre_empleado=$array_emp[$row["IDUsuarioFideliza"]];
			//$nombre_empleado=get_field("Empleado","Nombre","IDEmpleado",$row["IDUsuarioFideliza"]);
			if (empty($nombre_empleado) || $nombre_empleado=="0"  ){
				//consulto algun empleado de la tienda si no esta el dato
				//$id_empleado=get_field("Empleado","IDEmpleado","IDPuntoVenta",$id_pto_vta);
				//$nombre_empleado=get_field("Empleado","Nombre","IDEmpleado",$id_empleado) . " " . get_field("Empleado","Apellidos","IDEmpleado",$id_empleado);
				if (empty($nombre_empleado))
					$linea.= "na". $sep;
				else
					$linea.= $nombre_empleado . $sep;
			}
			else{
				$linea.= $nombre_empleado . $sep;
			}




			//$fecha_fidelizacion=get_field("TarjetaFidelizacion","FechaTrCr","IDCliente",$row["IDCliente"],0,10);
			$fecha_fidelizacion=$array_tarj[$row["IDCliente"]]["FechaTrCr"];



			if ($fecha_fidelizacion=="0000-00-00 00:00:00" || empty($fecha_fidelizacion)){
				//$fecha_fidelizacion=get_field("TarjetaFidelizacion","FechaTrEd","IDCliente",$row["IDCliente"],0,10);
				$fecha_fidelizacion=$array_tarj[$row["IDCliente"]]["FechaTrEd"];
				if(empty($fecha_fidelizacion)):
					$fecha_fidelizacion = $row["FechaTrCr"];
				endif;


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
				$linea.= "na". $sep;
			else
				$linea.= $dia_fidelizacion."/".$mes_fidelizacion."/".$ano_fidelizacion . $sep;


			$linea.= $mes_fidelizacion . $sep;
			$linea.= $dia_fidelizacion . $sep;
			$linea.= $ano_fidelizacion . $sep;


			//Puntos
			$sql_total_puntos = "Select SUM(Puntos) as Total_Puntos From PuntosClienteFidelizacion Where IDCliente = '".$row["IDCliente"]."'";
			$qry_total_puntos = db_query($sql_total_puntos);
			$row_total_puntos = db_fetch_array($qry_total_puntos);

			if (empty($row_total_puntos["Total_Puntos"]) || $row_total_puntos["Total_Puntos"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row_total_puntos["Total_Puntos"] . $sep;

			//Puntos Redimidos
			$sql_total_puntos_redimidos = "Select SUM(PuntosRedimidos) as Total_Puntos_Redimidos From PuntosClienteFidelizacion Where IDCliente = '".$row["IDCliente"]."'";
			$qry_total_puntos_redimidos = db_query($sql_total_puntos_redimidos);
			$row_total_puntos_redimidos = db_fetch_array($qry_total_puntos_redimidos);

			if (empty($row_total_puntos_redimidos["Total_Puntos_Redimidos"]) || $row_total_puntos_redimidos["Total_Puntos_Redimidos"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row_total_puntos_redimidos["Total_Puntos_Redimidos"] . $sep;


			//Puntos disponibles
			$sql_total_puntos_sin_redimir = "Select SUM(Puntos) as Total_Puntos_Redimidos From PuntosClienteFidelizacion Where IDCliente = '".$row["IDCliente"]."' and Redimido = 'N'";
			$qry_total_puntos_sin_redimir = db_query($sql_total_puntos_sin_redimir);
			$row_total_puntos_sin_redimir = db_fetch_array($qry_total_puntos_sin_redimir);
			//$linea.= $row_total_puntos_sin_redimir["Total_Puntos_Redimidos"] . $sep;

			if (empty($row_total_puntos_sin_redimir["Total_Puntos_Redimidos"]) || $row_total_puntos_sin_redimir["Total_Puntos_Redimidos"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row_total_puntos_sin_redimir["Total_Puntos_Redimidos"] . $sep;


			//Bonos disponibles
			$sql_bonos_disponibles = "Select COUNT(IDBonoFidelizacion) as Total_Bonos_Disponibles From BonoFidelizacion Where IDCliente = '".$row["IDCliente"]."' and Estado = 'D'";
			$qry_bonos_disponibles = db_query($sql_bonos_disponibles);
			$row_bonos_disponibles = db_fetch_array($qry_bonos_disponibles);
			//$linea.= $row_bonos_disponibles["Total_Bonos_Disponibles"] . $sep;

			if (empty($row_bonos_disponibles["Total_Bonos_Disponibles"]) || $row_bonos_disponibles["Total_Bonos_Disponibles"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row_bonos_disponibles["Total_Bonos_Disponibles"] . $sep;


			//Bonos Redimidos
			$sql_bonos_redimidos = "Select COUNT(IDBonoFidelizacion) as Total_Bonos_Disponibles From BonoFidelizacion Where IDCliente = '".$row["IDCliente"]."' and Estado = 'R'";
			$qry_bonos_redimidos = db_query($sql_bonos_redimidos);
			$row_bonos_redimidos = db_fetch_array($qry_bonos_redimidos);
			//$linea.= $row_bonos_redimidos["Total_Bonos_Disponibles"] . $sep;

			if (empty($row_bonos_redimidos["Total_Bonos_Disponibles"]) || $row_bonos_redimidos["Total_Bonos_Disponibles"]=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $row_bonos_redimidos["Total_Bonos_Disponibles"] . $sep;


			//Bonos Asignados
			$bonos_asignados=(int)$row_bonos_disponibles["Total_Bonos_Disponibles"] + (int)$row_bonos_redimidos["Total_Bonos_Disponibles"];
			if (empty($bonos_asignados) || $bonos_asignados=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= $bonos_asignados . $sep;


			//Total Compras
			 $total_compras=0;
			 $fecha_ultima_compra="";
			 $total_compras_fidelizacion=0;

			unset($array_factura);
			$sql_compra_cliente = "Select ValorTotal, FechaFactura, IDFactura From Factura Where IDCliente = '".$row["IDCliente"]."' Order by IDFactura DESC";
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
				$linea.= "na". $sep;
			else
				$linea.= "$".str_replace(".","",(int)$total_compras) . $sep;


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
				$linea.= "na". $sep;
			else
				$linea.= $dia_ultima_compra."/".$mes_ultima_compra."/".$ano_ultima_compra . $sep;


			$linea.= $mes_ultima_compra . $sep;
			$linea.= $dia_ultima_compra . $sep;
			$linea.= $ano_ultima_compra . $sep;

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
				$linea.= "na". $sep;
			else
				$linea.= "$".str_replace(".","",(int)$total_compras_bonos) . $sep;

			//Total Compras desde que se fidelizo
			if (empty($total_compras_fidelizacion) || $total_compras_fidelizacion=="0"  )
				$linea.= "na". $sep;
			else
				$linea.= "$".str_replace(".","",(int)$total_compras_fidelizacion) . $sep;

			//Fac 1
			$linea.= $array_factura[2]["FechaFactura"] . $sep;
			$linea.= $array_factura[2]["ValorFactura"] . $sep;
			$linea.= $array_factura[2]["ItemFactura"] . $sep;
			//Fac 2
			$linea.= $array_factura[1]["FechaFactura"] . $sep;
			$linea.= $array_factura[1]["ValorFactura"] . $sep;
			$linea.= $array_factura[1]["ItemFactura"] . $sep;
			//Fac 3
			$linea.= $array_factura[0]["FechaFactura"] . $sep;
			$linea.= $array_factura[0]["ValorFactura"] . $sep;
			$linea.= $array_factura[0]["ItemFactura"] . $sep;


			//2014
			$linea.= $array_year[2]["FechaFactura"] . $sep;
			$linea.= $array_valor[2]["ValorFactura"] . $sep;
			$linea.= $array_item[2]["ItemFactura"] . $sep;
			//2015
			$linea.= $array_year[2]["FechaFactura"] . $sep;
			$linea.= $array_valor[2]["ValorFactura"] . $sep;
			$linea.= $array_item[2]["ItemFactura"] . $sep;
			//2016
			$linea.= $array_year[2]["FechaFactura"] . $sep;
			$linea.= $array_valor[2]["ValorFactura"] . $sep;
			$linea.= $array_item[2]["ItemFactura"] . $sep;


			$array_year[substr($row_compra_cliente["FechaFactura"],0,4)]++;
					$array_valor[substr($row_compra_cliente["FechaFactura"],0,4)]+=$row_compra_cliente["ValorTotal"];
					$array_item[substr($row_compra_cliente["FechaFactura"],0,4)]+=$row_compra_cliente["ValorTotal"];


			//$linea = "\n";
			fwrite($file, $linea . PHP_EOL);
			//exit;
		}

	fwrite($file, "FIN" . PHP_EOL);
	fclose($file);


		exit;

?>
