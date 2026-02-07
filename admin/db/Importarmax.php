<body> <?php
$TitleMod ="Referencia";

$Table = "Referencia";
$TableJoin = "CodificacionEspecifica";
$Key = "IDReferencia";
$MOD = "Referencia";
$m="dbmaximos";

		switch (nvl($action)) {
			case "maximos" :
				GLOBAL $main_types,$tamano_archivo;
				$files = $_FILES;
				//print_r( $_POST );
				$dirroot = dirname(__FILE__)."/";
				$filedir  = $dirroot."filexls/";
				foreach($files AS $key => $file){
					$ext = $main_types[ $file['type'] ];
					if(copy($file['tmp_name'], "$filedir/DatosMax.csv")){
						$FileName = "DatosMax.csv";
					}
					else echo "Error al cargar archivo verifique!!! ".$filedir.$file['name'];
				}
				db_query( "BEGIN" );
				insert_inventario($filedir.$FileName);
				//db_query( "tales" );
				db_query( "COMMIT" );
				unlink("$filedir$FileName");
				print_form("","Referencia","Importar Archivos","submit");
			break;	
			default : 
					print_form("","Referencia","Importar Archivos","submit");
			break;
		
		} // End switch
		
function validar_existe_referencia($numero){
	$sql="SELECT IDTipoTalla FROM Referencia WHERE Numero = '$numero'";
	$qry = db_query($sql);
	$numreg = db_num_rows($qry);
	if($numreg>0) return false;
	else return true;
}


/*************** puntoventareferencia **************************/
function puntoventareferencia( $numero, $IDPuntoVenta )
{
	
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
	Global $ID_Usuario,$Table,$Tallas;
	/***** array con tallas del archivo y su posicion     ***/
	

if( $Tallas == "Hombre" )
	$tarchivo = array( "37"=>"3",
						"38"=>"4",
						"39"=>"5",
						"40"=>"6",
						"41"=>"7",
						"42"=>"8",
						"43"=>"9");
	
if( $Tallas == "Mujer" )	
	$tarchivo = array( "34"=>"3",
						"35"=>"4",
						"36"=>"5",
						"37"=>"6",
						"38"=>"7",
						"39"=>"8",
						"40"=>"9");
if( $Tallas == "Unicas" )	
	$tarchivo = array( "1"=>"3");

//print_r( $tarchivo );
if( empty( $tarchivo ) )
	exit;
	
//	exit;
					
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
			$fields = array_map('addslashes',array_map('trim', explode(",",$linea)));
			
			$IDPuntoVenta = $fields[1];
			
			$valido = validar_existe_referencia($fields[0]);
			if(!$valido){
				$puntoventaref = puntoventareferencia( $fields[0], $IDPuntoVenta );
				$tipotalla = get_field( "Referencia","IDTipoTalla","Numero",$fields[0] );
				
				foreach( $array_tallas[$tipotalla] as $key => $valor )
				{
					$ubtalla = $tarchivo[$valor[Descripcion]];
					$maximo =  $fields[$ubtalla];
					if( is_numeric( $maximo ) > 0 )
					{
					
						//existe registro?
						$sql_cod = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$puntoventaref' AND IDTalla = '$valor[IDTalla]' ";
						$qry_cod = db_query( $sql_cod );
						if( db_num_rows( $qry_cod ) == 0 )
						{
							$id = get_maxID("CodificacionEspecifica","IDCodificacionEspecifica");
							echo $sql_insert = "INSERT INTO CodificacionEspecifica ( IDCodificacionEspecifica, IDPuntoVentaReferencia, IDTalla, Existencias,
												Maximo, Minimo, Publicar, UsuarioTrCr, FechaTrCr, UsuarioTrEd, FechaTrEd ) 
												VALUES('$id','$puntoventaref','$valor[IDTalla]','0','$maximo','0',
															'S','$ID_Usuario',NOW(),'','')";
							echo "<br>";
							db_query( $sql_insert );
							//insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
							$cont++;
						}//end if
						else
						{
							$r_cod = db_fetch_object( $qry_cod );
							echo $sql_insert = "UPDATE CodificacionEspecifica SET Maximo = '$maximo' WHERE IDCodificacionEspecifica = '$r_cod->IDCodificacionEspecifica' ";
							echo "<br>";
							db_query( $sql_insert );
							//insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
							$cont++;
						}//end else
					}//end if
				}//end foreach
								
			}
			else{
				$contfallas++;
				echo $referencias .= "<br>".$fields[0];
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




/*************** actualiza precios **************************/


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
				var Check2 = new Array("Importar");
				</script>
				
					<form name="frmInv" action="<?=$PhP_SELF?>" method="post" enctype="multipart/form-data" onsubmit="return EvaluaReg(this,Check2)">
						<tr class=row2>
							<td colspan="2"><?php echo Mensaje_Info("Importar Inventario Referencias");?></td>
						</tr>
						<tr class=row2>
							<td>Importar desde Excel</td>
							<td><input name=Datos type=file size=25 style="font-size:10px"></td>
						</tr>
						<tr class=row2>
							<td>Tallas a Importar</td>
							<td><select name="Tallas" class="input">
									<option value=''>Seleccione</option>
									<option value='Mujer'>Dama</option>
									<option value='Hombre'>Hombre</option>
									<option value='Unicas'>Unicas</option>
								</select></td>
						</tr>
						<tr class=row2>
							<td align="center"><input type=hidden name=action value="maximos"></td>
							<td><input type=submit name=submit value="Importar" class=submit></td>
						</tr>
					</form>
					
					<
					
				</table>
			</td>
	</tr>
</table>
<?php
}// End function print_form()

?>