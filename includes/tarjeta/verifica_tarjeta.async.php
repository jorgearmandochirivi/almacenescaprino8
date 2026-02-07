<?
	include("../../admin/config.inc.php");
	
	header( "Content-type: text/json" );
	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );             
	header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" ); 
	header( "Cache-Control: no-cache, must-revalidate" );           
	header( "Pragma: no-cache" );
	
	$columns = array();
	
	$datos = Verifica_SesionCliente();
	$ID_Usuario = $datos["IDUsuario"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];	
	$codigo_tarjeta = $_POST["Codigo"];
	$documento = $_POST["Documento"];
	$id_cliente= $_POST["IDCliente"];
	
	
	$sql_tarjeta = " Select * From TarjetaFidelizacion Where Codigo = '" . $codigo_tarjeta . "' AND (Estado = 'A' or Estado = 'E') LIMIT 1 ";
	$qry_tarjeta = db_query( $sql_tarjeta );
	//$r_tarjeta = db_fetch_array( $qry_tarjeta );
	if (db_num_rows($qry_tarjeta)<=0){
		echo json_encode("no_existe");	
	}
	else{
		//$sql_tarjeta_cliente = " Select NumeroTarjeta From Cliente Where NumeroTarjeta = '" . $codigo_tarjeta . "' and Cedula <> '".$documento."' ";
		if (!empty($id_cliente)){
			//verifico que este id tenga alguna tarjeta para validar
			$id_tarjeta=get_field("TarjetaFidelizacion","IDTarjetaFidelizacion","IDCliente",$id_cliente);
			if(empty($id_tarjeta))
				$condicion=" and Estado = 'A'";	
			else	
				$condicion=" and IDCliente = '".$id_cliente."'";
		}
		else{
			$condicion=" and Estado = 'A'";	
		}
			
		$sql_tarjeta_cliente = " Select * From TarjetaFidelizacion Where Codigo = '" . $codigo_tarjeta . "'  $condicion";		
		$qry_tarjeta_cliente = db_query( $sql_tarjeta_cliente );
		//$r_tarjeta = db_fetch_array( $qry_tarjeta );
		if (db_num_rows($qry_tarjeta_cliente)<=0){
			//echo "asignado";	
			echo json_encode("asignado");	
		}
		else{
			//echo "ok";	
			echo json_encode("ok");
		}
	}
?>