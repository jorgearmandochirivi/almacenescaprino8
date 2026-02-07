<body> <?php
$TitleMod ="Referencia";

$Table = "Referencia";
$TableJoin = "CodificacionEspecifica";
$Key = "IDReferencia";
$MOD = "dbcodificacion";
$m="db";

		switch (nvl($action)) {
			case "dbcodificacion" :
				GLOBAL $main_types,$tamano_archivo;
				
				db_query( "BEGIN" );
				
				$sql_puntos = "SELECT IDPuntoVenta FROM PuntoVenta ";
				$qry_puntos = db_query( $sql_puntos );
				while( $r_puntos = db_fetch_array( $qry_puntos ) )
					$array_puntos[ $r_puntos["IDPuntoVenta"] ] = $r_puntos;

				$sql_referencias = " SELECT * FROM Referencia  WHERE IDReferencia = '7339' ";
				//exit;
				$qry_referencia = db_query( $sql_referencias );
				while( $r = db_fetch_object( $qry_referencia ) )
				{
					$qry_tallas = db_query( $sql_tallas = "SELECT * from Talla WHERE IDTipoTalla = '$r->IDTipoTalla'" );
		
					while( $r_tallas = db_fetch_array( $qry_tallas  ) )
					{
						$array_tallas[ $r->IDTipoTalla ][ $r_tallas[ IDTalla ] ] = $r_tallas;
					}//end while

					foreach( $array_puntos as $idpuntoventa => $datos_punto )
					{
						//validar si existe
						$sql_valida = " SELECT * FROM PuntoVentaReferencia WHERE IDPuntoVenta = '" . $idpuntoventa . "' AND IDReferencia = '" . $r->IDReferencia . "'  ";
						$qry_valida = db_query( $sql_valida );
						if( db_num_rows( $qry_valida ) == 0 )
						{
							//INSERTAR PuntRef y Codificacion Especifica
							$idpuntoventaref = get_maxID( "PuntoVentaReferencia", "IDPuntoVentaReferencia" );
							$sql_insert = "INSERT INTO PuntoVentaReferencia VALUES ( '$idpuntoventaref','$r->IDReferencia','$idpuntoventa' )";
							db_query( $sql_insert );
							
							foreach( $array_tallas[ $r->IDTipoTalla ] as $idtalla => $datostalla )
							{
								
								//existe registro?
								//$sql_cod = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$idpuntoventa' AND IDTalla = '$idtalla' ";
								//$qry_cod = db_query( $sql_cod );
								//if( db_num_rows( $qry_cod ) == 0 )
								//{
									$id = get_maxID("CodificacionEspecifica","IDCodificacionEspecifica");
									echo $sql_insert = "INSERT INTO CodificacionEspecifica VALUES('$id','$idpuntoventaref','$idtalla','0','5','0',
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
						}//end if
						else // es porque existe punoref
						{
							$r_puntoref = db_fetch_object($qry_valida);
							//existe registro?
							
							
								foreach( $array_tallas[ $r->IDTipoTalla ] as $idtalla => $datostalla )
								{
									echo $sql_cod = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_puntoref->IDPuntoVentaReferencia' AND IDTalla = '$idtalla' ";
									$qry_cod = db_query( $sql_cod );
									if( db_num_rows( $qry_cod ) == 0 )
									{

										$id = get_maxID("CodificacionEspecifica","IDCodificacionEspecifica");
										echo $sql_insert = "INSERT INTO CodificacionEspecifica VALUES('$id','$r_puntoref->IDPuntoVentaReferencia','$idtalla','0','5','0',
																		'S','$ID_Usuario',NOW(),'','')";
										echo "<br>";
										db_query( $sql_insert );
										//insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
										$cont++;
									}//end if
								}//end for
							

						}//end else
					}//end for


				}//end while
				//exit;	
				db_query( "COMMIT" );
								
			break;	
			default : 
					print_form("","Refeencia","Importar Archivos","submit");
			break;
		
		} // End switch
		



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
					
					<form name="frmInv" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" >
						<tr class=row2>
							<td colspan="2"><?php echo Mensaje_Info("Asignar Referencias a los puntos");?></td>
						</tr>
						<tr class=row2>
							<td>Referencias Mayores a</td>
							<td>
								<input type="text" name="NumeroReferencia" value="" />
							</td>
						</tr>
						<tr class=row2>
							<td align="center">
								<input type=hidden name=action value="dbcodificacion"></td>
							<td><input type=submit name=submit value="Cargar" class=submit></td>
						</tr>
					</form>
				</table>
			</td>
	</tr>
</table>
<?php
}// End function print_form()


?>
