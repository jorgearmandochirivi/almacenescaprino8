<?php
	error_reporting(1);
	require("../config.inc.php");

$IDPuntoVentaNuevo = 34;
exit;

/*
$sql_ref = "Select * from PuntoVentaReferencia Where IDPuntoVenta = '17'";
$result_ref = db_query($sql_ref);
while($row_ref = db_fetch_array($result_ref)){
	//$row_ref["IDReferencia"];
	//Verifico si existe en pto vta ref
		$id_pto_vta_ref=get_maxID("PuntoVentaReferencia","IDPuntoVentaReferencia");
		$sql_pto_ref = "Insert into PuntoVentaReferencia (IDPuntoVentaReferencia, IDReferencia, IDPuntoVenta) 
						Values ('".$id_pto_vta_ref."','".$row_ref["IDReferencia"]."','".$IDPuntoVentaNuevo."')";
		$result_pto_ref = db_query($sql_pto_ref);	
}
*/

/*
$sql_ref = "Select * from Referencia Where Publicar = 'S'";
$result_ref = db_query($sql_ref);
while($row_ref = db_fetch_array($result_ref)){
	//$row_ref["IDReferencia"];
	//Verifico si existe en pto vta ref
		$id_pto_vta_ref=get_maxID("PuntoVentaReferencia","IDPuntoVentaReferencia");
		$sql_pto_ref = "Insert into PuntoVentaReferencia (IDPuntoVentaReferencia, IDReferencia, IDPuntoVenta) 
						Values ('".$id_pto_vta_ref."','".$row_ref["IDReferencia"]."','".$IDPuntoVentaNuevo."')";
		$result_pto_ref = db_query($sql_pto_ref);			
}
*/




//Crear la codf Especifica
$sql_inv=" SELECT CE.*, PR.IDReferencia
	   FROM CodificacionEspecifica CE, PuntoVentaReferencia PR 
	   WHERE PR.IDPuntoVenta = '17' AND 
	   PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia
	   ";
$query_inv = db_query( $sql_inv );
while($row_inv = db_fetch_array($query_inv)):
	$sql_pto_ref = "Select * From PuntoVentaReferencia Where IDReferencia = '".$row_inv["IDReferencia"]."' and IDPuntoVenta = '".$IDPuntoVentaNuevo."'";
	$result_pto_ref = db_query($sql_pto_ref);
	if(db_num_rows($result_pto_ref)>0):
		$row_pto_vta_ref = db_fetch_array($result_pto_ref);
		$id_codificacion_especifica=get_maxID("CodificacionEspecifica","IDCodificacionEspecifica");
		$inserta_codif_espec="INSERT INTO CodificacionEspecifica (IDCodificacionEspecifica, IDPuntoVentaReferencia, IDTalla, Existencias, Maximo, Minimo, Publicar)
									VALUES ('".$id_codificacion_especifica."','".$row_pto_vta_ref["IDPuntoVentaReferencia"]."','".$row_inv["IDTalla"]."','0','10',0,'S')";
		
		db_query($inserta_codif_espec);		
	else:
		echo "<br>No existe " . $sql_pto_ref;
	endif;
	
endwhile;



/*
$tallas = array("1","2","3","4","5","7","8","9","11","12","16","24","25","26","27");

//Crear la codf Especifica

*/


//Borarr almacen
/*
$sql_inv=" SELECT *
	   FROM  PuntoVentaReferencia PR 
	   WHERE PR.IDPuntoVenta = '".$IDPuntoVentaNuevo."'";
$query_inv = db_query( $sql_inv );
while($row_inv = db_fetch_array($query_inv)):				
			echo $inserta_codif_espec="DELETE FROM  CodificacionEspecifica Where IDPuntoVentaReferencia = '".$row_inv["IDPuntoVentaReferencia"]."'";
			db_query($inserta_codif_espec);				
endwhile;
*/

echo "Fin";
exit;
?>