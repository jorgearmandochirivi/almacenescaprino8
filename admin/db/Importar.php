<body> <?php
$TitleMod ="Referencia";

$Table = "Referencia";
$TableJoin = "CodificacionEspecifica";
$Key = "IDReferencia";
$MOD = "Referencia";
$m="db";

		switch (nvl($action)) {
			case "Referencias" :
				GLOBAL $main_types,$tamano_archivo;
				$files = $_FILES;
				$dirroot = dirname(__FILE__)."/";
				echo $filedir  = $dirroot."filexls/";
				foreach($files AS $key => $file){
					$ext = $main_types[ $file['type'] ];
					if(copy($file['tmp_name'], "$filedir/Datos.xls")){
						$FileName = "Datos.xls";
					}
					else echo "Error al cargar archivo verifique!!! ".$filedir.$file['name'];
				}
				db_query( "BEGIN" );
				insert_referecias($filedir.$FileName);
				//db_query( "tales" );
				db_query( "COMMIT" );
				unlink("$filedir$FileName");
				print_form("","Refeencia","Importar Archivos","submit");
			break;
			case "Inventario" :
				GLOBAL $main_types,$tamano_archivo;
				$files = $_FILES;
				//print_r( $HTTP_POST_VARS );
				$dirroot = dirname(__FILE__)."/";

				$filedir  = $dirroot."filexls/";
				foreach($files AS $key => $file){
					$ext = $main_types[ $file['type'] ];
					if(copy($file['tmp_name'], "$filedir/DatosInv.xls")){
						$FileName = "DatosInv.xls";
					}
					else echo "Error al cargar archivo verifique!!! ".$filedir.$file['name'];
				}
				db_query( "BEGIN" );
				insert_inventario($filedir.$FileName);
				//db_query( "tales" );
				db_query( "COMMIT" );
				unlink("$filedir$FileName");
				print_form("","Refeencia","Importar Archivos","submit");
			break;
			case "Precio" :
				GLOBAL $main_types,$tamano_archivo;
				//$files = $_FILES;
				$files = $_FILES;
				//print_r( $_FILES );
				//exit;
				$dirroot = dirname(__FILE__)."/";
				$filedir  = $dirroot."filexls/";
				foreach($files AS $key => $file){
					$ext = $main_types[ $file['type'] ];
					if(copy($file['tmp_name'], "$filedir/DatosPr.xls")){
						$FileName = "DatosPr.xls";
					}
					else echo "Error al cargar archivo verifique!!! ".$filedir.$file['name'];
				}
				db_query( "BEGIN" );
				insert_precios($filedir.$FileName);
				//db_query( "tales" );
				db_query( "COMMIT" );
				unlink("$filedir$FileName");
				print_form("","Refeencia","Importar Archivos","submit");
			break;
			default :
					print_form("","Refeencia","Importar Archivos","submit");
			break;

		} // End switch

function validar_existe_referencia($numero){
	$sql="SELECT IDTipoTalla FROM Referencia WHERE Numero = '$numero'";
	$qry = db_query($sql);
	$numreg = db_num_rows($qry);
	if($numreg>0) return false;
	else return true;
}

function insert_referecias($filename){
	Global $ID_Usuario,$Table;
	/***** traer array con cueros y colores      ***/
	$colores = array( );
	$cueros = array( );
	$lineas = array( );

	$qry_cuero = db_query( $sql_cueros = " SELECT IDCuero, Descripcion FROM Cuero " );
	while( $r_cuero = db_fetch_array( $qry_cuero ) )
	{
		$array_cuero[$r_cuero[Descripcion]] = $r_cuero[IDCuero];
		$i++;
	}//end while

	$qry_color = db_query( $sql_color = " SELECT IDColor, Nombre FROM Color " );
	while( $r_color = db_fetch_array( $qry_color ) )
	{
		$array_color[$r_color[Nombre]] = $r_color[IDColor];
		$i++;
	}//end while

	$qry_lineas = db_query( $sql_lineas = "SELECT IDLinea, Nombre FROM Linea" );
	while( $r_linea = db_fetch_array( $qry_lineas ) )
	{
		$array_linea[$r_linea[Nombre]] = $r_linea[IDLinea];
		$i++;
	}//end while

	//print_r( $array_cuero );

	/****** fin traer array con cueros y colores ***/

	if($fp = fopen($filename,"r")){
		$cont = 0;
		$contfallas = 0;
		while(!feof($fp)){
			ini_set('auto_detect_line_endings', true);
			$linea = fgets($fp,4096);
			$fields = array_map('addslashes',array_map('trim', explode(",",$linea)));
			$valido = validar_existe_referencia($fields[6]);
			if($valido){
				$id = get_maxID("Referencia","IDReferencia");
				$cuero = substr( $fields[6], 4,1 );
				$color = substr( $fields[6], 5,1 );
				$linea = substr( $fields[6], 0,2 );

				$cuero = $array_cuero[$cuero];
				$color = $array_color[$color];
				$linea = $array_linea[$linea];

if( empty( $linea ) )
{
$linea = substr( $fields[6], 0,2 );
$idlinea = get_maxID( "Linea","IDLinea" );
db_query( "INSERT INTO Linea (IDLinea, Nombre, IDTipo ) VALUES ( '$idlinea','$linea','1' )" );
$linea = $idlinea;
db_query( "commit" );
}//end linea
				echo $sql_insert = "INSERT INTO Referencia VALUES('$id','','$fields[0]','$fields[1]','$fields[2]','$fields[3]',
																		'$color','$linea','$fields[5]','$cuero','$fields[6]','$fields[7]',
																		'$fields[6]','S','$ID_Usuario',NOW(),'','')";

				db_query( $sql_insert );
db_query( "commit" );

				insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
				$cont++;
			}
			else{
				$contfallas++;
				$add_msg = "\\n\\nNo se insertaron $contfallas referencias, verifique por favor ";
				$referenciasno .= $fields[6]."  ";
				$add_msg .= $referenciasno;
			}
		}

		window_alert("Se ha cargado $cont registros de Referencias.$add_msg");
		fclose($fp);
		return $data;
	}
	else
		echo "error open $filename";

}

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
	Global $ID_Usuario,$Table,$IDPuntoVenta,$Tallas;
	/***** array con tallas del archivo y su posicion     ***/


if( $Tallas == "Hombre" )
	$tarchivo = array( "37"=>"1",
						"38"=>"2",
						"39"=>"3",
						"40"=>"4",
						"41"=>"5",
						"42"=>"6",
						"43"=>"7",
						"44"=>"8");

if( $Tallas == "Mujer" )
	$tarchivo = array( "34"=>"1",
						"35"=>"2",
						"36"=>"3",
						"37"=>"4",
						"38"=>"5",
						"39"=>"6",
						"40"=>"7");

if( $Tallas == "CMujer" )
	$tarchivo = array( "L"=>"1",
						"M"=>"2",
						"S"=>"3",
						"XL"=>"4");

if( $Tallas == "CHombre" )
	$tarchivo = array( "32"=>"1",
						"34"=>"2",
						"35"=>"3",
						"36"=>"4",
						"37"=>"5",
						"38"=>"6",
						"39"=>"7",
						"40"=>"8",
						"41"=>"9",
						"42"=>"10");

if( $Tallas == "Unicas" )
	$tarchivo = array( "1"=>"1");

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
						$sql_insert = "INSERT INTO CodificacionEspecifica VALUES('$id','$puntoventaref','$valor[IDTalla]','$existencias','10','0',
														'S','$ID_Usuario',NOW(),'','')";
						echo "<br>";
						db_query( $sql_insert );
						insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
						$cont++;
					}//end if
					else
					{
						$r_cod = db_fetch_object( $qry_cod );
						$sql_insert = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '$r_cod->IDCodificacionEspecifica' ";
						echo "<br>";
						db_query( $sql_insert );
						insertlog($ID_Usuario,$Table,$id,"Agregar Referencias",$sql_insert);
						$cont++;
					}//end else
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


/********************************** FIN INSERTAR INVENTARIO ******************************/

/********************************** INICIAR PRECIOS ******************************/
function insert_precios($filename){
	Global $ID_Usuario,$Table;

	if($fp = fopen($filename,"r")){
		$cont = 0;
		while(!feof($fp)){
			ini_set('auto_detect_line_endings', true);
			$linea = fgets($fp,4096);
			$fields = array_map('addslashes',array_map('trim', explode(",",$linea)));
			if( empty( $fields[0] ) )
				continue;
			$sql_referencias = " SELECT IDReferencia FROM Referencia WHERE Numero LIKE '$fields[0]%' ";

			$qry_referencias = db_query( $sql_referencias );

			
			$precio = get_field( "Precio","IDPrecio","ValorVenta",$fields[1]."' AND Descuento = '$fields[2]");
			//$id_tipologia = get_field( "Tipologia","IDTipologia","Nombre",$fields[3]);

if( empty($precio) )
{
	$idprecio = get_maxID( "Precio","IDPrecio" );
	db_query( "INSERT INTO Precio (IDPrecio, Descripcion, ValorVenta, Publicar, Descuento) VALUES ( '$idprecio','$fields[1]','$fields[1]','S','$fields[2]' )" );
db_query( "COMMIT" );
$precio = $idprecio;
}//end if

			$r_referencias = db_fetch_object( $qry_referencias);



					if($fields[3]=="S"):
						$actualiza_saldo = ", Saldo = 'S' ";
					else:
						$actualiza_saldo = ", Saldo = 'N'";
					endif;


				 //$sql_actualiza = "UPDATE Referencia SET IDPrecio = '$precio' ".$actualiza_saldo.", IDTipologia = '".$id_tipologia."' WHERE IDReferencia = '$r_referencias->IDReferencia'";
				 $sql_actualiza = "UPDATE Referencia SET IDPrecio = '$precio' ".$actualiza_saldo."  WHERE IDReferencia = '$r_referencias->IDReferencia'";

				//echo "<br>";
				db_query( $sql_actualiza );
				db_query("commit");
				insertlog($ID_Usuario,"Referencia",$id,"Actualizar Precio",$sql_actualiza);
				$cont++;


		}

		window_alert("Se ha cargado $cont registros de Referencias.");
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
					<form name="frmUsr" action="" method="post" enctype="multipart/form-data" onSubmit="return EvaluaReg(this,Check2)">
						<tr class=row2>
							<td colspan="2"><?php echo Mensaje_Info("Importar Referencias");?></td>
						</tr>
						<tr class=row2>
							<td>Importar desde Excel</td>
							<td><input name=Datos type=file size=25 style="font-size:10px"></td>
						</tr>
						<tr class=row2>
							<td align="center"><input type=hidden name=action value="Referencias"></td>
							<td><input type=submit name=submit value="Importar" class=submit></td>
						</tr>
					</form>
					<form name="frmInv" action="" method="post" enctype="multipart/form-data" onSubmit="return EvaluaReg(this,Check2)">
						<tr class=row2>
							<td colspan="2"><?php echo Mensaje_Info("Importar Inventario Referencias");?></td>
						</tr>
						<tr class=row2>
							<td>Importar desde Excel</td>
							<td><input name=Datos type=file size=25 style="font-size:10px"></td>
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
							<td>Tallas a Importar</td>
							<td><select name="Tallas" class="input">
									<option value=''>Seleccione</option>
									<option value='Mujer'>Dama</option>
									<option value='Hombre'>Hombre</option>

									<option value='CMujer'>Correas Mujer</option>
									<option value='CHombre'>Correas Hombre</option>

									<option value='Unicas'>Unicas</option>
								</select></td>
						</tr>
						<tr class=row2>
							<td align="center"><input type=hidden name=action value="Inventario"></td>
							<td><input type=submit name=submit value="Importar" class=submit></td>
						</tr>
					</form>

					<form name="frmPr" action="" method="post" enctype="multipart/form-data" >
						<tr class=row2>
							<td colspan="2"><?php echo Mensaje_Info("Actualizar Precios");?></td>
						</tr>
						<tr class=row2>
							<td>Importar desde Excel</td>
							<td><input name=Datos type=file size=25 style="font-size:10px"></td>
						</tr>
						<tr class=row2>
							<td align="center"><input type=hidden name=action value="Precio"></td>
							<td><input type=submit name=submit value="Importar" class=submit></td>
						</tr>
					</form>

				</table>
			</td>
	</tr>
</table>
<?php
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY $Key";

		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=30;
   		$nav->execute($sql,$dblink);
		$total_records =  $nav->total_result;
		$rows = $nav->rows;
		$result = $nav->sql_result;
		$row = $offset;
		$startrow = $offset + 1;
		$finalrow = ($row * $nav->limit) + $rows;

	 	$pages = $nav->show_num_pages('&laquo;','&laquo; prev','&raquo;','next &raquo;','|','class=navvar');   // show pages

		$info = $nav->show_info();

		 if($_GET['in_order']=="ASC" || $_GET['in_order']==""){
								$img="down.png";
								$order="DESC";
							}else if($_GET['in_order']=="DESC"){
								$img="up.png";
								$order="ASC";
							}

							?>
<br>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
		<a href="./?mod=<?php echo $MOD;?>">Administrar <?php echo $TitleMod;?></a> </td>
		<td><a href="./?mod=<?php echo $MOD;?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
<?php
		if($rows > 0){
?>
<br>
<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
<?php filtrar();?>
<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php echo $info;;?></td>
		</tr>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=16 nowrap>
<?php
	print $pages;
?>
</td>
</tr>
	<tr>
			<td>
				<table width=100% border=0 cellspacing=1 cellpadding=0>
				<tr>
				<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Numero&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Numero<?php if($_GET['order_by']=="Numero")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDProveedor&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Proveedor&nbsp;<?php if($_GET['order_by']=="IDProveedor")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Tipo de Talla</a><a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>"><?php if($_GET['order_by']=="IDTipoTalla")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDLinea&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Tipo Ref.&nbsp;<?php if($_GET['order_by']=="IDLinea")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDLinea&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Linea&nbsp;<?php if($_GET['order_by']=="IDLinea")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Nombre<?php if($_GET['order_by']=="Nombre")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Publicar&nbsp;<?php if($_GET['order_by']=="Publicar")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
				<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
				</tr>

<?php while($r = db_fetch_object($result)){
?>

<tr>
<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
<td nowrap class=row1><?php echo $r->Numero ?></td>
<td nowrap class=row1><?php echo $r->IDProveedor ?></td>
						<td nowrap class=row1><?php echo get_field("TipoTalla","Descripcion","IDTipoTalla",$r->IDTipoTalla) ?></td>
						<td nowrap class=row1><?php echo get_field("Tipo","Descripcion","IDTipo",get_field("Linea","IDTipo","IDLinea",$r->IDLinea))?></td>
						<td nowrap class=row1><?php echo get_field("Linea","Nombre","IDLinea",$r->IDLinea) ?></td>
						<td nowrap class=row1><?php echo $r->Nombre ?></td>
						<td nowrap class=row1><?php echo $r->Publicar ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>
</td>
					</tr>
<?php } // END for
?>
<tr>
						<td class=texto bgcolor=#DBEAF5 colspan=8 nowrap>
	<?php
		print $pages;
		?>
</td>
					</tr>
</table></td>
		</tr>
</table>

<?php
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
}// Enf function list()

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD;
?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="Numero">Numero</option>
					<option value="Tipo.descripcion">Tipo</option>
					<option value="Linea.Nombre">Linea</option>
					<option value="TipoTalla.Descripcion">tipo de Talla</option>
					<option value="Nombre">Nombre</option>
				</select>
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post">
				ordenar por
				<select name="order_by" class="popup">
					<option value="Numero">Numero</option>
					<option value="Tipo.descripcion">Tipo</option>
					<option value="Linea.Nombre">Linea</option>
					<option value="Nombre">Nombre</option>
				</select>
				<br>
				de forma
				<select name="in_order" class="popup">
					<option value="ASC">Ascendente</option>
					<option value="DESC">Descendente</option>
				</select>
				Listar
				<select name="listar" class="popup">
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="30">30</option>
				</select>
				<br>
				<input type="hidden" name="mod" value="<?=$MOD?>">
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Linea">
				<input type="hidden" name="tlevel" value="TipoTalla">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
?>
