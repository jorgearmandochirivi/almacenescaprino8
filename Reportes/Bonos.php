<body><?php
$MOD = "bonos";
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
			
			<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="100%">
		
				<tr>
					<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
					</td>
					<td class="tbtbot"><b></b>
						<span class="gen">
							Reporte Bonos Almacen : <?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>&nbsp; &nbsp; Fecha: <?=formatofecha( $Fecha )?>
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
					 $sql_facturas = " SELECT F.NumeroFacturaBono,F.IDFacturaBono, F.FechaFacturaBono, F.ValorBono, F.Excedente, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef, P.Descuento, F.Descuento as DescuentoFactura 
										FROM FacturaBono F, DetalleFacturaBono DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
										WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
										AND DATE_FORMAT( F.FechaFacturaBono,'%Y-%c-%d' ) = DATE_FORMAT('$Fecha','%Y-%c-%d' ) 
										AND F.IDFacturaBono = DF.IDFacturaBono
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio
										AND R.Reportes <> 'N';";
											
					
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
							<td class="navpic" nowrap>No. Factura</td>
							<td class="navpic" align="center" nowrap>Referencia </td>
							<td class="navpic" align="center" nowrap>Vr. Bono</td>
							<td class="navpic" align="center" nowrap>Vr. Unitario</td>
							<td class="navpic" align="center" nowrap>Excedente</td>
										<td class="navpic" align="center" nowrap>Pares</td>
										<td class="navpic" align="center" nowrap>Dto.</td>
										<td class="navpic" align="center" nowrap>Dto. Factura</td>
										<td class="navpic" align="center" nowrap>Dto. Par</td>
										<td class="navpic" align="center" nowrap>Pago</td>
										<td class="navpic" align="center" nowrap>Vr. Parcial</td>
							<td class="navpic" align="center" nowrap>IVA</td>
							<td class="navpic" align="center" nowrap>Valor Bruto</td>
						</tr>
						<?php
						foreach( $r_facturas as $key => $valor )
						{ 
							//print_r( $valor );
							$class = repetition()?"row2":"row1";
							//print_r($valor);
						?>
						<tr>
							<td class="<?=$class?>" align="center" nowrap><?=$valor['NumeroFacturaBono']?></td>
							<td class="<?=$class?>" align="center" nowrap><?=$valor['Numero']?> </td>
							<td class="<?=$class?>" align="center" nowrap><?php echo number_format( $valor['ValorBono'], 2 ); ?></td>
							<td class="<?=$class?>" align="right" nowrap><?=number_format( $valor['PrecioU'] / ( 1 - ( $valor['DescuentoRef'] / 100 ) ) ,2)?></td>
							<td class="<?=$class?>" align="center" nowrap><?php echo number_format( $valor['Excedente'], 2 ); ?></td>
							<td class="<?=$class?>" align="center" nowrap><?php echo $valor['Cantidad']; $Pares += $valor['Cantidad'];?></td>
							<td class="<?=$class?>" align="center" nowrap><?=$valor['DescuentoRef']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['DescuentoFactura']?> </td>
										<td class="<?=$class?>" align="center" nowrap><?=$valor['DescuentoPar']?></td>
										<td class="<?=$class?>" align="right" nowrap>
											<?php
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
											?>
										</td>
										<?php
										//Traer Comision
										$pcomision = 0;
										$comision = 0;
										$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' ";
										$qry_comisiones = db_Query( $sql_comisiones );
										while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
										{
											$pcomision = $r_comisiones->Comision / 100;
											$comision +=  ( $valorparcial / (1 + $IVA) ) * $pcomision;
										}
										
										?>
										<td class="<?=$class?>" align="right" nowrap><?php echo number_format( $valorbruto = $valorparcial - $comision ,2); $ValorParcial += $valorbruto; ?></td>
							<td class="<?=$class?>" align="right" nowrap><?php echo number_format( $valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) ),2 );$ValorIVA += $valoriva; ?> </td>
							<td class="<?=$class?>" align="right" nowrap><?php echo number_format( $valorparcial - $valoriva ,2 ); $ValorBruto += ( $valorparcial - $valoriva );?></td>
						</tr>
						
						<?php
						}//end foreach( $r_facturas as $key => $valor )
						?>
							
						<tr>
							<td class="navpic" colspan="5" align="right" nowrap><b>TOTALES</b></td>
							<td class="navpic" align="center" nowrap><b><?=$Pares ?></b></td>
							<td class="navpic" align="center" colspan="2" nowrap></td>
										<td class="navpic" align="right" nowrap></td>
										<td class="navpic" align="right" nowrap><b><?=number_format( $Pago , 2)?></b></td>
										<td class="navpic" align="right" nowrap><b><?=number_format( $ValorParcial , 2)?></b></td>
							<td class="navpic" align="right" nowrap><b><?=number_format( $ValorIVA , 2)?></b></td>
							<td class="navpic" align="right" nowrap><b><?=number_format( $ValorBruto , 2)?></b></td>
						</tr>
											
						
						
						</table>
					<br><br>

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

