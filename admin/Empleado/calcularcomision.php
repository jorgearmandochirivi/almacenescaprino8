<body> <?

$TitleMod ="Comision Empleados";

$Table = "ComisionEmpleado";
$TableJoin = "ComisionEmpleado";
$Key = "IDComisionEmpleado";
$MOD = "CalcularComision";
$m="Empleado";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
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

	GLOBAL $TitleMod,$Table,$MOD,$Key,$IDPuntoVenta,$FechaInicio,$FechaFinal;
	
	db_query("SET AUTOCOMMIT = 0");
	$sql_empleados = "SELECT * FROM Empleado WHERE Publicar = 'S' ";
	$query_empleados = db_query( $sql_empleados );
	
	$FechaInicio = $FechaInicio." "."00:00:00";
		
	$FechaFinal = $FechaFinal." "."23:59:59";
	
	while( $r_empleados = db_fetch_object( $query_empleados ) )
	{
	
		$Administrador = "N";  //Variable para saber si el empleado es administrador o no
		$TotalIVA = 0; //Total del iva en el periodo
		$tarjetas = 0; //Variable para el total en tarjetas
		$contado = 0; //Variable para el total en cheque o efectivo
			
		//Verificar si el empleado es administrador de algun punto de venta
		$sql_administra = " SELECT * FROM PuntoVenta WHERE IDEmpleado = '$r_empleados->IDEmpleado' ";
		$query_administra = db_query( $sql_administra );
		
		if( db_num_rows( $query_administra ) > 0 )
		{
		
			$r_administra = db_fetch_object( $query_administra );
			
			//Coonsultamos el total de ventas del punto de venta
			
			$sql_facturapunto = "SELECT * 
						FROM Factura 
						WHERE FechaFactura >= '$FechaInicio' AND FechaFactura <= '$FechaFinal' 
						AND IDPuntoVenta = '$r_administra->IDPuntoVenta'";
		
			$query_facturapunto = db_query($sql_facturapunto);
			
			while($r_facturaspunto = db_fetch_object( $query_facturapunto ))
			{
				$TotalIVA = $TotalIVA + $r_facturaspunto->ValorIVA;
				
				//Buscar lo que se pago en tarjeta de credito o debito paa descontar la comision
				$sql_formapago = "SELECT * FROM FormaPagoFactura WHERE IDFactura = '$r_facturaspunto->IDFactura'";
				$query_formapago = db_query($sql_formapago);
				while( $r_formapago = db_fetch_object( $query_formapago ) )
				{
					
					/**************Forma de Pago*************/
					/*	Efectivo = 1						*/
					/*	Cheque = 2							*/
					/**************Forma de Pago*************/
					
					if( $r_formapago->IDFormaPago > 2 ) // Si no es ni efectivo ni cheque
					{
					
						$comision = get_field( "PuntoVentaBanco","Comision","IDFormaPago", $r_formapago->IDFormaPago." AND IDPuntoVenta = ".$r_facturaspunto->IDPuntoVenta );
						
						$tarjetas = $tarjetas + ( $r_formapago->Valor - ( $r_formapago->Valor * ( $comision / 100 ) ) );
					
					}//end if( $r_formapago > 2 )
					else
					{
					
						$contado = $contado + $r_formapago->Valor;
					
					}//end else
					
				}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
				
			}//end while($r_facturaspunto = db_fetch_object( $query_facturapunto ))
		
		    
		    $TotalVentasPunto = $tarjetas + $contado - $TotalIVA ;
			
			$porcentajepunto = get_field( "Comision","Porcentaje","IDComision",  $r_administra->IDComision );
			
			$ValorComisionPunto =  $TotalVentasPunto * ( $porcentajepunto / 100 );
			
			$Administrador = "S";
		
		}//end if( db_num_rows( $query_administra ) > 0 )
		
		else
		{
		
			//Porcentaje de comision que corresponde en el periodo
			$porcentaje = get_field( "Comision","Porcentaje","IDComision", $r_empleados->IDComision );
			
			//Consultar las ventas del empleado en el periodo
			
			$sql_factura = "SELECT * 
							FROM Factura 
							WHERE FechaFactura >= '$FechaInicio' AND FechaFactura <= '$FechaFinal' 
							AND IDEmpleado = '$r_empleados->IDEmpleado'";
			
			$query_factura = db_query($sql_factura);
			
			while( $r_facturas = db_fetch_object( $query_factura ) )
			{
				$TotalIVA = $TotalIVA + $r_facturas->ValorIVA;
				
				//Buscar lo que se pago en tarjeta de credito o debito paa descontar la comision
				$sql_formapago = "SELECT * FROM FormaPagoFactura WHERE IDFactura = '$r_facturas->IDFactura'";
				$query_formapago = db_query($sql_formapago);
				while( $r_formapago = db_fetch_object( $query_formapago ) )
				{
					
					/**************Forma de Pago*************/
					/*	Efectivo = 1						*/
					/*	Cheque = 2							*/
					/**************Forma de Pago*************/
					
					if( $r_formapago->IDFormaPago > 2 ) // Si no es ni efectivo ni cheque
					{
					
						$comision = get_field( "PuntoVentaBanco","Comision","IDFormaPago", $r_formapago->IDFormaPago." AND IDPuntoVenta = ".$r_facturas->IDPuntoVenta );
						$tarjetas = $tarjetas + ( $r_formapago->Valor - ( $r_formapago->Valor * ( $comision / 100 ) ) );
					
					}//end if( $r_formapago > 2 )
					else
					{
					
						$contado = $contado + $r_formapago->Valor;
					
					}//end else
					
				}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
				
			}//end  while( $r_facturas = db_fetch_object( $query_factura ) )
			
			
			$TotalVentas = $tarjetas + $contado - $TotalIVA ;
			$ValorComision = $TotalVentas * ( $porcentaje / 100 );			
			
		
		}//end else
		
		if( ( $ValorComisionPunto > 0 ) || ( $ValorComision > 0 ) )
		{	
			//Actualizamos la tabla de comisiones para el historico
			
			$IDComisionempleado = get_maxID("ComisionEmpleado","IDComisionEmpleado"); 
			
			$sql_insert = "INSERT INTO ComisionEmpleado VALUES ('$IDComisionempleado','$r_empleados->IDEmpleado','$Administrador',
							NOW(),'$FechaInicio','$FechaFinal','$TotalVentas','$porcentaje','$ValorComision','$TotalVentasPunto',
							'$porcentajepunto','$ValorComisionPunto')";
	
			db_query( $sql_insert );
		
		}//end if( ( $ValorComisionPunto > 0 ) || ( $ValorComision > 0 ) )
		
					
	}//end while( $r_empleados = db_fetch_object( $query_empleados ) )

	
	db_query("COMMIT");
	
	echo "<script>location.href='?mod=CalcularComision'</script>";

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
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
		<td></td>
	</tr>
</table>

<br><br>

<table cellspacing='1' cellpadding='2' border='0' align='center' width='700' class='bordertable'>
	<form name="frmcalcular" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data">
	<tr>
		<td class="titlemedium" colspan=2>Calcular Comisi&oacute;n		</td>
	</tr>
	
	
	<tr>
		<td class="rowtable">Desde		</td>
		<td class="row1">
		
			<input type="text" class="input" name="FechaInicio" size="19" value='<?=fecha()?>' readonly>
			<script language="JavaScript1.2">
				<!--
					if (!document.layers)
						document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmcalcular.FechaInicio,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
				//-->
			</script>
		
		</td>
	</tr>
			<tr>
				<td class="rowtable">Hasta</td>
				<td class="row1"><input type="text" class="input" name="FechaFinal" size="19" value='<?=fecha()?>' readonly>

					<script language="JavaScript1.2">
				<!--
					if (!document.layers)
						document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmcalcular.FechaFinal,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
				//-->
			</script>
				</td>
			</tr>
			<tr>
		<td class="row1" colspan="2" align="center">
			<input type="hidden" name="action" value="add">
			<input type="submit" class="input" name="submit" value="Calcular Comisi&oacute;n">
		</td>
	</tr>
	</form>
</table>

<?
		if($rows > 0){
?>		
<br>
<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
		<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <? echo $TitleMod ?></b></td>
		</tr>
		<?filtrar();?>	
		<tr>
			<td class=titlemedium  bgcolor=#9daac6><% echo $info;%></td>
		</tr>
		<tr>
			<td class=texto bgcolor=#DBEAF5 nowrap>
			<?
				print $pages;
			?>
			</td>
		</tr>
		<tr>
			<td>
			<table width=100% border=0 cellspacing=1 cellpadding=0>
				<tr>
						<td class=rowform align=center nowrap bgcolor=#DBEAF5>Empleado</td>
						<td class=rowform align=center nowrap bgcolor=#DBEAF5>Fecha</td>
					<td align=center class=rowform nowrap bgcolor=#DBEAF5>Desde</td>
						<td align=center  class=rowform valign=middle nowrap bgcolor=#DBEAF5 width=69>Hasta</td>
						<td align=center class=rowform valign=middle nowrap bgcolor=#DBEAF5 width=69>TotalVendido</td>
						<td align=center class=rowform valign=middle nowrap bgcolor=#DBEAF5 width=69>Porcentaje</td>
						<td align=center class=rowform valign=middle nowrap bgcolor=#DBEAF5 width=69>Valor Comisi&oacute;n</td>
						<td align=center class=rowform valign=middle nowrap bgcolor=#DBEAF5 width=69>Total Vendido Punto</td>
						<td align=center class=rowform valign=middle nowrap bgcolor=#DBEAF5 width=69>Porcentaje Punto</td>
						<td align=center class=rowform valign=middle nowrap bgcolor=#DBEAF5 width=69>Valor Comision Punto</td>
						<td align=center class=rowform valign=middle nowrap bgcolor=#DBEAF5 width=69>Total Comisi&oacute;n</td>
					</tr>
				<? while($r = db_fetch_object($result)){
				?>
  				<tr>
						<td nowrap class=row1><? echo get_field("Empleado","CONCAT(Nombre,' ',Apellidos)","IDEmpleado",$r->IDEmpleado)?></td>
						<td nowrap class=row1><? echo $r->Fecha?></td>
					<td nowrap class=row1><? echo formatofecha( $r->FechaInicio )?></td>
						<td align=center valign=middle nowrap width=60 class=row1>&nbsp;<? echo formatofecha( $r->FechaFin )?>&nbsp;</td>
						<td align=center valign=middle nowrap width=60 class=row1><? echo number_format( $r->TotalVendido )?></td>
						<td align=center valign=middle nowrap width=60 class=row1><? echo $r->Porcentaje ?></td>
						<td align=center valign=middle nowrap width=60 class=row1><? echo number_format( $r->ValorComision )?></td>
						<td align=center valign=middle nowrap width=60 class=row1><? echo number_format( $r->TotalVendidoPunto )?></td>
						<td align=center valign=middle nowrap width=60 class=row1><? echo $r->PorcentajePunto?></td>
						<td align=center valign=middle nowrap width=60 class=row1><? echo number_format( $r->ValorComisionPunto )?></td>
						<td align=center valign=middle nowrap width=60 class=row1><? echo number_format( $r->ValorComisionPunto + $r->ValorComision )?></td>
					</tr>
				<? } // END for
				?>
				<tr>
						<td class=texto bgcolor=#DBEAF5 colspan=11 nowrap>
						<?
							print $pages;
							?>
					</td>
					</tr>		
			</table>
		</td>
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
					<option value="Empleado.Apellidos">Apellidos</option>
					<option value="Empleado.Nombre">Nombre</option>
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
				
				ordenar por 
				<select name="order_by" class="popup">
					<option value="Empleado.Apellidos">Apellidos</option>
					<option value="Empleado.Nombre">Nombre</option>
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
				<input type="hidden" name="rangofield" value="FechaInicio">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Empleado">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?		
	}//End function filtrar
?>