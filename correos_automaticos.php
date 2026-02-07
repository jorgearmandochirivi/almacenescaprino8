<?php
include("admin/config.inc.php");

//CORREOS AUTOMATICOS

include_once("admin/lib/class.phpmailer.php");
include_once("admin/lib/class.smtp.php");


/***************************************************************************************
CUMPLEAÑOS
/***************************************************************************************/
$dia_hoy=date("d");
$mes_hoy=date("m");
$year_hoy=date("Y");

if ((int)$dia_hoy==1){	
	//envio cumpleaños del mes	
	$sql_cumple="Select IDCliente, Nombre, Apellido, EMail, Ano, Mes, Dia from Cliente where Mes = '".(int)$mes_hoy."' and AutorizaMail = 'S' and AceptaHabeas = 'S'";	
	$qry_cumple=db_query($sql_cumple);
	while ($row_cumple=db_fetch_array($qry_cumple)){
		$nombre_cliente=$row_cumple[Nombre] . " " . $row_cumple[Apellido];
		$correo=$row_cumple[EMail];
		$mensaje=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",7);
		$mensaje=str_replace("[Nombre]",$nombre_cliente,$mensaje);
		$url_baja="http://www.calzadocaprino.com";
		$mail = new phpmailer();	
		$mail->Subject=get_field("EmailFidelizacion","Asunto","IDEmailFidelizacion",7);
		$mail->Body =$mensaje;
		$mail->IsHTML(true);
		$mail->Sender = get_field("EmailFidelizacion","Replyto","IDEmailFidelizacion",7);	
		$mail->Timeout=120;				
		$mail->Host = "ns.correosim.com";				
		$mail->Mailer = 'smtp';
		$mail->Password = 'soluciones';
		$mail->Username = 'postmater@correosim.com';
		$mail->SMTPAuth = true;
		$mail->From = get_field("EmailFidelizacion","EmailRemitente","IDEmailFidelizacion",7);
		$mail->FromName = get_field("EmailFidelizacion","NombreRemitente","IDEmailFidelizacion",7);
		$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
		$mail->AddAddress($correo);		
		$confirm=$mail->Send();		
		
		inserta_log_envio("7",$row_cumple[IDCliente]);
			
	}
}
/***************************************************************************************
FIN CUMPLEAÑOS
/***************************************************************************************/


/***************************************************************************************
BONOS QUE FALTAN UN MES PARA VENCER
/***************************************************************************************/
//bonos proximos a vencer
$sql_bonos_prox_vence = "SELECT * FROM BonoFidelizacion WHERE FechaVencimiento >= CURDATE() AND  Estado = 'D' AND FechaVencimiento = DATE_ADD( CURDATE( ) , INTERVAL 1 MONTH ) ";
$qry_bonos_prox_vence = db_query( $sql_bonos_prox_vence );
while($r_bonos_vence = db_fetch_array( $qry_bonos_prox_vence )){
	$id_bono=$r_bonos_vence[IDBonoFidelizacion];	
	$sql_cliente="Select IDCliente, Nombre, Apellido, EMail from Cliente where IDCliente = '".$r_bonos_vence[IDCliente]."' and AutorizaMail = 'S' and AceptaHabeas = 'S'";	
	$qry_cliente=db_query($sql_cliente);
	$msj_bonos="";
	$mensaje="";
	$cuerpo_bono="";
	while ($row_cliente=db_fetch_array($qry_cliente)){
		$nombre_cliente=$row_cliente[Nombre] . " " . $row_cliente[Apellido];
		$correo=$row_cliente[EMail];
		$mensaje=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",3);
		$mensaje=str_replace("[Nombre]",$nombre_cliente,$mensaje);
		//adjunto el bono
		if (!empty($id_bono)){			
				$fecha_vencimiento=get_field("BonoFidelizacion","FechaVencimiento","IDBonoFidelizacion",$id_bono);
				$valor_bono=get_field("BonoFidelizacion","Valor","IDBonoFidelizacion",$id_bono);						
				$cuerpo_bono=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",9);
				$cuerpo_bono=str_replace("[NumeroBono]",$id_bono,$cuerpo_bono);
				$cuerpo_bono=str_replace("[FechaVencimiento]",$fecha_vencimiento,$cuerpo_bono);
				$cuerpo_bono=str_replace("[ValorBono]",$valor_bono,$cuerpo_bono);
				$cuerpo_bono=str_replace("[NombreCliente]",$nombre_cliente,$cuerpo_bono);
				$cuerpo_bono=str_replace("[DocumentoCliente]",$cedula,$cuerpo_bono);
				$msj_bonos.=$cuerpo_bono;		
		}
		
		
		$mensaje=str_replace("[Bono]",$msj_bonos,$mensaje);		
		$url_baja="http://www.calzadocaprino.com";
		
		$mail = new phpmailer();	
		$mail->Subject=get_field("EmailFidelizacion","Asunto","IDEmailFidelizacion",3);
		$mail->Body =$mensaje;
		$mail->IsHTML(true);
		$mail->Sender = get_field("EmailFidelizacion","Replyto","IDEmailFidelizacion",3);	
		$mail->Timeout=120;				
		$mail->Host = "ns.correosim.com";				
		$mail->Mailer = 'smtp';
		$mail->Password = 'soluciones';
		$mail->Username = 'postmater@correosim.com';
		$mail->SMTPAuth = true;
		$mail->From = get_field("EmailFidelizacion","EmailRemitente","IDEmailFidelizacion",7);
		$mail->FromName = get_field("EmailFidelizacion","NombreRemitente","IDEmailFidelizacion",3);
		$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
		$mail->AddAddress($correo);		
		$confirm=$mail->Send();					
		inserta_log_envio("3",$row_cliente[IDCliente]);
	}
}



/***************************************************************************************
FIN BONOS QUE FALTAN UN MES PARA VENCER
/***************************************************************************************/




/***************************************************************************************
CLIENTE QUE LLEVE 10 MESES SIN COMPRAR
/***************************************************************************************/
	
if ((int)$dia_hoy==15){	
	$sql_cliente="Select IDCliente, Nombre, Apellido, EMail from Cliente where AutorizaMail = 'S' and AceptaHabeas = 'S'";
	$qry_cliente=db_query($sql_cliente);
	$mensaje="";
	while ($row_cliente=db_fetch_array($qry_cliente)){
		// verifico si lleva mas de 10 meses sin comprar
		$sql_ultima_compra="Select IDCliente, IDFactura, FechaFactura From Factura Where IDCliente = '".$row_cliente[IDCliente]."' Order By FechaFactura DESC limit 1";
		$qry_ultima_compra=db_query($sql_ultima_compra);
		$row_ultima_compra=db_fetch_array($qry_ultima_compra);
		$row_ultima_compra[IDFactura];
		$fecha_ultima_compra=substr($row_ultima_compra[FechaFactura],0,10);		
		
		$datetime1 = date_create($fecha_ultima_compra);
		$datetime2 = date_create('now');
		$interval = date_diff($datetime2, $datetime1);
		
		$meses = ( $interval->y * 12 ) + $interval->m;
		
		if ($meses>=10){ //leva mas de 10 meses sin comprar		
			$nombre_cliente=$row_cliente[Nombre] . " " . $row_cliente[Apellido];
			$correo=$row_cliente[EMail];
			$mensaje=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",4);
			$mensaje=str_replace("[Nombre]",$nombre_cliente,$mensaje);
			$url_baja="http://www.calzadocaprino.com";
			
			$mail = new phpmailer();	
			$mail->Subject=get_field("EmailFidelizacion","Asunto","IDEmailFidelizacion",4);
			$mail->Body =$mensaje;
			$mail->IsHTML(true);
			$mail->Sender = get_field("EmailFidelizacion","Replyto","IDEmailFidelizacion",4);	
			$mail->Timeout=120;				
			$mail->Host = "ns.correosim.com";				
			$mail->Mailer = 'smtp';
			$mail->Password = 'soluciones';
			$mail->Username = 'postmater@correosim.com';
			$mail->SMTPAuth = true;
			$mail->From = get_field("EmailFidelizacion","EmailRemitente","IDEmailFidelizacion",4);
			$mail->FromName = get_field("EmailFidelizacion","NombreRemitente","IDEmailFidelizacion",4);
			$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
			$mail->AddAddress($correo);		
			$confirm=$mail->Send();	
			inserta_log_envio("4",$row_cliente[IDCliente]);				
		}
 	}
}
/***************************************************************************************
FIN CLIENTE QUE LLEVE 10 MESES SIN COMPRAR
/***************************************************************************************/

/***************************************************************************************
UN AÑO DE AFILIACION
/***************************************************************************************/

	$ano_pasado=$year_hoy-1;
	$sql_cliente="Select IDCliente, Nombre, Apellido, EMail from Cliente where YEAR(FechaRegistroClubSuavidad) = '".$ano_pasado."' and MONTH(FechaRegistroClubSuavidad) = '". $mes_hoy."' and DAY(FechaRegistroClubSuavidad) = '".$dia_hoy."' and AutorizaMail = 'S' and AceptaHabeas = 'S'";	
	$qry_cliente=db_query($sql_cliente);
	$mensaje="";
	while ($row_cliente=db_fetch_array($qry_cliente)){
			$nombre_cliente=$row_cliente[Nombre] . " " . $row_cliente[Apellido];
			$correo=$row_cliente[EMail];
			$mensaje=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",5);
			$mensaje=str_replace("[Nombre]",$nombre_cliente,$mensaje);
			$url_baja="http://www.calzadocaprino.com";
			
			$mail = new phpmailer();	
			$mail->Subject=get_field("EmailFidelizacion","Asunto","IDEmailFidelizacion",5);
			$mail->Body =$mensaje;
			$mail->IsHTML(true);
			$mail->Sender = get_field("EmailFidelizacion","Replyto","IDEmailFidelizacion",5);	
			$mail->Timeout=120;				
			$mail->Host = "ns.correosim.com";				
			$mail->Mailer = 'smtp';
			$mail->Password = 'soluciones';
			$mail->Username = 'postmater@correosim.com';
			$mail->SMTPAuth = true;
			$mail->From = get_field("EmailFidelizacion","EmailRemitente","IDEmailFidelizacion",5);
			$mail->FromName = get_field("EmailFidelizacion","NombreRemitente","IDEmailFidelizacion",5);
			$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
			$mail->AddAddress($correo);		
			$confirm=$mail->Send();	
			inserta_log_envio("5",$row_cliente[IDCliente]);					
 	}
/***************************************************************************************
FIN UN AÑO DE AFILIACION
/***************************************************************************************/




?>