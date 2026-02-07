<body> <?
$TitleMod ="Plan Contactos / Campa&ntilde;as";

$Table = "PlanContacto";
$TableJoin = "";
$Key = "IDPlanContacto";
$MOD = "PlanContacto";
$m="Fidelizacion";
$permisos = get_permiso($ID_Usuario,$m,$Table);




//********************* INSERTA CEDULA REGLA*******************************************
function insert_cedula_plan($filename,$id_plan){	
	if (!empty($id_plan)){
		// Borro clientes que pertenezacan a la regla
		$sql_elimina="Delete from PlanCedula where IDPlanContacto = '".$id_plan."'";
		db_query($sql_elimina);
		
		
			if($fp = fopen($filename,"r")){
				$cont = 0;
				$contfallas = 0;
				while(!feof($fp)){
					$id_cliente="";
					ini_set('auto_detect_line_endings', true); 
					$linea = fgets($fp,4096);
					
					$fields = array_map('addslashes',array_map('trim', explode(",",$linea)));
					$cedula = (int)$fields[0];
					$sql_cedula = " SELECT IDCliente FROM Cliente WHERE Cedula = '" . $cedula."'";
					$qry_cedula = db_query( $sql_cedula );
					$r_cedula = db_fetch_array( $qry_cedula );
					$id_cliente = $r_cedula["IDCliente"];
					
					if (empty($id_cliente)){
						$cedula_no_existe[]=$cedula;
					}
					else{
						//insertar cedula regla	
						if ($cedula!=0){					
							$sql_cedula_regla = " INSERT INTO PlanCedula (IDPlanContacto, IDCliente,Cedula, UsuarioTrCr, FechaTrCr) VALUES ( '".$id_plan."','".$id_cliente."','" .$cedula . "','Admin',NOW())";						
							$qry_cedula_regla = db_query( $sql_cedula_regla );													
						}
					}
				}
				fclose($fp);
				return $cedula_no_existe;
			}
			else
				echo "error open $filename";
	}
	
}
//********************* FIN INSERTA CEDULA REGLA*******************************************














if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;
			
			case "insert" :
			
			
				$frm= vars_LOG($_POST);
				$id = insert($frm);
				
				$files = $HTTP_POST_FILES;
				//print_r( $HTTP_POST_FILES );
				
								//Linea				
				$seleccionados_linea = $_POST['IDLinea'];
				if(count($seleccionados_linea)>0){
					for($i=0; $i < count($seleccionados_linea); $i++){
						$linea[]=$seleccionados_linea[$i];
					}	
					$_POST['IDLinea']	= implode("|",$linea);
				}


				$filedir  = $dirroot."files/reglascedula/";
				$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
				foreach($files AS $key => $file){
					if(!empty($file['name'])){
						$ext =  $file['type'] ;					
						if(in_array($file['type'],$mimes)){	
							$nombre_archivo=date("Y-m-d_H:s:i").$file['name'];				
							if(copy($file['tmp_name'], $filedir.$nombre_archivo )){
								$_POST['ArchivoCliente'] = $nombre_archivo;								
								insert_cedula_plan($filedir.$nombre_archivo,$_POST[IDPlanContacto]);
								unlink($filedir.$nombre_archivo);			
							}
							else{ 
								echo "Error al cargar archivo verifique!!! ".$filedir.$file['name'];
								exit;
							}
							
						}
						else{
							echo "El archivo no tiene una extension valida por favor verifique que sea un archivo de texto o csv";	
							exit;
						}
					}
				}	
				
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				
				
			
				$files = $HTTP_POST_FILES;
				//print_r( $HTTP_POST_FILES );
				
				//Linea				
				$seleccionados_linea = $_POST['IDLinea'];
				if(count($seleccionados_linea)>0){
					for($i=0; $i < count($seleccionados_linea); $i++){
						$linea[]=$seleccionados_linea[$i];
					}	
					$_POST['IDLinea']	= implode("|",$linea);
				}
				
				
				$filedir  = $dirroot."files/reglascedula/";
				$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
				foreach($files AS $key => $file){
					if(!empty($file['name'])){
						$ext =  $file['type'] ;					
						if(in_array($file['type'],$mimes)){	
							$nombre_archivo=date("Y-m-d_H:s:i").$file['name'];				
							if(copy($file['tmp_name'], $filedir.$nombre_archivo )){
								$_POST['ArchivoCliente'] = $nombre_archivo;								
								insert_cedula_plan($filedir.$nombre_archivo,$_POST[IDPlanContacto]);								
								unlink($filedir.$nombre_archivo);			
							}
							else{ 
								echo "Error al cargar archivo verifique!!! ".$filedir.$file['name'];
								exit;
							}
							
						}
						else{
							echo "El archivo no tiene una extension valida por favor verifique que sea un archivo de texto o csv";	
							exit;
						}
					}
				}
				
				$frm= vars_LOG($_POST);
				update($frm);
				
				
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$_GET[action]="";
				delete($ID);
			break;

			case "delcedulas" :
				$sql_elimina="Delete from PlanCedula where IDPlanContacto = '".$_GET[id]."'";
				db_query($sql_elimina);	
				window_alert("Cedulas eliminadas con exito ");
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
						
			break;
			
			case "list" :	
			$sql = make_qry_string($_GET);
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

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>



<script>

var Check = new Array('Nombre','Descripcion','Puntos','FechaInicio','FechaFin');


</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
			<td><a href="./?mod=<%=$MOD%>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>


<table cellpadding=1 cellspacing=0 class=bordertable align=left >
	<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <? if($newmode!="delete"){ ?>onsubmit="return EvaluaReg(this,Check)"<?}?>>
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<? echo $TitleMod ?> <? echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr class=row2>
						  <td>Nombre</td>
						  <td><input type="text" name="Nombre" id="Nombre" title="Nombre" class="tbox" value="<?=$r->Nombre ?>"></td>
		  </tr>
						<tr class=row2>
						  <td>Descripcion</td>
						  <td>
                          <textarea name="Descripcion" class="tbox" title="Descripcion" id="Descripcion" cols="40" rows="4"><?=$r->Descripcion ?></textarea>
                          </td>
		  </tr>
						<tr class=row2>
						  <td>Linea</td>
						  <td><select name="IDLinea[]" id="IDLinea" data-placeholder="Seleccione Linea..." class="chosen-select" multiple style="width:350px;" tabindex="4">
						    <? 
																
															$array_linea_guardados=explode("|",$r->IDLinea); 	
                                                            $sql_tipo = "SELECT * FROM Tipo ORDER BY Descripcion";
                                                            $query_tipo = db_query($sql_tipo);
                                                            while($r_tipo = db_fetch_object($query_tipo))
                                                            {
                                                                echo "<option value=''>----".$r_tipo->Descripcion."</option>";
                                                                $sql_linea = "SELECT * FROM Linea WHERE IDTipo = '$r_tipo->IDTipo'";
                                                                $query_linea = db_query($sql_linea);
                                                                while ( $r_linea = db_fetch_object($query_linea) )
                                                                {
																	if(in_array($r_linea->IDLinea,$array_linea_guardados))
																		$opcion_selecc=" selected ";	
																	else
																		$opcion_selecc="";																	
                                                                    echo "<option value='$r_linea->IDLinea' $opcion_selecc";                                                                    
                                                                    echo ">".$r_linea->Nombre."</option>";
                                                                }
                                                            }
                                                        ?>
						    </select></td>
		  </tr>
		  <tr class=row2>
						  <td>% Descuento</td>
						  <td><input type="text" name="Descuento" class="tbox" value="<?=$r->Descuento ?>"></td>
		  </tr>
						<tr class=row2>
						  <td>Compra minima $</td>
						  <td><input type="text" name="CompraMinima" class="tbox" value="<?=$r->CompraMinima ?>"></td>
		  </tr>
						<tr class=row2>
						  <td>Fecha Inicio</td>
						  <td><span class="col2">
						    <input type=text readonly size=10 class=input name=FechaInicio id="FechaInicio" title="Fecha Inicio" value="<?=$r->FechaInicio ?>">
						    <script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaInicio,\"yyyy-mm-dd\")' width=16 height=16 border=0>")	
					//-->
				        </script>
						    </span></td>
		  </tr>
						<tr class=row2>
						  <td>Fecha Fin</td>
						  <td><input type=text size=10 readonly class=input name=FechaFin id="FechaFin" title="Fecha Fin"  value="<?=$r->FechaFin ?>">
						    <script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaFin,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				          </script></td>
		  </tr>
						<tr class=row2>
						  <td>Aplicar Plan a clientes especificos (Cargue archivo txt con el numero de cedula sin puntos ni comas)</td>
						  <td><input type="file" name="ArchivoCliente" id="ArchivoCliente">
                          
                          <?php
                            $sql_cedula=db_query("Select * from PlanCedula where IDPlanContacto = '". $_GET[id] ."' Order By Cedula");
						    while($row_cedula=db_fetch_array($sql_cedula)){ ?>
								<?php $datos.="\r".$row_cedula[Cedula]; ?>
                            <?php		
							}
							
							if(!empty($datos)){?>
                              <b>Cedulas a las que aplica el plan</b><br>	
                              <textarea name="Cedula" id="Cedula" cols="10" rows="4"><?php echo $datos; ?></textarea>
                              <a href="?mod=PlanContacto&action=delcedulas&id=<?php echo $_GET[id] ?>">Eliminar cedulas</a>
                          <? } ?>
                          </td>
		  </tr>
						<tr class=row2>
						  <td>Publicar</td>
						  <td><span class="col2"><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></span></td>
		  </tr>
						<tr class=row2>
						  <td>&nbsp;</td>
						  <td>&nbsp;</td>
		  </tr>
						
						
						<tr class=row2>
			<td width="50%" colspan="2"></td>
			</tr>
			<tr>
			<td align=center class=row2 colspan="2">
            	<input type=hidden name=IDPlanContacto value="<?=$r->IDPlanContacto ?>">
            	<input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=ID value="<? echo $r->$Key ?>">
				<input type=hidden name=action value=<?=$newmode?>>
				<input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			</td>
			</tr>
			</table>
		</td>
	</tr>
</table>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
  <script src="jscripts/choosen/chosen.jquery.js" type="text/javascript"></script>
  <script src="jscripts/choosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>


</form>
<?
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
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
		<td><a href="./?mod=<%=$MOD%>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
<?
		if($rows > 0){
?>		
<br>
<table width=750 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <? echo $TitleMod ?></b></td>
		</tr>
<tr>
			<td class=titlemedium  bgcolor=#9daac6><% echo $info;%></td>
		</tr>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=16 nowrap>
<?
	print $pages;
?>
</td>
</tr>
	<tr>
			<td>
				<table width=100% border=0 cellspacing=1 cellpadding=0>
				<tr>
				<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
				<td class=rowform nowrap bgcolor=#DBEAF5>Nombre</td>
				<td class=rowform nowrap bgcolor=#DBEAF5> 
					Descripcion
				</td>
				<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Inicio</td>
				<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Fin</td>
				<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Publicar</td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
				</tr>

<? while($r = db_fetch_object($result)){
?>
  	
<tr>
<td align=center valign=middle nowrap width=50 class=row2>
  &nbsp;<a href='<? echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
<td nowrap class=row1><? echo $r->Nombre ?></td>
<td nowrap class=row1><? echo $r->Descripcion ?></td>
						<td align=center valign=middle nowrap width=60 class=row2><span class="row1"><? echo $r->FechaInicio; ?></span></td>
						<td align=center valign=middle nowrap width=60 class=row2><span class="row1"><? echo $r->FechaFin; ?></span></td>
						<td align=center valign=middle nowrap width=60 class=row2><span class="row1"><? echo $r->Publicar; ?></span></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<? echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<? } // END for
?>
<tr>
						<td class=texto bgcolor=#DBEAF5 colspan=3 nowrap>
	<?
		print $pages;
		?>
</td>
					</tr>		
</table></td>
		</tr>
</table>	

<? 			
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
					<option value="Linea.Pregunta">Linea</option>
					<option value="TipoTalla.Descripcion">tipo de Talla</option>
					<option value="Pregunta">Pregunta</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				ordenar por 
				<select name="order_by" class="popup">
					<option value="Numero">Numero</option>
					<option value="Tipo.descripcion">Tipo</option>
					<option value="Linea.Pregunta">Linea</option>
					<option value="Pregunta">Pregunta</option>
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
<?		
	}//End function filtrar
?>
