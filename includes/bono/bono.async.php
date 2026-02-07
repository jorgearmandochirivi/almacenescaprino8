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

	$sql_bono = " SELECT * From BonoZarate Where Codigo = '" . $_POST["Bono"] . "' LIMIT 1 ";
	$qry_bono = db_query( $sql_bono );
	$r_bono = db_fetch_array( $qry_bono );

		$columns = array(
			"\"IDBonoZarate\":\"" . $r_bono["IDBonoZarate"] . "\"",
			"\"Codigo\":\"" . $r_bono["Codigo"] . "\"",
			"\"Disponible\":\"" . $r_bono["Disponible"] . "\""
		);

$str = "{\"column\":{";
$str .= implode( "," , $columns );
$str .= "}}";
echo $str;?>
