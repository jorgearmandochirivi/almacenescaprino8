<?php

	include("../config.inc.php");
	Encabezado();
	$datos = Verifica_Sesion();

db_query("SET AUTOCOMMIT=0");
db_query("BEGIN");

//Seleccionar Referencias 

$sql_ref = " SELECT IDReferencia, Numero FROM Referencia ";
$qry_ref = db_query( $sql_ref );
while( $r_ref = db_fetch_object( $qry_ref ) )
{

	$array_punto = array( );
	//Seleccionar puntoVentaReferencia y mirar si esta repetida
	$sql_punto = " SELECT * from PuntoVentaReferencia WHERE IDReferencia = '$r_ref->IDReferencia' ";
	$qry_punto = db_query( $sql_punto );
	while( $r_punto = db_fetch_array( $qry_punto ) )
	{
		
		$array_punto[$r_punto[IDPuntoVenta]] = $array_punto[$r_punto[IDPuntoVenta]]+1;
		
	}//end while
	
	foreach( $array_punto as $idpunto => $valor )
	{
	
		if( $valor > 1 )
		{
			
			echo "IDReferencia: ".$r_ref->IDReferencia." - NumeroReferencia: ".$r_ref->Numero." IDPuntoVenta: ".$idpunto."  Cantidad Repetidos".$valor."<br>";
			
			$sql_punto = " SELECT * FROM PuntoVentaReferencia WHERE IDReferencia = '$r_ref->IDReferencia' AND IDPuntoVenta = '$idpunto' ";
			$qry_punto = db_query( $sql_punto );
			$borrar = 0;
			while( $r_punto = db_fetch_array( $qry_punto ) )
			{
				
				echo $sql_codificacion = "SELECT SUM(Existencias) as existencias FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_punto[IDPuntoVentaReferencia]' ";
				echo "<br>";
				$qry_codificacion = db_query( $sql_codificacion );
				$r_codificacion = db_fetch_object( $qry_codificacion );
				
				if( $r_codificacion->existencias == 0 && $borrar == 0 )
				{
					
					echo $sql_borrar  = " DELETE FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_punto[IDPuntoVentaReferencia]' ";
					echo "<br>";
					$borrar = 1;
					db_query( $sql_borrar );
					
					
					echo $sql_borrar  = " DELETE FROM PuntoVentaReferencia WHERE IDPuntoVentaReferencia = '$r_punto[IDPuntoVentaReferencia]' AND IDPuntoVenta = '$r_punto[IDPuntoVenta]'  ";
					echo "<br>";
					db_query( $sql_borrar );
					
				}//end if
				
			}//end while
			
		}//end if
	
	}//end while
	
	

}//end while


	//db_query( "tales" );
	db_query("COMMIT");
	


?>

