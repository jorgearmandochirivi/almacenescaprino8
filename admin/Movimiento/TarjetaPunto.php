<body> <?
$TitleMod ="Importar Archivo de Codigos de Tarjetas";

$Table = "TarjetaPunto";
$TableJoin = "";
$Key = "TarjetaPunto";
$MOD = "TarjetaPunto";
$m="TarjetaPunto";


		switch (nvl($action)) {
			
			case "TarjetaPunto" :
				GLOBAL $main_types,$tamano_archivo;
				$files = $_FILES;
				
				
				//print_r( $HTTP_POST_VARS );
				$dirroot = dirname(__FILE__)."/";

				$filedir  = $dirroot."filexls/";
				foreach($files AS $key => $file){
					$ext = $main_types[ $file['type'] ];
					/*
					if(copy($file['tmp_name'], "$filedir/DatosTarj.xls")){
						$FileName = "DatosTarj.xls";
					}
					else{ 
						echo "Error al cargar archivo verifique!!! ".$filedir.$file['name'];
					}
					*/
					if(copy($file['tmp_name'], "$filedir/DatosTarj.txt")){
						$FileName = "DatosTarj.txt";
					}
					else{ 
						echo "Error al cargar archivo verifique!!! ".$filedir.$file['name'];
					}
					
				}
				
				db_query( "BEGIN" );
				insert_codigo_tarjeta($filedir.$FileName);
				db_query( "COMMIT" );
				unlink("$filedir$FileName");
				print_form("","Refeencia","Importar Archivos","submit");
			break;	
						
			default : 
					print_form("","Importar Archivos","submit");
			break;
		
		} // End switch
		





/************************* INSERTAR INVENTARIO *************************************/

function insert_codigo_tarjeta($filename){
	Global $ID_Usuario,$Table,$IDPuntoVenta;
	$referencia = 7615;
	$talla = 16;
	
	//	exit;
					
		
	//print_r( $array_tallas );
		
	/****** fin traer array con tallas ***/
	
	if($fp = fopen($filename,"r")){
		
		$cont = 0;
		$contfallas = 0;
		while(!feof($fp)){
			ini_set('auto_detect_line_endings', true); 
			$linea = fgets($fp,4096);
			
			$fields = array_map('addslashes',array_map('trim', explode(",",$linea)));
			$codigotarjeta = $fields[0];
			
			if(!empty($codigotarjeta)):
			
			
					$sql_punto_venta_referencia = " SELECT IDPuntoVentaReferencia FROM PuntoVentaReferencia WHERE IDReferencia = '" . $referencia . "' AND IDPuntoVenta = '" . $IDPuntoVenta . "'  ";
					$qry_punto_venta_referencia = db_query( $sql_punto_venta_referencia );
					$r_punto_venta_referencia = db_fetch_array( $qry_punto_venta_referencia );
					$puntoventaref = $r_punto_venta_referencia["IDPuntoVentaReferencia"];
					
					//insertar codifo en TarjetaPunto
					 $sql_tarjeta = " INSERT INTO TarjetaPunto (CodigoTarjeta, IDPuntoVenta, UsuarioTrCr, FechaTrCr) VALUES ('" . $fields[0] . "','" . $IDPuntoVenta . "','" . $ID_Usuario . "', NOW() )  ";
					$qry_tarjeta = db_query( $sql_tarjeta );
					
					//existe registro?
					$sql_cod = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$puntoventaref' AND IDTalla = '$talla' ";
					$qry_cod = db_query( $sql_cod );
					if( db_num_rows( $qry_cod ) == 0 )
					{
						$id = get_maxID("CodificacionEspecifica","IDCodificacionEspecifica");
						$sql_insert = "INSERT INTO CodificacionEspecifica VALUES('$id','$puntoventaref','$talla','1','10','0',
														'S','$ID_Usuario',NOW(),'','')";
						db_query( $sql_insert );
						$cont++;
					}//end if
					else
					{
						$r_cod = db_fetch_object( $qry_cod );
						$sql_insert = "UPDATE CodificacionEspecifica SET Existencias = Existencias + 1 WHERE IDCodificacionEspecifica = '$r_cod->IDCodificacionEspecifica' ";
						db_query( $sql_insert );
						insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
						$cont++;
					}//end else
		endif;			

					
			
		}
		
		window_alert("Se han revisado y cargado $cont registros de Referencias.$add_msg");
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
function print_form($id="",$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	

?>
<br>

<table cellpadding=1 cellspacing=0 class=bordertable align=left >
	<tr>
			<td class=maintitle bgcolor=#9daac6>&nbsp;<? echo $TitleMod ?></td>
		</tr>
	<tr>
			<td>
				<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
					<script>
				var Check2 = new Array("Importar");
				</script>
					
					<form name="frmInv" action="" method="post" enctype="multipart/form-data" onSubmit="return EvaluaReg(this,Check2)">
						<tr class=row2>
							<td colspan="2"><?=Mensaje_Info("Importar Codigos Tarjetas Puntos de Venta") ?></td>
						</tr>
						<tr class=row2>
							<td>Importar desde Excel</td>
							<td><input name="Datos" type="file" size=25 style="font-size:10px"></td>
						</tr>
						<tr class=row2>
							<td>Punto Venta</td>
							<td><select name="IDPuntoVenta" class="input">
									<?
										$sql_puntoventa = "SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre";
										$query_puntoventa = db_query($sql_puntoventa);
										while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
										{
											echo "<option value='".$r_puntoventa->IDPuntoVenta."'>".$r_puntoventa->Nombre."</option>";	
										}//end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
									?>
								</select></td>
						</tr>
						
						<tr class=row2>
							<td align="center"><input type="hidden" name="action" value="TarjetaPunto"></td>
							<td><input type="submit" name="submit" value="Importar" class="submit"></td>
						</tr>
					</form>
					
					
					
				</table>
			</td>
	</tr>
</table>
<?
}// End function print_form()

?>
