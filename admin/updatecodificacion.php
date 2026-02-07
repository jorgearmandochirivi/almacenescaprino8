<?php

//error_reporting(E_ALL);
include("config.inc.php");
include("simple_html_dom.php");


$sql_detalle = "SELECT IDCodificacionEspecifica, Cantidad FROM DetalleFactura WHERE FechaTrCr = '2014-11-09' ";
$qry_detalle = db_query( $sql_detalle );
while( $r_detalle = db_fetch_array( $qry_detalle ) )
{
	$cantidad = $r_detalle["Cantidad"];
	$codificacion = $r_detalle["IDCodificacionEspecifica"];
	
	$existencias = get_field( "CodificacionEspecifica","Existencias","IDCodificacionEspecifica", $codificacion );
	
	$existencias = $existencias - $cantidad;
	$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '$codificacion'";
	//echo $str_actualiza_inventario .= "<br>";
	db_query( $str_actualiza_inventario );
}//end fhilw

	
			
?>