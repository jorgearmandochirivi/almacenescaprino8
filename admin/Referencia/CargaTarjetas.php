<?php
	error_reporting(1);
	require("../config.inc.php");


$ruta_archivo="CargaInventarioTarjeta.txt";


if($fp = fopen($ruta_archivo,"r")){
	$cont = 0;	
	$punto_venta_ant=0;
	while(!feof($fp)){
		unset($talla);
		ini_set('auto_detect_line_endings', true); 
		$linea = fgets($fp,4096);
		
		$fields = array_map('addslashes',array_map('trim', explode("\t",$linea)));
		
		$punto_venta = trim($fields[0]);
		$codigo_tarjeta = trim($fields[1]);
		
		if (!empty($punto_venta) && !empty($codigo_tarjeta)){
		
				//si cambia el codigo del almacen actualizo las tarjetas a vendidas para dejar disponibles las del archivo
				if ($punto_venta!=$punto_venta_ant){			
					$sql_actualiza_tarjetas="Update  TarjetaPunto Set Estado= 'V' where IDPuntoVenta = '".$punto_venta."'";
					db_query($sql_actualiza_tarjetas);
					$punto_venta_ant=$punto_venta;
				}
				
				//Verifico si existe la tarjeta
				$sql_tarjeta="Select * From TarjetaPunto Where CodigoTarjeta like '%".$codigo_tarjeta."%' and IDPuntoVenta = '".$punto_venta."'";
				$qry_tarjeta=db_query($sql_tarjeta);
				if (db_num_rows($qry_tarjeta)<=0){
					echo "<br>Esta tarjeta no existe: " . $codigo_tarjeta . " Punto Venta: " . $punto_venta;
					// La creo
					/*
					$sql_inserta="Insert Into TarjetaPunto (CodigoTarjeta, IDPuntoVenta, Estado, UsuarioTrCr, FechaTrCr)
								  Values ('".$codigo_tarjeta."','".$punto_venta."','D','Carga',NOW())";	
					$qry_inserta=db_query($sql_inserta);			
					*/
				}
				
				
							
				
				//Actualizo estado tarjeta
				$sql_borra_tarjeta="Update  TarjetaPunto Set Estado= 'D' where CodigoTarjeta like '%".$codigo_tarjeta."%' and IDPuntoVenta = '".$punto_venta."'";
				$qry_borra_tarjeta=db_query($sql_borra_tarjeta);
				$cont++;
		}
	}
	
	fclose($fp);
	echo "Terminado Actualizados: " . $cont;
	
	
	//Actualizo los inventarios
	
	
	
	
	
}
else
	echo "error open $filename";
?>