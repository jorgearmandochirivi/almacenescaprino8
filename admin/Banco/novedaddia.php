<body> <?php 

$TitleMod ="Novedades Bancos";

$Table = "NovedadBanco";
$TableJoin = "";
$Key = "IDNovedadBanco";
$MOD = "NovedadBancoDia";
$m = "Banco";


$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
	echo $action;
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Crear Novedad");
			break;
			
			case "insert" :
				
				$frm= vars_LOG($HTTP_POST_VARS);
				
				//print_r($frm);
				
				foreach( $frm['Banco'] as $idbanco => $valor )
				{
				
					$idpuntoventabanco = get_field("PuntoVentaBanco","IDPuntoVentaBanco","IDBanco",$idbanco."' AND IDPuntoVenta = '$IDPuntoVenta");
					
					$idnovedadbanco = get_maxID("NovedadBanco","IDNovedadBanco");
					
					$valorbanco = $frm['valor'][$idbanco];
					
					$sqlinsert = "INSERT INTO NovedadBanco VALUES ('$idnovedadbanco','$idpuntoventabanco','$Fecha','$valorbanco','$frm[UsuarioTrCr]','$frm[FechaTrCr]','$frm[UsuarioTrEd]','$frm[FechaTrEd]') ";
					db_query( $sqlinsert );
					
				}
				
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);
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
			if((int)$HTTP_GET_VARS['IDFormaPago']>0)
				$sql=str_replace("NovedadBanco.IDPuntoVentaBanco = PuntoVentaBanco.IDPuntoVentaBanco","NovedadBanco.IDPuntoVentaBanco = PuntoVentaBanco.IDPuntoVentaBanco AND PuntoVentaBanco.IDFormaPago = '".$HTTP_GET_VARS['IDFormaPago']."'",$sql);
			
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

	GLOBAL $TitleMod,$Table,$MOD,$Key,$IDPuntoVenta,$Fecha;
	
	$qid = db_query(" SELECT * FROM PuntoVentaBanco WHERE IDPuntoVenta = '$IDPuntoVenta' ");

?>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td></td>
		</tr>
</table>
<br>
<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>
	
<table cellpadding=1 cellspacing=0 class=bordertable align=center width=500>
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<?php 
	//INFORMACION DEL DIA QUE SE SELECCIONA 
	?>
	<tr>
		<td align=center class=row2>
			<br>
		</td>
	</tr>
	
	
	<?php 
	//INFORMACION DE TOTAL VENDIDO EL DIA QUE SE SELECCIONA
	?>
	<tr>
		<td align=center class=row2>
			<table width=500 border=0 cellspacing=1 cellpadding=1 class=bordertable bgcolor="ffffff">
				<?php 
					
					$Fechainicio = $Fecha." "."00:00:00";
					$Fechafin = $Fecha." "."23:59:59";
					
					$sql_factura = "SELECT sum(ValorTotal) as Total 
									FROM Factura 
									WHERE FechaFactura >= '$Fechainicio' AND FechaFactura <= '$Fechafin' 
									AND IDPuntoVenta = '$IDPuntoVenta'";
					
					$query_factura = db_query($sql_factura);
					
					while( $r_factura = db_fetch_object( $query_factura ) )
					{
						
				?>
				<tr>
					<td width="30%" align="right" class=rowtable><b>Total Vendido</b></td>
					<td align="left" class=rowtablesoft>
						<?php echo "$ ".number_format( $r_factura->Total )?>
					</td>
				</tr>
				<?php 		
					
					}//end while( $r_factura = db_fetch_object( $query_factura ) )
				?>
			</table>
		</td>
	</tr>
	<?php 
	//INFORMACION DE TOTAL VENDIDO EL DIA QUE SE SELECCIONA
	?>
	
	<tr>
		<td align=center class=row2>
			<table width=500 border=0 cellspacing=1 cellpadding=1 class=bordertable bgcolor="ffffff">
				<tr>
					<td width="30%" align="center"><b>Banco</b></td>
					<td align="center">
						<b>Valor</b>
					</td>
				</tr>
				<?php 
					$sql_novedad = "SELECT NB.* 
									FROM NovedadBanco NB, PuntoVentaBanco PB 
									WHERE NB.Fecha = '$Fecha' AND NB.IDPuntoVentaBanco = PB.IDPuntoVentaBanco
									AND PB.IDPuntoVenta = '$IDPuntoVenta'";
					
					$query_novedad = db_query($sql_novedad);
					
					while( $r_novedad = db_fetch_object( $query_novedad ) )
					{
						
				?>
				<tr>
					<td width="30%" align="center" class=rowtable><?php echo get_field("Banco","Nombre","IDBanco",get_field("PuntoVentaBanco","IDBanco","IDPuntoVentaBanco",$r_novedad->IDPuntoVentaBanco))?></td>
					<td align="center" class=rowtable>
						<?php echo "$ ".number_format( $r_novedad->Valor )?>
					</td>
				</tr>
				<?php 		
					
					}//end while( $r_novedad = db_fetch_object( $query_novedad ) )
				?>
			</table>
		</td>
	</tr>
	<tr>
		<td align=center class=row2>
			<br>
		</td>
	</tr>
	<?php 
	//INFORMACION DEL DIA QUE SE SELECCIONA 
	?>
	
	<tr>
		<td class="row1">
			<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
				<tr>
					<td width="30%" align="center"><b>Banco</b></td>
					<td align="center">
						<b>Valor</b>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php 
	while( $r = db_fetch_object( $qid ) )
	{
	?>
	<tr>
		<td>
			<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
				<tr class=row2>
					<td width="30%"><?php echo get_field( "Banco","Nombre","IDBanco", $r->IDBanco )?></td>
					<td>
						<input type=text class=input name="valor[<?php echo $r->IDBanco?>]" value="">
						<input type=hidden name="Banco[<?php echo $r->IDBanco?>]" value="<?php echo $r->IDBanco?>">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php 
	}//end while( $r = db_fetch_object( $qid ) )
	?>
	
	<tr>
		<td class="row1" align="center">
			<input type="hidden" name="Fecha" value="<?php echo $Fecha?>">
			<input type="hidden" name="IDPuntoVenta" value="<?php echo $IDPuntoVenta?>">
			<input type="hidden" name="action" value="<?php echo $newmode?>">
			<input type="submit" name="submit" class="input" value="<?php echo $submit_caption?>">
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
	 	$sql =  "SELECT * FROM $Table ORDER BY Fecha DESC";

		
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
?>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td></td>
	</tr>
</table>

<br><br>

<table cellspacing='1' cellpadding='2' border='0' align='center' width='500' class='bordertable'>
	<form name="frm1" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data">
	<tr>
		<td class="titlemedium" colspan=2>Crear Novedad		</td>
	</tr>
	<tr>
		<td class="rowtable">
			Punto de Venta
		</td>
		<td class="row1">
			<?php echo formpopup("PuntoVenta","Nombre","Nombre","IDPuntoVenta",$r->IDPuntoVenta,"input\" id=\"IDPuntoVenta"); ?>
		</td>
	</tr>
	
	<tr>
		<td class="rowtable">
			Fecha
		</td>
		<td class="row1">
		
			<input type="text" class="input" name="Fecha" size="19" value='<?php echo fecha()?>' readonly>
			<script language="JavaScript1.2">
				<!--
					if (!document.layers)
						document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm1.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
				//-->
			</script>
		
		</td>
	</tr>
	
	<tr>
		<td class="row1" colspan="2" align="center">
			<input type="hidden" name="action" value="add">
			<input type="submit" class="input" name="submit" value="Crear Novedad">
		</td>
	</tr>
	</form>
</table>
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
			<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
		</tr>
		<tr>
			<td class=texto bgcolor=#DBEAF5 nowrap>
			<?php 
				print $pages;
			?>
			</td>
		</tr>
		<tr>
			<td>
			<table width=100% border=0 cellspacing=1 cellpadding=0>
				<tr>
						<td class=rowform nowrap bgcolor=#DBEAF5>Punto de Venta</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Banco</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Forma de Pago</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fecha</td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Valor</td>
					</tr>
				<?php while($r = db_fetch_object($result)){
				?>
  				<tr>
						<td nowrap class=row1><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",get_field("PuntoVentaBanco","IDPuntoVenta","IDPuntoVentaBanco",$r->IDPuntoVentaBanco))?></td>
						<td nowrap class=row1><?php echo get_field("Banco","Nombre","IDBanco",get_field("PuntoVentaBanco","IDBanco","IDPuntoVentaBanco",$r->IDPuntoVentaBanco))?></td>
						<td nowrap class=row1><?php echo get_field("FormaPago","Descripcion","IDFormapago",get_field("PuntoVentaBanco","IDFormaPago","IDPuntoVentaBanco",$r->IDPuntoVentaBanco))?></td>
						<td nowrap class=row1><?php echo formatofecha( $r->Fecha )?></td>
						<td align=center valign=middle nowrap width=60 class=row2><?php echo "$ ".number_format($r->Valor) ?>&nbsp;&nbsp;</td>
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
	<form name="frm" action="./" method="get" >
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="Banco.Nombre">Banco</option>
					<option value="PuntoVenta.Nombre">PuntoVenta</option>					
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				Forma de pago:
				<?php echo formpopup("FormaPago","Descripcion","Descripcion","IDFormaPago",$r->IDFormaPago,"input\" id=\"Forms Pago"); ?>
				<br>Entre <input type=text readonly size=10 class=input name=limit1>
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
					<option value="Banco.Nombre">Banco</option>
					<option value="PuntoVenta.Nombre">PuntoVenta</option>
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
					<option value="30" selected>30</option>
				</select> 
				<br>
				<input type="hidden" name="mod" value="<?php echo $MOD?>">
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="PuntoVentaBanco">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 		
	}//End function filtrar
?>
