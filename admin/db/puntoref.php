<body> <?php
$TitleMod ="Referencia";

$Table = "Referencia";
$TableJoin = "CodificacionEspecifica";
$Key = "IDReferencia";
$MOD = "Referencia";
$m="db";

		switch (nvl($action)) {
			case "puntoref" :
				GLOBAL $main_types,$tamano_archivo;
				
				db_query( "BEGIN" );
				
				$sql_referencias = " SELECT * FROM Referencia ";
				$qry_referencia = db_query( $sql_referencias );
				while( $r = db_fetch_object( $qry_referencia ) )
				{
					$qry_tallas = db_query( $sql_tallas = "SELECT * from Talla WHERE IDTipoTalla = '$r->IDTipoTalla'" );
		
					while( $r_tallas = db_fetch_array( $qry_tallas  ) )
					{
						$array_tallas[ $r->IDTipoTalla ][ $r_tallas[ IDTalla ] ] = $r_tallas;
					}//end while
					
					$idpuntoventa = get_maxID( "PuntoVentaReferencia", "IDPuntoVentaReferencia" );
					echo $sql_insert = "INSERT INTO PuntoVentaReferencia VALUES ( '$idpuntoventa','$r->IDReferencia','$IDPuntoVenta' )";
					db_query( $sql_insert );
					
					foreach( $array_tallas[ $r->IDTipoTalla ] as $idtalla => $datostalla )
					{
						
						//existe registro?
						//$sql_cod = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$idpuntoventa' AND IDTalla = '$idtalla' ";
						//$qry_cod = db_query( $sql_cod );
						//if( db_num_rows( $qry_cod ) == 0 )
						//{
							$id = get_maxID("CodificacionEspecifica","IDCodificacionEspecifica");
							echo $sql_insert = "INSERT INTO CodificacionEspecifica VALUES('$id','$idpuntoventa','$idtalla','0','5','0',
															'S','$ID_Usuario',NOW(),'','')";
							echo "<br>";
							db_query( $sql_insert );
							//insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
							$cont++;
						//}//end if
						/*
						else
						{
							$r_cod = db_fetch_object( $qry_cod );
							echo $sql_insert = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '$r_cod->IDCodificacionEspecifica' ";
							echo "<br>";
							db_query( $sql_insert );
							insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
							$cont++;
						}//end else
						*/
					}//end foreach
					
					
				}//end while
				
				db_query( "COMMIT" );
								
			break;	
			default : 
					print_form("","Refeencia","Importar Archivos","submit");
			break;
		
		} // End switch
		

/*************** puntoventareferencia **************************/
function puntoventareferencia( $numero )
{
	GLOBAL $IDPuntoVenta;
	
	$IDReferencia = get_field( "Referencia","IDReferencia","Numero",$numero );
	$sql = "SELECT * FROM PuntoVentaReferencia WHERE IDPuntoVenta = '$IDPuntoVenta' AND IDReferencia = '$IDReferencia'  ";
	$qry = db_query( $sql );
	if( db_num_rows( $qry )  > 0 )
	{
		$r = db_fetch_object( $qry );
		return $r->IDPuntoVentaReferencia;
	}//end if
	else
	{
		$idpuntoventa = get_maxID( "PuntoVentaReferencia", "IDPuntoVentaReferencia" );
		echo $sql_insert = "INSERT INTO PuntoVentaReferencia VALUES ( '$idpuntoventa','$IDReferencia','$IDPuntoVenta' )";
		echo "<br>";
		db_query( $sql_insert );
		return $idpuntoventa;
	}//end else
}//end punto
/*************** fin puntoventareferencia **********************/


/************************* INSERTAR INVENTARIO *************************************/

function insert_inventario($filename){
	Global $ID_Usuario,$Table,$IDPuntoVenta;
	/***** array con tallas del archivo y su posicion     ***/
	
	/*
	$tarchivo = array( "37"=>"1",
						"38"=>"2",
						"39"=>"3",
						"40"=>"4",
						"41"=>"5",
						"42"=>"6",
						"43"=>"7",
						"44"=>"8");
	
	
	*/
	$tarchivo = array( "34"=>"1",
						"35"=>"2",
						"36"=>"3",
						"37"=>"4",
						"38"=>"5",
						"39"=>"6",
						"40"=>"7");
	
	
	/*
	$tarchivo = array( "1"=>"1");
	*/				
	$qry_tipotalla = db_query( $sql_tipotalla = "SELECT * FROM TipoTalla" );
	
	while( $r_tipotalla = db_fetch_array( $qry_tipotalla ) )
	{
		$qry_tallas = db_query( $sql_tallas = "SELECT * from Talla WHERE IDTipoTalla = '$r_tipotalla[IDTipoTalla]'" );
		
		$i = 0;
		while( $r_tallas = db_fetch_array( $qry_tallas  ) )
		{
			$array_tallas[ $r_tipotalla[IDTipoTalla] ][$i] = $r_tallas;
			$i++;
		}//end while
	}//end while
		
	//print_r( $array_tallas );
		
	/****** fin traer array con tallas ***/
	
	if($fp = fopen($filename,"r")){
		$cont = 0;
		$contfallas = 0;
		while(!feof($fp)){
			ini_set('auto_detect_line_endings', true); 
			$linea = fgets($fp,4096);
			$fields = array_map('addslashes',array_map('trim', explode("\t",$linea)));

			$valido = validar_existe_referencia($fields[0]);
			if(!$valido){
				$puntoventaref = puntoventareferencia( $fields[0] );
				$tipotalla = get_field( "Referencia","IDTipoTalla","Numero",$fields[0] );
				
				foreach( $array_tallas[$tipotalla] as $key => $valor )
				{
					$ubtalla = $tarchivo[$valor[Descripcion]];
					$existencias =  $fields[$ubtalla];
					
					//existe registro?
					$sql_cod = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$puntoventaref' AND IDTalla = '$valor[IDTalla]' ";
					$qry_cod = db_query( $sql_cod );
					if( db_num_rows( $qry_cod ) == 0 )
					{
						$id = get_maxID("CodificacionEspecifica","IDCodificacionEspecifica");
						echo $sql_insert = "INSERT INTO CodificacionEspecifica VALUES('$id','$puntoventaref','$valor[IDTalla]','$existencias','10','0',
														'S','$ID_Usuario',NOW(),'','')";
						echo "<br>";
						db_query( $sql_insert );
						insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
						$cont++;
					}//end if
					else
					{
						$r_cod = db_fetch_object( $qry_cod );
						echo $sql_insert = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '$r_cod->IDCodificacionEspecifica' ";
						echo "<br>";
						db_query( $sql_insert );
						insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
						$cont++;
					}//end else
				}//end foreach
								
			}
			else{
				$contfallas++;
				$referencias .= ",".$fields[6];
				$add_msg = "\\n\\nNo se insertaron $contfallas referencias, verifique por favor: ".$referencias;
			}
		}
		
		window_alert("Se ha cargado $cont registros de Referencias.$add_msg");
		fclose($fp);
		return $data;
	}
	else
		echo "error open $filename";
	
}


/********************************** FIN INSERTAR INVENTARIO ******************************/



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<br>

<table cellpadding=1 cellspacing=0 class=bordertable align=left >
	<tr>
			<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
		</tr>
	<tr>
			<td>
				<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
					<script>
				var Check2 = new Array("IDPuntoVenta");
				</script>
					
					<form name="frmInv" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" onsubmit="return EvaluaReg(this,Check2)">
						<tr class=row2>
							<td colspan="2"><?php echo Mensaje_Info("Asignar Referencias a los puntos");?></td>
						</tr>
						<tr class=row2>
							<td>Punto Venta</td>
							<td><select name="IDPuntoVenta" class="input">
									<?php 								$sql_puntoventa = "SELECT * FROM PuntoVenta";
								$query_puntoventa = db_query($sql_puntoventa);
								while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
								{
									echo "<option value='".$r_puntoventa->IDPuntoVenta."'>".$r_puntoventa->Nombre."</option>";	
								}//end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
							?>
								</select></td>
						</tr>
						<tr class=row2>
							<td align="center"><input type=hidden name=action value="puntoref"></td>
							<td><input type=submit name=submit value="Importar" class=submit></td>
						</tr>
					</form>
				</table>
			</td>
	</tr>
</table>
<?php
}// End function print_form()


?>
