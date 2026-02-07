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
		<title>Caprino :: Entradas</title>
		<link rel="stylesheet" href="../styles.css?1" type="text/css">
        <script>
		function imprimir(){
		  var objeto=document.getElementById('areaimprimir');  //obtenemos el objeto a imprimir
