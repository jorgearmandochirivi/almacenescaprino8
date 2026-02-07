<?php

	error_reporting(1);
	require("../config.inc.php");


$ruta_archivo="CargaReferencia123.txt";

$consecutivo_linea="394";
if($fp = fopen($ruta_archivo,"r")){
	$cont = 0;	
	while(!feof($fp)){
		unset($talla);
		ini_set('auto_detect_line_endings', true); 
		$linea = fgets($fp,4096);
		
		$fields = array_map('addslashes',array_map('trim', explode("\t",$linea)));
		
		$IDReferencia = trim($fields[0]);
		$IDProveedor = trim($fields[1]);
		$IDTipoTalla = trim($fields[2]);
		$Sexo = trim($fields[3]);
		$Saldo = trim($fields[4]);
		$TipoReferencia=trim($fields[5]);
		$Numero = trim($fields[6]);
		$Nombre = trim($fields[7]);
		$Descripcion = trim($fields[8]);
		$Publicar = trim($fields[9]);
		$Reportes = trim($fields[10]);
		$UsuarioTrCr="Carga plano";
		
		
		// Consulto el id del tipo referencia
		$id_tiporeferencia=get_field("TipoReferencia","IDTipoReferencia","Descripcion",$TipoReferencia);		
		$id_linea=get_field("Linea","IDLinea","Nombre",substr($Numero,0,2));
		if (empty($id_linea)){
			$consecutivo_linea++;
			echo "<br>Esta linea no existe " . substr($Numero,0,2) . " de  " . $IDReferencia;
			if ($Sexo=="M")
				$tipo_linea=1;
			elseif ($Sexo=="F")
				$tipo_linea=2;
				
			$sql_inserta_linea=db_query("INSERT INTO Linea (IDLinea, IDTipo, Nombre, Publicar, UsuarioTrCr, FechaTrCr)
								VALUES ('".$consecutivo_linea."','".$tipo_linea."','".substr($Numero,0,2)."','S','Archivo Plano',NOW()) ");
			$id_linea=$consecutivo_linea;					
		}
		
		
			
		$sql_inserta_referencia=db_query("INSERT INTO Referencia (IDReferencia,IDColor, IDCuero, IDPrecio, IDProveedor, IDTipoTalla, IDTipoReferencia, Sexo, Saldo, IDLinea, Numero, Nombre, Descripcion, Publicar, Reportes, UsuarioTrCr, FechaTrCr )
								 VALUES('".$IDReferencia."','19','46', 1, '".$IDProveedor."','".$IDTipoTalla."', '".$id_tiporeferencia."','".$Sexo."','".$Saldo."','".$id_linea."','".$Numero."','".$Nombre."','".$Descripcion."','".$Publicar."','".$Reportes."','".$UsuarioTrCr."',NOW())");
		
		$cont++;
	
	}
	fclose($fp);
	echo "Terminado Actualizados: " . $cont;
}
else
	echo "error open $filename";
	
	
//Cargo los puntos de venta de las referencias	
$sql_ref=db_query("Select * from Referencia Where IDReferencia >= 8442");
while ($row_referencia = db_fetch_array($sql_ref)){
	// consulto los puntos de venta
	$sql_punto=db_query("Select * from PuntoVenta Where 1");
	while ($row_punto=db_fetch_array($sql_punto)){
		// consulto que no este creado
		$sql_punto_ref=db_query("Select * from PuntoVentaReferencia Where IDReferencia = '".$row_referencia[IDReferencia]."' and IDPuntoVenta = '".$row_punto[IDPuntoVenta]."'");
		if (db_num_rows($sql_punto_ref)<=0){
			$idpuntoventareferencia = get_maxID("PuntoVentaReferencia","IDPuntoVentaReferencia");			
			$sql_insertapunto=db_query("INSERT INTO  PuntoVentaReferencia (IDPuntoVentaReferencia, IDReferencia, IDPuntoVenta) VALUES('".$idpuntoventareferencia."','".$row_referencia[IDReferencia]."','".$row_punto[IDPuntoVenta]."');");			
		}
		
	}
}

//Cargo los puntos de venta de las referencias	
$sql_ref=db_query("Select * from Referencia Where IDReferencia >= 8442");
while ($row_referencia = db_fetch_array($sql_ref)){
	
	$row_referencia[IDReferencia];
	insert_codEspecifica_plano($row_referencia[IDReferencia]);
	
}


// Carga la codificacion especifica

function insert_codEspecifica_plano($id)
{
	Global $Nombre_Usuario;
	
	$sql_puntosreferencia = "SELECT * FROM PuntoVentaReferencia WHERE IDReferencia = $id";
	$query_puntosreferencia = db_query($sql_puntosreferencia);
	
	$sql_tallas = "SELECT IDTalla FROM TipoTalla Tt, Talla T, Referencia R ";
	$sql_tallas .= "WHERE R.IDReferencia = '$id' AND Tt.IDTipoTalla = R.IDTipoTalla ";
	$sql_tallas .= "AND Tt.IDTipoTalla = T.IDTipoTalla";
	
	$query_tallas = db_query( $sql_tallas );
	
	if(db_num_rows($query_tallas) > 0)
	{
		
		$i = 0;
		while( $r_tallas[$i] = db_fetch_array( $query_tallas ) )
			$i++;		
	
		while($r_puntosreferencia = db_fetch_object( $query_puntosreferencia ))
		{
		
			$query_borra_codesp = db_query( "DELETE FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = $r_puntosreferencia->IDPuntoVentaReferencia" );
		
			foreach( $r_tallas as $talla )
			{
			
				if( !empty( $talla['IDTalla'] ) )
				{
					$qid = db_query("Select MAX(IDCodificacionEspecifica) AS maximo FROM CodificacionEspecifica");
						
					$result = db_fetch_object($qid);
				
					if (isset ($result->maximo))
						$idCodEsp = $result->maximo + 1;
					else
						$idCodEsp = 1;	
				
					$sql_insert = "INSERT INTO CodificacionEspecifica (IDCodificacionEspecifica, IDPuntoVentaReferencia, IDTalla, Publicar, UsuarioTrCr, FechaTrCr, Maximo) ";
					$sql_insert .= "VALUES ('$idCodEsp', '$r_puntosreferencia->IDPuntoVentaReferencia', '$talla[IDTalla]', 'S', '$Nombre_Usuario', now(),'10')";
					


					db_query($sql_insert);
					
				}//end if( !empty( $talla['IDTalla'] ) )
			
			}//end foreach( $r_tallas as $talla )

			//GENERAR PEDIDO AUTOMATICO EN LA REFERENCIA CUANDO SE CREA Modificado
			//Por John Escobar el 16 de Abril
			$array_puntos['IDPuntoVenta'] = $r_puntosreferencia->IDPuntoVenta;
			$array_puntos["IDReferencia"] = $r_puntosreferencia->IDReferencia;
			//generarpedidoreferencia( $array_puntos );
			
		
		}//end while($r_puntosreferencia = db_fetch_object( $query_puntosreferencia ))
		
		window_alert("Codificacion Especifica Creada Correctamente ");
	
	}//end if(db_num_rows($query_tallas))
	else
		window_alert("No Hay Tallas");	

}//end function insert_codEspecifica($id)

	


?>