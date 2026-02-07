
<body> <?php

$TitleMod ="Pedido Tercero";

$Table = "PedidoTercero";
$TableJoin = "PedidoTercero";
$Key = "IDPedidoTercero";
$MOD = "PedidoTercero";
$m="PedidoTercero";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;
			
			case "insert" :
				/*$frm= vars_LOG($HTTP_POST_VARS);
				$id = insert($frm);
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
				*/
				
				$valido = validar_existe_usuario($HTTP_POST_VARS[User]);
				if($valido){
					$frm= vars_LOG($HTTP_POST_VARS);
					$frm['Password']=encode_passwd($frm['Password'],$strcript);
					$frm['Nombre']=ucwords(strtolower($frm['Nombre']));
					$frm['Apellidos']=ucwords(strtolower($frm['Apellidos']));
					$id = insert($frm);
					print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
				}
				else{
					window_alert("El nombre de Usuario ya existe. Intente uno diferente! ");
					echo "<script>javascript:history.back();</script>";
				}
				
				
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);
				
				$frm['Password']=encode_passwd($frm['Password'],$strcript);
				$frm['Nombre']=ucwords(strtolower($frm['Nombre']));
				$frm['Apellidos']=ucwords(strtolower($frm['Apellidos']));
				
				update($frm);
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$HTTP_GET_VARS[action]="";
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

function validar_existe_usuario($usuario){
	$sql="SELECT User FROM Empleado WHERE User = '$usuario'";
	$qry_usr = db_query($sql);
	$numreg = db_num_rows($qry_usr);
	if($numreg>0) return false;
	else return true;

}

/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$strcript,$array_nivel, $Nivel;
	

	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' $where ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('Nombre','Apellidos','IDCargo','Salario','IDComision','IDPuntoVenta','CodigoVendedor','Publicar','Nivel');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<?php echo $MOD;?>">Administrar <?php echo $TitleMod;?></a> </td>
			<td><a href="./?mod=<?php echo $MOD;?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>
<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>
	
<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=440 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
				<td>Proveedor</td>
				<td><?php echo formpopup("Proveedor","Nombre","Nombre","IDProveedor",$r->IDProveedor,"input proveedor_pedido\" id=\"Proveedor"); ?></td>
			</tr>
						<tr class=row2>
				<td colspan="2"><table width="56%" border="0" style="border:1px solid #E8E2E2" align="center">
				  <tbody>
				    <tr>
				      <th colspan="4">Datos Proveedor
				        <?php 
									if( $_GET[IDProveedor] ):										
									 	 $datos_proveedor = $dbo->fetchAll( "Proveedor", " IDProveedor = '" . $EditPropietario[IDProveedor] . "' ", "array" );										  	 
										 
									endif;  
									?>
			          </th>
			        </tr>
				    <tr>
				      <td width="32%"><strong>Nombre</strong></td>
				      <td width="24%"><span id="NombreProveedor"><?php echo $datos_proveedor[Nombre] ?></span></td>
				      <td width="22%"><strong>Direccion</strong></td>
				      <td width="22%"><span id="DireccionProveedor"><?php echo $datos_proveedor[Direccion] ?></span></td>
			        </tr>
				    <tr>
				      <td><strong>Telefono</strong></td>
				      <td><span id="TelefonoProveedor"><?php echo $datos_proveedor[Telefono] ?></span></td>
				      <td><strong>Ciudad</strong></td>
				      <td><span id="CiudadProveedor"><?php echo $datos_proveedor[Ciudad] ?></span></td>
			        </tr>
				    <tr>
				      <td><strong>Email</strong></td>
				      <td colspan="3"><span id="EmailProveedor"><?php echo $datos_proveedor[Email] ?></span></td>
			        </tr>
			      </tbody>
				  </table></td>
			</tr>
			<tr class=row2>
				<td>Reingrese Password :</td>
				<td><input type=password size=25 class=input  id=psw name=RePasswd maxlength="20" value=""></td>
			</tr>
			<tr class=row2>
				<td>Autorizado</td>
				<td>
					<?php  echo formradiogroup(array("Si"=>"S","No"=>"N"),$r->Autorizado, "Autorizado"); ?>
				</td>
			</tr>
           
			<tr class=row2>
				<td>Nivel</td>
				<td>
                	 <?php
					if($r->Nivel <> 0   )
					{
					?>
                    <select name=Nivel>
						<?php foreach($array_nivel as $k => $N)<?php 								
						<option value="<?php echo $k;?>" <?php if($r->Nivel==$k) echo "selected";;?>><?php echo $N;?></option>
						<?php };?>
					</select>
                    <?php
					}//end if
					else
					{
					?>
                    	<input type="hidden" value="0" name="Nivel" />
                    <?php	
					}//end else
								
					?>
				</td>
			</tr>
			
            <tr class=row2>
			<td> Cedula </td><td><input type=text size=25 class=input   name=Cedula id=Cedula value="<?php echo $r->Cedula ?>"> </td>
			</tr>
			<tr class=row2>
			<td> Nombre </td><td><input type=text size=25 class=input   name=Nombre id=Nombre value="<?php echo $r->Nombre ?>"> </td>
			</tr>
			<tr class=row2>
			<td> Apellidos </td><td><input type=text size=25 class=input   name=Apellidos id=Apellidos value="<?php echo $r->Apellidos ?>"> </td>
			</tr>
			<tr class=row2>
			<td>Cargo</td><td><?php echo formpopup("Cargo","Cargo","Cargo","IDCargo",$r->IDCargo,"input\" id=\"Cargo"); ?>  </td>
			</tr>
			<tr class=row2>
			<td> Salario </td><td><input type=text size=25 class=input   name=Salario id=Salario value=""> </td>
			</tr>
			<tr class=row2>
			<td>Comision</td><td><?php echo formpopup("Comision","Porcentaje","Porcentaje","IDComision",$r->IDComision,"input\" id=\"Comision"); ?></td>
			</tr>
			<?php
			if($r->Publicar == 'S')
			{
			?>
			<tr class=row2>
			<td>Punto de Venta</td><td><?php echo formpopup("PuntoVenta","Nombre","Nombre","IDPuntoVenta",$r->IDPuntoVenta,"input\" id=\"PuntoVenta"); ?></td>
			</tr>
			<?php
			}
			?>
			<tr class=row2>
			<td> CodigoVendedor </td><td><input type=text size=25 class=input   name=CodigoVendedor id=CodigoVendedor value="<?php echo $r->CodigoVendedor ?>"> </td>
			</tr>
			<tr class=row2>
			<td> Publicar </td><td><?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></td>
			</tr>
			<tr>
			<td colspan=2 align=center class=row2><input type=hidden name=IDEmpleado id=IDEmpleado value="<?php echo $r->IDEmpleado ?>"><input type=hidden name=UsuarioTrCr value="<?php echo $r->UsuarioTrCr ?>">
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
	 	$sql =  "SELECT * FROM $Table WHERE 1 ORDER BY $Key";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=50;
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
				<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
				<a href="./?mod=<?php echo $MOD;?>">Administrar <?php echo $TitleMod;?></a> </td>
				<td><a href="./?mod=<?php echo $MOD;?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
			</tr>
		</table>
		<?php
				if($rows > 0){
		?>		
		<br>
		<table  cellpadding=0 cellspacing=0 align=center class=bordertable>
			<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
			<?php filtrar();?>	
			<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php echo $info;;?></td>
		</tr>
			<tr>
				<td class=texto bgcolor=#DBEAF5 colspan=12 nowrap>
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
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Usuario</a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;<?php if($_GET['order_by']=="User")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Nombre&nbsp;<?php if($_GET['order_by']=="Nombre")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Apellidos&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Apellidos&nbsp;<?php if($_GET['order_by']=="Apellidos")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDCargo&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Cargo&nbsp;<?php if($_GET['order_by']=="IDCargo")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDComision&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">IDComision&nbsp;<?php if($_GET['order_by']=="IDComision")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Publicar&nbsp;<?php if($_GET['order_by']=="Publicar")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

						<?php while($r = db_fetch_object($result)){
						?>
						  	
						<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
							</td>
						<td nowrap class=row1><?php echo $r->User ?></td>
						<td nowrap class=row1><?php echo $r->Nombre ?></td>
						<td nowrap class=row1><?php echo $r->Apellidos ?></td>
						<td nowrap class=row1><?php echo get_field("Cargo","Cargo","IDCargo",$r->IDCargo) ?></td>
						<td nowrap class=row1><?php echo get_field("Comision","Porcentaje","IDComision",$r->IDComision)."%" ?></td>
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
					<option value="Nombre">Nombre</option>
					<option value="Apellidos">Apellidos</option>
					<option value="Cargo.Cargo">Cargo</option>
					<option value="Salario">Salario</option>
					<option value="CodigoVendedor">CodigoVendedor</option>
					<option value="Publicar">Publicar</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				Entre <input type=text readonly size=10 class=input name=limit1>
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")	
					//-->
				</script>
				 y <input type=text size=10 readonly class=input name=limit2> 
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				</script>
				<br>
				ordenar por 
				<select name="order_by" class="popup">
					<option value="Nombre">Nombre</option>
					<option value="Apellidos">Apellidos</option>
					<option value="Cargo.Cargo">Cargo</option>
					<option value="Salario">Salario</option>
					<option value="CodigoVendedor">CodigoVendedor</option>
					<option value="Publicar">Publicar</option>
				</select> 
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
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Cargo">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
?>
