<body>

<?php
$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "InventarioCon";
$m="Referencia";
$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		
		
} // End switch
else{
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");
	exit;
}	
?>


<?php
		
		require( $libdir."dhabiles.inc.php" );
		
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
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$FechaDesde, $FechaHasta,$IDPuntoVenta;
 //require( "Reportes/Calc.php" );
 //$Calendario = new Date_Calc;
  
  ?>
	
	<table width="100%">
		
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">
							
								Desde	<input  type="text" name="FechaDesde" class="input" value="<?php echo $FechaDesde?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="nav">
								
								Hasta	<input  type="text" name="FechaHasta" class="input" value="<?php echo $FechaHasta?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
							<td  align='left' valign='middle' class="nav"><img src='images/house.png' border='0'  alt=''></td>
							<td align="left" valign="middle" class="nav">Puntos de Venta	
								<select name="IDPuntoVenta" onchange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><?php 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select> <input type="hidden" name="mod" value="ReporteCreditoAbonos"><input type="hidden" name="action" value="view"></td>
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
		if( !empty( $FechaDesde ) && !empty( $FechaHasta ) ){
		?>
		<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp; <br>
				<br>
				<!--<a href="exportar/exporttventas.php?IDPuntoVenta=<?php echo $IDPuntoVenta?>&FechaDesde=<?php echo $FechaDesde?>&FechaHasta=<?php echo $FechaHasta?>">Exportar Archivo</a>-->
				<br>
				<br>
				<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							
						<?php
						echo "CREDITOS ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta )." ".$FechaDesde." - ".$FechaHasta;
						?>
					</td>
				</tr>
				<?php
					
										
					 
					 /********************* TRAER DATOS DE VENTAS CON TARJETAS DE CREDITO Y DEBITO 'ID'S MAYOR QUE 2'*********************/
					if( !empty( $IDPuntoVenta ) )
						$condicion = " C.IDPuntoVenta = '$IDPuntoVenta' AND ";
					
					$sql_facturas = " SELECT C.*,CC.*,  DATE_FORMAT( C.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF,  DATE_FORMAT( CC.FechaPago,'%Y-%m-%d' ) as FechaPago
											FROM Credito C, CreditoCuota CC
											WHERE $condicion C.IDFactura = CC.IDFactura AND C.IDPuntoVenta = CC.IDPuntoVenta
											AND CC.FechaPago BETWEEN '$FechaDesde' AND '$FechaHasta' 
											ORDER BY CC.FechaPago DESC, C.IDPuntoVenta";
											
					$qry_facturas = db_query( $sql_facturas );
					
					//Puntos de Venta
					$sql_puntos = " SELECT IDPuntoVenta, Nombre FROM PuntoVenta ";
					$qry_puntos = db_query( $sql_puntos );
					while( $r_puntos = db_fetch_array( $qry_puntos ) )
						$array_puntos[ $r_puntos[ IDPuntoVenta ] ] = $r_puntos[ Nombre ];
					
										
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
										<td class="titlemedium" nowrap>Fecha</td>
										<td class="titlemedium" align="center" nowrap>Numero Credito</td>
										<td class="titlemedium" nowrap>Cedula</td>
										<td class="titlemedium" nowrap>Cliente</td>
							<td class="titlemedium" align="center" nowrap>Punto de Venta</td>
							<td class="titlemedium" align="center" nowrap>Numero Factura</td>
							<td class="titlemedium" align="center" nowrap>Valor Total</td>
										<td class="titlemedium" align="center" nowrap>Fecha de Cuota</td>
										<td class="titlemedium" align="center" nowrap>Fecha Pago</td>
									</tr>
						<?php
						
						//print_r( $array_fechas );
						
						/************************* MOSTRAR LAS VENTAS CON TARJETA DE CREDITO Y DEBITO ********************************/

						while( $r_facturas = db_fetch_array( $qry_facturas ) )
						{
							
							$class = repetition()?"row2":"row1";
							
							$cliente = array( );
							$coutas = array( );
							$candeladas = 0;
							$mostrar = 0;
							$fechaproximo = "";
							$pendientes = 0;
							//SELECT CLIENTE
							$sql_cliente = "SELECT IDCliente, Cedula, Nombre, Apellido FROM Cliente WHERE IDCliente = '$r_facturas[IDCliente]'";
							$qry_cliente = db_query( $sql_cliente );
							$cliente = db_fetch_array( $qry_cliente );
						?>
								<tr>
										<td class="<?php echo $class?>" align="center" nowrap><?php echo $r_facturas[FechaFacturaF]?></td>
										<td class="<?php echo $class?>" align="right" nowrap><?php echo $r_facturas[NumeroDocumento]?></td>
										<td class="<?php echo $class?>" align="center" nowrap><?php echo $cliente[Cedula] ?></td>
										<td class="<?php echo $class?>" align="center" nowrap><?php echo $cliente[Nombre]." ".$cliente[Apellido] ?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo $array_puntos[$r_facturas[IDPuntoVenta]]?> </td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo $r_facturas[NumeroFactura]?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $r_facturas[ValorTotal],2 ); $tValorTotal += $r_facturas[ValorTotal];?></td>
										<td class="<?php echo $class?>" align="right" nowrap>
										<?php	
											echo $r_facturas[FechaCuota];
										?>
									 </td>
										<td class="<?php echo $class?>" align="right" nowrap><?php	
											echo $r_facturas[FechaPago];
											
										?></td>
									</tr>
									<?php
						}//end for
						
						/****************************** FIN DE MOSTRAR LAS VENTAS CON TARJETA DE CREDITO Y DEBITO ********************************/
						?>
								
								<tr>
										<td class="titlemedium" nowrap></td>
										<td class="titlemedium" align="center" nowrap></td>
										<td class="titlemedium" nowrap></td>
										<td class="titlemedium" nowrap></td>
							<td class="titlemedium" align="center" nowrap></td>
							<td class="titlemedium" align="center" nowrap></td>
							<td class="titlemedium" align="center" nowrap><?php echo number_format( $tValorTotal,2 )?></td>
										<td class="titlemedium" align="center" nowrap></td>
										<td class="titlemedium" align="center" nowrap></td>
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
