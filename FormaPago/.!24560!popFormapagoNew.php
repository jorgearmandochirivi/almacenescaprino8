<?php
	include("../admin/config.inc.php");
	Encabezado();
	$datos = Verifica_SesionCliente();
	$IDPuntoVenta = $datos['IDPuntoVenta'];
  ?>
<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
		<meta name="generator" content="Adobe GoLive 6">
		<title>Caprino :: Forma de Pago</title>
		<link rel="stylesheet" href="../styles.css?1" type="text/css">
	</head>

	<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
<?php

$TitleMod ="Forma de Pago Factura";

$Table = "PuntoVentaBanco";
$TableJoin = "";
$Key = "IDPuntoVentaBanco";


		switch (nvl($action)) {
			case "insert" :
				if(insertarformapago($_POST))
					echo "<script>window.open( '../Factura/FImpresion.php?id=".$id."&idpunto=".$idpunto."','','width=426, height=350' );opener.location.reload(true);window.close();</script>";
			break;
			default : 
				print_form("insert","Realizar Pago");
			break;
		
		} // End switch

/*******************************************************************************************
	insertarformapago: Borra un Item ( Referencia del Pedido ).
	Parametros:
			$frm : array con las formas de pago de la factura
	Retorna:	
			void
*******************************************************************************************/
function insertarformapago($frm)
{
	GLOBAL $Table, $Key, $ID_Usuario, $idpunto,$datos, $numerocuotas, $diascuota; 
	

	
	foreach( $frm['IDPuntoVentaBanco'] as $FormaPago )
	{
		
		$FormaPago; 
		$Valor = $Valor + $frm[Valor][$FormaPago];
	
	}//end foreach( $frm['IDFormaPago'] as $FormaPago )
	
