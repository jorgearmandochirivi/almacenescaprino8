<body><?php


$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "InventarioCon";
$m="Referencia";
$permisos = get_permiso($ID_Usuario,$m,$Table);
	
	if($permisos[0] >= 2)
{
		switch ($action) {
			
			case "view" :
				print_from($IDPuntoVenta,$Fecha);
			break;
			
			default :
				print_from("");
			break;
		
		} // End switch
}
else{
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");
	exit;
}

		

 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($IDPuntoVenta="", $Fecha=""){
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA, $FechaHasta;
 
?>
	
	<table width="100%">
		
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">Fecha	<input readonly type="text" name="Fecha" class="input" value="<?php echo fecha()?>">

								<script language="JavaScript1.2">
							<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
							//-->
						</script>
								  Hasta Fecha <input readonly type="text" name="FechaHasta" class="input" value="<?php echo $FechaHasta?>">

								<script language="JavaScript1.2">
							<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
							//-->
						</script>
							</td>
							<td  align='left' valign='middle' class="nav"><img src='images/house.png' border='0'  alt=''></td>
							<td align="left" valign="middle" class="nav">Puntos de Venta	<select name="IDPuntoVenta" onchange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><?php 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select> <input type="hidden" name="mod" value="MensualCreditos"><input type="hidden" name="action" value="view"></td>
							<td align="left" valign="middle" class="nav">
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
		<td><br>
				<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							
						Reporte Creditos  Almacen : 
					    <?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>&nbsp; &nbsp; Fecha: <?php echo formatofecha( $Fecha )?>
						 - <?php echo formatofecha( $FechaHasta )?>
					</td>
				</tr>
				<?php
					$sql_facturas = "SELECT * 
									FROM CreditoCuota 
									WHERE DATE_FORMAT( FechaPago,'%Y-%c-%d' ) >= DATE_FORMAT('$Fecha','%Y-%c-%d' )  AND DATE_FORMAT( FechaPago,'%Y-%c-%d' ) <= DATE_FORMAT('$FechaHasta','%Y-%c-%d' )
									AND IDPuntoVentaPago = '$IDPuntoVenta'";
					
					$qry_facturas = db_query( $sql_facturas );
					
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
										<td class="titlemedium" align="center" nowrap>Numero Factura</td>
										<td class="titlemedium" align="center" nowrap>Recibo Abono Cr&eacute;dito No</td>
										<td class="titlemedium" align="center" nowrap>Cuota</td>
										<td class="titlemedium" align="center" nowrap>Cuotas Pendientes</td>
										<td class="titlemedium" align="center" nowrap>Fecha de Cuota</td>
							<td class="titlemedium" align="center" nowrap>Fecha de Pago</td>
							<td class="titlemedium" align="center" nowrap>Valor</td>
						</tr>
						<?php
						while( $r_credito = db_fetch_object( $qry_facturas ) )
						{
							$r_facturas[$i] = $array_factura;
							$i++;
							//echo $array_factura[IDFactura]."<br>";
							$class = repetition()?"row2":"row1";
							//print_r($valor);
						?>
						<tr>
							<td class="<?php echo $class?>" align="center" nowrap><?php echo $r_credito->NumeroFactura ?></td>
							<td class="<?php echo $class?>" align="right" nowrap><?php echo $r_credito->Consecutivo ?></td>
							<td class="<?php echo $class?>" align="right" nowrap><?php echo $r_credito->IDCuota?></td>
							<td class="<?php echo $class?>" align="right" nowrap><?php
									$sql_cuotas = " SELECT count(*) as numero FROM CreditoCuota WHERE IDFactura = '".$r_credito->IDFactura."' AND IDPuntoVenta = '$r_credito->IDPuntoVenta' AND FechaPago = '0000-00-00 00:00:00' ";
									$qry_cuotas = db_query( $sql_cuotas );
									$r_cuotas = db_fetch_object( $qry_cuotas );
									echo $r_cuotas->numero;
									?></td>
							<td class="<?php echo $class?>" align="right" nowrap><?php echo $r_credito->FechaCuota?></td>
							<td class="<?php echo $class?>" align="right" nowrap><?php echo $r_credito->FechaPago?></td>
							<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $r_credito->ValorTotal , 2); $ValorTotal += $r_credito->ValorTotal?></td>
						</tr>
						
						<?php
						}//end foreach( $r_facturas as $key => $valor )
						?>
							
						<tr>
							<td class="titlemedium" align="right" nowrap></td>
							<td class="titlemedium" align="right" nowrap>&nbsp;</td>
							<td class="titlemedium" align="right" nowrap>&nbsp;</td>
							<td class="titlemedium" align="right" nowrap>&nbsp;</td>
							<td class="titlemedium" align="right" nowrap>&nbsp;</td>
							<td class="titlemedium" align="right" nowrap>&nbsp;</td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $ValorTotal , 2)?></td>
						</tr>
											
							
					</table>
				</td>
			</tr>
		</form>
	
		</table>
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