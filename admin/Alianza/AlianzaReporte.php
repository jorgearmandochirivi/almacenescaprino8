<body> <?php 

$TitleMod ="Alianza";

$Table = "Alianza";
$TableJoin = "Factura";
$Key = "IDAlianza";
$MOD = "AlianzaReporte";
$m = "Alianza";

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
			case "list" :	
				
				
				if (!empty($_GET[IDAlianza]))
					$condicion[] = "IDAlianza  = '".$_GET[IDAlianza]."'";
					
				if (!empty($_GET[IDPuntoVenta]))
					$condicion[] = "IDPuntoVenta  = '".$_GET[IDPuntoVenta]."'";
					
				if (!empty($_GET[limit1]) && !empty($_GET[limit2]))
					$condicion[] = "FechaFactura Between  '".$_GET[limit1]."' and '".$_GET[limit2]."' ";
					
				if (count($condicion)>0):
					$condicion_busqueda = implode(" and " , $condicion);
				else:
					$condicion_busqueda =" IDAlianza > 0 ";
				endif;
						
				$sql="Select * from Factura Where  " . $condicion_busqueda . "  Order by IDFactura DESC";
				
				$sql_resumen = "Select *, count(IDFactura) TotalFactura From Factura Where  " . $condicion_busqueda . " Group by IDPuntoVenta Order by IDFactura DESC";
				
					
				list_r($sql,$sql_resumen);
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
function print_form($id,$newmode,$title,$submit_caption){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $idpunto;

	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' AND IDPuntoVenta = '$idpunto' ");
		
	$r = db_fetch_object($qid);
	
	$club_suavidad=get_field("Cliente","ClubSuavidad","IDCliente",$r->IDCliente);
	
?>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td></td>
	</tr>
</table>

<br>
<br>
<script>
function eliminafactura( IDFactura, IDPuntoVenta )
{
	if( confirm( "Seguro que desea eliminar esta factura?" ) )
		window.open( 'Factura/eliminafactura.php?IDFactura='+IDFactura+'&IDPuntoVenta='+IDPuntoVenta,'','width=100, height=100' );
}
</script>
<?php 
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql="",$sql_resumen=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table Where IDAlianza > 0 ORDER BY Nombre DESC";
	
	if(empty($sql_resumen))
		$sql_resumen = "Select *, count(IDFactura) TotalFactura From Factura Where IDAlianza > 0 Group by IDAlianza,IDPuntoVenta Order by IDFactura DESC";
	

		
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=80;
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
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td></td>
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

<?php	if(!empty($sql_resumen)): ?>
<tr>
<td class=texto  colspan=16 nowrap>

	
    <table class=rowform valign=middle align="center" >
        <tr>
            <td colspan="3" class=titlemedium bgcolor=#9daac6>
                RESUMEN
            </td>
        </tr>
        <tr>
          <td bgcolor="#DBEAF5">Alianza</td>
          <td bgcolor="#DBEAF5">Punto Venta</td>
          <td bgcolor="#DBEAF5">Total Facturas</td>
      </tr>
      <?php 
        $query_resumen=db_query($sql_resumen);
        while($r_resumen = db_fetch_array($query_resumen)):?>
        <tr>
          <td class=row2><?php echo get_field("Alianza","Nombre","IDAlianza",$r_resumen[IDAlianza]); ?></td>
          <td class=row2><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_resumen[IDPuntoVenta]); ?></td>
          <td class=row2><?php echo $r_resumen[TotalFactura]; ?></td>
      </tr>
      <?php endwhile; ?>
    </table><br>
</td>
</tr>
 <?php endif; ?>
	


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
  <td colspan="7" align=left valign=middle bgcolor=#DBEAF5 class=rowform>
	  <a href="Alianza/exportaalianza.php?sql=<?php echo base64_encode($sql); ?>"><img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0" >Exportar Registros </a>
  </td>
  </tr>
<tr>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDCliente&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Cliente&nbsp;<?php  if($_GET['order_by']=="IDCliente"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NumeroDocumento&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Punto de Venta&nbsp;<?php  if($_GET['order_by']=="NumeroDocumento"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NumeroFactura&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">NumeroFactura&nbsp;<?php  if($_GET['order_by']=="NumeroFactura"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Numero de Fidelizacion</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=FechaFactura&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">FechaFactura&nbsp;<?php  if($_GET['order_by']=="FechaFactura"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=ValorTotal&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">ValorTotal&nbsp;<?php  if($_GET['order_by']=="ValorTotal"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=69 class=row2>
	&nbsp;<a href='<?php echo "?mod=Factura&action=edit&id="; echo $r->$Key; ?>&idpunto=<?php echo $r->IDPuntoVenta?>'><img src='images/edit.gif' border='0'></a></td>
						<td nowrap class=row1><?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente)." ".get_field("Cliente","Apellido","IDCliente",$r->IDCliente)?></td>
						<td nowrap class=row1><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta) ?></td>
						<td nowrap class=row1><?php echo $r->NumeroFactura ?></td>
						<td nowrap class=row1><?php echo $r->NumeroFideliazcion ?></td>
						<td nowrap class=row1><?php echo $r->FechaFactura ?></td>
						<td nowrap class=row1><?php echo $r->ValorTotal ?></td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=7 nowrap>
	<?php 
		print $pages;
		?></td>
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
			<td class="rowform" align="center" colspan=8>Alianza 
			  <select name="IDAlianza" id="IDAlianza" class="input seleccion_alianza">
			  <option value=""></option>
			  <?php 
				$sql_alianza = "Select * From Alianza  Where 1";
				$qry_alianza = db_query($sql_alianza);
				while($r_alianza = db_fetch_array($qry_alianza)): ?>
				<option class="<?php echo (int)$r_alianza[Descuento]; ?>"  value="<?php echo $r_alianza[IDAlianza] ?>"><?php echo $r_alianza[Nombre] . " - " . $r_alianza[Descuento] . "%";  ?></option>
			  <?php endwhile; ?>
			  </select>
				 Punto Venta <?php echo formpopup("PuntoVenta","Nombre","Nombre","IDPuntoVenta",$r->IDPuntoVenta,"input\" id=\"PuntoVenta"); ?><br>
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
				<br>
				<input type="hidden" name="mod" value="<?php echo $MOD?>">
				<input type="hidden" name="rangofield" value="FechaFactura">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Cliente">
				<input type="hidden" name="tlevel" value="PuntoVenta">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 		
	}//End function filtrar
?>
