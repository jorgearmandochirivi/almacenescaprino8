<?php

/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Sistema de Fidelizaciona
	Creador por: John Escobar
	Iniciado: Septiembre /2011
	Modificado: Septiembre 25 / 2014 por Jorge Chirivi
 *******************************************************************************************/

function check_in_range($start_date, $end_date, $evaluame)
{
	$start_ts = strtotime($start_date);
	$end_ts = strtotime($end_date);
	$user_ts = strtotime($evaluame);
	return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
}


function genera_bonos($idcliente, $frm)
{

	$CedulaCliente = get_field("Cliente", "Cedula", "IDCliente", $idcliente);
	$ClienteSuavidad = get_field("Cliente", "ClubSuavidad", "IDCliente", $idcliente);
	if ($CedulaCliente == "222222222222"  || $ClienteSuavidad != "S")
		return true;

	//consulto si tiene los puntos necesarios para generar un bono
	$sql_puntos_total = "SELECT * FROM PuntosClienteFidelizacion WHERE IDCliente = '" . $idcliente . "' AND FechaVencimiento >= CURDATE() AND Redimido = 'N' Order by IDPuntosClienteFidelizacion	";
	$qry_puntos_total = db_query($sql_puntos_total);
	while ($r_puntos_total = db_fetch_array($qry_puntos_total)) {
		$puntos_total_cliente += (int)$r_puntos_total["Puntos"];
		$id_puntos_utilizados[] = $r_puntos_total[IDPuntosClienteFidelizacion];
	}

	$total_puntos_disponibles = (int)$puntos_total_cliente;

	//verifico cuantos puntos son necesarios para generar bono
	$puntos_para_bono = (int)get_field("ParametroFidelizacion", "Valor", "IDParametroFidelizacion", "2");
	//verifico por cual valor se debe generar el bono
	$valor_bono = get_field("ParametroFidelizacion", "Valor", "IDParametroFidelizacion", "8");

	if ($total_puntos_disponibles >= $puntos_para_bono) {
		//verifico cuantos bonos puedo generar
		$bonos_ha_generar = (int)$total_puntos_disponibles / $puntos_para_bono;
		//puntos que sobran		
		$puntos_sobran = $total_puntos_disponibles - ($puntos_para_bono * (int)$bonos_ha_generar);
		if ($puntos_sobran > 0) {
			// creo los puntos sobrantes para utilizarlos en un proximo bono
			//los puntos se vencen en X año a partir del ultimo dia del mes
			$vigencia_puntos = get_field("ParametroFidelizacion", "Valor", "IDParametroFidelizacion", 1);
			if ((int)$vigencia_puntos == 0)
				$vigencia_puntos = "4";
			$array_fecha_factura = explode("-", substr($frm["FechaFactura"], 0, 10));
			$mes = $array_fecha_factura[1];
			$year = date("Y") + $vigencia_puntos;

			$m = mktime(0, 0, 0, $mes, 1, $year);
			$dia = date("t", $m);

			$fechavencimiento = $year . "-" . $mes . "-" . $dia;
			$sql_puntos = " INSERT INTO PuntosClienteFidelizacion (IDCliente, IDPuntoVenta, IDFactura,IDReglaPunto,NombreRegla, DescripcionRegla, Puntos, FechaVencimiento,ObservacionesRegla, FechaTrCr) VALUES ('" . $frm["IDCliente"] . "','','" . $frm["id"] . "', '',  'Puntos sobraron al generar bono','Puntos excedente de bono','" . (int)$puntos_sobran . "','" . $fechavencimiento . "', '" . $obser_regla . "',  NOW() ) ";
			$id_nuevo_punto = $qry_puntos = db_query($sql_puntos);

			//Actualizo el total de puntos de la ultima factura 			
			$sql_actualiza_puntos = db_query("Update Factura set PuntosDisponiblesFactura = '" . (int)$puntos_sobran . "' where IDFactura = '" . $frm['id'] . "'");
		}

		//Crear los bonos
		for ($i = 1; $i <= (int)$bonos_ha_generar; $i++) {
			//Estado bono D=Disponible			
			//los bonos se vencen en X meses a partir del ultimo dia del mes
			$vigencia_bonos = get_field("ParametroFidelizacion", "Valor", "IDParametroFidelizacion", 3);
			if ((int)$vigencia_bonos == 0)
				$vigencia_bonos = "3";

			$fecha_fact = substr($frm["FechaFactura"], 0, 10);
			$fecha_actual_calcular = date('Y-m-d');
			$fecha_vence_bono = strtotime('+' . $vigencia_bonos . ' month', strtotime($fecha_actual_calcular));
			$fecha_vence_bono = date('Y-m-d', $fecha_vence_bono);

			$sql_inserta_bono = db_query("Insert into BonoFidelizacion (IDCliente, IDPuntoVenta, IDFacturaPadre, Valor, Fecha, Estado, FechaVencimiento, UsuarioTrCr, FechaTrCr) Values ('" . $idcliente . "','" . $frm['idpunto'] . "','" . $frm["id"] . "','" . $valor_bono . "',NOW(),'D','" . $fecha_vence_bono . "','" . $frm['UsuarioTrCr'] . "',NOW())");
			$id_bonos[] = db_insert_id();
		}

		// mustro ventana para imprimir bono
		if (count($id_bonos) > 0) {
			$correo_cliente = get_field("Cliente", "EMail", "IDCliente", $idcliente);
			$bonos_id = implode("|", $id_bonos);
			echo "<script>window.open('../Movimiento/popBono.php?id=" . $bonos_id . "&correo=" . $correo_cliente . "&id_cliente=" . $idcliente . "','','width=650, height=450, scrollbars=yes');</script>";
		}

		//Actualizo los puntos a Redimidos para no volver a utilizarlos
		if (count($id_puntos_utilizados) > 0) {
			$puntos_descontar = $puntos_para_bono * (int)$bonos_ha_generar;
			foreach ($id_puntos_utilizados as $id_punto) {
				// traigo los puntos disponibles por registro
				$punto_registro = get_field("PuntosClienteFidelizacion", "Puntos", "IDPuntosClienteFidelizacion", $id_punto);

				if ($punto_registro <= $puntos_descontar) {
					$puntos_resta = $punto_registro;
					$puntos_descontar -= $punto_registro;
				} else {
					$puntos_resta = $puntos_descontar;
				}
				$sql_actualiza_punto = db_query("Update PuntosClienteFidelizacion set Redimido = 'S', PuntosRedimidos = '" . $puntos_resta . "' where IDPuntosClienteFidelizacion in (" . $id_punto . ")");
				// inserto log de puntos por bono					
				foreach ($bonos_id as $bono_value) {
					$sql_log_puntos = "Insert into LogPuntosFidelizacion (IDPuntosClienteFidelizacion, IDBonoFidelizacion, PuntosRedimidos)
										 Values ('" . $id_punto . "','" . $bono_value . "','" . $puntos_resta . "') ";
					db_query($sql_log_puntos);
				}
			}
		}
	} else {
		//Actualizo el total de puntos de la ultima factura 			
		$sql_actualiza_puntos = db_query("Update Factura set PuntosDisponiblesFactura = '" . (int)$total_puntos_disponibles . "' where IDFactura = '" . $frm['id'] . "'");
	}
}



function fid_calculapuntos($frm)
{

	global $Nombre_Usuario, $ID_Usuario, $IDPuntoVenta;
	$array_return = array();
	$puntos_esta_factura = "";
	//convierto valor total de compra en numerico
	$total_compra_factura = (int)$frm['ValorTotal'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/", "", $frm['ValorTotal']);

	// Solo se dan puntos con el valor de formas de pago diferente a bono 
	$total_compra = 0; // Inicializar variable
	foreach ($frm['IDPuntoVentaBanco'] as $FormaPago) {
		if ($frm['IDFormaPago'][$FormaPago] <> 17 && $frm['IDFormaPago'][$FormaPago] <> 13) { // si no es con bono y cuando no es con credito como se hace en medellin
			if (!empty($frm['Valor'][$FormaPago])) {
				$total_compra += $frm['Valor'][$FormaPago];
			}
		}
	}



	$id_cliente = $frm['IDCliente'];
	$cedula_cliente = get_field("Cliente", "Cedula", "IDCliente", $id_cliente);


	//consulto el detalle de la factura
	$sql_detalle_factura = "Select * From  DetalleFactura Where IDFactura = '" . $frm['id'] . "' and IDPuntoVenta = '" . $frm['idpunto'] . "'";
	$query_detalle = db_query($sql_detalle_factura);
	while ($row_detalle = db_fetch_array($query_detalle)) {
		$referencia_item = get_field("Referencia", "Numero", "IDReferencia", get_field("PuntoVentaReferencia", "IDReferencia", "IDPuntoVentaReferencia", get_field("CodificacionEspecifica", "IDPuntoVentaReferencia", "IDCodificacionEspecifica", $row_detalle['IDCodificacionEspecifica'])));
		$Cantidad = $row_detalle['Cantidad'];
		$num_ref = $referencia_item;
		$array_genero_item[] = get_field("Referencia", "Sexo", "Numero", $num_ref);
		$array_referencia_item[] = get_field("Referencia", "IDReferencia", "Numero", $num_ref);
		$array_linea_item[] = get_field("Referencia", "IDLinea", "Numero", $num_ref);
		$array_tiporeferencia_item[] = get_field("Referencia", "IDTipoReferencia", "Numero", $num_ref);
		$array_color_item[] = get_field("Referencia", "IDColor", "Numero", $num_ref);
	}

	//consulto las reglas de puntos que estan activas
	$sql_regla = db_query("Select * from ReglaPunto Where Activo = 'S' and FechaInicio <= CURDATE() and FechaFin >= CURDATE()");
	while ($r_regla = db_fetch_array($sql_regla)) {
		$aplica_regla = 0;
		$cumple_condicion_genero = false;
		$cumple_condicion_linea = false;
		$cumple_condicion_referencia = false;
		$cumple_condicion_tiporeferencia = false;
		$cumple_condicion_color = false;
		$cumple_condicion_cedula = false;
		$nombre_regla_utilizada = "";
		$descrip_regla_utilizada = "";
		$cantidas_puntos = "";
		$por_cada_valor = "";
		$puntos_esta_factura = "";
		$valor_sobrante = "";
		$vigencia_puntos = "";
		$array_fecha_factura = "";
		$fechavencimiento = "";
		$obser_regla = "";

		//verifico que el valor de la compra aplique para la regla
		if ($total_compra >= $r_regla['Valor'])
			$aplica_regla = 1;

		//GENERO
		if ($r_regla['Genero'] != "") {
			$array_genero_regla = explode("|", $r_regla['Genero']);
			//verifico que la compra tenga solo los generos requeridos
			if (count($array_genero_item) > 0) {
				foreach ($array_genero_item as $genero_value) {
					if (in_array($genero_value, $array_genero_regla)) {
						$cumple_condicion_genero = true;
					} else {
						$cumple_condicion_genero = false;
					}
				}
			}
		} else {
			$cumple_condicion_genero = true;
		}


		//LINEA
		if ($r_regla['IDLinea'] != "") {
			$array_linea_regla = explode("|", $r_regla['IDLinea']);

			//verifico que la compra tenga solo los generos requeridos
			if (count($array_linea_item) > 0) {
				foreach ($array_linea_item as $linea_value) {
					if (in_array($linea_value, $array_linea_regla)) {
						$cumple_condicion_linea = true;
					} else {
						$cumple_condicion_linea = false;
					}
				}
			}
		} else {
			$cumple_condicion_linea = true;
		}


		//REFERENCIA
		if ($r_regla['IDReferencia'] != "") {
			$array_referencia_regla = explode("|", $r_regla['IDReferencia']);

			//verifico que la compra tenga solo los generos requeridos
			if (count($array_referencia_item) > 0) {
				foreach ($array_referencia_item as $referencia_value) {
					if (in_array($referencia_value, $array_referencia_regla)) {
						$cumple_condicion_referencia = true;
					} else {
						$cumple_condicion_referencia = false;
					}
				}
			}
		} else {
			$cumple_condicion_referencia = true;
		}


		//TIPO REFERENCIA
		if ($r_regla['IDTipoReferencia'] != "") {
			$array_tiporeferencia_regla = explode("|", $r_regla['IDTipoReferencia']);

			//verifico que la compra tenga solo los generos requeridos
			if (count($array_tiporeferencia_item) > 0) {
				foreach ($array_tiporeferencia_item as $tiporeferencia_value) {
					if (in_array($tiporeferencia_value, $array_tiporeferencia_regla)) {
						$cumple_condicion_tiporeferencia = true;
					} else {
						$cumple_condicion_tiporeferencia = false;
					}
				}
			}
		} else {
			$cumple_condicion_tiporeferencia = true;
		}


		//COLOR
		if ($r_regla['IDColor'] != "") {
			$array_color_regla = explode("|", $r_regla['IDColor']);

			//verifico que la compra tenga solo los generos requeridos
			if (count($array_color_item) > 0) {
				foreach ($array_color_item as $color_value) {
					if (in_array($color_value, $array_color_regla)) {
						$cumple_condicion_color = true;
					} else {
						$cumple_condicion_color = false;
					}
				}
			}
		} else {
			$cumple_condicion_color = true;
		}



		if ($r_regla['ArchivoCliente'] != "") {
			//consulto las cedulas ligadas a la regla
			$sql_cedula_regla = db_query("Select * from ReglaCedula Where Cedula = '" . $cedula_cliente . "'");
			if ($total_cedula = db_num_rows($sql_cedula_regla) > 0) {
				$cumple_condicion_cedula = true;
			} else {
				$cumple_condicion_cedula = false;
			}
		} else {
			$cumple_condicion_cedula = true;
		}


		//verifico que tenga por lo menos una factura para que aplique descuentos
		$sql_fac_prim = "SELECT IDFactura From Factura WHERE IDCliente = '" . $id_cliente . "' and FechaFactura >='2017-01-01' and Estado <> 'ANULADA' ";
		$qry_fac_prim = db_query($sql_fac_prim);
		$TotalFac = db_num_rows($qry_fac_prim);
		if ((int)$TotalFac <= 0) {
			$cumple_condicion_cedula = false;
		}

		// Si todas las condiciones son verdaderas la regla aplica para esta factura			
		if ($cumple_condicion_genero = true &&
			$cumple_condicion_linea = true  &&
			$cumple_condicion_referencia = true &&
			$cumple_condicion_tiporeferencia = true &&
			$cumple_condicion_color = true &&
			$cumple_condicion_cedula = true
		) {

			$nombre_regla_utilizada = $r_regla['Nombre'];
			$descrip_regla_utilizada = $r_regla['Descripcion'];

			//cada X Valor pesos vale X puntos
			$cantidas_puntos = $r_regla['Puntos'];
			$por_cada_valor = $r_regla['Valor'];

			//si hay sobrantes en otras facturas lo sumo al total de la factura								
			$sql_sobrante_otra_fac = db_query("select * from ClienteSobrante Where IDCliente = '" . $id_cliente . "' and IDFactura <> '" . $frm['id'] . "' and IDFacturaSumado = 0");
			if (db_num_rows($sql_sobrante_otra_fac) > 0) {
				$row_sobrante = db_fetch_array($sql_sobrante_otra_fac);
				$total_compra += (int)$row_sobrante['Valor'];
				//actualizo el sobrante con la factura en la que se utilizo para no utilizar nunca mas					
				$sql_actualiza_sobrante = db_query("Update ClienteSobrante Set IDFacturaSumado = '" . $frm['id'] . "', FechautilizoSobrante = CURDATE(), UsuarioTrEd = 'Venta Factura', FechatrEd = NOW() Where IDClienteSobrante = '" . $row_sobrante['IDClienteSobrante'] . "'");
			}

			$puntos_esta_factura = (int)$total_compra * (int)$cantidas_puntos / $por_cada_valor;
			$valor_sobrante = (int)$total_compra  - ((int)$puntos_esta_factura * (int)$por_cada_valor / (int)$cantidas_puntos);

			if ($valor_sobrante > 0) {
				//verifico si ya existe un registro con un valor sobrante
				$sql_sobrante = db_query("select * from ClienteSobrante Where IDCliente = '" . $id_cliente . "' and IDFactura = '" . $frm['id'] . "' and IDPuntoVenta = '" . $frm['idpunto'] . "'");
				// inserto el valor $$ sobrante para poder utilizarlo en una factura factura diferente a la actual
				if (db_num_rows($sql_sobrante) == 0) {
					$sql_inserta_valor_sobrante = db_query("Insert into ClienteSobrante (IDCliente,IDFactura,IDPuntoVenta,Valor,Fecha,UsuariotrCr,FechaTrCr) Values ('" . $id_cliente . "','" . $frm['id'] . "','" . $frm['idpunto'] . "','" . $valor_sobrante . "',CURDATE(),'Venta Factura',NOW())");
				}
			}


			//los puntos se vencen en X año a partir del ultimo dia del mes
			$vigencia_puntos = get_field("ParametroFidelizacion", "Valor", "IDParametroFidelizacion", 1);
			if ((int)$vigencia_puntos == 0)
				$vigencia_puntos = "4";

			$array_fecha_factura = explode("-", substr($frm["FechaFactura"], 0, 10));

			$mes = $array_fecha_factura[1];
			$year = date("Y") + $vigencia_puntos;

			$m = mktime(0, 0, 0, $mes, 1, $year);
			$dia = date("t", $m);

			$fechavencimiento = $year . "-" . $mes . "-" . $dia;


			// si el mes actual es el de cumpleaños doy doble puntaje
			//$mes_nacimiento=get_field("Cliente","Mes","IDCliente",$id_cliente);
			if ($mes_nacimiento == date("m")) {
				//$obser_regla="Puntaje Doble por cumpleaños, puntaje normal: ".(int)$puntos_esta_factura;
				//$puntos_esta_factura=(int)$puntos_esta_factura*2;
			}

			$sql_puntos = " INSERT INTO PuntosClienteFidelizacion (IDCliente, IDPuntoVenta, IDFactura,IDReglaPunto,NombreRegla, DescripcionRegla, Puntos, FechaVencimiento,ObservacionesRegla, FechaTrCr) VALUES ('" . $frm["IDCliente"] . "','" . $frm["idpunto"] . "','" . $frm["id"] . "', '" . $r_regla['IDReglaPunto'] . "',  '" . $nombre_regla_utilizada . "','" . $descrip_regla_utilizada . "','" . (int)$puntos_esta_factura . "','" . $fechavencimiento . "', '" . $obser_regla . "',  NOW() ) ";
			$qry_puntos = db_query($sql_puntos);


			$total_puntos_esta_factura += $puntos_esta_factura;
		}
	}

	//Genera bonos		
	if ($cumple_condicion_cedula == true) {
		genera_bonos($frm["IDCliente"], $frm);
	}


	$array_return = fid_get_puntos($id_cliente);
} //end function actualiza_puntos_fid( $frm )

function fid_actualizapuntos($frm)
{
	global $Nombre_Usuario, $ID_Usuario, $IDPuntoVenta;
	$array_return = array();

	$sql_cliente = " SELECT Fidelizado FROM Cliente WHERE IDCliente = '" . $frm["IDCliente"] . "' ";
	$qry_cliente = db_query($sql_cliente);
	$r_cliente = db_fetch_array($qry_cliente);

	if ($r_cliente["Fidelizado"] == 'S') {

		//cada 100 pesos vale 1 punto
		$valorpunto = 100;

		$puntos = $frm["ValorTotal"] / $valorpunto;

		//los puntos se vencen en 1 año a partir del ultimo dia del mes
		$array_fecha_factura = explode("-", substr($frm["FechaFactura"], 0, 10));

		$mes = $array_fecha_factura[1];
		$year = date("Y") + 1;

		$m = mktime(0, 0, 0, $mes, 1, $year);
		$dia = date("t", $m);

		$fechavencimiento = $year . "-" . $mes . "-" . $dia;

		$sql_puntos = " INSERT INTO PuntosCliente (IDCliente, IDPuntoVenta, IDFactura, Puntos, FechaVencimiento, FechaTrCr) VALUES ('" . $frm["IDCliente"] . "','" . $frm["IDPuntoVenta"] . "','" . $frm["IDFactura"] . "','" . $puntos . "','" . $fechavencimiento . "',NOW() ) ";
		$qry_puntos = db_query($sql_puntos);

		//return puntos
		$array_return = fid_get_puntos($idcliente);
	} //end if


} //end function actualiza_puntos_fid( $frm )

function fid_get_puntos($idcliente, $id_factura = "")
{
	$estemes = date("Y-m");
	$hoy = date("Y-m-d");

	$array_return = array();

	//puntos total
	$sql_puntos_total = "SELECT SUM(Puntos) as PuntosTotales FROM PuntosClienteFidelizacion WHERE IDCliente = '" . $idcliente . "' AND FechaVencimiento >= CURDATE() AND Redimido = 'N' ";
	$qry_puntos_total = db_query($sql_puntos_total);
	$r_puntos_total = db_fetch_array($qry_puntos_total);

	$array_return["puntostotal"] = $r_puntos_total["PuntosTotales"];

	//puntos ultima compra
	$sql_puntos_ultima_compra = "SELECT Puntos as PuntosTotales FROM PuntosClienteFidelizacion WHERE IDCliente = '" . $idcliente . "' AND FechaVencimiento >= CURDATE() ORDER BY IDFactura DESC, IDPuntosClienteFidelizacion  ASC LIMIT 1 ";
	$qry_puntos_ultima_compra = db_query($sql_puntos_ultima_compra);
	$r_puntos_ultima_compra = db_fetch_array($qry_puntos_ultima_compra);

	$array_return["puntosultimacompra"] = $r_puntos_ultima_compra["PuntosTotales"];;


	//puntos proximos a vencer
	$sql_puntos_prox_vence = "SELECT SUM(Puntos) as PuntosTotales FROM PuntosClienteFidelizacion WHERE IDCliente = '" . $idcliente . "' AND FechaVencimiento >= CURDATE() AND  Redimido = 'N' AND FechaVencimiento <= DATE_ADD( CURDATE( ) , INTERVAL 30 DAY ) ";
	$qry_puntos_prox_vence = db_query($sql_puntos_prox_vence);
	$r_puntos_prox_vence = db_fetch_array($qry_puntos_prox_vence);

	$array_return["puntosproxvence"] = $r_puntos_prox_vence["PuntosTotales"];

	//bonos proximos a vencer
	$sql_bonos_prox_vence = "SELECT COUNT(IDBonoFidelizacion) as BonosTotales FROM BonoFidelizacion WHERE IDCliente = '" . $idcliente . "' AND FechaVencimiento >= CURDATE() AND  Estado = 'D' AND FechaVencimiento <= DATE_ADD( CURDATE( ) , INTERVAL 30 DAY) ";
	$qry_bonos_prox_vence = db_query($sql_bonos_prox_vence);
	$r_bonos_prox_vence = db_fetch_array($qry_bonos_prox_vence);

	$array_return["bonosproxvence"] = $r_bonos_prox_vence["BonosTotales"];

	//puntos del mes
	$sql_puntos_mes = "SELECT SUM(Puntos) as PuntosTotales FROM PuntosClienteFidelizacion WHERE IDCliente = '" . $idcliente . "' AND FechaVencimiento >= CURDATE() AND Redimido = 'N' AND DATE_FORMAT( FechaVencimiento, '%Y-%m' ) = '" . $estemes . "' ";
	$qry_puntos_mes = db_query($sql_puntos_mes);
	$r_puntos_mes = db_fetch_array($qry_puntos_mes);

	$array_return["puntosmes"] = $r_puntos_mes["PuntosTotales"];


	//puntos antes de la ultima compra
	$sql_ultimos_puntos = "Select PuntosDisponiblesFactura From Factura Where IDCliente = '" . $idcliente . "' and IDFactura <> '" . $id_factura . "' Order by IDFactura DESC limit 1";
	$qry_ultimos_puntos = db_query($sql_ultimos_puntos);
	$row_ultimos_puntos = db_fetch_array($qry_ultimos_puntos);
	$PuntosUltimaFactura = $row_ultimos_puntos["PuntosDisponiblesFactura"];
	//$PuntosUltimaFactura=(int)get_field("Cliente","PuntosUltimaFactura","IDCliente",$idcliente);
	if ($PuntosUltimaFactura > 0)
		$array_return["puntoantescompra"] = $PuntosUltimaFactura;
	else
		$array_return["puntoantescompra"] = 0;


	//puntos redimidos de la ultima compra
	//puntos total
	$sql_puntos_redimidos_ultimacompra = "SELECT SUM(Puntos) as PuntosTotales FROM PuntosClienteFidelizacion WHERE IDCliente = '" . $idcliente . "' AND FechaVencimiento >= CURDATE() AND Redimido = 'S' and IDFactura = '" . $id_factura . "' ";
	$qry_puntos_redimidos_ultimacompra = db_query($sql_puntos_redimidos_ultimacompra);
	$r_puntos_redimidos_ultimacompra = db_fetch_array($qry_puntos_redimidos_ultimacompra);

	if ((int)$r_puntos_redimidos_ultimacompra["PuntosTotales"] > 0) {
		$puntos_ultima_compra = $r_puntos_redimidos_ultimacompra["PuntosTotales"] - $array_return["puntostotal"];
		$total_puntos_disponibles = (int)$PuntosUltimaFactura + (int)$puntos_ultima_compra;
		$puntos_para_bono = (int)get_field("ParametroFidelizacion", "Valor", "IDParametroFidelizacion", "2");
		$bonos_ha_generar = (int)$total_puntos_disponibles / $puntos_para_bono;
		$puntos_redimidos = (int)$bonos_ha_generar * $puntos_para_bono;
		if ($puntos_redimidos > 0)
			$array_return["puntoredimidos"] = $puntos_redimidos;
		else
			$array_return["puntoredimidos"] = $puntos_ultima_compra;
	} else
		$array_return["puntoredimidos"] = "0";






	return $array_return;
} //end fucntion



function fid_notificaciones($datos_cliente)
{
	$descuento_por_cumpleanos = 0;
	$notificacion = array();
	$valores_retorna = array();
	$mes_actual = date("m");
	$dia_actual = date("d");
	$year_actual = date("Y");
	$dia_semana = date("w");
	$datos_cliente->Mes;
	$MesFidelizado = $datos_cliente->FechaClubSuavidad;
	$MesFidelizado = date("m", strtotime($MesFidelizado));

	// verifico si esta en el mes de cumpleaños
	if ($mes_actual == $datos_cliente->Mes || $mes_actual == $datos_cliente->Mes) {
		//$notificacion[]="<li>* Doble  puntaje  por   mes  de  cumplea&ntilde;os</li>";
	}
	// verifico si esta en la semana de cumpleaños
	$fecha_cumpleaños = date("Y") . "-" . (int)$mes_actual . "-" . (int)$datos_cliente->Dia;
	$fecha = date($fecha_cumpleaños);

	$fecha_actual = date("Y-m-d");
	//$fecha_actual=date("2015-01-30");
	$dia_inicio_semana_actual = strtotime('-3 day', strtotime($fecha_actual));
	$dia_empieza_semana = date("d", $dia_inicio_semana_actual);
	$fecha_empieza_rango = date("Y-m-d", $dia_inicio_semana_actual);

	$dia_para_terminar_semana = 6 - (int)$dia_semana;
	$dia_fin_semana_actual = strtotime('+3 day', strtotime($fecha_actual));
	$dia_fin_semana = date("d", $dia_fin_semana_actual);
	$fecha_termina_rango = date("Y-m-d", $dia_fin_semana_actual);


	$evaluame = date("Y") . "-" . $datos_cliente->Mes . "-" . $datos_cliente->Dia;
	/*		
		echo "<br>Empieza: " . $fecha_empieza_rango;
		echo "<br>Termina: " . $fecha_termina_rango;
		echo "<br>Evalua: " . date("Y")."-".$datos_cliente->Mes."-".$datos_cliente->Dia;;
		*/

	$fecha_en_rango = check_in_range($fecha_empieza_rango, $fecha_termina_rango, $evaluame);

	//Promocion si esta en el mes de cumpleaños
	if ($mes_actual == $datos_cliente->Mes || $mes_actual == $datos_cliente->Mes) {
		$fecha_en_rango = 1;
		//verifico que no tenga una fcatura en este mes	por que solo se puede 1
		$fechainival = date("Y-m-01 00:00:00");
		$fechafinival = date("Y-m-t 23:59:59", strtotime($fechainival));

		//verifico que tenga por lo menos una factura para que aplique descuentos
		$sql_fac_prim = "SELECT IDFactura From Factura WHERE IDCliente = '" . $datos_cliente->IDCliente . "' and FechaFactura >='2017-01-01' and Estado <> 'ANULADA' ";
		$qry_fac_prim = db_query($sql_fac_prim);
		$TotalFac = db_num_rows($qry_fac_prim);
		if ((int)$TotalFac <= 0) {
			$notificacion = array();
			return true;
		}

		// si ya tiene una factura de descuento de cumple en este año ya no se le toma mas
		$sql_fac_cumpl = "SELECT IDFactura From Factura WHERE IDCliente = '" . $datos_cliente->IDCliente . "' and YEAR(FechaFactura) = '" . date("Y") . "' and Estado <> 'ANULADA' and (ObservacionDescuento like '%semana de cumpleanos%' or DescuentoCumple = 1)";
		$qry_fac_cumpl = db_query($sql_fac_cumpl);
		$TotalFac = db_num_rows($qry_fac_cumpl);
		if ((int)$TotalFac > 0) {
			$notificacion = array();
			return true;
		}


		$NumeroFacturasMesFidelizado = 0;
		$cumple_condicion_fid = "S";
		$sql_fac = "SELECT IDFactura, IDPuntoVenta, FechaFactura From Factura WHERE IDCliente = '" . $datos_cliente->IDCliente . "' and FechaFactura >='" . $fechainival . "' and  FechaFactura <='" . $fechafinival . "' and Estado <> 'ANULADA' ";
		$qry_fac = db_query($sql_fac);
		while ($row_fac = db_fetch_array($qry_fac)) {


			// Si se fidelizo en el mismo mes que hizo la factura no le tomo en cuenta esa factura
			$mes_factura = date("m", strtotime($row_fac["FechaFactura"]));
			if ((int)$MesFidelizado == (int)$mes_factura && date("Y", strtotime($row_fac["FechaFactura"])) == date("Y")) {
				$NumeroFacturasMesFidelizado++;
				if ($NumeroFacturasMesFidelizado > 1) {
					$cumple_condicion_fid = "N";
					$notificacion = array();
					$valores_retorna["Mensaje"] = $notificacion;
					$valores_retorna["DescuentoSemanaCumple"] = $descuento_por_cumpleanos;
					return $valores_retorna;
				}
			}

			//verifico que la factura no sea de servicio por que no cuenta para cumpleaños	
			$sql_detalle_fact = "SELECT IDCodificacionEspecifica FROM DetalleFactura WHERE IDFactura='" . $row_fac["IDFactura"] . "' and IDPuntoVenta = '" . $row_fac["IDPuntoVenta"] . "' ";
			$qry_detalle_fac = db_query($sql_detalle_fact);
			while ($row_detalle_fac = db_fetch_array($qry_detalle_fac)) {




				if ($fecha_en_rango == 1 && $cumple_condicion_fid == "S") {
					$sql_codif = "SELECT IDPuntoVentaReferencia FROM CodificacionEspecifica WHERE IDCodificacionEspecifica = '" . $row_detalle_fac["IDCodificacionEspecifica"] . "' LIMIT 1";
					$qry_codif = db_query($sql_codif);
					$row_detalle_fac = db_fetch_array($qry_codif);
					$IDPtoCodif = $row_detalle_fac["IDPuntoVentaReferencia"];

					$sql_pto_r = "SELECT IDReferencia FROM PuntoVentaReferencia WHERE IDPuntoVentaReferencia = '" . $IDPtoCodif . "' and IDPuntoVenta = '" . $row_fac["IDPuntoVenta"] . "' LIMIT 1";
					$qry_pto_r = db_query($sql_pto_r);
					$row_pto_r = db_fetch_array($qry_pto_r);
					$IDReferencia = $row_pto_r["IDReferencia"];

					$sql_ref = "SELECT IDReferencia,Numero FROM Referencia WHERE IDReferencia = '" . $IDReferencia . "' and Numero not like 'ZSE%' and  Numero not like 'Excedente' and Numero not like 'TARJETA' LIMIT 1";
					$qry_ref = db_query($sql_ref);
					$row_ref = db_fetch_array($qry_ref);
					$IDReferencia = $row_ref["IDReferencia"];

					if ((int)$IDReferencia <= 0) {
						$fecha_en_rango = 0;
						$notificacion = array();
					}
				}
			}
		}
	}

	if (($datos_cliente->Dia) != 0 && ($fecha_en_rango == 1)) {
		$porcentaje_descuento = (int)get_field("ParametroFidelizacion", "Valor", "IDParametroFidelizacion", "10");
		$notificacion[] = "<li>*" . $porcentaje_descuento . "% de descuento en  productos de  l&iacute;nea  por  mes  de  cumplea&ntilde;os.</li>";
		$descuento_por_cumpleanos = 1;
	}



	// cosnulto mensajes por plan de contactos	
	$sql_plan_contacto = "Select * From PlanContacto Where Publicar = 'S' and FechaInicio <= CURDATE() and FechaFin >= CURDATE()";
	$qry_plan_contacto = db_query($sql_plan_contacto);
	while ($row_plan_contacto = db_fetch_array($qry_plan_contacto)) {
		$aplica_plan = 0;
		$lineas_promocion = "";
		if (!empty($row_plan_contacto[ArchivoCliente])) {
			//verifico si aploica para el cliente actual
			$sql_plan_cedula = "Select * from PlanCedula Where IDCliente = '" . $datos_cliente->IDCliente . "'";
			$qry_plan_cedula = db_query($sql_plan_cedula);
			if (db_num_rows($qry_plan_cedula) > 0) {
				$aplica_plan = 1;
			}
		} else {
			$aplica_plan = 1;
		}

		if ($aplica_plan == 1) {
			if (!empty($row_plan_contacto[IDLinea])) {
				$array_linea_guardados = explode("|", $row_plan_contacto[IDLinea]);
				foreach ($array_linea_guardados as $valor_linea) {
					$lineas_promocion .= " Linea: " . get_field("Linea", "Nombre", "IDLinea", $valor_linea);
				}
			}

			if (!empty($row_plan_contacto[CompraMinima])) {
				$compra_minima = " Compra Minima de : $" . number_format($row_plan_contacto[CompraMinima], 0);
			}



			$notificacion[] = "<li>*" . $row_plan_contacto[Descuento] . "% de descuento " . $row_plan_contacto[Nombre] . $lineas_promocion . $compra_minima . "</li>";
		}
	}

	$valores_retorna["Mensaje"] = $notificacion;
	$valores_retorna["DescuentoSemanaCumple"] = $descuento_por_cumpleanos;


	return $valores_retorna;
}



function fid_redimir($idcliente, $idredimir)
{

	$sql_tabla_puntos = " SELECT * FROM ValorPuntos WHERE IDValorPuntos = '" . $idredimir . "' LIMIT 1 ";
	$qry_tabla_puntos = db_query($sql_tabla_puntos);
	$r_tabla_puntos = db_fetch_array($qry_tabla_puntos);

	$falta = $r_tabla_puntos["PuntosNecesarios"]; //La cantidad de puntos que faltan

	do {
		$sql_redimir = " SELECT * FROM PuntosCliente WHERE IDCliente = '" . $idcliente . "' AND Redimido = 'N' AND FechaVencimiento >= CURDATE()  ORDER BY FechaVencimiento ASC LIMIT 1 ";
		$qry_redimir = db_query($sql_redimir);
		$r_redimir = db_fetch_array($qry_redimir);

		if ($r_redimir["Puntos"] > $falta) {
			$puntosquedan = $r_redimir["Puntos"] - $falta;
			$sql_update = " UPDATE PuntosCliente SET Puntos = '" . $puntosquedan . "', FechaTrEd = NOW() WHERE IDCliente = '" . $r_redimir["IDCliente"] . "' AND IDPuntoVenta = '" . $r_redimir["IDPuntoVenta"] . "' AND IDFactura = '" . $r_redimir["IDFactura"] . "' ";
			$qry_update = db_query($sql_update);
			$falta = 0;
		} //end if
		else {
			$sql_update = " UPDATE PuntosCliente SET Redimido = 'S', FechaTrEd = NOW() WHERE IDCliente = '" . $r_redimir["IDCliente"] . "' AND IDPuntoVenta = '" . $r_redimir["IDPuntoVenta"] . "' AND IDFactura = '" . $r_redimir["IDFactura"] . "' ";
			$qry_update = db_query($sql_update);
			$falta = $falta - $r_redimir["Puntos"];
		} //end else
	} while ($falta > 0);
} //end function


function fid_redimir_bono($idcliente, $idredimir, $id_factura, $id_punto_venta, $idclienteredimio)
{
	$sql_bono_redimido = "Update BonoFidelizacion set  IDFactura = '" . $id_factura . "', IDPuntoVentaRedimido = '" . $id_punto_venta . "',Estado = 'R', IDClienteRedimioBono = '" . $idclienteredimio . "', FechaRedimido=NOW(), FechaTrEd = NOW(), UsuarioTrEd = '" . $id_punto_venta . "' Where IDBonoFidelizacion = '" . $idredimir . "'";
	$qry_actualiza_bono = db_query($sql_bono_redimido);
} //end function
