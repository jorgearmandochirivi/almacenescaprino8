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

	 if (strlen($_POST["Referencia"])>8):
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
	$sql_referencia = " Select * From Referencia Where Numero = '" . $referencia . "' or NumeroAnterior = '" . $referencia . "'  AND Publicar = 'S' LIMIT 1 ";
	$qry_referencia = db_query( $sql_referencia );
	$r_referencia = db_fetch_array( $qry_referencia );


	 $sql_existencias = " SELECT C.* FROM PuntoVentaReferencia PVR, CodificacionEspecifica C, Referencia R, Talla
							WHERE PVR.IDReferencia = '" . $r_referencia["IDReferencia"] . "' AND PVR.IDPuntoVenta = '" . $IDPuntoVenta . "'
							AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
							AND C.IDTalla = Talla.IDTalla
							AND Talla.Descripcion = '" . $talla . "'
							GROUP BY C.IDCodificacionEspecifica ";

	$qry_existencias = db_query( $sql_existencias );
	$r_existencias = db_fetch_array( $qry_existencias );



	if( $r_existencias["Existencias"] >= 1 )
	{

		/**********Consulta de Valor de la Referencia***********/

		$Precio = get_field("Precio","ValorVenta","IDPrecio",$r_referencia["IDPrecio"]);
		$Descuento = get_field("Precio","Descuento","IDPrecio",$r_referencia["IDPrecio"]);

		if( ( ($Descuento <> "") && ($Descuento <> 0) ) )
			$ValorUnitario = $Precio - ( $Precio * ( $Descuento/100 ) );
		else
			$ValorUnitario = $Precio;

		if($ref_nueva == 1):
			$nom_referencia = $referencia;
		else:
			$nom_referencia = $r_referencia["Nombre"];
		endif;


		$columns = array(
			"\"referencia\":\"" . $referencia . "\"",
			"\"nombre\":\"" . $nom_referencia . "\"",
			"\"talla\":\"" . $talla . "\"",
			"\"codificacion\":\"" . $r_existencias["IDCodificacionEspecifica"] . "\"",
			"\"existencias\":\"". $r_existencias["Existencias"]. "\"",
			"\"valor\":\"" . $ValorUnitario . "\"",
			"\"sexo\":\"" . $r_referencia["Sexo"] . "\"",
			"\"tipotalla\":\"" . $r_referencia["IDTipoTalla"] . "\"",
			"\"tiporeferencia\":\"" . $r_referencia["IDTipoReferencia"] . "\"",
			"\"descuento\":\"" . $Descuento. "\""
		);


	}//end if

$str = "{\"column\":{";



$str .= implode( "," , $columns );

$str .= "}}";

echo $str;?>
