<body> 
<?php 
require_once($libdir."PHPExcel_1.8.0_doc/Classes/PHPExcel/IOFactory.php");

$TitleMod ="Curvas Pedido Terceros";

$Table = "CurvaTercero";
$TableJoin = "";
$Key = "IDCurvaTercero";
$MOD = "CurvaTercero";
$m = "PedidoTercero";

$permisos = get_permiso($ID_Usuario,$m,$Table);
	
function insert_curva($filename,$id_curva_tercero){	

	$sql_tallas = "Select * From Talla Where 1 Group By Descripcion Order By Nombre"; 
	$result_talla = db_query($sql_tallas);
	$contador_talla=3; // en la posicon 3 empieza las tallas del excel
	while ($row_talla = db_fetch_array($result_talla)){
		$array_talla[ $contador_talla ] = $row_talla["IDTalla"];
		$contador_talla++;
	}

	if (!empty($id_curva_tercero)){
		// Borro clientes que pertenezacan a la regla
		$sql_elimina="Delete from DetalleCurvaTercero where IDCurvaTercero = '".$id_curva_tercero."'";
		db_query($sql_elimina);
		
		
			if($fp = fopen($filename,"r")){
					$objReader = PHPExcel_IOFactory::createReader('Excel2007');
					$objReader->setReadDataOnly(true);
					$objPHPExcel = $objReader->load($filename);
					$objWorksheet = $objPHPExcel->getActiveSheet();
					$fila=1;
					foreach ($objWorksheet->getRowIterator() as $row) {
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false); 
						$columna=1;
						foreach ($cellIterator as $cell) {
							if($fila!=1){ // Encabezados
								$valor = $cell->getValue();
								$valor = ereg_replace("[^0-9]", "", $valor); 
								if($columna==1){
									$codigo_almacen=$valor;	
								}
								if($columna!="2" && $columna!="1" && !empty($valor)){ // Contiene el nombre del almacen que no interesa para la carga
											$sql_minimos = "Insert Into DetalleCurvaTercero (IDCurvaTercero, IDPuntoVenta, IDTalla, Tipo, Valor)
														Values('".$id_curva_tercero."','".$codigo_almacen."','".$array_talla[$columna]."','Maximo','".$valor."')";
														
											db_query($sql_minimos);			
								}
							}	
							$columna++;	
						}
						$fila++;
					}
				
				fclose($fp);
				return true;
			}
			else{
				echo "Error al abrir el archivo";
			}
	}
	
}	
		
		
if($permisos[0] >= 2)
{
	
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;
			
			case "insert" :
				$frm= vars_LOG($HTTP_POST_VARS);
				$id = insert($frm);
				
				//ACTUALIZO LOS MINIMOS Y MAXIMOS POR PUNTO
				foreach($frm as $id_dato => $datos1){
					if ($id_dato=="Minimo" || $id_dato=="Maximo"){
						foreach($datos1 as $id_talla => $dato_talla)	{
							foreach($dato_talla as $id_punto => $cantidad)	{
								//echo "<br>ES Talla " . 	$id_talla . " Punto " . $id_punto . " Valor = " . $cantidad;
								if ((int)$cantidad>0){
									$sql_minimos = db_query("Insert Into DetalleCurvaTercero (IDCurvaTercero, IDPuntoVenta, IDTalla, Tipo, Valor)
											   	    Values('".$id."','".$id_punto."','".$id_talla."','".$id_dato."','".$cantidad."')");
								}
							}
						}
					}
				}
				
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update":
				$frm= vars_LOG($HTTP_POST_VARS);
				$resultado_carga = 0;
				$files = $_FILES;
				$filedir  = $dirroot."files/reglascedula/";
				foreach($files AS $key => $file){
					if(!empty($file['name'])){
							$nombre_archivo=date("Y-m-d_H:s:i").$file['name'];				
							if(copy($file['tmp_name'], $filedir.$nombre_archivo )){
								$_POST['ArchivoCliente'] = $nombre_archivo;								
								insert_curva($filedir.$nombre_archivo,$frm[IDCurvaTercero]);
								$resultado_carga = 1;
								//unlink($filedir.$nombre_archivo);			
							}
							else{ 
								echo "Error al cargar archivo verifique!!! ".$filedir.$file['name'];
								exit;
							}
					}
				}	
				
				
				if ($resultado_carga=="0"){
					//ACTUALIZO LOS MINIMOS Y MAXIMOS POR PUNTO
					$borrar_datos_curva = db_query("DELETE FROM DetalleCurvaTercero Where  IDCurvaTercero = '".$frm[IDCurvaTercero]."'");
					foreach($frm as $id_dato => $datos1){
						if ($id_dato=="Minimo" || $id_dato=="Maximo"){
							foreach($datos1 as $id_talla => $dato_talla)	{
								foreach($dato_talla as $id_punto => $cantidad)	{
									//echo "<br>ES Talla " . 	$id_talla . " Punto " . $id_punto . " Valor = " . $cantidad;
									if ((int)$cantidad>0){
										$sql_minimos = db_query("Insert Into DetalleCurvaTercero (IDCurvaTercero, IDPuntoVenta, IDTalla, Tipo, Valor)
														Values('".$frm[IDCurvaTercero]."','".$id_punto."','".$id_talla."','".$id_dato."','".$cantidad."')");
									}
								}
							}
						}
					}
				}
				
				update($frm);
			break;
			
			case "cargar_curva":
			require_once($libdir."PHPExcel_1.8.0_doc/Classes/PHPExcel/IOFactory.php");
					$objReader = PHPExcel_IOFactory::createReader('Excel2007');
					$objReader->setReadDataOnly(true);
					$objPHPExcel = $objReader->load("PedidoTercero/FormatoCargaCurvas.xlsx");
					$objWorksheet = $objPHPExcel->getActiveSheet();
					echo '<table>' . "\n";
					foreach ($objWorksheet->getRowIterator() as $row) {
					echo '<tr>' . "\n";
					 
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
					// even if it is not set.
					// By default, only cells
					// that are set will be
					// iterated.
					foreach ($cellIterator as $cell) {
					echo '<td>' . $cell->getValue() . '</td>' . "\n";
					}
					echo '</tr>' . "\n";
					}
					echo '</table>' . "\n";
					exit;

			break;
			
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$HTTP_GET_VARS[action]="";
				$borrar_datos_curva = db_query("DELETE FROM DetalleCurvaTercero Where  IDCurvaTercero = '".$ID."'");
				delete($ID);
			break;
			case "list" :	
			$sql = make_qry_string($HTTP_GET_VARS);
			list_r($sql);
			break;
			default : 
					list_r();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$Dia_array;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);
	
	$sql_tallas = "Select * From Talla Where 1 Group By Descripcion Order By Nombre"; 
	$result_talla = db_query($sql_tallas);
	while ($row_talla = db_fetch_array($result_talla)){
		$array_talla[ $row_talla["IDTalla"] ] = $row_talla;
	}
	
	$sql_punto_venta = "Select IDPuntoVenta,Nombre From PuntoVenta Where 1  Order By Nombre"; 
	$result_punto_venta = db_query($sql_punto_venta);
	while ($row_punto_venta = db_fetch_array($result_punto_venta)){
		$array_punto_venta[ $row_punto_venta["IDPuntoVenta"] ] = $row_punto_venta;
	}
	
	
	if (!empty($id)){
		//Consulto el detalle de minimos y maximos
		$sql_datos_curva= "Select* From DetalleCurvaTercero Where IDCurvaTercero = '".$id."'"; 
		$result_datos_curva = db_query($sql_datos_curva);
		while ($row_datos_curva = db_fetch_array($result_datos_curva)){
			$array_datos_curva[ $row_datos_curva["IDPuntoVenta"] ] [ $row_datos_curva["IDTalla"] ] [ $row_datos_curva["Tipo"] ]  = $row_datos_curva["Valor"];
		}
	}
	
	if ($newmode=="insert"){
		$minimo = get_field("ParametroTercero","Valor","IDParametroTercero",5);
		if ((int)$minimo!=0)
			$minimo_inicial = $minimo;
		
		$maximo = get_field("ParametroTercero","Valor","IDParametroTercero",6);	
		if ((int)$maximo!=0)
			$maximo_inicial = $maximo;
		
		
	}

?>
<script>
var Check = new Array('Fecha','Dia');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>
<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?> onSubmit="return EvaluaReg(this,Check)" <?php }?>>
	
<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
			<td>Nombre</td><td><input type="text" class="input" name="Nombre" id="Nombre" size="19" value="<?php echo $r->Nombre?>" >
								
							</td>
			</tr>
						<tr class=row2>
						  <td>Descripci&oacute;n</td>
						  <td><textarea name="Descripcion" id="Descripcion" cols="40" rows="4"><?php echo $r->Descripcion?></textarea></td>
		  </tr>
						<tr class=row2>
						  <td>Cargar Curva <br>
                          Solo se aceptan xlsx</td>
						  <td><input type="file" name="ArchivoCurva" id="ArchivoCliente"></td>
		  </tr>
						<tr class=row2>
						  <td  class="maintitle" bgcolor="#9daac6" colspan="2">DETALLE CURVA</td>
		  </tr>
						<tr class=row2>
						  <td colspan="2">
                          
                          <?php
                          if (count($array_punto_venta)>0):
							foreach($array_punto_venta as $id_punto_venta => $datos_punto_venta):
						   ?>	
                          <table width="100%" border="0" cellspacing="1" cellpadding="0">
						    <tbody>
						      <tr>
						        <td class="maintitle" bgcolor="#9daac6" colspan="2" ><?php echo $id_punto_venta  . " - " . $datos_punto_venta[Nombre]; ?></td>
					          </tr>
						      <tr>
						        <td class="titlemedium">Talla:</td>
                                <?php 
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>	
							        <td class="titlemedium" nowrap><?php echo $datos_talla[Nombre]; ?></td>
                                    <?php endforeach; 
								endif;	
								?>
                                
					          </tr>
						      <tr>
						        <td class="rowform" >Minimo</td>
                                <?php 
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>	
							        <td class=row1 align=center><input type="text" name="Minimo[<?php echo $datos_talla[IDTalla]; ?>][<?php echo $datos_punto_venta[IDPuntoVenta]; ?>]"  size="5" value="<?php if(!empty($minimo_inicial)) echo $minimo_inicial; else echo $array_datos_curva[$datos_punto_venta[IDPuntoVenta]][$datos_talla[IDTalla]] ["Minimo"] ?>" ></td>
                                    <?php endforeach; 
								endif;	
								?>
					          </tr>
						      <tr>
						        <td class="rowform">Maximo</td>
	                            <?php 
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>	
							        <td class=row1 align=center><input type="text" name="Maximo[<?php echo $datos_talla[IDTalla]; ?>][<?php echo $datos_punto_venta[IDPuntoVenta]; ?>]"  size="5" value="<?php if(!empty($maximo_inicial)) echo $maximo_inicial; echo $array_datos_curva[$datos_punto_venta[IDPuntoVenta]][$datos_talla[IDTalla]] ["Maximo"] ?>"></td>
                                    <?php endforeach; 
								endif;	
								?>
					          </tr>
					        </tbody>
					      </table>
                          <br />
                         <?php
                          endforeach;
						 endif; 
						  ?>
                          
                          </td>
		  </tr>		
			
			<tr>
			<td colspan=2 align=center class=row2><input type=hidden name=IDCurvaTercero value="<?php echo $r->IDCurvaTercero ?>">    <input type=hidden name=ID value="<?php echo $r->$Key ?>">
				<input type=hidden name=action value=<?php echo $newmode?>>
				<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?php 
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$Dia_array;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY $Key";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=100;
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
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
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
	
	<tr>
		<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
	</tr>
	<tr>
		<td class=texto bgcolor=#DBEAF5 colspan= nowrap>
		<?php 
			print $pages;
		?>
		</td>
	</tr>
	<tr><td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Nombre<a style="color: #3A4F6C;text-decoration: none" href='<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Descripcion</td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class=row1><?php echo  $r->Nombre ?></td>
						<td  class=row1><?php echo  $r->Descripcion ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
						  &nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=4 nowrap>
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

?>
