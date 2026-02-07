<body> <?

$TitleMod ="Alianza";

$Table = "Alianza";
$TableJoin = "";
$Key = "IDAlianza";
$MOD = "Alianza";
$m = "Alianza";


$permisos = get_permiso($ID_Usuario,$m,$Table);

/*
$sql_tipo_referencia = "SELECT * FROM TipoReferencia WHERE Publicar = 'S' ";
$qry_tipo_referencia = db_query($sql_tipo_referencia);
while($r_tipo_referencia = db_fetch_object($qry_tipo_referencia))
{
	$sql_inser= "INSERT INTO TipoReferenciaAlianza (IDAlianza,IDTipoReferencia) values('46','$r_tipo_referencia->IDTipoReferencia')";
	db_query($sql_inser);
}
exit;
*/



function table_check_list_desc($Table,$Key,$key_value,$table_option,$key_option,$table_reference,$check_name,$newmode,$condicion=""){

	$str_qry = "SELECT $key_option FROM $table_reference WHERE $Key = $key_value ";
	
	if($newmode <> "insert")
		$qry_option = db_query($str_qry);
	
	$option_checked = array();
	
	while($option = db_fetch_object($qry_option))
		$option_checked[] = $option->$key_option;
	
		
	$qry = db_query("SELECT * FROM $table_option WHERE 1 ".$condicion);
	
	$array_option = array();
	
	while ($option = db_fetch_object($qry)){
		if(!empty($option->Nombre))
			$clave=$option->Nombre;
		else
			$clave=$option->Descripcion;

		$array_option[$clave] = $option->$key_option;
	}
		

	
	echo formcheckgroup($array_option,$option_checked,$check_name);
	
}


if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;

			case "insert" :
				$frm= vars_LOG($_POST);
				$id = insert($frm);

				if(isset($PuntoVenta))
				foreach ($PuntoVenta as $IDPuntoVenta)
				{
					$idpuntoventaalianza = get_maxID("PuntoVentaAlianza","IDPuntoVentaAlianza");
					$qry_PuntoVentaAlianza = db_query("INSERT INTO PuntoVentaAlianza values('$idpuntoventaalianza', '$id','$IDPuntoVenta')");
				}

				if(isset($TipoReferencia))
				foreach ($TipoReferencia as $IDTipoReferencia)
				{
					$idtiporefalianza = get_maxID("TipoReferenciaAlianza","IDTipoReferenciaAlianza");
					$qry_TipoReferenciaAlianza = db_query("INSERT INTO TipoReferenciaAlianza values('$idtiporefalianza', '$id','$IDTipoReferencia')");
				}
				
				

				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($_POST);

				$frm["FechaInicio"]=$frm["limit1"];
				$frm["FechaFin"]=$frm["limit2"];
				
					if(isset($PuntoVenta)):
					$qry_PuntoVentaAlianza = db_query("DELETE FROm PuntoVentaAlianza Where IDAlianza = '".$frm[ID]."'");
					foreach ($PuntoVenta as $IDPuntoVenta)
					{
						$idpuntoventaalianza = get_maxID("PuntoVentaAlianza","IDPuntoVentaAlianza");
						$qry_PuntoVentaAlianza = db_query("INSERT INTO PuntoVentaAlianza values('$idpuntoventaalianza', '$id','$IDPuntoVenta')");
					}
				endif;

				if(isset($TipoReferencia)):
					$qry_TipoReferenciaAlianza = db_query("DELETE FROm TipoReferenciaAlianza Where IDAlianza = '".$frm[ID]."'");
					foreach ($TipoReferencia as $IDTipoReferencia)
					{
						$idtiporefalianza = get_maxID("TipoReferenciaAlianza","IDTipoReferenciaAlianza");
						$qry_PuntoVentaAlianza = db_query("INSERT INTO TipoReferenciaAlianza values('$idtiporefalianza', '$id','$IDTipoReferencia')");
					}
				endif;


				update($frm);
				print_form($frm['ID'],"update","Actualizar $TitleMod","Realizar Cambios");

		break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$_GET[action]="";
				$qry_PuntoVentaAlianza = db_query("DELETE FROm PuntoVentaAlianza Where IDAlianza = '".$ID."'");
				delete($ID);
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
var Check = new Array('IDCiudad','Nombre','Direccion','Telefono','Publicar');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
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
		<td class=maintitle bgcolor=#9daac6>&nbsp;<? echo $TitleMod ?> <? echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
			<td> Nombre </td><td><input type=text size=25 class=input   name=Nombre id=Nombre value="<?=$r->Nombre ?>"> </td>
			</tr>
			<tr class=row2>
			<td> Descuento otorgado</td><td><input type="number" size=25 class=input   name=Descuento id=Descuento value="<?=$r->Descuento ?>">
			  % </td>
			</tr>
			<tr class=row2>
			  <td>Aplica descuento para</td>
			  <td><? echo formradiogroup(array('Solo  para  l&iacute;nea'=>'L','Todas  las   referencias'=>'T'),$r->TipoProducto, 'TipoProducto'); ?></td>
		  </tr>
			<tr class=row2>
			  <td>Aplica para referidos</td>
			  <td><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AplicaReferido, 'AplicaReferido'); ?></td>
		  </tr>
			<tr class=row2>
			  <td>Numero Referidos Efectivos</td>
			  <td><input type="number" size=25 class=input   name=NumeroReferido id=NumeroReferido value="<?=$r->NumeroReferido ?>"></td>
		  </tr>
		  	<tr class=row2>
				<td> Aplica solo para fidelizados? </td><td><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->SoloFidelizados, 'SoloFidelizados'); ?></td>
			</tr>
			<tr class=row2>
				<td> Excluir proveedores y referencias especiales? 
					ZH, ZQ, ZC, ZWP, COPL70CF, COPL70NE, CORE60CF, CORE60NE, CORE70CF, CORE70NE, CORE80CF, CORE80NE, CREMACM*, CREMACN*, OW28****, OW95****, RAPQ, TARJETA, ZSE1****, ZSE2****, ZSE3****, ZSP1COMI, ZSP1CONE
				</td><td><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->ExcluirProveedores, 'ExcluirProveedores'); ?>
				</td>
			</tr>
			<tr class=row2>
				<td> Fecha Inicio
				</td>
				<td>
				<input type=text readonly size=10 class=input name=limit1 value="<?php if($r->FechaInicio!="0000-00-00") echo $r->FechaInicio; ?>">
					<script language='JavaScript1.2'>
								<!--
								if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
								//-->
					</script>
				</td>
			</tr>
			<tr class=row2>
				<td> Fecha Fin
				</td>
				<td>
				<input type=text size=10 readonly class=input name=limit2 value="<?php if($r->FechaFin!="0000-00-00") echo $r->FechaFin; ?>">
				<script language='JavaScript1.2'>
								<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
								//-->
					</script>
				</td>
			</tr>

			<tr class=row2>
			  <td>M&iacute;nimo de productos que debe tener la factura para aplicar la alianza</td>
			  <td><input type="number" size=25 class=input   name=MinimoProducto id=MinimoProducto value="<?=$r->MinimoProducto ?>"></td>
		  </tr>


			<tr class=row2>
			<td> Activo </td><td><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Activo, 'Activo'); ?></td>
			</tr>
			<tr class=row2>
			  <td >Puntos de venta que aplica</td>
			  <td> Tipo de Referencia a la que aplica:</td>
		  </tr>
			<tr class=row2>
			  <td >
                <?php
                    //table_check_list($Table,$Key,$r->$Key,"PuntoVenta","IDPuntoVenta","PuntoVentaAlianza","PuntoVenta[]",$newmode, " and PuntoVenta.Publicar = 'S' ");
					$condicion = " and Publicar = 'S' ";									
					table_check_list_desc($Table,$Key,$r->$Key,"PuntoVenta","IDPuntoVenta","PuntoVentaAlianza","PuntoVenta[]",$newmode, $condicion);
									
                ?>

              </td>
			  <td valign="top">
				<?php
				//table_check_list_desc($Table,$Key,$r->$Key,"TipoReferencia","IDTipoReferencia","TipoReferenciaAlianza","TipoReferencia[]",$newmode, " and Publicar = 'S' and IDTipoReferencia not in (16,17,22,24) ");
				table_check_list_desc($Table,$Key,$r->$Key,"TipoReferencia","IDTipoReferencia","TipoReferenciaAlianza","TipoReferencia[]",$newmode, " and Publicar = 'S' ");
				?>
				

			  </td>
		  </tr>

			<tr>
			<td colspan=2 align=center class=row2>
            	<input type=hidden name=IDAlianza id=IDAlianza value="<?=$r->IDAlianza ?>">
                <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=ID value="<? echo $r->$Key ?>">
				<input type=hidden name=action value=<?=$newmode?>>
				<input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			</td>
			</tr>
			<tr>
			  <td colspan=2 align=center class=row2>&nbsp;</td>
		  </tr>
			</table>
		</td>
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
<table width=500  cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <? echo $TitleMod ?></b></td>
		</tr>
<?filtrar();?>
<tr>
			<td class=titlemedium  bgcolor=#9daac6><% echo $info;%></td>
		</tr>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=12 nowrap>
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
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; %>'>Nombre
	      <% if($_GET['order_by']=="Nombre"){%><img src="images/<%=$img%>" border=0><%}%></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDCiudad&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Descuento&nbsp;
	      <% if($_GET['order_by']=="IDCiudad"){%><img src="images/<%=$img%>" border=0><%}%></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Aplica Para</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">Activo&nbsp;
                            <% if($_GET['order_by']=="Nombre"){%><img src="images/<%=$img%>" border=0><%}%></a> </td>
<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<? while($r = db_fetch_object($result)){
?>

<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<? echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class=row1><? echo $r->Nombre ?></td>
						<td nowrap class=row1><? echo $r->Descuento ?>%</td>
						<td nowrap class=row1><?
						switch($r->TipoProducto):
							case "L":
								echo "Solo para l&iacute;nea";
							break;
							case "T":
								echo "Todas las referencias";
							break;

						endswitch;
						  ?></td>
						<td nowrap class=row1><? echo $r->Activo ?></td>
		  <td align=center valign=middle nowrap width=60 class=row2>
		    &nbsp;&nbsp;<a href='<? echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>
</td>
					</tr>
<? } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=6 nowrap>
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
					<option value="Nombre">Nombre</option>
				</select>
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post">
				ordenar por
				<select name="order_by" class="popup">
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
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?
	}//End function filtrar
?>
