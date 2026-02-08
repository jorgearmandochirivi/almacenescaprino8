<script>
	function EvaluaCampos(){
		if (document.getElementById("Codigo").value==""){
			alert ("El codigo es obligatorio");	
			return false;
		}

		var resultado="";
		var porEstado=document.getElementsByName("Estado");
		
		for(var i=0;i<porEstado.length;i++)
        {
            if(porEstado[i].checked)
                resultado=porEstado[i].value;
        }
		
		if (resultado==""){
			alert ("Debe seleccionar un estado poara la tarjeta");	
			return false;
		}
		
		if (resultado=="A"){
			if (confirm("ATENCION: Si cambia el Estado por Activo se desvinculara la tarjeta del cliente. Desea continuar?")){
				return true;
			}
			else{
				return false;
			}	
		}	
		return true;	
	}
</script>


<body> <?php 
$TitleMod ="Tarjetas Fidelizacion";

$Table = "TarjetaFidelizacion";
$TableJoin = "Cliente";
$Key = "IDTarjetaFidelizacion";
$MOD = "TarjetaFidelizacion";
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
				
				if ($frm["Estado"]=="A"){
					$sql_libera_tarjeta="Update Cliente set IDTarjetaFidelizacion = '', NumeroTarjeta = '' Where IDTarjetaFidelizacion = '".$frm[IDTarjetaFidelizacion]."' Limit 1 ";
					$qry_libera_tarjeta=db_query($sql_libera_tarjeta);
					$frm[IDCliente]=0;
				}
				update($frm);
				
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$_GET[action]="";
					$sql_libera_tarjeta="Update Cliente set IDTarjetaFidelizacion = '', NumeroTarjeta = '' Where IDTarjetaFidelizacion = '".$_POST[IDTarjetaFidelizacion]."' Limit 1 ";					
					$qry_libera_tarjeta=db_query($sql_libera_tarjeta);
				delete($ID);
			break;

			case "delcedulas" :
				$sql_elimina="Delete from PlanCedula where IDPlanContacto = '".$_GET[id]."'";
				db_query($sql_elimina);	
				window_alert("Cedulas eliminadas con exito ");
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
						
			break;
			
			case "list" :
			
				
			if ($_GET[field]=="Cedula"){
				$sql="Select TF.* FROM TarjetaFidelizacion TF, Cliente C Where TF.IDCliente = C.IDCliente and C.Cedula = '".$_GET[QryString]."' ";

			}
			if ($_GET[field]=="Codigo"){
				$sql="Select TF.* FROM TarjetaFidelizacion TF Where TF.Codigo = '".$_GET[QryString]."' ";
			}			
			
			//$sql = make_qry_string($_GET);
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

var Check = new Array('Codigo','Estado');


</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>


<table cellpadding=1 cellspacing=0 class=bordertable align=left >
	<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){ ?>onsubmit="return EvaluaCampos(this,Check)"<?php }?>>
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr class=row2>
						  <td>Codigo</td>
						  <td><input type="text" name="Codigo" id="Codigo" title="Codigo" class="tbox" value="<?php echo $r->Codigo ?>"></td>
		  </tr>
						<tr class=row2>
						  <td>Punto de venta</td>
						  <td><?php echo formpopup("PuntoVenta","Nombre","Nombre","IDPuntoVenta",$r->IDPuntoVenta,"input\" id=\"PuntoVenta"); ?></td>
		  </tr>
						<tr class=row2>
						  <td>Estado</td>
						  <td><span class="col2"><?php echo formradiogroup(array('Activa'=>'A','Inactiva'=>'I','Entregada'=>'E','Perdida'=>'P'),$r->Estado, 'Estado'); ?></span></td>
		  </tr>
						<tr class=row2>
						  <td>Observacion</td>
						  <td><textarea name="Observacion" id="Observacion" rows="5" cols="30"><?php echo $r->Observacion; ?></textarea></td>
		  </tr>
						<tr class=row2>
						  <td>Cliente:</td>
						  <td>
                              <a href="?mod=ClienteFidelizado&action=edit&id=<?php echo $r->IDCliente; ?>&idnot=">
                                  <?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$r->IDCliente); ?>
                              </a>  
                              
                          </td>
		  </tr>
						
						
						<tr class=row2>
			<td width="50%" colspan="2"></td>
			</tr>
			<tr>
			<td align=center class=row2 colspan="2">
            	<input type=hidden name=IDTarjetaFidelizacion value="<?php echo $r->IDTarjetaFidelizacion ?>">
                <input type=hidden name=IDCliente value="<?php echo $r->IDCliente ?>">
            	<input type=hidden name=UsuarioTrCr value="<?php echo $r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?php echo $r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?php echo $r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?php echo $r->FechaTrEd ?>">
				<input type=hidden name=ID value="<?php echo $r->$Key ?>">
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
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY $Key";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=60;
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
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
<?php 
		if($rows > 0){
?>		
<br>
<table width=750 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
        
<?php filtrar();?>	        
        
<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
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
				<td class=rowform nowrap bgcolor=#DBEAF5>Codigo</td>
				<td class=rowform nowrap bgcolor=#DBEAF5> 
					Punto Venta
				</td>
				<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Estado</td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
				</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
<td align=center valign=middle nowrap width=50 class=row2>
  &nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
<td nowrap class=row1><?php echo $r->Codigo ?></td>
<td nowrap class=row1><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta);  ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
                        <span class="row1">
							<?php 
							if ($r->Estado=="A")
								echo "Activa";
							elseif($r->Estado=="I")
								echo "Inactiva";
							elseif($r->Estado=="E")
								echo "Entregada";
							elseif($r->Estado=="P")
								echo "Perdida";
								
							 ?>
                        </span></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<?php } // END for
?>
<tr>
						<td class=texto bgcolor=#DBEAF5 colspan=3 nowrap>
	<?php 
		print $pages;
		?>
</td>
					</tr>		
</table>
</td>
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
					<option value="Codigo">Codigo</option>
					<option value="Cedula">Documento</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				ordenar por 
				<select name="order_by" class="popup">
					<option value="Codigo">Codigo</option>
					<option value="Cedula">Documento</option>
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
				<input type="hidden" name="mod" value="<?php echo $MOD?>">
				<input type="hidden" name="action" value="list">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 		
	}//End function filtrar
?>
