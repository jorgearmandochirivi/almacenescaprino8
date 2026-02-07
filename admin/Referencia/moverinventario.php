<?php
	error_reporting(1);
	require("../config.inc.php");


$ruta_archivo="CargaPlanoSalitreW.txt";


if($fp = fopen($ruta_archivo,"r")){
	$cont = 0;
	$contador_fila = 1;	
	while(!feof($fp)){
		unset($talla);
		ini_set('auto_detect_line_endings', true); 
		$linea = fgets($fp,4096);		
		$fields = array_map('addslashes',array_map('trim', explode("\t",$linea)));
		
		$id_alamacen = "17";
		$referencia = trim($fields[0]);
		$talla[1] = trim($fields[2]);
		$talla[32] = trim($fields[3]);
		$talla[34] = trim($fields[4]);
		$talla[35] = trim($fields[5]);
		$talla[36] = trim($fields[6]);
		$talla[37] = trim($fields[7]);
		$talla[38] = trim($fields[8]);
		$talla[39] = trim($fields[9]);
		$talla[40] = trim($fields[10]);
		$talla[41] = trim($fields[11]);
		$talla[42] = trim($fields[12]);
		$talla[43] = trim($fields[13]);
		$talla[44] = trim($fields[14]);
		$talla["L"] = trim($fields[15]);
		$talla["M"] = trim($fields[16]);
		$talla["S"] = trim($fields[17]);
		$talla["XL"] = trim($fields[18]);
		
		
		// Consulto el id de la referencia
		$id_referencia=get_field("Referencia","IDReferencia","Numero",$referencia);
		
		if((int)$id_referencia>0):
			
					unset($array_codigos_esp);
					
				
					// verifico que la talla exista cuando haya un valor		
					foreach( $talla as $idtalla => $valortalla){				
						unset($tallas_posibles);
						if (!empty($valortalla) && $valortalla <> "0" ){
							// consulto el id de la talla posibles por que la misma talla esta repetida en la tabla ¿?
							$sql_tallas_posibles=db_query("Select * from Talla Where Descripcion = '".$idtalla."'");
							while ($row_talla_posibles = db_fetch_array($sql_tallas_posibles)){
								$tallas_posibles[]=$row_talla_posibles[IDTalla];
							}
							
							if (count($tallas_posibles)>0){
								$id_tallas_posibles=implode(",",$tallas_posibles);	
							}
							
							
							$id_talla_tabla=get_field("Talla","IDTalla","Descripcion",$idtalla);					
							
							 $sql_verf_talla="SELECT * 
									   FROM CodificacionEspecifica CE, PuntoVentaReferencia PR 
									   WHERE PR.IDPuntoVenta = '".$id_alamacen."' AND PR.IDReferencia = '".$id_referencia."' AND 
									   IDTalla in (".$id_tallas_posibles.") AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia";
							$query_verif = db_query( $sql_verf_talla );
							if (db_num_rows($query_verif)<=0){						
								echo "<br>OJO NO EXISTE ";
								
							}
						}
					}
					
				
					 $sql="SELECT * 
							   FROM CodificacionEspecifica CE, PuntoVentaReferencia PR 
							   WHERE PR.IDPuntoVenta = '".$id_alamacen."' AND PR.IDReferencia = '".$id_referencia."' AND 
							   PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia
							   GROUP BY IDTalla
							   ";
					$query_inv = db_query( $sql );
					
					
						
					
					while ($row_inv=db_fetch_array($query_inv)){
						// actualizo a cero 
						
						
						$numero_talla=get_field("Talla","Nombre","IDTalla",$row_inv[IDTalla]);
						$valor = $talla[$numero_talla];					
						$total_talla[$numero_talla]+=$valor;
						$gran_total+=$valor;						
						//$sql_actualiza = db_query("UPDATE CodificacionEspecifica SET Existencias = '".$valor."' WHERE IDCodificacionEspecifica = '".$row_inv[IDCodificacionEspecifica]."'");	
						//Dejo en 0 del almacenes destino						
						$sql_actualiza = db_query("UPDATE CodificacionEspecifica SET Existencias = '0' WHERE IDCodificacionEspecifica = '".$row_inv[IDCodificacionEspecifica]."'");	
						$cont++;
					}
					
					
					
			else:
				echo "<br>REFERENCIA NO EXISTE " . 	$referencia;	
					
			endif;		
		
	
	}
	
	
	fclose($fp);
	echo "Terminado Actualizados: " . $cont;
}
else
	echo "error open $filename";


?>