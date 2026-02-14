
<body><?php

$sql_actualiza_venta_empleado=db_query("INSERT IGNORE INTO VentasEmpleadoBck SELECT * FROM `VentasEmpleado` WHERE IDFactura >= 387606");
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
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$FechaDesde, $FechaHasta;
 //require( "Reportes/Calc.php" );
 //$Calendario = new Date_Calc;
?>
	
	<table width="100%">
		
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td class="col2" valign="middle"><img src="admin/images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="col2">
							
								Desde	<input readonly type="text" name="FechaDesde" class="input" value="<?=fecha()?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="col2">
								
								Hasta	<input readonly type="text" name="FechaHasta" class="input" value="<?=fecha()?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
								<input type="hidden" name="mod" value="vendedores"><input type="hidden" name="action" value="view"></td>
							<td  align='left' valign='middle' class="col2"></td>
							<td align="left" valign="middle" class="col2"></td>
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
		if(!empty( $FechaDesde ) && !empty( $FechaHasta ) ){
		?>
		<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
				<table width="100%" border="0" align="center" cellspacing="1" cellpadding="0" bgcolor="#345487">
					<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
						<tr>
							<td class="navpic" valign="middle">
								Reporte Ventas Empleados <b>Desde :</b> <?=formatofecha($FechaDesde)?>  <b>Hasta :</b> <?=formatofecha($FechaHasta)?> 
							</td>
						</tr>
						<?php 			
						$condicionpunto=" and F.IDPuntoVenta = '$IDPuntoVenta'";
						$qry_empleado = db_query( $sql_empleados = "SELECT IDEmpleado, CONCAT(Nombre,' ',Apellidos) as Nombre, Cedula FROM Empleado WHERE IDPuntoVenta = '$IDPuntoVenta'");
						$i = 0;
						while( $r_empleado = db_fetch_array( $qry_empleado ) )
						{
						
							$array_empleados[$i] = $r_empleado;
							
							/*
							$sql_ventasadmin = " SELECT VE.Venta as ValorTotal, VE.IDPuntoVenta, VE.Cargo , F.*, DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
												FROM VentasEmpleadoBck VE, Factura F
												WHERE VE.IDEmpleado = '".$r_empleado["IDEmpleado"]."' 
												AND FIND_IN_SET( 'Administrador', VE.Cargo) > 0 
												AND VE.IDFactura = F.IDFactura
												AND F.IDPuntoVenta = '$IDPuntoVenta' 
												AND F.IDPuntoVenta = VE.IDPuntoVenta 
												AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) >= DATE_FORMAT( '$FechaDesde', '%Y-%m-%d' )
												AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) <= DATE_FORMAT( '$FechaHasta', '%Y-%m-%d' )";												
							*/					
												
							 $sql_ventasadmin = " SELECT VE.Venta as ValorTotal, VE.IDPuntoVenta, VE.Cargo , F.*, DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
										FROM VentasEmpleadoBck VE, Factura F
										WHERE VE.IDEmpleado = '".$r_empleado["IDEmpleado"]."' 
										AND FIND_IN_SET( 'Administrador', VE.Cargo) > 0 
										AND VE.IDFactura = F.IDFactura
										AND F.IDPuntoVenta = VE.IDPuntoVenta 
										$condicionpunto
										AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) >= DATE_FORMAT( '$FechaDesde', '%Y-%m-%d' )
										AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) <= DATE_FORMAT( '$FechaHasta', '%Y-%m-%d' )";
												
												
							
							$qry_ventasadmin = db_query( $sql_ventasadmin );
							
							//echo db_num_rows( $qry_ventasadmin );
							
							$j = 0;
							while( $r_ventasadmin = db_fetch_array( $qry_ventasadmin ) )
							{

								
									
								$array_ventasadmin[$r_empleado["IDEmpleado"]][$r_ventasadmin["IDPuntoVenta"]]["ValorTotal"] += $r_ventasadmin["ValorTotal"];
								$array_ventasadmin[$r_empleado["IDEmpleado"]][$r_ventasadmin["IDPuntoVenta"]]["Cargo"] = $r_ventasadmin["Cargo"];
								$j++;
								
								
								
									//DETALLE FACTURA
									$sql_detalle = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.IDPuntoVenta, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, 
									P.Descuento, F.Descuento as DescuentoFactura ,DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
										FROM FacturaBck F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
											WHERE F.IDFactura = '".$r_ventasadmin["IDFactura"]."' 
											AND F.IDPuntoVenta = '".$r_ventasadmin["IDPuntoVenta"]."' 
										AND F.IDFactura = DF.IDFactura 
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio;";
										
										
									/*$sql_detalle = "SELECT DF.* FROM DetalleFactura DF WHERE DF.IDFactura = '$r_ventasadmin[IDFactura]' 
													AND DF.IDPuntoVenta = '$r_ventasadmin[IDPuntoVenta]'";*/
									$qry_detalle = db_query( $sql_detalle );
									$array_detalle_factura = array();
									while( $r_detalle = db_fetch_array( $qry_detalle ) )
										$array_detalle_factura[] = $r_detalle;
									
									foreach( $array_detalle_factura as $key => $valor )
									{
										//sif( $r_empleado[IDEmpleado] == 8 )
										//echo $r_ventasadmin[IDFactura]."<br>";
										//DESCUENTO PAR
										//comienzo pago
										if( $valor['DescuentoPar'] > 0 )
											$valordescuentopar = ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( $valor['DescuentoPar'] / 100 );
										else
											$valordescuentopar = 0;
										
										
										//consultar forma de pago pa saber si se le resta
											$sql_formasdepago = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '".$valor["IDFactura"]."' AND IDPuntoVenta = '$IDPuntoVenta' ";
										$qry_formasdepago = db_query( $sql_formasdepago );
										$saldo = 0;
										while( $r_formasdepago = db_fetch_object( $qry_formasdepago ) )
										{
											if( $r_formasdepago->IDFormaPago == 13 ) //13 FormaPago Saldo
											{
												$saldo = $r_formasdepago->Valor;  //saldo
											}//end if
										}//end whoile
										
										
										if( $valor['DescuentoFactura'] == 0 )
										{
											$valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar ) ;
											$pago = $valorparcial - $saldo ;  //PAGO
										}
										else
										{
											//$valorparcial =  ( ( $valor['PrecioU'] * $valor['Cantidad'] ) + ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) - ( $valordescuentopar );
											$Precio =  $valor['PrecioU'] - $valordescuentopar;
											$valorparcial =  ( ( $Precio * $valor['Cantidad'] ) + ( ( $Precio * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) ;
																	
											$pago = $valorparcial - $saldo ;  //PAGO
										}//end else
										//fin pago
										
										//Traer Comision
										$pcomision = 0;
										$comision = 0;
										$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '".$valor["IDFactura"]."' AND IDPuntoVenta = '".$valor["IDPuntoVenta"]."' ";
										
										//if( $r_empleado[IDEmpleado] == 23 )
										//	echo $sql_comisiones;
										
										$qry_comisiones = db_Query( $sql_comisiones );
										while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
										{
											$pcomision = $r_comisiones->Comision / 100;
											$comision +=  ( $valorparcial / (1 + $IVA) ) * $pcomision;
										}						//Comision
										
				
										//valor iva
										$valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) );
										
										//valor bruto
										$valorbruto = $valorparcial - $valoriva;
										//echo $comision."<br>";
										
										$ValorParcial[ $r_empleado["IDEmpleado"] ][ $valor["IDPuntoVenta"] ]['admin'] += $valorparcial  - $comision;
										$Comision[ $r_empleado["IDEmpleado"] ] += $comision;
										$ValorIVA[ $r_empleado["IDEmpleado"] ]  += $valoriva;
										$ValorBruto[ $r_empleado["IDEmpleado"] ] += $valorbruto;
										$Pago[ $r_empleado["IDEmpleado"] ] += $pago;
										
										
										/*
										$array_facturas[ $valor['FechaFacturaF'] ][ 'ValorParcial' ] += $valorparcial - $comision;
										$array_facturas[ $valor['FechaFacturaF'] ][ 'Pago' ] += $pago;
										$array_facturas[ $valor['FechaFacturaF'] ][ 'Saldo' ] += $saldo;
										$array_facturas[ $valor['FechaFacturaF'] ][ 'ValorBruto' ] += $valorbruto;
										$array_facturas[ $valor['FechaFacturaF'] ][ 'Comision' ] += $comision;
										$array_facturas[ $valor['FechaFacturaF'] ][ 'IVA' ] += $valoriva;
										*/
										$valorparcial = 0;
										$pago = 0;
										$saldo = 0;
										$valorbruto = 0;
										$comision = 0;
										$valoriva = 0;
										
									}//end for
								
								
							}	
							/**********************************************/
							/*******************Empleados******************/
							/**********************************************/
												
							 $sql_ventasvendedor = " SELECT VE.Venta as ValorTotal, VE.IDPuntoVenta, VE.Cargo , F.*, DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
												FROM VentasEmpleadoBck VE, Factura F
												WHERE VE.IDEmpleado = '".$r_empleado["IDEmpleado"]."' 
												AND FIND_IN_SET( 'Empleado', VE.Cargo) > 0 
												AND VE.IDFactura = F.IDFactura
												AND F.IDPuntoVenta = VE.IDPuntoVenta 
												$condicionpunto
												AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) >= DATE_FORMAT( '$FechaDesde', '%Y-%m-%d' )
												AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) <= DATE_FORMAT( '$FechaHasta', '%Y-%m-%d' )";
							
							$qry_ventasvendedor = db_query( $sql_ventasvendedor );
							$j = 0;
							while( $r_ventasvendedor = db_fetch_array( $qry_ventasvendedor ) )
							{
								//$array_ventasvendedor[$r_empleado[IDEmpleado]][$j] = $r_ventasvendedor;
								$array_ventasvendedor[$r_empleado["IDEmpleado"]][$r_ventasvendedor["IDPuntoVenta"]]["ValorTotal"] += $r_ventasvendedor["ValorTotal"];
								$array_ventasvendedor[$r_empleado["IDEmpleado"]][$r_ventasvendedor["IDPuntoVenta"]]["Cargo"] = $r_ventasvendedor["Cargo"];
								$j++;
								
								
									//DETALLE FACTURA
									$sql_detalle = "SELECT F.NumeroFactura,F.IDFactura, F.IDPuntoVenta, F.FechaFactura, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura ,DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
											WHERE F.IDFactura = '".$r_ventasvendedor["IDFactura"]."' 
											AND F.IDPuntoVenta = '".$r_ventasvendedor["IDPuntoVenta"]."' 
										AND F.IDFactura = DF.IDFactura 
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio;";
										
									$qry_detalle = db_query( $sql_detalle );
									$array_detalle_factura = array();
									while( $r_detalle = db_fetch_array( $qry_detalle ) )
										$array_detalle_factura[] = $r_detalle;
									
									foreach( $array_detalle_factura as $key => $valor )
									{
										//DESCUENTO PAR
										if( $valor['DescuentoPar'] > 0 )
											$valordescuentopar = ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( $valor['DescuentoPar'] / 100 );
									
										//VALOR PARCIAL
										if( $valor['DescuentoFactura'] == 0 )
											$valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar );
										else
										{
											$Precio =  $valor['PrecioU'] - $valordescuentopar;
											$valorparcial =  ( ( $Precio * $valor['Cantidad'] ) + ( ( $Precio * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) ;
										}//end else
										
										//Traer Comision
										$pcomision = 0;
										$comision = 0;
										$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '".$valor["IDFactura"]."' AND IDPuntoVenta = '".$valor["IDPuntoVenta"]."' ";
										$qry_comisiones = db_query( $sql_comisiones );
										while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
										{
											$pcomision = $r_comisiones->Comision / 100;
											$comision +=  ( $valorparcial / (1 + $IVA) ) * $pcomision;
										}//Comision
										
										//valor iva
										$valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) );
										
										//valor bruto
										$valorbruto = $valorparcial - $valoriva;
										
										
										$ValorParcial[ $r_empleado["IDEmpleado"] ][ $valor["IDPuntoVenta"] ]['empleado'] += $valorparcial - $comision;
										$Comision[ $r_empleado["IDEmpleado"] ] += $comision;
										$ValorIVA[ $r_empleado["IDEmpleado"] ]  += $valoriva;
										$ValorBruto[ $r_empleado["IDEmpleado"] ] += $valorbruto;
										
										$valorparcial = 0;
										$pago = 0;
										$saldo = 0;
										$valorbruto = 0;
										$comision = 0;
										$valoriva = 0;
										
									}//end for
								
								
							}//end while
							
							$i++;
						}//end while( $r_empleado = db_fetch_array( $qry_empleado ) )
						
						$qry_puntosventa = db_query( "SELECT IDPuntoVenta, Nombre FROM PuntoVenta" );
						while( $r_puntodeventa = db_fetch_array( $qry_puntosventa ) )
						{
							$array_punto[$r_puntodeventa['IDPuntoVenta']] = $r_puntodeventa['Nombre'];
						}//end while punto venta
				?>
						<tr>
							<td class="mainbg">
								<table width="100%" border="0" cellspacing="1" cellpadding="1">
									<tr>
										<td class="navpic" align="center" nowrap>Cedula</td>
										<td class="navpic" align="center" nowrap>Nombre</td>
										<td class="navpic" align="center" nowrap>Tipo</td>
										<td class="navpic" align="center" nowrap>Punto Venta</td>
										<td class="navpic" align="center" nowrap>Valor Parcial</td>
										<td class="navpic" align="center" nowrap>Valor Vendido</td>
									</tr>
				<?php
						//print_r($array_ventasadmin);
						
						/*
						foreach( $array_facturas as $fecha => $valor )
						{
							echo $fecha." ".$valor["ValorBruto"]."<br>";
							$totalbruto += $valor["ValorBruto"];
							
						}
						echo $totalbruto." TOTAL ";
						*/
						foreach( $array_empleados as $key => $valor )
						{ 
							$class = repetition()?"row2":"row1";
							
							foreach( $array_ventasadmin[$valor['IDEmpleado']] as $idpunto => $venta  )
								
							{
							
									
				?>
									<tr>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['Cedula']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['Nombre']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$venta['Cargo']?></td>
										<td class="<?=$class?>" align="center" nowrap><?php echo $array_punto[ $idpunto ];?></td>
										<td class="<?=$class?>" align="right" nowrap><?=number_format( $ValorParcial[$valor['IDEmpleado']][ $idpunto ]['admin'], 2 ); $totalb +=  $ValorParcial[$valor['IDEmpleado']][ $idpunto ]['admin']?></td>
										<td class="<?=$class?>" align="right" nowrap><?=number_format( $venta['ValorTotal'], 2); $total +=  $venta['ValorTotal']?></td>
									</tr>
				<?php
							}//end foreach( $array_ventasadmin[$valor['IDEmpleado']] as $llave => $venta  )
							foreach( $array_ventasvendedor[$valor['IDEmpleado']] as $idpunto => $venta  )
							{
				?>
									<tr>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['Cedula']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['Nombre']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$venta['Cargo']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$array_punto[ $idpunto ];?></td>
										<td class="<?=$class?>" align="right" nowrap><?=number_format( $ValorParcial[$valor['IDEmpleado']][ $idpunto ]['empleado'], 2 ) ; $totalb +=  $ValorParcial[$valor['IDEmpleado']][ $idpunto ]['empleado']?></td>
										<td class="<?=$class?>" align="right" nowrap><?=number_format( $venta['ValorTotal'], 2) ; $total +=  $venta['ValorTotal']?></td>
									</tr>
				<?php
							}//end foreach( $array_ventasvendedor[$valor['IDEmpleado']] as $llave => $venta  )
							
						}//end foreach( $r_facturas as $key => $valor )
				?>
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
