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
$TitleMod ="Tarjetas Regalo";

$Table = "TarjetaPunto";
$TableJoin = "";
$Key = "CodigoTarjeta";
$MOD = "TarjetaRegalo";
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
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :

					$frm= vars_LOG($_POST);

					$sql_tarjeta="UPDATE TarjetaPunto set Estado = '".$frm["Estado"]."', Descripcion= '".$frm["Descripcion"]."' Where CodigoTarjeta = '".$frm[CodigoTarjeta]."' Limit 1 ";
					$qry_libera_tarjeta=db_query($sql_tarjeta);
					window_alert("Modificacion realizada con exito ");
					print_form($frm[CodigoTarjeta],"update","Actualizar $TitleMod","Realizar Cambios");

			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
			break;

			case "delcedulas" :
			break;

			case "list" :


			if ($_GET[field]=="PuntoVenta"){
				$sql="Select TP.* FROM TarjetaPunto TP, PuntoVenta PV Where TP.IDPuntoVenta = PV.IDPuntoVenta and PV.Nombre like '%".$_GET[QryString]."%' ";

			}
			if ($_GET[field]=="CodigoTarjeta"){
				$sql="Select TP.* FROM TarjetaPunto TP Where TP.CodigoTarjeta = '".$_GET[QryString]."' ";
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
						  <td><input type="text" name="CodigoTarjeta" id="Codigo" title="Codigo Tarjeta" class="tbox" value="<?php echo $r->CodigoTarjeta ?>"></td>
		  </tr>
		  <tr class=row2>
						  <td>Punto de venta</td>
						  <td><?php echo formpopup("PuntoVenta","Nombre","Nombre","IDPuntoVenta",$r->IDPuntoVenta,"input\" id=\"PuntoVenta"); ?></td>
		  </tr>
		  <tr class=row2>
						  <td>Descripci&oacute;n</td>
						  <td><textarea name="Descripcion" id="Descripcion"><?php echo $r->Descripcion; ?></textarea></td>
		  </tr>
						<tr class=row2>
						  <td>Estado</td>
						  <td><span class="col2"><?php echo formradiogroup(array('Vendida'=>'V','Disponible'=>'D','Anulado'=>'A','Obsequio'=>'O'),$r->Estado, 'Estado'); ?></span></td>
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
<td nowrap class=row1><?php echo $r->CodigoTarjeta ?></td>
<td nowrap class=row1><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta);  ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
                        <span class="row1">
							<?php 
							if ($r->Estado=="V")
								echo "Vendida";
							elseif($r->Estado=="D")
								echo "Disponible";
								if ($r->Estado=="O")
									echo "Obsequio";
								elseif($r->Estado=="A")
									echo "Anulado";
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
					<option value="CodigoTarjeta">Codigo Tarjeta</option>
					<option value="PuntoVenta">Punto de Venta</option>
				</select>
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post">
				ordenar por
				<select name="order_by" class="popup">
					<option value="CodigoTarjeta" selected>Codigo Tarjeta</option>
					<option value="PuntoVenta">Punto de Venta</option>
				</select>
				<br>
				de forma
				<select name="in_order" class="popup">
					<option value="ASC">Ascendente</option>
					<option value="DESC" selected>Descendente</option>
				</select>
				Listar
				<select name="listar" class="popup">
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="60" selected>60</option>
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
