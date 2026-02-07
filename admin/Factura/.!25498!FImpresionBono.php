<?php
	include("../admin/config.inc.php");
	Encabezado();
	$datos = Verifica_SesionCliente();
	//print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];
	include("admin/jscripts/tabs.php");
		
	$TitleMod ="Factura";
	
	$Table = "FacturaBono";
	$TableJoin = "FacturaBono";
	$Key = "IDFacturaBono";
	
	$qid = db_query(" SELECT * FROM FacturaBono WHERE IDFacturaBono = '$id' AND IDPuntoVenta = '$idpunto'");
		
	$r = db_fetch_object($qid);

	 $sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
	$qry_puntoventa = db_query( $sql_puntoVenta );
	$r_puntoventa = db_fetch_object( $qry_puntoventa );

	$filedir = $dirroot . "/files/facturas/";
	
	$name = "FBonos" . $r_puntoventa->Codigo.$r->IDFacturaBono . ".html";
	$namePDF = "FBonos" . $r_puntoventa->Codigo.$r->IDFacturaBono . ".pdf";
	$file = "$filedir$name";
	$filepdf = "$filedir$namePDF";

	ob_start();
?>
<html>
<head>
<title>Imprimir Recibo</title>
</head>

<style>
<!--
body{
	font-size:6.5px;
	margin:0;
}
table{
	font-size:6.5px;
}
@page { size 6cm 12cm; 
	margin-left: 0;
	}

@media print{
*{
	margin:0;
	padding:0;
}
body{
	font-size:7px;
	margin:0;
	padding:0;
}

.texto {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 6.5px;
	color: #000000;
}
.mensajefooter{
	font-size:6px;
}


.bordertable {border: dotted 1px; color:#c3c3c3}
#content { margin-left:0; 
     float:none; 
     width:auto;
     height : 300px; 
     color:black;
	 }
table{
	font-size:6.5px;
	margin:0;
}


-->
}
</style>

<script>
<!--
function printWindow() {
  if (window.print)
    window.print();
  else
    alert("Lo siento, pero a tu navegador no se le puede ordenar imprimir" +
