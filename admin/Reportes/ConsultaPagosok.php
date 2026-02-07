<body><?php
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
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA, $ReteIVA, $ReteICA, $ReteFuente, $FechaDesde, $FechaHasta;
 
 $sql_retefuente = "SELECT * FROM ReteFuente LIMIT 1";
	$query_retefuente = db_query( $sql_retefuente );
	$r_retefuente = db_fetch_object( $query_retefuente );
	
	$ReteFuente = $r_retefuente->Valor / 100;
 
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
							<td  align='left' valign='middle' class="nav"><img src="images/house.png" border='0'  alt=''></td>
							<td align="left" valign="middle" class="nav">Puntos de Venta	<select name="IDPuntoVenta" onchange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><?php 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select> <input type="hidden" name="mod" value="ConsultaPagos"><input type="hidden" name="action" value="view"></td>
							<td align="left" valign="middle" class="nav"><input type="submit" value="Ver Reporte" name="submit" class="submit"></td>
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
				&nbsp;&nbsp;&nbsp;&nbsp;  <img src="images/book_go.png" border="0" alt="">&nbsp; 
				<a href="./?mod=ReportePagos&Fecha=<?php echo $Fecha?>&IDPuntoVenta=<?php echo $IDPuntoVenta?>" class="menuppal">
					Ver informe formas de pago
				</a>
				<br>&nbsp; 
			<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							
						Reporte Ventas Diarias Almacen : <?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>&nbsp; &nbsp; Fecha: <?php echo formatofecha( $Fecha )?>
					</td>
				</tr>
				<?php
					//Seleccionar Formas de Pago
					$sql_formapago = "SELECT IDFormaPago, Descripcion FROM FormaPago ";
					$qry_formaPago = db_query( $sql_formapago );
					while( $r_formapago =  db_fetch_array( $qry_formaPago ) )
						$array_formapago[ $r_formapago[IDFormaPago] ] = $r_formapago;
						
					//Seleccionar Banco
					$sql_banco = "SELECT * FROM Banco ";
					$qry_banco = db_query( $sql_banco );
					while( $r_banco =  db_fetch_array( $qry_banco ) )
						$array_banco[ $r_banco[IDBanco] ] = $r_banco;
					
					//print_r( $array_banco );
					//Seleccionar Bancos
					
					$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura,
										DF.IVA, DF.IDDetalleFactura, DF.ReteIVA, DF.ReteICA, F.IDFactura, F.IDPuntoVenta, DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF 
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
										WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
										AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) >= DATE_FORMAT('$FechaDesde','%Y-%c-%d' ) 
										AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) <= DATE_FORMAT('$FechaHasta','%Y-%c-%d' ) 
										AND F.IDFactura = DF.IDFactura 
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio;";
											
					
					$qry_facturas = db_query( $sql_facturas );
					
					$i = 0;
					$formapago = array();
					
					while( $array_factura = db_fetch_array( $qry_facturas ) )
					{
						$r_facturas[$i] = $array_factura;
						$i++;
						
					}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )
										
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
										<td class="titlemedium" nowrap>No. Factura</td>
										<td class="titlemedium" nowrap>Fecha</td>
										<td class="titlemedium" align="center" nowrap>Referencia </td>
										<td class="titlemedium" align="center" nowrap>Vr. Unitario</td>
										<td class="titlemedium" align="center" nowrap>Pares</td>
										<td class="titlemedium" align="center" nowrap>Dto.</td>
										<td class="titlemedium" align="center" nowrap>Dto. Factura</td>
										<td class="titlemedium" align="center" nowrap>Dto. Par</td>
										<td class="titlemedium" align="center" nowrap>Pago</td>
										<td class="titlemedium" align="center" nowrap>Saldo</td>
										<td class="titlemedium" align="center" nowrap>Comision Bancos</td>
										<td class="titlemedium" align="center" nowrap>Vr. Parcial</td>
							<td class="titlemedium" align="center" nowrap>IVA</td>
										<td class="titlemedium" align="center" nowrap>Valor Bruto</td>
										<td class="titlemedium" align="center" nowrap>Forma de Pago</td>
										<td class="titlemedium" align="center" nowrap>Banco</td>
										<td class="titlemedium" align="center" nowrap>Rete fuente</td>
										<td class="titlemedium" align="center" nowrap>Rete IVA</td>
										<td class="titlemedium" align="center" nowrap>Rete ICA</td>
										<td class="titlemedium" align="center" nowrap>Ingreso</td>
									</tr>
						<?php
						foreach( $r_facturas as $key => $valor )
						{ 
							//print_r( $valor );
							$class = repetition()?"row2":"row1";
							//print_r($valor);
						?>
						<tr>
										<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['NumeroFactura']?></td>
										<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['FechaFacturaF']?></td>
										<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['Numero']?> </td>
										<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $ElValorUnitario = $valor['PrecioU'] / ( 1 - ( $valor['DescuentoRef'] / 100 ) ) ,2)?></td>
							<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['Cantidad']; $Pares += $valor['Cantidad'];?></td>
							<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['DescuentoRef']?></td>
										<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['DescuentoFactura']?> </td>
										<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['DescuentoPar']?></td>
										<td class="<?php echo $class?>" align="right" nowrap>
											<?php
												$TotalFactura = $valor[ValorTotal] ;
												if( $valor['DescuentoPar'] > 0 )
													$valordescuentopar = ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( $valor['DescuentoPar'] / 100 );
												else
													$valordescuentopar = 0;
												
												
												//consultar forma de pago pa saber si se le resta
												$sql_formasdepago = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' AND IDPuntoVenta = '$IDPuntoVenta' ";
												$qry_formasdepago = db_query( $sql_formasdepago );
												$saldo = 0;
												while( $r_formasdepago = db_fetch_object( $qry_formasdepago ) )
													if( $r_formasdepago->IDFormaPago == 13 ) //13 FormaPago Saldo
														$saldo = $r_formasdepago->Valor;
												
												if( $valor['DescuentoFactura'] == 0 )
												{
													$valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar ) ;
													//echo $valorparcial."-".$TotalFactura."--";
													$pago = $valorparcial - $saldo ;
													echo number_format( $pago ,2); $Pago += $pago;
												}
												else
												{
													//$valorparcial =  ( ( $valor['PrecioU'] * $valor['Cantidad'] ) + ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) - ( $valordescuentopar );
													$Precio =  $valor['PrecioU'] - $valordescuentopar;
													$valorparcial =  ( ( $Precio * $valor['Cantidad'] ) + ( ( $Precio * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) ;
													/* Se agrega pa las mayores */
													$mayortotal = $TotalFactura - $valorparcial;
													if( $mayortotal <> 0 )
													{
														$saldo = ( $valorparcial / $TotalFactura ) * $saldo ; //Que porcentaje del item es para el total														
														$pago = $valorparcial - $saldo ;
													}//end if
													else //Hasta aqui se agrega pa las mayores
														$pago = $valorparcial - $saldo ;
													echo number_format( $pago ,2); $Pago += $pago;
												}//end else
												
												//Traer Comision
												$pcomision = 0;
												$comision = 0;
												$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' AND IDPuntoVenta = '$valor[IDPuntoVenta]' ";
												$qry_comisiones = db_Query( $sql_comisiones );
												$array_forma_pago = array();
												while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
												{
													$pcomision = $r_comisiones->Comision / 100;
													$comision +=  ( $valorparcial / (1 + $IVA) ) * $pcomision;
													
													$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][IDFormaPago] = $r_comisiones->IDFormaPago;
													$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][Valor] = $r_comisiones->Valor;
													$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][Comision] = $r_comisiones->Comision;
													$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][IDBanco] = $r_comisiones->IDBanco;
												}
												
											?>
										</td>
										<td class="<?php echo $class?>" align="right" nowrap>
											<?php
												echo number_format( $saldo ,2); $Saldo += $saldo;
											?>
										</td>
										<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $comision  ,2 ); $ComisionBancos += $comision;?></td>
										<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $valorbruto = $valorparcial - $comision ,2); $ValorParcial += $valorbruto; ?></td>
							<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) ),2 );$ValorIVA += $valoriva; ?> </td>
							
							
							
							<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $valorparcial - $valoriva ,2 ); $ValorBruto += ( $valorparcial - $valoriva );?></td>
							<td class="<?php echo $class?>" align="right" nowrap><?php    echo $array_formapago[ $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][IDFormaPago] ][Descripcion] ;?></td>
							<td class="<?php echo $class?>" align="right" nowrap><?php  echo $array_banco[ $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][IDBanco] ][Nombre];?></td>
							<td class="<?php echo $class?>" align="right" nowrap>
								<?php
									$ValorReteICA = 0;
									$ValorReteIVA = 0;
									
									if( $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][IDFormaPago] <> 1 )
									{
										//echo $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][Valor];
										
										$Valor = $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][Valor];
										
										$ValorReteICA = ( $Valor / (1 + $IVA ) ) * $ReteICA;
										$ValorReteIVA = ( $Valor - ( $Valor / (1 + $IVA ) ) ) * $ReteIVA;
										//echo $ReteFuente;
										//echo "<br>";
										echo number_format( $ValorReteFuente = ( $Valor / ( 1 + $IVA ) )  * $ReteFuente , 2 ); 
										$valorretefuente += $ValorReteFuente; 
									}
								?>
							</td>
							<td class="<?php echo $class?>" align="right" nowrap>
							
							<?php
							echo number_format( $ValorReteIVA, 2 );
							?>
							</td>
							<td class="<?php echo $class?>" align="right" nowrap>
							<?php
							echo number_format( $ValorReteICA, 2 );
							?>
							</td>
							<td class="<?php echo $class?>" align="right" nowrap>
							<?php
							echo number_format(  $valorparcial  - ($ValorReteICA + $ValorReteIVA + $ValorReteFuente + $valoriva + $comision ), 2 );
							?>
							</td>
						</tr>
						
						<?php
						}//end foreach( $r_facturas as $key => $valor )
						?>
							
						<tr>
							<td class="titlemedium" colspan="4" align="right" nowrap>TOTALES</td>
							<td class="titlemedium" align="center" nowrap><?php echo $Pares ?></td>
							<td class="titlemedium" align="center" colspan="2" nowrap></td>
										<td class="titlemedium" align="right" nowrap></td>
										<td class="titlemedium" align="right" nowrap><?php echo number_format( $Pago , 2)?></td>
										<td class="titlemedium" align="right" nowrap><?php echo number_format( $Saldo , 2)?></td>
										<td class="titlemedium" align="right" nowrap><?php echo number_format( $ComisionBancos , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $ValorParcial , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $ValorIVA , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $ValorBruto , 2)?></td>
										<td class="titlemedium" align="right" nowrap></td>
										<td class="titlemedium" align="right" nowrap></td>
										<td class="titlemedium" align="right" nowrap></td>
										<td class="titlemedium" align="right" nowrap></td>
										<td class="titlemedium" align="right" nowrap></td>
										<td class="titlemedium" align="right" nowrap></td>
									</tr>
											
					</table>
					<br><br>
					<table width="100%" border="0" cellspacing="1" cellpadding="1">	
						<tr>
							<td class="maintitle" valign="middle">&nbsp; 
									
								Reporte Creditos : <?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>&nbsp; &nbsp; Fecha: <?php echo formatofecha( $Fecha )?>
							</td>
						</tr>
						
						<tr>
							<td  valign="middle">&nbsp; 
									
								<table width="100%" >
							
							<tr>
								<td class="titlemedium" align="center" nowrap>Numero Factura</td>
								<td class="titlemedium" align="center" colspan="2" nowrap>Cuota</td>
								<td class="titlemedium" align="right" nowrap>Fecha de Cuota</td>
								<td class="titlemedium" align="right" nowrap>Fecha de Pago</td>
								<td class="titlemedium" align="right" nowrap>Valor</td>
							</tr>
						<?php
						$sql_credito = "SELECT * FROM CreditoCuota WHERE DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND IDPuntoVenta = '$IDPuntoVenta'";
						$qry_credito = db_query( $sql_credito );
						while( $r_credito = db_fetch_object( $qry_credito ) )
						{
							$class = repetition()?"row2":"row1";
						?>
							<tr>
								<td class="<?php echo $class?>" align="center" nowrap><?php echo $r_credito->NumeroFactura ?></td>
								<td class="<?php echo $class?>" align="center" colspan="2" nowrap><?php echo $r_credito->IDCuota?></td>
								<td class="<?php echo $class?>" align="right" nowrap><?php echo $r_credito->FechaCuota?></td>
								<td class="<?php echo $class?>" align="right" nowrap><?php echo $r_credito->FechaPago?></td>
								<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $r_credito->ValorTotal , 2); $ValorTotal += $r_credito->ValorTotal?></td>
							</tr>
						<?php	
						}//ebd while
						?>	
							<tr>
								<td class="titlemedium" align="center" nowrap></td>
								<td class="titlemedium" align="center" colspan="2" nowrap></td>
								<td class="titlemedium" align="right" nowrap></td>
								<td class="titlemedium" align="right" nowrap>Total</td>
								<td class="titlemedium" align="right" nowrap><?php echo number_format( $ValorTotal, 2 )?></td>
							</tr>
							
							</table>
						</td>
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
