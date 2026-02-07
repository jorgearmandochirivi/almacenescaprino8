<body><?php
		
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
 
 $ReteICA =  get_field( "ReteICA","Valor","IDReteICA",1 )  ;
 $ReteIVA = get_field( "ReteIVA","Valor","IDReteIVA",1 ) / 100;
 $ReteFuente = get_field( "ReteFuente","Valor","IDReteFuente",1 ) / 100;
 
  if(strtotime($FechaDesde)<=strtotime("2017-01-31")):
 	$IVA = 0.16;
 endif;
 
  ?>
	
	<table width="100%">
		
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">
							
								Desde	<input readonly type="text" name="FechaDesde" class="input" value="<?=$FechaDesde?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="nav">
								
								Hasta	<input readonly type="text" name="FechaHasta" class="input" value="<?=$FechaHasta?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
							<td  align='left' valign='middle' class="nav"><img src='images/house.png' border='0'  alt=''></td>
							<td align="left" valign="middle" class="nav">Puntos de Venta	<select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><?php 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta WHERE Publicar = 'S' ORDER BY IDCiudad, Nombre");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select> <input type="hidden" name="mod" value="ReporteVMensual"><input type="hidden" name="action" value="view"></td>
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
		if(!empty($IDPuntoVenta) && !empty( $FechaDesde ) && !empty( $FechaHasta ) ){
		?>
		<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp; <br>
				<br>
				<a href="exportar/exporttventastot.php?IDPuntoVenta=<?=$IDPuntoVenta?>&FechaDesde=<?=$FechaDesde?>&FechaHasta=<?=$FechaHasta?>">Exportar Archivo</a>
				<br>
				<br>
				<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							
						<?php
						echo "MENSUAL VENTAS ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta )." ".$FechaDesde." - ".$FechaHasta;
						?>
					</td>
				</tr>
				<?php					
										
					 $sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura,DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef, DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura 
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
										WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
										AND F.FechaFactura >= '$FechaDesde 00:00:00' AND F.FechaFactura <= '$FechaHasta 23:59:59' 
										AND F.IDFactura = DF.IDFactura
										AND F.IDPuntoVenta = DF.IDPuntoVenta 
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio;";
										
					$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, F.IDCliente, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura, F.ValorBono as ValorBono,
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
					
					
					while( $r_facturas = db_fetch_array( $qry_facturas ) )
					{
						$array_factura[ $i ] = $r_facturas;
						$i++;
					}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )
					//print_r( $array_factura );
					
					foreach( $array_factura as $key => $valor )
					{
						
						unset($array_referencias);
						if($valor['Numero']=="Excedente"):
							// consulto cliente
							$sql_cambio = db_query("SELECT * FROM Cambio WHERE IDCliente in (".$valor['IDCliente'].") and IDFactura = 0 and Excedente = '".($valor['PrecioU'] / ( 1 - ( $valor['DescuentoRef'] / 100 ) ))."'  Order By IDCambio DESC");
							$row_cambio = db_fetch_array($sql_cambio);
							if (!empty($row_cambio["IDCambio"])):
								$sql_detalle_cambio = db_query("SELECT * FROM DetalleCambio WHERE IDCambio = '".$row_cambio["IDCambio"]."'  Order By IDCambio DESC");
								while($row_detalle_cambio = db_fetch_array($sql_detalle_cambio)):
									$pto_vta_ref = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_detalle_cambio["IDCodificacionEspecifica"]);
									$id_ref = get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$pto_vta_ref);
									$referencia = get_field("Referencia","Numero","IDReferencia",$id_ref);
									$array_referencias[]= $referencia;
								endwhile;

								if(count($array_referencias)>0):
									//echo implode("<br>",$array_referencias);
								endif;

							endif;

								if(count($array_referencias)<=0):
									//echo "tarjeta";
								endif;


							//echo "<br>Excedente";
							//$valor['Cantidad'] = 0;
						else:
							//echo $valor['Numero'];
						endif;


						$ElValorUnitarioExc=  $valor['PrecioU'] / ( 1 - ( $valor['DescuentoRef'] / 100 ) );
						//echo number_format( $ElValorUnitario = $valor['PrecioU'] / ( 1 - ( $valor['DescuentoRef'] / 100 ) ) ,2);

						if($valor['Numero']=="Excedente"):
							//echo "0";
							//$Pares += $valor['Cantidad'];
						else:
							//echo $valor['Cantidad'];
							$Pares += $valor['Cantidad'];
						endif;


						$descuento_bono=0;
		                				if ((int)$valor['ValorBono']>0 && $numero_factura_ant !=  $valor['NumeroFactura']){
											$valor_bono_impr=number_format($valor['ValorBono']);
											$descuento_bono=$valor['ValorBono'];
										}
										else{
											$descuento_bono=0;
											//echo "0";
											$valor_bono_impr=0;
										}



										$conta_bono++;
										if($conta_bono==1){
											$descuento_bono=$valor['ValorBono'];

										}




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

													//$descuento_bono=0;
													//echo $descuento_bono;
													$valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar ) -  ($descuento_bono) ;
													//echo $valorparcial."-".$TotalFactura."--";
													$pago = $valorparcial - $saldo ;

													if($pago<0 || $valor["ValorTotal"]==0):
														$pago = 0;
													endif;

													//echo number_format( $pago ,2);
													$Pago += $pago;
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

													if($pago<0):
														$pago = 0;
													endif;


													//echo number_format( $pago ,2);
													$Pago += $pago;
												}//end else

												//Traer Comision
												$pcomision = 0;
												$comision = 0;
												if($array_factura_con_comision[$valor[IDFactura]]["Calculada"]=="S"){
													$array_forma_pago[ $valor[IDFactura] ][ValorComision]=0;
												}
												else{
													$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' AND IDPuntoVenta = '$valor[IDPuntoVenta]' ";
													$qry_comisiones = db_Query( $sql_comisiones );
													$array_forma_pago = array();
													$k = 0;
													while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
													{
														$pcomision = $r_comisiones->Comision / 100;
														//echo "<br>(". $r_comisiones->Valor ."/" . "(1 + " . $IVA.") ) * " . $pcomision;
														//echo "<br> RESULTADO: ". $comisioncalculo=( $valorparcial / (1 + $IVA) ) * $pcomision."<br>";
														$comisioncalculo=( $r_comisiones->Valor / (1 + $IVA) ) * $pcomision;
														$comision +=  ( $valorparcial / (1 + $IVA) ) * $pcomision;
														$k++;
														$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][IDFormaPago] = $r_comisiones->IDFormaPago;
														$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][Valor] = $r_comisiones->Valor;
														$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][Comision] = $r_comisiones->Comision;
														$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][IDBanco] = $r_comisiones->IDBanco;
														$array_forma_pago[ $valor[IDFactura] ][ValorComision] += $comisioncalculo;
													}
													$array_factura_con_comision[$valor[IDFactura]]["Calculada"]="S";

												}

												$ventadia=$pago+$saldo;
												if((int)$ventadia<=0){
														$ventadia = $ElValorUnitarioExc;
												}

											//echo number_format( $ventadia ,2);
											$TotalVentaDia += $ventadia;

											//echo $valor_bono_impr;
											$numero_factura_ant = $valor['NumeroFactura'];


											if((int)$pago<=0){
												$pago = $ElValorUnitarioExc;
												//$Pago += $ElValorUnitarioExc;
										}
										else{
											//$pago -= (int)$descuento_bono;

										}

												$PagoVerdadero+=$pago;

											//echo number_format( $pago ,2);

											$comision=$array_forma_pago[ $valor[IDFactura] ][ValorComision];										
											$ComisionBancos += $comision;


											$valorbruto = $valorparcial - $comision;

											//echo  "FORMU: " . 	$valorparcial ."-". $comision."<br>";

											
											if($valorbruto<0 || $valor["ValorTotal"]==0):
												$valorbruto = 0;
											endif;


											if((int)$valorbruto<=0){
													$valorbruto = $ElValorUnitarioExc;
											}

											//echo number_format( $valorbruto,2 );
											$ValorParcial += $valorbruto;


											$valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) );

								if($valoriva<0 || $valor["ValorTotal"]==0):
									$valoriva=0;
								endif;

								//echo number_format( $valoriva,2 );
								$ValorIVA += $valoriva;


								$valor_bruto_item=$valorparcial - $valoriva;

							if($valor_bruto_item<0 || $valor["ValorTotal"]==0):
								$valor_bruto_item=0;
							endif;

							//echo number_format( $valor_bruto_item ,2 );
							$ValorBruto += ( $valorparcial - $valoriva );


							$AgregaValorRteFte="N";
									$TotRteIca=0;
									$TotRteIva=0;
									$TotRteFte=0;
									foreach(  $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ] as $keyfp => $valuefp )
									{
										$AgregaValorRteFte="S";
										$ValorReteICA = 0;
										$ValorReteIVA = 0;
										$ValorReteFuente=0;
										$ReteFuente=0.015;

										if( $valuefp[IDFormaPago] <> 1 )
										{
											$Valor = $valuefp[Valor];

											$ValorReteICA = ( $Valor / (1 + $IVA ) ) * $ReteICA;
											$ValorReteIVA = ( $Valor - ( $Valor / (1 + $IVA ) ) ) * $ReteIVA;
											//echo $ReteFuente;
											//echo "<br>";
											//echo number_format( $ValorReteFuente = ( $Valor / ( 1 + $IVA ) )  * $ReteFuente , 2 );
													$TotRteFte+=$ValorReteFuente;
													$valorretefuente += $ValorReteFuente;												
										}//end if
									}//end for

									if($AgregaValorRteFte=="N")
						  				$ValorReteFuente=0;


										  $AgregaValorRteIva="N";
							
										  foreach(  $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ] as $keyfp => $valuefp )
										  {
			  
											  if( $valuefp[IDFormaPago] <> 1 )
											  {
												  $Valor = $valuefp[Valor];
												  $ValorReteIVA = ( $Valor - ( $Valor / (1 + $IVA ) ) ) * $ReteIVA;
												  //echo "FORMULAIV" . "(" . $Valor ."- ( " . $Valor ."/ (1 + " . $IVA. " ) ) ) * " . $ReteIVA;									
												  $TotRteIva+=$ValorReteIVA;
											  }	
			  
														  
											   $AgregaValorRteIva="S";
											  $IvaTotal+=$ValorReteIVA;
											  if( $valuefp[IDFormaPago] <> 1 ){
												  //echo number_format( $ValorReteIVA, 2 )."<br>";
											  }	  
										  }	
										  if($AgregaValorRteIva=="N")
												$ValorReteIVA=0;
												
												
												$AgregaValorIca="N";
												foreach(  $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ] as $keyfp => $valuefp )
												{
					  
												  if( $valuefp[IDFormaPago] <> 1 )
													  {
														  $Valor = $valuefp[Valor];
														  $ValorReteICA = ( $Valor / (1 + $IVA ) ) * $ReteICA;
														  $TotRteIca+=$ValorReteICA;
													  }	
					  
					  
												  $AgregaValorIca="S";
												  $IcaTotal+=$ValorReteICA;
													  if( $valuefp[IDFormaPago] <> 1 ){
														  //echo number_format( $ValorReteICA, 2 )."<br>";									
													  } 
												}
												if($AgregaValorIca=="N")
													$ValorReteICA=0;
													
													
													$valor_ingreso = $valorparcial  - ($TotRteIca + $TotRteIva + $TotRteFte + $valoriva + $comision );
													//echo  "<br>FORMULA: " . $valorparcial  ."- (".$TotRteIca ."+". $TotRteIva ."+". $TotRteFte ."+". $valoriva ."+". $comision .")"."<br>";
						
													if($valor_ingreso<0 || $valor["ValorTotal"]==0):
														$valor_ingreso = 0;
													endif;
						
													$TotalIngreso+= $valor_ingreso;
						
													if((int)$valor_ingreso<=0){
															$valor_ingreso = $ElValorUnitarioExc;
															$TotalIngreso+= $ElValorUnitarioExc;
													}
						
						
													//echo number_format( $valor_ingreso , 2 );	

						
						
						
						$ventas[$valor[FechaFacturaF]][Venta] += $ventadia;
						$ventas[$valor[FechaFacturaF]][valoriva] += $valoriva;
						$ventas[$valor[FechaFacturaF]][parcial] += $valorbruto;
						$tventa += $ventadia;
						$tiva += $valoriva;
						$ttotal += $valorbruto;
					}//end foreach
					
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td class="titlemedium" nowrap>Fecha</td>
							<td class="titlemedium" align="center" nowrap>Venta</td>
							<td class="titlemedium" align="center" nowrap>IVA</td>
							<td class="titlemedium" align="center" nowrap>TOTAL</td>
						</tr>
						<?php
						//print_r( $ventas );
						foreach( $ventas as $Fecha => $datos )
						{
							$class="row1";
						?>
							<tr>
								<td class="<?=$class?>" align="center" nowrap><?=$Fecha?></td>
								<td class="<?=$class?>" align="right" nowrap><?=number_format( $datos[Venta] ,2 ); ?></td>
								<td class="<?=$class?>" align="right" nowrap><?=number_format( $datos[valoriva] ,2 ); ?> </td>
								<td class="<?=$class?>" align="right" nowrap><?=number_format( $datos[parcial] ,2 ); ?></td>
							</tr>
						<?php
						}//end for
						?>					
						<tr>
							<td class="titlemedium" align="right" nowrap>TOTALES</td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $tventa, 2) ?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $tiva , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $ttotal , 2)?></td>
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