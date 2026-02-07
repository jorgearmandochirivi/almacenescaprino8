<body><?php 

$Table = "CostoReferencia";
$TableJoin = "Referencia";
$Key = "IDCostoReferencia";
$MOD = "ReporteCostoInventario";
$m="Referencia";

$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		
		
} // End switch
else{
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");
	exit;
}	


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
 
 if(strtotime($FechaDesde)<=strtotime("2017-01-31")):
 	$IVA = 0.16;
 endif;
 
?>
	
	<table width="100%">
		
		<tr>
		<td>
				<table width='60%' align='center' border="0" cellspacing="0" cellpadding="2" class="bordertable">
					<form action="./" name="frmPuntoVenta" method="post">
						<tr>
						  <td  align='left' valign='middle' class="nav"> Desde
					      </td>
						  <td align="left" valign="middle" class="nav">
						  <input  type="text" name="FechaDesde" class="input" value="<?=$FechaDesde?>" size="10">
                          <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
                          
                          </td>
						  <td width="3%"  align='left' valign='middle' class="nav">Hasta</td>
							<td width="28%" align="left" valign="middle" class="nav"><input  type="text" name="FechaHasta" class="input" value="<?=$FechaHasta?>" size="10">
                            <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
                            </td>
						</tr>
						<tr>
						  <td align="left" valign="middle" class="nav"><span class="col2">Proveedor</span></td>
						  <td align="left" valign="middle" class="nav"><span class="col2">
						    <select name="IDProveedor" id="IDProveedor" class="input proveedor_pedido">
						      <option value="">[Seleccione]</option>
						      <?php 
						$sql_prov = db_query("Select * From Proveedor where Publicar = 'S' order by Nombre asc");
						while ($row_prov = db_fetch_array($sql_prov)): ?>
						      <option value="<?php echo $row_prov[IDProveedor];?>" <?php if($_POST["IDProveedor"]==$row_prov[IDProveedor]) echo "selected"; ?>><?php echo $row_prov[Nombre];?></option>
						      <?php	
						endwhile;
					?>
					      </select>
						  </span></td>
						  <td  align='left' valign='middle' class="nav">Referencia</td>
						  <td align="left" valign="middle" class="nav"><span class="col2">
					      <input type=text class=tbox name=referencia value="<?php echo $_POST["referencia"]?>">
						  </span></td>
					  </tr>
						<tr>
						  <td  align='left' valign='middle' class="nav">Puntos de Venta</td>
						  <td align="left" valign="middle" class="nav"><select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
						    <option value="">Seleccione Un Punto de Venta</option>
						    <?php  								
								$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
						    </select>
						    <input type="hidden" name="mod" value="ConsolidadoVentasTercero">
						    <input type="hidden" name="action" value="view"></td>
						  <td  align='left' valign='middle' class="nav"><input type="submit" value="Ver Reporte" name="submit" class="submit"></td>
						  <td align="left" valign="middle" class="nav">&nbsp;</td>
					  </tr>
					</form>
				</table>
			</td>
		</tr>
		
		<br>
		<br>
		
		<tr>
		<td>
				 
			<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							
						Reporte Ventas Diarias Almacen : <?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>&nbsp; &nbsp; Fecha: <?=formatofecha( $Fecha )?>
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
						
						
					if($_POST["IDProveedor"])	
						$condicion_filtro = " and IDProveedor = '".$_POST["IDProveedor"]."' ";
						
					if($_POST["referencia"])	
						$condicion_filtro .= " and R.Numero = '".$_POST["referencia"]."' ";
						
					if($_POST["IDPuntoVenta"])	
						$condicion_filtro .= " and F.IDPuntoVenta = '".$IDPuntoVenta."' ";	
						
					
					//print_r( $array_banco );
					//Seleccionar Bancos
					
					
										
					$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, F.IDCliente, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura, F.ValorBono as ValorBono,
										DF.IVA, DF.IDDetalleFactura, DF.ReteIVA, DF.ReteICA, F.IDFactura, F.IDPuntoVenta, DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF 
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
										WHERE 
										 F.FechaFactura >= '".$FechaDesde . '00:00:00'."' and F.FechaFactura <= '".$FechaHasta . '23:59:59'."'
										AND F.IDFactura = DF.IDFactura 
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio
										AND R.Numero <> 'Excedente'
										".$condicion_filtro."										
										";					
								
					
					
											
					
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
										<!--
                                        <td class="titlemedium" nowrap>No. Factura</td>
										<td class="titlemedium" nowrap>Fecha</td>
                                        -->
										<td class="titlemedium" align="center" nowrap>Referencia </td>
										<!--
                                        <td class="titlemedium" align="center" nowrap>Vr. Unitario</td>
                                        -->
										<td class="titlemedium" align="center" nowrap>Pares</td>
										<!--
                                        <td class="titlemedium" align="center" nowrap>Dto.</td>
										<td class="titlemedium" align="center" nowrap>Dto. Factura</td>
										<td class="titlemedium" align="center" nowrap>Dto. Bono</td>
										<td class="titlemedium" align="center" nowrap>Dto. Par</td>
										<td class="titlemedium" align="center" nowrap>Pago</td>
										<td class="titlemedium" align="center" nowrap>Saldo</td>
										<td class="titlemedium" align="center" nowrap>Comision Bancos</td>
										<td class="titlemedium" align="center" nowrap>Vr. Parcial</td>
										<td class="titlemedium" align="center" nowrap>IVA</td>
                            			-->
										<td class="titlemedium" align="center" nowrap>Valor Bruto</td>
                                        <!--
										<td class="titlemedium" align="center" nowrap>Forma de Pago</td>
										<td class="titlemedium" align="center" nowrap>Banco</td>
										<td class="titlemedium" align="center" nowrap>Rete fuente</td>
										<td class="titlemedium" align="center" nowrap>Rete IVA</td>
										<td class="titlemedium" align="center" nowrap>Rete ICA</td>
										<td class="titlemedium" align="center" nowrap>Ingreso</td>
                                        -->
									</tr>
						<?php
						foreach( $r_facturas as $key => $valor )
						{ 
							//print_r( $valor );
							$class = repetition()?"row2":"row1";
							//print_r($valor);
						?>
                        <!--
						<tr>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['NumeroFactura']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['FechaFacturaF']?></td>
										<td class="<?=$class?>" align="center" nowrap>
										<?php
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
										?> 
                                        </td>
										<td class="<?=$class?>" align="right" nowrap><?=number_format( $ElValorUnitario = $valor['PrecioU'] / ( 1 - ( $valor['DescuentoRef'] / 100 ) ) ,2)?></td>
							<td class="<?=$class?>" align="center" nowrap>                            
                            <?php 
							if($valor['Numero']=="Excedente"):
								echo "0"; 
								//$Pares += $valor['Cantidad'];
							else:
							echo $valor['Cantidad']; 
								$Pares += $valor['Cantidad'];
								
								$array_referencia_resumen[$valor['Numero']]["Pares"] += $valor['Cantidad'];
							endif;
							
							?>
                            
                            </td>
                           
							<td class="<?=$class?>" align="center" nowrap><?=$valor['DescuentoRef']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['DescuentoFactura']?> </td>
										<td class="<?=$class?>" align="center" nowrap><?php
								$descuento_bono=0;
                                if ((int)$valor['ValorBono']>0 && $numero_factura_ant !=  $valor['NumeroFactura']){
									echo $descuento_bono=number_format($valor['ValorBono']);	
															
								}
								else{
									echo "0";
									
									$valor['ValorBono']=0;
									
									
								}
								
								$numero_factura_ant = $valor['NumeroFactura'];
								?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['DescuentoPar']?></td>
										<td class="<?=$class?>" align="right" nowrap>
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
													$valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar ) -  ($valor['ValorBono']) ;
													//echo $valorparcial."-".$TotalFactura."--";
													$pago = $valorparcial - $saldo ;
													
													if($pago<0 || $valor["ValorTotal"]==0):
														$pago = 0;
													endif;
													
													echo number_format( $pago ,2); 
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
														
														
													echo number_format( $pago ,2); $Pago += $pago;
												}//end else
												
												//Traer Comision
												$pcomision = 0;
												$comision = 0;
												$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' AND IDPuntoVenta = '$valor[IDPuntoVenta]' ";
												$qry_comisiones = db_Query( $sql_comisiones );
												$array_forma_pago = array();
												$k = 0;
												while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
												{
													$pcomision = $r_comisiones->Comision / 100;
													$comision +=  ( $valorparcial / (1 + $IVA) ) * $pcomision;
													$k++;
													$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][IDFormaPago] = $r_comisiones->IDFormaPago;
													$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][Valor] = $r_comisiones->Valor;
													$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][Comision] = $r_comisiones->Comision;
													$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][IDBanco] = $r_comisiones->IDBanco;
												}
												
											?>
										</td>
										<td class="<?=$class?>" align="right" nowrap>
											<?php
												echo number_format( $saldo ,2); $Saldo += $saldo;
											?>
										</td>
										<td class="<?=$class?>" align="right" nowrap>
										<?php 
										echo number_format( $comision  ,2 ); 
										$ComisionBancos += $comision;?>
                                        </td>
										<td class="<?=$class?>" align="right" nowrap>
                                        <?php 
											$valorbruto = $valorparcial - $comision;
											
											if($valorbruto<0 || $valor["ValorTotal"]==0):
												$valorbruto = 0;
											endif;
											
											echo number_format( $valorbruto,2 ); 
											$ValorParcial += $valorbruto; 
										?>
                                        </td>
							<td class="<?=$class?>" align="right" nowrap>
                            <?php 
								$valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) );
								
								if($valoriva<0 || $valor["ValorTotal"]==0):
									$valoriva=0;
								endif;
								
								echo number_format( $valoriva,2 );
								$ValorIVA += $valoriva; ?> 
                            </td>
							
							
							
							<td class="<?=$class?>" align="right" nowrap>
                            
                            <?php 
							$valor_bruto_item=$valorparcial - $valoriva;
							
							if($valor_bruto_item<0 || $valor["ValorTotal"]==0):
								$valor_bruto_item=0;
							endif;
							
							echo number_format( $valor_bruto_item ,2 ); 
							
							$array_referencia_resumen[$valor['Numero']]["Bruto"] += $valor_bruto_item;
							
							$ValorBruto += ( $valorparcial - $valoriva );
							?>
                            </td>
                            
                            
							<td class="<?=$class?>" align="right" nowrap>
								<table width=100;?>
									<?php
									foreach(  $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ] as $keyfp => $valuefp )
									{
									?>
										<tr>
											<td align="right">
												<?php echo $array_formapago[  $valuefp[IDFormaPago] ][Descripcion] ?>
											</td>
										</tr>
									<?php
									}//end for
									?>
								</table>
								<?php    
									
									
									
									//echo $array_formapago[ $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][IDFormaPago] ][Descripcion] ;
								
								
								?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
								<table width=100;?>
									<?php
									foreach(  $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ] as $keyfp => $valuefp )
									{
									?>
										<tr>
											<td align="right">
												<?php echo $array_banco[   $valuefp[IDBanco] ][Nombre] ?>
											</td>
										</tr>
									<?php
									}//end for
									?>
								</table>
								
								<?php  
									//echo $array_banco[ $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][IDBanco] ][Nombre];
								?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
								<table>
									<?php
									foreach(  $array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ] as $keyfp => $valuefp )
									{
										$ValorReteICA = 0;
										$ValorReteIVA = 0;
										
										if( $valuefp[IDFormaPago] <> 1 )
										{
											$Valor = $valuefp[Valor];
											
											$ValorReteICA = ( $Valor / (1 + $IVA ) ) * $ReteICA;
											$ValorReteIVA = ( $Valor - ( $Valor / (1 + $IVA ) ) ) * $ReteIVA;
											//echo $ReteFuente;
											//echo "<br>";
											
									
									?>
										<tr>
											<td>
												<?php 
													echo number_format( $ValorReteFuente = ( $Valor / ( 1 + $IVA ) )  * $ReteFuente , 2 ); 
													$valorretefuente += $ValorReteFuente; 
												?>
											</td>
										</tr>
									<?php
										}//end if
									}//end for
									?>
								</table>
								
								
								<?php
									/*
									
									Asi era antes
									
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
									*/
								?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
							
							<?php
							$IvaTotal+=$ValorReteIVA;
							echo number_format( $ValorReteIVA, 2 );
							?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
							<?php
							$IcaTotal+=$ValorReteICA;
							echo number_format( $ValorReteICA, 2 );
							?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
							<?php
							 
							$valor_ingreso = $valorparcial  - ($ValorReteICA + $ValorReteIVA + $ValorReteFuente + $valoriva + $comision );
							
							if($valor_ingreso<0 || $valor["ValorTotal"]==0):
								$valor_ingreso = 0;
							endif;
							
							$TotalIngreso+= $valor_ingreso;
							echo number_format( $valor_ingreso , 2 );
							
							?>
							</td>
                           
						</tr>
                         -->
						<?php
						}//end foreach( $r_facturas as $key => $valor )
						?>
                        
                        <?php foreach($array_referencia_resumen as $id => $valor ): 
								$class = repetition()?"row2":"row1";
							?>
                        	<tr>
                        	<td class="<?=$class?>" align="center" nowrap><?php echo $id; ?></td>
                            <td class="<?=$class?>" align="center" nowrap><?php echo $valor["Pares"]; ?></td>
                            <td class="<?=$class?>" align="right" nowrap><?php echo number_format( $valor["Bruto"] , 2); ?></td>
                            </td>
                        <?php endforeach;?>
                        
                        
							
						<tr>
							<td class="titlemedium" colspan="1" align="right" nowrap>TOTALES</td>
							<td class="titlemedium" align="center" nowrap><?=$Pares ?></td>
							<!--
                            <td class="titlemedium" align="center" colspan="3" nowrap></td>
										<td class="titlemedium" align="right" nowrap></td>
										<td class="titlemedium" align="right" nowrap><?=number_format( $Pago , 2)?></td>
										<td class="titlemedium" align="right" nowrap><?=number_format( $Saldo , 2)?></td>
										<td class="titlemedium" align="right" nowrap><?=number_format( $ComisionBancos , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $ValorParcial , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $ValorIVA , 2)?></td>
                            -->
							<td class="titlemedium" align="right" nowrap><?=number_format( $ValorBruto , 2)?></td>
                            <!--
										<td class="titlemedium" align="right" nowrap></td>
										<td class="titlemedium" align="right" nowrap></td>
										<td class="titlemedium" align="right" nowrap><?php echo number_format( $valorretefuente , 2 ); ?></td>
										<td class="titlemedium" align="right" nowrap><?php echo number_format( $IvaTotal , 2 ); ?></td>
										<td class="titlemedium" align="right" nowrap><?php echo number_format( $IcaTotal , 2 ); ?></td>
										<td class="titlemedium" align="right" nowrap><?php echo number_format( $TotalIngreso , 2 ); ?></td>
                                -->        
									</tr>
											
					</table>
				  <br><br></td>
			</tr>
		</form>
	
		</table>
	</td>
	</tr>
	
	</table>
	<?php 						
}// Enf function print()	

?>
</body>
