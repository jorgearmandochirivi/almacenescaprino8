<body>

	<?php 
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
		
			case "add" :
				print_form("","insert","Nuevo Usuario","Agregar Registro");
			break;
			
			case "insert" :
				insert_user($HTTP_POST_VARS,$HTTP_POST_FILES);
			break;
			
			case "edit":
					print_form($id,"update","Actualizar Usuarios","Realizar Cambios");
			break ;
			
			case "update" :
					update_user($HTTP_POST_VARS);
			break;
			
			case "del":
					print_form($id,"delete","Eliminar Usuario","Remover Usuario");
			break ;
			
			case "delete" :
					delete_user($IDUsuario);
			break;
			
			case "delete_foto":
					delete_img($foto,$campo,$id);
					print_form($id,"update","Actualizar Categor&iacute;a","Realizar Cambios");
			break;
			
			case "list" :
				$sql = make_qry_string($HTTP_POST_VARS);
				list_r($sql);
			break;
			
			default :
				list_r("");
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
 
function insert_user($frm,$files){

				$qid = db_query("SELECT MAX(IDUsuario) AS maximo FROM Usuario");
				$result = db_fetch_object($qid);
				if (isset ($result->maximo))
					$IDUsuario = $result->maximo + 1;
				else
					$IDUsuario = 1;
			
			$qry_usr = db_query("Select IDUsuario FROM Usuario Where User = '$frm[Usuario]' ");
			
			if(db_num_rows($qry_usr) == 0){
				$qry_insert = db_query("INSERT INTO Usuario (IDUsuario, Nombre,Telefono,Nivel,Autorizado,User,Password)
										VALUES ($IDUsuario,'$frm[Nombre]','$frm[Telefono]','$frm[Nivel]',
												'$frm[Autorizado]','$frm[Usuario]',md5('$frm[Passwd]'))
										");
				
				print_form($IDUsuario,"update","Actualizar Usuario","Realizar Cambios");
			}	
			else {
				window_alert("Usuario $frm[Usuario] ya existe ");
				auto_back(1,0);
			}
				
				
		
}

function update_user($frm){

	if (!empty($frm['Passwd']))
				$Passwd = ", Password = Password('$frm[Passwd]')";
		
	$qry_insert = db_query("UPDATE Usuario SET
								Nombre = '$frm[Nombre]',
								Telefono = '$frm[Telefono]',
								Nivel = '$frm[Nivel]',
								Autorizado = '$frm[Autorizado]'
								$Passwd
							WHERE IDUsuario = '$frm[IDUsuario]'
							");

	print_form($frm['IDUsuario'],"update","Actualizar Categor&iacute;a","Realizar Cambios");
					
}

function delete_user($id){

		$qry_delete = db_query("DELETE FROM Usuario WHERE IDUsuario = '$id' ");
		window_alert("Usuario $id eliminado del sistema \\n");
		list_r();
}


/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/


function print_form($id="",$newmode,$title,$submit_caption) {

	Global $array_nivel;
	
	$qid = db_query(" SELECT * FROM Usuario WHERE IDUsuario = '$id' ");
	$r = db_fetch_object($qid);
	
	
?>
<script language="JavaScript1.2"><!--

	 function Evalua(Form)
	 {
	 	if (Form.Nombre.value==""){
			window.alert("El campo nombre es obligatorio");
			Form.Nombre.focus();
			return false;
		}
		if (Form.IDGrupoUsuarios.value==""){
			window.alert("Seleccione el Grupo de usuario");
			Form.IDGrupoUsuarios.focus();
			return false;
		}
		if (Form.action.value=="insert"){
			if (Form.Usuario.value==""){
				window.alert("El campo usuario es obligatorio");
				Form.Usuario.focus();
				return false;
			}
			if ((Form.Passwd.value == "") || (Form.Passwd.value != Form.RePasswd.value)){
				window.alert("Por favor verifique el password");
				Form.Passwd.value = ''; Form.RePasswd.value = '';
				Form.Passwd.focus();
				return false;
			}
		}
		if ( Form.Passwd.value != "") 
			if (Form.Passwd.value != Form.RePasswd.value){
				window.alert("Por favor verifique el password");
				Form.Passwd.value = ''; Form.RePasswd.value = '';
				Form.Passwd.focus();
				return false;
			}
		
 	}

	// -->
</script>
	
					  
	
	<table cellpadding='0' cellspacing='1' border='0' width="100%" align='center'>
	<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
		<tr>
			  <td>&nbsp;  </td>
		</tr>
        <tr>
				<td  cla>
                <table cellpadding='1' cellspacing='1' border='0' width='100%'>
						<tr>
					  <td class="col1" align="right">Nombre :</td>
					  <td class="2"><input type="text" size="25" name="Nombre" value="<?php pv($r->Nombre)?>"class="post"></td>
					</tr>
						<tr>
							<td class="col1" align="right">Tel&eacute;fono :</td>
							<td class="col2"><input type="text" size="25" name="Telefono" value="<?php pv($r->Telefono)?>"class="post"></td>
						</tr>
						<tr>
							<td class="col1" align="right">Autorizado:</td>
							<td class="col2"><select name="Autorizado" class="popup">
									<option></option>
									<option value="S" <?php if ($r->Autorizado == "S") echo "selected"; ?> >Si</option>
									<option value="N" <?php if ($r->Autorizado == "N") echo "selected"; ?>>No</option>
								</select></td>
						</tr>
						<tr>
							<td class="col1" align="right">Nivel :</td>
							<td class="col2"><select class="popup" name="Nivel">
									<option value=""></option><?php 
										foreach($array_nivel as $key => $value){
										 	
										 	echo "<option value=$key ";
										 	if ($r->Nivel == $key)
										 		echo "selected";
										 	echo ">$value</option>\n";
										}
										?>
								</select></td>
						</tr>
						<tr>
							<td class="titulodetablas" colspan="2"><b>Datos de Acceso</b></td>
						</tr>
						<tr>
							<td class="col1" align="right">Usuario :</td>
							<td class="col2"><input type="text" <?php if($newmode == "update") echo "readonly"; ?>  size="20" name="Usuario" value="<?php pv($r->User)?>" class="post"></td>
						</tr>
						<tr>
							<td class="col1" align="right">Password :</td>
							<td class="col2"><input type="password" name="Passwd" size="16" maxlength="16" class="post"></td>
						</tr>
						<tr>
							<td class="col1" align="right">Reingrese Password :</td>
							<td class="col2"><input type="password" name="RePasswd" size="16" maxlength="16" class="post"></td>
						</tr>
						<tr>
					  <td class="colform" colspan="2" align="center">
					  <input type=hidden name="IDUsuario" value="<?php pv($r->IDUsuario) ?>">
					  <input type="hidden" name="action" value="<?php echo $newmode;?>">
					  <input type="hidden" name="mod" value="usr">
					  <input type="submit" name="submit" value="<?php echo $submit_caption;?>" class="submit">
					  </td>
					</tr>
					</table>
	
				   </td>
			</tr>
			</form>
				  </table>
			
	<?php 
}// End function print_form()
 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function list_r($sql="", $listar=null){
 Global $dblink,$total_records,$col,$numtoshow,$array_nivel;
 
 if(empty($sql))
 	$sql = "Select * From Usuario WHERE Nivel <> '0' ORDER BY Nombre";
 

$nav = new buildNav;
$nav->offset = 'offset';
$nav->number_type = 'number';
(!empty($listar))? $nav->limit = $listar:$nav->limit=10;
$nav->execute($sql,$dblink);
$total_records =  $nav->total_result;
$rows = $nav->rows;
$result = $nav->sql_result;
$row = $offset;
$startrow = $offset + 1;
$finalrow = ($row * $nav->limit) + $rows;

	$pages = $nav->show_num_pages('&laquo;','&laquo; prev','&raquo;','next &raquo;','|','class=navvar');   // show pages

$info = $nav->show_info(); 


	
?>

	<table width="98%" border="0" align='center' cellspacing="1" cellpadding="0" class="Tablas">	
		<tr>
			<td>&nbsp; 
				<?php echo "Se encontraron $total_records registro(s) Mostrando del $startcol al $finalcol ";
				?>	
			</td>
		</tr>
		<tr>
			<td> 
				<table width="100%" border="0" cellspacing="1" cellpadding="4">
					<tr>
						<td  class="titulodetablas" nowrap>Usuario</td>
						<td class="titulodetablas" nowrap>Nombre</td>
						<td class="titulodetablas" nowrap>Tel&eacute;fono</td>
						<td  class="titulodetablas" align="center">Nivel</td>
						<td class="titulodetablas" align="center">Autorizado</td>
						<td class="titulodetablas" align="center" valign="middle" width="29">Edit</td>
											<td class="titulodetablas" align="center" valign="middle" width="40">Delete</td>
										</tr>
									<?php while( $r = db_fetch_object($result) )
									{
  									?>	
					<tr>
						<td class="col1list"><span class="gen"><a class="gen2"><?php pv($r->User)?></a></span></td>
						<td class="col1list"><?php pv($r->Nombre) ?></td>
						<td class="col1list"><?php echo $r->Telefono;?></td>
						<td class="col1list" align="center" valign="middle"><?php echo $array_nivel[$r->Nivel];?></td>
						<td class="col1list" align="center" valign="middle"><?php pv($r->Autorizado) ?></td>
						<td class="col1list" align="center" valign="middle" width="29">
							<a href='<?php echo "?m=Permisos&a=usuario&action=edit&id=$r->IDUsuario"; ?>'>
							<img src='images/edit.gif' border='0'></a>
						</td>
						<td class="col1list" align="center" valign="middle" width="40">
							<a href='<?php echo "?m=Permisos&a=usuario&action=del&id=$r->IDUsuario"; ?>'>
								<img src='images/trash.gif' border='0'>
								</a>
						</td>
					</tr>
									<?php } // END while(db_fetch_object($qry_Usuario)){
									?>	
					<tr>
						<td class="colform" colspan="7" nowrap>
						<?php 
							print($pages);
						?>
						</td>
					</tr>
					<tr height="1">
						<td colspan="7" height="1" class="spaceRow"></td>
					</tr>
				
				</table>
			</td>
		</tr>
	</table>
	<?php 					
}// Enf function list()	

?>
