<?php
	error_reporting(1);
	require("../config.inc.php");


$ruta_archivo="CreditosCodigos.txt";


if($fp = fopen($ruta_archivo,"r")){
	$cont = 0;	
	while(!feof($fp)){		
		ini_set('auto_detect_line_endings', true); 
		$linea = fgets($fp,4096);
		
		$fields = array_map('addslashes',array_map('trim', explode("\t",$linea)));
		
		$fecha = trim($fields[0]);
		$mes = trim($fields[1]);
		$cedula = trim($fields[2]);
		$nombre_cliente=trim($fields[3]);		
		$id_punto_venta = trim($fields[4]);
		$numero_factura = trim($fields[5]);
		$valor_total = trim($fields[6]);
		$cuota_cancelada = trim($fields[7]);
		$cuota_pendiente = trim($fields[8]);
		$fecha_proximo_pago = trim($fields[9]);
		
		//Consulto el id de la factura
		$sql_cuota=db_query("Select * from Factura Where NumeroFactura = '".$numero_factura."' and IDPuntoVenta='".$id_punto_venta."'");
		$row_factura=db_fetch_array($sql_cuota);
		
		// Consulto si tiene las cuotas creadas
		$sql_cuota=db_query("Select * from CreditoCuota Where IDFactura = '".$row_factura[IDFactura]."' and NumeroFactura='".$numero_factura."' and IDPuntoVenta = '".$id_punto_venta."'");
		$total_cuotas=db_num_rows($sql_cuota);


		// resetar cuotas
		/*
		$sql_actualiza_pago="Update CreditoCuota Set FechaPago = '' Where IDFactura = '".$row_factura[IDFactura]."' and NumeroFactura = '".$numero_factura."' and IDPuntoVenta = '".$id_punto_venta."'";
		db_query($sql_actualiza_pago);
		*/


			if ((int)$cuota_cancelada>0){
				echo "CANCE " . $cuota_cancelada;
				for($num_pagas=1;$num_pagas<=$cuota_cancelada;$num_pagas++){
					$sql_actualiza_pago="Update CreditoCuota Set FechaPago = '2014-10-16' Where IDFactura = '".$row_factura[IDFactura]."' and NumeroFactura = '".$numero_factura."' and IDPuntoVenta = '".$id_punto_venta."' and FechaPago = '0000-00-00 00:00:00' order by IDCuota ASC Limit 1";
					db_query($sql_actualiza_pago);
				}
			}
		
		if ($numero_factura=="20114"){
			//echo "<br>Select * from Factura Where NumeroFactura = '".$numero_factura."' and IDPuntoVenta='".$id_punto_venta."'";
			//echo "<br>Select * from CreditoCuota Where IDFactura = '".$row_factura[IDFactura]."' and NumeroFactura='".$numero_factura."' and IDPuntoVenta = '".$id_punto_venta."'";		
		}
		
		
		
		/*
		if ($total_cuotas>0){
			if ($total_cuotas<5){
				echo "<br>esta factura  tiene cuotas incompletas IDFactura: " . $row_factura[IDFactura] . " PuntoVenta " . $id_punto_venta . " Numero " . $numero_factura;					
			}
		}
		else{
			
			//creo el encabezado de la cuota
			$numero_documento=get_field("Cliente","Cedula","IDCliente",$row_factura[IDCliente]);
			//verifico que no exista el registro en credito
			$sql_verifica_credito=db_query("Select * from Credito Where IDFactura='".$row_factura[IDFactura]."' and IDCliente = '".$row_factura[IDCliente]."' and NumeroDocumento = '".$numero_documento."' and IDPuntoVenta = '".$id_punto_venta."'");
			$total_cuotas_encabezado=db_num_rows($sql_verifica_credito);
			if ($total_cuotas_encabezado>0){				
				//echo "<br>esta factura  tiene Encabezado de cuotas IDFactura: " . $row_factura[IDFactura] . " PuntoVenta " . $id_punto_venta . " Numero " . $numero_factura;	
			}
			else{
				if ($row_factura[IDFactura]!="" && $row_factura[IDCliente]!="" && $id_punto_venta!=""){
					$inserta_credito=db_query("insert into Credito (IDFactura, IDCliente, NumeroDocumento, NumeroFactura, IDPuntoVenta, FechaFactura, ValorTotal, Cancelado, UsuarioTrCr, FechaTrCr)
									  VALUES ('".$row_factura[IDFactura]."','".$row_factura[IDCliente]."','".$numero_documento."','".$numero_factura."','".$id_punto_venta."','".$row_factura[FechaFactura]."','".$row_factura[ValorTotal]."','S','1',NOW()) ");			
					$creditos_creados++;
				}
			}
			
			// inserto las cuotas
			$valor_cuota=(int)($row_factura[ValorTotal]/6);
			$fecha_actual=substr($row_factura[FechaFactura],0,10);
			if ($row_factura[IDFactura]!="" && $row_factura[IDCliente]!="" && $id_punto_venta!=""){
				for($i=1;$i<=5;$i++){
					$siguiente_fecha=strtotime ( '+15 day' , strtotime ( $fecha_actual ) );
					$fecha_proxima_cuota=date("Y-m-d",$siguiente_fecha);
					$fecha_actual=$fecha_proxima_cuota;
					
					$sql_inserta_cuota="Insert Into CreditoCuota (IDFactura, IDCuota, NumeroFactura, IDPuntoVenta, FechaCuota, ValorTotal, UsuarioTrCr, FechaTrCr)
										Values ('".$row_factura[IDFactura]."',".$i.",'".$numero_factura."','".$id_punto_venta."','".$fecha_proxima_cuota."','".$valor_cuota."','1',NOW())";	
					
										
					//db_query($sql_inserta_cuota);
										
				}
				
			}			
			
		}
	
	
		
		//consulto las cuotas que registra como pagadas	
		$primera_fecha="2014-10-16";
		$contador_fecha=0;
		for($i_columna=10;$i_columna<=116;$i_columna++){			
			if (!empty($fields[$i_columna])){
					$fecha_columna=strtotime ( '+'.$contador_fecha.' day' , strtotime ( $primera_fecha ) );
					$fecha_pago_registrada=date("Y-m-d",$fecha_columna);				
					echo "<br>$nombre_cliente: Dato en columna " . $i_columna . " Valor = " . trim($fields[$i_columna] . " En la fecha " . $fecha_pago_registrada);		
					//Actualizo feha de pago segun corresponda
					if(trim($fields[$i_columna])!=""){
						$sql_actualiza_pago="Update CreditoCuota Set FechaPago = '".$fecha_pago_registrada."' Where IDFactura = '".$row_factura[IDFactura]."' and NumeroFactura = '".$numero_factura."' and IDPuntoVenta = '".$id_punto_venta."' and FechaPago = '0000-00-00 00:00:00' order by IDCuota ASC Limit ".trim($fields[$i_columna]);
						db_query($sql_actualiza_pago);
					}
			}
			$contador_fecha++;			
		}
	*/
		
	}
	

	echo "<br>TOTAL CREDITOS CREADOS" . $creditos_creados;
	echo "<br><br>TOTAL CUOTAS CREADAS " . $gran_total;
	
	fclose($fp);
	echo "Terminado Actualizados: " . $cont;
}
else
	echo "error open $filename";







?>