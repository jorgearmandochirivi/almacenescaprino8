<?php
	include("../config.inc.php");
	Encabezado();
	
	
	//borrar puntos de cliente no fidelizados
	$sql_puntos = "Select * from PuntosClienteFidelizacion Where ObservacionesRegla like '%Cuota credito Proceso Automatico%' Group by IDCliente";
	$resul_punto = db_query($sql_puntos);
	while ($row_punto = db_fetch_array($resul_punto)) {
		$fidelizado=get_field("Cliente","ClubSuavidad","IDCliente",$row_punto[IDCliente]);
		if ($fidelizado=="N" || $fidelizado==""):
			$sql_borra = db_query("Delete From PuntosClienteFidelizacion Where IDCliente = '".$row_punto[IDCliente]."' and ObservacionesRegla like '%Cuota credito Proceso Automatico%'");
			$sql_borra_bono = db_query("Delete From BonoFidelizacion Where IDCliente = '".$row_punto[IDCliente]."'");
		endif;
		
	}
	
	echo "Listo";
	
	exit;
				
	
	
	
	function genera_bonos_especiales($idcliente,$frm){
	//consulto si tiene los puntos necesarios para generar un bono
	$sql_puntos_total = "SELECT * FROM PuntosClienteFidelizacion WHERE IDCliente = '" . $idcliente . "' AND FechaVencimiento >= CURDATE() AND Redimido = 'N' Order by IDPuntosClienteFidelizacion	";
	$qry_puntos_total = db_query( $sql_puntos_total );
	while($r_puntos_total = db_fetch_array( $qry_puntos_total )){
		$puntos_total_cliente+=(int)$r_puntos_total["Puntos"];	
		$id_puntos_utilizados[]=$r_puntos_total[IDPuntosClienteFidelizacion];
	}
	
	$total_puntos_disponibles = (int)$puntos_total_cliente;
	
	//verifico cuantos puntos son necesarios para generar bono
	$puntos_para_bono=(int)get_field("ParametroFidelizacion","Valor","IDParametroFidelizacion","2");
	//verifico por cual valor se debe generar el bono
	$valor_bono=get_field("ParametroFidelizacion","Valor","IDParametroFidelizacion","8");
	
	if ($total_puntos_disponibles>=$puntos_para_bono){
		//verifico cuantos bonos puedo generar
		$bonos_ha_generar=(int)$total_puntos_disponibles/$puntos_para_bono;				
		//puntos que sobran		
		$puntos_sobran=$total_puntos_disponibles - ($puntos_para_bono*(int)$bonos_ha_generar);
		if ($puntos_sobran>0){
			// creo los puntos sobrantes para utilizarlos en un proximo bono
				//los puntos se vencen en X año a partir del ultimo dia del mes
				$vigencia_puntos=get_field("ParametroFidelizacion","Valor","IDParametroFidelizacion",1);									
				if ((int)$vigencia_puntos==0)
				$vigencia_puntos="4";
				$array_fecha_factura = explode("-", substr( $frm["FechaFactura"], 0, 10 ) );						
				$mes = $array_fecha_factura[1];
				$year = date("Y") + $vigencia_puntos;
				
				$m = mktime( 0, 0, 0, $mes, 1, $year ); 
				$dia = date("t",$m);
				
				$fechavencimiento = $year . "-" . $mes . "-" . $dia;				
				
				//Actualizo el total de puntos de la ultima factura 			
				$sql_actualiza_puntos=db_query("Update Factura set PuntosDisponiblesFactura = '".(int)$puntos_sobran."' where IDFactura = '".$frm[id]."'");
				
		}
		
		//Crear los bonos
		for ($i=1;$i<=(int)$bonos_ha_generar;$i++){
			//Estado bono D=Disponible			
				//los bonos se vencen en X meses a partir del ultimo dia del mes
				$vigencia_bonos=get_field("ParametroFidelizacion","Valor","IDParametroFidelizacion",3);
				if ((int)$vigencia_bonos==0)
					$vigencia_bonos="6";
				
				$fecha_fact=substr( $frm["FechaFactura"], 0, 10 );
				$fecha_actual_calcular=date ( 'Y-m-d');
				$fecha_vence_bono = strtotime ( '+'.$vigencia_bonos.' month' , strtotime ( $fecha_actual_calcular ) ) ;
				$fecha_vence_bono = date ( 'Y-m-d' , $fecha_vence_bono );				
			
			$sql_inserta_bono=db_query("Insert into BonoFidelizacion (IDCliente, IDPuntoVenta, IDFacturaPadre, Valor, Fecha, Estado, FechaVencimiento, UsuarioTrCr, FechaTrCr) Values ('".$idcliente."','".$frm[idpunto]."','".$frm["id"]."','".$valor_bono."',NOW(),'D','".$fecha_vence_bono."','".$frm[UsuarioTrCr]."',NOW())");
			$id_bonos[]=db_insert_id();
		}
		
		//Actualizo los puntos a Redimidos para no volver a utilizarlos
		if (count($id_puntos_utilizados)>0){
			$puntos_descontar=$puntos_para_bono*(int)$bonos_ha_generar;
			foreach ($id_puntos_utilizados as $id_punto){
				// traigo los puntos disponibles por registro
				$punto_registro=get_field("PuntosClienteFidelizacion","Puntos","IDPuntosClienteFidelizacion",$id_punto);				
				
				if ($punto_registro<=$puntos_descontar){
					$puntos_resta=$punto_registro;
					$puntos_descontar-=$punto_registro;					
				}
				else{	
					$puntos_resta=$puntos_descontar;										
				}
					$sql_actualiza_punto=db_query("Update PuntosClienteFidelizacion set Redimido = 'S', PuntosRedimidos = '".$puntos_resta."' where IDPuntosClienteFidelizacion in (".$id_punto.")");	
					// inserto log de puntos por bono					
					foreach($bonos_id as $bono_value){
						$sql_log_puntos="Insert into LogPuntosFidelizacion (IDPuntosClienteFidelizacion, IDBonoFidelizacion, PuntosRedimidos)
										 Values ('".$id_punto."','".$bono_value."','".$puntos_resta."') ";
						db_query($sql_log_puntos);
					}					
				}
			}	
			
	}
	else{
		//Actualizo el total de puntos de la ultima factura 			
		$sql_actualiza_puntos=db_query("Update Factura set PuntosDisponiblesFactura = '".(int)$total_puntos_disponibles."' where IDFactura = '".$frm[id]."'");
	
	}		
}

	
	
	
    $sql_cuota = db_query("SELECT count(`IDFactura`) as CuotasPagadas, CreditoCuota.* 
						  FROM CreditoCuota 
						  WHERE YEAR(FechaPago)  = 2015 and (YEAR(FechaCuota)=2014 or YEAR(FechaCuota)=2015) 
						  Group by  IDFactura");
	while($row_cuota = db_fetch_array($sql_cuota))
	{
		$contador++;
		$id_cliente = "";
		
		//Verifico si esta creada los primeros puntos de la primera cuota
		$sql_puntos_primero = "Select count(IDFactura) as CuotasRegistradas, PuntosClienteFidelizacion.*
					  from PuntosClienteFidelizacion 
					  Where IDPuntoVenta = '".$row_cuota[IDPuntoVenta]."' and IDFactura = '".$row_cuota[IDFactura]."' and ObservacionesRegla not like '%cuota%'";
		$result_cliente_primero = db_query($sql_puntos_primero);			  
		$row_puntos_primero = db_fetch_array($result_cliente_primero);
		if ((int)$row_puntos_primero[CuotasRegistradas]<=0):
			$ingresar_cuota_adicional=1;
		else:
			$ingresar_cuota_adicional=0;	
		endif;
		
		
			
		$sql_puntos = "Select count(IDFactura) as CuotasRegistradas, PuntosClienteFidelizacion.*
					  from PuntosClienteFidelizacion 
					  Where IDPuntoVenta = '".$row_cuota[IDPuntoVenta]."' and IDFactura = '".$row_cuota[IDFactura]."' and ObservacionesRegla like '%cuota%'";
		$result_cliente = db_query($sql_puntos);			  
		$row_puntos = db_fetch_array($result_cliente);
		
		if ($row_puntos[CuotasRegistradas]!=$row_cuota[CuotasPagadas]){
			$faltan_cuotas = ((int)$row_cuota[CuotasPagadas]+(int)$ingresar_cuota_adicional) - $row_puntos[CuotasRegistradas]; // la cuota pagada al recibir el producto
			$id_cliente = get_field("Factura","IDCliente","IDFactura",$row_cuota[IDFactura]);
			$cliente_fidelizado = get_field("Cliente","ClubSuavidad","IDCliente",$id_cliente);
			if ($cliente_fidelizado=="S"){
						for($i=1;$i<=$faltan_cuotas;$i++){
						
								$sql_regla=db_query("Select * from ReglaPunto Where Activo = 'S' and FechaInicio <= CURDATE() and FechaFin >= CURDATE() limit 1");
								while($r_regla = db_fetch_array( $sql_regla )){
										$nombre_regla_utilizada=$r_regla[Nombre];
										$descrip_regla_utilizada=$r_regla[Descripcion];
						
										//cada X Valor pesos vale X puntos
										$cantidas_puntos = $r_regla[Puntos];
										$por_cada_valor=$r_regla[Valor];				
										
										$puntos_esta_factura = (int)$row_cuota[ValorTotal] * (int)$cantidas_puntos / $por_cada_valor;
										
										//los puntos se vencen en X año a partir del ultimo dia del mes
										$vigencia_puntos=get_field("ParametroFidelizacion","Valor","IDParametroFidelizacion",1);									
										if ((int)$vigencia_puntos==0)
											$vigencia_puntos="4";
											
										$array_fecha_factura = explode("-", substr( $row_cuota["FechaTrCr"], 0, 10 ) );
												
										$mes = $array_fecha_factura[1];
										$year = date("Y") + $vigencia_puntos;
										
										$m = mktime( 0, 0, 0, $mes, 1, $year ); 
										$dia = date("t",$m);
										
										$fechavencimiento = $year . "-" . $mes . "-" . $dia;
										
										echo "<br>" .$sql_puntos = " INSERT INTO PuntosClienteFidelizacion (IDCliente, IDPuntoVenta, IDFactura,IDReglaPunto,NombreRegla, DescripcionRegla, Puntos, FechaVencimiento,ObservacionesRegla, FechaTrCr) 
															VALUES ('" .$id_cliente . "','" . $row_cuota[IDPuntoVenta] . "','" . $row_cuota[IDFactura] . "', '".$r_regla[IDReglaPunto]."',  '".$nombre_regla_utilizada."','".$descrip_regla_utilizada."','" . (int)$puntos_esta_factura . "','" . $fechavencimiento . "', 'Cuota credito Proceso Automatico',  NOW() ) ";				
										
										$qry_puntos = db_query( $sql_puntos );
										
								}
								
								$frm["FechaFactura"]=date("Y-m-d");
								$frm["IDCliente"]=$id_cliente;
								$frm["id"]=$row_cuota[IDFactura]; // id factura
								$frm[idpunto]=$row_cuota[IDPuntoVenta];
								$frm[UsuarioTrCr]=13;
								
								genera_bonos_especiales($id_cliente,$frm);			
						}
			}
						
			
			
			
			
				
			
		}
		
		echo "<br><br>OTRO $contador <br><br>";
		
		
	}	
	
?>	