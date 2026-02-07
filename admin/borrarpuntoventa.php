<?php
exit;
include( "config.inc.php" );

$punto = 11;

$sql_punto = " SELECT IDPuntoVenta FROM PuntoVenta WHERE IDPuntoVenta = '$punto' ";
$qry_punto = db_query( $sql_punto );
if( db_num_rows( $qry_punto ) > 0 )
{
	$sql_punto_ref = "select * from PuntoVentaReferencia WHERE IDPuntoVenta='$punto' ";
	$qry_punto_ref = db_query( $sql_punto_ref );
	while( $r_punto_ref = db_fetch_object( $qry_punto_ref ) )
	{
		$sql_cod = "DELETE FROM CodificacionEspecifica  WHERE IDPuntoVentaReferencia = '$r_punto_ref->IDPuntoVentaReferencia'";
		$qry_cod = db_query( $sql_cod );
	}//end while
	
	$del_punto_ref = "DELETE FROM PuntoVentaReferencia  WHERE IDPuntoVenta = '$punto' ";
	db_query( $del_punto_ref );
	
}//end if

$borrar_punto = " DELETE FROM PuntoVenta WHERE IDPuntoVenta = '$punto'  ";
db_query( $borrar_punto );
	 
?>