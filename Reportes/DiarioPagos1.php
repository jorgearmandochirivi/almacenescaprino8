<body>
	<?php
		switch ($action) {
			
			case "view" :
				print_from($IDPuntoVenta,$Fecha);
			break;
			
			default :
				print_from($IDPuntoVenta,$Fecha);
			break;
		
		} // End switch


 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($IDPuntoVenta="", $Fecha=""){
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA, $dirroot;
 
	$sql_retefuente = "SELECT * FROM ReteFuente LIMIT 1";
	$query_retefuente = db_query( $sql_retefuente );
	$r_retefuente = db_fetch_object( $query_retefuente );
	
	$ReteFuente = $r_retefuente->Valor / 100;
 
  ?>
	<table width="100%">
		
		<br>
		<?php
		if(!empty($IDPuntoVenta) && !empty( $Fecha )){
		?>
		<tr>
			<td>	
				<table width="100%" border="0" align="center" cellspacing="1" cellpadding="0" >
					<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
						<tr>
							<td class="col2" valign="middle">
								<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="100%">
		
									<tr>
										<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
										</td>
										<td class="tbtbot"><b></b>
											<span class="gen">
												Reporte Formas de Pago Almacen : <b><?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta)?></b> Fecha : <b><?=formatofecha($Fecha)?></b>
											</span>
										</td>
										<td class="tbtr">
											<img src="images/spacer.gif" alt="" width="124" height="22" />
										</td>
									</tr>
								</table>
								
							</td>
						</tr>
						<?php 				
							$sql_formapago = "SELECT PVB.IDFormaPago, FP.Descripcion, PVB.IDBanco 
											FROM PuntoVentaBanco PVB, FormaPago FP 
											WHERE PVB.IDPuntoVenta = '$IDPuntoVenta'
											AND FP.IDFormaPago = PVB.IDFormaPago";
							
							$qry_formapago = db_query( $sql_formapago );
							
							$i = 0;
							while( $array_formapago = db_fetch_array( $qry_formapago ) )
							{
								$r_formapago[$i] = $array_formapago;
								$i++;
								
							}//end while( $array_formapago = db_fetch_array( $qry_formapago ) )
							
							$sql_bancos = "SELECT * FROM Banco ";
							$qry_bancos = db_query( $sql_bancos );
							
							$bancos = array();
							while( $array_banco = db_fetch_array( $qry_bancos ) )
								$bancos[$array_banco[IDBanco]] = $array_banco[Nombre];
																					
				?>
						<tr>
							<td class="mainbg">
	<?php
	$filedir = $dirroot."files/";
	ob_start();	
	?>
								<table class="bordertable" width="100%" border="0" cellspacing="1" cellpadding="1">
									<tr>
										<td class="navpic" align="center" >No. Factura</td>
										<td class="navpic" align="center" nowrap>Valor</td>
										<td class="navpic" align="center" nowrap>Comision (%)</td>
										<td class="navpic" align="center" nowrap>Vr. Comision</td>
										<td class="navpic" align="center" nowrap>Vr. Neto.</td>
										<td class="navpic" align="center" nowrap>Rte. Fuente</td>
										<td class="navpic" align="center" nowrap>Ingreso</td>
									</tr>
									<?php
									foreach( $r_formapago as $key => $valor )
									{ 
										
										//print_r($valor);
										//print_r($bancos);

										$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, DF.ValorU,DF.PrecioU, DF.Cantidad, FPF.IDFormaPagoFactura, FPF.Valor, FPF.Comision 
										FROM Factura F, DetalleFactura DF, FormaPagoFactura FPF
										WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
										AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = DATE_FORMAT( '$Fecha','%Y-%c-%d' ) 
										AND F.IDFactura = DF.IDFactura
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND F.IDFactura = FPF.IDFactura
										AND FPF.IDPuntoVenta = F.IDPuntoVenta 
										AND FPF.IDFormaPago = $valor[IDFormaPago]
										GROUP BY F.IDFactura";
					
										$qry_facturas = db_query( $sql_facturas );
										
										$i = 0;
										$formapago = array();
										
										if( db_num_rows( $qry_facturas ) > 0)
										{
										?>
											<tr>
												<td class="rowform" colspan="7" align="left" nowrap><?=$valor['Descripcion']?> <br><?=$bancos[$valor[IDBanco]]?></td>
											</tr>
											<?php
											$valorfactura = 0;
											$valorcomision = 0;
											$valorneto = 0;
											$valorretefuente = 0;
											$valoringreso = 0;
											while( $r_factura = db_fetch_object( $qry_facturas ) )
											{
												$class = repetition()?"row2":"row1";
												
											?>
											
											<tr>
												<td class="<?=$class?>" align="center" nowrap><?php echo $r_factura->NumeroFactura?></td>
												<td class="<?=$class?>" align="right" nowrap><?php echo number_format( $r_factura->Valor, 2 );  $valorfactura += $r_factura->Valor; ?></td>
												<td class="<?=$class?>" align="center" nowrap><?php echo $r_factura->Comision?></td>
												<td class="<?=$class?>" align="right" nowrap><?php echo number_format( $comision =  ( $r_factura->Valor / ( 1 + $IVA ) ) * ( $r_factura->Comision / 100 ), 2 ); $valorcomision += $comision;?></td>
												<td class="<?=$class?>" align="right" nowrap><?php echo number_format( ($r_factura->Valor - $comision ), 2 ); $valorneto += $r_factura->Valor - $comision; ?></td>
												<td class="<?=$class?>" align="right" nowrap><?php echo number_format( $ValorReteFuente = ( $r_factura->Valor/ ( 1 + $IVA ) )  * $ReteFuente , 2 ); $valorretefuente += $ValorReteFuente; ?></td>
												<td class="<?=$class?>" align="right" nowrap><?php echo number_format( $r_factura->Valor - $comision - $ValorReteFuente, 2 ); $valoringreso +=  $r_factura->Valor - $comision - $ValorReteFuente?></td>
											</tr>
											
										

									
									<?php
											}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )
									?>
											<tr>
												<td align="right" nowrap>TOTAL F. PAGO</td>
												<td  align="right" nowrap><?php echo  number_format($valorfactura , 2) ; ?></td>
												<td  align="center" nowrap></td>
												<td  align="right" nowrap><?php echo number_format($valorcomision , 2);?></td>
												<td  align="right" nowrap><?php echo number_format($valorneto , 2) ; ?></td>
												<td  align="right" nowrap><?php echo number_format($valorretefuente , 2) ; ?></td>
												<td  align="right" nowrap><?php echo number_format( $valoringreso , 2);?></td>
											</tr>
									<?php
										$totalvalorfactura += $valorfactura ;
										$totalvalorcomision += $valorcomision;
										$totalvalorneto += $valorneto ;
										$totalvalorretefuente += $valorretefuente ;
										$totalvaloringreso += $valoringreso;	
										}//end if( db_num_rows( $qry_facturas ) )						
									}//end foreach( $r_formapago as $key => $valor )
									?>
									<tr>
										<td class="navpic" align="right" nowrap>TOTALES</td>
										<td class="navpic" align="right" nowrap><?=number_format( $totalvalorfactura, 2) ?></td>
										<td class="navpic" align="center" nowrap></td>
										<td class="navpic" align="right" nowrap><?=number_format( $totalvalorcomision , 2)?></td>
										<td class="navpic" align="right" nowrap><?=number_format( $totalvalorneto , 2)?></td>
										<td class="navpic" align="right" nowrap><?=number_format( $totalvalorretefuente , 2)?></td>
										<td class="navpic" align="right" nowrap><?=number_format( $totalvaloringreso , 2)?></td>
									</tr>
								</table>
		<?php
		$page = ob_get_contents();
		$fecha = date( "Y-m-d H:i:s" );
		$name = "DiarioPagos$fecha.xls";
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