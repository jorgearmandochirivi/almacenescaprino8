<body><?php
$MOD = "diariocredito";
		switch ($action) {
			
			case "view" :
				print_from($IDPuntoVenta,$Fecha);
			break;
			
			default :
				print_from("");
			break;
		
		} // End switch


 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($IDPuntoVenta="", $Fecha=""){
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$IDPuntoVenta,$MOD,$dirroot;
 
 if( empty( $Fecha ) )
 	$Fecha = fecha( );
 
?>
	
	<table width="100%">
		
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td class="col1" valign="middle"><img src="admin/images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="col2">Fecha	<input readonly type="text" name="Fecha" class="input" value="<?=fecha()?>">

								<script language="JavaScript1.2">
							<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
							//-->
						</script>
							</td>
							<td class="col1"  align='left' valign='middle' class="nav"><img src="admin/images/house.png" border='0'  alt=''></td>
							<td align="left" valign="middle" class="col2"> <input type="hidden" value="<?=$IDPuntoVenta?>" name=IDPuntoVenta>
							<input type="hidden" name="mod" value="<?=$MOD?>"><input type="hidden" name="action" value="view"></td>
							<td align="left" valign="middle" class="col2">
								<input type="submit" value="Ver Reporte" name="submit" class="submit">
							</td>
						</tr>
				</form>
			</table>
	
		</td>
		</tr>
		
		<br>
		<br>
		<?php
		if(!empty($IDPuntoVenta)){
		?>
		<tr>
		<td>
			<br>
			<a href="?mod=pagos&Fecha=<?=$Fecha?>">Ver Consignaciones</a>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
			| <a href="javascript:void();" onClick="window.open('Reportes/PRDiarioCredito.php?Fecha=<?=$Fecha?>','','width=426, height=350')">Imprimir</a>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
			<br><br>
			<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="100%">
		
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
			</td>
			<td class="tbtbot"><b></b>
				<span class="gen">
					Reporte Ventas Diarias Almacen : <?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>&nbsp; &nbsp; Fecha: <?=formatofecha( $Fecha )?>
				</span>
			</td>
			<td class="tbtr">
				<img src="images/spacer.gif" alt="" width="124" height="22" />
			</td>
		</tr>
	</table>
	<?php
	$filedir = $dirroot."files/";
	ob_start();	
	?>
	<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#FFFFFF">	
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
				<tr>
					<td class='mainbg'><br><br>
					<table width="100%" border="0" cellspacing="1" cellpadding="1">	
						<tr>
							<td class="maintitle" valign="middle">&nbsp; 
									
								Reporte Creditos : <?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>&nbsp; &nbsp; Fecha: <?=formatofecha( $Fecha )?>
							</td>
						</tr>
						
						<tr>
							<td  valign="middle">&nbsp; 
									
								<table width="100%" >
							
							<tr>
								<td class="titlemedium" align="center" nowrap>Numero Factura</td>
								<td class="titlemedium" align="center" nowrap>Almacen Emite Credito</td>
								<td class="titlemedium" align="center" nowrap>Recibo Abono  No</td>
								<td class="titlemedium" align="center" colspan="2" nowrap>Cuota Abono</td>
								<td class="titlemedium" align="center" nowrap>Cuotas Pendientes</td>
								<td class="titlemedium" align="right" nowrap>Fecha de Cuota</td>
								<td class="titlemedium" align="right" nowrap>Fecha de Pago</td>
								<td class="titlemedium" align="right" nowrap>Valor</td>
							</tr>
						<?php
						//$sql_credito = "SELECT * FROM CreditoCuota WHERE DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND IDPuntoVentaPago = '$IDPuntoVenta'";
						$sql_credito = "SELECT * FROM CreditoCuota WHERE DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND (IDPuntoVentaPago = '$IDPuntoVenta' or ( IDPuntoVenta = '$IDPuntoVenta' and IDPuntoVentaPago = 0 )) ";
						
						$qry_credito = db_query( $sql_credito );
						while( $r_credito = db_fetch_object( $qry_credito ) )
						{
							$class = repetition()?"row2":"row1";
						?>
							<tr>
								<td class="<?=$class?>" align="center" nowrap><?=$r_credito->NumeroFactura ?></td>
								<td class="<?=$class?>" align="center" nowrap><?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_credito->IDPuntoVenta) ?></td>
								<td class="<?=$class?>" align="center" nowrap><?=$r_credito->Consecutivo ?></td>
								<td class="<?=$class?>" align="center" colspan="2" nowrap><?=$r_credito->IDCuota?></td>
								<td class="<?=$class?>" align="center" nowrap>
								<?php
									$sql_cuotas = " SELECT count(*) as numero FROM CreditoCuota WHERE IDFactura = '".$r_credito->IDFactura."' AND IDPuntoVenta = '$r_credito->IDPuntoVenta' AND FechaPago = '0000-00-00 00:00:00' ";
									$qry_cuotas = db_query( $sql_cuotas );
									$r_cuotas = db_fetch_object( $qry_cuotas );
									echo $r_cuotas->numero;
									?>
								</td>
								<td class="<?=$class?>" align="right" nowrap><?=$r_credito->FechaCuota?></td>
								<td class="<?=$class?>" align="right" nowrap><?=$r_credito->FechaPago?></td>
								<td class="<?=$class?>" align="right" nowrap><?=number_format( $r_credito->ValorTotal , 2); $ValorTotal += $r_credito->ValorTotal?></td>
							</tr>
						<?php	
						}//ebd while
						?>	
							<tr>
								<td class="titlemedium" align="center" nowrap></td>
								<td class="titlemedium" align="center" nowrap></td>
								<td class="titlemedium" align="center" nowrap></td>
								<td class="titlemedium" align="center" colspan="2" nowrap></td>
								<td class="titlemedium" align="center" nowrap></td>
								<td class="titlemedium" align="right" nowrap></td>
								<td class="titlemedium" align="right" nowrap>Total</td>
								<td class="titlemedium" align="right" nowrap><?=number_format( $ValorTotal, 2 )?></td>
							</tr>
							
							</table>
						</td>
						</tr>	
						
						
							
					</table>
				</td>
			</tr>
		</form>
	
		</table>
		<?php
		$page = ob_get_contents();
		$fecha = date( "Y-m-d H:i:s" );
		$name = "DiarioVentas$fecha.xls";
		$file = $filedir.$name;
		
		$fw = fopen($file, "w");
		fputs($fw,$page,strlen($page));
		fclose($fw);

		$name = "DiarioVentas$fecha.sxc";
		$file = $filedir.$name;
		
		$fw = fopen($file, "w");
		fputs($fw,$page,strlen($page));
		fclose($fw);
		ob_end_clean();
		
		//header_export($file);
		echo $page;
		?>
	</td>
	</tr>
	<?php 
	 } // END if(!empty($IDEmpresa))
	?>
	</table>
	<?php						
}// Enf function print()	

?>
</body>

