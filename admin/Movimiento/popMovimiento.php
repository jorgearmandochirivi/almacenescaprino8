<?php
	include("../config.inc.php");
	Encabezado();
	$datos = Verifica_Sesion();
	//$Nombre_Usuario = $datos["Nombre"];
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
  ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title><?php echo $app_title;;?></title>
<link rel="stylesheet" href="../styles.css?1" type="text/css">
<link href="../default.css" rel="stylesheet" media="screen">
<script language="JavaScript1.2" src="../jscripts/popcalendar.js?1"></script>
<script language="JavaScript1.2" src="../jscripts/validaForm.js?1"></script>

</head>
<body onload="init();"> 
 <?php

$TitleMod ="Movimientos";

$Table = "Movimiento";
$TableJoin = "DetalleMovimiento";
$Key = "IDMovimiento";
$MOD = "VerMovimiento";
$m = "Movimientos";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form($id,"insert","Actualizar $TitleMod","Realizar Movimiento");
			break;
			
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");
			break ;
			case "list" :	
				$sql = make_qry_string($HTTP_GET_VARS);
				list_r($sql);
			break;
			default : 
				if(empty($Orden))
					list_r();
				else
				{
					$sql = "SELECT * FROM OrdenCompra WHERE NumeroOrden = '$Orden'";
					list_r($sql);
				}
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");

/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption)
{
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario;
	
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	
	$r = db_fetch_object($qid);
?>
<script>
var Check = new Array('IDMovimiento','IDTipoMovimiento','Remision','FechaRemision','IDOrdenCompra','Fecha','IDempleado','Estado','Observaciones','Publicar','UsuarioTrCr','FechaTrCr','UsuarioTrEd','FechaTrEd');
</script>

	<br>
	<table width=590 cellpadding=0 cellspacing=0 align=center class=bordertable>
		<tr>
			<td class="maintitle" bgcolor="#9daac6"><b><?php echo $TitleMod ?></b></td>
		</tr>
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
						<tr>
							<td class="row1" nowrap>
								<table width=100% cellspacing="1" cellpadding="1" bgcolor=#ffffff>
										<tr>
										<td class=row1>Punto de Venta</td>
										<td class=row1><?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta)?></td>
										<td class=row1>Documento</td>
										<td class=row1><?=$r->Remision?></td>
									</tr>
										<tr>
										<td class=row1>Fecha Remisi&oacute;n</td>
										<td class=row1>
											<?php
											echo $r->Fecha;
											?>
										</td>
										<td class=row1>Tipo de Movimiento</td>
										<td class=row1><?php echo get_field("TipoMovimiento","NombreMovimiento","IDTipoMovimiento",$r->IDTipoMovimiento)?>
										</td>
									</tr>
									<tr>
										<td class=row1>Observaciones</td>
										<td colspan="3" class=row1><?=$r->Observaciones?></td>
									</tr>
									<tr>
										<td class=row1 colspan="4"></td>
									</tr>
									<tr>
										<td class=titlemedium colspan="4">Detalle Movimiento</td>
									</tr>
									<tr>
										<td class=row2 colspan="4">
											<?php verdetallemovimiento($r->IDMovimiento);?>
										</td>
									</tr>
									<tr>
										<td class=row1 colspan="4" align="center">
											<input type="hidden" name="action" value="<?=$newmode?>">
											<input type="hidden" name="ID" value="<?=$id?>">
											<input type="hidden" name="IDEmpleado" value="<?php if($newmode == "insert") echo $ID_Usuario; else echo  $r->IDEmpleado;?>">
											<input type="button" class="submit" name="submit" value="Seleccionar Movimiento" onclick="window.opener.selmovimiento( '<?=$r->IDMovimiento?>','<?=$r->Fecha?>' );window.close();">
										</td>
									</tr>
								</table>
							</td>
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
	 	$sql =  "SELECT * FROM Movimiento WHERE IDTipoMovimiento = '4' ORDER BY Fecha DESC ";
	 	
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

 		if($_GET['in_order']=="ASC" || $_GET['in_order']==""){
			$img="down.png";
			$order="DESC";
		}else if($_GET['in_order']=="DESC"){
			$img="up.png";
			$order="ASC";
		}
							
							?>
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
	<td class=texto bgcolor=#DBEAF5 colspan=11 nowrap>
	<?php
		print $pages;
	?>
	</td>
	</tr>
		<tr>
				<td>
				<table width=100% border=0 cellspacing=1 cellpadding=0>
					<tr>
					<td align=center class=rowform valign=middle nowrap bgcolor=#DBEAF5>Ver</td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDPuntoVenta&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Punto de Venta</a><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDPuntoVenta&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;<?php if($_GET['order_by']=="IDPuntoVenta	")<?php <img src="../images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoMovimiento&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Tipo Movimiento</a><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoMovimiento&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;<?php if($_GET['order_by']=="IDTipoMovimiento")<?php <img src="../images/<?php echo $img;?>" border=0><?php };?></a></td>
					<td class=rowform nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Fecha</a><a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">&nbsp;<?php if($_GET['order_by']=="Fecha")<?php <img src="../images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Remision&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Remision</a><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Remision&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'><?php if($_GET['order_by']=="Remision")<?php <img src="../images/<?php echo $img;?>" border=0><?php };?></a></td>
					</tr>
	
				<?php 
				while($r = db_fetch_object($result)){
				?>
	  	
					<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
							&nbsp;<a href='<?php echo "?mod=$MOD&action=add&id="; echo $r->$Key; ?>'  title="Crear Movimiento"><img src='../images/edit.gif' border='0'></a>
						</td>
						<td nowrap class=row1><?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta)?></td>
						<td nowrap class=row1><?php echo get_field("TipoMovimiento","NombreMovimiento","IDTipoMovimiento",$r->IDTipoMovimiento)?></td> <td nowrap class=row1><?php echo $r->Fecha?></td>
						<td nowrap class=row1><?php echo $r->Remision; ?></td>
					</tr>
				<?php } // END for
				?>
					<tr>
						<td class=texto bgcolor=#DBEAF5 colspan=5 nowrap>
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
	<form name="frm" action="./" method="get" onsubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="NumeroOrden">NumeroOrden</option>
					<option value="Remision">Remision</option>
					<option value="PuntoVenta.Nombre">Punto de Venta</option>
					<option value="TipoMovimiento.NombreMovimiento">Tipo Movimiento</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				Entre <input type=text readonly size=10 class=input name=limit1>
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=../jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")	
					//-->
				</script>
				 y <input type=text size=10 readonly class=input name=limit2> 
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=../jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				</script>
				<br>
				ordenar por 
				<select name="order_by" class="popup">
					<option value="NumeroOrden">NumeroOrden</option>
					<option value="Fecha">Fecha</option>
					<option value="Remision">Remision</option>
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
				<input type="hidden" name="mod" value="<?=$MOD?>">
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="PuntoVenta">
				<input type="hidden" name="tlevel" value="TipoMovimiento">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
	
/*******************************************************************************************
	verdetallemovimiento: Muestra el detalle de el Movimiento
	Parametros:
			$id : id del Pedido a Mostrar
	Retorna:	
			Void
*******************************************************************************************/

function verdetallemovimiento($id)
{
	
	Global $TitleMod,$MOD,$Table,$Key,$TableJoin;
	
	$sql_referencias =  "SELECT * FROM $TableJoin WHERE $Key = '$id' GROUP BY IDPuntoVentaReferencia";
	
	$query_referencias = db_query( $sql_referencias );
	
	$i=0;
?>
	<table width=80% cellpadding=1 cellspacing=1 class=text align=center bgcolor=#ffffff>
<?php
	while( $r_referencias = db_fetch_object( $query_referencias ) )
	{
		
		$sql_detalle =  "SELECT * FROM $TableJoin WHERE $Key = '$id' AND IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia'";
		$query_detalle = db_query($sql_detalle);
		$rows_detalle = db_num_rows($query_detalle);
		
		while($r_detalle[$i] = db_fetch_array($query_detalle))
		{
			$i++;
		} //end while($r[$i] = db_fetch_array($query_detalle))
		
		$i = 0;
		//print_r($r);
				
	?>
		
			<tr>
				<td class=rowform align=center>
				<?php
					echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$r_referencias->IDPuntoVentaReferencia));	
				?>
				</td>
				<?php
					foreach($r_detalle as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=rowform align=center>".get_field("Talla","Descripcion","IDTalla",$talla[IDTalla])."</td>";
					}//end foreach($r_detalle as $talla)
				?>
			</tr>
			
			
			
			<tr>
				<td class="row2" align=center>
					<b>CANTIDAD</b>
				</td>
				<?php
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						echo "<td class=row2 align=center>".$talla[Cantidad];
					}
				}
				?>
			</tr>
	<?php
	
	$r_detalle = array();
	$r_codificacion = array();
	
	}//end while( $r_referencias = db_fetch_object( $query_referencias ) )
	?>
	</table>
<?php
}// end function verdetallepedido($id)
?>
</body>
</html>