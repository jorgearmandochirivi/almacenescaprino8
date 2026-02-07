<?	
	include("../../admin/config.inc.php");	
	header( "Content-type: text/json" );
	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );             
	header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" ); 
	header( "Cache-Control: no-cache, must-revalidate" );           
	header( "Pragma: no-cache" );
	
	$columns = array();
	
	$datos = Verifica_SesionCliente();
//	print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];
	
	 if (strlen($_POST["Referencia"])>=8):
		$referencia = substr( $_POST["Referencia"], 0, 8 );
	    $talla = substr( $_POST["Referencia"], 8, 2 );
		$ref_nueva = 1;
	else:	
		$referencia = substr( $_POST["Referencia"], 0, 6 );
		$talla = substr( $_POST["Referencia"], 6, 2 );
		$ref_nueva = 0;
	endif;
	 
	 
	 //$referencia = substr( $_POST["Referencia"], 0, 6 );
	 //$talla = substr( $_POST["Referencia"], 6, 2 );
	
	//$sql_referencia = " Select * From Referencia Where Numero = '" . $referencia . "' AND Publicar = 'S' LIMIT 1 ";
	$sql_referencia = " Select IDTipoReferencia From Referencia Where Numero = '" . $referencia . "' or NumeroAnterior = '" . $referencia . "'  AND Publicar = 'S' LIMIT 1 ";
	$qry_referencia = db_query( $sql_referencia );
	$r_referencia = db_fetch_array( $qry_referencia );
	echo $r_referencia["IDTipoReferencia"];?>