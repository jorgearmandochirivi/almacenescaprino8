<body> <?

$TitleMod ="Costo Referencia";

$Table = "CostoReferencia";
$TableJoin = "Referencia";
$Key = "IDCostoReferencia";
$MOD = "CargaCostoReferencia";
$m="CargaCostoRefrencia";



		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;
			
			case "insert" :
				$frm= vars_LOG($_POST);
				$id = insert($frm);
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
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
			
			case "cargacosto":
			$insertados=0;
			$files = $_FILES;
			
				
				$filedir  = $dirroot."files/reglascedula/";
				$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
				foreach($files AS $key => $file){
					
					if(!empty($file['name'])){
						$ext =  $file['type'] ;					
						if(in_array($file['type'],$mimes)){	
							$nombre_archivo=date("Y-m-d_H:s:i").$file['name'];				
							if(copy($file['tmp_name'], $filedir.$nombre_archivo )){
								$_POST['ArchivoCosto'] = $nombre_archivo;	
								$filename=$filedir.$nombre_archivo;
								
									
											if($fp = fopen($filename,"r")){
												$cont = 0;
												$contfallas = 0;
												while(!feof($fp)){
													ini_set('auto_detect_line_endings', true); 
													$linea = fgets($fp,4096);
													
													$fields = array_map('addslashes',array_map('trim', explode("\t",$linea)));
													$referencia = $fields[0];
													$costo = (int)$fields[1];
													$fecha = $fields[2];
													$observacion = $fields[3];
													$usuario = "admin";
													$fecha_creacion=date("Y-m-d");
													
													// consulto el id de la referencia
													$id_referencia=get_field("Referencia","IDReferencia","Numero",$referencia);
													
													
													//verifico que no exista
													$sql_costo = " SELECT IDCostoReferencia FROM CostoReferencia WHERE IDReferencia = '" . $id_referencia."' and Costo = '".$costo ."' and Fecha = '".$fecha."'";
													$qry_costo = db_query( $sql_costo );
													$r_costo = db_fetch_array( $qry_costo );
													$id_costo = $r_costo["IDCostoReferencia"];
													
													if (!empty($id_costo)){
														$array_errores[]="Ya existe: " . $referencia . " " . $costo . " " . $fecha;
													}
													else{
														if ($id_referencia!=""){					
															$id_max_costo=get_maxID("CostoReferencia","IDCostoReferencia");
															$sql_inserta_costo = " INSERT INTO CostoReferencia (IDCostoReferencia, IDReferencia, Costo,Fecha, Observacion, UsuarioTrCr, FechaTrCr) VALUES ( '".$id_max_costo."','".$id_referencia."','".$costo."','" .$fecha . "','".$observacion."', 'Admin',NOW())";						
															$qry_inserta_costo = db_query( $sql_inserta_costo );													
															$insertados++;
														}
														else{
															$array_errores[]="No existe la referencia " . $referencia . " " . $costo . " " . $fecha;	
														}
													}
												}
												fclose($fp);
												if (count($array_errores)>0):
													foreach($array_errores as $datos):
														echo "<br>" . $datos;
													endforeach;
												endif;
												echo "<br>Insertados: ".$insertados;
											}
											else
												echo "error open $filename";
												
									
									
										
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
			
			break;
			
			case "list" :	
			
			if ($_GET[field]=="Referencia"):
				$sql = "Select * 
					   from Referencia R, CostoReferencia CR
					   Where R.IDReferencia = CR.IDReferencia
					   and R.Numero like '%".$_GET[QryString]."%'";
			else:
				$sql = make_qry_string($_GET);		   
			endif;
			
			
			
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
var Check = new Array('Descripcion','Publicar');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgColor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
			<td><a href="./?mod=<%=$MOD%>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>




<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?}?>>
	
<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=maintitle bgColor=#9daac6>&nbsp;<? echo $TitleMod ?> <? echo $r->$Key ?></td>
	</tr>
	<tr>
	<td><table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>
	  <tr class=row2>
	    <td>Referencia</td>
	    <td><? echo formpopup("Referencia","Numero","IDReferencia","IDReferencia",$r->IDReferencia,"input\" id=\"Referencia"); ?></td>
	    </tr>
	  <tr class=row2>
	    <td width="200"> Costo </td>
	    <td><input type=text size=25 class=input   name=Costo id=costo value="<?=$r->Costo ?>"></td>
	    </tr>
	  <tr class=row2>
	    <td width="200"> fecha </td>
	    <td><input type=text size=25 class=input   name=Fecha id=Fecha value="<?=$r->Fecha ?>">
	      <script language="JavaScript1.2">
						<!--
							if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
						//-->
					</script></td>
	    </tr>
	  <tr class=row2>
	    <td width="200"> Observaciones </td>
	    <td><textarea name="Observacion" id="Observacion" rows="5" cols="30" class="input" ><?=$r->Observacion ?></textarea></td>
	    </tr>
	  <tr>
	    <td colspan=3 align=center class=row2><input type=hidden name=IDCostoReferencia id=IDCostoReferencia value="<?=$r->IDCostoReferencia ?>">
	      <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
          <input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
          <input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
          <input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
          <input type=hidden name=ID value="<? echo $r->$Key ?>">
          <input type=hidden name=action value=<?=$newmode?>>
          <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit></td>
	    </tr>
	  </table></td>
	</tr>
</table>
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
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgColor='#FFFFFF'>
	<tr>
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
		<td><a href="./?mod=<%=$MOD%>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
<br>
<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data">
<table cellpadding=1 cellspacing=0 class=bordertable align=center >
		<tr>
			<td class=titlemedium bgColor=#9daac6 colspan="2">Cargar Costos</td>
		</tr>
	<tr>
    	<td bgcolor="#DBEAF5"><p>Cargar Costos</p>
   	    <p>
        Estructura<br>
        Referencia|Costo|Fecha|Observacion</p></td>
						  <td bgcolor="#DBEAF5" ><input type="file" name="ArchivoCosto" id="ArchivoCosto"></td>
    </tr>
	<tr>
	  <td colspan="2"  align="center" class=row1><span class="row2">
	    <input type=submit name=submit value="Cargar" class=submit>
        <input type="hidden" name="action" id="action" value="cargacosto">
  </span></td>
  </tr>
</table>

</form>

<?
		if($rows > 0){
?>		
<br>


<br>
<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgColor=#9daac6><b>Listar <? echo $TitleMod ?></b></td>
		</tr>
<?filtrar();?>	
<tr>
			<td class=titlemedium  bgColor=#9daac6><% echo $info;%></td>
		</tr>
<tr>
<td class=texto bgColor=#DBEAF5 colspan=12 nowrap>
<?
	print $pages;
?>
</td>
</tr>
	<tr>
			<td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
<td align=center class=rowform valign=middle bgColor=#DBEAF5 width=69>Editar</td>
<td class=rowform nowrap bgColor=#DBEAF5>Referencia</td>
<td class=rowform nowrap bgColor=#DBEAF5>Costo</td>
						<td class=rowform nowrap bgColor=#DBEAF5>Fecha</td>
						<td class=rowform nowrap bgColor=#DBEAF5>Observacion</td>
						<td align=center  class=rowform valign=middle bgColor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<? while($r = db_fetch_object($result)){
?>
  	
<tr>
<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<? echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
<td nowrap class=row1><? echo get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);  ?></td> <td nowrap class=row1><? echo $r->Costo ?></td>
						<td nowrap class=row1><? echo $r->Fecha ?></td>
						<td nowrap class=row1><? echo $r->Observacion ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<? echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<? } // END for
?>
<tr>
<td class=texto bgColor=#DBEAF5 colspan=6 nowrap>
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
	<form name="frm" action="./" method="get" onsubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="Referencia">Referencia</option>
					<option value="Costo">Costo</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				ordenar por 
				<select name="order_by" class="popup">
					<option value="Referencia">Referencia</option>
					<option value="Costo">Costo</option>
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
				<input type="hidden" name="action" value="list">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?		
	}//End function filtrar
?>
