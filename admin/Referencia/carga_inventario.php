<?php
	error_reporting(1);
	require("../config.inc.php");


$ruta_archivo="CaprinoInventarioNov11_2014.txt";


if($fp = fopen($ruta_archivo,"r")){
	$cont = 0;	
	while(!feof($fp)){
		unset($talla);
		ini_set('auto_detect_line_endings', true); 
		$linea = fgets($fp,4096);
		
		$fields = array_map('addslashes',array_map('trim', explode("\t",$linea)));
		
		$id_alamacen = trim($fields[0]);
		$referencia = trim($fields[1]);
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
		$talla["ZJ0"] = trim($fields[19]);
		
		
		
		// Consulto el id de la referencia
		$id_referencia=get_field("Referencia","IDReferencia","Numero",$referencia);
		
		if (empty($id_referencia)){
			//creo la referencia
			//crear_referencia($fields);
		}
		
		
			
			unset($array_codigos_esp);
			
			
			if ($id_alamacen==31){
			
			if ($id_alamacen!=$id_alamacen_ant){
				
				
				$sql_idcod_especifica=db_query("SELECT IDCodificacionEspecifica 
				FROM CodificacionEspecifica CE, PuntoVentaReferencia PR
				WHERE PR.IDPuntoVenta =  '".$id_alamacen."'
				AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia");
				while ($row_cod_especifica=db_fetch_array($sql_idcod_especifica)){
					$array_codigos_esp[]=$row_cod_especifica["IDCodificacionEspecifica"];
				}
				
				if (count($array_codigos_esp)>0){
					$id_codigos_esp=implode(",",$array_codigos_esp);	
					//Pongo inventario en cero para actualizar deacuerdo al archivo
					$sql_inicializa="UPDATE CodificacionEspecifica SET Existencias = 0 Where IDCodificacionEspecifica in (".$id_codigos_esp.")";			
					db_query($sql_inicializa);				
				}
				
				
			}
			
			$id_alamacen_ant=$id_alamacen;
		
		
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
						$sql_punto_ref=db_query("Select * From PuntoVentaReferencia Where IDReferencia = '".$id_referencia."' and IDPuntoVenta = '".$id_alamacen."'");
						$row_punto_ref=db_fetch_array($sql_punto_ref);
						$IDPuntoVentaReferencia=$row_punto_ref[IDPuntoVentaReferencia];
						
						//echo "<br>Esta talla ". $idtalla ." se debe crear en la referencia: " . $referencia . $sql_verf_talla;
						// se debe crear la talla
						$id_codificacion_especifica=get_maxID("CodificacionEspecifica","IDCodificacionEspecifica");
						$inserta_codif_espec=db_query("INSERT INTO CodificacionEspecifica (IDCodificacionEspecifica, IDPuntoVentaReferencia, IDTalla, Existencias, Maximo, Minimo, Publicar)
											  VALUES ('".$id_codificacion_especifica."','".$IDPuntoVentaReferencia."','".$tallas_posibles[0]."','".$valortalla."','10',0,'S')");
						
						
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
			
			
				// verifico que la talla exista en CodificacionEspecifica
				if (db_num_rows($query_inv)<=0){
					echo "<br>Esta no existe " . $id_referencia;
					//exit;
				}
			
			
			while ($row_inv=db_fetch_array($query_inv)){
				// actualizo a cero 
				
				
				$numero_talla=get_field("Talla","Nombre","IDTalla",$row_inv[IDTalla]);
				$valor = $talla[$numero_talla];					
				$total_talla[$numero_talla]+=$valor;
				$gran_total+=$valor;
		
				if ($referencia=="3B95XN"){
					echo "<br>" . $sql	;
					//exit;
				}
				
				if ($numero_talla=="1"){
					//echo "<br>Ref $referencia " . $valor;
				}
				
				
				$sql_actualiza = db_query("UPDATE CodificacionEspecifica SET Existencias = '".$valor."' WHERE IDCodificacionEspecifica = '".$row_inv[IDCodificacionEspecifica]."'");
				
				$cont++;
			}
			
		
			}
		
		
	
	}
	print_r($total_talla);
	
	echo "<br>TOTAL " . $valor_total;
	echo "<br><br>GRAN TOTAL " . $gran_total;
	
	fclose($fp);
	echo "Terminado Actualizados: " . $cont;
}
else
	echo "error open $filename";





function crear_referencia($fields){
		$IDReferencia = get_maxID("Referencia","IDReferencia");
		$IDProveedor = trim($fields[21]);
		//Tipo Talla
		$IDTipoTalla = trim($fields[22]);
		if ($IDTipoTalla=="Hombre")
			$id_tipo_talla="1";
		elseif ($IDTipoTalla=="Mujer")	
			$id_tipo_talla="2";
		elseif ($IDTipoTalla=="Unica")	
			$id_tipo_talla="3";

			
		
		$Sexo = trim($fields[24]);
		$Saldo = trim($fields[25]);
		$TipoReferencia=trim($fields[23]);
		$Numero = trim($fields[1]);
		$Nombre = trim($fields[1]);
		$Descripcion = trim($fields[1]);
		$Publicar = "S";
		$Reportes = "S";
		$UsuarioTrCr="Carga plano";
		
		
		// Consulto el id del tipo referencia
		$id_tiporeferencia=get_field("TipoReferencia","IDTipoReferencia","Descripcion",$TipoReferencia);		
		$id_proveedor=get_field("Proveedor","IDProveedor","Nombre",$IDProveedor);		
		
		if (empty($id_proveedor)){
			if($IDProveedor=="#N/A")
				$id_proveedor=19;
			else	
				echo "<br>Este proveedor no existe " . $IDProveedor;
			
		}
		
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
		
		
		if (!empty($Numero)){
			echo "<br>" . "INSERT INTO Referencia (IDReferencia,IDColor, IDCuero, IDPrecio, IDProveedor, IDTipoTalla, IDTipoReferencia, Sexo, Saldo, IDLinea, Numero, Nombre, Descripcion, Publicar, Reportes, UsuarioTrCr, FechaTrCr )
									 VALUES('".$IDReferencia."','19','46', 1, '".$id_proveedor."','".$id_tipo_talla."', '".$id_tiporeferencia."','".$Sexo."','".$Saldo."','".$id_linea."','".$Numero."','".$Nombre."','".$Descripcion."','".$Publicar."','".$Reportes."','".$UsuarioTrCr."',NOW())";		
			/*	
			$sql_inserta_referencia=db_query("INSERT INTO Referencia (IDReferencia,IDColor, IDCuero, IDPrecio, IDProveedor, IDTipoTalla, IDTipoReferencia, Sexo, Saldo, IDLinea, Numero, Nombre, Descripcion, Publicar, Reportes, UsuarioTrCr, FechaTrCr )
									 VALUES('".$IDReferencia."','19','46', 1, '".$id_proveedor."','".$id_tipo_talla."', '".$id_tiporeferencia."','".$Sexo."','".$Saldo."','".$id_linea."','".$Numero."','".$Nombre."','".$Descripcion."','".$Publicar."','".$Reportes."','".$UsuarioTrCr."',NOW())");
			*/
			$cont++;
		}
	
	

//Cargo los puntos de venta de las referencias - Procesamiento por lotes
$batch_size = 100;
$offset = 0;
do {
	$sql_ref=db_query("Select * from Referencia Where IDReferencia >= 8506 LIMIT $batch_size OFFSET $offset");
	$num_rows = db_num_rows($sql_ref);
	
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
	$offset += $batch_size;
} while ($num_rows == $batch_size);


//Cargo los maximos - Procesamiento por lotes
$batch_size = 100;
$offset = 0;
do {
	$sql_ref=db_query("Select * from Referencia Where IDReferencia >= 8506000 LIMIT $batch_size OFFSET $offset");
	$num_rows = db_num_rows($sql_ref);
	
	while ($row_referencia = db_fetch_array($sql_ref)){
		$row_referencia[IDReferencia];
		insert_codEspecifica_plano($row_referencia[IDReferencia]);
	}
	$offset += $batch_size;
} while ($num_rows == $batch_size);

	
	
}


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