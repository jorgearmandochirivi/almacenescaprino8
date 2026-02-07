<body><%

$MOD = "mensual";
		
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
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$FechaDesde, $FechaHasta,$IDPuntoVenta,$MOD, $dirroot;
 //require( "Reportes/Calc.php" );
 //$Calendario = new Date_Calc;
 
 $ReteICA =  get_field( "ReteICA","Valor","IDReteICA",1 )  ;
 $ReteIVA = get_field( "ReteIVA","Valor","IDReteIVA",1 ) / 100;
 $ReteFuente = get_field( "ReteFuente","Valor","IDReteFuente",1 ) / 100;
 
%>
	
	<table width="100%">
		
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td class="col2" valign="middle"><img src="admin/images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="col2">
							
								Desde	<input readonly type="text" name="FechaDesde" class="input" value="<?=$FechaDesde?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="col2">
								
								Hasta	<input readonly type="text" name="FechaHasta" class="input" value="<?=$FechaHasta?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
							<td  align='left' valign='middle' class="col2"><img src='admin/images/house.png' border='0'  alt=''></td>
							<td align="left" valign="middle" class="col2"> <input type="hidden" value="<?=$IDPuntoVenta?>" name=IDPuntoVenta> <input type="hidden" name="mod" value="<?=$MOD?>"><input type="hidden" name="action" value="view"></td>
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
		<%
		if(!empty($IDPuntoVenta) && !empty( $FechaDesde ) && !empty( $FechaHasta ) ){
		%>
		<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp; <br>
				<br>
	<?
	$filedir = $dirroot."files/";
	ob_start();	
	?>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="100%">
		
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
			</td>
			<td class="tbtbot"><b></b>
				<span class="gen">
						<?
						echo "MENSUAL CONSIGNACIONES ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta )." ".$FechaDesde." - ".$FechaHasta;
						?>
				</span>
			</td>
			<td class="tbtr">
				<img src="images/spacer.gif" alt="" width="124" height="22" />
			</td>
		</tr>
	</table>
				<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#FFFFFF">	
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<?
					
					$array_fechas = dhabiles($FechaDesde, $FechaHasta);
					
					//print_r( $array_fechas );
					
					 
					 /********************* TRAER DATOS DE VENTAS CON TARJETAS DE CREDITO Y DEBITO 'ID'S MAYOR QUE 2'*********************/
					 $sql_facturas = " SELECT F.*, 
											DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF,
											SUM( F.ValorTotal ) as TotalFactura, SUM( FP.Valor ) as ValorFP,FP.*,
											SUM( ( FP.Valor / ( 1 + $IVA ) ) * ( FP.Comision / 100 ) ) as ComisionFP
											FROM Factura F, FormaPagoFactura FP
											WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
											AND F.FechaFactura BETWEEN '$FechaDesde' AND '$FechaHasta' 
											AND F.IDFactura = FP.IDFactura
											AND F.IDPuntoVenta = FP.IDPuntoVenta
											AND FP.IDFormaPago > 2
											GROUP BY FP.IDFormaPago, DATE_FORMAT( F.FechaFactura, '%Y-%c-%d' )
											ORDER BY FechaFactura ASC";
											
					$qry_facturas = db_query( $sql_facturas );
					
					$i = 0;
					$formapago = array();
					
					while( $r_facturas = db_fetch_array( $qry_facturas ) )
					{
						$array_factura[$r_facturas['FechaFacturaF']][$i] = $r_facturas;
						$i++;
					}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )
					//print_r( $array_factura );
					
					 /********************* TRAER DATOS DE VENTAS CON EFECTIVO 'ID'S MENOR O IGUAL QUE 2'*********************/
					 $sql_facturasef = " SELECT F.*, 
											DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF,
											SUM( F.ValorTotal ) as TotalFactura, SUM( FP.Valor ) as ValorFP,FP.*
											FROM Factura F, FormaPagoFactura FP
											WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
											AND F.FechaFactura BETWEEN '$FechaDesde' AND '$FechaHasta' 
											AND F.IDFactura = FP.IDFactura
											AND F.IDPuntoVenta = FP.IDPuntoVenta
											AND FP.IDFormaPago <= 2
											GROUP BY  DATE_FORMAT( F.FechaFactura, '%Y-%c-%d' )
											ORDER BY FechaFactura ASC
											";
											
					$qry_facturasef = db_query( $sql_facturasef );
					
					$i = 0;
					$formapagoef = array();
					
					while( $r_facturasef = db_fetch_array( $qry_facturasef ) )
					{
						$array_facturaef[$r_facturasef['FechaFacturaF']][$i] = $r_facturasef;
						$i++;
					}//end while( $r_facturasef = db_fetch_array( $qry_facturasef ) )
					
					//print_r( $array_facturaef );
					
					//traer los codigo de bancos
					$qry_bancos = db_query( $sql_bancos = "SELECT IDBanco, Nombre FROM Banco");
					while( $r_bancos = db_fetch_array( $qry_bancos ) )
					{
						$array_bancos[$r_bancos[IDBanco]] = $r_bancos[Nombre];
					}//en while bancos
									
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td class="navpic" nowrap>Fecha</td>
							<td class="navpic" nowrap>Banco</td>
							<td class="navpic" align="center" nowrap>Venta</td>
							<td class="navpic" align="center" nowrap>Comisi&oacute;n</td>
							<td class="navpic" align="center" nowrap>Rete. Fuente</td>
							<td class="navpic" align="center" nowrap>Valor Neto</td>
							<td class="navpic" align="center" nowrap>Rte. IVA</td>
							<td class="navpic" align="center" nowrap>Rte. ICA</td>
							<td class="navpic" align="center" nowrap>Ingreso</td>
						</tr>
						<?
						
						//voltear el array porque viene al reves
						
						$array_fechas = array_reverse( $array_fechas );
						//print_r( $array_fechas );
						
						/************************* MOSTRAR LAS VENTAS CON TARJETA DE CREDITO Y DEBITO ********************************/
						foreach( $array_fechas as $Fecha => $valor )
						{ 
							$class = repetition()?"row2":"row1";
							//print_r($valor);
							//print_r( $array_factura );
							
							foreach( $array_factura as $key => $datos )
							{
								
								//print_r( $datos );
								
								foreach( $datos as $llave => $dato )
								{
									
									//echo $key;
									
									if( in_array( $key, $valor ) )
									{
										
										$Base = $dato['ValorFP'] / ( 1 + $IVA );
										$ValorIVA = $dato['ValorFP'] - ( $dato['ValorFP'] /  (1 + $IVA ) ) ;
							
										$ValorTotal = $dato['ValorFP'];
										$Comision = $dato['ComisionFP'];
										$ValorRetefuente = ( $Base * $ReteFuente ); 
										$Neto = $ValorTotal - $Comision;
										$ValorRIVA = $ValorIVA * $ReteIVA; 
										$ValorRICA = ( ($Base / 1000) * $ReteICA );
										$Ingreso = ( $Neto - $ValorRetefuente -  $ValorRIVA - $ValorRICA );
										$CBanco = $array_bancos[$dato[IDBanco]];
										
										$array_valores[$Fecha][$dato['IDFormaPago']]['IDBanco'] =  $CBanco;
										$array_valores[$Fecha][$dato['IDFormaPago']]['Venta'] +=  $ValorTotal;
										$array_valores[$Fecha][$dato['IDFormaPago']]['Comision'] +=  $Comision;
										$array_valores[$Fecha][$dato['IDFormaPago']]['ValorRetefuente'] +=  $ValorRetefuente;
										$array_valores[$Fecha][$dato['IDFormaPago']]['Neto'] +=  $Neto;
										$array_valores[$Fecha][$dato['IDFormaPago']]['ValorRIVA'] +=  $ValorRIVA;
										$array_valores[$Fecha][$dato['IDFormaPago']]['ValorRICA'] +=  $ValorRICA;
										$array_valores[$Fecha][$dato['IDFormaPago']]['Ingreso'] +=  $Ingreso;
									}//end if
								}//end forech
							}//end foreach
						}//end foreach( $r_facturas as $key => $valor )

						foreach( $array_valores as $Fecha => $valor )
						{
							foreach( $valor as $FormaPago => $datos )
							{
								$class = repetition()?"row2":"row1";
								
						?>
								<tr>
									<td class="<?=$class?>" align="center" nowrap><?=$Fecha?></td>
									<td class="<?=$class?>" align="center" nowrap><?=$datos[IDBanco]?></td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $ValorTotal = $datos[Venta],2 ); $tValorTotal += $ValorTotal;?> </td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $Comision = $datos[Comision] ,2); $tComision += $Comision?></td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $ValorRetefuente = $datos[ValorRetefuente],2 ); $tValorRetefuente += $ValorRetefuente;?></td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $Neto = $datos[Neto] ,2 ); $tNeto += $Neto; ?></td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $ValorRIVA = $datos[ValorRIVA] ,2 ); $tValorRIVA += $ValorRIVA; ?> </td>
									<td class="<?=$class?>" align="right" nowrap><?echo number_format( $ValorRICA = $datos[ValorRICA],2); $tValorRICA += $ValorRICA; ?></td>
									<td class="<?=$class?>" align="right" nowrap><?echo number_format( $Ingreso = $datos[Ingreso] ,2 ); $tIngreso += $Ingreso; ?></td>
								</tr>
						<?
							}//end for
						}//end for
						
						/****************************** FIN DE MOSTRAR LAS VENTAS CON TARJETA DE CREDITO Y DEBITO ********************************/
						
						
						/************************* MOSTRAR LAS VENTAS CON EFECTIVO ********************************/
						foreach( $array_fechas as $Fecha => $valor )
						{ 
							$class = repetition()?"row2":"row1";
							//print_r($valor);
							foreach( $array_facturaef as $key => $datos )
							{
								foreach( $datos as $llave => $dato )
								{
									if( in_array( $key, $valor ) )
									{
										$Base = $dato['ValorFP'] / ( 1 + $IVA );
										$ValorIVA = $dato['ValorFP'] - ( $dato['ValorFP'] / ( 1 + $IVA ) ) ;
							
										$ValorTotal = $dato['ValorFP'];
										$Comision = 0;
										$ValorRetefuente = 0;//( $Base * $ReteFuente ); 
										$Neto = $ValorTotal - $Comision;
										$ValorRIVA = 0;//$ValorIVA * $ReteIVA; 
										$ValorRICA = 0;//( ($Base / 1000) * $ReteICA );
										$Ingreso = ( $Neto - $ValorRetefuente -  $ValorRIVA - $ValorRICA );
										$CBanco = $array_bancos[$dato[IDBanco]];
										
										$array_valoresef[$Fecha]['IDBanco'] =  $CBanco;
										$array_valoresef[$Fecha]['Venta'] +=  $ValorTotal;
										$array_valoresef[$Fecha]['Comision'] +=  $Comision;
										$array_valoresef[$Fecha]['ValorRetefuente'] +=  $ValorRetefuente;
										$array_valoresef[$Fecha]['Neto'] +=  $Neto;
										$array_valoresef[$Fecha]['ValorRIVA'] +=  $ValorRIVA;
										$array_valoresef[$Fecha]['ValorRICA'] +=  $ValorRICA;
										$array_valoresef[$Fecha]['Ingreso'] +=  $Ingreso;
									}//end if
								}//end forech
							}//end foreach
						}//end foreach( $r_facturas as $key => $valor )

						foreach( $array_valoresef as $Fecha => $datos )
						{
							$class = repetition()?"row2":"row1";
								
						?>
								<tr>
									<td class="<?=$class?>" align="center" nowrap><?=$Fecha?></td>
									<td class="<?=$class?>" align="center" nowrap><?=$datos[IDBanco]?></td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $ValorTotal = $datos[Venta],2 ); $tValorTotal += $ValorTotal;?> </td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $Comision = $datos[Comision] ,2); $tComision += $Comision?></td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $ValorRetefuente = $datos[ValorRetefuente],2 ); $tValorRetefuente += $ValorRetefuente;?></td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $Neto = $datos[Neto] ,2 ); $tNeto += $Neto; ?></td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $ValorRIVA = $datos[ValorRIVA] ,2 ); $tValorRIVA += $ValorRIVA; ?> </td>
									<td class="<?=$class?>" align="right" nowrap><?echo number_format( $ValorRICA = $datos[ValorRICA],2); $tValorRICA += $ValorRICA; ?></td>
									<td class="<?=$class?>" align="right" nowrap><?echo number_format( $Ingreso = $datos[Ingreso] ,2 ); $tIngreso += $Ingreso; ?></td>
								</tr>
						<?
						}//end for
						
						/********************** FIN DE MOSTRAR LAS FECHAS CON EFECTIVO *********************************************/
						
						
						
						?>					
						<tr>
							<td class="navpic" colspan="2" align="right" nowrap>TOTALES</td>
							<td class="navpic" align="right" nowrap><?=number_format( $tValorTotal, 2) ?></td>
							<td class="navpic" align="right" nowrap><?=number_format( $tComision, 2) ?></td>
							<td class="navpic" align="right" nowrap><?=number_format( $tValorRetefuente, 2) ?></td>
							<td class="navpic" align="right" nowrap><?=number_format( $tNeto , 2) ?></td>
							<td class="navpic" align="right" nowrap><?=number_format( $tValorRIVA , 2)?></td>
							<td class="navpic" align="right" nowrap><?=number_format( $tValorRICA , 2)?></td>
							<td class="navpic" align="right" nowrap><?=number_format( $tIngreso , 2)?></td>
						</tr>	
					</table>
				</td>
			</tr>
		</form>
	
		</table>
		<?
		$page = ob_get_contents();
		$fecha = date( "Y-m-d H:i:s" );
		$name = "MensualVentas$fecha.xls";
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
	<% 
	 } // END if(!empty($IDEmpresa))
	%>
	</table>
	<%						
}// Enf function print()	

%>
</body>