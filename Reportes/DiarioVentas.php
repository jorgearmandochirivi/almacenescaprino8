<body><?php
$MOD = "diario";
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

	 if(strtotime($Fecha)<=strtotime("2017-01-31")):
		$IVANormal = 0.16;
	 else:	
		$IVANormal = $IVA;
	endif;
	
	
	 $IDDiaSinIva = get_field("DiaSinIva","IDDiaSinIva","Fecha",$Fecha);
	 if(strtotime($Fecha)<=strtotime("2017-01-31")):
		$IVA = 0.16;
	elseif((int)$IDDiaSinIva > 0 || strtotime($Fecha)==strtotime("2020-06-19") || strtotime($Fecha)==strtotime("2020-07-03") || strtotime($Fecha)==strtotime("2020-07-19") || strtotime($Fecha)==strtotime("2020-11-21") ):
	   $IVA = 0;
	else:   
	$IVA = $IVANormal;
	endif;
	

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
			| <a href="javascript:void();" onClick="window.open('Reportes/PRDiario.php?Fecha=<?=$Fecha?>','','width=426, height=350')">Imprimir</a>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
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
				<?php
					 $sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, F.IDCliente, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura, F.ValorBono as ValorBono,
					 DF.IVA, DF.IDDetalleFactura, DF.ReteIVA, DF.ReteICA, F.IDFactura, F.IDPuntoVenta, DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
					 FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P
					 WHERE F.IDPuntoVenta = '$IDPuntoVenta'
					 AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = DATE_FORMAT('$Fecha','%Y-%c-%d' )
					 AND F.IDFactura = DF.IDFactura
					 AND F.IDPuntoVenta = DF.IDPuntoVenta
					 AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica
					 AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
					 AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio AND R.Reportes <> 'N' Order by NumeroFactura;";





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
				      <td class='mainbg'><table width="100%" border="0" cellspacing="1" cellpadding="1">
				        <tr>
				          <td class="titlemedium" nowrap>No. Factura</td>
				          <td class="titlemedium" nowrap>Fecha</td>
				          <td class="titlemedium" align="center" nowrap>Referencia </td>
				          <td class="titlemedium" align="center" nowrap>Vr. Unitario</td>
				          <td class="titlemedium" align="center" nowrap>Pares</td>
				          <td class="titlemedium" align="center" nowrap>Dto.</td>
				          <td class="titlemedium" align="center" nowrap>Dto. Factura</td>
				          <td class="titlemedium" align="center" nowrap>Venta Dia</td>
				          <td class="titlemedium" align="center" nowrap>Dto. Bono</td>
				          <td class="titlemedium" align="center" nowrap>Dto. Par</td>
				          <td class="titlemedium" align="center" nowrap>Pago</td>
				          <td class="titlemedium" align="center" nowrap>Saldo</td>
				          <td class="titlemedium" align="center" nowrap>Comision Bancos</td>
				          <td class="titlemedium" align="center" nowrap>Vr. Parcial</td>
				          <td class="titlemedium" align="center" nowrap>IVA</td>
				          <td class="titlemedium" align="center" nowrap>Valor Bruto</td>
				          
			            </tr>
				        <?php
						$array_factura_con_comision=array();
						foreach( $r_facturas as $key => $valor )
						{
							//print_r( $valor );
							$class = repetition()?"row2":"row1";
							//print_r($valor);
							
						?>
				        <tr>
				          <td class="<?=$class?>" align="center" nowrap><?=$valor['NumeroFactura']?></td>
				          <td class="<?=$class?>" align="center" nowrap><?=$valor['FechaFacturaF']?></td>
				          <td class="<?=$class?>" align="center" nowrap><?php
											$array_referencias = array();
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
													echo implode("<br>",$array_referencias);
												endif;

											endif;

												if(count($array_referencias)<=0):
													echo "tarjeta";
												endif;


											echo "<br>Excedente";
											//$valor['Cantidad'] = 0;
										else:
											echo $valor['Numero'];
										endif;
										?></td>
				          <td class="<?=$class?>" align="right" nowrap>
										<?php
										$ElValorUnitarioExc=  $valor['PrecioU'] / ( 1 - ( $valor['DescuentoRef'] / 100 ) );
										echo number_format( $ElValorUnitario = $valor['PrecioU'] / ( 1 - ( $valor['DescuentoRef'] / 100 ) ) ,2);
										?>

									</td>
				          <td class="<?=$class?>" align="center" nowrap><?php
							if($valor['Numero']=="Excedente"):
								echo "0";
								//$Pares += $valor['Cantidad'];
							else:
							echo $valor['Cantidad'];
								$Pares += $valor['Cantidad'];
							endif;

							?></td>
				          <td class="<?=$class?>" align="center" nowrap><?=$valor['DescuentoRef']?></td>
				          <td class="<?=$class?>" align="center" nowrap><?=$valor['DescuentoFactura']?></td>
				          <td class="<?=$class?>" align="right" nowrap>&nbsp;
							








				            <?php


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




										$TotalFactura = $valor["ValorTotal"] ;
												if( $valor['DescuentoPar'] > 0 )
													$valordescuentopar = ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( $valor['DescuentoPar'] / 100 );
												else
													$valordescuentopar = 0;

													

												//consultar forma de pago pa saber si se le resta
												$sql_formasdepago = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '".$valor["IDFactura"]."' AND IDPuntoVenta = '$IDPuntoVenta' ";
												$qry_formasdepago = db_query( $sql_formasdepago );
												$saldo = 0;
												while( $r_formasdepago = db_fetch_object( $qry_formasdepago ) )
													if( $r_formasdepago->IDFormaPago == 13 ) //13 FormaPago Saldo
														$saldo = $r_formasdepago->Valor;

												
												if( $valor['DescuentoFactura'] == 0 )
												{

													//$descuento_bono=0;
													//echo $descuento_bono;													
													if($IVA==0){														
														$valor['PrecioU']=$valor['PrecioU']/($IVANormal+1);
													}	

													//echo "EL VALOR  " . $valor['PrecioU'] . "<br>";
												
													//echo "<br>valorparcial = ( ( ".$valor['PrecioU'] ."*". $valor['Cantidad']." ) *   ( 1 - (  ". $valor['DescuentoFactura'] ."/ 100 ) ) ) - ( " .$valordescuentopar." ) -  (".$descuento_bono.")";
													//echo "<br>valorparcial = ( ( PrecioU' * Cantidad ) *   ( 1 - (  DescuentoFactura / 100 ) ) ) - ( valordescuentopar ) -  (descuento_bono)";
													//$valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar ) -  ($descuento_bono) ;
													$valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar ) -  ($descuento_bono) ;
													//echo "valorparcial = ( ( ".$valor['PrecioU']." * " . $valor['Cantidad']." ) *   ( 1 - (  ".$valor['DescuentoFactura']." / 100 ) ) ) - ( ".$valordescuentopar." ) -  (".$descuento_bono.")<br>" ;
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
													//echo "<br>Precio =  " . $valor['PrecioU'] ."-". $valordescuentopar;	
													//$valorparcial =  ( ( $valor['PrecioU'] * $valor['Cantidad'] ) + ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) - ( $valordescuentopar );
													$Precio =  $valor['PrecioU'] - $valordescuentopar;
													$valorparcial =  ( ( $Precio * $valor['Cantidad'] ) + ( ( $Precio * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) ;

													/* Se agrega pa las mayores */
													$mayortotal = $TotalFactura - $valorparcial;
													if( $mayortotal <> 0 )
													{
														//echo "saldo = ( ".$valorparcial ."/". $TotalFactura." ) * ".$saldo ."  FIN ";
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

												//echo "PAGO " . $pago;

												//Traer Comision
												$pcomision = 0;
												$comision = 0;
												if($array_factura_con_comision[$valor["IDFactura"]]["Calculada"]=="S"){
													$array_forma_pago[ $valor["IDFactura"] ]["ValorComision"]=0;
												}
												else{
													$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '".$valor["IDFactura"]."' AND IDPuntoVenta = '".$valor["IDPuntoVenta"]."' ";
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
														$array_forma_pago[ $valor["IDFactura"] ][ $valor["IDDetalleFactura"] ][$k]["IDFormaPago"] = $r_comisiones->IDFormaPago;
														$array_forma_pago[ $valor["IDFactura"] ][ $valor["IDDetalleFactura"] ][$k]["Valor"] = $r_comisiones->Valor;
														$array_forma_pago[ $valor["IDFactura"] ][ $valor["IDDetalleFactura"] ][$k]["Comision"] = $r_comisiones->Comision;
														$array_forma_pago[ $valor["IDFactura"] ][ $valor["IDDetalleFactura"] ][$k]["IDBanco"] = $r_comisiones->IDBanco;
														$array_forma_pago[ $valor["IDFactura"] ]["ValorComision"] += $comisioncalculo;
													}


													


													$array_factura_con_comision[$valor["IDFactura"]]["Calculada"]="S";

												}

												
											
											?>
				            <?php
							
						  $ventadia=$pago+$saldo;
						  //echo "FORMU " . "ventadia=" . $pago."+".$saldo."<br>";
							if((int)$ventadia<=0){
									if($valor['DescuentoPar']=="100"):
										$ventadia = 0;
									else:
										$ventadia = $ElValorUnitarioExc;
									endif;
							}

						  echo number_format( $ventadia ,2);
						  $TotalVentaDia += $ventadia;
						  ?></td>
				          <td class="<?=$class?>" align="center" nowrap><?php



								echo $valor_bono_impr;

								$numero_factura_ant = $valor['NumeroFactura'];
								?></td>
				          <td class="<?=$class?>" align="center" nowrap><?=$valor['DescuentoPar']?></td>
				          <td class="<?=$class?>" align="right" nowrap>
	                          <?php
							  							if((int)$pago<=0){
															if($valor['DescuentoPar']=="100"):
																$pago = 0;
															else:
																$pago = $ElValorUnitarioExc;
																//$Pago += $ElValorUnitarioExc;
															endif;
														}
														else{
															//$pago -= (int)$descuento_bono;

														}

														if($IVA==0){
															//$pago=$pago/($IVANormal+1);
															
														}

																$PagoVerdadero+=$pago;

															echo number_format( $pago ,2);

															?>
                          </td>
				          <td class="<?=$class?>" align="right" nowrap><?php
												echo number_format( $saldo ,2); $Saldo += $saldo;
											?></td>
				          <td class="<?=$class?>" align="right" nowrap>
						 <!-- COMISION BANCOS --> 
						  <?php 
									
										$comision=$array_forma_pago[ $valor["IDFactura"] ]["ValorComision"];										
										echo number_format( $comision  ,2 );
										$ComisionBancos += $comision;?></td>
				          <td class="<?=$class?>" align="right" nowrap><?php
						  					//Valor parcial	
											$valorbruto = $valorparcial - $comision;

											//echo  "FORMU: " . 	$valorparcial ."-". $comision."<br>";
																									
											
											if($valorbruto<0 || $valor["ValorTotal"]==0):
												$valorbruto = 0;
											endif;


											if((int)$valorbruto<=0){
												if($valor['DescuentoPar']=="100"):
													$valorbruto = 0;
												else:
													$valorbruto = $ElValorUnitarioExc;
												endif;	
											}

											

											if($valoriva==0){
												//$valorbruto=$pago-$comision;
											}

											
											echo number_format( $valorbruto,2 );
											$ValorParcial += $valorbruto;
										?></td>
				          <td class="<?=$class?>" align="right" nowrap><?php
								$valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) );

								if($valoriva<0 || $valor["ValorTotal"]==0):
									$valoriva=0;
								endif;

								echo number_format( $valoriva,2 );
								$ValorIVA += $valoriva; ?></td>
				          <td class="<?=$class?>" align="right" nowrap><?php
							$valor_bruto_item=$valorparcial - $valoriva;

							if($valor_bruto_item<0 || $valor["ValorTotal"]==0):
								$valor_bruto_item=0;
							endif;

							echo number_format( $valor_bruto_item ,2 );
							$ValorBruto += ( $valorparcial - $valoriva );
							?></td>
				          
				          
				         
				          
				          
				          
			            </tr>
				        <?php
						}//end foreach( $r_facturas as $key => $valor )
						?>
				        <tr>
				          <td class="titlemedium" colspan="4" align="right" nowrap>TOTALES</td>
				          <td class="titlemedium" align="center" nowrap><?=$Pares ?></td>
				          <td class="titlemedium" align="center" nowrap></td>
				          <td class="titlemedium" align="center" nowrap></td>
				          <td class="titlemedium" align="right" nowrap><?php echo number_format( $TotalVentaDia ,2); ?></td>
				          <td class="titlemedium" align="center" nowrap></td>
				          <td class="titlemedium" align="right" nowrap></td>
				          <td class="titlemedium" align="right" nowrap><?php echo number_format( $PagoVerdadero , 2); //number_format( $Pago , 2); ?></td>
				          <td class="titlemedium" align="right" nowrap><?=number_format( $Saldo , 2)?></td>
				          <td class="titlemedium" align="right" nowrap><?=number_format( $ComisionBancos , 2)?></td>
				          <td class="titlemedium" align="right" nowrap><?=number_format( $ValorParcial , 2)?></td>
				          <td class="titlemedium" align="right" nowrap><?=number_format( $ValorIVA , 2)?></td>
				          <td class="titlemedium" align="right" nowrap><?=number_format( $ValorBruto , 2)?></td>
				          <td class="titlemedium" align="right" nowrap></td>
				          <td class="titlemedium" align="right" nowrap></td>
				          
			            </tr>
				        </table>
				        <br>
				        <br>
				        <table width="100%" border="0" cellspacing="1" cellpadding="1">
				          <tr>
				            <td class="maintitle" valign="middle">&nbsp;

				              Reporte Creditos :
				              <?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>
				              &nbsp; &nbsp; Fecha:
				              <?=formatofecha( $Fecha )?></td>
			              </tr>
				          <tr>
				            <td  valign="middle">&nbsp;
				              <table width="100%" >
				                <tr>
				                  <td class="titlemedium" align="center" nowrap>Numero Factura</td>
				                  <td class="titlemedium" align="center" colspan="2" nowrap>Cuota</td>
				                  <td class="titlemedium" align="right" nowrap>Fecha de Cuota</td>
				                  <td class="titlemedium" align="right" nowrap>Fecha de Pago</td>
								  <td class="titlemedium" align="right" nowrap>Medio de Pago</td>
				                  <td class="titlemedium" align="right" nowrap>Valor</td>
			                    </tr>
				                <?php
						//$sql_credito = "SELECT * FROM CreditoCuota WHERE DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND IDPuntoVenta = '$IDPuntoVenta'";
						$sql_credito = "SELECT * FROM CreditoCuota WHERE (MedioPago = 'Efectivo' or MedioPago='') and DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND (IDPuntoVentaPago = '$IDPuntoVenta' or ( IDPuntoVenta = '$IDPuntoVenta' and IDPuntoVentaPago = 0 )) ";
						$qry_credito = db_query( $sql_credito );
						while( $r_credito = db_fetch_object( $qry_credito ) )
						{
							$class = repetition()?"row2":"row1";
						?>
				                <tr>
				                  <td class="<?=$class?>" align="center" nowrap><?=$r_credito->NumeroFactura ?></td>
				                  <td class="<?=$class?>" align="center" colspan="2" nowrap><?=$r_credito->IDCuota?></td>
				                  <td class="<?=$class?>" align="right" nowrap><?=$r_credito->FechaCuota?></td>
				                  <td class="<?=$class?>" align="right" nowrap><?=$r_credito->FechaPago?></td>
								  <td class="<?=$class?>" align="right" nowrap><?=$r_credito->MedioPago?></td>
				                  <td class="<?=$class?>" align="right" nowrap>
								  	<?=number_format( $r_credito->ValorTotal , 2); 
									$ValorTotal += $r_credito->ValorTotal;
									$ValorTotalEfect += $r_credito->ValorTotal;
									?>
								</td>
			                    </tr>
				                <?php
						}//ebd while
						?>
				                <tr>
				                  <td class="titlemedium" align="center" nowrap></td>
				                  <td class="titlemedium" align="center" colspan="2" nowrap></td>
				                  <td class="titlemedium" align="right" nowrap></td>
								  <td class="titlemedium" align="right" nowrap></td>
				                  <td class="titlemedium" align="right" nowrap>Total</td>
				                  <td class="titlemedium" align="right" nowrap><?=number_format( $ValorTotalEfect, 2 )?></td>
			                    </tr>
			                  </table>

							  <table width="100%" >
				                <tr>
				                  <td class="titlemedium" align="center" nowrap>Numero Factura</td>
				                  <td class="titlemedium" align="center" colspan="2" nowrap>Cuota</td>
				                  <td class="titlemedium" align="right" nowrap>Fecha de Cuota</td>
				                  <td class="titlemedium" align="right" nowrap>Fecha de Pago</td>
								  <td class="titlemedium" align="right" nowrap>Medio de Pago</td>
				                  <td class="titlemedium" align="right" nowrap>Valor</td>
			                    </tr>
				                <?php
						//$sql_credito = "SELECT * FROM CreditoCuota WHERE DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND IDPuntoVenta = '$IDPuntoVenta'";
						$sql_credito = "SELECT * FROM CreditoCuota WHERE MedioPago = 'Transferencia' and DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND (IDPuntoVentaPago = '$IDPuntoVenta' or ( IDPuntoVenta = '$IDPuntoVenta' and IDPuntoVentaPago = 0 )) ";
						$qry_credito = db_query( $sql_credito );
						while( $r_credito = db_fetch_object( $qry_credito ) )
						{
							$class = repetition()?"row2":"row1";
						?>
				                <tr>
				                  <td class="<?=$class?>" align="center" nowrap><?=$r_credito->NumeroFactura ?></td>
				                  <td class="<?=$class?>" align="center" colspan="2" nowrap><?=$r_credito->IDCuota?></td>
				                  <td class="<?=$class?>" align="right" nowrap><?=$r_credito->FechaCuota?></td>
				                  <td class="<?=$class?>" align="right" nowrap><?=$r_credito->FechaPago?></td>
								  <td class="<?=$class?>" align="right" nowrap><?=$r_credito->MedioPago?></td>
				                  <td class="<?=$class?>" align="right" nowrap>
								  <?=number_format( $r_credito->ValorTotal , 2); 
								  $ValorTotal += $r_credito->ValorTotal;
								  $ValorTotalTransf += $r_credito->ValorTotal;
								  ?>
								  </td>
			                    </tr>
				                <?php
						}//ebd while
						?>
				                <tr>
				                  <td class="titlemedium" align="center" nowrap></td>
				                  <td class="titlemedium" align="center" colspan="2" nowrap></td>
				                  <td class="titlemedium" align="right" nowrap></td>
								  <td class="titlemedium" align="right" nowrap></td>
				                  <td class="titlemedium" align="right" nowrap>Total</td>
				                  <td class="titlemedium" align="right" nowrap><?=number_format( $ValorTotalTransf, 2 )?></td>
			                    </tr>

								<tr>
				                  <td class="titlemedium" align="center" nowrap></td>
				                  <td class="titlemedium" align="center" colspan="2" nowrap></td>
				                  <td class="titlemedium" align="right" nowrap></td>
								  <td class="titlemedium" align="right" nowrap></td>
				                  <td class="titlemedium" align="right" nowrap>GRAN TOTAL </td>
				                  <td class="titlemedium" align="right" nowrap><?=number_format( $ValorTotal, 2 )?></td>
			                    </tr>


			                  </table>  
							
							
							
							</td>
			              </tr>
		              </table></td>
			        </tr>
			      </form>

		</table>
		<?php
		$page = ob_get_contents();
		$fecha = date( "Y-m-d H:i:s" );
		$name = "DiarioVentas$fecha.xls";
		$file = $filedir.$name;

		file_put_contents($file, $page);

		$name = "DiarioVentas$fecha.sxc";
		$file = $filedir.$name;

		file_put_contents($file, $page);
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
